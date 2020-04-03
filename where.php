<!DOCTYPE html>
<html lang="ja">
	<head>
		<title>どこUNI？</title>
		<link rel="stylesheet" href="css/import.css?<?php echo date('Ymd-Hi'); ?>" type="text/css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
		<meta charset="utf-8">
		<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
		<script src="js/table_tr_link.js"></script>
		<script src="js/Cookie.js"></script>
		<script src="js/Functions.js?<?php echo date('Ymd-Hi'); ?>"></script>
		<script src="js/modal.js"></script>

		<script>
			// ==========================
			//  簡易的に描画を停止します
			// ==========================
			function SuspendRayout()
			{
				$("#updatableContents").css({ visibility: "hidden" });
			}

			// ==========================
			//  描画を再開します
			// ==========================
			function ResumeRayout()
			{
				$("#updatableContents").css({ visibility: "visible" });
			}
		
			// ====================
			// 詳細画面へ移動
			// ====================
			function MoveToDetail(placeID)
			{
				setCookie("PLACE_ID", placeID, 1);
				window.location.href = "popularity_detail.php";
			}
			
			// =====================
			//  データのロード
			// =====================
			function LoadPopularity(_addDays)
			{
				document.getElementById("output").innerHTML = "";
				var args = {
					areaID : getCookie("AREA_ID"),
					addDays : _addDays
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetPopularityPlace.php",
					data: args,
					success: function(data)
					{
						var beforePlaceID;
						var isFirst = true;
						var rowCnt = 0;
						var colCnt = 0;
						$("#output").append('<table id="popularity_data">');
						$("#popularity_data").append('<tr id="popularity_header">');
						$("#popularity_header").append('<th class="header1">場所</th>');
						$("#popularity_header").append('<th class="header2">確定</th>');
						$("#popularity_header").append('<th class="header3">候補</th>');
						$("#popularity_header").append('<th class="header4">可能性</th>');
						$("#popularity_data").append('</tr>');
						$("#popularity_data").append('<tbody id="popularity_body">');
						data.forEach(function(value)
						{
							// 1行目以外は</tr>で〆る
							if(!isFirst)
							{
								$("#popularity_body").append("</tr>");
								rowCnt += 1;
							}
							else
							{
								// 1行目フラグをおろす
								isFirst = false;
							}
							
							// 行を変更する
							$("#popularity_body").append('<tr id="row' + rowCnt + '" onClick="MoveToDetail(' + value.PLACE_ID + ')">');
							$('#row' + rowCnt).append('<td>' + value.PLACE_NAME + '</td>');
							$('#row' + rowCnt).append("<td>" + value.VALUE1 + "人</td>");
							$('#row' + rowCnt).append("<td>" + value.VALUE2 + "人</td>");
							$('#row' + rowCnt).append("<td>" + value.VALUE3 + "人</td>");
						});
						
						$("#popularity_body").append('</tbody>');
						$("#output").append("</table>");
						var date = GetTodayAddDays(_addDays);
						$("#addedDate").remove();
						$("#watchingDate").append('<div id="addedDate" class="heading">'+ date +'</div>');
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
					}
				});
				
				return true;
			}
			
			// =======================
			//  自分の投票データを取得
			// =======================
			function LoadMyPopularity()
			{
				var args = {
					userID: getCookie("USER_ID")
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetMyPopularity.php",
					data: args,
					success: function(data)
					{
						data.forEach(function(value)
						{
							if(value.PLACE_ID != null)
							{
								document.sendData.placeID.value = value.PLACE_ID;
							}
							
							document.sendData.joinType.value = value.JOIN_TYPE;
							document.sendData.purposeType.value = value.PURPOSE_TYPE;
							document.sendData.datetime_from.value =  value.JOIN_TIME_FROM;
							document.sendData.datetime_to.value = value.JOIN_TIME_TO;
						});
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
					}
				});
			}
			
			// ========================
			//  場所のロード
			// ========================
			function LoadPlaceData()
			{
				var args = {
					areaID : getCookie('AREA_ID'),
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetPlaceAPI.php",
					data: args,
					success: function(data)
					{
						data.forEach(function(value)
						{
							// 行を変更する
							$("#placeID").append('<option value=' + value.PLACE_ID + '>' + value.PLACE_NAME + '</option>');
						});
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
						return false;
					}
				});
				
				return true;
			}
			
			// ========================
			//  参加区分のロード
			// ========================
			function GetJoinType()
			{
				var data = {
					key : "JOIN_TYPE",
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetType.php",
					data: data,
					success: function(data)
					{
						if(data.RESULT == true)
						{
							data.TYPE_DATA.forEach(function(value)
							{
								// 行を変更する
								$("#joinType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
							});
						}
						else
						{
							alert(data.MESSAGE);
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
						return false;
					}
				});
				
				return true;
			}
			
			// ========================
			//  目的のロード
			// ========================
			function GetPurposeType()
			{
				var args = {
					key : "PURPOSE",
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetType.php",
					data: args,
					success: function(data)
					{
						if(data.RESULT == true)
						{
							data.TYPE_DATA.forEach(function(value)
							{
								// 行を変更する
								$("#purposeType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
							});
						}
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
						return false;
					}
				});
				
				return true;
			}
			
			// ========================
			//  ユーザデータのロード
			// ========================
			function GetUserData()
			{
				var userID = getCookie("USER_ID");
				var args = {
					userID : userID,
				};
				
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "php/GetUserData.php",
					data: args,
					success: function(data)
					{
						data.forEach(function(value)
						{
							// クッキー設定
							setCookie("USER_NAME", value.USER_NAME, 1);
							setCookie("MAIN_CHARACTER_ID", value.MAIN_CHARACTER_ID, 1);
							setCookie("RIP", value.RIP, 1);
							setCookie("AREA_ID", value.AREA_ID, 1);
						});
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
						return false;
					}
				});
				
				return true;
			}
			
			// =========================
			//  イベントクリック
			// =========================
			function Test(_eventID)
			{
				var args = {
					eventID : _eventID
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetEventDetailAPI.php",
					data: args,
					success: function(data)
					{
						data.forEach(function(value)
						{
							if(value.PLACE_ID != null)
							{
								document.sendData.placeID.value = value.PLACE_ID;
								document.sendData.datetime_from.value = value.EVENT_TIME_FROM;
								document.sendData.datetime_to.value = value.EVENT_TIME_TO;
								document.sendData.purposeType.value = 3;
								
								alert("イベント情報をフォームにコピーします。");
							}
						});
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
					}
				});
				
				return true;      
			}
			
			// ========================
			//  イベントのロード
			// ========================
			function GetEventData(_addDays)
			{
				document.getElementById("event").innerHTML = "";
				var args = {
					areaID : getCookie('AREA_ID'),
					addDaysFrom : _addDays,
					addDaysTo : _addDays + 6,
					userID : getCookie("USER_ID")
				};
				$.ajax({
					type: "POST",
					url: "php/GetEventAPI.php",
					data: args,
					success: function(data)
					{
						var beforePlaceID;
						var isFirst = true;
						var rowCnt = 0;
						var colCnt = 0;
						if(data.RESULT == true)
						{
							if(data.DATA.length)
							{
								$("#event").append('<table class= "event" id="event_data">');
								$("#event_data").append('<tr id="event_header">');
								$("#event_header").append('<th class="event_header1">日付</th>');
								$("#event_header").append('<th class="event_header2">場所</th>');
								$("#event_header").append('<th class="event_header3">イベント名</th>');
								$("#event_header").append('<th class="event_header4">時間</th>');
								$("#event_data").append('</tr>');
								$("#event_data").append('<tbody id="event_body">');
								
								data.DATA.forEach(function(value)
								{
									// 1行目以外は</tr>で〆る
									if(!isFirst)
									{
										$("#event_body").append("</tr>");
										rowCnt += 1;
									}
									else
									{
										// 1行目フラグをおろす
										isFirst = false;
									}
									
									// 行を変更する
									$("#event_body").append('<tr id="ev_row' + rowCnt + '" onClick="Test(' +value.EVENT_ID+');">');
									$('#ev_row' + rowCnt).append(
										'<td>' + value.EVENT_DATE + '</td>');
										
									$('#ev_row' + rowCnt).append(
										'<td>' + value.PLACE_NAME + '</td>');
										
									$('#ev_row' + rowCnt).append(
										'<td>' + value.EVENT_NAME + '</td>');
										
									$('#ev_row' + rowCnt).append(
										'<td>' + value.EVENT_TIME_FROM + ' - ' + value.EVENT_TIME_TO + '</td>');
								});
							
								$("#event_body").append('</tbody>');
								$("#event").append("</table>");
							}
						}
						else
						{
							$("#event").append('<span class="attention">登録されているイベントはありません</span>');
						}
					},

					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
					}
				});

				return true;
			}

			// =========================
			//  ロード完了時イベント
			// =========================
			$(document).ready(function()
			{
				SuspendRayout();
				
				var userID = getCookie("USER_ID");
				var canRegistEvent = getCookie("REGIST_EVENT");

				if(userID == undefined || userID == "null" || userID == null || userID == '')
				{
					document.getElementById("sendDataForm").style.display="none";
					document.getElementById("loggedinFunctions").style.display="none";
				}
				else
				{
					document.getElementById("noLoggedinFunctions").style.display="none";
				}

				if(canRegistEvent != "1")
				{
					document.getElementById("eventOrganizerFunctions").style.display="none";
				}

				var now = new Date();
				var hour = now.getHours(); // 時

				if(String(hour).length == 1)
				{
					hour = "0" + hour;
				}

				var min = now.getMinutes(); // 分
				if(String(min).length == 1)
				{
					min = "0" + min;
				}

				var sec = now.getSeconds(); // 秒
				if(String(sec).length == 1)
				{
					sec = "0" + sec;
				}

				// ボタンを押下不可に
				document.getElementById("SendPopularity").disabled = true;
				document.getElementById("SendCancel").disabled = true;
				document.sendData.datetime_from.value = hour + ":" + min + ":00";
				document.sendData.datetime_to.value = hour + ":" + min + ":00";

				var userID = getCookie("USER_ID");
				setCookie("ADD_DAYS", 0);
				
				let promise = new Promise((resolve, reject) => {
					setTimeout(() => {
						resolve(LoadPopularity(0));
					}, 100);
				});

				promise.then((result) => {
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							resolve(GetEventData(0));
						}, 100);
					});
				}).then((result) => {
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							resolve(LoadPlaceData());
						}, 100);
					});
				}).then((result) => {
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							resolve(GetJoinType());
						}, 100);
					});
				}).then((result) => {
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							resolve(GetPurposeType());
						}, 100);
					});
				}).then((result) => {
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							resolve(GetUserData());
						}, 100);
					});
				}).then((result) => {
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							resolve(LoadMyPopularity());
						}, 100);
					});
				}).catch(() => { // エラーハンドリング
					alert('ロード処理でエラーが発生しました。');
				});
				
				$('#functionButtons').load('functions.html');
				$('#menuScript').load('functionsEnabled.html');
				ResumeRayout();

				// ボタンを押下可能に
				document.getElementById("SendPopularity").disabled = false;
				document.getElementById("SendCancel").disabled = false;
				return false;
			});
		</script>
	</head>
<body>

<script>

	// ***
	// * 日付移動
	// ***************
	function MoveDay(moveDay_)
	{
		// 現在の加算日付日数
		var currentAddDay = getCookie("ADD_DAYS");
		
		// 新しい日付日数
    	var addDays = Number(currentAddDay) + moveDay_;
		
		// クッキーに設定
		setCookie("ADD_DAYS", addDays);
		
		let promise = new Promise((resolve, reject) => {
			setTimeout(() => {
				resolve(GetEventData(addDays));
			}, 100);
		});
		
		promise.then((result) => {
			return new Promise((resolve, reject) => {
				setTimeout(() => {
					resolve(LoadPopularity(addDays));
				}, 100);
			});
		}).catch(() => { // エラーハンドリング
			alert('日付移動でエラーが発生しました。');
		});
	}
	
	// ***
	// * 送信します。
	// ***************
	function SendPopularity()
	{
		var loginUserID = getCookie("USER_ID");
		var _addDays = getCookie("ADD_DAYS");
		
		if(_addDays < 0){
			alert('過去の日付に送信はできません');
			return;
		}
		
		// ボタンを押下不可に
		document.getElementById("SendPopularity").disabled = true;
		document.getElementById("SendCancel").disabled = true;
		
		checkbox = document.getElementById("with_tweet");
		
		var data = {
			placeID : $('#placeID').val(),
			placeName : $('#placeID option:selected').text(),
			userID : loginUserID,
			playerName : getCookie("USER_NAME"),
			joinType : $('#joinType').val(),
			joinText : $('#joinType option:selected').text(),
			purposeType : $('#purposeType').val(),
			purposeText : $('#purposeType option:selected').text(),
			from : $('#datetime_from').val(),
			to : $('#datetime_to').val(),
			addDays : _addDays,
			withTweet : checkbox.checked,
			comment : document.getElementById("comment").text,
			
			// オプション
			RIP : getCookie("RIP"),
			characterID : getCookie("MAIN_CHARACTER_ID"),
		};
		
		$.ajax({
			type: "POST",
			url: "php/SendPopularity.php",
			data: data,
			success: function(jsonData)
			{
				// 処理を記述
				return false;
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert("送信に失敗しました。");
				console.log('error : ' + errorThrown);
				return false;
			},
			
			complete: function()
			{
				alert("送信しました。");
				
				let promise = new Promise((resolve, reject) => {
					setTimeout(() => {
						resolve(LoadPopularity(_addDays));
					}, 100);
				});
				
				// ボタンの状態を変更
				document.getElementById("SendPopularity").disabled = false;
				document.getElementById("SendCancel").disabled = false;
			}
		});
		
		return false;
	}
	
	// ===========================
	// キャンセルボタン押下イベント
	// ===========================
	function SendCancel()
	{
		var loginUserID = getCookie("USER_ID");
		document.getElementById("SendPopularity").disabled = true;
		document.getElementById("SendCancel").disabled = true;
		var _addDays = getCookie("ADD_DAYS");

		var data = {
			userID : loginUserID,
			cancelAddDays : _addDays,
		};
		
		$.ajax({
			type: "POST",
			url: "php/CancelPopularity.php",
			data: data,
			success: function(jsonData)
			{
				// 処理を記述
				return false;
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			return false;
			},
			
			complete: function()
			{
				alert("申請を取り消しました。");
				
				LoadPopularity();
				
				document.getElementById("SendPopularity").disabled = false;
				document.getElementById("SendCancel").disabled = false;
			}
		});
		
		return false
	}
</script>

<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>

</head>

<body>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<div id="main_header">
    <h1>どこＵＮＩ？</h1>
</div>

<div id="watchingDateWrapper">
    <p>
        <div class="datePager" onClick="MoveDay(-10); return false;"><</div>
        <div class="datePager" onClick="MoveDay(-1); return false;"><</div>
        <div id="watchingDate" class="watchingDate"></div>
        <div class="datePager" onClick="MoveDay(1);">></div>
        <div class="datePager" onClick="MoveDay(10);">></div>
    </p>
</div>

<div id="eventWrapper">
    <div id="event_title">直近イベント</div>
    <div id="event"></div>
</div>

<div id="output"></div>
<div class="sendWrapper" id="sendDataForm">
        <span class="box-title">どこかに行く</span>
        <form id="sendData" name="sendData">
        <div>

        <p>
            <label for="label_place" accesskey="n">場所：</label><br/>
            <select class="formComboBox" name="placeID" id="placeID">
            </select><span class="mini">←表示されない場合は再ログインしてみてください。</span>
        </p>
        <p>
            <label for="label_place" accesskey="n">参加区分：</label><br/>
            <select class="formComboBox" name="joinType" id="joinType">
            </select>
        </p>
        <p>
            <label for="label_place" accesskey="n">目的：</label><br/>
            <select class="formComboBox" name="purposeType" id="purposeType">
            </select>
        </p>

        <br/>

        <p>
            <label for="label_place" accesskey="n">参加時刻（FROM - TO)：</label><br/>
            <input type="time" id="datetime_from" name="datetime_from" class="from_to">時 から<br/>
            <input type="time" id="datetime_to" name="datetime_to"  class="from_to">時 まで
        </p>
		
		<p>
			<textarea id="comment" cols=64 rows=3></textarea>
			<input type="checkbox" id="with_tweet">ツイートする</input>
		</p>

        </div>
    </form>
	<button id="SendPopularity" name="SendPopularity" onClick="SendPopularity(); return false;">Send</button>
	<button id="SendCancel" name="SendCancel" onClick="SendCancel(); return false;">cancel</button>
</div>

<div id="noLoggedinFunctions">
    <p>※参加投票はログインしないと使用できません。</p>
    <button type="submit" class="noBorderButton" id="MoveLoginPage" name="MoveLoginPage" onsubmit="return false;">
        <a class="btn" href="javascript:void(0)">ログイン</a>
    </button>
</div>

<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
</body>



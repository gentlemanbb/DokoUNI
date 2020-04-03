
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import2.css?ver=201812060001" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Functions.js?ver=201812060001"></script>

<script>
	var loaded;
	
	// ====================
	// 詳細画面へ移動
	// ====================
	function MoveToDetail(placeID)
	{
		setCookie("PLACE_ID", placeID, 1);
		window.location.href = "place_detail.php";
	}

	// ====================
	// スクロール
	// ====================
	function ScrollToDetail(placeID)
	{
		document.getElementById("sendDataForm").scrollIntoView(true);
		document.sendData.placeID.value = placeID;
	}

	// =====================
	//  データのロード
	// =====================
	function LoadPopularity(_addDays)
	{
		loaded = false;
		document.getElementById("output").innerHTML = "";
		
		if(getCookie("AREA_ID") == null)
		{
			setCookie("AREA_ID", 1, 1);
		}
		
		var args = {
			areaID : getCookie("AREA_ID"),
			addDays : _addDays
		};

		$.ajax({
			type: "POST",
			url: "php/GetPopularityPlace2.php",
			data: args,
			success: function(data)
			{
				var beforePlaceID;
				var isFirst = true;
				var rowCnt = 0;
				var colCnt = 0;

				$("#output").append('<table id="popularity_data">');
				$("#popularity_data").append('<tr id="popularity_header">');
				$("#popularity_header").append('<th class="header">場所</th>');
				$("#popularity_header").append('<th class="header">絶対行く</th>');
				$("#popularity_header").append('<th class="header">人がいれば</th>');
				$("#popularity_header").append('<th class="header">行けたら</th>');
				$("#popularity_data").append('</tr>');
				$("#popularity_data").append('<tbody id="popularity_body">');

				data.forEach(function(value){
					// 1行目以外は</tr>で〆る
					if(!isFirst){
						$("#popularity_body").append("</tr>");
						rowCnt += 1;
					}
					else
					{
						// 1行目フラグをおろす
						isFirst = false;
					}

					// 行を変更する
					$("#popularity_body").append('<tr id="row' + rowCnt + '">');
					$('#row' + rowCnt).append('<td><span onClick="ScrollToDetail(' + value.PLACE_ID + ')">' + value.PLACE_NAME + '</span><img src="img/buttons/info.png?ver=1" onClick="MoveToDetail(\'' + value.PLACE_ID + '\')"</td>');
					$('#row' + rowCnt).append("<td>" + value.VALUE1 + "人</td>");
					$('#row' + rowCnt).append("<td>" + value.VALUE2 + "人</td>");
					$('#row' + rowCnt).append("<td>" + value.VALUE3 + "人</td>");
				});
				
				$("#popularity_body").append('</tbody>');
				$("#output").append("</table>");

				var date = GetTodayAddDays(_addDays);
				$("#addedDate").remove();
				$("#watchingDate").append('<div id="addedDate" class="heading">'+ date +'</div>');
				document.getElementById("joinDate").innerHTML = "";
				$("#joinDate").append(date);
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			},

			complete: function(){
				loaded = true;
			}
			
		});

		return true;
	}

	// =======================
	//  自分の投票データを取得
	// =======================
	function LoadMyPopularity(_addDays)
	{
		var userID = getCookie("USER_ID");
		data = GetMyPopularity(userID, _addDays);
		document.getElementById("sent").innerHTML = "";

		if(data != null)
		{
			document.sendData.placeID.value = data.PLACE_ID;
			document.sendData.joinType.value = data.JOIN_TYPE;
			document.sendData.purposeType.value = data.PURPOSE_TYPE;
			document.sendData.datetime_from.value =  data.JOIN_TIME_FROM;
			document.sendData.datetime_to.value = data.JOIN_TIME_TO;
			document.sendData.comment.value = data.COMMENT;
			document.getElementById("sent").innerHTML = "投票済み";
		}
		else
		{
			document.getElementById("sent").innerHTML = "未投票";
		}
		
		return true;
	}

	// ========================
	//  場所のロード
	// ========================
	function LoadPlaceData(){
		var args = {
			 areaID : getCookie('AREA_ID'),
		};
		
		// 今入ってるリストをクリアする
		sl = document.getElementById('placeID');
		while(sl.lastChild)
		{
			sl.removeChild(sl.lastChild);
		}
		
		$.ajax({
			type: "POST",
			url: "php/GetPlaceAPI.php",
			data: args,
			success: function(data)
			{
				data.forEach(function(value){
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
	function GetJoinType(){
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
					data.TYPE_DATA.forEach(function(value){
						// 行を変更する
						$("#joinType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
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
	//  目的のロード
	// ========================
	function GetPurposeType(){

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
	function GetUserData(){
		var userID = getCookie("USER_ID");
		
		if(userID == null){
			return true;
		}
		var args = {
			userID : userID,
		};

		var userData = GetUserDataFunc(userID);

		if(userData != null)
		{
			// クッキー設定
			setCookie("USER_NAME", userData.USER_DATA.USER_NAME, 1);
			setCookie("MAIN_CHARACTER_ID", userData.USER_DATA.MAIN_CHARACTER_ID, 1);
			setCookie("RIP", userData.USER_DATA.RIP, 1);
			setCookie("AREA_ID", userData.USER_DATA.AREA_ID, 1);
		}

		return true;
	}

	// =========================
	//  イベントクリック
	// =========================
	function EventClicked(_eventID){
		var args = {
			 eventID : _eventID
		};

		$.ajax({
			type: "POST",
			url: "php/GetEventDetailAPI.php",
			data: args,
			success: function(data)
			{
				data.forEach(function(value){
					if(value.PLACE_ID != null){
						
						setTimeout(function(){
							if(!MoveDay(value.EVENT_DATE)) {
								setTimeout(arguments.callee, 100);
							}
							else {
								GetEventData(getDiff(GetTodayAddDays(0), value.EVENT_DATE));
								document.sendData.placeID.value = value.PLACE_ID;
								document.sendData.datetime_from.value = value.EVENT_TIME_FROM;
								document.sendData.datetime_to.value = value.EVENT_TIME_TO;
								document.sendData.purposeType.value = 4;
							}
						}, 100);

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

	// =========================
	//  特別イベントクリック
	// =========================
	function SpecialEventClicked(_url){
		window.location.href = _url;
	}

	// ========================
	//  イベントのロード
	// ========================
	function GetEventData(_addDays){
		document.getElementById("event").innerHTML = "";

		var args = {
			 areaID : getCookie('AREA_ID'),
			 addDaysTo : _addDays + 6,
			 addDaysFrom : _addDays
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

				if(data.length > 0){
					$("#event").append('<table class= "event" id="event_data">');
					$("#event_data").append('<tr id="event_header">');
					$("#event_header").append('<th class="eventHeader1">日付</th>');
					$("#event_header").append('<th class="eventHeader2">場所</th>');
					$("#event_header").append('<th class="eventHeader3">イベント名</th>');
					$("#event_header").append('<th class="eventHeader4">時間</th>');
					// $("#event_header").append('<th class="eventHeader5">コメント</th>');
					$("#event_data").append('</tr>');
					$("#event_data").append('<tbody id="event_body">');

					data.forEach(function(value){
						// 1行目以外は</tr>で〆る
						if(!isFirst){
							$("#event_body").append("</tr>");
							rowCnt += 1;
						}
						else
						{
							// 1行目フラグをおろす
							isFirst = false;
						}

						// 行を変更する
						if(value.SPECIAL_EVENT != 1){
							$("#event_body").append('<tr id="ev_row' + rowCnt + '" onClick="EventClicked(' +value.EVENT_ID+');">');
						}
						else{
							$("#event_body").append('<tr class="specialEvent" id="ev_row' + rowCnt + '" onClick="SpecialEventClicked(\'' + value.SPECIAL_URL + '\');">');
						}
						$('#ev_row' + rowCnt).append('<td>' + value.EVENT_DATE + '</td>');
						$('#ev_row' + rowCnt).append('<td>' + value.PLACE_NAME + '</td>');
						$('#ev_row' + rowCnt).append("<td>" + value.EVENT_NAME + "</td>");
						$('#ev_row' + rowCnt).append("<td>" + value.EVENT_TIME_FROM + " - " + value.EVENT_TIME_TO +"</td>");
						// $('#ev_row' + rowCnt).append("<td>" + value.COMMENT + "</td>");
					});
				
					$("#event_body").append('</tbody>');
					$("#event").append("</table>");
				}
				else {
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
	
	// ========================
	//  おススメ場所のロード
	// ========================
	function GetRecommendedPlace(){
		var args = {
			areaID	:	getCookie('AREA_ID'),
			addDays	:	getCookie("ADD_DAYS")
		};
		
		$.ajax({
			type: "POST",
			url: "php/GetRecommendPlaceAPI.php",
			data: args,
			success: function(data)
			{
				document.getElementById("recommendPlace").innerHTML = "";
				
				if(data != false)
				{
					data[0].DATA.forEach(function(value)
					{
						// 行を変更する
						$("#recommendPlace").append(value.PLACE_NAME);
					});
				}
				else
				{
					$("#recommendPlace").append('特に無し');
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
	
	// =================
	//  エリアのロード
	// =================
	function LoadAreaData(){
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetAreaAPI.php",
			success: function(data)
			{
				data.forEach(function(value)
				{
					var cnt = 0;
					if(value.AREA_NAME != '未分類')
					{
						$("#watchingArea").append('<button style="width:75px;" class="" onClick="ChangeArea(\'' + value.AREA_ID +'\');"><a class="" href="javascript:void(0)">' + value.AREA_NAME.replace('地方', '') + '</a></button>');
						cnt += 1;
					}
				});
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
	   });

	   return true;
	}
	
	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function(){
		var userID = getCookie("USER_ID");
		var canRegistEvent = getCookie("REGIST_EVENT");

		var now = new Date();

		var hour = now.getHours(); // 時
		if(String(hour).length == 1){
			hour = "0" + hour;
		}
		var min = now.getMinutes(); // 分
		if(String(min).length == 1){
			min = "0" + min;
		}

		var sec = now.getSeconds(); // 秒
		if(String(sec).length == 1){
			sec = "0" + sec;
		}
		
		SuspendRayout();
		
		// ボタンを押下不可に
		document.sendData.elements["SendPopularity"].disabled = true;
		document.sendData.elements["SendCancel"].disabled = true;
		document.sendData.datetime_from.value = hour + ":" + min + ":00";
		document.sendData.datetime_to.value = hour + ":" + min + ":00";
		
		// ユーザＩＤ取得
		var userID = getCookie("USER_ID");
		
		// 現在の日付を設定
		setCookie("ADD_DAYS", 0);
		
		// 本日のデータを取得
		LoadPopularity(0);
		
		// イベントデータを取得
		GetEventData(0);
		
		setTimeout(function(){
			if(!LoadPlaceData()) {
				setTimeout(arguments.callee, 100);
			}
			else {
				setTimeout(function(){
					if(!LoadAreaData())
					{
						setTimeout(arguments.callee, 100);
					}
					else
					{
						setTimeout(function(){
							if(!GetJoinType()) {
								setTimeout(arguments.callee, 100);
							}
							else {
								setTimeout(function(){
									if(!GetPurposeType()) {
										setTimeout(arguments.callee, 100);
									}
									else {
										setTimeout(function(){
											if(!GetUserData()) {
											}
											else {
												setTimeout(function(){
													if(!LoadMyPopularity(0)) {
													}
													else {
														setTimeout(function(){
															if(!GetRecommendedPlace()) {
															}
															else {
																$('#functionButtons').load('functions.html');
																$('#menuScript').load('functionsEnabled.html');
																ResumeRayout();
																loaded = true;
															}
														}, 100);
													}
												}, 100);
											}
										}, 100);
									}
								}, 100);
							}
						}, 100);
					}
				}, 100);
			}
		}, 100);

		// ボタンを押下可能に
		document.sendData.elements["SendPopularity"].disabled = false;
		document.sendData.elements["SendCancel"].disabled = false;
		
		
		return false;
	});
	

	// ========================
	//  おススメ場所のロード
	// ========================
	function LoadFunctionButtons()
	{
		$(window).load(function() {
			
			return true;
		});
	}
	
</script>
</head>
<body>
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
	
	// ===================
	// 前の日付表示
	// ===================
	$(function() {
		$("#prevDate").click(function(){
			if(loaded == false){
				return;
			}
			
			// 描画を停止
			SuspendRayout();
			
			var addDays = getCookie("ADD_DAYS");
			addDays = Number(addDays) - 1;
			setCookie("ADD_DAYS", addDays);

			setTimeout(function(){
				if(!GetEventData(addDays)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetRecommendedPlace()) {
							setTimeout(arguments.callee, 100);
						}
						else {
							setTimeout(function(){
								if(!LoadPopularity(addDays)) {
								}
								else {
									setTimeout(function(){
										if(!LoadMyPopularity(addDays)) {
										}
										else {
											// 描画再開
											ResumeRayout();
										}
									}, 100);
								}
							}, 100);
						}
					}, 100);
				}
			}, 100);
		});
   });


	// ===================
	// 10日前の日付表示
	// ===================
	$(function() {
		$("#prev10Date").click(function(){
			if(loaded == false){
				return;
			}
			
			// 描画を停止
			SuspendRayout();
			
			var addDays = getCookie("ADD_DAYS");
			addDays = Number(addDays) - 10;
			setCookie("ADD_DAYS", addDays);

			setTimeout(function(){
				if(!GetEventData(addDays)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetRecommendedPlace()) {
							setTimeout(arguments.callee, 100);
						}
						else {
							setTimeout(function(){
								if(!LoadPopularity(addDays)) {
								}
								else {
									setTimeout(function(){
										if(!LoadMyPopularity(addDays)) {
										}
										else {
											// 描画再開
											ResumeRayout();
										}
									}, 100);
								}
							}, 100);
						}
					}, 100);
				}
			}, 100);
		});
   });


	// ===================
	// 次の日付表示
	// ===================
	$(function() {
		$("#nextDate").click(function(){
			if(loaded == false){
				return;
			}
			
			SuspendRayout();
			
			var addDays = getCookie("ADD_DAYS");
			var addDays = Number(addDays) + 1;
			setCookie("ADD_DAYS", addDays);

			setTimeout(function(){
				if(!GetEventData(addDays)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetRecommendedPlace()) {
							setTimeout(arguments.callee, 100);
						}
						else {
							setTimeout(function(){
								if(!LoadPopularity(addDays)) {
								}
								else {
									setTimeout(function(){
										if(!LoadMyPopularity(addDays)) {
										}
										else {
											// 描画再開
											ResumeRayout();
										}
									}, 100);
								}
							}, 100);
						}
					}, 100);
				}
			}, 100);
		});
	});


	// ===================
	// 10日送り
	// ===================
	$(function() {
		$("#next10Date").click(function(){
			if(loaded == false){
				return;
			}
			
			SuspendRayout();
			
			var addDays = getCookie("ADD_DAYS");
			var addDays = Number(addDays) + 10;
			setCookie("ADD_DAYS", addDays);

			setTimeout(function(){
				if(!GetEventData(addDays)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetRecommendedPlace()) {
							setTimeout(arguments.callee, 100);
						}
						else {
							setTimeout(function(){
								if(!LoadPopularity(addDays)) {
								}
								else {
									setTimeout(function(){
										if(!LoadMyPopularity(addDays)) {
										}
										else {
											// 描画再開
											ResumeRayout();
										}
									}, 100);
								}
							}, 100);
						}
					}, 100);
				}
			}, 100);
		});
	});

	// ====================
	//  日付移動メソッド
	// ====================
	function MoveDay(datetime){
		var todayDate = GetTodayAddDays(0);
		var diffDays = getDiff(todayDate, datetime);
		LoadPopularity(diffDays);

		setCookie("ADD_DAYS", diffDays, 0);
		return true;
	}
	
	// =========================================================
	//  投票
	// =========================================================
	$(function() {
		$("#SendPopularity").click(function(){
			
			var loginUserID = getCookie("USER_ID");
			var _addDays = getCookie("ADD_DAYS");

			if(_addDays < 0){
				alert('過去の日付に送信はできません');
				return;
			}
			
			var commentStr = $('#comment').val();
			if(commentStr.length > 50)
			{
				alert('コメントは 50文字以下 にしてください。');
				return;
			}
			
			// ボタンを押下不可に
			document.sendData.elements["SendPopularity"].disabled = true;
			document.sendData.elements["SendCancel"].disabled = true;
			
			
			var data = {
				placeID : $('#placeID').val(),
				placeName : $('#placeID option:selected').text(),
				userID : loginUserID,
				playerName : getCookie("USER_NAME"),
				joinType : $('#joinType').val(),
				joinText : $('#joinType option:selected').text(),
				purposeType : $('#purposeType').val(),
				purposeText : $('#purposeType option:selected').text(),
				RIP : getCookie("RIP"),
				characterID : getCookie("MAIN_CHARACTER_ID"),
				from : $('#datetime_from').val(),
				to : $('#datetime_to').val(),
				comment : $('#comment').val(),
				addDays : _addDays,
				withTweet : document.forms.sendData.withTweet.checked
			};

			$.ajax({
				type: "POST",
				url: "php/SendPopularity.php",
				data: data,
				success: function(jsonData){
					// 処理を記述
					alert("送信しました！");
					return false;
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					console.log('error : ' + errorThrown);
					return false;
				},

				complete: function()
				{
					$("#popularity_data").remove();
					LoadPopularity();
					
					// ボタンの状態を変更
					document.sendData.elements["SendPopularity"].disabled = false;
					document.sendData.elements["SendCancel"].disabled = false;
				}
			});

			return false;
		});
	});

	// ===========================
	// キャンセルボタン押下イベント
	// ===========================
	$(function() {
		$("#SendCancel").click(function(){
			var loginUserID = getCookie("USER_ID");

			document.sendData.elements["SendPopularity"].disabled = true;
			document.sendData.elements["SendCancel"].disabled = true;

			var data = {
				userID : loginUserID
			};

			$.ajax({
				type: "POST",
				url: "php/CancelPopularity.php",
				data: data,
				success: function(jsonData){
					// 処理を記述
					return false;
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					console.log('error : ' + errorThrown);
					return false;
				},

				complete: function(){
					$("#popularity_data").remove();
					LoadPopularity();

					document.sendData.elements["SendPopularity"].disabled = false;
					document.sendData.elements["SendCancel"].disabled = false;
			
				}
			});

			return false;
		});
	});

	// ============
	//  エリア変更
	// ============
	function ChangeArea(_areaID)
	{
		SuspendRayout();
		
		setCookie("AREA_ID", _areaID, 1);
		
		var addDays = getCookie("ADD_DAYS");


		setTimeout(function(){
			if(!LoadPlaceData()) {
				setTimeout(arguments.callee, 100);
			}
			else{
				setTimeout(function(){
					if(!GetEventData(parseInt(addDays)))
					{
						setTimeout(arguments.callee, 100);
					}
					else
					{
						setTimeout(function(){
							if(!LoadPopularity(parseInt(addDays)))
							{
								setTimeout(arguments.callee, 100);
							}
							else
							{
								setTimeout(function(){
									if(!GetRecommendedPlace())
									{
										setTimeout(arguments.callee, 100);
									}
									else
									{
										ResumeRayout();
									}
								},100);
							}
						}, 100);
					}
				}, 100);
			}
		}, 100);
	}
</script>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<!-- ここから下は書き換わる可能性がある -->
<div id="updatableContents">

<div id="main_header">
	<h1>どこＵＮＩ？</h1>
</div>

<div id="watchingDateWrapper">
	<p>
		<div id="prev10Date">&lt;</div>
		<div id="prevDate">&lt;</div>
		<div id="watchingDate" class="watchingDate"></div>
		<div id="nextDate">&gt;</div>
		<div id="next10Date">&gt;</div>
	</p>
</div>
<div id="watchingAreaWrapper">
	<p>
		<div id="watchingArea" class="watchingArea"></div>
	</p>
</div>

<div id="eventWrapper">
	<div id="event_title">直近イベント（タップで自動入力）</div>
	<div id="eventTableWrapper">
		<div id="event"></div>
	</div>
</div>

<div id="output"></div>
<div class="sendWrapper" id="sendDataForm">
		<span class="box-title">どこかに行く</span>
		<form id="sendData" name="sendData">
		<div>
		<p>
			<div>行く日：<span id="sent"></span></div>
			<div id="joinDate"></div>
		</p>
		<p>
			<label for="label_recommend_place" accesskey="n">この曜日に集まりやすい場所：</label>
			<div id="recommendPlace"></div>
		</p>
		<p>
			<label for="label_place" accesskey="n">場所：</label><br/>
			<select class="formComboBox" name="placeID" id="placeID"></select><br/>
			<span class="mini">表示されない場合は再ログインしてみてください。</span>
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
			<input type="time" id="datetime_from" name="datetime_from" class="from_to" />時 から<br/>
			<input type="time" id="datetime_to" name="datetime_to"  class="from_to" />時 まで
		</p>
		
		<p>
			<label for="label_comment" accesskey="n">コメント：</label><br/>
			<textarea row="3" name="comment" id="comment" style="height:3.5em; width:60%"></textarea>（50文字以内）
		</p>
				
		<p>
			<input type="checkbox" id="withTweet" name="withTweet" />ツイートする
		</p>
		
		<button type="submit" class="noBorderButton" id="SendPopularity" name="SendPopularity" onsubmit="return false;">
			<a class="btn" href="javascript:void(0)">送信</a>
		</button>
		<button type="submit" class="noBorderButton" id="SendCancel" name="SendCancel" onsubmit="return false;">
			<a class="btn" href="javascript:void(0)">行くのやめた</a>
		</button>
		</div>
	</form>
</div>

<div id="noLoggedinFunctions">
	<p>※参加投票はログインしないと使用できません。</p>
	<button type="submit" class="noBorderButton" id="MoveLoginPage" name="MoveLoginPage" onsubmit="return false;">
		<a class="btn" href="javascript:void(0)">ログイン</a>
	</button>
</div>

</div>
<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
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



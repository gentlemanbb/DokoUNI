
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css"/>

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/modal.js"></script>
<script src="js/Functions.js?ver=201901"></script>
<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>

<script>
	var loaded;

	// ====================
	// 詳細画面へ移動
	// ====================
	function MoveToDetail(placeID)
	{
		setCookie("PLACE_ID", placeID, 1);
		window.location.href = "place_detail.php?placeID=" + placeID;
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
	function GetCurrentData(_recentTime)
	{
		loaded = false;
		document.getElementById("output").innerHTML = "";
		document.getElementById("watchingHour").innerHTML = _recentTime + "時間以内のプレイ履歴";
		var args = {
			areaID : getCookie("AREA_ID"),
			recentTime : _recentTime
		};

		$.ajax({
			type: "POST",
			url: "php/GetCurrentData.php",
			data: args,
			success: function(data)
			{
				if(data == false)
				{
				}
				var isFirst = true;
				var rowCnt = 0;
				var colCnt = 0;

				$("#output").append('<table id="popularity_data">');
				$("#popularity_data").append('<thead id="popularity_header">');
				$("#popularity_header").append('<tr id="popularity_tr">');
				$("#popularity_tr").append('<th class="header1">場所</th>');
				$("#popularity_tr").append('<th class="header2">人数</th>');
				$("#popularity_header").append('</tr>');
				$("#popularity_data").append('</thead>');
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
					$('#row' + rowCnt).append("<td>" + value.PLAY_COUNT + "人</td>");
				});
				
				$("#popularity_body").append('</tbody>');
				$("#output").append("</table>");
				
				// デフォルトの設定を変更
				$.extend( $.fn.dataTable.defaults, { 
					language: {
						url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json"
					} 
				}); 
				
				$("#popularity_data").dataTable({
					// 件数切替機能 有効
					lengthChange: true,
					
					// 検索機能 有効
					searching: true,
					
					// ソート機能 有効
					ordering: true,
					
					// 情報表示 有効
					info: true,
					
					// ページング機能 有効
					paging: true,
					
					order: [ [ 1, "desc"] ],
					
					// 件数切替の値を 5,10,15,20 刻みにする
					lengthMenu: [ 5, 7, 10, 15, 20 ],
					
					// 件数のデフォルトの値を 7 にする
					displayLength: 7
				});
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
	function LoadMyPopularity()
	{
		var userID = getCookie("USER_ID");
		data = GetMyPopularity(userID, 0);
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
	function LoadPlaceData()
	{
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

		var groups = null;
		var groups = GetGroup(userID);

		var args = {
			userID : userID,
		};

	   $.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetUserData.php",
			data: args,
			timeout: 2000,
			success: function(data)
			{
				data.forEach(function(value){
					// クッキー設定
					setCookie("USER_NAME", value.USER_NAME, 1);
					setCookie("MAIN_CHARACTER_ID", value.MAIN_CHARACTER_ID, 1);
					setCookie("RIP", value.RIP, 1);
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
	$(document).ready(function()
	{
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
		setCookie("ADD_HOUR", 3);
		
		// 本日のデータを取得
		GetCurrentData(3);
		
		setTimeout(function(){
			if(!LoadPlaceData()) {
				setTimeout(arguments.callee, 100);
			}
			else {
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
											if(!LoadMyPopularity()) {
											}
											else {
												setTimeout(function(){
													if(!LoadAreaData())
													{
														setTimeout(arguments.callee, 100);
													}
													else
													{
														$('#functionButtons').load('functions.html');
														$('#menuScript').load('functionsEnabled.html');
														ResumeRayout();
														loaded = true;
													}
												},100);
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
	//  投票
	// ===================
	$(function() {
		$("#SendPopularity").click(function(){
			
			var loginUserID = getCookie("USER_ID");
			var _addDays = 0;
			
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
				addDays : _addDays,
				comment : $('#comment').val(),
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

				complete: function(){
					$("#popularity_data").remove();
					GetCurrentData(getCookie("ADD_HOUR"));
					
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
					GetCurrentData(3);

					document.sendData.elements["SendPopularity"].disabled = false;
					document.sendData.elements["SendCancel"].disabled = false;
			
				}
			});

			return false;
		});
	});

	// ===================
	// 前の時間表示
	// ===================
	$(function() {
		$("#prevHour").click(function(){
			if(loaded == false){
				return;
			}
			
			// 描画を停止
			SuspendRayout();
			
			// 現在の時間を取得
			var addHour = getCookie("ADD_HOUR");
			
			if(addHour <= 1)
			{
				// これ以上下げられない場合は処理を中断
				ResumeRayout();
				return;
			}
			
			// 1時間戻す
			addHour = Number(addHour) - 1;
			
			// 時間を再設定
			setCookie("ADD_HOUR", addHour);

			setTimeout(function(){
				if(!GetCurrentData(addHour)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					ResumeRayout();
				}
			}, 100);
		});
   });

	// ===================
	// 次の時間表示
	// ===================
	$(function() {
		$("#nextHour").click(function(){
			if(loaded == false){
				return;
			}
			
			// 描画を停止
			SuspendRayout();
			
			// 現在の時間を取得
			var addHour = getCookie("ADD_HOUR");
			
			if(addHour >= 24)
			{
				// 24時間以上はいらないでしょ
				ResumeRayout();
				return;
			}
			
			// 1時間戻す
			addHour = Number(addHour) + 1;
			
			// 時間を再設定
			setCookie("ADD_HOUR", addHour);

			setTimeout(function(){
				if(!GetCurrentData(addHour)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					ResumeRayout();
				}
			}, 100);
		});
   });

	// ===================
	// 3時間
	// ===================
	$(function() {
		$("#3Hour").click(function(){
			if(loaded == false){
				return;
			}
			
			// 描画を停止
			SuspendRayout();
			
			// 現在の時間を取得
			var addHour = 3;
			
			// 時間を再設定
			setCookie("ADD_HOUR", addHour);

			setTimeout(function(){
				if(!GetCurrentData(addHour)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					ResumeRayout();
				}
			}, 100);
		});
   });
	
	// ======
	// 12時間
	// ======
	$(function() {
		$("#12Hour").click(function(){
			if(loaded == false){
				return;
			}
			
			// 描画を停止
			SuspendRayout();
			
			// 現在の時間を取得
			var addHour = 12;
			
			// 時間を再設定
			setCookie("ADD_HOUR", addHour);

			setTimeout(function(){
				if(!GetCurrentData(addHour)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					ResumeRayout();
				}
			}, 100);
		});
   });
  	
	// ======
	// 24時間
	// ======
	$(function() {
		$("#24Hour").click(function(){
			if(loaded == false){
				return;
			}
			
			// 描画を停止
			SuspendRayout();
			
			// 現在の時間を取得
			var addHour = 24;
			
			// 時間を再設定
			setCookie("ADD_HOUR", addHour);

			setTimeout(function(){
				if(!GetCurrentData(addHour)) {
					setTimeout(arguments.callee, 100);
				}
				else {
					ResumeRayout();
				}
			}, 100);
		});
   });
    
	// ============
	//  エリア変更
	// ============
	function ChangeArea(_areaID)
	{
		SuspendRayout();
		
		// 現在の時間を取得
		var addHour = getCookie("ADD_HOUR");
		
		setCookie("AREA_ID", _areaID, 1);
		
		var addDays = getCookie("ADD_DAYS");


		setTimeout(function(){
			if(!LoadPlaceData()) {
				setTimeout(arguments.callee, 100);
			}
			else{
				setTimeout(function(){
					if(!GetCurrentData(addHour))
					{
						setTimeout(arguments.callee, 100);
					}
					else
					{
						ResumeRayout();
					}
				}, 100);
			}
		}, 100);
	}
</script>

</head>

<body>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<!-- ここから下は書き換わる可能性がある -->
<div id="updatableContents">

<div id="main_header">
	<h1>どこＵＮＩ？<img  id="OpenTwitterModal" src="img/buttons/twitter32.png"></h1>
</div>

<div id="wachingHour">
	<p>
		<span id="prevHour"><<</span>
		<span id="watchingHour">3</span>
		<span id="nextHour">>></span>
	</p>
	<p>
		<button class="noBorderButton"><span id="3Hour" class="btn3">3h</span></button>
		<button class="noBorderButton"><span id="12Hour" class="btn3">12h</span></button>
		<button class="noBorderButton"><span id="24Hour" class="btn3">24h</span></button>
	</p>
</div>
<div id="watchingAreaWrapper">
	<p>
		<div id="watchingArea" class="watchingArea"></div>
	</p>
</div>
<div id="output"></div>
<br/>
<div class="sendWrapper" id="sendDataForm">
		<span class="box-title">どこかに行く</span>
		<form id="sendData" name="sendData">
		<p>
			<div id="sent"></div>
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
</body>



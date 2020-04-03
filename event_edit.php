
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/ValidationUtil.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Functions.js?ver=201812060004"></script>

<script>

	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		SuspendRayout();
		setTimeout(function()
		{
			if(!GetPlace()) {
				setTimeout(arguments.callee, 100);
			}
			else {
				setTimeout(function()
				{
					if(!LoadEventData())
					{
						setTimeout(arguments.callee, 100);
					}
					else {
						$('#functionButtons').load('functions.html');
						$('#menuScript').load('functionsEnabled.html');
						ResumeRayout();
					}
				}, 100);
			}
		});
		var now = new Date();

		var year = now.getFullYear();
		var month = now.getMonth() + 1;
		if(String(month).length == 1){
			month = "0" + month;
		}
		
		var day = now.getDate();
		if(String(day).length == 1){
			day = "0" + day;
		}
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

		document.sendData.eventDate.value = year + "-" + month + "-" + day;

		document.sendData.datetimeFrom.value = hour + ":" + min + ":00";
		document.sendData.datetimeTo.value = hour + ":" + min + ":00";
	});

	function GetPlace(){
		var argData ={
			eventID : getCookie("EVENT_ID"),
			areaID : getCookie("AREA_ID")
		}
		
		$.ajax({
			type: "POST",
			url: "php/GetPlaceAPI.php",
			data : argData,
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
			}
		});

		return true;
	}

	function LoadEventData(){
		var argData ={
			eventID : getCookie("EVENT_ID"),
			areaID : getCookie("AREA_ID")
		}
		
		$.ajax({
			type: "POST",
			url: "php/GetEventDetailAPI.php",
			data : argData,
			success: function(data)
			{
				document.getElementById("uploadedImage").innerHTML = "";
				
				data.forEach(function(value){
					if(value.PLACE_ID != null){
						document.sendData.placeID.value = value.PLACE_ID;
					}
					document.sendData.eventName.value = value.EVENT_NAME;
					document.sendData.comment.value = value.COMMENT;
					document.sendData.eventDate.value = value.EVENT_DATE;
					document.sendData.datetimeFrom.value =  value.EVENT_TIME_FROM;
					document.sendData.datetimeTo.value = value.EVENT_TIME_TO;
					
					if(value.IMAGE_PATH != null && value.IMAGE_PATH != "")
					{
						var timestamp = Date.now();
						document.getElementById("uploadedImage").innerHTML = '<img src="php/' +  value.IMAGE_PATH + '?ver=' + timestamp + '" style="width:100%;">';
						document.sendData.imagePath.value = value.IMAGE_PATH;
						$('img').error(function(){$(this).attr('src', 'upload/no_image.jpg');});
					}else
					{
						document.getElementById("uploadedImage").innerHTML = '<img src="upload/no_image.jpg" style="width:100%;">';
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

	$(document).ready(function(){
		$('#BtnEventUpdate').click(function(){
			var argData = {
				eventID :getCookie("EVENT_ID"),
				userID :getCookie("USER_ID"),
				userName :getCookie("USER_NAME"),
				eventName : $('#eventName').val(),
				placeID :$('#placeID').val(),
				placeName :$('#placeName').val(),
				comment :$('#comment').val(),
				eventDate :$('#eventDate').val(),
				eventTimeFrom :$('#datetimeFrom').val(),
				eventTimeTo :$('#datetimeTo').val()
			};

			// バリデーションの代わり
			if(argData.eventName.length < 2){
				alert("イベント名は2文字以上にしなければいけません");
				return false;
			}

			$.ajax({
				type: "POST",
				dataType: 'json',
				url: "php/UpdateEventAPI.php",
				data: argData,

				success: function(data, dataType)
				{
					// ログイン結果
					if(data == true){
						alert("イベントを更新しました");
						window.location.href = "all_event.php";
					}
					else{
						alert("イベント更新に失敗しました。管理者に問い合わせてください。");
					}
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					alert('error : ' + errorThrown);
				}

			});

			return false;
		});
	});


	$(document).ready(function(){
		$('#BtnEventDelete').click(function(){
			var argData = {
				eventID :getCookie("EVENT_ID")
			};

			$.ajax({
				type: "POST",
				dataType: 'json',
				url: "php/DeleteEventAPI.php",
				data: argData,

				success: function(data, dataType)
				{
					// ログイン結果
					if(data == true){
						alert("イベントを削除しました");
						window.location.href = "all_event.php";
					}
					else{
						alert("イベント削除に失敗しました。管理者に問い合わせてください。");
					}
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					alert('error : ' + errorThrown);
				}

			});

			return false;
		});
	});


	$(document).ready(function(){
		$('#BtnEventCopy').click(function(){
		
			var argData = {
				userID			:getCookie("USER_ID"),
				userName		:getCookie("USER_NAME"),
				eventName		:$('#eventName').val(),
				placeID			:$('#placeID').val(),
				placeName		:$('#placeID option:selected').text(),
				comment			:$('#comment').val(),
				eventDate		:$('#eventDate').val(),
				eventTimeFrom	:$('#datetimeFrom').val(),
				eventTimeTo		:$('#datetimeTo').val(),
				imagePath		:$('#imagePath').val()
			};

			// バリデーションの代わり
			if(argData.eventName.length < 2)
			{
				alert("イベント名は2文字以上にしなければいけません");
				return false;
			}

			$.ajax({
				type: "POST",
				dataType: 'json',
				url: "php/RegistEventAPI.php",
				data: argData,

				success: function(data, dataType)
				{
					if(data == false)
					{
						alert("イベントのコピーに失敗しました");
					}
					else
					{
						alert("イベントをコピーしました");
					}
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					alert('error : ' + errorThrown);
				}

			});

			return false;
		});
	});

	$(document).ready(function(){
		$('#BtnEventTweet').click(function(){
		
			var argData = {
				eventID :getCookie("EVENT_ID")
			};
			
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: "php/TweetEventDetailAPI.php",
				data: argData,

				success: function(data, dataType)
				{
					// ログイン結果
					if(data == true){
						alert("ツイートしました");
						window.location.href = "where.php";
					}
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					alert('error : ' + errorThrown);
				}

			});

			return false;
		});
	});

	// ==========================
	//  簡易的に描画を停止します
	// ==========================
	function ChangeFile()
	{
		//フォームのデータを変数formに格納
		var form = $('#sendData').get()[0];
						
		//FormData オブジェクトを作成
		var formData = new FormData(form);
		var eventID = getCookie("EVENT_ID");

		var newImagePath = UploadEventImage(formData, eventID);
		document.getElementById("uploadedImage").innerHTML = '<img src="' + newImagePath + '" style="width:100%;">';
	}

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
	
</script>
<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
</head>
<body>
	<div id="functionButtons"></div>
	<div id="menuScript"></div>

	<div id="main_header">
		<h1>どこＵＮＩ？</h1>
	</div>
	<div class="sendWrapper">
		<span class="box-title">新規イベント登録</span>
		<form method="post" id="sendData" name="sendData">
		<div>
			<p>
				<label for="label_event_name" accesskey="n">イベント名：</label><br/>
				<input type="text"  id="eventName" cols="20" rows="1"></textarea>
			</p>
			<p>
				<label for="label_place" accesskey="n">場所：</label><br/>
				<select class="formComboBox" name="placeID" id="placeID">
				</select>
			</p>
			<p>
				<label for="label_comment" accesskey="n">コメント：</label><br/>
				<input type="text" name="comment" id="comment" cols="20" rows="1"></textarea>
			</p>
			<p>
				<label for="label_event_date" accesskey="n">日程：</label><br/>
				<input type="date" id="eventDate" name="eventDate" class="from_to"><br/>
			</p>
			<p>
				<label for="label_date_from_to" accesskey="n">参加時刻（FROM - TO)：</label><br/>
				<input type="time" id="datetimeFrom" name="datetimeFrom" class="from_to">時 から<br/>
				<input type="time" id="datetimeTo" name="datetimeTo"  class="from_to">時 まで
			</p>
			
			<div id="uploadedImage" class="topic"></div>
			<input type="text" name="imagePath" id="imagePath" cols="20" rows="1" readonly></textarea>

			<p>
				<input type="file" name="file_name" onchange="ChangeFile()" />
			</p>

			<p>
				<button type="submit" class="noBorderButton" id="BtnEventUpdate" name="BtnEventUpdate" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">更新</a>
				</button>
				<button class="noBorderButton" id="BtnEventDelete" name="BtnEventDelete" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">削除</a>
				</button>
				<button class="noBorderButton" id="BtnEventCopy" name="BtnEventCopy" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">コピー</a>
				</button>
				<button class="noBorderButton" id="BtnEventTweet" name="BtnEventTweet" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">ツイートする</a>
				</button>
				<button class="noBorderButton" type="submit" id="MoveAllData"  name="MoveAllData">
					<a class="btn" href="all_event.php">一覧に戻る</a>
				</button>
			</p>
		</div>
	</form>
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
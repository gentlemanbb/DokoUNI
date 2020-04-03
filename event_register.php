
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/ValidationUtil.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/modal.js"></script>
<script src="js/Functions.js?ver=201901052"></script>

<script>

	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		SuspendRayout();
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

		document.sendData.datetime_from.value = hour + ":" + min + ":00";
		document.sendData.datetime_to.value = hour + ":" + min + ":00";

		setTimeout(function(){
			if(!GetPlace()) {
			}
			else {
				$('#functionButtons').load('functions.html');
				$('#menuScript').load('functionsEnabled.html');
				ResumeRayout();
			}
		}, 100);
		

	});

	function GetPlace(){
		var args = {
			 areaID : getCookie('AREA_ID'),
		};

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
			}
		});

		return true;
	}

	$(document).ready(function()
	{
		$('#BtnEventRegist').click(function()
		{
			var userID = getCookie("USER_ID");
			var userName = getCookie("USER_NAME");
			var eventName = $('#eventName').val();
			var placeID = $('#placeID').val();
			var placeName = $('#placeID option:selected').text();
			var comment = $('#comment').val();
			var eventDate = $('#eventDate').val();
			var eventTimeFrom = $('#datetime_from').val();
			var eventTimeTo = $('#datetime_to').val();
			
			// バリデーションの代わり
			if(eventName.length < 2)
			{
				alert("イベント名は2文字以上にしなければいけません");
				return false;
			}
			
			var registResult;

			if(document.sendData.weeklyEvent.checked == true)
			{
				var date = new Date(eventDate);
				var year  = date.getFullYear(); //年
				var month = date.getMonth() + 1;    //月

				registResult = RegistWeeklyEvent(userID, eventName, eventDate, year, month, placeID, placeName, comment, eventTimeFrom, eventTimeTo);
			}
			else
			{
				registResult = RegistEvent(userID, userName, eventName, placeID, placeName, comment, eventDate, eventTimeFrom, eventTimeTo);
			}

			// 呼び出し結果
			if(registResult == null)
			{
				alert("イベントの作成に失敗しました");
			}
			else
			{
				if(document.sendData.file_name.value)
				{
					if(Array.isArray(registResult))
					{
						registResult.forEach(function(day)
						{
							//フォームのデータを変数formに格納
							var form = $('#sendData').get()[0];
								
							//FormData オブジェクトを作成
							var formData = new FormData(form);
							UploadEventImage(formData, day);
						});
					}
					else
					{
						//フォームのデータを変数formに格納
						var form = $('#sendData').get()[0];
							
						//FormData オブジェクトを作成
						var formData = new FormData(form);
						UploadEventImage(formData, registResult);
					}
				}

				alert("イベントを作成しました");
				window.location.href = "all_event.php";
			}

			return false;
		});
	});

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
	<h1>どこＵＮＩ？<img  id="OpenTwitterModal" src="img/buttons/twitter32.png"></h1>
</div>
	<div class="sendWrapper">
		<span class="box-title">新規イベント登録</span>
		<form method="post" id="sendData" name="sendData" enctype="multipart/form-data">
		<div>
			<input type="hidden" id="userID" />
			<input type="hidden" id="userName" />
			<p>
				<label for="label_event_name" accesskey="n">イベント名：</label><br/>
				<input type="text"  id="eventName" cols="20" rows="1">
			</p>
			<p>
				<label for="label_place" accesskey="n">場所：</label><br/>
				<select class="formComboBox" name="placeID" id="placeID">
				</select>
			</p>
			<p>
				<label for="label_comment" accesskey="n">コメント：</label><br/>
				<textarea  id="comment" cols="40" rows="3"></textarea>
			</p>
			<p>
				<label for="label_event_date" accesskey="n">日程：</label><br/>
				<input type="date" id="eventDate" name="eventDate" class="from_to"><br/>
			</p>
			<p>
				<label for="label_date_from" accesskey="n">開始時刻：</label><input type="time" id="datetime_from" name="datetime_from" class="from_to"/> <br/>
				<label for="label_date_to" accesskey="n">終了時刻：</label><input type="time" id="datetime_to" name="datetime_to"  class="from_to"/> <br/>
			</p>
			
			<p>
				<label for="label_image_file" accesskey="n">宣伝画像：</label><br/>
				<input type="file" name="file_name" />
			</p>

			<p>
				<input type="checkbox" name="weeklyEvent"/>同月の同曜日に一括登録
			</p>
			
			<p>
				<button type="submit" class="noBorderButton" id="BtnEventRegist" name="BtnEventRegist" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">登録</a>
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
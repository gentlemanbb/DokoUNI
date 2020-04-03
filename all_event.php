
<!DOCTYPE html>
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css?ver=6" type="text/css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css"/>

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Functions.js?<?php echo date('Ymd-His'); ?>"></script>
<script src="js/html2canvas.js"></script>
<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>
<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>

<script>
	var loaded;

	// =====================
	//  データのロード
	// =====================
	function LoadEvent(areaID)
	{
		try
		{
			document.getElementById("output").innerHTML = "";
			
			// ユーザＩＤ取得
			var userID = getCookie("USER_ID");

			var eventData = GetEventData(0, 31, '1', userID);
			
			// イベントを出力
			if(eventData != null && eventData.length > 0)
			{
				document.getElementById("output").innerHTML = `
					<table id="event_list" class="event">
						<thead>
							<tr>
								<th>イベント名</th>
								<th>日時</th>
								<th>編集</th>
							</tr>
						</thead>
						<tbody id="event_tbody">
						
						</tbody>
					</table>

				`;
				var tbodyElement = document.getElementById("event_tbody");
				
				eventData.forEach(function(event)
				{
					var rowElement = document.createElement("tr");
					var eventNameElement = document.createElement("td");
					var eventDateTimeElement = document.createElement("td");
					var eventEditButtonElement = document.createElement("td");
					
					rowElement.setAttribute("id", event.EVENT_ID);
					tbodyElement.appendChild(rowElement);
					
					var trElement = document.getElementById(event.EVENT_ID);

					eventNameElement.innerHTML = event.EVENT_NAME;
					eventDateTimeElement.innerHTML = event.EVENT_DATE + "<br/>" + event.EVENT_TIME_FROM + "-" + event.EVENT_TIME_TO;
					eventEditButtonElement.innerHTML = "<button onClick='MoveEventEditPage(" + event.EVENT_ID + ")'>編集</button>";
					
					trElement.appendChild(eventNameElement);
					trElement.appendChild(eventDateTimeElement);
					trElement.appendChild(eventEditButtonElement);
					

				});
			}
			return true;
		}
		catch
		{
			alert('error');
		}
	}
	
	function MoveEventEditPage(eventID_)
	{
		setCookie("EVENT_ID", eventID_);
		
		SuspendRayout();
		window.location.href = "event_edit.php";
		ResumeRayout();
		
		return false;
	}

	function MoveEventRegistPage(eventID_)
	{
		SuspendRayout();
		window.location.href = "event_register.php";
		ResumeRayout();
		
		return false;
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
	
	// ===================
	// ロード処理
	// ===================
	$(document).ready(function()
	{
		var areaID = getCookie("AREA_ID");
		// 描画を停止
		SuspendRayout();
		
		// ロードを順順に行う
		let promise = new Promise((resolve, reject) => {
			setTimeout(() => {
				resolve(LoadEvent(1));
			}, 100);
		});
		
		promise.then((result) => {
			return new Promise((resolve, reject) => {
				setTimeout(() => {
					$('#functionButtons').load('functions.html');
					$('#menuScript').load('functionsEnabled.html');
					return true;
				}, 100);
			});
		}).then((result) => {
			return new Promise((resolve, reject) => {
				setTimeout(() => {
					resolve(LoadPlaceData());
				}, 100);
			});
		}).catch(() => {
			// エラーハンドリング
			alert('ロード処理でエラーが発生しました。');
		});
		
		ResumeRayout();
		return false;
	});
</script>

</head>

<body>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<!-- ここから下は書き換わる可能性がある -->
<div id="updatableContents">

<div id="main_header">
	<h1>どこＵＮＩ？</h1>
</div>

<div id="eventListWrapper">
	<div id="output"></div>
	<div ip="event_regist">
		<button onClick="MoveEventRegistPage()">
			新規登録
		</button>
	</div>
</div>

</div>

<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
<br/>
</body>
</html>

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
<script src="js/Functions.js?ver=3"></script>
<script src="js/html2canvas.js"></script>
<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>

<script>
	var loaded;

	// =====================
	//  データのロード
	// =====================
	function GetEventData(areaID)
	{
		// ユーザＩＤ取得
		var userID = getCookie("USER_ID");

		var eventData = GetMonthlyEvent(areaID, userID);

		var date = new Date();
		var maxDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
		var maxDay = maxDate.getDate();

		$("#output").append('<table id="event_calendar"></table>');
		$("#event_calendar").append('<tr id="calendar_header"></tr>');
		$("#calendar_header").append('<th class="calendar_header Sunday">日</th>');
		$("#calendar_header").append('<th class="calendar_header Monday">月</th>');
		$("#calendar_header").append('<th class="calendar_header Tuesday">火</th>');
		$("#calendar_header").append('<th class="calendar_header Wednesday">水</th>');
		$("#calendar_header").append('<th class="calendar_header Thursday">木</th>');
		$("#calendar_header").append('<th class="calendar_header Friday">金</th>');
		$("#calendar_header").append('<th class="calendar_header Saturday">土</th>');
		$("#event_calendar").append('<tbody id="calendar_body"></tbody>');

		var rowHeight = Math.ceil(maxDay / 7) + 1;
		var dayCnt = 1;
		var isFirst = true;
		for(var i = 1; i <= rowHeight; i++)
		{
			$("#calendar_body").append('<tr id="rowCnt' + i + '"></tr>');	
			var rowID = '#rowCnt' + i;
			var skipCnt = 0;
			for(var j = 1; j <= 7; j++)
			{
				if(maxDay < dayCnt)
				{
					break;
				}

				var date = new Date();
				var currentDate = new Date(date.getFullYear(), date.getMonth(), dayCnt);
				var weekDay = currentDate.getDay();

				if(isFirst)
				{
					for(var k = 0; k < weekDay; k++)
					{
						$(rowID).append('<td class="calendar_cell"></td>');
						skipCnt = skipCnt + 1;
					}
					isFirst = false;
				}
				

				if((skipCnt+j) > 7)
				{
					continue;
				}

				$(rowID).append('<td id="day' + dayCnt + '" class="calendar_cell"><div class="calendar_day">' + dayCnt + '</div></td>');
				var celID = "#day" + dayCnt;

				$(celID).append('<div id="event' + dayCnt + '" class="calendar_event"></td>');
				dayCnt = dayCnt + 1;
			}
		}

		if(eventData != null && eventData.length > 0)
		{
			eventData.forEach(function(value)
			{
				var date = new Date('2019-' +value.EVENT_DATE);
				var dateID = '#event' + date.getDate();
				$(dateID).append(
					' <div class="calendar_place_name1" style="color:red">' + value.PLACE_NAME + '</div>' +
					'<div class="calendar_event_name1">' + value.EVENT_NAME + '</div>'
				);
			});
		}
		return true;
	}

	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		var areaID = getCookie("AREA_ID");

		SuspendRayout();

		GetEventData(areaID);

		LoadFunctionButtons();

		ResumeRayout();

		return false;
	});

	function ExecuteAndWait(callback)
	{
		setTimeout(function()
		{
			if(!callback())
			{
				setTimeout(arguments.callee, 100);
			}
			else
			{
				return true;
			}
		}, 100);
	}

	// ========================
	//  おススメ場所のロード
	// ========================
	function LoadFunctionButtons()
	{
		$(window).load(function() {
			
			return true;
		});
	}
		
	// ==========================
	//  描画を再開します
	// ==========================
	function CreateImage()
	{
		var img_url;
		html2canvas(document.querySelector("#output")).then(canvas => 
		{
			document.body.appendChild(canvas)
			img_url = canvas.toDataURL("image/png").replace(new RegExp("data:image/png;base64,"),"");
			$.post("php/TweetCalendarAPI.php",{"upload_data":img_url},function(data)
			{
				alert("DONE");
			},"html");
		});
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

<div id="output"></div>
<div id="result"></div>

<button onClick="CreateImage(); return false;">カレンダーをツイート</button>

</div>

<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
<br/>
</body>
</html>



<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>

<script>
	// ========================
	//  場所データの取得
	// ========================
	function LoadUnknownPlace(){
		var args = {
			 areaID : 99,
		};

		$.ajax({
			type: "POST",
			url: "php/GetPlaceAPI.php",
			data: args,
			success: function(data)
			{
				var rowCnt = 0;
				
				$("#output").append('<table id="place_data">');
				$("#place_data").append('<tr id="place_header">');
				$("#place_header").append('<th class="placeHeader1">場所</th>');
				$("#place_header").append('<th class="placeHeader2"></th>');
				$("#place_data").append('</tr>');
				$("#place_data").append('<tbody id="place_body">');
				
				var beforeDatetime;
				
				data.forEach(function(value)
				{
					// 行を変更する
					$("#place_body").append('<tr class="place_tr" id="row' + rowCnt + '">');
					if(value.PLACE_NAME != null)
					{
						$("#row" + rowCnt).append('<td class="place_td">' + value.PLACE_NAME + '</td>');
						$("#row" + rowCnt).append('<td class="place_td"><button onClick="MoveToEventEdit(' + value.PLACE_ID + ')">ｼｯﾃﾙ</button></td>');
					}
					
					$("#place_body").append('</tr>');
					
					rowCnt += 1;
				});
				
				$("#place_body").append('</tbody>');
				$("#output").append("</table>");
				
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});
		
		return true;
	}
	
	// =========================
	//  場所編集クリック
	// =========================
	function MoveToEventEdit(_placeID){
		setCookie('PLACE_ID', _placeID, '1');

		window.location.href = "area_select.php";
	}
	
	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function(){
		setTimeout(function(){
			if(!LoadUnknownPlace()) {
			
			}
			else
			{
				$('#functionButtons').load('functions.html');
				$('#menuScript').load('functionsEnabled.html');
				ResumeRayout();
			}
		}, 100);
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
<body>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<div id="main_header">
	<h1>どこＵＮＩ？</h1>
</div>

<div class="sendWrapper" id="sendDataForm">
	<span class="box-title">
		未分類ゲームセンター一覧
	</span>
		<p class="topic">
			ここに表示されているゲーセンは『地域』が未分類です。知っているゲーセンがある場合、『知ってる』から、地域選択をお願いします。
		</p>
<div id="output"></div>
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

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
<script src="js/Functions.js"></script>

<script>
	// ========================
	//  ランキングデータのロード
	// ========================
	function GetUserRanking(_addDays){
		var args = {
			addDays : _addDays,
		};
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetUserRankingAPI.php",
			data: args,
			success: function(data)
			{
				var isFirst = true;
				var rowCnt = 0;
				var colCnt = 0;
				document.getElementById("output").innerHTML = "";
				
				if(data.length > 0)
				{
					$("#output").append('<table class= "ranking" id="ranking_data">');
					$("#ranking_data").append('<tr id="ranking_header">');
					$("#ranking_header").append('<th class="rankingHeader1">回数</th>');
					$("#ranking_header").append('<th class="rankingHeader2">プレーヤー名</th>');
					$("#ranking_data").append('</tr>');
					$("#ranking_data").append('<tbody id="ranking_body">');
					
					data.forEach(function(value){
						// 1行目以外は</tr>で〆る
						if(!isFirst){
							$("#ranking_body").append("</tr>");
							rowCnt += 1;
						}
						else
						{
							// 1行目フラグをおろす
							isFirst = false;
						}
						
						// 行を変更する
						$("#ranking_body").append('<tr id="ranking_row' + rowCnt + '" class="ranking_tr">');
						$('#ranking_row' + rowCnt).append('<td>' + value.POPULARITY_COUNT + '</td>');
						$('#ranking_row' + rowCnt).append("<td>" + value.PLAYER_NAME + "</td>");
					});
					
					$("#ranking_body").append('</tbody>');
					$("#output").append("</table>");				}
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
    //  ロード完了時イベント
    // =========================
    $(document).ready(function()
    {
    	// 描画の停止
    	SuspendRayout();
    	
		setTimeout(function(){
			if(!GetUserRanking(-365)) {
			}
			else {
				$('#functionButtons').load('functions.html');
				$('#menuScript').load('functionsEnabled.html');
				ResumeRayout();
				loaded = true;
			}
		}, 100);
		
		return false;
    });
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
	
	$(function(){
		$( 'input[name="intervalRadio"]:radio' ).change( function() {
			var radioval = $(this).val();
			
			GetUserRanking(-radioval);
		});
	});

	
</script>

</head>

<body>
<div id="main_header">
    <h1>どこＵＮＩ？</h1>
</div>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<div class="sendWrapper" id="ranking">
	<span class="box-title">投票数ランキング</span>
	<p>
		<label for="label_ranking" accesskey="n">期間</label>
		<div class="daysRadio">
			<input type="radio" name="intervalRadio" value="30"> 30日
			<input type="radio" name="intervalRadio" value="90"> 90日
			<input type="radio" name="intervalRadio" value="365" checked="checked"> 365日
		</div>
	</p>
	<p class="topic">
		マイページで『ランキングに名前を表示』を≪許可する≫にしていないユーザーは<br/>
		『UNKNOWN』で表示されます。
	</p>
	<div id="output">
	</div>
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



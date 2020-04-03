
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">
<link rel="stylesheet" href="css/jquery.jqplot.min.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/jquery.jqplot.min.js"></script>

<script>
	// ===============================
	//  連勝記録をロードします。
	// ===============================
	function LoadWinningStreak()
	{
		var loginUserID = getCookie("USER_ID");
		
		var args = { };

		$.ajax({
			type: "POST",
			url: "php/GetWinningStreakAPI.php",
			data: args,
			success: function(data)
			{
				var rowCnt = 0;
				
				$("#output").append('<table id="streak_data">');
				$("#streak_data").append('<tr id="streak_header">');
				$("#streak_header").append('<th class="streakHeader1">場所</th>');
				$("#streak_header").append('<th class="streakHeader2">プレーヤーネーム</th>');
				$("#streak_header").append('<th class="streakHeader3">キャラ</th>');
				$("#streak_header").append('<th class="streakHeader4">連勝数</th>');
				$("#streak_data").append('</tr>');
				$("#streak_data").append('<tbody id="streak_body">');
				
				var beforeDatetime;
				
				data.forEach(function(value)
				{
					var datetime = value.REGISTERED_DATETIME.substring(0, 10);
					
					if(beforeDatetime != datetime)
					{
						$("#streak_body").append('<tr class="streak_splitter"><td colspan=4> ◆'+ datetime +'</td></tr>');
					}
					
					// 行を変更する
					$("#streak_body").append('<tr class="streak_tr" id="row' + rowCnt + '">');
					if(value.PLACE_NAME != null)
					{
					    $("#row" + rowCnt).append('<td class="streak_td">' + value.PLACE_NAME + '</td>');
					}
					
					if(value.PLAYER_NAME != null)
					{
					    $("#row" + rowCnt).append('<td>' + value.PLAYER_NAME + '</td>');
					}
					$("#row" + rowCnt).append('<td>' + value.CHARACTER_NAME + '</td>');
					$("#row" + rowCnt).append('<td>' + value.WINNING_STREAK + '</td>');
					$("#streak_body").append('</tr>');
					
					rowCnt += 1;
					beforeDatetime = datetime;
				});
				
				$("#streak_body").append('</tbody>');
				$("#output").append("</table>");
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
			    alert('error : ' + errorThrown);
			    return false;
			}
		});
		
		return true;
	}
	
    // =========================
    //  ロード完了時イベント
    // =========================
    $(document).ready(function(){
		setTimeout(function(){
			if(!LoadWinningStreak()) {
			
			}
			else
			{
				$('#functionButtons').load('functions.html');
				$('#menuScript').load('functionsEnabled.html');
				ResumeRayout();
			}
		}, 100);
    });


    $(function() {
        $("#MoveMyPage").click(function(){
           window.location.href = "user_page.php";
        });
    });

    $(function() {
        $("#MoveAllData").click(function(){
           window.location.href = "where.php";
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
</head>
<body>

<div id="main_header">
    <h1 id="title_logo">どこＵＮＩ？</h1>
</div>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<div id="output"></div>
<div id="graph" style="height: 500px; width: 700px;"></div>

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
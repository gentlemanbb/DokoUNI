<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css?ver=201812010004" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">
<link rel="stylesheet" href="css/jquery.jqplot.min.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Functions.js?var=201812010001"></script>
<script src="js/jquery.jqplot.min.js"></script>

<script>
	var loaded = false;
	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		SuspendRayout();
		
		setTimeout(function(){
			if(!LoadIntroData()) {
				setTimeout(arguments.callee, 100);
			}
			else{
				$('#functionButtons').load('functions.html');
				$('#menuScript').load('functionsEnabled.html');
				ResumeRayout();
				loaded = true;
			}
		}, 100);
		
		return false;
	});
	
	// =========================
	//  紹介データをロード
	// =========================
	function LoadIntroData()
	{
		var introUserID = getCookie("INTRO_ID");
		var introUserName = getCookie("INTRO_USER_NAME");
		var loginUserID = getCookie("USER_ID");

		if(loginUserID == "undefined" || loginUserID == "null"){
			document.getElementById("moveMypageButton").style.display="none";
		}

		if(introUserID == "undefined"){
			alert("ユーザーを選択してください");
			window.location.href = "where.php";
			return false;
		}

		var introData = GetIntroduce(introUserID);

		var rowCnt = 0;
		$("#output").append(
			'<div id="introInfo">'
			+ '<div id="intro_title">'
			+ introUserName
			+ 'さんへの紹介'
			+ '</div>'
			+ '<div id="add_icon"></div>'
			+ '</div>'
		);

		$("#add_icon").append(
			'<a class="imgBtn24" onClick="AddFriend(\''
			+ introData.USER_ID
			+ '\'); return false;" href="javascript:void(0)" style="text-decoration:none;">'
			+ '<img src="img/buttons/user_add32.png">'
			+ '</a>');

		$("#output").append('<table id="intro_data">');
		$("#intro_data").append('<tr id="intro_header">');
		$("#intro_header").append('<th class="introHeader1">紹介者</th>');
		$("#intro_header").append('<th class="introHeader2">コメント</th>');
		$("#intro_data").append('</tr>');
		$("#intro_data").append('<tbody id="intro_body">');

		if(introData.INTRO_DATA != null)
		{
			introData.INTRO_DATA.forEach(function(value)
			{
				$("#intro_body").append('<tr id="row' + rowCnt + '">');
				$("#row" + rowCnt).append('<td class="alignCenter"><div><img src="' + value.ICON_IMAGE_PATH + '" style="width:75px; height:75px;" onClick="MoveToIntroduce(\''+ value.USER_ID + '\', \'' + value.USER_NAME + '\')"><br/>' + value.USER_NAME + '</div></td>');
				$("#row" + rowCnt).append('<td class="alignLeft"><div>' + value.COMMENT + '</div></td>');
				$("#intro_body").append('</tr>');
				rowCnt = rowCnt + 1;
			});
		}
		else 
		{
			$("#intro_body").append('<tr id="row' + rowCnt + '"></tr>');
			$("#row" + rowCnt).append('<td colspan="2" style="text-align:center;">データはありません</td>');
		}

		$("#intro_body").append('</tbody>');
		$("#output").append("</table>");

		return true;
	}

	function MoveToIntroduce(introUserID, introUserName)
	{
		setCookie("INTRO_ID", introUserID, 1);
		setCookie("INTRO_USER_NAME", introUserName, 1);
		window.location.href = "introduce.php";
	}

	// ==========================
	//  簡易的に描画を停止します
	// ==========================
	function AddFriend(_userID)
	{
		var introUserName = getCookie("INTRO_USER_NAME");
		var message = introUserName + "さん にフレンドを申請します。";
		var result = confirm(message);

		if(!result)
		{
			return;
		}

		var sendUserID = getCookie('USER_ID');
		var receiveUserID = _userID.trim();
		SendAddFriend(sendUserID, receiveUserID);
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
	
	// ==========================
	//  紹介文を書く
	// ==========================
	function SendIntroduce()
	{
		window.location.href = "send_introduce.php";
	}
	
	// ==========================
	//  前画面に戻る
	// ==========================
	function ReturnPrevPage()
	{
		window.location.href = "place_detail.php";
	}
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
<div id="output2"></div>

<button class="noBorderButton" onClick="SendIntroduce(); return false;">
	<a class="btn" href="javascript:void(0)">紹介文を書く</a>
</button>

<button class="noBorderButton" onClick="ReturnPrevPage(); return false;">
	<a class="btn" href="javascript:void(0)">戻る</a>
</button>
<div>

</div>
<br/>
</body>

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
<script src="js/ValidationUtil.js"></script>
<script>

	// -----------------------
	//  紹介文を送る
	// -----------------------
	function SendIntroduce(){
		var introUserID = getCookie("INTRO_ID");
		var userID = getCookie("USER_ID");

		if(userID == "undefined" || userID == null || userID == "null")
		{
			alert("ログイン状態が解除されました");
			window.location.href = "index.php";
			return false;
		}
		
		if(introUserID == "undefined" || introUserID == null || introUserID == "null")
		{
			alert("ユーザーが選択されていません");
			window.location.href = "where.php";
			return false;
		}
		
		var args = {
			introUserID : introUserID,
			userID : userID,
			introComment : $('#introComment').val(),
		};

		$.ajax({
			type: "POST",
			url: "php/SendIntroduceAPI.php",
			data: args,
			success: function(data)
			{
				if(data.RESULT != false)
				{
					alert("紹介文を送信しました。");
				}
				else
				{
					alert("失敗しました。");
				}
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return true;
	}

	// -----------------------
	//  紹介文の取得
	// -----------------------
	function LoadIntroduceData(){
		var introUserID = getCookie("INTRO_ID");
		var introUserName = getCookie("INTRO_USER_NAME");
		var userID = getCookie("USER_ID");
		
		
		
		if(userID == "undefined" || userID == null || userID == "null")
		{
			alert("ログイン状態が解除されました");
			window.location.href = "index.php";
			return false;
		}
		
		if(introUserID == "undefined" || introUserID == null || introUserID == "null")
		{
			alert("ユーザーが選択されていません");
			window.location.href = "where.php";
			return false;
		}
		
		var args = {
			introUserID : introUserID,
			userID : userID,
		};
		
		document.getElementById("introUserName").innerHTML = introUserName + "の紹介文";
		
		$.ajax({
			type: "POST",
			url: "php/GetMyIntroduceAPI.php",
			data: args,
			success: function(data)
			{
				if(data.COMMENT != null)
				{
					document.sendData.introComment.value = data.COMMENT;
				}
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return true;
	}

	// =========================
	//  ロード完了イベント
	// =========================
	$(document).ready(function()
	{
		// 描画を停止
		SuspendRayout();
		
		setTimeout(function(){
			// 紹介文をロード
			if(!LoadIntroduceData())
			{
				setTimeout(arguments.callee, 100);
			}
			else {
				$('#functionButtons').load('functions.html');
				$('#menuScript').load('functionsEnabled.html');
				// 描画再開
				ResumeRayout();
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
	
	// ==========================
	//  前画面に戻る
	// ==========================
	function ReturnPrevPage()
	{
		window.location.href = "introduce.php";
	}
</script>


<div id="functionButtons"></div>
<div id="menuScript"></div>

<div id="updatableContents">

<div id="main_header">
	<h1>どこＵＮＩ？</h1>
</div>


<div class="sendWrapper">
	<form id="sendData" name="sendData">
		<div>
			<p id="introUserName"></p>
			<p>
				<textarea id="introComment" name="introComment" rows="3" wrap="hard" style="width:90%"></textarea>
			</p>
			<button class="noBorderButton" onClick="SendIntroduce(); return false;">
				<a class="btn" href="javascript:void(0)">紹介文を送る</a>
			</button>
			
			<button class="noBorderButton" onClick="ReturnPrevPage(); return false;">
				<a class="btn" href="javascript:void(0)">戻る</a>
			</button>
		</div>
	</form>
</div>

</div>

</body>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
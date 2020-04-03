
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
<script src="js/Functions.js?ver=201812100002"></script>
<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>

<script>
	var loaded;

	// =======================
	//  自分のコンボデータを取得
	// =======================
	function LoacComboDetail()
	{
		var characterID = getCookie("CHARACTER_ID");
		characterID = "1";
		data = GetComboDataList(characterID);
		document.getElementById("output").innerHTML = "";

		var rowCnt = 0;

		$("#output").append('<table id="combo_data"></table>');
		$("#combo_data").append('<thead id="combo_header"></thead>');
		$("#combo_header").append('<tr id="combo_tr"></tr>');
		$("#combo_tr").append('<th class="comboHeader1">コンボ名</th>');
		$("#combo_tr").append('<th class="comboHeader2">コンボレシピ</th>');
		$("#combo_data").append('<tbody id="combo_body"></tbody>');

		if(data != null)
		{
			data.forEach(function(value){
				// 行を変更する
				$("#combo_body").append('<tr id="row' + rowCnt + '">');
				$('#row' + rowCnt).append('<td><span onClick="MoveToDetail(\'' + value.COMBO_ID + '\')">' + value.COMBO_NAME + '</span></td>');
				$('#row' + rowCnt).append("<td>" + value.COMBO_RECIPE + "</td>");

				rowCnt = rowCnt + 1;
			});
		}
		
		return true;
	}


	// =======================
	//  自分のコンボデータを取得
	// =======================
	function LoacComboDetail()
	{
		var characterID = getCookie("CHARACTER_ID");
		characterID = "1";
		data = GetComboDataList(characterID);
		document.getElementById("output").innerHTML = "";

		var rowCnt = 0;

		$("#output").append('<table id="combo_data"></table>');
		$("#combo_data").append('<thead id="combo_header"></thead>');
		$("#combo_header").append('<tr id="combo_tr"></tr>');
		$("#combo_tr").append('<th class="comboHeader1">コンボ名</th>');
		$("#combo_tr").append('<th class="comboHeader2">コンボレシピ</th>');
		$("#combo_data").append('<tbody id="combo_body"></tbody>');

		if(data != null)
		{
			data.forEach(function(value){
				// 行を変更する
				$("#combo_body").append('<tr id="row' + rowCnt + '">');
				$('#row' + rowCnt).append('<td><span onClick="MoveToDetail(\'' + value.COMBO_ID + '\')">' + value.COMBO_NAME + '</span></td>');
				$('#row' + rowCnt).append("<td>" + value.COMBO_RECIPE + "</td>");

				rowCnt = rowCnt + 1;
			});
		}
		else
		{
		}
		
		return true;
	}
	
	function MoveToDetail(comboID)
	{
		
	}

	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		var userID = getCookie("USER_ID");
		var canRegistEvent = getCookie("REGIST_EVENT");
		
		SuspendRayout();
		
		// 現在の日付を設定
		setCookie("ADD_HOUR", 3);
		
		setTimeout(function(){
			if(!LoacComboDetail())
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
		}, 100);

		return false;
	});
</script>
<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
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
</script>
<body>
	<div id="functionButtons"></div>
	<div id="menuScript"></div>

	<!-- ここから下は書き換わる可能性がある -->
	<div id="updatableContents">
	
	<div id="main_header">
		<h1>どこＵＮＩ？</h1>
	</div>

	<div id="output"></div>

	<br/>

	<!-- admax -->
	<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
	<!-- admax -->
</body>



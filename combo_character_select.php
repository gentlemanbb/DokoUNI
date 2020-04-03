
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css?ver=2019022101" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/modal.js"></script>
<script src="js/Functions.js?ver=201903"></script>

<script>
</script>
</head>
<body>
<script>
	
	// ===========================
	// キャラクター個別ページに移動
	// ===========================
	function MoveToDetail(characterID)
	{
		window.location.href = "combo_list.php?characterID=" + characterID;
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

<div id="functionButtons"></div>
<div id="menuScript"></div>

	<!-- ここから下は書き換わる可能性がある -->
	<div id="updatableContents">

		<div id="main_header">
			<h1>どこＵＮＩ？<img  id="OpenTwitterModal" src="img/buttons/twitter32.png"></h1>
		</div>

		<div class="topic">

		<div id="character_data">
			<span onClick="MoveToDetail('0')"><img src="img/icons/icon_hyde1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('1')"><img src="img/icons/icon_linne1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('2')"><img src="img/icons/icon_waldstein1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('3')"><img src="img/icons/icon_carmine1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('4')"><img src="img/icons/icon_orie1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('5')"><img src="img/icons/icon_gordeau1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('6')"><img src="img/icons/icon_merkava1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('7')"><img src="img/icons/icon_vatista1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('8')"><img src="img/icons/icon_seth1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('9')"><img src="img/icons/icon_yuzuriha1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('10')"><img src="img/icons/icon_hilda1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('11')"><img src="img/icons/icon_chaos1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('12')"><img src="img/icons/icon_eltnum1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('13')"><img src="img/icons/icon_akatsuki1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('14')"><img src="img/icons/icon_nanase1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('15')"><img src="img/icons/icon_byakuya1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('16')"><img src="img/icons/icon_phonon1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('17')"><img src="img/icons/icon_mika1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('18')"><img src="img/icons/icon_enkidu1.png" style="width:65px; height:65px;"></span>
			<span onClick="MoveToDetail('19')"><img src="img/icons/icon_wagner1.png" style="width:65px; height:65px;"></span>
		</div>
	</div>

</div>
<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
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




<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css"/>

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Functions.js"></script>
<script src="js/modal.js"></script>
<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>

<script>
	var loaded;

	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		return false;
	});
	
	
</script>
</head>
<body>
<script>
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

<div id="wachingHour">
	<p>
		<span id="prevHour"><<</span>
		<span id="watchingHour">3</span>
		<span id="nextHour">>></span>
		<span id="Invite" class="btn">
			<button class="noBorderButton" onClick = "modalOpen('modal-main'); return false;">Invite</button>
		</span>
	</p>
	<p>
		<button class="noBorderButton"><span id="3Hour" class="btn3">3h</span></button>
		<button class="noBorderButton"><span id="12Hour" class="btn3">12h</span></button>
		<button class="noBorderButton"><span id="24Hour" class="btn3">24h</span></button>
	</p>
</div>
<div id="watchingAreaWrapper">
	<p>
		<div id="watchingArea" class="watchingArea"></div>
	</p>
</div>
<div id="output"></div>
<br/>
<div class="sendWrapper" id="sendDataForm">
		<span class="box-title">どこかに行く</span>
		<form id="sendData" name="sendData">
		<p>
			<label for="label_place" accesskey="n">場所：</label><br/>
			<select class="formComboBox" name="placeID" id="placeID">
			</select><span class="mini">←表示されない場合は再ログインしてみてください。</span>
		</p>
		<p>
			<label for="label_place" accesskey="n">参加区分：</label><br/>
			<select class="formComboBox" name="joinType" id="joinType">
			</select>
		</p>
		<p>
			<label for="label_place" accesskey="n">目的：</label><br/>
			<select class="formComboBox" name="purposeType" id="purposeType">
			</select>
		</p>

		<br/>

		<p>
			<label for="label_place" accesskey="n">参加時刻（FROM - TO)：</label><br/>
			<input type="time" id="datetime_from" name="datetime_from" class="from_to">時 から<br/>
			<input type="time" id="datetime_to" name="datetime_to"  class="from_to">時 まで
		</p>

		<br/>

		<button type="submit" class="noBorderButton" id="SendPopularity" name="SendPopularity" onsubmit="return false;">
			<a class="btn" href="javascript:void(0)">送信</a>
		</button>
		<button type="submit" class="noBorderButton" id="SendCancel" name="SendCancel" onsubmit="return false;">
			<a class="btn" href="javascript:void(0)">行くのやめた</a>
		</button>
		</div>
	</form>
</div>

<div id="noLoggedinFunctions">
	<p>※参加投票はログインしないと使用できません。</p>
	<button type="submit" class="noBorderButton" id="MoveLoginPage" name="MoveLoginPage" onsubmit="return false;">
		<a class="btn" href="javascript:void(0)">ログイン</a>
	</button>
</div>

<div id="modal-main" class="modal">
	グループ情報
	<div id="group_info"></div>

	<button class="noBorderButton" id="SendInvite" name="SendInvite" onClick="Invite(); return false;">
		<a class="btn" href="javascript:void(0)">招待</a>
	</button>
</div>


<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
<br/>
</body>



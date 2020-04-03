<!DOCTYPE html>
<html lang="ja">
	<head>
		<title>どこUNI？</title>
		<link rel="stylesheet" href="css/import.css?<?php echo date('Ymd-Hi'); ?>" type="text/css">
		<meta name="viewport"
			content="width=device-width,
			initial-scale=1.0,
			minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
		<meta charset="utf-8">
		<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
		<script src="js/table_tr_link.js"></script>
		<script src="js/Cookie.js"></script>
		<script src="js/Functions.js?<?php echo date('Ymd-Hi'); ?>"></script>
		<script src="js/modal.js"></script>

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

			// =========================
			//  ロード完了時イベント
			// =========================
			$(document).ready(function()
			{
				SuspendRayout();
				
				let promise = new Promise((resolve, reject) => {
					setTimeout(() => {
						resolve(LoadMainHeader("main_header"));
					}, 100);
				});

				promise.then((result) => {
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							resolve(true);
						}, 100);
					});
				}).then((result) =>{
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							GetPlaceData(1);
							resolve(true);
						}, 100);
					});
				}).then((result) =>{
					return new Promise((resolve, reject) => {
						setTimeout(() => {
							var eventTemplateList = GetEventTemplateData('gentlemanbb');
							
							eventTemplateList.forEach(function(event)
							{
								$("#eventTemplateID").append('<option value=' + event.EVENT_ID + '>' + event.EVENT_NAME + '</option>');
							});
							resolve(true);
						}, 100);
					});
				}).catch(() => { // エラーハンドリング
					alert('ロード処理でエラーが発生しました。');
				});

				ResumeRayout();
				
				
				return false;
			});
			
			function eventTemplateChanged()
			{
				
				
			}
		</script>
	</head>
<body>
	<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>

	<div id="functionButtons"></div>
	<div id="menuScript"></div>
	<div id="main_header"></div>

	<div id="output"></div>
	<div class="sendWrapper" id="sendDataForm">
		<span class="box-title">イベントを登録する</span>
		<p>
			<button onClick="modalOpen('modal-template'); return false;">テンプレートから入力</button>
		</p>
		<form id="sendData" name="sendData">
		<div>
			<p>
				<label for="label_place" accesskey="n"/>場所：</label><br/>
				<select class="formComboBox" name="placeID" id="placeID"></select>
				<button>新規</button>
			</p>
			
			<br/>
			
			<p>
				<label for="label_date" accesskey="n">開催日程（FROM - TO)：</label><br/>
				<input type="date" id="eventDate" name="eventDate" class="from_to"><br/>
				<input type="time" id="datetime_from" name="datetime_from" class="from_to"/＞
				〜
				<input type="time" id="datetime_to" name="datetime_to"  class="from_to"/>時 まで
			</p>
			<p>
				<label for="label_comment">コメント</label><br>
				<textarea id="comment" cols=64 rows=3></textarea>
				<input type="checkbox" id="with_tweet">ツイートする</input>
			</p>
		</div>
	</form>
	<button id="SendPopularity" name="SendPopularity" onClick="SendPopularity(); return false;">Send</button>
	<button id="SendCancel" name="SendCancel" onClick="SendCancel(); return false;">cancel</button>
</div>

<div id="noLoggedinFunctions">
    <p>※参加投票はログインしないと使用できません。</p>
    <button type="submit" class="noBorderButton" id="MoveLoginPage" name="MoveLoginPage" onsubmit="return false;">
        <a class="btn" href="javascript:void(0)">ログイン</a>
    </button>
</div>

<div id="modal-template" class="modal-small">
	<label for="label_event_template" accesskey="n">テンプレート選択</label><br/>
	<p>
		<select class="formComboBox" id="eventTemplateID" name="eventTemplateComboBox" onchange="alert('test')"></select>
	</p>
	<label for="label_event_template" accesskey="n">場所</label><br/>
	<label for="label_event_template" accesskey="n">場所名</label><br/>
	<label for="label_event_template" accesskey="n">開催時刻</label><br/>
	<label for="label_event_template" accesskey="n">From 〜　To</label><br/>
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



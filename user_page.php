
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/ValidationUtil.js"></script>
<script src="js/Functions.js?ver=201812060002"></script>
<script src="js/modal.js"></script>
<script>

	// -----------------------
	//  ユーザ情報の取得
	// -----------------------
	function LoadUserData(){
		var userID = getCookie("USER_ID");

		if(userID == "undefined" || userID == null || userID == "null")
		{
			alert("ログイン状態が解除されました");
			window.location.href = "index.php";
			return false;
		}

		data = GetUserDetailData(userID);
		
		if(data != null)
		{
			document.sendData.RIP.value = data.USER_DATA.RIP;
			document.sendData.playerName.value = data.USER_DATA.USER_NAME;
			setCookie('USER_NAME', '', Date.now() - 3600, '/');
			setCookie('USER_NAME', data.USER_DATA.USER_NAME, 1);
			$("#characterID").val(data.USER_DATA.MAIN_CHARACTER_ID); 
			$("#twitterID").val(data.USER_DATA.TWITTER);
			$("#noticeType").val(data.USER_DATA.NOTIFICATION); 
			$("#areaID").val(data.USER_DATA.AREA_ID); 
			$("#authorityName").append(data.USER_DATA.AUTHORITY_NAME); 
			$("#sendCount").append(data.USER_DATA.SEND_COUNT + "回 (" + data.USER_DATA.RANK + "位)");
			$("#agreeDisplayName").val(data.USER_DATA.AGREE_DISPLAY_NAME); 
			$("#comment").val(data.USER_DATA.COMMENT); 

			if(data.USER_DATA.SEND_COUNT>= 20)
			{
				document.getElementById("regularAccount").style.display="block";
				document.getElementById("userImage").innerHTML = '<img src="' + data.USER_DATA.ICON_IMAGE_PATH + '" style="width:75px; height:75px;">';
			}
			else
			{
				document.getElementById("regularAccount").style.display="none";
			}
		}

		document.getElementById("userGroup").innerHTML = '';
		$("#userGroup").append('<table id = "group_data" class="group_data"></table>');
		$("#group_data").append('<thead id="rowHeader"></thead>');
		$("#rowHeader").append('<th id ="rowHeader1">グループ名</th>');
		$("#rowHeader").append('<th id ="rowHeader2">状態</th>');
		$("#rowHeader").append('<th id ="rowHeader3">移動</th>');
		$("#group_data").append('<tbody id="group_tbody"></tbody>');

		var rowCnt = 0;

		if(data.GROUP_DATA != null && data.GROUP_DATA.length > 0)
		{
			data.GROUP_DATA.forEach(function(value)
			{
				var rowID = "row" + rowCnt;
				var rowID2 = "#row" + rowCnt;
				var statusText = 'メンバー';

				if(value.STATUS == 1)
				{
					$("#group_tbody").append('<tr id = ' + rowID +'></tr>');
					$(rowID2).append('<td>' + value.GROUP_NAME + '</td>');
					$(rowID2).append('<td>' + statusText + '</td>');
					$(rowID2).append('<td><button onClick="MoveToGroupPage(\'' + value.GROUP_ID + '\')">移動</button></td>');
					
					rowCnt = rowCnt + 1;
				}
			});
		}
		
		$("#group_tbody").append('<tr id = "addGroup"></tr>');
		$('#addGroup').append('<td colspan="3"><button onClick="modalOpen(\'modal-add-group\'); return false;">グループを新規作成する</button></td>');

		return true;
	}

	// -----------------------
	//  ユーザ情報の取得
	// -----------------------
	function LoadSupportData()
	{
		var userID = getCookie("USER_ID");

		var args = {
			userID : userID,
		};

		$.ajax({
			type: "POST",
			url: "php/GetUserSupportAPI.php",
			data: args,
			success: function(data)
			{
				var isFirst = true;
				var rowCnt = 0;
				var colCnt = 0;

				if(data.length > 0){
					$("#userSupport").append('<table class= "userSupportTable" id="userSupportData">');
					$("#userSupportData").append('<tr id="userSupportHeader">');
					$("#userSupportHeader").append('<th class="userSupportHeader1">カテゴリ</th>');
					$("#userSupportHeader").append('<th class="userSupportHeader2">内容</th>');
					$("#userSupportHeader").append('<th class="userSupportHeader3">ステータス</th>');
					$("#userSupportHeader").append('<th class="userSupportHeader4">対応</th>');
					$("#userSupportData").append('</tr>');
					$("#userSupportData").append('<tbody id="userSupportBody">');

					data.forEach(function(value){
						// 1行目以外は</tr>で〆る
						if(!isFirst){
							$("#userSupportBody").append("</tr>");
							rowCnt += 1;
						}
						else
						{
							// 1行目フラグをおろす
							isFirst = false;
						}

						// 行を変更する
						$("#userSupportBody").append('<tr id="us_row' + rowCnt + '">');
						$('#us_row' + rowCnt).append('<td>' + value.CATEGORY + '</td>');
						$('#us_row' + rowCnt).append("<td>" + value.TEXT + "</td>");
						$('#us_row' + rowCnt).append("<td>" + value.STATUS + "</td>");
						$('#us_row' + rowCnt).append("<td>" + value.SUPPORT_RESULT +"</td>");
					});
				
					$("#userSupportBody").append('</tbody>');
					$("#userSupport").append("</table>");
				}
				else {
					$("#userSupport").append('<span class="attention">問い合わせはありません</span>');
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
	//  ユーザ情報の取得
	// -----------------------
	function MoveToGroupPage(_groupID)
	{
		setCookie("GROUP_ID", _groupID, 1);
		window.location.href = "group_detail.php";
	}


	// --------------------
	//  ユーザ情報一覧を取得
	// --------------------
	function LoadCharacterData(){
		var args = {
			gameID : 1
		};

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetCharacter.php",
			data: args,
			success: function(data)
			{
				data.forEach(function(value){
					// 行を変更する
					$("#characterID").append('<option value=' + value.CHARACTER_ID + '>' + value.CHARACTER_NAME + '</option>');
				});
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
	   });

	   return true;
	}

	// ========================
	//  ランキングへの名前表示許可のロード
	// ========================
	function LoadAgreeDisplayNameData(){
		var args = {
			key : "AGREE_DISPLAY_NAME"
		};
		
		$.ajax({
			type: "POST",
			url: "php/GetType.php",
			data: args,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					data.TYPE_DATA.forEach(function(value)
					{	
						// 行を変更する
						$("#agreeDisplayName").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
					});
				}
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				return false;
			}
		});
		
		return true;
	}
	
	function LoadAreaData(){

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetAreaAPI.php",
			success: function(data)
			{
				data.forEach(function(value){
					// 行を変更する
					$("#areaID").append('<option value=' + value.AREA_ID + '>' + value.AREA_NAME + '</option>');
				});
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
	   });

	   return true;
	}

	// =========================
	//  通知設定のマスタ取得
	// =========================
	function LoadNoticeData(){

		var args = {
			 key : "NOTICE",
		};

		$.ajax({
			type: "POST",
			url: "php/GetType.php",
			data: args,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					data.TYPE_DATA.forEach(function(value)
					{	
						// 行を変更する
						$("#noticeType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
					});
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
			// キャラクターデータロード
			if(!LoadCharacterData())
			{
				setTimeout(arguments.callee, 100);
			}
			else {
				setTimeout(function() {
					// 地方データロード
					if(!LoadAreaData()) {
						setTimeout(arguments.callee, 100);
					}
					else {
						setTimeout(function() {
							// 通知区分ロード
							if(!LoadNoticeData()) {
								setTimeout(arguments.callee, 100);
							}
							else {
								setTimeout(function() {
									if(!LoadAgreeDisplayNameData()) {
										setTimeout(arguments.callee, 100);
									}
									else
									{
										setTimeout(function() {
											// ユーザー情報のロード
											if(!LoadUserData()) {
												setTimeout(arguments.callee, 100);
											}
											else {
												setTimeout(function() {
													// サポート情報のロード
													if(!LoadSupportData()) {
														setTimeout(agruments.callee, 100);
													}
													else
													{
														$('#functionButtons').load('functions.html');
														$('#menuScript').load('functionsEnabled.html');
														// 描画再開
														ResumeRayout();
													}
												}, 100);
											}
										}, 100);
									}
								}, 100);
							}
						}, 100);
					}
				}, 100);
			}
		}, 100);
		
		
		return false;
	});

</script>

<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
</head>
<body>

<script>
	// ========================
	// ユーザ情報を更新します
	// ========================
	$(function() {
		$("#UpdateUserData").click(function(){

			// ログインユーザＩＤ
			var loginUserID = getCookie("USER_ID");

			// 引数
			var args = {
				userID : loginUserID,
				playerName : $('#playerName').val(),
				RIP :$('#RIP').val(),
				mainCharacterID :$('#characterID').val(),
				notification :$('#noticeType').val(),
				areaID :$('#areaID').val(),
				agreeDisplayName :$('#agreeDisplayName').val(),
				comment :$('#comment').val()
			};

			// バリデーションの代わり
			if(args.playerName.length < 2){
				alert("ユーザ名は2文字以上でなければいけません");
				return false;
			}

			if(!IsNumber(args.RIP, "RIP")){
				return false;
			}

			$.ajax({
				type: "POST",
				dataType: "json",
				url: "php/UpdateUserAPI.php",
				data: args,
				success: function(data, dataType)
				{
					if(data.RESULT == true)
					{
						setCookie("USER_ID"				, loginUserID, 1);
						setCookie("USER_NAME"			, data.USER_NAME, 1);
						setCookie("RIP"					, data.RIP, 1);
						setCookie("MAIN_CHARACTER_ID"	, data.MAIN_CHARACTER_ID, 1);
						setCookie("AREA_ID"				, data.AREA_ID, 1);
						
						// 更新成功時
						alert('更新に成功しました。');
						window.location.href = "user_page.php";
					}
					else
					{
						alert(data.MESSAGE);
					}
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					alert('error : ' + errorThrown);
					return false;
				},

				complete: function(){
				}
			});

			return false;
		});
	});
	
	/* -----------------------
	//  ファイル変更
	// ----------------------- */
	function changeFile()
	{
		//フォームのデータを変数formに格納
		var form = $('#sendData').get()[0];
		
		//FormData オブジェクトを作成
		var formData = new FormData(form);
		formData.append("userID", getCookie('USER_ID'));
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/UploadUserIconAPI.php",
			data: formData,
			cache	   : false,
			processData : false,
			contentType : false,
			success: function(data, dataType)
			{
				if(data.RESULT == false)
				{
					// 何もしない
					alert(data.MESSAGE);
				}
				else
				{
					var dateTime= new Date();
					var hour = dateTime.getHours();
					var minute = dateTime.getMinutes();
					var second = dateTime.getSeconds();
					var timeStamp = hour + minute + second;
					
					if(document.getElementById("userImage").innerHTML != '')
					{
						// 一回消してもっかいつけてみる
						document.getElementById("userImage").innerHTML = '';
						document.getElementById("userImage").innerHTML = '<img src="' + data.ICON_IMAGE_PATH + '?' + timeStamp + '" style="width:75px; height:75px;">';
					}
					else
					{
						// ない場合はそのままつける
						document.getElementById("userImage").innerHTML = '<img src="' + data.ICON_IMAGE_PATH + '?' + timeStamp + '" style="width:75px; height:75px;">';
					}
					
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
				return false;
			}
		});

		return false;
	}
	// ==========================
	//  グループを新規作成します。
	// ==========================
	function RegistNewGroup()
	{
		var userID = getCookie('USER_ID');
		var groupName = document.newGroupForm.newGroupName.value;
		
		RegistGroup(groupName, userID);

		location.reload();
	}

	// ==========================
	//  グループを新規作成します。
	// ==========================
	function UpdateTwitterID()
	{
		var userID = getCookie('USER_ID');
		var twitterID = $('#twitterID').val();
		var checkResult = CheckTwitterAccount(userID, twitterID);

		if(checkResult)
		{
			var updateResult = UpdateTwitterAccountFunc(userID, twitterID);

			if(updateResult)
			{
				alert('Twitterの連携に成功しました。');
			}
		}
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
	
	$(function() {
		$("#ChangePassword").click(function(){
		   window.location.href = "change_password.php";
		});
	});

	/* **********
	// 協力願い
	// ********** */
	$(function() {
		$("#MoveUnknownPlace").click(function(){
			SuspendRayout();
			window.location.href = "unknown_place.php";
			ResumeRayout();
		});
	});

	$(function() {
		$("#LogOut").click(function(){
			SuspendRayout();
			setCookie('USER_ID','', Date.now() - 3600, '/');
			setCookie("USER_ID", null);
			window.location.href = "index.php";
			ResumeRayout();
		});
	});

</script>


<div id="functionButtons"></div>
<div id="menuScript"></div>

<div id="updatableContents">

	<div id="main_header">
		<h1>どこＵＮＩ？<img  id="OpenTwitterModal" src="img/buttons/twitter32.png"></h1>
	</div>

	<button class="noBorderButton" type="submit" id="MoveUnknownPlace" name="MoveUnknownPlace">
		<a class="btn2" href="javascript:void(0)">協力願い</a>
	</button>
	<button class="noBorderButton" type="submit" id="LogOut" name="LogOut">
		<a class="btn" href="javascript:void(0)">ログアウト</a>
	</button>
	<div class="sendWrapper">
		<form id="sendData" name="sendData">
			<div>
				<span class="box-title">ユーザ情報</span>
				<form id="sendData" name="sendData">
				<p>
					<label for="label_player_name" accesskey="n">権限：</label>
					<span id="authorityName">
				</p>
				<p>
					<label for="label_send_count" accesskey="n">投票回数：</label>
					<span id="sendCount">
				</p>
				
				<!-- アイコン設定 -->
				<div id="regularAccount" style="display:none;">
					<label for="label_icon" accesskey="n">画像：</label>
					<div id="userImage"></div>
					<input type="file" name="file_name" onchange="changeFile();"/>
				</div>

				<p>
					<label for="label_player_name" accesskey="n">プレーヤー名：</label><br/>
					<input type="text" name="playerName" id="playerName" />
				</p>
				 <p>
					<label for="label_character" accesskey="n">メインキャラ：</label><br/>
					<select class="formComboBox" name="characterID" id="characterID">
					</select>
				</p>
				 <p>
					<label for="label_area" accesskey="n">地域：</label><br/>
					<select class="formComboBox" name="areaID" id="areaID">
					</select>
				</p>
				<p>
					<label for="label_RIP" accesskey="n">RIP（万）：</label><br/>
					<input type="text" name="RIP" id="RIP" />（例：168)
				</p>
				<p>
					<label for="label_comment" accesskey="n">一言プロフィール：</label><br/>
					<textarea row="3" name="comment" id="comment" style="height:3.5em; width:60%"></textarea>（256文字以内）
				</p>
				<p>
					<label for="label_notice" accesskey="n">通知設定：</label><br/>
					<select class="formComboBox" name="noticeType" id="noticeType">
					</select>		
				</p>
				
				<p>
					<label for="label_agree_display_name" accesskey="n">プレーヤー名の公開：</label><br/>
					<select class="formComboBox" name="agreeDisplayName" id="agreeDisplayName">
					</select>		
				</p>

				<br/>
				<button class="noBorderButton" type="submit" id="UpdateUserData" name="UpdateUserData" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">更新</a>
				</button>
				<button class="noBorderButton" type="submit" id="ChangePassword" name="ChangePassword" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">パスワードの変更</a>
				</button>
			</div>
		</form>
		<p>
			<span class="topic">Twitter(@マークを除く)</span><br/>
			<input type="text" name="twitterID" id="twitterID" size="20"/><button id="updateTwitter" onClick="UpdateTwitterID(); return false;">連携する</button>
		</p>
	</div>

	<div id="userGroupWrapper">
		<p>グループ一覧</p>
		<div id="userGroup"></div>
</div>

	<div id="userSupportWrapper">
		<p>問い合わせ一覧</p>
		<div id="userSupport"></div>
	</div>

</div>

<br/>

<div id="modal-add-group" class="modal-small">
	新しいグループ名
	<div id="add_group_info"></div>
	<form id="newGroupForm" name ="newGroupForm">
		<p>
			<input type="text" name="newGroupName">
		</p>
		<p>
			<button class="noBorderButton" onClick="RegistNewGroup(); return false;">
				<a class="btn" href="javascript:void(0)">作成</a>
			</button>
		</p>
	</form>
</div>

<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
</body>
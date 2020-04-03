
<html lang="ja">
	<head>
	<title>どこUNI？</title>
	<link rel="stylesheet" href="css/import.css?ver=2019032801" type="text/css">
	<link rel="stylesheet" href="css/rayout.css" type="text/css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css"/>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
	<meta charset="utf-8">
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
	<script src="js/table_tr_link.js"></script>
	<script src="js/Cookie.js"></script>
	<script src="js/modal.js?v=001"></script>
	<script src="js/Functions.js?ver=2019032803"></script>
	<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>
	<script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script>
	<style type="text/css">
		.combo_label {
			font-size:0.6em;
			padding-left:5px;
			margin-bottom:3px;
		}

		/************************************
		** トグル表示ボタン
		************************************/
		.toggle-wrap .toggle-button {
			display: block;
			cursor: pointer;
			padding: 3px 10px;
			background-color: #555;
			border: 1px solid #777;
			text-align: center;
			margin-bottom: 1em;
			border-radius: 3px;
			font-size:0.8rem;
			width:200px;
		}
		
		.toggle-wrap .toggle-button:hover {
			border-color: red;
		}
		
		.toggle-wrap .toggle-content,
		.toggle-wrap > input[type="checkbox"] {
			display: none;
			font-size:0.7rem;
		}
		
		.toggle-wrap > input[type="checkbox"]:checked ~ .toggle-content {
			display: block;
		}

	</style>

	<script type="text/javascript">
		var loaded;
		var comboDataList = null;

		// ====================
		// 詳細画面へ移動
		// ====================
		function MoveToDetail(_comboID)
		{
			var loginUserID = getCookie("USER_ID");
			var comboData = null;

			comboDataList.forEach(function(value){
				if(value.COMBO_ID == _comboID)
				{
					comboData = value;
				}
			});

			if(comboData != null)
			{
				var contentBlock = document.getElementById('combo_info');
				contentBlock.innerHTML = "";

				var dateTime= new Date();
				var hour = dateTime.getHours();
				var minute = dateTime.getMinutes();
				var second = dateTime.getSeconds();
				var timeStamp = hour + minute + second;
				var twitterID = null;

				// ルートタグ
				if(comboData.MOVIE_PATH != null && comboData.MOVIE_PATH != '')
				{
					$("#combo_info").append('<div id="combo_video"><div id="movieToggle" onClick="MovieDisplay()">[動画を閉じる]</div><div id="movieTweet" style="display:block"></div></div>');

					// 動画はここ
					var container = document.getElementById("movieTweet");

					twttr.widgets.createTweet
					(
						// 第1引数: ツイートID
						comboData.MOVIE_PATH,
						// 第2引数: コンテナの要素
						container ,	
						{	// 第3引数: パラメータ
							theme: "dark" ,
							align: "center"
						}
					);

					twitterID = GetTwitterID(comboData.MOVIE_PATH);

				}
				else
				{
					$("#combo_info").append('<div id="combo_video"><div id="movieToggle">[動画は登録されていません]</div><div id="movieTweet" style="display:none"></div></div>');
				}
				$("#combo_info").append('<div id="combo_detail" style="border-radius:3px; padding:5px 5px 15px 5px; margin-top:5px; background-color:rgba(50, 50, 50, 200);"></div>');

				// コンボ名
				$("#combo_detail").append('<div id="combo_name"><div class="combo_label">●コンボ名</div></div>');
				$("#combo_name").append('<div class="combo_value">' + comboData.COMBO_NAME + '</div>');

				// タグ
				if(comboData.TAGS != null)
				{
					$("#combo_detail").append('<div id="combo_tags"><div class="combo_label">●タグ</div></div>');
					$("#combo_tags").append('<div id="tag_values" class="combo_tags"></div>');
					comboData.TAGS.split(',').forEach( function(value)
					{
						$("#tag_values").append('<span class="tag_value" onClick="Search(\'' + value +'\')">' + value + '</span>');
					});
				}

				// レシピ
				$("#combo_detail").append('<div id="combo_recipe"><div class="combo_label">●コンボレシピ</div></div>');
				$("#combo_recipe").append('<div class="combo_value">' +comboData.COMBO_RECIPE + '</div>');

				// ゲージは横並び
				$("#combo_detail").append('<div id="numbersData" style="margin-bottom:10px;"><div class="combo_label">●データ</div></div>');
				$("#numbersData").append('<span id="damage" class="combo_value">ダメージ：</span>');
				$("#numbersData").append('<span id="gain_gauge" class="combo_value">増加EXS：</span>');
				$("#numbersData").append('<span id="use_gauge" class="combo_value">使用EXS：</span>');
				$("#damage").append('<span>' +comboData.COMBO_DAMAGE + '</span>');
				$("#gain_gauge").append('<span>' +comboData.GAIN_GAUGE + '%</span>');
				$("#use_gauge").append('<span>' +comboData.USE_GAUGE + '%</span>');

				// コメント
				$("#combo_detail").append('<div id="comment"><div class="combo_label">●コメント</div></div>');			
				$("#comment").append('<div class="combo_value">' +comboData.COMMENT + '</div>');

				// 登録ユーザー
				$("#combo_detail").append('<div id="user_names"></div>');
				$("#user_names").append('<span id="regist_user_name"><span class="combo_label">●登録者</span></span>');
				$("#user_names").append('<span class="combo_value">' +comboData.TOROKU_USER_NAME + 'さん</span>');
				$("#user_names").append('<span class="combo_value">／</span>');
				$("#user_names").append('<span id="update_user_name"><span class="combo_label">●更新者</span></div>');
				$("#user_names").append('<span class="combo_value">' +comboData.KOSHIN_USER_NAME + 'さん</span>');
				
				$("#combo_detail").append('<div id="evalution" style="display:-webkit-inline-box"></div>');			
				$("#evalution").append('<div id="evalGood" onClick="SendEvalutionData(' + comboData.COMBO_ID + ', 1)" class="combo_value" style="height:32px; width:32px; text-align:center; font-size:1.3rem; background-repeat: no-repeat; background-image: url(\'img/buttons/good32.png\');">' + comboData.EVALUTION_GOOD + '</div>');
				$("#evalution").append('<div id="evalBad" onClick="SendEvalutionData(' + comboData.COMBO_ID + ', 2)" class="combo_value" style="height:32px; width:32px; text-align:center; font-size:1.3rem; background-repeat: no-repeat; background-image: url(\'img/buttons/bad32.png\');">' + comboData.EVALUTION_BAD + '</div>');			


				// とりあえず誰でも編集可能に
				// if(comboData.TOROKU_USER_ID == loginUserID)
				// {
					$("#combo_detail").append('<div id="editButton" style="font-size:0.6em; padding-left:5px;"><button onClick="MoveToEditPage('+ comboData.COMBO_ID + ')">編集</button></div>');

					if(twitterID != null)
					{
						$("#editButton").append('<a href="https://twitter.com/intent/tweet?text=https://zawa-net.com/dokouni/combo_list.php?comboID=' + comboData.COMBO_ID + '%0a' + comboData.COMBO_NAME + '%20' + comboData.COMBO_DAMAGE + 'ダメージhttps://twitter.com/' + twitterID + '/status/' + comboData.MOVIE_PATH + '/video/1%0a&hashtags=どこUNI"><button>共有</button></a>');
					}
					else
					{
						$("#editButton").append('<a href="https://twitter.com/intent/tweet?text=https://zawa-net.com/dokouni/combo_list.php?comboID=' + comboData.COMBO_ID + '%0a' + comboData.COMBO_NAME + '%20' + comboData.COMBO_DAMAGE + 'ダメージ%0a&hashtags=どこUNI"><button>共有</button></a>');
					}
				// }

				modalOpen('modal-combo-detail');
			}
		}

		// ====================
		// 編集画面へ移動
		// ====================
		function SendEvalutionData(_comboID, _value)
		{
			var loginUserID = getCookie("USER_ID");

			// 送信
			SendEvalution(_comboID, 0, _value, loginUserID, '');

			var evalutionData = GetEvalutionData(_comboID, 0);

			$('#evalGood').remove();
			$('#evalBad').remove();

			$("#combo_detail").append('<div id="evalution" style="display:-webkit-inline-box"></div>');			
			$("#evalution").append('<div id="evalGood" onClick="SendEvalutionData(' + _comboID + ', 1)" class="combo_value" style="height:32px; width:32px; text-align:center; font-size:1.3rem; background-repeat: no-repeat; background-image: url(\'img/buttons/good32.png\');">' + evalutionData.EVALUTION_GOOD + '</div>');
			$("#evalution").append('<div id="evalBad" onClick="SendEvalutionData(' + _comboID + ', 2)" class="combo_value" style="height:32px; width:32px; text-align:center; font-size:1.3rem; background-repeat: no-repeat; background-image: url(\'img/buttons/bad32.png\');">' + evalutionData.EVALUTION_BAD + '</div>');

			 LoacCharacterComboData();
		}

		// ====================
		// 編集画面へ移動
		// ====================
		function MoveToEditPage(comboID)
		{
			var url = location.href;
			var characterID = GetParam('characterID', url);

			window.location.href = "combo_register.php?comboID=" + comboID + "&characterID=" + characterID;
		}

		// ====================
		// 編集画面へ移動
		// ====================
		function MovieDisplay()
		{
			var status = document.getElementById("movieTweet").style.display;

			if(status == 'none')
			{
				document.getElementById("movieTweet").style.display = "block";
				document.getElementById("movieToggle").innerText = "[動画を閉じる]";
			}
			else
			{
				document.getElementById("movieTweet").style.display = "none";
				document.getElementById("movieToggle").innerText = "[動画を見る]";
			}
		}

		// ====================
		// 編集画面へ移動
		// ====================
		function Search(keyWord)
		{
			var table = $('#combo_data').DataTable();
 
			table.search(keyWord).draw();
			modalFadeOut('#modal-combo-detail');
		}

		// =================
		// タグデータのロード
		// =================
		function LoadTagData()
		{
			var tagData = GetTagsData("2");
			if(tagData != null)
			{
				var count = 0;
				var rowID = 1;
				tagData.forEach(function(value)
				{
					// 行ID
					var tagRowID = "tagRow" + rowID;

					var obj = document.getElementById(tagRowID);

					if(obj == null || obj.innerText.length >= 25)
					{
						$("#search_tags").append('<div style="margin:1px 0 4px 0;" id="' + tagRowID + '"></div>');

						if(obj != null)
						{
							rowID++;
						}
					}

					var appendID = '#' + "tagRow" + rowID;
					$(appendID).append('<span class="tag_value_large" style="margin:1px 3px 1px 3px;" onClick="Search(\'' + value.TAG_NAME +'\')">' + value.TAG_NAME + '</span>');
				});
			}

			return true;
		}

		// ====================
		// 編集画面へ移動
		// ====================
		function MoveToRegister()
		{
			var url = location.href;
			var characterID = GetParam('characterID', url);

			window.location.href = "combo_register.php?characterID=" + characterID;
		}

		// =======================
		//  コンボデータを取得
		// =======================
		function LoacCharacterComboData()
		{
			document.getElementById("output").innerHTML = "";
			document.getElementById("characterName").innerHTML = "";

			var url = location.href;

			var comboID = GetParam('comboID', url);
			var characterID = null;

			if(comboID != null)
			{
				// コンボIDがURLに存在する場合
				// コンボ情報からキャラクターIDを取得する
				var comboData = GetComboDetailData(comboID).COMBO_DATA;
				characterID = comboData.CHARACTER_ID;
			}
			else
			{
				// キャラクターIDのみの場合
				// URLから取得する
				characterID = GetParam('characterID', url);
			}
				
			characterData = GetCharacterDetailData(characterID);
			$("#characterName").append(characterData.CHARACTER_NAME + 'のコンボ <span class="topic" onClick="MoveToRegister()">登録</span>');

			data = GetComboDataList(characterID);

			var rowCnt = 0;

			$("#output").append('<table id="combo_data"></table>');
			$("#combo_data").append('<thead id="combo_header"></thead>');
			$("#combo_header").append('<tr id="combo_tr"></tr>');
			$("#combo_tr").append('<th class="comboHeader1">コンボ名</th>');
			$("#combo_tr").append('<th class="comboHeader2">DMG</th>');
			$("#combo_tr").append('<th class="comboHeader3">評価</th>');
			$("#combo_tr").append('<th class="invisibleCol">タグ</th>');
			$("#combo_data").append('<tbody id="combo_body"></tbody>');
			
			if(data != null)
			{
				comboDataList = data;

				data.forEach(function(value)
				{
					// 行を変更する
					$("#combo_body").append('<tr id="row' + rowCnt + '">');
					$('#row' + rowCnt).append('<td><span style="font-size:0.9rem; text-decoration:underline; color:rgba(0, 200, 200, 100)" onClick="MoveToDetail(\'' + value.COMBO_ID + '\');">' + value.COMBO_NAME + '</span></td>');
					$('#row' + rowCnt).append('<td style="text-align:center">' + value.COMBO_DAMAGE + '</td>');
					$('#row' + rowCnt).append('<td><div id="eval' + rowCnt + '" style="width:16px; height:16px; display:-webkit-inline-box"></div></td>');
					$('#eval' + rowCnt).append('<div style="width:16px; height:16px; margin-right:10px; text-align:center; font-size:0.7rem; font-weight:bold; background-image: url(\'img/buttons/good16.png\');">' + value.EVALUTION_GOOD + '</div>');
					$('#eval' + rowCnt).append('<div style="width:16px; height:16px; text-align:center; right:0; font-size:0.7rem; font-weight:bold; background-image: url(\'img/buttons/bad16.png\');">' + value.EVALUTION_BAD + '</div>');
					$('#row' + rowCnt).append('<td class="invisibleCol"><div>' + value.TAGS +'</div></td>');
					rowCnt = rowCnt + 1;
				});
			}

			$("#combo_data").dataTable({
				// 件数切替機能 有効
				lengthChange: true,
						
				// 検索機能 有効
				searching: true,
						
				// ソート機能 有効
				ordering: true,
						
				// 情報表示 有効
				info: true,
						
				// ページング機能 有効
				paging: true,
						
				order: [ [ 2, "desc"] ],
						
				// 件数切替の値を 5,10,15,20 刻みにする
				lengthMenu: [ 5, 7, 10, 15, 20 ],
						
				// 件数のデフォルトの値を 7 にする
				displayLength: 7
			});
			
			return true;
		}


		// =======================
		//  検索用タグデータを取得
		// =======================
		function LoadSearchTags()
		{
			var url = location.href;
			var characterID = GetParam('characterID', url);
			
			characterData = GetCharacterDetailData(characterID);
			$("#characterName").append(characterData.CHARACTER_NAME + 'のコンボ <span class="topic" onClick="MoveToRegister()">登録</span>');

			data = GetComboDataList(characterID);
			document.getElementById("output").innerHTML = "";

			var rowCnt = 0;

			$("#output").append('<table id="combo_data"></table>');
			$("#combo_data").append('<thead id="combo_header"></thead>');
			$("#combo_header").append('<tr id="combo_tr"></tr>');
			$("#combo_tr").append('<th class="comboHeader1">コンボ名</th>');
			$("#combo_tr").append('<th class="comboHeader2">DMG</th>');
			$("#combo_tr").append('<th class="comboHeader3">評価</th>');
			$("#combo_tr").append('<th class="invisibleCol">タグ</th>');
			$("#combo_data").append('<tbody id="combo_body"></tbody>');
			
			if(data != null)
			{
				comboDataList = data;

				data.forEach(function(value)
				{
					// 行を変更する
					$("#combo_body").append('<tr id="row' + rowCnt + '">');
					$('#row' + rowCnt).append('<td><span style="font-size:0.9rem; text-decoration:underline; color:rgba(0, 200, 200, 100)" onClick="MoveToDetail(\'' + value.COMBO_ID + '\');">' + value.COMBO_NAME + '</span></td>');
					$('#row' + rowCnt).append('<td style="text-align:center">' + value.COMBO_DAMAGE + '</td>');
					$('#row' + rowCnt).append('<td><div style="background-color:rgba(100, 255, 100, 100); height:10px; width:' + value.EVALUTION_VALUE * 20 + '%;"></div></td>');
					$('#row' + rowCnt).append('<td class="invisibleCol"><div>' + value.TAGS +'</div></td>');
					rowCnt = rowCnt + 1;
				});
			}

			$("#combo_data").dataTable({
				// 件数切替機能 有効
				lengthChange: true,
						
				// 検索機能 有効
				searching: true,
						
				// ソート機能 有効
				ordering: true,
						
				// 情報表示 有効
				info: true,
						
				// ページング機能 有効
				paging: true,
						
				order: [ [ 2, "desc"] ],
						
				// 件数切替の値を 5,10,15,20 刻みにする
				lengthMenu: [ 5, 7, 10, 15, 20 ],
						
				// 件数のデフォルトの値を 7 にする
				displayLength: 7
			});
			
			return true;
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
			
			setTimeout(function()
			{
				// 検索用のタグ取得
				if(!LoadTagData())
				{
					setTimeout(arguments.callee, 100);
				}
				else
				{
					// キャラクターのコンボ一覧を取得
					if(!LoacCharacterComboData())
					{
						setTimeout(arguments.callee, 100);
					}
					else
					{
						setTimeout(function()
						{
							// ボタンをロード
							$('#functionButtons').load('functions.html');
							$('#menuScript').load('functionsEnabled.html');
							ResumeRayout();
							loaded = true;
							
							var url = location.href;
							var comboID = GetParam('comboID', url);

							if(comboID != undefined && comboID != '' && comboID != null)
							{
								// コンボIDがパラメータにあった場合はモーダルを表示
								MoveToDetail(comboID);
							}
						}, 100);
					}
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

		<div class="location_manager">
			<span class="link" onClick='MoveToLink("combo_character_select.php")'>コンボ検索</span>
			>コンボ一覧
		</div>

		<h3 id="characterName" style="position: relative; color: white; padding:0.25em 0.25em 0.25em 0.25em; background-color: #446689; border-radius:0.5em;"></h3>
		<div class="toggle-wrap">
			<input type="checkbox" id="toggle-checkbox">
			<label class="toggle-button" for="toggle-checkbox">タグ検索</label>
			<div class="toggle-content" id="search_tags">
				
			</div>
		</div><!-- /.toggle-wrap -->
		<div id="output"></div>

		<div id="modal-combo-detail" class="modal">
			<div id="combo_info" style="border-radius:5px; padding:5px; position:static; left:0; top:0; background: linear-gradient(90deg, rgb(22, 135, 237), rgb(20, 55, 90));">
			</div>
		</div>
		<!-- admax -->
		<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
		<!-- admax -->

	</body>
</html>
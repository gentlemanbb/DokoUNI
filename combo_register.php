
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
<script src="js/Functions.js?ver=2019031404"></script>
<script src="js/DragAndDrop.js"></script>
<script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script>
<script>
	var loaded;
	var editMode = false;
	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		var url = location.href;
		var comboID = GetParam('comboID', url);
		
		if(comboID != null && comboID != undefined)
		{
			editMode = true;
			var comboDetailData = GetComboDetailData(comboID);
			var comboData = comboDetailData.COMBO_DATA;
			var tagData = comboDetailData.TAG_DATA;

			document.sendData.comboName.value = comboData.COMBO_NAME;
			document.sendData.comboRecipe.value = comboData.COMBO_RECIPE;
			document.sendData.comboDamage.value = comboData.COMBO_DAMAGE;
			document.sendData.comment.value = comboData.COMMENT;
			document.sendData.useGauge.value = comboData.USE_GAUGE;
			document.sendData.gainGauge.value = comboData.GAIN_GAUGE;
			document.sendData.movieTweetID.value = comboData.MOVIE_PATH;

			var dateTime= new Date();
			var hour = dateTime.getHours();
			var minute = dateTime.getMinutes();
			var second = dateTime.getSeconds();
			var timeStamp = hour + minute + second;
			$("#combo_video").append('<div id="combo_movie"></div>');
			
			if(comboData.MOVIE_PATH != null && comboData.MOVIE_PATH != '')
			{
				// 動画はここ
				var container = document.getElementById("combo_movie");

				twttr.widgets.createTweet
				(
					// 第1引数: ツイートID
					comboData.MOVIE_PATH,
					// 第2引数: コンテナの要素
					container ,	
					{	// 第3引数: パラメータ
						theme: "dark" ,
					}
				);
			}

			var loginUserID = getCookie("USER_ID");
			// 削除ボタンを追加
			if(loginUserID == comboData.TOROKU_USER_ID)
			{
				$("#deleteButton").append('<button onClick="Delete(' + comboID +')">削除</button>');
			}

			// コンボ名
			$("#currentPageName").append(comboData.COMBO_NAME);
			
			// タグ情報を追加
			if(tagData != null && tagData.length > 0)
			{
				var count = 0;
				tagData.forEach(function(value)
				{
					$("#input_tags").append('<span id="' + value.TAG_INFO_ID + '" onClick="removeTag(\'' + value.TAG_INFO_ID + '\', \'' + value.TAG_NAME +'\')">' + value.TAG_NAME + '</span>');
				});
			}
			else
			{
				$("#tags").append('<span>タグが見つかりませんでした。</span>');
			}
		}
		else
		{
			$("#currentPageName").append('コンボ新規登録');
		}

		setTimeout(function()
		{
			if(!LoadTagsData())
			{
				// 呼び出しが完了するまで先に進まない
				setTimeout(arguments.callee, 100);
			}
		}, 100);

		return false;
	});

	// ====================
	//  リンク先へ移動
	// ====================
	function MoveToPrevPage()
	{
		var url = location.href;
		var characterID = GetParam('characterID', url);
		window.location.href = "combo_list.php?characterID=" + characterID;
	}

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

	function LoadTagsData()
	{
		// タグ種別 2 (コンボ) のデータを取得します。
		var data = GetTagsData(2);
		document.getElementById("tags").innerHTML = "";

		// 追加済みのタグは追加しない
		var inputtedTags = [];
				
		var tagChildren = $('#input_tags').children();

		var div = document.getElementById("input_tags");
		var divChildren = div.childNodes;

		for (var i = 0, len = divChildren.length; i < len; i++)
		{
			if (divChildren[i].nodeName === 'SPAN')
			{
				inputtedTags.push(divChildren[i].textContent);
			}
		}

		if(data != null)
		{
			var count = 0;
			data.forEach(function(value)
			{
				var found = inputtedTags.find(function(element)
				{
					return element === value.TAG_NAME;
				});

				if(found === undefined)
				{
					if(count == 0)
					{
						$("#tags").append('<p>');
					}

					$("#tags").append('<span id="' + value.TAG_ID + '" onClick="addTag(\'' + value.TAG_ID + '\', \''+ value.TAG_NAME +'\')">' + value.TAG_NAME + '</span>');

					if(count == 2)
					{
						$("#tags").append('</p>');
						count = 0;
					}
					else
					{
						count++;
					}
				}
			});
		}
		else
		{
			$("#tags").append('<span>タグが見つかりませんでした。</span>');
		}
		
		return true;
	}
	
	// ==========================
	//  描画を再開します
	// ==========================
	function removeTag(tagID, tagName)
	{
		var htmlID = '#' + tagID;
		$(htmlID).remove();
		$("#tags").append('<span id="' + tagID + '" onClick="addTag(\'' + tagID + '\', \''+ tagName +'\')">' + tagName + '</span>');
	}
	
	// ==========================
	//  描画を再開します
	// ==========================
	function addTag(tagID, tagName)
	{
		var htmlID = '#' + tagID;
		$(htmlID).remove();
		$("#input_tags").append('<span id="' + tagID + '" onClick="removeTag(\'' + tagID + '\', \''+ tagName +'\')">' + tagName + '</span>');
	}
	
	// ==========================
	//  描画を再開します
	// ==========================
	function ResumeRayout()
	{
		$("#updatableContents").css({ visibility: "visible" });
	}

	// =========================================================
	//  イベント登録
	// =========================================================
	$(function() {
		$("#SendCombo").click(function()
		{
			// ボタンを押下不可に
			document.sendData.elements["SendCombo"].disabled = true;

			if(editMode)
			{
				var loginUserID = getCookie("USER_ID");
				var url = location.href;
				var comboID = GetParam('comboID', url);
				var comboName = $('#comboName').val();
				var comboRecipe = $('#comboRecipe').val();
				var comboDamage = $('#comboDamage').val();
				var comment = $('#comment').val();
				var useGauge = $('#useGauge').val();
				var gainGauge = $('#gainGauge').val();
				var movieTweetID = $('#movieTweetID').val();

				// タグ配列
				var tags = [];
				
				var tagChildren = $('#input_tags').children();

				var div = document.getElementById("input_tags");
				var divChildren = div.childNodes;

				for (var i = 0, len = divChildren.length; i < len; i++)
				{
					if (divChildren[i].nodeName === 'SPAN')
					{
						tags.push(divChildren[i].textContent);
					}
				}

				var registResult = UpdateCombo(comboID, loginUserID, comboName
					, comboRecipe, comboDamage, comment, useGauge, gainGauge, tags, movieTweetID);

				if(registResult == true)
				{
					alert("コンボを更新しました");
					var url = location.href;
					var characterID = GetParam('characterID', url);
					window.location.href = "combo_list.php?characterID=" + characterID;
				}				
			}
			else
			{
				// 登録時は先にチェック
				var loginUserID = getCookie("USER_ID");
				var url = location.href;
				var characterID = GetParam('characterID', url);

				var comboName = $('#comboName').val();
				var comboRecipe = $('#comboRecipe').val();
				var comboDamage = $('#comboDamage').val();
				var comment = $('#comment').val();
				var useGauge = $('#useGauge').val();
				var gainGauge = $('#gainGauge').val();
				var movieTweetID = $('#movieTweetID').val();

				// タグ配列
				var tags = [];
				
				var tagChildren = $('#input_tags').children();

				var div = document.getElementById("input_tags");
				var divChildren = div.childNodes;

				for (var i = 0, len = divChildren.length; i < len; i++)
				{
					if (divChildren[i].nodeName === 'SPAN')
					{
						tags.push(divChildren[i].textContent);
					}
				}
				
				var registResult = RegistCombo(characterID, loginUserID, comboName
					, comboRecipe, comboDamage, comment, useGauge, gainGauge, tags, movieTweetID);
				
				if(registResult != null)
				{
					alert("コンボを作成しました");
					MoveToPrevPage();
				}
			}

			// ボタンを押下不可に
			document.sendData.elements["SendCombo"].disabled = false;

			return false;
		});
	});

	// ==========================
	//  コンボを削除します。
	// ==========================
	function Delete(comboID)
	{
		var result = window.confirm('本当に削除しますか？');
    
		if(result)
		{
			var result = DeleteCombo(comboID);

			if(result)
			{
				alert('削除に成功しました。');
				MoveToPrevPage();
			}
		}

		return false;
	}
</script>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<!-- ここから下は書き換わる可能性がある -->
<div id="updatableContents">

<div id="main_header">
	<h1>どこＵＮＩ？<img  id="OpenTwitterModal" src="img/buttons/twitter32.png"></h1>
</div>

<div class="location_manager">
		<span class="link" onClick='MoveToLink("combo_character_select.php")'>コンボ検索</span>
		><span class="link" onClick='MoveToPrevPage()'>コンボ一覧</span>
		><span id="currentPageName"></span>
</div>

<div id="output"></div>
<div class="sendWrapper" id="sendDataForm">
	<span class="box-title">コンボを登録する</span>
		<form id="sendData" name="sendData">
		<div>		
		<p>
			<label for="label_comment" accesskey="n">コンボ名：</label><br/>
			<input type="text" name="comboName" id="comboName" style="width:200px" />
		</p>
		<p>
			<label for="label_comment" accesskey="n">レシピ：</label><br/>
			<input type="text" name="comboRecipe" id="comboRecipe" style="width:90%" />
		</p>
		<p>
			<label for="label_comment" accesskey="n">コンボダメージ(数字)：</label><br/>
			<input type="text" name="comboDamage" id="comboDamage" style="width:60px" />
		</p>
		<p>
			<label for="label_comment" accesskey="n">コメント：</label><br/>
			<input type="text" name="comment" id="comment" style="width:90%"/>
		</p>
		<p>
			<label for="label_comment" accesskey="n">使用ゲージ(数字)：</label><br/>
			<input type="text" name="useGauge" id="useGauge" style="width:50px" />%
		</p>
		<p>
			<label for="label_comment" accesskey="n">増加ゲージ(数字)：</label><br/>
			<input type="text" name="gainGauge" id="gainGauge" style="width:50px"/>%
		</p>

		<p>
		<label for="label_tags" accesskey="n">タグ：</label>
			<div id="input_tags" style="background-color:rgb(100,100,100); width:80%" ondragover="f_dragover(event)" ondrop="f_drop(event)"></div>
		</p>

		<p>
			<label for="label_tags" accesskey="n">タグ一覧：</label><br/>
			<div id="tags" style="background-color:rgb(100,100,100); width:80%"ondragover="f_dragover(event)" ondrop="f_drop(event)"></div>
		</p>
		<!-- 動画設定 -->
		<div id="combo_video"></div>
		<label for="label_movie" accesskey="n">動画付きツイートID：</label>
		<input type="text" name="movieTweetID" id="movieTweetID" style="width:250px"/>

		
		<button type="submit" class="noBorderButton" id="SendCombo" name="SendCombo" onsubmit="return false;">
			<a class="btn" href="javascript:void(0)">送信</a>
		</button>
		</div>
	</form>
	
	<div id="deleteButton"></div>
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



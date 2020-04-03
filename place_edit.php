
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/ValidationUtil.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Functions.js?ver=201812230001"></script>
<script src="js/modal.js?ver=20181222"></script>
<script src="https://maps.google.com/maps/api/js?key=AIzaSyCzzALgxyJwwdoI5VQWRUcpeqpEdHGfIdA"></script>
<script>
	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		SuspendRayout();

		setTimeout(function()
		{
			if(!LoadPlaceData()) {
				setTimeout(arguments.callee, 100);
			}
			else {
					$('#functionButtons').load('functions.html');
					$('#menuScript').load('functionsEnabled.html');
					ResumeRayout();
			}
		});
	});

	/* ---------------------------
	//  場所データをロードします。
	// --------------------------- */
	function LoadPlaceData()
	{
		var placeID = getCookie("PLACE_ID");
		var placeData = GetCurrentPlaceData(placeID);

		if(placeData != null)
		{
			$('#officialName').append(placeData.OFFICIAL_NAME);
			document.placeData.placeName.value = placeData.PLACE_NAME;

			// 画像用のタイムスタンプ
			var dateTime= new Date();
			var hour = dateTime.getHours();
			var minute = dateTime.getMinutes();
			var second = dateTime.getSeconds();
			var timeStamp = hour + minute + second;
			
			if(placeData.IMAGE_PATH != null && placeData.IMAGE_PATH != '')
			{
				$('#placeImage').append('<img src="' + placeData.IMAGE_PATH + '?' + timeStamp + '" style="width:90%">');
			}

			if(placeData.ADDRESS != null && placeData.ADDRESS != '')
			{
				document.getElementById("map").style.display="block";
				document.placeData.address.value = placeData.ADDRESS;
				DrawMap(placeData.ADDRESS, 'map');
			}
			else
			{
				document.getElementById("map").style.display="none";
			}

			document.placeData.comment.value = placeData.COMMENT;
			document.placeData.placeName.value = placeData.PLACE_NAME;
		}

		return true;
	}
	
	/* ---------------------------
	//  バリデーション
	//  引数：更新APIへの引数
	// --------------------------- */
	function Validating(argData)
	{
		var strMessage = '';
		var result = true;

		// バリデーションの代わり
		if(argData.placeName.length > 32)
		{
			strMessage = '「ゲームセンター名」 32文字以内<br/>';
		}

		if(argData.comment.length > 1024)
		{
			strMessage = strMessage + '「紹介」 1024文字以内<br/>';
		}

		if(strMessage != '')
		{
			displayMessage = '入力内容にエラーがあります。<br/>' + strMessage;
			alert(displayMessage);
			result = false;
		}

		return result;
	}

	/* ---------------------------
	//  更新ボタンイベント
	// --------------------------- */
	$(document).ready(function(){
		$('#BtnPlaceUpdate').click(function()
		{
			var _placeID = getCookie("PLACE_ID");
			var _placeName = $('#placeName').val();
			var _address = $('#address').val();
			var _imagePath = $('#imagePath').val();
			var _comment = $('#comment').val();
			var _userID = getCookie("USER_ID");
				
			var argData = {
				placeID : _placeID,
				placeName : _placeName,
				address : _address,
				imagePath : _imagePath,
				comment : _comment,
				userID : _userID
			};

			// バリデーションの代わり
			if(!Validating(argData))
			{
				return;
			}
			
			var result = UpdateCurrentPlaceData(_placeID, _placeName, _address, _imagePath, _comment, _userID);

			if(result)
			{
				alert('更新に成功しました。');
			}

			return false;
		});
	});

	// ==========================
	//  簡易的に描画を停止します
	// ==========================
	function ChangeFile()
	{
		//フォームのデータを変数formに格納
		var form = $('#placeData').get()[0];
						
		//FormData オブジェクトを作成
		var formData = new FormData(form);
		var placeID = getCookie("PLACE_ID");

		var newImagePath = UploadPlaceImage(formData, placeID, 'PLACE_IMAGE');

		var dateTime= new Date();
		var hour = dateTime.getHours();
		var minute = dateTime.getMinutes();
		var second = dateTime.getSeconds();
		var timeStamp = hour + minute + second;
		
		document.getElementById("placeImage").innerHTML = '<img src="' + newImagePath + '?' + timeStamp + '" style="width:100%;">';
	}

	// ==========================
	//  前画面に戻る
	// ==========================
	function ReturnPrevPage()
	{
		window.location.href = "place_detail.php";
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

<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
</head>
<body>
	<div id="functionButtons"></div>
	<div id="menuScript"></div>

	<div id="main_header">
		<h1>どこＵＮＩ？</h1>
	</div>
	<div class="sendWrapper">
		<span class="box-title">ゲーセン情報</span>
		<form method="post" id="placeData" name="placeData">
		<div>
			<p id="officialName"></p>
			<p id="placeImage">
			</p>
			
			<p>
				<input type="file" id="file_name" name="file_name" onchange="ChangeFile()" />
			</p>

			<div id="map" style="width:95%; height:200px; background-color:white!important">
			</div>

			<p>
				通称：<input type="text" name="placeName" id="placeName" />
			</p>
			<p>
				住所：<input type="text" name="address" id="address" />
			</p>
			<p>
				<textarea row="3" name="comment" id="comment" style="height:3.5em; width:80%"></textarea>（1024文字以内）
			</p>

			<p>
				<button type="submit" class="noBorderButton" id="BtnPlaceUpdate" name="BtnPlaceUpdate" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">更新</a>
				</button>
			</p>
		</div>
	</form>
	</div>

	<button class="noBorderButton" onClick="ReturnPrevPage(); return false;">
		<a class="btn" href="javascript:void(0)">戻る</a>
	</button>
<br/>

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
	

</body>
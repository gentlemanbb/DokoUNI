
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/ValidationUtil.js"></script>
<script src="js/Cookie.js"></script>

<script>

	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function(){
		var placeID = getCookie("PLACE_ID");
		
		SuspendRayout();
		
		setTimeout(function(){
			if(!LoadAreaData()) {
			}
			else {
				setTimeout(function() {
					if(!LoadPlaceData(placeID)) {
					}
					else {
						$('#functionButtons').load('functions.html');
						$('#menuScript').load('functionsEnabled.html');
						ResumeRayout();
					}
				}, 100);
			}
		}, 100);
	});
	
	// =================
	//  場所のロード
	// =================
	function LoadPlaceData(_placeID){
		
		var argData ={
			placeID : _placeID
		}
		
		$.ajax({
			type: "POST",
			url: "php/GetPlaceDetailAPI.php",
			data : argData,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					// 行を変更する
					document.getElementById("placeID").value = data.PLACE_DATA.PLACE_ID;
					document.getElementById("officialName").innerHTML = data.PLACE_DATA.PLACE_NAME;
					document.getElementById("placeName").value = data.PLACE_DATA.PLACE_NAME;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return true;
	}
	
	// =================
	//  場所のロード
	// =================
	function LoadAreaData(){
		var argData ={
			areaID : getCookie("AREA_ID")
		}
				
		$.ajax({
			type: "POST",
			url: "php/GetAreaAPI.php",
			data : argData,
			success: function(data)
			{
				data.forEach(function(value){
				   // 行を変更する
				   $("#areaID").append('<option value=' + value.AREA_ID + '>' + value.AREA_NAME + '</option>');
				});
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return true;
	}
	
	$(document).ready(function(){
		$('#BtnPlaceUpdate').click(function(){
			var argData = {
				placeID :$('#placeID').val(),
				placeName :$('#placeName').val(),
				areaID :$('#areaID').val()
			};
			
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: "php/UpdatePlaceAPI.php",
				data: argData,

				success: function(data, dataType)
				{
					// 結果
					if(data.RESULT == true){
						alert("設定しました。ご協力ありがとうございます。");
						window.location.href = "unknown_place.php";
					}
					else
					{
						if(data.MESSAGE != null)
						{
							alert(data.MESSAGE);
						}
					}
				},

				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					alert('error : ' + errorThrown);
				}

			});

			return false;
		});
	});
	
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
</head>
<body>
<div id="main_header">
    <h1>どこＵＮＩ？</h1>
</div>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<div class="sendWrapper">
	<span class="box-title">
		地域設定
	</span>
	<form method="post" id="sendData" name="sendData">
		<div>
			<input type="hidden" id="placeID" value="">
			<p>
				<label for="label_place_name" accesskey="n">公式名称（Twitter名称）：</label><br/>
				<div id="officialName"></div>
			</p>
			<p>
				<label for="label_place_name" accesskey="n">通称：</label><br/>
				<input type="text"  id="placeName" cols="20" rows="1"></textarea>
			</p>
			<p>
				<label for="label_place" accesskey="n">地域：</label><br/>
				<select class="formComboBox" name="areaID" id="areaID">
				</select>
			</p>
			<br/>
			<p>
				<button type="submit" class="noBorderButton" id="BtnPlaceUpdate" name="BtnPlaceUpdate" onsubmit="return false;">
					<a class="btn" href="javascript:void(0)">設定</a>
				</button>
			</p>
		</div>
	</form>
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
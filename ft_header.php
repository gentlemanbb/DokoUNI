<html>
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/ft_header_rayout.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<body>

<script>
	// ========================
	//  現在のカウントを取得
	// ========================
	function GetFTCount()
	{
		var args = null;
		$.ajax({
			type: "POST",
			url: "php/GetFTCountAPI.php",
			data: args,
			success: function(data)
			{
				document.getElementById("player1_count").innerHTML = "";
				
				if(data != false)
				{
					data.forEach(function(value)
					{
						document.getElementById("player1_count").innerHTML = value.PLAYER1_COUNT;
						document.getElementById("player2_count").innerHTML = value.PLAYER2_COUNT;
						document.getElementById("player1_name").innerHTML = value.PLAYER1_NAME;
						document.getElementById("player2_name").innerHTML = value.PLAYER2_NAME;
					});
				}
				else
				{
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
	
	// ========================
	//  カウントを更新
	// ========================
	function UpdateCount(_player1Count, _player2Count, _player1Name, _player2Name)
	{
		var _ftUserID = 'gentlemanbb';
		var _password = 'passw0rd';
		var _number = 1;
		var args = {
			ftUserID : _ftUserID,
			number : _number,
			player1Count : _player1Count,
			player2Count : _player2Count,
			player1Name : _player1Name,
			player2Name : _player2Name,
			password : _password
		};
		$.ajax({
			type: "POST",
			url: "php/UpdateFTCountAPI.php",
			data: args,
			success: function(data)
			{
				if(data == true)
				{
				}
				else
				{
					return false;
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
	// ===================
	// プレーヤー１カウントアップ
	// ===================
	$(function() {
		$("#Player1CountUp").click(function(){
			var str1 = $('#player1_count').text();
			var str2 = $('#player2_count').text();
			var count1 = Number(str1) + 1;
			var count2 = Number(str2);
			var P1Name = $('#player1Name').val();
			var P2Name = $('#player2Name').val();
			
			SuspendRayout();
			setTimeout(function(){
				if(!UpdateCount(count1, count2, P1Name, P2Name)){
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetFTCount()){
							setTimeout(arguments.callee, 100);
						}
						else{
							ResumeRayout();
						}
					}, 100);
				}
			}, 100);
		});
	});
	
	// ===================
	// プレーヤー２カウントアップ
	// ===================
	$(function() {
		$("#Player2CountUp").click(function(){
			var str1 = $('#player1_count').text();
			var str2 = $('#player2_count').text();
			var count1 = Number(str1);
			var count2 = Number(str2) + 1;
			var P1Name = $('#player1Name').val();
			var P2Name = $('#player2Name').val();
			
			SuspendRayout();
			setTimeout(function(){
				if(!UpdateCount(count1, count2, P1Name, P2Name)){
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetFTCount()){
							setTimeout(arguments.callee, 100);
						}
						else{
							ResumeRayout();
						}
					}, 100);
				}
			}, 100);
		});
	});
	// ===================
	// プレーヤー１カウントダウン
	// ===================
	$(function() {
		$("#Player1CountDown").click(function(){
			var str1 = $('#player1_count').text();
			var str2 = $('#player2_count').text();
			var count1 = Number(str1) - 1;
			var count2 = Number(str2);
			var P1Name = $('#player1Name').val();
			var P2Name = $('#player2Name').val();
			
			SuspendRayout();
			setTimeout(function(){
				if(!UpdateCount(count1, count2, P1Name, P2Name)){
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetFTCount()){
							setTimeout(arguments.callee, 100);
						}
						else{
							ResumeRayout();
						}
					}, 100);
				}
			}, 100);
		});
	});
	
	// ===================
	// プレーヤー２カウントダウン
	// ===================
	$(function() {
		$("#Player2CountDown").click(function(){
			var str1 = $('#player1_count').text();
			var str2 = $('#player2_count').text();
			var count1 = Number(str1);
			var count2 = Number(str2) - 1;
			var P1Name = $('#player1Name').val();
			var P2Name = $('#player2Name').val();
			
			SuspendRayout();
			setTimeout(function(){
				if(!UpdateCount(count1, count2, P1Name, P2Name)){
					setTimeout(arguments.callee, 100);
				}
				else {
					setTimeout(function(){
						if(!GetFTCount()){
							setTimeout(arguments.callee, 100);
						}
						else{
							ResumeRayout();
						}
					}, 100);
				}
			}, 100);
		});
	});
				
	// ===================
	// ロード
	// ===================
	$(function() {
		GetFTCount();
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
	
		// ==========================
	//  描画を再開します
	// ==========================
	function InputName()
	{
		var str1 = $('#player1_count').text();
		var str2 = $('#player2_count').text();
		var count1 = Number(str1);
		var count2 = Number(str2) - 1;
		var P1Name = $('#player1Name').val();
		var P2Name = $('#player2Name').val();
		
		SuspendRayout();
		setTimeout(function(){
			if(!UpdateCount(count1, count2, P1Name, P2Name)){
				setTimeout(arguments.callee, 100);
			}
			else {
				setTimeout(function(){
					if(!GetFTCount()){
						setTimeout(arguments.callee, 100);
					}
					else{
						ResumeRayout();
					}
				}, 100);
			}
		}, 100);
	}
	
	
</script>
<body>

<div id="updatableContents">
	<div id="ft_header">
		<div id="player1_count">0</div>
		<div id="player1_name">PLAYER1</div>
		<div id="player2_name">PLAYER2</div>
		<div id="player2_count">0</div>
	</div>

	<div id="Player1_Counter">
		<p>プレーヤー１</p>
		<p>
			<span id="Player1CountUp"><button>Win</button></span>
			<span id="Player1CountDown"><button>Lose</button></span>
		</p>
	</div>

	<div id="Player2_Counter">
		<p>プレーヤー２</p>
		<p>
			<span id="Player2CountUp"><button>Win</button></span>
			<span id="Player2CountDown"><button>Lose</button></span>
	</div>
	
	<input type="text" id="player1Name" onChange="InputName(); return false;" /></input>
	<input type="text" id="player2Name" onChange="InputName(); return false;"></input>
</div>

</body>
</html>
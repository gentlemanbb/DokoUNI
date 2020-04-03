<!DOCTYPE html>
<html>
	<head>
		<title>どこUNI？</title>
		<link rel="stylesheet" href="css/form.css" type="text/css">
		<link rel="stylesheet" href="css/rayout.css" type="text/css">
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
		<meta charset="utf-8">
		
		<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
		<script src="../js/Cookie.js"></script>
		<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
		
	</head>
	<body>
		<script>
			/*	
			$(document).ready(function(){
$				document.sendData.UserID.value =  getCookie("INPUT_USER_ID");
				
				$('#BtnLogin').click(function(){
					var data = {
						userID : $('#UserID').val(),
						password :$('#Password').val()
					};
					
					setCookie('USER_ID', '', -3600, '/');
					
					$.ajax({
						type: "POST",
						dataType: 'json',
						url: "../API/LoginAPI.php",
						data: data,
						
						success: function(data, dataType)
						{
							alert(data.RESULT);
							if(data.RESULT == true)
							{
								// ログイン成功時
								// クッキー設定
								setCookie("USER_NAME", data.USER_NAME, 30);
								setCookie("USER_ID", data.USER_ID, 30);
								setCookie("INPUT_USER_ID", data.USER_ID, 30);
								setCookie("SYSTEM_MANAGEMENT", data.SYSTEM_MANAGEMENT, 30);
								setCookie("REGIST_EVENT", data.REGIST_EVENT, 30);
								setCookie("LOGIN", data.LOGIN, 30);
								setCookie("AREA_ID", data.AREA_ID, 30);
								
								window.location.href = "where.php";
							}
							else
							{
								// 返却されたメッセージを取得
								var message = data.MESSAGE;
								
								if(message == null || message == "undefined")
								{
									message = "ログインに失敗しました。";
								}
								
								// メッセージ出力
								alert(message);
							
								// 失敗
								return false;
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
			
			$(document).ready(function(){
				$('#NoLoginUse').click(function(){
					var dt = new Date('1999-12-31T23:59:59Z');
					document.cookie = null;
					document.cookie = "INPUT_USER_ID=; expires=" + dt.toUTCString();
					document.cookie = "USER_ID=; expires=" + dt.toUTCString();
					document.cookie = "USER_NAME=; expires=" + dt.toUTCString();
					window.location.href = "where.php";
					
					return false;
				});
			});*/
			
		</script>

		<div id="contents_wrapper">
			<div class="loginWrapper">
				<span class="box-title">どこUNI？ログイン</span>
				<!-- フォーム送信時にcheckText関数を実行する -->
				<form name="loginForm" onsubmit="login(); return false;">
					<input name="userID" type="text" />
					<input name="password" type="text" />
					<input type="submit" value="送信" />
				</form>
				<script>
					function login() {
						var data = {
							userID   : document.loginForm.userID.text,
							password : document.loginForm.password.text,
						};
						
						alert('START');
						setCookie('USER_ID', '', -3600, '/');
						
						$.ajax({
							//  リクエストの内容
							url: '../API/LoginAPI.php',
							dataType: "json",
							data: send_data,
							
							//  レスポンス成功時の処理
							success: function(responce) {
								
								alert('A');
								if (responce.result === "OK") {
									console.log(responce);
									$('div[data-result=""]').html(JSON.stringify(responce));
								} else {
									console.log(responce);
									$('div[data-result=""]').html(JSON.stringify(responce));
								}
								
								return false;
							},
							
							//  レスポンス失敗時の処理
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								
								alert('B');
								console.log(XMLHttpRequest);
								console.log(textStatus);
								console.log(errorThrown);
								$('div[data-result=""]').html(JSON.stringify("データ取得中にエラーが発生しました。"));
								return false;
							}
						});

						$.ajax({
							type: "POST",
							dataType: 'json',
							url: "../API/LoginAPI.php",
							data: data,
						
							success: function(data, dataType)
							{
								alert('END');
								if(data.RESULT == true)
								{
									// ログイン成功時
									// クッキー設定
									setCookie("USER_NAME", data.USER_NAME, 30);
									setCookie("USER_ID", data.USER_ID, 30);
									setCookie("INPUT_USER_ID", data.USER_ID, 30);
									setCookie("SYSTEM_MANAGEMENT", data.SYSTEM_MANAGEMENT, 30);
									setCookie("REGIST_EVENT", data.REGIST_EVENT, 30);
									setCookie("LOGIN", data.LOGIN, 30);
									setCookie("AREA_ID", data.AREA_ID, 30);
									
									window.location.href = "where.php";
								}
								else
								{
									// 返却されたメッセージを取得
									var message = data.MESSAGE;
									
									if(message == null || message == "undefined")
									{
										message = "ログインに失敗しました。";
									}
									
									// メッセージ出力
									alert(message);
									
									// 失敗
									return false;
								}
							},
							
							error: function(XMLHttpRequest, textStatus, errorThrown)
							{
								alert('error : ' + errorThrown);
							}
						});
						alert('END2');
						return false;
					}
				</script>
			</div>
			
			<div id="footer" class="topic">どこUNI？は、2D格闘ゲーム『UNDER NIGHT IN-BIRTH』のアーケード版をプレーするにあたって、対戦相手を探したり、普段流行っているゲームセンターを探す支援をするWebサービスです。<br/>
				<a href="register.php">
					<button>新規登録</button>
				</a>
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
</html>

<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/ValidationUtil.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Encode.js"></script>
<script src="js/sh/sha256.js"></script>

<script>
    $(document).ready(function(){
        $('#BtnRegist').click(function(){
            var isTest = false;
            
            if(isTest == true){
                alert('現在新規登録停止中です');
                return false;
            }
            var password1 = $('#Password1').val()
            var password2 = $('#Password2').val()

            // バリデーションの代わり
            if(password1 < 8 && password1 <= 20){
                alert("パスワードは8文字以上20文字以下でなければいけません");
                return false;
            }

            if(!InputCheck(password1, "パスワード")){
                return false;
            }

            if(password1 != password2){
                alert("２つのパスワードが一致しません");
                return false;
            }

            var argData = {
                userID : $('#UserID').val(),
                password1 :password1,
                userName :$('#UserName').val()
            };


            // バリデーションの代わり
            if(argData.userID.length < 6){
                alert("ユーザIDは6文字以上でなければいけません");
                return false;
            }

            // バリデーションの代わり
            if(argData.userName.length < 2){
                alert("ユーザ名は2文字以上でなければいけません");
                return false;
            }


            if(!InputCheck(argData.userID, "ユーザID")){
                return false;
            }

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "php/RegistUserAPI.php",
                data: argData,

                success: function(data, dataType)
                {
                    // ログイン結果
                    data.forEach(function(value){
                        if(value.RESULT != false){
                            alert("ユーザーを作成しました");
                            // ログイン成功時
                            setCookie("USER_NAME", value.USER_NAME, 1);
                            setCookie("USER_ID", value.USER_ID, 1);

                            window.location.href = "where.php";
                        }
                        else
                        {
                            alert("失敗：" + value.MESSAGE);
                        }
                    });             
                },

                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert('error : ' + errorThrown);
                }

            });

            return false;
        });
    });

</script>
</head>
<body>
    <div class="sendWrapper">
        <span class="box-title">新規登録</span>
        <form method="post" id="sendData">
        <div>
            <p>
                <label for="label_user_id" accesskey="n">ユーザID：</label><br/>
                <input type="text" id="UserID" cols="20" rows="1"></textarea>
            </p>
            <p>
                <label for="label_user_name" accesskey="n">ユーザ名：</label><br/>
                <input type="text"  id="UserName" cols="20" rows="1"></textarea>
            </p>
            <p>
                <label for="label_password1" accesskey="n">パスワード：</label><br/>
                <input type="text"  id="Password1" cols="20" rows="1"></textarea>
            </p>
            <p>
                <label for="label_password2" accesskey="n">パスワード（再確認）：</label><br/>
                <input type="text"  id="Password2" cols="20" rows="1"></textarea>
            </p>

            <p>
                <input id="BtnRegist" value="送信" type="submit" onsubmit="return false;" />
                <p class="mini">
                    Twitter備え付きのブラウザだと正常に登録が行えない可能性があります。<br/>
                    登録ができない場合、Safari, FireFox, Chrome等でお試しください。
                </p>
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
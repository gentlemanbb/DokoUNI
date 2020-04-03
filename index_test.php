
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
<script src="js/Cookie.js"></script>
<script>
    $(document).ready(function(){
        document.sendData.UserID.value =  getCookie("INPUT_USER_ID");

        $('#BtnLogin').click(function(){
            var data = {
                userID : $('#UserID').val(),
                password :$('#Password').val()
            };

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "php/LoginAPI_test.php",
                data: data,

                success: function(data, dataType)
                {
                    data.forEach(function(value){
                        if(value.RESULT != false){
                            // ログイン成功時
                            // クッキー設定
                            setCookie("USER_NAME", value.USER_NAME, 30);
                            setCookie("USER_ID", value.USER_ID, 30);
                            setCookie("INPUT_USER_ID", value.USER_ID, 30);
                            setCookie("INPUT_PASSWORD", $('#Password').val(), 30);
                            setCookie("SYSTEM_MANAGEMENT", value.SYSTEM_MANAGEMENT, 30);
                            setCookie("REGIST_EVENT", value.REGIST_EVENT, 30);
                            setCookie("LOGIN", value.LOGIN, 30);
                            setCookie("AREA_ID", value.AREA_ID, 30);

                            window.location.href = "where.php";
                        }
                        
                        else
                        {
                            alert('ログインに失敗しました');
                            return false;
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


    $(document).ready(function(){
        $('#NoLoginUse').click(function(){
            var dt = new Date('1999-12-31T23:59:59Z');
            document.cookie = "INPUT_USER_ID=; expires=" + dt.toUTCString();
            document.cookie = "USER_ID=; expires=" + dt.toUTCString();
            document.cookie = "USER_NAME=; expires=" + dt.toUTCString();
            window.location.href = "where.php";

            return false;
        });
    });
</script>
</head>
<body>

    <div id="contents_wrapper">
        <div class="loginWrapper">
            <span class="box-title">どこUNI？ログイン</span>
            <form method="post" id="sendData" name="sendData">
                <div>
                    <p>
                        <label for="label_place" accesskey="n">ユーザID：</label><br/>
                        <input type="text" id="UserID" name="UserID" cols="20" rows="1"></textarea>
                    </p>
                    <p>
                        <label for="label_place" accesskey="n">パスワード：</label><br/>
                        <input type="text" id="Password" name="Password" cols="20" rows="1"></textarea>
                    </p>

                    <br/>

                    <p>
                        <button type="submit" class="noBorderButton" id="BtnLogin" name="BtnLogin" onsubmit="return false;">
                            <a class="btn" href="javascript:void(0)">ログイン</a>
                        </button>

                        <button type="submit" class="noBorderButton" id="NoLoginUse" name="NoLoginUse" onsubmit="return false;">
                            <a class="btn" href="javascript:void(0)">ノーログイン</a>
                        </button><span class="mini"> (環境によってはログイン情報がクリアされません)</span>
                    </p>
                </div>
            </form>
        </div>
    <div id="footer">
        <a href="register.php"><button>新規登録</button></a>
    </div>
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
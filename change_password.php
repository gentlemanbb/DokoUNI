
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/ValidationUtil.js"></script>
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

        var data = {
            userID : userID,
        };

        $.ajax({
            type: "POST",
            url: "php/GetUserData.php",
            data: data,
            success: function(data)
            {
                data.forEach(function(value){
                    document.sendData.RIP.value = value.RIP;
                    document.sendData.playerName.value = value.USER_NAME;
                    $("#characterID").val(value.MAIN_CHARACTER_ID); 
                    document.sendData.twitterAccount.value = value.TWITTER;
                    $("#noticeType").val(value.NOTIFICATION); 
                    $("#areaID").val(value.AREA_ID); 
                    $("#authorityName").append(value.AUTHORITY_NAME); 
                    $("#sendCount").append(value.SEND_COUNT + "回 (" + value.RANK + "位)");
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
    //  ロード完了イベント
    // =========================
    $(document).ready(function(){
        return false;
    });

</script>
</head>
<body>

<script>
    $(function() {
        $("#ChangePassword").click(function(){

            // ログインユーザＩＤ
            var loginUserID = getCookie("USER_ID");
            var oldPassword = $('#oldPassword').val()
            
            var password1 = $('#newPassword1').val()
            var password2 = $('#newPassword2').val()
            if(password1 != password2)
            {
                alert("２つのパスワードが一致しません");
                return false;
            }
            // バリデーションの代わり
            if(password1 < 8 && password1 <= 20){
                alert("パスワードは8文字以上20文字以下でなければいけません");
                return false;
            }            
            
            // 引数
            var args = {
                userID : loginUserID,
                oldPassword : oldPassword,
                newPassword : password1
            };

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "php/ChangePasswordAPI.php",
                data: args,
                success: function(data, dataType){
                    
                    if(data != false){

                        // 更新結果
                        data.forEach(function(value){

                            if(value.RESULT != false)
                            {
                                // ログイン成功時
                                window.location.href = "user_page.php";

                            }
                            else
                            {
                                alert(value.MESSAGE);
                            }
                        });
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

    $(function() {
        $("#MoveAllData").click(function(){
           window.location.href = "where.php";
        });
    });

</script>


<div id="main_header">
    <h1>どこＵＮＩ？</h1>
</div>
<button class="noBorderButton" id="MoveAllData">
    <a class="btn" href="javascript:void(0)">一覧に戻る</a>
</button>

<div class="sendWrapper">
    <form id="sendData" name="sendData">
        <div>
        	<p>
        	    <label for="label_player_name" accesskey="n">古いパスワード：</label><br/>
        	    <input type="text"  id="oldPassword" cols="20" rows="1"></textarea>
        	</p>
        	<p>
        	    <label for="label_RIP" accesskey="n">新しいパスワード：</label><br/>
        	    <input type="text"  id="newPassword1" cols="20" rows="1"></textarea>
        	</p>
        	<p>
        	    <label for="label_twitter" accesskey="n">新しいパスワード（確認）：</label><br/>
        	    <input type="text"  id="newPassword2" cols="20" rows="1"></textarea>
        	</p>
        	
        	<br/>
        	<button class="noBorderButton" type="submit" id="ChangePassword" name="ChangePassword" onsubmit="return false;">
        	    <a class="btn" href="javascript:void(0)">更新</a>
        	</button>
        </div>
    </form>
</div>

<div id="userSupportWrapper">
    <p>問い合わせ一覧</p>
    <div id="userSupport"></div>
</div>
</body>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
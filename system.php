
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

<script>
    $(function() {
        var loginUserID = getCookie("LOGIN_USER_ID");
        CheckUser(loginUserID);
    });

    // ========================
    //  ユーザデータのロード
    // ========================
    function CheckUser(){
        var userID = getCookie("USER_ID");

        var args = {
            userID : userID,
        };

       $.ajax({
            type: "POST",
            dataType: "json",
            url: "php/CheckSystemUser.php",
            data: args,
            success: function(data)
            {
            	document.getElementById("output").innerHTML = "";
            	
                if(data.RESULT == true)
                {
                	$("#output").append('<div id="text">Welcome System Master</div>');
                }
                else
                {
                    alert("不正なアクセスを検知しました。");
                    window.location.href = "index.php";
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

    $(function() {
        $("#MoveUserManagementPage").click(function(){
            window.location.href = "user_management.php";
        });
    });


    $(function() {
        $("#MoveSupportPage").click(function(){
            window.location.href = "support_management.php";
        });
    });

</script>
<body id="systemManagement">

<div id="main_header">
    <h1>システム管理画面</h1>
</div>
<div id="functions">
    <span id="functions">
        <button class="noBorderButton" type="submit" id="MoveSystemManagementPage" name="MoveSystemManagementPage">
            <a class="btn" href="javascript:void(0)">システム管理</a>
        </button>
        <button class="noBorderButton" type="submit" id="MoveUserManagementPage" name="MoveUserManagementPage">
            <a class="btn" href="javascript:void(0)">ユーザー管理</a>
        </button>
        <button class="noBorderButton" type="submit" id="MoveSupportPage" name="MoveSupportPage">
            <a class="btn" href="javascript:void(0)">問い合わせ一覧</a>
        </button>
    </span>
</div>

<div id="output"></div>
<br/>
<br/>
</body>
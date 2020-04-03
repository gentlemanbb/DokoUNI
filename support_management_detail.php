
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
        setTimeout(function(){
            if(!LoadSupportStatus()) {
                setTimeout(arguments.callee, 100);
            }
            else {
                LoadSupportDetailData();
            }
        });
    });


    $(function() {
        $("#UpdateSupport").click(function(){
            var args = {
                supportID : getCookie("SUPPORT_ID"),
                replyText :  $('#replyText').val(),
                statusType : $('#statusType').val()
            };

            // ボタンを押下不可に
            document.sendData.elements["UpdateSupport"].disabled = true;

            $.ajax({
                type: "POST",
                url: "php/UpdateSupportAPI.php",
                data: args,
                success: function(jsonData){
                    // 処理を記述
                    return false;
                },

                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    console.log('error : ' + errorThrown);
                    return false;
                },

                complete: function(){
                    alert("更新しました");
                    window.location.href = "support_management.php";
                }
            });

            return false;
        });
    });

    // ========================
    //  目的のロード
    // ========================
    function LoadSupportStatus(){

        var args = {
             key : "SUPPORT_STATUS",
        };

        $.ajax({
            type: "POST",
            url: "php/GetType.php",
            data: args,
            success: function(data)
            {
                data.forEach(function(value){
                    // 行を変更する
                    $("#statusType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
                });
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
    //  ユーザチェック
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
                if(data != true){
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

    // ========================
    //  問い合わせデータの取得
    // ========================
    function LoadSupportDetailData(){
        var targetSupportID = getCookie("SUPPORT_ID");

        args = {
            supportID: targetSupportID
        };

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "php/GetSupportDetailAPI.php",
            data: args,
            success: function(data)
            {
                var isFirst = true;
                var rowCnt = 0;
                
                data.forEach(function(value){
                    $("#categoryCaption").append(value.CATEGORY_CAPTION);
                    $("#supportText").append("<p>"+value.TEXT.replace(/\r?\n/g, '<br>')+"</p>");
                    document.sendData.statusType.value = value.STATUS;
                    document.sendData.replyText.value = value.SUPPORT_RESULT;
                });
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
        $("#MoveSystemManagementPage").click(function(){
            window.location.href = "system.php";
        });
    });

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

    <div class="sendWrapper">
        <span class="box-title">問い合わせ詳細</span>
        <form method="post" name="sendData" id="sendData">
        <div>
            <p>
                <label for="label_category" accesskey="n">カテゴリ：</label><br/>
                    <div id="categoryCaption"></div>
                </select>
            </p>
            <p>
                <label for="label_status" accesskey="n">ステータス：</label><br/>
                <select class="formComboBox" name="statusType" id="statusType">
                </select>
            </p>
            <p>
                <label for="label_text" accesskey="n">内容：</label><br/>
                <div id="supportText"></div>
            </p>
            <p>
                <label for="label_reply" accesskey="n">返信：</label><br/>
                <textarea name="replyText" id="replyText" rows="5" cols="60"></textarea>
                <br/>

            </p>
            <p>
                <button type="submit" class="noBorderButton" id="UpdateSupport" name="UpdateSupport" onsubmit="return false;">
                    <a class="btn" href="javascript:void(0)">送信</a>
                </button>
            </p>
        </div>
    </form>
    </div>
</body>
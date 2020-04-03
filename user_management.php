
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
        LoadSupportData();
    });

    // ========================
    //  コンバート
    // ========================
    function InitializePassword(targetUserID){
        
        var res = confirm("パスワードを初期化してよいですか？");

        if(res != true){
            // キャンセルなら削除しない
            return false;
        }

        var args = {
            userID : targetUserID,
        };

       $.ajax({
            type: "POST",
            dataType: "json",
            url: "php/InitializePasswordAPI.php",
            data: args,
            success: function(data)
            {
                if(data != true){
                    alert(data[0].MESSAGE);
                }
                else{
                    alert("成功。");
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
                if(data.RESULT != true)
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

    // ========================
    //  問い合わせデータの取得
    // ========================
    function LoadSupportData(){

       $.ajax({
            type: "POST",
            dataType: "json",
            url: "php/GetAllUserDataAPI.php",
            success: function(data)
            {
                var isFirst = true;
                var rowCnt = 0;
            
                if(data.length > 0){
                    $("#output_header").append('<p class="mini">' + data.length + '人 のユーザを表示</p>');
                    $("#output").append('<table class="userDataTable" id="userData">');
                    $("#userData").append('<tr id="userDataHeader">');
                    $("#userDataHeader").append('<th class="userDataHeader1">ユーザID</th>');
                    $("#userDataHeader").append('<th class="userDataHeader2">ユーザ名</th>');
                    $("#userDataHeader").append('<th class="userDataHeader3">権限</th>');
                    $("#userDataHeader").append('<th class="userDataHeader4">エリア</th>');
                    $("#userDataHeader").append('<th class="userDataHeader5">メインキャラ</th>');
                    $("#userDataHeader").append('<th class="userDataHeader6">RIP</th>');
                    $("#userDataHeader").append('<th class="userDataHeader7">通知設定</th>');
                    $("#userDataHeader").append('<th class="userDataHeader8">Twitter</th>');
                    $("#userData").append('</tr>');
                    $("#userData").append('<tbody id="userDataBody">');

                    data.forEach(function(value){
                        // 1行目以外は</tr>で〆る
                        if(!isFirst){
                            $("#userDataBody").append("</tr>");
                            rowCnt += 1;
                        }
                        else
                        {
                            // 1行目フラグをおろす
                            isFirst = false;
                        }

                        // 行を変更する
                        $("#userDataBody").append("<tr id='usr_row" + rowCnt + "' onClick='InitializePassword(\""+value.USER_ID+"\")'>");
                        $('#usr_row' + rowCnt).append('<td>' + value.USER_ID + '</td>');
                        $('#usr_row' + rowCnt).append('<td>' + value.USER_NAME + '</td>');
                        $('#usr_row' + rowCnt).append("<td>" + value.AUTHORITY_NAME + "</td>");
                        $('#usr_row' + rowCnt).append("<td>" + value.AREA_NAME + "</td>");
                        $('#usr_row' + rowCnt).append("<td>" + value.CHARACTER_NAME + "</td>");
                        $('#usr_row' + rowCnt).append("<td>" + value.RIP + "万</td>");
                        $('#usr_row' + rowCnt).append("<td>" + value.NOTIFICATION + "</td>");
                        $('#usr_row' + rowCnt).append("<td>" + value.TWITTER + "</td>");
                    });
                
                    $("#userDataBody").append('</tbody>');
                    $("#output").append("</table>");
                }
                else {
                    $("#output").append('<span class="attention">ユーザーは 0件 です。</span>');
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
    //  問い合わせデータの削除
    // ========================
    function DeleteSupportData(deleteSupportID){
        var args = {
            userID : getCookie("USER_ID"),
            supportID : deleteSupportID
        };

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "php/DeleteSupportDataAPI.php",
            data: args,
            success: function(data)
            {
                if(data == true){
                    alert("削除しました");
                    document.getElementById("output").innerHTML = "" ;
                    LoadSupportData();
                }
                else{
                    alert("削除できませんでした");
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
        $("#MoveSystemManagementPage").click(function(){
            window.location.href = "system.php";
        });
    });

    $(function() {
        $("#MoveSupportPage").click(function(){
            window.location.href = "support_management.php";
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
        <button class="noBorderButton" type="submit" id="c" name="MoveUserManagementPage">
            <a class="btn" href="javascript:void(0)">ユーザー管理</a>
        </button>
        <button class="noBorderButton" type="submit" id="MoveSupportPage" name="MoveSupportPage">
            <a class="btn" href="javascript:void(0)">問い合わせ一覧</a>
        </button>
    </span>
</div>

<div id="output_header"></div>
<div id="output"></div>

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
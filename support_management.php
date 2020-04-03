
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
    function MoveToDetail(supportID){
        setCookie("SUPPORT_ID", supportID, 1);
        window.location.href = "support_management_detail.php";
    }


    $(function() {
        var loginUserID = getCookie("LOGIN_USER_ID");
        CheckUser(loginUserID);
        LoadSupportData();
    });

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
    function LoadSupportData(){

       $.ajax({
            type: "POST",
            dataType: "json",
            url: "php/GetSupportDataAPI.php",
            success: function(data)
            {
                var isFirst = true;
                var rowCnt = 0;
                
                if(data.length > 0){
                    $("#output").append('<table class="systemTable" id="supportData">');
                    $("#supportData").append('<tr id="supportHeader">');
                    $("#supportHeader").append('<th class="supportHeader1">カテゴリ</th>');
                    $("#supportHeader").append('<th class="supportHeader2">内容</th>');
                    $("#supportHeader").append('<th class="supportHeader4">ステータス</th>');
                    $("#supportHeader").append('<th class="supportHeader5">問い合わせ主</th>');
                    $("#supportHeader").append('<th class="supportHeader6">受付時刻</th>');
                    $("#supportHeader").append('<th class="supportHeader7">削除</th>');
                    $("#supportData").append('</tr>');
                    $("#supportData").append('<tbody id="supportBody">');

                    data.forEach(function(value){
                        // 1行目以外は</tr>で〆る
                        if(!isFirst){
                            $("#event_body").append("</tr>");
                            rowCnt += 1;
                        }
                        else
                        {
                            // 1行目フラグをおろす
                            isFirst = false;
                        }

                        // 行を変更する
                        $("#supportBody").append('<tr id="sp_row' + rowCnt + '" onClick="MoveToDetail(' + value.SUPPORT_ID + ')">');
                        $('#sp_row' + rowCnt).append('<td>' + value.CATEGORY + '</td>');
                        $('#sp_row' + rowCnt).append('<td class="LeftAlign">' + value.TEXT.replace(/\r?\n/g, "<br>") + '</td>');
                        $('#sp_row' + rowCnt).append("<td>" + value.STATUS + "</td>");
                        $('#sp_row' + rowCnt).append("<td>" + value.REGIST_USER_ID + "</td>");
                        $('#sp_row' + rowCnt).append("<td>" + value.REGIST_DATETIME + "</td>");
                        $('#sp_row' + rowCnt).append('<td><button onClick="DeleteSupportData('+ value.SUPPORT_ID+')">削除</button></td>');
                    });
                
                    $("#supportBody").append('</tbody>');
                    $("#output").append("</table>");
                }
                else {
                    $("#output").append('<span class="attention">未解決のお問い合わせは 0件 です。</span>');
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
        var res = confirm("削除してよろしいですか？");

        if(res != true){
            // キャンセルなら削除しない
            return false;
        }

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
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
</body>
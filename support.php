
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/ValidationUtil.js"></script>
<script src="js/Cookie.js"></script>

<script>
    $(document).ready(function(){

        GetCategoryType();

        $('#SendSupport').click(function(){
            var loginUserID = getCookie("USER_ID");

            if(loginUserID == null || loginUserID == undefined){
                loginUserID == "NO_LOGIN";
            }
            var argData = {
                userID : loginUserID,
                text : $('#supportText').val(),
                categoryType :$('#categoryType').val()
            };

            if(argData.text.length > 512){
                alert("お問い合わせ内容が512文字を超過しています。");
                return;
            }

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "php/RegistSupport.php",
                data: argData,

                success: function(data, dataType)
                {
                    // ログイン結果
                    data.forEach(function(value){
                        if(value.RESULT != false){
                            alert("お問い合わせを受け付けました。");

                            window.location.href = "where.php";
                        }
                        else
                        {
                            alert("お問い合わせが失敗しました。公式Twitterにご連絡ください。");
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

    // ========================
    //  カテゴリのロード
    // ========================
    function GetCategoryType(){

        var args = {
             key : "CATEGORY_TYPE",
        };

        $.ajax({
            type: "POST",
            url: "php/GetType.php",
            data: args,
            success: function(data)
            {
                data.forEach(function(value){
                    // 行を変更する
                    $("#categoryType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
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
        $("#MoveLoginPage").click(function(){
           window.location.href = "index.php";
        });
    });

    $(function() {
        $("#MoveAllData").click(function(){
           window.location.href = "where.php";
        });
    });

</script>
</head>
<body>
    <div class="sendWrapper">
        <span class="box-title">ユーザーサポート</span>
        <form method="post" id="sendData">
        <div>
            <p>
                <label for="label_category" accesskey="n">カテゴリ：</label><br/>
                <select class="formComboBox" name="categoryType" id="categoryType">
                </select>
            </p>
            <p>
                <label for="label_text" accesskey="n">内容(1000文字以内)：</label><br/>
                <span class="mini">
                例）[要望] ゲームセンターの追加をしてほしい。<br/>
                地域：東海地方<br/>
                場所：静岡（タイトーステーション浜松）or 浜松（タイトーステーション）
                </span>

                <textarea name="supportText" id="supportText" rows="5" cols="60"></textarea>
                <br/>

            </p>
            <p>
                <button type="submit" class="noBorderButton" id="SendSupport" name="SendSupport" onsubmit="return false;">
                    <a class="btn" href="javascript:void(0)">送信</a>
                </button>
            </p>
        </div>
    </form>
    </div>

<button class="noBorderButton" id="MoveAllData">
    <a class="btn" href="javascript:void(0)">戻る</a>
</button>
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

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
    $(function() {
        $("#MoveDataPage").click(function(){
           window.location.href = "where.php";
        });
    });
</script>
</head>
<body>
    <div class="helpWrapper">
        <span class="box-title">どこUNI？ Q<font color="red">&</font>A</span>
        <div class="topic">
            <p>
                <span class="attention">Q.ユーザ登録するとどうなるの？</span>
            </p>
            <p>
                <span class="attention">A.ユーザ登録（ログイン）するとできること</span><br/>
                　１．行きたい場所への投票ができます。<br/>
                　２．同じ場所に投票があった時にTwitter通知がくるよう設定できます。<br/>
                <br/>
                　通知の種類は<br/>
                　・通知なし<br/>
                　・全てのチェックインを通知<br/>
                　・ガチ対戦のみ通知<br/>
                　<br/>
                　といったように、自身のプレースタイルに合わせて変更できます。<br/>
            </p>
                <div class="helpImagesWrapper">
                    <div class="contents">
                    設定方法
                    <p><img class="help_images" src="img/001_home.png" /></p>
                    <p class="attention">↓</p>
                    <p><img class="help_images" src="img/002_mypage.png" /></p>
                    <p class="attention">↓</p>
                    <p><img class="help_images" src="img/003_twitter.png" /></p>
                    </div>
                </div>
        </div>
    </div>
    <br/>
    <button type="submit" class="noBorderButton" id="MoveDataPage" name="MoveDataPage" onsubmit="return false;">
        <a class="btn" href="javascript:void(0)">データ一覧に戻る</a>
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
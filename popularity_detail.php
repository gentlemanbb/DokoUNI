
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/jquery.jqplot.min.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/jquery.jqplot.min.js"></script>

<script>
    // =========================
    //  ロード完了時イベント
    // =========================
    $(document).ready(function(){
        var placeID = getCookie("PLACE_ID");
        var loginUserID = getCookie("USER_ID");
        var _addDays = getCookie("ADD_DAYS");

        if(loginUserID == "undefined" || loginUserID == "null"){
            document.getElementById("moveMypageButton").style.display="none";
        }

        if(placeID == "undefined"){
            alert("地域を選択してください");
            window.location.href = "where.php";
            return false;
        }

        var args = {
            placeID: placeID,
            addDays: _addDays
        }

        $.ajax({
            type: "POST",
            url: "php/GetPopularityDetail.php",
            data: args,
            success: function(data)
            {
                var rowCnt = 0;

                $("#output").append('<table id="popularity_data">');
                $("#popularity_data").append('<tr id="popularity_header">');
                $("#popularity_header").append('<th class="header1">キャラ</th>');
                $("#popularity_header").append('<th class="header2">ＲＩＰ</th>');
                $("#popularity_header").append('<th class="header3">ＦＲＯＭ</th>');
                $("#popularity_header").append('<th class="header4">ＴＯ</th>');
                $("#popularity_data").append('</tr>');
                $("#popularity_data").append('<tbody id="popularity_body">');

                data.forEach(function(value){
                    // 行を変更する
                    $("#popularity_body").append('<tr id="row' + rowCnt + '">');
                    if(value.CHARACTER_NAME != null){
                        $("#row" + rowCnt).append('<td>' + value.CHARACTER_NAME + '</td>');
                    }
                    else {
                        $("#row" + rowCnt).append('<td>キャラ未選択</td>');
                    }
                    
                    if(value.RIP != null) {
                        $("#row" + rowCnt).append('<td>' + value.RIP + '万</td>');
                    }
                    else {
                        $("#row" + rowCnt).append('<td>RIP未入力</td>');
                    }
                    $("#row" + rowCnt).append('<td>' + value.JOIN_TIME_FROM + '</td>');
                    $("#row" + rowCnt).append('<td>' + value.JOIN_TIME_TO + '</td>');
                    $("#detail_body").append('</tr>');

                    rowCnt += 1;
                });

                $("#popularity_body").append('</tbody>');
                $("#output").append("</table>");
                
            },

            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert('error : ' + errorThrown);
            }
        });
        
        var args = {
            placeID:   placeID,
            addDays:   _addDays
        };
        
        $.ajax({
            type: "POST",
            url: "php/GetPopularityTimeDetail.php",
            data: args,
            success: function(data)
            {
                var rowCnt = 0;
                var maxNumber = 0;
                var timeDetail = [];
                data.forEach(function(value){
                    timeDetail[rowCnt] = {
                            time : value.TIME,
                            number : value.NUMBER
                    };

                    if(maxNumber < value.NUMBER){
                        maxNumber = value.NUMBER;
                    }

                    rowCnt += 1;
                });

                jQuery(function() {
                    jQuery.jqplot(
                        'graph',
                        [
                            [
                                [timeDetail[0].time, timeDetail[0].number],
                                [timeDetail[1].time, timeDetail[1].number],
                                [timeDetail[2].time, timeDetail[2].number],
                                [timeDetail[3].time, timeDetail[3].number],
                                [timeDetail[4].time, timeDetail[4].number],
                                [timeDetail[5].time, timeDetail[5].number],
                                [timeDetail[6].time, timeDetail[6].number],
                                [timeDetail[7].time, timeDetail[7].number],
                                [timeDetail[8].time, timeDetail[8].number],
                                [timeDetail[9].time, timeDetail[9].number],
                                [timeDetail[10].time, timeDetail[10].number],
                                [timeDetail[11].time, timeDetail[11].number],
                                [timeDetail[12].time, timeDetail[12].number],
                                [timeDetail[13].time, timeDetail[13].number],
                            ]
                        ],
                        
                        {
                            axes : {
                                xaxis : {
                                    renderer: jQuery . jqplot . DateAxisRenderer,
                                    label : '時間',
                                    min : 10,
                                    max : 23,
                                    tickInterval : '1'
                                },

                                yaxis :{
                                    label : '人数',
                                    min : '0',
                                    max : maxNumber.toString(),
                                    tickInterval : '1'

                                }
                            }
                        }
                    );
                } );
            },

            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert('error : ' + errorThrown);
            }
        });

        return false;
    });


    $(function() {
        $("#MoveMyPage").click(function(){
           window.location.href = "user_page.php";
        });
    });

    $(function() {
        $("#MoveAllData").click(function(){
           window.location.href = "where.php";
        });
    });

</script>

<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
</head>
<body>

<div id="main_header">
    <h1>どこＵＮＩ？</h1>
</div>

<div id="headerButtonWrapper">
    <button class="noBorderButton" type="submit" id="MoveMyPage" name="MoveMyPage">
        <a class="btn" href="javascript:void(0)">マイページ</a>
    </button>
    <button id="MoveAllData" class="noBorderButton" type="submit"name="MoveAllData">
        <a class="btn" href="where.php">一覧に戻る</a>
    </button>
</div>

<div id="output"></div>
<div id="graph" style="width:100%;"></div>

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
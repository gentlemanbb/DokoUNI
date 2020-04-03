
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/Functions.js"></script>


<script>
    // ====================
    // 詳細画面へ移動
    // ====================
    function MoveToDetail(placeID){
        setCookie("PLACE_ID", placeID, 1);
        window.location.href = "popularity_detail.php";
    }

    // =====================
    //  データのロード
    // =====================
    function LoadPopularity(_addDays){

        document.getElementById("output").innerHTML = "";

        var args = {
            areaID : getCookie("AREA_ID"),
            addDays : _addDays
        };

        $.ajax({
            type: "POST",
            url: "php/GetPopularityPlace2.php",
            data: args,
            success: function(data)
            {
                var beforePlaceID;
                var isFirst = true;
                var rowCnt = 0;
                var colCnt = 0;

                $("#output").append('<table id="popularity_data">');
                $("#popularity_data").append('<tr id="popularity_header">');
                $("#popularity_header").append('<th class="header1">場所</th>');
                $("#popularity_header").append('<th class="header2">確定</th>');
                $("#popularity_header").append('<th class="header3">候補</th>');
                $("#popularity_header").append('<th class="header4">可能性</th>');
                $("#popularity_data").append('</tr>');
                $("#popularity_data").append('<tbody id="popularity_body">');

                data.forEach(function(value){
                    // 1行目以外は</tr>で〆る
                    if(!isFirst){
                        $("#popularity_body").append("</tr>");
                        rowCnt += 1;
                    }
                    else
                    {
                        // 1行目フラグをおろす
                        isFirst = false;
                    }

                    // 行を変更する
                    $("#popularity_body").append('<tr id="row' + rowCnt + '" onClick="MoveToDetail(' + value.PLACE_ID + ')">');
                    $('#row' + rowCnt).append('<td>' + value.PLACE_NAME + '</td>');
                    $('#row' + rowCnt).append("<td>" + value.VALUE1 + "人</td>");
                    $('#row' + rowCnt).append("<td>" + value.VALUE2 + "人</td>");
                    $('#row' + rowCnt).append("<td>" + value.VALUE3 + "人</td>");
                });
                
                $("#popularity_body").append('</tbody>');
                $("#output").append("</table>");

                var date = GetTodayAddDays(_addDays);
                $("#addedDate").remove();
                $("#watchingDate").append('<div id="addedDate" class="heading">'+ date +'</div>');

            },

            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                console.log('error : ' + errorThrown);
            }
            
        });

        return true;
    }

    // =======================
    //  自分の投票データを取得
    // =======================
    function LoadMyPopularity(){
        var args = {
            userID: getCookie("USER_ID")
        };

        $.ajax({
            type: "POST",
            url: "php/GetMyPopularity.php",
            data: args,
            success: function(data)
            {
                data.forEach(function(value){
                    if(value.PLACE_ID != null){
                        document.sendData.placeID.value = value.PLACE_ID;
                    }
                    document.sendData.joinType.value = value.JOIN_TYPE;
                    document.sendData.purposeType.value = value.PURPOSE_TYPE;
                    document.sendData.datetime_from.value =  value.JOIN_TIME_FROM;
                    document.sendData.datetime_to.value = value.JOIN_TIME_TO;
                });
            },

            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                console.log('error : ' + errorThrown);
            }
            
        });
    }

    // ========================
    //  場所のロード
    // ========================
    function LoadPlaceData(){
        var args = {
             areaID : getCookie('AREA_ID'),
        };
        $.ajax({
            type: "POST",
            url: "php/GetPlaceAPI.php",
            data: args,
            success: function(data)
            {
                data.forEach(function(value){
                   // 行を変更する
                   $("#placeID").append('<option value=' + value.PLACE_ID + '>' + value.PLACE_NAME + '</option>');
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
    //  参加区分のロード
    // ========================
    function GetJoinType(){
        var data = {
             key : "JOIN_TYPE",
        };

        $.ajax({
            type: "POST",
            url: "php/GetType.php",
            data: data,
            success: function(data)
            {
                data.forEach(function(value){
                    // 行を変更する
                    $("#joinType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
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
    //  目的のロード
    // ========================
    function GetPurposeType(){

        var args = {
             key : "PURPOSE",
        };

        $.ajax({
            type: "POST",
            url: "php/GetType.php",
            data: args,
            success: function(data)
            {
                data.forEach(function(value){
                    // 行を変更する
                    $("#purposeType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
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
    //  ユーザデータのロード
    // ========================
    function GetUserData(){
        var userID = getCookie("USER_ID");

        var args = {
            userID : userID,
        };

       $.ajax({
            type: "POST",
            dataType: "json",
            url: "php/GetUserData.php",
            data: args,
            success: function(data)
            {
                data.forEach(function(value){
                    // クッキー設定
                    setCookie("USER_NAME", value.USER_NAME, 1);
                    setCookie("MAIN_CHARACTER_ID", value.MAIN_CHARACTER_ID, 1);
                    setCookie("RIP", value.RIP, 1);
                    setCookie("AREA_ID", value.AREA_ID, 1);
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

    // =========================
    //  イベントクリック
    // =========================
    function Test(_eventID){
        var args = {
             eventID : _eventID
        };

        $.ajax({
            type: "POST",
            url: "php/GetEventDetailAPI.php",
            data: args,
            success: function(data)
            {
                data.forEach(function(value){
                    if(value.PLACE_ID != null){
                        document.sendData.placeID.value = value.PLACE_ID;
                        document.sendData.datetime_from.value = value.EVENT_TIME_FROM;
                        document.sendData.datetime_to.value = value.EVENT_TIME_TO;
                        document.sendData.purposeType.value = 3;
                    }
                });
            },

            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                console.log('error : ' + errorThrown);
            }
            
        });

        return true;      
    }
    // ========================
    //  イベントのロード
    // ========================
    function GetEventData(_addDays){
        document.getElementById("event").innerHTML = "";

        var args = {
             areaID : getCookie('AREA_ID'),
             addDays : _addDays
        };

        $.ajax({
            type: "POST",
            url: "php/GetEventAPI.php",
            data: args,
            success: function(data)
            {
                var beforePlaceID;
                var isFirst = true;
                var rowCnt = 0;
                var colCnt = 0;

                if(data.length > 0){
                    $("#event").append('<table class= "event" id="event_data">');
                    $("#event_data").append('<tr id="event_header">');
                    $("#event_header").append('<th class="event_header1">日付</th>');
                    $("#event_header").append('<th class="event_header2">場所</th>');
                    $("#event_header").append('<th class="event_header3">イベント名</th>');
                    $("#event_header").append('<th class="event_header4">時間</th>');
                    $("#event_data").append('</tr>');
                    $("#event_data").append('<tbody id="event_body">');

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
                        $("#event_body").append('<tr id="ev_row' + rowCnt + '" onClick="Test(' +value.EVENT_ID+');">');
                        $('#ev_row' + rowCnt).append('<td>' + value.EVENT_DATE + '</td>');
                        $('#ev_row' + rowCnt).append("<td>" + value.PLACE_NAME + "</td>");
                        $('#ev_row' + rowCnt).append("<td>" + value.EVENT_NAME + "</td>");
                        $('#ev_row' + rowCnt).append("<td>" + value.EVENT_TIME_FROM + " - " + value.EVENT_TIME_TO +"</td>");
                    });
                
                    $("#event_body").append('</tbody>');
                    $("#event").append("</table>");
                }
                else {
                    $("#event").append('<span class="attention">登録されているイベントはありません</span>');
                }
            },

            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                console.log('error : ' + errorThrown);
            }
            
        });

        return true;
    }

    // =========================
    //  ロード完了時イベント
    // =========================
    $(document).ready(function(){
        var userID = getCookie("USER_ID");
        var canRegistEvent = getCookie("REGIST_EVENT");

        if(userID == undefined || userID == "null" || userID == null || userID == ''){
            document.getElementById("sendDataForm").style.display="none";
            document.getElementById("loggedinFunctions").style.display="none";
        }
        else
        {
            document.getElementById("noLoggedinFunctions").style.display="none";
        }

        if(canRegistEvent != "1"){
            document.getElementById("eventOrganizerFunctions").style.display="none";
        }

        var now = new Date();

        var hour = now.getHours(); // 時
        if(String(hour).length == 1){
            hour = "0" + hour;
        }
        var min = now.getMinutes(); // 分
        if(String(min).length == 1){
            min = "0" + min;
        }

        var sec = now.getSeconds(); // 秒
        if(String(sec).length == 1){
            sec = "0" + sec;
        }

        // ボタンを押下不可に
        document.sendData.elements["SendPopularity"].disabled = true;
        document.sendData.elements["SendCancel"].disabled = true;
        document.sendData.datetime_from.value = hour + ":" + min + ":00";
        document.sendData.datetime_to.value = hour + ":" + min + ":00";
        
        var userID = getCookie("USER_ID");
        setCookie("ADD_DAYS", 0);
        LoadPopularity(0);
        GetEventData(0);
        setTimeout(function(){
            if(!LoadPlaceData()) {
                setTimeout(arguments.callee, 100);
            }
            else {
                setTimeout(function(){
                    if(!GetJoinType()) {
                        setTimeout(arguments.callee, 100);
                    }
                    else {
                        setTimeout(function(){
                            if(!GetPurposeType()) {
                                setTimeout(arguments.callee, 100);
                            }
                            else {
                                GetUserData();
                                LoadMyPopularity();
                            }
                        }, 100);

                    }
                }, 100);
            }
        }, 100);

        // ボタンを押下可能に
        document.sendData.elements["SendPopularity"].disabled = false;
        document.sendData.elements["SendCancel"].disabled = false;

        return false;
    });
</script>
</head>
<body>

<script>
    // ===================
    // 前の日付表示
    // ===================
    $(function() {
        $("#prevDate").click(function(){
            var addDays = getCookie("ADD_DAYS");
            var addDays = Number(addDays) - 1;
            setCookie("ADD_DAYS", addDays);

            setTimeout(function(){
                if(!GetEventData(addDays)) {
                    setTimeout(arguments.callee, 100);
                }
                else {
                    LoadPopularity(addDays);                            
                }
            }, 100);
        });
    });

    // ===================
    // 次の日付表示
    // ===================
    $(function() {
        $("#nextDate").click(function(){

            var addDays = getCookie("ADD_DAYS");
            var addDays = Number(addDays) + 1;
            setCookie("ADD_DAYS", addDays);

            setTimeout(function(){
                if(!GetEventData(addDays)) {
                    setTimeout(arguments.callee, 100);
                }
                else {
                    LoadPopularity(addDays);                            
                }
            }, 100);

        });
    });

    $(function() {
        $("#SendPopularity").click(function(){

            var loginUserID = getCookie("USER_ID");
            var _addDays = getCookie("ADD_DAYS");

            if(_addDays < 0){
                alert('過去の日付に送信はできません');
                return;
            }
            
            // ボタンを押下不可に
            document.sendData.elements["SendPopularity"].disabled = true;
            document.sendData.elements["SendCancel"].disabled = true;

            var data = {
                placeID : $('#placeID').val(),
                placeName : $('#placeID option:selected').text(),
                userID : loginUserID,
                playerName : getCookie("USER_NAME"),
                joinType : $('#joinType').val(),
                joinText : $('#joinType option:selected').text(),
                purposeType : $('#purposeType').val(),
                purposeText : $('#purposeType option:selected').text(),
                RIP : getCookie("RIP"),
                characterID : getCookie("MAIN_CHARACTER_ID"),
                from : $('#datetime_from').val(),
                to : $('#datetime_to').val(),
                addDays : _addDays
            };

            $.ajax({
                type: "POST",
                url: "php/SendPopularity.php",
                data: data,
                success: function(jsonData){
                    // 処理を記述
                    alert("送信しました！");
                    return false;
                },

                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    console.log('error : ' + errorThrown);
                    return false;
                },

                complete: function(){
                    $("#popularity_data").remove();
                    LoadPopularity();
                    
                    // ボタンの状態を変更
                    document.sendData.elements["SendPopularity"].disabled = false;
                    document.sendData.elements["SendCancel"].disabled = false;
                }
            });

            return false;
        });
    });

    // ===========================
    // キャンセルボタン押下イベント
    // ===========================
    $(function() {
        $("#SendCancel").click(function(){
            var loginUserID = getCookie("USER_ID");

            document.sendData.elements["SendPopularity"].disabled = true;
            document.sendData.elements["SendCancel"].disabled = true;

            var data = {
                userID : loginUserID
            };

            $.ajax({
                type: "POST",
                url: "php/CancelPopularity.php",
                data: data,
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
                    $("#popularity_data").remove();
                    LoadPopularity();

                    document.sendData.elements["SendPopularity"].disabled = false;
                    document.sendData.elements["SendCancel"].disabled = false;
            
                }
            });

            return false;
        });
    });

	$('#sampleButton').click( function () {
		$('#sampleModal').modal();
	});

    $(function() {
        $("#MoveMyPage").click(function(){
           window.location.href = "user_page.php";
        });
    });

    $(function() {
        $("#MoveLoginPage").click(function(){
           window.location.href = "index.php";
        });
    });

    $(function() {
        $("#MoveEventRegistPage").click(function(){
           window.location.href = "event_register.php";
        });
    });

    $(function() {
        $("#MoveHelpPage").click(function(){
           window.location.href = "help.php";
        });
    });

    $(function() {
        $("#MoveSupportPage").click(function(){
           window.location.href = "support.php";
        });
    });

    $(function() {
        $("#MoveSystemManagementPage").click(function(){
           window.location.href = "system.php";
        });
    });

    $(function() {
        $("#LogOut").click(function(){
            setCookie("USER_ID", null);
            window.location.href = "index.php";
        });
    });

    
</script>

</head>

<body>
<div id="main_header">
    <h1>どこＵＮＩ？</h1>
</div>
<div id="functions">
    <span id="loggedinFunctions">
        <button class="noBorderButton" type="submit" id="MoveMyPage" name="MoveMyPage">
            <a class="btn" href="javascript:void(0)">マイページ</a>
        </button>
        <button class="noBorderButton" type="submit" id="LogOut" name="LogOut">
            <a class="btn" href="javascript:void(0)">ログアウト</a>
        </button>
    </span>
    <span id="eventOrganizerFunctions">
        <button class="noBorderButton" type="submit" id="MoveEventRegistPage" name="MoveEventRegistPage">
            <a class="btn" href="javascript:void(0)">イベント登録</a>
        </button>
    </span>
    <span id="allUserFunction">
        <button class="noBorderButton" type="submit" id="MoveHelpPage" name="MoveHelpPage">
            <a class="btn" href="javascript:void(0)">Q&A</a>
        </button>
        <button class="noBorderButton" type="submit" id="MoveSupportPage" name="MoveSupportPage">
            <a class="btn" href="javascript:void(0)">お問い合わせ</a>
        </button>
    </span>
</div>

<div id="watchingDateWrapper">
    <p>
        <div id="prevDate"><<</div>
        <div id="watchingDate" class="watchingDate"></div>
        <div id="nextDate">>></div>
    </p>
</div>

<div id="eventWrapper">
    <div id="event_title">直近イベント</div>
    <div id="event"></div>
</div>

<div id="output"></div>
<div class="sendWrapper" id="sendDataForm">
        <span class="box-title">どこかに行く</span>
        <form id="sendData" name="sendData">
        <div>

        <p>
            <label for="label_place" accesskey="n">場所：</label><br/>
            <select class="formComboBox" name="placeID" id="placeID">
            </select><span class="mini">←表示されない場合は再ログインしてみてください。</span>
        </p>
        <p>
            <label for="label_place" accesskey="n">参加区分：</label><br/>
            <select class="formComboBox" name="joinType" id="joinType">
            </select>
        </p>
        <p>
            <label for="label_place" accesskey="n">目的：</label><br/>
            <select class="formComboBox" name="purposeType" id="purposeType">
            </select>
        </p>

        <br/>

        <p>
            <label for="label_place" accesskey="n">参加時刻（FROM - TO)：</label><br/>
            <input type="time" id="datetime_from" name="datetime_from" class="from_to">時 から<br/>
            <input type="time" id="datetime_to" name="datetime_to"  class="from_to">時 まで
        </p>

        <br/>

        <button type="submit" class="noBorderButton" id="SendPopularity" name="SendPopularity" onsubmit="return false;">
            <a class="btn" href="javascript:void(0)">送信</a>
        </button>
        <button type="submit" class="noBorderButton" id="SendCancel" name="SendCancel" onsubmit="return false;">
            <a class="btn" href="javascript:void(0)">行くのやめた</a>
        </button>
        </div>
    </form>
</div>

<div id="noLoggedinFunctions">
    <p>※参加投票はログインしないと使用できません。</p>
    <button type="submit" class="noBorderButton" id="MoveLoginPage" name="MoveLoginPage" onsubmit="return false;">
        <a class="btn" href="javascript:void(0)">ログイン</a>
    </button>
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



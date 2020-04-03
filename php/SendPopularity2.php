<?php
    require "DBAccess.php";
    require "TweetAPI.php";

    // 引数
    $playerName = $_POST['playerName'];
    $userID = $_POST['userID'];
    $placeID = $_POST['placeID'];
    $placeName = $_POST['placeName'];
    $joinType = $_POST['joinType'];
    $joinText = $_POST['joinText'];
    $purposeType = $_POST['purposeType'];
    $purposeText = $_POST['purposeText'];
    $RIP = $_POST['RIP'];
    $characterID = $_POST['characterID'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $addDays = $_POST['addDays'];

    $tweetResult = False;

    $resultPattern;

    if($RIP == null){
        $RIP = 'NULL';
    }

    if($playerName == '' || $playerName == null){
        $playerName = 'NO NAME';
    }

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    // ---------------------
    // 送信済みデータの取得
    // ---------------------
    $query = sprintf("
        SELECT
            POPULARITY_ID
            , JOIN_TYPE
            , PURPOSE_TYPE
            , JOIN_TIME_FROM
            , JOIN_TIME_TO

        FROM D_POPULARITY POP

        WHERE
            POP.USER_ID = '%s'
        AND
            POP.JOIN_DATE_FROM BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
    ", $userID, $addDays, $addDays+1);

    $rows = $dbAccess->Select($query);

    $prevPopularityID;

    foreach ($rows as $row){
        $prevPopularityID = $row['POPULARITY_ID'];
    }

    // ---------------------
    // 通知ユーザの取得
    // ---------------------
    $query = sprintf("
        SELECT
            POP.USER_ID
            , USR.TWITTER

        FROM D_POPULARITY POP
        LEFT JOIN D_USER USR
         ON POP.USER_ID = USR.USER_ID

        WHERE
            POP.PLACE_ID = %s
        AND
            POP.JOIN_DATE_FROM BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
        AND (
                (%s = 0
                    AND (USR.NOTIFICATION = 1 
                    OR USR.NOTIFICATION = 2
                    OR USR.NOTIFICATION = 3))
            OR
                (%s = 1
                    AND (USR.NOTIFICATION = 1 
                    OR USR.NOTIFICATION = 2
                    OR USR.NOTIFICATION = 4))
            OR
                (%s = 2 AND USR.NOTIFICATION = 1)
            OR
                (%s = 3 AND USR.NOTIFICATION = 1)
            )
        AND
            USR.TWITTER IS NOT NULL

        GROUP BY USER_ID"
    , $placeID
    , $addDays
    , $addDays+1
    , $purposeType
    , $purposeType
    , $purposeType
    , $purposeType);

    $rows = $dbAccess->Select($query);

    $notificationUsers = [];

    foreach ($rows as $row){
        if($row['TWITTER'] != null && strlen($row['TWITTER']) > 0){
            array_push($notificationUsers, $row['TWITTER']);
        }
    }
    
    $joinDate = date("Y/m/d", strtotime(sprintf("+%s day", $addDays)));

    if($prevPopularityID != NULL){

        $resultPattern = "UPDATE";

        $query = sprintf("
            UPDATE D_POPULARITY SET
                PLACE_ID = %s
                , CHARACTER_ID = %s
                , RIP = %s
                , JOIN_TYPE = %s
                , PURPOSE_TYPE = %s
                , JOIN_TIME_FROM = '%s'
                , JOIN_TIME_TO = '%s'
            WHERE POPULARITY_ID = '%s'"
            , $placeID
            , $characterID
            , $RIP
            , $joinType
            , $purposeType
            , $from
            , $to
            , $prevPopularityID);

        $queryResult = $dbAccess->Update($query);

        $tweetStr = sprintf(
            "#どこUNI %s さん が %s の %s「%s, %s」 に変更しました。"
            , $playerName
            , $joinDate
            , $placeName
            , $purposeText
            , $joinText);

        
        $notificationStr;

        foreach($notificationUsers as $value){
            $notificationStr = $notificationStr . sprintf(" @%s", $value);
        }

        $tweetResult = Tweet($tweetStr . $notificationStr);
    }
    else{

        $resultPattern = "INSERT";

        $query = sprintf("
            INSERT D_POPULARITY (
                PLACE_ID
                , USER_ID
                , CHARACTER_ID
                , RIP
                , JOIN_TYPE
                , PURPOSE_TYPE
                , JOIN_DATE_FROM
                , JOIN_TIME_FROM
                , JOIN_DATE_TO
                , JOIN_TIME_TO
                )
        
            VALUES (
                %s
                , '%s'
                , %s
                , %s
                , %s
                , %s
                , DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
                , '%s'
                , DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
                , '%s'
            )", $placeID, $userID, $characterID, $RIP, $joinType, $purposeType, $addDays, $from, $addDays, $to);
        
        $queryResult = $dbAccess->Insert($query);
        

        $tweetStr = sprintf(
            "#どこUNI %s さん が %s の %s に 「%s, %s」 でチェックインしました。"
             , $playerName
            , $joinDate
            , $placeName
            , $purposeText
            , $joinText);

        
        $notificationStr;

        foreach($notificationUsers as $value){
            $notificationStr = $notificationStr . sprintf(" @%s", $value);
        }

        // $tweetResult = Tweet($tweetStr . $notificationStr);
    }

    header('Content-type: application/json');

    if($queryResult == True){
        $result = [
            'PATTERN' => $resultPattern,
            'RESULT' => true,
            'POPULARITY_ID' => $prevPopularityID,
            'NotificationUsers' => $notificationUsers,
            'Tweet' => $tweetStr
        ];

        echo json_encode($result);
    }
    else{
        $result = [
            'PATTERN' => $resultPattern,
            'RESULT' => false,
            'POPULARITY_ID' => $prevPopularityID,
            'NotificationUsers' => $notificationUsers
        ];

        echo json_encode($result);
    }
?>
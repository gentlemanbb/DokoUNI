                                                                                                                                                                                                                                                                                                                                    <?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();

    // DB接続
    $pdo = $dbAccess->DBConnect2();

    // 引数取得
    $areaID = $_POST['areaID'];
    $addDaysTo = $_POST['addDaysTo'];
    $addDaysFrom = $_POST['addDaysFrom'];
    $userID = $_POST['userID'];

    $areaSearchQuery = '';

    if($areaID != null || $areaID != ''){
        $areaSearchQuery = sprintf('
            AND PLACE.AREA_ID = %s
        ', $areaID);
    }

    if($userID != null || $userID != ''){
        $userID = 'NO_LOGIN_USER';
    }

    $query = sprintf("
        SELECT
            EVENT.EVENT_ID
            , EVENT.EVENT_NAME
            , PLACE.PLACE_NAME
            , EVENT.EVENT_DATE
            , EVENT.EVENT_TIME_FROM
            , EVENT.EVENT_TIME_TO
            , EVENT.COMMENT
            , TOROKU_USER_ID
            
        FROM D_EVENT EVENT
        LEFT JOIN M_PLACE PLACE
            ON EVENT.PLACE_ID = PLACE.PLACE_ID
        WHERE
            EVENT_DATE BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
        %s
        ORDER BY
            EVENT_DATE ASC
            , EVENT_TIME_FROM ASC
        ",
        $addDaysFrom,
        $addDaysTo,
        $areaSearchQuery);
        
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $eventData = [];

    // データの行数分詰める
    // 存在しない場合は0行で返る
    foreach ($rows as $row){
       $eventData[] = 
       [
           'EVENT_ID' => $row['EVENT_ID'],
           'PLACE_NAME' => $row['PLACE_NAME'],
           'EVENT_NAME' => $row['EVENT_NAME'],
           'EVENT_DATE' => $row['EVENT_DATE'],
           'EVENT_TIME_FROM' => substr($row['EVENT_TIME_FROM'], 0, 5),
           'EVENT_TIME_TO' => substr($row['EVENT_TIME_TO'], 0, 5),
           'COMMENT' => $row['COMMENT'],
           'TOROKU_USER_ID' => $row['TOROKU_USER_ID']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($eventData);
?>
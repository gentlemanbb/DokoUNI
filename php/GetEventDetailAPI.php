<?php
    require "DBAccess.php";
	require "LogUtility.php";
	
	
	WriteLog("log", "GetEventDetail1");
	
    $dbAccess = new DBAccess();
    $pdo = $dbAccess->DBConnect2();
    $eventID = $_POST['eventID'];

    $query = sprintf("
        SELECT
            EVENT.EVENT_ID
            , EVENT.EVENT_NAME
            , EVENT.PLACE_ID
            , PLACE.PLACE_NAME
            , EVENT.EVENT_DATE
            , EVENT.EVENT_TIME_FROM
            , EVENT.EVENT_TIME_TO
            , EVENT.COMMENT
            , EVENT.IMAGE_PATH
        FROM D_EVENT EVENT
        LEFT JOIN M_PLACE PLACE
            ON EVENT.PLACE_ID = PLACE.PLACE_ID
        WHERE
            EVENT_ID = :eventID
        ");


    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':eventID', $eventID, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $eventData = [];
	
	WriteLog("log", "GetEventDetail2");
	
    foreach ($rows as $row){
       $eventData[] = 
       [
           'EVENT_ID' => $row['EVENT_ID'],
           'PLACE_ID' => $row['PLACE_ID'],
           'PLACE_NAME' => $row['PLACE_NAME'],
           'EVENT_NAME' => $row['EVENT_NAME'],
           'EVENT_DATE' => $row['EVENT_DATE'],
           'EVENT_TIME_FROM' => substr($row['EVENT_TIME_FROM'], 0, 5),
           'EVENT_TIME_TO' => substr($row['EVENT_TIME_TO'], 0, 5),
           'COMMENT' => $row['COMMENT'],
           'IMAGE_PATH' => $row['IMAGE_PATH']
        ];
    }
    
    header('Content-type: application/json');
    echo json_encode($eventData);
?>
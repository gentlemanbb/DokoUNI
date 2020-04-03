<?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();
    $areaID = $_POST['areaID'];
    $addDays = $_POST['addDays'];


    $areaSearchQuery = '';

    if($areaID != null || $areaID != ''){
        $areaSearchQuery = sprintf('
            AND PLACE.AREA_ID = %s
        ', $areaID);
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
            
        FROM D_EVENT EVENT
        LEFT JOIN M_PLACE PLACE
            ON EVENT.PLACE_ID = PLACE.PLACE_ID
        WHERE
            EVENT_DATE BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
        %s
        ORDER BY
            EVENT_DATE ASC
            , EVENT_TIME_FROM ASC
        ", $addDays, $addDays + 6, $areaSearchQuery);
        
    $rows = $dbAccess->Select($query);

    $eventData = [];

    foreach ($rows as $row){
       $eventData[] = 
       [
           'EVENT_ID' => $row['EVENT_ID'],
           'PLACE_NAME' => $row['PLACE_NAME'],
           'EVENT_NAME' => $row['EVENT_NAME'],
           'EVENT_DATE' => $row['EVENT_DATE'],
           'EVENT_TIME_FROM' => substr($row['EVENT_TIME_FROM'], 0, 5),
           'EVENT_TIME_TO' => substr($row['EVENT_TIME_TO'], 0, 5),
           'COMMENT' => $row['COMMENT']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($eventData);
?>
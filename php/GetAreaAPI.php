<?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = "
        SELECT
         AREA_ID
         , AREA_NAME
        FROM M_AREA
        WHERE AREA_ID <> 999";

    $rows = $dbAccess->Select($query);

    $placeData = [];

    foreach ($rows as $row){
        $placeData[] = 
        [
            'AREA_ID' => $row['AREA_ID'],
            'AREA_NAME' => $row['AREA_NAME']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($placeData);
?>
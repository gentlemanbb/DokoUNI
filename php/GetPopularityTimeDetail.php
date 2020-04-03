<?php
    require "DBAccess.php";
    $placeID = $_POST['placeID'];
    $addDays = $_POST['addDays'];

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = "";
    $isFirst = True;

    for($i=10; $i <= 23; $i++){

        if($isFirst == False){
            $query = $query . " UNION ALL ";
        }
        else{
            $isFirst = False;
        }

        $query = $query . sprintf("
            SELECT
                %s AS TIME
                ,COUNT(1) AS NUMBER
                FROM D_POPULARITY POP
            WHERE POP.PLACE_ID = %s
            AND JOIN_DATE_FROM BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL %s + 1 DAY)
            AND POP.JOIN_TIME_FROM <= '%s:00:00'
            AND POP.JOIN_TIME_TO  >= '%s:00:00'
            "
            , $i
            , $placeID
            , $addDays
            , $addDays
            , $i
            , $i + 1
        );
    }

    $rows = $dbAccess->Select($query);

    $popularityTimeDetail = [];

    foreach ($rows as $row){
        $popularityTimeDetail[] = 
        [
            'TIME' => $row['TIME'],
            'NUMBER' => $row['NUMBER']
        ];
    }
    header('Content-type: application/json');
    echo json_encode($popularityTimeDetail);
?>
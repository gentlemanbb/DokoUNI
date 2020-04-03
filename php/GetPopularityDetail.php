<?php
    require "DBAccess.php";
    $placeID = $_POST['placeID'];
    $addDays = $_POST['addDays'];

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = sprintf("
            SELECT
                PLACE.PLACE_NAME
                , CHARA.CHARACTER_NAME
                , POP.RIP
                , POP.JOIN_TIME_FROM
                , POP.JOIN_TIME_TO
                , POP.USER_ID
            FROM
                D_POPULARITY POP
            LEFT JOIN
                M_PLACE PLACE
            ON
                POP.PLACE_ID = PLACE.PLACE_ID
            LEFT JOIN
                D_CHARA CHARA
            ON
                POP.CHARACTER_ID = CHARA.CHARACTER_ID
            WHERE
                POP.PLACE_ID = '%s'
            AND
                JOIN_DATE_FROM = DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)",
            $placeID, $addDays);

    $rows = $dbAccess->Select($query);

    $popularityDetail = [];

    foreach ($rows as $row)
    {
        $dateFrom = date_create($row['JOIN_TIME_FROM']);
        $dateTo = date_create($row['JOIN_TIME_TO']);

        $popularityDetail[] = 
        [
            'PLACE_NAME' => $row['PLACE_NAME'],
            'CHARACTER_NAME' => $row['CHARACTER_NAME'],
            'RIP' => $row['RIP'],
            'JOIN_TIME_FROM' => date_format($dateFrom, 'H:i'),
            'JOIN_TIME_TO' => date_format($dateTo, 'H:i'),
            'USER_ID' => $row['USER_ID']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($popularityDetail);
?>
<?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = sprintf("
    SELECT
        USER_ID
        , USER_NAME
        , AUTH.AUTHORITY_NAME
        , AREA.AREA_NAME
        , CHARA.CHARACTER_NAME
        , RIP
        , TYPE1.CAPTION AS NOTIFICATION
        , TWITTER

    FROM D_USER USR

    LEFT JOIN M_AUTHORITY AUTH
        ON USR.AUTHORITY_ID = AUTH.AUTHORITY_ID

    LEFT JOIN M_AREA AREA
        ON USR.AREA_ID = AREA.AREA_ID

    LEFT JOIN D_CHARA CHARA
        ON USR.MAIN_CHARACTER_ID = CHARA.CHARACTER_ID

    LEFT JOIN M_TYPE TYPE1
        ON USR.NOTIFICATION = TYPE1.VALUE
        AND TYPE1.TYPE_KEY = 'NOTICE'
    
    ORDER BY USR.USER_ID ASC, AUTH.AUTHORITY_ID ASC
    ");

    $rows = $dbAccess->Select($query);

    $userData = [];

    foreach ($rows as $row){
        $userData[] = 
        [
            'USER_ID' => $row['USER_ID'],
            'USER_NAME' => $row['USER_NAME'],
            'AUTHORITY_NAME' => $row['AUTHORITY_NAME'],
            'AREA_NAME' => $row['AREA_NAME'],
            'CHARACTER_NAME' => $row['CHARACTER_NAME'],
            'RIP' => $row['RIP'],
            'NOTIFICATION' => $row['NOTIFICATION'],
            'TWITTER' => $row['TWITTER']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($userData);
?>
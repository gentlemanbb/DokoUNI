<?php
    require "DBAccess.php";
    $gameID = $_POST['gameID'];

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = sprintf("
        SELECT
            CHARACTER_ID
            , CHARACTER_NAME
        FROM D_CHARA
        WHERE
         GAME_ID = %s
        ORDER BY PRIOLITY ASC
        ", $gameID);

    $rows = $dbAccess->Select($query);

    $characterData = [];

    foreach ($rows as $row){
        $characterData[] = 
        [
            'CHARACTER_ID' => $row['CHARACTER_ID'],
            'CHARACTER_NAME' => $row['CHARACTER_NAME']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($characterData);
?>
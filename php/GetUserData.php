<?php
    require "DBAccess.php";
    $userID = $_POST['userID'];

    $dbAccess = new DBAccess();
    $pdo = $dbAccess->DBConnect2();

    $query = sprintf("
        SELECT
            USER_NAME
            , MAIN_CHARACTER_ID
            , RIP
            , TWITTER
            , NOTIFICATION
            , AREA_ID
            , AUTH.AUTHORITY_NAME
            , USR.AGREE_DISPLAY_NAME
            , (SELECT COUNT(1) FROM D_POPULARITY POP WHERE POP.USER_ID = :userID1) AS SEND_COUNT
            , IFNULL(
                (SELECT
                (
                    SELECT
                        COUNT(1)+1
                    FROM (
                        SELECT
                            COUNT(1) AS SCORE
                        FROM D_POPULARITY
                        GROUP BY USER_ID) AS RANK_TABLE2
                        WHERE RANK_TABLE2.SCORE > RANK_TABLE1.SCORE) AS RANK
                FROM (
                    SELECT
                        POP.USER_ID,
                        COUNT(1) AS SCORE
                    FROM D_POPULARITY POP
                    WHERE USER_ID = :userID2
                    GROUP BY USER_ID) AS RANK_TABLE1
                ), (SELECT COUNT(1) FROM (SELECT 1 FROM D_POPULARITY GROUP BY USER_ID) AS SENT_USER_NUM)) AS RANK

        FROM D_USER USR
        LEFT JOIN M_AUTHORITY AUTH
        ON USR.AUTHORITY_ID = AUTH.AUTHORITY_ID
        WHERE
         USR.USER_ID = :userID3
        ");

    // $rows = $dbAccess->Select($query);
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userID1', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':userID2', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':userID3', $userID, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if(count($rows) == 0){
        header('Content-type: application/json');
        echo json_encode(false);
    }

    // 返し値用インスタンス
    $userData = [];

    foreach ($rows as $row){
        $userData[] = 
        [
            'USER_NAME' => $row['USER_NAME'],
            'MAIN_CHARACTER_ID' => $row['MAIN_CHARACTER_ID'],
            'RIP' => $row['RIP'],
            'TWITTER' => $row['TWITTER'],
            'NOTIFICATION' => $row['NOTIFICATION'],
            'AREA_ID' => $row['AREA_ID'],
            'AUTHORITY_NAME' => $row['AUTHORITY_NAME'],
            'SEND_COUNT' => $row['SEND_COUNT'],
            'RANK' => $row['RANK'],
            'AGREE_DISPLAY_NAME' => $row['AGREE_DISPLAY_NAME']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($userData);
?>
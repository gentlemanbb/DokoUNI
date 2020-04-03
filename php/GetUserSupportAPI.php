<?php
    require "DBAccess.php";

    try{
        header('Content-type: application/json');
    $dbAccess = new DBAccess();
    $pdo = $dbAccess->DBConnect2();
    $userID = $_POST['userID'];

    $query = sprintf("
    SELECT
        SUPPORT_ID
        , TYPE1.CAPTION AS CATEGORY
        , TEXT
        , SUPPORT_RESULT
        , TYPE2.CAPTION AS STATUS
        , REGIST_USER_ID
        , REGIST_DATETIME
    FROM D_SUPPORT SUP
    LEFT JOIN M_TYPE TYPE1
        ON SUP.CATEGORY = TYPE1.VALUE
        AND TYPE1.TYPE_KEY = 'CATEGORY_TYPE'

    LEFT JOIN M_TYPE TYPE2
        ON SUP.STATUS = TYPE2.VALUE
        AND TYPE2.TYPE_KEY = 'SUPPORT_STATUS'

    WHERE SUP.REGIST_USER_ID = :userID
    ORDER BY REGIST_DATETIME ASC
    ");

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->execute();

    $supportData = [];

    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $supportData[] = [
            'SUPPORT_ID' => $row['SUPPORT_ID'],
            'CATEGORY' => $row['CATEGORY'],
            'TEXT' => $row['TEXT'],
            'SUPPORT_RESULT' => $row['SUPPORT_RESULT'],
            'STATUS' => $row['STATUS'],
            'REGIST_USER_ID' => $row['REGIST_USER_ID'],
            'REGIST_DATETIME' => $row['REGIST_DATETIME']
        ];
    }
    
        echo json_encode($supportData);
    }
    catch(Exception $e){
        echo json_encode($e);
        
    }
?>
<?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = sprintf("
    SELECT
        SUPPORT_ID
        , TYPE1.CAPTION AS CATEGORY
        , TEXT
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

    ORDER BY REGIST_DATETIME ASC
    ");

    $rows = $dbAccess->Select($query);

    $supportData = [];

    foreach ($rows as $row){
        $supportData[] = 
        [
            'SUPPORT_ID' => $row['SUPPORT_ID'],
            'CATEGORY' => $row['CATEGORY'],
            'TEXT' => $row['TEXT'],
            'STATUS' => $row['STATUS'],
            'REGIST_USER_ID' => $row['REGIST_USER_ID'],
            'REGIST_DATETIME' => $row['REGIST_DATETIME']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($supportData);
?>
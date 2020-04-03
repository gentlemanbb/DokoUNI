<?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $supportID = $_POST['supportID'];

    $query = sprintf("
    SELECT
        SUPPORT_ID
        , TYPE1.CAPTION AS CATEGORY_CAPTION
        , TEXT
        , SUPPORT_RESULT
        , STATUS
        , REGIST_USER_ID
        , REGIST_DATETIME
    FROM D_SUPPORT SUP

    LEFT JOIN M_TYPE TYPE1
        ON SUP.CATEGORY = TYPE1.VALUE
        AND TYPE1.TYPE_KEY = 'CATEGORY_TYPE'

    LEFT JOIN M_TYPE TYPE2
        ON SUP.STATUS = TYPE2.VALUE
        AND TYPE2.TYPE_KEY = 'SUPPORT_STATUS'

    WHERE SUPPORT_ID = %s

    ORDER BY REGIST_DATETIME ASC
    ", $supportID);

    $rows = $dbAccess->Select($query);

    $supportDetailData = [];

    foreach ($rows as $row){
        $supportDetailData[] = 
        [
            'SUPPORT_ID' => $row['SUPPORT_ID'],
            'CATEGORY_CAPTION' => $row['CATEGORY_CAPTION'],
            'TEXT' => $row['TEXT'],
            'SUPPORT_RESULT' => $row['SUPPORT_RESULT'],
            'STATUS' => $row['STATUS'],
            'REGIST_USER_ID' => $row['REGIST_USER_ID'],
            'REGIST_DATETIME' => $row['REGIST_DATETIME']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($supportDetailData);
?>
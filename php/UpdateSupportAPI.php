<?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $replyText = $_POST['replyText'];
    $statusType = $_POST['statusType'];
    $supportID = $_POST['supportID'];

    $query = sprintf("
    UPDATE D_SUPPORT SET
        SUPPORT_RESULT = '%s'
        , STATUS = %s
    WHERE SUPPORT_ID = %s
    ",
    $replyText,
    $statusType,
    $supportID);

    $rows = $dbAccess->Update($query);

    header('Content-type: application/json');
    echo json_encode(true);
?>
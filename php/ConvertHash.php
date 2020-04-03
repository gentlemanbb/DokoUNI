<?php
    require "DBAccess.php";
    require "Encrypt.php";

    // 引数
    $userID = $_POST['userID'];

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = sprintf("
        SELECT
            PASSWORD
        FROM D_USER
        WHERE
            USER_ID = '%s'
        ", $userID);

    $rows = $dbAccess->Select($query);

    if(count($rows) == 0){
        die();
    }

    $dbPassword;
    $result = [];

    foreach ($rows as $row){
        $dbPassword = $row['PASSWORD'];
    }

   	header('Content-type: application/json');

    if(strlen($dbPassword) == 60){
            $rtnValue[] = [
                'RESULT' => false,
                'MESSAGE' => '暗号化済み'
            ];

        echo json_encode($rtnValue);
    }
    else{
    $newPassword = Encrypt($dbPassword);

    $query = sprintf("
        UPDATE D_USER SET
            PASSWORD = '%s'
        WHERE
            USER_ID = '%s'
        ",$newPassword, $userID);

    $rows = $dbAccess->Update($query);

    echo json_encode(true);
    }

?>
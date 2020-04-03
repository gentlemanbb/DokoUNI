<?php
    require "DBAccess.php";

    // 引数
    $userID = $_POST['userID'];
    $password = $_POST['password'];

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();

    $query = sprintf("
        SELECT
            PASSWORD
            , USER_NAME
            , USER_ID
        FROM D_USER
        WHERE
            USER_ID = '%s'
        ", $userID);

    $rows = $dbAccess->Select($query);

    if(count($rows) == 0){
        die('ログイン失敗');
    }

    $dbPassword;
    $result = [];

    foreach ($rows as $row){
        $dbPassword = $row['PASSWORD'];
    }

    header('Content-type: application/json');

    if($dbPassword == $password)
    {
        $result[] = [
            'RESULT' => true,
            'USER_NAME' => $row['USER_NAME'],
            'USER_ID' => $row['USER_ID']
        ];

        // パスワードが一致している場合
        echo json_encode($result);
    }
    else
    {
        // パスワードが不一致の場合
        echo  json_encode(false);
    }


?>
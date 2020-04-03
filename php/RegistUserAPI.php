<?php
    require "DBAccess.php";
    require "Encrypt.php";

    // 引数
    $userID = $_POST['userID'];
    $password = $_POST['password1'];
    $userName = $_POST['userName'];
    $password = Encrypt($password);

    $dbAccess = new DBAccess();
    $dbAccess->DBConnect();
    $pdo = $dbAccess->DBConnect2();

    $query = sprintf("
        SELECT
            1 AS EXIST_RESULT
        FROM D_USER
        WHERE USER_ID = :userID");

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->execute();
    
    $selectResult;
    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        $selectResult = $row['EXIST_RESULT'];
    }

    header('Content-type: application/json');

    if($selectResult != null){
        $rtnValue[] = [
            'RESULT' => false,
            'MESSAGE' => '入力したユーザＩＤはすでに使用されています。'
        ];
        echo json_encode($rtnValue);
    }
    else
    {
        $query = sprintf("
        INSERT INTO D_USER (
            USER_ID
            , PASSWORD
            , USER_NAME
            , AUTHORITY_ID
            , TOROKU_DATE
            , TOROKU_USER_ID
            , KOSHIN_DATE
            , KOSHIN_USER_ID
            , DELETE_FLG
            , BAN_FLG)
        VALUES(
            :userID
            , :password
            , :userName
            , 2
            , CURRENT_DATE()
            , 'SYSTEM'
            , CURRENT_DATE()
            , 'SYSTEM'
            , 0
            , 0
        )");

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
        $result = $stmt->execute();

        // $result = $dbAccess->Insert($query);

        if($result == True){
            $rtnValue[] = [
                'RESULT' => true,
                'USER_NAME' => $userName,
                'PASSWORD' => $password
            ];

            echo json_encode($rtnValue);
        }
        else{
            echo json_encode(False);
        }
    }
?>
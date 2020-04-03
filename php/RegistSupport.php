<?php
    require "DBAccess.php";

    try{
    // 引数
    $userID = $_POST['userID'];
    $categoryType = $_POST['categoryType'];
    $text = $_POST['text'];

    $dbAccess = new DBAccess();
    $pdo = $dbAccess->DBConnect2();

    if($userID == ''){
        $userID = '未ログインユーザ';
    }

    $query = sprintf("
        INSERT INTO D_SUPPORT (
            CATEGORY
            , TEXT
            , STATUS
            , REGIST_USER_ID
            , REGIST_DATETIME)

            VALUES(
                :categoryType
                , :text
                , 0
                , :userID
                , NOW()
            )");


        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':categoryType', $categoryType, PDO::PARAM_STR);
        $stmt->bindParam(':text', $text, PDO::PARAM_STR);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
        $result = $stmt->execute();

        header('Content-type: application/json');
    
        if($result == True){
            $rtnValue[] = [
                'RESULT' => true
            ];

            echo json_encode($rtnValue);
        }
        else{
            $rtnValue[] = [
                'RESULT' => false,
                'QUERY_RESULT' => $result
            ];

            echo json_encode($rtnValue);
        }
    }
    catch (Exception $e) {
        return json_encode($e);
    }
?>
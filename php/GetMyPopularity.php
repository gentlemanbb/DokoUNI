<?php
    try
    {
		// ライブラリ読み込み
		require "DBAccess.php";
		require "TweetAPI.php";
        require "LogUtility.php";

        header('Content-type: application/json');

		// DB接続
		$dbAccess = new DBAccess();
        $pdo = $dbAccess->DBConnect2();
        
        // 引数取得
        $userID = $_POST['userID'];
        $addDays =  $_POST['addDays'];

   		// 登録に使う日付
        $joinDate = date("Y-m-d", strtotime(sprintf("+%s day", $addDays)));

        $query = sprintf("
            SELECT
                POP.PLACE_ID
                , POP.JOIN_TYPE
                , POP.PURPOSE_TYPE
                , POP.JOIN_TIME_FROM
                , POP.JOIN_TIME_TO
                , POP.COMMENT
            FROM
                D_POPULARITY POP
            WHERE
                POP.USER_ID = :userID
            AND
                POP.JOIN_DATE_FROM = :joinDate");

        $stmt = $pdo->prepare($query);
        $pdo->beginTransaction();
        $stmt->bindParam(':userID'		, $userID		, PDO::PARAM_STR);
        $stmt->bindParam(':joinDate'	, $joinDate		, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $returnData = [];
        
        if(is_array($rows) && count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $data = [
                    'PLACE_ID' => $row['PLACE_ID'],
                    'JOIN_TYPE' => $row['JOIN_TYPE'],
                    'PURPOSE_TYPE' => $row['PURPOSE_TYPE'],
                    'JOIN_TIME_FROM' => $row['JOIN_TIME_FROM'],
                    'JOIN_TIME_TO' => $row['JOIN_TIME_TO'],
                    'COMMENT' => $row['COMMENT'],
                ];
            }

            $returnData = [
                'RESULT' => true,
                'MESSAHE' => '',
                'POP_DATA' => $data
            ];
        }
        else
        {
            $returnData = [
                'RESULT' => true,
                'MESSAGE' => 'データが存在しませんでした。',
                'POP_DATA' => null
            ];
        }

        echo json_encode($returnData);

        return;
    }
    catch(Exception $ex)
    {
        WriteErrorLog($ex);
        
        $result = [
            'RESULT' => false,
            'MESSAGE' => $ex->getMessage()
        ];
        echo json_encode($result);
    }
?>
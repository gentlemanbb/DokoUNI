<?php
    require "DBAccess.php";
    require "LogUtility.php";

    $userID = $_POST['userID'];

    $dbAccess = new DBAccess();
    $pdo = $dbAccess->DBConnect2();

    header('Content-type: application/json');

    $result = [];

    try
    {
        $query = sprintf("
            SELECT
                USER_ID
                , AUTHORITY_NAME
                , SYSTEM_MANAGEMENT
                , REGIST_EVENT
                , LOGIN
            FROM
                D_USER USR
            LEFT JOIN
                M_AUTHORITY AUTH
            ON
                USR.AUTHORITY_ID = AUTH.AUTHORITY_ID
            WHERE
                USER_ID = :userID");
                
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
        $result = $stmt->execute();
        $dataRows = $stmt->fetchAll();

        if(is_array($dataRows) && count($dataRows) > 0)
        {
            foreach ($dataRows as $dataRow)
            {
                if($dataRow['SYSTEM_MANAGEMENT'] == 1)
                {
                    $result = [
                        'RESULT' => true,
                        'MESSAGE' => '管理者ユーザーであることを確認しました。'
                    ];
                }
                else
                {
                    $result = [
                        'RESULT' => false,
                        'MESSAGE' => '管理者ユーザーではありませんでした。'
                    ];
                }
            }
        }
        else
        { 
            $result = [
                'RESULT' => false,
                'MESSAGE' => 'ユーザーが存在しませんでした。'
            ];
        }
    }
    catch(Exception $ex)
    {
        WriteErrorLog($ex);

        $result = [
            'RESULT' => false,
            'MESSAGE' => '確認に失敗しました。'
        ];
    }

    echo json_encode($result);
?>
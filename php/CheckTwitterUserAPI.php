<?php	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
    require "GetDataUtility.php";
    
    header('Content-type: application/json');

    $returnData = [];

    try
    {
        $dbAccess = new DBAccess();
        $pdo = $dbAccess->DBConnect2();

        // 引数取得
        $twitterID = $_POST['twitterID'];
        $userID = $_POST['userID'];

        $query = sprintf("
            SELECT
                USER_ID
            FROM
                D_USER
            WHERE
                TWITTER = :twitterID");
            
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':twitterID', $twitterID, PDO::PARAM_STR);
        $stmt->execute();
        $dataRows = $stmt->fetchAll();
        
        if(is_array($dataRows) && count($dataRows) > 0)
        {
            foreach ($dataRows as $dataRow)
            {
                $dbUserID = $dataRow['USER_ID'];
                $data = null;

                if($userID == $dbUserID)
                {
                    $returnData = [
                        'RESULT' => true,
                        'MESSAGE' => 'すでに連携済みです。'
                    ];
                }
                else
                {
                    $returnData = [
                        'RESULT' => false,
                        'MESSAGE' => 'このTwitterアカウントは他のユーザーに使用されています。'
                    ];
                }
            }
        }
        else
        {
            $returnData = [
                'RESULT' => true,
                'MESSAGE' => '未連携のユーザーです。'
            ];
        }
    }
    catch(Exception $ex)
    {
        WriteErrorLog($ex);

        $returnData = [
            'RESULT' => false,
            'MESSAGE' => $ex->Message
        ];
    }

    echo json_encode($returnData);
?>
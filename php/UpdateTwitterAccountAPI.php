<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	require "GetDataUtility.php";
	
	// DBアクセサ
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	header('Content-type: application/json');

	$returnData = null;

	try
	{
        // 引数取得
        $twitterID = $_POST['twitterID'];
		$userID = $_POST['userID'];
				
    	$query = sprintf("
			UPDATE
				D_USER
			SET
    			TWITTER = :twitterID	
			WHERE
				USER_ID = :userID
		");
		
    	$stmt = $pdo->prepare($query);
    	
    	// トランザクション
    	$pdo->beginTransaction();
    	$stmt->bindParam(':twitterID', 		$twitterID,		PDO::PARAM_STR);
    	$stmt->bindParam(':userID', 		$userID, 		PDO::PARAM_STR);
		$sqlResult = $stmt->execute();

    	if($sqlResult)
    	{
			// ここまで来たらコミット
			$pdo->commit();

			$returnData = [
				'RESULT' => true,
				'MESSAGE' => 'Twitterアカウントの連携に成功しました。'
			];
		}
		else
		{
			$pdo->rollback();

			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'Twitterアカウントの連携に失敗しました。'
			];
		}
    }
    catch(Exception $ex)
    {
		$pdo->rollback();

		WriteErrorLog($ex);
					
		$returnData = [
			'RESULT' => false,
			'MESSAGE' => 'イベント画像の更新に失敗しました。'
		];
	}
	
	echo json_encode($returnData);
?>
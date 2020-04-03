<?php
	// ライブラリの読み込み
	require 'DBAccess.php';
	require 'TweetAPI.php';
	require 'LogUtility.php';
	
	$dbAccess = new DBAccess();
    
	// DB接続
	$pdo = $dbAccess->DBConnect2();
	
	try
	{
		$query = '
			SELECT
				BASE.USER_ID
				, BASE.TWITTER as TWITTER_ID
			FROM
				D_USER as BASE
			LEFT JOIN
				D_PLAYING as SUB
			ON
				BASE.TWITTER = SUB.PLAYER_NAME
			WHERE
				SUB.USER_ID IS NULL
			AND
				BASE.TWITTER IS NOT NULL';
				
		$stmt = $pdo->prepare($query);
		$stmt->execute();
		$dataRows = $stmt->fetchAll();
		
		$updateCount = 0;
		
		// 取得結果があるかチェックする
		if(is_array($dataRows))
		{
			// $updateCount = count($dataRows);
			
			// １件ずつ処理する
			foreach($dataRows as $dataRow)
			{
				// ツイッターIDを取得
				$userID = $dataRow['USER_ID'];
				$twitterID = $dataRow['TWITTER_ID'];
				
				// ツイッターＩＤ登録済みのユーザーがいたら
				// ユーザーＩＤを更新する
				$query = "
					UPDATE
						D_PLAYING
					SET
						USER_ID = :userID
					WHERE
						PLAYER_NAME = :playerName
				";
				
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':userID', 	$userID, 	PDO::PARAM_STR);
				$stmt->bindParam(':playerName', $twitterID, PDO::PARAM_STR);
				$stmt->execute();
				
				WriteLog('UpdatePlayingDataForTwitter', sprintf('%s, %s', $userID, $twitterID));
			}
			
			$result = [
				'RESULT' => TRUE,
				'MESSAGE' => sprintf('%s件 更新完了しました。', $updateCount)
			];
		}
		else
		{
			$result = [
				'RESULT' => TRUE,
				'MESSAGE' => '更新対象はありませんでした。'
			];
		}
		
		header('Content-type: application/json');
		
		echo json_encode($result);
	}
	catch(Exception $ex)
	{
		$result = [
			'RESULT' => FALSE,
			'MESSAGE' => $ex->getMessage()
		];
		
		// ログ出力
		WriteLog('ErrorLog', $ex->getMessage());
		
		header('Content-type: application/json');
		
		echo json_encode($result);
	}
?>
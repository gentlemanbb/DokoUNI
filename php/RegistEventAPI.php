<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	
	// 今日の日付取得
	$now = date('Y-m-d');
	$registeredDatetime;
	$yesterday = date('Y-m-d', strtotime("-1 day"));
	
	header('Content-type: application/json');

	try
	{
		// 引数を取得
		$userID = $_POST['userID'];
		$eventName = $_POST['eventName'];
		$eventDate = $_POST['eventDate'];
		$placeID = $_POST['placeID'];
		$placeName = $_POST['placeName'];
		$eventTimeFrom = $_POST['eventTimeFrom'];
		$eventTimeTo = $_POST['eventTimeTo'];
		$comment = $_POST['comment'];
		$imagePath = $_POST['imagePath'];
		
		// Tweetを行う
		$withTweet = $_POST['withTweet'];
		
		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		
		// 
		$query = "
			INSERT INTO D_EVENT (
				PLACE_ID
				, EVENT_NAME
				, EVENT_DATE
				, EVENT_TIME_FROM
				, EVENT_TIME_TO
				, COMMENT
				, TOROKU_USER_ID
				, KOSHIN_USER_ID
				, DISPLAY_FLG";

		if($imagePath != null && $imagePath != '')
		{
			$query = $query . ", IMAGE_PATH";
		}
		
		$query = $query . ")";
		
		$query = $query . "
			VALUES(
				:placeID
				, :eventName
				, :eventDate
				, :eventTimeFrom
				, :eventTimeTo
				, :comment
				, :torokuUserID
				, :koshinUserID
				, false";
		
		if($imagePath != null && $imagePath != '')
		{
			$query = $query . ", :imagePath";
		}
				
		$query = $query . ")";
		
		$stmt = $pdo->prepare($query);
		
		// トランザクション
		$pdo->beginTransaction();
		$stmt->bindParam(':placeID', 		$placeID, 		PDO::PARAM_INT);
		$stmt->bindParam(':eventName', 		$eventName, 	PDO::PARAM_STR);
		$stmt->bindParam(':eventDate', 		$eventDate, 	PDO::PARAM_STR);
		$stmt->bindParam(':eventTimeFrom', 	$eventTimeFrom, PDO::PARAM_STR);
		$stmt->bindParam(':eventTimeTo', 	$eventTimeTo, 	PDO::PARAM_STR);
		$stmt->bindParam(':comment', 		$comment, 		PDO::PARAM_STR);
		$stmt->bindParam(':torokuUserID', 	$userID, 		PDO::PARAM_STR);
		$stmt->bindParam(':koshinUserID', 	$userID, 		PDO::PARAM_STR);

		if($imagePath != null && $imagePath != '')
		{
			$stmt->bindParam(':imagePath', 	$imagePath, 	PDO::PARAM_STR);
		}
		
		$result = $stmt->execute();
		$insertID = $pdo->lastInsertId('id');

		//if($withTweet)
		//{
		//	// TWEETする場合
		//	$tweetStr = sprintf('#どこUNI [イベント情報] %s %s %s が登録されました。', $eventDate, $placeName, $eventName);
		//	$tweetResult = Tweet($tweetStr . $notificationStr);
		//}
		
		if($result == True)
		{
			// テキストファイルに書き込み
			WriteLog('log', 'イベント登録に成功しました。');

			// コミット
			$pdo->commit();
			
			// 返し値
			$returnValue = 
			[
				'RESULT' => True,
				'INSERT_ID' => $insertID
			];
			
			echo json_encode($returnValue);
		}
		else
		{
			// テキストファイルに書き込み
			WriteLog('log', 'イベント登録に失敗しました。');

			// 返し値
			$returnValue = 
			[
				'RESULT' => False,
				'INSERT_ID' => null
			];

			echo json_encode($returnValue);
		}
	}
	catch(Exception $ex)
	{
		// ロールバック
		$pdo->rollBack();
		
		// エラーログを出力
		WriteErrorLog($ex);
		
		$result = [
			'RESULT' => false,
			'MESSAGE' => 'イベント登録に失敗しました。',
			'INSERT_ID' => null
		];
		
		echo json_encode($result);
	}
?>
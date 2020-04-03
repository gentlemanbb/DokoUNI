<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	
	header('Content-type: application/json');

	try
	{
		// 引数を取得
		$userID = $_POST['userID'];
		$eventName = $_POST['eventName'];
		$year = $_POST['year'];
		$month = $_POST['month'];
		$comment = $_POST['comment'];
		$placeID = $_POST['placeID'];
		$placeName = $_POST['placeName'];
		$eventDate = $_POST['eventDate'];
		$eventTimeFrom = $_POST['eventTimeFrom'];
		$eventTimeTo = $_POST['eventTimeTo'];
		$comment = $_POST['comment'];
		$imagePath = $_POST['imagePath'];

		$eventDate = $_POST['eventDate'];
		$eventDateTime = new DateTime($eventDate);
		$eventWeek = (int)$eventDateTime->format('w');

		$yearMonth = sprintf('%s-%s', $year, $month);
		$firstDate = date('d', strtotime('first day of ' . $yearMonth));
		$lastDate = date('d', strtotime('last day of ' . $yearMonth));

		$logStr = sprintf(
			'[ユーザID：%s], [イベント名：%s], [場所ID：%s], [場所名：%s], [登録年月：%s-%s to %s-%s], [開始日時：%s], [終了日時：%s], [コメント：%s]'
			, $userID, $eventName, $placeID, $placeName
			, $yearMonth, $firstDate, $yearMonth, $lastDate
			, $eventTimeFrom, $eventTimeTo, $comment);

		WriteLog('イベント一括登録API', $logStr);
	
		$registDays = [];
		$insertIDs = [];

		for($i=1; $i < $lastDate; $i++)
		{
			$date = sprintf("%s-%s", $yearMonth, $i);
			$datetime = new DateTime($date);
			$w = (int)$datetime->format('w');

			//  0     1     2     3    4      5    6
			// "日", "月", "火", "水", "木", "金", "土"

			if($eventWeek == $w)
			{
				WriteLog('イベント一括登録API', sprintf('%s に登録します。', $date));

				// 登録する日付として配列に追加
				$registDays[] = $i;
			}
		}

		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		
		// トランザクション開始
		$pdo->beginTransaction();

		$result = true;

		foreach($registDays as $registDay)
		{
			// クエリ生成
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
			
			$stmt->bindParam(':placeID', 		$placeID, 		PDO::PARAM_INT);
			$stmt->bindParam(':eventName', 		$eventName, 	PDO::PARAM_STR);
			$stmt->bindParam(':eventDate', 		sprintf("%s-%s", $yearMonth, $registDay), 	PDO::PARAM_STR);
			$stmt->bindParam(':eventTimeFrom', 	$eventTimeFrom, PDO::PARAM_STR);
			$stmt->bindParam(':eventTimeTo', 	$eventTimeTo, 	PDO::PARAM_STR);
			$stmt->bindParam(':comment', 		$comment, 		PDO::PARAM_STR);
			$stmt->bindParam(':torokuUserID', 	$userID, 		PDO::PARAM_STR);
			$stmt->bindParam(':koshinUserID', 	$userID, 		PDO::PARAM_STR);

			if($imagePath != null && $imagePath != '')
			{
				$stmt->bindParam(':imagePath', 	$imagePath, 	PDO::PARAM_STR);
			}

			$queryResult = $stmt->execute();

			if(!$queryResult)
			{
				// クエリが１回でも失敗したらFalseにする
				$result = false;
			}
			else
			{
				$insertIDs[] = $pdo->lastInsertId('id');
			}
		}
		
		// ＳＱＬの実行結果をファイルに書き込む
		if($result)
		{
			$fileStr = $fileStr . 'イベントの登録に成功しました。' . "\n";
		}
		else
		{
			$fileStr = $fileStr . 'イベントの登録に失敗しました。' . "\n";
		}
		
		if($result == True)
		{
			// コミット
			$pdo->commit();
			
			// 返し値
			$returnValue = 
			[
				'RESULT' => True,
				'INSERT_ID' => $insertIDs
			];
			
			echo json_encode($returnValue);
		}
		else
		{
			// 返し値
			$returnValue = 
			[
				'RESULT' => False,
				'INSERT_ID' => null
			];

			echo json_encode(False);
		}
	}
	catch(Exception $ex)
	{
		if($pdo != null && $pdo->inTransaction())
		{
			// ロールバック
			$pdo->rollBack();
		}

		// ツイート内容を書き込む
		WriteErrorLog($ex);
		
		$result = [
			'RESULT' => false,
			'MESSAGE' => 'イベントの登録に失敗しました。',
			'INSERT_ID' => null
		];
		
		echo json_encode($result);
	}
?>
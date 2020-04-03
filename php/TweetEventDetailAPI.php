<?php
	// ライブラリの読み込み
	require 'DBAccess.php';
	require 'TweetAPI.php';
	
	try
	{
		$dbAccess = new DBAccess();

		// DB接続
		$pdo = $dbAccess->DBConnect2();

		// 引数取得
    	$eventID = $_POST['eventID'];
    	
		$now = date('Y-m-d');
		$query = sprintf("
			SELECT
				EVENT.EVENT_ID
				, EVENT.EVENT_NAME
				, PLACE.PLACE_NAME
				, EVENT.EVENT_DATE
				, EVENT.EVENT_TIME_FROM
				, EVENT.EVENT_TIME_TO
				, EVENT.COMMENT
				, EVENT.TOROKU_USER_ID
				, EVENT.SPECIAL_EVENT
				, EVENT.SPECIAL_URL
				, EVENT.IMAGE_PATH

			FROM
				D_EVENT EVENT
				
			LEFT JOIN
				M_PLACE PLACE
			ON
				EVENT.PLACE_ID = PLACE.PLACE_ID
				
			WHERE
				EVENT_ID = :eventID");
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':eventID'		, $eventID	, PDO::PARAM_INT);
		
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		$isFirst = false;
		$eventIsNothing = true;
		
		$tweetStr = sprintf('#どこUNI ≪イベント情報≫');
		
		// データの行数分詰める
		// 存在しない場合は0行で返る
		foreach ($rows as $row)
		{
			$eventIsNothing = false;
			$eventData[] = 
			[
				'EVENT_ID'			=> $row['EVENT_ID'],
				'EVENT_NAME' 		=> $row['EVENT_NAME'],
				'PLACE_NAME' 		=> $row['PLACE_NAME'],
				'EVENT_DATE' 		=> $row['EVENT_DATE'],
				'COMMENT' 			=> $row['COMMENT'],
				'TOROKU_USER_ID' 	=> $row['TOROKU_USER_ID'],
				'SPECIAL_EVENT' 	=> $row['SPECIAL_EVENT'],
				'SPECIAL_URL' 		=> $row['SPECIAL_URL'],
				'IMAGE_PATH' 		=> $row['IMAGE_PATH']
			];
			
			// 改行を入れる
			$tweetStr = $tweetStr . "\r\n";
			
			// 追加
			$tweetStr = $tweetStr . sprintf(
				'%s %s %s' . "\r\n" . '%s'
				, substr($row['EVENT_DATE'],5, 5)
				, $row['PLACE_NAME']
				, $row['EVENT_NAME']
				, $row['COMMENT']
			);
			
			$tweetFileName = $row['IMAGE_PATH'];
		}
		
		header('Content-type: application/text');
		
		if($eventIsNothing)
		{
			echo json_encode(False);
		}
		else
		{
			// ツイート
			$tweetResult = TweetWithFile($tweetStr, $tweetFileName);
			echo json_encode(True);
		}
	}
    catch(Exception $ex)
    {
    	$fileStr = $fileStr . $ex.Message;
    	
		// ツイート内容を書き込む
		fwrite($fp, $fileStr);
		
		// ファイルを閉じる
		fclose($fp);
		
    	$result[] = [
    		'RESULT' => false,
    		'MESSAGE' => $ex.Message
    	];
    	
    	header('Content-type: application/json');
    	
    	echo json_encode($result);
    }
	
?>
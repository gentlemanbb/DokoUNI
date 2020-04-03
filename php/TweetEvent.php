#!/usr/local/php/7.2/bin/php

<?php

	try
	{
		// ライブラリの読み込み
		require 'DBAccess.php';
		require 'TweetAPI.php';
		require 'LogUtility.php';
		
		header('Content-type: application/text');

		$dbAccess = new DBAccess();

		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 今日の日付
		$now = date('m/d');
		
		// エリアを取得。
		$query = sprintf("
			SELECT
				AREA_ID
				, AREA_NAME
			FROM
				M_AREA
			ORDER BY
				AREA_ID ASC");
		
		$stmt = $pdo->prepare($query);
		$stmt->execute();
		$areaRows = $stmt->fetchAll();
		
		// エリアごとに処理
		foreach($areaRows as $areaRow)
		{
			$areaID = $areaRow['AREA_ID'];
			$areaName = $areaRow['AREA_NAME'];
			
			$query = 
			
				"SELECT
					EVENT.EVENT_ID
					, EVENT.EVENT_NAME
					, PLACE.PLACE_NAME
					, DATE_FORMAT(EVENT.EVENT_DATE, '%m/%d') AS EVENT_DATE
					, CASE WEEKDAY(EVENT_DATE)
						WHEN 0 THEN '月'
						WHEN 1 THEN '火'
						WHEN 2 THEN '水'
						WHEN 3 THEN '木'
						WHEN 4 THEN '金'
						WHEN 5 THEN '土'
						WHEN 6 THEN '日'
						ELSE '' END AS WEEKDAY
					, EVENT.EVENT_TIME_FROM
					, EVENT.EVENT_TIME_TO
					, EVENT.COMMENT
					, EVENT.TOROKU_USER_ID
					, EVENT.SPECIAL_EVENT
					, EVENT.SPECIAL_URL
					, EVENT.SPECIAL_URL
					, EVENT.IMAGE_PATH
					, PLACE.AREA_ID
				FROM
					D_EVENT EVENT
				LEFT JOIN
					M_PLACE PLACE
				ON
					EVENT.PLACE_ID = PLACE.PLACE_ID
				WHERE
					EVENT_DATE BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL 6 DAY)
				AND
					PLACE.AREA_ID = :areaID
				ORDER BY
					EVENT_DATE ASC
					, EVENT_TIME_FROM ASC";
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':areaID'		, $areaID	, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll();
			
			$eventData = [];
			
			$isFirst = false;
			$eventIsNothing = true;
			
			$tweetStr = sprintf('#どこUNI ≪%sイベント情報≫', $areaName);
			
			$todayTweetStr = '';
			
			if(is_array($rows) && count($rows) > 0)
			{
				// データの行数分詰める
				// 存在しない場合は0行で返る
				foreach ($rows as $row)
				{
					$eventIsNothing = false;
					
					$eventData[] = 
					[
						'EVENT_ID' => $row['EVENT_ID'],
						'PLACE_NAME' => $row['PLACE_NAME'],
						'EVENT_NAME' => $row['EVENT_NAME'],
						'EVENT_DATE' => $row['EVENT_DATE'],
						'WEEKDAY' => $row['WEEKDAY'],
						'EVENT_TIME_FROM' => substr($row['EVENT_TIME_FROM'], 0, 5),
						'EVENT_TIME_TO' => substr($row['EVENT_TIME_TO'], 0, 5),
						'COMMENT' => $row['COMMENT'],
						'TOROKU_USER_ID' => $row['TOROKU_USER_ID'],
						'SPECIAL_EVENT' => $row['SPECIAL_EVENT'],
						'SPECIAL_URL' => $row['SPECIAL_URL'],
						'IMAGE_PATH' => $row['IMAGE_PATH']
					];
					
					// 改行を入れる
					$tweetStr = $tweetStr . "\r\n";
					
					// 追加
					$tweetStr = $tweetStr . sprintf(
						'%s(%s) %s %s'
						, $row['EVENT_DATE']
						, $row['WEEKDAY']
						, $row['PLACE_NAME']
						, $row['EVENT_NAME']
					);
					
					if((string)$now == (string)$row['EVENT_DATE'])
					{
						// 追加
						$todayTweetStr = sprintf(
							'#どこUNI ≪今日のイベント情報≫' . "\r\n" . '%s - %s' . "\r\n" . '%s で %s が開催予定です。' . "\r\n" . '%s'
							, $row['EVENT_TIME_FROM']
							, $row['EVENT_TIME_TO']
							, $row['PLACE_NAME']
							, $row['EVENT_NAME']
							, $row['COMMENT']
						);
						
						$imagePath = $row['IMAGE_PATH'];
						
						$existsImage = false;
						
						if ($imagePath != null && file_exists($imagePath))
						{
							$existsImage = true;
						}
						
						if($existsImage)
						{
							// ツイート
							$tweetResult = TweetWithFile($todayTweetStr, $imagePath);
						}
						else
						{
							$tweetResult = Tweet($todayTweetStr);
						}
					}
				}

				// イベントがある時だけツイートする
				// ツイート
				if($areaID == '1')
				{
					$number = rand(1, 3);

					$tweetResult = TweetWithFile($tweetStr, '../img/izuko/izuko' . $number . '.jpg');
				}
				else
				{
					$tweetResult = Tweet($tweetStr);
				}
			}
		}
	}
	catch(Exception $ex)
	{
		
	}
	
	echo json_encode($tweetResult);
?>
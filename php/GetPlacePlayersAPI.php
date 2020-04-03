<?php
	// ライブラリの読み込み
	require 'DBAccess.php';
	require 'TweetAPI.php';
	require 'LogUtility.php';
	
	header('Content-type: application/json');

	try
	{
		// 引数取得
		$placeID = $_POST['placeID'];
		
		$dbAccess = new DBAccess();
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		$query = '
			SELECT
				COUNT(1) AS PLAY_COUNT
				, IFNULL(SUB.USER_NAME, BASE.PLAYER_NAME) AS NAME
				, SUB.USER_ID
				, IFNULL(SUB.ICON_IMAGE_PATH, SUB2.IMAGE_PATH) as IMAGE_PATH
				, SUB.AGREE_DISPLAY_NAME
				, SUB.COMMENT
				, COUNT(1)
				, IFNULL(SUB3.INTRODUCED_COUNT, 0) AS INTRODUCED_COUNT
				, SUB.TWITTER
			FROM
				D_PLAYING BASE
			LEFT JOIN
				D_USER SUB
			ON
				BASE.USER_ID = SUB.USER_ID
			LEFT JOIN
				D_CHARA SUB2
			ON
				SUB.MAIN_CHARACTER_ID = SUB2.CHARACTER_ID
			LEFT JOIN
				(SELECT COUNT(1) AS INTRODUCED_COUNT, INTRODUCED_USER_ID FROM D_INTRODUCE GROUP BY INTRODUCED_USER_ID) SUB3
			ON
				SUB.USER_ID = SUB3.INTRODUCED_USER_ID
			WHERE
				TWEET_DATETIME >= DATE_ADD(NOW(), INTERVAL -30 DAY)
			AND
				PLACE_ID = :placeID
			GROUP BY
				PLAYER_NAME, PLACE_ID, SUB3.INTRODUCED_USER_ID
			ORDER BY
				PLAY_COUNT DESC';
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':placeID', $placeID, PDO::PARAM_INT);
		$stmt->execute();
		$dataRows = $stmt->fetchAll();
		
		$data;
		
		if(is_array($dataRows) && count($dataRows) > 0)
		{
			foreach($dataRows as $dataRow)
			{
				// 表示名
				$name = $dataRow['NAME'];
				
				if($dataRow['AGREE_DISPLAY_NAME'] != null)
				{
					if($dataRow['AGREE_DISPLAY_NAME'] == 0)
					{
						$name = '匿名希望';
					}
				}
				
				// 空文字が初期値
				$comment = '';
					
				if($dataRow['COMMENT'] != null)
				{
					// 改行を置き換える
					$comment = str_replace($order, '<br/>', $dataRow['COMMENT']);
				}
				
				// 返し値の配列
				$data[] =
				[
					'PLAY_COUNT' => $dataRow['PLAY_COUNT'],
					'NAME' => $name,
					'ARG_NAME' => "'" . $name . "'",
					'USER_ID' => "'" . $dataRow['USER_ID'] . "'",
					'IMAGE_PATH' => $dataRow['IMAGE_PATH'],
					'COMMENT' => $comment,
					'INTRODUCED_COUNT' => $dataRow['INTRODUCED_COUNT'],
					'TWITTER' => $dataRow['TWITTER']
				];
			}
			
			$result = [
				'RESULT' => true,
				'MESSAGE' => '正常終了',
				'DATA' => $data
			];
		}
		else
		{
			$result = [
				'RESULT' => true,
				'MESSAGE' =>  'データがありません',
				'DATA' => null
			];
		}
		
		echo json_encode($result);
	}
	catch(Exception $ex)
	{
		// エラーの場合
		$result = [
			'RESULT' => false,
			'MESSAGE' =>  $ex->getMessage
		];
		
		WriteLog('GetPlacePlayersAPI', $ex->getMessage());

		echo json_encode($result);
	}
?>
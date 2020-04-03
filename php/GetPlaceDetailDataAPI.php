<?php
	// ライブラリ読み込み
	require_once "DBAccess.php";
	require_once "LogUtility.php";
	require_once "GetDataUtility.php";
	
	header('Content-type: application/json');

	try
	{
		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		
		// 引数取得
		$placeID = $_POST['placeID'];
		$userID = null;

		if(isset($_POST['userID']))
		{
			$userID = $_POST['userID'];
		}
		
		$query = sprintf("
			SELECT
				COUNT(1) AS PLAY_COUNT
				, SUB.PLACE_NAME
				, SUB.PLACE_ID
				, DAYOFWEEK(BASE.TWEET_DATETIME) AS WEEKDAY
			FROM
			D_PLAYING BASE
				
			LEFT JOIN
				M_PLACE SUB
			ON
				BASE.PLACE_ID = SUB.PLACE_ID
			WHERE
				BASE.PLACE_ID = :placeID
			AND
				BASE.TWEET_DATETIME >= DATE_ADD(NOW(), INTERVAL -30 DAY)
			GROUP BY
				PLACE_ID, WEEKDAY
			ORDER BY
				WEEKDAY ASC
			");
			
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':placeID', $placeID, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		$weekData = [];
		$weekDay = '';
		$placeName = '';
		$maxCount = 0;

		$placeData = GetPlaceDetailData($placeID);

		if($placeData != null)
		{
			$placeName = $placeData['PLACE_NAME'];
		}

		if(is_array($rows) && count($rows) != 0)
		{
			foreach ($rows as $row)
			{
				if($maxCount < $row['PLAY_COUNT'])
				{
					$maxCount = $row['PLAY_COUNT'];
				}
				
				$weekData[] = 
				[
					'PLAY_COUNT' 	=> $row['PLAY_COUNT'],
					'WEEKDAY' 		=> $row['WEEKDAY']
				];
			}
		}

		// お気に入り存在フラグ
		$isFavorite = false;

		// お気に入り登録しているかチェック
		if($userID != null)
		{
			// お気に入りIDを取得してみる
			$favoriteID = GetFavoriteID($userID, 'PLACE', $placeID);

			// お気に入り済みかチェック
			if($favoriteID != null)
			{
				// あった場合
				$isFavorite = true;
			}
		}

		// 返し値
		$returnData = [
			'PLACE_NAME' => $placeName,
			'DATA' => $weekData,
			'MAX_COUNT' => $maxCount,
			'IS_FAVORITE' => $isFavorite
		];

		// 終わり
		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);

		$returnData = [
			'RESULT' => false,
			'MESSAGE' => $ex->getMessage()
		];

		echo json_encode($returnData);
	}

	return;
?>
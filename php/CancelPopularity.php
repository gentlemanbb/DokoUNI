<?php
	require_once "DBAccess.php";
	require_once "TweetAPI.php";
	require_once "LogUtility.php";
	header('Content-type: application/json');
	
	// 引数
	$userID = $_POST['userID'];
	$cancelAddDays = $_POST['cancelAddDays'];
	$rtn;
	
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	// 該当する日付の投票IDを取得する
	$query = "
		SELECT
			POP.POPULARITY_ID
			, USR.USER_NAME
			, POP.PLACE_ID
			, PLACE.PLACE_NAME
			, POP.JOIN_DATE_FROM
			, POP.PURPOSE_TYPE
		FROM D_POPULARITY POP
			LEFT JOIN D_USER USR
		ON
			POP.USER_ID = USR.USER_ID
		LEFT
			JOIN M_PLACE PLACE
		ON
			POP.PLACE_ID = PLACE.PLACE_ID
		WHERE
			POP.USER_ID = :userID
		AND
			JOIN_DATE_FROM BETWEEN
				DATE_ADD(CURRENT_DATE(), INTERVAL :cancelAddDays1 DAY)
			AND
				DATE_ADD(CURRENT_DATE(), INTERVAL :cancelAddDays2 + 1 DAY)";
	
	WriteLog("log", sprintf("userID: %s", $userID));
	WriteLog("log", sprintf("cancelAddDays: %s", $cancelAddDays));
	
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':userID',			$userID,		PDO::PARAM_STR);
	$stmt->bindParam(':cancelAddDays1',	$cancelAddDays,	PDO::PARAM_INT);
	$stmt->bindParam(':cancelAddDays2',	$cancelAddDays,	PDO::PARAM_INT);
	
	$stmt->execute();
	$rows = $stmt->fetchAll();
	
	WriteLog("log", sprintf("削除対象 %s件", count($rows)));
	$prevPopularityID = null;
	$playerName;
	$placeID;
	$placeName;
	$joinDate;
	$purposeType;
	
	if(count($rows) > 0)
	{
		foreach ($rows as $row)
		{
			$prevPopularityID = $row['POPULARITY_ID'];
			$playerName = $row['USER_NAME'];
			$placeName = $row['PLACE_NAME'];
			$placeID = $row['PLACE_ID'];
			$joinDate = $row['JOIN_DATE_FROM'];
			$purposeType = $row['PURPOSE_TYPE'];
		}
		
		if($prevPopularityID != null)
		{
			$resultPattern = "DELETE";
			$query = "
				DELETE FROM
					D_POPULARITY
				WHERE
					POPULARITY_ID = :popularityID";
					
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':popularityID'	, $prevPopularityID	, PDO::PARAM_STR);
			$deleteResult = $stmt->execute();
			
			if($deleteResult == False)
			{
				$rtn = [
					'RESULT' => False,
					'MESSAGE' => '取り消しに失敗しました。'
				];
				
				return json_encode($rtn);
			}
			else
			{
				$rtn = [
					'RESULT' => true
				];
			}
		}
		else
		{
			$rtn = [
				'RESULT' => false
			];
			
			return json_encode($rtn);
		}
	}
	
	// 通知ユーザの取得
	$query = "
		SELECT
			POP.USER_ID
			, USR.TWITTER
		FROM
			D_POPULARITY POP
		LEFT JOIN
			D_USER USR
		ON
			POP.USER_ID = USR.USER_ID
		
		WHERE
			POP.PLACE_ID = :placeID
		AND
			POP.JOIN_DATE_FROM BETWEEN
				DATE_ADD(CURRENT_DATE(), INTERVAL :addDays1 DAY)
				AND DATE_ADD(CURRENT_DATE(), INTERVAL :addDays2 + 1 DAY)
		AND (
				(:purposeType1 = 0 AND (USR.NOTIFICATION = 1 OR USR.NOTIFICATION = 2 OR USR.NOTIFICATION = 3))
				OR
				(:purposeType2 = 1 AND (USR.NOTIFICATION = 1 OR USR.NOTIFICATION = 2 OR USR.NOTIFICATION = 4))
				OR
				(:purposeType3 = 2 AND USR.NOTIFICATION = 1)
				OR
				(:purposeType4 = 3 AND USR.NOTIFICATION = 1)
			)
		AND
			USR.TWITTER IS NOT NULL
		GROUP BY USER_ID";
					
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':placeID'	, $placeID	, PDO::PARAM_INT);
	$stmt->bindParam(':addDays1'	, $cancelAddDays	, PDO::PARAM_INT);
	$stmt->bindParam(':addDays2'	, $cancelAddDays	, PDO::PARAM_INT);
	$stmt->bindParam(':purposeType1'	, $purposeType	, PDO::PARAM_INT);
	$stmt->bindParam(':purposeType2'	, $purposeType	, PDO::PARAM_INT);
	$stmt->bindParam(':purposeType3'	, $purposeType	, PDO::PARAM_INT);
	$stmt->bindParam(':purposeType4'	, $purposeType	, PDO::PARAM_INT);
	$deleteResult = $stmt->execute();
	
	// SELECT実行
	$rows = $stmt->fetchAll();
	
	// ユーザ配列
	$notificationUsers = [];
	
	foreach ($rows as $row)
	{
		if($row['TWITTER'] != null && strlen($row['TWITTER']) > 0)
		{
			array_push($notificationUsers, $row['TWITTER']);
		}
	}
	
	// ツイートする文章の設定
	$tweetStr = sprintf(
		"#どこUNI %s さん が %s の %s へのチェックインを キャンセル しました。"
		, $playerName
		, $joinDate
		, $placeName);
		
	$notificationStr;
	
	foreach($notificationUsers as $value)
	{
		$notificationStr = $notificationStr . sprintf(" @%s", $value);
	}
	
	$tweetResult = Tweet($tweetStr . $notificationStr);
	
	echo json_encode($rtn);

?>



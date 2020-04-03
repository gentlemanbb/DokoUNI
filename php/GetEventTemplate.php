<?php
	require_once "DBAccess.php";
	require_once "LogUtility.php";
	
	header('Content-type: application/json');
	
	try
	{
		$dbAccess = new DBAccess();
		
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 引数取得
		$userID = $_POST['userID'];

		if($userID != null || $userID != '')
		{
			$errorResult[] = [
				'RESULT' => false,
				'MESSAGE' => 'この機能はログインしないと使用できません。'
			];
		}

		$query = "
			SELECT
				EVENT.EVENT_TEMPLATE_ID
				, EVENT.EVENT_NAME
				, PLACE.PLACE_NAME
				, EVENT.EVENT_TIME_FROM
				, EVENT.EVENT_TIME_TO
				, EVENT.COMMENT
				, EVENT.USER_ID
			FROM
				D_EVENT_TEMPLATE EVENT
			LEFT JOIN
				M_PLACE PLACE
			ON
				EVENT.PLACE_ID = PLACE.PLACE_ID
			WHERE
				EVENT.USER_ID = :userID";

		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$eventData = [];
		$returnData = [];
		// データの行数分詰める
		// 存在しない場合は0行で返る
		foreach ($rows as $row)
		{
			WriteLog('log', sprintf('【GetEventTemplate】%s', $row['EVENT_NAME']));
			$eventData[] = [
				'EVENT_ID' => $row['EVENT_ID'],
				'PLACE_NAME' => $row['PLACE_NAME'],
				'EVENT_NAME' => $row['EVENT_NAME'],
				'EVENT_TIME_FROM' => substr($row['EVENT_TIME_FROM'], 0, 5),
				'EVENT_TIME_TO' => substr($row['EVENT_TIME_TO'], 0, 5),
				'COMMENT' => $row['COMMENT']
			];
		}

		$returnData = [
			'RESULT' => true,
			'DATA' => $eventData
		];

		WriteLog('log', '【GetEventTemplate】success');
		
		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		$result[] = [
			'RESULT' => false,
			'MESSAGE' => 'イベントテンプレートの取得に失敗しました。'
		];
		
		echo json_encode($result);
	}
?>


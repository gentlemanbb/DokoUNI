<?php

	require_once "DBAccess.php";
	require_once "LogUtility.php";
	
	header('Content-Type: application/json; charset=utf-8');
	
	try
	{
		WriteLog('log', '【GetEvent】 Start');
		
		$dbAccess = new DBAccess();
		
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 引数取得
		$areaID = $_GET['areaID'];
		$addDaysFrom = $_GET['addDaysFrom'];
		$addDaysTo = $_GET['addDaysTo'];
		$userID = $_GET['userID'];
		
		if($areaID != null || $areaID != '')
		{
			$areaSearchQuery = sprintf('', $areaID);
		}
		
		if($userID != null || $userID != '')
		{
			$userID = 'NO_LOGIN_USER';
		}
		
		$query = "
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
			FROM D_EVENT EVENT
				LEFT JOIN M_PLACE PLACE
				ON EVENT.PLACE_ID = PLACE.PLACE_ID
			WHERE
				(EVENT_DATE BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL :addDaysFrom DAY)
				AND DATE_ADD(CURRENT_DATE(), INTERVAL :addDaysTo1 DAY)
				OR
				(EVENT_DATE > DATE_ADD(CURRENT_DATE(), INTERVAL :addDaysTo2 DAY) AND SPECIAL_EVENT = 1))
				AND (PLACE.AREA_ID = :areaID OR SPECIAL_EVENT = 1)
			ORDER BY
				EVENT_DATE ASC, EVENT_TIME_FROM ASC";
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':addDaysFrom'	, $addDaysFrom	, PDO::PARAM_INT);
		$stmt->bindParam(':addDaysTo1'	, $addDaysTo	, PDO::PARAM_INT);
		$stmt->bindParam(':addDaysTo2'	, $addDaysTo	, PDO::PARAM_INT);
		$stmt->bindParam(':areaID'		, $areaID		, PDO::PARAM_INT);
		
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		$eventData = [];
		$returnData;
		if(count($rows) > 0)
		{
			// データの行数分詰める
			// 存在しない場合は0行で返る
			foreach ($rows as $row)
			{
				WriteLog("log", $row['EVENT_NAME']);
				$eventData[] = [
					'EVENT_ID' => $row['EVENT_ID'],
					'PLACE_NAME' => $row['PLACE_NAME'],
					'EVENT_NAME' => $row['EVENT_NAME'],
					'EVENT_DATE' => substr($row['EVENT_DATE'], 5, 5),
					'EVENT_TIME_FROM' => substr($row['EVENT_TIME_FROM'], 0, 5),
					'EVENT_TIME_TO' => substr($row['EVENT_TIME_TO'], 0, 5),
					'COMMENT' => $row['COMMENT'],
					'TOROKU_USER_ID' => $row['TOROKU_USER_ID'],
					'SPECIAL_EVENT' => $row['SPECIAL_EVENT'],
					'SPECIAL_URL' => $row['SPECIAL_URL']
				];
				
			}
		}
		else
		{
			WriteLog("log", "【GetEvent】0 length.");
		}
		
		WriteLog('log', '【GetEvent】 End');
		$returnData = [
			'RESULT' => true,
			'DATA' => $eventData
		];
		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		
		// WriteLog('log', '【GetEvent】 error');
		
		// WriteErrorLog($ex);
		
		// $result = [
		// 	'RESULT' => false,
		// 	'MESSAGE' => $ex->getMessage(),
		// ];
		
		// echo json_encode(false);
	}
?>
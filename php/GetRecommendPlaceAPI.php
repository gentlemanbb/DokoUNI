<?php
	require "DBAccess.php";
	header('Content-type: application/json');
	
	$dbAccess = new DBAccess();
	$dbAccess->DBConnect();
	$pdo = $dbAccess->DBConnect2();
	
	// 引数を取得
	$areaID = $_POST['areaID'];
	$addDays = $_POST['addDays'];
	
	// 週データを作成
	$week = date("w", strtotime($addDays . ' day')) - 1;
	
	$query = sprintf("
		SELECT
			COUNT(1) AS POPULARITY_POINT
			, WEEKDAY(JOIN_DATE_FROM) AS WEEK_NUM
			, BASE.PLACE_ID
			, SUB.PLACE_NAME
			, SUB2.AREA_NAME
		FROM
			D_POPULARITY BASE
		LEFT JOIN
			M_PLACE SUB
		ON
			BASE.PLACE_ID = SUB.PLACE_ID
		LEFT JOIN
			M_AREA SUB2
		ON
			SUB.AREA_ID = SUB2.AREA_ID
		WHERE
			JOIN_DATE_FROM BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY) AND CURRENT_DATE()
		AND
			SUB.AREA_ID = :areaID
		AND
			WEEKDAY(JOIN_DATE_FROM) = :week
			
		GROUP BY
			PLACE_ID
			
		ORDER BY
			POPULARITY_POINT DESC LIMIT 1");
			
		//
		$stmt = $pdo->prepare($query);
		
		$stmt->bindParam(':areaID',		$areaID,	PDO::PARAM_INT);
		$stmt->bindParam(':week',		$week,		PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		$popularityData = [];
		
		foreach ($rows as $row)
		{
			$popularityData[] = [
				'POPULARITY_POINT' => $row['POPULARITY_POINT'],
				'WEEK_NUM' => $row['WEEK_NUM'],
				'PLACE_NAME' => $row['PLACE_NAME'],
				'AREA_NAME' => $row['AREA_NAME']
			];
		}
		
		if($popularityData != null && is_array($popularityData) && count($popularityData) != 0)
		{
			$result[] = [
				'RESULT' => True,
				'DATA' => $popularityData
			];
		}
		else
		{
			$result = False;
		}
		
		echo json_encode($result);

?>
<?php
	// ƒ‰ƒCƒuƒ‰ƒŠ‚Ì“Ç‚Ýž‚Ý
	require "DBAccess.php";
	
	// DBÚ‘±
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	// ˆø”‚ÌŽæ“¾
	$areaID = $_POST['areaID'];
	$recentTime = $_POST['recentTime'] * -1;
	
	$query = sprintf("
		SELECT
			ifnull(SUB.PLAY_COUNT_BASE, 0) AS PLAY_COUNT
			, BASE.PLACE_NAME
			, BASE.PLACE_ID
		FROM
			M_PLACE BASE
			
		LEFT JOIN
			(SELECT
				COUNT(1) AS PLAY_COUNT_BASE
				, PLACE_ID
			FROM D_PLAYING
			
			WHERE TWEET_DATETIME >= DATE_ADD(NOW(), INTERVAL :recentTime HOUR)
			GROUP BY PLACE_ID) AS SUB 
		ON
			BASE.PLACE_ID = SUB.PLACE_ID
		WHERE
			BASE.AREA_ID = :areaID
		GROUP BY
			PLACE_ID
		ORDER BY
			PLAY_COUNT DESC, PLACE_NAME ASC
		");
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':recentTime'	, $recentTime	, PDO::PARAM_INT);
		$stmt->bindParam(':areaID'		, $areaID		, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		$popuralityData = [];
		$hasFoundData = False;
		
		if(is_array($rows) && count($rows) != 0)
		{
			$hasFoundData = True;
			
			foreach ($rows as $row){
				$popuralityData[] = 
				[
					'PLAY_COUNT' => $row['PLAY_COUNT'],
					'PLACE_NAME' => $row['PLACE_NAME'],
					'PLACE_ID' => $row['PLACE_ID']
				];
			}
		}
		
		header('Content-type: application/json');
		
		if($hasFoundData)
		{
			echo json_encode($popuralityData);
		}
		else
		{
			echo json_encode($popuralityData);
		}
?>
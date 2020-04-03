<?php
	require "DBAccess.php";

	$dbAccess = new DBAccess();
	
	// DBÚ‘±
	$pdo = $dbAccess->DBConnect2();
	
	// ˆø”Žæ“¾
	$areaID = $_POST['areaID'];
	
	$query = '
		SELECT
		 PLACE.PLACE_ID
		 , PLACE.PLACE_NAME
		 , COUNT(1) AS RANK_SEQ_NO
		FROM
			M_PLACE PLACE
		LEFT JOIN
			D_POPULARITY POP
		ON
			PLACE.PLACE_ID = POP.PLACE_ID
		WHERE
			AREA_ID = :areaID
		GROUP BY
			PLACE.PLACE_ID
		ORDER BY
			RANK_SEQ_NO DESC';

	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':areaID', $areaID, PDO::PARAM_INT);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	
	$placeData = [];

	foreach ($rows as $row){
		$placeData[] = 
		[
			'PLACE_ID' => $row['PLACE_ID'],
			'PLACE_NAME' => $row['PLACE_NAME']
		];
	}

	header('Content-type: application/json');
	
	echo json_encode($placeData);
?>
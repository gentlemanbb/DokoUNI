<?php
	require "DBAccess.php";
	
	// ˆø”‚ðŽæ“¾
	
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();	
	
	$query = sprintf("
		SELECT
			PLACE_NAME
			, OFFICIAL_NAME
			, BASE.PLAYER_NAME
			, CHARA.CHARACTER_NAME
			, WINNING_STREAK
			, REGISTERED_DATETIME
		FROM
			D_WINNING_STREAK BASE
		LEFT JOIN
			D_CHARA CHARA
		ON
			BASE.CHARACTER_ID = CHARA.CHARACTER_ID
		LEFT JOIN
			M_PLACE PLACE
		ON
			BASE.PLACE_ID = PLACE.PLACE_ID
		WHERE
			REGISTERED_DATETIME BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL -10 DAY) AND CURRENT_DATE()
		ORDER BY
			BASE.REGISTERED_DATETIME DESC
			, BASE.PLACE_ID ASC
			, BASE.WINNING_STREAK DESC
	");
	
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':placeID'		, $placeID		, PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	
	$winningData = [];
	
	foreach ($rows as $row){
		$winningData[] = 
		[
			'PLACE_NAME' => $row['PLACE_NAME'],
			'PLAYER_NAME' => $row['PLAYER_NAME'],
			'OFFICIAL_NAME' => $row['OFFICIAL_NAME'],
			'CHARACTER_NAME' => $row['CHARACTER_NAME'],
			'WINNING_STREAK' => $row['WINNING_STREAK'],
			'REGISTERED_DATETIME' => $row['REGISTERED_DATETIME']
		];
	}
	
	header('Content-type: application/json');
	echo json_encode($winningData);
?>
<?php
	// ƒ‰ƒCƒuƒ‰ƒŠ‚Ì“Ç‚Ýž‚Ý
	require 'DBAccess.php';
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
		
	$query = sprintf("
		SELECT
			FT_USER_ID
			, NUMBER
			, PLAYER1_NAME
			, PLAYER2_NAME
			, PLAYER1_COUNT
			, PLAYER2_COUNT
			, PASSWORD
		FROM
			D_FT_COUNT");
			
	$stmt = $pdo->prepare($query);
	$result = $stmt->execute();
	$rows = $stmt->fetchAll();
		
	$data = [];

	foreach ($rows as $row){
		$data[] = 
		[
			'FT_USER_ID' => $row['FT_USER_ID'],
			'NUMBER' => $row['NUMBER'],
			'PLAYER1_NAME' => $row['PLAYER1_NAME'],
			'PLAYER2_NAME' => $row['PLAYER2_NAME'],
			'PLAYER1_COUNT' => $row['PLAYER1_COUNT'],
			'PLAYER2_COUNT' => $row['PLAYER2_COUNT'],
			'PASSWORD' => $row['PASSWORD']
		];
	}
	
	header('Content-type: application/json');
	echo json_encode($data);
?>
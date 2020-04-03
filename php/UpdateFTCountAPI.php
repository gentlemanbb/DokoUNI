<?php
	// ƒ‰ƒCƒuƒ‰ƒŠ‚Ì“Ç‚Ýž‚Ý
	require 'DBAccess.php';
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	// ˆø”Žæ“¾
	$ftUserID = $_POST['ftUserID'];
	$number = $_POST['number'];
	$player1Count = $_POST['player1Count'];
	$player2Count = $_POST['player2Count'];
	$player1Name = $_POST['player1Name'];
	$player2Name = $_POST['player2Name'];
	$password = $_POST['password'];
	
	$query = sprintf("
		UPDATE D_FT_COUNT SET
			PLAYER1_COUNT = :player1Count
			, PLAYER2_COUNT = :player2Count
			, PLAYER1_NAME = :player1Name
			, PLAYER2_NAME = :player2Name
		WHERE
			FT_USER_ID = :ftUserID
		AND
			NUMBER = :number
		AND
			PASSWORD = :password");
			
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':player1Count'	, $player1Count	, PDO::PARAM_INT);
	$stmt->bindParam(':player2Count'	, $player2Count	, PDO::PARAM_INT);
	$stmt->bindParam(':player1Name'		, $player1Name	, PDO::PARAM_STR);
	$stmt->bindParam(':player2Name'		, $player2Name	, PDO::PARAM_STR);
	$stmt->bindParam(':ftUserID'		, $ftUserID		, PDO::PARAM_STR);
	$stmt->bindParam(':number'			, $number		, PDO::PARAM_INT);
	$stmt->bindParam(':password'		, $password		, PDO::PARAM_STR);
	$result = $stmt->execute();
		
	$rtnValue = false;
	
	if($result){
		$rtnValue = true;
	}
	
	header('Content-type: application/json');
	echo json_encode($rtnValue);
?>
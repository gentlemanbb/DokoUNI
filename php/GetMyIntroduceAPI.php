<?php
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	// 引数
	$introUserID = $_POST['introUserID'];
	$userID = $_POST['userID'];
	
	$query = '
		SELECT
			COMMENT
		FROM
			D_INTRODUCE
		WHERE
			INTRODUCED_USER_ID = :introUserID
		AND
			WRITE_USER_ID = :userID';
	
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':introUserID'	, $introUserID	, PDO::PARAM_STR);
	$stmt->bindParam(':userID'		, $userID		, PDO::PARAM_STR);
	$result = $stmt->execute();
	$dataRows = $stmt->fetchAll();
	
	foreach ($dataRows as $dataRow)
	{
		$result = [
			'RESULT' => true,
			'COMMENT' => $dataRow['COMMENT']
		];
	}

	header('Content-type: application/json');
	echo json_encode($result);
?>
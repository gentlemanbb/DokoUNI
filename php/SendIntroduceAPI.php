<?php
	require "DBAccess.php";
	require "TweetAPI.php";

	// 引数
	$introUserID = $_POST['introUserID'];
	$userID = $_POST['userID'];
	$introComment = $_POST['introComment'];
	
	// インジェクション対策
	$introComment = preg_replace("/<script.*<\/script>/", "", $introComment);
	
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	// すでに登録されているか確認する
	$query = '
		SELECT
			1
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
	
	if(is_array($dataRows) && count($dataRows) > 0)
	{
		// データがあった場合
		foreach($dataRows as $dataRow)
		{
			$query = '
				UPDATE
					D_INTRODUCE
				SET
					COMMENT = :introComment
				WHERE
					INTRODUCED_USER_ID = :introUserID
				AND
					WRITE_USER_ID = :userID';
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':introComment'	, $introComment	, PDO::PARAM_STR);
			$stmt->bindParam(':introUserID'		, $introUserID	, PDO::PARAM_STR);
			$stmt->bindParam(':userID'			, $userID		, PDO::PARAM_STR);
			$result = $stmt->execute();
		}
	}
	else
	{
		// データがなかった場合
		$query = '
			INSERT INTO
				D_INTRODUCE(INTRODUCED_USER_ID, WRITE_USER_ID, COMMENT)
			VALUES
				(:introUserID, :userID, :comment)';
				
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':comment'	, $introComment			, PDO::PARAM_STR);
		$stmt->bindParam(':introUserID'		, $introUserID			, PDO::PARAM_STR);
		$stmt->bindParam(':userID'			, $userID				, PDO::PARAM_STR);

		$result = $stmt->execute();
	}
	
	header('Content-type: application/json');
	echo json_encode($result);
?>
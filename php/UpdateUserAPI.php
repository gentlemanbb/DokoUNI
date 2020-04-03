<?php
	require "DBAccess.php";

	// 引数
	$userID = $_POST['userID'];
	$userName = $_POST['playerName'];
	$mainCharacterID = $_POST['mainCharacterID'];
	$RIP = $_POST['RIP'];
	$notification = $_POST['notification'];
	$areaID = $_POST['areaID'];
	$agreeDisplayName = $_POST['agreeDisplayName'];
	$comment = $_POST['comment'];
	
	// インジェクション対策
	$comment = preg_replace("/<script.*<\/script>/", "", $comment);
	
	if($RIP == '')
	{
		$RIP = 'null';
	}
	
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	// ユーザーIDが存在しているかチェックする
	$query = '
		SELECT
			1 AS EXIST_RESULT
		FROM
			D_USER
		WHERE
			USER_ID = :userID';
			
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':userID'		, $userID		, PDO::PARAM_STR);
	$result = $stmt->execute();
	$rows = $stmt->fetchAll();
	
	header('Content-type: application/json');
	
	if(is_array($rows) && count($rows) > 0)
	{
		$query = '
			UPDATE
				D_USER
			SET
				USER_NAME = :userName
				, MAIN_CHARACTER_ID = :mainCharacterID
				, RIP = :RIP
				, NOTIFICATION = :notification
				, AREA_ID = :areaID
				, AGREE_DISPLAY_NAME = :agreeDisplayName
				, COMMENT = :comment
			WHERE
				USER_ID = :userID';
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':userName', 			$userName, 			PDO::PARAM_STR);
		$stmt->bindParam(':mainCharacterID', 	$mainCharacterID, 	PDO::PARAM_INT);
		$stmt->bindParam(':RIP', 				$RIP, 				PDO::PARAM_INT);
		$stmt->bindParam(':notification', 		$notification, 		PDO::PARAM_INT);
		$stmt->bindParam(':areaID', 			$areaID, 			PDO::PARAM_INT);
		$stmt->bindParam(':agreeDisplayName', 	$agreeDisplayName, 	PDO::PARAM_STR);
		$stmt->bindParam(':comment', 			$comment, 			PDO::PARAM_STR);
		$stmt->bindParam(':userID', 			$userID, 			PDO::PARAM_STR);
		$result = $stmt->execute();
		$rows = $stmt->fetchAll();
		
		if($result == true)
		{
			$rtnValue = [
				'RESULT' => true,
				'USER_NAME' => $userName,
				'MAIN_CHARACTER_ID' => $mainCharacterID,
				'RIP' => $RIP,
				'AREA_ID' => $areaID
			];
			
			echo json_encode($rtnValue);
			
			return;
		}
		else
		{
			$rtnValue = [
				'RESULT' => false,
				'MESSAGE' => '更新に失敗しました。'
			];
			
			echo json_encode($rtnValue);
			
			return;
		}
	}
	else
	{
		$rtnValue = [
			'RESULT' => false,
			'MESSAGE' => '更新対象のユーザは存在しませんでした。'
		];
		
		echo json_encode($rtnValue);
		return;
	}
?>
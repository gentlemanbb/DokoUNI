<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "LogUtility.php";	
		
	try
	{
		header('Content-type: application/json');

		// 引数を取得
		$groupName = $_POST['groupName'];
		$userID = $_POST['userID'];
		
		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		
		// インサート
		$query = "
			INSERT INTO
				D_GROUP (
					GROUP_NAME
					, TOROKU_USER_ID
					, KOSHIN_DATETIME
					, PUBLIC_GROUP
				)
			VALUES(
				:groupName
				, :userID
				, CURRENT_TIMESTAMP()
				, 0
			)";
		
		$stmt = $pdo->prepare($query);
		$pdo->beginTransaction();
		$stmt->bindParam(':groupName', 		$groupName, 	PDO::PARAM_STR);
		$stmt->bindParam(':userID', 		$userID, 		PDO::PARAM_STR);		
		$result = $stmt->execute();
		$groupID = $pdo->lastInsertId('id');
		
		if($result == True)
		{
			// インサート
			$query = "
				INSERT INTO
					D_GROUP_USER (
						GROUP_ID
						, USER_ID
						, STATUS
						, MANAGE_TYPE
						, TOROKU_DATE
						, UPDATE_DATE
					)
				VALUES(
					:groupID
					, :userID
					, 1
					, 1
					, CURRENT_TIMESTAMP()
					, CURRENT_TIMESTAMP()
				)";
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':groupID', 	$groupID, 	PDO::PARAM_INT);
			$stmt->bindParam(':userID', 	$userID, 	PDO::PARAM_STR);		
			$result = $stmt->execute();
			$insertID = $pdo->lastInsertId('id');

			// コミット
			$pdo->commit();
			
			// 返し値
			$returnValue = 
			[
				'RESULT' => true,
				'INSERT_ID' => $insertID,
				'MESSAGE' => '登録に成功しました。'
			];
			
			echo json_encode($returnValue);
		}
		else
		{
			// 返し値
			$returnValue = 
			[
				'RESULT' => false,
				'MESSAGE' => '登録に失敗しました。'
			];
			
			echo json_encode($returnValue);
		}
	}
	catch(Exception $ex)
	{
		// ロールバック
		$stmt->rollBack();

		$result = [
			'RESULT' => false,
			'MESSAGE' => $ex.Message
		];

		echo json_encode($result);
	}
?>
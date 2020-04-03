<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "LogUtility.php";	
	require "GetDataUtility.php";	
		
	try
	{
		header('Content-type: application/json');

		// 引数を取得
		$groupID = $_POST['groupID'];
		$userID = $_POST['userID'];
		
		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		$groupUserID = GetGroupUserID($groupID, $userID);
		$groupUserData = GetGroupUserDetailData($groupUserID);

		if($groupUserData != null)
		{
			$manageType = $groupUserData['MANAGE_TYPE'];

			if($manageType != 1)
			{
				// 返し値
				$returnValue = 
				[
					'RESULT' => false,
					'MESSAGE' => '管理者以外は削除できません。'
				];
				
				echo json_encode($returnValue);
			}
		}
		else
		{
			// 返し値
			$returnValue = 
			[
				'RESULT' => false,
				'MESSAGE' => 'データが存在しませんでした。'
			];
				
			echo json_encode($returnValue);
		}

		// インサート
		$query = "
			DELETE FROM
				D_GROUP
			WHERE
				GROUP_ID = :groupID";
		
		$stmt = $pdo->prepare($query);
		$pdo->beginTransaction();
		$stmt->bindParam(':groupID', 		$groupID, 	PDO::PARAM_INT);	
		$result = $stmt->execute();
		
		if($result == True)
		{
			// インサート
			$query = "
			DELETE FROM
				D_GROUP_USER
			WHERE
				GROUP_ID = :groupID";
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':groupID', 	$groupID, 	PDO::PARAM_INT);		
			$result = $stmt->execute();

			// コミット
			$pdo->commit();
			
			// 返し値
			$returnValue = 
			[
				'RESULT' => true,
				'MESSAGE' => '削除に成功しました。'
			];
			
			echo json_encode($returnValue);
		}
		else
		{
			// 返し値
			$returnValue = 
			[
				'RESULT' => false,
				'MESSAGE' => '削除に失敗しました。'
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
<?php
	// ===================
	//  場所を更新します。
	// ===================
	require "DBAccess.php";
	require "GetDataUtility.php";
	require "LogUtility.php";

	header('Content-type: application/json');

	try
	{
		// 引数
		$placeID = $_POST['placeID'];
		$placeName = $_POST['placeName'];
		$address = $_POST['address'];
		$comment = $_POST['comment'];
		$userID = $_POST['userID'];
		
		// インジェクション対策
		$placeName = preg_replace("/<script.*<\/script>/", "", $placeName);
		$address = preg_replace("/<script.*<\/script>/", "", $address);
		$comment = preg_replace("/<script.*<\/script>/", "", $comment);
		
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		$placeData = GetPlaceDetailData($placeID);
		$returnData = null;

		if($placeData == null)
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'データが存在しませんでした。',
				'PLACE_DATA' => null
			];

			echo json_encode($returnData);

			return;
		}

		$newRevision = $placeData['REVISION'] + 1;
		$imagePath = $placeData['IMAGE_PATH'];

		$query = sprintf("
			INSERT INTO
				D_PLACE_HISTORY
				(
					PLACE_ID,
					PLACE_NAME,
					ADDRESS,
					COMMENT,
					IMAGE_PATH,
					REVISION,
					KOSHIN_USER_ID,
					KOSHIN_DATETIME
				)
				
				VALUES
				(
					:placeID,
					:placeName,
					:address,
					:comment,
					:imagePath,
					:revision,
					:koshinUserID,
					CURRENT_TIMESTAMP()
				)
			");

		$stmt = $pdo->prepare($query);

    	// トランザクション
    	$pdo->beginTransaction();
		$stmt->bindParam(':placeID', 		$placeID, 		PDO::PARAM_INT);
		$stmt->bindParam(':placeName', 		$placeName, 	PDO::PARAM_STR);
		$stmt->bindParam(':address', 		$address, 		PDO::PARAM_STR);
		$stmt->bindParam(':comment', 		$comment, 		PDO::PARAM_STR);
		$stmt->bindParam(':imagePath', 		$imagePath, 	PDO::PARAM_STR);
		$stmt->bindParam(':revision', 		$newRevision, 	PDO::PARAM_INT);
		$stmt->bindParam(':koshinUserID', 	$userID, 		PDO::PARAM_STR);
		$result = $stmt->execute();

		if(!$result)
		{
			WriteLog('UpdatePlaceAPI2', '履歴の登録に失敗しました。');

			// ロールバック
			$pdo->rollback();

			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '履歴の登録に失敗しました。'
			];

			echo json_encode($returnData);
		}

		// INSERTしたIDを取得
		$lastInsertedID = $pdo->lastInsertId('id');

		$query = sprintf("
			UPDATE
				M_PLACE
			SET
				PLACE_HISTORY_ID = :placeHistoryID
			WHERE
				PLACE_ID = :placeID
			");

		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':placeHistoryID', 	$lastInsertedID, 	PDO::PARAM_INT);
		$stmt->bindParam(':placeID', 			$placeID, 			PDO::PARAM_INT);
		$result = $stmt->execute();

		if($result)
		{
			$pdo->commit();
		}
		else
		{
			WriteLog('UpdatePlaceAPI2', 'ゲームセンターの更新に失敗しました。');

			// ロールバック
			$pdo->rollback();

			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'ゲームセンターの更新に失敗しました。'
			];

			echo json_encode($returnData);
			return;
		}

		$returnData = [
			'RESULT' => true,
			'MESSAGE' => '更新に成功しました。'
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);

		if ($pdo->inTransaction())
		{ 
			// ロールバック
			$pdo->rollback();
		}

		$returnData = [
			'RESULT' => false,
			'MESSAGE' => '更新に失敗しました。'
		];

		echo json_encode($returnData);
	}
?>
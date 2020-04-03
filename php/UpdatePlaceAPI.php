<?php
	// ===================
	//  場所を更新します。
	// ===================
	require "DBAccess.php";
	require "GetDataUtility.php";
	require "LogUtility.php";

	// 引数
	$placeID = $_POST['placeID'];
	$placeName = $_POST['placeName'];
	$areaID = $_POST['areaID'];
	
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();

	$placeData = GetPlaceDetailData($placeID);
	$returnData = null;

	if($placeData['FIX_FLG'] == null)
	{
		WriteLog('UpdatePlaceAPI',
			printf('%s %s %s'
				, $placeData['PLACE_ID']
				, $placeData['PLACE_NAME']
				, $placeData['OFFICIAL_NAME']
				, $placeData['FIX_FLG']));
				
		$returnData = [
			'RESULT' => false,
			'MESSAGE' => '元データが取得できませんでした。'
		];

		echo json_encode($returnData);
		return;
	}
	else if($placeData['FIX_FLG'] == 1)
	{
		$returnData = [
			'RESULT' => false,
			'MESSAGE' => 'そのデータはすでに更新済みです。'
		];

		echo json_encode($returnData);
		return;
	}

	$query = sprintf("
		UPDATE
			M_PLACE
		SET
			AREA_ID = :areaID
			, PLACE_NAME = :placeName
			, FIX_FLG = TRUE
		WHERE
			PLACE_ID = :placeID");

	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':areaID', 	$areaID, 	PDO::PARAM_STR);
	$stmt->bindParam(':placeName', 	$placeName, PDO::PARAM_STR);
	$stmt->bindParam(':placeID', 	$placeID, 	PDO::PARAM_STR);
	$result = $stmt->execute();

	header('Content-type: application/json');
	
	if($result == True)
	{
		$returnData = [
			'RESULT' => true,
			'MESSAGE' => '更新に成功しました。'
		];
		echo json_encode($returnData);
	}
	else
	{
		$returnData = [
			'RESULT' => false,
			'MESSAGE' => '更新に失敗しました。'
		];
		echo json_encode($returnData);
	}

	return;
?>
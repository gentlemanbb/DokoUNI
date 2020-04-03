<?php

	try
	{
		// ライブラリ読み込み
		require "DBAccess.php";
		
		// 引数取得
		$placeID = $_POST['placeID'];
		
		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		header('Content-type: application/json');

		$query = sprintf("
			SELECT
				PLACE_NAME
				, OFFICIAL_NAME
			FROM
				M_PLACE
			WHERE
				PLACE_ID = :placeID
			");
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':placeID', $placeID, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		$returnData = [];
		$placeData = null;

		if(is_array($rows) && count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$placeData = 
				[
					'PLACE_ID' => $placeID,
					'PLACE_NAME' => $row['PLACE_NAME'],
					'OFFICIAL_NAME' => $row['OFFICIAL_NAME']
				];
			}
			
			$returnData = 
			[
				'RESULT' => true,
				'PLACE_DATA' => $placeData
			];
		}
		else 
		{
			$returnData = 
			[
				'RESULT' => false,
				'MESSAGE' => 'データが見つかりませんでした。'
			];
		}

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		$result =
		[
			'RESULT' => false,
			'MESSAGE' => $ex.message
		];
		
		header('Content-type: application/json');
		echo json_encode($result);
	}
?>
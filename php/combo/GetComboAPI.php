<?php
	// ライブラリの読み込み
	require '../DBAccess.php';
		
	$dbAccess = new DBAccess();
    
	// DB接続
	$pdo = $dbAccess->DBConnect2();
	
	$characterID = 1;
	
	// データベース内に同一の名前の場所が存在するか確認する
	$query = sprintf("
		SELECT
			PLACE_ID
			, PLACE_NAME
		FROM
			M_PLACE
		WHERE
			OFFICIAL_NAME = :placeName");
	
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':placeName', $placeName, PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	
	$officialName = $placeName;
	$dataHasFound = false;
	
	// 存在するか確認
	if(is_array($rows))
	{
		foreach($rows as $row)
		{
			// 見つけた場合フラグを立てる
			// 表示名称をDBのものを使用する
			$dataHasFound = true;
			$displayName = $row['PLACE_NAME'];
			$placeID = $row['PLACE_ID'];
		}
	}
	
	// データをがあった場合
	// データがなかった場合 で分岐
	if($dataHasFound == false)
	{
		// データが見つからなかった場合
		// DBに新しいデータとして登録する
		try
		{
			// INSERT文の生成
			$query = sprintf("
				INSERT INTO M_PLACE (
					PLACE_NAME
					, AREA_ID
					, SEQ_NO
					, OFFICIAL_NAME)
				VALUES (
					:displayName
					, 99
					, 99
					, :officialName
				)");
			
			// 地方を未分類とする
			$areaID = '99';
			
			// 並び順も最下層
			$seqNo = 99;
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':displayName'		, $displayName	, PDO::PARAM_STR);
			$stmt->bindParam(':officialName'	, $officialName	, PDO::PARAM_STR);
			
			// インサート
			$stmt->execute();
			
			$fileStr = $fileStr . $placeName . ' をインサートしました。' . "\n";
			$fileStr = $fileStr . $query . "\n";
			
			// インサートしたデータを取得します。
			$query = sprintf("
				SELECT
					PLACE_ID
					, PLACE_NAME
				FROM
					M_PLACE
				WHERE
					OFFICIAL_NAME = :placeName");
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':placeName', $placeName, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchAll();
			
			$officialName = $placeName;
			$dataHasFound = false;
			
			// 存在するか確認
			if(is_array($rows))
			{
				foreach($rows as $row)
				{
					// 見つけた場合フラグを立てる
					// 表示名称をDBのものを使用する
					$dataHasFound = true;
					$displayName = $row['PLACE_NAME'];
					$placeID = $row['PLACE_ID'];
				}
			}
		}
		catch(Exception $ex)
		{
			$fileStr = $fileStr . $placeName . ' のインサートに失敗しました。' . "\n";
		}
	}
	
	header('Content-type: application/json');
	
	echo json_encode($placeArray);
?>
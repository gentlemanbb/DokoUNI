<?php
	try
	{
		// ライブラリの読み込み
		require "DBAccess.php";
		require "TweetAPI.php";
		require "LogUtility.php";
		require "GetDataUtility.php";
		
		// 返し値の型を設定
		header('Content-type: application/json');
		
		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		
		$query = '
			SELECT
				BASE.PLACE_HISTORY_ID
				, BASE.PLACE_ID
				, BASE.REVISION
				, BASE.PLACE_NAME
			FROM
				D_PLACE_HISTORY BASE
			INNER JOIN
				(SELECT
					PLACE_ID
					, MAX(REVISION) AS MAX_REVISION
				FROM
					D_PLACE_HISTORY
				GROUP BY
					PLACE_ID
				) SUB
			ON
				BASE.PLACE_ID = SUB.PLACE_ID
			AND
				BASE.REVISION = SUB.MAX_REVISION
			ORDER BY
				BASE.PLACE_ID ASC';

		$stmt = $pdo->prepare($query);
		$sqlResult = $stmt->execute();
		$rows = $stmt->fetchAll();

		if(is_array($rows) && count($rows) > 0)
		{
			// トランザクション開始
			$pdo->beginTransaction();

			foreach($rows as $row)
			{
				$placeHistoryID = $row['PLACE_HISTORY_ID'];
				$placeID = $row['PLACE_ID'];
		
				$query = '
					UPDATE
						M_PLACE
					SET
						PLACE_HISTORY_ID = :placeHistoryID
					WHERE
						PLACE_ID = :placeID';
	
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':placeHistoryID',	$placeHistoryID,	PDO::PARAM_INT);
				$stmt->bindParam(':placeID',		$placeID,			PDO::PARAM_INT);
				$sqlResult = $stmt->execute();

				if(!$sqlResult)
				{
					$pdo->rollback();

					$result = [
						'RESULT' => false,
						'MESSAGE' => 'Update Failed.'
					];

					echo json_encode($result);
					return;
				}
			}

			$pdo->commit();
		}

		return;
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);

		$pdo->rollback();
		
		$result = [
			'RESULT' => false,
			'MESSAGE' => $ex.Message
		];
		
		echo json_encode($result);
		
		return;
	}
?>
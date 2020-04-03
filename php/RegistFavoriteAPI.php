<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	require "GetDataUtility.php";
	require "Constants.php";

	header('Content-type: application/json');

	try
	{
		// 引数を取得
		$userID = $_POST['userID'];
		$keyID = $_POST['keyID'];
		$favoriteType = $_POST['favoriteType'];
		
		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		$favoriteID = null;
		$returnData = null;

		$favoriteID = GetFavoriteID($userID, $favoriteType, $keyID);

		if($favoriteID == null)
		{
			// グループの取得
            $query = '
                INSERT INTO
					D_FAVORITE_PLACE
					(
						USER_ID,
						PLACE_ID,
						TOROKU_DATETIME
					)

					VALUES (
						:userID,
						:keyID,
						CURRENT_TIMESTAMP()
					)';

			$stmt = $pdo->prepare($query);
			$pdo->beginTransaction();
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
			$stmt->bindParam(':keyID', $keyID, PDO::PARAM_INT);
			$sqlResult = $stmt->execute();

			if($sqlResult)
			{
				// 成功したらコミット
				$pdo->commit();

				$returnData = [
					'RESULT' => true,
					'MESSAGE' => 'お気に入り登録に成功しました。'
				];
			}
			else
			{
				$returnData = [
					'RESULT' => false,
					'MESSAGE' => 'お気に入り登録に失敗しました。'
				];	
			}
		}
		else
		{
			$returnData = [
				'RESULT' => true,
				'MESSAGE' => 'お気に入りに登録済みです。'
			];			
		}
		
		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		// エラーログを出力
		WriteErrorLog($ex);

		if($pdo->inTransaction())
		{
			// トランザクションが開始していればロールバック
			$pdo->rollBack();
		}

		$result = [
			'RESULT' => false,
			'MESSAGE' => $ex.getMessage()
		];
		
		echo json_encode($result);
	}
?>
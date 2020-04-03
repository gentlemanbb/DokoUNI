<?php
	try
	{
		// ライブラリ読み込み
		require "DBAccess.php";
		require "LogUtility.php";
		
		// 引数取得
		$userID = $_POST['userID'];

		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		
		header('Content-type: application/json');

		$query = '
			SELECT
				USER_NAME
				, MAIN_CHARACTER_ID
				, RIP
				, TWITTER
				, NOTIFICATION
				, AREA_ID
				, AUTH.AUTHORITY_NAME
				, USR.AGREE_DISPLAY_NAME
				, USR.ICON_IMAGE_PATH
				, USR.COMMENT
				, (SELECT COUNT(1) FROM D_POPULARITY POP WHERE POP.USER_ID = :userID1 AND POP.JOIN_DATE_FROM <= NOW()) AS SEND_COUNT
				, IFNULL(
					(SELECT
						(SELECT
							COUNT(1)+1
						FROM (
							SELECT
								COUNT(1) AS SCORE
							FROM
								D_POPULARITY
							WHERE
								JOIN_DATE_FROM <= NOW()
							GROUP BY
								USER_ID) AS RANK_TABLE2
							WHERE
								RANK_TABLE2.SCORE > RANK_TABLE1.SCORE) AS RANK
						FROM (
							SELECT
								POP.USER_ID
								, COUNT(1) AS SCORE
							FROM
								D_POPULARITY POP
							WHERE
								USER_ID = :userID2
							AND
								POP.JOIN_DATE_FROM <= NOW()
							GROUP BY
								USER_ID) AS RANK_TABLE1
					), (SELECT COUNT(1) FROM (SELECT 1 FROM D_POPULARITY GROUP BY USER_ID) AS SENT_USER_NUM)) AS RANK
					
			FROM
				D_USER USR
			LEFT JOIN
				M_AUTHORITY AUTH
			ON
				USR.AUTHORITY_ID = AUTH.AUTHORITY_ID
			WHERE
				USR.USER_ID = :userID3';
				
		// $rows = $dbAccess->Select($query);
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':userID1', $userID, PDO::PARAM_STR);
		$stmt->bindParam(':userID2', $userID, PDO::PARAM_STR);
		$stmt->bindParam(':userID3', $userID, PDO::PARAM_STR);
		$stmt->execute();
		$userDataRows = $stmt->fetchAll();

		if(!is_array($userDataRows) || count($userDataRows) == 0)
		{
			$returnData = [
				'RESULT' => false,
				'USER_DATA' => null,
				'GROUP_DATA' => null,
				'MESSAGE' => 'データが存在しませんでした。'
			];
	
			echo json_encode($returnData);

			return;
		}

		// 返し値用インスタンス
		$returnData = [];
		$userData = [];
		$groupData = [];

		if(is_array($userDataRows) && count($userDataRows) > 0)
		{
			foreach ($userDataRows as $row)
			{
				$userData = 
				[
					'USER_NAME' => $row['USER_NAME'],
					'MAIN_CHARACTER_ID' => $row['MAIN_CHARACTER_ID'],
					'RIP' => $row['RIP'],
					'TWITTER' => $row['TWITTER'],
					'NOTIFICATION' => $row['NOTIFICATION'],
					'AREA_ID' => $row['AREA_ID'],
					'AUTHORITY_NAME' => $row['AUTHORITY_NAME'],
					'SEND_COUNT' => $row['SEND_COUNT'],
					'RANK' => $row['RANK'],
					'AGREE_DISPLAY_NAME' => $row['AGREE_DISPLAY_NAME'],
					'ICON_IMAGE_PATH' => $row['ICON_IMAGE_PATH'],
					'COMMENT' => $row['COMMENT']
				];
			}
		}

		// グループの取得
		$query = sprintf('
			SELECT
				BASE.GROUP_ID
				, GROUP_NAME
				, STATUS
			FROM
				D_GROUP BASE
			LEFT JOIN
				D_GROUP_USER SUB
			ON
				BASE.GROUP_ID = SUB.GROUP_ID
			WHERE
				SUB.USER_ID = :userID');
			
		// $rows = $dbAccess->Select($query);
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
		$stmt->execute();
		$groupDataRows = $stmt->fetchAll();

		if(is_array($groupDataRows) && count($groupDataRows) > 0)
		{
			foreach ($groupDataRows as $row)
			{
				$groupData[] = 
				[
					'GROUP_ID' => $row['GROUP_ID'],
					'GROUP_NAME' => $row['GROUP_NAME'],
					'STATUS' => $row['STATUS']
				];
			}
		}

		$returnData = [
			'RESULT' => true,
			'USER_DATA' => $userData,
			'GROUP_DATA' => $groupData
		];
		
		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);

		$returnData = [
			'RESULT' => false,
			'USER_DATA' => null,
			'GROUP_DATA' => null
		];

		echo json_encode($returnData);
	}
?>
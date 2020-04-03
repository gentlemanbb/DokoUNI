<?php
	function GetGroup($userID)
	{
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
			$pdo = $dbAccess->DBConnect2();
			
			// 返し値用インスタンス
			$groupData = [];

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

			return $groupData;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
		}
	}
?>
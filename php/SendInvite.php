<?php
	try
	{
		// ライブラリの読み込み
		require_once 'DBAccess.php';
		require_once 'TweetAPI.php';
		require_once 'GetDataUtility.php';
		require_once 'LogUtility.php';

		header('Content-type: application/json');
		
		// 引数
		$sendUserID = $_POST['sendUserID'];
		$sendUserName = $_POST['sendUserName'];
		$placeID = $_POST['placeID'];
		$groups = $_POST['groups'];

		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		if(!is_array($groups) || count($groups) == 0)
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'グループが選択されていません。'
			];

			echo json_encode($returnData);
		}

		$placeData = GetPlaceDetailData($placeID);

		if($placeData != null)
		{
			$placeName = $placeData['PLACE_DATA']['PLACE_NAME'];
		}
		else
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '指定したゲームセンターは存在しませんでした。'
			];

			echo json_encode($returnData);
			return;
		}
		foreach($groups as $group)
		{
			// 承認済みのユーザーのみ
			$status = 1;

			// データを取得します。
			$query = sprintf("
				SELECT
					BASE.GROUP_NAME
					, SUB2.USER_NAME
					, SUB2.USER_ID
					, SUB2.TWITTER
				FROM
					D_GROUP BASE
				LEFT JOIN
					D_GROUP_USER SUB1
				ON
					BASE.GROUP_ID = SUB1.GROUP_ID
				LEFT JOIN
					D_USER SUB2
				ON
					SUB1.USER_ID = SUB2.USER_ID
				WHERE
					BASE.GROUP_ID = :groupID
				AND
					SUB1.STATUS = :status");
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':groupID', 	$group, 	PDO::PARAM_INT);
			$stmt->bindParam(':status', 	$status, 	PDO::PARAM_INT);
			$stmt->execute();
			$dataRows = $stmt->fetchAll();

			foreach($dataRows as $dataRow)
			{
				$receiveUserID = null;
				$groupName = null;
				$userName = null;
				$receiveUserID = null;
				$twitterID = null;

				$receiveUserID = $dataRow['USER_ID'];

				if($sendUserID == $receiveUserID)				
				{
					// 送信者と受信者が同じの場合は送信しない。
					continue;
				}

				if($groupName == null)
				{
					$groupName = $dataRow['GROUP_NAME'];
				}

				if($dataRow['USER_NAME'] == null || $dataRow['USER_NAME'] == '')
				{
					$userName = $dataRow['USER_ID'];
				}
				else
				{
					$userName = $dataRow['USER_NAME'];
				}

				$twitterID = $dataRow['TWITTER'];

				$message = sprintf(
					'%s さん -> %s' . "\n"
					. '%s さん %s で UNI しませんか？' . "\n"
					. 'https://zawa-net.com/dokouni/receive_invite.php?userID=%s&placeID=%s&groupID=%s'
					, $sendUserName, $groupName, $userName, $placeName, $receiveUserID, $placeID, $group);

				WriteLog('SendInvite', sprintf('%s宛：%s',$twitterID, $message));

				$DMResult = SendDM($message, $twitterID);
			}
		}

		$returnData = [
			'RESULT' => true,
			'MESSAGE' => '正常に送信できました。'
		];

		echo json_encode($returnData);
	}
	catch(Exception $e)
	{
		$returnData = [
			'RESULT' => false,
			'MESSAGE' => $e->getMessage()
		];

		echo json_encode($returnData);
	}

?>
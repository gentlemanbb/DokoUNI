<?php
	try {
		// ライブラリの読み込み
		require_once 'DBAccess.php';
		require_once 'TweetAPI.php';
		require_once 'GetDataUtility.php';

		header('Content-type: application/json');

		$dbAccess = new DBAccess();
		
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 引数
		$sendUserName = $_POST['sendUserName'];
		$placeID = $_POST['placeID'];
		$groups = $_POST['groups'];
		$receiveType = $_POST['receiveType'];
		$comment = $_POST['comment'];

		if(!is_array($groups) || count($groups) == 0)
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'グループが選択されていません。'
			];

			echo json_encode($returnData);
		}

		$caption = GetTypeData('RECEIVE_TYPE', $receiveType);
		WriteLog('ReceiveInvite', $caption);

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
					BASE.GROUP_ID = :groupID");
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':groupID', $group, PDO::PARAM_INT);
			$stmt->execute();
			$dataRows = $stmt->fetchAll();

			foreach($dataRows as $dataRow)
			{
				$groupName = null;
				$userName = null;
				$twitterID = null;

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

				$receiveUserID = $dataRow['USER_ID'];
				$twitterID = $dataRow['TWITTER'];

				$message = sprintf(
					'%s さん -> %s' . "\n"
					. '%s ' . "\n"
					. '%s %s'
					, $sendUserName, $groupName, $placeName, $caption, $comment);
			
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
		header('Content-type: application/json');
		
		echo json_encode(false);
	}

?>
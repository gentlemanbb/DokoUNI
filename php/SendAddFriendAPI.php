<?php
	try
	{
		header('Content-type: application/json');

		// ライブラリの読み込み
		require_once 'DBAccess.php';
		require_once 'TweetAPI.php';
		require_once 'GetDataUtility.php';
		require_once 'LogUtility.php';
		
		// 引数
		$sendUserID = $_POST['sendUserID'];
		$receiveUserID = $_POST['receiveUserID'];

		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		$sendUserData = GetUserDetailData($sendUserID);
		$receiveUserData = GetUserDetailData($receiveUserID);

		$sendUserName = $sendUserData['USER_NAME'];
		$receiveUserName = $receiveUserData['USER_NAME'];

		$twitterID = $receiveUserData['TWITTER'];

		WriteLog('SendAddFriendAPI', '処理開始');

		if($sendUserData == null)
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '送信ユーザーが存在しませんでした。'
			];

			echo json_encode($returnData);
			return;
		}

		if($receiveUserData == null)
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '受信ユーザーが存在しませんでした。'
			];

			echo json_encode($returnData);
			return;
		}

		$sendUserData = GetUserDetailData($sendUserID);

		// データを取得します。
		$query = sprintf("
			SELECT
				FRIEND_ID
				, TOROKU_USER_ID
				, FRIEND_USER_ID
				, STATUS
			FROM
				D_FRIEND 
			WHERE
				(TOROKU_USER_ID = :sendUserID1 AND FRIEND_USER_ID = :receiveUserID1)
			OR
				(TOROKU_USER_ID = :receiveUserID2 AND FRIEND_USER_ID = :sendUserID2)");
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':sendUserID1', 	$sendUserID, 	PDO::PARAM_STR);
		$stmt->bindParam(':receiveUserID1', $receiveUserID, PDO::PARAM_STR);
		$stmt->bindParam(':receiveUserID2', $receiveUserID, PDO::PARAM_STR);
		$stmt->bindParam(':sendUserID2', 	$sendUserID, 	PDO::PARAM_STR);
		$stmt->execute();
		$dataRows = $stmt->fetchAll();

		if(is_array($dataRows) && count($dataRows) > 0)
		{
			$status = null;

			foreach($dataRows as $dataRow)
			{
				$status = $dataRow['STATUS'];

				if($status == 1)
				{
					$returnData = [
						'RESULT' => true,
						'MESSAGE' => '既にフレンド登録済みです。'
					];
	
					echo json_encode($returnData);
					return;
				}				
			}

			// 更新の必要がある場合
			foreach($dataRows as $dataRow)
			{
				$updateStatus = 0;
				$updateStatusText = '未承認';
				$friendID = null;
				$friendID = $dataRow['friendID'];

				$query = sprintf("
					UPDATE
						D_FRIEND
					SET
						STATUS = :updateStatus
						, KOSHIN_DATETIME = CURRENT_TIMESTAMP()
					WHERE
						(TOROKU_USER_ID = :sendUserID1 AND FRIEND_USER_ID = :receiveUserID1)
					OR
						(TOROKU_USER_ID = :receiveUserID2 AND FRIEND_USER_ID = :sendUserID2)");
				
				$stmt = $pdo->prepare($query);
				$pdo->beginTransaction();
				$stmt->bindParam(':updateStatus', 	$updateStatus, 		PDO::PARAM_INT);
				$stmt->bindParam(':sendUserID1', 	$sendUserID, 		PDO::PARAM_STR);
				$stmt->bindParam(':receiveUserID1', $receiveUserID, 	PDO::PARAM_STR);
				$stmt->bindParam(':receiveUserID2', $receiveUserID, 	PDO::PARAM_STR);
				$stmt->bindParam(':sendUserID2', 	$sendUserID, 		PDO::PARAM_STR);
				$sqlResult = $stmt->execute();

				WriteLog('SendAddFriendAPI', $query);
		
				if($sqlResult == true)
				{
					$message = sprintf(
						'%s さん -> %s' . "\n"
						. 'フレンドのリクエストが来ています。' . "\n"
						. 'https://zawa-net.com/dokouni/receive_friend_invite.php?sendUserID=%s&receiveUserID=%s'
						, $sendUserName, $receiveUserName, $sendUserID, $receiveUserID);

					WriteLog('SendAddFriendAPI', $message);
					$DMResult = SendDM($message, $twitterID);
					$pdo->commit();
				}
				else
				{
					$stmt->rollBack();
					WriteLog('SendAddFriendAPI', 
						sprintf('アップデートに失敗しました。[sendUserID:%s], [receiveUserID:%s], [status:%s]'
						, $sendUserID
						, $receiveUserID
						, $updateStatusText));
						$returnData = [
						'RESULT' => false,
						'MESSAGE' => '送信に失敗しました。'
					];
					
					echo json_encode($returnData);
					return;
				}
			}
		}
		else
		{
			WriteLog('SendAddFriendAPI', 'INSERTルート');
			$insertStatus = 0;

			$query = sprintf("
				INSERT INTO
					D_FRIEND (
						TOROKU_USER_ID
						, FRIEND_USER_ID
						, STATUS
						, TOROKU_DATETIME
						, KOSHIN_DATETIME)
					VALUES (
						:sendUserID
						, :receiveUserID
						, :insertStatus
						, CURRENT_TIMESTAMP()
						, CURRENT_TIMESTAMP()
					)");
			
			$stmt = $pdo->prepare($query);
			$pdo->beginTransaction();
			$stmt->bindParam(':sendUserID', 	$sendUserID, 	PDO::PARAM_STR);
			$stmt->bindParam(':receiveUserID', 	$receiveUserID, PDO::PARAM_STR);
			$stmt->bindParam(':insertStatus', 	$insertStatus, 	PDO::PARAM_INT);
			$sqlResult = $stmt->execute();

			WriteLog('SendAddFriendAPI', 'INSERT終了');

			if($sqlResult == true)
			{
				$message = sprintf(
					'%s さん -> %s' . "\n"
					. 'フレンドのリクエストが来ています。' . "\n"
					. 'https://zawa-net.com/dokouni/receive_friend_invite.php?sendUserID=%s&receiveUserID=%s'
					, $sendUserName, $receiveUserName, $sendUserID, $receiveUserID);
				
				WriteLog('SendAddFriendAPI', $message);

				$DMResult = SendDM($message, $twitterID);
				$pdo->commit();
			}
			else
			{
				$stmt->rollBack();
				WriteLog('SendAddFriendAPI', 
					sprintf('インサートに失敗しました。[sendUserID:%s], [receiveUserID:%s], [status:%s]'
					, $sendUserID
					, $receiveUserID
					, $updateStatusText));

				$returnData = [
					'RESULT' => false,
					'MESSAGE' => '送信に失敗しました。'
				];
				
				echo json_encode($returnData);
				return;
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
		WriteErrorLog($e);

		$returnData = [
			'RESULT' => false,
			'MESSAGE' => $e->getMessage()
		];

		echo json_encode($returnData);
	}
?>
<?php
	try
	{
		// ライブラリの読み込み
		require_once 'DBAccess.php';
		require_once 'TweetAPI.php';
		require_once 'GetDataUtility.php';

		header('Content-type: application/json');
		
		// 引数
		$sendUserID = $_POST['sendUserID'];
		$sendUserName = $_POST['sendUserName'];
		$groupID = $_POST['groupID'];
		$friends = $_POST['friends'];

		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		if(!is_array($friends) || count($friends) == 0)
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'グループが選択されていません。'
			];

			echo json_encode($returnData);
		}

		$group = GetGroupDetailData($groupID);
		$groupName = null;

		if($group != null)
		{
			$groupName = $group['GROUP_NAME'];
		}
		else
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '指定したグループは存在しませんでした。'
			];

			echo json_encode($returnData);
			return;
		}

		foreach($friends as $friend)
		{
			// データを取得します。
			$query = sprintf("
				SELECT
					USER_NAME
					, TWITTER
				FROM
					D_USER
				WHERE
					USER_ID = :friend");
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':friend', $friend, PDO::PARAM_STR);
			$stmt->execute();
			$dataRows = $stmt->fetchAll();

			foreach($dataRows as $dataRow)
			{
				// 一般ユーザー
				$manageType = 1;
				// 未承認
				$status = 0;

				// データを取得します。
				$prevDataID = GetGroupUserID($groupID, $friend);
				WriteLog('SendGroupInvite', sprintf('[groupID:%s], [userID:%s] に該当するデータは [prevDataID:%s]', $groupID, $friend, $prevDataID));

				if($prevDataID != null)
				{
					WriteLog('SendGroupInvite', sprintf('更新対象 [groupUserID:%s]', $prevDataID));

					$query = sprintf("
						UPDATE
							D_GROUP_USER
						SET
							STATUS = :status
							, UPDATE_DATE = CURRENT_TIMESTAMP()
						WHERE
							GROUP_USER_ID = :prevDataID");
					
					$stmt = $pdo->prepare($query);
					$pdo->beginTransaction();
					$stmt->bindParam(':status', $status, PDO::PARAM_INT);
					$stmt->bindParam(':prevDataID', $prevDataID, PDO::PARAM_INT);
					$sqlResult = $stmt->execute();

					if($sqlResult == true)
					{
						$friendName = null;
						$twitterID = null;

						if($dataRow['USER_NAME'] == null || $dataRow['USER_NAME'] == '')
						{
							$friendName = $friend;
						}
						else
						{
							$friendName = $dataRow['USER_NAME'];
						}

						$receiveUserID = $dataRow['USER_ID'];
						$twitterID = $dataRow['TWITTER'];

						$message = sprintf(
							'%s さん -> %s' . "\n"
							. 'グループ 「%s」 に招待されています。' . "\n"
							. 'https://zawa-net.com/dokouni/test3.php?sendUserID=%s&receiveUserID=%s&groupID=%s'
							, $sendUserName, $friendName, $groupName, $sendUserID, $friend, $groupID);
					
						$DMResult = SendDM($message, $twitterID);
						$pdo->commit();
					}
					else
					{
						$stmt->rollBack();
						WriteLog('SendGroupInvite', sprintf('アップデートに失敗しました。[groupID:%s], [userID:%s], [status:%s]', $groupID, $friend, $status));
					}
				}
				else
				{
					$query = sprintf("
						INSERT INTO
							D_GROUP_USER (
								GROUP_ID
								, USER_ID
								, STATUS
								, MANAGE_TYPE
								, TOROKU_DATE
								, UPDATE_DATE)
							VALUES (
								:groupID
								, :userID
								, :status
								, :manageType
								, CURRENT_TIMESTAMP()
								, CURRENT_TIMESTAMP()
							)");
					
					$stmt = $pdo->prepare($query);
					$pdo->beginTransaction();
					$stmt->bindParam(':groupID', $groupID, PDO::PARAM_STR);
					$stmt->bindParam(':userID', $friend, PDO::PARAM_STR);
					$stmt->bindParam(':status', $status, PDO::PARAM_STR);
					$stmt->bindParam(':manageType', $manageType, PDO::PARAM_STR);
					$sqlResult = $stmt->execute();

					if($sqlResult == true)
					{
						$friendName = null;
						$twitterID = null;

						if($dataRow['USER_NAME'] == null || $dataRow['USER_NAME'] == '')
						{
							$friendName = $friend;
						}
						else
						{
							$friendName = $dataRow['USER_NAME'];
						}

						$receiveUserID = $dataRow['USER_ID'];
						$twitterID = $dataRow['TWITTER'];

						$message = sprintf(
							'%s さん -> %s' . "\n"
							. 'グループ 「%s」 に招待されています。' . "\n"
							. 'https://zawa-net.com/dokouni/receive_group_invite.php?sendUserID=%s&receiveUserID=%s&groupID=%s'
							, $sendUserName, $friendName, $groupName, $sendUserID, $friend, $groupID);
					
						$DMResult = SendDM($message, $twitterID);
						$pdo->commit();
					}
					else
					{
						$stmt->rollBack();
						WriteLog('SendGroupInvite', sprintf('インサートに失敗しました。[groupID:%s], [userID:%s], [status:%s]', $groupID, $friend, $status));
					}
				}
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
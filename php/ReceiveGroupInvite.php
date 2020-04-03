<?php
	try {
		// ライブラリの読み込み
		require_once 'DBAccess.php';
		require_once 'TweetAPI.php';
		require_once 'GetDataUtility.php';
		require_once 'LogUtility.php';

		header('Content-type: application/json');

		$dbAccess = new DBAccess();
		
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 引数
		$receiveUserID = $_POST['receiveUserID'];
		$inviteUserID = $_POST['inviteUserID'];
		$receiveType = $_POST['receiveType'];
		$groupID = $_POST['groupID'];

		if($groupID == null || $groupID == '')
		{
			WriteLog('ReceiveGroupInvite'
				, sprintf('[receiveUserID:%s], [receiveType:%s], [groupID:%s] グループの値が不正です。'
					, $receiveUserID
					, $receiveType
					, $groupID));

			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'グループの値が不正です。'
			];

			echo json_encode($returnData);
		}

		// テキストに変換
		$agreeCaption = GetTypeData('AGREE_TYPE', $receiveType);

		$groupData = GetGroupDetailData($groupID);
		$groupName = null;

		if($groupData != null)
		{
			$groupName = $groupData['GROUP_NAME'];
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
		
		$status = null;

		if($receiveType == 0)
		{
			// 承認の場合
			$status = 1;
		}
		else
		{
			// 承認以外の場合
			$status = 2;
		}

		// GetGroupUserID($groupID, $receiveUserID);
		$query = sprintf("
			UPDATE
				D_GROUP_USER
			SET
				STATUS = :status
				, UPDATE_DATE = CURRENT_TIMESTAMP()
			WHERE
				GROUP_ID = :groupID
			AND
				USER_ID = :receiveUserID");

		// トランザクション
		$stmt = $pdo->prepare($query);
		// $pdo->beginTransaction();
		$stmt->bindParam(':status', 		$status, 		PDO::PARAM_INT);
		$stmt->bindParam(':groupID', 		$groupID, 		PDO::PARAM_INT);
		$stmt->bindParam(':receiveUserID', 	$receiveUserID, PDO::PARAM_STR);
		$sqlResult = $stmt->execute();
		
		if($sqlResult == false)
		{
			WriteLog('ReceiveGroupInvite', sprintf('%s, [status:%s], [groupID:%s], [receiveUserID:%s]', $query, $status, $groupID, $receiveUserID));

			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '更新に失敗しました。'
			];

			return;
		}

		$inviteUserData = GetUserDetailData($inviteUserID);
		$receiveUserData = GetUserDetailData($receiveUserID);
		$groupData = GetGroupDetailData($groupID);

		if($inviteUserData != null)
		{
			$inviteUserName = $inviteUserData['USER_NAME'];
			$inviteUserTwitterID = $inviteUserData['TWITTER'];
			$groupName = $groupData['GROUP_NAME'];
			$receiveUserName = $receiveUserData['USER_NAME'];

			$message = sprintf(
				'%s さん が %s への招待を %s しました。'
				, $receiveUserName, $groupName, $agreeCaption);
		
			$DMResult = SendDM($message, $inviteUserTwitterID);
		}

		if($DMResult == true)
		{
			$returnData = [
				'RESULT' => true,
				'MESSAGE' => '正常に送信できました。'
			];

			// 全部成功ならコミット
			// $pdo->commit();
		}
		else
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '送信に失敗しました。'
			];

			// 失敗するならロールバック
			// $stmt->rollBack();
		}
		
		echo json_encode($returnData);
	}
	catch(Exception $e)
	{
		header('Content-type: application/json');
		
		echo json_encode(false);
	}

?>
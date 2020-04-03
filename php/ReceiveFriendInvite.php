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

		// テキストに変換
		$agreeCaption = GetTypeData('AGREE_TYPE', $receiveType);
		
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
				D_FRIEND
			SET
				STATUS = :status
				, KOSHIN_DATETIME = CURRENT_TIMESTAMP()
			WHERE
				(TOROKU_USER_ID = :inviteUserID1 AND FRIEND_USER_ID = :receiveUserID1)
			OR
				(TOROKU_USER_ID = :receiveUserID2 AND FRIEND_USER_ID = :inviteUserID2)
			");

		// トランザクション
		$stmt = $pdo->prepare($query);
		$pdo->beginTransaction();
		$stmt->bindParam(':status', 		$status, 		PDO::PARAM_INT);
		$stmt->bindParam(':inviteUserID1', 	$inviteUserID, 	PDO::PARAM_STR);
		$stmt->bindParam(':receiveUserID1', $receiveUserID, PDO::PARAM_STR);
		$stmt->bindParam(':receiveUserID2', $receiveUserID, PDO::PARAM_STR);
		$stmt->bindParam(':inviteUserID2', 	$inviteUserID, 	PDO::PARAM_STR);
		$sqlResult = $stmt->execute();
		
		if($sqlResult == false)
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '更新に失敗しました。'
			];

			echo json_encode($returnData);
			return;
		}

		$inviteUserData = GetUserDetailData($inviteUserID);
		$receiveUserData = GetUserDetailData($receiveUserID);

		if($inviteUserData != null)
		{
			$inviteUserName = $inviteUserData['USER_NAME'];
			$inviteUserTwitterID = $inviteUserData['TWITTER'];
			$receiveUserName = $receiveUserData['USER_NAME'];

			$message = sprintf(
				'%s さん が %s のフレンド申請を %s しました。'
				, $receiveUserName, $inviteUserName, $agreeCaption);
		
			$DMResult = SendDM($message, $inviteUserTwitterID);
		}

		if($DMResult == true)
		{
			$returnData = [
				'RESULT' => true,
				'MESSAGE' => '正常に送信できました。'
			];

			// 全部成功ならコミット
			$pdo->commit();
		}
		else
		{
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => '送信に失敗しました。'
			];

			// 失敗するならロールバック
			$stmt->rollBack();
		}
		
		echo json_encode($returnData);
	}
	catch(Exception $e)
	{
		header('Content-type: application/json');
		
		echo json_encode(false);
	}

?>
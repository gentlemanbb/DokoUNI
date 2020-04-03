<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";

		// 引数取得
		$inviteUserID = $_POST['inviteUserID'];
		$receiveUserID = $_POST['receiveUserID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$friendData = [];
		$friendData = GetFriendDetailData($inviteUserID, $receiveUserID);

		if($friendData['RESULT'])
		{
			$returnData = [
				'RESULT' => true,
				'FRIEND_DATA' => $friendData['FRIEND_DATA']
			];
		}
		else
		{
			$returnData = [
				'RESULT' => true,
				'FRIEND_DATA' => null
			];
		}

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);
		echo json_encode(false);
	}
?>
<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";

		// 引数取得
		$groupID = $_POST['groupID'];
		$userID = $_POST['userID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$groupUserData = [];
		$groupUserID = GetGroupUserID($groupID, $userID);
		$groupUserData = GetGroupUserDetailData($groupUserID);
		$returnData = [
			'RESULT' => true,
			'GROUP_USER_DATA' => $groupUserData
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);
		echo json_encode(false);
	}
?>
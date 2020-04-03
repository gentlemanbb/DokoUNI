<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";

		// 引数取得
		$userID = $_POST['userID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$friendData = [];
		$friendData = GetAllFriendData($userID);
		$returnData = [
			'RESULT' => true,
			'FRIEND_DATA' => $friendData['FRIEND_DATA']
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);
		echo json_encode(false);
	}
?>
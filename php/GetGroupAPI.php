<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetGroup.php";

		// 引数取得
		$userID = $_POST['userID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$groupData = [];
		$groupData = GetGroup($userID);
		$returnData = [
			'RESULT' => true,
			'GROUP_DATA' => $groupData
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);
		echo json_encode(false);
	}
?>
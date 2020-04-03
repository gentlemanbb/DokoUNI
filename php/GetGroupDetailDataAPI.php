<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";

		// 引数取得
		$groupID = $_POST['groupID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$groupData = [];
		$groupData = GetGroupDetailData($groupID);
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
<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";
		require_once "LogUtility.php";

		// 引数取得
		$tagType = $_POST['tagType'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$tagData = [];
		$tagData = GetTagsData($tagType);

		$returnData = [
			'RESULT' => true,
			'TAG_DATA' => $tagData
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
        WriteErrorLog($ex);

        $returnData = [
            'RESULT' => false,
            'TAG_DATA' => null
        ];

		echo json_encode($returnData);
	}
?>
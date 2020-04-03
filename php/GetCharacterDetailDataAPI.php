<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";

		// 引数取得
		$characterID = $_POST['characterID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$characterDetailData = [];
		$characterDetailData = GetCharacterDetailData($characterID);

		$returnData = [
			'RESULT' => true,
			'CHARACTER_DATA' => $characterDetailData
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
        WriteErrorLog($ex);

        $returnData = [
            'RESULT' => false,
			'CHARACTER_DATA' => null,
			'MESSAGE' => $ex->getMessage()
        ];

		echo json_encode($returnData);
	}
?>
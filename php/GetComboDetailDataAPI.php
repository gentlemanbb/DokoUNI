<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";
		require_once "LogUtility.php";

		// 引数取得
		$comboID = $_POST['comboID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$comboData = [];
		$comboData = GetComboDetailData($comboID);

		$returnData = [
			'RESULT' => true,
			'COMBO_DATA' => $comboData['COMBO_DATA'],
			'TAG_DATA' => $comboData['TAG_DATA']
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
        WriteErrorLog($ex);

        $returnData = [
            'RESULT' => false,
            'COMBO_DATA' => null
        ];

		echo json_encode($returnData);
	}
?>
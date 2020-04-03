<?php
	try
	{
		// ライブラリの読み込み
		require_once 'DBAccess.php';
		require_once 'TweetAPI.php';
		require_once 'RegistDataUtility.php';
		require_once 'GetDataUtility.php';
		require_once 'DeleteDataUtility.php';
		require_once 'LogUtility.php';

		header('Content-type: application/json');
		
		// 引数
		$targetID = $_POST['targetID'];
		$evalutionType = $_POST['evalutionType'];
		$value = $_POST['value'];
		$userID = $_POST['userID'];
		$comment = $_POST['comment'];

		$evalutionData = GetEvalutionID($evalutionType, $targetID, $userID);

		if($evalutionData != null)
		{
			if($evalutionData['VALUE'] == $value)
			{
				DeleteEvalutionData($evalutionData['EVALUTION_ID']);

				$returnData = [
					'RESULT' => true,
					'MESSAGE' => '評価を取り消しました。'
				];
				
				echo json_encode($returnData);
				return;
			}
			else
			{
				DeleteEvalutionData($evalutionData['EVALUTION_ID']);
			}
		}

		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		$insertID = RegistEvalutionData($targetID, $evalutionType, $value, $userID, $comment);

		$returnData = [
			'RESULT' => false,
			'MESSAGE' => '評価に失敗しました。'
		];

		if($insertID != null)
		{
			$returnData = [
				'RESULT' => true,
				'MESSAGE' => '評価しました。'
			];
		}

		echo json_encode($returnData);
		return;
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);

		$returnData = [
			'RESULT' => false,
			'MESSAGE' => '評価に失敗しました。'
		];

		echo json_encode($returnData);
		return;
	}

?>
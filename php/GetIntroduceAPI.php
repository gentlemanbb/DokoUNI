<?php
	// ライブラリの読み込み
	require 'DBAccess.php';
	require 'TweetAPI.php';
	require 'LogUtility.php';

	try
	{
		// 引数取得
		$introUserID = $_POST['introUserID'];
		
		$dbAccess = new DBAccess();
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		$query = '
			SELECT
				SUB.USER_ID
				, SUB.ICON_IMAGE_PATH
				, SUB.USER_NAME
				, BASE.COMMENT
				, IFNULL(SUB.ICON_IMAGE_PATH, SUB2.IMAGE_PATH) as IMAGE_PATH
			FROM
				D_INTRODUCE BASE
			LEFT JOIN
				D_USER SUB
			ON
				BASE.WRITE_USER_ID = SUB.USER_ID
			LEFT JOIN
				D_CHARA SUB2
			ON
				SUB.MAIN_CHARACTER_ID = SUB2.CHARACTER_ID
			WHERE
				BASE.INTRODUCED_USER_ID = :introUserID';
		
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':introUserID', $introUserID, PDO::PARAM_STR);
		$stmt->execute();
		$dataRows = $stmt->fetchAll();
		
		$data;
		
		if(is_array($dataRows) && count($dataRows) > 0)
		{
			foreach($dataRows as $dataRow)
			{
				$order = array("\r\n", "\n", "\r");
				
				$comment = str_replace($order, '<br/>', $dataRow['COMMENT']);
				
				$data[] =
				[
					'USER_ID' => $dataRow['USER_ID'],
					'USER_NAME' => $dataRow['USER_NAME'],
					'COMMENT' => $comment,
					'ICON_IMAGE_PATH' => $dataRow['IMAGE_PATH']
				];
			}
			
			$returnData = [
				'USER_ID' => $introUserID,
				'INTRO_DATA' => $data
			];

			$result = [
				'RESULT' => true,
				'MESSAGE' => '正常終了',
				'DATA' => $returnData
			];
		}
		else
		{
			WriteLog('GetPlacePlayersAPI', 'データ無し');

			$returnData = [
				'USER_ID' => $introUserID,
				'INTRO_DATA' => null
			];

			$result = [
				'RESULT' => true,
				'MESSAGE' =>  'データがありません',
				'DATA' => $returnData
			];
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
	}
	catch(Exception $ex)
	{
		// エラーの場合
		$result = [
			'RESULT' => false,
			'USER_ID' => $introUserID,
			'MESSAGE' =>  $ex->getMessage
		];
		
		WriteLog('GetPlacePlayersAPI', $ex->getMessage());
		
		header('Content-type: application/json');
		echo json_encode($result);
	}
?>
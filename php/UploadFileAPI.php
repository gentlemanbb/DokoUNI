<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	require "GetDataUtility.php";
	
	// 今日の日付取得
	$now = date('Y-m-d');
	$registeredDatetime;
	$yesterday = date('Y-m-d', strtotime("-1 day"));
	$eventID = $_POST['eventID'];
	
	// DBアクセサ
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	header('Content-type: application/json');
    	
	try
	{
    	// イベントの更新に成功した場合
		// 一時アップロード先ファイルパス
		$file_tmp  = $_FILES["file_name"]["tmp_name"];
		
		if($file_tmp == null ||  $file_tmp == '')
		{
			$result = [
				'RESULT' => false,
				'MESSAGE' => 'ファイルが添付されていません。' 
			];

			return json_encode($result);
		}

		if($_FILES["file_name"]["size"]===0)
		{
			$result = [
				'RESULT' => false,
				'MESSAGE' => 'ファイルが添付されていません。' 
			];

			return json_encode($result);
		}
		
		// 正式保存先ファイルパス
		$file_save = "../upload/" . $_FILES["file_name"]["name"];
		$returnPath = "upload/" . $_FILES["file_name"]["name"];
		
		$prevEventData = GetEventDetailData($eventID);
		
    	$query = sprintf("
			UPDATE
				D_EVENT
			SET
    			IMAGE_PATH = :imagePath	
			WHERE
				EVENT_ID = :eventID
			");
		
    	$stmt = $pdo->prepare($query);
    	
    	// トランザクション
    	$pdo->beginTransaction();
    	$stmt->bindParam(':imagePath', 		$file_save,		PDO::PARAM_STR);
    	$stmt->bindParam(':eventID', 		$eventID, 		PDO::PARAM_INT);
		$sqlResult = $stmt->execute();
		
		WriteLog('UploadFileAPI', sprintf('[query:%s], [imagePath:%s], [eventID:%s]', $query, $file_save, $eventID));

		$returnData = [];

    	if($sqlResult)
    	{
			// ファイル移動
			$result = @move_uploaded_file($file_tmp, $file_save);
			
			if($result == True)
			{
				WriteLog('UploadFileAPI', sprintf('%s を削除します。', $prevEventData['IMAGE_PATH']));

				if (file_exists($prevEventData['IMAGE_PATH']))
				{
					// ファイルが存在した場合は削除する
					if(unlink($prevEventData['IMAGE_PATH']))
					{
						WriteLog('UploadFileAPI', '過去ファイルの削除に成功。' . "\n");
					}
				}
				else
				{

				}

				WriteLog('UploadFileAPI', 'ファイルの移動に成功しました。');

				$returnData = [
					'RESULT' => true,
					'MESSAGE' => 'イベント画像の更新に成功しました。',
					'IMAGE_PATH' => $returnPath
				];

				// ここまで来たらコミット
				$pdo->commit();
			}
			else
			{
				$pdo->rollback();

				WriteLog('UploadFileAPI', 'ファイルの移動に失敗しました。');

				$returnData = [
					'RESULT' => false,
					'MESSAGE' => 'イベント画像の更新に失敗しました。'
				];
			}
		}
		else
		{
			$pdo->rollback();

			WriteLog('UploadFileAPI', 'イベントの更新に失敗しました。');
							
			$returnData = [
				'RESULT' => false,
				'MESSAGE' => 'イベント画像の更新に失敗しました。'
			];
		}

		echo json_encode($returnData);
    }
    catch(Exception $ex)
    {
		$pdo->rollback();

		WriteErrorLog($ex);
							
		$returnData = [
			'RESULT' => false,
			'MESSAGE' => 'イベント画像の更新に失敗しました。'
		];

    	echo json_encode($returnData);
    }
?>
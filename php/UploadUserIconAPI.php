<?php
	
	// ライブラリの読み込み
	require "DBAccess.php";
	require "TweetAPI.php";
	require "LogUtility.php";
	
	// DBアクセサ
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();
	
	// 引数
	$userID = $_POST['userID'];
	
	// 返し値の型を設定
	header('Content-type: application/json');
	
	try
	{
		// 一時アップロード先ファイルパス
		$file_tmp  = $_FILES["file_name"]["tmp_name"];
		
		// 添付ファイルチェック
		if($file_tmp == null || $file_tmp == '')
		{
			// 添付ファイルがなかった場合は処理を中断
			WriteLog('UploadUserIconAPI', '添付ファイルがありませんでした' . "\n");
			
			$result = [
				'RESULT' => false,
				'MESSAGE' => '添付ファイルがありませんでした。'
			];
			
			echo json_encode($result);
			
			return;
		}
		
		// 拡張子を取得
		$ext = pathinfo($_FILES["file_name"]["name"], PATHINFO_EXTENSION);

		// 小文字に変換する
		$ext = mb_strtolower($ext);

		// 正式保存先ファイルパス
		$file_save = "../img/users/icons/" . $userID . '.' . $ext;
		
		// DBには ".." を抜いたディレクトリで保存する
		$iconImagePath = "img/users/icons/" . $userID . '.' . $ext;
		
		// アイコンアドレスを更新
		$query = '
			UPDATE
				D_USER
			SET
				ICON_IMAGE_PATH = :imagePath
			WHERE
				USER_ID = :userID';
		
		$stmt = $pdo->prepare($query);
		
		// トランザクション
		$pdo->beginTransaction();
		$stmt->bindParam(':imagePath', 		$iconImagePath,		PDO::PARAM_STR);
		$stmt->bindParam(':userID', 		$userID, 		PDO::PARAM_STR);
		$sqlResult = $stmt->execute();
		
		if (file_exists($file_save))
		{
			// ファイルが存在したら、ファイル名を付けて存在していると表示
			WriteLog('UploadUserIconAPI', 'ファイルは既に存在します。' . "\n");
			
			// ファイルが存在した場合は削除する
			if(unlink($file_save))
			{
				WriteLog('UploadUserIconAPI', 'ファイルの削除に成功。' . "\n");
			}
			else
			{
				// 失敗を返す
				WriteLog('UploadUserIconAPI', 'ファイルの削除に失敗。' . "\n");
				
				$result = [
					'RESULT' => false,
					'MESSAGE' => '現在のアイコンの削除に失敗しました。'
				];
				
				echo json_encode($result);
				
				return;
			}
		}
		
		// 一時ファイルを正式パスに移動
		$result = @move_uploaded_file($file_tmp, $file_save);
		
		if($result == True)
		{
			$result = [
				'RESULT' => true,
				'MESSAGE' => 'アップロードに成功しました。',
				'ICON_IMAGE_PATH' => $iconImagePath
			];
			
			echo json_encode($result);
		}
		else
		{
			$result = [
				'RESULT' => false,
				'MESSAGE' => 'アップロードに失敗しました。'
			];
			
			echo json_encode($result);
		}
		
		return;
	}
	catch(Exception $ex)
	{
		$fileStr = $fileStr . $ex.Message;
		
		WriteLog('UploadUserIconAPI', $fileStr);
		
		$result = [
			'RESULT' => false,
			'MESSAGE' => $ex.Message
		];
		
		echo json_encode(result);
		
		return;
	}
?>
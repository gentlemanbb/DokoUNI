<?php
	try
	{
		// ライブラリの読み込み
		require "DBAccess.php";
		require "TweetAPI.php";
		require "LogUtility.php";
		require "GetDataUtility.php";
		
		// 返し値の型を設定
		header('Content-type: application/json');
		
		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
		
		// 引数
		$id = $_POST['placeID'];
		$fileType = $_POST['fileType'];

		// 一時アップロード先ファイルパス
		$file_tmp  = $_FILES["file_name"]["tmp_name"];
		
		// 添付ファイルチェック
		if($file_tmp == null || $file_tmp == '')
		{
			// 添付ファイルがなかった場合は処理を中断
			WriteLog('UploadFileAPI2', '添付ファイルがありませんでした' . "\n");
			
			$result = [
				'RESULT' => false,
				'MESSAGE' => '添付ファイルがありませんでした。'
			];
			
			echo json_encode($result);
			
			return;
		}

		WriteLog('UploadFileAPI2', '添付ファイルチェック');
		
		// 拡張子を取得
		$ext = pathinfo($_FILES["file_name"]["name"], PATHINFO_EXTENSION);

		// 小文字に変換する
		$ext = mb_strtolower($ext);

		// 拡張子を jpeg -> jpg にする
		if($ext == 'jpeg')
		{
			$ext = 'jpg';
		}

		// 正式保存先ファイルパス
		$file_save = null;
		$filePath = null;
		$query = null;

		if($fileType == 'PLACE_IMAGE')
		{
			WriteLog('UploadFileAPI2', 'ファイル種別：PLACE_IMAGE');

			// 保存する先
			$file_save = "../img/places/" . $id . '.' . $ext;

			// DBには ".." を抜いたディレクトリで保存する
			$filePath = "img/places/" . $id . '.' . $ext;

			// データを取得
			$placeHistoryID = GetPlaceHistoryID($id);

			// アイコンアドレスを更新
			$query = '
				UPDATE
					D_PLACE_HISTORY
				SET
					IMAGE_PATH = :filePath
				WHERE
					PLACE_HISTORY_ID = :placeHistoryID';

			WriteLog('UploadFileAPI2', sprintf('[FlePath : %s], [PlaceHistoryID : %s]', $filePath, $placeHistoryID));

			$stmt = $pdo->prepare($query);
		
			// トランザクション
			$pdo->beginTransaction();
			$stmt->bindParam(':filePath', 		$filePath,			PDO::PARAM_STR);
			$stmt->bindParam(':placeHistoryID', $placeHistoryID, 	PDO::PARAM_INT);
			$sqlResult = $stmt->execute();
			$pdo->commit();
		}
		else if($fileType == 'COMBO_MOVIE')
		{
			$filename = $_FILES["file_name"]["name"];

			WriteLog('UploadFileAPI2', 'ファイル種別：COMBO_MOVIE');

			// 保存する先
			$file_save = "../upload/combo/" . $id . '.' . $ext;

			// DBには ".." を抜いたディレクトリで保存する
			$filePath = "upload/combo/" . $id . '.' . $ext;

			// アイコンアドレスを更新
			$query = '
				UPDATE
					D_COMBO
				SET
					MOVIE_PATH = :filePath
				WHERE
					COMBO_ID = :id';

			WriteLog('UploadFileAPI2', sprintf('[FlePath : %s], [ComboID : %s]', $filePath, $id));

			$stmt = $pdo->prepare($query);
		
			// トランザクション
			$pdo->beginTransaction();
			$stmt->bindParam(':filePath', 	$filePath,	PDO::PARAM_STR);
			$stmt->bindParam(':id', 		$id, 		PDO::PARAM_INT);
			$sqlResult = $stmt->execute();
			$pdo->commit();
		}

		if(!$sqlResult)
		{
			$result = [
				'RESULT' => false,
				'MESSAGE' => 'データの更新に失敗しました。'
			];
			
			echo json_encode($result);
			return;
		}

		if (file_exists($file_save))
		{
			// ファイルが存在したら、ファイル名を付けて存在していると表示
			WriteLog('UploadFileAPI2', 'ファイルは既に存在します。' . "\n");
					
			// ファイルが存在した場合は削除する
			if(unlink($file_save))
			{
				WriteLog('UploadFileAPI2', 'ファイルの削除に成功。' . "\n");
			}
			else
			{
				// 失敗を返す
				WriteLog('UploadFileAPI2', 'ファイルの削除に失敗。' . "\n");
						
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
				'MOVIE_PATH' => $filePath
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
		WriteErrorLog($ex);
		
		$result = [
			'RESULT' => false,
			'MESSAGE' => $ex.Message
		];
		
		echo json_encode($result);
		
		return;
	}
?>
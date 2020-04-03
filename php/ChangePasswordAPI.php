<?php
    require "DBAccess.php";
	require "Encrypt.php";
	
    // 引数
    $userID = $_POST['userID'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $newPassword = Encrypt($newPassword);
    
	$dbAccess = new DBAccess();

	// DB接続
	$pdo = $dbAccess->DBConnect2();

    $query = sprintf("
    	SELECT
    		PASSWORD
    	FROM
    		D_USER
    	WHERE
    		USER_ID = :userID
    ");
    
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':userID'	, $userID	, PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	$dbPassword = '';
	
	if(is_array($rows))
	{
    	foreach ($rows as $row)
    	{
    		// DBのパスワードを取得
    		$dbPassword = $row['PASSWORD'];
    	}
    	
    	// パスワードチェック
    	if(password_verify($oldPassword, $dbPassword) == false)
    	{
    		// 違う場合
			$result[] = [
				'RESULT' => false,
				'MESSAGE' => 'パスワードが違います。'
			];
			header('Content-type: application/json');
			echo json_encode($result);
    	}
    	else
    	{
    		try
    		{
    			// パスワードが一致している場合更新をする
				$query = sprintf("
					UPDATE
						D_USER
					SET
						PASSWORD = :newPassword
					WHERE
						USER_ID = :userID
				");
				
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':newPassword'		, $newPassword		, PDO::PARAM_STR);
				$stmt->bindParam(':userID'			, $userID			, PDO::PARAM_STR);
				
				// アップデート
				$stmt->execute();
				
				$result[] = [
					'RESULT' => true,
					'MESSAHE' => 'SUCCESS',
				];
			
				header('Content-type: application/json');
				echo json_encode($result);
			}
			catch(Exception $ex)
			{
				$result[] = [
					'RESULT' => false,
					'MESSAHE' => $ex.Message,
				];
				
				echo json_encode($result);
			}
		}
    }
    else
    {
    	// 取得できなかった場合
		$result[] = [
			'RESULT' => false,
			'MESSAGE' => '更新対象のユーザは存在しませんでした。',
		];
		
		header('Content-type: application/json');
		echo json_encode($result);
	}
?>
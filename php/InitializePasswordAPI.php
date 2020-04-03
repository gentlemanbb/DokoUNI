<?php
	try {
		// ライブラリの読み込み
		require 'DBAccess.php';
		require 'TweetAPI.php';
		require 'Encrypt.php';
		
		$dbAccess = new DBAccess();
		
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 引数
		$userID = $_POST['userID'];
		
		// 新しいパスワード（ユーザＩＤと同じになる）
		$newPassword = Encrypt($userID);
		
		$query = sprintf("
			SELECT 
				USER_ID
				, USER_NAME
				, TWITTER
			FROM
				D_USER
			WHERE
				USER_ID = :userID");
			
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
		$stmt->execute();
		$userRows = $stmt->fetchAll();
		
		$userName = '';
		$dokoUNIID = '';
		$twitterID = '';
		
		if(is_array($userRows)){
			foreach ($userRows as $userRow)
			{
				$userName = $userRow['USER_NAME'];
				$dokoUNIID = $userRow['USER_ID'];
				$twitterID = $userRow['TWITTER'];
			}
		}

		$query = "
			UPDATE 
				D_USER
			SET
				PASSWORD = :newPassword
			WHERE
				USER_ID = :userID
			";
			
		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
		$stmt->execute();
		$message = sprintf(
			'%s さん' . "\n"
			. 'どこうにID：%s' . "\n"
			. 'パスワードをユーザIDと同じに変更しました。' . "\n"
			. 'ログイン完了後、マイページからパスワードの変更を行ってください。', $userName, $dokoUNIID);
		
		$DMResult = SendDM($message, $twitterID);
		
		header('Content-type: application/json');
		
		echo json_encode($DMResult);
	}
	catch(Exception $e)
	{
		header('Content-type: application/json');
		
		echo json_encode(false);
	}

?>
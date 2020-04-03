<?php
	require_once "./DBAccess.php";
	require_once "./Utility/LogUtility.php";
	require_once "./Utility/GetDataUtility.php";
    require_once "./Utility/Encrypt.php";
	require_once "./Resources/Constants.php";
	
    header('Content-type: application/json');
	
    // 引数
    $userID = $_POST['userID'];
    $password = $_POST['password'];
	
	// DBアクセサ
	$dbAccess = new DBAccessor();
	$pdo = $dbAccess->DBConnect();
	
	// ユーザー情報を取得する
	$query = sprintf("
		SELECT
			USR.PASSWORD
			, USR.USER_NAME
			, USR.USER_ID
			, AUTH.SYSTEM_MANAGEMENT
			, AUTH.REGIST_EVENT
			, AUTH.LOGIN
			, USR.AREA_ID
		FROM D_USER USR
			LEFT JOIN M_AUTHORITY AUTH
			ON USR.AUTHORITY_ID = AUTH.AUTHORITY_ID
		WHERE
			USR.USER_ID = '%s'
		", $userID);
		
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':userID'		, $userID		, PDO::PARAM_STR);
	$stmt->bindParam(':joinDate'	, $joinDate		, PDO::PARAM_STR);
	$stmt->execute();
   	$dataRows = $stmt->fetchAll();
	
	$result = [];
	$dbPassword = null;
	
	// 取得結果をチェック
	if(!is_array($dataRows) || count($dataRows) == 0)
	{
		// 取得できなかった場合
		$result = [
			'RESULT' => false,
			'MESSAGE' => 'ユーザーが存在しませんでした。'
		];
		
		// エラー結果を返す
		echo json_encode($result);
		return;
	}
	else
	{
		$row = null;
		foreach ($dataRows as $dataRow)
		{
			$dbPassword = $dataRow['PASSWORD'];
			$row = $dataRow;
		}
		if(password_verify($password, $dbPassword) == false)
		{
			$result = [
				'RESULT' => new ReturnCode(ReturnCode::ERROR),
				'MESSAGE' => 'パスワードが違います。'
			];
		}
		else
		{
			$query = sprintf("
				UPDATE
					D_USER
				SET
					LOGIN_DATETIME = CURRENT_TIMESTAMP()
				WHERE
					USER_ID = :userID");
			$stmt = $pdo->prepare($query);
			$pdo->beginTransaction();
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
			$stmt->execute();
			$pdo->commit();
			
			$result = [
				'RESULT' => new ReturnCode(ReturnCode::SUCCESS),
				'MESSAGE' => 'ログインに成功しました。',
				'USER_NAME' => $row['USER_NAME'],
				'USER_ID' => $row['USER_ID'],
				'SYSTEM_MANAGEMENT' => $row['SYSTEM_MANAGEMENT'],                
				'REGIST_EVENT' => $row['REGIST_EVENT'],                
				'LOGIN' => $row['LOGIN'],
				'AREA_ID' => $row['AREA_ID']
			];
		}
	}
	
	echo json_encode($result);
?>
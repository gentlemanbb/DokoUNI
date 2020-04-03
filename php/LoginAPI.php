<?php
	require_once "DBAccess.php";
	require_once "LogUtility.php";
	require_once "GetDataUtility.php";
    require_once "Encrypt.php";
    header('Content-type: application/json');

    // 引数
    $userID = $_POST['userID'];
    $password = $_POST['password'];

	// DBアクセサ
	$dbAccess = new DBAccess();
	$pdo = $dbAccess->DBConnect2();

    $query = sprintf("
        SELECT
            PASSWORD
            , USER_NAME
            , USER_ID
            , SYSTEM_MANAGEMENT
            , REGIST_EVENT
            , LOGIN
            , AREA_ID
        FROM D_USER USR
        LEFT JOIN M_AUTHORITY AUTH
         ON USR.AUTHORITY_ID = AUTH.AUTHORITY_ID
        WHERE
			USER_ID = :userID");
	
	$stmt = $pdo->prepare($query);
	$stmt->bindParam(':userID'		, $userID		, PDO::PARAM_STR);
	$stmt->execute();
    $dataRows = $stmt->fetchAll();
	
    $result = [];
    $dbPassword = null;
	
	if(!is_array($dataRows) || count($dataRows) == 0)
	{
		// 取得できなかった場合
		$result = [
            'RESULT' => false,
            'MESSAGE' => 'ユーザーが存在しませんでした。'
        ];

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
                'RESULT' => false,
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
                'RESULT' => true,
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
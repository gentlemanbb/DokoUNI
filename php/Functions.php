<?php
    require_once "DBAccess.php";

    function CheckSystemUser($userID){

    	$dbAccess = new DBAccess();
    	$dbAccess->DBConnect();

    	$query = sprintf("
    	    SELECT
    	        USER_ID,
    	        AUTHORITY_NAME,
    	        SYSTEM_MANAGEMENT,
    	        REGIST_EVENT,
    	        LOGIN
    	    FROM D_USER USR

    	    LEFT JOIN M_AUTHORITY AUTH
    	        ON USR.AUTHORITY_ID = AUTH.AUTHORITY_ID

    	    WHERE
    	     USER_ID = '%s'
    	    ", $userID);

    	$rows = $dbAccess->Select($query);

    	$userData = [];

    	if(count($rows) == 0){
    	    header('Content-type: application/json');
    	    echo json_encode(false);
    	}

    	$result = false;

    	foreach ($rows as $row){
    	    if($row['SYSTEM_MANAGEMENT'] == 1){
    	        $result = true;
    	    }
    	}
    	
        return $result;
	}
	
	function CheckLoggedIn($userID)
	{
		// 未入力はその時点で弾く
		if($userID == null || $userID == '')
		{
			return false;
		}


		// DBアクセサ
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();
			
		// 
		$query = "
			SELECT
				1
			FROM
				D_USER
			WHERE
				USER_ID = :userID
			";
			
		$stmt = $pdo->prepare($query);
			
		// トランザクション
		$pdo->beginTransaction();
		$stmt->bindParam(':userID', 	$userID, 	PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll();

		$result = false;

		if(is_array($rows) && count($rows))
		{
			$result = true;
		}

		return $result;
	}
	
	function argvArray($argv)
	{
		$return = array();
	 
		foreach ($argv as $key => $item) {
			if ($key == 0) {
				continue;
			}
	 
			list($param, $value) = explode('=', $item);
			$return[$param] = $value;
		}
	 
		return $return;
	}
?>
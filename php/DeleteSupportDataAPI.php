<?php
    require "DBAccess.php";
    require "Functions.php";

   	header('Content-type: application/json');

    // 引数
    $supportID = $_POST['supportID'];
    $userID = $_POST['userID'];
    $authCheck = CheckSystemUser($userID);

    if($authCheck != true){
         echo json_encode(false);
    }
    else {

    	$dbAccess = new DBAccess();
    	$dbAccess->DBConnect();

    	$query = sprintf("
    	    DELETE
            FROM D_SUPPORT
    	    WHERE
                SUPPORT_ID = %s", $supportID);

    	$result = $dbAccess->Delete($query);

 	    echo json_encode(true);
    }
?>
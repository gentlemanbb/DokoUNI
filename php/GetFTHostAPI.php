<?php
	require "DBAccess.php";
	
	$dbAccess = new DBAccess();
	$dbAccess->DBConnect();
		
	$query = "
		SELECT
			D_FT_HOST.FT_HOST_ID,
			D_FT_HOST.USER_ID,
			D_FT_HOST.WISH_FT_COUNT,
			D_FT_HOST.WISH_PREP_TIME,
			D_FT_HOST.WISH_PREP_TYPE,
			D_FT_HOST.WISH_RIVAL_RIP,
			D_FT_HOST.WISH_AREA_ID,
			D_FT_HOST.COMMENT,
			D_FT_HOST.TOROKU_DATETIME
		FROM
			D_FT_HOST";

        $rows = $dbAccess->Select($query);

        $popuralityData = [];

        foreach ($rows as $row){
           $popuralityData[] = 
           [
               'PLACE_ID' => $row['PLACE_ID'],
               'PLACE_NAME' => $row['PLACE_NAME'],
               'VALUE1' => $row['VALUE1'],
               'VALUE2' => $row['VALUE2'],
               'VALUE3' => $row['VALUE3'],
               'QUERY' => $query,
               'ADD_DAYS' => $addDays
            ];
        }

        header('Content-type: application/json');
        echo json_encode($popuralityData);
?>
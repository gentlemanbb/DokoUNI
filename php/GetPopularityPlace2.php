<?php
	require "DBAccess.php";

	header('Content-type: application/json');

	$dbAccess = new DBAccess();
	$dbAccess->DBConnect();
	$areaID = $_POST['areaID'];
	$addDays = $_POST['addDays'];
	
	$areaSearchQuery1 = '';
	$areaSearchQuery2 = '';
	$areaSearchQuery3 = '';
	
	if($areaID != null || $areaID != ''){
		$areaSearchQuery1 = sprintf('
		    AND PLACE1.AREA_ID = %s
		', $areaID);
		$areaSearchQuery2 = sprintf('
		    AND PLACE2.AREA_ID = %s
		', $areaID);
		$areaSearchQuery3 = sprintf('
		    AND PLACE3.AREA_ID = %s
		', $areaID);
	}
	
	$query = sprintf("
		SELECT
			PLACE.PLACE_ID AS PLACE_ID
			, CASE WHEN HIS.PLACE_NAME IS NOT NULL THEN HIS.PLACE_NAME ELSE PLACE.PLACE_NAME END AS PLACE_NAME
			, CASE WHEN VALUE1 IS NULL THEN 0 ELSE VALUE1 END AS VALUE1
			, CASE WHEN VALUE2 IS NULL THEN 0 ELSE VALUE2 END AS VALUE2
			, CASE WHEN VALUE3 IS NULL THEN 0 ELSE VALUE3 END AS VALUE3
		FROM
			M_PLACE PLACE
		LEFT JOIN
			D_PLACE_HISTORY HIS
		ON
			PLACE.PLACE_HISTORY_ID = HIS.PLACE_HISTORY_ID
		LEFT JOIN (
			SELECT
				POP1.PLACE_ID
				, COUNT(1) AS VALUE1
			FROM D_POPULARITY POP1
			
			LEFT JOIN
				M_PLACE PLACE1
			ON
				POP1.PLACE_ID = PLACE1.PLACE_ID
			WHERE
				POP1.JOIN_TYPE = 0
			AND
				POP1.JOIN_DATE_FROM = DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
			
			%s
			GROUP BY
				POP1.PLACE_ID) TBL1
		ON
			PLACE.PLACE_ID = TBL1.PLACE_ID
		
		LEFT JOIN (
			SELECT
				POP2.PLACE_ID
				, COUNT(1) AS VALUE2
			FROM
				D_POPULARITY POP2
			
			LEFT JOIN
				M_PLACE PLACE2
			ON
				POP2.PLACE_ID = PLACE2.PLACE_ID
			
			WHERE
				POP2.JOIN_TYPE = 1
			AND
				POP2.JOIN_DATE_FROM = DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
			
			%s
			GROUP BY POP2.PLACE_ID) TBL2
			
		ON
			PLACE.PLACE_ID = TBL2.PLACE_ID

		LEFT JOIN (
			SELECT
				POP3.PLACE_ID
				, COUNT(1) AS VALUE3
			FROM
				D_POPULARITY POP3
			
			LEFT
				JOIN M_PLACE PLACE3
			ON
				POP3.PLACE_ID = PLACE3.PLACE_ID
			
			WHERE
				POP3.JOIN_TYPE = 2
			AND
				POP3.JOIN_DATE_FROM = DATE_ADD(CURRENT_DATE(), INTERVAL %s DAY)
			
			%s
			
			GROUP BY POP3.PLACE_ID) TBL3
		ON
			PLACE.PLACE_ID = TBL3.PLACE_ID
			
			
		WHERE
			(TBL1.VALUE1 > 0 OR TBL2.VALUE2 > 0 OR TBL3.VALUE3)
		GROUP BY
			PLACE_ID
		ORDER BY
			VALUE1 DESC",
			
		$addDays, $areaSearchQuery1,
		$addDays, $areaSearchQuery2,
		$addDays, $areaSearchQuery3);

        $rows = $dbAccess->Select($query);

        $popuralityData = [];
		
		if(count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$popuralityData[] = [
					'PLACE_ID' => $row['PLACE_ID'],
					'PLACE_NAME' => $row['PLACE_NAME'],
					'VALUE1' => $row['VALUE1'],
					'VALUE2' => $row['VALUE2'],
					'VALUE3' => $row['VALUE3'],
					'QUERY' => $query,
					'ADD_DAYS' => $addDays
				];
			}
		}
		
		echo json_encode($popuralityData);
?>
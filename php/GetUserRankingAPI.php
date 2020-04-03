<?php
    require "DBAccess.php";

    $dbAccess = new DBAccess();

    // DB接続
    $pdo = $dbAccess->DBConnect2();

    // 引数取得
    $addDays = $_POST['addDays'];

    $query = sprintf("
		SELECT
			COUNT(1) AS POPULARITY_COUNT
			, CASE AGREE_DISPLAY_NAME
				WHEN TRUE THEN SUB.USER_NAME
				WHEN FALSE THEN 'UNKNOWN'
				ELSE 'UNKNOWN'
			END AS PLAYER_NAME
		FROM
			D_POPULARITY BASE
		LEFT
			JOIN D_USER SUB
		ON
			BASE.USER_ID = SUB.USER_ID
		WHERE
			BASE.JOIN_DATE_FROM BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL :addDays DAY) AND CURRENT_DATE()
		GROUP BY
			BASE.USER_ID
		ORDER BY
			POPULARITY_COUNT DESC
		LIMIT 5
		");
        
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':addDays', $addDays, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $rankingData = [];

    // データの行数分詰める
    // 存在しない場合は0行で返る
    foreach ($rows as $row){
       $rankingData[] = 
       [
           'POPULARITY_COUNT' => $row['POPULARITY_COUNT'],
           'PLAYER_NAME' => $row['PLAYER_NAME']
        ];
    }

    header('Content-type: application/json');
    echo json_encode($rankingData);
?>
#!/usr/local/php/7.2/bin/php

<?php
	// ライブラリの読み込み
	require 'DBAccess.php';
	require 'TweetAPI.php';
	require 'LogUtility.php';
	
	header('Content-type: application/text');

	$dbAccess = new DBAccess();

	// DB接続
	$pdo = $dbAccess->DBConnect2();
	
	// 2時間以内のFROM宣言を取得
	$query = "
		SELECT
			POPULARITY_ID
			, TWEET_ID
			, JOIN_TIME_FROM
		FROM
			D_POPULARITY
		WHERE
			CAST(CONCAT(CAST(JOIN_DATE_FROM AS CHAR(20)), ' ', CAST(JOIN_TIME_FROM AS CHAR(20))) AS DATETIME) <= (CURRENT_TIMESTAMP() + INTERVAL 2 HOUR)
		AND
			TWEET_ID IS NOT NULL
		AND
			RETWEET_FLG = 0";
	
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$popRows = $stmt->fetchAll();
	
	if(is_array($popRows) && count($popRows) > 0)
	{
		// エリアごとに処理
		foreach($popRows as $popRow)
		{
			$popID = $popRow['POPULARITY_ID'];
			$tweetID = $popRow['TWEET_ID'];
			
			$RTResult = ReTweet($tweetID);

			$query =  "
				UPDATE
					D_POPULARITY
				SET
					RETWEET_FLG = 1
				WHERE
					POPULARITY_ID = :popID
				";
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':popID'		, $popID	, PDO::PARAM_INT);
			$stmt->execute();
		}

		WriteLog('log', '【ReTweetPopularityAPI】リツイート終了');
	}
	else
	{
		WriteLog('log', '【ReTweetPopularityAPI】リツイート対象無し');
	}

	WriteLog('log', '【ReTweetPopularityAPI】リツイート処理が終了しました。');

	$result = [
		'RESULT' => true,
		'MESSAGE' => 'リツイート処理が完了しました。'
	];
	
	echo json_encode($result);
?>
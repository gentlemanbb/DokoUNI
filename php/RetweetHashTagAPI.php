#!/usr/local/php/7.2/bin/php

<?php
	// ライブラリの読み込み
	require_once 'DBAccess.php';
	require_once 'TweetAPI.php';
	require_once 'LogUtility.php';
	require_once 'Functions.php';
	
	header('Content-type: application/text');

	try
	{
		$dbAccess = new DBAccess();

		// DB接続
		$pdo = $dbAccess->DBConnect2();

		// URLからハッシュタグを取得
		// $argsResult = argvArray($argv);
		$hashTag = '#UNIプレマ募集';

		// 今日の日付取得
		$now = date('Y-m-d');
		$registeredDatetime;
		$yesterday = date('Y-m-d', strtotime("-1 day"));

		// AND検索条件を設定
		$andArray = array($hashTag);

		// OR検索条件は不要
		$orArray = null;

		// 20件のみ取得
		$count = 20;

		// 当日のみ
		$since = $now;
		$until = $yesterday;
		
		$includeRT = false;
		
		// ====================
		//  ツイートを取得する
		// ====================
		$tweetResult = GetTweets($andArray, null, $since, $until, $includeRT, $count);

		// 取得結果があるかチェックする
		if(is_array($tweetResult))
		{
			// １件ずつ処理する
			foreach($tweetResult as $tweet)
			{
				$executeRT = false;

				// ツイッターIDを取得
				$tweetID = $tweet->id;

					// データベース内に同一の名前の場所が存在するか確認する
				$query = sprintf("
						SELECT
							TWEET_ID
							, RETWEET_FLG
						FROM
							D_RETWEET_MANAGE
						WHERE
							TWEET_ID = :tweetID
				");
					
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':tweetID', $tweetID, PDO::PARAM_STR);
				$stmt->execute();
				$rows = $stmt->fetchAll();

				if(is_array($rows) && count($rows) > 0)
				{
					// 存在する場合
					foreach($rows as $row)
					{
						$retweetFlg = $row['RETWEET_FLG'];
						
						if($retweetFlg == '0')
						{
							// RTする
							$executeRT = true;
						}
					}
				}
				else
				{
					// 存在しない場合
					// RTする
					$executeRT = true;
				}

				if($executeRT)
				{
					$RTResult = ReTweet($tweetID);
					
					// データベースにINSERTする
					$query = sprintf("
						INSERT INTO
							D_RETWEET_MANAGE  (
								TWEET_ID
								, RETWEET_FLG
							)
						VALUES (
							:tweetID
							, true
						)");
				
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':tweetID', $tweetID, PDO::PARAM_STR);
					$stmt->execute();
					$rows = $stmt->fetchAll();
				}
			}
		}

		$result = [
			'RESULT' => true,
			'MESSAGE' => 'リツイート処理が完了しました。'
		];
		
		echo json_encode($result);
	}
	catch(Exception $ex)
	{
		// エラーログを出力
		WriteErrorLog($ex);
			
		$result = [
			'RESULT' => false,
			'MESSAGE' => sprintf('『{0}』 を含むツイートに対するリツイート処理が終了しました。', $hashTag)
		];
			
		echo json_encode($result);
	}
?>
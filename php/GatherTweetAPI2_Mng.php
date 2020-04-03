<?php
	// ライブラリの読み込み
	require 'DBAccess.php';
	require 'TweetAPI.php';
	require 'LogUtility.php';
	require 'GetDataUtility.php';
	
	header('Content-type: application/json');

	try
	{
		$dbAccess = new DBAccess();
		
		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 今日の日付取得
		$now = date('Y-m-d');
		$registeredDatetime;
		$yesterday = date('Y-m-d', strtotime("-30 day"));

		// AND検索条件を設定
		$andArray = array('#UNIEST_AC', 'プレイ中', '-RT');

		// OR検索条件は不要
		$orArray = null;

		// 15件のみ取得
		$count = 15;

		// 当日のみ
		$since = $now;
		$until = $yesterday;
		
		$includeRT = false;
		
		// ====================
		//  ツイートを取得する
		// ====================
		$tweetResult = GetTweets($andArray, null, $since, $until, $includeRT, $count);
		
		$placeArray = [];
		
		// 取得結果があるかチェックする
		if(is_array($tweetResult))
		{
			// １件ずつ処理する
			foreach($tweetResult as $tweet)
			{
				// ツイッターIDを取得
				$name = $tweet->user->name;
				$twitterID = $tweet->user->screen_name;
				
				// ツイート日時
				$created_at =  $tweet->created_at;
				$timestamp = strtotime($created_at);
				$registeredDatetime = date('Y-m-d H:i:s', $timestamp);
				
				// ツイート内容を取得
				$text = $tweet->text;
				
				// ツイート内容を分割
				$splitStr = explode('[st] ', $text);
				$splitStr = explode('で', $splitStr[1]);
				
				// 分割した内容からゲームセンターの名前を抽出
				$placeName = $splitStr[0];
				$displayName = $placeName;
				
				$added = array_search($placeName, $placeArray);
				
				// DBの場所ID
				$placeID = '';
				
				if(!$added)
				{
					// =========================================
					//  取得したいツイートだった場合こちらに来る
					// =========================================
					
					// 配列内に存在しなかった場合
					// ゲームセンターの名前を配列に追加する
					$placeArray[] =
					[
						'PLACE_NAME' => $placeName
					];
					
					// ファイルに書き込む文字列にも追加
					$fileStr = sprintf('%s' . "\n" . '******' . "\n", $fileStr);
					$fileStr = sprintf('%s処理中のゲームセンター：%s' . "\n", $fileStr, $placeName);
					
					// データベース内に同一の名前の場所が存在するか確認する
					$query = sprintf("
						SELECT
							PLACE_ID
							, PLACE_NAME
						FROM
							M_PLACE
						WHERE
							OFFICIAL_NAME = :placeName");
					
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':placeName', $placeName, PDO::PARAM_STR);
					$stmt->execute();
					$rows = $stmt->fetchAll();
					
					$officialName = $placeName;
					$dataHasFound = false;
					
					// 存在するか確認
					if(is_array($rows))
					{
						foreach($rows as $row)
						{
							// 見つけた場合フラグを立てる
							// 表示名称をDBのものを使用する
							$dataHasFound = true;
							$displayName = $row['PLACE_NAME'];
							$placeID = $row['PLACE_ID'];
						}
					}
					
					// データをがあった場合
					// データがなかった場合 で分岐
					if($dataHasFound == false)
					{
						// データが見つからなかった場合
						// DBに新しいデータとして登録する
						try
						{
							// INSERT文の生成
							$query = sprintf("
								INSERT INTO M_PLACE (
									PLACE_NAME
									, AREA_ID
									, SEQ_NO
									, OFFICIAL_NAME)
								VALUES (
									:displayName
									, 99
									, 99
									, :officialName
								)");
							
							// 地方を未分類とする
							$areaID = '99';
							
							// 並び順も最下層
							$seqNo = 99;
							
							$stmt = $pdo->prepare($query);
							$stmt->bindParam(':displayName'		, $displayName	, PDO::PARAM_STR);
							$stmt->bindParam(':officialName'	, $officialName	, PDO::PARAM_STR);
							
							// インサート
							$stmt->execute();
							
							// ログファイルに書き込む情報
							$fileStr = sprintf('%sDBへのインサート：%s' . "\n", $fileStr, $placeName);
							$fileStr = sprintf('%s%s' . "\n", str_replace("\t", ' ', $query));
							
							// インサートしたデータを取得します。
							$query = sprintf("
								SELECT
									PLACE_ID
									, PLACE_NAME
								FROM
									M_PLACE
								WHERE
									OFFICIAL_NAME = :placeName");
							
							$stmt = $pdo->prepare($query);
							$stmt->bindParam(':placeName', $placeName, PDO::PARAM_STR);
							$stmt->execute();
							$rows = $stmt->fetchAll();
							
							$officialName = $placeName;
							$dataHasFound = false;
							
							// 存在するか確認
							if(is_array($rows))
							{
								foreach($rows as $row)
								{
									// 見つけた場合フラグを立てる
									// 表示名称をDBのものを使用する
									$dataHasFound = true;
									$displayName = $row['PLACE_NAME'];
									$placeID = $row['PLACE_ID'];
								}
							}
						}
						catch(Exception $ex)
						{
							// 
							$fileStr = sprintf('%s %s のインサートに失敗しました。' . "\n", $fileStr, $placeName);
						}
					}
				}
				else
				{
					// 配列にすでに存在している場合
				}
				
				$userID = null;
				
				// Twitter ID -> どこUNI ID に変換
				$query = sprintf("
					SELECT
						USER_ID
						, USER_NAME
					FROM
						D_USER
					WHERE
						TWITTER = :twitterID");
				
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':twitterID', $twitterID, PDO::PARAM_STR);
				$stmt->execute();
				
				$userRows = $stmt->fetchAll();
				
				// レコードが取得できたか確認する
				if(is_array($userRows) && count($userRows) > 0)
				{
					foreach($userRows as $userRow)
					{
						$userID = $userRow['USER_ID'];
						$name =  $userRow['USER_NAME'];
					}
				}
				
				// ゲームセンターに対するレコードをチェック
				// データベース内に同一の名前の場所が存在するか確認する
				$query = sprintf("
					SELECT
						BASE.USER_ID
						, PLAYER_NAME
						, TWEET_DATETIME
					FROM
						D_PLAYING BASE
					LEFT JOIN
						D_USER SUB
					ON
						BASE.PLAYER_NAME = SUB.USER_NAME
					WHERE
						PLACE_ID = :placeID
					AND
						PLAYER_NAME = :twitterID
					AND
						TWEET_DATETIME = :tweetDatetime");
						
				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':placeID', 		$placeID, 				PDO::PARAM_STR);
				$stmt->bindParam(':twitterID', 		$twitterID, 			PDO::PARAM_STR);
				$stmt->bindParam(':tweetDatetime', 	$registeredDatetime, 	PDO::PARAM_STR);
				$stmt->execute();
				$playingRows = $stmt->fetchAll();
				
				$updateType = 'nothing';
				$playingID = '';
				
				// 登録内容をログに出力
				$fileStr = sprintf('%s場所ID:%s' . "\n"
					. 'ツイッターID:%s' . "\n"
					. '登録日時:%s' . "\n"
					, $fileStr
					, $placeID
					, $twitterID
					, $registeredDatetime);
				
				// レコードが取得できたか確認する
				if(is_array($playingRows) && count($playingRows) > 0)
				{
					// あった場合
					$updateType='EXISTS';
					$fileStr = $fileStr . '登録済みデータ。' . "\n";
				}
				else
				{
					$fileStr = $fileStr . 'プレイ中データが見つかりませんでした。' . "\n";
					
					// 連勝レコードが存在しない場合
					// 有無を言わさず登録する
					$updateType = 'INSERT';
				}

				if($updateType == 'INSERT')
				{
					try
					{
						// INSERT文の生成
						$query = "
							INSERT INTO
								D_PLAYING
								(
									PLACE_ID
									, USER_ID
									, PLAYER_NAME
									, TWEET_DATETIME
								)

								VALUES
								(
									:placeID
									, :displayName1
									, :displayName2
									, :tweetDatetime
								)";
						
						$stmt = $pdo->prepare($query);
						$stmt->bindParam(':placeID'				, $placeID				, PDO::PARAM_INT);
						$stmt->bindParam(':displayName1'		, $userID				, PDO::PARAM_STR);
						$stmt->bindParam(':displayName2'		, $twitterID			, PDO::PARAM_STR);
						$stmt->bindParam(':tweetDatetime'		, $registeredDatetime	, PDO::PARAM_STR);
						
						// インサート
						$stmt->execute();
						
						$fileStr = $fileStr . sprintf('プレイ履歴を登録しました。' . "\n");
					}
					catch(Exception $ex)
					{
						$fileStr = $fileStr . sprintf('プレイ履歴の登録に失敗しました。' . "\n");
					}
				}
				else
				{
				}
			}
		}
		else
		{
			$fileStr = sprintf('ツイートはありませんでした。');
		}
		
		// ツイート内容を書き込む
		WriteLog('GatherTweetAPI2', $fileStr);
		
		echo json_encode($placeArray);
	}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);

		$returnData = [
			'RESULT' => false,
			'MESSAGE' => $ex->getMessage()
		];

		echo json_encode($placeArray);
	}
?>
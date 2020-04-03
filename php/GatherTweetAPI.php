<?php
	// ライブラリの読み込み
	require 'DBAccess.php';
	require 'TweetAPI.php';
	require 'LogUtility.php';

	header('Content-type: application/json');

	$dbAccess = new DBAccess();
    
	// DB接続
	$pdo = $dbAccess->DBConnect2();
	
	// 今日の日付取得
	$now = date('Y-m-d');
	$registeredDatetime;
	$yesterday = date('Y-m-d', strtotime("-1 day"));
	
	$tweetStr = sprintf('#どこUNI ≪イベント情報≫');
	
	// AND検索条件を設定
	$andArray = array('#UNIEST_AC', '連勝達成', '-RT');

	// OR検索条件は不要
	$orArray = null;

	// 30件のみ取得
	$count = 20;

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
			$registeredDatetime = date('Y-m-d', $timestamp);
			
			// ツイート内容を取得
			$text = $tweet->text;
			
			// ツイート内容を分割
			$splitStr = explode('にて', $text);
			
			// 分割した内容からゲームセンターの名前を抽出
			$placeName = $splitStr[0];
			$displayName = $placeName;
			
			$added = array_search($placeName, $placeArray);
			
			// 連勝数を抽出
			$splitStreakStr1 = explode('で', $splitStr[1]);
			
			// [1] キャラ名
			// [2] ゴミ
			// [3] 連勝数
			// [4] ゴミ
			$characterName = $splitStreakStr1[0];
			$splitStreakStr2 = explode('連勝達成', $splitStreakStr1[1]);
			$newStreakCount = $splitStreakStr2[0];

			WriteLog('log', '******');
			WriteLog('log', sprintf('処理中のゲームセンター：%s', $placeName));
			WriteLog('log', sprintf('[1] %s', $splitStreakStr1[0]));
			WriteLog('log', sprintf('[2] %s 連勝', $splitStreakStr2[0]));

			// DBの場所ID
			$placeID = '';
			
			if(!$added)
			{
				// 配列内に存在しなかった場合
				// ゲームセンターの名前を配列に追加する
				$placeArray[] =
				[
					'PLACE_NAME' => $placeName
				];
				
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

						WriteLog('log', sprintf('データを登録。'));
						
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
						WriteErrorLog($ex);
						WriteLog('log', '場所データの登録に失敗しました。');
					}
				}
				else
				{
				}
			}
			else
			{
				// 配列にすでに存在している場合
			}
			
			// ゲームセンターに対する
			// 連勝数をチェック
			// データベース内に同一の名前の場所が存在するか確認する
			$query = sprintf("
				SELECT
					WIN_ID
					, WINNING_STREAK
					, TWITTER_ID
				FROM
					D_WINNING_STREAK BASE
				LEFT JOIN
					D_USER SUB
				ON
					BASE.TWITTER_ID = SUB.TWITTER
				WHERE
					PLACE_ID = :placeID
				AND
					TWITTER_ID = :twitterID
				AND
					REGISTERED_DATETIME = :registeredDatetime");
			
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':placeID', $placeID, PDO::PARAM_STR);
			$stmt->bindParam(':twitterID', $twitterID, PDO::PARAM_STR);
			$stmt->bindParam(':registeredDatetime', $registeredDatetime, PDO::PARAM_STR);
			$stmt->execute();
			$winningRows = $stmt->fetchAll();
			
			$updateType = 'nothing';
			$winID = '';
			
			// 連勝レコードが取得できたか確認する
			if(is_array($winningRows)
				&& count($winningRows) > 0)
			{
				foreach($winningRows as $winningRow)
				{
					// ツイッターIDを取得する
					$dbTwitterID = $winningRow['TWITTER_ID'];
					$oldStreakCount = $winningRow['WINNING_STREAK'];
					
					if($newStreakCount >= $oldStreakCount
							&& $dbTwitterID == $twitterID)
					{
						WriteLog('log', sprintf('最大連勝を更新します。'));
						$updateType = 'UPDATE';
						$winID = $winningRow['WIN_ID'];
					}
				}
			}
			else
			{
				WriteLog('log', sprintf('連勝が見つかりませんでした。'));
				
				// 連勝レコードが存在しない場合
				// 有無を言わさず登録する
				$updateType = 'INSERT';
			}
			
			if($updateType != 'NOTHING')
			{
				$query = sprintf("
					SELECT
						USER_NAME
					FROM
						D_USER
					WHERE
						TWITTER = :twitterID
				");

				$stmt = $pdo->prepare($query);
				$stmt->bindParam(':twitterID', $twitterID, PDO::PARAM_STR);
				$stmt->execute();
				
				
				$userRows = $stmt->fetchAll();
				
				// 連勝レコードが取得できたか確認する
				if(is_array($userRows)
					&& count($userRows) > 0)
				{
					foreach($userRows as $userRow)
					{
						$name = $userRow['USER_NAME'];
					}
				}
			}
			
			if($updateType == 'INSERT')
			{
				try
				{
					// データベース内に同一の名前の場所が存在するか確認する
					$query = sprintf("
						SELECT
							CHARACTER_ID
						FROM
							D_CHARA
						WHERE
							CHARACTER_NAME = :characterName");
					
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':characterName', $characterName, PDO::PARAM_STR);
					$stmt->execute();
					$charaRows = $stmt->fetchAll();
					
					if(is_array($charaRows)
						&& count($charaRows) > 0)
					{
						foreach($charaRows as $charaRow)
						{
							$characterID = $charaRow['CHARACTER_ID'];
						}
					}
					
					// INSERT文の生成
					$query = sprintf("
					INSERT INTO D_WINNING_STREAK (
							PLAYER_NAME
							, CHARACTER_ID
							, TWITTER_ID
							, WINNING_STREAK
							, PLACE_ID
							, REGISTERED_DATETIME
							, UPDATE_DATETIME)
						VALUES (
							:displayName
							, :characterID
							, :name
							, :newStreakCount
							, :placeID
							, :registeredDatetime
							, :updateDatetime
						)");
						
					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':displayName'			, $name					, PDO::PARAM_STR);
					$stmt->bindParam(':characterID'			, $characterID			, PDO::PARAM_STR);
					$stmt->bindParam(':name'				, $twitterID			, PDO::PARAM_STR);
					$stmt->bindParam(':newStreakCount'		, $newStreakCount		, PDO::PARAM_INT);
					$stmt->bindParam(':placeID'				, $placeID				, PDO::PARAM_INT);
					$stmt->bindParam(':registeredDatetime'	, $registeredDatetime	, PDO::PARAM_STR);
					$stmt->bindParam(':updateDatetime'		, $registeredDatetime	, PDO::PARAM_STR);
					
					// インサート
					$stmt->execute();
					
					WriteLog('log', sprintf('連勝データを登録しました。'));
				}
				catch(Exception $ex)
				{
					WriteErrorLog($ex);
					WriteLog('log', sprintf('連勝データの登録に失敗しました。'));
				}
			}
			else if($updateType == 'UPDATE')
			{
				try
				{
					// INSERT文の生成
					$query = sprintf("
						UPDATE
							D_WINNING_STREAK
						SET
							WINNING_STREAK = :newStreakCount
							, REGISTERED_DATETIME=:registeredDatetime
							, UPDATE_DATETIME=:updateDatetime
						WHERE
							WIN_ID = :winID
					");

					$stmt = $pdo->prepare($query);
					$stmt->bindParam(':newStreakCount'		, $newStreakCount		, PDO::PARAM_INT);
					$stmt->bindParam(':registeredDatetime'	, $registeredDatetime	, PDO::PARAM_STR);
					$stmt->bindParam(':updateDatetime'		, $registeredDatetime	, PDO::PARAM_STR);
					$stmt->bindParam(':winID'				, $winID				, PDO::PARAM_INT);
					
					// アップデート
					$stmt->execute();
					
					WriteLog('log', sprintf('連勝データを更新しました。'));

				}
				catch(Exception $ex)
				{
					WriteErrorLog($ex);
					WriteLog('log', sprintf('連勝データの更新に失敗しました。'));
				}
			}
		}
	}
	else
	{
		WriteLog('log', sprintf('ツイートがありませんでした。'));
	}
	
	echo json_encode($placeArray);
?>
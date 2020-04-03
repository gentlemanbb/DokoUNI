<?php
	try
	{
		// ライブラリ読み込み
		require_once "DBAccess.php";
		require_once "TweetAPI.php";
		require_once "LogUtility.php";
		require_once "GetDataUtility.php";

		header('Content-type: application/json');

		// 引数の取得
		$playerName = $_POST['playerName'];
		$userID = $_POST['userID'];
		
		// 場所
		$placeID = $_POST['placeID'];
		$placeName = $_POST['placeName'];
		
		// 参加区分
		$joinType = $_POST['joinType'];
		$joinText = $_POST['joinText'];
		
		// 目的区分
		$purposeType = $_POST['purposeType'];
		$purposeText = $_POST['purposeText'];
		
		// 日時
		$joinTimeFrom = $_POST['from'];
		$joinTimeTo = $_POST['to'];
		$addDays = $_POST['addDays'];

		// コメント
		$comment = $_POST['comment'];
		
		// ツイートを同時に行うかどうか
		$withTweet = $_POST['withTweet'];
		WriteLog('SendPopularity', sprintf('withTweet = [%s] ', $withTweet));

		// 登録に使う日付
		$joinDate = date("Y-m-d", strtotime(sprintf("%s day", $addDays)));

		// ログファイル名
		$logFileName = date('Y-m-d');

		// RIP
		$RIP = $_POST['RIP'];
		$characterID = $_POST['characterID'];

		// ツイート結果
		$tweetResult = False;
		
		// 最終的な結果
		$resultPattern;

		if($RIP == null)
		{
			$RIP = 'NULL';
		}
		
		if(true)
		{
			WriteLog("log", $playerName);
			WriteLog("log", $userID);
			WriteLog("log", $placeID);
			WriteLog("log", $placeName);
			WriteLog("log", $joinType);
			WriteLog("log", $joinText);
			WriteLog("log", $purposeType);
			WriteLog("log", $purposeText);
			WriteLog("log", $joinTimeFrom);
			WriteLog("log", $joinTimeTo);
			WriteLog("log", $addDays);
			WriteLog("log", $comment);
			WriteLog("log", $withTweet);
			WriteLog("log", $joinDate);
		}


		$playerData = GetUserDetailData($userID);

		if($playerData == null)
		{
			// プレーヤーデータが取得できなかった場合
			$result = [
				'RESULT' => false,
				'MESSAGE' => '送信されたユーザーは存在しませんでした。'
			];

			echo json_encode($result);
		}
		else
		{
			$playerName = $playerData['USER_NAME'];

			if($playerName == '' || $playerName == null)
			{
				$playerName = '【UNKNOWN】';
			}
		}


		// DB接続
		$dbAccess = new DBAccess();
		$pdo = $dbAccess->DBConnect2();

		// ---------------------
		// 送信済みデータの取得
		// ---------------------
		$query = sprintf("
			SELECT
				POPULARITY_ID
				, JOIN_TYPE
				, PURPOSE_TYPE
				, JOIN_TIME_FROM
				, JOIN_TIME_TO
				, PLACE_ID

			FROM D_POPULARITY POP	

			WHERE
				POP.USER_ID = :userID
			AND
				POP.JOIN_DATE_FROM = :joinDate
		");

		$stmt = $pdo->prepare($query);
		$pdo->beginTransaction();
		$stmt->bindParam(':userID'		, $userID		, PDO::PARAM_STR);
		$stmt->bindParam(':joinDate'	, $joinDate		, PDO::PARAM_STR);
		$stmt->execute();
		$prevRows = $stmt->fetchAll();
		
		// 前データの投票ＩＤ
		$prevPopularityID = NULL;
		$prevPlaceID = NULL;

		if(is_array($prevRows) && count($prevRows) > 0)
		{
			// データがあった場合
			foreach ($prevRows as $prevRow)
			{
				$prevPopularityID = $prevRow['POPULARITY_ID'];
				$prevPlaceID = $prevRow['PLACE_ID'];
			}
			
			WriteLog('SendPopularity', sprintf('投票済みデータ [ID:%s] が見つかりました。', $prevPopularityID));
		}
		else
		{
			WriteLog('SendPopularity', sprintf('%s の %s 新規投票です。', $userID, $joinDate));
		}

		// ---------------------
		// 通知ユーザの取得
		// ---------------------
		$query = sprintf('
			SELECT
				POP.USER_ID
				, USR.TWITTER		

			FROM
				D_POPULARITY POP
			LEFT
				JOIN D_USER USR
			ON
				POP.USER_ID = USR.USER_ID

			WHERE
				(POP.PLACE_ID = :placeID
			OR
				POP.PLACE_ID = :prevPlaceID)
			AND
				POP.JOIN_DATE_FROM = :joinDate
			AND (
					(%s) OR (%s) OR (%s) OR (%s)
				)
			AND
				USR.TWITTER IS NOT NULL

			GROUP BY
				USER_ID ASC',
			
			// 【目的】 0:ガチ, 1:ゆるく, 2:観戦, 3:トレモ, 4:イベント参加, 5:仕事
			// 【通知】 0:通知しない, 1:同じ場所の全て, 2:対戦の全て, 3:ガチ対戦のみ, 4:ゆるく対戦のみ

			// 目的区分「ガチ対戦」の場合 1, 2, 3 に対して通知
			':purposeType1 = 0 AND (USR.NOTIFICATION = 1 OR USR.NOTIFICATION = 2 OR USR.NOTIFICATION = 3)',
			// 目的区分「ゆるく対戦」の場合 1, 2, 4 に対して通知
			':purposeType2 = 1 AND (USR.NOTIFICATION = 1 OR USR.NOTIFICATION = 2 OR USR.NOTIFICATION = 4)',
			// 目的区分「観戦」の場合 1に対してのみ通知
			':purposeType3 = 2 AND USR.NOTIFICATION = 1',
			// 目的区分「トレモ」の場合 1に対してのみ通知
			':purposeType4 = 3 AND USR.NOTIFICATION = 1'
		);

		$stmt = $pdo->prepare($query);
		$stmt->bindParam(':placeID'		, $placeID		, PDO::PARAM_INT);
		$stmt->bindParam(':prevPlaceID'		, $prevPlaceID		, PDO::PARAM_INT);
		$stmt->bindParam(':joinDate'	, $joinDate		, PDO::PARAM_STR);
		$stmt->bindParam(':purposeType1'	, $purposeType	, PDO::PARAM_INT);
		$stmt->bindParam(':purposeType2'	, $purposeType	, PDO::PARAM_INT);
		$stmt->bindParam(':purposeType3'	, $purposeType	, PDO::PARAM_INT);
		$stmt->bindParam(':purposeType4'	, $purposeType	, PDO::PARAM_INT);
		$sqlResult = $stmt->execute();
		$notificationRows = $stmt->fetchAll();

		// リプライするTWITTER IDの配列
		$notificationUsers = [];

		if(is_array($notificationRows) && count($notificationRows) > 0)
		{
			foreach ($notificationRows as $notificationRow)
			{
				if(strlen($notificationRow['TWITTER']) > 0)
				{
					// 有効なTWITTER IDの場合
					// 配列に追加する
					array_push($notificationUsers, $notificationRow['TWITTER']);
				}
			}
		}
		
		if($prevPopularityID != NULL)
		{
			// 更新対象のデータがある場合
			$resultPattern = 'UPDATE';

			$query = sprintf('
				UPDATE
					D_POPULARITY
				SET
					PLACE_ID = :placeID
					, CHARACTER_ID = :characterID
					, RIP = :rip
					, JOIN_TYPE = :joinType
					, PURPOSE_TYPE = :purposeType
					, JOIN_TIME_FROM = :joinTimeFrom
					, JOIN_TIME_TO = :joinTimeTo
					, COMMENT = :comment
				WHERE
					POPULARITY_ID = :prevPopularityID'
			);

			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':placeID'			, $placeID			, PDO::PARAM_INT);
			$stmt->bindParam(':characterID'		, $characterID		, PDO::PARAM_INT);
			$stmt->bindParam(':rip'				, $rip				, PDO::PARAM_INT);
			$stmt->bindParam(':joinType'		, $joinType			, PDO::PARAM_INT);
			$stmt->bindParam(':purposeType'		, $purposeType		, PDO::PARAM_INT);
			$stmt->bindParam(':joinTimeFrom'	, $joinTimeFrom		, PDO::PARAM_STR);
			$stmt->bindParam(':joinTimeTo'		, $joinTimeTo		, PDO::PARAM_STR);
			$stmt->bindParam(':comment'			, $comment			, PDO::PARAM_STR);
			$stmt->bindParam(':prevPopularityID', $prevPopularityID	, PDO::PARAM_INT);
			$sqlResult = $stmt->execute();

			$tweetStr = sprintf(
				'#どこUNI %s さん が %s %s - %s の %s [%s, %s]  に変更しました。'
				, $playerName
				, $joinDate
				, $joinTimeFrom
				, $joinTimeTo
				, $placeName
				, $purposeText
				, $joinText);

			if($comment != null && $comment != '')
			{
				$tweetStr = sprintf('%s' . "\r\n" . 'コメント「%s」', $tweetStr, $comment);
			}

			$notificationStr;

			// 通知対象のＩＤを付け加える
			foreach($notificationUsers as $value)
			{
				$notificationStr = $notificationStr . sprintf(" @%s", $value);
			}

			// ツイート文字列に加える
			$tweetStr = $tweetStr . $notificationStr;

			// チェックボックスから取得した値なので文字列比較
			if($withTweet == 'true')
			{				
				WriteLog('log', '【SendPopularity.php】ツイート処理開始 +++');
				WriteLog('log', sprintf('【SendPopularity.php】ツイート内容：[%s]', $tweetStr));
				$tweetResult = Tweet($tweetStr);
				WriteLog('log', '【SendPopularity.php】ツイート処理終了 +++');
			}
			else
			{
				WriteLog('SendPopularity', sprintf('もしツイートされていたら：[%s]', $tweetStr));
			}
		}
		else
		{
			$resultPattern = "INSERT";

			$tweetStr = sprintf(
				'#どこUNI %s さん が %s %s - %s の %s に [%s, %s] でチェックインしました。' 
					, $playerName
					, $joinDate
					, $joinTimeFrom
					, $joinTimeTo
					, $placeName
					, $purposeText
					, $joinText);

			if($comment != null && $comment != '')
			{
				$tweetStr = sprintf('%s' . "\r\n" . 'コメント「%s」', $tweetStr, $comment);
			}

			$tweetID = null;

			// チェックボックスから取得した値なので文字列比較
			if($withTweet == 'true')
			{				
				WriteLog('log', '【SendPopularity.php】ツイート処理開始 +++');
				WriteLog('log', sprintf('【SendPopularity.php】ツイート内容：[%s]', $tweetStr));
				$tweetResult = Tweet($tweetStr);
				WriteLog('log', '【SendPopularity.php】ツイート処理終了 +++');

				$tweetID = $tweetResult->id;
				WriteLog('log', sprintf('【SendPopularity.php】ツイートIDはコレ：[%s]', $tweetID));

			}
			else
			{
				// ツイートしない
				WriteLog('log', sprintf('【SendPopularity.php】もしツイートされていたら：[%s]', $tweetStr));
			}
			


			$query = sprintf("
				INSERT D_POPULARITY (
					PLACE_ID
					, USER_ID
					, CHARACTER_ID
					, RIP
					, JOIN_TYPE
					, PURPOSE_TYPE
					, JOIN_DATE_FROM
					, JOIN_TIME_FROM
					, JOIN_DATE_TO
					, JOIN_TIME_TO
					, COMMENT
					, TWEET_ID
				)
		
				VALUES (
					:placeID
					, :userID
					, :characterID
					, :rip
					, :joinType
					, :purposeType
					, :joinDateFrom
					, :joinTimeFrom
					, :joinDateTo
					, :joinTimeTo
					, :comment
					, :tweetID
			)");

			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':placeID'			, $placeID		, PDO::PARAM_INT);
			$stmt->bindParam(':userID'			, $userID		, PDO::PARAM_STR);
			$stmt->bindParam(':characterID'		, $characterID	, PDO::PARAM_INT);
			$stmt->bindParam(':rip'				, $rip			, PDO::PARAM_INT);
			$stmt->bindParam(':joinType'		, $joinType		, PDO::PARAM_INT);
			$stmt->bindParam(':purposeType'		, $purposeType	, PDO::PARAM_INT);
			$stmt->bindParam(':joinDateFrom'	, $joinDate		, PDO::PARAM_STR);
			$stmt->bindParam(':joinTimeFrom'	, $joinTimeFrom	, PDO::PARAM_STR);
			$stmt->bindParam(':joinDateTo'		, $joinDate		, PDO::PARAM_STR);
			$stmt->bindParam(':joinTimeTo'		, $joinTimeTo	, PDO::PARAM_STR);
			$stmt->bindParam(':comment'			, $comment		, PDO::PARAM_STR);
			$stmt->bindParam(':tweetID'			, $tweetID		, PDO::PARAM_STR);
			$stmt->execute();
		}

		// ここまで来たらコミット
		$pdo->commit();
	
		$queryResult = True;

		if($queryResult == True)
		{
			$result = [
				'RESULT' => true,
				'PATTERN' => $resultPattern,
				'POPULARITY_ID' => $prevPopularityID,
				'NotificationUsers' => $notificationUsers,
				'Tweet' => $tweetStr,
				'MESSAGE' => '送信に成功しました。'
			];

			echo json_encode($result);
		}
		else
		{
			$result = [
				'RESULT' => false,
				'PATTERN' => $resultPattern,
				'POPULARITY_ID' => $prevPopularityID,
				'NotificationUsers' => $notificationUsers,
				'MESSAGE' => 'ツイートに失敗しました。'
			];

			echo json_encode($result);
		}

		return;
	}
	catch(Exception $ex)
	{
		// エラーログを出力
		WriteErrorLog($ex);

		$result = [
			'RESULT' => false,
			'MESSAGE' => '送信に失敗しました。'
		];

		echo json_encode($result);
	}
?>
#!/usr/local/php/7.2/bin/php

<?php
	// ライブラリの読み込み
	require_once 'DBAccess.php';
	require_once 'TweetAPI.php';
	require_once 'LogUtility.php';
	require_once "Constants.php";
	require_once "GetDataUtility.php";

	$logFileName = sprintf('%s', date('y-m-d'));

	$result = [
		'RESULT' => true,
		'MESSAGE' => 'イベント通知処理が正常に終了しました。'
	];

	try
	{
		header('Content-type: application/text');

		$dbAccess = new DBAccess();

		// DB接続
		$pdo = $dbAccess->DBConnect2();
		
		// 当日のイベントを全件取得する
		$query = 
			"SELECT
				BASE.PLACE_ID
				, PLACE_NAME
				, EVENT_NAME
				, EVENT_DATE
				, EVENT_TIME_FROM
				, EVENT_TIME_TO
			FROM
				D_EVENT BASE
			LEFT JOIN
				M_PLACE SUB1
			ON
				BASE.PLACE_ID = SUB1.PLACE_ID
			WHERE
				EVENT_DATE = CURRENT_DATE()
			ORDER BY
				EVENT_ID ASC";
		
			$stmt = $pdo->prepare($query);
			$stmt->execute();
			$eventRows = $stmt->fetchAll();

			if(is_array($eventRows) && count($eventRows) > 0)
			{
				// イベントデータ数分実行する
				foreach ($eventRows as $eventRow)
				{
					$placeID = $eventRow['PLACE_ID'];
					$placeName = $eventRow['PLACE_NAME'];
					$eventName = $eventRow['EVENT_NAME'];
					$eventDate = $eventRow['EVENT_DATE'];
					$eventTimeFrom = $eventRow['EVENT_TIME_FROM'];
					$eventTimeTo = $eventRow['EVENT_TIME_TO'];

					$message = sprintf(
						'≪イベント情報≫' . "\r\n" . '本日 %s は %s で %s が開催予定です。' . "\r\n" . '予定時刻：%s - %s'
						, $eventDate
						, $placeName
						, $eventName
						, $eventTimeFrom
						, $eventTimeTo
					);

					// 通知するメッセージをログに残す
					WriteLog($logFileName, sprintf('【DMEventAPI】%s', $message));

					$favoriteUserArray = GetFavoriteUserDataArray(FAVORITE_TYPE::PLACE, $placeID);

					// 通知ユーザーを書き出してみる
					WriteLog($logFileName, sprintf('【DMEventAPI】通知対象：%s人', count($favoriteUserArray)));

					if(is_array($favoriteUserArray) && count($favoriteUserArray) > 0)
					{
						foreach ($favoriteUserArray as $favoriteUserData)
						{
							// お気に入りユーザーのIDからユーザデータを取得する
							$userID = $favoriteUserData;
							$userData = GetUserDetailData($userID);

							// Twitter ID を取得
							$twitterID = $userData['TWITTER'];

							// 通知ユーザーを書き出してみる
							WriteLog(($logFileName), '【DMEventAPI】対象ユーザー：' . $userID . '('. $twitterID . ')');

							// ダイレクトメッセージを送る
							$DMResult = SendDM($message, $twitterID);

							if($DMResult)
							{
								// 通知ユーザーを書き出してみる
								WriteLog($logFileName, '【DMEventAPI】送信完了');
							}
							else
							{
								// 通知ユーザーを書き出してみる
								WriteLog($logFileName, '【DMEventAPI】送信失敗');
							}
						}
					}
					else
					{
						// お気に入りユーザーがいなかった場合
						// 次のイベントに進む
						continue;
					}
				}
			}

			WriteLog('DMEventAPI', '【DMEventAPI】イベント通知が正常に完了しました。');
			WriteLog($logFileName, '【DMEventAPI】イベント通知が正常に完了しました。');
		}
	catch(Exception $ex)
	{
		WriteErrorLog($ex);

		$result = [
			'RESULT' => false,
			'MESSAGE' => $ex->getMessage()
		];
	}

	echo json_encode($result);
?>
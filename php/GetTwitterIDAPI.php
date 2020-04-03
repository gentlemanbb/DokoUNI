<?php
	try
	{
		// ライブラリ読み込み
		require_once "GetDataUtility.php";
		require_once "LogUtility.php";
		require_once "TweetAPI.php";

		// 引数取得
		$tweetID = $_POST['tweetID'];

		// 返し値の型設定
		header('Content-type: application/json');

		// 返し値用インスタンス
		$tweetData =  GetTweet($tweetID);
		$twitterAccountID = $tweetData->user->screen_name;

		$returnData = [
			'RESULT' => true,
			'TWITTER_ID' => $twitterAccountID
		];

		echo json_encode($returnData);
	}
	catch(Exception $ex)
	{
        WriteErrorLog($ex);

        $returnData = [
            'RESULT' => false,
            'TWITTER_ID' => null
        ];

		echo json_encode($returnData);
	}
?>
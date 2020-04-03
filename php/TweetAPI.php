<?php
	require "twitteroauth/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;
	
	// *********************************
	// * ツイートする
	// * -------------------------------
	// *
	// * 引数１：ツイートする文字列
	// *
	// *********************************
	function Tweet($str)
	{
		require_once "LogUtility.php";

		try
		{
			//認証情報４つ
			$consumerKey = "ztGK1nKVHMjtbCcKJDYBtI8z1";
			$consumerSecret = "ev7v2JJzu01Dqws4qADXK8OcZXa40jKgcqhrWSJGLU7LeBTPm1";
			$accessToken = "878436267976867840-mUDu31tM2uju7raVo4vBaWBra1ajpRV";
			$accessTokenSecret = "FtZ7vXQqje0Bzskgrw1xGHMcQbKXJBle9eMEYKhWbyfZO";
			
			//接続
			$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
			
			//ツイート
			$res = $connection->post("statuses/update", array("status" => $str));

			return $res;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);

			return null;
		}
	}

	// *********************************
	// * ツイートIDからツイートを取得する
	// * -------------------------------
	// *
	// * 引数１：取得したいツイートID
	// *
	// *********************************
	function GetTweet($tweetID)
	{
		//認証情報４つ
		$consumerKey = "ztGK1nKVHMjtbCcKJDYBtI8z1";
		$consumerSecret = "ev7v2JJzu01Dqws4qADXK8OcZXa40jKgcqhrWSJGLU7LeBTPm1";
		$accessToken = "878436267976867840-mUDu31tM2uju7raVo4vBaWBra1ajpRV";
		$accessTokenSecret = "FtZ7vXQqje0Bzskgrw1xGHMcQbKXJBle9eMEYKhWbyfZO";
		
		//接続
		$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
		
		//ツイート
		$res = $connection->get('statuses/show', array('id' => $tweetID));

		return $res;
	}

	// *********************************
	// * リツイートする
	// * -------------------------------
	// *
	// * 引数１：リツイートするツイートID
	// *
	// *********************************
	function ReTweet($tweetID)
	{
		//認証情報４つ
		$consumerKey = "ztGK1nKVHMjtbCcKJDYBtI8z1";
		$consumerSecret = "ev7v2JJzu01Dqws4qADXK8OcZXa40jKgcqhrWSJGLU7LeBTPm1";
		$accessToken = "878436267976867840-mUDu31tM2uju7raVo4vBaWBra1ajpRV";
		$accessTokenSecret = "FtZ7vXQqje0Bzskgrw1xGHMcQbKXJBle9eMEYKhWbyfZO";
		
		//接続
		$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
		
		//ツイート
		$res = $connection->post("statuses/retweet/" . $tweetID);

		return $res;
	}

	// *********************************
	// * 画像と一緒にツイートする
	// * -------------------------------
	// *
	// * 引数１：ツイートする文字列
	// * 引数２：添付する画像のURL
	// *
	// *********************************
	function TweetWithFile($str, $fileName)
	{
		//認証情報４つ
		$consumerKey = "ztGK1nKVHMjtbCcKJDYBtI8z1";
		$consumerSecret = "ev7v2JJzu01Dqws4qADXK8OcZXa40jKgcqhrWSJGLU7LeBTPm1";
		$accessToken = "878436267976867840-mUDu31tM2uju7raVo4vBaWBra1ajpRV";
		$accessTokenSecret = "FtZ7vXQqje0Bzskgrw1xGHMcQbKXJBle9eMEYKhWbyfZO";
		
		//接続
		$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
		
		// 画像のアップロード
		$mediaID = $connection->upload('media/upload', ['media' => $fileName]);
		
		// ツイート内容
		$parameters = array('status' => $str, 'media_ids' => $mediaID->media_id_string);
		
		//ツイート
		$result = $connection->post("statuses/update", $parameters);
		
		header('Content-type: application/text');
		
		return $result;
	}

	// *********************************
	// * ツイートを検索する
	// * -------------------------------
	// *
	// * 引数１：AND条件で検索するパラメータ（配列）
	// * 引数２：OR条件で検索するパラメータ（配列）
	// * 引数３：期間（From）
	// * 引数４：期間（To）
	// * 引数５：リツイートを含むか（True or False）
	// * 引数６：検索上限数
	// *
	// *********************************
	function GetTweets($andSearchParams, $orSearchParams, $since, $until, $includeRT, $count)
	{
		try
		{
			//認証情報４つ
			$consumerKey = "ztGK1nKVHMjtbCcKJDYBtI8z1";
			$consumerSecret = "ev7v2JJzu01Dqws4qADXK8OcZXa40jKgcqhrWSJGLU7LeBTPm1";
			$accessToken = "878436267976867840-mUDu31tM2uju7raVo4vBaWBra1ajpRV";
			$accessTokenSecret = "FtZ7vXQqje0Bzskgrw1xGHMcQbKXJBle9eMEYKhWbyfZO";
			
			//接続
			$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
			
			// 初期化
			$strParams = '';
			
			// ----- -------
			//  AND検索条件
			// ---- --------
			if(is_array($andSearchParams))
			{
				foreach($andSearchParams as $value)
				{
					$strParams = $strParams . '+' . $value;
				}
			}
			
			if($includeRT)
			{
				// $strParams = $strParams . '-' . '-RT';
			}
			
			// ------------
			//  OR検索条件
			// ------------
			if(is_array($orSearchParams))
			{
				foreach($orSearchParams as $value)
				{
					$strParams = $strParams . '+OR+' . $value;
				}
			}
			
			//ツイート
			$tweets = $connection->get(
				"search/tweets",
				array(
					'q' 			=>	$strParams,
					'result_type'	=>	'mixed',
					'count'			=>	$count,
					'locale'		=>	'ja'
				)
			)->statuses;
			
			return $tweets;
		}
		catch(Exception $ex)
		{
			echo '補足した例外：', $ex->getMessage(), "\n";
		}
	}
	
	// ==================================
	//  指定したユーザにＤＭを送信します。
	// ==================================
	//  引数 $sendText		： 送信する内容
	//  引数 $targetUserID	： ＤＭを送信する相手のTwitterID
	// ==================================
	function SendDM($sendText, $targetUserID)
	{
		try
		{
			//認証情報４つ
			$consumerKey = "ztGK1nKVHMjtbCcKJDYBtI8z1";
			$consumerSecret = "ev7v2JJzu01Dqws4qADXK8OcZXa40jKgcqhrWSJGLU7LeBTPm1";
			$accessToken = "878436267976867840-mUDu31tM2uju7raVo4vBaWBra1ajpRV";
			$accessTokenSecret = "FtZ7vXQqje0Bzskgrw1xGHMcQbKXJBle9eMEYKhWbyfZO";
			$url = 'https://api.twitter.com/1.1/direct_messages/events/new.json';
			//接続
			$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
			
			// Twitterから相手のユーザデータを取得
			$user_data=$connection->get("users/show",["screen_name"=>$targetUserID]);
			$twitterID = $user_data->id;
			
			// ここからコピペで使ってる
			$json = [
				'event'=>[
					'type'=>'message_create',
					'message_create'=>[
						'target'=>[
							'recipient_id'=>$twitterID
						],
						'message_data'=>[
							'text'=>$sendText,
							/*
							'attachment'=>[
								'type'=>'media',
								'id'=>[
									'...'
								]
							]
							*/
						]

					]
				]
			];
			
			$oauth_params = [
				'oauth_consumer_key'	 => $consumerKey,
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp'		=> time(),
				'oauth_version'		  => '1.0a',
				'oauth_nonce'			=> bin2hex(openssl_random_pseudo_bytes(16)),
				'oauth_token'			=> $accessToken,
			];
			
			$base = $oauth_params;
			// キー
			$key = [$consumerSecret, $accessTokenSecret];
			uksort($base, 'strnatcmp');
			
			$oauth_params['oauth_signature'] = base64_encode(hash_hmac(
				'sha1',
				implode('&', array_map('rawurlencode', array(
					'POST',
					$url,
					str_replace(
						array('+', '%7E'),
						array('%20', '~'),
						http_build_query($base, '', '&')
					)
				))),
				implode('&', array_map('rawurlencode', $key)),
				true
			));
			
			foreach ($oauth_params as $name => $value) {
				$items[] = sprintf('%s="%s"', urlencode($name), urlencode($value));
			}
			$signature = 'Authorization: OAuth ' . implode(', ', $items);
			
			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL			=> $url,
				CURLOPT_POST		   => true,
				CURLOPT_POSTFIELDS	 => json_encode($json),
				CURLOPT_HTTPHEADER	 => [
					'Content-Type: application/json',
					$signature
				],
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING	   => 'gzip',
				CURLINFO_HEADER_OUT	   => true,
			]);
			
			$response =  curl_exec($ch);
			// var_dump($response);
			// var_dump(curl_getinfo($ch));
			curl_close($ch);
			
			header('Content-type: application/text');
			
			return true;
		}
		catch(Exception $ex)
		{
			echo '補足した例外：', $ex->getMessage(), "\n";
		}
	}
?>
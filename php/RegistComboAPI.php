<?php
	// =======================
	// 処理本体
	// ======================================================================
	{
		try
		{
			// ライブラリの読み込み
			require_once "DBAccess.php";
			require_once "TweetAPI.php";
			require_once "LogUtility.php";
			require_once "Functions.php";
			require_once "GetDataUtility.php";
			require_once "RegistDataUtility.php";
			
			header('Content-type: application/json; charset=UTF-8');
			
			// 引数を取得
			$userID = $_POST['userID'];
			$characterID = $_POST['characterID'];

			$comboName = $_POST['comboName'];
			$comboRecipe = $_POST['comboRecipe'];
			$comboDamage = $_POST['comboDamage'];
			$comment = $_POST['comment'];
			$useGauge = $_POST['useGauge'];
			$gainGauge = $_POST['gainGauge'];
			$movieTweetID = $_POST['movieTweetID'];
			
			$validationResult = Validation($userID, $characterID, $comboName, $comboRecipe,
				$comboDamage, $comment, $useGauge, $gainGauge);

			$tags = $_POST['tags'];

			if(is_array($tags) && count($tags) > 0)
			{
				foreach($tags as $tag)
				{
					WriteLog('log', sprintf('[RegistComboAPI] TAGS %s', $tag));
				}
			}
			else
			{
				WriteLog('log', sprintf('[RegistComboAPI] タグはありませんでした。'));
			}

			if($validationResult['RESULT'] == false)
			{
				echo json_encode($validationResult);
				return;
			}

			// コンボ登録
			$result = RegistCombo($userID
				, $characterID
				, $comboName
				, $comboRecipe
				, $comboDamage
				, $comment
				, $useGauge
				, $gainGauge
				, $tags
				, $movieTweetID);

			// 出力
			echo json_encode($result);

			return;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);

			$returnValue = [
				'RESULT' => false,
				'MESSAGE' => '失敗'
			];

			echo json_encode($returnValue);
		}
	}

	// ==================================================================

	function Validation($userID, $characterID, $comboName, $comboRecipe,
		$comboDamage, $comment, $useGauge, $gainGauge)
	{	
		$validationResult = true;
		$errorArgs = [];
		$message = '';

		// ログインチェック
		if(!CheckLoggedIn($userID))
		{
			$validationResult = false;
			$message = '未ログインでのコンボ登録はできません';
		}
		// 入力チェック
		if($validationResult)
		{
			if ($comboName == null || $comboName == '')
			{
				$errorArgs[] = 'コンボ名（未入力）';
			}
			else if (strlen($comboName) > 50)
			{
				$errorArgs[] = 'コンボ名（50文字以内）';
			}

			if ($comboRecipe == null || $comboRecipe == '')
			{
				$errorArgs[] = 'コンボレシピ（未入力）';
			}
			else if (strlen($comboRecipe) > 256)
			{
				$errorArgs[] = 'コンボレシピ（256文字以内）';
			}

			if ($comment == null || $comment == '')
			{
				$errorArgs[] = 'コメント（未入力）';
			}
			else if (strlen($comment) > 256)
			{
				$errorArgs[] = 'コメント（256文字以内）';
			}

			if ($comboDamage == null || $comboDamage == '')
			{
				$errorArgs[] = 'コンボダメージ（未入力）';
			}
			else if (!ctype_digit($comboDamage))
			{
				$errorArgs[] = 'コンボダメージ（数値）';
			}
			
			if ($useGauge == null || $useGauge == '')
			{
				$errorArgs[] = '使用ゲージ（未入力）';
			}
			else if (!ctype_digit($useGauge))
			{
				$errorArgs[] = '使用ゲージ（数値）';
			}

			if ($gainGauge == null || $gainGauge == '')
			{
				$errorArgs[] = '増加ゲージ（未入力）';
			}
			else if (!ctype_digit($gainGauge))
			{
				$errorArgs[] = '増加ゲージ（数値）';
			}
		}
		
		// 不正な入力値の場合
		if(count($errorArgs) != 0)
		{
			// 不正
			$validationResult = false;

			// メッセージ
			$errorMessage = '入力された内容が不正です。' . "\r\n";
			foreach($errorArgs as $arg)
			{
				$errorMessage = $errorMessage . $arg . "\r\n";
			}
		}

		$returnValue = [
			'RESULT' => $validationResult,
			'MESSAGE' => $errorMessage
		];

		return $returnValue;
	}
	// ===============================
	//
	//
	// ===============================
	function RegistCombo($userID, $characterID, $comboName, $comboRecipe,
		$comboDamage, $comment, $useGauge, $gainGauge, $tags)
	{		
		// 今日の日付取得
		$logFileName = date('Y-m-d');

		try
		{
			// インジェクション対策
			$comment = preg_replace("/<script.*<\/script>/", "", $comment);

			// DBアクセサ
			$dbAccess = new DBAccess();
			$pdo = $dbAccess->DBConnect2();
			
			// 
			$query = "
				INSERT INTO D_COMBO (
					CHARACTER_ID
					, COMBO_NAME
					, COMBO_RECIPE
					, COMBO_DAMAGE
					, USE_GAUGE
					, GAIN_GAUGE
					, COMMENT
					, MOVIE_PATH
					, TOROKU_DATE
					, KOSHIN_DATE
					, TOROKU_USER
					, KOSHIN_USER
					, PRIOLITY)
				VALUES (
					:characterID
					, :comboName
					, :comboRecipe
					, :comboDamage
					, :useGauge
					, :gainGauge
					, :comment
					, :movieTweetID
					, CURRENT_TIMESTAMP()
					, CURRENT_TIMESTAMP()
					, :torokuUserID
					, :koshinUserID
					, 0)";
			
			$stmt = $pdo->prepare($query);
			
			// トランザクション
			$pdo->beginTransaction();
			$stmt->bindParam(':characterID', 	$characterID, 	PDO::PARAM_INT);
			$stmt->bindParam(':comboName', 		$comboName, 	PDO::PARAM_STR);
			$stmt->bindParam(':comboRecipe', 	$comboRecipe, 	PDO::PARAM_STR);
			$stmt->bindParam(':comboDamage', 	$comboDamage, 	PDO::PARAM_INT);
			$stmt->bindParam(':useGauge', 		$useGauge, 		PDO::PARAM_INT);
			$stmt->bindParam(':gainGauge', 		$gainGauge, 	PDO::PARAM_INT);
			$stmt->bindParam(':comment', 		$comment, 		PDO::PARAM_STR);
			$stmt->bindParam(':movieTweetID', 	$movieTweetID, 	PDO::PARAM_STR);
			$stmt->bindParam(':torokuUserID', 	$userID, 		PDO::PARAM_STR);
			$stmt->bindParam(':koshinUserID', 	$userID, 		PDO::PARAM_STR);
			
			$sqlResult = $stmt->execute();
			$comboID = $pdo->lastInsertId('id');

			if($sqlResult == True)
			{
				// テキストファイルに書き込み
				WriteLog('log', 'コンボ登録に成功しました。');

				// コンボの登録に成功した場合
				$tagID = null;

				if(is_array($tags) && count($tags) > 0)
				{
					WriteLog('log', 'タグの登録を行います。');

					// タグの数だけループ
					foreach($tags as $tag)
					{
						// タグ名で被りを検索
						$tagID = SearchTagData($tag, 2, 0);

						// 被りが見つかったらIDを取得
						if($tagID == null)
						{
							WriteLog('log', sprintf('[RegistComboAPI] タグ挿入します:%s', $tag));

							// 被りがなかった場合 タグを登録する
							$tagID = RegistTagData($tag, 2, 0);
						}
						else
						{
							WriteLog('log', sprintf('[RegistComboAPI] タグ発見:%s', $tagID));
						}

						// タグの登録をしたら
						// INSERTしたIDを使用してタグ情報も更新する
						RegistTagInfoData(2, $comboID, $tagID, $userID);
					}
				}
				else
				{
					WriteLog('log', '登録対象のタグがありませんでした。');
				}
			}
			else
			{
				// テキストファイルに書き込み
				WriteLog('log', 'コンボ登録に失敗しました。');
			}
			
			// 返し値
			$result = 
			[
				'RESULT' => False,
				'INSERT_ID' => null
			];

			if($sqlResult == True)
			{
				// コミット
				$pdo->commit();
				
				// 返し値
				$result = 
				[
					'RESULT' => True,
					'INSERT_ID' => $comboID
				];
			}
			else
			{
				// テキストファイルに書き込み
				WriteLog('log', 'コンボ登録に失敗しました。');
			}
			
			return $result;
		}
		catch(Exception $ex)
		{
			// ロールバック
			$pdo->rollBack();
			
			// エラーログを出力
			WriteErrorLog($ex);
			
			$result = [
				'RESULT' => false,
				'MESSAGE' => 'コンボ登録に失敗しました。',
				'INSERT_ID' => null
			];
			
			return $result;
		}
	}
?>
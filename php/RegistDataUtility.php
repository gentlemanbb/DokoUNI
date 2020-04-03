<?php
    /* **************************************
    // タグを登録します。
    // -------------------------------------
    // 引数：タグ名称
    // 引数：タグ種別（1:プレーヤー、2:コンボ）
    // 引数：タグカテゴリ（小分類とか）
    // 
    // ************************************** */
    function RegistTagData($tagName, $tagType, $tagCategory)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
            
			// グループの取得
			$query = sprintf("
                INSERT INTO
                    M_TAG (
                        TAG_NAME
                        , TAG_TYPE
                        , TAG_CATEGORY
                        , PRIOLITY
                    )

                VALUES(
                    :tagName
                    , :tagType
                    , :tagCategory
                    , 1
                )");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':tagName', $tagName, PDO::PARAM_STR);
			$stmt->bindParam(':tagType', $tagType, PDO::PARAM_INT);
			$stmt->bindParam(':tagCategory', $tagCategory, PDO::PARAM_INT);
            $result = $stmt->execute();
            $insertID = $pdo->lastInsertId('id');

            return $insertID;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // タグ情報を登録します。
    // -------------------------------------
    // 引数：タグ名称
    // 引数：タグ種別（1:プレーヤー、2:コンボ）
    // 引数：タグカテゴリ（小分類とか）
    // 
    // ************************************** */
    function RegistTagInfoData($tagType, $joinID, $tagID, $userID)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
            
			// グループの取得
			$query = sprintf("
                INSERT INTO
                    D_TAG_INFO (
                        TAG_TYPE
                        , JOIN_ID
                        , TAG_ID
                        , TOROKU_USER_ID
                    )

                VALUES(
                    :tagType
                    , :joinID
                    , :tagID
                    , :userID
                )");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':tagType', $tagType, PDO::PARAM_INT);
			$stmt->bindParam(':joinID', $joinID, PDO::PARAM_INT);
			$stmt->bindParam(':tagID', $tagID, PDO::PARAM_INT);
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
            $result = $stmt->execute();
            $insertID = $pdo->lastInsertId('id');

            return $insertID;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // 評価を登録します。
    // -------------------------------------
    // 引数1：対象ID
    // 引数2：評価区分
    // 引数3：値
    // 引数4：登録ユーザID
    // 引数5：コメント
    // ************************************** */
    function RegistEvalutionData($targetID, $evalutionType, $value, $userID, $comment)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
            
			// グループの取得
			$query = sprintf("
                INSERT INTO
                    D_EVALUTION (
                        TARGET_ID
                        , EVALUTION_TYPE
                        , VALUE
                        , TOROKU_USER_ID
                        , COMMENT
                    )

                VALUES(
                    :targetID
                    , :evalutionType
                    , :value
                    , :userID
                    , :comment
                )");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':targetID', $targetID, PDO::PARAM_INT);
			$stmt->bindParam(':evalutionType', $evalutionType, PDO::PARAM_INT);
			$stmt->bindParam(':value', $value, PDO::PARAM_INT);
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $result = $stmt->execute();

            $insertID = null;
            
            if($result)
            {
                $insertID = $pdo->lastInsertId('id');
            }

            return $insertID;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }
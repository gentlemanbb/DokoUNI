<?php
    /* **************************************
    // タグ情報を削除します。
    // -------------------------------------
    // 引数：タグ情報ID
    // 
    // ************************************** */
    function DeleteTagInfoData($tagInfoID)
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
                DELETE FROM
                    D_TAG_INFO
                WHERE
                    TAG_INFO_ID = :tagInfoID");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':tagInfoID', $tagInfoID, PDO::PARAM_INT);
            $result = $stmt->execute();

            return $result;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return false;
        }
    }

    /* **************************************
    // コンボを削除します。
    // -------------------------------------
    // 引数：コンボID
    // 
    // ************************************** */
    function DeleteComboData($comboID)
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
            DELETE FROM
                D_COMBO
            WHERE
                COMBO_ID = :comboID");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':comboID', $comboID, PDO::PARAM_INT);
            $result = $stmt->execute();

            return $result;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return false;
        }
    }


    /* **************************************
    // 評価を削除します。
    // -------------------------------------
    // 引数：評価ID
    // 
    // ************************************** */
    function DeleteEvalutionData($evalutionID)
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
            DELETE FROM
                D_EVALUTION
            WHERE
                EVALUTION_ID = :evalutionID");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':evalutionID', $evalutionID, PDO::PARAM_INT);
            $result = $stmt->execute();

            return $result;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return false;
        }
    }
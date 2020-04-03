<?php
    function GetTypeData($typeKey, $value)
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
				SELECT
                    CAPTION
				FROM
					M_TYPE
				WHERE
                    TYPE_KEY = :typeKey
                AND
                    VALUE = :value");

			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':typeKey', $typeKey, PDO::PARAM_STR);
			$stmt->bindParam(':value', $value, PDO::PARAM_INT);
			$stmt->execute();
            $typeDataRows = $stmt->fetchAll();

            $caption = null;

			if(is_array($typeDataRows) && count($typeDataRows) > 0)
			{
				foreach ($typeDataRows as $typeDataRow)
				{
					$caption = $typeDataRow['CAPTION'];
                }
            }

            return $caption;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    function GetPlaceDetailData($placeID)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
 
            $query = sprintf("
                SELECT
                    BASE.PLACE_NAME
                    , BASE.OFFICIAL_NAME
                    , BASE.FIX_FLG
                    , SUB.REVISION
                    , SUB.IMAGE_PATH
                FROM
                    M_PLACE BASE
                LEFT JOIN
                    D_PLACE_HISTORY SUB
                ON
                    BASE.PLACE_HISTORY_ID = SUB.PLACE_HISTORY_ID
                WHERE
                    BASE.PLACE_ID = :placeID
                ");
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':placeID', $placeID, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            
            $placeData = null;

            if(is_array($rows) && count($rows) > 0)
            {
                foreach ($rows as $row)
                {
                    $placeData = 
                    [
                        'PLACE_ID' => $placeID,
                        'PLACE_NAME' => $row['PLACE_NAME'],
                        'OFFICIAL_NAME' => $row['OFFICIAL_NAME'],
                        'FIX_FLG' => $row['FIX_FLG'],
                        'REVISION' => $row['REVISION'],
                        'IMAGE_PATH' => $row['IMAGE_PATH']
                    ];
                }
            }

            return $placeData;
        }
        catch(Exception $ex)
        {
            WriteErrorLog($ex);
            
            return null;
        }
    }

    function GetGroupDetailData($groupID)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
 
            $returnData = null;

            $query = sprintf("
			SELECT
				GROUP_ID
				, GROUP_NAME
			FROM
				D_GROUP
			WHERE
				GROUP_ID = :groupID
			");
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            
            $groupData = null;

            if(is_array($rows) && count($rows) > 0)
            {
                foreach ($rows as $row)
                {
                    $groupData = 
                    [
                        'GROUP_ID' => $groupID,
                        'GROUP_NAME' => $row['GROUP_NAME']
                    ];
                }
            }

            $query = sprintf("
                SELECT
                    BASE.USER_ID
                    , USER_NAME
                    , MANAGE_TYPE
                    , BASE.STATUS
                FROM
                    D_GROUP_USER BASE
                LEFT JOIN
                    D_GROUP SUB1
                ON 
                    BASE.GROUP_ID = SUB1.GROUP_ID
                LEFT JOIN
                    D_USER SUB2
                ON
                    BASE.USER_ID = SUB2.USER_ID
                WHERE
                    BASE.GROUP_ID = :groupID");
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $stmt->execute();
            $groupUserDataRows = $stmt->fetchAll();
            
            $groupUserData = [];

            if(is_array($groupUserDataRows) && count($groupUserDataRows) > 0)
            {
                foreach ($groupUserDataRows as $groupUserDataRow)
                {
                    $groupUserData[] = 
                    [
                        'USER_ID' => $groupUserDataRow['USER_ID'],
                        'USER_NAME' => $groupUserDataRow['USER_NAME'],
                        'MANAGE_TYPE' => $groupUserDataRow['MANAGE_TYPE'],
                        'STATUS' => $groupUserDataRow['STATUS'],
                    ];
                }
            }

            $returnData = [
                'GROUP_ID' => $groupData['GROUP_ID'],
                'GROUP_NAME' => $groupData['GROUP_NAME'],
                'GROUP_MEMBER' => $groupUserData
            ];

            return $returnData;
        }
        catch(Exception $ex)
        {
            WriteErrorLog($ex);
            
            return null;
        }
    }

    function GetAllFriendData($userID)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
 
            $query = sprintf("
            SELECT
                USER_ID
                , USER_NAME
                , TWITTER
                , STATUS
            FROM (
                    SELECT
                        CASE BASE.TOROKU_USER_ID
                            WHEN :userID1 THEN BASE.FRIEND_USER_ID
                            ELSE BASE.TOROKU_USER_ID END AS FRIEND_USER_ID
                        , STATUS
                    FROM
                        D_FRIEND BASE
                    WHERE
                        BASE.TOROKU_USER_ID = :userID2
                    OR
                        BASE.FRIEND_USER_ID = :userID3) AS BASE1
            LEFT JOIN
                D_USER SUB
            ON
                BASE1.FRIEND_USER_ID = SUB.USER_ID");
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':userID1', $userID, PDO::PARAM_STR);
            $stmt->bindParam(':userID2', $userID, PDO::PARAM_STR);
            $stmt->bindParam(':userID3', $userID, PDO::PARAM_STR);
            $stmt->execute();
            $dataRows = $stmt->fetchAll();
            
            $returnData = [];
            $friendData = [];

            if(is_array($dataRows) && count($dataRows) > 0)
            {
                foreach ($dataRows as $row)
                {
                    $friendData[] = 
                    [
                        'USER_ID' => $row['USER_ID'],
                        'USER_NAME' => $row['USER_NAME'],
                        'TWITTER' => $row['TWITTER'],
                        'STATUS' => $row['STATUS']
                    ];
                }
                
                $returnData = 
                [
                    'RESULT' => true,
                    'FRIEND_DATA' => $friendData
                ];
            }
            else 
            {
                $returnData = 
                [
                    'RESULT' => false,
                    'MESSAGE' => 'データが見つかりませんでした。'
                ];
            }

            return $returnData;
        }
        catch(Exception $ex)
        {
            WriteErrorLog($ex);
            
            return null;
        }
    }

    function GetFriendDetailData($inviteUserID, $receiveUserID)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
 
            $query = sprintf("
                SELECT
                    TOROKU_USER_ID
                    , FRIEND_USER_ID
                    , STATUS
                FROM
                    D_FRIEND
                WHERE
                    (TOROKU_USER_ID = :sendUserID1 AND FRIEND_USER_ID = :receiveUserID1)
                OR
                    (TOROKU_USER_ID = :receiveUserID2 AND FRIEND_USER_ID = :sendUserID2)
            ");
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':sendUserID1',    $inviteUserID,  PDO::PARAM_STR);
            $stmt->bindParam(':receiveUserID1', $receiveUserID, PDO::PARAM_STR);
            $stmt->bindParam(':receiveUserID2', $inviteUserID,  PDO::PARAM_STR);
            $stmt->bindParam(':sendUserID2',    $receiveUserID, PDO::PARAM_STR);
            $stmt->execute();
            $dataRows = $stmt->fetchAll();
            
            $returnData = [];
            $friendData = [];

            if(is_array($dataRows) && count($dataRows) > 0)
            {
                foreach ($dataRows as $row)
                {
                    $friendData = 
                    [
                        'TOROKU_USER_ID' => $row['TOROKU_USER_ID'],
                        'FRIEND_USER_ID' => $row['FRIEND_USER_ID'],
                        'STATUS' => $row['STATUS'],
                    ];
                }
                
                $returnData = 
                [
                    'RESULT' => true,
                    'FRIEND_DATA' => $friendData
                ];
            }
            else 
            {
                $returnData = 
                [
                    'RESULT' => false,
                    'MESSAGE' => 'データが見つかりませんでした。'
                ];
            }

            return $returnData;
        }
        catch(Exception $ex)
        {
            WriteErrorLog($ex);

            $returnData = 
            [
                'RESULT' => false,
                'MESSAGE' => $ex->getMessage()
            ];

            return returnData;
        }
    }

    function GetUserDetailData($userID)
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
				SELECT
                    USER_NAME
                    , TWITTER
				FROM
					D_USER
				WHERE
                    USER_ID = :userID");

			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
			$stmt->execute();
            $userDataRows = $stmt->fetchAll();

            $userData = null;

			if(is_array($userDataRows) && count($userDataRows) > 0)
			{
				foreach ($userDataRows as $userDataRow)
				{
					$userData = [
                        'USER_NAME' => $userDataRow['USER_NAME'],
                        'TWITTER' => $userDataRow['TWITTER']
                    ];                        
                }
            }
            else
            {
                $userData = null;
            }

            return $userData;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    function GetGroupUserID($groupID, $userID)
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
				SELECT
                    GROUP_USER_ID
				FROM
					D_GROUP_USER
				WHERE
                    USER_ID = :userID
                AND
                    GROUP_ID = :groupID");

			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
			$stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $groupUserID = null;

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$groupUserID = $dataRow['GROUP_USER_ID'];
                }
            }

            return $groupUserID;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // グループユーザー詳細データを取得します。
    // -------------------------------------
    // 引数：グループユーザーID
    // 
    // ************************************** */
    function GetGroupUserDetailData($groupUserID)
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
                SELECT
                    GROUP_ID
                    , USER_ID
                    , STATUS
                    , MANAGE_TYPE
				FROM
					D_GROUP_USER
				WHERE
                    GROUP_USER_ID = :groupUserID");

			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':groupUserID', $groupUserID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $groupUserdataData = null;

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$groupUserdataData = [
                        'GROUP_ID' => $dataRow['GROUP_USER_ID'],
                        'USER_ID' => $dataRow['USER_ID'],
                        'STATUS' => $dataRow['STATUS'],
                        'MANAGE_TYPE' => $dataRow['MANAGE_TYPE']
                    ];
                }
            }

            return $groupUserdataData;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // イベント詳細データを取得します。
    // -------------------------------------
    // 引数：イベントID
    // 
    // ************************************** */
    function GetEventDetailData($eventID)
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
                SELECT
                    PLACE_ID
                    , EVENT_NAME
                    , EVENT_DATE
                    , EVENT_TIME_FROM
                    , EVENT_TIME_TO
                    , COMMENT
                    , SPECIAL_URL
                    , TOROKU_USER_ID
                    , KOSHIN_USER_ID
                    , IMAGE_PATH
                    , DISPLAY_FLG
				FROM
					D_EVENT
				WHERE
                    EVENT_ID = :eventID");

			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $eventDetailData = null;

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$eventDetailData = [
                        'PLACE_ID' => $dataRow['PLACE_ID'],
                        'EVENT_NAME' => $dataRow['EVENT_NAME'],
                        'EVENT_DATE' => $dataRow['EVENT_DATE'],
                        'EVENT_TIME_FROM' => $dataRow['EVENT_TIME_FROM'],
                        'EVENT_TIME_TO' => $dataRow['EVENT_TIME_TO'],
                        'COMMENT' => $dataRow['COMMENT'],
                        'SPECIAL_URL' => $dataRow['SPECIAL_URL'],
                        'TOROKU_USER_ID' => $dataRow['TOROKU_USER_ID'],
                        'KOSHIN_USER_ID' => $dataRow['KOSHIN_USER_ID'],
                        'IMAGE_PATH' => $dataRow['IMAGE_PATH'],
                        'DISPLAY_FLG' => $dataRow['DISPLAY_FLG']
                    ];
                }
            }

            return $eventDetailData;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // キャラクター詳細データを取得します。
    // -------------------------------------
    // 引数：キャラクターID
    // 
    // ************************************** */
    function GetCharacterDetailData($characterID)
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
                SELECT
                    CHARACTER_NAME
                    , IMAGE_PATH
                FROM
                    D_CHARA
                WHERE
                    CHARACTER_ID = :characterID");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':characterID', $characterID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $characterData = [];

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$characterData = [
                        'CHARACTER_NAME' => $dataRow['CHARACTER_NAME'],
                        'IMAGE_PATH' => $dataRow['IMAGE_PATH']
                    ];
                }
            }

            return $characterData;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // コンボデータを取得します。
    // -------------------------------------
    // 引数：イベントID
    // 
    // ************************************** */
    function GetCharacterComboList($characterID)
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
                SELECT
                    COMBO_ID
                    , COMBO_NAME
                    , COMBO_RECIPE
                    , COMBO_DAMAGE
                    , USE_GAUGE
                    , GAIN_GAUGE
                    , BASE.COMMENT
                    , MOVIE_PATH
                    , BASE.TOROKU_DATE
                    , SUB1.USER_NAME AS TOROKU_USER_NAME
                    , BASE.TOROKU_USER AS TOROKU_USER_ID
                    , BASE.KOSHIN_DATE
                    , SUB2.USER_NAME AS KOSHIN_USER_NAME
                    , SUB3.CHARACTER_NAME
                    , (SELECT COUNT(VALUE) FROM D_EVALUTION WHERE TARGET_ID = BASE.COMBO_ID AND VALUE = 1) AS EVALUTION_GOOD
                    , (SELECT COUNT(VALUE) FROM D_EVALUTION WHERE TARGET_ID = BASE.COMBO_ID AND VALUE = 2) AS EVALUTION_BAD
                    , GROUP_CONCAT(SUB5.TAG_NAME) AS TAGS
                FROM
                    D_COMBO BASE
                LEFT JOIN
                    D_USER SUB1
                ON
                    BASE.TOROKU_USER = SUB1.USER_ID
                LEFT JOIN
                    D_USER SUB2
                ON
                    BASE.KOSHIN_USER = SUB2.USER_ID
                LEFT JOIN
                    D_CHARA SUB3
                ON
                    BASE.CHARACTER_ID = SUB3.CHARACTER_ID
                LEFT JOIN
                    D_TAG_INFO SUB4
                ON
                    SUB4.TAG_TYPE = '2'
                AND
                    BASE.COMBO_ID = SUB4.JOIN_ID
                LEFT JOIN
                    M_TAG SUB5
                ON
                    SUB4.TAG_ID = SUB5.TAG_ID
                WHERE
                    BASE.CHARACTER_ID = :characterID
                GROUP BY
                    BASE.COMBO_ID
                ORDER BY
                    EVALUTION_GOOD DESC");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':characterID', $characterID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $comboDataList = [];

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
                    $goodValue = 0;
                    $badValue = 0;

                    if($dataRow['EVALUTION_GOOD'] != null)
                    {
                        $goodValue = $dataRow['EVALUTION_GOOD'];
                    }

                    if($dataRow['EVALUTION_BAD'] != null)
                    {
                        $badValue = $dataRow['EVALUTION_BAD'];
                    }

					$comboDataList[] = [
                        'COMBO_ID' => $dataRow['COMBO_ID'],
                        'COMBO_NAME' => $dataRow['COMBO_NAME'],
                        'COMBO_RECIPE' => $dataRow['COMBO_RECIPE'],
                        'COMBO_DAMAGE' => $dataRow['COMBO_DAMAGE'],
                        'CHARACTER_NAME' => $dataRow['CHARACTER_NAME'],
                        'USE_GAUGE' => $dataRow['USE_GAUGE'],
                        'GAIN_GAUGE' => $dataRow['GAIN_GAUGE'],
                        'COMMENT' => $dataRow['COMMENT'],
                        'EVALUTION_GOOD' => $goodValue,
                        'EVALUTION_BAD' => $badValue,
                        'MOVIE_PATH' => $dataRow['MOVIE_PATH'],
                        'TOROKU_DATE' => $dataRow['TOROKU_DATE'],
                        'TOROKU_USER_NAME' => $dataRow['TOROKU_USER_NAME'],
                        'TOROKU_USER_ID' => $dataRow['TOROKU_USER_ID'],
                        'KOSHIN_DATE' => $dataRow['KOSHIN_DATE'],
                        'KOSHIN_USER_NAME' => $dataRow['KOSHIN_USER_NAME'],
                        'TAGS' => $dataRow['TAGS']
                    ];
                }
            }

            return $comboDataList;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    
    /* **************************************
    // コンボデータを取得します。
    // -------------------------------------
    // 引数：イベントID
    // 
    // ************************************** */
    function GetComboDetailData($comboID)
    {
		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			
			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();
            
            //  ****************** タグ取得 ********************
			$query = sprintf("
                SELECT
                    BASE.TAG_INFO_ID AS TAG_INFO_ID
                    , SUB.TAG_NAME
                FROM
                    D_TAG_INFO AS BASE
                LEFT JOIN
                    M_TAG AS SUB
                ON
                    BASE.TAG_ID = SUB.TAG_ID
                WHERE
                    BASE.TAG_TYPE = 2
                AND
                    BASE.JOIN_ID = :comboID");
        
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':comboID', $comboID, PDO::PARAM_INT);
            $stmt->execute();
            $tagRows = $stmt->fetchAll();

            $tagData = [];

            if(is_array($tagRows) && count($tagRows) > 0)
            {
                foreach ($tagRows as $tagRow)
                {
                    $tagData[] = [
                        'TAG_INFO_ID' => $tagRow['TAG_INFO_ID'],
                        'TAG_NAME' => $tagRow['TAG_NAME']
                    ];

                    WriteLog('log', sprintf('[GetDataUtility] TAG_INFO_ID=%s, TAG_NAME=%s', $tagRow['TAG_INFO_ID'], $tagRow['TAG_NAME']));
                }
            }

			// ************************* コンボの取得 *********************
			$query = sprintf("
                SELECT
                    COMBO_ID
                    , CHARACTER_ID
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
                    , PRIOLITY
                FROM
                    D_COMBO BASE
                WHERE
                    COMBO_ID = :comboID");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':comboID', $comboID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $comboData = [];

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$comboData = [
                        'COMBO_ID' => $dataRow['COMBO_ID'],
                        'CHARACTER_ID' => $dataRow['CHARACTER_ID'],
                        'COMBO_NAME' => $dataRow['COMBO_NAME'],
                        'COMBO_RECIPE' => $dataRow['COMBO_RECIPE'],
                        'COMBO_DAMAGE' => $dataRow['COMBO_DAMAGE'],
                        'USE_GAUGE' => $dataRow['USE_GAUGE'],
                        'GAIN_GAUGE' => $dataRow['GAIN_GAUGE'],
                        'COMMENT' => $dataRow['COMMENT'],
                        'MOVIE_PATH' => $dataRow['MOVIE_PATH'],
                        'TOROKU_DATE' => $dataRow['TOROKU_DATE'],
                        'TOROKU_USER_ID' => $dataRow['TOROKU_USER'],
                        'KOSHIN_DATE' => $dataRow['KOSHIN_DATE'],
                        'KOSHIN_USER_ID' => $dataRow['KOSHIN_USER_NAME'],
                        'PRIOLITY' => $dataRow['PRIOLITY']
                    ];
                }
            }

			// ************************* 評価の取得 *********************
			$query = sprintf("
                SELECT
                    COUNT(1) AS GOOD
                FROM
                    D_EVALUTION
                WHERE
                    EVALUTION_TYPE = 0
                AND
                    TARGET_ID = :comboID1
                AND
                    VALUE = 1
                
                UNION ALL
                
                SELECT
                    COUNT(1) AS BAD
                FROM
                    D_EVALUTION
                WHERE
                    EVALUTION_TYPE = 0
                AND
                    TARGET_ID = :comboID2
                AND
                    VALUE = 2;");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':comboID1', $comboID, PDO::PARAM_INT);
			$stmt->bindParam(':comboID2', $comboID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $evalData = [];

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$evalData = [
                        'GOOD' => $dataRow['GOOD'],
                        'BAD' => $dataRow['BAD']
                    ];
                }
            }


            $returnData = [
                'COMBO_DATA' => $comboData,
                'TAG_DATA' => $tagData,
                'EVALUTION_DATA' => $evalData
            ];

            return $returnData;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // タグデータを取得します。
    // -------------------------------------
    // 引数：タグ区分
    // 
    // ************************************** */
    function GetTagsData($tagType)
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
                SELECT
                    TAG_ID
                    , TAG_NAME
                FROM
                    M_TAG
                WHERE
                    TAG_TYPE = :tagType
                ORDER BY
                    PRIOLITY");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':tagType', $tagType, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $tagData = [];

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$tagData[] = [
                        'TAG_ID' => $dataRow['TAG_ID'],
                        'TAG_NAME' => $dataRow['TAG_NAME']
                    ];
                }
            }

            return $tagData;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // タグ名でタグを取得します。
    // -------------------------------------
    // 引数1：タグ名
    // 引数2：タグ区分
    // 引数3：タグカテゴリ
    // 
    // ************************************** */
    function SearchTagData($tagName, $tagType, $tagCategory)
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
                SELECT
                    TAG_ID
                FROM
                    M_TAG
                WHERE
                    TAG_NAME = :tagName
                AND
                    TAG_TYPE = :tagType
                AND
                    TAG_CATEGORY = :tagCategory");
            
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':tagName', $tagName, PDO::PARAM_STR);
			$stmt->bindParam(':tagType', $tagType, PDO::PARAM_INT);
			$stmt->bindParam(':tagCategory', $tagCategory, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $tagID = null;

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$tagID = $dataRow['TAG_ID'];
                }
            }

            return $tagID;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // 場所データを取得
    // -------------------------------------
    // 引数：イベントID
    // 
    // ************************************** */
    function GetCurrentPlaceData($placeID)
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
                SELECT
                    SUB.PLACE_NAME
                    , BASE.OFFICIAL_NAME
                    , BASE.FIX_FLG
                    , SUB.ADDRESS
                    , SUB.COMMENT
                    , IMAGE_PATH
                    , KOSHIN_USER_ID
                    , KOSHIN_DATETIME
                FROM
                    M_PLACE BASE
                LEFT JOIN
                    D_PLACE_HISTORY SUB
                ON
                    BASE.PLACE_ID = SUB.PLACE_ID
                WHERE
                    BASE.PLACE_ID = :placeID");
                
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':placeID', $placeID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $placeDataList = [];

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{              
					$placeDataList[] = [
                        'PLACE_NAME' => $dataRow['PLACE_NAME'],
                        'OFFICIAL_NAME' => $dataRow['OFFICIAL_NAME'],
                        'FIX_FLG' => $dataRow['FIX_FLG'],
                        'ADDRESS' => $dataRow['ADDRESS'],
                        'COMMENT' => $dataRow['COMMENT'],
                        'IMAGE_PATH' => $dataRow['IMAGE_PATH'],
                        'KOSHIN_USER_ID' => $dataRow['KOSHIN_USER_ID'],
                        'KOSHIN_DATETIME' => $dataRow['KOSHIN_DATETIME']
                    ];
                }
            }

            return $placeDataList;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }


    /* **************************************
    // 場所履歴を取得します。
    // -------------------------------------
    // 引数：イベントID
    // 
    // ************************************** */
    function GetPlaceHistoryID($placeID)
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
                SELECT
                    PLACE_HISTORY_ID
                FROM
                    M_PLACE
                WHERE
                    PLACE_ID = :placeID");
                
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':placeID', $placeID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

            $placeHistoryID = null;

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{              
					$placeHistoryID = $dataRow['PLACE_HISTORY_ID'];
                }
            }

            return $placeHistoryID;
		}
		catch(Exception $ex)
		{
			WriteErrorLog($ex);
			
			return null;
        }
    }

    /* **************************************
    // お気に入りデータを取得します。
    // -------------------------------------
    // 引数１：ユーザID
    // 引数２：お気に入り区分
    // 引数３：キーID
    // ************************************** */
    function GetFavoriteID($userID, $favoriteType, $keyID)
    {
        $favoriteID = null;

		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			require_once "Constants.php";

			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();

            $tableName = '';
            $keyIDName = '';

            if($favoriteType == FAVORITE_TYPE::PLACE)
            {
                $tableName = 'D_FAVORITE_PLACE';
                $keyIDName = 'PLACE_ID';
            }
            else if($favoriteType == FAVORITE_TYPE::USER)
            {
                $tableName =~ 'D_FAVORITE_USER';
                $keyIDName = 'USER_ID';
            }
            else
            {
                throw new Exception('お気に入り区分が未指定です。');
            }

			// グループの取得
            $query = sprintf("
                SELECT
                    FAVORITE_ID
                FROM
                    %s
                WHERE
                    %s = :keyID
                AND
                    USER_ID = :userID"
                , $tableName
                , $keyIDName);
                
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':keyID', $keyID, PDO::PARAM_INT);
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{              
					$favoriteID = $dataRow['FAVORITE_ID'];
                }
            }
		}
		catch(Exception $ex)
		{
            WriteErrorLog($ex);
        }

        return $favoriteID;
    }

    /* **************************************
    // お気に入り登録済みのユーザー一覧を取得します。
    // -------------------------------------
    // 引数２：お気に入り区分
    // 引数３：キーID
    // ************************************** */
    function GetFavoriteUserDataArray($favoriteType, $keyID)
    {
        $idArray[] = null;

		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			require_once "Constants.php";

			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();

            $tableName = '';
            $keyIDName = '';

            if($favoriteType == FAVORITE_TYPE::PLACE)
            {
                $tableName = 'D_FAVORITE_PLACE';
                $keyIDName = 'PLACE_ID';
                $columnName = 'USER_ID';
            }
            else if($favoriteType == FAVORITE_TYPE::USER)
            {
                $tableName =~ 'D_FAVORITE_USER';
                $keyIDName = 'USER_ID';
                $columnName = 'TARGET_USER_ID';
            }
            else
            {
                throw new Exception('お気に入り区分が未指定です。');
            }

			// グループの取得
            $query = sprintf("
                SELECT
                    %s
                FROM
                    %s
                WHERE
                    %s = :keyID"
                , $columnName
                , $tableName
                , $keyIDName);
                
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':keyID', $keyID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$idArray[] = $dataRow[$columnName];
                }
            }
            else
            {
                return null;
            }
		}
		catch(Exception $ex)
		{
            WriteErrorLog($ex);
        }

        return $idArray;
    }

    
    /* **************************************
    // 評価データが存在するか取得します。
    // -------------------------------------
    // 引数１：評価区分
    // 引数２：対象ID
    // 引数３：ユーザID
    // ************************************** */
    function GetEvalutionID($evalutionType, $targetID, $userID)
    {
        $evalutionData = null;

		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			require_once "Constants.php";

			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();

			// グループの取得
            $query = sprintf("
                SELECT
                    EVALUTION_ID
                    , VALUE
                FROM
                    D_EVALUTION
                WHERE
                    EVALUTION_TYPE = :evalutionType
                AND
                    TARGET_ID = :targetID
                AND
                    TOROKU_USER_ID = :userID");
                
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':evalutionType', $evalutionType, PDO::PARAM_INT);
			$stmt->bindParam(':targetID', $targetID, PDO::PARAM_INT);
			$stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

			if(is_array($dataRows) && count($dataRows) > 0)
			{
				foreach ($dataRows as $dataRow)
				{
					$evalutionData = [
                        'EVALUTION_ID' => $dataRow['EVALUTION_ID'],
                        'VALUE' => $dataRow['VALUE']
                    ];
                }
            }
            else
            {
                return null;
            }
		}
		catch(Exception $ex)
		{
            WriteErrorLog($ex);
        }

        return $evalutionData;
    }


    /* **************************************
    // 対象コンボの評価を取得します。
    // -------------------------------------
    // 引数１：評価区分
    // 引数２：対象ID
    // ************************************** */
    function GetComboEvalutionData($evalutionType, $targetID)
    {
        $evalutionData = null;

		try
		{
			// ライブラリ読み込み
			require_once "DBAccess.php";
			require_once "LogUtility.php";
			require_once "Constants.php";

			// DB接続
			$dbAccess = new DBAccess();
            $pdo = $dbAccess->DBConnect2();

			// グループの取得
            $query = sprintf("
                SELECT
                    COUNT(1) AS EVALUTION_VALUE
                FROM
                    D_EVALUTION
                WHERE
                    EVALUTION_TYPE = :evalutionType1
                AND
                    TARGET_ID = :targetID1
                AND
                    VALUE = 1
                
                UNION ALL
                
                SELECT
                    COUNT(1) AS EVALUTION_VALUE
                FROM
                    D_EVALUTION
                WHERE
                    EVALUTION_TYPE = :evalutionType2
                AND
                    TARGET_ID = :targetID2
                AND
                    VALUE = 2
            ");
                
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':evalutionType1', $evalutionType, PDO::PARAM_INT);
			$stmt->bindParam(':targetID1', $targetID, PDO::PARAM_INT);
			$stmt->bindParam(':evalutionType2', $evalutionType, PDO::PARAM_INT);
			$stmt->bindParam(':targetID2', $targetID, PDO::PARAM_INT);
			$stmt->execute();
            $dataRows = $stmt->fetchAll();

			if(is_array($dataRows) && count($dataRows) > 0)
			{
                $isFirstRow = true;
                $goodValue = 0;
                $badValue = 0;

				foreach ($dataRows as $dataRow)
				{
                    if($isFirstRow)
                    {
                        // 1行目
                        if($dataRow['EVALUTION_VALUE'] != null)
                        {
                            $goodValue = $dataRow['EVALUTION_VALUE'];
                        }

                        $isFirstRow = false;
                    }
                    else
                    {
                        // 2行目
                        if($dataRow['EVALUTION_VALUE'] != null)
                        {
                            $badValue = $dataRow['EVALUTION_VALUE'];
                        }
                    }
                }
                
				$evalutionData = [
                    'EVALUTION_GOOD' => $goodValue,
                    'EVALUTION_BAD' => $badValue
                ];
            }
            else
            {
                return null;
            }
		}
		catch(Exception $ex)
		{
            WriteErrorLog($ex);
        }

        return $evalutionData;
    }
?>
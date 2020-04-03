<?php
    try
    {
		// ライブラリ読み込み
		require "DBAccess.php";
        require "LogUtility.php";
        require "GetDataUtility.php";

        header('Content-type: application/json');

		// DB接続
		$dbAccess = new DBAccess();
        $pdo = $dbAccess->DBConnect2();
        
        // 引数取得
        $placeID = $_POST['placeID'];
        
        // データを取得
        $placeDataList = GetCurrentPlaceData($placeID);

        // 返却値
        $returnData = [];

        if(is_array($placeDataList) && count($placeDataList) > 0)
        {
            $placeData = null;

            foreach ($placeDataList as $data)
            {
                $placeData = [
                    'PLACE_NAME' => $data['PLACE_NAME'],
                    'OFFICIAL_NAME' => $data['OFFICIAL_NAME'],
                    'FIX_FLG' => $data['FIX_FLG'],
                    'ADDRESS' => $data['ADDRESS'],
                    'COMMENT' => $data['COMMENT'],
                    'IMAGE_PATH' => $data['IMAGE_PATH'],
                    'KOSHIN_USER_ID' => $data['KOSHIN_USER_ID'],
                    'KOSHIN_DATETIME' => $data['KOSHIN_DATETIME']
                ];
            }

            $returnData = [
                'RESULT' => true,
                'MESSAGE' => '',
                'PLACE_DATA' => $placeData
            ];
        }
        else
        {
            $returnData = [
                'RESULT' => true,
                'MESSAGE' => 'データが存在しませんでした。',
                'PLACE_DATA' => null
            ];
        }

        echo json_encode($returnData);

        return;
    }
    catch(Exception $ex)
    {
        $result = [
            'RESULT' => false,
			'MESSAGE' => $ex->getMessage(),
			'PLACE_DATA' => null
		];
		
        echo json_encode($result);
    }
?>
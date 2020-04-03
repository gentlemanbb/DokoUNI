<?php
    // ライブラリ読み込み
    require "DBAccess.php";
    require "GetDataUtility.php";
    require "DeleteDataUtility.php";

    header('Content-type: application/json');

    // 引数
    $comboID = $_POST['comboID'];
    $userID = $_POST['userID'];

    try
    {
        $dbAccess = new DBAccess();
        $pdo = $dbAccess->DBConnect2();

        $comboData = GetComboDetailData($comboID);

        if($comboData['USER_ID'] != $userID)
        {
            $result = [
                'RESULT' => false,
                'MESSAGE' => '登録者以外は削除できません。'
            ];
                    
            echo json_encode($result);
            return;
        }

        if (($comboData['MOVIE_PATH'] != null && $comboData['MOVIE_PATH'] != '')
            && file_exists('../' . $comboData['MOVIE_PATH']))
        {
			// ファイルが存在した場合は削除する
			if(unlink('../' . $comboData['MOVIE_PATH']))
			{
				WriteLog('DeleteCombo', 'ファイルの削除に成功。' . "\n");
			}
			else
			{
				// 失敗を返す
				WriteLog('DeleteCombo', 'ファイルの削除に失敗。' . "\n");
						
				$result = [
					'RESULT' => false,
					'MESSAGE' => '現在のアイコンの削除に失敗しました。'
				];
						
				echo json_encode($result);
						
				return;
			}
        }

        $comboDetailData = GetComboDetailData($comboID);
        $tagDataList = $comboDetailData['TAG_DATA'];

        $deleteResult = DeleteComboData($comboID);

        foreach($tagDataList as $tagData)
        {
            $tagInfoID = $tagData['TAG_INFO_ID'];
            $deleteResult = DeleteTagInfoData($tagInfoID);
        }

        if($deleteResult == True)
        {
            $returnData = [
                'RESULT' => true
            ];
    
            echo json_encode($returnData);
        }
        else
        {
            $returnData = [
                'RESULT' => false,
                'MESSAGE' => '削除に失敗しました。'
            ];

            echo json_encode($returnData);
        }
    }
    catch(Exception $ex)
    {
        WriteErrorLog($ex);
        
        $returnData = [
            'RESULT' => false,
            'MESSAGE' => '削除に失敗しました。'
        ];

        echo json_encode($returnData);
    }
?>
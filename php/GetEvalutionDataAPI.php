<?php
    $result = [
        'RESULT' => false,
        'MESSAGE' => '取得に失敗しました。'
    ];

    try
    {
        require_once "DBAccess.php";
        require_once "TweetAPI.php";
        require_once "LogUtility.php";
        require_once "GetDataUtility.php";

        header('Content-type: application/json');
        
        $dbAccess = new DBAccess();
        $pdo = $dbAccess->DBConnect2();
        
        // 引数
        $targetID = $_POST['targetID'];
        $evalutionType = $_POST['evalutionType'];
        WriteLog('log', sprintf('【GetEvalutionDataAPI】targetID:[%s], evalType[%s]', $targetID, $evalutionType));
        
        $evalutionData = GetComboEvalutionData($evalutionType, $targetID);
        
        $result = [
            'RESULT' => true,
            'MESSAGE' => '取得に成功しました。',
            'EVALUTION_DATA' => $evalutionData
        ];    
    }
    catch(Exception $ex)
    {
        WriteErrorLog($ex);
    }
    
    echo json_encode($result);
    return;
?>
<?php
    // ライブラリ読み込み
    require "DBAccess.php";
    require "LogUtility.php";

    // 引数取得
    $key = $_POST['key'];

    header('Content-type: application/json');

    // DB接続
    $dbAccess = new DBAccess();
    $pdo = $dbAccess->DBConnect2();

    $query = sprintf("
        SELECT
            CAPTION
            , VALUE
        FROM
            M_TYPE
        WHERE
            TYPE_KEY = :key
        ORDER BY
            SEQ_NO ASC");
		
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':key', $key, PDO::PARAM_STR);
    $stmt->execute();
    $dataRows = $stmt->fetchAll();

    $returnData = [];
    $typeData = [];

    if(is_array($dataRows) && count($dataRows) > 0)
    {
        foreach ($dataRows as $row)
        {
            $typeData[] = 
            [
                'CAPTION' => $row['CAPTION'],
                'VALUE' => $row['VALUE']
            ];
        }

        $returnData = 
        [
            'RESULT' => true,
            'TYPE_DATA' => $typeData
        ];
    }
    else
    {
        $returnData = 
        [
            'RESULT' => false,
            'MESSAGE' => sprintf('%s のデータが取得できませんでした。')
        ];
    }

    echo json_encode($returnData);
?>
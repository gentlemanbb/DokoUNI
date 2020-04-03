<?php

    try
    {
        header('Content-type: application/json');

        require_once "DBAccess.php";
        require_once "LogUtility.php";
        require_once "RegistDataUtility.php";
        require_once "DeleteDataUtility.php";
        require_once "GetDataUtility.php";

        WriteLog('log', sprintf('[UpdateComboAPI] 呼び出し開始'));

        // 引数
        $comboID = $_POST['comboID'];
        $comboName = $_POST['comboName'];
        $comboDamage = $_POST['comboDamage'];
        $useGauge = $_POST['useGauge'];
        $gainGauge = $_POST['gainGauge'];
        $comboRecipe = $_POST['comboRecipe'];
        $comment = $_POST['comment'];
        $userID = $_POST['userID'];
        $tags = $_POST['tags'];
        $movieTweetID = $_POST['movieTweetID'];

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

        $dbAccess = new DBAccess();
        $pdo = $dbAccess->DBConnect2();

        $query = sprintf("
            UPDATE
                D_COMBO
            SET
                COMBO_NAME = :comboName
                , COMBO_DAMAGE = :comboDamage
                , USE_GAUGE = :useGauge
                , GAIN_GAUGE = :gainGauge
                , COMBO_RECIPE = :comboRecipe
                , COMMENT = :comment
                , KOSHIN_USER = :userID
                , KOSHIN_DATE = CURRENT_TIMESTAMP()
                , MOVIE_PATH = :movieTweetID

            WHERE COMBO_ID = :comboID");

        $stmt = $pdo->prepare($query);
        $pdo->beginTransaction();
        $stmt->bindParam(':comboName',      $comboName,     PDO::PARAM_STR);
        $stmt->bindParam(':comboDamage',    $comboDamage,   PDO::PARAM_INT);
        $stmt->bindParam(':useGauge',       $useGauge,      PDO::PARAM_INT);
        $stmt->bindParam(':gainGauge',      $gainGauge,     PDO::PARAM_INT);
        $stmt->bindParam(':comboRecipe',    $comboRecipe,   PDO::PARAM_STR);
        $stmt->bindParam(':comment',        $comment,       PDO::PARAM_STR);
        $stmt->bindParam(':userID',         $userID,        PDO::PARAM_STR);
        $stmt->bindParam(':movieTweetID',   $movieTweetID,  PDO::PARAM_STR);
        $stmt->bindParam(':comboID',        $comboID,       PDO::PARAM_INT);
        $result = $stmt->execute();

        if($result == true)
        {
            // コンボ情報を取得する
            $comboDetailData = GetComboDetailData($comboID);

            // コンボ情報からタグリストを取得
            $tagDataList = $comboDetailData['TAG_DATA'];

            DeleteTagInfo($tagDataList);

            if(is_array($tags) && count($tags) > 0)
            {
                WriteLog('log', '[UpdateComboAPI] タグの登録を行います。');

                // タグの数だけループ
                foreach($tags as $tag)
                {
                    // タグ名で被りを検索
                    $tagID = SearchTagData($tag, 2, 0);

                    // 被りが見つかったらIDを取得
                    if($tagID == null)
                    {
                        WriteLog('log', sprintf('[UpdateComboAPI] タグ挿入します:%s', $tag));

                        // 被りがなかった場合 タグを登録する
                        $tagID = RegistTagData($tag, 2, 0);
                    }
                    else
                    {
                        WriteLog('log', sprintf('[UpdateComboAPI] タグ発見:%s', $tagID));
                    }

                    // タグの登録をしたら
                    // INSERTしたIDを使用してタグ情報も更新する
                    RegistTagInfoData(2, $comboID, $tagID, $userID);
                }
            }
            else
            {
                WriteLog('log', '[UpdateComboAPI] 登録対象のタグがありませんでした。');
            }

            // コミット
            $pdo->commit();

            $returnResult = [
                'RESULT' => true,
                'MESSAGE' => 'コンボの更新に成功しました。'
            ];
    
            echo json_encode($returnResult);
        }
        else
        {
            $returnResult = [
                'RESULT' => false,
                'MESSAGE' => 'コンボの更新に失敗しました。'
            ];

            echo json_encode($returnResult);
        }
    }
    catch(Exception $ex)
    {
        // エラーログ出力
        WriteErrorLog($ex);

        $result = [
            'RESULT' => false,
            'MESSAGE' => 'コンボの更新に失敗しました。'
        ];

        echo json_encode($result);
    }

    // -------------------
    //  タグデータを削除する
    //
    // -------------------
    function DeleteTagInfo($tagInfoDataList)
    {

        if(is_array($tagInfoDataList) && count($tagInfoDataList) > 0)
        {
            // 既存のタグを消す
            WriteLog('log', sprintf('[UpdateComboAPI] コンボのタグを削除します。', $comboID));

            // タグが1件以上存在した場合
            // タグリストから１つずつ消す
            foreach($tagInfoDataList as $tagData)
            {
                $tagInfoID = $tagData['TAG_INFO_ID'];
                $tagName = $tagData['TAG_NAME'];

                // 既存のタグを消す
                WriteLog('log', sprintf('[UpdateComboAPI] ID [%s], タグ名 [%s] のタグ情報を削除します。', $tagInfoID, $tagName));

                $deleteResult = DeleteTagInfoData($tagInfoID);
            }
        }
    }
?>
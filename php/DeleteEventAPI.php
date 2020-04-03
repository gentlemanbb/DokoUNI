<?php
    require_once "DBAccess.php";
    require_once "LogUtility.php";
    header('Content-type: application/json');

    // 引数
    $eventID = $_POST['eventID'];

    try
    {
        $dbAccess = new DBAccess();
        $pdo = $dbAccess->DBConnect2();

        $query = sprintf("
            DELETE FROM D_EVENT
            WHERE EVENT_ID = :eventID");

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':eventID', $eventID, PDO::PARAM_STR);
        $result = $stmt->execute();

        if($result == True)
        {
            echo json_encode(True);
        }
        else
        {
            echo json_encode(False);
        }
    }
    catch(Exception $ex)
    {
        WriteErrorLog($ex);
        echo json_encode(False);
    }
?>
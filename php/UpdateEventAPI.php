<?php
    require "DBAccess.php";

    // 引数
    $eventID = $_POST['eventID'];
    $userID = $_POST['userID'];
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['eventDate'];
    $placeID = $_POST['placeID'];
    $eventTimeFrom = $_POST['eventTimeFrom'];
    $eventTimeTo = $_POST['eventTimeTo'];
    $comment = $_POST['comment'];

    $dbAccess = new DBAccess();
    $pdo = $dbAccess->DBConnect2();

    $query = sprintf("
        UPDATE D_EVENT SET
            EVENT_NAME = :eventName
            , PLACE_ID = :placeID
            , EVENT_DATE = :eventDate
            , EVENT_TIME_FROM = :eventTimeFrom
            , EVENT_TIME_TO = :eventTimeTo
            , COMMENT = :comment
            , KOSHIN_USER_ID = :userID

        WHERE EVENT_ID = :eventID");

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':eventName', $eventName, PDO::PARAM_STR);
    $stmt->bindParam(':placeID', $placeID, PDO::PARAM_STR);
    $stmt->bindParam(':eventDate', $eventDate, PDO::PARAM_STR);
    $stmt->bindParam(':eventTimeFrom', $eventTimeFrom, PDO::PARAM_STR);
    $stmt->bindParam(':eventTimeTo', $eventTimeTo, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':eventID', $eventID, PDO::PARAM_STR);
    $result = $stmt->execute();

    if($result == True){
        echo json_encode(True);
    }
    else{
        echo json_encode(False);
    }
?>
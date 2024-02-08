<?php
    include_once "include/config/database.php";

    function exception_handler($exception) {
        // header("Location: /error.php?error=" . urlencode("PHP error"));
        throw $exception;
    }
      
    set_exception_handler('exception_handler');

    $databaseConnection = new mysqli($servername, $username, $password, $dbname);

    if ($databaseConnection->connect_error) {
        die("Connection failed: " . $databaseConnection->connect_error);
    }

    function error($message) {
        header("Location: /error.php?error=" . urlencode($message));
        die($message);
    }

    function cleanupSessions($db) {
        $deleteOldSessionsStatement = $db->prepare("delete from user_sessions where timestamp < now() - interval 1 day");
        $deleteOldSessionsStatement->execute();
        $deleteOldSessionsStatement->close();
    }

    cleanupSessions($databaseConnection);
?>
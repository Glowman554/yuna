<?php
    include_once "include/user.php";

    if (isset($_GET["url"]) && isset($_GET["id"])) {
        $url = $_GET["url"];
        $id = (int)$_GET["id"];
 
        if ($username && $userinfo->keep_history) {
            $searchHistoryStatement = $databaseConnection->prepare("insert into click_history (username, site_id) values (?, ?)");
            $searchHistoryStatement->bind_param("si", $username, $id);
            $searchHistoryStatement->execute();
            $searchHistoryStatement->close();
        }

        header("Location: " . $url);
    }

    $databaseConnection->close();
?>
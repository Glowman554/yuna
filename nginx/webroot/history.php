<?php
    include_once "include/user.php";
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";
    
    $results = array();

    if (isset($_GET["page"])) {
        $page = (int) $_GET["page"];
    } else {
        $page = 0;
    }

    $limit = 25;
    $offset = $limit * $page;


    class HistoryResult {
        public $timestamp;
        public $text;

        function __construct($timestamp, $text) {
            $this->timestamp = $timestamp;
            $this->text = $text;
        }
    }

    if (isset($_GET["mode"])) {
        $mode = $_GET["mode"];

        if ($username) {

            switch ($mode) {
                case "search":
                    {
                        $searchHistoryStatement = $databaseConnection->prepare("select search, time from search_history where username = ? order by time desc limit ? offset ?");
                        $searchHistoryStatement->bind_param("sii", $username, $limit, $offset);

                        $searchHistoryStatement->execute();
                        $searchResult = $searchHistoryStatement->get_result();


                        $row = $searchResult->fetch_assoc();
                        while ($row) {
                            array_push($results, new HistoryResult(strtotime($row["time"]), $row["search"]));
                            $row = $searchResult->fetch_assoc();
                        }

                        $searchResult->close();
                        $searchHistoryStatement->close();
                    }
                    break;
                case "click":
                    {
                        $clickHistoryStatement = $databaseConnection->prepare("select title, link, time from sites, click_history where sites.site_id = click_history.site_id and username = ? order by time desc limit ? offset ?");
                        $clickHistoryStatement->bind_param("sii", $username, $limit, $offset);

                        $clickHistoryStatement->execute();
                        $clickResult = $clickHistoryStatement->get_result();

                        $row = $clickResult->fetch_assoc();
                        while ($row) {
                            array_push($results, new HistoryResult(strtotime($row["time"]), "<a href=\"" . $row["link"] . "\">" . $row["title"] . "</a>"));
                            $row = $clickResult->fetch_assoc();
                        }

                        $clickResult->close();
                        $clickHistoryStatement->close();
                    }
                    break;
            }
        } else {
            error("You need to be logged in");
        } 
    }

    $databaseConnection->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/searched.css">
	<link rel="stylesheet" href="style/search-bar.css">

    <?php
        echo "<title>ユウナ - History</title>";
    ?>
    <?php echo $unsplashwidget ?>
    <?php include_once "include/widgets/onionwidget.php" ?>
</head>

<body>
    <?php echo $userwidget ?>

	<header>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>
	</header>
	<main>
        <form action="search.php" id="search_form">
            <input spellcheck="false" autocorrect="on" title="Enter your search" type="search" inputmode="search" id="search" name="search">
        </form>

		<div class="results" id="results" style="background-color: var(--background);">
            <?php
                foreach ($results as $result) {
                    echo "<div class=\"resultContainer\">";
                    echo "<p>";

                    echo date("Y-m-d H:i:s", $result->timestamp) . ": " . $result->text;
                    
                    echo "</p>";
                    echo "</div>";
                }
            ?>

            <?php
                if ($page > 0) {
                    echo "<a href=\"/history.php?page=" . ($page - 1) . "&mode=" . $_GET["mode"] . "\">Previous</a>";
                } else {
                    echo "<a>Previous</a>";
                }
                echo "<a href=\"/history.php?page=" . ($page + 1) . "&mode=" . $_GET["mode"] . "\">Next</a>";
            ?>

		</div>
	</main>
</body>

</html>
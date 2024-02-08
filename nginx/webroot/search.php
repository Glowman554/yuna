<?php
    include_once "include/database.php";
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";

    class SearchResult {
        public $site_id;
        public $link;
        public $title;
        public $description;
        public $shortText;

        public function __construct($site_id, $link, $title, $description, $shortText) {
            $this->site_id = $site_id;
            $this->link = $link;
            $this->title = $title;
            $this->description = $description;
            $this->shortText = $shortText;
        }
    }

    if ($_GET["search"]) {
        $search = $_GET["search"];
    } else {
        header("Location: /");
        error("Missing parameter search");
    }

    if (isset($_GET["page"])) {
        $page = (int) $_GET["page"];
    } else {
        $page = 0;
    }

    $limit = 25;
    $offset = $limit * $page;

    $searchStatement = $databaseConnection->prepare("SELECT site_id, link, title, description, shortText, MATCH (title, link, text, keywords) AGAINST (?) as score FROM sites WHERE MATCH (title, link, text, keywords) AGAINST (?) ORDER BY score DESC LIMIT ? OFFSET ?");
    $searchStatement->bind_param("ssii", $search, $search, $limit, $offset);
    $searchStatement->execute();

    $searchResult = $searchStatement->get_result();
    $searchResults = array();

    $row = $searchResult->fetch_assoc();
    while ($row) {
        array_push($searchResults, new SearchResult($row["site_id"], $row["link"], $row["title"], $row["description"], $row["shortText"]));
        $row = $searchResult->fetch_assoc();
    }
    

    $searchResult->close();
    $searchStatement->close();

    if ($username && $userinfo->keep_history) {
        $searchHistoryStatement = $databaseConnection->prepare("insert into search_history (username, search) values (?, ?)");
        $searchHistoryStatement->bind_param("ss", $username, $search);
        $searchHistoryStatement->execute();
        $searchHistoryStatement->close();
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
        echo "<title>ユウナ - " . $search . "</title>";
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
            <input spellcheck="false" autocorrect="on" title="Enter your search" type="search" inputmode="search" id="search" name="search" value="<?php echo $search ?>">
        </form>

		<div class="results" id="results" style="background-color: var(--background);">
            <?php
                foreach ($searchResults as $result) {
                    echo "<div class=\"resultContainer\">";
                    echo "<div class=\"upper\">";
                    echo "<div class=\"info\">";

                    echo "<small id=\"link\">" . $result->link . "</small><br>";

                    echo "<a href=\"/redirect.php?id=" . $result->site_id . "&url=" . $result->link . "\" id=\"title\" class=\"Title\">" . $result->title . "</a>";

                    echo "</div>";
                    echo "</div>";

                    echo "<div class=\"bottom\">";
                    echo "<div class=\"info\">";
                    echo "<h5>";
                    if ($result->description) {
                        echo $result->description;
                    } else {
                        echo $result->shortText;
                    }
                    echo "</h5>";
                    echo "</div>";
                    echo "</div>";

                    echo "</div>";
                }
            ?>

            <?php
                if ($page > 0) {
                    echo "<a href=\"/search.php?page=" . ($page - 1) . "&search=" . $search . "\">Previous</a>";
                } else {
                    echo "<a>Previous</a>";
                }
                echo "<a href=\"/search.php?page=" . ($page + 1) . "&search=" . $search . "\">Next</a>";
            ?>

		</div>
	</main>
</body>

</html>
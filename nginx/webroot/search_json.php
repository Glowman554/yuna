<?php
    include_once "include/database.php";

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
        error("Missing parameter search");
    }

    if (isset($_GET["page"])) {
        $page = (int) $_GET["page"];
    } else {
        $page = 0;
    }

    $limit = 5;
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
    $databaseConnection->close();

    header("Content-Type: text/json");
    echo json_encode($searchResults);
?>
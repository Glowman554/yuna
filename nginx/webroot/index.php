<?php
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";

    if (isset($_GET["mode"])) {
        $mode = $_GET["mode"];

        switch ($mode) {
            case "New image":
                {
                    setcookie("bg_image", "", time() - 3600, "/");
                    header("Location: /");
                }
                break;
        }
    }

    $databaseConnection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ユウナ</title>
    
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/index.css">
    <link rel="stylesheet" href="style/search-bar.css">  
    
    <meta name="description" content="Big baba bubu Search Engine">
	<meta name="keywords" content="Yuna yuna Search Websites Search Engine Search-Enginge searchengine">

    <?php echo $unsplashwidget ?>
    <?php include_once "include/widgets/onionwidget.php" ?>
</head>

<body>

    <?php echo $userwidget ?>
    
    <main>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>

        <form action="search.php" id="search_form">
            <input spellcheck="false" autocorrect="on" title="Enter your search" type="search" inputmode="search" id="search" name="search">
            <label for="search" style="margin-top: 2.5vh;">
                <button type="submit">
                    <h5>[ Search ]</h5>
                </button>
            </label>
        </form>

        <?php 
            if ($username && $userinfo->premium) {
                echo "<form style=\"position: absolute; left: 1rem; bottom: 1rem;\">";
                echo "<input translate=\"no\" name=\"mode\" type=\"submit\" value=\"New image\">";
                echo "</form>";
            }
        ?>
    </main>
</body>

</html>

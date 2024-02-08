<?php
    include_once "include/user.php";
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";

    $result = null;

    if (isset($_GET["url"])) {
        $url = $_GET["url"];

        $username = loadUsernameCookieCheck($databaseConnection);
        if ($username) {
            $result = file_get_contents("http://crawler/api/crawl?url=" . urlencode($url));
            if ($result) {
                $resultStatement = $databaseConnection->prepare("insert into crawl_requests (username, link, status) values (?, ?, ?)");
                $resultStatement->bind_param("sss", $username, $url, $result);
                $resultStatement->execute();
                $resultStatement->close();
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
    <title>ユウナ - Crawl</title>
    <link rel="stylesheet" href="style/login.css">
    <link rel="stylesheet" href="style/style.css">
    <?php echo $unsplashwidget ?>

</head>

<body>
    <?php echo $userwidget ?>

    <header>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>
    </header>
    <main>
        <form class="form" id="login" action="crawl.php">
            <div id="loggedIn">
                <p>*Only crawl legal sites!</p>
                <input type="text" name="url" id="url" placeholder="URL to crawl*" required autofocus>
                <input translate="no" id="crawl" type="submit" value="Crawl">

                <?php
                    if ($result) {
                        echo "<p>Crawl respone " . $result . "</p>";
                    }
                ?>
            </div>
        </form>
    </main>
</body>

</html>
<?php
    $error = "Some error happened!";
    if (isset($_GET["error"])) {
        $error = $_GET["error"];
    }
    http_response_code(500);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユウナ - Error</title>
    <link rel="stylesheet" href="style/login.css">
    <link rel="stylesheet" href="style/style.css">

</head>

<body>
    <header>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>
    </header>
    <main>
        <form class="form" id="login" action="crawl.php">
            <div id="loggedIn">
                <?php
                    echo "<p>" . $error . "</p>"
                ?>
                <a href="/">Return to main page</a>
            </div>
        </form>
    </main>
</body>

</html>
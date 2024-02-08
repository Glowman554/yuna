<?php
    include_once "include/user.php";
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";

    if (!$username) {                
        error("You need to be logged in");
    }

    if (isset($_POST["mode"])) {
        $mode = $_POST["mode"];

        $location = "/";

        switch ($mode) {
            case "Logout":
                {
                    $deleteTokenStatement = $databaseConnection->prepare("delete from user_sessions where token = ?");
                    $deleteTokenStatement->bind_param("s", $_COOKIE["usertoken"]);
                    $deleteTokenStatement->execute();
                    $deleteTokenStatement->close();
                
                }
                break;
            case "Delete account":
                {
                    deleteAccount($username, $databaseConnection);
                }
                break;
            case "Update profile picture":
                {
                    if (isset($_POST["pfp_url"])) {
                        $updateUserStatement = $databaseConnection->prepare("update users set pfp_url = ? where username = ?");
                        $updateUserStatement->bind_param("ss", $_POST["pfp_url"], $username);
                        $updateUserStatement->execute();
                        $updateUserStatement->close();
                    }
                }
                break;
            case "Update password":
                {
                    if (isset($_POST["password_old"]) && isset($_POST["password_new"])) {
                        $password_old = $_POST["password_old"];
                        $password_new = $_POST["password_new"];

                        $hash = loadUserPasswordHash($username, $databaseConnection);
                        if (password_verify($password_old, $hash)) {
                            $updateUserStatement = $databaseConnection->prepare("update users set password_hash = ? where username = ?");
                            $updateUserStatement->bind_param("ss", password_hash($password_new, PASSWORD_BCRYPT), $username);
                            $updateUserStatement->execute();
                            $updateUserStatement->close();
                        } else {
                            error("Invalid password");
                        }
                    }

                }
                break;
            case "Disable search history":
                {
                    updateUserKeepHistory($username, false, $databaseConnection);
                }
                break;
            case "Enable search history":
                {
                    updateUserKeepHistory($username, true, $databaseConnection);
                }
                break;
            case "Click history":
                {
                    $location = "/history.php?mode=click";
                }
                break;
            case "Search history":
                {
                    $location = "/history.php?mode=search";
                }
                break;
        }

        header("Location: " . $location);
    }

    $databaseConnection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユウナ - Account</title>
    <link rel="stylesheet" href="style/account.css">
    <link rel="stylesheet" href="style/style.css">
    <?php echo $unsplashwidget ?>
</head>

<body>
    <?php echo $userwidget ?>
    <header>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>
    </header>
    <main>
        <form class="form" id="pfp" action="account.php" method="POST">
            <label for="pfp_url">
                <h5>Profile picture url:</h5>
            </label>
            <input type="text" name="pfp_url" id="pfp_url" placeholder="Url" required>

            <input translate="no" id="submit" type="submit" name="mode" value="Update profile picture">
        </form>

        <form class="form" id="password" action="account.php" method="POST">
            <label for="password_old">
                <h5>Old password:</h5>
            </label>
            <input type="password" name="password_old" id="password_old" placeholder="Password" required>

            <label for="password_new">
                <h5>New password:</h5>
            </label>
            <input type="password" name="password_new" id="password_new" placeholder="Password" required>

            <input translate="no" id="submit" type="submit" name="mode" value="Update password">
        </form>

        <form class="form" id="account_action" action="account.php" method="POST">
            <input translate="no" id="submit" type="submit" name="mode" value="Logout">
            <input translate="no" id="submit" type="submit" name="mode" value="Delete account">

            <?php
                if ($userinfo->keep_history) {
                    echo "<input translate=\"no\" id=\"submit\" type=\"submit\" name=\"mode\" value=\"Disable search history\">\n";
                    echo "<input translate=\"no\" id=\"submit\" type=\"submit\" name=\"mode\" value=\"Click history\">\n";
                    echo "<input translate=\"no\" id=\"submit\" type=\"submit\" name=\"mode\" value=\"Search history\">\n";
                } else {
                    echo "<input translate=\"no\" id=\"submit\" type=\"submit\" name=\"mode\" value=\"Enable search history\">\n";
                }

                if (!$userinfo->premium) {
                    echo "<a href=\"/upgrade_account.php\"><input translate=\"no\" type=\"button\" value=\"Upgrade to premium account\"></a>\n";
                }
                if ($userinfo->admin) {
                    echo "<a href=\"/admin.php\"><input translate=\"no\" type=\"button\" value=\"Admin pannel\"></a>\n";
                }
            ?>

            <a href="/crawl.php"><input translate="no" type="button" value="Crawl site"></a>
        </form>

    </main>
</body>

</html>
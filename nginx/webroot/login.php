<?php 
    include_once "include/user.php";
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";

    function insertToken($token, $username, $db) {
        $userTokenStatement = $db->prepare("insert into `user_sessions` (username, token) values (?, ?)");
        $userTokenStatement->bind_param("ss", $username, $token);
        $userTokenStatement->execute(); 
        $userTokenStatement->close();
    }

    function createUserToken() {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    
        $str = "";
        for ($i = 0; $i < 99; $i++) {
            $str .= $chars[rand(0, strlen($chars) - 1)];
        }
    
        return $str;
    }

    if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["mode"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $mode = urldecode($_POST["mode"]);

        $token = null;

        if ($mode == "Log in") {
            $hash = loadUserPasswordHash($username, $databaseConnection);
            if ($hash) {
                if (password_verify($password, $hash)) {
                    $token = createUserToken();
                } else {
                    error("Invalid password");
                }
            } else {
                error("Invalid username");
            }
        } else if ($mode == "Create account") {
            $token = createUserToken();

            $userInsertStatement = $databaseConnection->prepare("insert into `users` (username, password_hash, admin, premium, pfp_url) values (?, ?, 0, 0, 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Anthro_vixen_colored.jpg/220px-Anthro_vixen_colored.jpg')");
            $userInsertStatement->bind_param("ss", $username, password_hash($password, PASSWORD_BCRYPT));
            $userInsertStatement->execute();
            $userInsertStatement->close();
        }

        if ($token) {
            insertToken($token, $username, $databaseConnection);
            setcookie("usertoken", $token, time() + (86400 * 30), "/");
            header("Refresh: 0");
        }
    }

    $databaseConnection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユウナ - Login</title>
    <link rel="stylesheet" href="style/login.css">
    <link rel="stylesheet" href="style/style.css">

    <style>
        <?php 
            if ($username) {
                echo "#notLoggedIn { display: none }";
            } else {
                echo "#loggedIn { display: none }";
            }
        ?>
    </style>
    <?php echo $unsplashwidget ?>
    <?php include_once "include/widgets/onionwidget.php" ?>
</head>

<body>
    <?php echo $userwidget ?>
    <header>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>
    </header>
    <main>
        <form class="form" id="login" action="login.php" method="POST">

            <div id="notLoggedIn">
                <label for="username">
                    <h5>Username:</h5>
                </label>
                <input type="text" name="username" id="username" placeholder="Username or Email" required autofocus>

                <label for="password">
                    <h5>Password:</h5>
                </label>
                <input type="password" name="password" id="password" placeholder="Password" required>

                <input translate="no" id="submit" type="submit" name="mode" value="Log in">
                <input translate="no" id="submit" type="submit" name="mode" value="Create account">
            </div>

            
            <div id="loggedIn">
                <p>You are logged in!</p>
                <a href="/crawl.php">Go to crawl site</a>
            </div>
        </form>
    </main>
</body>

</html>
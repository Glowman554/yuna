<?php
    include_once "include/user.php";
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";

    if ($username) {
        if (!$userinfo->admin) {
            error("Admin only page");
        }
    } else {
        error("You need to be logged in");
    }


    if (isset($_GET["mode"])) {
        $mode = $_GET["mode"];
        $target = $_GET["user"];

        switch ($mode) {
            case "Delete":
                {
                    deleteAccount($target, $databaseConnection);
                }
                break;
            case "Make admin":
                {
                    updateUserAdmin($target, true, $databaseConnection);
                }
                break;
            case "Remove admin":
                {
                    updateUserAdmin($target, false, $databaseConnection);
                }
                break;
            case "Make premium":
                {
                    updateUserPremium($target, true, $databaseConnection);
                }
                break;
            case "Remove premium":
                {
                    updateUserPremium($target, false, $databaseConnection);
                }
                break;
        }

        header("Location: /admin.php");
    }

    $page = 0;
    if (isset($_GET["page"])) {
        $page = (int) $_GET["page"];
    }

    $users = loadUserInfoMulti($page, 10, $databaseConnection);

    $databaseConnection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/admin.css">

    <title>ユウナ - Admin</title>
    <?php echo $unsplashwidget ?>
</head>

<body>
    <?php echo $userwidget ?>

	<header>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>
	</header>
	<main>
            <table class="user_entries">
                <?php
                    foreach ($users as $user) {
                        echo "<tr class=\"user_entry\">";


                        echo "<td>";
                        echo "<div class=\"profile_admin\">";
                        echo "<img id=\"user-pp\" src=\"" . $user->pfp_url . "\"><span>" . $user->username . "</span>";
                        echo "</div>";
                        echo "</td>";
                        
                        echo "<td>";
                        echo "<form id=\"account_action\" action=\"admin.php\" method=\"get\">";
                        echo "<input type=\"submit\" name=\"mode\" value=\"Delete\">";

                        if ($user->admin) {
                            echo "<input type=\"submit\" name=\"mode\" value=\"Remove admin\">";
                        } else {
                            echo "<input type=\"submit\" name=\"mode\" value=\"Make admin\">";
                        }
                        if ($user->premium) {
                            echo "<input type=\"submit\" name=\"mode\" value=\"Remove premium\">";
                        } else {
                            echo "<input type=\"submit\" name=\"mode\" value=\"Make premium\">";
                        }
                        echo "<input type=\"text\" name=\"user\" value=" . $user->username . " style=\"display: none;\">";
                        echo "</form>";
                        echo "</td>";

                        echo "</tr>";
                    }
                ?>
            </table>

            <?php
                if ($page > 0) {
                    echo "<a href=\"/admin.php?page=" . ($page - 1) . "\">Previous</a>";
                } else {
                    echo "<a>Previous</a>";
                }
                echo "<a href=\"/admin.php?page=" . ($page + 1) . "\">Next</a>";
            ?>

	</main>
</body>

</html>
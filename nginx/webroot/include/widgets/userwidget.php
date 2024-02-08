<?php
    include_once "include/user.php";

    $userwidget = "";

    if ($username) {
        $userwidget .= "<a href=\"account.php\" class=\"profile\">";
        $userwidget .= "<p>" . $username . "</p><img id=\"user-pp\" src=\"" . $userinfo->pfp_url . "\">";
    } else {
        $userwidget .= "<a href=\"login.php\" class=\"profile\">";
        $userwidget .= "<p>Sign Up / Log in</p><img id=\"user-pp\">";
    }

    $userwidget .= "</a>";
?>
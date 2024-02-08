<?php
    include_once "include/config/unsplash.php";
    include_once "include/unsplash.php";
    include_once "include/user.php";

    $unsplashwidget = "";

    if ($username) {
        if ($userinfo->premium) {
            $bg = fetchUnsplashImage($unsplashtoken);

            $unsplashwidget .= "<style>";
            $unsplashwidget .= "body {";
            $unsplashwidget .= "    background: url(" . $bg . ");";
            $unsplashwidget .= "    background-position: 50% 50%;";
            $unsplashwidget .= "    background-repeat: no-repeat;";
            $unsplashwidget .= "    background-size: cover;";
            $unsplashwidget .= "    animation: none;";
            $unsplashwidget .= "}";
            $unsplashwidget .= "</style>";
        }
    }
?>
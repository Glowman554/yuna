<?php
    function fetchUnsplashImage($unsplashtoken) {
        $unsplashurl = "https://api.unsplash.com/photos/random?orientation=landscape&content_filter=high&topics=nature,wallpapers&client_id=" . $unsplashtoken;

        if (isset($_COOKIE["bg_image"])) {
            return $_COOKIE["bg_image"];
        } else {
            $data = file_get_contents($unsplashurl);
            $image = "https://images.unsplash.com/photo-1544259342-306eccfec481?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80";
            if ($data) {
                $data = json_decode($data);
                if ($data->urls && $data->urls->regular) {
                    $image = $data->urls->regular;
                } else {
                    file_put_contents('php://stderr', print_r($data, TRUE));
                }
            }

            setcookie("bg_image", $image, time() + 3600, "/");
            return $image;
        }
    }
?>
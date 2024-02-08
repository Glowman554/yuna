<?php
    function sendWebhookMessage($webhook, $message, $username, $avatar) {
        $data = array(
            "content" => $message,
            "avatar_url" => $avatar,
            "username" => $username
        );

        $jsonData = json_encode($data);

        $ch = curl_init($webhook);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);
    }
?>
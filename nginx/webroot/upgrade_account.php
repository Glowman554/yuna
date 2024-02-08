<?php
    include_once "include/user.php";
    // include_once "include/webhook.php";
    // include_once "include/config/webhook.php";
    include_once "include/widgets/userwidget.php";
    include_once "include/widgets/unsplashwidget.php";

    if (!$username) {                
        error("You need to be logged in");
    }

    if ($userinfo->premium) {
        error("Already premium (thx btw)");
    }

    $methods = null;
    $paymentId = null;

    $receivedAmount = null;
    $receivedFrom = null;
    $receivedMethod = null;

    if (isset($_COOKIE["paymentId"]) && isset($_COOKIE["methods"])) {
        $methods = json_decode($_COOKIE["methods"]);
        $paymentId = $_COOKIE["paymentId"];

        $data = file_get_contents("http://payments/api/poll/" . $_COOKIE["paymentId"]);
        if ($data) {
            $data = json_decode($data);
            if (!isset($data->receivedAmount)) {
                header("Refresh: 10");
            } else {
                $receivedAmount = $data->receivedAmount;
                $receivedFrom = $data->receivedFrom;
                $receivedMethod = $data->receivedMethod;

                setcookie("paymentId", "", time() - 3600, "/");
                setcookie("methods", "", time() - 3600, "/");

                updateUserPremium($username, true, $databaseConnection);

                $paymentStatement = $databaseConnection->prepare("insert into payments (method, amount, username, address) values (?, ?, ?, ?)");
                $paymentStatement->bind_param("sdss", $receivedMethod, $receivedAmount, $username, $receivedFrom);
                $paymentStatement->execute();
                $paymentStatement->close();

                // sendWebhookMessage($paymentwebhook, "Got payment from " . $username . " of " . $receivedAmount . " TON!", "Ton payment", "https://cwstatic.nyc3.digitaloceanspaces.com/6499/Toncoin.jpg");
            }
        }
    } else {
        $data = file_get_contents("http://payments/api/begin");
        if ($data) {
            $data = json_decode($data);
            setcookie("paymentId", $data->paymentId);
            setcookie("methods", json_encode($data->methods));
            header("Location: /upgrade_account.php");
        }
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

    <style>
        #information {
            display: <?php echo $receivedAmount ? "none": "block"?>; 
        }
        #success {
            display: <?php echo $receivedAmount ? "block": "none"?>; 
        }
    </style>
    <?php include_once "include/widgets/onionwidget.php" ?>
</head>

<body>
    <?php echo $userwidget ?>
    <header>
        <a href="/" style="text-decoration: none; color: inherit"><h1 translate="no">ユウナ</h1></a>
    </header>
    <main>

        <form class="form" id="success">
            <p>Thank you for donating <?php echo $receivedAmount ?> <?php echo $receivedMethod ?>!</p>
        </form>

        
        <form class="form" id="information">

            <p>Thank you for considering a donation to support our cause. To send your contribution:</p><br>

            <ul style="padding-left: 1rem">
                <li>Send one of the following crypto currencies to one of the following wallet addresses:</li>
                <br>
                <?php
                    foreach ($methods as $method) {
                        echo "<li>Send " . $method->methodName . " to <span class=\"copyText\">" . $method->walletAddress . "</span>.</li>";
                    }                
                ?>
                <br>
                <li>Include Specific String: Add <span class="copyText"><?php echo $paymentId ?></span> in the comment field.</li>
                <li>No Limits: Send as much crypto currency as you like; every contribution helps.</li>
                <li>Double-Check Details: Verify the address and comment field for accuracy.</li>
            </ul>
            <br>
            <p>Your support is greatly appreciated!</p><br>
            <p style="color: red;"><strong>Important:</strong> Processing your donation may take up to 5 minutes. Please do not close this page until the transaction is complete.</p>

        </form>

    </main>
</body>

</html>
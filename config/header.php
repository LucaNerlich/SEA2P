<?php
$src_extend = strpos($_SERVER["REQUEST_URI"], "src/") ? "../" : "";
if (isset($_GET["signout"])) {
    unset($_SESSION["user"]);
    $_SESSION["state"] = 0;
    addMessage(message("Logout erfolgreich!", 1));
} else if (isset($_GET["signin"]) && isset($_POST["email"])) {
    $email = escape($_POST["email"]);
    $password = md5($_POST["password"]);
    $user = query("SELECT * FROM client WHERE email = '$email' AND password='$password'");
    if (sizeof($user) == 1) {
        $_SESSION["user"] = $user[0];
        $_SESSION["state"] = 1;
        addMessage(message("Login erfolgreich!", 1));
        header("Location: ../index.php");
    } else {
        addMessage(message("Login fehlgeschlagen, bitte &uuml;berpr&uuml;fe deine E-Mail und dein Passwort", 2));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">

    <title>HAW Bike Tracking</title>
    <link href="<?php echo $src_extend; ?>css/style.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap core CSS -->
    <!-- Custom styles for this template -->
    <link href="<?php echo $src_extend; ?>css/bootstrap.css" rel="stylesheet" type="text/css">
    <script src="<?php echo $src_extend; ?>js/jquery-2.1.3.min.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $src_extend; ?>js/bootstrap.min.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $src_extend; ?>index.php">Home</a>
        </div>

        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo $src_extend; ?>src/about.php">About</a></li>
                <?php

                if ($_SESSION["state"] == 0) {
                    echo '<li><a href="' . $src_extend . 'src/signin.php">Sign In</a></li>';
                } else {
                    echo '<li><a href="' . $src_extend . 'src/trip.php">Kilometer eintragen</a></li>';
                    echo '<li><a href="' . $src_extend . 'src/contact.php">Kontakt aufnehmen</a></li>';
                    echo '<li><a href="http://141.22.29.119:9000/hipergate">Hipergate</a></li>';

                    if ($_SESSION["user"]["email"] == "luca.nerlich@haw-hamburg.de" || $_SESSION["user"]["email"] == "daniel.sommerlig@haw-hamburg.de"  ||
                        $_SESSION["user"]["email"] == "fabian.simroth@haw-hamburg.de" || $_SESSION["user"]["email"] == "clemens.rassbach@haw-hamburg.de") {
                        echo '<li><a href="' . $src_extend . 'src/register.php">Register User</a></li>';
                    }

                    echo '<li><a href="' . $src_extend . 'src/signin.php?signout">Sign Out</a></li>';
                    echo '<li style="padding:15px 10px;color: #eee;">Moin, ' . $_SESSION["user"]["surname"] . '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

<div align="center">

    <?php
    if (isset($_SESSION["messages"])) {
        foreach ($_SESSION["messages"] as $message) {
            echo $message;
        }
        unset($_SESSION["messages"]);
    }

    ?>

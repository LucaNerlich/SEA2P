<?php
include '../config/config.php';
include '../config/header.php';

if (isset($_POST["email"]))
{
	$email = escape($_POST["email"]);
	$password = escape($_POST["password"]);
	$user = query("SELECT * FROM client WHERE email = '$email' AND password='$password'");
	if (sizeof($user) == 1)
	{
		$_SESSION["user"] = $user[0];
		$_SESSION["state"] = 1;
	}
	else
	{
		echo message("Login fehlgeschlagen, bitte &uuml;berpr&uuml;fe deine E-Mail und dein Passwort",1);
	}
}

if ($_SESSION["state"] == 0)
{
	echo "<form class='form-signin' action='signin.php' method='post'>";
	echo "<h2 class='form-signin-heading'>Please sign in</h2>";
	echo '<label for="inputEmail" class="sr-only">Email address</label>';
    echo '<input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>';
    echo '<label for="inputPassword" class="sr-only">Password</label>';
    echo '<input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>';
    echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>';
	
	echo "</form>";
}
else
{
	echo "<strong>Du bist bereits eingeloggt!</strong>";
}

include 'spielwiese/footer.php';
?>
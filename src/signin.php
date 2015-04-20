<?php
include '../config/config.php';
include '../config/header.php';

if ($_SESSION["state"] == 0)
{
    echo '<div id="phone">';
	echo "<form class='form-signin' action='signin.php?signin' method='post' style='width: 40%'>";
	echo "<h2 class='form-signin-heading'>Please sign in</h2>";
	echo '<label for="inputEmail" class="sr-only">Email address</label>';
    echo '<input type="email" style="margin: 2px" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>';
    echo '<label for="inputPassword" class="sr-only">Password</label>';
    echo '<input type="password" style="margin: 2px" id="inputPassword" name="password" class="form-control" placeholder="Password" required>';
    echo '<button class="btn btn-lg btn-primary btn-block" style="margin: 2px" type="submit">Sign in</button>';
	echo "</form>";
    echo '</div>';
}
else if (isset($_GET["signout"]))
{
	echo message("Logout erfolgreich!",1);
}

include '../config/footer.php';
?>
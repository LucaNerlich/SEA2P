<?php
include '../config/config.php';
include '../config/header.php';

if ($_SESSION["state"] == 1 && ($_SESSION["user"]["email"] == "luca.nerlich@haw-hamburg.de") || ($_SESSION["user"]["email"] == "daniel.sommerlig@haw-hamburg.de") || ($_SESSION["user"]["email"] == "stefan.zinke@haw-hamburg.de") ||
    ($_SESSION["user"]["email"] == "fabian.simroth@haw-hamburg.de") || ($_SESSION["user"]["email"] == "clemens.rassbach@haw-hamburg.de")) {
    echo "<h2 class='form-signin-heading'>Please provide the following information</h2>";
    echo "<form class='form-signin' action='register.php' method='post' style='width: 40%'>";

    echo '<label for="inputEmail" class="sr-only">Email address</label>';
    echo '<input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>';

    echo '<label for="inputPassword" class="sr-only">Password</label>';
    echo '<input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>';

    echo '<label for="surname" class="sr-only">Surname</label>';
    echo '<input type="text" name="surname" class="form-control" placeholder="Surname" aria-describedby="basic-addon1" required>';

    echo '<label for="firstname" class="sr-only">Firstname</label>';
    echo '<input type="text" name="firstname" class="form-control" placeholder="Firstname" aria-describedby="basic-addon1" required>';

    echo '<label for="streetname" class="sr-only">Streetname</label>';
    echo '<input type="text" name="streetname" class="form-control" placeholder="Streetname" aria-describedby="basic-addon1" required>';

    echo '<div class="input-group">';
    echo '<label for="streetnumber" class="sr-only">Streetname</label>';
    echo '<span class="input-group-addon">No.</span>';
    echo ' <input type="number" name="streetnumber" class="form-control" placeholder="Streetnumber" aria-label="Streetnumber" min="1" max="999" required>';
    echo '</div>';

    echo '<div class="input-group">';
    echo '<label for="zipcode" class="sr-only">Zipcode</label>';
    echo '<span class="input-group-addon">Zip.</span>';
    echo ' <input type="number" name="zipcode" class="form-control" placeholder="Zipcode" aria-label="Zipcode" min="0" max="99999" required>';
    echo '</div>';

    echo '<label for="city" class="sr-only">City</label>';
    echo '<input type="text" name="city" class="form-control" placeholder="City" aria-describedby="basic-addon1" required>';

    echo '<div class="input-group">';
    echo '<label for="telephoneNumber" class="sr-only">Telephone No.</label>';
    echo '<span class="input-group-addon">Tel. No.</span>';
    echo ' <input type="text" name="telephoneNumber" class="form-control" placeholder="Telephone No." aria-label="Telephone No.">';
    echo '</div>';

    echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Register User</button>';

    echo "</form>";
} else {
    echo message("Fehler beim Anlegen eines neuen Nutzers.");
}

include '../config/footer.php';
?>
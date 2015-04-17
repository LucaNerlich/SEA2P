<?php
include 'config/config.php';
include 'config/header.php';

echo '<div id="damagereport">';
echo '<form action="form.php">';
echo '            <select name="type" size="1">';
//echo '                <option>---</option>';
echo '                <option>Schadenmeldung</option>';
//echo '               <option>Message</option>';
echo '            </select>';

echo '<br>';

echo '            <select name="product" size="1">';
echo '                <option>Produkt ausw&auml;hlen</option>';
echo '                <option>$product1</option>';
echo '               <option>$product1</option>';
echo '            </select>';

echo '<br>';

echo '            <label for="serial">S/N:</label> ';
echo '            <input type="text" id="serial" size="10" maxlength="10" pattern="[0-9]{10}">/>';

echo '            <input type="submit" value="send">';
echo '        </form>';
echo '</div>';

include 'config/footer.php';
?>
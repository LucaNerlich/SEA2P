<?php
include 'config/config.php';
include 'config/header.php';

echo '<form action="GUI.html">';
echo '            <select name="type" size="1">';
echo '                <option>---</option>';
echo '                <option>Schadenmeldung</option>';
echo '               <option>Message</option>';
echo '            </select>';

echo '            <input type="submit" value="send">';
echo '        </form>'';

include 'config/footer.php';
?>
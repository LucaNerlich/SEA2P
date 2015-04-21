<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">DTA Datei an Verband senden</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form action="" method="post">
<table width="100%"><tr valign="top"><td>
<table>
<tr><td width="140">Verband:</td><td>[VERBAND]</td></tr>
<tr><td>Empf&auml;nger:</td><td><input type="text" size="50" name="mail" value="[EMAIL]"></td></tr>
<tr><td>Betreff:</td><td><input type="text" name="betreff" size="50" value="[BETREFF]"></td></tr>
<tr><td>Dateiname:</td><td>[DATEINAME]</td></tr>
</table>
</td><td>
<table>
<tr valign="top"><td width="140">Nachricht:</td><td><textarea rows="7" cols="50" name="nachricht">[NACHRICHT]</textarea></td></tr>
</table>

</td>
<td valign="bottom"><input type="submit" value="Senden" name="submit"></td>
</tr></table>
</form>
Datei:

<iframe src="./index.php?module=verband&action=datei&id=[ID]" width="100%" height="400"></iframe>


</div>


<!-- tab view schließen -->
</div>


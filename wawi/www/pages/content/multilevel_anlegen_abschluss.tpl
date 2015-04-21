<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">neue Abrechnung anlegen</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<div class="info">Die Berechnung wurde durchgef&uuml;hrt und kann jetzt abgeschlossen und versendet werden.</div>
<form action="" method="post">
<input type="hidden" name="erster_tag" value="[ERSTERTAG]">
<input type="hidden" name="letzter_tag" value="[LETZTERTAG]">
<table align="center">
<tr><td width="100"><input type="radio" name="auswahl" value="pdf" checked></td><td>Sammelpdf erstellen</td></tr>
<tr><td><input type="radio" name="auswahl" value="email" disabled></td><td>direkt per E-Mail versenden</td></tr>
</table>
<br><br>
<center><input type="submit" value="Abrechnung jetzt abschliessen" name="abschliessen"></center>
<!--
<fieldset><legend>Zeitbereich</legend>
 <table width="" align="center" cellspacing="5" border="0">
  <tr><td>Erster Tag</td>
  <td><input type="text" size="12" id="erster_tag" name="erster_tag" value="[ERSTERTAG]"></td>
  <td>Letzter Tag</td>
<td><input type="text" size="12" id="letzter_tag" name="letzter_tag" value="[LETZTERTAG]"></td>

<td><input type="submit" value="Abrechnung jetzt anlegen" name="submit"></td>
</tr>
	</table>
-->
</form>
</fieldset>
</div>

<!-- tab view schließen -->
</div>


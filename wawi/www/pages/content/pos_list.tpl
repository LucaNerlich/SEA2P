<table width="100%">

<tr><td>
<fieldset><legend>Kunde</legend>
<table align="center">
<tr><td><input type="radio" name="kunde" value="1" checked></td><td>Bestandskunde</td><td><input type="text" size="40"></td>
<td><input type="radio" name="kunde" value="1"></td><td>Laufkundschaft</td><td></td></tr>
</table>
</fieldset>
</td></tr>

<tr><td>
<fieldset><legend>Artikel</legend>
</fieldset>
</td></tr>


<tr><td>
<fieldset><legend>Zahlweise</legend>
<table align="center">
<tr>
<td><input type="radio" name="zahlweise" value="1" checked>&nbsp;Bar</td>
<td><input type="radio" name="zahlweise" value="1">&nbsp;EC-Karte</td>
<td><input type="radio" name="zahlweise" value="1">&nbsp;Kreditkarte</td>
<td><input type="radio" name="zahlweise" value="1">&nbsp;Sonstiges</td>
</table>
</fieldset>
</td></tr>



<tr><td></td></tr>


</table>
<fieldset><legend>Aktionen</legend>
<div class="tabsbutton" align="center">
<!--<a href="#" onclick="if(!confirm('sas wirklich anlegen?')) return false; else window.location.href='index.php?module=adresse&action=createdokument&id=1&cmd=1';">
<table width="150" height="40"><tr><td>Ersatzteillieferung anlegen</td></tr></table></a>
<a href="#" onclick="if(!confirm('sas wirklich anlegen?')) return false; else window.location.href='index.php?module=adresse&action=createdokument&id=1&cmd=1';">
<table width="150" height="40"><tr><td>Rechnung anlegen</td></tr></table></a>
<a href="#" onclick="if(!confirm('sas wirklich anlegen?')) return false; else window.location.href='index.php?module=adresse&action=createdokument&id=1&cmd=1';">
<table width="150" height="40"><tr><td>Weitere Serviceanfrage anlegen</td></tr></table></a>-->
<a href="#" onclick="if(!confirm('Soll die Anfrage abgeschossen werden?')) return false; else window.location.href='index.php?module=service&action=abschluss&id=[ID]';">
<table width="400" height="40"><tr><td>Verkauf ausl&ouml;sen</td></tr></table></a>
<a href="#" onclick="if(!confirm('Soll die Anfrage abgeschlossen werden und ein Eintrag in der Zeiterfassung angelegt werden?')) return false; else window.location.href='index.php?module=service&action=abschlusszeit&id=[ID]';">
<table width="400" height="40"><tr><td>Ticket schlie&szlig;en und Zeiterfassung anlegen</td></tr></table></a>




</div>
</fieldset>


<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Inventur</a></li>
        <li><a href="#tabs-3">Spezialfunktionen</a></li>
        <li><a href="#tabs-2">Abschluss</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post">
<table width="100%"><tr><td align="center">Regal:&nbsp;[REGALAUTOSTART]<input type="text" name="regal" id="regal" value="" style="background-color: red">[REGALAUTOEND]&nbsp;Jetzt Regal abscannen!
&nbsp;<input type="submit" value="Suchen" name="submit">
</td><td align="right">
</td></tr>

</table>

<script type="text/javascript">document.getElementById("regal").focus();</script>
<br><br>
<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="inventurspeichern"></td></tr></table>
[TAB1]
<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="inventurspeichern"></td></tr></table>
</form>
</div>

<div id="tabs-2">
<form action="" method="post">
<table width="100%"><tr>
<td align="center">
[PERMISSIONINVENTURSTART]
&nbsp;<input type="button" onclick="if(!confirm('Soll die Inventur jetzt abgeschlossen werden? Alle Anpassungen werden &uuml;bernommen und der anschlie&szlig;end neue Lagerbestand gesondert als Inventur Stand: [STAND] gespeichert.')) return false; else window.location.href='index.php?module=lager&action=inventur&cmd=einfrieren&id=[ID]';" value="[KURZUEBERSCHRIFT2] jetzt einfrieren.">
[PERMISSIONINVENTURENDE]
</td></tr>

</table>
</div>



<div id="tabs-3">
<center><input type="button" onclick="if(!confirm('Soll die Inventur jetzt aufgrund des Lagerbestandes vorausgef&uuml;llt werden?')) return false; else window.location.href='index.php?module=lager&action=inventurladen&id=[ID]';" value="Inventur f&uuml;r [KURZUEBERSCHRIFT2] jetzt aus Lagerbestand laden">

[PERMISSIONINVENTURSTART]

&nbsp;<input type="button" onclick="if(!confirm('Soll die Inventur jetzt zur&uuml;ckgesetz werden? Alle Anpassungen werden gel&ouml;scht.')) return false; else window.location.href='index.php?module=lager&action=inventur&cmd=resetalle&id=[ID]';" value="Inventur [LAGERNAME] (komplett) zur&uuml;cksetzten.">

</center>

[PERMISSIONINVENTURENDE]
</div>

<!-- tab view schlieï¿½en -->
</div>




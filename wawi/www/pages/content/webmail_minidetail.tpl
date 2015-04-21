<table cellspacing="5" cellpadding="4" border="0" width="100%" align="left">
<tr>
<th>[SUBJECT]</th><th></th>
</tr>
<tr valign="top">
<td style="background-color: white;" width="800">


<!--<div style="OVERFLOW: auto; WIDTH: 700px; HEIGHT: 400px; background-color: white;">
<pre>[BODY]</pre>
</div>
-->
<iframe id="webmail_print[ID]" name="webmail_print[ID]" src="index.php?module=webmail&action=iframe&id=[ID]" style="WIDTH: 800px; HEIGHT: 400px; background-color: white;"></iframe>

</td>
<td width="100" align="center"><b>Markierung:</b><br><br>
<input type="button" value="dringend sp&auml;ter antworten" onclick="window.location.href='index.php?module=webmail&action=antworten&id=[ID]'" style="width:170px">
<input type="button" value="als beantwortet markiert" onclick="window.location.href='index.php?module=webmail&action=beantwortet&id=[ID]'" style="width:170px">
<input type="button" value="in Warteschlange schieben" onclick="window.location.href='index.php?module=webmail&action=warteschlange&id=[ID]'" style="width:170px">
<input type="button" value="als ungelesen" onclick="window.location.href='index.php?module=webmail&action=ungelesen&id=[ID]'" style="width:170px">
<!--<input type="button" value="gelesen" onclick="window.location.href='index.php?module=webmail&action=gelesen&id=[ID]'" style="width:170px">-->
<br><br><b>Men&uuml;:</b><br><br>

<input type="button" value="Weiterleiten" style="width:170px" onclick="window.location.href='index.php?module=webmail&action=schreiben&cmd=fwd&id=[ID]'">
<input type="button" value="Antwort schreiben" style="width:170px" onclick="window.location.href='index.php?module=webmail&action=schreiben&id=[ID]'"style="width:170px">
<input type="button" value="Zuordnen" onclick="window.location.href='index.php?module=webmail&action=view&id=[ID]'"style="width:170px">
<input type="button" value="Drucken" style="width:170px" onclick="document.getElementById('webmail_print[ID]').contentWindow.print();">
<!--
<input type="button" value="Aufgabe erzeugen" style="width:170px">
<input type="button" value="Termin in Kalender" style="width:170px">
<input type="button" value="Drucken" style="width:170px">
<input type="button" value="L&ouml;schen" style="width:170px">
<input type="button" value="Vollbild" style="width:170px">-->
</td>
</tr>
<tr><td colspan="2">Anlagen: [ANHANG]</td></tr>
</table>

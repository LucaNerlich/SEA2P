<form action="" method="post">
<table width="700">
<tr valign="top"><td width="60%">



<table width="100%" cellspacing="5">
<tr class="gentable"><td><b>Funktion</b></td><td><b>Kommando</b></td></tr>
<tr class="gentable"><td>E-Mails als gelesen markieren</td><td><input type="button" value="Alle E-Mails als gelesen markieren" onclick="if(!confirm('Wirklich alle E-Mails als gelesen markieren?')) return false; else window.location.href='index.php?module=webmail&action=allegelesen&key=ok';"></td></tr>
<tr class="gentable"><td>E-Mails nach X Tagen auf Server l&ouml;schen</td><td><input type="text"  size="4" value="[LOESCHTAGE]" name="loeschtage"></td></tr>
<!--<tr class="gentable"><td>Antwort sich selbst als E-Mails senden</td><td><input type="checkbox" value="1"></td></tr>-->
<tr class="gentable"><td>Auto-Responder aktivieren</td><td><input type="checkbox" value="1" name="autoresponder" [AUTORESPONDER]></td></tr>
<tr class="gentable"><td>Auto-Responder Betreff</td><td><input type="text" value="[AUTORESPONDERBETREFF]" name="autoresponderbetreff" size="45"></td></tr>
<tr class="gentable"><td>Auto-Responder Text</td><td><textarea rows="5" cols="50" name="autorespondertext">[AUTORESPONDERTEXT]</textarea></td></tr>
</table>
</td>

</tr>

<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Senden" name="submit">
    <input type="button" value="Abbrechen"  onclick="window.location.href='index.php?module=webmail&action=list'"/>
</td>
    </tr>

</table>
</form>

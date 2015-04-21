<script>
  $(function() {
$(".button").button();
  });
  </script>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Tickets</a></li>
    </ul>
<!-- ende gehort zu tabview -->
<div id="tabs-1">


[JAVASCRIPTCODE]
<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>
<form action="" method="post" name="eproosubmitform">

[MELDUNG]
<br>

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
<tr valign="top" colspan="3">
<td>
<table width="100%" border="0">
<tr><td valign="top" colspan="2">
<table>
<tr><td width="70">Von:</td><td>[NAME] &lt;[EMAIL]&gt;</td></tr>
<tr><td>Betreff:</td><td><b>[BETREFF]</b></td></tr>
</table>
<br>
[TEXT]
<table width="100%">
<!--<tr><td>Vorschlag:</td><td>[VORSCHLAG] (<a href="#" onclick="document.getElementById('adresse').value='[VORSCHLAG]';">&uuml;bernehmen</a>)</td></tr>-->
<tr valign="bottom"><td>
Adresse zuordnen:</td><td width="80%">&nbsp;[KUNDEAUTOSTART]<input type="text" size="40" name="adresse" id="adresse" value="[VORSCHLAG]" >[KUNDEAUTOEND]&nbsp;[ADRESSEPOPUP]
</td></tr></table>
<br>
<fieldset><legend>Anh&auml;nge:</legend>
[ANHAENGE]
</fieldset>
<br>
<fieldset><legend>Gespr&auml;chsverlauf:</legend>
[TABLE]
</fieldset>

</td></tr>



<tr valign="top">
<td width="60%">

<!-- kunde -->
<fieldset><legend>Kunde</legend>
<table width="100%" height="140">
<tr><td>Kunde: </td><td><b>[NAME]</b></td></tr>
<tr><td>Kontakt: </td><td>[EMAIL]</td></tr>
<tr><td>Zeit: </td><td>[ZEIT]</td></tr>
<tr><td>Wartezeit: </td><td><font color="red"><b>[WARTEZEIT]</b></font></td></tr>
<tr><td>Quelle: </td><td>[QUELLE]</td></tr>
</table>
</fieldset>
</td>
<td>
<fieldset style="float: right;" valign="top"><legend>Zuordnung</legend>
<table height="140">
<tr><td>1.</td><td>Projekt: </td><td>[SELECT_PROJEKT]</td></tr>
<tr><td>2.</td><td>Warteschlange: </td><td><select name="warteschlange">[WARTESCHLANGE]</select></td></tr>
<tr><td>3.</td><td>Prio: </td><td><b><select name="prio">[PRIO]</select></b></td></tr>
</table>
</fieldset>



</td></tr>
</table>

</td>
</tr>


    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right"  bordercolor="" classname="orange2" class="orange2">
[LESENSTART]
<table width="100%" border="0"><tr><td>
<input type="button" value="Abbrechen" onclick="window.location.href='index.php?module=ticket&action=freigabe&id=[ID]'"></td><td align="center">
<input type="radio" name="antwort" value="spam">in den Papierkorb 
<input type="radio" name="antwort" value="zuordnen">nur zuordnen / speichern
<input type="radio" name="antwort" value="beantwortet">als beantwortet markieren
<input type="radio" name="antwort" value="sofort" checked>sofort beantworten
</td><td align="right">
    <input type="submit" name="abschicken" value="Abschicken" ></td></tr></table>
[LESENENDE]
</td>
    </tr>
  
    </tbody>
  </table>
  </form>
</td></tr></table></td></tr>
</table>

</div>




<!-- tab view schließen -->
</div>


<div id="tabs">
<ul>   
        <li><a href="#tabs-1">Gruppe/Verband</a></li>
 </ul>

<div id="tabs-1">
<form action="" method="post">
<table height="80" width="100%"><tr><td>
<fieldset><legend>&nbsp;Auswahl</legend>
<table width="100%" cellspacing="5">
<tr>
  <td>Gruppe/Verband:</td>
  <td><input type="text" id="gruppe" name="gruppe" size="35" value="[GRUPPE]" onclick=document.getElementById("kunde").value='';></td>
</tr>


</table>
</fieldset>
</td><td>
<fieldset><legend>&nbsp;Datum</legend>
<table width="100%" cellspacing="5">
<tr>
  <td>Von:</td>
  <td><input type="text" id="von" name="von" size="15" value="[VON]"></td>
  <td>Bis:</td>
  <td><input type="text" id="bis" name="bis" size="15" value="[BIS]"></td>
  <td><input type="submit" value="Ums&auml;tze laden" name="laden"></td>
<!--  <td><input type="checkbox" name="ust" value="1" [UST]>&nbsp;netto ohne USt.</td>-->
</tr>

</table>
</fieldset>
</form>

</td></tr></table>

<table width="100%>">
<tr><td>Umsatz Gesamt (netto)</td><td>Anzahl Belege</td><td>Deckungsbeitrag</td><td>DB in %</td></tr>
<tr>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[UMSATZ]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[ANZAHL]</td>     
	<td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[DECKUNGSBEITRAG]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[DBPROZENT]</td>
</tr>
</table>

[GRUPPEUMSATZTABELLE]

</div>

</div>

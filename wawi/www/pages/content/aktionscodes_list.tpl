<div id="tabs">
<ul>   
        <li><a href="#tabs-1">Auswertung</a></li>
        <li><a href="#tabs-2">Aktionscodes Angebot</a></li>
        <li><a href="#tabs-3">Aktionscodes Auftrag</a></li>
        <li><a href="#tabs-4">Aktionscodes Rechnung</a></li>
        <li><a href="#tabs-5">Aktionscodes Gutschrift</a></li>
 </ul>

<div id="tabs-1">
<form action="" method="post">
<table height="80" width="100%"><tr><td>
<fieldset><legend>&nbsp;Auswahl</legend>
<table width="100%" cellspacing="5">
<tr>
  <td>Belege:</td>
  <td>
<select name="belege"><option value="angebot" [CHECKEDANGEBOT]>Angebot</option>
<option value="auftrag" [CHECKEDAUFTRAG]>Aufrag</option>
<option value="rechnung" [CHECKEDRECHNUNG]>Rechnung</option>
<!--<option value="lieferschein">Lieferschein</option>-->
<option value="gutschrift" [CHECKEDGUTSCHRIFT]>Gutschrift</option>
</select>
</td>
</tr>


</table>
</fieldset>
</td><td>
<fieldset><legend>&nbsp;Filter</legend>
<table width="100%" cellspacing="5">
<tr>
<!--  <td>Kunde:</td>
  <td><input type="text" id="kunde" name="kunde" size="35" value="[KUNDE]" onclick=document.getElementById("gruppe").value='';>&nbsp;(Optional)</td>-->
  <td>Aktionscode:</td>
  <td><input type="text" id="aktionscode" name="aktionscode" size="15" value="[AKTIONSCODE]"></td>
  <td>Von:</td>
  <td><input type="text" id="von" name="von" size="15" value="[VON]"></td>
  <td>Bis:</td>
  <td><input type="text" id="bis" name="bis" size="15" value="[BIS]"></td>
  <td><input type="submit" value="Aktionscodes auswerten" name="laden"></td>
</tr>

</table>
</fieldset>
</form>

</td></tr></table>

<table width="100%>">
<tr><td>Umsatz Gesamt (netto)</td><td>Erl&ouml;se netto</td><td>Deckungsbeitrag in %</td><td>Anzahl Belege</td></tr>
<tr>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[UMSATZ]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[ERLOESE]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[DECKUNGSBEITRAG]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[ANZAHL]</td>
</tr>
</table>

[UMSATZTABELLE]

</div>
<div id="tabs-2">[TAB2]</div>
<div id="tabs-3">[TAB3]</div>
<div id="tabs-4">[TAB4]</div>
<div id="tabs-5">[TAB5]</div>
</div>

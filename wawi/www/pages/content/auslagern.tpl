<script type="text/javascript">
var intAnzahl = [MENGE];  // Anzahl gesetzter Checkboxen
var intGesamt = [MENGE];  // Gesamtanzahl Checkboxen, die gesetzt werden dürfen

function countChecks(objCheck){
  // Falls die Checkbox angewählt wurde
  if(objCheck.checked == true){
    intAnzahl++;
    // Falls die Gesamtanzahl überschritten wurde
    if(intAnzahl > intGesamt){
      alert("Maximal " + intGesamt + " auswählen!");
      intAnzahl--;                // Anzahl wieder zurücksetzen
      objCheck.checked = false;   // Checkbox wieder abwählen
    }
  // Falls eine Checkbox wieder abgewählt wird
  }else{
    intAnzahl--;  // Anzahl dekrementieren
  }
}
</script>
<fieldset>
<form action="index.php?module=lager&action=[ACTION]&cmd=[CMD]" method="post" name="eprooform">
<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>
[MESSAGELAGER]<br>
<table width="80%" align="center">
<tr valign="top">
<td align="center">
<table width="90%">
  <tr><td width="170">Lagerbewegung:</td><td align="left">
		<select name="grund">
    [STARTNICHTUMLAGERN]<option [DIFFERENZ]>Manuelle Lageranpassung</option>
    <option [PRODUKTION]>Kundenauftrag / Produktion</option>
		<option [MUSTER]>Interner Entwicklungsbedarf</option>
    <option [RMA]>RMA / Reparatur / Reklamation</option>[ENDENICHTUMLAGERN]
    [STARTUMLAGERN]<option [UMLAGERN]>Umlagern</option>[ENDEUMLAGERN]
<!--    <option [ALTE]>Alte Bestellung</option>-->
    </select></td></tr>


  <tr><td><br></td><td></td></tr>
  <tr><td><b>Menge*:</b></td><td align="left"><input type="text" name="menge" value="[MENGE]"  size="27" style="width:200px" id="menge">[MSGMENGE]</td></tr>
  <tr valign="top"><td><b>Artikelnummer*:</b></td><td align="left">[NUMMERAUTOSTART]<input type="text" name="nummer" style="width:200px" id="nummer" value="[NUMMER]" [ARTIKELSTYLE]  size="27">[NUMMERAUTOEND][MSGARTIKEL]</td></tr>

[STARTDISABLESTOCK]
  <tr><td><br><br></td><td></td></tr>
  <tr><td>Projekt:</td><td align="left">[PROJEKTSTART]<input name="projekt" id="projekt" type="text" value="[PROJEKT]" size="27" style="width:200px">[PROJEKTENDE][MSGPROJEKT]</td></tr>
  <tr><td>Kunde / Lieferant / Mitarbeiter*:</td><td align="left">[ADRESSESTART]<input type="text" name="adresse" value="[ADRESSE]" style="width:200px" id="adresse"  size="27">[ADRESSEEND][MSGADRESSE]</td></tr>

[ENDEDISABLESTOCK]
  <tr><td>Grund:</td><td><input type="text" id="grundreferenz" name="grundreferenz" value="[GRUNDREFERENZ]"  size="27" style="width:200px"></td></tr>
  <tr><td><br><br></td><td></td></tr>

[ZWISCHENLAGERINFO]
<tr><td><br></td><td></td></tr>

[BEZEICHNUNG]
</table>
<br>
</td>
<td><br><div style="height: 300px; overflow: auto;"><table>[SRNINFO]</table></div></td>
</tr>
</table>
<br><br>
<!--	


        <table width="100%">
          <tr><td>Kundennummer:</td><td><input type="text" name="kundeadressid" size="20" value="[KUNDENNUMMER]"></td>
            <td>&nbsp;</td><td>Lieferantenummer:</td><td><input type="text" name="lieferantadressid" size="20" value="[LIEFERANTENNUMMER]">
	    </td></tr>
          <tr><td>Name/Firma:</td><td><input type="text" name="name" size="20" value="[NAME]"></td>
          <td>&nbsp;</td>
            <td>Vorname:</td><td><input type="text" name="vorname" size="20" value="[VORNAME]"></td></tr>
          <tr><td>Abteilung:</td><td><input type="text" name="abteilung" size="20" value="[ABTEILUNG]"></td><td>&nbsp;</td>
            <td>Unterabteilung:</td><td><input type="text" name="unterabteilung" value="[UNTERABTEILUNG]" size="20"></td></tr>
          <tr><td>Strasse:</td><td><input type="text" name="strasse" size="20" value="[STRASSE]"></td>
          <td>&nbsp;</td>
            <td>Adresszusatz:</td><td><input type="text" name="adresszusatz" size="20" value="[ADRESSZUSATZ]"></td></tr>
          <tr><td>PLZ:</td><td><input type="text" name="plz" size="20"></td><td>&nbsp;</td>
            <td>Ort:</td><td><input type="text" name="ort" size="20"></td></tr>
          <tr><td>Land:</td><td colspan="3">[EPROO_SELECT_LAND]<input type="hidden" name="land"></td>
            </tr>
          <tr><td>USt-ID:</td><td><input type="text" name="ustid" size="20" value="[USTID]"></td><td>&nbsp;</td>
            <td>E-Mail:</td><td><input type="text" name="email" size="20" value="[EMAIL]"></td></tr>
          <tr><td>Telefon:</td><td><input type="text" name="telefon" size="20" value="[TELEFON]"></td><td>&nbsp;</td>
            <td>Telefax:</td><td><input type="text" name="telefax" size="20" value="[TELEFAX]"></td></tr>
        </table>
-->
</td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
<table width="100%"><tr><td>
 <input type="button" name="zurueck" onclick="window.location.href='';" value="Nein, doch nicht" />
</td><td align="right">
       <input type="submit" name="submit" value="Weiter" />
</td></tr></table>
</td>
    </tr>

    </tbody>
  </table>
</form>
</fieldset>


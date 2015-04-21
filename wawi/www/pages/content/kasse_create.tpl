<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Neuer Eintrag f&uuml;r Kasse: [KASSE]</a></li>
    </ul>



<div id="tabs-1">
[MESSAGE]

<form action="" method="post" name="eprooform">

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>Kasse</legend>
    <table width="100%">
          <tr><td width="300">Datum*:</td><td><input type="text" name="datum" size="10" value="[DATUM]" id="datum" [READONLY]></td></tr>
	  <tr><td>Betrag*:</td><td><input type="text" name="betrag" value="[BETRAG]" size="23" [READONLY]>&nbsp;<select name="auswahl" [DISABLED]><option value="einnahme" [EINAHME]>Einnahme</option><option value="ausgabe" [AUSGABE]>Ausgabe</option></select></td><td></tr>
    <tr><td>Belegfeld*:</td><td><input type="text" name="grund" size="40" value="[GRUND]" [READONLY]></td></tr>
	  <tr><td width="300">Adresse (optional):</td><td><input type="text" name="adresse" id="adresse" size="40" value="[ADRESSE]"></td><td></tr>
</table></fieldset>
<fieldset><legend>Erweitert</legend>
    <table width="100%">

	  <tr><td width="300">Konto:</td><td><input type="text" name="sachkonto" value="[SACHKONTO]" size="18" id="sachkonto">&nbsp;<select name="steuergruppe"><option value="0" [STANDARD]>[STANDARDSTEUERSATZ]%</option>
        <option value="1" [ERMAESSIGT]>[ERMAESSIGTSTEUERSATZ]%</option>
        <option value="2" [OHNEUST]>[OHNESTEUERSATZ]%</option></select></td><td></tr>

    <!--<tr><td width="300">Belegdatum:</td><td><input type="text" name="belegdatum" size="10" value="[BELEGDATUM]" id="belegdatum"></td></tr>-->
    <tr><td>Projekt:</td><td><input type="text" name="projekt" id="projekt" size="40" value="[PROJEKT]"></td></tr>
</table></fieldset>

<fieldset><legend>Korrektur</legend>
    <table width="100%">
	  <!--<tr><td width="300">Korrekturmarkierung:</td><td><input type="checkbox" name="storniert" value="1" [STORNIERT] id="storniert">&nbsp;</td><td></tr>-->
    <tr><td width="300">Grund:</td><td><input type="text" name="storniert_grund" id="storniert_grund" size="40" value="[STORNIERT_GRUND]">&nbsp;<i>Diese Buchung ist eine Korrektur.</i></td></tr>
<!--    <tr><td>Bearbeiter:</td><td><input type="text" name="storniert_bearbeiter" id="storniert_grund" size="40" value="[STORNIERT_BEARBEITER]" readonly></td></tr>-->
</table></fieldset>

<fieldset><legend>Sonstiges</legend>
    <table width="100%">
    <tr><td width="300">Bemerkung:</td><td><textarea name="bemerkung" id="bemerkung" rows="5" cols="40">[BEMERKUNG]</textarea></td></tr>
</table></fieldset>


</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
		<input type="button" onclick="window.location.href='index.php?module=kasse&action=edit&id=[ID]'" value="Abbrechen">
    <input type="submit" name="anlegen"
    value="Speichern" /> </td>
    </tr>
  
    </tbody>
  </table>
</form>
</div>

</div>

<script type="text/javascript">

$(document).ready(function(){

    var art = $('select[name=art]').val();

    if(art=='lieferant') {
      document.getElementById('lieferant_span').style.display="";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="none";
    }

    if(art=='kunde') {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="none";
    }

    if(art=='mitarbeiter') {

      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="";
      document.getElementById('sonstige_span').style.display="none";
    }

    if(art=='sonstige') {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="";
    }



 
});

	function onchange_art(el)  {
    if(el=='lieferant') {
      document.getElementById('lieferant_span').style.display="";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="none";
    }

    else if(el=='kunde') {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="none";
    }

    else if(el=='mitarbeiter') {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="";
      document.getElementById('sonstige_span').style.display="none";
    }
 		else {
      document.getElementById('lieferant_span').style.display="none";
      document.getElementById('kunde_span').style.display="none";
      document.getElementById('mitarbeiter_span').style.display="none";
      document.getElementById('sonstige_span').style.display="";
    } 
}

</script>

<form action="" method="post" name="eprooform">
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Zahlung</a></li>
        <li><a href="#tabs-2">Zuordnung Bestellungen</a></li>
    </ul>

<div id="tabs-1">
[FORMHANDLEREVENT]
[MESSAGE]

<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>Rechnungsdaten</legend>

    <table width="100%" border="0">

	  <tr valign="top"><td width="150">Lieferant:</td><td>[ADRESSEAUTOSTART][ADRESSE][MSGADRESSE][ADRESSEAUTOEND]</td>
          <td>&nbsp;</td>
            <td colspan="2" rowspan="2" align="center"><b style="color:green">[MELDUNG]</b>
<br><font size="7">[ID]</font>
</td></tr>

          <tr><td><br><br>Rechnungs Nr.:</td><td><br><br>[RECHNUNG][MSGRECHNUNG]</td>
          <td>&nbsp;</td>
            </tr>
 
						<tr><td>Bestellung:</td><td width="250">[DISABLESTART][BESTELLUNG][MSGBESTELLUNG][MULTIBESTELLUNG][DISABLEENDE]</td>
          <td>&nbsp;</td>
            <td width="200"></td><td></td></tr>

          <tr><td>Rechnungsdatum:</td><td width="250">[RECHNUNGSDATUM][MSGRECHNUNGSDATUM]</td>
          <td>&nbsp;</td>
            <td width="200">Zahlbar bis:</td><td>[ZAHLBARBIS][MSGZAHLBARBIS][DATUM_ZAHLBARBIS]</td></tr>

	  			<tr><td>Betrag/Total (Brutto):</td><td>[BETRAG][MSGBETRAG]&nbsp;[WAEHRUNG][MSGWAEHRUNG]</td><td>&nbsp;</td>
						<td>Skonto in %:</td><td>[SKONTO][MSGSKONTO]</td>
				</tr>

	  			<tr><td>USt. 19%:</td><td>[SUMMENORMAL][MSGSUMMENORMAL]</td><td>&nbsp;</td>
            <td>Skonto bis:</td><td>[SKONTOBIS][MSGSKONTOBIS][DATUM_SKONTOBIS]</td>
					</tr>

          <tr>
           <td>USt. 7%:</td><td>[SUMMEERMAESSIGT][MSGSUMMEERMAESSIGT]</td>
          <td>&nbsp;</td>
           <td>Umsatzsteuer</td><td>[UMSATZSTEUER][MSGUMSATZSTEUER]</td>

					</tr>

          <tr>
           <td>Verwendungszweck:</td><td>[VERWENDUNGSZWECK][MSGVERWENDUNGSZWECK]</td>
          <td>&nbsp;</td>
           <td>Frachtkosten:</td><td>[FRACHTKOSTEN][MSGFRACHTKOSTEN]</td>
					</tr>


          <tr>
           <td>Kostenstelle:</td><td>[KOSTENSTELLE][MSGKOSTENSTELLE]</td>
          <td>&nbsp;</td>
						<td>Freigabe:</td><td>[FREIGABE][MSGFREIGABE]&nbsp;<i>Wareneingangspr&uuml;fung</i>&nbsp;&nbsp;[RECHNUNGSFREIGABE][MSGRECHNUNGSFREIGABE]&nbsp;<i>Rechnungseingangspr&uuml;fung</i></td>
					</tr>

          <tr>
           <td>Sachkonto:</td><td>[SACHKONTO][MSGSACHKONTO]</td>
          <td>&nbsp;</td>
						<td></td><td></td>
					</tr>



</table>



</fieldset>
</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="center" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" /> <input type="button" onclick="window.location.href='index.php?module=verbindlichkeit&action=list'" value="Abbrechen" /></td>
    </tr>
  
    </tbody>
  </table>
</div>



<div id="tabs-2">
<div class="error">Hier m&uuml;ssen alle Bestellnummern eintragen werden. Die komplette Summe von [SUMME] muss hier aufgeteilt werden.</div>
<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
<tr><td>
<table cellspacing="5">
<tr><td><b>Bestellung</b></td><td><b>Bestell-Nr.</b></td><td><b>Teilbetrag</b></td><td><b>Bemerkung</b></td></tr>
<tr><td>Nr. 1</td><td>[BESTELLUNG1][MSGBESTELLUNG1]</td><td>[BESTELLUNG1BETRAG][MSGBESTELLUNG1BETRAG]</td><td>[BESTELLUNG1BEMERKUNG][MSGBESTELLUNG1BEMERKUNG]</td></tr>
<tr><td>Nr. 2</td><td>[BESTELLUNG2][MSGBESTELLUNG2]</td><td>[BESTELLUNG2BETRAG][MSGBESTELLUNG2BETRAG]</td><td>[BESTELLUNG2BEMERKUNG][MSGBESTELLUNG2BEMERKUNG]</td></tr>
<tr><td>Nr. 3</td><td>[BESTELLUNG3][MSGBESTELLUNG3]</td><td>[BESTELLUNG3BETRAG][MSGBESTELLUNG3BETRAG]</td><td>[BESTELLUNG3BEMERKUNG][MSGBESTELLUNG3BEMERKUNG]</td></tr>
<tr><td>Nr. 4</td><td>[BESTELLUNG4][MSGBESTELLUNG4]</td><td>[BESTELLUNG4BETRAG][MSGBESTELLUNG4BETRAG]</td><td>[BESTELLUNG4BEMERKUNG][MSGBESTELLUNG4BEMERKUNG]</td></tr>
<tr><td>Nr. 5</td><td>[BESTELLUNG5][MSGBESTELLUNG5]</td><td>[BESTELLUNG5BETRAG][MSGBESTELLUNG5BETRAG]</td><td>[BESTELLUNG5BEMERKUNG][MSGBESTELLUNG5BEMERKUNG]</td></tr>
<tr><td>Nr. 6</td><td>[BESTELLUNG6][MSGBESTELLUNG6]</td><td>[BESTELLUNG6BETRAG][MSGBESTELLUNG6BETRAG]</td><td>[BESTELLUNG6BEMERKUNG][MSGBESTELLUNG6BEMERKUNG]</td></tr>
<tr><td>Nr. 7</td><td>[BESTELLUNG7][MSGBESTELLUNG7]</td><td>[BESTELLUNG7BETRAG][MSGBESTELLUNG7BETRAG]</td><td>[BESTELLUNG7BEMERKUNG][MSGBESTELLUNG7BEMERKUNG]</td></tr>
<tr><td>Nr. 8</td><td>[BESTELLUNG8][MSGBESTELLUNG8]</td><td>[BESTELLUNG8BETRAG][MSGBESTELLUNG8BETRAG]</td><td>[BESTELLUNG8BEMERKUNG][MSGBESTELLUNG8BEMERKUNG]</td></tr>
<tr><td>Nr. 9</td><td>[BESTELLUNG9][MSGBESTELLUNG9]</td><td>[BESTELLUNG9BETRAG][MSGBESTELLUNG9BETRAG]</td><td>[BESTELLUNG9BEMERKUNG][MSGBESTELLUNG9BEMERKUNG]</td></tr>
<tr><td>Nr. 10</td><td>[BESTELLUNG10][MSGBESTELLUNG10]</td><td>[BESTELLUNG10BETRAG][MSGBESTELLUNG10BETRAG]</td><td>[BESTELLUNG10BEMERKUNG][MSGBESTELLUNG10BEMERKUNG]</td></tr>
<tr><td>Nr. 11</td><td>[BESTELLUNG11][MSGBESTELLUNG11]</td><td>[BESTELLUNG11BETRAG][MSGBESTELLUNG11BETRAG]</td><td>[BESTELLUNG11BEMERKUNG][MSGBESTELLUNG11BEMERKUNG]</td></tr>
<tr><td>Nr. 12</td><td>[BESTELLUNG12][MSGBESTELLUNG12]</td><td>[BESTELLUNG12BETRAG][MSGBESTELLUNG12BETRAG]</td><td>[BESTELLUNG12BEMERKUNG][MSGBESTELLUNG12BEMERKUNG]</td></tr>
<tr><td>Nr. 13</td><td>[BESTELLUNG13][MSGBESTELLUNG13]</td><td>[BESTELLUNG13BETRAG][MSGBESTELLUNG13BETRAG]</td><td>[BESTELLUNG13BEMERKUNG][MSGBESTELLUNG13BEMERKUNG]</td></tr>
<tr><td>Nr. 14</td><td>[BESTELLUNG14][MSGBESTELLUNG14]</td><td>[BESTELLUNG14BETRAG][MSGBESTELLUNG14BETRAG]</td><td>[BESTELLUNG14BEMERKUNG][MSGBESTELLUNG14BEMERKUNG]</td></tr>
<tr><td>Nr. 15</td><td>[BESTELLUNG15][MSGBESTELLUNG15]</td><td>[BESTELLUNG15BETRAG][MSGBESTELLUNG15BETRAG]</td><td>[BESTELLUNG15BEMERKUNG][MSGBESTELLUNG15BEMERKUNG]</td></tr>
</table>
</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="center" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" onclick="this.form.action += '#tabs-2';"
    value="Speichern" /> <input type="button" onclick="window.location.href='index.php?module=verbindlichkeit&action=list'" value="Abbrechen" /></td>
    </tr>
  
    </tbody>
  </table>

</div>
</form>

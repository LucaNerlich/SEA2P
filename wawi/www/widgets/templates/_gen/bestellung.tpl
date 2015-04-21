[SAVEPAGEREALLY]

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Bestellung</a></li>
        <li><a href="#tabs-2" onclick="callCursor();">Positionen</a></li>
       <li><a href="index.php?module=bestellung&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
      [FURTHERTABS]

    </ul>





<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[FORMHANDLEREVENT]

<center>
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center" style="-moz-box-shadow: 10px 10px 5px #888;-webkit-box-shadow: 10px 10px 5px #888;box-shadow: 10px 10px 5px #888; background-color: #EFEFEF;">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr> 
<td width="33%"></td> 
<td align="center"><b style="font-size: 14pt">Bestellung <font color="blue">[NUMMER]</font></b>[LIEFERANT]</td> 
<td width="33%" align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td> 
</tr>
</table>


<table width="100%"><tr valign="top"><td width="50%">

<fieldset><legend>Lieferant</legend>
<table width="100%">
  <tr><td width="120">Lieferanten-Nr.:</td><td nowrap>[LIEFERANTAUTOSTART][ADRESSE][MSGADRESSE][LIEFERANTAUTOEND]&nbsp;
[BUTTON_UEBERNEHMEN]
</td></tr>
</table>
</fieldset>



<fieldset><legend>Allgemein</legend>
<table width="100%">
  <tr><td>Projekt:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
  <tr><td>Status:</td><td>[STATUS]</td></tr>
  <tr><td>Ihr Angebot:</td><td>[ANGEBOT][MSGANGEBOT]</td></tr>
  <tr><td>Datum:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>Abweichende Lieferadresse:</td><td>[ABWEICHENDELIEFERADRESSE][MSGABWEICHENDELIEFERADRESSE]</td></tr>
  <tr><td>Schreibschutz:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>

</table>
</fieldset>


</td><td>


<div style="display:[ABWEICHENDELIEFERADRESSESTYLE]" id="abweichendelieferadressestyle">
<fieldset style="background-color: #FFDEAD;"><legend>Abweichende Lieferadresse</legend>
   <table height="160">
          <tr><td>Name:</td><td>[LIEFERNAME][MSGLIEFERNAME]</td></tr>
          <tr><td>Abteilung:</td><td>[LIEFERABTEILUNG][MSGLIEFERABTEILUNG]</td></tr>
          <tr><td>Unterabteilung:</td><td>[LIEFERUNTERABTEILUNG][MSGLIEFERUNTERABTEILUNG]</td></tr>
          <tr><td>Ansprechpartner:</td><td>[LIEFERANSPRECHPARTNER][MSGLIEFERANSPRECHPARTNER]</td></tr>
          <tr><td>Adresszusatz:</td><td>[LIEFERADRESSZUSATZ][MSGLIEFERADRESSZUSATZ]</td></tr>
          <tr><td>Stra&szlig;e</td><td>[LIEFERSTRASSE][MSGLIEFERSTRASSE]</td><td>&nbsp;</td></tr>
          <tr><td>PLZ/Ort</td><td>[LIEFERPLZ][MSGLIEFERPLZ]&nbsp;[LIEFERORT][MSGLIEFERORT]</td>
          </tr>
          <tr><td>Land:</td><td>[EPROO_SELECT_LIEFERLAND]</td>
          </tr>
</table>
</fieldset>
</div>


</td></tr></table>

<!--
<table width="100%"><tr><td>
<fieldset><legend>Positionen</legend>
[POSITIONEN]
</fieldset>
</td></tr></table>
-->

<table width="100%"><tr><td>
<fieldset><legend>Stammdaten</legend>
  <table align="center" border="0" width="100%">
          <tr><td width="150">Anrede:</td><td width="200">[TYP][MSGTYP]</td>
          <td width="20">&nbsp;</td>
            <td width="120"></td><td></td></tr>
          <tr><td>Name:</td><td>[NAME][MSGNAME]</td>
          <td>&nbsp;</td>
            <td>Telefon:</td><td>[TELEFON][MSGTELEFON]</td></tr>
          <tr><td>Ansprechpartner:</td><td>[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td>
          <td>&nbsp;</td>
            <td>Telefax:</td><td>[TELEFAX][MSGTELEFAX]</td></tr>
          <tr><td>Abteilung:</td><td>[ABTEILUNG][MSGABTEILUNG]</td><td>&nbsp;</td>
          <td>E-Mail:</td><td>[EMAIL][MSGEMAIL]</td></tr>
          <tr><td>Unterabteilung:</td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td><td>&nbsp;</td>
           <td>Anschreiben</td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
          <tr><td>Adresszusatz:</td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td><td>&nbsp;</td>
           <td></td><td></td></tr>
          <tr><td>Stra&szlig;e</td><td>[STRASSE][MSGSTRASSE]</td><td>&nbsp;</td>
            <td></td><td>[ANSPRECHPARTNERPOPUP]
            </td></tr>
          <tr><td>PLZ/Ort</td><td>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td><td>&nbsp;</td>
            <td></td><td></td></tr>

          <tr><td>Land:</td><td colspan="3">[EPROO_SELECT_LAND]</td>
          </tr>
</table>
</fieldset>
</td></tr></table>

<table width="100%"><tr><td>
<fieldset><legend>Freitext</legend>
[FREITEXT][MSGFREITEXT]
</fieldset>
</td></tr></table>


<table width="100%"><tr valign="top"><td width="50%">


<fieldset><legend>Bestellung</legend>
<table width="100%">
<!--<tr><td>Unsere Kundennummer:</td><td>[KUNDENNUMMER][MSGKUNDENNUMMER]</td></tr>-->
<tr><td>Bezahlung per:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>
<tr><td>Bearbeiter:</td><td>[BEARBEITER][MSGBEARBEITER]</td></tr>
<tr><td>Bestellbest&auml;tigung:</td><td>[BESTELLBESTAETIGUNG][MSGBESTELLBESTAETIGUNG]</td></tr>
<tr><td>Keine Artikelnummern:</td><td>[KEINEARTIKELNUMMERN][MSGKEINEARTIKELNUMMERN]</td></tr>
<tr><td>Keine Preise anzeigen:</td><td>[BESTELLUNGOHNEPREIS][MSGBESTELLUNGOHNEPREIS]</td></tr>
<tr><td>Artikelnummern als Artikeltext:</td><td>[ARTIKELNUMMERNINFOTEXT][MSGARTIKELNUMMERNINFOTEXT]</td></tr>
<tr><td>Kein Briefpapier:</td><td>[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>


</table>
</fieldset>


</td><td>

  <script type="text/javascript"><!--

        function aktion_buchen(cmd)
        {
          document.getElementById('rechnung').style.display="none";
          document.getElementById('kreditkarte').style.display="none";
          document.getElementById('einzugsermaechtigung').style.display="none";
          document.getElementById('paypal').style.display="none";
          document.getElementById('vorkasse').style.display="none";
          document.getElementById(cmd).style.display="";

        }

       function versand(cmd)
        {
          document.getElementById('packstation').style.display="none";
          document.getElementById(cmd).style.display="";
        }

	function abweichend(cmd)
        {
          document.getElementById('abweichendelieferadressestyle').style.display="none";
	  if(document.getElementById('abweichendelieferadresse').checked)
	    document.getElementById('abweichendelieferadressestyle').style.display="";
        }



      //-->
     </script>



<div id="rechnung" style="display:[RECHNUNG]">
<fieldset><legend>Rechnung</legend>
<table width="100%">
<tr><td>Zahlungsziel (in Tagen):</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]</td></tr>
<tr><td nowrap> Skonto (in Tagen):</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]</td></tr>
<tr><td>Skonto (in Prozent):</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]</td></tr>
</table>
</fieldset>
</div>

<div style="display:[EINZUGSERMAECHTIGUNG]" id="einzugsermaechtigung">
<fieldset><legend>Einzugserm&auml;chtigung</legend>
<table width="100%">
<tr><td width="150">Inhaber:</td><td>[BANK_INHABER][MSGBANK_INHABER]</td></tr>
<tr><td>Institut:</td><td>[BANK_INSTITUT][MSGBANK_INSTITUT]</td></tr>
<tr><td>BLZ:</td><td>[BANK_BLZ][MSGBANK_BLZ]</td></tr>
<tr><td>Konto:</td><td>[BANK_KONTO][MSGBANK_KONTO]</td></tr>
</table>
</fieldset>
</div>

<div style="display:[VORKASSE]" id="vorkasse">
<fieldset><legend>Vorkasse</legend>
<table width="100%">
<tr><td width="150">Inhaber:</td><td>[BANK_INHABER][MSGBANK_INHABER]</td></tr>
<tr><td>Institut:</td><td>[BANK_INSTITUT][MSGBANK_INSTITUT]</td></tr>
<tr><td>BLZ:</td><td>[BANK_BLZ][MSGBANK_BLZ]</td></tr>
<tr><td>Konto:</td><td>[BANK_KONTO][MSGBANK_KONTO]</td></tr>
</table>
</fieldset>

</div>

<div style="display:[PAYPAL]" id="paypal">
<fieldset><legend>Paypal</legend>
<table width="100%">
<tr><td width="150">Account:</td><td>[PAYPALACCOUNT][MSGPAYPALACCOUNT]</td></tr>
</table>
</fieldset>

</div>

<div style="display:[KREDITKARTE]" id="kreditkarte">
<fieldset><legend>Kreditkarte</legend>
 <table>
        <tr><td width="150">Kreditkarte:</td><td>[KREDITKARTE_TYP][MSGKREDITKARTE_TYP]</td>
        </tr>
        <tr><td>Karteninhaber:</td><td>[KREDITKARTE_INHABER][MSGKREDITKARTE_INHABER]</td>
	</tr>
        <tr><td>Kreditkartennummer:</td><td>[KREDITKARTE_NUMMER][MSGKREDITKARTE_NUMMER]</td>
	</tr>
        <tr><td>Pr&uuml;fnummer:</td><td>[KREDITKARTE_PRUEFNUMMER][MSGKREDITKARTE_PRUEFNUMMER]</td>
        </tr>
        <tr><td>G&uuml;ltig bis:</td><td>
        [KREDITKARTE_MONAT][MSGKREDITKARTE_MONAT]&nbsp;
        [KREDITKARTE_JAHR][MSGKREDITKARTE_JAHR]&nbsp;
        </td>
        </tr>
        </table>

</fieldset>
</div>

</td></tr></table>

<table width="100%"><tr><td>
<fieldset><legend>Interne Bemerkung</legend>
[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
</fieldset>
</td></tr></table>

<table width="100%"><tr><td>
<fieldset><legend>UST-Pr&uuml;fung</legend>
<table width="100%">
<tr><td>UST ID:</td><td>[USTID][MSGUSTID]</td></tr>
<tr><td>Besteuerung:</td><td>[UST_BEFREIT][MSGUST_BEFREIT]</td></tr>
</table>
</fieldset>
</td></tr></table>





</center>


</td>
</table>


<br><br>
<table width="100%">
<tr><td align="right">
    <input type="submit" name="speichern"
    value="Speichern" />
</td></tr></table>
</div>

<div id="tabs-2">

<center>
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center" style="-moz-box-shadow: 10px 10px 5px #888;-webkit-box-shadow: 10px 10px 5px #888;box-shadow: 10px 10px 5px #888; background-color: #EFEFEF;">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr> 
<td width="33%"></td> 
<td align="center"><b style="font-size: 14pt">Bestellung <font color="blue">[NUMMER]</font></b>[LIEFERANT]</td> 
<td width="33%" align="right">[ICONMENU2]</td> 
</tr>
</table>



[POS]

</td></tr></table>
</center>




</div>

<div id="tabs-3"></div>
      [FURTHERTABSDIV]


</form>

 <!-- tab view schließen -->
</div>


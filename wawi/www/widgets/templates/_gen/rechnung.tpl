[SAVEPAGEREALLY]
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Rechnung</a></li>
       <li><a href="#tabs-2" onclick="callCursor();">Positionen</a></li>
       <li><a href="index.php?module=rechnung&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
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
<td align="center"><b style="font-size: 14pt">Rechnung <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
<td width="33%" align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>

<fieldset><legend>Kunde</legend>
<table width="100%">
  <tr><td width="230">Kunden-Nr.:</td><td nowrap width="300">[KUNDEAUTOSTART][ADRESSE][MSGADRESSE][KUNDEAUTOEND]&nbsp;
[BUTTON_UEBERNEHMEN]
</td><td>Aktionscode: </td><td>[AKTION][MSGAKTION]</td></tr>
</table>
</fieldset>

<table width="100%"><tr valign="top"><td width="50%">
<fieldset><legend>Allgemein</legend>
<table width="100%">

  <tr><td>Projekt:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
  <tr><td>Status:</td><td>[STATUS]</td></tr>
  <tr><td>Auftrag:</td><td>[AUFTRAGID][MSGAUFTRAGID]</td></tr>
  <tr><td>Ihre Bestellnummer:</td><td>[IHREBESTELLNUMMER][MSGIHREBESTELLNUMMER]</td></tr>
  <tr><td>Lieferdatum:</td><td>[LIEFERDATUM][MSGLIEFERDATUM]</td></tr>
  <tr><td>Lieferschein:</td><td>[LIEFERSCHEINAUTOSTART][LIEFERSCHEIN][MSGLIEFERSCHEIN][LIEFERSCHEINAUTOEND]</td></tr>
  <tr><td>Datum:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>Rechnungskopie:</td><td>[DOPPEL][MSGDOPPEL]&nbsp;</td></tr>
  <tr><td>Schreibschutz:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>

</table>
</fieldset>



</td><td>

[MAHNWESENIF]
<fieldset><legend>Mahnwesen</legend>
<table width="100%">
  <tr><td colspan="2">Alle Einstellungen manuell festsetzen:&nbsp;[MAHNWESENFESTSETZEN][MSGMAHNWESENFESTSETZEN]&nbsp;</td></tr>
  <tr><td></td><td><br></td></tr>
  <tr><td nowrap>Zahlungsstatus:</td><td width="70%">[ZAHLUNGSSTATUS][MSGZAHLUNGSSTATUS]
    </td></tr>
<tr><td colspan="2">IST: [IST][MSGIST] SOLL: [SOLL][MSGSOLL]&nbsp;Skonto gegeben:&nbsp;[SKONTO_GEGEBEN][MSGSKONTO_GEGEBEN]</tr>
  <tr><td>Mahnstufe:</td><td width="70%">[MAHNWESEN][MSGMAHNWESEN]
    </td></tr>
  <tr><td>Mahndatum:</td><td>[MAHNWESEN_DATUM][MSGMAHNWESEN_DATUM]</td></tr>
  <tr><td>Sperre:</td><td>[MAHNWESEN_GESPERRT][MSGMAHNWESEN_GESPERRT]&nbsp;(nicht im Mahnwesen)</td></tr>
  <tr><td>Bemerkung:</td><td>[MAHNWESEN_INTERNEBEMERKUNG][MSGMAHNWESEN_INTERNEBEMERKUNG]</td></tr>
</table>
</fieldset>
[MAHNWESENELSE]
[MAHNWESENENDIF]




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
<br>
</fieldset>

</td></tr></table>

<table width="100%"><tr><td>
<fieldset><legend>Freitext</legend>
[FREITEXT][MSGFREITEXT]
</fieldset>
</td></tr></table>


<table width="100%"><tr valign="top"><td width="50%">


<fieldset><legend>Rechnung</legend>
<table width="100%">
<tr><td>Zahlungsweise:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>
<!--<tr><td>Buchhaltung:</td><td>[BUCHHALTUNG][MSGBUCHHALTUNG]</td></tr>-->
<tr><td>Vertrieb:</td><td>[VERTRIEB][MSGVERTRIEB]</td></tr>
<tr><td>Bearbeiter:</td><td>[BEARBEITER][MSGBEARBEITER]</td></tr>
<tr><td>Kein Briefpapier:</td><td>[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>
</table>
</fieldset>


</td><td>

  <script type="text/javascript"><!--

        function aktion_buchen(cmd)
        {
					if(cmd=="lastschrift") cmd="einzugsermaechtigung";
          document.getElementById('rechnung').style.display="none";
          document.getElementById('kreditkarte').style.display="none";
          document.getElementById('einzugsermaechtigung').style.display="none";
          document.getElementById('paypal').style.display="none";
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
<tr><td width="200">Zahlungsziel (in Tagen):</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]</td></tr>
<tr><td nowrap>Zahlungsziel Skonto (in Tagen):</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]</td></tr>
</table>
</fieldset>
</div>


<div style="display:[EINZUGSERMAECHTIGUNG]" id="einzugsermaechtigung">
<fieldset><legend>Einzugserm&auml;chtigung</legend>
<table width="100%">
<tr><td width="200">Einzugsdatum (fr&uuml;hestens):</td><td>[EINZUGSDATUM][MSGEINZUGSDATUM]</td></tr>
<!--
<tr><td width="150">Inhaber:</td><td>[BANK_INHABER][MSGBANK_INHABER]</td></tr>
<tr><td>Institut:</td><td>[BANK_INSTITUT][MSGBANK_INSTITUT]</td></tr>
<tr><td>BLZ:</td><td>[BANK_BLZ][MSGBANK_BLZ]</td></tr>
<tr><td>Konto:</td><td>[BANK_KONTO][MSGBANK_KONTO]</td></tr>
-->
</table>
</fieldset>

</div>

<div style="display:[PAYPAL]" id="paypal">
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


<div>
<fieldset><legend>Skonto (nur bei Rechnung und Lastschrift)</legend>
<table width="100%">
<tr><td width="200">Skonto:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]</td></tr>
</table>
</fieldset>
</div>


[STARTDISABLEVERBAND]
<div style="">
<fieldset><legend>Verband</legend>
<table width="100%">
[VERBANDINFOSTART]<tr><td>Verband / Gruppe:</td><td colspan="6">[VERBAND]</td></tr>[VERBANDINFOENDE]<tr><td>Rabatt:</td><td>Grund %</td><td>1 in %</td><td>2 in %</td><td>3 in %</td><td>4 in %</td><td>5 in %</td></tr>
<tr><td></td>
 <td>[RABATT][MSGRABATT]</td>
    <td>[RABATT1][MSGRABATT1]</td>
    <td>[RABATT2][MSGRABATT2]</td>
    <td>[RABATT3][MSGRABATT3]</td>
    <td>[RABATT4][MSGRABATT4]</td>
    <td>[RABATT5][MSGRABATT5]</td>
  </tr>
<tr><td colspan="7">Information:<br>[VERBANDINFO]</td></tr>
</table>
</fieldset>
</div>
[ENDEDISABLEVERBAND]

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
<tr><td>Sonstige steuerfrei:</td><td>[KEINSTEUERSATZ][MSGKEINSTEUERSATZ]&nbsp;(ohne gesetzlichen Hinweis bei EU oder Export)</td></tr>
<tr><td>Brief bestellt:</td><td>[USTBRIEF][MSGUSTBRIEF]</td></tr>
<tr><td>Brief Eingang:</td><td>[USTBRIEF_EINGANG][MSGUSTBRIEF_EINGANG]</td></tr>
<tr><td>Brief Eingang am:</td><td>[USTBRIEF_EINGANG_AM][MSGUSTBRIEF_EINGANG_AM]</td></tr>
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


</form>

<div id="tabs-2">

<center>
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center" style="-moz-box-shadow: 10px 10px 5px #888;-webkit-box-shadow: 10px 10px 5px #888;box-shadow: 10px 10px 5px #888; background-color: #EFEFEF;">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">Rechnung <font color="blue">[NUMMER]</font></b>[KUNDE][RABATTANZEIGE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>
</table>



[POS]

</td></tr></table>
</center>


</div>
<div id="tabs-3">
</div>

[FURTHERTABSDIV]
 <!-- tab view schließen -->
</div>


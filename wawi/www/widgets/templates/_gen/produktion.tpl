<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-2">Produktion</a></li>
        <li><a href="#tabs-4" onclick="callCursor();">Positionen</a></li>
        <!--<li><a href="#tabs-3">Rechnungsadresse / Kontakt</a></li>-->
    </ul>



<div id="tabs-2">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

<center>
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center" style="-moz-box-shadow: 10px 10px 5px #888;-webkit-box-shadow: 10px 10px 5px #888;box-shadow: 10px 10px 5px #888; background-color: #EFEFEF;">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%">[STATUSICONS]</td>
<td align="center"><b style="font-size: 14pt">Produktion <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td width="33%" align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>

<table width="100%"><tr valign="top"><td width="50%">


<fieldset><legend>Allgemein</legend>
<table width="100%" height="160">
  <tr><td>Kunde:</td><td width="70%">[KUNDEAUTOSTART][ADRESSE][MSGADRESSE][KUNDEAUTOEND]</td></tr>
  <tr><td>Projekt:</td><td>[PROJEKTSTART][PROJEKT][MSGPROJEKT][PROJEKTENDE]</td></tr>
  <tr><td>Status:</td><td>[STATUS]</td></tr>
  <!--<tr><td>Internet:</td><td>[INTERNET]</td></tr>-->
  <tr><td>Produktion:</td><td>[BELEGNR]</td></tr>
<!--  <tr><td>Angebot:</td><td>[ANGEBOT][MSGANGEBOT]</td></tr>-->
  <tr><td>Angelegt am:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>Schreibschutz:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>

  <tr><td><br></td><td></td></tr>
<!--  <tr><td colspan="2">Abweichende Lieferadresse:&nbsp;&nbsp;&nbsp;[ABWEICHENDELIEFERADRESSE][MSGABWEICHENDELIEFERADRESSE]</td></tr>-->

</table>
</fieldset>



</td><td>


<div style="display:[ABWEICHENDELIEFERADRESSESTYLE]" id="abweichendelieferadressestyle">
<fieldset style="background-color: #FFDEAD;"><legend>Abweichende Lieferadresse</legend>
   <table height="160">
          <tr><td width="200">Name:</td><td>[LIEFERNAME][MSGLIEFERNAME]</td></tr>
          <tr><td>Abteilung:</td><td>[LIEFERABTEILUNG][MSGLIEFERABTEILUNG]</td></tr>
          <tr><td>Unterabteilung:</td><td>[LIEFERUNTERABTEILUNG][MSGLIEFERUNTERABTEILUNG]</td></tr>
          <tr><td>Ansprechpartner:</td><td>[LIEFERANSPRECHPARTNER][MSGLIEFERANSPRECHPARTNER]</td></tr>
          <tr><td>Adresszusatz:</td><td>[LIEFERADRESSZUSATZ][MSGLIEFERADRESSZUSATZ]</td></tr>
          <tr><td>Stra&szlig;e</td><td>[LIEFERSTRASSE][MSGLIEFERSTRASSE]</td><td>&nbsp;</td></tr>
          <tr><td>PLZ/Ort</td><td>[LIEFERPLZ][MSGLIEFERPLZ]&nbsp;[LIEFERORT][MSGLIEFERORT]</td>
          </tr>
          <tr><td>Land:</td><td>[EPROO_SELECT_LIEFERLAND]</td>
          </tr>
				<tr><td></td><td>[LIEFERADRESSEPOPUP]</td></tr>
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

<!--
<table width="100%"><tr><td>
<fieldset><legend>Bezeichnung</legend>
[BEZEICHNUNG][MSGBEZEICHNUNG]
</fieldset>
</td></tr></table>
-->

<table width="100%"><tr><td>
<fieldset><legend>Einstellung Produktion</legend>
<table>
<tr><td width="200">Reservierungen der Artikel:</td><td>
[RESERVIERART][MSGRESERVIERART]
</td></tr>
<tr><td>Entnahme im Lager:</td><td>[AUSLAGERART][MSGAUSLAGERART]</td></tr>
<tr><td>Produktions Tag:</td><td>[DATUMPRODUKTION][MSGDATUMPRODUKTION]</td></tr>
</table>
</fieldset>
</td></tr></table>



<table width="100%"><tr><td>
<fieldset><legend>Freitext</legend>
[FREITEXT][MSGFREITEXT]
</fieldset>
</td></tr></table>

<!--
<table width="100%"><tr valign="top"><td width="50%">


<fieldset><legend>Produktion</legend>
<table width="100%">
<tr><td>Auftragsart:</td><td>[ART][MSGART]</td></tr>
<tr><td>Versandart:</td><td>[VERSANDART][MSGVERSANDART]</td></tr>
<tr><td>Zahlungsweise:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>
  <tr><td colspan="2"><table><tr><td>Auto-Versand:</td><td>[AUTOVERSAND][MSGAUTOVERSAND]</td></tr>
  <tr><td>Porto freie Lieferung:</td><td>[KEINPORTO][MSGKEINPORTO]
  <tr><td>Keine Stornomail:</td><td>[KEINESTORNOMAIL][MSGKEINESTORNOMAIL]
  <tr><td>Keine Trackingmail:</td><td>[KEINETRACKINGMAIL][MSGKEINETRACKINGMAIL]

    </table></td></tr>
<tr><td>Vertrieb:</td><td>[VERTRIEB][MSGVERTRIEB]</td></tr>
<tr><td>Bearbeiter:</td><td>[BEARBEITER][MSGBEARBEITER]</td></tr>

</table>
</fieldset>


</td><td>

  <script type="text/javascript">

        function aktion_buchen(cmd)
        {
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



     </script>



<div id="rechnung" style="display:[RECHNUNG]">
<fieldset><legend>Rechnung</legend>
<table width="100%">
<tr><td>Zahlungsziel (in Tagen):</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]</td></tr>
<tr><td nowrap>Zahlungsziel Skonto (in Tagen):</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]</td></tr>
<tr><td>Skonto:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]</td></tr>
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



</td></tr></table>
-->

<table width="100%"><tr><td>
<fieldset><legend>Interne Bemerkung</legend>
[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
</fieldset>
</td></tr></table>
<!--
<table width="100%"><tr><td>
<fieldset><legend>UST-Pr&uuml;fung</legend>
<table width="100%">
<tr><td>UST ID:</td><td>[USTID][MSGUSTID]</td></tr>
<tr><td>Besteuerung:</td><td>[UST_BEFREIT][MSGUST_BEFREIT]</td></tr>
<tr><td>UST-ID gepr&uuml;ft:</td><td>[UST_OK][MSGUST_OK]&nbsp;(Auftrag darf versendet werden)</td></tr>
</table>
</fieldset>
</td></tr></table>
-->
</center>


</td>
</table>
<div style="display:[PACKSTATION]" id="packstation">
<fieldset style="background-color: #FFDEAD;"><legend>Packstation</legend>
 <table>
        <tr><td width="150">Inhaber:</td><td>[PACKSTATION_INHABER][MSGPACKSTATION_INHABER]</td>
        </tr>
        <tr><td>Packstation:</td><td>[PACKSTATION_STATION][MSGPACKSTATION_STATION]</td>
	</tr>
        <tr><td>PostCardIdent:</td><td>[PACKSTATION_IDENT][MSGPACKSTATION_IDENT]</td>
	</tr>
        <tr><td>PLZ / Ort:</td><td>[PACKSTATION_PLZ][MSGPACKSTATION_PLZ]&nbsp;[PACKSTATION_ORT][MSGPACKSTATION_ORT]</td>
        </tr>
        </table>
</fieldset>
</div>
<br><br>
<table width="100%">
<tr><td align="center">
    <input type="submit" name="speichern"
    value="Speichern" />
</td></tr></table>
</div>

</form>

<div id="tabs-4">

<center>
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center" style="-moz-box-shadow: 10px 10px 5px #888;-webkit-box-shadow: 10px 10px 5px #888;box-shadow: 10px 10px 5px #888; background-color: #EFEFEF;">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%">[STATUSICONS]</td>
<td align="center"><b style="font-size: 14pt">Produktion <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>
</table>


[POS]

</td></tr></table>
</center>




</div>


 <!-- tab view schließen -->
</div>


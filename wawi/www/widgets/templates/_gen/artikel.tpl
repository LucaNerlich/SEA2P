<script type="text/javascript">

	$(document).ready(function(){

 		document.getElementById('rabattstyle').style.display="none";

    if(document.getElementById('rabatt').checked)
      document.getElementById('rabattstyle').style.display="";

  });


  function rabattevent()
        {
          document.getElementById('rabattstyle').style.display="none";
    if(document.getElementById('rabatt').checked)
      document.getElementById('rabattstyle').style.display="";
        }


	function juststuecklisteevent(cmd)
  {
    		if(document.getElementById('juststueckliste').checked)
				{
					document.getElementById("stueckliste").checked = true;
				}
				else
				{
					document.getElementById("stueckliste").checked = false;
				}
					
   }
      //-->
</script>

[SAVEPAGEREALLY]

<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Artikel</a></li>
        <li><a href="#tabs-2">Texte und Beschreibungen</a></li>
[DISABLEOPENPARAMETER]<li><a href="#tabs-3">Parameter und Freifelder</a></li>[DISABLECLOSEPARAMETER]
        <li><a href="#tabs-4">Online-Shop Optionen</a></li>
        <li><a href="#tabs-5">Export</a></li>
    </ul>

<!-- ende gehort zu tabview -->

<div id="tabs-1">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td colspan="3">


[MESSAGE]


<fieldset><legend>&nbsp;Name und Nummer des Artikel&nbsp;</legend>

<table align="center" cellspacing="5" border="0">
<tr><td width="170">Artikel (DE):</td><td colspan="4">[NAME_DE][MSGNAME_DE]</td></tr>
<tr><td width="170">Artikel Nr.:</td><td width="180">[NUMMER][MSGNUMMER]</td><td width="20">&nbsp;</td><td width="150">Projekt:</td><td width="170">[PROJEKTSTART][PROJEKT][MSGPROJEKT][PROJEKTENDE]</td></tr>
<tr><td>Artikelkategorie</td><td>[TYP][MSGTYP]
              </td><td></td><td>Standardlieferant:</td><td>[LIEFERANTSTART][ADRESSE][MSGADRESSE][LIEFERANTENDE]</td></tr>
 <tr><td nowrap>Artikelbeschreibung (DE):<br><i>f&uuml;r Angebote, Auftr&auml;ge, etc.</i></td><td colspan="4">[ANABREGS_TEXT][MSGANABREGS_TEXT]</td></tr>
 <tr><td nowrap>Kurztext (DE):<br><i>f&uuml;r Suche oder Online-Shops</i></td><td colspan="4">[KURZTEXT_DE][MSGKURZTEXT_DE]</td></tr>

<tr><td>Interner Kommentar:</td><td colspan="4">[INTERNERKOMMENTAR][MSGINTERNERKOMMENTAR]</td><tr>

<tr><td width="170">Hersteller:</td><td>[HERSTELLERSTART][HERSTELLER][MSGHERSTELLER][HERSTELLERENDE]</td><td width="20">&nbsp;</td><td width="150">Min. Lagermenge:</td><td>[MINDESTLAGER][MSGMINDESTLAGER]</td></tr>
<tr><td width="170">Herstellerlink:</td><td>[HERSTELLERLINKSTART][HERSTELLERLINK][MSGHERSTELLERLINK][HERSTELLERLINKENDE]</td><td width="20">&nbsp;</td><td width="150">Min. Bestellmenge:</td><td>[MINDESTBESTELLUNG][MSGMINDESTBESTELLUNG]</td></tr>
<tr><td width="170">Hersteller Nr.:</td><td>[HERSTELLERNUMMER][MSGHERSTELLERNUMMER]</td><td width="20">&nbsp;</td><td width="150">Standardlager:</td><td>[LAGER_PLATZSTART][LAGER_PLATZ][MSGLAGER_PLATZ][LAGER_PLATZENDE]</td></tr>

<tr><td width="170">EAN Nr.:</td><td>[EAN][MSGEAN]</td><td width="20">&nbsp;</td><td width="150">Gewicht:</td><td>[GEWICHT][MSGGEWICHT]</td></tr>

<tr><td></td><td></td>
<td width="20">&nbsp;</td><td width="150">Einheit:</td><td>[EINHEIT][MSGEINHEIT]</td></tr>
</table>



</fieldset>
<table>
<tr><td width="50%">
<fieldset><legend>&nbsp;Online-Shop&nbsp;</legend>
<table align="right" cellspacing="5" border="0">
    <tr><td width="250">Artikel ausverkauft:</td><td width="180">[AUSVERKAUFT][MSGAUSVERKAUFT]&nbsp;</td></tr>
    <tr><td width="">Artikel inaktiv:</td><td>[INAKTIV][MSGINAKTIV]&nbsp;</td></tr>
    <tr><td width="">Restmenge (Abverkauf)</td><td>[RESTMENGE][MSGRESTMENGE]</td></tr>
</table>
</fieldset>
</td><td>
<fieldset><legend>&nbsp;Artikel Optionen&nbsp;</legend>
<table align="right" cellspacing="5" border="0">
    <tr><td width="250"><font color="#961F1C">Lagerartikel:</font></td><td width="340">[LAGERARTIKEL][MSGLAGERARTIKEL]</td></tr>
    <tr><td width=""><font color="#961F1C">Artikel ist Porto:</font></td><td>[PORTO][MSGPORTO]</td></tr>
    <tr><td width=""><font color="#961F1C">Artikel ist Rabatt:</font></td><td width="200">[RABATT][MSGRABATT]&nbsp;<span id="rabattstyle">[RABATT_PROZENT][MSGRABATT_PROZENT] in %</span></td></tr>
</table>
</fieldset>

</td></tr></table>

[ARTIKELKUNDENSPEZIFISCH]


<fieldset><legend>&nbsp;Varianten&nbsp;</legend>
<table align="center" cellspacing="5" width="100%">
 <tr><td width="65"></td><td width="250">Variante:</td><td width="180">[VARIANTE][MSGVARIANTE]</td><td width="20"></td><td width="150">Variante von Artikel:</td><td>[ARTIKELSTART][VARIANTE_VON][MSGVARIANTE_VON][ARTIKELENDE]</td></tr>

</table>

</fieldset>


<fieldset><legend>&nbsp;Sonstige Einstellung&nbsp;</legend>

<table align="right" cellspacing="5" border="0">
<tr>
<td>Erm&auml;&szlig;igte Umsatzsteuer:</td><td>[UMSATZSTEUER][MSGUMSATZSTEUER]</td>
  <td width="20">&nbsp;</td>
  <td width="150">St&uuml;ckliste:</td><td>[STUECKLISTE][MSGSTUECKLISTE]</td>
</tr>

<tr>
<td width="250">Kein Rabatt erlaubt</td><td width="180">[KEINRABATTERLAUBT][MSGKEINRABATTERLAUBT]</td>
<td></td>
<td>Just-In-Time St&uuml;ckliste:</td><td>[JUSTSTUECKLISTE][MSGJUSTSTUECKLISTE]<i>(Explodiert im Auftrag)</i>&nbsp;[KEINEEINZELARTIKELANZEIGEN][MSGKEINEEINZELARTIKELANZEIGEN]&nbsp;<i>Einzelpos. ausblenden</td>
</tr>
<tr>
<td width="170">Chargenverwaltung:</td><td width="180">[CHARGENVERWALTUNG][MSGCHARGENVERWALTUNG]</td>
<td></td>
<td><!--Auto-Bestellung:--></td><td><!--[AUTOBESTELLUNG][MSGAUTOBESTELLUNG]--></td>
</tr>


<tr><td width="170">Seriennummern:</td><td> [SERIENNUMMERN][MSGSERIENNUMMERN] </td>
<td></td>
<td>Produktionsartikel:</td><td>[PRODUKTION][MSGPRODUKTION]</td>
</tr>

<tr>
<td>Mindesthaltbarkeitsdatum:</td><td>[MINDESTHALTBARKEITSDATUM][MSGMINDESTHALTBARKEITSDATUM]</td>
<td></td>
<td>Endmontage:</td><td>[ENDMONTAGE][MSGENDMONTAGE]<i>(Bei Auftragsversand ist eine Endmontage notwendig)</i></td>
</tr>


<tr>
<td></td><td></td>
<td></td>
<td>Ger&auml;t:</td><td>[GERAET][MSGGERAET]<i>(Protokoll beim Kunden verf&uuml;gbar.)</i></td>
</tr>

<tr>
<td></td><td></td>
<td></td>
<td>Serviceartikel:</td><td>[SERVICEARTIKEL][MSGSERVICEARTIKEL]<i>(Protokoll beim Kunden verf&uuml;gbar.)</i></td>
</tr>




<tr>
<td></td>
<td></td><td></td>
</tr>

</table>
</fieldset>
<fieldset><legend>&nbsp;Sperre&nbsp;</legend>
<table align="center" cellspacing="5" border="0">
<tr><td width="170">Interner Sperre:</td><td colspan="4">[INTERN_GESPERRTGRUND][MSGINTERN_GESPERRTGRUND]</td><tr>
<tr><td>Sperre aktiv:</td><td colspan="4">[INTERN_GESPERRT][MSGINTERN_GESPERRT]</td><tr>
</table>
</fieldset>



<fieldset><legend>&nbsp;Kundenfreigabe&nbsp;</legend>
<table align="center" cellspacing="5" border="0">
<tr><td>Kundenfreigabe Pr&uuml;fung notwendig:</td><td colspan="4">[FREIGABENOTWENDIG][MSGFREIGABENOTWENDIG]&nbsp;<i>z.B. Artikel der nur an Fachleute verkauft werden darf.</i></td><tr>
<tr><td>Freigabe Regel:</td><td colspan="4">[FREIGABEREGEL][MSGFREIGABEREGEL]</td><tr>
</table>
</fieldset>




        </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-1';"/> [ABBRECHEN]</td>
    </tr>


    </tbody>
  </table>

 </div>
<div id="tabs-2">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td colspan="3">

<fieldset><legend>&nbsp;Beschreibung&nbsp;</legend>
<table cellspacing="5">

				<tr valign="top"><td><br><i>siehe Tab "Artikel"</i></td><td width="20"></td>
	      <td>Artikel (EN):<br>[NAME_EN][MSGNAME_EN]</td></tr>


				<tr><td>Kurztext (DE):<br><i>siehe Tab "Artikel"</i></td><td width="20"></td>
	      <td>Kurztext (EN):<br>[KURZTEXT_EN][MSGKURZTEXT_EN]</td></tr>


	    	<tr><td>&Uuml;bersicht (DE):<br>[UEBERSICHT_DE][MSGUEBERSICHT_DE]</td><td width="20"></td>
	      <td>&Uuml;bersicht (EN):<br>[UEBERSICHT_EN][MSGUEBERSICHT_EN]</td></tr>
	      <tr><td nowrap>Beschreibung (DE):<br>[BESCHREIBUNG_DE][MSGBESCHREIBUNG_DE]</td><td width="20"></td>
	      <td>Beschreibung (EN):<br>[BESCHREIBUNG_EN][MSGBESCHREIBUNG_EN]</td></tr>
	      <tr><td>Links (DE):<br>[LINKS_DE][MSGLINKS_DE]</td><td width="20"></td>
	      <td>Links (EN):<br>[LINKS_EN][MSGLINKS_EN]</td></tr>
	      <tr><td>Startseite (DE):<br>[STARTSEITE_DE][MSGSTARTSEITE_DE]</td><td width="20"></td>
	      <td>Startseite (EN):<br>[STARTSEITE_EN][MSGSTARTSEITE_EN]</td></tr>

</table>
</fieldset>


<fieldset><legend>&nbsp;Katalog&nbsp;</legend>

<table cellspacing="5" border="0">
	     <tr><td width="300" colspan="3">Katalogartikel:&nbsp;[KATALOG][MSGKATALOG]</td></tr>
	     <tr><td width="300">Bezeichnung (DE):<br>[KATALOGBEZEICHNUNG_DE][MSGKATALOGBEZEICHNUNG_DE]</td><td width="40"></td>
	      <td>Bezeichnung (EN):<br>[KATALOGBEZEICHNUNG_EN][MSGKATALOGBEZEICHNUNG_EN]</td></tr>
	     <tr><td>Katalogtext (DE):<br>[KATALOGTEXT_DE][MSGKATALOGTEXT_DE]</td><td width="40"></td>
	     <td>Katalogtext (EN):<br>[KATALOGTEXT_EN][MSGKATALOGTEXT_EN]</td></tr>
</table>
</fieldset>

        </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" onclick="this.form.action += '#tabs-2';"/>  [ABBRECHEN]</td>
    </tr>


    </tbody>
  </table>

 </div>

[DISABLEOPENPARAMETER]

<div id="tabs-3">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td colspan="3">


<fieldset><legend>&nbsp;Parameter und Freifelder</legend>

<table align="center" cellspacing="5" border="0">

<tr><td width="170">[FREIFELD1BEZEICHNUNG]:</td><td>[FREIFELD1START][FREIFELD1][MSGFREIFELD1][FREIFELD1ENDE]</td><td width="20">&nbsp;</td><td width="150">[FREIFELD2BEZEICHNUNG]:</td><td>[FREIFELD2START][FREIFELD2][MSGFREIFELD2][FREIFELD2ENDE]</td></tr>
<tr><td width="170">[FREIFELD3BEZEICHNUNG]:</td><td>[FREIFELD3START][FREIFELD3][MSGFREIFELD3][FREIFELD3ENDE]</td><td width="20">&nbsp;</td><td width="150">[FREIFELD4BEZEICHNUNG]:</td><td>[FREIFELD4START][FREIFELD4][MSGFREIFELD4][FREIFELD4ENDE]</td></tr>
<tr><td width="170">[FREIFELD5BEZEICHNUNG]:</td><td>[FREIFELD5START][FREIFELD5][MSGFREIFELD5][FREIFELD5ENDE]</td><td width="20">&nbsp;</td><td width="150">[FREIFELD6BEZEICHNUNG]:</td><td>[FREIFELD6START][FREIFELD6][MSGFREIFELD6][FREIFELD6ENDE]</td></tr>

</table>



</fieldset>
 </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" onclick="this.form.action += '#tabs-3';"/>  [ABBRECHEN]</td>
    </tr>


    </tbody>
  </table>



</div>
[DISABLECLOSEPARAMETER]

<div id="tabs-4">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td colspan="3">


<fieldset><legend>&nbsp;Online-Shops</legend>
<table align="center" cellspacing="5" width="700">
<tr><td width="170"><font color="#961F1C">Shop (1):</font></td><td width="180">[SHOPSTART][SHOP][MSGSHOP][SHOPENDE]
                </td><td width="20"></td><td width="150"></td><td></td></tr>
<tr><td width="170"><font color="#961F1C">Shop (2):</font></td><td width="180">[SHOP2START][SHOP2][MSGSHOP2][SHOP2ENDE]
                </td><td width="20"></td><td width="150"></td><td></td></tr>
<tr><td width="170"><font color="#961F1C">Shop (3):</font></td><td width="180">[SHOP3START][SHOP3][MSGSHOP3][SHOP3ENDE]
                </td><td width="20"></td><td width="150"></td><td></td></tr>

</table>

</fieldset>

<fieldset><legend>&nbsp;Online-Shop Optionen</legend>
<table align="center" cellspacing="5" width="700">
<tr><td width="170">Shop-Optionen:</font></td><td width="180">[OPTIONEN]
                </td><td width="20"></td><td width="150">Partnerprogramm Sperre:</td><td>[PARTNERPROGRAMM_SPERRE][MSGPARTNERPROGRAMM_SPERRE]</td></tr>
 
 <tr><td>Automatischer Abgleich Lagerzahlen:</td><td>[AUTOLAGERLAMPE][MSGAUTOLAGERLAMPE]</td><td></td><td>Neu:</td><td>[NEU][MSGNEU]</td></tr>
   <tr><td></td><td>
	      </td><td></td><td>TopSeller:</td><td>[TOPSELLER][MSGTOPSELLER]</td></tr>

      <tr><td>Lieferzeit in Tagen<br>(unabh&auml;ngig von Grundeinstellung):</td><td>[LIEFERZEITMANUELL][MSGLIEFERZEITMANUELL]</td><td></td><td>Startseite:</td><td>[STARTSEITE][MSGSTARTSEITE]</td></tr>
      <tr><td></td><td></td><td></td><td>Reihenfolge <br>(Gr&ouml;&szlig;er = oben)</td><td>[WICHTIG][MSGWICHTIG]</td></tr>
      <tr><td>Pseudo Preis (Brutto):</td><td>[PSEUDOPREIS][MSGPSEUDOPREIS]</td><td></td><td>Cache Lagerzahl</td><td>[CACHE_LAGERPLATZINHALTMENGE][MSGCACHE_LAGERPLATZINHALTMENGE]</td></tr>
 <!--<tr><td>Sonderaktion (DE):</td><td>[SONDERAKTION][MSGSONDERAKTION]</td><td></td><td></td><td></td></tr>
 <tr><td>Sonderaktion (EN):</td><td>[SONDERAKTION_EN][MSGSONDERAKTION_EN]</td><td></td><td></td><td></td></tr>-->
</table>
</fieldset>

<fieldset><legend>&nbsp;Online-Shop Abgleich&nbsp;</legend>
<table align="center" cellspacing="5" width="700">
 <tr><td width="170">Auto-Abgleich:</td><td colspan="4">[AUTOABGLEICHERLAUBT][MSGAUTOABGLEICHERLAUBT]&nbsp;<i>Preis und Artikelname von Online-Shop bei Auftragsimport &uuml;bernehmen</i></td></tr>
</table>

</fieldset>
        </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern" value="Speichern" onclick="this.form.action += '#tabs-4';" />
    [ABBRECHEN]</td>
    </tr>


    </tbody>
  </table>
</div>
<div id="tabs-5">
<fieldset><legend>&nbsp;Online-Shop Update</legend>
[MESSAGE]
<table align="center" cellspacing="5">

      <tr><td colspan="2">[SHOPEXPORBUTTON]</td><td></td><td></td><td></td></tr>
</table>

</fieldset>



</div>



<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->
</form>

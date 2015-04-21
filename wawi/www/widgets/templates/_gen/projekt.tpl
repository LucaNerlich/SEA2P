<form action="" method="post" name="eprooform">
      [FORMHANDLEREVENT]

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Grundeinstellungen</a></li>
        <li><a href="#tabs-2">Zeitkonto</a></li>
        <li><a href="#tabs-3">Logistik / Versand</a></li>
        <li><a href="#tabs-4">Eigene Nummernkreise</a></li>
        <li><a href="#tabs-5">Steuer / W&auml;hrung</a></li>
    </ul>

<div id="tabs-1">
<table cellspacing="5" width="100%">
<tr><td>
[MESSAGE]


	  <fieldset><legend>Allgemein</legend>
          <table border="0" width="100%">
	      <tr><td width="300">Bezeichnung:</td><td>[NAME][MSGNAME]</td></tr>
	      <tr><td>Abk&uuml;rzung (in GROSSBUCHSTABEN):</td><td>[ABKUERZUNGSTART][ABKUERZUNG][MSGABKUERZUNG][ABKUERZUNGENDE]&nbsp;<i>(Ohne Leer- und Sonderzeichen!)</i></td></tr>
	      <tr><td>Beschreibung:</td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td></tr>
	      <tr><td>Sonstiges:</td><td>[SONSTIGES][MSGSONSTIGES]</td></tr>
	      <tr><td width="300">Farbe:</td><td>[FARBE][MSGFARBE]</td></tr>
        <tr><td>Verkaufszahlen:</td><td>[VERKAUFSZAHLENDIAGRAM][MSGVERKAUFSZAHLENDIAGRAM]&nbsp; <i>Anzeige in Verkaufszahlendiagramm</i></td></tr>
              <tr><td width="300">Rechte: </td><td>[OEFFENTLICH][MSGOEFFENTLICH]&nbsp;&Ouml;ffentlich f&uuml;r alle Mitarbeiter</td></tr>
          </table>
	  </fieldset>

	  <fieldset><legend>Buchhaltung</legend>
          <table border="0" width="100%">
              <tr><td width="300">Zahlungsmail:</td><td>[ZAHLUNGSERINNERUNG][MSGZAHLUNGSERINNERUNG]&nbsp;Optional Bedingungen:&nbsp;[ZAHLUNGSMAILBEDINUNGEN][MSGZAHLUNGSMAILBEDINUNGEN]</td></tr>
              <tr><td>Stornomail:</td><td>[STORNOMAIL][MSGSTORNOMAIL]&nbsp; bei Stornierung E-Mail <b>Stornierung</b> an Kunden</td></tr>
          </table>
	  </fieldset>

	  <fieldset><legend>Briefpapier Einstellungen</legend>
          <table border="0" width="100%">
              <tr><td width="300">Eigenes Briefpapier f&uuml;r Projekt:</td><td>[SPEZIALLIEFERSCHEIN][MSGSPEZIALLIEFERSCHEIN]&nbsp;</td></tr>
              <tr><td>Beschriftung:</td><td>[SPEZIALLIEFERSCHEINBESCHRIFTUNG][MSGSPEZIALLIEFERSCHEINBESCHRIFTUNG]&nbsp;(mit Beschriftung Header und Footer wie bei Firmendaten)</td></tr>
          </table>
	  </fieldset>
 


</td></tr>
<!-- speichern -->
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern"
    value="Speichern" /> <input type="button" value="Abbrechen" /></td>
    </tr>
  </table>
</div>

<div id="tabs-2">

<table cellspacing="5" width="100%">
<tr><td>
[MESSAGE]


	  <fieldset><legend>Projekt Management</legend>
          <table border="0" width="100%">
	      <tr><td>Verantwortlicher:</td><td>[VERANTWORTLICHERSTART][VERANTWORTLICHER][MSGVERANTWORTLICHER][VERANTWORTLICHERENDE]
	      </td></tr>

			  <tr><td>Abrechnungsart:</td><td>[ABRECHNUNGSART][MSGABRECHNUNGSART]</td></tr>
			  <tr><td>Kunde:</td><td>[KUNDEAUTOSTART][KUNDE][MSGKUNDE][KUNDEAUTOEND]</td></tr>
        <tr><td>Auftrag:</td><td>[AUFTRAGAUTOSTART][AUFTRAGID][MSGAUFTRAGID][AUFTRAGAUTOEND]</td></tr>

	      <!--<tr><td width="300">Gesamtstunden (max.):</td><td>[GESAMTSTUNDEN_MAX][MSGGESAMTSTUNDEN_MAX]</td></tr>-->
        <tr><td>Abgeschlossen:</td><td>[AKTIV][MSGAKTIV]</td></tr>
				</table>
		</fieldset>

</td></tr>
<!-- speichern -->
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-2';"/> <input type="button" value="Abbrechen" /></td>
    </tr>
  </table>
</div>




<div id="tabs-3">

<table cellspacing="5" width="100%">
<tr><td>
[MESSAGE]

	  <fieldset><legend>Versandprozess und Kommissionierung</legend>
          <table border="0" width="100%">
  					<tr><td width="300">Kommissionierverfahren:</td><td>[KOMMISSIONIERVERFAHREN][MSGKOMMISSIONIERVERFAHREN]</td></tr>
<!--           <tr><td width="300">Automatisch Versand anlegen:</td><td>[AUTOVERSAND][MSGAUTOVERSAND]&nbsp;<i>Bei Auftr&auml;gen ist die Option "per Versandzentrum versenden" automatisch gesetzt.</i></td></tr>-->
						<tr><td>Drucker Stufe (Komissionierung)</td><td>[DRUCKERLOGISTIKSTUFE1][MSGDRUCKERLOGISTIKSTUFE1]&nbsp;<i>z.B. Lieferschein drucken</i></td></tr>
						<tr><td>Drucker Stufe (Versand)</td><td>[DRUCKERLOGISTIKSTUFE2][MSGDRUCKERLOGISTIKSTUFE2]&nbsp;<i>Belege bei Versandstation</i></td></tr>
			</table>
		</fieldset>
	 	  <fieldset><legend>Stufe (Versand) an Versandstation</legend>
          <table border="0" width="100%">
           <tr><td width="300"></td><td>Drucker</td><td width="30%">Anzahl Exemplare</td><td width="30%">E-Mail</td></tr>
           <tr><td width="300">Versandbest&auml;tigung/Tracking:</td><td></td><td></td><td>[AUTOMAILVERSANDBESTAETIGUNG][MSGAUTOMAILVERSANDBESTAETIGUNG]</td></tr>
           <tr><td width="300">Rechnung:</td><td>[AUTODRUCKRECHNUNG][MSGAUTODRUCKRECHNUNG]</td><td>[AUTODRUCKRECHNUNGMENGE][MSGAUTODRUCKRECHNUNGMENGE]</td><td>[AUTOMAILRECHNUNG][MSGAUTOMAILRECHNUNG]</td></tr>
           <tr><td width="300">Lieferschein:</td><td>[AUTODRUCKLIEFERSCHEIN][MSGAUTODRUCKLIEFERSCHEIN]</td><td>[AUTODRUCKLIEFERSCHEINMENGE][MSGAUTODRUCKLIEFERSCHEINMENGE]</td><td>[AUTOMAILLIEFERSCHEIN][MSGAUTOMAILLIEFERSCHEIN]</td></tr>
           <tr><td width="300">PDF Anhang bei Auftrag:</td><td>[AUTODRUCKANHANG][MSGAUTODRUCKANHANG]</td><td></td><td>[AUTOMAILANHANG][MSGAUTOMAILANHANG]</td></tr>
           <tr><td width="300">Stornobenachrichtigung:</td><td></td><td></td><td>[STORNOMAIL][MSGSTORNOMAIL]</td></tr>
 					<tr><td>Zahlungsmail:</td><td></td><td></td><td>[ZAHLUNGSERINNERUNG][MSGZAHLUNGSERINNERUNG]</td></tr>
          </table>
	  </fieldset>
  <fieldset><legend>Optionen</legend>
          <table border="0" width="100%">
          <!--<tr><td width="300">Wechsel auf einstufige Kommissionierung:</td><td>[WECHSELAUFEINSTUFIG][MSGWECHSELAUFEINSTUFIG]&nbsp; <i>Wenn mehr als x Artikel in einem Auftrag sind.</i></td></tr>-->
          <tr><td>Auto-Reservierung im Lager:</td><td>[RESERVIERUNG][MSGRESERVIERUNG]&nbsp;<i>(f&uuml;r alle bereits <b>freigegebenen</b> Auftr&auml;ge)</i></td></tr>
          <tr><td>EAN, Hersteller- oder Artikel-Nr. scanbar:</td><td>[EANHERSTELLERSCAN][MSGEANHERSTELLERSCAN]&nbsp;<i>(Wenn Artikelnummer gescannt wird andere erlauben.)</i></td></tr>
          <tr><td>Selbstabholer Mail:</td><td>[SELBSTABHOLERMAIL][MSGSELBSTABHOLERMAIL]&nbsp;<i>(Automatische Mail bei Auftragsversand.)</i></td></tr>
          <!--<tr><td>Projekt&uuml;bergreifende Kommissionierung:</td><td>[PROJEKTUEBERGREIFENDKOMMISIONIEREN][MSGPROJEKTUEBERGREIFENDKOMMISIONIEREN]</td></tr>-->
 					<tr><td width="300">Folgebest&auml;tigung:</td><td>[FOLGEBESTAETIGUNG][MSGFOLGEBESTAETIGUNG]&nbsp;<i>(Regelm&auml;&szlig;ige E-Mail an Kunden wenn Ware noch nicht versendet.)</i></td></tr>
          <tr><td>Porto-Check:</td><td>[PORTOCHECK][MSGPORTOCHECK]&nbsp;(<i>Auftrag wird nur gr&uuml;n wenn Porto als Artikel vorhanden ist.)</i></td></tr>
          <tr><td>Auftrags-Check installiert:</td><td>[CHECKOK][MSGCHECKOK]&nbsp;Funktion:&nbsp;[CHECKNAME][MSGCHECKNAME]</td></tr>
           <tr><td width="300">Online-Shop Projekt: </td><td>[SHOPZWANGSPROJEKT][MSGSHOPZWANGSPROJEKT]&nbsp;<i>Auftrag wird sobald ein Artikel aus diesem Projekt verwendet wird bei Import auf dieses Projekt gebucht</i></td></tr>
          <tr><td>Kundenfreigabe l&ouml;schen:</td><td>[KUNDENFREIGABE_LOESCHEN][MSGKUNDENFREIGABE_LOESCHEN]&nbsp;<i>Kundenfreigabe nach Auftragsabschluss l&ouml;schen.</i></td></tr>

          </table>
	  </fieldset>



  	  <fieldset><legend>Paketdienstleister Einstellungen</legend>
          <table border="0" width="100%">
 					<tr><td width="300">Export als Einzeldatei:</td><td>[PAKETMARKE_EINZELDATEI][MSGPAKETMARKE_EINZELDATEI]&nbsp;<i>pro Paket eigene Datei</i></td></tr>
           <tr><td width="300">DPD Kennung:</td><td colspan="2">[DPDKUNDENNR][MSGDPDKUNDENNR]</td></tr>
           <tr><td width="300">DPD Pfad:</td><td colspan="2">[DPDPFAD][MSGDPDPFAD]</td></tr>
           <tr><td width="300">DPD Format:</td><td colspan="2">[DPDFORMAT][MSGDPDFORMAT]<br><i>{NAME}, 
{NAME2},
{NAME3},
{STRASSE},
{HAUSNUMMER},
{PLZ},  
{ORT},  
{LAND}, 
{GEWICHT},
{VERFAHREN},
{PRODUKT},
{SERVICE},
{BETRAG},
{NACHNAHMETEXT},
{LIEFERSCHEINNUMMER},
{KUNDENNUMMER},
{INTERNETNUMMER}</i></td></tr>

	<tr><td width="300">DHL Kennung:</td><td colspan="2">[DHLKUNDENNR][MSGDHLKUNDENNR]</td></tr>
           <tr><td width="300">DHL Pfad:</td><td colspan="2">[DHLPFAD][MSGDHLPFAD]</td></tr>
           <tr><td width="300">DHL Format:</td><td colspan="2">[DHLFORMAT][MSGDHLFORMAT]</td></tr>

	</table>
	  	</fieldset>


  	  <fieldset><legend>Intraship Einstellungen</legend>
          <table border="0" width="100%">
 
	<tr><td width="300">Intraship verwenden:</td><td colspan="2">[INTRASHIP_ENABLED][MSGINTRASHIP_ENABLED]<i>Immer wenn DHL als Versandart ausgew&auml;hlt ist.</i></td></tr>
	<tr><td width="300">Intraship Drucker f&uuml;r Paketmarken:</td><td colspan="2">[INTRASHIP_DRUCKER][MSGINTRASHIP_DRUCKER]</td></tr>
	<tr><td width="300">Intraship im Testmodus betreiben:</td><td colspan="2">[INTRASHIP_TESTMODE][MSGINTRASHIP_TESTMODE]</td></tr>
	<tr><td width="300">Intraship Benutzer:</td><td colspan="2">[INTRASHIP_USER][MSGINTRASHIP_USER]&nbsp;<i>geschaeftskunden_api</i></td></tr>
	<tr><td width="300">Intraship Signature:</td><td colspan="2">[INTRASHIP_SIGNATURE][MSGINTRASHIP_SIGNATURE]&nbsp;<i>Dhl_ep_test1</i></td></tr>
	<tr><td width="300">Intraship EKP:</td><td colspan="2">[INTRASHIP_EKP][MSGINTRASHIP_EKP]&nbsp;<i>5000000000</i></td></tr>
	<tr><td width="300">Intraship API User:</td><td colspan="2">[INTRASHIP_API_USER][MSGINTRASHIP_API_USER]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship API Password:</td><td colspan="2">[INTRASHIP_API_PASSWORD][MSGINTRASHIP_API_PASSWORD]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender Firma:</td><td colspan="2">[INTRASHIP_COMPANY_NAME][MSGINTRASHIP_COMPANY_NAME]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender Strasse:</td><td colspan="2">[INTRASHIP_STREET_NAME][MSGINTRASHIP_STREET_NAME]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender Strasse Nr.:</td><td colspan="2">[INTRASHIP_STREET_NUMBER][MSGINTRASHIP_STREET_NUMBER]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender PLZ:</td><td colspan="2">[INTRASHIP_ZIP][MSGINTRASHIP_ZIP]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender Land:</td><td colspan="2">[INTRASHIP_COUNTRY][MSGINTRASHIP_COUNTRY]&nbsp;<i>germany</i></td></tr>
	<tr><td width="300">Intraship Versender Stadt:</td><td colspan="2">[INTRASHIP_CITY][MSGINTRASHIP_CITY]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender E-Mail:</td><td colspan="2">[INTRASHIP_EMAIL][MSGINTRASHIP_EMAIL]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender Telefon:</td><td colspan="2">[INTRASHIP_PHONE][MSGINTRASHIP_PHONE]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender Web:</td><td colspan="2">[INTRASHIP_INTERNET][MSGINTRASHIP_INTERNET]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship Versender Ansprechpartner:</td><td colspan="2">[INTRASHIP_CONTACT_PERSON][MSGINTRASHIP_CONTACT_PERSON]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship f&uuml;r Nachnahme Bank Inhaber:</td><td colspan="2">[INTRASHIP_ACCOUNT_OWNER][MSGINTRASHIP_ACCOUNT_OWNER]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship f&uuml;r Nachnahme Kontonummer:</td><td colspan="2">[INTRASHIP_ACCOUNT_NUMBER][MSGINTRASHIP_ACCOUNT_NUMBER]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship f&uuml;r Nachnahme BLZ:</td><td colspan="2">[INTRASHIP_BANK_CODE][MSGINTRASHIP_BANK_CODE]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship f&uuml;r Nachnahme Bank Name:</td><td colspan="2">[INTRASHIP_BANK_NAME][MSGINTRASHIP_BANK_NAME]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship f&uuml;r Nachnahme IBAN:</td><td colspan="2">[INTRASHIP_IBAN][MSGINTRASHIP_IBAN]&nbsp;<i></i></td></tr>
	<tr><td width="300">Intraship f&uuml;r Nachnahme BIC:</td><td colspan="2">[INTRASHIP_BIC][MSGINTRASHIP_BIC]&nbsp;<i></i></td></tr>

	<tr><td width="300">Intraship Standard Gewicht:</td><td colspan="2">[INTRASHIP_WEIGHTINKG][MSGINTRASHIP_WEIGHTINKG]&nbsp;<i>in KG</i></td></tr>
	<tr><td width="300">Intraship Standard L&auml;nge:</td><td colspan="2">[INTRASHIP_LENGTHINCM][MSGINTRASHIP_LENGTHINCM]&nbsp;<i>in cm</i></td></tr>
	<tr><td width="300">Intraship Standard Breite:</td><td colspan="2">[INTRASHIP_WIDTHINCM][MSGINTRASHIP_WIDTHINCM]&nbsp;<i>in cm</i></td></tr>
	<tr><td width="300">Intraship Standard H&ouml;he:</td><td colspan="2">[INTRASHIP_HEIGHTINCM][MSGINTRASHIP_HEIGHTINCM]&nbsp;<i>in cm</i></td></tr>
	<tr><td width="300">Intraship Standard Paket:</td><td colspan="2">[INTRASHIP_PACKAGETYPE][MSGINTRASHIP_PACKAGETYPE]&nbsp;<i>z.B. PL</i></td></tr>
	</table>
	  	</fieldset>


   
  	  <fieldset><legend>E-Mail Versand Einstellungen (falls abweichend von Daten aus Firmeneinstellungen)</legend>
          <table border="0" width="100%">
           <tr><td width="300">E-Mail:</td><td colspan="2">[ABSENDEADRESSE][MSGABSENDEADRESSE]</td></tr>
           <tr><td width="300">Name:</td><td colspan="2">[ABSENDENAME][MSGABSENDENAME]</td></tr>
           <tr><td width="300">Signatur:</td><td colspan="2">[ABSENDESIGNATUR][MSGABSENDESIGNATUR]</td></tr>
					</table>
	  	</fieldset>

   
</td></tr>
<!-- speichern -->
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-3';" /> <input type="button" value="Abbrechen" /></td>
    </tr>
  </table>

</div>

<div id="tabs-4">
<table cellspacing="5" width="100%">
<tr><td>
[MESSAGE]
 <fieldset><legend>Nummernkreis</legend>
  <table border="0" width="100%">
 	<tr><td width="300">Eigene Nummernkreise:</td><td>[EIGENERNUMMERNKREIS][MSGEIGENERNUMMERNKREIS]</td></tr>
			  [STARTNUMMER]<tr><td width="200">N&auml;chste Angebotsnummer</td><td>[NEXT_ANGEBOT][MSGNEXT_ANGEBOT]&nbsp;</td></tr>
  <tr><td>N&auml;chste Auftragsnummer</td><td>[NEXT_AUFTRAG][MSGNEXT_AUFTRAG]&nbsp;</td></tr>
  <tr><td>N&auml;chste Lieferscheinnummer</td><td>[NEXT_LIEFERSCHEIN][MSGNEXT_LIEFERSCHEIN]&nbsp;</td></tr>
  <tr><td>N&auml;chste Rechnungsnummer</td><td>[NEXT_RECHNUNG][MSGNEXT_RECHNUNG]&nbsp;</td></tr>
  <tr><td>N&auml;chste Gutschriftnummer</td><td>[NEXT_GUTSCHRIFT][MSGNEXT_GUTSCHRIFT]&nbsp;</td></tr>
  <tr><td>N&auml;chste Bestellungsnummer</td><td>[NEXT_BESTELLUNG][MSGNEXT_BESTELLUNG]&nbsp;</td></tr>
  <tr><td>N&auml;chste Arbeitsnachweisnummer</td><td>[NEXT_ARBEITSNACHWEIS][MSGNEXT_ARBEITSNACHWEIS]&nbsp;</td></tr>
  <tr><td>N&auml;chste Reisekostennummer</td><td>[NEXT_REISEKOSTEN][MSGNEXT_REISEKOSTEN]&nbsp;</td></tr>
  <tr><td>N&auml;chste Produktionnummer</td><td>[NEXT_PRODUKTION][MSGNEXT_PRODUKTION]&nbsp;</td></tr>
  <tr><td>N&auml;chste Anfragenummer</td><td>[NEXT_ANFRAGE][MSGNEXT_ANFRAGE]&nbsp;</td></tr>
  <tr><td>N&auml;chste Kundennummer</td><td>[NEXT_KUNDENNUMMER][MSGNEXT_KUNDENNUMMER]&nbsp;</td></tr>
  <tr><td>N&auml;chste Lieferantenummer</td><td>[NEXT_LIEFERANTENNUMMER][MSGNEXT_LIEFERANTENNUMMER]&nbsp;</td></tr>
  <tr><td>N&auml;chste Mitarbeiternummer</td><td>[NEXT_MITARBEITERNUMMER][MSGNEXT_MITARBEITERNUMMER]&nbsp;</td></tr>	
  <tr><td>N&auml;chste Artikelnummer</td><td>[NEXT_ARTIKELNUMMER][MSGNEXT_ARTIKELNUMMER]&nbsp;</td></tr>	
[ENDENUMMER]
           </table>
	  </fieldset>
   
</td></tr>
<!-- speichern -->
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-4';"/> <input type="button" value="Abbrechen" /></td>
    </tr>
  </table>
</div>

<div id="tabs-5">
<table cellspacing="5" width="100%">
<tr><td>
 <fieldset><legend>Steuer / Standardw&auml;hrung</legend>
  <table border="0" width="100%">
 	<tr><td width="300">Eigene Steuers&auml;tze verwenden:</td><td>[EIGENESTEUER][MSGEIGENESTEUER]</td></tr>
  <tr><td>Steuersatz Normal</td><td>[STEUERSATZ_NORMAL][MSGSTEUERSATZ_NORMAL]&nbsp;</td></tr>
  <tr><td>Steuersatz Erm&auml;ssigt</td><td>[STEUERSATZ_ERMAESSIGT][MSGSTEUERSATZ_ERMAESSIGT]&nbsp;</td></tr>
  <tr><td>W&auml;hrung</td><td>[WAEHRUNG][MSGWAEHRUNG]&nbsp;</td></tr>
   </table>
	  </fieldset>
   
</td></tr>
<!-- speichern -->
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-5';"/> <input type="button" value="Abbrechen" /></td>
    </tr>
  </table>
</div>




</div>

  </form>

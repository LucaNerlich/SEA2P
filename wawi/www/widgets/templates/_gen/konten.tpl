<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tab1">Gesch&auml;ftskonten</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div>
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>Einstellungen</legend>
    <table width="100%">
          <tr><td width="150">Bezeichnung:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
          <tr><td>Typ:</td><td>[TYPE][MSGTYPE]</td></tr>
          <tr><td>Aktiv:</td><td>[AKTIV][MSGAKTIV]<i>Nicht mehr verwendete Konten k&ouml;nnen deaktiviert werden.</i></td></tr>
          <tr><td>Keine E-Mail:</td><td>[KEINEEMAIL][MSGKEINEEMAIL]<i>Normalerweise wird beim Zahlungseingang eine Mail an den Kunden gesendet. Soll dies unterdr&uuml;ckt werden muss diese Option gesetzt werden.</i></td></tr>
          <tr><td>&Auml;nderungen  erlauben:</td><td>[SCHREIBBAR][MSGSCHREIBBAR]<i>&nbsp;Es d&uuml;rfen nachtr&auml;glich Kontobuchungen ver&auml;ndert werden</i></td></tr>
</table></fieldset>
<fieldset><legend>Bankverbindung (bei Typ Bank)</legend>
    <table width="100%">
	  <tr><td width="150">Inhaber:</td><td>[INHABER][MSGINHABER]</td><td></tr>
	  <tr><td>BIC:</td><td>[SWIFT][MSGSWIFT]</td><td></tr>
	  <tr><td>IBAN:</td><td>[IBAN][MSGIBAN]</td><td></tr>
	  <tr><td>BLZ:</td><td>[BLZ][MSGBLZ]</td><td></tr>
	  <tr><td>Konto:</td><td>[KONTO][MSGKONTO]</td><td></tr>
	  <tr><td>Gl&auml;ubiger ID:</td><td>[GLAEUBIGER][MSGGLAEUBIGER]</td><td></tr>

          <tr><td>Lastschrift:</td><td>[LASTSCHRIFT][MSGLASTSCHRIFT]</td></tr>
          <tr><td>HBCI:</td><td>[HBCI][MSGHBCI]</td></tr>
	  <tr><td>HBCI-Kennung:</td><td>[HBCIKENNUNG][MSGHBCIKENNUNG]</td><td></tr>
</table></fieldset>
<fieldset><legend>DATEV</legend>
    <table width="100%">
	  <tr><td width="150">Konto:</td><td>[DATEVKONTO][MSGDATEVKONTO]</td><td></tr>
</table></fieldset>
<fieldset><legend>CSV-Import</legend>
    <table width="100%">
	  <!--<tr><td width="150">Erste Zeile<br>Datei-Import:</td><td>[ERSTEZEILE][MSGERSTEZEILE]</td></tr>-->
	  <tr><td width="150">Erste Datenzeile:</td><td>[IMPORTERSTEZEILENUMMER][MSGIMPORTERSTEZEILENUMMER]&nbsp;<!--Format:&nbsp;[IMPORTFELDWAEHRUNGFORMAT][MSGIMPORTFELDWAEHRUNGFORMAT]-->&nbsp;<i>Zeilennummer in der echte Daten stehen (Erste Zeile: 1)</i></td></tr>
	  <tr><td width="150">Kodierung:</td><td>[CODIERUNG][MSGCODIERUNG]</td></tr>
	  <tr><td width="150">Trennzeichen:</td><td>[IMPORTTRENNZEICHEN][MSGIMPORTTRENNZEICHEN]</td></tr>
	  <tr><td width="150">Maskierung:</td><td>[IMPORTDATENMASKIERUNG][MSGIMPORTDATENMASKIERUNG]</td></tr>
	  <tr><td width="150">Letzte Zeilen ignorieren:</td><td>[IMPORTLETZTENZEILENIGNORIEREN][MSGIMPORTLETZTENZEILENIGNORIEREN]</td></tr>
</table>
<br><br>
<table>
	  <tr><td width="150">Spalte in CSV</td><td>Spalten 1 bis n (Spaltennummer in CSV).</tr>
	  <tr><td width="150">Datum:</td><td>[IMPORTFELDDATUM][MSGIMPORTFELDDATUM]&nbsp;Format:&nbsp;[IMPORTFELDDATUMFORMAT][MSGIMPORTFELDDATUMFORMAT]&nbsp;Ausgabe:&nbsp;[IMPORTFELDDATUMFORMATAUSGABE][MSGIMPORTFELDDATUMFORMATAUSGABE]&nbsp;<i>Ziel: %Y-%m-%d / %3-%2-%1</i></td></tr>
	  <tr><td width="150">Betrag:</td><td>[IMPORTFELDBETRAG][MSGIMPORTFELDBETRAG]</td></tr>
	  <tr><td width="150">Extra Haben u. Soll:</td><td>[IMPORTEXTRAHABENSOLL][MSGIMPORTEXTRAHABENSOLL]</td></tr>
<tr><td></td><td>
<table>
	  <tr><td width="150">Haben:</td><td>[IMPORTFELDHABEN][MSGIMPORTFELDHABEN]</td></tr>
	  <tr><td width="150">Soll:</td><td>[IMPORTFELDSOLL][MSGIMPORTFELDSOLL]</td></tr>
</table>
</tr>
	  <tr><td width="150">Buchungstext:</td><td>[IMPORTFELDBUCHUNGSTEXT][MSGIMPORTFELDBUCHUNGSTEXT]&nbsp;<i> Mit + mehre Spalten zusammenf&uuml;gen (aus dem Inhalt wird eine Pr&uuml;fsumme berechnet, daher so eindeutig wie m&ouml;glich machen.)</i></td></tr>
	  <tr><td width="150">W&auml;hrung:</td><td>[IMPORTFELDWAEHRUNG][MSGIMPORTFELDWAEHRUNG]&nbsp;<i>Ziel: EUR, USD</i></td></tr>
	  <tr><td width="150">Haben/Soll Kennung:</td><td>[IMPORTFELDHABENSOLLKENNUNG][MSGIMPORTFELDHABENSOLLKENNUNG]&nbsp;<i>Extra Spalte in der steht was der Betrag ist.</i></td></tr>
	  <tr><td width="150"></td><td>
            <table><tr><td width="150">Markierung Eingang:</td><td>[IMPORTFELDKENNUNGHABEN][MSGIMPORTFELDKENNUNGHABEN]&nbsp;<i>z.B. H oder +</i></td></tr>
            <tr><td>Markierung Ausgang:&nbsp;</td><td>[IMPORTFELDKENNUNGSOLL][MSGIMPORTFELDKENNUNGSOLL]&nbsp;<i>z.B. S oder -D</i></td></tr>
            </table>
          </td></tr>
</table></fieldset>
<fieldset><legend>Live-Import</legend>
<table>
          <tr><td>Live-Import aktiv:</td><td>[LIVEIMPORT_ONLINE][MSGLIVEIMPORT_ONLINE]</td></tr>
	  <tr><td width="150">Zugangsdaten<br>Live-Import:</td><td>[LIVEIMPORT][MSGLIVEIMPORT]</td></tr>
</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->



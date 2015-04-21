<!-- gehort zu tabview -->
<div id="tabs">
<ul>
        <li><a href="#tabs-1">Online-Shops</a></li>
    </ul>

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>Einstellungen</legend>
    <table width="100%">
	  <tr><td>Aktiv:</td><td>[AKTIV][MSGAKTIV]</td><td></tr>
          <tr><td width="300">Bezeichnung:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
          <tr><td>Typ:</td><td>[TYP][MSGTYP]</td></tr>
	  <tr><td>URL:</td><td>[URL][MSGURL]</td><td></tr>
	  <tr><td>Projekt:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
	  <tr><td>Demo Modus:</td><td>[DEMOMODUS][MSGDEMOMODUS]&nbsp;<i>Es wird der letzte Auftrag aus dem Shop geladen - der Status aber nicht umgestellt.</i></td><td></tr>

	 </table></fieldset>
<fieldset><legend>Optionen</legend>
    <table width="100%">
	  <tr><td>Multiprojekt Shop:</td><td>[MULTIPROJEKT][MSGMULTIPROJEKT]&nbsp;<i>In diesem Shop werden Artikel aus verschiedenen Projekten angeboten</i></td><td></tr>
	  <tr><td>Artikel &Uuml;bertragung erlauben:</td><td>[ARTIKELEXPORT][MSGARTIKELEXPORT]&nbsp;<i>(Von WaWision zu Shopware)</td><td></tr>
	<tr><td>Lagerzahlen &Uuml;bertragung erlauben:</td><td>[LAGEREXPORT][MSGLAGEREXPORT]&nbsp;<i>(Von WaWision zu Shopware)</i></td><td></tr>
	  <tr><td>Automatischer Abgleich:</td><td>[ARTIKELIMPORT][MSGARTIKELIMPORT]&nbsp;immer&nbsp;<i>(Automatisch Preis aus Shop &uuml;bernehmen + fehlende Artikel neu anlegen)</i></td><td></tr>
	  <tr><td></td><td>[ARTIKELIMPORTEINZELN][MSGARTIKELIMPORTEINZELN]&nbsp;einzeln&nbsp;<i>(Nur bei Artikeln mit Option: Artikel->Online-Shop Optionen->Online Shop Abgleich)</i></td><td></tr>

</table></fieldset>
<fieldset><legend>Artikel f&uuml;r Porto und Nachnahmegeb&uuml;hr</legend>
    <table width="100%">
	  <tr><td width="300">Porto:</td><td>[ARTIKELPORTOAUTOSTART][ARTIKELPORTO][MSGARTIKELPORTO][ARTIKELPORTOAUTOEND]&nbsp;<i>Artikel-Nr. auf die das Porto gebucht wird.</i></td></tr>
	  <tr><td>Nachnahmegeb&uuml;hr:</td><td>[ARTIKELNACHNAHMEAUTOSTART][ARTIKELNACHNAHME][MSGARTIKELNACHNAHME][ARTIKELNACHNAHMEAUTOEND]&nbsp;<i>Artikel-Nr. f&uuml;r die Nachnahme Geb&uuml;hr.</i></td></tr>
	 </table></fieldset>
<fieldset><legend>Sicherheit</legend>
    <table width="100%">
	  <tr><td width="300">Passwort:</td><td>[PASSWORT][MSGPASSWORT]&nbsp;<i>32 Zeichen langes Sicherheitspasswort</i></td><td></tr>
	  <tr><td>Token:</td><td>[TOKEN][MSGTOKEN]&nbsp;<i>6 Zeichen langes Sicherheitstoken</i></td><td></tr>
	  <tr><td>Challenge:</td><td>[CHALLENGE][MSGCHALLENGE]</td><td></tr>
 	<!--	  <tr><td>Internes CMS:</td><td>[CMS][MSGCMS]</td><td></tr>-->
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



<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Service Formular</a></li>
        <li><a href="#tabs-2">Antwort an Kunden</a></li>
        <li><a href="#tabs-3">Aktionen</a></li>
    </ul>

<div id="tabs-1">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
<!--
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign="">Adresse<br></td>
      </tr>

-->
      <tr valign="top" colspan="1">
        <td >
<table border="0" width="100%"><tr><td>
[MESSAGE]
<form action="" method="post" name="eprooform">
[BUTTONS]
[FORMHANDLEREVENT]

<fieldset><legend>Daten</legend>
<table width="" border="0">
	<tr><td width="150">Kunde:</td><td>[ADRESSE][MSGADRESSE]</td><td></td><td>Eingang:</td><td>[EINGANGART][MSGEINGANGART]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[NEUMAIL]</td></tr>
	<tr><td width="150">Ansprechpartner:</td><td colspan="4">[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td></tr>
  <tr valign="top"><td>Betreff:</td><td>[BETREFF][MSGBETREFF]</td><td></td><td>Prio:</td><td>[PRIO][MSGPRIO]&nbsp;bis:&nbsp;[ERLEDIGENBIS][MSGERLEDIGENBIS]</td></tr>
  <tr><td>Beschreibung:</td><td colspan="4">[BESCHREIBUNG_HTML][MSGBESCHREIBUNG_HTML]</td></tr>
	<tr><td width="150">Zuweisen an:</td><td>[ZUWEISEN][MSGZUWEISEN]</td><td></td><td>Status:</td>
      <td>[STATUS][MSGSTATUS]</td></tr>

  <tr valign="top"><td>Artikel:</td><td>[ARTIKEL][MSGARTIKEL]</td><td></td><td>Seriennummer:</td><td>[SERIENNUMMER][MSGSERIENNUMMER]</td></tr>
     </table>
</fieldset>
</td>
</tr></table>
        </td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="1" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" /> </td>
    </tr>
  
    </tbody>
  </table>

</div>

<div id="tabs-2">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
<!--
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign="">Adresse<br></td>
      </tr>

-->
      <tr valign="top" colspan="1">
        <td >
[MESSAGE]
[BUTTONS]
[FORMHANDLEREVENT]

<fieldset><legend>Aktionen</legend>
<table border="0" width="100%"><tr><td>
  <tr><td width="150">Antwort an Kunden:</td><td colspan="4">[ANTWORTANKUNDEN][MSGANTWORTANKUNDEN]</td></tr>
	<tr><td width="150">bei Abschluss:</td><td colspan="4">[ANTWORTPERMAIL][MSGANTWORTPERMAIL]&nbsp;an Kunden senden</td></tr>
</table>
</fieldset>
        </td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="1" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" /> </td>
    </tr>
  
    </tbody>
  </table>

</div>



<div id="tabs-3">

<fieldset><legend>Aktionen</legend>
<div class="tabsbutton" align="center">
<!--<a href="#" onclick="if(!confirm('sas wirklich anlegen?')) return false; else window.location.href='index.php?module=adresse&action=createdokument&id=1&cmd=1';">
<table width="150" height="40"><tr><td>Ersatzteillieferung anlegen</td></tr></table></a>
<a href="#" onclick="if(!confirm('sas wirklich anlegen?')) return false; else window.location.href='index.php?module=adresse&action=createdokument&id=1&cmd=1';">
<table width="150" height="40"><tr><td>Rechnung anlegen</td></tr></table></a>
<a href="#" onclick="if(!confirm('sas wirklich anlegen?')) return false; else window.location.href='index.php?module=adresse&action=createdokument&id=1&cmd=1';">
<table width="150" height="40"><tr><td>Weitere Serviceanfrage anlegen</td></tr></table></a>-->
<a href="#" onclick="if(!confirm('Soll die Anfrage abgeschossen werden?')) return false; else window.location.href='index.php?module=service&action=abschluss&id=[ID]';">
<table width="400" height="40"><tr><td>Ticket schlie&szlig;en</td></tr></table></a>
<a href="#" onclick="if(!confirm('Soll die Anfrage abgeschlossen werden und ein Eintrag in der Zeiterfassung angelegt werden?')) return false; else window.location.href='index.php?module=service&action=abschlusszeit&id=[ID]';">
<table width="400" height="40"><tr><td>Ticket schlie&szlig;en und Zeiterfassung anlegen</td></tr></table></a>




</div>
</fieldset>
</div>




</div>


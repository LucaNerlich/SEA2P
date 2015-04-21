<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1">Berichte</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs1">
<table width="100%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br><b style="font-size: 14pt">Berichte:</b>
<br>
<br>
Einfache Ausgabe von Daten als Tabelle.<br>
<br>
</td>
</tr>
</table>
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>Einstellung</legend>
    <table width="100%">
          <tr><td width="150">Name:</td><td>[NAME][MSGNAME]</td></tr>
          <tr><td width="150">Beschreibung:</td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td><td></tr>
          <tr><td width="150">Struktur:</td><td>[STRUKTUR][MSGSTRUKTUR]</td><td></tr>
          <tr><td width="150">Spaltennamen:</td><td>[SPALTENNAMEN][MSGSPALTENNAMEN]<br><i>Mit Semikolon getrennt Spaltennamen angeben.</i></td></tr>
          <tr><td width="150">Spaltenbreite:</td><td>[SPALTENBREITE][MSGSPALTENBREITE]<br><i>Mit Semikolon getrennt in Millimeter Spaltenbreite angeben. Gesamtbreite: 190 mm)</i></td></tr>
          <tr><td width="150">Spaltenausrichtung:</td><td>[SPALTENAUSRICHTUNG][MSGSPALTENAUSRICHTUNG]<br><i>Mit Semikolon getrennt Ausrichtung je Spalte (R,L,C) angeben.</i></td></tr>
          <tr><td width="150">Interne Bemerkung:</td><td>[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]</td><td></tr>

</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" name="submit"/>
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>



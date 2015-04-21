<form action="" method="post">
<table border="0">
  <tr><td valign="top"><input type="radio" name="type" id="typeProjekt" value="projekt" /> Projekt</td>
  <td valign="top">
    [PROJEKTAUTOSTART]<input name="projekt" type="text" onfocus="document.getElementById('typeProjekt').checked=true" size="20">[PROJEKTAUTOEND]
  </td></tr>
  <tr><td valign="top"><input type="radio" name="type" id="typeAdresse" value="adresse" /> Person:</td>
  <td valign="top">
    [PERSONAUTOSTART]<input name="adresse" type="text" onfocus="document.getElementById('typeAdresse').checked=true" size="20">[PERSONAUTOEND]
  </td></tr>
  <tr><td valign="top"><input type="radio" name="type" id="typeAuftrag" value="auftrag" /> Auftrag:</td>
  <td valign="top">
    [AUFTRAGAUTOSTART]<input name="auftrag" type="text" onfocus="document.getElementById('typeAuftrag').checked=true" size="20">[AUFTRAGAUTOEND]
  </td></tr>
</table>
<div align="left" style="float:left;"><input name="s1" type="submit" value="Übernehmen" /></div>
<div align="right" style="float:right;"><input name="b" type="button" value="Schliessen" onclick="window.opener.location.reload();window.close();" /></div>
</form>
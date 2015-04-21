<script type="text/javascript">
  function goto(pid)
  {
    if(pid!="")
    {
      window.location="index.php?module=massenartikel&action=edit&pid="+pid;
    }
  }
</script>
<form action="" method="POST" name="massenartikelform">
<table height="80" width="100%"><tr><td>
<fieldset><legend>&nbsp;Filter</legend>
<center>
<table width="100%" cellspacing="5">
<tr>
  <td>Adresse:&nbsp;[ADRESSEAUTOSTART]<input type="text" name="adresse" id="adresse" size="30" value="[ADRESSE]">[ADRESSEAUTOENDE]&nbsp;&nbsp;</td>
  <td>Projekt:&nbsp;[PROJEKTAUTOSTART]<input type="text" name="projekt" id="projekt" size="30" value="[PROJEKT]">[PROJEKTAUTOENDE]&nbsp;&nbsp;</td>
	<td><input type="submit" name="search" value="Suchen"></td>
</tr></table>
</center>
</fieldset>
</td></tr></table>
<br>

	<input type="hidden" name="tablemode" value="[TABLEMODE]">
	<input type="hidden" name="adresseid" value="[ADRESSEID]">
	<input type="hidden" name="projektid" value="[PROJEKTID]">


  <table width="100%" border="0" cellspacing="0" cellpading="0">
    <tr align="center" >
      <td><b>Artikel</b></td>
      <td><b>Nummer</b></td>
      <td><b>Lieferant/Kunde</b></td>
      <td><b>Liefernummer</b></td>
      <td><b>Lieferbezeichnung</b></td>
			<td><b>Menge</b></td>
      <td><b>EK</b></td>
      <td><b>VK</b></td>
      <td><b>Marge</b></td>
    </tr>
  [TABELLE]
  </table>
	<br>
	<center><input type="submit" name="submit" value="Speichern"></center>

</form>


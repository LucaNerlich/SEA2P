<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Kassenbuch</a></li>
        <li><a href="#tabs-2">Export</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<table width="100%>">
<tr><td>Datum heute:</td><td>Kassenstand Vortag:</td><td>Kassensaldo heute:</td><td>Kassenstand aktuell:</td></tr>
<tr>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[DATUM]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[VORTAG]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[HEUTE]</td>
  <td style="background-color:lightgrey;color:white;padding:10px;font-size:2em;" width="25%">[AKTUELL]</td>

</tr>
</table>
[MESSAGE]

<br>
<form action="" method="post">
<table width="100%"><tr><td align="right"><input type="submit" value="Tagesabschluss jetzt durchf&uuml;hren" name="abschluss" onclick="this.form.action += '#tabs-1';" ></td></tr></table>
</form>
[TAB1]
</div>

<!-- erstes tab -->
<div id="tabs-2">
[MESSAGE]
<br>
<fieldset><legend>Datei Export</legend>
<form action="" method="post">
<table>
<tr>
	<td>Von:</td><td><input type="text" size="10" id="von" name="von"></td>
	<td>Bis:</td><td><input type="text" size="10" id="bis" name="bis"></td>
	<td><input type="submit" value="Download" name="download" onclick="this.form.action += '#tabs-2';" ></td>
	<!--<td><input type="submit" value="Nur Eintr&auml;ge seit [LASTDOWNLOAD] Download" name="lastdownload" onclick="this.form.action += '#tabs-2';" ></td>-->
	</tr>
</table>
</form>
</fieldset>
<br>



</div>

<!-- tab view schließen -->
</div>


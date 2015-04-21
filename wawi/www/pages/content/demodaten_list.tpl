<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Generator</a></li>
        <li><a href="#tabs-2">Live Daten</a></li>
        <li><a href="#tabs-3">Online-Shop</a></li>
        <li><a href="#tabs-4">Reset</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGETAB1]
<form action="#tabs-1" method="post">

<!--<table>
<tr><td>Branche:</td><td><input type="text" name="branche"></td><td><i>Es werden passende Testartikel angelegt</i></td></tr>
<tr><td>Adressen:</td><td><input type="checkbox" name="adressen" value="1" checked></td><td><i>Kunden, Lieferanten, Mitarbeiter, Benutzer</i></td></tr>
<tr><td>Termine / Aufgaben:</td><td><input type="checkbox" name="termine" value="1" checked></td><td><i>Passende Termine auf Aufgaben werden anleget</i></td></tr>
<tr><td>Lager:</td><td><input type="checkbox" name="lager" value="1" checked></td><td><i>Es werden Lager und passende Artikelbewegungen anleget</i></td></tr>
<tr><td>Buchhaltung:</td><td><input type="checkbox" name="adressen" value="1" checked></td><td><i>Geschäftskonten, Datevbuchungen</i></td></tr>
<tr><td>Projektmanagement:</td><td><input type="checkbox" name="projekt" value="1" checked></td><td><i>Demoprojekte werden angelegt.</i></td></tr>
<tr><td></td><td><input type="submit" name="generator" value="Jetzt anlegen"></td><td></td></tr>
</table>
-->
Branche: <input type="text" name="branche">&nbsp;&nbsp;
<input type="submit" name="generator" value="Jetzt anlegen">
</form>
</div>
<div id="tabs-2">
<h2>Kontoauszuege</h2>
<ul>
	<li><a href="">Bank 1 Download csv-Datei</a></li>
	<li><a href="">Bank 2 Download csv-Datei</a></li>
</ul>


</div>

<div id="tabs-3">
[MESSAGETAB3]
<form action="#tabs-4" method="post">
<input type="submit" name="onlineshop" value="Online-Shop Auftr&auml;ge erzeugen">
</form>
</div>

<div id="tabs-4">
[MESSAGETAB4]
<form action="#tabs-4" method="post">
<input type="submit" name="reset" value="Jetzt Reset">
</form>
<i>(Es wird alles bis auf eine Adresse, der User Admin (Passwort admin) zur&uuml;ckgesetzt.</i>
</div>

<!-- tab view schließen -->
</div>


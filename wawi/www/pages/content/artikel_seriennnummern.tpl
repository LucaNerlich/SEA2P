<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-2">Kunden</a></li>
        <li><a href="#tabs-1">Im Lager</a></li>
        <li><a href="#tabs-3">Generator</a></li>
    </ul>
<!-- ende gehort zu tabview -->
<div id="tabs-2">
[TAB2]
</div>


<!-- erstes tab -->
<div id="tabs-1">
[SERIENNUMMERNFORMULAR]
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>
<!-- ende gehort zu tabview -->
<div id="tabs-3">
[TAB3]
[STARTDISABLE]
[MESSAGE]
<form action="" method="post">
<center>
<h2>Seriennummern erzeugen</h2><br><br>
<table>
<tr><td width="150">Anzahl:</td><td width="300"><input type="text" name="menge" size="30"></td></tr>
<tr><td width="150">Startnummer:</td><td><input type="text" name="startnummer" size="30">[LETZTESERIENNUMMER]</td></tr>
</table>
<br><br>
<table>
<tr><td width="150">Direkt einlagern:</td><td width="10"><input type="checkbox" name="lager" value="1"></td><td width="275"><input type="text" size="26" name="lager_platz" id="lager_platz"></td></tr>
<tr><td width="150">Etiketten drucken:</td><td><input type="checkbox" name="drucken" value="1"></td><td><select name="etiketten">[ETIKETTEN]</select></td></tr>
<tr><td width="150">Drucker:</td><td></td><td><select name="etikettendrucker">[ETIKETTENDRUCKER]</select></td></tr>
<tr><td></td><td></td><td><br><br><input type="submit" value="Seriennummern jetzt erstellen" name="erstellen"></td></tr>
</table>
</center>
</form>
[ENDEDISABLE]
</div>

<!-- tab view schließen -->
</div>


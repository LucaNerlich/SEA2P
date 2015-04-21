<form action="" name="surveyform" method="POST">

<b>Bevorzugen Sie wawision als herunterladbare Online-Version oder als vorkonfiguriertes Server-Paket?</b><br>
<input type="radio" name="survey[version]" value="online" checked>Online-Version&nbsp;&nbsp;<input type="radio" name="survey[version]" value="server">Server-Version
<br><br>

<b>Bevorzugen Sie ein Preismodel auf einmaliger, monatlicher oder auf j&auml;hrlicher Basis und wie viel w&auml;ren Sie bereit daf&uuml;r zu zahlen?</b><br>
<table>
	<tr><td><input type="radio"name="survey[preismodel]" value="einmalig" checked> Einmalig</td><td><input type="text" name="survey[preis_einmalig]" size="4"></td>
	<tr><td><input type="radio"name="survey[preismodel]" value="monatlich"> Monatlich</td><td><input type="text" name="survey[preis_monatlich]" size="4"></td>
	<tr><td><input type="radio"name="survey[preismodel]" value="jaehrlich"> J&auml;hrlich</td><td><input type="text" name="survey[preis_jaehrlich]" size="4"></td>
</table>
<br>

<b>Haben Sie ein Feedback f&uuml;r uns?</b><br>
<textarea name="survey[feedback]" cols="60" rows="5"></textarea>
<br><br>

<b>Haben Sie wichtige Funktionen vermisst?</b><br>
<textarea name="survey[funktionen]" cols="60" rows="5"></textarea>
<br><br>

<b>Haben Sie Verbesserungsvorschl&auml;ge f&uuml;r uns?</b><br>
<textarea name="survey[vorschlaege]" cols="60" rows="5"></textarea>
<br><br>

<b>Sonstige Anmerkungen:</b><br>
<textarea name="survey[sonstiges]" cols="60" rows="5"></textarea>
<br><br>
<center><input type="submit" name="cancel" value="Abbrechen">&nbsp;<input type="submit" name="submit" value="Senden"></center>
</form>

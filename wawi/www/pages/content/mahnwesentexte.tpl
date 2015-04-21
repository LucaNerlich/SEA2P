<form action="" method="post">
<table width="100%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br><b style="font-size: 14pt">Mahnungstexte:</b>
<br>
<br>
</td>
</tr>
</table>
<br>
<br>
<h4>Variablen</h4>
<pre>

{DATUM} = Ende der neu gegebene Frist (Datum)
{TAGE} = Anzahl Tage in aktueller Mahnfrist (Tage)
{SOLL} = Soll Betrag (EUR)
{IST} =  Ist Betrag (EUR)
{OFFEN} =  Betrag Soll - Ist (EUR)
{MAHNGEBUEHR} =  Mahngebuehr (EUR)
{RECHNUNG} = Rechnungsnummer 
{DATUMRECHNUNG} = Datum der Rechnung 
{MAHNDATUM} = Datum der letzte Zahlungserinnerung bzw. Mahnung 
{DATUMRECHNUNGZAHLUNGSZIEL} = Datum der urspr&uuml;nglichen geforderten Bezahlung (Rechnungsdatum + Tage Zahlungsziel) der Rechnung (Datum)
{DATUMZAHLUNGSERINNERUNG} = Datum der Zahlungserinnerung
{DATUMMAHNUNG1} = Datum der Mahnung 1
{DATUMMAHNUNG2} = Datum der Mahnung 2
{DATUMMAHNUNG3} = Datum der Mahnung 3
</pre>
<hr width="100%">
<h4>Zahlungserinnerung</h4>
<textarea rows=10 cols=100 name="textz">[TEXTZ]</textarea>
<br><br><center><input type="submit" value="speichern" name="mahnungstextespeichern"></center><br>
<hr width="100%">
<h4>Mahnung 1</h4>
<textarea rows=10 cols=100 name="textm1">[TEXTM1]</textarea>
<br><br><center><input type="submit" value="speichern" name="mahnungstextespeichern"></center><br>
<hr width="100%">
<h4>Mahnung 2</h4>
<textarea rows=10 cols=100 name="textm2">[TEXTM2]</textarea>
<br><br><center><input type="submit" value="speichern" name="mahnungstextespeichern"></center><br>
<hr width="100%">
<h4>Mahnung 3</h4>
<textarea rows=10 cols=100 name="textm3">[TEXTM3]</textarea>
<br><br><center><input type="submit" value="speichern" name="mahnungstextespeichern"></center><br>
<hr width="100%">
<h4>Inkasso-Mahnung</h4>
<textarea rows=10 cols=100 name="texti">[TEXTI]</textarea>
<br><br><center><input type="submit" value="speichern" name="mahnungstextespeichern"></center><br>

</form>

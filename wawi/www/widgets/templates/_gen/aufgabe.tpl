<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]



<div id="accordion"> 
<h3><a href="#">Allgemein</a></h3>
<div>

          <table border="0" width="70%">
	    [MESSAGE]
            <tbody>
	      <tr><td>Aufgabe:</td><td>[AUFGABE][MSGAUFGABE]</td></tr>
	      <tr><td>Mitarbeiter:</td><td>[MITARBEITERAUTOSTART][ADRESSE][MSGADRESSE][MITARBEITERAUTOEND]</td></tr>
	      <tr><td>Prio</td><td>
		  [PRIO][MSGPRIO]
	      </td></tr>
	      <tr><td>&Ouml;ffentlich:</td><td>[OEFFENTLICH][MSGOEFFENTLICH]</td></tr>
	      <tr><td>Auf Startseite:</td><td>[STARTSEITE][MSGSTARTSEITE]</td></tr>
	      <tr><td>Auf Pinwand:</td><td>[PINWAND][MSGPINWAND]&nbsp;Farbe:&nbsp;[NOTE_COLOR][MSGNOTE_COLOR]
</td></tr>
	      <tr><td>Beschreibung:<br><i>(Optional Text auf Pinwand)</i></td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td></tr>
	      <tr><td>Projekt:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
</table>
</div>
<h3><a href="#">Dauer / Datum / Intervall</a></h3>
<div>

          <table border="0" width="70%">
	      <tr><td><br><br></td><td></td></tr>
	      <tr><td>Dauer (optional):</td><td>[STUNDEN][MSGSTUNDEN]&nbsp;<i>(in Stunden)</i></td></tr>
	      <tr><td>Abgabe bis (optional):</td><td>[ABGABE_BIS][MSGABGABE_BIS]</td></tr>

        <tr><td>E-Mail Errinnerung:</td><td>[EMAILERINNERUNG][MSGEMAILERINNERUNG]&nbsp;</td></tr>
        <tr><td>E-Mail Anzahl Tage zuvor:</td><td>[EMAILERINNERUNG_TAGE][MSGEMAILERINNERUNG_TAGE]&nbsp;<i>(in Tagen)</i></td></tr>

	      <tr><td>Regelm&auml;&szlig;ig (Intervall):</td><td>
		  [INTERVALL_TAGE][MSGINTERVALL_TAGE]
	      </td></tr>
	    <tr><td>Countdown auf Startseite:</td><td>[VORANKUENDIGUNG][MSGVORANKUENDIGUNG]&nbsp;<i>(in Tagen)</i></td></tr>
 
</table>
</div>
<h3><a href="#">Status / Abschluss</a></h3>
<div>

          <table border="0" width="70%">
		    <tr><td>Status:</td><td>
		[STATUS][MSGSTATUS]
	      </td></tr>
	      <tr valign="top"><td>Notizen:</td><td>[SONSTIGES][MSGSONSTIGES]</td></tr>

	</table>
 		</div>
</div>
          <table border="0" width="100%">
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" />[ABBRECHEN]</td>
    </tr>
  
    </tbody>
  </table>

</form>

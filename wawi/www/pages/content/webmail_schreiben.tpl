<form action="" method="post">
[MESSAGE]
<table width="700">
<tr valign="top"><td width="60%">

<table>
<tr><td>Von:</td><td><select name="from">[EMAILFROM]</select></td></tr>
<tr><td>An:</td><td>[ADRESSESTART]<textarea rows="1" cols="80" name="adresse" id="adresse">[ADRESSE]</textarea>[ADRESSEEND]</td></tr>
<tr><td>CC:</td><td>[CCSTART]<textarea rows="1" cols="80" name="cc" id="cc">[CCADRESSE]</textarea>[CCEND]</td></tr>
<tr><td>BCC:</td><td>[BCCSTART]<textarea rows="1" cols="80" name="bcc" id="bcc">[BCCADRESSE]</textarea>[BCCEND]</td></tr>
<tr><td><br></td><td></td></tr>
<tr><td>Betreff:</td><td><input type="text" size="80" name="betreff" value="[EMAILBETREFF]"></td></tr>
<tr valign="top"><td>E-Mail:</td><td><textarea cols="78" rows="20" name="emailtext">[EMAILTEXT]</textarea><br>(Die Signatur wird automatisch eingef&uuml;gt.)</td></tr>
  <tr><td>Anhang:</td><td><input type="file" name="datei" ></td></tr>

</table>
</td>

</tr>

<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Senden" name="senden">
    <input type="button" value="Abbrechen" onclick="window.location.href='[BACK]'" />
</td>
    </tr>

</table>
</form>

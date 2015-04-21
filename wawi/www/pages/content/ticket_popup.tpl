<!--<script type="text/javascript"> if([ADRESSE]!=0 && [ADRESSE]!=5) opener.window.location.href='index.php?module=adresse&action=edit&id=[ADRESSE]'; </script>-->
<br><center><form action="" method="post" enctype="multipart/form-data">
<table border="0"  class="tableborder" cellpadding="3" cellspacing="0" align="center">
<tr classname="orange1" class="orange1"><td>Betreff: <input type="text" size="83" name="betreff" value="[BETREFF]"></td><td width="300"><b>Vorlagen</b></td></tr>
<tr classname="orange1" class="orange1"><td>Kunde:   <input type="text" size="84" name="verfasser" value="[VERFASSER]"></td><td width="300"></td></tr>
<tr classname="orange1" class="orange1"><td>E-Mail:  <input type="text" size="84" name="email" value="[EMAIL]"></td><td width="300"></td></tr>
<tr valign="top"><td>
<textarea cols="90" rows="15" name="eingabetext">[TEXTVORLAGE]</textarea></td><td rowspan="2"><br>[VORLAGEN]</td></tr>
<tr valign="top"><td><table width="100%"><tr><td><b>Anh&auml;nge:</b> [ANHAENGE]</td><td align="right">
&nbsp;<b>Anhang mitsenden:</b>&nbsp;<input type="file" name="datei"></td></tr></table></td></tr>
<tr valign="top"><td> 
[TEXT]
</td></tr>

<tr height="40" classname="orange2" class="orange2"><td align="right" colspan="2">
<input type="button" value="Abbrechen" onclick="window.location.href='index.php?module=ticket&action=freigabe&id=[ID]&cmd=[CMD]'; self.close();">
<input type="submit" value="Senden" name="senden"></td></tr>
</table>
</form>
</center>
<bR><br>
<!-- nclick="opener.focus();opener.location.reload();window.close();return false;" -->

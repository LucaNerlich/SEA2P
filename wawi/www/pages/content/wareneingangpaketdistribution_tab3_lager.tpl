<form action="" method="post" name="eprooform">
<br> 
<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td>

[MESSAGE]
<br><br>
<table width="60%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br>

<table height="200" border="0" width="450">
<tr valign="top"><td><b>Artikel:</b></td><td><u>[NAME]</u></td></tr> 
<tr valign="top"><td><b>Nummer:</b></td><td>[NUMMER]</td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr> 
[SHOWIMGSTART]<tr valign="top"><td><b>Bild:</b></td><td align="center"><img src="index.php?module=dateien&action=send&id=[DATEI]" width="200"></td></tr> [SHOWIMGEND]
<tr valign="top"><td><br></td><td align="center"></td></tr> 
<tr valign="top"><td><b>Bemerkung:</b></td><td><textarea cols="35" rows="2" name="bemerkung">[BEMERKUNG]</textarea>
</td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr> 

<tr valign="top"><td nowrap><b>Menge:</b></td><td>
<input type="radio" name="anzahlauswahl" checked value="fix">&nbsp;<input type="text" size="5" name="anzahl_fix" value="[MENGE]" readonly>&nbsp;[ANZAHLAENDERN][ETIKETTENDRUCKEN]
<!--(VPE: [VPE]).--><br>
[SHOWANZAHLSTART]<input type="radio" name="anzahlauswahl" value="dyn">&nbsp;<input type="text" size="5" name="anzahl_dyn" value="[ANZAHL]">&nbsp;St&uuml;ck weil anders geliefert
  </td></tr> [SHOWANZAHLENDE]
[ETIKETTENDRUCKENSTART]<tr valign="top"><td><br></td><td align="center"></td></tr> 
<tr valign="top"><td><b>Etiketten:</b></td><td><select name="etiketten">[ETIKETTEN]</select></td></tr>[ETIKETTENDRUCKENENDE]
<tr valign="top"><td><br></td><td align="center"></td></tr> 
[SHOWMHDSTART]<tr valign="top"><td><b style="color:red">MHD:</b></td><td><input type="text" name="mhd" id="mhd">&nbsp;<br><i>(Mindesthaltbarkeitsdatum)</i></td></tr>
<tr valign="top"><td><br></td><td align="center"></td></tr>[SHOWMHDEND]
[SHOWCHRSTART]<tr valign="top"><td><b style="color:red">Charge:</b></td><td><input type="text" name="charge" id="charge">&nbsp;<br><i>(Chargennummer von Hersteller)</i></td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr>[SHOWCHREND]
[SHOWSRNSTART]<tr valign="top"><td><b style="color:red">Seriennummern:</b></td><td>[SERIENNUMMERN]<i>(Pro Artikel eine Nummer)</i></td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr> [SHOWSRNEND]

<tr valign="top"><td><b>Standardlager:</b></td><td>[STANDARDLAGER]<br><br></td></tr>
<tr valign="top"><td><b>Einlagern in:</b></td><td><select name="lager">[LAGER]</select>
</td></tr> 

<tr valign="top"><td><br></td><td align="left"><br>
    <input type="submit" name="submit" value="[TEXTBUTTON]" />&nbsp;<input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distriinhalt&id=[ID]'"      value="Abbrechen" /></td></tr>
</table>

<br>
<br>
</td>
</tr>
</table>
<br><br>


</td>
      </tr>
<!--
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distriinhalt&id=[ID]'"
      value="Zur&uuml;ck" />
    <input type="submit" name="submit" value="[TEXTBUTTON]" /></td>
    </tr>
-->
    </tbody>
  </table>
</form>


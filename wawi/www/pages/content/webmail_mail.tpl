<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Eingang</a></li>
    </ul>

<!-- ende gehort zu tabview -->

<div id="tabs-1">
 <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td>

[MESSAGE]

<table border="0" width="100%">
<tr valign="top"><td width="100%">

<fieldset><legend>E-Mail</legend>
<table width="100%">
  <tr valign="top"><td width="80px">Von:</td><td>[MAILSENDER]&nbsp;<br>Am: [MAILDATUM]</td></tr>
[CC]
[BCC]
[REPLYTO]
  <tr><td>Betreff:</td><td>[MAILSUBJECT]</td></tr>
  <tr><td><br></td><td></td></tr>
  <tr><td colspan=2><iframe src="index.php?module=webmail&action=iframe&id=[ID]" id="webmail_print[ID]" name="webmail_print[ID]" style="WIDTH: 750px; HEIGHT: 400px; background-color: white;"></iframe>

  </td></tr>
    
</table>
</fieldset>

<fieldset><legend>Anhänge</legend>
<table width="100%">
[ANHAENGE]
</table>
</fieldset>

</td><td>
<fieldset><legend>Zuordnung Adresse</legend> 

<table width="100%">
<tr><td>[ADRESSEAUTOSTART]<input type="text" name="adresse" id="adresse" value="[ADRESSEVALUE]" size="30">[ADRESSEAUTOEND]</td></tr>
</table>
[ADRESSEMESSAGE]
<div align="center" style="margin-top:6px;">
<input type="submit" name="speichern" value="Speichern" style="width:150px"/><br>
</div>

</fieldset>
<fieldset><legend>Markierung</legend> 
<table width="100%">
<tr><td align="center"><input type="button" value="dringend sp&auml;ter antworten" onclick="window.location.href='index.php?module=webmail&action=antworten&id=[ID]'" style="width:170px">
<input type="button" value="als beantwortet markiert" onclick="window.location.href='index.php?module=webmail&action=beantwortet&id=[ID]'" style="width:170px">
<!--<input type="button" value="als gelesen" onclick="window.location.href='index.php?module=webmail&action=gelesen&id=[ID]'" style="width:170px">-->
<input type="button" value="als ungelesen" onclick="window.location.href='index.php?module=webmail&action=ungelesen&id=[ID]'" style="width:170px">
<input type="button" value="in Warteschlange schieben" onclick="window.location.href='index.php?module=webmail&action=warteschlange&id=[ID]'" style="width:170px">
</td></tr>
<!--<tr><td><input type="radio" name="do" value="print"/></td><td> Drucken</td></tr>
<tr><td><input type="radio" name="do" value="spam"/></td><td> SPAM</td></tr>-->
</table>
</fieldset>
<fieldset><legend>Aktionen</legend> 
<div align="center" style="margin-top:6px;">
<input type="button" value="Weiterleiten" style="width:150px" onclick="window.location.href='index.php?module=webmail&action=schreiben&cmd=fwd&id=[ID]'"/><br>
<input type="button" value="Antworten" style="width:150px" onclick="window.location.href='index.php?module=webmail&action=schreiben&id=[ID]'"/><br>
<input type="button" value="Drucken" style="width:150px" onclick="document.getElementById('webmail_print[ID]').contentWindow.print();"/><br>
<input type="button" value="Zur&uuml;ck zur &Uuml;bersicht" onclick="window.location.href='index.php?module=webmail&action=list'"style="width:150px"/><br>
</div>
</fieldset>
<fieldset><legend>E-Mail Verkehr</legend>

<table width="100%">
[VERKEHR]
</table>
</fieldset>


</td></tr>


</table>




        </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
</td>
    </tr>


    </tbody>
  </table>

</div>


</div>

 <!-- tab view schließen -->
<!-- ende tab view schließen -->
  
  </form>

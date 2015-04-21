<script type="text/javascript" src="http://www.google.com/jsapi">
</script>
<script type="text/javascript">
  google.load("language", "1");
</script>

<script type="text/javascript">

  function initialize() {
      translate("name_de", "name_v");
      translate("uebersicht_de", "uebersicht_v");
      translate("beschreibung_de", "beschreibung_v");
      translate("links_de", "links_v");

  }
 
   function translate(quelle, ziel){
    var text = document.getElementById(quelle).value;
    google.language.translate(text, "de", "en", function(result) {
        if (!result.error) {

	  if(ziel=='name_v')
	  {
	    var container = document.getElementById(ziel);
	    container.value = result.translation;
	  } else 
          tinyMCE.execInstanceCommand(ziel,'mceReplaceContent',false,result.translation);
        }
    });
  }

  google.setOnLoadCallback(initialize);

</script>

<table width="1200" border="0">
<form action="" method="post" name="eprooform">
<tr>
  <td>
      <table>
      <tr align="center"><td colspan="2"><b>Deutsch:<b></td>
      <td><b>Englisch:</b></td>
      <td rowspan="2"><b>Englisch-Vorschlag:</b></td></tr> 
     
      <tr align="center"><td colspan="2">[PREVLINK]</td>
      <td>[NEXTLINK]</td></tr>
       
      <tr><td>Name:&nbsp;</td><td><input id="name_de" type="text" size="46" maxlength="100" name="name_de" value="[NAME_DE]"></td>
      <td><input type="text" size="46" maxlength="100" name="name_en" value="[NAME_EN]"></td> 
      <td><input id="name_v" type="text" size="46" maxlength="100" name="name_vorschlag" ></td></tr>
      
      <tr><td>&Uuml;bersicht:&nbsp;</td><td><textarea id="uebersicht_de" rows="13" cols="53" wrap="physical" name="uebersicht_de">[UEBERSICHT_DE]</textarea></td>
      <td><textarea rows="13" cols="53" wrap="physical" name="uebersicht_en" id="uebersicht_en">[UEBERSICHT_EN]</textarea></td>
      <td><textarea id="uebersicht_v" rows="13" cols="53" wrap="physical" name="uebersicht_"></textarea></td></tr>

      <tr><td>Beschreibung:&nbsp;</td><td><textarea id="beschreibung_de" rows="13" cols="53" wrap="physical" name="beschreibung_de">[BESCHREIBUNG_DE]</textarea></td>
      <td><textarea rows="13" cols="53" wrap="physical" name="beschreibung_en" id="beschreibung_en">[BESCHREIBUNG_EN]</textarea></td>
      <td><textarea  name="beschreibung_" id="beschreibung_v" rows="13" cols="53" wrap="physical"></textarea></td></tr>
      
      <tr><td>Links:&nbsp;</td><td><textarea id="links_de" rows="6" cols="53" wrap="physical" name="links_de">[LINKS_DE]</textarea></td>
      <td><textarea rows="6" cols="53" wrap="physical" name="links_en" id="links_en">[LINKS_EN]</textarea></td>
      <td><textarea id="links_v" rows="6" cols="53" wrap="physical" name="links_v"></textarea></td></tr>
      </table>
</tr>
</form>
</table>



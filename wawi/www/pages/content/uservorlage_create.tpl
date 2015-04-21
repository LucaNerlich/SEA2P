<!--<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>[USER_CREATE]</td></tr></table></td></tr>
</table>-->

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Benutzer Vorlagen</a></li>
        <li><a href="#tabs-2">Vorlagen kopieren</a></li>
        <li><a href="#tabs-3">Rechte</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>Vorlage</legend>
    <table width="100%">
    <tr><td>Bezeichnung:*</td><td><input type="text" name="bezeichnung" value="[BEZEICHNUNG]" size="40"></td><td></tr>
    <tr><td width="200">Interne Beschreibung:</td><td><textarea name="beschreibung" rows="5" cols="40">[BESCHREIBUNG]</textarea>&nbsp;<i>Dient f&uuml;r Infos oder Notizen.</i></td></tr>
</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="submituser" value="Speichern" />
    </tr>

    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
<style>
table.module {
	width: 100%;
	border-spacing: 1px;
}

table.module td.name {
	width: 100%;
	padding: 5px 10px;
	background:#5CCD00;
	color: #fff;
	font-size: 15px;
	font-weight: 600;
	border-radius: 3px;
	background:-moz-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
	background:-webkit-gradient(linear,left top,left bottom,color-stop(0%,#5CCD00),color-stop(100%,#4AA400));
	background:-webkit-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
	background:-o-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
	background:-ms-linear-gradient(top,#5CCD00 0%,#4AA400 100%);
	background:linear-gradient(top,#5CCD00 0%,#4AA400 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#5CCD00',endColorstr='#4AA400',GradientType=0);
}

table.action {
  width: 100%;
	margin-bottom: 20px;
	border-spacing: 2px;
}

table.action td.blue {
  padding: 3px;
	background:#25A6E1;
	color: #fff;
	border: 1px solid #0D7EE8;
	border-radius: 2px;
	background:-moz-linear-gradient(top,#25A6E1 0%,#188BC0 100%);
	background:-webkit-gradient(linear,left top,left bottom,color-stop(0%,#25A6E1),color-stop(100%,#188BC0));
	background:-webkit-linear-gradient(top,#25A6E1 0%,#188BC0 100%);
	background:-o-linear-gradient(top,#25A6E1 0%,#188BC0 100%);
	background:-ms-linear-gradient(top,#25A6E1 0%,#188BC0 100%);
	background:linear-gradient(top,#25A6E1 0%,#188BC0 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#25A6E1',endColorstr='#188BC0',GradientType=0);
}

table.action td.grey {
  padding: 3px;
  color: #fff;
  border-radius: 2px;
	background: #666666;
	background: -moz-linear-gradient(top, #666666 0%, #969696 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#666666), color-stop(100%,#969696));
	background: -webkit-linear-gradient(top, #666666 0%,#969696 100%);
	background: -o-linear-gradient(top, #666666 0%,#969696 100%);
	background: -ms-linear-gradient(top, #666666 0%,#969696 100%);
	background: linear-gradient(to bottom, #666666 0%,#969696 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#969696',GradientType=0 );
}

table.action td.blue:hover, td.grey:hover {
	cursor: pointer;
	text-shadow: 0px 2px 2px #555;
}
</style>
<script>
	function ChangeRights(el, vorlage, module, action) {
		var value = $(el).attr('value');
		if(value=='1') value = 0; else value = 1;
		$.ajax({
			url: './index.php?module=uservorlage&action=chrights&b_vorlage='+vorlage+'&b_module='+module+'&b_action='+action+'&b_value='+value, 
			success: function(r) {
				if(r=='1') {
					$(el).removeClass('grey');
					$(el).addClass('blue');
					$(el).attr('value', '1')
				}else{
					$(el).removeClass('blue');
          $(el).addClass('grey');
					$(el).attr('value', '0')
				}
			}
		});

	}
</script>


<div id="tabs-2">
<fieldset><legend>Rechte von Benutzer Vorlage kopieren</legend>  <form action="" id="copyuserrightsform" method="POST">
    <table><tr><td width="300">Rechte von Benutzer kopieren:</td><td><select name="copyusertemplate" style="margin-left: 15px">[BEZEICHNUNGSELECT]</select>
    <input type="button" name="templatesubmit" value="kopieren und &uuml;bernehmen" style="margin-left: 15px" 
      onclick="if(!confirm('Es werden alle Benutzerrechte überschrieben. Fortfahren?')) return false;else document.getElementById('copyuserrightsform').submit();">
      </td></tr></table>
  </form>
</fieldset>

</div>
<div id="tabs-3">
	<i>Hinweis: Blau = erlaubt, Grau = gesperrt</i>
[HINWEISADMIN]
	<br><br>
	<table class="module">
		[MODULES]
	</table>

</div>
</div>

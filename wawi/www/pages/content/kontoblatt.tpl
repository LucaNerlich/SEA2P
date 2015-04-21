<script type="text/javascript">
function addKomma(box)
{
	var val = box.value;

	if(!isNaN(val) && val.length > 2 && val.indexOf('.')<0)
	{
		box.value = val.substr(0,val.length-2) + "." + val.substr(val.length-2, 2);
	}
}

function getDatum(box)
{
	var val = box.value;

	if(!isNaN(val) && val.length >= 4)
	{
		var tag = val.substr(0,2);
		var monat = val.substr(2, 2);
		if(val.length==6)
			var jahr = "." + val.substr(4,2);
		else
			var jahr = "";

		box.value = tag + "." + monat + jahr
	}
}

function SollHaben(box)
{
	if(box.value == "" || box.value == "+" || box.value == "s")	box.value = "+";
	if(box.value == "-" || box.value == "h")	box.value = "-"; 
}

function showPrompt(link)
{
	var betrag = prompt("Welchen Betrag möchten Sie abspalten?", "");

	if(betrag===false)
		return false;
	else
		window.location.href=link + "&betrag=" + betrag;

}




var status;
var group;
document.onkeydown = function(event) {

	if(event.keyCode == 33)
		document.getElementById("forward").click();

	if(event.keyCode == 34)
		document.getElementById("backward").click();
	

	if (event.keyCode == 13)
	{
		if(group==1)
		{
			if(status==1)
			{
				document.getElementById("typ").focus();
				status = 2;
			}else if(status==2)
			{
				document.getElementById("gegenkonto").focus();
				status = 3;
			}else if(status==3)
			{
				document.getElementById("beleg").focus();
				status=4;
			}else if(status==4)
			{
				document.getElementById("datum").focus();
				status=5;
			}else if(status==5)
			{
				document.getElementById("kontoneu").focus();
      	status=6;
			}else if(status==6)
			{
				document.getElementById("skonto").focus();
				status=7;
			}else if(status==7)
			{
				document.getElementById("text").focus();
      	status=8;
			}else
			{
				status="";
				document.getElementById("buchen").click();
			}
		}

		if(group==2)
		{
			if(status==1)
			{
				document.getElementById("konto").focus();
        status = 2;
			}else if(status==2)
      {
        document.getElementById("von").focus();
        status = 3;
      }else if(status==3)
      {
        document.getElementById("bis").focus();
        status=4;
      }else
			{
				status="";
        document.getElementById("submitkonto").click();
			}

		}
		//alert(status + " " + group);
	}
}

function setStatus(val,grp)
{
	status = val;
	group = grp;
}
</script>

<form action="" method="POST" name="kontoform">
<table width="100%" border="0">
<tr><td>Konto:</td>
  <td width="80%" align="left"><input type="text" value="[KONTO]" size="8" name="konto" id="konto" onfocus="setStatus(1,2);" [SPLITMODE]>&nbsp;
	<input type="text" value="[VON]" size="10" name="von" id="von" [SPLITMODE] onfocus="setStatus(2,2);" onblur="getDatum(this)">&nbsp;[DATUMVON]&nbsp;bis:&nbsp;
	<input type="text" name="bis" id="bis" value="[BIS]" size="10" [SPLITMODE] onfocus="setStatus(3,2);"onblur="getDatum(this)">&nbsp;
	<input type="button" onclick="document.getElementById('mysubmit').value='1';document.kontoform.submit()" value="&uuml;bernehmen" name="uebernehmen" onfocus="setStatus(0)"[SPLITMODE] id="submitkonto">
	<input type="hidden" value="" id="mysubmit" name="mysubmit" >



<input type="checkbox" value="1" name="kontozeile" [KONTOZEILE]>&nbsp;Kontozeile anzeigen </td>
  <td width="10%"><input type="hidden" name="backward" id="backwardvalue" value=""><input type="button" id="backward" [SPLITMODE] value="&nbsp;<<" 
			onclick="document.getElementById('backwardvalue').value='&nbsp;<<';document.kontoform.submit()"></td>
  <td width="10%"><input type="hidden" name="forward" id="forwardvalue" value=""><input type="button" id="forward" value=">>&nbsp;"  [SPLITMODE] 
			onclick="document.getElementById('forwardvalue').value='&nbsp;<<';document.kontoform.submit()"></td>
</tr>

<tr><td>Volltextsuche:</td><td colspan="9"><input type="text" value="[VOLLTEXT]" name="volltext" size="40">&nbsp;</td></tr>

</table>
<br><br>
<fieldset><legend>Neue Buchung</legend>
<table width="100%">
<tr>
	<td>Betrag</td>
	<td>+ / -</td>
	<td>Gegenkonto</td>
	<td>Beleg</td>
	<td>Datum</td>
	<td>Konto</td>
	<td>SKonto</td>
	<td>Buchungstext</td>
	<td></td>
</tr>
<tr>
	<td><input type="text" name="betrag" id="betrag" size="7" value="[BETRAG]" onblur="addKomma(this);" onfocus="setStatus(1,1);"></td>
	<td><input type="text" name="typ" id="typ" size="1" value="[TYP]" onblur="SollHaben(this);" onfocus="setStatus(2,1);"></td>
	<td><input type="text" name="gegenkonto" id="gegenkonto" size="8" value="[GEGENKONTO]" onfocus="setStatus(3,1);"></td>
	<td><input type="text" name="beleg" id="beleg" size="10" value="[BELEG]" onfocus="setStatus(4,1);"></td>
	<td><input type="text" name="datum" id="datum" size="10" value="[DATUM]" onBlur="getDatum(this);" onfocus="setStatus(5,1);"></td>
	<td><input type="text" name="kontoneu" id="kontoneu" size="6" value="[KONTONEU]" onfocus="setStatus(6,1);"></td>
	<td><input type="text" name="skonto" id="skonto" size="7" value="[SKONTO]" onblur="addKomma(this);" onfocus="setStatus(7,1);"></td>
	<td><input type="text" name="text" id="text" size="50" value="[TEXT]" onfocus="setStatus(8,1);"></td>
	<td><input type="button" value="Buchen" id="buchen" onclick="document.getElementById('neueBuchung').value='Buchen'; document.kontoform.submit()"></td>
</tr>
	<input type="hidden" id="neueBuchung" name="neueBuchung" value="">
</table>
[GESPERRT]
</fieldset>
<hr>

<table width="100%" cellspacing="0" cellpadding="3" id="tableone" border="0">
<tr>
	<td width="60"><b>Datum</b></td>
	<td width="80" align="right"><b>Betrag</b></td>
	<td width="20"><b>&nbsp;+/-&nbsp;</b></td>
	<td width="90" align="left"><b>Gegenkonto</b></td>
	<td width="580"><b>Buchungstext</b></td>
	<td><b>Belegfeld</b></td>
	<td>&nbsp;<b>Skonto</b></td>
	<td>&nbsp;<b>Konto</b></td>
	<td>&nbsp;<b>Aktion</b></td>
</tr>
[TABLE]
</table>
</form>

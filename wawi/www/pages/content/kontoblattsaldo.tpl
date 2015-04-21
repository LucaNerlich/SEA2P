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



var status;
var group;
document.onkeydown = function(event) {

	if(event.keyCode == 33)
		document.getElementById("forward").click();

	if(event.keyCode == 34)
		document.getElementById("backward").click();
	

	if (event.keyCode == 13)
	{
     document.getElementById("submitkonto").click();

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

<input type="checkbox" value="1" name="kontozeile" [KONTOZEILE]>&nbsp;Gleiche Buchungen gruppieren
<input type="checkbox" value="1" name="ohnekontozeile" [OHNEKONTOZEILE]>&nbsp;manuell gebuchten
</td>
  <td width="10%"><input type="hidden" name="backward" id="backwardvalue" value=""><input type="button" id="backward" [SPLITMODE] value="&nbsp;<<" 
			onclick="document.getElementById('backwardvalue').value='&nbsp;<<';document.kontoform.submit()"></td>
  <td width="10%"><input type="hidden" name="forward" id="forwardvalue" value=""><input type="button" id="forward" value=">>&nbsp;"  [SPLITMODE] 
			onclick="document.getElementById('forwardvalue').value='&nbsp;<<';document.kontoform.submit()"></td>
</tr>

<tr><td>Volltextsuche:</td><td colspan="9"><input type="text" value="[VOLLTEXT]" name="volltext" size="40">&nbsp;</td></tr>

</table>
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

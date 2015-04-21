
<script type="text/javascript">

function AddValue()
{
//	document.getElementById("menge").focus();
	if(document.getElementById("menge").value=="")
		document.getElementById("menge").value='1';
	else
		document.getElementById("menge").value=parseInt(document.getElementById("menge").value)+1;
}

function SubValue()
{
	//	document.getElementById("menge").focus();
	if(document.getElementById("menge").value=="" || parseInt(document.getElementById("menge").value) <1)
		document.getElementById("menge").value='1';
	else
		document.getElementById("menge").value=parseInt(document.getElementById("menge").value)-1;

}

</script>
<form action="" method="post">
<h2 class="smartphone">Einlagern</h2>
<table align="center">
<tr><td><h3 class="smartphone">Menge:</h3></td></tr>
<tr valig="middle"><td nowrap><input type="text" size="8" id="menge" class="menge" name="menge" value="[DEFAULTVALUE]" placeholder="[PLACEHOLDER]"></td></tr>
<!--<tr><td><input type="button" value="Wareneingang" class="smartphone_mainbutton"></td></tr>
<tr><td><input type="button" value="Inventur" class="smartphone_mainbutton"></td></tr>-->
<tr><td><br></td></tr>
<tr><td><input type="button" value="+" name="submit" class="smartphone_mainbutton" onclick="AddValue()" style="width:50px">&nbsp;&nbsp;<input type="button" value="- " style="width:50px" name="submit" class="smartphone_mainbutton" onclick="SubValue()"></td></tr>
<tr><td><br></td></tr>
<tr><td><input type="submit" value="weiter " name="submit" class="smartphone_mainbutton">&nbsp;<input type="button" value="abbrechen" class="smartphone_mainbutton" style="width:75px" onclick="window.location.href='index.php?module=mobile&action=cancel'"></td></tr>
<!--<tr><td><input type="button" value="Zur&uuml;ck" class="smartphone_mainbutton" onclick="window.location.href='index.php?module=mobile&action=list'"></td></tr>-->
</table>
</form>

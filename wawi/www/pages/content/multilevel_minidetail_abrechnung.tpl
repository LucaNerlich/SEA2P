<script type="text/javascript">

function BaumAnsicht(adresse,abrechnung)
{

$.ajax({
  url: 'index.php',
  type: 'GET',
  data: 'module=multilevel&action=baumansicht&id='+adresse+'&abrechnung='+abrechnung,
  success: function(data) {
        //called when successful

newwin=window.open('','printwin','width=700,height=500,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Print Page</TITLE>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY>\n')
newwin.document.write(data)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()


  },
  error: function(e) {
        //called when there is an error
        //console.log(e.message);
  }
});
}

function DetailsZahlung(rechnung_position)
{

$.ajax({
  url: 'index.php',
  type: 'GET',
  data: 'module=multilevel&action=detailsposition&id='+rechnung_position,
  success: function(data) {
	//called when successful

newwin=window.open('','printwin','width=1000,height=650,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Print Page</TITLE>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY>\n')
newwin.document.write(data)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()


  },
  error: function(e) {
	//called when there is an error
	//console.log(e.message);
  }
});
}

<!--
function printContent(id){
str=document.getElementById(id).innerHTML
newwin=window.open('','printwin','left=100,top=100,width=400,height=400')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Print Page</TITLE>\n')
newwin.document.write('<script>\n')
newwin.document.write('function chkstate(){\n')
newwin.document.write('if(document.readyState=="complete"){\n')
newwin.document.write('window.close()\n')
newwin.document.write('}\n')
newwin.document.write('else{\n')
newwin.document.write('setTimeout("chkstate()",2000)\n')
newwin.document.write('}\n')
newwin.document.write('}\n')
newwin.document.write('function print_win(){\n')
newwin.document.write('window.print();\n')
newwin.document.write('chkstate();\n')
newwin.document.write('}\n')
newwin.document.write('<\/script>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY onload="print_win()">\n')
newwin.document.write(str)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()
}
//-->
</script><div id="multilevel_minidetail_[ID]">
  
<script>
      $(function() {
    $( "#accordion[ID]" ).accordion({ autoHeight: true,fillSpace: true });
  });
  </script>
<style>

.auftrag_cell {
  color: #636363;border: 1px solid #ccc;padding: 0;

}
a:hover {cursor:pointer}
</style>


<table width="900" border="0">
<tr valign="top"><td>

</td><td width="100%">  
<div style="width:900px; height:100%; background-color:white; padding:20px;">
  <div>
		<h2>Auszahlungen</h2><hr><p>[AUSZAHLUNGEN]</p>
			</div>
<br><br>
<center><button onclick="printContent('multilevel_minidetail_[ID]')">Drucken</button></center>
</div>

</td></tr>

</table>

</div>

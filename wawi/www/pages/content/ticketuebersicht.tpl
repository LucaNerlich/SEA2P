<script>
	$(function() {
$(".button").button();
	});
	</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#selecctall').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.checkall').each(function() { //loop through each checkbox                this.checked = true;  //select all checkboxes with class "checkbox1"              
            });
        }else{
            $('.checkall').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
        }
    });
   });
</script>

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Tickets</a></li>
[ZUGEWIESENESTART]        <li><a href="#tabs-2">Wiedervorlagen</a></li>[ZUGEWIESENEENDE]
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<!--<table width="100%"><tr>
        <td><a href="index.php?module=ticket&action=list" class="button">offene Tickets</a></td>
        <td><a href="index.php?module=ticket&action=list&cmd=zugewiesene" class="button">zugewiesene Tickets</a></td>
        <td><a href="index.php?module=ticket&action=list&cmd=bearbeitung" class="button">In Bearbeitung</a></td>
        <td><a href="index.php?module=ticket&action=list&cmd=archiv" class="button">Archiv</a></td>
        <td><a href="index.php?module=ticket&action=list&cmd=spam" class="button">Papierkorb</a></td>
    </tr></table>
<br><hr><br>-->

[TAB1]
</div>

[ZUGEWIESENESTART]  
<div id="tabs-2">
[TAB2]
</div>
[ZUGEWIESENEENDE]


<!-- tab view schließen -->
</div>

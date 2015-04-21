

<script>
$(function() {
$( 'a.ui-dialog-titlebar-close' ).remove();
$( "#dialog-message" ).dialog({
modal: true,
	closeOnEscape:false,
open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();  navigator.geolocation.getCurrentPosition(successHandler,errorHandler); },
buttons: {
Ok: function() {

$( this ).dialog( "close" );

window.location.href='index.php?module=gpsstechuhr&action=save&koordinaten='+location2;
}

}
});
});
jQuery("a.ui-dialog-titlebar-close").hide();


var location2;

function successHandler(location) {
    //.push("<p>Longitude: ", location.coords.longitude, "</p>");
    //.push("<p>Latitude: ", location.coords.latitude, "</p>");
    //.push("<p>Accuracy: ", location.coords.accuracy, " meters</p>");

   var message = document.getElementById("message"), html = [];
    html.push("<img width='180' height='180' src='http://maps.google.com/maps/api/staticmap?center=", location.coords.latitude, ",", location.coords.longitude, "&markers=size:small|color:blue|", location.coords.latitude, ",", location.coords.longitude, "&zoom=14&size=180x180&sensor=false' />");
    message.innerHTML = html.join("");

    location2 = location.coords.latitude + ";" + location.coords.longitude;// + ";"+location.coords.accuracy;
}
function errorHandler(error) {
    alert('Attempt to get location failed: ' + error.message);
}
</script>

<div id="dialog-message" title="Speicherung des Ortes">
<p>

Bitte best&auml;tigen Sie mit einem Klick auf Ok den aktuellen Ort f&uuml;r den ersten Login an diesem Tag.

<div id="message"><img align="center" src="./themes/[THEME]/images/load.gif">&nbsp;&nbsp;&nbsp;Bitte warten - lade Daten ...</div>

</p>
</div>


Sie befinden sich in der Stechuhr.


function isTouchDevice()
{
        var ua = navigator.userAgent;
        var isTouchDevice = (
            ua.match(/iPad/i) ||
            ua.match(/iPhone/i) ||
            ua.match(/iPod/i) ||
            ua.match(/Android/i)
        );
 
        return isTouchDevice;
}

function callCursorArbeitsnachweis()
{
  setTimeout(continueExecutionArbeitsnachweis, 1000) //wait ten seconds before continuing
}

function callCursor()
{
  setTimeout(continueExecution, 1000) //wait ten seconds before continuing
}

function continueExecutionArbeitsnachweis()
{
  document.getElementById('framepositionen').contentWindow.document.getElementById('adresse').value = ""; 
  document.getElementById('framepositionen').contentWindow.document.getElementById('adresse').focus();
}

function continueExecution()
{
  document.getElementById('framepositionen').contentWindow.document.getElementById('artikel').value = ""; 
  document.getElementById('framepositionen').contentWindow.document.getElementById('artikel').focus();
}

function Ansprechpartner(value)
{

     var strSource = "./index.php";
     var strData = "module=ajax&action=ansprechpartner&id="+value;
     var intType= 0; //GET
     var intID = 0;
     command = 'getAnsprechpartner';
     sendRequest(strSource,strData,intType,intID);

}

function Lieferadresse(value)
{

     var strSource = "./index.php";
     var strData = "module=ajax&action=lieferadresse&id="+value;
     var intType= 0; //GET
     var intID = 0;
     command = 'getLieferadresse';
     sendRequest(strSource,strData,intType,intID);
}


function AnsprechpartnerIframe(value)
{

     var strSource = "./index.php";
     var strData = "module=ajax&action=ansprechpartner&id="+value;
     var intType= 0; //GET
     var intID = 0;
     command = 'getAnsprechpartner';
     sendRequest(strSource,strData,intType,intID);
		 parent.closeIframe();
}

function LieferadresseIframe(value)
{

     var strSource = "./index.php";
     var strData = "module=ajax&action=lieferadresse&id="+value;
     var intType= 0; //GET
     var intID = 0;
     command = 'getLieferadresse';
     sendRequest(strSource,strData,intType,intID);
		 parent.closeIframe();
}



function LieferadresseLS(value)
{

     var strSource = "./index.php";
     var strData = "module=ajax&action=lieferadresse&id="+value;
     var intType= 0; //GET
     var intID = 0;
     command = 'getLieferadresseLS';
     sendRequest(strSource,strData,intType,intID);
}




function CopyDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich kopiert werden?")) return false;
    else window.location.href=value;

}


function PrintDialog(value)
{
  if(!confirm("Soll der Eintrag gedruckt werden?")) return false;
    else window.location.href=value;
}

function InsertDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich eingefügt werden?")) return false;
    else window.location.href=value;

}

function DisableDialog(value)
{
  if(!confirm("Soll der Eintrag wirklich deaktiviert werden?")) return false;
    else window.location.href=value;

}

function FinalDialog(value)
{
  if(!confirm("Soll der Eintrag wirklich abgeschlossen werden?")) return false;
    else window.location.href=value;

}

function UndoDialog(value)
{
  if(!confirm("Soll der Eintrag wirklich rückgängig gemacht werden?")) return false;
    else window.location.href=value;
}


function BezahltDialog(value)
{

  if(!confirm("Soll der Eintrag manuell ohne SEPA Überweisung; als bezahlt markiert werden?")) return false;
    else window.location.href=value;
}





function StornoDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich storniert werden?")) return false;
    else window.location.href=value;

}

function DeleteDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich gelöscht oder storniert werden?")) return false;
    else window.location.href=value;

}

function DialogGutschrift(value)
{

  if(!confirm("Soll die Rechnung storniert oder gut geschrieben werden?")) return false;
    else window.location.href=value;

}

function DialogAnfrageStart(value)
{

  if(!confirm("Soll die Anfrage gestartet werden?")) return false;
    else window.location.href=value;

}


function DialogAnfrageAbschluss(value)
{

  if(!confirm("Soll die Anfrage abgeschlossen werden?")) return false;
    else window.location.href=value;
}




function DialogDifferenz(value)
{

  if(!confirm("Soll der fehlende Betrag als Skonto gebucht werden?")) return false;
    else window.location.href=value;

}

function DialogMahnwesen(value)
{

  if(!confirm("Soll die Rechnung vorrübergehend aus dem Mahnwesen genommen werden?")) return false;
    else window.location.href=value;

}



function VerbandAbrechnen(value)
{
	var today = new Date();
	var month = today.getMonth()+1;
	var year = today.getYear();
	var day = today.getDate();
	if(day<10) day = "0" + day;
	if(month<10) month= "0" + month;
	if(year<1000) year+=1900;

	var vorschlag = year+ "-" + month + "-" + day;

  var termin = prompt("Abrechnung für Rechnungen bis zum YYYY-MM-DD starten:",vorschlag);

	if (termin != '' && termin != null) 
  	window.location.href=value+"&tag="+termin;

}

function BackupDialog(value)
{

	if(!confirm("Soll das Backup wirklich wieder eingespielt werden? Alle seit dem vorgenommenen Änderungen gehen verloren.")) return false;
    else window.location.href=value;

}

function ResetDialog()
{

  if(!confirm("Wollen Sie die Datenbank wirklich zurücksetzen?")) return false;
    else return true;

}

function getXMLRequester( )
{
    var xmlHttp = false; //Variable initialisieren
            
    try
    {
        // Der Internet Explorer stellt ein ActiveXObjekt zur Verfügung
        if( window.ActiveXObject )
        {
            // Versuche die neueste Version des Objektes zu laden
            for( var i = 5; i; i-- )
            {
                try
                {
                    //Wenn keine neuere geht, das alte Objekt verwenden
                    if( i == 2 )
                    {
                        xmlHttp = new ActiveXObject( "Microsoft.XMLHTTP" );    
                    }
                    // Sonst die neuestmögliche Version verwenden
                    else
                    {
                        
                        xmlHttp = new ActiveXObject( "Msxml2.XMLHTTP." + i + ".0" );
                    }
                    break; //Wenn eine Version geladen wurde, unterbreche Schleife
                }
                catch( excNotLoadable )
                {                        
                    xmlHttp = false;
                }
            }
        }
        // alle anderen Browser
        else if( window.XMLHttpRequest )
        {
            xmlHttp = new XMLHttpRequest();
        }
    }
    // loading of xmlhttp object failed
    catch( excNotLoadable )
    {
        xmlHttp = false;
    }
    return xmlHttp ;
}
// Konstanten
var REQUEST_GET        = 0;
var REQUEST_POST        = 2;
var REQUEST_HEAD    = 1;
var REQUEST_XML        = 3;

function sendRequest( strSource, strData, intType, intID )
{
    // Falls strData nicht gesetzt ist, als Standardwert einen leeren String setzen
    if( !strData )
        strData = '';

    // Falls der Request-Typ nicht gesetzt ist, standardmäßig auf GET setzen
    if( isNaN( intType ) )
        intType = 0;

    // wenn ein vorhergehender Request noch nicht beendet ist, beenden
    if( xmlHttp && xmlHttp.readyState )
    {
        xmlHttp.abort( );
        xmlHttp = false;
    }
        
    // wenn möglich, neues XMLHttpRequest-Objekt erzeugen, sonst abbrechen
    if( !xmlHttp )
    {
        xmlHttp = getXMLRequester( );
        if( !xmlHttp )
            return;
    }
    
    // Falls die zu sendenden Daten mit einem & oder einem ? beginnen, erstes Zeichen abschneiden
    if( intType != 1 && ( strData && strData.substr( 0, 1 ) == '&' || strData.substr( 0, 1 ) == '?' ))
        strData = strData.substring( 1, strData.length );

// Als Rückgabedaten die gesendeten Daten, oder die Zieladresse setzen
    var dataReturn = strData ? strData : strSource;
    
    switch( intType )
    {
        case 1:    //Falls Daten in XML-Form versendet werden, xml davorschreiben
            strData = "xml=" + strData;
        case 2: // falls Daten per POST versendet werden
            // Verbindung öffnen 
            xmlHttp.open( "POST", strSource, true );
            xmlHttp.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
            xmlHttp.setRequestHeader( 'Content-length', strData.length );
            break;
        case 3: // Falls keine Daten versendet werden
            // Verbindung zur Seite aufbauen
            xmlHttp.open( "HEAD", strSource, true );
            strData = null;
            break;
        default: // Falls Daten per GET versendet werden
            //Zieladresse zusammensetzen aus Adresse und Daten
            var strDataFile = strSource + (strData ? '?' + strData : '' );
            // Verbindung aufbauen
            xmlHttp.open( "GET", strDataFile, true );
            strData = null;
    }
    
    // die Funktion processResponse als Event-handler setzen, wenn sich der Verarbeitungszustand der 
    xmlHttp.onreadystatechange = new Function( "", "processResponse(" + intID + ")" ); ;

    // Anfrage an den Server setzen
    xmlHttp.send( strData );    //strData enthält nur dann Daten, wenn die Anfrage über POST passiert

    // gibt die gesendeten Daten oder die Zieladresse zurück
    return dataReturn;
}


function processResponse( intID )
{
    //aktuellen Status prüfen
    switch( xmlHttp.readyState )
    {
        //nicht initialisiert
        case 0:
        // initialisiert
        case 1:
        // abgeschickt
        case 2:
        // ladend
        case 3:
            break;
        // fertig
        case 4:    
            // Http-Status überprüfen
            if( xmlHttp.status == 200 )    // Erfolg
            {
                processData( xmlHttp, intID ); //Daten verarbeiten
            }
            //Fehlerbehandlung
            else
            {
                if( window.handleAJAXError )
                    handleAJAXError( xmlHttp, intID );
                else
                    alert( "ERROR\n HTTP status = " + xmlHttp.status + "\n" + xmlHttp.statusText ) ;
            }
    }
}

// handle response errors
function handleAJAXError( xmlHttp, intID )
{
  //alert("AJAX Fehler!");
}

var command;
var lastartikelnummer;

var once;

function Select_Value_Set(SelectName, Value) {
  eval('SelectObject = parent.document.' + 
    SelectName + ';');
  for(index = 0; 
    index < SelectObject.length; 
    index++) {
   if(SelectObject[index].value == Value)
     SelectObject.selectedIndex = index;
   }
}

function processData( xmlHttp, intID )
{
  // process text data
  //updateMenu( xmlHttp.responseText );
  var render=0;
  switch(command)
  {
		case 'getAnsprechpartner':
			var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");

			if(trim(mySplitResult[0])!="") parent.document.getElementById('ansprechpartner').value=trim(mySplitResult[0]);
			if(trim(mySplitResult[1])!="") parent.document.getElementById('email').value=trim(mySplitResult[1]);
			if(trim(mySplitResult[2])!="") parent.document.getElementById('telefon').value=trim(mySplitResult[2]);
			if(trim(mySplitResult[3])!="") parent.document.getElementById('telefax').value=trim(mySplitResult[3]);
			if(trim(mySplitResult[4])!="") parent.document.getElementById('abteilung').value=trim(mySplitResult[4]);
			if(trim(mySplitResult[5])!="") parent.document.getElementById('unterabteilung').value=trim(mySplitResult[5]);
			Select_Value_Set('eprooform.land',trim(mySplitResult[6]));
			if(trim(mySplitResult[7])!="") parent.document.getElementById('strasse').value=trim(mySplitResult[7]);
			if(trim(mySplitResult[8])!="") parent.document.getElementById('plz').value=trim(mySplitResult[8]);
			if(trim(mySplitResult[9])!="") parent.document.getElementById('ort').value=trim(mySplitResult[9]);
			if(trim(mySplitResult[10])!="") parent.document.getElementById('adresszusatz').value=trim(mySplitResult[10]);
			Select_Value_Set('eprooform.typ',trim(mySplitResult[11]));
			parent.document.getElementById('anschreiben').value=trim(mySplitResult[12]);
		break;
		case 'getLieferadresse':
			var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
			if(parent.document.getElementById('liefername'))
			{
			parent.document.getElementById('liefername').value=trim(mySplitResult[0]);
			parent.document.getElementById('lieferabteilung').value=trim(mySplitResult[1]);
			parent.document.getElementById('lieferunterabteilung').value=trim(mySplitResult[2]);
			//parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
			Select_Value_Set('eprooform.lieferland',trim(mySplitResult[3]));
			parent.document.getElementById('lieferstrasse').value=trim(mySplitResult[4]);
			parent.document.getElementById('lieferort').value=trim(mySplitResult[5]);
			parent.document.getElementById('lieferplz').value=trim(mySplitResult[6]);
			parent.document.getElementById('lieferadresszusatz').value=trim(mySplitResult[7]);
			parent.document.getElementById('lieferansprechpartner').value=trim(mySplitResult[8]);

			} else {

			parent.document.getElementById('name').value=trim(mySplitResult[0]);
			parent.document.getElementById('abteilung').value=trim(mySplitResult[1]);
			parent.document.getElementById('unterabteilung').value=trim(mySplitResult[2]);
			//parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
			Select_Value_Set('eprooform.land',trim(mySplitResult[3]));
			parent.document.getElementById('strasse').value=trim(mySplitResult[4]);
			parent.document.getElementById('ort').value=trim(mySplitResult[5]);
			parent.document.getElementById('plz').value=trim(mySplitResult[6]);
			parent.document.getElementById('adresszusatz').value=trim(mySplitResult[7]);
			parent.document.getElementById('ansprechpartner').value=trim(mySplitResult[8]);
			}

    //name  abteilung   unterabteilung  land  strasse   ort   plz adresszusatz
/*
			parent.document.getElementById('').value=trim(mySplitResult[1]);
			parent.document.getElementById('telefon').value=trim(mySplitResult[2]);
			parent.document.getElementById('telefax').value=trim(mySplitResult[3]);
			parent.document.getElementById('abteilung').value=trim(mySplitResult[4]);
			parent.document.getElementById('unterabteilung').value=trim(mySplitResult[5]);
*/

		break;

    case 'fillArtikel':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
				render=1;
      	document.getElementById("artikel").value=trim(mySplitResult[0]);
      	document.getElementById("nummer").value=mySplitResult[1];

			if(mySplitResult[1]=="") { 
				alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel: Suche oder Neu anlegen.');
			} else {

      	document.getElementById("projekt").value=mySplitResult[2];
      	document.getElementById("preis").value=mySplitResult[3];
      	document.getElementById("menge").value=mySplitResult[4];


			if(document.getElementById("preis").value==0 || document.getElementById("preis").value=="") {
				document.getElementById('preis').style.background ='#FE2E2E';
				if(once!=1)
				alert('Achtung: Es ist kein Verkaufspreis hinterlegt!');
				once = 1;
				document.getElementById('preis').focus();
			} else {
				document.getElementById('preis').style.background ='';
				//document.getElementById('preis').setAttribute("readonly", "readonly");
				if(lastartikelnummer!=mySplitResult[1])
				{
					document.getElementById('menge').focus();
					document.getElementById('menge').select();
				}
			}
			}
				lastartikelnummer = mySplitResult[1];
      }
   	break;


   case 'fillArtikelBestellung':
  
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
				render=1;
      	document.getElementById("artikel").value=trim(mySplitResult[0]);
      	document.getElementById("nummer").value=mySplitResult[1];
				if(mySplitResult[1]=="") { 
					alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel: Suche oder Neu anlegen.');
			} else {
      document.getElementById("projekt").value=mySplitResult[2];
      document.getElementById("preis").value=mySplitResult[3];
      document.getElementById("menge").value=mySplitResult[4];
      document.getElementById("bestellnummer").value=mySplitResult[5];
      document.getElementById("bezeichnunglieferant").value=mySplitResult[6];
      document.getElementById("vpe").value=mySplitResult[7];
    }
    }
    break;


    case 'fillArtikelLieferschein':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
			render=1;
      document.getElementById("artikel").value=trim(mySplitResult[0]);
      document.getElementById("nummer").value=mySplitResult[1];
			if(mySplitResult[1]=="") { 
				alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel: Suche oder Neu anlegen.');
			} else {
      document.getElementById("projekt").value=mySplitResult[2];
      document.getElementById("menge").value=mySplitResult[4];
      }
      }
   break;
    case 'fillArtikelProduktion':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
			render=1;
      document.getElementById("artikel").value=trim(mySplitResult[0]);
      document.getElementById("nummer").value=mySplitResult[1];
			if(mySplitResult[1]=="") { 
				alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel: Suche oder Neu anlegen.');
			} else {
      document.getElementById("projekt").value=mySplitResult[2];
      document.getElementById("menge").value=mySplitResult[4];
      }
      }
   break;

   case 'fillArtikelInventur':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
				render=1;
      document.getElementById("artikel").value=trim(mySplitResult[0]);
      document.getElementById("nummer").value=mySplitResult[1];
			if(mySplitResult[1]=="") { 
				alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel: Suche oder Neu anlegen.');
			} else {


      document.getElementById("projekt").value=mySplitResult[2];
      document.getElementById("preis").value=mySplitResult[3];
      document.getElementById("menge").value=mySplitResult[4];
    }
    }
    break;

  }
  if(render<=0)
  {

    document.getElementById("menge").value="";
    document.getElementById("nummer").value="";
    document.getElementById("projekt").value="";
		if(command!='fillArtikelProduktion')
    document.getElementById("preis").value="";
  }
}

function trim (zeichenkette) {
  // Erst führende, dann Abschließende Whitespaces entfernen
  // und das Ergebnis dieser Operationen zurückliefern
  return zeichenkette.replace (/^\s+/, '').replace (/\s+$/, '');
}





// globales XMLHttpRequest-Objekt erzeugen
var xmlHttp = getXMLRequester();



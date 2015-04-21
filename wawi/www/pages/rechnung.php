<?php
include ("_gen/rechnung.php");
//require_once("Payment/DTA.php"); //PEAR

class Rechnung extends GenRechnung
{

  function Rechnung(&$app)
  {
    $this->app=&$app; 
  
    $this->app->ActionHandlerInit($this);

/*    $this->app->ActionHandler("list","MahnwesenList");
    $this->app->ActionHandler("mahnweseneinstellungen","MahnwesenMahnwesenEinstellungen");
    $this->app->ActionHandler("mahnpdf","MahnwesenMahnPDF");

    $this->app->ActionHandler("stop","MahnwesenStop");
    $this->app->ActionHandler("destop","MahnwesenDestop");
    $this->app->ActionHandler("skonto","MahnwesenSkonto");
    $this->app->ActionHandler("forderungsverlust","MahnwesenForderungsverlust");



    $this->app->ActionHandler("manuellbezahltmarkiert","RechnungManuellBezahltMarkiert");
    $this->app->ActionHandler("manuellbezahltentfernen","MahnwesenManuellBezahltEntfernen");
    $this->app->ActionHandler("zahlungsmahnungswesen","MahnwesenZahlungMahnungswesen");

*/

    $this->app->ActionHandler("list","RechnungList");
    $this->app->ActionHandler("create","RechnungCreate");
    $this->app->ActionHandler("positionen","RechnungPositionen");
    $this->app->ActionHandler("addposition","RechnungAddPosition");
    $this->app->ActionHandler("uprechnungposition","UpRechnungPosition");
    $this->app->ActionHandler("delrechnungposition","DelRechnungPosition");
    $this->app->ActionHandler("downrechnungposition","DownRechnungPosition");
    $this->app->ActionHandler("positioneneditpopup","RechnungPositionenEditPopup");
    $this->app->ActionHandler("search","RechnungSuche");
    $this->app->ActionHandler("edit","RechnungEdit");
    $this->app->ActionHandler("delete","RechnungDelete");
    $this->app->ActionHandler("gutschrift","RechnungGutschrift");
    $this->app->ActionHandler("copy","RechnungCopy");
    $this->app->ActionHandler("freigabe","RechnungFreigabe");
  //  $this->app->ActionHandler("mahnwesen","RechnungMahnwesen");
   // $this->app->ActionHandler("mahnweseneinstellungen","RechnungMahnwesenEinstellungen");
    $this->app->ActionHandler("abschicken","RechnungAbschicken");
    $this->app->ActionHandler("pdf","RechnungPDF");
    $this->app->ActionHandler("inlinepdf","RechnungInlinePDF");
    //$this->app->ActionHandler("mahnpdf","RechnungMahnPDF");
    $this->app->ActionHandler("dta","RechnungDTA");
    //$this->app->ActionHandler("stop","RechnungStop");
    //$this->app->ActionHandler("destop","RechnungDestop");
    //$this->app->ActionHandler("skonto","RechnungSkonto");
    //$this->app->ActionHandler("forderungsverlust","RechnungForderungsverlust");
    $this->app->ActionHandler("lastschrift","RechnungLastschrift");
    $this->app->ActionHandler("protokoll","RechnungProtokoll");
    $this->app->ActionHandler("zahlungseingang","RechnungZahlungseingang");
    $this->app->ActionHandler("minidetail","RechnungMiniDetail");
    $this->app->ActionHandler("editable","RechnungEditable");
    $this->app->ActionHandler("livetabelle","RechnungLiveTabelle");
    $this->app->ActionHandler("schreibschutz","RechnungSchreibschutz");
    $this->app->ActionHandler("manuellbezahltmarkiert","RechnungManuellBezahltMarkiert");
    $this->app->ActionHandler("manuellbezahltentfernen","RechnungManuellBezahltEntfernen");
    $this->app->ActionHandler("zahlungsmahnungswesen","RechnungZahlungMahnungswesen");
    $this->app->ActionHandler("multilevel","RechnungMultilevel");
    $this->app->ActionHandler("deleterabatte","RechnungDeleteRabatte");
    $this->app->ActionHandler("updateverband","RechnungUpdateVerband");
    $this->app->ActionHandler("lastschriftwdh","RechnungLastschriftWdh");
  
    $this->app->DefaultActionHandler("list");
 
    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("adresse");

    if($nummer=="")
      $adresse= $this->app->DB->Select("SELECT a.name FROM rechnung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    else
      $adresse = $nummer;

    $nummer = $this->app->DB->Select("SELECT b.belegnr FROM rechnung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    if($nummer=="" || $nummer==0) $nummer="ohne Nummer";

    $this->app->Tpl->Set(UEBERSCHRIFT,"Rechnung:&nbsp;".$adresse." (".$nummer.")");
    $this->app->Tpl->Set(FARBE,"[FARBE4]");


    $this->app->ActionHandlerListen($app);
  }

  function RechnungUpdateVerband()
  {
    $id=$this->app->Secure->GetGET("id");
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
    $this->app->erp->RabatteLaden($id,"rechnung",$adresse);
    $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Verbandsinformation wurde neu geladen!</div>  ");
    header("Location: index.php?module=rechnung&action=edit&id=$id&msg=$msg");
    exit;
  }


  function RechnungLastschriftWdh()
  {

    $id=$this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen',dta_datei=0 WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Rechnung kann nochmal eingezogen werden!</div>  ");
    header("Location: index.php?module=rechnung&action=edit&id=$id&msg=$msg");
    exit;
  }

  function RechnungDeleteRabatte()
  {

    $id=$this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE rechnung SET rabatt='',rabatt1='',rabatt2='',rabatt3='',rabatt4='',rabatt5='',realrabatt='' WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Rabatte wurden entfernt!</div>  ");
    header("Location: index.php?module=rechnung&action=edit&id=$id&msg=$msg");
    exit;
  }

 	function RechnungMultilevel()
  {
    $this->RechnungMenu();
    $id = $this->app->Secure->GetGET("id");
    $provdatum = $this->app->Secure->GetPOST("provdatum");

    $this->app->Tpl->Set(TABTEXT,"MLM Optionen");

    if($this->app->Secure->GetPOST("mlmsubmit"))
    {
      $punkte = $this->app->Secure->GetPOST("punkte");
      $bonuspunkte = $this->app->Secure->GetPOST("bonuspunkte");

    	$provdatum  = $this->app->String->Convert($provdatum,"%1.%2.%3","%3-%2-%1");

      //$this->app->DB->Update("UPDATE rechnung SET punkte='$punkte',keineprovision='$keineprovision', 
      //  bonuspunkte='$bonuspunkte',keinepunkte='$keinepunkte' WHERE id='$id' LIMIT 1");

 			$this->app->DB->Update("UPDATE rechnung SET provdatum='$provdatum' WHERE id='$id' LIMIT 1");

      $this->app->Tpl->Set(MESSAGE,"<div class=\"error2\">Die MLM Optionen wurden gespeichert!</div>");
    }

    $punkte = $this->app->DB->Select("SELECT punkte FROM rechnung WHERE id='$id' LIMIT 1");
    $bonuspunkte = $this->app->DB->Select("SELECT bonuspunkte FROM rechnung WHERE id='$id' LIMIT 1");
    $provdatum = $this->app->DB->Select("SELECT provdatum FROM rechnung WHERE id='$id' LIMIT 1");

    $this->app->YUI->DatePicker("provdatum");
   	$provdatum  = $this->app->String->Convert($provdatum,"%3-%2-%1","%1.%2.%3");


    $this->app->Tpl->Set(PUNKTE,$punkte);
    $this->app->Tpl->Set(BONUSPUNKTE,$bonuspunkte);
		if($provdatum!='..')
    	$this->app->Tpl->Set(PROVDATUM,$provdatum);

    $this->app->Tpl->Parse(TAB1,"rechnung_multilevel.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }



	function RechnungManuellBezahltEntfernen()
	{

    $id = $this->app->Secure->GetGET("id");

		$this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen', ist='0',mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt entfernt am ".date('d.m.Y')."') WHERE id='$id'");

		header("Location: index.php?module=rechnung&action=edit&id=$id");
		exit;
	}

	function RechnungManuellBezahltMarkiert()
	{

    $id = $this->app->Secure->GetGET("id");

		$this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='bezahlt', ist=soll,mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n','Manuell als bezahlt markiert am ".date('d.m.Y')."') WHERE id='$id'");

		header("Location: index.php?module=rechnung&action=edit&id=$id");
		exit;

	}


	function RechnungSchreibschutz()
	{

    $id = $this->app->Secure->GetGET("id");

		$this->app->DB->Update("UPDATE rechnung SET schreibschutz='0' WHERE id='$id'");
		header("Location: index.php?module=rechnung&action=edit&id=$id");
		exit;

	}


  function RechnungCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->CopyRechnung($id);

    header("Location: index.php?module=rechnung&action=edit&id=$newid");
    exit;
  }



  function RechnungIconMenu($id)
  {
  	$status = $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");
  	$zahlungsstatus = $this->app->DB->Select("SELECT zahlungsstatus FROM rechnung WHERE id='$id' LIMIT 1");

    if($status=="angelegt" || $status=="")
      $freigabe = "<option value=\"freigabe\">Rechnung freigeben</option>";

		if($this->app->erp->RechteVorhanden("rechnung","manuellbezahltmarkiert") && $zahlungsstatus=="offen")
      $bezahlt = "<option value=\"manuellbezahltmarkiert\">manuell als bezahlt markieren</option>";

		if($this->app->erp->RechteVorhanden("rechnung","manuellbezahltentfernen") && $zahlungsstatus=="bezahlt")
      $bezahlt = "<option value=\"manuellbezahltentfernen\">manuell bezahlt entfernen</option>";


    $menu ="
  <script type=\"text/javascript\">
  function onchangerechnung(cmd)
  {
    switch(cmd)
    {
			case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=delete&id=%value%'; break;
			case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=copy&id=%value%'; break;
			case 'gutschrift': if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%'; break;
			case 'pdf': window.location.href='index.php?module=rechnung&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
      case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
			case 'manuellbezahltmarkiert': window.location.href='index.php?module=rechnung&action=manuellbezahltmarkiert&id=%value%'; break;
			case 'manuellbezahltentfernen': window.location.href='index.php?module=rechnung&action=manuellbezahltentfernen&id=%value%'; break;
			case 'freigabe': window.location.href='index.php?module=rechnung&action=freigabe&id=%value%'; break;
    }
    
  }
    </script>


&nbsp;Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangerechnung(this.value)\"> 
<option>bitte w&auml;hlen ...</option>
<option value=\"storno\">Rechnung stornieren</option>
<option value=\"copy\">Rechnung kopieren</option>
$freigabe
<option value=\"abschicken\">Rechnung abschicken</option>
<option value=\"gutschrift\">als Gutschrift/Stornore. weiterf&uuml;hren</option>
<option value=\"pdf\">PDF &ouml;ffnen</option>
$bezahlt

</select>&nbsp;

        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\" title=\"PDF\"></a>
      <!--  <a href=\"index.php?module=rechnung&action=edit&id=%value%\" title=\"Bearbeiten\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=rechnung&action=delete&id=%value%';\" title=\"Stornieren\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\" title=\"Kopieren\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
     <a onclick=\"if(!confirm('Wirklich als Gutschrift weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\" title=\"als Gutschrift weiterf&uuml;hren\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift\"></a>-->";

      //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }


  function RechnungLiveTabelle()
  {
    $id = $this->app->Secure->GetGET("id");
    $status = $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
    $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M,ap.preis as P
      FROM rechnung_position ap, artikel a WHERE ap.rechnung='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","A","noAction");
    } else {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M,ap.preis as P
      FROM rechnung_position ap, artikel a WHERE ap.rechnung='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","Menge","noAction");
    }
    echo $artikel;
    exit;
  }



  function RechnungEditable()
  {
    $this->app->YUI->AARLGEditable();
  }



  function RechnungMiniDetail($parsetarget="",$menu=true)
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->erp->BerechneDeckungsbeitrag($id,"rechnung");

    $auftragArr = $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$auftragArr[0]['projekt']}' LIMIT 1");
    $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");

    $this->app->Tpl->Set(DECKUNGSBEITRAG,$auftragArr[0]['erloes_netto']);
    $this->app->Tpl->Set(DBPROZENT,$auftragArr[0]['deckungsbeitrag']);

    $this->app->Tpl->Set(KUNDE,$kundennummer." ".$kundenname);
    $this->app->Tpl->Set(PROJEKT,$projekt);
    $this->app->Tpl->Set(ZAHLWEISE,$auftragArr[0]['zahlungsweise']);
    $this->app->Tpl->Set(STATUS,$auftragArr[0]['status']);


    if($auftragArr[0]['mahnwesen']=="")$auftragArr[0]['mahnwesen']="-";
    $this->app->Tpl->Set(MAHNWESEN,$auftragArr[0]['mahnwesen']);
    if($auftragArr[0]['mahnwesen_datum']=="0000-00-00")$auftragArr[0]['mahnwesen_datum']="-";
    $this->app->Tpl->Set(MAHNWESENDATUM,$auftragArr[0]['mahnwesen_datum']);


    if($auftragArr[0]['auftrag']==0) $auftragArr[0]['auftrag']="kein Auftrag";
    $this->app->Tpl->Set(AUFTRAG,"<a href=\"index.php?module=auftrag&action=edit&id=".$auftragArr[0]['auftragid']."\" target=\"_blank\">".$auftragArr[0]['auftrag']."</a>");

    $gutschrift = $this->app->DB->Select("SELECT CONCAT(belegnr,'&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=',id,'\">
	<img src=\"./themes/new/images/pdf.png\" title=\"Gutschrift PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=gutschrift&action=edit&id=',id,'\"><img src=\"./themes/new/images/edit.png\" title=\"Gutschrift bearbeiten\" border=\"0\"></a>') 
      FROM gutschrift WHERE rechnungid='$id' LIMIT 1");

    if($gutschrift=="") $gutschrift = "-";
    $this->app->Tpl->Set(GUTSCHRIFT,$gutschrift);


    $lieferschein = $this->app->DB->Select("SELECT CONCAT(belegnr,'&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=',id,'\">
	<img src=\"./themes/new/images/pdf.png\" title=\"Lieferschein PDF\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=lieferschein&action=edit&id=',id,'\"><img src=\"./themes/new/images/edit.png\" title=\"Lieferschein bearbeiten\" border=\"0\"></a>') 
      FROM lieferschein WHERE id='{$auftragArr[0]['lieferschein']}' LIMIT 1");
    if($lieferschein=="") $lieferschein = "-";
    $this->app->Tpl->Set(LIEFERSCHEIN,$lieferschein);


    if($auftragArr[0]['ust_befreit']==0)
      $this->app->Tpl->Set(STEUER,"Deutschland");
    else if($auftragArr[0]['ust_befreit']==1)
      $this->app->Tpl->Set(STEUER,"EU-Lieferung");
    else
      $this->app->Tpl->Set(STEUER,"Export");
 

    if($menu)
    {
      $menu = $this->RechnungIconMenu($id);
      $this->app->Tpl->Set(MENU,$menu);
    }
 // ARTIKEL

    $status = $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
      $table->Query("SELECT ap.bezeichnung as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, ap.menge as M,ap.preis as P
	FROM rechnung_position ap, artikel a WHERE ap.rechnung='$id' AND a.id=ap.artikel");
      $artikel = $table->DisplayNew("return","A","noAction");

        $this->app->Tpl->Add(JAVASCRIPT,"
            var auto_refresh = setInterval(
        function ()
        {
        $('#artikeltabellelive$id').load('index.php?module=rechnung&action=livetabelle&id=$id').fadeIn('slow');
        }, 3000); // refresh every 10000 milliseconds
        ");
    } else {
      $table->Query("SELECT ap.bezeichnung as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\"  target=\"_blank\">', ap.nummer,'</a>') as Nummer, ap.menge as M
      FROM rechnung_position ap, artikel a WHERE ap.rechnung='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","Menge","noAction");
    }

    $this->app->Tpl->Set(ARTIKEL,'<div id="artikeltabellelive'.$id.'">'.$artikel.'</div>');

		if($auftragArr[0]['belegnr'] <=0) $auftragArr[0]['belegnr'] = "ENTWURF";
    $this->app->Tpl->Set(BELEGNR,"<a href=\"index.php?module=rechnung&action=edit&id=".$auftragArr[0]['id']."\">".$auftragArr[0]['belegnr']."</a>");
    $this->app->Tpl->Set(RECHNUNGID,$auftragArr[0]['id']);


    if($auftragArr[0]['status']=="freigegeben")
    {
      $this->app->Tpl->Set(ANGEBOTFARBE,"orange");
      $this->app->Tpl->Set(ANGEBOTTEXT,"Das Angebot wurde noch nicht als Auftrag weitergef&uuml;hrt!");
    }
    else if($auftragArr[0]['status']=="versendet")
    {
      $this->app->Tpl->Set(ANGEBOTFARBE,"red");
      $this->app->Tpl->Set(ANGEBOTTEXT,"Das Angebot versendet aber noch kein Auftrag vom Kunden erhalten!");
    }
    else if($auftragArr[0]['status']=="beauftragt")
    {
      $this->app->Tpl->Set(ANGEBOTFARBE,"green");
      $this->app->Tpl->Set(ANGEBOTTEXT,"Das Angebot wurde beauftragt und abgeschlossen!");
    }
    else if($auftragArr[0]['status']=="angelegt")
    {
      $this->app->Tpl->Set(ANGEBOTFARBE,"grey");
      $this->app->Tpl->Set(ANGEBOTTEXT,"Das Angebot wird bearbeitet und wurde noch nicht freigegeben und abgesendet!");
    }



    $this->app->Tpl->Set(ZAHLUNGEN,$this->RechnungZahlung(true));
    if($gutschrift!="-")
    $this->app->Tpl->Add(ZAHLUNGEN,"<div class=\"info\">Zu dieser Rechnung existiert eine Gutschrift!</div>");
    else {

    if($auftragArr[0]['zahlungsstatus']!="bezahlt")
    $this->app->Tpl->Add(ZAHLUNGEN,"<div class=\"error\">Diese Rechnung ist noch nicht komplett bezahlt!</div>");
    else
    $this->app->Tpl->Add(ZAHLUNGEN,"<div class=\"success\">Diese Rechnung ist bezahlt.</div>");
    }

    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM rechnung_protokoll WHERE rechnung='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(PROTOKOLL,"Protokoll","noAction");




    if($parsetarget=="")
    {
      $this->app->Tpl->Output("rechnung_minidetail.tpl");
      exit;
    }  else {
      $this->app->Tpl->Parse($parsetarget,"rechnung_minidetail.tpl");
    }
  }


  function RechnungZahlung($return=false)
  {
    $id = $this->app->Secure->GetGET("id");

    $rechnungArr = $this->app->DB->SelectArr("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, belegnr, soll FROM rechnung WHERE id='$id' LIMIT 1");
    $auftragid = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id='$id' LIMIT 1");
    $eingang ="<tr><td colspan=\"3\"><b>Zahlungen</b></td></tr>";


    $eingang .="<tr><td class=auftrag_cell>".$rechnungArr[0][datum]."</td><td class=auftrag_cell>RE ".$rechnungArr[0][belegnr]."</td><td class=auftrag_cell align=right>".$this->app->erp->EUR($rechnungArr[0][soll])." EUR</td></tr>";



    if($auftragid > 0)
    {
    $eingangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, k.id as kontoauszuege, ke.betrag as betrag,k.id as zeile FROM kontoauszuege_zahlungseingang ke 
      LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='rechnung' AND ke.parameter='$id') OR (ke.objekt='auftrag' AND ke.parameter='$auftragid')");
    } else {

    $eingangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, k.id as kontoauszuege, ke.betrag as betrag,k.id as zeile FROM kontoauszuege_zahlungseingang ke 
      LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='rechnung' AND ke.parameter='$id')");
    }

    for($i=0;$i<count($eingangArr);$i++)
      $eingang .="<tr><td class=auftrag_cell>".$eingangArr[$i][datum]."</td><td class=auftrag_cell>".$eingangArr[$i][konto]."&nbsp;(<a href=\"index.php?module=zahlungseingang&action=editzeile&id=".$eingangArr[$i][zeile]."\">zur Buchung</a>)</td><td class=auftrag_cell align=right>".$this->app->erp->EUR($eingangArr[$i][betrag])." EUR</td></tr>";

    // gutschriften zu dieser rechnung anzeigen

    $gutschriften = $this->app->DB->SelectArr("SELECT belegnr, DATE_FORMAT(datum,'%d.%m.%Y') as datum,soll FROM gutschrift WHERE rechnungid='$id'");

    for($i=0;$i<count($gutschriften);$i++)
      $eingang .="<tr><td class=auftrag_cell>".$gutschriften[$i][datum]."</td><td class=auftrag_cell>GS ".$gutschriften[$i][belegnr]."</td><td class=auftrag_cell align=right>".$this->app->erp->EUR($gutschriften[$i][soll])." EUR</td></tr>";

 


    if($auftragid > 0)
    {
    $ausgangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, ke.betrag as betrag,k.id as zeile FROM kontoauszuege_zahlungsausgang ke 
      LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='rechnung' AND ke.parameter='$id') OR (ke.objekt='auftrag' AND ke.parameter='$auftragid')");
    } else {
    $ausgangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, ke.betrag as betrag,k.id as zeile FROM kontoauszuege_zahlungsausgang ke 
      LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='rechnung' AND ke.parameter='$id') ");
    }

    for($i=0;$i<count($ausgangArr);$i++)
      $ausgang .="<tr><td class=auftrag_cell>".$ausgangArr[$i][datum]."</td><td class=auftrag_cell>".$ausgangArr[$i][konto]."&nbsp;(<a href=\"index.php?module=zahlungseingang&action=editzeile&id=".$ausgangArr[$i][zeile]."\">zur Buchung</a>)</td><td class=auftrag_cell align=right>-".$this->app->erp->EUR($ausgangArr[$i][betrag])." EUR</td></tr>";

    $saldo = $this->app->erp->EUR($this->app->erp->RechnungSaldo($id));
    if(str_replace(",",".",$saldo) < 0) $saldo = "<b style=\"color:red\">$saldo</b>";

    $ausgang .="<tr><td class=auftrag_cell></td><td class=auftrag_cell align=right>Saldo</td><td class=auftrag_cell align=right>$saldo EUR</td></tr>";

    if($return)return "<table width=100% border=0 class=auftrag_cell cellpadding=0 cellspacing=0>".$eingang." ".$ausgang."</table>";

  }


	function RechnungForderungsverlust()
	{
    $id = $this->app->Secure->GetGET("id");

 		$ist = $this->app->DB->Select("SELECT ist FROM rechnung WHERE id='$id'");
    $jahr = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y') FROM rechnung WHERE id='$id'");
    $mahnwesen_datum = $this->app->DB->Select("SELECT mahnwesen_datum FROM rechnung WHERE id='$id'");
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id'");
    $soll = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id'");
    $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$id'");
    $ustid = $this->app->DB->Select("SELECT ustid FROM rechnung WHERE id='$id'");
    $land = $this->app->DB->Select("SELECT land FROM rechnung WHERE id='$id'");
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id'");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse'");
    $liefersperregrund = $this->app->DB->Select("SELECT liefersperregrund FROM adresse WHERE id='$adresse'");
    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse'");

    $forderungverlust_datum = $this->app->DB->Select("SELECT if(forderungsverlust_datum IS NULL,'0000-00-00',forderungsverlust_datum) FROM rechnung WHERE id='$id'");

    $skonto = $soll -$ist;

		$forderungverlust = $soll - $ist;

		// skonto loeschen
		$datum = date('d.m.Y'); // RICHTIG
		// NOW() ins STATEMENT RICHTIG!!!

		//NOW $mahnwesen_datum 
//		$datum = $mahnwesen_datum;

		if($forderungverlust_datum=="0000-00-00")
		{
      if($ust_befreit==1 && $ustid!="")
            $gegenkonto=6930;
      else if(($ust_befreit==1 && $ustid=="") || $ust_befreit==2)
            $gegenkonto=6930; //Drittland
      else if($ust_befreit==0 && $land=="DE")
            $gegenkonto=6936; //DE
      else
            $gegenkonto=906930;  //privat EU

   		//if($jahr=="2010") $jahr="'2010-12-31'"; else $jahr="NOW()";

     	$this->app->DB->Insert("INSERT INTO datev_buchungen (id,umsatz,haben,gegenkonto,datum,konto,buchungstext,belegfeld1,firma)
     	 VALUES ('','$forderungverlust','0','$gegenkonto',NOW(),'$kundennummer','Forderungsverl. KD $kundennummer RE $belegnr $name','$belegnr','".$this->app->User->GetFirma()."')"); 


			$this->app->erp->RechnungProtokoll($id,"Forderungsverlust Buchung");
   		$this->app->DB->Update("UPDATE rechnung SET skonto_gegeben='0', 
			zahlungsstatus='forderungsverlust',mahnwesen_internebemerkung=CONCAT(mahnwesen_internebemerkung,'\r\n".$this->app->User->GetName()." als Forderungsverlust gebucht am: $datum'), mahnwesen='forderungsverlust',
			forderungsverlust_datum=NOW(),forderungsverlust_betrag='$forderungverlust'
			WHERE id='$id' LIMIT 1");

			
			$this->app->DB->Update("UPDATE adresse SET liefersperre='1',liefersperregrund='Forderungsverlust gebucht am $datum\r\n$liefersperregrund' 
					WHERE id='$adresse' LIMIT 1");

		}
		
    $this->RechnungMahnwesen();

	}


  function RechnungSkonto()
  {
    $id = $this->app->Secure->GetGET("id");


    $ist = $this->app->DB->Select("SELECT ist FROM rechnung WHERE id='$id'");
    $jahr = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y') FROM rechnung WHERE id='$id'");
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id'");
    $soll = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id'");
    $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$id'");
    $ustid = $this->app->DB->Select("SELECT ustid FROM rechnung WHERE id='$id'");
    $land = $this->app->DB->Select("SELECT land FROM rechnung WHERE id='$id'");
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id'");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse'");
    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse'");

    $skonto = $soll -$ist;

    //echo "skonto $id $skonto";


   $this->app->erp->RechnungProtokoll($id,"Skonto f&uuml;r Differenzbetrag von $skonto gegeben.");
   $this->app->DB->Update("UPDATE rechnung SET skonto_gegeben='$skonto', zahlungsstatus='bezahlt',mahnwesen_internebemerkung='".$this->app->User->GetName()." Skonto gegeben: $skonto und auf bezahlt gestellt' WHERE id='$id' LIMIT 1");

    //gegenkonto 4400 konto kundennummer haben 0
 
          if($ust_befreit==1 && $ustid!="")
            $gegenkonto=4125;
          else if(($ust_befreit==1 && $ustid=="") || $ust_befreit==2)
            $gegenkonto=4120; //Drittland
          else if($ust_befreit==0 && $land=="DE")
            $gegenkonto=4400; //DE
          else
            $gegenkonto=4315;  //privat EU


   // if($jahr=="2010") $jahr="'2010-12-31'"; else $jahr="NOW()";

     // $this->app->DB->Insert("INSERT INTO datev_buchungen (id,umsatz,haben,gegenkonto,datum,konto,buchungstext,belegfeld1,firma)
     //   VALUES ('','$skonto','0','$gegenkonto',$jahr,'$kundennummer','Skonto KD $kundennummer RE $belegnr','$belegnr','".$this->app->User->GetFirma()."')"); 


    
    $this->RechnungMahnwesen();
  }

  function RechnungDestop()
  {
    $id = $this->app->Secure->GetGET("id");

    //mahnwesen_gesperrt=0
     $this->app->erp->RechnungProtokoll($id,"Rechnung aus Mahnwesensperre genommen");
    $versendet = $this->app->DB->Select("SELECT versendet FROM rechnung WHERE WHERE id='$id' LIMIT 1");
    if($versendet)
    $this->app->DB->Update("UPDATE rechnung SET mahnwesen_gesperrt='0',status='versendet' WHERE id='$id' LIMIT 1");
    else
    $this->app->DB->Update("UPDATE rechnung SET mahnwesen_gesperrt='0',status='freigegeben' WHERE id='$id' LIMIT 1");
   
    
    $this->RechnungMahnwesen();
  }

  function RechnungStop()
  {
    $id = $this->app->Secure->GetGET("id");
   //   echo "stop $id";
    //mahnwesen_gesperrt=1
   $this->app->erp->RechnungProtokoll($id,"Rechnung im Mahnwesen gesperrt.");
   $this->app->DB->Update("UPDATE rechnung SET mahnwesen_gesperrt='1',status='gesperrt' WHERE id='$id' LIMIT 1");
    
    $this->RechnungMahnwesen();
  }

  function RechnungDTA()
  {
    $id = $this->app->Secure->GetGET("id");
    $inhalt = $this->app->DB->Select("SELECT inhalt FROM dta_datei WHERE id='$id' LIMIT 1");
    $datum= $this->app->DB->Select("SELECT datum FROM dta_datei WHERE id='$id' LIMIT 1");

    $this->app->DB->Update("UPDATE dta_datei SET status='verarbeitet' WHERE id='$id' LIMIT 1");



    header("Content-Disposition: attachment; filename=\"".$datum."_Lastschrift.txt\"");
    header("Content-type: text/plain");
    header("Cache-control: public");
    echo $inhalt;
    exit;

  }


  function RechnungLastschrift()
  {
    $this->app->Tpl->Set(UEBERSCHRIFT,"Lastschrift&nbsp;/&nbsp;Sammel&uuml;berweisung");
    $erzeugen = $this->app->Secure->GetPOST("erzeugen"); 
    $lastschrift= $this->app->Secure->GetPOST("lastschrift"); 
    $kontointern=$this->app->Secure->GetPOST("konto");

		$this->app->Tpl->Set(KURZUEBERSCHRIFT,"Lastschriften");
   	$this->app->erp->MenuEintrag("index.php?module=rechnung&action=list","Zur Rechnungs&uuml;bersicht");

    if($erzeugen!="")
    {
      //erzeugen
      $rechnung= $this->app->Secure->GetPOST("rechnung"); 

      for($i=0;$i<count($rechnung);$i++)
      {
	//echo $rechnung[$i]."<br>";
	// dta erzeugen 
	$adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='{$rechnung[$i]}' limit 1");
	$auftrag = $this->app->DB->Select("SELECT auftrag FROM rechnung WHERE id='{$rechnung[$i]}' limit 1");
	$name = $this->app->DB->Select("SELECT bank_inhaber FROM auftrag WHERE belegnr='$auftrag' LIMIT 1");
	$konto= $this->app->DB->Select("SELECT bank_konto FROM auftrag WHERE belegnr='$auftrag' LIMIT 1");
	$blz = $this->app->DB->Select("SELECT bank_blz FROM auftrag WHERE belegnr='$auftrag' LIMIT 1");
	$betrag = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='{$rechnung[$i]}' LIMIT 1");
	$vz1= "Rechnung ".$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='{$rechnung[$i]}' limit 1");
	$lastschrift=1;

	$this->app->DB->Insert("INSERT INTO dta (id,adresse,datum,name,konto,blz,betrag,vz1,lastschrift,kontointern,firma) VALUES ('','$adresse',NOW(),'$name','$konto','$blz','$betrag','$vz1','$lastschrift','$kontointern','".$this->app->User->GetFirma()."')");

	//rechnung auf bezahlt markieren + soll auf ist
	$this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='abgebucht' WHERE id='{$rechnung[$i]}' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }
    }


    
    // offene Rechnungen
    $this->app->Tpl->Set(SUB1TABTEXT,"Offene Rechnungen");
    $table = new EasyTable($this->app);
$table->Query("SELECT CONCAT('<input type=checkbox name=rechnung[] value=\"',r.id,'\" checked>') as auswahl, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, if(r.belegnr,r.belegnr,'ohne Nummer') as beleg, r.name, p.abkuerzung as projekt, r.soll as betrag, r.ist as ist, r.zahlungsweise, a.bank_inhaber, a.bank_institut, a.bank_blz, a.bank_konto, r.id
      FROM rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN auftrag a ON a.id=r.auftragid WHERE (r.zahlungsstatus!='bezahlt' AND r.zahlungsstatus!='abgebucht') AND (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') AND (r.belegnr!=0) order by r.datum DESC, r.id DESC");
    $table->DisplayNew(SUB1TAB,"
	<!--<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>-->
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        ");


  $summe = $this->app->DB->Select("SELECT SUM(r.soll)
      FROM rechnung r, projekt p WHERE (r.zahlungsstatus!='bezahlt' AND r.zahlungsstatus!='abgebucht')  AND (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzug') AND r.belegnr!=0 AND p.id=r.projekt");

		if($summe <=0) $summe = "0,00";
    $this->app->Tpl->Set(TAB1,"<center>Gesamt offen: $summe EUR</center>");


    $this->app->YUI->TableSearch(TAB1,"lastschriften");
  	$this->app->Tpl->Add(TAB1,"<br><center>
        <input type=\"submit\" name=\"submit\" value=\"Lastschriften an Zahlungstransfer &uuml;bergeben\"></center></form>");

    $this->app->YUI->TableSearch(TAB2,"lastschriftenarchiv");

    $this->app->Tpl->Parse(PAGE,"rechnung_lastschrift.tpl");


  }


  function RechnungGutschrift()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->WeiterfuehrenRechnungZuGutschrift($id);

    // pruefe obes schon eine gutschrift fuer diese rechnung gibt
    $anzahlgutschriften = $this->app->DB->Select("SELECT COUNT(id) FROM gutschrift WHERE rechnungid='$id' 
				AND rechnungid!=0 AND rechnungid!=''");

    if($anzahlgutschriften>1){
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Achtung es gibt bereits eine oder mehrer Gutschriften f&uuml;r diese Rechnung!</div>");
		}

    header("Location: index.php?module=gutschrift&action=edit&id=$newid&msg=$msg");
    exit;
  }


  function RechnungFreigabe()
  {
    $id = $this->app->Secure->GetGET("id");
    $freigabe= $this->app->Secure->GetGET("freigabe");
    $this->app->Tpl->Set(TABTEXT,"Freigabe");
    $this->app->erp->RechnungNeuberechnen($id);

    $this->app->erp->CheckVertrieb($id,"rechnung");
    $this->app->erp->CheckBearbeiter($id,"rechnung");

    if($freigabe==$id)
    {
      //$belegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM rechnung WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegnr <= 0) $belegnr = 400000; else $belegnr = $belegnr + 1;
      $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
		
			if($belegnr=="")
			{	
				$belegnr = $this->app->erp->GetNextNummer("rechnung",$projekt);
      	$this->app->DB->Update("UPDATE rechnung SET belegnr='$belegnr', status='freigegeben' WHERE id='$id' LIMIT 1");
      	$this->app->erp->RechnungProtokoll($id,"Rechnung freigegeben");
   			$msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Rechnung wurde freigegeben und kann jetzt versendet werden!</div>");
      	header("Location: index.php?module=rechnung&action=edit&id=$id&msg=$msg");
      	exit;
			} else {
				$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Rechnung wurde bereits freigegeben!</div>");
      	header("Location: index.php?module=rechnung&action=edit&id=$id&msg=$msg");
      	exit;
			}

    } else { 

      $name = $this->app->DB->Select("SELECT a.name FROM rechnung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
			$summe = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM rechnung_position
	WHERE rechnung='$id' LIMIT 1");

      $this->app->Tpl->Set(TAB1,"<div class=\"info\">Soll die Rechnung an <b>$name</b> im Wert von <b>$summe $waehrung</b> 
	jetzt freigegeben werden? <input type=\"button\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=rechnung&action=freigabe&id=$id&freigabe=$id'\">
	</div>");
    }
    $this->RechnungMenu();
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }



  function RechnungAbschicken()
  {
    $this->RechnungMenu();
		$this->app->erp->DokumentAbschicken();
  }




  function RechnungDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM rechnung WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");

    if($belegnr==0 || $belegnr=="")
    {

      $this->app->erp->DeleteRechnung($id);
      if($belegnr<=0) $belegnr="ENTWURF";
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Rechnung \"$belegnr\" von \"$name\" wurde storniert!</div>");
      //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
      header("Location: index.php?module=rechnung&action=list&msg=$msg");
      exit;
    } else
    {
    if(0)//$status=="versendet")
      {
      // KUNDE muss RMA starten                                                                                                                             
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rechnung \"$belegnr\" von \"$name\" kann nicht storniert werden sie bereits versendet ist. <br>Um die Rechnung zu stornieren muss eine Gutschrift angelegt werden.</div>");
      }
      else
      {
        $maxbelegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM rechnung");
        if(0)//$maxbelegnr == $belegnr)
        {
          $this->app->DB->Delete("DELETE FROM rechnung_position WHERE rechnung='$id'");
          $this->app->DB->Delete("DELETE FROM rechnung_protokoll WHERE rechnung='$id'");
          $this->app->DB->Delete("DELETE FROM rechnung WHERE id='$id'");
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rechnung \"$belegnr\" von \"$name\" wurde storniert!</div>");
        } else
        {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rechnung \"$belegnr\" von \"$name\" kann nicht storniert werden das sie bereits versendet wurde, es neuere Nummern im Rechnungskreis gibt oder sie schon storniert wurde! Um die Rechnung zu stornieren muss eine Gutschrift angelegt werden (weiterf&uuml;hren als Gutschrift / Stornorechnung).</div>");
        }
        header("Location: index.php?module=rechnung&action=list&msg=$msg");
        exit;
      }

      //$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rechnung \"$name\" ($belegnr) kann nicht storniert werden, da es bereits versendet wurde!</div>");
      header("Location: index.php?module=rechnung&action=list&msg=$msg#tabs-1");
      exit;
    }

  }

  function RechnungProtokoll()
  {
    $this->RechnungMenu();
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set(TABTEXT,"Protokoll");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM rechnung_protokoll WHERE rechnung='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(TAB1,"Protokoll","noAction");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function RechnungAddPosition()
  {
    $sid = $this->app->Secure->GetGET("sid");
    $id = $this->app->Secure->GetGET("id");
    $menge = $this->app->Secure->GetGET("menge");
    $datum  = $this->app->Secure->GetGET("datum");
    $datum  = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
    $this->app->erp->AddRechnungPosition($id, $sid,$menge,$datum);
    $this->app->erp->RechnungNeuberechnen($id);
    header("Location: index.php?module=rechnung&action=positionen&id=$id");
    exit;
 
  }

  function RechnungMahnPDF()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
    $mahnwesen = $this->app->DB->Select("SELECT mahnwesen FROM rechnung WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");


    if(is_numeric($belegnr) && $belegnr!=0)
    {
      $Brief = new RechnungPDF($this->app,$projekt);
      $Brief->GetRechnung($id,$mahnwesen);
      $Brief->displayDocument(); 
    } //else
      //$this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Rechnungen k&ouml;nnen nicht als PDF betrachtet werden.!</div>");

    //$this->RechnungList();
    exit;
 }

  function RechnungInlinePDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->RechnungNeuberechnen($id);

    $frame = $this->app->Secure->GetGET("frame");
    $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");

		if($frame=="")
		{
      $Brief = new RechnungPDF($this->app,$projekt);
      $Brief->GetRechnung($id);
      $Brief->inlineDocument(); 
		} else {
			$file = urlencode("../../../../index.php?module=rechnung&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"600\" src=\"./js/production/generic/web/viewer.html?file=$file\" frameborder=\"0\"></iframe>";
			exit;
		}
 }

  function RechnungPDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->RechnungNeuberechnen($id);
    $doppel = $this->app->Secure->GetGET("doppel");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");

//    if(is_numeric($belegnr) && $belegnr!=0)
  //  {
      $Brief = new RechnungPDF($this->app,$projekt);
      if($doppel=="1")
      $Brief->GetRechnung($id,"doppel");
      else
      $Brief->GetRechnung($id);
      $Brief->displayDocument(); 
 //   } else
   //   $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Rechnungen k&ouml;nnen nicht als PDF betrachtet werden.!</div>");


    $this->RechnungList();
 }

  function RechnungSuche()
  {
    $this->app->Tpl->Set(UEBERSCHRIFT,"Rechnungen");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Rechnungen");

    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=create","Neue Rechnung anlegen");
    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=search\">Rechnung suchen</a></li>");
 

    $this->app->Tpl->Set(TABTEXT,"Rechnungen");

    $name = $this->app->Secure->GetPOST("name");
    $plz = $this->app->Secure->GetPOST("plz");
    $auftrag = $this->app->Secure->GetPOST("auftrag");
    $kundennummer = $this->app->Secure->GetPOST("kundennummer");

    if($name!="" || $plz!="" || $proforma!="" || $kundennummer!="" || $auftrag!="")
    {
      $table = new EasyTable($this->app);
      $this->app->Tpl->Add(ERGEBNISSE,"<h2>Trefferliste:</h2><br>");
      if($name!="")
        $table->Query("SELECT a.name, a.belegnr as rechung, adr.kundennummer, a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
          LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.name LIKE '%$name%')");
      else if($plz!="")
        $table->Query("SELECT a.name, a.belegnr as rechnung, adr.kundennummer, a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
          LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.plz LIKE '$plz%')");
      else if($kundennummer!="")
        $table->Query("SELECT a.name, a.belegnr as rechnung, adr.kundennummer, a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
          LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (adr.kundennummer='$kundennummer')");
      else if($auftrag!="")
        $table->Query("SELECT a.name, a.belegnr as rechnung , adr.kundennummer,a.plz, a.ort, a.strasse, a.status, a.id FROM rechnung a 
          LEFT JOIN adresse adr ON adr.id = a.adresse WHERE (a.belegnr='$auftrag')");

 //     $table->DisplayNew(ERGEBNISSE,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\">Lesen</a>");
    $table->DisplayNew(ERGEBNISSE,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>


        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");

    } else {
      $this->app->Tpl->Add(ERGEBNISSE,"<div class=\"info\">Rechnungssuche (bitte entsprechende Suchparameter eingeben)</div>");
    }

      $this->app->Tpl->Parse(INHALT,"rechnungssuche.tpl");

    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    $this->app->Tpl->Parse(TAB1,"rahmen77.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function RechnungMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM rechnung WHERE id='$id' LIMIT 1");

    if($belegnr<=0) $belegnr ="(Entwurf)";

//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Rechnung $belegnr");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,"$name Rechnung $belegnr");

    $this->app->erp->RechnungNeuberechnen($id);

    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE4]\">Rechnung</h2></li>");
		//this->app->erp->MenuEintrag("index.php?module=rechnung&action=edit&id=$id","Rechnungsdaten");

		//$this->app->Tpl->Add(FURTHERTABS,'<li><a href="index.php?module=rechnung&action=zahlungsmahnungswesen&id=[ID]&frame=true#tabs-4">Zahlung-/ Mahnwesen</a></li>');
		//this->app->Tpl->Add(FURTHERTABSDIV,'<div id="tabs-4"></div>');

 		//if($this->app->Secure->GetGET("action")!="abschicken")
		//this->app->erp->MenuEintrag("index.php?module=rechnung&action=zahlungsmahnungswesen&id=$id","Zahlung-/ Mahnwesen");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=positionen&id=$id\">Positionen</a></li>");

    // status bestell
    $status = $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");

		if ($status=="angelegt")
    {
      $this->app->erp->MenuEintrag("index.php?module=rechnung&action=freigabe&id=$id","Freigabe");
    }
    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=edit&id=$id","Details");
   
    if($status=='bestellt')
    { 
     // $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=wareneingang&id=$id\">Wareneingang<br>R&uuml;ckst&auml;nde</a></li>");
     // $this->app->Tpl->Add(TABS,"<li><a class=\"tab\" href=\"index.php?module=rechnung&action=wareneingang&id=$id\">Mahnstufen</a></li>");
    } 


//    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=abschicken&id=$id","Abschicken / Protokoll");
//    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=protokoll&id=$id","Protokoll");

    if($this->app->erp->RechteVorhanden("multilevel","list") && $this->app->Secure->GetGET("action")!="abschicken" && $this->app->erp->Firmendaten("modul_mlm")=="1")
      $this->app->erp->MenuEintrag("index.php?module=rechnung&action=multilevel&id=$id","MLM");


//		if($this->app->Secure->GetGET("action")=="abschicken" || $this->app->Secure->GetGET("action")=="multilevel" || $this->app->Secure->GetGET("action")=="zahlungsmahnungswesen")
//    	$this->app->erp->MenuEintrag("index.php?module=rechnung&action=edit&id=$id","Rechnung");

   	$this->app->erp->MenuEintrag("index.php?module=rechnung&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function RechnungPositionen()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->RechnungNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);
    return;


    $this->RechnungMenu();


    /* neu anlegen formular */
    $artikelart = $this->app->Secure->GetPOST("artikelart");
    $bestellnummer = $this->app->Secure->GetPOST("bestellnummer");
    $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
    $vpe = $this->app->Secure->GetPOST("vpe");
    $umsatzsteuerklasse = $this->app->Secure->GetPOST("umsatzsteuerklasse");
    $waehrung = $this->app->Secure->GetPOST("waehrung");
    $projekt= $this->app->Secure->GetPOST("projekt");
    $preis = $this->app->Secure->GetPOST("preis");
    $preis = str_replace(',','.',$preis);
    $menge = $this->app->Secure->GetPOST("menge");
    $lieferdatum = $this->app->Secure->GetPOST("lieferdatum");

    if($lieferdatum=="") $lieferdatum="00.00.0000";


    $rechnungsart = $this->app->DB->Select("SELECT rechnungsart FROM rechnung WHERE id='$id' LIMIT 1");
    $lieferant  = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");

    $anlegen_artikelneu = $this->app->Secure->GetPOST("anlegen_artikelneu");

    if($anlegen_artikelneu!="")
    {

      if($bezeichnung!="" && $menge!="" && $preis!="")
      {
	$sort = $this->app->DB->Select("SELECT MAX(sort) FROM rechnung_position WHERE rechnung='$id' LIMIT 1");
	$sort = $sort + 1;

  $neue_nummer = $this->app->erp->NeueArtikelNummer($artikelart,$this->app->User->GetFirma(),$projekt);

	// anlegen als artikel
	$this->app->DB->InserT("INSERT INTO artikel (id,typ,nummer,projekt,name_de,umsatzsteuer,adresse,firma) 	
	 VALUES ('','$artikelart','$neue_nummer','$projekt','$bezeichnung','$umsatzsteuerklasse','$lieferant','".$this->app->User->GetFirma()."')"); 	
	
	$artikel_id = $this->app->DB->GetInsertID();
	// einkaufspreis anlegen
	$this->app->DB->Insert("INSERT INTO verkaufspreise (id,artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter)
          VALUES ('','$artikel_id','$lieferant','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."')");

	$lieferdatum = $this->app->String->Convert($lieferdatum,"%1.%2.%3","%3-%2-%1");

	$this->app->DB->Insert("INSERT INTO rechnung_position (id,rechnung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
	  VALUES ('','$id','$artikel_id','$bezeichnung','$bestellnummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");

	header("Location: index.php?module=rechnung&action=positionen&id=$id");
	exit;
      } else
	$this->app->Tpl->Set(NEUMESSAGE,"<div class=\"error\">Bezeichnung, Menge und Preis sind Pflichfelder!</div>");

    }
    $ajaxbuchen = $this->app->Secure->GetPOST("ajaxbuchen");
    if($ajaxbuchen!="")
    {
      $artikel = $this->app->Secure->GetPOST("artikel");
      $nummer = $this->app->Secure->GetPOST("nummer");
      $projekt = $this->app->Secure->GetPOST("projekt");
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM rechnung_position WHERE rechnung='$id' LIMIT 1");
      $sort = $sort + 1;
      $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $bezeichnung = $artikel;
      $neue_nummer = $nummer;
      $waehrung = 'EUR';
      $umsatzsteuerklasse = $this->app->DB->Select("SELECT umsatzsteuerklasse FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $vpe = 'einzeln';

        $this->app->DB->Insert("INSERT INTO rechnung_position (id,rechnung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
    }


    if(1)
    {
      $this->app->Tpl->Set(ARTIKELART,$this->app->erp->GetSelect($this->app->erp->GetArtikelart(),$artikelart));
      $this->app->Tpl->Set(VPE,$this->app->erp->GetSelect($this->app->erp->GetVPE(),$vpe));
      $this->app->Tpl->Set(WAEHRUNG,$this->app->erp->GetSelect($this->app->erp->GetWaehrung(),$vpe));
      $this->app->Tpl->Set(UMSATZSTEUERKLASSE,$this->app->erp->GetSelect($this->app->erp->GetUmsatzsteuerklasse(),$umsatzsteuerklasse));
      $this->app->Tpl->Set(PROJEKT,$this->app->erp->GetProjektSelect($projekt));
      $this->app->Tpl->Set(PREIS,$preis);
      $this->app->Tpl->Set(MENGE,$menge);
      $this->app->Tpl->Set(LIEFERDATUM,$lieferdatum);
      $this->app->Tpl->Set(BEZEICHNUNG,$bezeichung);
      $this->app->Tpl->Set(BESTELLNUMMER,$bestellnummer);


      $this->app->Tpl->Set(SUBSUBHEADING,"Externe Artikel anlegen (kein Stammartikel, kein Lagerartikel, etc.)");
      $this->app->Tpl->Parse(INHALT,"rechnung_artikelneu.tpl");
      $this->app->Tpl->Set(EXTEND,"<input type=\"submit\" value=\"Artikel unter Stammdaten anlegen\" name=\"anlegen_artikelneu\">");
      $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
      $this->app->Tpl->Set(EXTEND,"");
      $this->app->Tpl->Set(INHALT,"");


      /* ende neu anlegen formular */


      $this->app->Tpl->Set(SUBSUBHEADING,"Artikel aus Datenstamm (keine Lagerware)");

      $table = new EasyTable($this->app);
      $table->Query("SELECT CONCAT(LEFT(a.name_de,80),'...') as artikel, a.nummer, 
              p.abkuerzung as projekt,
              CONCAT('<input type=\"text\" size=\"3\" value=\"\" id=\"menge',a.id,'\">') as menge, a.id as id
              FROM artikel a LEFT JOIN projekt p ON a.projekt=p.id WHERE a.lagerartikel=0",5);
      $table->DisplayNew(INHALT, "<input type=\"button\" 
	      onclick=\"document.location.href='index.php?module=rechnung&action=addposition&id=$id&sid=%value%&menge=' + document.getElementById('menge%value%').value;\" value=\"anlegen\">");
      $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
      $this->app->Tpl->Set(INHALT,"");


      /* artikel aus lager */
      $this->app->Tpl->Set(SUBSUBHEADING,"Artikel aus Auftrag");

      $table = new EasyTable($this->app);
      $table->Query("SELECT CONCAT(LEFT(a.name_de,80),'...') as artikel, a.nummer, 
              p.abkuerzung as projekt, '223223' as auftrag, 'im LS 2332' as lieferschein,
              CONCAT('<input type=\"text\" size=\"3\" value=\"\" id=\"menge',a.id,'\">') as menge, a.id as id
              FROM artikel a LEFT JOIN projekt p ON a.projekt=p.id WHERE a.lagerartikel=1",5);

      $table->DisplayNew(INHALT, "<input type=\"button\" 
	      onclick=\"document.location.href='index.php?module=rechnung&action=addposition&id=$id&sid=%value%&menge=' + document.getElementById('menge%value%').value;\" value=\"anlegen\">");
      $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
      $this->app->Tpl->Set(INHALT,"");


    // child table einfuegen

      $this->app->Tpl->Set(SUBSUBHEADING,"Positionen");
      $menu = array("up"=>"uprechnungposition",
			  "down"=>"downrechnungposition",
			  //"add"=>"addstueckliste",
			  "edit"=>"positioneneditpopup",
			  "del"=>"delrechnungposition");

      $sql = "SELECT a.name_de as Artikel, p.abkuerzung as projekt, a.nummer as nummer, 
		DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.id as id
		FROM rechnung_position b
		LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
		WHERE b.rechnung='$id'";

      $this->app->Tpl->Add(EXTEND,"<input type=\"submit\" value=\"Gleiche Positionen zusammenf&uuml;gen\">");

      $this->app->YUI->SortListAdd(INHALT,$this,$menu,$sql);
      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

      if($anlegen_artikelneu!="")
	$this->app->Tpl->Set(AKTIV_TAB2,"selected");
      else
	$this->app->Tpl->Set(AKTIV_TAB1,"selected");
      $this->app->Tpl->Parse(PAGE,"rechnung_positionuebersicht.tpl");
    } 
  }

  function DelRechnungPosition()
  {
    $this->app->YUI->SortListEvent("del","rechnung_position","rechnung");
    $this->RechnungPositionen();
  }

  function UpRechnungPosition()
  {
    $this->app->YUI->SortListEvent("up","rechnung_position","rechnung");
    $this->RechnungPositionen();
  }

  function DownRechnungPosition()
  {
    $this->app->YUI->SortListEvent("down","rechnung_position","rechnung");
    $this->RechnungPositionen();
  }


  function RechnungPositionenEditPopup()
  {
   $id = $this->app->Secure->GetGET("id");

      // nach page inhalt des dialogs ausgeben
      $widget = new WidgetRechnung_position($this->app,PAGE);
      $sid= $this->app->DB->Select("SELECT rechnung FROM rechnung_position WHERE id='$id' LIMIT 1");
      $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=rechnung&action=positionen&id=$sid");
      $widget->Edit();
      $this->app->BuildNavigation=false;
  }



//		       <li><a href="index.php?module=rechnung&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>


  function RechnungEdit()
  {
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
    $msg = $this->app->Secure->GetGET("msg");

    if($this->app->erp->DisableModul("rechnung",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->RechnungMenu();
      return;
    }

    $this->app->YUI->AARLGPositionen();

    $this->app->erp->DisableVerband();
    $this->app->erp->CheckBearbeiter($id,"rechnung");
    $this->app->erp->CheckBuchhaltung($id,"rechnung");

    $zahlungsweise= $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$id' LIMIT 1");
    $zahlungszieltage= $this->app->DB->Select("SELECT zahlungszieltage FROM rechnung WHERE id='$id' LIMIT 1");
    if($zahlungsweise=="rechnung" && $zahlungszieltage<1)
    {
      $this->app->Tpl->Add(MESSAGE,"<div class=\"info\">Hinweis: F&auml;lligkeit auf \"sofort\", da Zahlungsziel in Tagen auf 0 Tage gesetzt ist!</div>");
    }

    $dta_datei = $this->app->DB->Select("SELECT dta_datei FROM rechnung WHERE id='$id' LIMIT 1");
    if($dta_datei > 0)
    {
      $this->app->Tpl->Add(MESSAGE,"<div class=\"info\">Hinweis: Die Rechnung wurde bereits per Lastschrift eingezogen <input type=\"button\" value=\"Nochmal einziehen\"
				onclick=\"if(!confirm('Soll die Rechnung nochmal eingezogen werden?')) return false;else window.location.href='index.php?module=rechnung&action=lastschriftwdh&id=$id';\">.</div>");
    }


 		$status= $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");
    $schreibschutz= $this->app->DB->Select("SELECT schreibschutz FROM rechnung WHERE id='$id' LIMIT 1");


    $this->app->erp->RechnungNeuberechnen($id);

    $this->RechnungMiniDetail(MINIDETAIL,false);
    $this->app->Tpl->Set(ICONMENU,$this->RechnungIconMenu($id));
    $this->app->Tpl->Set(ICONMENU2,$this->RechnungIconMenu($id,2));

    $nummer = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM rechnung WHERE id='$id' LIMIT 1");
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
    $punkte = $this->app->DB->Select("SELECT punkte FROM rechnung WHERE id='$id' LIMIT 1");
    $bonuspunkte = $this->app->DB->Select("SELECT bonuspunkte FROM rechnung WHERE id='$id' LIMIT 1");

		$this->app->Tpl->Set(PUNKTE,"<input type=\"text\" name=\"punkte\" value=\"$punkte\" size=\"10\" readonly>");
		$this->app->Tpl->Set(BONUSPUNKTE,"<input type=\"text\" name=\"punkte\" value=\"$bonuspunkte\" size=\"10\" readonly>");

		if($schreibschutz!="1" && $this->app->erp->RechteVorhanden("rechnung","schreibschutz"))
    	$this->app->erp->AnsprechpartnerButton($adresse);

    if($nummer>0)
    {
      $this->app->Tpl->Set(NUMMER,$nummer);
      $this->app->Tpl->Set(KUNDE,"&nbsp;&nbsp;&nbsp;Kd-Nr.".$kundennummer);
    }

    $lieferdatum= $this->app->DB->Select("SELECT lieferdatum FROM rechnung WHERE id='$id' LIMIT 1");
    $rechnungsdatum= $this->app->DB->Select("SELECT datum FROM rechnung WHERE id='$id' LIMIT 1");
    $lieferscheinid= $this->app->DB->Select("SELECT lieferschein FROM rechnung WHERE id='$id' LIMIT 1");
    $lieferscheiniddatum = $this->app->DB->Select("SELECT datum FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
    if($lieferdatum=="0000-00-00" && $schreibschutz!="1")
    {   
      if($lieferscheiniddatum!="0000-00-00")
        $this->app->DB->Update("UPDATE rechnung SET lieferdatum='$lieferscheiniddatum' WHERE id='$id' LIMIT 1");
      else
        $this->app->DB->Update("UPDATE rechnung SET lieferdatum='$rechnungsdatum' WHERE id='$id' LIMIT 1");
    } 
    

    $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST("zahlungsweise")!="") $zahlungsweise = $this->app->Secure->GetPOST("zahlungsweise");
    $zahlungsweise = strtolower($zahlungsweise);
    $this->app->Tpl->Set(RECHNUNG,"none");
    $this->app->Tpl->Set(KREDITKARTE,"none");
    $this->app->Tpl->Set(VORKASSE,"none");
    $this->app->Tpl->Set(PAYPAL,"none");
    $this->app->Tpl->Set(EINZUGSERMAECHTIGUNG,"none");
    if($zahlungsweise=="rechnung") $this->app->Tpl->Set(RECHNUNG,"");
    if($zahlungsweise=="paypal") $this->app->Tpl->Set(PAYPAL,"");
    if($zahlungsweise=="kreditkarte") $this->app->Tpl->Set(KREDITKARTE,"");
    if($zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift") $this->app->Tpl->Set(EINZUGSERMAECHTIGUNG,"");
    if($zahlungsweise=="vorkasse" || $zahlungsweise=="kreditkarte" || $zahlungsweise=="paypal" || $zahlungsweise=="bar") $this->app->Tpl->Set(VORKASSE,"");


    //ZAHLUNGSEINGANG // wenn einer der reicht dann text sonst tabelle
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(kz.datum,'%d.%m.%Y') as eingang, kz.betrag, 
			ko.bezeichnung as konto,kz.abgeschlossen as komplett FROM kontoauszuege_zahlungseingang kz 
			LEFT JOIN kontoauszuege k ON kz.kontoauszuege=k.id LEFT JOIN konten ko ON ko.id=k.konto 
			WHERE kz.adresse='$adresse' AND kz.objekt='rechnung' AND kz.parameter='$id'");
    $table->DisplayNew(ZAHLUNGSEINGANG,"Komplett","noAction");

    //ZAHLUNGSEINGANG // wenn einer der reicht dann text sonst tabelle
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
    $auftrag= $this->app->DB->Select("SELECT auftrag FROM rechnung WHERE id='$id' LIMIT 1");
    $auftragid= $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr='$auftrag' LIMIT 1");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(kz.datum,'%d.%m.%Y') as eingang, kz.betrag, ko.bezeichnung as konto,kz.abgeschlossen as komplett FROM kontoauszuege_zahlungseingang kz LEFT JOIN kontoauszuege k ON kz.kontoauszuege=k.id LEFT JOIN konten ko ON ko.id=k.konto WHERE kz.adresse='$adresse' AND kz.objekt='auftrag' AND kz.parameter='$auftragid'");
    $table->DisplayNew(ZAHLUNGSEINGANGVORKASSE,"Komplett","noAction");


   
    if($schreibschutz=="1" && $this->app->erp->RechteVorhanden("rechnung","schreibschutz"))
		{
      $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Diese Rechnung wurde bereits versendet und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml; diese Rechnung wirklich entfernt werden?')) return false;else window.location.href='index.php?module=rechnung&action=schreibschutz&id=$id';\"></div>");
			$this->app->erp->CommonReadonly();
		}
  	if($schreibschutz=="1")
      $this->app->erp->CommonReadonly();

    if($status=="")
      $this->app->DB->Update("UPDATE rechnung SET status='angelegt' WHERE id='$id' LIMIT 1");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1")
    {
    $this->app->Tpl->Set(BUTTON_UEBERNEHMEN,'      <input type="button" value="&uuml;bernehmen" onclick="document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
    ');
    } else {
      $this->app->Tpl->Set(BUTTON_UEBERNEHMEN,'
      <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Kunde wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
    ');
    }


    // immer wenn sich der lieferant genändert hat standartwerte setzen
    if($this->app->Secure->GetPOST("adresse")!="")
    {
      $tmp = $this->app->Secure->GetPOST("adresse");
      $kundennummer = $this->app->erp->FirstTillSpace($tmp);

      $name = substr($tmp,6);
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer'  AND geloescht=0 LIMIT 1");

			$uebernehmen =$this->app->Secure->GetPOST("uebernehmen");
      if($uebernehmen=="1") // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
			{
 				$this->app->erp->LoadRechnungStandardwerte($id,$adresse);
	  		$this->app->erp->RechnungNeuberechnen($id);
	  		header("Location: index.php?module=rechnung&action=edit&id=$id");
				exit;
			}
    } 
 

    $land = $this->app->DB->Select("SELECT land FROM rechnung WHERE id='$id' LIMIT 1");
    $ustid = $this->app->DB->Select("SELECT ustid FROM rechnung WHERE id='$id' LIMIT 1");
    $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$id' LIMIT 1");
    if($ust_befreit)$this->app->Tpl->Set(USTBEFREIT,"<div class=\"info\">EU-Lieferung <br>(bereits gepr&uuml;ft!)</div>");
    else if($land!="DE" && $ustid!="") $this->app->Tpl->Set(USTBEFREIT,"<div class=\"error\">EU-Lieferung <br>(Fehler bei Pr&uuml;fung!)</div>");


    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
        $table->Query("SELECT bezeichnung as artikel, nummer as Nummer, menge, vpe as VPE, FORMAT(preis,4) as preis
      FROM rechnung_position
      WHERE rechnung='$id'");
    $table->DisplayNew(POSITIONEN,"Preis","noAction");
/*
    $table->Query("SELECT nummer as Nummer, menge,vpe as VPE, FORMAT(preis,4) as preis, FORMAT(menge*preis,4) as gesamt
      FROM rechnung_position
      WHERE rechnung='$id'");
    $table->DisplayNew(POSITIONEN,"Preis","noAction");
*/
    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(menge*preis),2) FROM rechnung_position
      WHERE rechnung='$id'");
    $waehrung = $this->app->DB->Select("SELECT waehrung FROM rechnung_position
      WHERE rechnung='$id' LIMIT 1");

   $summebrutto = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
    $ust_befreit_check = $this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$id' LIMIT 1");

    if($ust_befreit_check==1)
      $tmp = "Kunde ist UST befreit";
    else
      $tmp = "Kunde zahlt mit UST";


    if($summe > 0)
      $this->app->Tpl->Add(POSITIONEN, "<br><center>Zu zahlen: <b>$summe (netto) $summebrutto (brutto) $waehrung</b> ($tmp)&nbsp;&nbsp;");

    $status= $this->app->DB->Select("SELECT status FROM rechnung WHERE id='$id' LIMIT 1");
//    $this->app->Tpl->Set(STATUS,"<input type=\"text\" size=\"35\" value=\"".$status."\" readonly>");
    $this->app->Tpl->Set(STATUS,"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

     
    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    parent::RechnungEdit();

    $this->app->erp->MessageHandlerStandardForm();


    if($this->app->Secure->GetPOST("weiter")!="")
    {
      header("Location: index.php?module=rechnung&action=positionen&id=$id");
      exit;
    }
    $this->RechnungMenu();

  }

  function RechnungCreate()
  {


    $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Rechnung");
    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=list","Zur&uuml;ck zur &Uuml;bersicht");


    $anlegen = $this->app->Secure->GetGET("anlegen");

    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      header("Location: index.php?module=rechnung&action=create&anlegen=1");
      exit;
    }

    if($anlegen != "")
    {
      $id = $this->app->erp->CreateRechnung();
      $this->app->erp->RechnungProtokoll($id,"Rechnung angelegt");
      header("Location: index.php?module=rechnung&action=edit&id=$id");
      exit;
    }
    $this->app->Tpl->Set(MESSAGE,"<div class=\"warning\">M&ouml;chten Sie eine Rechnung jetzt anlegen? &nbsp;
      <input type=\"button\" onclick=\"window.location.href='index.php?module=rechnung&action=create&anlegen=1'\" value=\"Ja - Rechnung jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set(TAB1,"
     <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
<tr>
<td align=\"center\">
<br><b style=\"font-size: 14pt\">Rechnungen in Bearbeitung</b>
<br>
<br>
Offene Auftr&auml;ge, die durch andere Mitarbeiter in Bearbeitung sind.
<br>
</td>
</tr>  
</table>
<br> 
      [AUFTRAGE]");


    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
		$this->app->YUI->TableSearch(AUFTRAGE,"rechnungeninbearbeitung");
/*
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as beleg, name, status, id
      FROM rechnung WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew(AUFTRAGE, "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=rechnung&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
*/

    $this->app->Tpl->Set(TABTEXT,"Rechnung anlegen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");

    //parent::RechnungCreate();
  }

  function RechnungMahnwesenEinstellungen()
  {
    $speichern = $this->app->Secure->GetPOST("speichern");
    $mahnungstextespeichern = $this->app->Secure->GetPOST("mahnungstextespeichern");

    if($mahnungstextespeichern!="")
    {

      $this->app->erp->SetKonfiguration("textz");
      $this->app->erp->SetKonfiguration("textm1");
      $this->app->erp->SetKonfiguration("textm2");
      $this->app->erp->SetKonfiguration("textm3");
      $this->app->erp->SetKonfiguration("texti");
    }
    if($speichern!="")
    {
      //UPDATE
      $this->app->erp->SetKonfiguration("mahnwesen_ze_versand");
      $this->app->erp->SetKonfiguration("mahnwesen_m1_versand");
      $this->app->erp->SetKonfiguration("mahnwesen_m2_versand");
      $this->app->erp->SetKonfiguration("mahnwesen_m3_versand");
      $this->app->erp->SetKonfiguration("mahnwesen_ik_versand");

      $this->app->erp->SetKonfiguration("mahnwesen_m1_tage");
      $this->app->erp->SetKonfiguration("mahnwesen_m2_tage");
      $this->app->erp->SetKonfiguration("mahnwesen_m3_tage");
      $this->app->erp->SetKonfiguration("mahnwesen_ik_tage");

      $this->app->erp->SetKonfiguration("mahnwesen_m1_gebuehr",true);
      $this->app->erp->SetKonfiguration("mahnwesen_m2_gebuehr",true);
      $this->app->erp->SetKonfiguration("mahnwesen_m3_gebuehr",true);
      $this->app->erp->SetKonfiguration("mahnwesen_ik_gebuehr",true);


    }

    if($this->app->erp->GetKonfiguration("mahnwesen_ze_versand")) $this->app->Tpl->Set(MAHNWESENZE,"checked");
    if($this->app->erp->GetKonfiguration("mahnwesen_m1_versand")) $this->app->Tpl->Set(MAHNWESENM1,"checked");
    if($this->app->erp->GetKonfiguration("mahnwesen_m2_versand")) $this->app->Tpl->Set(MAHNWESENM2,"checked");
    if($this->app->erp->GetKonfiguration("mahnwesen_m3_versand")) $this->app->Tpl->Set(MAHNWESENM3,"checked");
    if($this->app->erp->GetKonfiguration("mahnwesen_ik_versand")) $this->app->Tpl->Set(MAHNWESENIK,"checked");

    $this->app->Tpl->Set(MAHNWESENM1TAGE,$this->app->erp->GetKonfiguration("mahnwesen_m1_tage"));
    $this->app->Tpl->Set(MAHNWESENM2TAGE,$this->app->erp->GetKonfiguration("mahnwesen_m2_tage"));
    $this->app->Tpl->Set(MAHNWESENM3TAGE,$this->app->erp->GetKonfiguration("mahnwesen_m3_tage"));
    $this->app->Tpl->Set(MAHNWESENIKTAGE,$this->app->erp->GetKonfiguration("mahnwesen_ik_tage"));

    $this->app->Tpl->Set(MAHNWESENM1GEBUEHR,$this->app->erp->GetKonfiguration("mahnwesen_m1_gebuehr"));
    $this->app->Tpl->Set(MAHNWESENM2GEBUEHR,$this->app->erp->GetKonfiguration("mahnwesen_m2_gebuehr"));
    $this->app->Tpl->Set(MAHNWESENM3GEBUEHR,$this->app->erp->GetKonfiguration("mahnwesen_m3_gebuehr"));
    $this->app->Tpl->Set(MAHNWESENIKGEBUEHR,$this->app->erp->GetKonfiguration("mahnwesen_ik_gebuehr"));
    
    $this->app->Tpl->Set(TEXTZ,$this->app->erp->GetKonfiguration("textz"));
    $this->app->Tpl->Set(TEXTM1,$this->app->erp->GetKonfiguration("textm1"));
    $this->app->Tpl->Set(TEXTM2,$this->app->erp->GetKonfiguration("textm2"));
    $this->app->Tpl->Set(TEXTM3,$this->app->erp->GetKonfiguration("textm3"));
    $this->app->Tpl->Set(TEXTI,$this->app->erp->GetKonfiguration("texti"));



    $this->RechnungMahnwesenMenu();
    $this->app->Tpl->Set(TABTEXT,"Mahnwesen Einstellungen");
    $this->app->Tpl->Set(TABTEXT2,"Mahntexte");
    $this->app->Tpl->Parse(TAB1,"mahnweseneinstellungen.tpl");
    $this->app->Tpl->Parse(TAB2,"mahnwesentexte.tpl");
    $this->app->Tpl->Parse(PAGE,"mahnweseneinstellung.tpl");

  } 

  function RechnungMahnwesenMenu()
  {

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Mahnwesen");
    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=create\">Neue Rechnung anlegen</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=searc\">Mahnung Suchen</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen&cmd=postausgang\">Postausgang</a></li>");
    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=mahnwesen","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=mahnweseneinstellungen","Einstellungen");

    $this->app->Tpl->Set(UEBERSCHRIFT,"Mahnwesen");
/*
    $this->app->Tpl->Add(TABS,"<li><h2>Mahnwesen</h2></li>");
    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen&cmd=offene\">offene Rechnung</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen&cmd=zahlungserinnerung\">Zahlungserinnerungen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen&cmd=mahnung1\">Mahnung 1</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen&cmd=mahnung2\">Mahnung 2</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen&cmd=mahnung3\">Mahnung 3</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen&cmd=inkasso\">Inkasso</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=rechnung&action=mahnwesen\">&Uuml;bersicht</a></li>");
*/
  }



  function RechnungMahnwesen()
  {

    $this->app->erp->MahnwesenBezahltcheck();
    $this->RechnungMahnwesenMenu();

		$rechnungen = $this->app->Secure->GetPOST("rechnung");
		$drucker= $this->app->Secure->GetPOST("drucker");
		$starten= $this->app->Secure->GetPOST("starten");

		if($starten!="")
		{	
			for($i=0;$i<count($rechnungen);$i++)
			{
	  		if(is_numeric($rechnungen[$i]))
	  		$this->app->erp->MahnwesenSend($rechnungen[$i],$drucker);
			}
		}


    // faellige
		$this->app->Tpl->Set(DRUCKER,$this->app->erp->GetSelectDrucker());
 
		$table = new EasyTable($this->app);
		$this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\">");

		$enddatum= $this->app->Secure->GetGET("enddatum");
		$startdatum= $this->app->Secure->GetGET("startdatum");
		$ohne= $this->app->Secure->GetGET("ohne");

    if($enddatum=="") $enddatum = "0000-00-00";
    if($startdatum=="") $startdatum = "9999-99-99";

		if($ohne=="1") $add_sql = " AND r.zahlungsweise!='nachnahme' AND r.zahlungsweise!='bar' ";
	
		$this->app->Tpl->Add(TAB1,"<br>");
//DATE_FORMAT(DATE_ADD('$datum_sql', INTERVAL $zahlungszieltage DAY),'%d.%m.%Y')

	$table->Query("SELECT if(r.datum < '$enddatum' AND r.datum > '$startdatum' $add_sql,CONCAT('<input type=\"checkbox\" value=\"',r.id,'\" name=\"rechnung[]\" checked>'),CONCAT('<input type=\"checkbox\" value=\"',r.id,'\" name=\"rechnung[]\" >')) as auswahl, CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',r.belegnr,'</a>') as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',r.adresse,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.', LEFT(r.name,20) as name, WAEHRUNG(r.soll) as betrag, WAEHRUNG(r.ist) as ist, r.zahlungsweise, r.mahnwesen as 'Status nach Mahnlauf',if(r.versendet_mahnwesen,'ja','nein') as versendet, r.id
	  FROM rechnung r, adresse a WHERE r.zahlungsstatus='offen' AND a.id=r.adresse AND r.belegnr!=0 AND r.mahnwesen!=''  AND r.versendet_mahnwesen!='1' AND mahnwesen_gesperrt='0'  order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>
	<a onclick=\"if(!confirm('Wirklich Differenzbetrag als Skonto buchen?')) return false; else window.location.href='index.php?module=rechnung&action=skonto&id=%value%';\">
        <img src=\"./themes/new/images/skonto.png\" border=\"0\" alt=\"Differenz als Skonto buchen\"></a>
	<a onclick=\"if(!confirm('Wirklich Rechnung aus Mahnwesen nehmen?')) return false; else window.location.href='index.php?module=rechnung&action=stop&id=%value%';\">
        <img src=\"./themes/new/images/stopmahnung.png\" border=\"0\" alt=\"Aus Mahnwesen nehmen\"></a>
	");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");
	$summe = $this->app->DB->Select("SELECT SUM(r.soll)
	  FROM rechnung r WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND r.mahnwesen!=''  AND r.versendet_mahnwesen!='1'");
//	$this->app->Tpl->Add(TAB1,"<p><br><center>Summe: $summe EUR&nbsp;E-Mail: buchhaltung@embedded-projects.net | Drucker: <select name=\"drucker\">".$this->app->erp->GetSelectDrucker($drucker)."</select>&nbsp;<input type=\"submit\" style=\"background-color: tomato\" value=\"versenden\"></center></p></form>");


		$this->app->Tpl->Add(TAB2,"<br>");
	//zahlungserinnerungen 
	$table = new EasyTable($this->app);
	$table->Query("SELECT CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',r.belegnr,'</a>') as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',r.adresse,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.', LEFT(r.name,20) as name, WAEHRUNG(r.soll) as betrag, WAEHRUNG(r.ist) as ist, r.zahlungsweise, r.mahnwesen, if(r.versendet_mahnwesen,r.mahnwesen_datum,'noch nicht versendet') as versendet_am, r.id
	  FROM rechnung r, adresse a WHERE a.id=r.adresse AND r.zahlungsstatus='offen' AND r.belegnr!=0 AND r.mahnwesen='zahlungserinnerung' AND mahnwesen_gesperrt='0' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB2,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>
	<a onclick=\"if(!confirm('Wirklich Differenzbetrag als Skonto buchen?')) return false; else window.location.href='index.php?module=rechnung&action=skonto&id=%value%#tabs-2';\">
        <img src=\"./themes/new/images/skonto.png\" border=\"0\" alt=\"Differenz als Skonto buchen\"></a>
	<a onclick=\"if(!confirm('Wirklich Rechnung aus Mahnwesen nehmen?')) return false; else window.location.href='index.php?module=rechnung&action=stop&id=%value%#tabs-2';\">
        <img src=\"./themes/new/images/stopmahnung.png\" border=\"0\" alt=\"Aus Mahnwesen nehmen\"></a>
      ");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");

	//mahnungen
     
		$this->app->Tpl->Add(TAB3,"<br>");
	$table = new EasyTable($this->app);
	$table->Query("SELECT  CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',r.belegnr,'</a>') as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',r.adresse,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.', LEFT(r.name,20) as name, WAEHRUNG(r.soll) as betrag, WAEHRUNG(r.ist) as ist, r.zahlungsweise, r.mahnwesen, if(r.versendet_mahnwesen,r.mahnwesen_datum,'noch nicht versendet') as versendet_am, r.id
	  FROM rechnung r, adresse a WHERE a.id=r.adresse AND r.zahlungsstatus='offen' AND r.belegnr!=0 AND (r.mahnwesen='mahnung1' OR r.mahnwesen='mahnung2' OR r.mahnwesen='mahnung3') AND mahnwesen_gesperrt='0' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB3,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>
	<a onclick=\"if(!confirm('Wirklich Differenzbetrag als Skonto buchen?')) return false; else window.location.href='index.php?module=rechnung&action=skonto&id=%value%#tabs-3';\">
        <img src=\"./themes/new/images/skonto.png\" border=\"0\" alt=\"Differenz als Skonto buchen\"></a>
	<a onclick=\"if(!confirm('Wirklich Rechnung aus Mahnwesen nehmen?')) return false; else window.location.href='index.php?module=rechnung&action=stop&id=%value%#tabs-3';\">
        <img src=\"./themes/new/images/stopmahnung.png\" border=\"0\" alt=\"Aus Mahnwesen nehmen\"></a>
	");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");

		$this->app->Tpl->Add(TAB4,"<br>");
	//inkasso
	$table = new EasyTable($this->app);
	$table->Query("SELECT  CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',r.belegnr,'</a>') as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',r.adresse,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.', LEFT(r.name,20) as name, WAEHRUNG(r.soll) as betrag, WAEHRUNG(r.ist) as ist, r.zahlungsweise,  r.mahnwesen, if(r.versendet_mahnwesen,r.mahnwesen_datum,'noch nicht versendet') as versendet_am, r.id
	  FROM rechnung r, adresse a WHERE a.id=r.adresse AND r.zahlungsstatus='offen' AND r.belegnr!=0 AND r.mahnwesen='inkasso' AND mahnwesen_gesperrt='0'  order by r.datum DESC, r.id DESC");


	$table->DisplayNew(TAB4,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>
	<a onclick=\"if(!confirm('Wirklich Differenzbetrag als Skonto buchen?')) return false; else window.location.href='index.php?module=rechnung&action=skonto&id=%value%#tabs-4';\">
        <img src=\"./themes/new/images/skonto.png\" border=\"0\" alt=\"Differenz als Skonto buchen\"></a>
	<a onclick=\"if(!confirm('Wirklich Rechnung aus Mahnwesen nehmen?')) return false; else window.location.href='index.php?module=rechnung&action=stop&id=%value%#tabs-4';\">
        <img src=\"./themes/new/images/stopmahnung.png\" border=\"0\" alt=\"Aus Mahnwesen nehmen\"></a>
	");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");

		$this->app->Tpl->Add(TAB5,"<br>");
	//gesperrt
	$table = new EasyTable($this->app);
	$table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, 
			CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.',
WAEHRUNG(r.soll) as betrag, WAEHRUNG(r.ist) as ist, r.zahlungsweise, r.mahnwesen as status, LEFT(mahnwesen_internebemerkung,20) as grund, if(r.versendet_mahnwesen,'ja','nein') as versendet,r.id
	  FROM rechnung r, adresse a WHERE a.id = r.adresse AND r.zahlungsstatus='offen' AND r.belegnr!=0 AND mahnwesen_gesperrt=1 order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB5,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>
	<a onclick=\"if(!confirm('Wirklich Differenzbetrag als Skonto buchen?')) return false; else window.location.href='index.php?module=rechnung&action=skonto&id=%value%#tabs-5';\">
        <img src=\"./themes/new/images/skonto.png\" border=\"0\" alt=\"Differenz als Skonto buchen\"></a>
  <a onclick=\"if(!confirm('Wirklich Betrag als Forderungsverlust buchen?')) return false; else window.location.href='index.php?module=rechnung&action=forderungsverlust&id=%value%#tabs-5';\">
        <img src=\"./themes/new/images/delete.gif\" border=\"0\" alt=\"Betrag als Forderungsverlust buchen\"></a>

	<a onclick=\"if(!confirm('Wirklich Rechnung in Mahnwesen wieder &uuml;bergeben?')) return false; else window.location.href='index.php?module=rechnung&action=destop&id=%value%';\">
        <img src=\"./themes/new/images/destopmahnung.png\" border=\"0\" alt=\"Rechnung an Mahnwesen &uuml;bergeben\"></a>
	");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");

		$this->app->Tpl->Add(TAB6,"<br>");
	//inkasso
	$table = new EasyTable($this->app);
	$table->Query("SELECT  CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',r.belegnr,'</a>') as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',r.adresse,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.', LEFT(r.name,20) as name, WAEHRUNG(r.soll) as betrag, WAEHRUNG(r.ist) as ist, r.zahlungsweise,  r.mahnwesen, r.id
	  FROM rechnung r, adresse a WHERE a.id=r.adresse AND r.zahlungsstatus='forderungsverlust' AND r.belegnr!=0 order by r.datum DESC, r.id DESC");


	$table->DisplayNew(TAB6,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");




    $this->app->Tpl->Parse(PAGE,"mahnunguebersicht.tpl");


  }

  function RechnungMahnwesenAlt()
  {

    $starten = $this->app->Secure->GetPOST("starten");
    $drucken = $this->app->Secure->GetPOST("drucken");

    $cmd = $this->app->Secure->GetGET("cmd");

    $this->app->erp->MahnwesenBezahltcheck();
    $this->RechnungMahnwesenMenu();
  
 
    $table = new EasyTable($this->app);
    $table->Query("SELECT r.mahnwesen,  SUM(r.soll)
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.versendet_mahnwesen!='1' 
	  AND r.mahnstatus!='' GROUP by r.mahnwesen");
    $table->DisplayNew(INHALT,"");
    $this->app->Tpl->Parse(SUMMEN,"rahmen70.tpl");


    if($cmd=="") $cmd = "offene";

	if($drucken!="")
	{
	  echo "drucken";

	}

    switch($cmd)
    {


      case "postausgang":

	$rechnungen = $this->app->Secure->GetPOST("rechnung");
	$drucker= $this->app->Secure->GetPOST("drucker");

	
	for($i=0;$i<=count($rechnungen);$i++)
	{
	  $this->app->erp->MahnwesenSend($rechnungen[$i],$drucker);
	}


	 
	$table = new EasyTable($this->app);
	$this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\">");
	$table->Query("SELECT CONCAT('<input type=\"checkbox\" value=\"',r.id,'\" name=\"rechnung[]\" checked>') as auswahl, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, r.soll as betrag, r.ist as ist, r.zahlungsweise, r.mahnwesen as status,if(r.versendet_mahnwesen,'ja','nein') as versendet, r.id
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen!=''  AND r.versendet_mahnwesen!='1' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");
	$summe = $this->app->DB->Select("SELECT SUM(r.soll)
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen!=''  AND r.versendet_mahnwesen!='1'");
	$this->app->Tpl->Add(TAB1,"<p><br><center>Summe: $summe EUR&nbsp;E-Mail: buchhaltung@embedded-projects.net | Drucker: <select name=\"drucker\">".$this->app->erp->GetSelectDrucker($drucker)."</select>&nbsp;<input type=\"submit\" style=\"background-color: tomato\" value=\"versenden\"></center></p></form>");

	$this->app->Tpl->Set(TABTEXT,"Mahnwesen &Uuml;bersicht");
	//$this->app->Tpl->Parse(TAB1,"mahnwesen.tpl");
	$this->app->Tpl->Parse(PAGE,"tabview.tpl");


      break;


      case "offene":
	 
	$table = new EasyTable($this->app);
	$table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, r.soll as betrag, r.ist as ist, r.zahlungsweise, 'offen' as status, r.id
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen='' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");

	$this->app->Tpl->Set(TABTEXT,"Mahnwesen &Uuml;bersicht");
	//$this->app->Tpl->Parse(TAB1,"mahnwesen.tpl");
	$this->app->Tpl->Parse(PAGE,"tabview.tpl");


      break;



      case "zahlungserinnerung":
	// offene Rechnungen
  
	$table = new EasyTable($this->app);
	$table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, r.soll as betrag, r.ist as ist, r.zahlungsweise, r.mahnwesen as status, if(r.versendet_mahnwesen,'ja','nein') as versendet,r.id
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen='zahlungserinnerung' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");


	$this->app->Tpl->Set(TABTEXT,"Mahnwesen &Uuml;bersicht");
	//$this->app->Tpl->Parse(TAB1,"mahnwesen.tpl");
	$this->app->Tpl->Parse(PAGE,"tabview.tpl");

      break;

      case "mahnung1":
	// offene Rechnungen
  
	$table = new EasyTable($this->app);
	$table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, r.soll as betrag, r.ist as ist, r.zahlungsweise, r.mahnwesen as status, if(r.versendet_mahnwesen,'ja','nein') as versendet,r.id
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen='mahnung1' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");


	$this->app->Tpl->Set(TABTEXT,"Mahnwesen &Uuml;bersicht");
	//$this->app->Tpl->Parse(TAB1,"mahnwesen.tpl");
	$this->app->Tpl->Parse(PAGE,"tabview.tpl");

      break;


      case "mahnung2":
	// offene Rechnungen
  
	$table = new EasyTable($this->app);
	$table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, r.soll as betrag, r.ist as ist, r.zahlungsweise, r.mahnwesen as status, if(r.versendet_mahnwesen,'ja','nein') as versendet,r.id
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen='mahnung2' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");


	$this->app->Tpl->Set(TABTEXT,"Mahnwesen &Uuml;bersicht");
	//$this->app->Tpl->Parse(TAB1,"mahnwesen.tpl");
	$this->app->Tpl->Parse(PAGE,"tabview.tpl");

      break;

      case "mahnung3":
	// offene Rechnungen
  
	$table = new EasyTable($this->app);
	$table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, r.soll as betrag, r.ist as ist, r.zahlungsweise, r.mahnwesen as status, if(r.versendet_mahnwesen,'ja','nein') as versendet,r.id
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen='mahnung3' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");


	$this->app->Tpl->Set(TABTEXT,"Mahnwesen &Uuml;bersicht");
	//$this->app->Tpl->Parse(TAB1,"mahnwesen.tpl");
	$this->app->Tpl->Parse(PAGE,"tabview.tpl");

      break;

      case "inkasso":
	// offene Rechnungen
  
	$table = new EasyTable($this->app);
	$table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, LEFT(r.name,20) as name, r.soll as betrag, r.ist as ist, r.zahlungsweise, r.mahnwesen as status, if(r.versendet_mahnwesen,'ja','nein') as versendet,r.id
	  FROM rechnung r, projekt p WHERE r.zahlungsstatus='offen' AND r.belegnr!=0 AND p.id=r.projekt AND r.mahnwesen='inkasso' order by r.datum DESC, r.id DESC");
	$table->DisplayNew(TAB1,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>");
	//$this->app->Tpl->Parse(TABLE,"rahmen70.tpl");


	$this->app->Tpl->Set(TABTEXT,"Mahnwesen &Uuml;bersicht");
	//$this->app->Tpl->Parse(TAB1,"mahnwesen.tpl");
	$this->app->Tpl->Parse(PAGE,"tabview.tpl");

      break;

      default:

      break;


    }



  }


  function RechnungList()
  {

    //$this->app->erp->MahnwesenBezahltcheck(); //TODO LANGSAM

    $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen' WHERE zahlungsstatus=''");

    $this->app->Tpl->Set(UEBERSCHRIFT,"Rechnungen");


    $backurl = $this->app->Secure->GetGET("backurl");
    $backurl = $this->app->erp->base64_url_decode($backurl);

//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Rechnungen");
    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=rechnung&action=create","Neue Rechnung anlegen");
 

    if(strlen($backurl)>5)
      $this->app->erp->MenuEintrag("$backurl","Zur&uuml;ck");
    //else
    //  $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");


    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    $this->app->Tpl->Set(INHALT,"");

    $this->app->YUI->TableSearch(TAB2,"rechnungenoffene");
    $this->app->YUI->TableSearch(TAB1,"rechnungen");
    $this->app->YUI->TableSearch(TAB3,"rechnungeninbearbeitung");

    $this->app->Tpl->Parse(PAGE,"rechnunguebersicht.tpl");

    return;




    $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\">Allgemein</h2></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=create\">Neue Rechnung anlegen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=search\">Rechnung Suchen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");
    $this->app->Tpl->Add(TABS,"<li><br><br></li>");


    // nicht versendete Rechnungen
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, if(r.belegnr,r.belegnr,'ohne Nummer') as beleg, r.name, p.abkuerzung as projekt, r.soll as betrag,if(r.zahlungsstatus='bezahlt',r.zahlungsstatus,'offen') as status, aborechnung as RL, r.id
      FROM rechnung r, projekt p WHERE r.versendet=0 AND r.status='freigegeben' AND p.id=r.projekt order by r.datum DESC, r.id DESC");

    $table->DisplayNew(INHALT,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>


        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
    $this->app->Tpl->Set(EXTEND,"<input type=\"button\" value=\"Sammelmailversand Rechnungslauf Rechnungen\">");
    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
    $this->app->Tpl->Set(INHALT,"");

    // offene Rechnungen
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, if(r.belegnr,r.belegnr,'ohne Nummer') as beleg, r.name, p.abkuerzung as projekt, r.soll as betrag, r.ist as ist, r.zahlungsweise, r.mahnwesen, r.id
      FROM rechnung r, projekt p WHERE r.zahlungsstatus!='bezahlt' AND r.belegnr!=0 AND p.id=r.projekt order by r.datum DESC, r.id DESC");
    $table->DisplayNew(INHALT,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>

        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");

    $summe = $this->app->DB->Select("SELECT SUM(r.soll)
      FROM rechnung r, projekt p WHERE r.zahlungsstatus!='bezahlt' AND r.belegnr!=0 AND p.id=r.projekt");
    $this->app->Tpl->Set(EXTEND,"Gesamt offen: $summe EUR");
/*
    $table->DisplayOwn(INHALT, "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ",30,"mid");
*/
    $this->app->Tpl->Parse(TAB2,"rahmen70.tpl");
 
    $this->app->Tpl->Set(INHALT,"");

     // Archiv 
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, if(r.belegnr,r.belegnr,'ohne Nummer') as beleg, r.name, p.abkuerzung as projekt, r.zahlungsweise, r.soll as betrag, status, r.id
      FROM rechnung r, projekt p WHERE zahlungsstatus='bezahlt' AND r.belegnr!=0 AND p.id=r.projekt order by r.datum DESC, r.id DESC");
    $table->DisplayNew(INHALT,"<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
       	<a onclick=\"if(!confirm('Wirklich als Gutschrift/Stornorechnung weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=rechnung&action=gutschrift&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>
 <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>

        ");

/*
    $table->DisplayOwn(INHALT, "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ",30,"mid");
*/
    $this->app->Tpl->Parse(TAB3,"rahmen70.tpl");
 
    $this->app->Tpl->Set(INHALT,"");

    // In Bearbeitung
    $table = new EasyTable($this->app);
     $table->Query("SELECT DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, if(r.belegnr,r.belegnr,'ohne Nummer') as beleg, r.name, p.abkuerzung as projekt, r.soll as betrag, r.id
      FROM rechnung r, projekt p WHERE r.versendet=0 AND status='angelegt' AND p.id=r.projekt order by r.datum DESC, r.id DESC");

    $table->DisplayNew(INHALT, "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=rechnung&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=rechnung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
    $this->app->Tpl->Parse(TAB4,"rahmen70.tpl");

//    if($this->app->DB->Select("SELECT SUM(id) FROM rechnung WHERE versendet=0")==0)
//      $this->app->Tpl->Set(TAB1,"<div class=\"info\">Es sind keine nicht versendeten Rechnungen in Arbeit!</div>");


   $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    $this->app->Tpl->Parse(PAGE,"rechnunguebersicht.tpl");

/*
    $this->app->Tpl->Set(TAB2,"lieferant, rechnung, waehrung, sprache, liefertermin, steuersatz, einkäufer, freigabe<br>
<br>Rechnung (NR),Bestellart (NB), Bestelldatum
<br>Projekt
<br>Kostenstelle pro Position
<br>Terminrechnung (am xx.xx.xxxx raus damit)
<br>vorschlagsdaten für positionen
<br>proposition reinklicken zum ändern und reihenfolge tabelle 
<br>Rechnung muss werden wie rechnung (rechnung beschreibung = allgemein)
<br>Positionen (wie stueckliste)
<br>Wareneingang / Rückstand
<br>Etiketten
<br>Freigabe
<br>Dokument direkt faxen
");
*/
  }

}
?>

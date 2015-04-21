<?php
include ("_gen/angebot.php");

class Angebot extends GenAngebot
{

  function Angebot(&$app)
  {
    $this->app=&$app; 
  
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","AngebotList");
    $this->app->ActionHandler("create","AngebotCreate");
    $this->app->ActionHandler("positionen","AngebotPositionen");
    $this->app->ActionHandler("addposition","AngebotAddPosition");
    $this->app->ActionHandler("upangebotposition","UpAngebotPosition");
    $this->app->ActionHandler("delangebotposition","DelAngebotPosition");
    $this->app->ActionHandler("downangebotposition","DownAngebotPosition");
    $this->app->ActionHandler("positioneneditpopup","AngebotPositionenEditPopup");
    $this->app->ActionHandler("edit","AngebotEdit");
    $this->app->ActionHandler("copy","AngebotCopy");
    $this->app->ActionHandler("auftrag","AngebotAuftrag");
    $this->app->ActionHandler("delete","AngebotDelete");
    $this->app->ActionHandler("freigabe","AngebotFreigabe");
    $this->app->ActionHandler("abschicken","AngebotAbschicken");
    $this->app->ActionHandler("pdf","AngebotPDF");
    $this->app->ActionHandler("inlinepdf","AngebotInlinePDF");
    $this->app->ActionHandler("protokoll","AngebotProtokoll");
    $this->app->ActionHandler("minidetail","AngebotMiniDetail");
    $this->app->ActionHandler("editable","AngebotEditable");
    $this->app->ActionHandler("livetabelle","AngebotLiveTabelle");
    $this->app->ActionHandler("schreibschutz","AngebotSchreibschutz");
    $this->app->ActionHandler("deleterabatte","AngebotDeleteRabatte");
  
    $this->app->DefaultActionHandler("list");
 
    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("adresse");

    if($nummer=="")
      $adresse= $this->app->DB->Select("SELECT a.name FROM angebot b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    else
      $adresse = $nummer;

    $nummer = $this->app->DB->Select("SELECT b.belegnr FROM angebot b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    if($nummer=="" || $nummer==0) $nummer="ohne Nummer";

    $this->app->Tpl->Set(UEBERSCHRIFT,"Angebot:&nbsp;".$adresse." (".$nummer.")");
    $this->app->Tpl->Set(FARBE,"[FARBE2]");


    
    $this->app->ActionHandlerListen($app);
  }

  function AngebotDeleteRabatte()
  {
  
    $id=$this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE angebot SET rabatt='',rabatt1='',rabatt2='',rabatt3='',rabatt4='',rabatt5='',realrabatt='' WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Rabatte wurden entfernt!</div>  ");
    header("Location: index.php?module=angebot&action=edit&id=$id&msg=$msg");
    exit;
  } 

  function AngebotEditable()
  {
    $this->app->YUI->AARLGEditable();
  }

  function AngebotSchreibschutz()
  {

    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE angebot SET schreibschutz='0' WHERE id='$id'");
    header("Location: index.php?module=angebot&action=edit&id=$id");
    exit;

  }


  function AngebotMiniDetail($parsetarget="",$menu=true)
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->BerechneDeckungsbeitrag($id,"angebot");

    $auftragArr = $this->app->DB->SelectArr("SELECT * FROM angebot WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$auftragArr[0]['projekt']}' LIMIT 1");
    $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$auftragArr[0]['adresse']}' LIMIT 1");


    $this->app->Tpl->Set(KUNDE,$kundennummer." ".$kundenname);
    $this->app->Tpl->Set(DECKUNGSBEITRAG,$auftragArr[0]['erloes_netto']);
    $this->app->Tpl->Set(DBPROZENT,$auftragArr[0]['deckungsbeitrag']);

    $this->app->Tpl->Set(PROJEKT,$projekt);
    $this->app->Tpl->Set(ZAHLWEISE,$auftragArr[0]['zahlungsweise']);
    $this->app->Tpl->Set(STATUS,$auftragArr[0]['status']);
    $this->app->Tpl->Set(ANFRAGE,$auftragArr[0]['anfrage']);
    
    if($auftragArr[0]['ust_befreit']==0)
      $this->app->Tpl->Set(STEUER,"Deutschland");
    else if($auftragArr[0]['ust_befreit']==1)
      $this->app->Tpl->Set(STEUER,"EU-Lieferung");
    else
      $this->app->Tpl->Set(STEUER,"Export");


    if($menu)
    {
      $menu = $this->AngebotIconMenu($id);
      $this->app->Tpl->Set(MENU,$menu);
    }
 // ARTIKEL

 $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
    $table->Query("SELECT ap.bezeichnung as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, ap.menge as M,
      if(a.porto,'-',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
      if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
      '<font color=red><b>aus</b></font>'))) as L
      FROM angebot_position ap, artikel a 
			WHERE ap.angebot='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","A","noAction");

	$this->app->Tpl->Add(JAVASCRIPT,"
	    var auto_refresh = setInterval(
	function ()
	{
	$('#artikeltabellelive$id').load('index.php?module=angebot&action=livetabelle&id=$id').fadeIn('slow');
	}, 3000); // refresh every 10000 milliseconds
	");
    } else {
      $table->Query("SELECT ap.bezeichnung as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',ap.artikel,'\" target=\"_blank\">', ap.nummer,'</a>') as Nummer, ap.menge as M
      FROM angebot_position ap, artikel a WHERE ap.angebot='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","Menge","noAction");
    }

    $this->app->Tpl->Set(ARTIKEL,'<div id="artikeltabellelive'.$id.'">'.$artikel.'</div>');

		if($auftragArr[0]['belegnr']<=0) $auftragArr[0]['belegnr'] = "ENTWURF";
    $this->app->Tpl->Set(BELEGNR,$auftragArr[0]['belegnr']);
    $this->app->Tpl->Set(ANGEBOTID,$auftragArr[0]['id']);


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

    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM angebot_protokoll WHERE angebot='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(PROTOKOLL,"Protokoll","noAction");



    if($parsetarget=="")
    {
      $this->app->Tpl->Output("angebot_minidetail.tpl");
      exit;
    }  else {
      $this->app->Tpl->Parse($parsetarget,"angebot_minidetail.tpl");
    }
  }



  function AngebotFreigabe()
  {
    $id = $this->app->Secure->GetGET("id");
    $freigabe= $this->app->Secure->GetGET("freigabe");
    $weiter= $this->app->Secure->GetPOST("weiter");
    $this->app->Tpl->Set(TABTEXT,"Freigabe");

    $this->app->erp->CheckVertrieb($id,"angebot");
    $this->app->erp->CheckBearbeiter($id,"angebot");

    if($weiter!="")
    {
       header("Location: index.php?module=angebot&action=abschicken&id=$id");
       exit;
    }

    
     $check = $this->app->DB->Select("SELECT b.belegnr FROM angebot b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");


    if($freigabe==$id)
    {
      $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
			if($belegnr=="")
			{
				$belegnr = $this->app->erp->GetNextNummer("angebot",$projekt);

      	$this->app->DB->Update("UPDATE angebot SET belegnr='$belegnr', status='freigegeben' WHERE id='$id' LIMIT 1");
      	$this->app->erp->AngebotProtokoll($id,"Angebot freigegeben");
      	$msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Das Angebot wurde freigegeben und kann jetzt versendet werden!</div>  ");
      	header("Location: index.php?module=angebot&action=edit&id=$id&msg=$msg");
      	exit;
			} else {
				$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Angebot wurde bereits freigegeben!</div>  ");
      	header("Location: index.php?module=angebot&action=edit&id=$id&msg=$msg");
      	exit;
			}
    } else { 

      $name = $this->app->DB->Select("SELECT a.name FROM angebot b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
			$summe = $this->app->DB->Select("SELECT gesamtsumme FROM angebot WHERE id='$id' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM angebot_position
	WHERE angebot='$id' LIMIT 1");

      $summe = $this->app->erp->EUR($summe);

      $this->app->Tpl->Set(TAB1,"<div class=\"info\">Soll das Angebot  
	jetzt freigegeben werden? <input type=\"button\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=angebot&action=freigabe&id=$id&freigabe=$id'\">
	</div>");
    }
    $this->AngebotMenu();
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function AngebotCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->CopyAngebot($id);

    header("Location: index.php?module=angebot&action=edit&id=$newid");
    exit;
  }


  function AngebotLiveTabelle()
  {
    $id = $this->app->Secure->GetGET("id");
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
    $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M,
      if(a.porto,'-',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
      if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
      '<font color=red><b>aus</b></font>'))) as L
      FROM angebot_position ap, artikel a WHERE ap.angebot='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","A","noAction");
    } else {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M
      FROM angebot_position ap, artikel a WHERE ap.angebot='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","Menge","noAction");
    }
    echo $artikel;
    exit;
  }


  function AngebotAuftrag()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->WeiterfuehrenAngebotZuAuftrag($id);

    header("Location: index.php?module=auftrag&action=edit&id=$newid");
    exit;
  }

  function AngebotAbschicken()
  {
    $this->AngebotMenu();
    $this->app->erp->DokumentAbschicken();
  }


  function AngebotDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM angebot WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

    if($belegnr==0 || $belegnr=="")
    {
    
      $this->app->erp->DeleteAngebot($id);
      if($belegnr<=0) $belegnr="ENTWURF";
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Angebot \"$name\" ($belegnr) wurde gel&ouml;scht!</div>  ");
      //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
      header("Location: index.php?module=angebot&action=list&msg=$msg");
      exit;
    } else 
    {
    if($status=="abgeschlossen")                                                                                                                            
      {                                                                                                                                                       
			// KUNDE muss RMA starten                                                                                                                             
				$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) kann nicht storniert werden da Angebot als Auftrag bereits weitergef&uuml;hrt worden ist!</div>  ");
			}                                                                                                                                                                   
			else if($status=="storniert")                                                                                                                                       
			{                                                                                                                                                                   
 				$maxbelegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot");
        if(0)//$maxbelegnr == $belegnr)
        {
          $this->app->DB->Delete("DELETE FROM angebot_position WHERE angebot='$id'");
          $this->app->DB->Delete("DELETE FROM angebot_protokoll WHERE angebot='$id'");
          $this->app->DB->Delete("DELETE FROM angebot WHERE id='$id'");
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) wurde ge&ouml;scht !</div>  ");
        } else
        {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) kann nicht storniert werden das sie er bereits storniert ist!</div>  ");
        }
        header("Location: index.php?module=angebot&action=list&msg=$msg");
        exit;
			}                                                                                                                                                                   

			else {                                                                                                                                                              
				$this->app->DB->Update("UPDATE angebot SET status='storniert' WHERE id='$id' LIMIT 1");                                                                             
				$this->app->erp->AngebotProtokoll($id,"Angebot storniert");                                                                                                         
      	$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Angebot \"$name\" ($belegnr) wurde storniert!</div>");                                                                                                 
			}
      //$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Angebot \"$name\" ($belegnr) kann nicht storniert werden, da es bereits versendet wurde!</div>");
      header("Location: index.php?module=angebot&action=list&msg=$msg#tabs-1");
      exit;
    }
  }

  function AngebotProtokoll()
  {
    $this->AngebotMenu();
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set(TABTEXT,"Protokoll");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM angebot_protokoll WHERE angebot='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(TAB1,"Protokoll","noAction");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function AngebotAddPosition()
  {
    $sid = $this->app->Secure->GetGET("sid");
    $id = $this->app->Secure->GetGET("id");
    $menge = $this->app->Secure->GetGET("menge");
    $datum  = $this->app->Secure->GetGET("datum");
    $datum  = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
    $this->app->erp->AddAngebotPosition($id, $sid,$menge,$datum);
    $this->app->erp->AngebotNeuberechnen($id);

    header("Location: index.php?module=angebot&action=positionen&id=$id");
    exit;
 
  }

  function AngebotInlinePDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AngebotNeuberechnen($id);

		$projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$id' LIMIT 1");

    $frame = $this->app->Secure->GetGET("frame");

    if($frame=="")
    {
      $Brief = new AngebotPDF($this->app,$projekt);
      $Brief->GetAngebot($id);
      $Brief->inlineDocument();
    } else {

			$file = urlencode("../../../../index.php?module=angebot&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"600\" src=\"./js/production/generic/web/viewer.html?file=$file\"></iframe>";
      exit;
    }
 }


  function AngebotPDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AngebotNeuberechnen($id);

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM angebot WHERE id='$id' LIMIT 1");

//    if(is_numeric($belegnr) && $belegnr!=0)
    {
      $Brief = new AngebotPDF($this->app,$projekt);
      $Brief->GetAngebot($id);
      $Brief->displayDocument(); 
    } //else
 //     $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Angeboten k&ouml;nnen nicht als PDF betrachtet werden.!</div>");


    $this->AngebotList();
 }




  function AngebotMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM angebot WHERE id='$id' LIMIT 1");

    if($belegnr<=0) $belegnr ="(Entwurf)";
//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Angebot $belegnr");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,"$name Angebot $belegnr");
    $this->app->erp->AngebotNeuberechnen($id);

    // status bestell
    $status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
		if ($status=="angelegt")
    {
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=freigabe&id=$id","Freigabe");
    }


    $this->app->erp->MenuEintrag("index.php?module=angebot&action=edit&id=$id","Details");
   
    if($status=='bestellt')
    { 
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=wareneingang&id=$id","Wareneingang<br>R&uuml;ckst&auml;nde");
      $this->app->erp->MenuEintrag("index.php?module=angebot&action=wareneingang&id=$id","Mahnstufen");
    } 

 //   $this->app->erp->MenuEintrag("index.php?module=angebot&action=abschicken&id=$id","Abschicken / Protokoll");
//    $this->app->erp->MenuEintrag("index.php?module=angebot&action=protokoll&id=$id","Protokoll");
		if($this->app->Secure->GetGET("action")!="abschicken")
    	$this->app->erp->MenuEintrag("index.php?module=angebot&action=list","Zur&uuml;ck zur &Uuml;bersicht");
		else
	    $this->app->erp->MenuEintrag("index.php?module=angebot&action=edit&id=$id","Zur&uuml;ck zum Angebot");


    $this->app->Tpl->Parse(MENU,"angebot_menu.tpl");

  }

  function AngebotPositionen()
  {

    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AngebotNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);

    return;


    $this->AngebotMenu();
    $id = $this->app->Secure->GetGET("id");

    /* neu anlegen formular */
    $artikelart = $this->app->Secure->GetPOST("artikelart");
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


    $angebotsart = $this->app->DB->Select("SELECT angebotsart FROM angebot WHERE id='$id' LIMIT 1");
    $lieferant  = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id='$id' LIMIT 1");

    $anlegen_artikelneu = $this->app->Secure->GetPOST("anlegen_artikelneu");

    if($anlegen_artikelneu!="")
    {

      if($bezeichnung!="" && $menge!="" && $preis!="")
      {
	$sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE angebot='$id' LIMIT 1");
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

	$this->app->DB->Insert("INSERT INTO angebot_position (id,angebot,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
	  VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");

	header("Location: index.php?module=angebot&action=positionen&id=$id");
	exit;
      } else
	$this->app->Tpl->Set(NEUMESSAGE,"<div class=\"error\">Bestellnummer, bezeichnung, Menge und Preis sind Pflichfelder!</div>");

    }

    $ajaxbuchen = $this->app->Secure->GetPOST("ajaxbuchen");
    if($ajaxbuchen!="")
    {
      $artikel = $this->app->Secure->GetPOST("artikel");
      $nummer = $this->app->Secure->GetPOST("nummer");
      $projekt = $this->app->Secure->GetPOST("projekt");
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE auftrag='$id' LIMIT 1");
      $sort = $sort + 1;
      $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $bezeichnung = $artikel;
      $neue_nummer = $nummer;
      $waehrung = 'EUR';
      $umsatzsteuerklasse = $this->app->DB->Select("SELECT umsatzsteuerklasse FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $vpe = 'einzeln';

        $this->app->DB->Insert("INSERT INTO angebot_position (id,angebot,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
    }
    $weiter = $this->app->Secure->GetPOST("weiter");
    if($weiter!="")
    {
       header("Location: index.php?module=angebot&action=freigabe&id=$id");
       exit;
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

      $this->app->Tpl->Set(SUBSUBHEADING,"Neuen Artikel anlegen");
      $this->app->Tpl->Parse(INHALT,"angebot_artikelneu.tpl");
      $this->app->Tpl->Set(EXTEND,"<input type=\"submit\" value=\"Artikel unter Stammdaten anlegen\" name=\"anlegen_artikelneu\">");
      $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
      $this->app->Tpl->Set(EXTEND,"");
      $this->app->Tpl->Set(INHALT,"");

      /* ende neu anlegen formular */


      $this->app->Tpl->Set(SUBSUBHEADING,"Artikelstamm");

      $lieferant = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id='$id' LIMIT 1");

      $table = new EasyTable($this->app);
      $table->Query("SELECT CONCAT(LEFT(a.name_de,80),'...') as artikel, a.nummer, 
	      v.ab_menge as ab, v.preis, p.abkuerzung as projekt,
	      CONCAT('<input type=\"text\" size=\"8\" value=\"00.00.0000\" id=\"datum',v.id,'\">
	      <img src=\"./themes/new/images/kalender.png\" height=\"12\" onclick=\"displayCalendar(document.forms[1].datum',v.id,',\'dd.mm.yyyy\',this)\" border=0 align=right>') as Lieferdatum, 
	      CONCAT('<input type=\"text\" size=\"3\" value=\"\" id=\"menge',v.id,'\">') as menge, v.id as id
	      FROM artikel a LEFT JOIN verkaufspreise v ON a.id=v.artikel LEFT JOIN projekt p ON v.projekt=p.id WHERE v.ab_menge>=1");
      $table->DisplayNew(INHALT, "<input type=\"button\" 
	      onclick=\"document.location.href='index.php?module=angebot&action=addposition&id=$id&sid=%value%&menge=' + document.getElementById('menge%value%').value + '&datum=' + document.getElementById('datum%value%').value;\" value=\"anlegen\">");
      $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
      $this->app->Tpl->Set(INHALT,"");

	    // child table einfuegen

      $this->app->Tpl->Set(SUBSUBHEADING,"Positionen");
      $menu = array("up"=>"upangebotposition",
			  "down"=>"downangebotposition",
			  //"add"=>"addstueckliste",
			  "edit"=>"positioneneditpopup",
			  "del"=>"delangebotposition");

      $sql = "SELECT a.name_de as Artikel, p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.id as id
		FROM angebot_position b
		LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id 
		WHERE b.angebot='$id'";

//      $this->app->Tpl->Add(EXTEND,"<input type=\"submit\" value=\"Gleiche Positionen zusammenf&uuml;gen\">");

      $this->app->YUI->SortListAdd(INHALT,$this,$menu,$sql);
      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

      if($anlegen_artikelneu!="")
	$this->app->Tpl->Set(AKTIV_TAB2,"selected");
      else
	$this->app->Tpl->Set(AKTIV_TAB1,"selected");
      $this->app->Tpl->Parse(PAGE,"angebot_positionuebersicht.tpl");
    } 
  }

  function DelAngebotPosition()
  {
    $this->app->YUI->SortListEvent("del","angebot_position","angebot");
    $this->AngebotPositionen();
  }

  function UpAngebotPosition()
  {
    $this->app->YUI->SortListEvent("up","angebot_position","angebot");
    $this->AngebotPositionen();
  }

  function DownAngebotPosition()
  {
    $this->app->YUI->SortListEvent("down","angebot_position","angebot");
    $this->AngebotPositionen();
  }


  function AngebotPositionenEditPopup()
  {
   	$id = $this->app->Secure->GetGET("id");

    $artikel= $this->app->DB->Select("SELECT artikel FROM angebot_position WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(ANZEIGEEINKAUFLAGER,$this->app->erp->AnzeigeEinkaufLager($artikel));

    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetAngebot_position($this->app,PAGE);
    $sid= $this->app->DB->Select("SELECT angebot FROM angebot_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=angebot&action=positionen&id=$sid");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }



  function AngebotIconMenu($id,$prefix="")
  { 

		$status = $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");

		if($status=="angelegt")
			$freigabe = "<option value=\"freigabe\">Angebot freigeben</option>";

    $menu ="

    <script type=\"text/javascript\">
	function onchangeangebot(cmd)
	{
		switch(cmd)
		{
			case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=delete&id=%value%'; break;
			case 'pdf': window.location.href='index.php?module=angebot&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
			case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=copy&id=%value%'; break;
			case 'auftrag': if(!confirm('Wirklich als Auftrag weiterführen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=angebot&action=auftrag&id=%value%'; break;
      case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;

			case 'freigabe':  window.location.href='index.php?module=angebot&action=freigabe&id=%value%'; break;
		}
	}
		</script>
Aktion:&nbsp;<select onchange=\"onchangeangebot(this.value)\" id=\"aktion$prefix\"><option>bitte w&auml;hlen ...</option>
<option value=\"storno\">Angebot stornieren</option>
<option value=\"copy\">Angebot kopieren</option>
$freigabe
<option value=\"abschicken\">Angebot abschicken</option>
<option value=\"auftrag\">als Auftrag weiterf&uuml;hren</option>
<option value=\"pdf\">PDF &ouml;ffnen</option>
</select>&nbsp;


        <a href=\"index.php?module=angebot&action=pdf&id=%value%\" title=\"PDF\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
<!--
        <a href=\"index.php?module=angebot&action=edit&id=%value%\" title=\"Bearbeiten\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=angebot&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" title=\"Stornieren\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\" title=\"Kopieren\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
     <a onclick=\"if(!confirm('Wirklich als Auftrag weiterf&uuml;hren?')) return false; else window.location.href='index.php?module=angebot&action=auftrag&id=%value%';\" title=\"Als Auftrag weiterf&uuml;hren\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Lieferschein\"></a>-->";

      //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }


  function AngebotEdit()
  {
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->AngebotNeuberechnen($id);

    if($this->app->erp->DisableModul("angebot",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->AngebotMenu();
      return;
    }

    $this->app->erp->InfoAuftragsErfassung("angebot",$id);

    $this->app->erp->DisableVerband();
    $this->app->erp->CheckBearbeiter($id,"angebot");
    $this->app->erp->CheckVertrieb($id,"angebot");


//		$this->app->DB->Select("set @order = 0;");
//		$this->app->DB->Update("update angebot_position set sort=@order:= @order + 1 WHERE angebot='$id' order by sort asc");

    $this->app->YUI->AARLGPositionen();

   $icons = $this->app->YUI->IconsSQLAll();
   $icons = $this->app->DB->Select("SELECT $icons FROM angebot a WHERE a.id='$id' LIMIT 1");


    $this->app->Tpl->Set(STATUSICONS,$icons);
/*
    $msg2 = $this->app->Secure->GetGET("msg");
    //$storno = $this->app->Secure->GetGET("storno");


    if($msg2!="")
    {
      $msg2 = $this->app->erp->base64_url_decode($msg);
      $this->app->Tpl->Set(MESSAGE,"<table width=\"100%\" border=\"1\"><tr><td>".$msg2."</td></tr></table>");
    }

*/




    //$this->AngebotMiniDetail(MINIDETAIL,false);


    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $nummer = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    $adresse = $this->app->DB->Select("SELECT adresse FROM angebot WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

  	$status= $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    $schreibschutz= $this->app->DB->Select("SELECT schreibschutz FROM angebot WHERE id='$id' LIMIT 1");


    $this->app->Tpl->Set(ICONMENU,$this->AngebotIconMenu($id));
    $this->app->Tpl->Set(ICONMENU2,$this->AngebotIconMenu($id,2));

		if($schreibschutz!="1" && $this->app->erp->RechteVorhanden("angebot","schreibschutz"))
		{
    	$this->app->erp->AnsprechpartnerButton($adresse);
    	$this->app->erp->LieferadresseButton($adresse);
		}

    if($nummer>0)
    {
      $this->app->Tpl->Set(NUMMER,$nummer);
      $this->app->Tpl->Set(KUNDE,"&nbsp;&nbsp;&nbsp;Kd-Nr.".$kundennummer);
    }


    if($this->app->Secure->GetPOST("speichern")!="")
    {
      $abweichenderechnungsadresse = $this->app->Secure->GetPOST("abweichenderechnungsadresse");
      $abweichendelieferdresse = $this->app->Secure->GetPOST("abweichendelieferadresse");
    } else {
      $abweichenderechnungsadresse = $this->app->DB->Select("SELECT abweichenderechnungsadresse FROM angebot WHERE id='$id' LIMIT 1");
      $abweichendelieferadresse = $this->app->DB->Select("SELECT abweichendelieferadresse FROM angebot WHERE id='$id' LIMIT 1");
    }
    if($abweichenderechnungsadresse) $this->app->Tpl->Set(RECHNUNGSADRESSE,"visible"); else $this->app->Tpl->Set(RECHNUNGSADRESSE,"none");
    if($abweichendelieferadresse) $this->app->Tpl->Set(LIEFERADRESSE,"visible"); else $this->app->Tpl->Set(LIEFERADRESSE,"none");

    if(!is_numeric($belegnr) || $belegnr==0)
    {
    $this->app->Tpl->Set(LOESCHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=angebot&action=delete&id=$id';\">");
    }
    $status= $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    if($status=="")
      $this->app->DB->Update("UPDATE angebot SET status='angelegt' WHERE id='$id' LIMIT 1");


	 if($schreibschutz=="1" && $this->app->erp->RechteVorhanden("angebot","schreibschutz"))
    {
      $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Dieses Angebot wurde bereits versendet und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml;r dieses Angebot wirklich entfernt werden?')) return false;else window.location.href='index.php?module=angebot&action=schreibschutz&id=$id';\"></div>");
      $this->app->erp->CommonReadonly();
    }

  if($schreibschutz=="1") 
      $this->app->erp->CommonReadonly();


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
 			$tmp = trim($tmp);
      $rest = explode(" ",$tmp);
      $kundennummer = $rest[0];

      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer'  AND geloescht=0 LIMIT 1");

      $uebernehmen =$this->app->Secure->GetPOST("uebernehmen");
      if($uebernehmen=="1") // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
	  		$this->app->erp->LoadAngebotStandardwerte($id,$adresse);
        header("Location: index.php?module=angebot&action=edit&id=$id");
        exit;
      }
    }

    $table = new EasyTable($this->app);
        $table->Query("SELECT bezeichnung as artikel, nummer as Nummer, menge, vpe as VPE, FORMAT(preis,4) as preis
      FROM angebot_position 
      WHERE angebot='$id'");
    $table->DisplayNew(POSITIONEN,"Preis","noAction");

   // $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM angebot WHERE id='$id' LIMIT 1");
   // $this->app->Tpl->Set(BEARBEITER,"<input type=\"text\" value=\"".$this->app->erp->GetAdressName($bearbeiter)."\" readonly>");

    
    $status= $this->app->DB->Select("SELECT status FROM angebot WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(STATUS,"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");
     
    $angebot = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
    if($angebot<=0) $angebot="keine Nummer";
    $this->app->Tpl->Set(ANGEBOT,"<input type=\"text\" value=\"".$angebot."\" readonly>");



    $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM angebot WHERE id='$id' LIMIT 1");
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


    $abweichendelieferadresse= $this->app->DB->Select("SELECT abweichendelieferadresse FROM angebot WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST("abweichendelieferadresse")!="") $versandart = $this->app->Secure->GetPOST("abweichendelieferadresse");
    $this->app->Tpl->Set(ABWEICHENDELIEFERADRESSESTYLE,"none");
    if($abweichendelieferadresse=="1") $this->app->Tpl->Set(ABWEICHENDELIEFERADRESSESTYLE,"");

 
    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    parent::AngebotEdit();


/*
    if($this->app->Secure->GetPOST("speichern")!="" && $storno=="")
    {

			if($this->app->Secure->GetGET("msg")=="")
			{
				$msg = $this->app->Tpl->Get(MESSAGE)." ";
				$msg = $this->app->erp->base64_url_encode($msg);
			} else {
				$msg = $this->app->Secure->GetGET("msg");
        //$msg = $this->app->erp->base64_url_encode($msg);
			}
      header("Location: index.php?module=angebot&action=edit&id=$id&msg=$msg");
      exit;
    } 
*/
		$this->app->erp->MessageHandlerStandardForm();

/*
    $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position
      WHERE angebot='$id'");

    $waehrung = $this->app->DB->Select("SELECT waehrung FROM angebot_position
      WHERE angebot='$id' LIMIT 1");

    $ust_befreit_check = $this->app->DB->Select("SELECT ust_befreit FROM angebot WHERE id='$id' LIMIT 1");
    $summebrutto  = $summe *1.19;

    if($ust_befreit_check==0)
      $tmp = "Kunde zahlt mit UST";
    else if($ust_befreit_check==1)
      $tmp = "Kunde ist UST befreit";
    else
      $tmp = "Kunde zahlt keine UST";


    if($summe > 0)
      $this->app->Tpl->Add(POSITIONEN, "<br><center>Zu zahlen: <b>$summe (netto) $summebrutto (brutto) $waehrung</b> ($tmp)&nbsp;&nbsp;");

*/
    if($this->app->Secure->GetPOST("weiter")!="")
    {
      header("Location: index.php?module=angebot&action=positionen&id=$id");
      exit;
    }
    $this->AngebotMenu();

  }

  function AngebotCreate()
  {
    //$this->app->Tpl->Add(TABS,"<li><h2>Angebot</h2></li>");

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Angebot anlegen");
   $this->app->erp->MenuEintrag("index.php?module=angebot&action=list","Zur&uuml;ck zur &Uuml;bersicht");


    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      header("Location: index.php?module=angebot&action=create&anlegen=1");
      exit;
    }


    if($anlegen != "")
    {
      $id = $this->app->erp->CreateAngebot();
      $this->app->erp->AngebotProtokoll($id,"Angebot angelegt");
      header("Location: index.php?module=angebot&action=edit&id=$id");
      exit;
    }
    $this->app->Tpl->Set(MESSAGE,"<div class=\"warning\">M&ouml;chten Sie eine Angebot jetzt anlegen? &nbsp;
      <input type=\"button\" onclick=\"window.location.href='index.php?module=angebot&action=create&anlegen=1'\" value=\"Ja - Angebot jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set(TAB1,"
     <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
<tr>
<td align=\"center\">
<br><b style=\"font-size: 14pt\">Angebote in Bearbeitung</b>
<br>
<br>
Offene Angebote, die durch andere Mitarbeiter in Bearbeitung sind.
<br>
</td>
</tr>
</table>
<br>
      [ANGEBOTE]");


    $this->app->Tpl->Set(AKTIV_TAB1,"selected");

		$this->app->YUI->TableSearch(ANGEBOTE,"angeboteinbearbeitung");
/*
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as beleg, name, vertrieb, status, id
      FROM angebot WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew(ANGEBOTE, "<a href=\"index.php?module=angebot&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=angebot&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
*/

    $this->app->Tpl->Set(TABTEXT,"Angebot anlegen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
    //parent::AngebotCreate();
  }


  function AngebotList()
  {

//    $this->app->Tpl->Set(UEBERSCHRIFT,"Angebote");
//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Angebote");


    $backurl = $this->app->Secure->GetGET("backurl");
    $msg = $this->app->Secure->GetGET("msg");
    $backurl = $this->app->erp->base64_url_decode($backurl);
 
    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE2]\">Allgemein</h2></li>");
    $this->app->erp->MenuEintrag("index.php?module=angebot&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=angebot&action=create","Neues Angebot anlegen");
     
    if(strlen($backurl)>5)
      $this->app->erp->MenuEintrag("$backurl","Zur&uuml;ck zur &Uuml;bersicht");
    else
      $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");


    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    $this->app->Tpl->Set(INHALT,"");

    $this->app->YUI->TableSearch(TAB2,"angeboteoffene");
    $this->app->YUI->TableSearch(TAB1,"angebote");
    $this->app->YUI->TableSearch(TAB3,"angeboteinbearbeitung");

    $this->app->Tpl->Parse(PAGE,"angebotuebersicht.tpl");

    return;

/*
    // suche
    $sql = $this->app->erp->AngebotSuche();

    // offene Angeboten
    $this->app->Tpl->Set(SUBSUBHEADING,"Offene Angebote");

    $table = new EasyTable($this->app);
    $table->Query($sql,$_SESSION[angebottreffer]);

    //$table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Angebot, a.name, p.abkuerzung as projekt, a.id
    //  FROM angebot a, projekt p WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt order by a.datum DESC, a.id DESC",10);


    $table->DisplayOwn(INHALT, "<a href=\"index.php?module=angebot&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Weiterf&uuml;fhren als Auftrag?')) return false; else window.location.href='index.php?module=angebot&action=auftrag&id=%value%';\">
        <img src=\"./themes/new/images/right.png\" border=\"0\"></a>

        ");
    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

    $this->app->Tpl->Set(INHALT,"");
    // wartende Angeboten

    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Angebot, ad.kundennummer as kunde, a.name, p.abkuerzung as projekt, a.id
      FROM angebot a, projekt p, adresse ad WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt AND a.adresse=ad.id order by a.datum DESC, a.id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=angebot&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
    $this->app->Tpl->Parse(TAB2,"rahmen70.tpl");


    $this->app->Tpl->Set(INHALT,"");
    // In Bearbeitung
    $this->app->Tpl->Set(SUBSUBHEADING,"In Bearbeitung");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as auftrag, name, vertrieb, status, id
      FROM angebot WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=angebot&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=angebot&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=angebot&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");

    $this->app->Tpl->Parse(TAB3,"rahmen70.tpl");
*/


/*
    $this->app->Tpl->Set(TAB2,"lieferant, angebot, waehrung, sprache, liefertermin, steuersatz, einkäufer, freigabe<br>
<br>Angebot (NR),Bestellart (NB), Bestelldatum
<br>Projekt
<br>Kostenstelle pro Position
<br>Terminangebot (am xx.xx.xxxx raus damit)
<br>vorschlagsdaten für positionen
<br>proposition reinklicken zum ändern und reihenfolge tabelle 
<br>Angebot muss werden wie angebot (angebot beschreibung = allgemein)
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

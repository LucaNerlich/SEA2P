<?php
include ("_gen/bestellung.php");

class Bestellung extends GenBestellung
{

  function Bestellung(&$app)
  {
    $this->app=&$app; 
  
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","BestellungList");
    $this->app->ActionHandler("create","BestellungCreate");
    $this->app->ActionHandler("positionen","BestellungPositionen");
    $this->app->ActionHandler("addposition","BestellungAddPosition");
    $this->app->ActionHandler("upbestellungposition","UpBestellungPosition");
    $this->app->ActionHandler("delbestellungposition","DelBestellungPosition");
    $this->app->ActionHandler("downbestellungposition","DownBestellungPosition");
    $this->app->ActionHandler("positioneneditpopup","BestellungPositionenEditPopup");
    $this->app->ActionHandler("edit","BestellungEdit");
    $this->app->ActionHandler("copy","BestellungCopy");
    $this->app->ActionHandler("auftrag","BestellungAuftrag");
    $this->app->ActionHandler("delete","BestellungDelete");
    $this->app->ActionHandler("freigabe","BestellungFreigabe");
    $this->app->ActionHandler("abschicken","BestellungAbschicken");
    $this->app->ActionHandler("dateien","BestellungDateien");
    $this->app->ActionHandler("pdf","BestellungPDF");
    $this->app->ActionHandler("inlinepdf","BestellungInlinePDF");
    $this->app->ActionHandler("protokoll","BestellungProtokoll");
    $this->app->ActionHandler("minidetail","BestellungMiniDetail");
    $this->app->ActionHandler("editable","BestellungEditable");
    $this->app->ActionHandler("livetabelle","BestellungLiveTabelle");
    $this->app->ActionHandler("schreibschutz","BestellungSchreibschutz");
    $this->app->ActionHandler("abschliessen","BestellungAbschliessen");
  
    $this->app->DefaultActionHandler("list");
 
    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("adresse");

    if($nummer=="")
      $adresse= $this->app->DB->Select("SELECT a.name FROM bestellung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    else
      $adresse = $nummer;

    $nummer = $this->app->DB->Select("SELECT b.belegnr FROM bestellung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    if($nummer=="" || $nummer==0) $nummer="ohne Nummer";

    $this->app->Tpl->Set(UEBERSCHRIFT,"Bestellung:&nbsp;".$adresse." (".$nummer.")");
    $this->app->Tpl->Set(FARBE,"[FARBE2]");
    
    $this->app->ActionHandlerListen($app);
  }

  function BestellungEditable()
  {
    $this->app->YUI->AARLGEditable();
  }

  function BestellungSchreibschutz()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE bestellung SET schreibschutz='0' WHERE id='$id'");
    header("Location: index.php?module=bestellung&action=edit&id=$id");
    exit;
  }


  function BestellungMiniDetail($parsetarget="",$menu=true)
  {
    $id = $this->app->Secure->GetGET("id");
 


		$belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM bestellung WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");
    $verbindlichkeiteninfo = $this->app->DB->Select("SELECT verbindlichkeiteninfo FROM bestellung WHERE id='$id' LIMIT 1");
		$this->app->Tpl->Set(VERBINDLICHKEITENINFO,$verbindlichkeiteninfo);

    if($belegnr<=0) $belegnr = "ENTWURF";

		$this->app->Tpl->Set(BELEGNR,$belegnr);
		$this->app->Tpl->Set(LIEFERANT,$name);
		$this->app->Tpl->Set(STATUS,$status);
		$this->app->Tpl->Set(BESTELLUNGID,$id);

    $table = new EasyTable($this->app);

    $table->Query("SELECT SUBSTRING(ap.bezeichnunglieferant,1,20) as artikel, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\"  target=\"_blank\">',a.nummer,'</a>') as nummer, ap.menge as Menge,
			ap.geliefert,if(ap.lieferdatum!='0000-00-00',DATE_FORMAT(ap.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum,ap.preis as preis
      FROM bestellung_position ap, artikel a WHERE ap.bestellung='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","Preis","noAction");


    $this->app->Tpl->Set(ARTIKEL,$artikel);

		if($this->app->erp->RechteVorhanden("verbindlichkeit","list"))
		{
			$this->app->Tpl->Add(ARTIKEL,"<br><div align=left><ul>");
			$arr_verb = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(rechnungsdatum,'%d.%m.%Y') as datum2 FROM verbindlichkeit WHERE bestellung='$id'");
			for($arr_verb_i=0;$arr_verb_i<count($arr_verb);$arr_verb_i++)
			{
				$gesamtsumme = $gesamtsumme + $arr_verb[$arr_verb_i][betrag];
				if($arr_verb[$arr_verb_i][freigabe]=="1") $label = "abgeschlossen"; else $label="<font color=red>Bitte Zahlung freigeben</font>";

				if($arr_verb[$arr_verb_i][rechnung]!="") $arr_verb[$arr_verb_i][rechnung] = "(".$arr_verb[$arr_verb_i][rechnung].")";
				$this->app->Tpl->Add(ARTIKEL,"<li>Verbindlichkeit Nr. ".$arr_verb[$arr_verb_i][id]." ".$arr_verb[$arr_verb_i][rechnung]." ".$arr_verb[$arr_verb_i][betrag]." ".$arr_verb[$arr_verb_i][waehrung]." vom ".$arr_verb[$arr_verb_i][datum2]." | <a href=\"index.php?module=verbindlichkeit&action=edit&id=".$arr_verb[$arr_verb_i][id]."\" target=\"_blank\">$label</a></li>");
			}


			$alleverbindlichkeiten = $this->app->erp->BestellungErweiterteVerbindlichkeiten($id);

			$gesamtsummesammel=0;

			if(count($alleverbindlichkeiten)>0)
			{
				foreach($alleverbindlichkeiten as $key=>$value)
				{
					$datum = $this->app->DB->Select("SELECT DATE_FORMAT(rechnungsdatum,'%d.%m.%Y') FROM verbindlichkeit WHERE id='".$key."' LIMIT 1");
					$freigabe = $this->app->DB->Select("SELECT freigabe FROM verbindlichkeit WHERE id='".$key."' LIMIT 1");

					if($freigabe) $label = "abgeschlossen"; else $label="<font color=red>Bitte Zahlung freigeben</font>";

					$this->app->Tpl->Add(ARTIKEL,"<li>Sammelverbindlichkeit Nr. ".$key." Teilbetrag: ".$value." EUR vom ".$datum." | <a href=\"index.php?module=verbindlichkeit&action=edit&id=".$key."\" target=\"_blank\">$label</a></li>");

					$gesamtsummesammel = $gesamtsummesammel + $value;
				}
			}

			$gesamtbetrag_bestellung = $this->app->DB->Select("SELECT gesamtsumme FROM bestellung WHERE id='$id' LIMIT 1");
			$differenz = $gesamtbetrag_bestellung - $gesamtsumme - $gesamtsummesammel;
			$differenz = round($differenz,2);

			if($differenz > 0) $differenz = "<font color=blue>Differenz: ".number_format($differenz,2,',','.')." ".$arr_verb[$arr_verb_i-1][waehrung]."</font>";
			else if($differenz < 0) $differenz = "<font color=red>Differenz: ".number_format($differenz,2,',','.')." ".$arr_verb[$arr_verb_i-1][waehrung]."</font>";
			else $differenz="Differenz: 0,00";

			$this->app->Tpl->Add(ARTIKEL,"</ul></div>");

			$gesamtsumme = $gesamtsumme + $gesamtsummesammel;

			if($gesamtsumme > 0)
			{
				$gesamtsumme = number_format($gesamtsumme,2,",",".");
				$this->app->Tpl->Add(ARTIKEL,"<center>Gesamtsumme: $gesamtsumme ".$arr_verb[$arr_verb_i-1][waehrung]." | $differenz</center>");
			}

			$this->app->Tpl->Add(ARTIKEL,"<br><center><input type=\"button\" onclick=\"window.open('index.php?module=verbindlichkeit&action=createbestellung&id=$id')\" value=\"Verbindlichkeit anlegen\" />&nbsp;<input type=\"button\" value=\"Bestellung abschliessen\" onclick=\"window.open('index.php?module=bestellung&action=abschliessen&id=$id')\"></center>");
		}

    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM bestellung_protokoll WHERE bestellung='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(PROTOKOLL,"Protokoll","noAction");


    if($parsetarget=="")
    {
      $this->app->Tpl->Output("bestellung_minidetail.tpl");
      exit;
    }  else {
      $this->app->Tpl->Parse($parsetarget,"bestellung_minidetail.tpl");
    }
  }


	function BestellungAbschliessen()
	{
    $id = $this->app->Secure->GetGET("id");

		if($id > 0)
		{
      $this->app->DB->Update("UPDATE bestellung SET status='abgeschlossen' WHERE id='$id' LIMIT 1");
      $this->app->DB->Update("UPDATE verbindlichkeit SET freigabe='1' WHERE bestellung='$id'");
      $this->app->erp->BestellungProtokoll($id,"Bestellung abgeschlossen");
		}
  	$msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Bestellung wurde als abgeschlossen markiert!</div>");
    header("Location: index.php?module=bestellung&action=list&msg=$msg");
    exit;
	}

  function BestellungFreigabe()
  {
    $id = $this->app->Secure->GetGET("id");
    $freigabe= $this->app->Secure->GetGET("freigabe");
    $weiter= $this->app->Secure->GetPOST("weiter");
    $this->app->Tpl->Set(TABTEXT,"Freigabe");

    if($weiter!="")
    {
       header("Location: index.php?module=bestellung&action=abschicken&id=$id");
       exit;
    }

    

    if($freigabe==$id)
    {
      $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");

			if($belegnr=="")
			{
      	$belegnr = $this->app->erp->GetNextNummer("bestellung",$projekt);

      	$this->app->DB->Update("UPDATE bestellung SET belegnr='$belegnr', status='freigegeben', einkaeufer='".$this->app->User->GetDescription()."' WHERE id='$id' LIMIT 1");
      	$this->app->erp->BestellungProtokoll($id,"Bestellung freigegeben");
      	$msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Das Bestellung wurde freigegeben und kann jetzt versendet werden!</div>");
      	header("Location: index.php?module=bestellung&action=edit&id=$id&msg=$msg");
      	exit;
			} else {
				$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Bestellung wurde bereits freigegeben!</div>");
      	header("Location: index.php?module=bestellung&action=edit&id=$id&msg=$msg");
      	exit;
			}

    } else { 

      $name = $this->app->DB->Select("SELECT a.name FROM bestellung b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position
	WHERE bestellung='$id'");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM bestellung_position
	WHERE bestellung='$id' LIMIT 1");

      $summe = $this->app->erp->EUR($summe);

      $this->app->Tpl->Set(TAB1,"<div class=\"info\">Soll die Bestellung an <b>$name</b> im Wert von <b>$summe $waehrung</b> 
	jetzt freigegeben werden? <input type=\"button\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=bestellung&action=freigabe&id=$id&freigabe=$id'\">
	</div>");
    }
    $this->BestellungMenu();
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function BestellungCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->CopyBestellung($id);

    header("Location: index.php?module=bestellung&action=edit&id=$newid");
    exit;
  }


  function BestellungLiveTabelle()
  {
    $id = $this->app->Secure->GetGET("id");
    $status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
    $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M,
      if(a.porto,'-',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
      if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
      '<font color=red><b>aus</b></font>'))) as L
      FROM bestellung_position ap, artikel a WHERE ap.bestellung='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","A","noAction");
    } else {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M
      FROM bestellung_position ap, artikel a WHERE ap.bestellung='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","Menge","noAction");
    }
    echo $artikel;
    exit;
  }


  function BestellungAuftrag()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->WeiterfuehrenBestellungZuAuftrag($id);

    header("Location: index.php?module=auftrag&action=edit&id=$newid");
    exit;
  }


  function BestellungAbschicken()
  {
    $this->BestellungMenu();
    $this->app->erp->DokumentAbschicken();
  }

  function BestellungDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM bestellung WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");

    if($belegnr==0 || $belegnr=="")
    {

      $this->app->erp->DeleteBestellung($id);
      if($belegnr<=0) $belegnr="ENTWURF";
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Bestellung \"$name\" ($belegnr) wurde gel&ouml;scht!</div>");
      //header("Location: ".$_SERVER['HTTP_REFERER']."&msg=$msg");
      header("Location: index.php?module=bestellung&action=list&msg=$msg");
      exit;
    } else
    {

      if($status=="storniert")
      {
        $maxbelegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM bestellung");
        if(0)//$maxbelegnr == $belegnr)
        {
          $this->app->DB->Delete("DELETE FROM bestellung_position WHERE bestellung='$id'");
          $this->app->DB->Delete("DELETE FROM bestellung_protokoll WHERE bestellung='$id'");
          $this->app->DB->Delete("DELETE FROM bestellung WHERE id='$id'");
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rechnung \"$name\" ($belegnr) wurde ge&ouml;scht !</div>");
        } else
        {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Bestellung \"$name\" ($belegnr) kann nicht storniert werden das sie er bereits storniert ist!</div>");
        }
        header("Location: index.php?module=bestellung&action=list&msg=$msg");
        exit;
      }

      else {
        $this->app->DB->Update("UPDATE bestellung SET status='storniert' WHERE id='$id' LIMIT 1");
        $this->app->erp->BestellungProtokoll($id,"Bestellung storniert");
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Das Bestellung \"$name\" ($belegnr) wurde storniert!</div>");                                                                              
      }
      //$msg = $this->app->erp->base64_url_encode("<div class=\"error\">Bestellung \"$name\" ($belegnr) kann nicht storniert werden, da es bereits versendet wurde!</div>");
      header("Location: index.php?module=bestellung&action=list&msg=$msg#tabs-1");
      exit;
    }

  }

  function BestellungProtokoll()
  {
    $this->BestellungMenu();
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set(TABTEXT,"Protokoll");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM bestellung_protokoll WHERE bestellung='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(TAB1,"Protokoll","noAction");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function BestellungAddPosition()
  {
    $sid = $this->app->Secure->GetGET("sid");
    $id = $this->app->Secure->GetGET("id");
    $menge = $this->app->Secure->GetGET("menge");
    $datum  = $this->app->Secure->GetGET("datum");
    $datum  = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
    $this->app->erp->AddBestellungPosition($id, $sid,$menge,$datum);
    $this->app->erp->BestellungNeuberechnen($id);

    header("Location: index.php?module=bestellung&action=positionen&id=$id");
    exit;
 
  }

  function BestellungInlinePDF()
  {
    $id = $this->app->Secure->GetGET("id");

    $frame = $this->app->Secure->GetGET("frame");
    $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$id' LIMIT 1");


    if($frame=="")
    {
      $Brief = new BestellungPDF($this->app,$projekt);
      $Brief->GetBestellung($id);
      $Brief->inlineDocument();
    } else {
      $file = urlencode("../../../../index.php?module=bestellung&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"600\" src=\"./js/production/generic/web/viewer.html?file=$file\" frameborder=\"0\"></iframe>";
      exit;
    }
 }

  function BestellungPDF()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$id' LIMIT 1");

//    if(is_numeric($belegnr) && $belegnr!=0)
    {
      $Brief = new BestellungPDF($this->app,$projekt);
      $Brief->GetBestellung($id);
      $Brief->displayDocument(); 
    } //else
 //     $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Bestellungen k&ouml;nnen nicht als PDF betrachtet werden.!</div>");


    $this->BestellungList();
 }


  function BestellungMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM bestellung WHERE id='$id' LIMIT 1");

    $this->app->erp->BestellungNeuberechnen($id);


    if($belegnr<=0) $belegnr ="(Entwurf)";
//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Bestellung $belegnr");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,"$name Bestellung $belegnr");
/* 
    $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE2]\">Allgemein</h2></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=bestellung&action=create\">Neues Bestellung anlegen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=bestellung&action=list\">Bestellung suchen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=bestellung&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");
    $this->app->Tpl->Add(TABS,"<li><br><br></li>");
*/

    
    //$this->app->Tpl->Add(TABS,"<li><h2 style=\"background-color: [FARBE2]\">Bestellung</h2></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=bestellung&action=positionen&id=$id\">Positionen</a></li>");

    // status bestell
    $status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");
   	if($status=="angelegt")
    {
      $this->app->erp->MenuEintrag("index.php?module=bestellung&action=freigabe&id=$id","Freigabe");
    }

    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=edit&id=$id","Details");

    if($status=='bestellt')
    { 
      $this->app->erp->MenuEintrag("index.php?module=bestellung&action=wareneingang&id=$id","Wareneingang<br>R&uuml;ckst&auml;nde");
      $this->app->erp->MenuEintrag("index.php?module=bestellung&action=wareneingang&id=$id","Mahnstufen");
    } 
	//    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=abschicken&id=$id","Abschicken / Protokoll");
//    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=protokoll&id=$id","Protokoll");

    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=dateien&id=$id","Dateien");


		if($this->app->Secure->GetGET("action")=="abschicken")
    	$this->app->erp->MenuEintrag("index.php?module=bestellung&action=edit&id=$id","Zur&uuml;ck zur Bestellung");
		else
    	$this->app->erp->MenuEintrag("index.php?module=bestellung&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->Tpl->Parse(MENU,"bestellung_menu.tpl");

  }


  function BestellungDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->BestellungMenu();
    $this->app->Tpl->Add(UEBERSCHRIFT," (Dateien)");
    $this->app->YUI->DateiUpload(PAGE,"Bestellung",$id);
  }



  function BestellungPositionen()
  {

    $id = $this->app->Secure->GetGET("id");

    $this->app->erp->AuftragNeuberechnen($id);
    $this->app->YUI->AARLGPositionen(false);

    return;


    $this->BestellungMenu();
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


    $bestellungsart = $this->app->DB->Select("SELECT bestellungsart FROM bestellung WHERE id='$id' LIMIT 1");
    $lieferant  = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");

    $anlegen_artikelneu = $this->app->Secure->GetPOST("anlegen_artikelneu");

    if($anlegen_artikelneu!="")
    {

      if($bezeichnung!="" && $menge!="" && $preis!="")
      {
	$sort = $this->app->DB->Select("SELECT MAX(sort) FROM bestellung_position WHERE bestellung='$id' LIMIT 1");
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

	$this->app->DB->Insert("INSERT INTO bestellung_position (id,bestellung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
	  VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");

	header("Location: index.php?module=bestellung&action=positionen&id=$id");
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
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM bestellung_position WHERE auftrag='$id' LIMIT 1");
      $sort = $sort + 1;
      $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $bezeichnung = $artikel;
      $neue_nummer = $nummer;
      $waehrung = 'EUR';
      $umsatzsteuerklasse = $this->app->DB->Select("SELECT umsatzsteuerklasse FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $vpe = 'einzeln';

        $this->app->DB->Insert("INSERT INTO bestellung_position (id,bestellung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
    }
    $weiter = $this->app->Secure->GetPOST("weiter");
    if($weiter!="")
    {
       header("Location: index.php?module=bestellung&action=freigabe&id=$id");
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
      $this->app->Tpl->Parse(INHALT,"bestellung_artikelneu.tpl");
      $this->app->Tpl->Set(EXTEND,"<input type=\"submit\" value=\"Artikel unter Stammdaten anlegen\" name=\"anlegen_artikelneu\">");
      $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
      $this->app->Tpl->Set(EXTEND,"");
      $this->app->Tpl->Set(INHALT,"");

      /* ende neu anlegen formular */


      $this->app->Tpl->Set(SUBSUBHEADING,"Artikelstamm");

      $lieferant = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");

      $table = new EasyTable($this->app);
      $table->Query("SELECT CONCAT(LEFT(a.name_de,80),'...') as artikel, a.nummer, 
	      v.ab_menge as ab, v.preis, p.abkuerzung as projekt,
	      CONCAT('<input type=\"text\" size=\"8\" value=\"00.00.0000\" id=\"datum',v.id,'\">
	      <img src=\"./themes/new/images/kalender.png\" height=\"12\" onclick=\"displayCalendar(document.forms[1].datum',v.id,',\'dd.mm.yyyy\',this)\" border=0 align=right>') as Lieferdatum, 
	      CONCAT('<input type=\"text\" size=\"3\" value=\"\" id=\"menge',v.id,'\">') as menge, v.id as id
	      FROM artikel a LEFT JOIN verkaufspreise v ON a.id=v.artikel LEFT JOIN projekt p ON v.projekt=p.id WHERE v.ab_menge>=1");
      $table->DisplayNew(INHALT, "<input type=\"button\" 
	      onclick=\"document.location.href='index.php?module=bestellung&action=addposition&id=$id&sid=%value%&menge=' + document.getElementById('menge%value%').value + '&datum=' + document.getElementById('datum%value%').value;\" value=\"anlegen\">");
      $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
      $this->app->Tpl->Set(INHALT,"");

	    // child table einfuegen

      $this->app->Tpl->Set(SUBSUBHEADING,"Positionen");
      $menu = array("up"=>"upbestellungposition",
			  "down"=>"downbestellungposition",
			  //"add"=>"addstueckliste",
			  "edit"=>"positioneneditpopup",
			  "del"=>"delbestellungposition");

      $sql = "SELECT a.name_de as Artikel, p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.id as id
		FROM bestellung_position b
		LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id 
		WHERE b.bestellung='$id'";

//      $this->app->Tpl->Add(EXTEND,"<input type=\"submit\" value=\"Gleiche Positionen zusammenf&uuml;gen\">");

      $this->app->YUI->SortListAdd(INHALT,$this,$menu,$sql);
      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

      if($anlegen_artikelneu!="")
	$this->app->Tpl->Set(AKTIV_TAB2,"selected");
      else
	$this->app->Tpl->Set(AKTIV_TAB1,"selected");
      $this->app->Tpl->Parse(PAGE,"bestellung_positionuebersicht.tpl");
    } 
  }

  function DelBestellungPosition()
  {
    $this->app->YUI->SortListEvent("del","bestellung_position","bestellung");
    $this->BestellungPositionen();
  }

  function UpBestellungPosition()
  {
    $this->app->YUI->SortListEvent("up","bestellung_position","bestellung");
    $this->BestellungPositionen();
  }

  function DownBestellungPosition()
  {
    $this->app->YUI->SortListEvent("down","bestellung_position","bestellung");
    $this->BestellungPositionen();
  }


  function BestellungPositionenEditPopup()
  {
   $id = $this->app->Secure->GetGET("id");

    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetBestellung_position($this->app,PAGE);
    $sid= $this->app->DB->Select("SELECT bestellung FROM bestellung_position WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=bestellung&action=positionen&id=$sid");
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }



  function BestellungIconMenu($id,$prefix="")
  { 
 		$status = $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");

    if($status=="angelegt" || $status=="")
      $freigabe = "<option value=\"freigabe\">Bestellung freigeben</option>";

		if($status=="versendet")
			$abschliessen = "<option value=\"abschliessen\">Bestellung abschliessen</option>";

    $menu ="

  <script type=\"text/javascript\">
  function onchangebestellung(cmd)
  {
    switch(cmd)
    {
			case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=delete&id=%value%'; break;
			case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=copy&id=%value%'; break;
			case 'pdf': window.location.href='index.php?module=bestellung&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
			case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
			case 'freigabe': window.location.href='index.php?module=bestellung&action=freigabe&id=%value%'; break;
			case 'abschliessen': if(!confirm('Wirklich abschliessen?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=bestellung&action=abschliessen&id=%value%'; break;
    }
		
  }
    </script>

&nbsp;Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangebestellung(this.value);\">
<option>bitte w&auml;hlen ...</option>
<option value=\"storno\">Bestellung stornieren</option>
<option value=\"copy\">Bestellung kopieren</option>
$freigabe
<option value=\"abschicken\">Bestellung abschicken</option>
$abschliessen
<option value=\"pdf\">PDF &ouml;ffnen</option>
</select>&nbsp;

        <a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
<!--        <a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich stornieren?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
     <a onclick=\"if(!confirm('Wirklich Auftrag abschicken?')) return false; else window.location.href='index.php?module=bestellung&action=abschicken&id=%value%';\">
        <img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"Auftrag abeschicken\"></a>-->";

      //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }


  function BestellungEdit()
  {
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->BestellungNeuberechnen($id);

   	if($this->app->erp->DisableModul("bestellung",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->BestellungMenu();
      return;
    }

    $this->app->YUI->AARLGPositionen();

    //$storno = $this->app->Secure->GetGET("storno");




    //$this->BestellungMiniDetail(MINIDETAIL,false);


    $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $nummer = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
    $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1");

  	$status= $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");
    $schreibschutz= $this->app->DB->Select("SELECT schreibschutz FROM bestellung WHERE id='$id' LIMIT 1");


		if($schreibschutz!="1" && $this->app->erp->RechteVorhanden("bestellung","schreibschutz"))
    	$this->app->erp->AnsprechpartnerButton($adresse);


    $this->app->Tpl->Set(ICONMENU,$this->BestellungIconMenu($id));
    $this->app->Tpl->Set(ICONMENU2,$this->BestellungIconMenu($id,2));


    if($nummer>0)
    {
      $this->app->Tpl->Set(NUMMER,$nummer);
      $this->app->Tpl->Set(KUNDE,"&nbsp;&nbsp;&nbsp;Kd-Nr.".$lieferantennummer);
    }


    if($this->app->Secure->GetPOST("speichern")!="")
    {
      $abweichenderechnungsadresse = $this->app->Secure->GetPOST("abweichenderechnungsadresse");
      $abweichendelieferdresse = $this->app->Secure->GetPOST("abweichendelieferadresse");
    } else {
      $abweichenderechnungsadresse = $this->app->DB->Select("SELECT abweichenderechnungsadresse FROM bestellung WHERE id='$id' LIMIT 1");
      $abweichendelieferadresse = $this->app->DB->Select("SELECT abweichendelieferadresse FROM bestellung WHERE id='$id' LIMIT 1");
    }
    if($abweichenderechnungsadresse) $this->app->Tpl->Set(RECHNUNGSADRESSE,"visible"); else $this->app->Tpl->Set(RECHNUNGSADRESSE,"none");
    if($abweichendelieferadresse) $this->app->Tpl->Set(LIEFERADRESSE,"visible"); else $this->app->Tpl->Set(LIEFERADRESSE,"none");

    if(!is_numeric($belegnr) || $belegnr==0)
    {
    $this->app->Tpl->Set(LOESCHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=$id';\">");
    }
    $status= $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");
    if($status=="")
      $this->app->DB->Update("UPDATE bestellung SET status='angelegt' WHERE id='$id' LIMIT 1");

    if($schreibschutz=="1" && $this->app->erp->RechteVorhanden("bestellung","schreibschutz"))
    {
      $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Diese Bestellung wurde bereits versendet und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml; diese Bestellung wirklich entfernt werden?')) return false;else window.location.href='index.php?module=bestellung&action=schreibschutz&id=$id';\"></div>");
//      $this->app->erp->CommonReadonly();
    }
    if($schreibschutz=="1")
      $this->app->erp->CommonReadonly();

    if($this->app->erp->Firmendaten("schnellanlegen")=="1")
    {
    $this->app->Tpl->Set(BUTTON_UEBERNEHMEN,'      <input type="button" value="&uuml;bernehmen" onclick="document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
    ');
    } else {
      $this->app->Tpl->Set(BUTTON_UEBERNEHMEN,'
      <input type="button" value="&uuml;bernehmen" onclick="if(!confirm(\'Soll der neue Lieferant wirklich &uuml;bernommen werden? Es werden alle Felder &uuml;berladen.\')) return false;else document.getElementById(\'uebernehmen\').value=1; document.getElementById(\'eprooform\').submit();"/><input type="hidden" id="uebernehmen" name="uebernehmen" value="0">
    ');
    }


    // immer wenn sich der lieferant genändert hat standartwerte setzen
    if($this->app->Secure->GetPOST("adresse")!="")
    {
      $tmp = $this->app->Secure->GetPOST("adresse");
      $lieferantennummer = $this->app->erp->FirstTillSpace($tmp);

      $name = substr($tmp,6);
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$lieferantennummer'  AND geloescht=0 LIMIT 1");

      $uebernehmen =$this->app->Secure->GetPOST("uebernehmen");
      if($uebernehmen=="1") // nur neuladen bei tastendruck auf uebernehmen // FRAGEN!!!!
      {
	  		$this->app->erp->LoadBestellungStandardwerte($id,$adresse);
        header("Location: index.php?module=bestellung&action=edit&id=$id");
        exit;
      }
    }


   
/* 
    $table = new EasyTable($this->app);
        $table->Query("SELECT a.bezeichnung as artikel, a.nummer as Nummer, b.menge, b.vpe as VPE, FORMAT(b.preis,4) as preis
      FROM bestellung_position b LEFT JOIN artikel a ON a.id=b.artikel
      WHERE b.bestellung='$id'");
    $table->DisplayNew(POSITIONEN,"Preis","noAction");
*/
    $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM bestellung WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(BEARBEITER,"<input type=\"text\" value=\"".$this->app->erp->GetAdressName($bearbeiter)."\" readonly>");

    
    $status= $this->app->DB->Select("SELECT status FROM bestellung WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(STATUS,"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

     
    $bestellung = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
    if($bestellung<=0) $bestellung="keine Nummer";
    $this->app->Tpl->Set(ANGEBOT,"<input type=\"text\" value=\"".$bestellung."\" readonly>");



    $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM bestellung WHERE id='$id' LIMIT 1");
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


    $abweichendelieferadresse= $this->app->DB->Select("SELECT abweichendelieferadresse FROM bestellung WHERE id='$id' LIMIT 1");
    if($this->app->Secure->GetPOST("abweichendelieferadresse")!="") $versandart = $this->app->Secure->GetPOST("abweichendelieferadresse");
    $this->app->Tpl->Set(ABWEICHENDELIEFERADRESSESTYLE,"none");
    if($abweichendelieferadresse=="1") $this->app->Tpl->Set(ABWEICHENDELIEFERADRESSESTYLE,"");

 
    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    parent::BestellungEdit();

		$this->app->erp->MessageHandlerStandardForm();	
/*
    if($this->app->Secure->GetPOST("speichern")!="" && $storno=="")
    {
			if($this->app->Secure->GetGET("msg")=="")
      {
        $msg = $this->app->Tpl->Get(MESSAGE);
        $msg = $this->app->erp->base64_url_encode($msg);
      } else {
        $msg = $this->app->erp->base64_url_encode($msg);
      }

      header("Location: index.php?module=bestellung&action=edit&id=$id&msg=$msg");
      exit;

    } 
*/

/*
    $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position
      WHERE bestellung='$id'");

    $waehrung = $this->app->DB->Select("SELECT waehrung FROM bestellung_position
      WHERE bestellung='$id' LIMIT 1");

    $ust_befreit_check = $this->app->DB->Select("SELECT ust_befreit FROM bestellung WHERE id='$id' LIMIT 1");
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
      header("Location: index.php?module=bestellung&action=positionen&id=$id");
      exit;
    }
    $this->BestellungMenu();

  }

  function BestellungCreate()
  {
    //$this->app->Tpl->Add(TABS,"<li><h2>Bestellung</h2></li>");

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Bestellung anlegen");
   $this->app->erp->MenuEintrag("index.php?module=bestellung&action=list","Zur&uuml;ck zur &Uuml;bersicht");


    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      header("Location: index.php?module=bestellung&action=create&anlegen=1");
      exit;
    }

    if($anlegen != "")
    {
      $id = $this->app->erp->CreateBestellung();
      $this->app->erp->BestellungProtokoll($id,"Bestellung angelegt");
      header("Location: index.php?module=bestellung&action=edit&id=$id");
      exit;
    }
    $this->app->Tpl->Set(MESSAGE,"<div class=\"warning\">M&ouml;chten Sie eine Bestellung jetzt anlegen? &nbsp;
      <input type=\"button\" onclick=\"window.location.href='index.php?module=bestellung&action=create&anlegen=1'\" value=\"Ja - Bestellung jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set(TAB1,"
     <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
<tr>
<td align=\"center\">
<br><b style=\"font-size: 14pt\">Bestellungen in Bearbeitung</b>
<br>
<br>
Offene Bestellunge, die durch andere Mitarbeiter in Bearbeitung sind.
<br>
</td>
</tr>
</table>
<br>
      [ANGEBOTE]");


    $this->app->Tpl->Set(AKTIV_TAB1,"selected");

	  $this->app->YUI->TableSearch(ANGEBOTE,"bestellungeninbearbeitung");
/*
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as beleg, name, status, id
      FROM bestellung WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew(ANGEBOTE, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
*/

    $this->app->Tpl->Set(TABTEXT,"Bestellung anlegen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
    //parent::BestellungCreate();
  }


  function BestellungList()
  {

//    $this->app->Tpl->Set(UEBERSCHRIFT,"Bestellungssuche");
 //   $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Bestellungssuche");
    $speichern = $this->app->Secure->GetPOST("speichern");
    $verbindlichkeiteninfo = $this->app->Secure->GetPOST("verbindlichkeiteninfo");
    $bestellungid = $this->app->Secure->GetPOST("bestellungid");

		if($verbindlichkeiteninfo!="" && $speichern!="" && $bestellungid > 0)
			$this->app->DB->Update("UPDATE bestellung SET verbindlichkeiteninfo='$verbindlichkeiteninfo' WHERE id='$bestellungid' LIMIT 1");

    $backurl = $this->app->Secure->GetGET("backurl");
    $backurl = $this->app->erp->base64_url_decode($backurl);
 
    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE2]\">Allgemein</h2></li>");
    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=bestellung&action=create","Neue Bestellung anlegen");
     
    if(strlen($backurl)>5)
      $this->app->erp->MenuEintrag("$backurl","Zur&uuml;ck zur &Uuml;bersicht");
    else
      $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");


    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    $this->app->Tpl->Set(INHALT,"");

    $this->app->YUI->TableSearch(TAB1,"bestellungen");
    $this->app->YUI->TableSearch(TAB2,"bestellungeninbearbeitung");

    $this->app->Tpl->Parse(PAGE,"bestellunguebersicht.tpl");

    return;

/*
    // suche
    $sql = $this->app->erp->BestellungSuche();

    // offene Bestellungen
    $this->app->Tpl->Set(SUBSUBHEADING,"Offene Bestellunge");

    $table = new EasyTable($this->app);
    $table->Query($sql,$_SESSION[bestellungtreffer]);

    //$table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Bestellung, a.name, p.abkuerzung as projekt, a.id
    //  FROM bestellung a, projekt p WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt order by a.datum DESC, a.id DESC",10);


    $table->DisplayOwn(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Weiterf&uuml;fhren als Auftrag?')) return false; else window.location.href='index.php?module=bestellung&action=auftrag&id=%value%';\">
        <img src=\"./themes/new/images/right.png\" border=\"0\"></a>

        ");
    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

    $this->app->Tpl->Set(INHALT,"");
    // wartende Bestellungen

    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Bestellung, ad.lieferantennummer as kunde, a.name, p.abkuerzung as projekt, a.id
      FROM bestellung a, projekt p, adresse ad WHERE (a.status='freigegeben' OR a.status='versendet') AND p.id=a.projekt AND a.adresse=ad.id order by a.datum DESC, a.id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
    $this->app->Tpl->Parse(TAB2,"rahmen70.tpl");


    $this->app->Tpl->Set(INHALT,"");
    // In Bearbeitung
    $this->app->Tpl->Set(SUBSUBHEADING,"In Bearbeitung");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as auftrag, name, vertrieb, status, id
      FROM bestellung WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=bestellung&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");

    $this->app->Tpl->Parse(TAB3,"rahmen70.tpl");
*/


/*
    $this->app->Tpl->Set(TAB2,"lieferant, bestellung, waehrung, sprache, liefertermin, steuersatz, einkäufer, freigabe<br>
<br>Bestellung (NR),Bestellart (NB), Bestelldatum
<br>Projekt
<br>Kostenstelle pro Position
<br>Terminbestellung (am xx.xx.xxxx raus damit)
<br>vorschlagsdaten für positionen
<br>proposition reinklicken zum ändern und reihenfolge tabelle 
<br>Bestellung muss werden wie bestellung (bestellung beschreibung = allgemein)
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

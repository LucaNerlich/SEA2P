<?php
include ("_gen/arbeitsnachweis.php");

class Arbeitsnachweis extends GenArbeitsnachweis
{

  function Arbeitsnachweis(&$app)
  {
    $this->app=&$app; 
  
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","ArbeitsnachweisList");
    $this->app->ActionHandler("create","ArbeitsnachweisCreate");
    $this->app->ActionHandler("positionen","ArbeitsnachweisPositionen");
    $this->app->ActionHandler("addposition","ArbeitsnachweisAddPosition");
    $this->app->ActionHandler("uparbeitsnachweisposition","UpArbeitsnachweisPosition");
    $this->app->ActionHandler("delarbeitsnachweisposition","DelArbeitsnachweisPosition");
    $this->app->ActionHandler("downarbeitsnachweisposition","DownArbeitsnachweisPosition");
    $this->app->ActionHandler("positioneneditpopup","ArbeitsnachweisPositionenEditPopup");
    $this->app->ActionHandler("edit","ArbeitsnachweisEdit");
    $this->app->ActionHandler("copy","ArbeitsnachweisCopy");
    $this->app->ActionHandler("delete","ArbeitsnachweisDelete");
    $this->app->ActionHandler("freigabe","ArbeitsnachweisFreigabe");
    $this->app->ActionHandler("abschicken","ArbeitsnachweisAbschicken");
    $this->app->ActionHandler("pdf","ArbeitsnachweisPDF");
    $this->app->ActionHandler("inlinepdf","ArbeitsnachweisInlinePDF");
    $this->app->ActionHandler("protokoll","ArbeitsnachweisProtokoll");
    $this->app->ActionHandler("minidetail","ArbeitsnachweisMiniDetail");
    $this->app->ActionHandler("abrechnung","ArbeitsnachweisAbrechnung");
    $this->app->ActionHandler("editable","ArbeitsnachweisEditable");
    $this->app->ActionHandler("livetabelle","ArbeitsnachweisLiveTabelle");
    $this->app->ActionHandler("createfromproject","ArbeitsnachweisCreateFromProject");
    $this->app->ActionHandler("schreibschutz","ArbeitsnachweisSchreibschutz");
  
    $this->app->DefaultActionHandler("list");

		// pruefe ob tabelle vorhanden wenn nicht installieren
		$this->ArbeitsnachweisDBInstall();
 
    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("adresse");

    if($nummer=="")
      $adresse= $this->app->DB->Select("SELECT a.name FROM arbeitsnachweis b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    else
      $adresse = $nummer;

    $nummer = $this->app->DB->Select("SELECT b.belegnr FROM arbeitsnachweis b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
    if($nummer=="" || $nummer==0) $nummer="ohne Nummer";

    $this->app->Tpl->Set(UEBERSCHRIFT,"Arbeitsnachweis:&nbsp;".$adresse." (".$nummer.")");
    $this->app->Tpl->Set(FARBE,"[FARBE3]");

    
    $this->app->ActionHandlerListen($app);
  }


	function ArbeitsnachweisAbrechnung()
	{
		$abgerechnetmarkiert = $this->app->Secure->GetPOST("abgerechnetmarkiert");	
		$versendetmarkiert = $this->app->Secure->GetPOST("versendetmarkiert");	

		$arbeitsnachweis = $this->app->Secure->GetPOST("arbeitsnachweis");

			
		if($abgerechnetmarkiert!="")
		{
			for($i=0;$i<count($arbeitsnachweis);$i++)
			{
				if($arbeitsnachweis[$i] > 0)
					$this->app->DB->Update("UPDATE arbeitsnachweis SET status='abgerechnet'  WHERE id='".$arbeitsnachweis[$i]."' LIMIT 1");
			}
			header("Location: index.php?module=arbeitsnachweis&action=list#tabs-1");
			exit;
		}


		if($versendetmarkiert!="")
		{
			for($i=0;$i<count($arbeitsnachweis);$i++)
			{
				if($arbeitsnachweis[$i] > 0)
					$this->app->DB->Update("UPDATE arbeitsnachweis SET status='versendet' WHERE id='".$arbeitsnachweis[$i]."' LIMIT 1");
			}
			header("Location: index.php?module=arbeitsnachweis&action=list#tabs-1");
			exit;
		}

  	$submitabrechnen = $this->app->Secure->GetPOST("submitabrechnen");
    if($submitabrechnen!="")
    {
      $cmd = $this->app->Secure->GetPOST("cmd"); // neu,auftag,rechnung,arbeitsnachweis
      $abrechnenals = $this->app->Secure->GetPOST("abrechnenals"); // auftag,rechnung,arbeitsnachweis

      $rechnung = $this->app->Secure->GetPOST("rechnung");
      $auftrag = $this->app->Secure->GetPOST("auftrag");
      $arbeitsnachweis = $this->app->Secure->GetPOST("arbeitsnachweis");
      $art = $this->app->Secure->GetPOST("art");

      $artikel = $this->app->Secure->GetPOST("artikel");
      $artikel = $this->app->erp->FirstTillSpace($artikel);
      $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikel' LIMIT 1");

      // soll neues Dokument anlegent werden?
			// soll neues Dokument anlegent werden?

      switch($cmd)
      {
        case "neu":
          switch($abrechnenals)
          {
            case "auftrag":
              // auftrag angelegen + Adresse Laden
              $dokumenttyp = "auftrag";
              $dokumenttypid = $this->app->erp->CreateAuftrag($id);
              $this->app->erp->LoadAuftragStandardwerte($dokumenttypid,$id);
            break;
            case "rechnung":
              // auftrag angelegen + Adresse Laden
              $dokumenttyp = "rechnung";
              $dokumenttypid = $this->app->erp->CreateRechnung($id);
              $this->app->erp->LoadRechnungStandardwerte($dokumenttypid,$id);
            break;
            case "arbeitsnachweis":
              // auftrag angelegen + Adresse Laden
              $dokumenttyp = "arbeitsnachweis";
              $dokumenttypid = $this->app->erp->CreateArbeitsnachweis($id);
              $this->app->erp->LoadArbeitsnachweisStandardwerte($dokumenttypid,$id);
            break;
          }

        break;
        case "auftrag":
          $dokumenttyp = "auftrag";
          $dokumenttypid = $auftrag;
        break;
        case "rechnung":
          $dokumenttyp = "rechnung";
          $dokumenttypid = $rechnung;
        break;
        case "arbeitsnachweis":
          $dokumenttyp = "arbeitsnachweis";
          $dokumenttypid = $arbeitsnachweis;
        break;
      }


			$mitarbeiter_liste = array();

			for($i=0;$i<count($arbeitsnachweis);$i++)
			{
				
				$tmp_positionen = $this->app->DB->SelectArr("SELECT * FROM arbeitsnachweis_position WHERE arbeitsnachweis='".$arbeitsnachweis[$i]."'");
				$this->app->DB->Update("UPDATE arbeitsnachweis SET status='abgerechnet'  WHERE id='".$arbeitsnachweis[$i]."' LIMIT 1");

				for($ij=0;$ij<count($tmp_positionen);$ij++)
				{
					$arbeitsnachweis_postion_id = $tmp_positionen[$ij]['id'];
					if($art=="mitarbeiter"){

						$zmenge = $this->app->DB->Select("SELECT (UNIX_TIMESTAMP(CONCAT('2012-01-01 ',bis,':00'))-UNIX_TIMESTAMP(CONCAT('2012-01-01 ',von,':00')))/3600.0 FROM 
            arbeitsnachweis_position WHERE id='".$arbeitsnachweis_postion_id."' LIMIT 1");

						$mitarbeiter_liste[$tmp_positionen[$ij]['adresse']] += $zmenge;
					} else if($art=="positionen")
					{
        		$zmenge = $this->app->DB->Select("SELECT (UNIX_TIMESTAMP(CONCAT('2012-01-01 ',bis,':00'))-UNIX_TIMESTAMP(CONCAT('2012-01-01 ',von,':00')))/3600.0 FROM 
          	arbeitsnachweis_position WHERE id='".$arbeitsnachweis_postion_id."' LIMIT 1");

						$zaufgabe = $this->app->DB->Select("SELECT bezeichnung FROM arbeitsnachweis_position WHERE id='".$arbeitsnachweis_postion_id."' LIMIT 1");
						$zbeschreibung = $this->app->DB->Select("SELECT beschreibung FROM arbeitsnachweis_position WHERE id='".$arbeitsnachweis_postion_id."' LIMIT 1");
      			$this->app->erp->AddPositionManuell($dokumenttyp,$dokumenttypid, $artikel_id,$zmenge,$zaufgabe,$zbeschreibung);
					}

					//optional position zeiterfassung wenn vorhanden auf abgerechnet setzte
					$checkzeiterfassung = $this->app->DB->Select("SELECT id FROM zeiterfassung WHERE arbeitsnachweispositionid='$arbeitsnachweis_postion_id' LIMIT 1");

					if($checkzeiterfassung > 0)
						$this->app->DB->Update("UPDATE zeiterfassung SET ist_abgerechnet='1',abgerechnet='1' WHERE id='$checkzeiterfassung' LIMIT 1");	

					//in arbeitsnachweis_posito status auf abgerechnet setzten
					$this->app->DB->Update("UPDATE arbeitsnachweis_position SET status='abgerechnet' WHERE id='$arbeitsnachweis_postion_id' LIMIT 1");
				}
			}

			if($art=="mitarbeiter" && count($mitarbeiter_liste) > 0)
			{
				foreach($mitarbeiter_liste as $key=>$stunden)
				{
					$zaufgabe = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel_id' LIMIT 1");
          $zbeschreibung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$key."' LIMIT 1");
          $this->app->erp->AddPositionManuell($dokumenttyp,$dokumenttypid, $artikel_id,$stunden,$zaufgabe,$zbeschreibung);
				}
			}

/*
      // Artikel buchen und markieren   
      foreach($zaufgabe as $zidkey=>$aufgabe)
      {
        echo $zidkey;
        echo $zmenge[$zidkey];

        if($zuebernehmen[$zidkey]=="1")
        {
          if($dokumenttyp=="rechnung"||$dokumenttyp=="auftrag")
          {
            $this->app->erp->AddPositionManuell($dokumenttyp,$dokumenttypid, $artikel_id,$zmenge[$zidkey],
              $zaufgabe[$zidkey],$zbeschreibung[$zidkey]);
          }
          if($dokumenttyp=="arbeitsnachweis")
          {

            // extra holen, mitarbeitername, ort, von, bis
            //$this->app->erp->AddPositionManuell($dokumenttyp,$dokumenttypid, $artikel_id,$zmenge[$zidkey],
            //  $zaufgabe[$zidkey],$zbeschreibung[$zidkey]);
          }
        }

        if($zabgerechnet[$zidkey]=="1")
        {
          $this->app->DB->Update("UPDATE zeiterfassung SET ist_abgerechnet='1',abrechnung_dokument='$dokumenttyp',abgerechnet='1',
            dokumentid='$dokumenttypid' WHERE id='$zidkey' LIMIT 1");

          if($dokumenttyp=="arbeitsnachweis") {
            $this->app->DB->Update("UPDATE zeiterfassung SET
             arbeitsnachweis='$dokumenttypid' WHERE id='$zidkey' LIMIT 1");
          }
        }
      }
*/

      header("Location: index.php?module=$dokumenttyp&action=edit&id=$dokumenttypid");
      exit;
    }
  if($back!="")
      $this->app->erp->MenuEintrag(base64_decode($back),"Zur&uuml;ck zur &Uuml;bersicht");
    else
      $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $arbeitsnachweis = $this->app->Secure->GetPOST("arbeitsnachweis");

    if(is_array($arbeitsnachweis) >= 1) {
         $this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\">");

      for($i=0;$i<count($arbeitsnachweis);$i++)
      {
        $zid = $arbeitsnachweis[$i];
					$hiddenfields .="<input type=\"hidden\" name=\"arbeitsnachweis[]\" value=\"$zid\">";
      }

    $select_auftrag_kunde = $this->app->erp->GetSelectAuftragKunde($id);
    $select_rechnung_kunde = $this->app->erp->GetSelectRechnungKunde($id);
    $select_arbeitsnachweis_kunde = $this->app->erp->GetSelectArbeitsnachweisKunde($id);
  $this->app->Tpl->Add(TAB1,
    "<center><table align=\"center\">
$hiddenfields

<tr><td colspan=\"3\"><br><br>Abrechnungsart:<br><br></td></tr>
    <tr><td><input type=\"radio\" name=\"art\" value=\"mitarbeiter\" checked></td><td>jeden Mitarbeiter aufsummiert als einzelne Position</td><td>
		</td></tr>
    <tr><td><input type=\"radio\" name=\"art\" value=\"positionen\"></td><td>alle Positionen 1:1</td><td>
		</td></tr>

<tr><td colspan=\"3\"><br><br>Arbeitsnachweis(e) abrechnen als:<br><br></td></tr>
    <tr><td><input type=\"radio\" name=\"cmd\" value=\"neu\" checked></td><td width=\"300\"><select name=\"abrechnenals\">
      <option value=\"auftrag\">neuen Auftrag</option>
      <option value=\"rechnung\">neue Rechnung</option>
<!--      <option value=\"arbeitsnachweis\">neuen Arbeitsnachweis</option>-->
      </select></td><td></td></tr>
<!--    <tr><td><input type=\"radio\" name=\"cmd\" value=\"auftrag\"></td><td>bestehenden Auftrag erweitern</td><td>
<select style=\"width:500px\" name=\"auftrag\">".$select_auftrag_kunde."</select>
</td></tr>
    <tr><td><input type=\"radio\" name=\"cmd\" value=\"rechnung\"></td><td>bestehende Rechnung erweitern</td><td>
<select style=\"width:500px\" name=\"rechnung\">".$select_rechnung_kunde."</select>
</td></tr>-->
<!--    <tr><td><input type=\"radio\" name=\"cmd\" value=\"arbeitsnachweis\"></td><td>bestehenden Arbeitsnachweis erweitern</td><td>
<select style=\"width:500px\" name=\"arbeitsnachweis\">".$select_arbeitsnachweis_kunde."</select>
</td></tr>-->
<tr><td colspan=\"3\"><br><br></td></tr>
    <tr><td>als Artikel:</td><td>[ARTIKELSTART]<input type=\"text\" name=\"artikel\" id=\"artikel\" size=\"35\">[ARTIKELENDE]</td><td><input type=\"button\" value=\"jetzt buchen\" name=\"submitabrechnen\" 
      onclick=\"if(document.getElementById('artikel').value==''){ alert('Bitte Artikel für Abrechnung angeben!'); } else {
      document.forms[0].submit();
      }\"><input type=\"hidden\" name=\"submitabrechnen\" value=\"ok\"></td></tr>
    <tr><td></td><td colspan=\"2\"><i>Bitte bei Auswahl von Auftrag oder Rechnung angeben</i></td></tr>
    </table></center></form>");

    $this->app->YUI->AutoComplete("artikel","artikelnummer");


    } else {
      $this->app->Tpl->Set(TAB1,"<div class=\"error\">Es wurden keine Arbeitsnachweise ausgew&auml;hlt.</div>");
    }
    $this->app->Tpl->Set(TABTEXT,"Abrechnung Arbeitsnachweise");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");



	}

  function ArbeitsnachweisSchreibschutz()
  {

    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE arbeitsnachweis SET schreibschutz='0' WHERE id='$id'");
    header("Location: index.php?module=arbeitsnachweis&action=edit&id=$id");
    exit;
  }


	function ArbeitsnachweisCreateFromProject()
	{
//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Arbeitsnachweis anlegen");
		$zid = $this->app->Secure->GetPOST("z_id");
		$id = $this->app->Secure->GetGET("id");
		$choose = $this->app->Secure->GetPOST("choose");
		$arbeitsnachweis = $this->app->Secure->GetPOST("arbeitsnachweis");
		$adresse = $this->app->Secure->GetPOST("adresse");
		$projekt = $this->app->Secure->GetPOST("projekt");

		$uebernehmen = $this->app->Secure->GetPOST("uebernehmen");

		if($uebernehmen!="")
		{
			//print_r($zid);
			//echo "choose $choose ($auftrag)";
			switch($choose)
			{
				case "neu":
					$kundennummer = explode(' ',$adresse);
					$adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$kundennummer[0]."'");
					$projekt= $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$projekt."'");
					$arbeitsnachweis = $this->app->erp->CreateArbeitsnachweis($adresse);
	        $this->app->erp->LoadArbeitsnachweisStandardwerte($arbeitsnachweis,$adresse,$projekt);
				break;
				case "bestehenden":
					$arbeitsnachweis = trim($arbeitsnachweis);
					$arbeitsnachweis = $this->app->DB->Select("SELECT id FROM arbeitsnachweis WHERE belegnr='".$arbeitsnachweis."' AND belegnr!=''");
					$projekt= $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$projekt."'");
				break;
			}

			for($i=0;$i<count($zid);$i++)
			{
				$this->app->erp->AddArbeitsnachweisPositionZeiterfassung($arbeitsnachweis,$zid[$i]);
			}
			//header("Location: index.php?module=arbeitsnachweis&action=edit&id=$arbeitsnachweis");
			$belegnr_tmp = $this->app->DB->Select("SELECT belegnr FROM arbeitsnachweis WHERE id='".$arbeitsnachweis."'");
 	   $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Zeiten wurden in Arbeitsnachweis $belegnr_tmp gebucht!</div>");
			header("Location: index.php?module=projekt&action=arbeitspaket&id=".$projekt."&msg=$msg");
			exit;
		}

    $this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\">");
    $this->app->Tpl->Add(TAB1,"<table width=\"100%\">");
    $this->app->Tpl->Add(TAB1,"<tr><td></td><td><b>Mitarbeiter</b></td><td><b>T&auml;tigkeit</b></td><td><b>Datum</b></td><td><b>Von</b></td><td><b>Bis</b></td></tr>");
		for($i=0;$i<count($zid);$i++)
		{
			$z_id_item = $zid[$i];
			$z = $this->app->DB->SelectArr("SELECT z.projekt, z.aufgabe, z.beschreibung, DATE_FORMAT(von,'%d.%m.%Y %H:%i') as datum, DATE_FORMAT(von,'%H:%i') as von, DATE_FORMAT(bis,'%H:%i') as bis, a.name FROM zeiterfassung z 
				LEFT JOIN adresse a ON a.id=z.adresse WHERE z.id='$z_id_item'");

			$projekt = $z[0][projekt];
			$kunde = $this->app->DB->Select("SELECT kunde FROM projekt WHERE id='$projekt'");

    	$this->app->Tpl->Add(TAB1,"<tr><td><input type=\"checkbox\" checked value=\"".$z_id_item."\" name=\"z_id[]\">
				<td>".$z[0][name]."</td><td>".$z[0][aufgabe]."</td><td>".$z[0][datum]."</td><td>".$z[0][von]."</td><td>".$z[0][bis]."</td></tr>");
    	$this->app->Tpl->Add(TAB1,"<tr><td><td></td><td colspan=\"4\">".nl2br($z[0][beschreibung])."</td></tr>");
		}
    $this->app->Tpl->Add(TAB1,"</table>");

		$this->app->YUI->AutoComplete("arbeitsnachweis","arbeitsnachweis",1);
		$this->app->YUI->AutoComplete("adresse","kunde");
		$this->app->YUI->AutoComplete("projekt","projekt",1);

		$kunde_komplett = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$kunde'");	
		$this->app->Tpl->Add(ADRESSE,$kunde_komplett);

		$projekt_komplett = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt'");	
		$this->app->Tpl->Add(PROJEKT,$projekt_komplett);


    $this->app->Tpl->Add(TAB1,"<br><br><br><table align=\"center\">");

    $this->app->Tpl->Add(TAB1,"<tr><td><input type=\"radio\" name=\"choose\" checked value=\"neu\"></td><td>Neuen Arbeitsnachweis f&uuml;r Kunde anlegen:</td><td>
			<input type=\"text\" name=\"adresse\" id=\"adresse\" value=\"[ADRESSE]\"></td></tr>");

    $this->app->Tpl->Add(TAB1,"<tr><td></td><td>Projekt:</td><td>
			<input type=\"text\" name=\"projekt\" id=\"projekt\" value=\"[PROJEKT]\"></td></tr>");


    $this->app->Tpl->Add(TAB1,"<tr><td><input type=\"radio\" name=\"choose\" value=\"bestehenden\"></td>
			<td>bestehenden Arbeitsnachweis verwenden:</td><td><input type=\"text\" name=\"arbeitsnachweis\" id=\"arbeitsnachweis\"></td></tr>");
    $this->app->Tpl->Add(TAB1,"<tr><td colspan=\"3\" align=\"center\"><br><br><input type=\"submit\" value=\"&uuml;bernehmen\" name=\"uebernehmen\">&nbsp;<input type=\"button\" value=\"abbrechen\" name=\"abbrechen\" onclick=\"window.location.href='index.php?module=projekt&action=arbeitspaket&id=$id'\"></td></tr>");
    $this->app->Tpl->Add(TAB1,"</table></form>");
    $this->app->Tpl->Set(TABTEXT,"Stunden von Projekt");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");

	}

	
  function ArbeitsnachweisEditable()
  { 
    $this->app->YUI->AARLGEditable();
  }

  function ArbeitsnachweisLiveTabelle()
  { 
    $id = $this->app->Secure->GetGET("id");
    $status = $this->app->DB->Select("SELECT status FROM arbeitsnachweis WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    if($status=="freigegeben")
    {
    $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M,
      if(a.porto,'-',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel) > ap.menge,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),
      if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel)>0,CONCAT('<font color=red><b>',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=ap.artikel),'</b></font>'),
      '<font color=red><b>aus</b></font>'))) as L
      FROM arbeitsnachweis_position ap, artikel a WHERE ap.arbeitsnachweis='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","A","noAction");
    } else {
      $table->Query("SELECT SUBSTRING(ap.bezeichnung,1,20) as artikel, ap.nummer as Nummer, ap.menge as M
      FROM arbeitsnachweis_position ap, artikel a WHERE ap.arbeitsnachweis='$id' AND a.id=ap.artikel");
    $artikel = $table->DisplayNew("return","Menge","noAction");
    }
    echo $artikel;
    exit;
  }

  function ArbeitsnachweisCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $newid = $this->app->erp->CopyArbeitsnachweis($id);

    header("Location: index.php?module=arbeitsnachweis&action=edit&id=$newid");
    exit;
  }

  function ArbeitsnachweisIconMenu($id,$prefix="")
  {
 		$status = $this->app->DB->Select("SELECT status FROM arbeitsnachweis WHERE id='$id' LIMIT 1");

    if($status=="angelegt" || $status=="")
      $freigabe = "<option value=\"freigabe\">Arbeitsnachweis freigeben</option>";

    $menu ="

  <script type=\"text/javascript\">
  function onchangearbeitsnachweis(cmd)
  {
    switch(cmd)
    {
      case 'storno': if(!confirm('Wirklich stornieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=arbeitsnachweis&action=delete&id=%value%'; break;
      case 'copy': if(!confirm('Wirklich kopieren?')) return document.getElementById('aktion$prefix').selectedIndex = 0; else window.location.href='index.php?module=arbeitsnachweis&action=copy&id=%value%'; break;
      case 'pdf': window.location.href='index.php?module=arbeitsnachweis&action=pdf&id=%value%'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
      case 'pdfformular': window.location.href='index.php?module=arbeitsnachweis&action=pdf&id=%value%&cmd=formular'; document.getElementById('aktion$prefix').selectedIndex = 0; break;
      case 'abschicken':  ".$this->app->erp->DokumentAbschickenPopup()." break;
      case 'freigabe': window.location.href='index.php?module=arbeitsnachweis&action=freigabe&id=%value%';  break;
    }
    
  }
    </script>

&nbsp;Aktion:&nbsp;<select id=\"aktion$prefix\" onchange=\"onchangearbeitsnachweis(this.value);\">
<option>bitte w&auml;hlen ...</option>
<option value=\"storno\">Arbeitsnachweis stornieren</option>
<option value=\"copy\">Arbeitsnachweis kopieren</option>
$freigabe
<option value=\"abschicken\">Arbeitsnachweis abschicken</option>
<option value=\"storno\">Arbeitsnachweis stornieren</option>
<option value=\"pdfformular\">Arbeitsnachweis Leerformular</option>
<option value=\"pdf\">PDF &ouml;ffnen</option>
</select>&nbsp;

        <a href=\"index.php?module=arbeitsnachweis&action=pdf&id=%value%\" title=\"PDF\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>
";

      //$tracking = $this->AuftragTrackingTabelle($id);

    $menu = str_replace('%value%',$id,$menu);
    return $menu;
  }

  function ArbeitsnachweisMiniDetail($parsetarget="",$menu=true)
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(ID,$id);


    $table = new EasyTable($this->app);
 $table->Query("SELECT a.name, an.bezeichnung, an.von, an.bis FROM arbeitsnachweis_position an LEFT JOIN adresse a ON an.adresse = a.id WHERE an.arbeitsnachweis='$id'");

    $table->DisplayNew(POSITIONEN,"Bis","noAction");

      $this->app->Tpl->Output("arbeitsnachweis_minidetail.tpl");
		exit;
	} 


  function ArbeitsnachweisFreigabe()
  {
    $id = $this->app->Secure->GetGET("id");
    $freigabe= $this->app->Secure->GetGET("freigabe");
    $this->app->Tpl->Set(TABTEXT,"Freigabe");

    if($freigabe==$id)
    {
      $projekt = $this->app->DB->Select("SELECT projekt FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
//      if($belegnr <= 0) $belegnr = 300000; else $belegnr = $belegnr + 1;

      $belegnr = $this->app->erp->GetNextNummer("arbeitsnachweis",$projekt);

      $this->app->DB->Update("UPDATE arbeitsnachweis SET belegnr='$belegnr', status='freigegeben' WHERE id='$id' LIMIT 1");
      $this->app->erp->ArbeitsnachweisProtokoll($id,"Arbeitsnachweis freigegeben");
      //$this->app->Tpl->Set(TAB1,"<div class=\"warning\">Die Arbeitsnachweis wurde freigegeben und kann jetzt versendet werden!</div>");  
 	   $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Der Arbeitsnachweis wurde freigegeben und kann jetzt versendet werden!</div>");
      header("Location: index.php?module=arbeitsnachweis&action=edit&id=$id&msg=$msg");
      exit;

    } else { 

      $name = $this->app->DB->Select("SELECT a.name FROM arbeitsnachweis b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
      $summe = $this->app->DB->Select("SELECT FORMAT(SUM(menge*preis),2) FROM arbeitsnachweis_position
	WHERE arbeitsnachweis='$id'");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM arbeitsnachweis_position
	WHERE arbeitsnachweis='$id' LIMIT 1");

      $this->app->Tpl->Set(TAB1,"<div class=\"info\">Soll die Arbeitsnachweis an <b>$name</b>  
	jetzt freigegeben werden? <input type=\"button\" value=\"Jetzt freigeben\" onclick=\"window.location.href='index.php?module=arbeitsnachweis&action=freigabe&id=$id&freigabe=$id'\">
	</div>");
    }
    $this->ArbeitsnachweisMenu();
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function ArbeitsnachweisAbschicken()
  {
    $this->ArbeitsnachweisMenu();
    $this->app->erp->DokumentAbschicken();
  }




  function ArbeitsnachweisDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $force = $this->app->Secure->GetGET("force");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM arbeitsnachweis WHERE id='$id' LIMIT 1");

    if($belegnr==0 || $belegnr=="" || $force=="1")
    {
      $this->app->erp->DeleteArbeitsnachweis($id);
      if($belegnr<=0) $belegnr="ENTWURF";
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Arbeitsnachweis \"$name\" ($belegnr) wurde gel&ouml;scht!</div>");
    } else 
    {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Arbeitsnachweis \"$name\" ($belegnr) kann nicht storniert werden er bereits versendet wurde! <input type=\"button\" value=\"Jetzt l&ouml;schen\" onclick=\"window.location.href='index.php?module=arbeitsnachweis&action=delete&force=1&id=$id';\"></div>");
    }
    header("Location: index.php?module=arbeitsnachweis&action=list&msg=$msg");
    exit;

  }

  function ArbeitsnachweisProtokoll()
  {
    $this->ArbeitsnachweisMenu();
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Set(TABTEXT,"Protokoll");
    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM arbeitsnachweis_protokoll WHERE arbeitsnachweis='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(TAB1,"Protokoll","noAction");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ArbeitsnachweisAddPosition()
  {
    $sid = $this->app->Secure->GetGET("sid");
    $id = $this->app->Secure->GetGET("id");
    $menge = $this->app->Secure->GetGET("menge");
    $datum  = $this->app->Secure->GetGET("datum");
    $datum  = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
    $this->app->erp->AddArbeitsnachweisPosition($id, $sid,$menge,$datum);
    header("Location: index.php?module=arbeitsnachweis&action=positionen&id=$id");
    exit;
 
  }

  function ArbeitsnachweisInlinePDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $projekt = $this->app->DB->Select("SELECT projekt FROM arbeitsnachweis WHERE id='$id' LIMIT 1");

    $frame = $this->app->Secure->GetGET("frame");

    if($frame=="")
    {
      $Brief = new ArbeitsnachweisPDF($this->app,$projekt);
      $Brief->GetArbeitsnachweis($id);
      $Brief->inlineDocument();
    } else {

      $file = urlencode("../../../../index.php?module=arbeitsnachweis&action=inlinepdf&id=$id");
      echo "<iframe width=\"100%\" height=\"600\" src=\"./js/production/generic/web/viewer.html?file=$file\" frameborder=\"0\"></iframe>";
      exit;
    }
 }


  function ArbeitsnachweisPDF()
  {
    $id = $this->app->Secure->GetGET("id");
    $cmd = $this->app->Secure->GetGET("cmd");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM arbeitsnachweis WHERE id='$id' LIMIT 1");


		
    $Brief = new ArbeitsnachweisPDF($this->app,$projekt);
		if($cmd=="formular")
    	$Brief->GetArbeitsnachweis($id,"","",true);
		else
    	$Brief->GetArbeitsnachweis($id);

    $Brief->displayDocument(); 


    $this->ArbeitsnachweisList();
 }


  function ArbeitsnachweisMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM arbeitsnachweis WHERE id='$id' LIMIT 1");

    if($belegnr<=0) $belegnr ="(Entwurf)";
//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Arbeitsnachweis $belegnr");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,"$name Arbeitsnachweis $belegnr");


    $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=edit&id=$id","Arbeitsnachweisdaten");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=arbeitsnachweis&action=positionen&id=$id\">Positionen</a></li>");

    // status bestell
    $status = $this->app->DB->Select("SELECT status FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
   
    if($status=='bestellt')
    { 
      //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=arbeitsnachweis&action=wareneingang&id=$id\">Wareneingang<br>R&uuml;ckst&auml;nde</a></li>");
      //$this->app->Tpl->Add(TABS,"<li><a class=\"tab\" href=\"index.php?module=arbeitsnachweis&action=wareneingang&id=$id\">Mahnstufen</a></li>");
    } else if ($status=="angelegt")
    {
      $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=freigabe&id=$id","Freigabe");
    }
//    $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=abschicken&id=$id","Abschicken / Protokoll");
//    $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=protokoll&id=$id","Protokoll");
    $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function ArbeitsnachweisPositionen()
  {
    $this->app->YUI->AARLGPositionen(false);
    return;
  }

  function DelArbeitsnachweisPosition()
  {
		$sid = $this->app->Secure->GetGET("sid");
		$id = $this->app->Secure->GetGET("id");
    $this->app->YUI->SortListEvent("del","arbeitsnachweis_position","arbeitsnachweis");
		$this->app->DB->Update("UPDATE zeiterfassung SET arbeitsnachweis='0',arbeitsnachweispositionid='0' WHERE arbeitsnachweis='$id' AND arbeitsnachweispositionid='$sid' LIMIT 1");

    $this->ArbeitsnachweisPositionen();
  }

  function UpArbeitsnachweisPosition()
  {
    $this->app->YUI->SortListEvent("up","arbeitsnachweis_position","arbeitsnachweis");
    $this->ArbeitsnachweisPositionen();
  }

  function DownArbeitsnachweisPosition()
  {
    $this->app->YUI->SortListEvent("down","arbeitsnachweis_position","arbeitsnachweis");
    $this->ArbeitsnachweisPositionen();
  }


  function ArbeitsnachweisPositionenEditPopup()
  {
   $id = $this->app->Secure->GetGET("id");
      // nach page inhalt des dialogs ausgeben
      $widget = new WidgetArbeitsnachweis_position($this->app,PAGE);
      $sid= $this->app->DB->Select("SELECT arbeitsnachweis FROM arbeitsnachweis_position WHERE id='$id' LIMIT 1");
      $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=arbeitsnachweis&action=positionen&id=$sid");
      $widget->Edit();
      $this->app->BuildNavigation=false;
  }


  function ArbeitsnachweisEdit()
  {
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
  	if($this->app->erp->DisableModul("arbeitsnachweis",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->ArbeitsnachweisMenu();
      return;
    }

    $this->app->YUI->AARLGPositionen();

    $this->app->erp->CheckBearbeiter($id,"arbeitsnachweis");


    $nummer = $this->app->DB->Select("SELECT belegnr FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    $adresse = $this->app->DB->Select("SELECT adresse FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

		$status= $this->app->DB->Select("SELECT status FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    $schreibschutz= $this->app->DB->Select("SELECT schreibschutz FROM arbeitsnachweis WHERE id='$id' LIMIT 1");

		if($schreibschutz!="1" && $this->app->erp->RechteVorhanden("arbeitsnachweis","schreibschutz"))
		{
    $this->app->erp->AnsprechpartnerButton($adresse);
//    $this->app->erp->LieferadresseButton($adresse);
		}

    //$this->ArbeitsnachweisMiniDetail(MINIDETAIL,false);
    $this->app->Tpl->Set(ICONMENU,$this->ArbeitsnachweisIconMenu($id));
    $this->app->Tpl->Set(ICONMENU2,$this->ArbeitsnachweisIconMenu($id,2));


    if($nummer>0)
    {
      $this->app->Tpl->Set(NUMMER,$nummer);
      $this->app->Tpl->Set(KUNDE,"&nbsp;&nbsp;&nbsp;Kd-Nr.".$kundennummer);
    } else 
		{
      $this->app->Tpl->Set(NUMMER,"ENTWURF");
      $this->app->Tpl->Set(KUNDE,"&nbsp;&nbsp;&nbsp;Kd-Nr.".$kundennummer);
		}


    $status= $this->app->DB->Select("SELECT status FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    if($status=="")
      $this->app->DB->Update("UPDATE arbeitsnachweis SET status='angelegt' WHERE id='$id' LIMIT 1");

    if($schreibschutz=="1" && $this->app->erp->RechteVorhanden("arbeitsnachweis","schreibschutz"))
    {
      $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Dieser Arbeitsnachweis wurde bereits versendet und darf daher nicht mehr bearbeitet werden!&nbsp;<input type=\"button\" value=\"Schreibschutz entfernen\" onclick=\"if(!confirm('Soll der Schreibschutz f&uuml; diesen Arbeitsnachweis wirklich entfernt werden?')) return false;else window.location.href='index.php?module=arbeitsnachweis&action=schreibschutz&id=$id';\"></div>");
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
	  		$this->app->erp->LoadArbeitsnachweisStandardwerte($id,$adresse);
        header("Location: index.php?module=arbeitsnachweis&action=edit&id=$id");
        exit;
      }
    }


    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT nummer as Nummer, bezeichnung, menge,vpe as VPE
      FROM arbeitsnachweis_position
      WHERE arbeitsnachweis='$id'");
    $table->DisplayNew(POSITIONEN,"VPE","noAction");

    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(menge*preis),2) FROM arbeitsnachweis_position
      WHERE arbeitsnachweis='$id'");
    $waehrung = $this->app->DB->Select("SELECT waehrung FROM arbeitsnachweis_position
      WHERE arbeitsnachweis='$id' LIMIT 1");

    
    $status= $this->app->DB->Select("SELECT status FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(STATUS,"<input type=\"text\" size=\"30\" value=\"".$status."\" readonly [COMMONREADONLYINPUT]>");

     
    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    parent::ArbeitsnachweisEdit();
    $this->app->erp->MessageHandlerStandardForm();


    if($this->app->Secure->GetPOST("weiter")!="")
    {
      header("Location: index.php?module=arbeitsnachweis&action=positionen&id=$id");
      exit;
    }
    $this->ArbeitsnachweisMenu();

  }

  function ArbeitsnachweisCreate()
  {
//    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Arbeitsnachweis");
    $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=list","Zur&uuml;ck zur &Uuml;bersicht");


    $anlegen = $this->app->Secure->GetGET("anlegen");

    if($this->app->erp->Firmendaten("schnellanlegen")=="1" && $anlegen!="1")
    {
      header("Location: index.php?module=arbeitsnachweis&action=create&anlegen=1");
      exit;
    }

    if($anlegen != "")
    {
      $id = $this->app->erp->CreateArbeitsnachweis();
      $this->app->erp->ArbeitsnachweisProtokoll($id,"Arbeitsnachweis angelegt");
      header("Location: index.php?module=arbeitsnachweis&action=edit&id=$id");

      exit;
    }
    $this->app->Tpl->Set(MESSAGE,"<div class=\"warning\">M&ouml;chten Sie eine Arbeitsnachweis jetzt anlegen? &nbsp;
      <input type=\"button\" onclick=\"window.location.href='index.php?module=arbeitsnachweis&action=create&anlegen=1'\" value=\"Ja - Arbeitsnachweis jetzt anlegen\"></div><br>");
    $this->app->Tpl->Set(TAB1,"
     <table width=\"100%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
<tr>
<td align=\"center\">
<br><b style=\"font-size: 14pt\">Arbeitsnachweise in Bearbeitung</b>
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
	  $this->app->YUI->TableSearch(AUFTRAGE,"arbeitsnachweiseinbearbeitung");
/*
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%y') as vom, if(belegnr,belegnr,'ohne Nummer') as beleg, name, status, id
      FROM arbeitsnachweis WHERE status='angelegt' order by datum DESC, id DESC");
    $table->DisplayNew(AUFTRAGE, "<a href=\"index.php?module=arbeitsnachweis&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=arbeitsnachweis&action=delete&id=%value%';\">
          <img src=\"./themes/new/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=arbeitsnachweis&action=copy&id=%value%';\">
        <img src=\"./themes/new/images/copy.png\" border=\"0\"></a>
        ");
*/

    $this->app->Tpl->Set(TABTEXT,"Arbeitsnachweis anlegen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");



    //parent::ArbeitsnachweisCreate();
  }


  function ArbeitsnachweisList()
  {
     $this->app->Tpl->Set(UEBERSCHRIFT,"Arbeitsnachweise");
 
     $backurl = $this->app->Secure->GetGET("backurl");
     $backurl = $this->app->erp->base64_url_decode($backurl);
 
//     $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Arbeitsnachweise");
    $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=list","&Uuml;bersicht");
     $this->app->erp->MenuEintrag("index.php?module=arbeitsnachweis&action=create","Neuen Arbeitsnachweis anlegen");
 
     if(strlen($backurl)>5)
     $this->app->erp->MenuEintrag("$backurl","Zur&uuml;ck zur &Uuml;bersicht");
     else
     $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");

     $this->app->YUI->TableSearch(TAB1,"arbeitsnachweise");
     $this->app->YUI->TableSearch(TAB2,"arbeitsnachweiseoffene");
     $this->app->YUI->TableSearch(TAB3,"arbeitsnachweiseinbearbeitung");


    $back=$this->app->erp->base64_url_encode("index.php?module=adresse&action=abrechnungzeit&id=$id");

    $this->app->Tpl->Set(BACK,$back);
    $this->app->Tpl->Set(ID,$id);

    $this->app->Tpl->Add(TAB1,
    "<center>
			<input type=\"checkbox\" id=\"selecctall\" checked/> Auswahl entfernen&nbsp;			
      <input type=\"submit\" value=\"markierte Zeiten Arbeitsnachweise in Rechnung oder Auftrag &uuml;berf&uuml;hren\" name=\"submit\">
      <input type=\"submit\" value=\"als abgerechnet markieren\" name=\"abgerechnetmarkiert\">
      <input type=\"submit\" value=\"als versendet markieren\" name=\"versendetmarkiert\">
    </center>");

 
     $this->app->Tpl->Parse(PAGE,"arbeitsnachweisuebersicht.tpl");

    return;
  }

	function ArbeitsnachweisDBInstall()
	{

		$sql ="
CREATE TABLE IF NOT EXISTS `arbeitsnachweis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `prefix` varchar(222) NOT NULL,
  `arbeitsnachweisart` varchar(255) NOT NULL,
  `belegnr` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `auftragid` int(11) NOT NULL,
  `freitext` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `versand` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `inbearbeitung_user` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ohne_briefpapier` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


		if(!$this->app->DB->CheckTableExistence("arbeitsnachweis"))
			$this->app->DB->Query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `arbeitsnachweis_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `arbeitsnachweis` varchar(255) NOT NULL,
  `artikel` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `ort` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `verrechnungsart` varchar(255) NOT NULL,
  `menge` float NOT NULL,
  `arbeitspaket` INT(11) NOT NULL,
  `datum` date NOT NULL,
  `von` varchar(255) NOT NULL,
  `bis` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `abgerechnet` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

		if(!$this->app->DB->CheckTableExistence("arbeitsnachweis_position"))
		$this->app->DB->Query($sql);


$sql = "CREATE TABLE IF NOT EXISTS `arbeitsnachweis_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arbeitsnachweis` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

		if(!$this->app->DB->CheckTableExistence("arbeitsnachweis_protokoll"))
		$this->app->DB->Query($sql);

	}	
	
}
?>

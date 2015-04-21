<?php

class Buchhaltungexport {
  var $app;

  function Buchhaltungexport($app) {
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","BuchhaltungexportList");
    $this->app->ActionHandler("exportadressen","BuchhaltungexportExportAdressen");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }

  function BuchhaltungexportExportAdressen()
  {

    $cmd = $this->app->Secure->GetGET("cmd");

    switch($cmd)
    {
      case "kunden": 
        $sql = "SELECT kundennummer, name, strasse, plz, ort, ustid, zahlungszieltage,konto,blz,iban,swift,bank FROM adresse WHERE kundennummer!='' AND kundennummer!=' ' AND geloescht!=1 AND kundennummer!=0 ";
        $dateiname ="KUNDEN";
        break;

      case "lieferanten": 
        $sql = "SELECT lieferantennummer, name, strasse, plz, ort, ustid, zahlungszieltage,konto,blz,iban,swift,bank FROM adresse WHERE lieferantennummer!='' AND lieferantennummer!=' ' AND geloescht!=1 AND lieferantennummer!=0";
        $dateiname ="LIEFERANTEN";
        break;

      case "kundenmitverband":
        $sql = "SELECT kundennummer, name, strasse, plz, ort, ustid, zahlungszieltage,konto,blz,iban,swift,bank,id,verbandsnummer, zahlungskonditionen_festschreiben,
                zahlungszieltageskonto,zahlungszielskonto FROM adresse WHERE kundennummer!='' AND geloescht!=1 AND kundennummer!=0";
        $dateiname ="KUNDENVERBAND";
        break;

    }       

    $adressen = $this->app->DB->SelectArr($sql);

    // Wir werden eine PDF Datei ausgeben
    header('Content-Type: application/text; charset=utf-8');

    // Es wird downloaded.pdf benannt
    header('Content-Disposition: attachment; filename="'.date('Y-m-d').'_BUCHHALTUNG_EXPORT_'.$dateiname.'.csv"');

    for($i=0;$i<count($adressen);$i++)
    {

      $adressen[$i][kundennummer] = str_replace($this->app->erp->Firmendaten("steuer_anpassung_kundennummer"),"",$adressen[$i][kundennummer]);
      $adresseid= $adressen[$i][id];

      if($adressen[$i]['zahlungskonditionen_festschreiben']=="1")
      {
        $zahlungszieltageskonto = $adressen[$i]['zahlungszieltageskonto'];
        $zahlungszielskonto = $adressen[$i]['zahlungszielkonto'];
      } else {
        $verband  = $this->app->erp->GetVerband($adresseid);
        if($verband > 0)
        {
          $zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltage FROM gruppen WHERE id='$verband' LIMIT 1");
          $zahlungszieltageskonto = $this->app->DB->Select("SELECT zahlungszieltageskonto FROM gruppen WHERE id='$verband' LIMIT 1");
          $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM gruppen WHERE id='$verband' LIMIT 1");
        } else {
          $zahlungszieltage=$this->app->erp->ZahlungsZielTage();
          $zahlungszieltageskonto=$this->app->erp->ZahlungsZielTageSkonto();
          $zahlungszielskonto=$this->app->erp->ZahlungsZielSkonto();
        }
      }
      $adressen[$i][zahlungszieltage] = $zahlungszieltage;
  
      if($cmd=="kunden")      
        echo "\"".$adressen[$i][kundennummer]."\";";
      else
        echo "\"".$adressen[$i][lieferantennummer]."\";";

      echo "\"".utf8_decode($this->app->erp->ReadyForPDF($this->app->erp->UmlauteEntfernen($adressen[$i][name])))."\";";
      echo "\"".utf8_decode($this->app->erp->ReadyForPDF(($this->app->erp->UmlauteEntfernen($adressen[$i][strasse]))))."\";";
      echo "\"".$adressen[$i][plz]."\";";
      echo "\"".utf8_decode($this->app->erp->ReadyForPDF(($this->app->erp->UmlauteEntfernen($adressen[$i][ort]))))."\";";
      echo "\"".$adressen[$i][ustid]."\";";
      echo "\"".$adressen[$i][zahlungszieltage]."\";";
      echo "\"".$adressen[$i][konto]."\";";
      echo "\"".$adressen[$i][blz]."\";";
      echo "\"".$adressen[$i][iban]."\";";
      echo "\"".$adressen[$i][swift]."\";";
      echo "\"".utf8_decode($this->app->erp->ReadyForPDF(($this->app->erp->UmlauteEntfernen($adressen[$i][bank]))))."\";";

      if($cmd=="kundenmitverband")
      {
        $verband = $this->app->erp->GetVerband($adressen[$i][id]);
        if($verband > 0)
        {
          $adressen[$i][verbandkennziffer] = $this->app->DB->Select("SELECT kennziffer FROM gruppen WHERE id='$verband' LIMIT 1");
          $adressen[$i][verbandname] = $this->app->DB->Select("SELECT name FROM gruppen WHERE id='$verband' LIMIT 1");
        }

        echo "\"".utf8_decode($this->app->erp->ReadyForPDF($this->app->erp->UmlauteEntfernen($adressen[$i][verbandkennziffer])))."\";";
        echo "\"".utf8_decode($this->app->erp->ReadyForPDF($this->app->erp->UmlauteEntfernen($adressen[$i][verbandname])))."\";";
        echo "\"".$adressen[$i][verbandsnummer]."\";";

        // zahlungsinfos
        echo "\"".$zahlungszieltageskonto."\";";
        echo "\"".$zahlungszielskonto."\";";
      }

      echo "\r\n";
    }
    exit;
  }




  function BuchhaltungexportList()
  {
    $this->BuchhaltungexportMenu();

    $this->app->YUI->DatePicker("von");
    $this->app->YUI->DatePicker("bis");
    $this->app->YUI->DatePicker("von2");
    $this->app->YUI->DatePicker("bis2");
    $this->app->YUI->DatePicker("von3");
    $this->app->YUI->DatePicker("bis3");

    $this->app->YUI->AutoComplete("projekt","projektname",1);

    $von = $this->app->Secure->GetPOST("von");
    $von2 = $this->app->Secure->GetPOST("von2");
    $von3 = $this->app->Secure->GetPOST("von3");
    $bis = $this->app->Secure->GetPOST("bis");
    $bis2 = $this->app->Secure->GetPOST("bis2");
    $bis3 = $this->app->Secure->GetPOST("bis3");
    $sort= $this->app->Secure->GetPOST("sort");
    $sort2= $this->app->Secure->GetPOST("sort2");
    $sort3= $this->app->Secure->GetPOST("sort3");

    $projekt= $this->app->Secure->GetPOST("projekt");

    $tmpvon = $this->app->String->Convert($von,"%1.%2.%3","%3-%2-%1");
    $tmpbis = $this->app->String->Convert($bis,"%1.%2.%3","%3-%2-%1");

    $tmpvon2 = $this->app->String->Convert($von2,"%1.%2.%3","%3-%2-%1");
    $tmpbis2 = $this->app->String->Convert($bis2,"%1.%2.%3","%3-%2-%1");

    $tmpvon3 = $this->app->String->Convert($von3,"%1.%2.%3","%3-%2-%1");
    $tmpbis3 = $this->app->String->Convert($bis3,"%1.%2.%3","%3-%2-%1");


    $exportiert = $this->app->Secure->GetPOST("exportiert");
    $exportiert2 = $this->app->Secure->GetPOST("exportiert2");
    $exportiert3 = $this->app->Secure->GetPOST("exportiert3");

    $defaultgegenkonto = $this->app->Secure->GetPOST("defaultgegenkonto");

    if($projekt !="")
    {
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
    }

    if($this->app->Secure->GetPOST("export")!="")
    {
      // alle kontoauszuege die zu datev_buchungen geworden sind!!!
      $this->app->erp->DatevEinnahmenExport($tmpvon,$tmpbis,$exportiert,$sort,$projekt);
    }
    if($this->app->Secure->GetPOST("buchhaltungexport")!="")
    {
      $this->app->erp->DatevBuchhaltungExport($tmpvon2,$tmpbis2,$exportiert2,$sort2,$defaultgegenkonto);
    }

    if($this->app->Secure->GetPOST("exportverbindlichkeit")!="")
    {
      $this->app->erp->DatevVerbindlichkeitExport($tmpvon3,$tmpbis3);
    }



    $startTime = mktime() - 30*3600*24;
    $endTime = mktime();

    if($von=="")
      $von = "01.".date('m.Y',$startTime);
    if($bis=="")
      $bis = date('t.m.Y',$startTime);

    if($von2=="")
      $von2 = "01.".date('m.Y',$startTime);
    if($bis2=="")
      $bis2 = date('t.m.Y',$startTime);

    if($von3=="")
      $von3 = "01.".date('m.Y',$startTime);
    if($bis3=="")
      $bis3 = date('t.m.Y',$startTime);


    $this->app->Tpl->Set(VON,$von);
    $this->app->Tpl->Set(VON2,$von2);
    $this->app->Tpl->Set(VON3,$von3);
    $this->app->Tpl->Set(BIS,$bis);
    $this->app->Tpl->Set(BIS2,$bis2);
    $this->app->Tpl->Set(BIS3,$bis3);

    $this->app->Tpl->Set(SCHLUESSEL,"\"datum\";\"betrag\";\"konto\";\"belegfeld1\";\"buchungstext\";\"land\";\"ustid\";\"gegenkonto\";\"waehrung\"");
    $this->app->Tpl->Set(SCHLUESSEL3,'"datum";"betrag";"konto";"belegfeld1";"buchungstext";"land";"ustid";"gegenkonto";"waehrung"');

    if($this->app->erp->ModulVorhanden("verband"))
    {
      $this->app->Tpl->Set(BUTTONVERBAND,"<input type=\"button\" value=\"Kunden mit Verbandsinfo\" onclick=\"window.location.href='index.php?module=buchhaltungexport&action=exportadressen&cmd=kundenmitverband'\">&nbsp;");
    }
    $this->app->Tpl->Set(TABTEXT,"Buchhaltung Export");
    $this->app->Tpl->Parse(TAB1,"buchhaltungexport_list.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");


  }

  function BuchhaltungexportMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Finanzbuchhaltung Export");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=zahlungseingang&action=list\">Zahlungseingang</a></li>");
    //    $this->app->erp->MenuEintrag("index.php?module=buchhaltungexport&action=list","Buchhaltungexport");
    //    $this->app->erp->MenuEintrag("index.php?module=buchhaltungexport&action=saldo","Saldoblatt");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=buchhaltungexport&action=experte\">Experte Buchungen</a></li>");
    //    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=buchhaltungexport&action=create\">BWA</a></li>");
    //   $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=buchhaltungexport&action=create\">ZM-Meldung</a></li>");
    //    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=buchhaltungexport&action=create\">Ust.-Voranmeldung</a></li>");
    $this->app->erp->MenuEintrag("index.php?module=buchhaltungexport&action=list","Export");

    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=artikel&action=search\">Verbindlichkeit suchen</a></li>");
  }

}
?>

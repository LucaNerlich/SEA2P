<?php

class Adresse_import  {
  var $app;
  
  function Adresse_import($app) {
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

   	$this->app->ActionHandler("list","Adresse_importList");
   	$this->app->ActionHandler("edit","Adresse_importAdresseEdit");
   	$this->app->ActionHandler("delete","Adresse_importAdresseDelete");
   	$this->app->ActionHandler("dateien","Adresse_importAdresseDateien");

    $this->app->ActionHandlerListen($app);

		$this->app->Tpl->Set(KURZUEBERSCHRIFT,"Adressen Import");


    $this->app = $app;
  }

  function Adresse_importAdresseDateien()
  {
		$this->Adresse_importMenu();
    $id = $this->app->Secure->GetGET("id");
    $this->app->YUI->DateiUpload(PAGE,"adresse_import",$id);
  }

  function Adresse_importAdresseDelete()
  {
    $id = $this->app->Secure->GetGET("id");

		$this->app->DB->Delete("DELETE FROM adresse_import WHERE id='$id' LIMIT 1");
    $msg = base64_encode("<div class=\"info\">Der Adresseintrag wurde entfernt!</div>");
    header("Location: index.php?module=adresse_import&action=list&msg=$msg");
    exit;
  }


	function Adresse_importMenu()
	{
    $id = $this->app->Secure->GetGET("id");
  	$this->app->erp->MenuEintrag("index.php?module=adresse_import&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=adresse_import&action=edit&id=$id","Adresse");
    $this->app->erp->MenuEintrag("index.php?module=adresse_import&action=dateien&id=$id","Dateien");
	}

	function Adresse_importAdresseEdit()
	{
    $id = $this->app->Secure->GetGET("id");
    $speichern = $this->app->Secure->GetPOST("speichern");

		$adresse = $this->app->DB->Select("SELECT adresse FROM adresse_import WHERE id='$id' LIMIT 1");
		$angelegt_von = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$adresse' LIMIT 1");

		$this->app->Tpl->Set(MESSAGE,"<div class=\"info\">Angelegt von $angelegt_von</div>");

		if($speichern!="")
		{
			  $werte = array('name','ansprechpartner','abteilung','unterabteilung','adresszusatz',
        'strasse','plz','ort','land','telefon','telefax','email','mobil','internetseite','ustid','typ');


        $this->app->DB->Insert("INSERT INTO adresse (id,firma) VALUES ('','1')");
        $tmpid = $this->app->DB->GetInsertID();

         // updat alle felde die angeben wurden sind
        foreach($werte as $key)
        {
          $value = $this->app->Secure->GetPOST($key);
          if(is_array($value))$value="";
          if($key!="id")
          {
            $this->app->DB->Update("UPDATE adresse SET $key='$value' WHERE id='$tmpid' LIMIT 1");
          }
        }

				$this->app->DB->Update("UPDATE adresse_import SET abgeschlossen='1' WHERE id='$id' LIMIT 1");

				$dateien = $this->app->DB->SelectArr("SELECT DISTINCT datei FROM datei_stichwoerter WHERE objekt='adresse_import' AND parameter='$id'");
				for($i=0;$i<count($dateien);$i++)
				{
					$dateiid = $dateien[$i][datei];
  				$this->app->erp->AddDateiStichwort($dateiid,"Sonstige","Adressen",$tmpid);
				}

				// wenn es das mlm modul gibt
				$sponsor = $this->app->DB->Select("SELECT adresse FROM adresse_import WHERE id='$id' LIMIT 1");
				$this->app->DB->Update("UPDATE adresse SET sponsor='$sponsor', geworbenvon='$sponsor',vertrieb='$sponsor' WHERE id='$tmpid' LIMIT 1");

				header("Location: index.php?module=adresse&action=edit&id=$tmpid");
				exit;
		}

		$this->Adresse_importMenu();
		$data = $this->app->DB->SelectArr("SELECT * FROM adresse_import WHERE id='$id' LIMIT 1");

		foreach($data[0] as $key=>$value)
		{
			$this->app->Tpl->Set(strtoupper($key),$value);
		}

    switch($data[0][typ])
    {
    	case "herr": $this->app->Tpl->Set(HERR,"selected"); break;
      case "frau": $this->app->Tpl->Set(FRAU,"selected"); break;
      case "firma": $this->app->Tpl->Set(FIRMA,"selected"); break;
    }
	
    $this->app->Tpl->Set(TABTEXT,"Import Adresse");
    $this->app->Tpl->Parse(PAGE,"adresse_import_edit.tpl");
	}

	function Adresse_importList()
	{
//    $this->app->Tpl->Parse(TAB1,"importvorlage_adressen.tpl");
    $this->app->YUI->TableSearch(TAB1,"adresse_import");
    $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->Tpl->Set(TABTEXT,"Import");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
	}

}

?>

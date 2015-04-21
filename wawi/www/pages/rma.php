<?php

class Rma {
  var $app;
  
  function Rma($app) {
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","RmaCreate");
    $this->app->ActionHandler("edit","RmaEdit");
    $this->app->ActionHandler("detail","RmaDetail");
    $this->app->ActionHandler("list","RmaList");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }


  function RmaCreate()
  {
    $this->app->erp->MenuEintrag("index.php?module=rma&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function RmaList()
  {  
		$this->RmaMenu();

    $this->app->Tpl->Set(TABTEXT,"RMA Lieferungen");
		$this->app->YUI->TableSearch(TAB1,"rma_list");

    $this->app->Tpl->Parse(PAGE,"rma_list.tpl");
  }


  function RmaMenu()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Rma Lieferungen");
//    $this->app->erp->MenuEintrag("index.php?module=rma&action=list","&Uuml;bersicht");
//    $this->app->erp->MenuEintrag("index.php?module=rma&action=create","RMA anlegen");
  }


  function RmaEdit()
  {
    $this->app->erp->MenuEintrag("index.php?module=rma&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


 function RmaDetail()
  {
    // erstens technik check
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=rma&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' LIMIT 1");
    $kundennummer  = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set(KUNDENNUMMER,$kundennummer);
    $this->app->Tpl->Set(NAME,$name);


    $this->app->Tpl->Set(TABTEXT,"RMA Artikel von $name (KD-Nr. $kundennummer)");

		$rmaartikel = $this->app->DB->SelectArr("SELECT * FROM rma_artikel WHERE adresse='$id' AND status!='abgeschlossen'");

		for($i=0;$i<count($rmaartikel);$i++)
		{
			$artikel = $this->app->DB->SelectArr("SELECT name_de, nummer FROM artikel WHERE id='".$rmaartikel[$i][artikel]."' LIMIT 1");

			$this->app->Tpl->Set(ARTIKELNAME,$artikel[0][name_de]);
			$this->app->Tpl->Set(ARTIKELNUMMER,$artikel[0][nummer]);
			$this->app->Tpl->Set(WUNSCH,$rmaartikel[$i][wunsch]);
			$this->app->Tpl->Set(BEMERKUNG,$rmaartikel[$i][bemerkung]);
			$this->app->Tpl->Set(DATUM,$rmaartikel[$i][angelegtam]);
		
			$this->app->Tpl->Parse(ARTIKEL,"rmadetail_artikel.tpl");
		}


    $this->app->Tpl->Parse(PAGE,"rmadetail.tpl");
  }




}

?>

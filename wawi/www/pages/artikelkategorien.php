<?php
include ("_gen/artikelkategorien.php");

class Artikelkategorien extends GenArtikelkategorien {
  var $app;
  
  function Artikelkategorien($app) {
    //parent::GenArtikelkategorien($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikelkategorienCreate");
    $this->app->ActionHandler("edit","ArtikelkategorienEdit");
   	$this->app->ActionHandler("list","ArtikelkategorienList");
   	$this->app->ActionHandler("delete","ArtikelkategorienDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function ArtikelkategorienCreate()
  {
    $this->ArtikelkategorienMenu();
    parent::ArtikelkategorienCreate();
  }

	function ArtikelkategorienDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("UPDATE artikelkategorien SET geloescht=1 WHERE id='$id'");
		}

		$this->ArtikelkategorienList();
	}


  function ArtikelkategorienList()
  {
    $this->ArtikelkategorienMenu();
    parent::ArtikelkategorienList();
  }

  function ArtikelkategorienMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=artikelkategorien&action=create","Neues Etikett anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=artikelkategorien&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function ArtikelkategorienEdit()
  {
    $this->ArtikelkategorienMenu();
    parent::ArtikelkategorienEdit();
  }





}

?>

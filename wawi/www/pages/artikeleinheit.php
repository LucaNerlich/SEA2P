<?php
include ("_gen/artikeleinheit.php");

class Artikeleinheit extends GenArtikeleinheit {
  var $app;
  
  function Artikeleinheit($app) {
    //parent::GenArtikeleinheit($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikeleinheitCreate");
    $this->app->ActionHandler("edit","ArtikeleinheitEdit");
   	$this->app->ActionHandler("list","ArtikeleinheitList");
   	$this->app->ActionHandler("delete","ArtikeleinheitDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function ArtikeleinheitCreate()
  {
    $this->ArtikeleinheitMenu();
    parent::ArtikeleinheitCreate();
  }

	function ArtikeleinheitDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("DELETE FROM artikeleinheit WHERE id='$id'");
		}

		$this->ArtikeleinheitList();
	}


  function ArtikeleinheitList()
  {
    $this->ArtikeleinheitMenu();
    parent::ArtikeleinheitList();
  }

  function ArtikeleinheitMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=artikeleinheit&action=create","Artikeleinheit anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=artikeleinheit&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function ArtikeleinheitEdit()
  {
    $this->ArtikeleinheitMenu();
    parent::ArtikeleinheitEdit();
  }





}

?>

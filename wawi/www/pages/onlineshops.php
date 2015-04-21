<?php
include ("_gen/shopexport.php");

class Onlineshops extends GenShopexport {
  var $app;
  
  function Onlineshops($app) {
    //parent::GenShopexport($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ShopexportCreate");
    $this->app->ActionHandler("edit","ShopexportEdit");
    $this->app->ActionHandler("list","ShopexportList");
    $this->app->ActionHandler("delete","ShopexportDelete");


		$this->app->Tpl->Set(KURZUEBERSCHRIFT,"Shopexport");
    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function ShopexportCreate()
  {
    $this->ShopexportMenu();
    parent::ShopexportCreate();
  }

  function ShopexportList()
  {
    $this->ShopexportMenu();
    parent::ShopexportList();
  }

  function ShopexportMenu()
  {
			$id = $this->app->Secure->GetGET("id");

			if($id <=0)
				$this->app->erp->MenuEintrag("index.php?module=onlineshops&action=create","Online-Shop anlegen");

			if($id > 0)
			{
				$name = $this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id='$id' LIMIT 1");
				$this->app->Tpl->Add(KURZUEBERSCHRIFT2,$name);
    		$this->app->erp->MenuEintrag("index.php?module=onlineshops&action=edit&id=$id","Einstellungen");
    		$this->app->erp->MenuEintrag("index.php?module=shopexport&action=export&id=$id","Export");
			}


    	$typ = $this->app->DB->Select("SELECT typ FROM shopexport WHERE id='$id' LIMIT 1");
    	if($typ=="wawision")
    	{
      	$this->app->erp->MenuEintrag("index.php?module=shopexport&action=navigationtab&id=$id","Navigation");
      	$this->app->erp->MenuEintrag("index.php?module=shopexport&action=artikelgruppen&id=$id","Artikelgruppen");
      	$this->app->erp->MenuEintrag("index.php?module=shopexport&action=dateien&id=$id","Dateien");
      	$this->app->erp->MenuEintrag("index.php?module=shopexport&action=live&id=$id","Live-Status");
      	$this->app->erp->MenuEintrag("index.php?module=inhalt&action=listshop&id=$id","Inhalte / E-Mailvorlagen");
    	}

			if($this->app->Secure->GetGET("action")=="list")
				$this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
			else
				$this->app->erp->MenuEintrag("index.php?module=onlineshops&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

	function ShopexportDelete() 
	{
		$id = $this->app->Secure->GetGET('id');
		if(is_numeric($id)) {
			$this->app->DB->Delete("DELETE FROM shopexport WHERE id='$id' LIMIT 1");
		}
		header('Location: ./index.php?module=onlineshops&action=list');
		exit;
	}


  function ShopexportEdit()
  {
		$id = $this->app->Secure->GetGET('id');
    $this->ShopexportMenu();
    parent::ShopexportEdit();
  }





}

?>

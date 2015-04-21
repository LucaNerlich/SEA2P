<?php

class Etikettendrucker {
  var $app;
  
  function Etikettendrucker($app) {
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","EtikettendruckerList");
    $this->app->DefaultActionHandler("list");
    $this->app->ActionHandlerListen($app);
  }

  function EtikettendruckerList()
  {
		$drucken = $this->app->Secure->GetPOST("drucken");
		$xmltest = $this->app->Secure->GetPOST("xmltest");

		$bezeichnung1 = $this->app->Secure->GetPOST("bezeichnung1");
		$bezeichnung2 = $this->app->Secure->GetPOST("bezeichnung2");
		$this->app->Tpl->Set(BEZEICHNUNG1,$bezeichnung1);
		$this->app->Tpl->Set(BEZEICHNUNG2,$bezeichnung2);
		$this->app->Tpl->Set(XML,$this->app->Secure->POST["xml"]);

		if($drucken!="")
		{
	    $this->app->erp->EtikettenDrucker("etikettendrucker_einfach",1,"","",array('bezeichnung1'=>$bezeichnung1,'bezeichnung2'=>$bezeichnung2));
		}

		if($xmltest!="")
		{
			$xml = $this->app->Secure->POST["xml"];
	    $this->app->erp->EtikettenDrucker("xml",1,"","","",$xml);
		}	

    $this->app->Tpl->Set(TABTEXT,"Etikettendrucker");
    $this->app->Tpl->Parse(PAGE,"etikettendrucker_list.tpl");
  }

  function EtikettendruckerMenu()
  {
    $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Etikettendrucker");
    $this->app->erp->MenuEintrag("index.php?module=artikel&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }



}

?>

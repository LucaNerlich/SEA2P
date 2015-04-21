<?php

class Paketmarke {
  var $app;
  
  function Paketmarke($app) {
    //parent::GenPaketmarke($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","PaketmarkeCreatePopup");
    $this->app->ActionHandler("tracking","PaketmarkeTracking");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }



  function PaketmarkeTracking()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Paketmarken Drucker");

    $this->app->Tpl->Set(PAGE,"Tracking-Nummer: <input type=\"text\" id=\"tracking\"><script type=\"text/javascript\">document.getElementById(\"tracking\").focus(); </script>");
    //$this->app->BuildNavigation=false;
  }



  function PaketmarkeCreatePopup()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Paketmarken Drucker");
   //$frame = $this->app->Secure->GetGET("frame");
  
   // if($frame=="false")
   // {
      // hier nur fenster größe anpassen
   //   $this->app->YUI->IframeDialog(665,670);
   // } else {
      // nach page inhalt des dialogs ausgeben
      $this->app->erp->PaketmarkeDHLEmbedded(PAGE,"lieferschein");   
     // $this->app->BuildNavigation=false;
    //}
  }





}

?>

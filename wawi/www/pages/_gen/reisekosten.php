<?php 

class GenReisekosten { 

  function GenReisekosten(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ReisekostenCreate");
    $this->app->ActionHandler("edit","ReisekostenEdit");
    $this->app->ActionHandler("copy","ReisekostenCopy");
    $this->app->ActionHandler("list","ReisekostenList");
    $this->app->ActionHandler("delete","ReisekostenDelete");

    $this->app->Tpl->Set(HEADING,"Reisekosten");    $this->app->ActionHandlerListen($app);
  }

  function ReisekostenCreate(){
    $this->app->Tpl->Set(HEADING,"Reisekosten (Anlegen)");
      $this->app->PageBuilder->CreateGen("reisekosten_create.tpl");
  }

  function ReisekostenEdit(){
    $this->app->Tpl->Set(HEADING,"Reisekosten (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("reisekosten_edit.tpl");
  }

  function ReisekostenCopy(){
    $this->app->Tpl->Set(HEADING,"Reisekosten (Kopieren)");
      $this->app->PageBuilder->CreateGen("reisekosten_copy.tpl");
  }

  function ReisekostenDelete(){
    $this->app->Tpl->Set(HEADING,"Reisekosten (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("reisekosten_delete.tpl");
  }

  function ReisekostenList(){
    $this->app->Tpl->Set(HEADING,"Reisekosten (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("reisekosten_list.tpl");
  }

} 
?>
<?php 

class GenPartner { 

  function GenPartner(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","PartnerCreate");
    $this->app->ActionHandler("edit","PartnerEdit");
    $this->app->ActionHandler("copy","PartnerCopy");
    $this->app->ActionHandler("list","PartnerList");
    $this->app->ActionHandler("delete","PartnerDelete");

    $this->app->Tpl->Set(HEADING,"Partner");    $this->app->ActionHandlerListen($app);
  }

  function PartnerCreate(){
    $this->app->Tpl->Set(HEADING,"Partner (Anlegen)");
      $this->app->PageBuilder->CreateGen("partner_create.tpl");
  }

  function PartnerEdit(){
    $this->app->Tpl->Set(HEADING,"Partner (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("partner_edit.tpl");
  }

  function PartnerCopy(){
    $this->app->Tpl->Set(HEADING,"Partner (Kopieren)");
      $this->app->PageBuilder->CreateGen("partner_copy.tpl");
  }

  function PartnerDelete(){
    $this->app->Tpl->Set(HEADING,"Partner (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("partner_delete.tpl");
  }

  function PartnerList(){
    $this->app->Tpl->Set(HEADING,"Partner (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("partner_list.tpl");
  }

} 
?>
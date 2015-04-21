<?php 

class GenArbeitsnachweis { 

  function GenArbeitsnachweis(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArbeitsnachweisCreate");
    $this->app->ActionHandler("edit","ArbeitsnachweisEdit");
    $this->app->ActionHandler("copy","ArbeitsnachweisCopy");
    $this->app->ActionHandler("list","ArbeitsnachweisList");
    $this->app->ActionHandler("delete","ArbeitsnachweisDelete");

    $this->app->Tpl->Set(HEADING,"Arbeitsnachweis");    $this->app->ActionHandlerListen($app);
  }

  function ArbeitsnachweisCreate(){
    $this->app->Tpl->Set(HEADING,"Arbeitsnachweis (Anlegen)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_create.tpl");
  }

  function ArbeitsnachweisEdit(){
    $this->app->Tpl->Set(HEADING,"Arbeitsnachweis (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_edit.tpl");
  }

  function ArbeitsnachweisCopy(){
    $this->app->Tpl->Set(HEADING,"Arbeitsnachweis (Kopieren)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_copy.tpl");
  }

  function ArbeitsnachweisDelete(){
    $this->app->Tpl->Set(HEADING,"Arbeitsnachweis (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_delete.tpl");
  }

  function ArbeitsnachweisList(){
    $this->app->Tpl->Set(HEADING,"Arbeitsnachweis (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_list.tpl");
  }

} 
?>
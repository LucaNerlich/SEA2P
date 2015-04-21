<?php 

class GenGeschaeftsbrief_Vorlagen { 

  function GenGeschaeftsbrief_Vorlagen(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Geschaeftsbrief_VorlagenCreate");
    $this->app->ActionHandler("edit","Geschaeftsbrief_VorlagenEdit");
    $this->app->ActionHandler("copy","Geschaeftsbrief_VorlagenCopy");
    $this->app->ActionHandler("list","Geschaeftsbrief_VorlagenList");
    $this->app->ActionHandler("delete","Geschaeftsbrief_VorlagenDelete");

    $this->app->Tpl->Set(HEADING,"Geschaeftsbrief_Vorlagen");    $this->app->ActionHandlerListen($app);
  }

  function Geschaeftsbrief_VorlagenCreate(){
    $this->app->Tpl->Set(HEADING,"Geschaeftsbrief_Vorlagen (Anlegen)");
      $this->app->PageBuilder->CreateGen("geschaeftsbrief_vorlagen_create.tpl");
  }

  function Geschaeftsbrief_VorlagenEdit(){
    $this->app->Tpl->Set(HEADING,"Geschaeftsbrief_Vorlagen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("geschaeftsbrief_vorlagen_edit.tpl");
  }

  function Geschaeftsbrief_VorlagenCopy(){
    $this->app->Tpl->Set(HEADING,"Geschaeftsbrief_Vorlagen (Kopieren)");
      $this->app->PageBuilder->CreateGen("geschaeftsbrief_vorlagen_copy.tpl");
  }

  function Geschaeftsbrief_VorlagenDelete(){
    $this->app->Tpl->Set(HEADING,"Geschaeftsbrief_Vorlagen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("geschaeftsbrief_vorlagen_delete.tpl");
  }

  function Geschaeftsbrief_VorlagenList(){
    $this->app->Tpl->Set(HEADING,"Geschaeftsbrief_Vorlagen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("geschaeftsbrief_vorlagen_list.tpl");
  }

} 
?>
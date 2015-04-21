<?php 

class GenProduktion_Position { 

  function GenProduktion_Position(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Produktion_PositionCreate");
    $this->app->ActionHandler("edit","Produktion_PositionEdit");
    $this->app->ActionHandler("copy","Produktion_PositionCopy");
    $this->app->ActionHandler("list","Produktion_PositionList");
    $this->app->ActionHandler("delete","Produktion_PositionDelete");

    $this->app->Tpl->Set(HEADING,"Produktion_Position");    $this->app->ActionHandlerListen($app);
  }

  function Produktion_PositionCreate(){
    $this->app->Tpl->Set(HEADING,"Produktion_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("produktion_position_create.tpl");
  }

  function Produktion_PositionEdit(){
    $this->app->Tpl->Set(HEADING,"Produktion_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("produktion_position_edit.tpl");
  }

  function Produktion_PositionCopy(){
    $this->app->Tpl->Set(HEADING,"Produktion_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("produktion_position_copy.tpl");
  }

  function Produktion_PositionDelete(){
    $this->app->Tpl->Set(HEADING,"Produktion_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("produktion_position_delete.tpl");
  }

  function Produktion_PositionList(){
    $this->app->Tpl->Set(HEADING,"Produktion_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("produktion_position_list.tpl");
  }

} 
?>
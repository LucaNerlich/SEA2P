<?php 

class GenArtikelgruppen { 

  function GenArtikelgruppen(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikelgruppenCreate");
    $this->app->ActionHandler("edit","ArtikelgruppenEdit");
    $this->app->ActionHandler("copy","ArtikelgruppenCopy");
    $this->app->ActionHandler("list","ArtikelgruppenList");
    $this->app->ActionHandler("delete","ArtikelgruppenDelete");

    $this->app->Tpl->Set(HEADING,"Artikelgruppen");    $this->app->ActionHandlerListen($app);
  }

  function ArtikelgruppenCreate(){
    $this->app->Tpl->Set(HEADING,"Artikelgruppen (Anlegen)");
      $this->app->PageBuilder->CreateGen("artikelgruppen_create.tpl");
  }

  function ArtikelgruppenEdit(){
    $this->app->Tpl->Set(HEADING,"Artikelgruppen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("artikelgruppen_edit.tpl");
  }

  function ArtikelgruppenCopy(){
    $this->app->Tpl->Set(HEADING,"Artikelgruppen (Kopieren)");
      $this->app->PageBuilder->CreateGen("artikelgruppen_copy.tpl");
  }

  function ArtikelgruppenDelete(){
    $this->app->Tpl->Set(HEADING,"Artikelgruppen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("artikelgruppen_delete.tpl");
  }

  function ArtikelgruppenList(){
    $this->app->Tpl->Set(HEADING,"Artikelgruppen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("artikelgruppen_list.tpl");
  }

} 
?>
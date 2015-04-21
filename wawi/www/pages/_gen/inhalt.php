<?php 

class GenInhalt { 

  function GenInhalt(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","InhaltCreate");
    $this->app->ActionHandler("edit","InhaltEdit");
    $this->app->ActionHandler("copy","InhaltCopy");
    $this->app->ActionHandler("list","InhaltList");
    $this->app->ActionHandler("delete","InhaltDelete");

    $this->app->Tpl->Set(HEADING,"Inhalt");    $this->app->ActionHandlerListen($app);
  }

  function InhaltCreate(){
    $this->app->Tpl->Set(HEADING,"Inhalt (Anlegen)");
      $this->app->PageBuilder->CreateGen("inhalt_create.tpl");
  }

  function InhaltEdit(){
    $this->app->Tpl->Set(HEADING,"Inhalt (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("inhalt_edit.tpl");
  }

  function InhaltCopy(){
    $this->app->Tpl->Set(HEADING,"Inhalt (Kopieren)");
      $this->app->PageBuilder->CreateGen("inhalt_copy.tpl");
  }

  function InhaltDelete(){
    $this->app->Tpl->Set(HEADING,"Inhalt (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("inhalt_delete.tpl");
  }

  function InhaltList(){
    $this->app->Tpl->Set(HEADING,"Inhalt (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("inhalt_list.tpl");
  }

} 
?>
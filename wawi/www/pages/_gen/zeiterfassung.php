<?php 

class GenZeiterfassung { 

  function GenZeiterfassung(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ZeiterfassungCreate");
    $this->app->ActionHandler("edit","ZeiterfassungEdit");
    $this->app->ActionHandler("copy","ZeiterfassungCopy");
    $this->app->ActionHandler("list","ZeiterfassungList");
    $this->app->ActionHandler("delete","ZeiterfassungDelete");

    $this->app->Tpl->Set(HEADING,"Zeiterfassung");   
    $this->app->ActionHandlerListen(&$app);
  }

  function ZeiterfassungCreate(){
    $this->app->Tpl->Set(HEADING,"Zeiterfassung (Anlegen)");
      $this->app->PageBuilder->CreateGen("zeiterfassung_create.tpl");
  }

  function ZeiterfassungEdit(){
    $this->app->Tpl->Set(HEADING,"Zeiterfassung (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("zeiterfassung_edit.tpl");
  }

  function ZeiterfassungCopy(){
    $this->app->Tpl->Set(HEADING,"Zeiterfassung (Kopieren)");
      $this->app->PageBuilder->CreateGen("zeiterfassung_copy.tpl");
  }

  function ZeiterfassungDelete(){
    $this->app->Tpl->Set(HEADING,"Zeiterfassung (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("zeiterfassung_delete.tpl");
  }

  function ZeiterfassungList(){
    $this->app->Tpl->Set(HEADING,"Zeiterfassung (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("zeiterfassung_list.tpl");
  }

} 
?>

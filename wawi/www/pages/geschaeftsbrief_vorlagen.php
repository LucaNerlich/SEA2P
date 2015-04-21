<?php
include ("_gen/geschaeftsbrief_vorlagen.php");

class Geschaeftsbrief_vorlagen extends GenGeschaeftsbrief_vorlagen {
  var $app;
  
  function Geschaeftsbrief_vorlagen($app) {
    //parent::GenGeschaeftsbrief_vorlagen($app);
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Geschaeftsbrief_vorlagenCreate");
    $this->app->ActionHandler("edit","Geschaeftsbrief_vorlagenEdit");
    $this->app->ActionHandler("delete","Geschaeftsbrief_vorlagenDelete");
    $this->app->ActionHandler("list","Geschaeftsbrief_vorlagenList");

    $this->app->ActionHandlerListen($app);
  }

  function Geschaeftsbrief_vorlagenDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM geschaeftsbrief_vorlagen WHERE id='$id'");
    }

    $this->Geschaeftsbrief_vorlagenList();
  }


  function Geschaeftsbrief_vorlagenCreate()
  {
    $this->Geschaeftsbrief_vorlagenMenu();
    parent::Geschaeftsbrief_vorlagenCreate();
  }

  function Geschaeftsbrief_vorlagenList()
  {
    $this->Geschaeftsbrief_vorlagenMenu();
    parent::Geschaeftsbrief_vorlagenList();
  }

  function Geschaeftsbrief_vorlagenMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Dokumenten Vorlagen");
    $this->app->erp->MenuEintrag("index.php?module=geschaeftsbrief_vorlagen&action=create","Vorlage anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=geschaeftsbrief_vorlagen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function Geschaeftsbrief_vorlagenEdit()
  {
    $this->Geschaeftsbrief_vorlagenMenu();

    parent::Geschaeftsbrief_vorlagenEdit();
  }





}

?>

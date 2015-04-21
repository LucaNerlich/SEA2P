<?php

class Einstellungen  {
  var $app;
  
  function Einstellungen($app) {
    $this->app=$app;

    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
      $this->app->Tpl->Set(SUBHEADING,": ".
        $this->app->DB->Select("SELECT nummer FROM artikel WHERE id=$id LIMIT 1"));

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","EinstellungenCreate");
    $this->app->ActionHandler("edit","EinstellungenEdit");
    $this->app->ActionHandler("list","EinstellungenList");


    $this->app->ActionHandlerListen($app);

    $this->app->Tpl->Set(UEBERSCHRIFT,"Einstellungen");
    $this->app->Tpl->Set(FARBE,"[FARBE5]");

    $this->app = $app;
  }


  function EinstellungenCreate()
  {
    $this->app->Tpl->Add(TABS,
      "<a class=\"tab\" href=\"index.php?module=artikel&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>");
  }

  function EinstellungenList()
  {

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Einstellungen");
		$this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","&Uuml;bersicht");
    /*$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE5]\">Einstellungen</h2></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Firmendaten</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Benutzer</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Dokumentenvorlagen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">E-Mail Accounts</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Gesch&auml;ftskonten</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Kassen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Prozessstarter</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Lizenz</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Updates</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php\">&Uuml;bersicht</a></li>");
*/

    $this->app->Tpl->Parse(TAB1,"einstellungen.tpl");
    //$this->app->Tpl->Set(TABTEXT,"Einstellungen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function EinstellungenMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Einstellungen</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">St&uuml;ckliste</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Verkauf</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Einkauf</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Projekte</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Lager</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Dateien</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Provisionen</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=etiketten&id=$id\">Etiketten</a>&nbsp;");
    //$this->app->Tpl->Add(TABS,"<a href=\"index.php?module=artikel&action=kosten&id=$id\">Gesamtkalkulation</a>&nbsp;");
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>&nbsp;");
  }


  function EinstellungenEdit()
  {
    $this->EinstellungenMenu();
    $this->app->Tpl->Set(TABLE_ADRESSE_KONTAKTHISTORIE,"TDB");
    $this->app->Tpl->Set(TABLE_ADRESSE_ROLLEN,"TDB");

    $this->app->Tpl->Set(TABLE_ADRESSE_USTID,"TDB");

  }





}

?>

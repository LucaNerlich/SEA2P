<?php

class Logfile {
  var $app;
  
  function Logfile($app) {
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","LogfileList");
    $this->app->ActionHandler("delete","LogfileDelete");
    $this->app->ActionHandler("deleteall","LogfileDeleteAll");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }

	function LogfileDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		$this->app->DB->Delete("DELETE FROM logfile WHERE id='$id' LIMIT 1");
	  $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Der Logeintrag wurde gel&ouml;scht!</div>  ");
    header("Location: index.php?module=logfile&action=list&msg=$msg");
    exit;	
	}	


	function LogfileDeleteAll()
	{
		$id = $this->app->Secure->GetGET("id");
		$this->app->DB->Delete("DELETE FROM logfile WHERE id > 0");
	  $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Alle Logeintr&auml;ge wurden wurden gel&ouml;scht!</div>  ");
    header("Location: index.php?module=logfile&action=list&msg=$msg");
    exit;	
	}	

  function LogfileList()
  {
    $this->LogfileMenu();
    $this->app->YUI->TableSearch(TAB1,"logfile");
		$this->app->Tpl->Parse(PAGE,"logfile_list.tpl");
  }

  function LogfileMenu()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Logdatei");
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=logfile&action=list","Aktualisieren");
    $this->app->erp->MenuEintrag("index.php?module=logfile&action=deleteall","Alle Eintr&auml;ge l&ouml;schen");
  }


}

?>

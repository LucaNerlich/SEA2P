<?php

class Help
{

  function Help(&$app)
  {
    $this->app=$app;
  }



	function Run()
	{
    $module = ucfirst($this->app->Secure->GetGET("module"));
    $action = ucfirst($this->app->Secure->GetGET("action"));

		$methodname = $module.$action;

    if(method_exists($this,$methodname))
    {
      $this->app->Tpl->Add(HELP,call_user_func( array( &$this, $methodname ), $this, null ));
    } else {
      $this->app->Tpl->Set(HELPDISABLEOPEN,"<!--");
      $this->app->Tpl->Set(HELPDISABLECLOSE,"-->");
    }
	}

/*
	function AngebotCreate()
	{
		return "angebot anlegen";
	}

	function AngebotList()
	{
		return "angebot list";
	}
*/


}

?>

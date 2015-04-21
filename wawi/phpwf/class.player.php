<?php
/* Author: Benedikt Sauter, sauter@sistecs.de, 2007
 * Player for PHP Applications
 */

class Player {

  public $DefautTemplates;
  public $DefautTheme;

  // the application object
  public $app;

  function Player()
  {
    $this->DefautTemplates="defaulttemplates";
    $this->DefautTheme="default";
  }

  function SetDefaultTemplates($path)
  {
  }

  function SetDefaultTheme($path)
  {

  }

  function BuildNavigation()
  {
    $type = $this->app->User->GetType();
		$version = $this->app->erp->Version();
	
		if($version=="com")
			$this->app->Page->CreateNavigation($this->app->erp->NavigationCOM($this->app->User->GetType())); 
		else if($version=="freelancer")
			$this->app->Page->CreateNavigation($this->app->erp->NavigationFREELANCER($this->app->User->GetType())); 
		else if($version=="stock")
			$this->app->Page->CreateNavigation($this->app->erp->NavigationStock($this->app->User->GetType())); 
		else
			$this->app->Page->CreateNavigation($this->app->erp->NavigationOSS($this->app->User->GetType())); 
  }

  function Run($sessionObj)
  {
    $this->app = $sessionObj->app;
    // play application only when layer 2 said that its ok
    if(!$sessionObj->GetCheck()) {
      if($sessionObj->reason=="PLEASE_LOGIN")
      {
				$module = "welcome";
				$action = "login";
				$this->app->Secure->GET[module]="welcome";
				$this->app->Secure->GET[action]="login";
				//header("Location: index.php?module=welcome&action=login");
				//exit;
      } else {
				//echo "verboten: ".$sessionObj->reason;
      }
    } else {
      // Get actual commands from URL
      $module = $this->app->Secure->GetGET('module','alpha');
      $action = $this->app->Secure->GetGET('action','alpha');
      if($module =="") {
				$module = "welcome";
				$action = "main";
      } 
    } 

		if($action!="list" && $action!="css" && $action!="logo" && $action!="poll" && $module!="ajax" && $module!="protokoll")
    	$this->app->erp->Protokoll();

    // plugin instanzieren
    // start module
    if(file_exists("pages/".$module.".php")){
      include("pages/".$module.".php");
      //create dynamical an object
      $constr=strtoupper($module{0}).substr($module, 1);
      $myApp = new $constr($this->app);
    }
    else {
      if(file_exists("pages/_gen/".$module.".php")){
				include("pages/_gen/".$module.".php");
				//create dynamical an object
				$constr="Gen".strtoupper($module{0}).substr($module, 1);
				$myApp = new $constr($this->app);
      }
      else {
				//echo "Dieses Modul gibt es nicht!";
				//echo $this->app->WFM->Error("Module <b>$module</b> doesn't exists in pages/");
      }
    }
    //$this->app->calledWhenAuth($this->app->User->GetType());
    // jetzt noch alles anzeigen
    //$this->app->Tpl->ReadTemplatesFromPath("../../conductor/themes/[THEME]/templates/");
    //$this->app->Tpl->ReadTemplatesFromPath("../../conductor/themes/[THEME]/templates/");

    if($this->app->BuildNavigation==true)
      $this->BuildNavigation();

    $this->app->endtime = microtime(); 

    list($startlow, $starthigh) = explode(" ", $this->app->starttime);
    $start = $starthigh + $startlow;
    list($low, $high) = explode(" ", $this->app->endtime);
    $t    = $high + $low;
    $used = $t - $start;
   
    $this->app->Tpl->Add(VERSIONUNDSTATUS,"&nbsp;|&nbsp;Generated page in ".$used." sec");

    $right = $this->app->Secure->GetGET("right");

		$firmenfarbedunkel = $this->app->erp->Firmendaten("firmenfarbedunkel");
		if($firmenfarbedunkel =="")
      $firmenfarbedunkel = "#3fbac9";
    $this->app->Tpl->Set(TPLFIRMENFARBEDUNKEL,$firmenfarbedunkel);
      
    if($this->app->BuildNavigation==true)
    {
      if($right==1) 
      echo $this->app->Tpl->FinalParse('right.tpl');
      else
      {
				if($module=="welcome" && $action=="login")
					echo $this->app->Tpl->FinalParse('loginpage.tpl');
				else {
					if($this->app->erp->UserDevice()=="smartphone")
						echo $this->app->Tpl->FinalParse('page_smartphone.tpl');
					else
						echo $this->app->Tpl->FinalParse('page.tpl');
				}
      }
    }
    else
      echo $this->app->Tpl->FinalParse('popup.tpl');
  }

}

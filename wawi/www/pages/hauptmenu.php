<?php

class Hauptmenu {
  var $app;
  
  function Hauptmenu($app) {
    //parent::GenHauptmenu($app);
    $this->app=&$app;


    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","HauptmenuList");


    $this->app->ActionHandlerListen($app);
    $this->app->Tpl->Set(UEBERSCHRIFT,"Hauptmen&uuml;");

    $this->app = $app;
  }



  function HauptmenuList()
  {

//this->WFconf[menu][mitarbeiter]
    $menu = $this->app->Conf->WFconf[menu][$this->app->User->GetType()];
    $this->app->Tpl->Add(PAGE,"<table border=\"0\" width=\"100%\" style=\"background-color: #ffffff;\"><tr valign=\"top\">");

    $spalten_anzahl = 5;

    $i=0;
    if(count($menu)>0){
      foreach($menu as $key=>$value){
        $i++;
        if($value[first][2]!="")
//          $this->app->Tpl->Set(FIRSTNAV,' href="index.php?module='.$value[first][1].'&action='.$value[first][2].'"
          $this->app->Tpl->Set(FIRSTNAV,'

          >'.$value[first][0].'</a>');
        else
          $this->app->Tpl->Set(FIRSTNAV,' href="index.php?module='.$value[first][1].'"
          >'.$value[first][0].'</a>');

        $this->app->Tpl->Add(PAGE,"<td>");
        $this->app->Tpl->Parse(PAGE,'firstnav.tpl');
        $spalten++;
        if(count($value[sec])>0){
          $this->app->Tpl->Add(PAGE,'<ul>');
          foreach($value[sec] as $secnav){
            if($secnav[2]!="")
              $this->app->Tpl->Set(SECNAV,' href="index.php?module='.$secnav[1].'&action='.$secnav[2].'"
              >'.$secnav[0].'</a>');
            else
              $this->app->Tpl->Set(SECNAV,' href="index.php?module='.$secnav[1].'">'.$secnav[0].'</a>');

            $this->app->Tpl->Parse(PAGE,'secnav.tpl');
          }
        $this->app->Tpl->Add(PAGE,"</td>");
        if($spalten % $spalten_anzahl == 0)
	{
          $this->app->Tpl->Add(PAGE,"</tr><tr valign=\"top\">");
	}
        }
      }
    }
    $restliche_td = $spalten % $spalten_anzahl;

    for($i=0;$i<$restliche_td;$i++) 
      $this->app->Tpl->Add(PAGE,"<td></td>");
    $this->app->Tpl->Add(PAGE,"</tr></table>");



  }





}

?>

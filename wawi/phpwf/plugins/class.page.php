<?php

/// central config board for the engine
class Page 
{
  var $engine;
  function Page(&$app)
  {
    $this->app = &$app;
    //$this->engine = &$engine;
  }

  /// load a themeset set
  function LoadTheme($theme)
  {
    //$this->app->Tpl->ReadTemplatesFromPath("themes/$theme/templates/");
    $this->app->Tpl->ReadTemplatesFromPath("themes/$theme/templates/");
  }

  /// show complete page
  function Show()
  {
    return $this->app->Tpl->FinalParse('page.tpl');
  }

  /// build navigation tree
  function CreateNavigation($menu)
  {
    $i=0;

    //if($this->app->erp->Firmendaten("standardaufloesung")=="1"){
    //if($this->app->erp->Version()=="stock")
    //StammdatenVerkaufEinkaufWareneingangBuchhaltungMarketingVerwaltungLagerAdministrationMein Bereich 

    $navwidth = array('Stammdaten'=>95,
                      'Verkauf'=>70,
                      'Einkauf'=>70,
                      'Wareneingang'=>105,
                      'Buchhaltung'=>100,
                      'Marketing'=>80,                  
                      'Verwaltung'=>90,
                      'Lager'=>60,
                      'Administration'=>119,
                      'Mein Bereich'=>109,
                      'Suche'=>194);

    //$navwidth = array(75,50,50,85,80,60,70,40,89,80,194); // alt
    $subnavwidth = array('Stammdaten'=>79,
                      'Verkauf'=>54,
                      'Einkauf'=>89,
                      'Wareneingang'=>89,
                      'Buchhaltung'=>174,
                      'Marketing'=>120,                  
                      'Verwaltung'=>120,
                      'Lager'=>130,
                      'Administration'=>93,
                      'Mein Bereich'=>84,
                      'Suche'=>80);

 

    if($this->app->erp->Firmendaten("standardaufloesung")!="1")
    {
      /*
         $menu[][first]  = array('Suche:&nbsp;
         <form action="index.php?module=welcome&action=direktzugriff" method="post">
         <input name="direktzugriff" id="direktzugriff"  type="text" size="20" style="font-size:9pt; margin:0px; padding:0px;"></form>','a','direktzugriff'); 
       */

    }

    if(count($menu)>0){
      foreach($menu as $key=>$value){
        if($value[first][2]!="direktzugriff")
        {
          if($value[first][2]!="")
            $this->app->Tpl->Set(FIRSTNAV,' style="width:'.$navwidth[$value[first][0]].'px;padding:12px;" >'.$value[first][0].'</a>');
          else
            $this->app->Tpl->Set(FIRSTNAV,' style="width:'.$navwidth[$value[first][0]].'px;padding:12px" href="index.php?module='.$value[first][1].'&top='.base64_encode($value[first][0]).'" >'.$value[first][0].'</a>');
        } else {
          if($value[first][2]!="")
            $this->app->Tpl->Set(FIRSTNAV,' style="width:'.$navwidth[$value[first][0]].'px;padding-top:10.5px; padding-bottom:11px;" >'.$value[first][0].'</a>');
        }

        $this->app->Tpl->Parse(NAV,'firstnav.tpl');
        if(count($value[sec])>0){
          $this->app->Tpl->Add(NAV,'<ul>');
          foreach($value[sec] as $secnav){
            if($secnav[2]!="")
              $this->app->Tpl->Set(SECNAV,' style="width:'.$subnavwidth[$value[first][0]].'px" href="index.php?module='.$secnav[1].'&action='.$secnav[2].'&top='.base64_encode($value[first][0]).'"
                  >'.$secnav[0].'</a>');
            else
              $this->app->Tpl->Set(SECNAV,' style="width:'.$subnavwidth[$value[first][0]].'px" href="index.php?module='.$secnav[1].'&top='.base64_encode($value[first][0]).'">'.$secnav[0].'</a>');

            $this->app->Tpl->Parse(NAV,'secnav.tpl');
          }
          $this->app->Tpl->Add(NAV,"</ul></li>");
        }

        $i++;
      }
    }
  }

  }
  ?>

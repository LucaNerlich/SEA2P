<?php
class Uservorlage 
{
  function Uservorlage(&$app)
  {
    $this->app=&$app; 

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","UservorlageCreate");
    $this->app->ActionHandler("delete","UservorlageDelete");
    $this->app->ActionHandler("edit","UservorlageEdit");
    $this->app->ActionHandler("list","UservorlageList");
    $this->app->ActionHandler("chrights","UserChangeRights");
    $this->app->ActionHandler("abgleich","UservorlageAbgleich");

    $this->app->DefaultActionHandler("login");
    //		$this->Templates = $this->GetTemplates();


    $this->app->ActionHandlerListen($app);
  }

  function UservorlageAbgleich()
  {

    $this->app->erp->AbgleichBenutzerVorlagen();

    $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Rechte wurden abgeglichen!</div>");
    header("Location: index.php?module=uservorlage&action=list&msg=$msg");
    exit;
  }

  function UservorlageList()
  {
    $msg2 = $this->app->Secure->GetGET("msg");
    $msg2 = $this->app->erp->base64_url_decode($msg2);
    $letzte_zeichen = strrpos ($msg2,'</div>');
    $msg2 = substr ($msg2,0,$letzte_zeichen+6);
    $this->app->Tpl->Set(MESSAGE,$msg2);

    //		$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Uservorlage");
    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=create","Vorlage anlegen");
    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=abgleich","Rechte abgleichen");
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    //  $this->app->Tpl->Set(SUBSUBHEADING,"User");

    $this->app->YUI->TableSearch(USER_TABLE,"uservorlagelist");
    $this->app->Tpl->Parse(PAGE, "uservorlage_list.tpl");

  }


  function UservorlageDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    // Lager reseten
    $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM uservorlage WHERE id='$id'");
    $this->app->DB->Delete("DELETE FROM uservorlage WHERE id='$id'");

    $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Der Benutzer Vorlage \"$bezeichnung\" wurde gel&ouml;scht</div>");

    $this->UservorlageList();
  }


  function UservorlageCreate()
  {
    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $input = $this->GetInput();
    $submit = $this->app->Secure->GetPOST('submituser');

    if($submit!='') {
      $error = '';
      if($input['bezeichnung']=='') $error .= 'Geben Sie bitte einen Uservorlagenamen ein.<br>';		
      if($this->app->DB->Select("SELECT '1' FROM uservorlage WHERE bezeichnung='{$input['bezeichnung']}' LIMIT 1")=='1')
        $error .= "Es existiert bereits ein Uservorlage mit diesem Namen";

      if($error!='')
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      else {
        $id = $this->app->erp->CreateBenutzerVorlage($input);

        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Die Benutzervorlage wurde erfolgreich angelegt</div>");
        header("Location: index.php?module=uservorlage&action=edit&id=$id&msg=$msg");
        exit;
      }
    }

    $this->SetInput($input);
    $this->app->Tpl->Parse(PAGE, "uservorlage_create.tpl");
  }

  function UservorlageEdit()
  {
    $id = $this->app->Secure->GetGET('id');
    $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM uservorlage WHERE id='$id'");
    //		$this->app->Tpl->Add(KURZUEBERSCHRIFT2,$bezeichnung);

    $this->app->erp->MenuEintrag("index.php?module=uservorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $id = $this->app->Secure->GetGET('id');
    $input = $this->GetInput();
    $submit = $this->app->Secure->GetPOST('submituser');
    $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM uservorlage WHERE id='$id' LIMIT 1");

    if($bezeichnung!="")$tmp = $bezeichnung;
    $this->app->Tpl->Add(KURZUEBERSCHRIFT2,$tmp);

    if(is_numeric($id) && $submit!='') {
      $error = '';
      if($input['bezeichnung']=='') $error .= 'Geben Sie bitte eine Bezeichnung ein.<br>';

      if($error!='')
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">$error</div>");
      else {
        $this->app->DB->Update("UPDATE uservorlage SET bezeichnung='{$input['bezeichnung']}', beschreibung='{$input['beschreibung']}'
            WHERE id='$id' LIMIT 1");

        $this->app->erp->AbgleichBenutzerVorlagen();
        $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
      }	
    }

    $data = $this->app->DB->SelectArr("SELECT * FROM uservorlage WHERE id='$id' LIMIT 1");

    $this->SetInput($data[0]);

    $this->UserRights();
    $this->app->Tpl->Parse(PAGE, "uservorlage_create.tpl");
  }

  function GetInput()
  {
    $input = array();
    $input['beschreibung'] = $this->app->Secure->GetPOST('beschreibung');
    $input['bezeichnung'] = $this->app->Secure->GetPOST('bezeichnung');
    return $input;
  }

  function SetInput($input)
  {
    $this->app->Tpl->Set('BESCHREIBUNG', $input['beschreibung']);
    $this->app->Tpl->Set('BEZEICHNUNG', $input['bezeichnung']);
  }


  function UserRights()
  {
    $id = $this->app->Secure->GetGET('id');
    $copytemplate = $this->app->Secure->GetPOST('copyusertemplate');

    $modules = $this->ScanModules();

    if($template!='') {
      $mytemplate = $this->app->Conf->WFconf[permissions][$template];
      $this->app->DB->Delete("DELETE FROM uservorlagerights WHERE vorlage='$id'");
      $sql = 'INSERT INTO uservorlagerights (vorlage, module, action, permission) VALUES ';

      $modulecount = count($modules);
      $curModule = 0;
      foreach($modules as $module=>$actions) {
        $lower_m = strtolower($module);	
        $curModule++;
        $actioncount = count($actions);
        for($i=0;$i<$actioncount;$i++) {
          $delimiter = (($curModule<$modulecount || $i+1<$actioncount) ? ', ' : ';');  
          $active = ((isset($mytemplate[$lower_m]) && in_array($actions[$i], $mytemplate[$lower_m])) ? '1' : '0');
          $sql .= "('$id', '$lower_m', '{$actions[$i]}', '$active')$delimiter";
        }
      }
      $this->app->DB->Query($sql);

      $this->app->erp->AbgleichBenutzerVorlagen();
    }

    if($copytemplate!='') {
      //			echo "User $id $copytemplate";	
      $this->app->DB->Delete("DELETE FROM uservorlagerights WHERE user='$id'");
      $this->app->DB->Update("INSERT INTO uservorlagerights (vorlage, module,action,permission) (SELECT '$id',module, action,permission FROM uservorlagerights WHERE vorlage='".$copytemplate."')");
    }

    $dbrights = $this->app->DB->SelectArr("SELECT module, action, permission FROM uservorlagerights WHERE vorlage='$id' ORDER BY module");

    $modules = $this->ScanModules();
    if(is_array($dbrights) && count($dbrights)>0)
      $rights = $this->AdaptRights($dbrights, $rights, $group);

    $table = $this->CreateTable($id, $modules, $rights);	

    $this->app->Tpl->Set('BEZEICHNUNGSELECT', $this->app->erp->GetSelectUserVorlage("",$id));	
    $this->app->Tpl->Set('MODULES', $table);
  }

  function UserChangeRights()
  {
    $vorlage = $this->app->Secure->GetGET('b_vorlage');
    $module = $this->app->Secure->GetGET('b_module');
    $action = $this->app->Secure->GetGET('b_action');
    $value = $this->app->Secure->GetGET('b_value');

    if(is_numeric($vorlage) && $module!='' && $action!='' && $value!='') {
      $id = $this->app->DB->Select("SELECT id FROM uservorlagerights WHERE vorlage='$vorlage' AND module='$module' AND action='$action' LIMIT 1");
      if(is_numeric($id) && $id>0)
        $this->app->DB->Update("UPDATE uservorlagerights SET permission='$value' WHERE id='$id' LIMIT 1");
      else {
        $this->app->DB->Insert("INSERT INTO uservorlagerights (vorlage, module, action, permission) VALUES ('$vorlage', '$module', '$action', '$value')");
      }

      $this->app->erp->AbgleichBenutzerVorlagen();
    }

    echo $this->app->DB->Select("SELECT permission FROM uservorlagerights WHERE vorlage='$vorlage' AND module='$module' AND action='$action' LIMIT 1");

    exit;
  }



  function AdaptRights($dbarr, $rights) 
  {
    $cnt = count($dbarr);
    for($i=0;$i<$cnt;$i++) {
      $module = $dbarr[$i]['module'];
      $action = $dbarr[$i]['action'];
      $perm = $dbarr[$i]['permission'];

      if(isset($rights[$module])) {
        if($perm=='1' && !in_array($action, $rights[$module])) 
          $rights[$module][] = $action;

        if($perm=='0' && in_array($action, $rights[$module])) {
          $index = array_search($action, $rights[$module]);
          unset($rights[$module][$index]);
          $rights[$module] = array_values($rights[$module]);
        }
      }else if($perm=='1') $rights[$module][] = $action;
    }
    return $rights;
  }

  function CreateTable($vorlage, $modules, $rights) 
  {
    $maxcols = 6;
    $width = 100 / $maxcols;
    $out = '';
    foreach($modules as $key=>$value) {
      $out .= "<tr><td class=\"name\">$key</td></tr>";

      $out .= "<tr><td><table class=\"action\">";
      $module = strtolower($key); 
      for($i=0;$i<$maxcols || $i<count($value);$i++) {
        if($i%$maxcols==0) $out .= "<tr>";

        if(isset($value[$i]) && in_array($value[$i], $rights[$module])) {
          $class = 'class="blue"';
          $active = '1';
        }else{
          $class = 'class="grey"';
          $active = 0;
        }
        $class = ((isset($value[$i])) ? $class : '');

        $action = ((isset($value[$i])) ? strtolower($value[$i]) : '');
        $onclick = ((isset($value[$i])) ? "onclick=\"ChangeRights(this, '$vorlage','$module','$action')\"" : '');
        $out .= "<td width=\"$width%\" $class value=\"$active\" $onclick>{$action}</td>";

        if($i%$maxcols==($maxcols-1)) $out .= "</tr>";
      }
      $out .= "</table></td></tr>";
    }

    return $out;
  }

  function ScanModules()
  {
    $files = glob('./pages/*.php');

                            $modules = array();
                            foreach($files as $page) {
                            $name = ucfirst(basename($page,'.php'));

                            $content = file_get_contents($page);		

                            $foundItems = preg_match_all('/ActionHandler\(\"[[:alnum:]].*\",/', $content, $matches);
                            if($foundItems > 0) {
                            $action = str_replace(array('ActionHandler("','",'),'', $matches[0]);
                            for($i=0;$i<count($action);$i++)
                            $modules[$name][] = $action[$i];	
                            sort($modules[$name]);
                            }
                            }
                            return $modules;	
                            }
                            }
                            ?>

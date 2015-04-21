<?php

class Adapterbox {
  var $app;
  
  function Adapterbox($app) {
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","AdapterboxList");
    $this->app->ActionHandler("delete","AdapterboxDelete");
    $this->app->ActionHandler("config","AdapterboxKonfiguration");
    $this->app->ActionHandler("deleteall","AdapterboxDeleteAll");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }

	function AdapterboxKonfiguration()
	{
    $this->AdapterboxMenu();
		$fields = array('dns','ipadresse','gateway','netmask','ssid','passphrase','url','devicekey');
		$this->app->erp->ParseFormVars($fields);

		$wlan = $this->app->Secure->GetPOST("wlan");
		$dhcp = $this->app->Secure->GetPOST("dhcp");

		if($wlan) $this->app->Tpl->Set(WLAN,"checked");
		if($dhcp) $this->app->Tpl->Set(DHCP,"checked");

		$submit = $this->app->Secure->GetPOST("submit");

		$settings['ip']="";
		$settings['subnetmask']="";
		$settings['gateway']="";
		$settings['dns']="";
		$settings['wlan']=false;
		$settings['dhcp']=true;
		$settings['ssid']="";
		$settings['passphrase']="";

		$settings['url']="";
		$settings['devicekey']="";

		if($submit!="")
		{
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=wawision.php");
echo '<?php'."\r\n";
echo '$settings["ip"]="'.$this->app->Secure->GetPOST("ipadresse").'";'."\r\n";
echo '$settings["subnetmask"]="'.$this->app->Secure->GetPOST("netmask").'";'."\r\n";
echo '$settings["dns"]="'.$this->app->Secure->GetPOST("dns").'";'."\r\n";

if($this->app->Secure->GetPOST("wlan")=="1")
echo '$settings["wlan"]=true;'."\r\n";
else
echo '$settings["wlan"]=false;'."\r\n";

if($this->app->Secure->GetPOST("dhcp")=="1")
echo '$settings["dhcp"]=true;'."\r\n";
else
echo '$settings["dhcp"]=false;'."\r\n";

echo '$settings["ssid"]="'.$this->app->Secure->GetPOST("ssid").'";'."\r\n";
echo '$settings["passphrase"]="'.$this->app->Secure->GetPOST("passphrase").'";'."\r\n";
echo '$settings["url"]="'.$this->app->Secure->GetPOST("url").'";'."\r\n";
echo '$settings["devicekey"]="'.$this->app->Secure->GetPOST("devicekey").'";'."\r\n";

echo '?>';
			exit;
		} else {
			$this->app->Tpl->Parse(PAGE,"adapterbox_config.tpl");
		}
	}	

	function AdapterboxDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		$this->app->DB->Delete("DELETE FROM adapterbox_log WHERE id='$id' LIMIT 1");
	  $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Der Logeintrag wurde gel&ouml;scht!</div>  ");
    header("Location: index.php?module=adapterbox&action=list&msg=$msg");
    exit;	
	}	


	function AdapterboxDeleteAll()
	{
		$id = $this->app->Secure->GetGET("id");
		$this->app->DB->Delete("DELETE FROM adapterbox_log WHERE id > 0");
	  $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Alle Logeintr&auml;ge wurden wurden gel&ouml;scht!</div>  ");
    header("Location: index.php?module=adapterbox&action=list&msg=$msg");
    exit;	
	}	

  function AdapterboxList()
  {
    $this->AdapterboxMenu();
    $this->app->YUI->TableSearch(TAB1,"adapterbox_log");
		$this->app->Tpl->Parse(PAGE,"adapterbox_list.tpl");
  }

  function AdapterboxMenu()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Logdatei");
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=list","Aktualisieren");
    $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=config","Konfiguration");
    $this->app->erp->MenuEintrag("index.php?module=adapterbox&action=deleteall","Alle Eintr&auml;ge l&ouml;schen");
  }


}

?>

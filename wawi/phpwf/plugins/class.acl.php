<?php


class Acl 
{
  //var $engine;
  function Acl(&$app)
  {
    $this->app = &$app;
  }


  function CheckTimeOut()
  {
		$this->session_id = session_id();

		if($_COOKIE["CH42SESSION"]!="")
		{
			$this->session_id = $_COOKIE["CH42SESSION"];
      $this->app->DB->Update("UPDATE useronline SET time=NOW() WHERE sessionid='".$_COOKIE[CH42SESSION]."' LIMIT 1");
    }

    // check if user is applied 
	  if($this->app->Conf->WFdbType=="postgre")
    {
    	$sessid =  $this->app->DB->Select("SELECT sessionid FROM useronline,\"user\" WHERE
       login='1' AND sessionid='".$this->session_id."' AND \"user\".id=useronline.user_id AND \"user\".activ='1' LIMIT 1");
    } else 
    {
   		// 	$this->app->DB->Delete("DELETE FROM useronline WHERE user_id='".$this->app->User->GetID()."' AND sessionid!='".$this->session_id."'");
    	$sessid =  $this->app->DB->Select("SELECT sessionid FROM useronline,user WHERE
       login='1' AND sessionid='".$this->session_id."' AND user.id=useronline.user_id AND user.activ='1' LIMIT 1");
		}
    
    if($this->session_id == $sessid)
    { 
      // check if time is expired
			if($this->app->Conf->WFdbType=="postgre") {
      	$time =  $this->app->DB->Select("SELECT extract(epoch from time) FROM useronline,\"user\" WHERE
       		login='1' AND sessionid='".$this->session_id."' AND \"user\".id=useronline.user_id AND \"user\".activ='1' LIMIT 1");
				
			} else {
				$time =  $this->app->DB->Select("SELECT UNIX_TIMESTAMP(time) FROM useronline,user WHERE
       		login='1' AND sessionid='".$this->session_id."' AND user.id=useronline.user_id AND user.activ='1' LIMIT 1");
			}

      if((time()-$time) > $this->app->Conf->WFconf[logintimeout])
      {
				if($_COOKIE["CH42SESSION"]=="")
				{
	      	//$this->app->WF->ReBuildPageFrame();
	      	$this->Logout("Ihre Zeit ist abgelaufen, bitte melden Sie sich erneut an.",true);
	      	return false;
				}
      }
      else {
			// update time
			if($this->app->Conf->WFdbType=="postgre") {
					$this->app->DB->Update("UPDATE useronline SET time=NOW() WHERE
            login='1' AND sessionid='".$this->session_id."'");
      } else {
	 			$this->app->DB->Update("UPDATE useronline,user SET useronline.time=NOW() WHERE
            login='1' AND sessionid='".$this->session_id."' AND user.id=useronline.user_id AND user.activ='1'");

			}     
         session_write_close(); // Blockade wegnehmen           
                
	return true; 
      }
    }

  }

  function Check($usertype,$module,$action, $userid='')
  {
    $ret = false;
    $permissions = $this->app->Conf->WFconf[permissions][$usertype][$module];

		if($usertype=="admin")
			return true;

		if($module=="welcome" && $action=="css") return true;
		if($module=="welcome" && $action=="logo") return true;
		if($module=="gpsstechuhr" && $action=="create") return true;
		if($module=="gpsstechuhr" && $action=="save") return true;

		// Change Userrights with new 'userrights'-Table	
		if(!is_array($permissions)) $permissions = array();
		if(is_numeric($userid) && $userid>0) {
			$permission_db = $this->app->DB->Select("SELECT permission FROM userrights WHERE module='$module' AND action='$action' AND user='$userid' LIMIT 1");
			$actionkey = array_search($action, $permissions);
			if($actionkey===false) {
				if($permission_db=='1')
					$permissions[] = $action;
			}else {
				if($permission_db=='0'){
					unset($permissions[$actionkey]);
					$permissions = array_values($permissions);
				}				
			}
		}
		// --- END ---
 
    while (list($key, $val) = @each($permissions)) 
    {
      if($val==$action || $usertype=="admin")
      {
				$ret = true;
				break;
      }
    }
		
		// TODO pruefen ob das so past
    if($action=="" && $module=="")
      $ret = true;
  
		//if($this->app->User->GetID()<=0)
    //  $this->app->Tpl->Parse(PAGE,"sessiontimeout.tpl");
		// wenn es nicht erlaubt ist 
    if($ret!=true)
    {
			if($this->app->User->GetID()<=0)
			{
      	echo str_replace('BACK',"index.php?module=welcome&action=login",$this->app->Tpl->FinalParse("permissiondenied.tpl"));
			}
			else
      	echo str_replace('BACK',$_SERVER['HTTP_REFERER'],$this->app->Tpl->FinalParse("permissiondenied.tpl"));
			exit;
    }
    return $ret;
  }

  function Login()
  {
    $username = $this->app->Secure->GetPOST("username");
    $password = $this->app->Secure->GetPOST("password");
    $token = $this->app->Secure->GetPOST("token");

  
    if($username=="" && ($password=="" || $token=="")){
      $this->app->Tpl->Set(LOGINMSG,"Bitte geben Sie Benutzername und Passwort ein.");  
      $this->app->Tpl->Parse(PAGE,"login.tpl");
    }
   /* elseif($username==""||$password==""){
      $this->app->Tpl->Set(LOGINERRORMSG,"Bitte geben Sie einen Benutzername und ein Passwort an.");  
      $this->app->Tpl->Parse(PAGE,"login.tpl");
    }*/
    else {
  		// Benutzer hat Daten angegeben
      $encrypted = $this->app->DB->Select("SELECT password FROM user
        WHERE username='".$username."' AND activ='1' LIMIT 1");

      $encrypted_md5 = $this->app->DB->Select("SELECT passwordmd5 FROM user
        WHERE username='".$username."' AND activ='1' LIMIT 1");

      $fehllogins= $this->app->DB->Select("SELECT fehllogins FROM user
        WHERE username='".$username."' AND activ='1' LIMIT 1");

			//$fehllogins=0;

      $type= $this->app->DB->Select("SELECT type FROM user
        WHERE username='".$username."' AND activ='1' LIMIT 1");

      $externlogin= $this->app->DB->Select("SELECT externlogin FROM user
        WHERE username='".$username."' AND activ='1' LIMIT 1");

      $hwtoken = $this->app->DB->Select("SELECT hwtoken FROM user
        WHERE username='".$username."' AND activ='1' LIMIT 1");


      // try login and set user_login if login was successfull
			// wenn intern geht immer passwort???
			//$hwtoken=0;

			// MOTP

      $user_id="";

      $userip = $_SERVER['REMOTE_ADDR'];
      $ip_arr = split('\.',$userip);

      if($ip_arr[0]=="192" || $ip_arr[0]=="10" || $ip_arr[0]=="127")
        $localconnection = 1;
      else 
        $localconnection = 0;


      //HACK intern immer Passwort
      if($localconnection==1)
        $hwtoken=0;
 
      if($hwtoken==1) //motp
      {
        $pin = $this->app->DB->Select("SELECT motppin FROM user
              WHERE username='".$username."' AND activ='1' LIMIT 1");
        
        $secret = $this->app->DB->Select("SELECT motpsecret FROM user
              WHERE username='".$username."' AND activ='1' LIMIT 1");

        if($this->mOTP($pin,$token,$secret) && $fehllogins<6)
        {
          $user_id = $this->app->DB->Select("SELECT id FROM user
              WHERE username='".$username."' AND activ='1' LIMIT 1");
        } else { $user_id = ""; }

      } 
			//picosafe login
			else if ($hwtoken==2)
			{
				//include("/var/www/wawision/trunk/phpwf/plugins/class.picosafelogin.php");
				$myPicosafe = new PicosafeLogin();

				$aes = $this->app->DB->Select("SELECT hwkey FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
				$datablock = $this->app->DB->Select("SELECT hwdatablock FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");
				$counter = $this->app->DB->Select("SELECT hwcounter FROM user WHERE username='".$username."' AND activ='1' LIMIT 1");

				$myPicosafe->SetUserAES($aes);
				$myPicosafe->SetUserDatablock($datablock);
				$myPicosafe->SetUserCounter($counter);		

				if($encrypted_md5!="")
				{
								if ( $myPicosafe->LoginOTP($token) && md5($password) == $encrypted_md5  && $fehllogins<6)
								{
										$user_id = $this->app->DB->Select("SELECT id FROM user
											WHERE username='".$username."' AND activ='1' LIMIT 1");

										// Update counter
										$newcounter = $myPicosafe->GetLastValidCounter();
										$this->app->DB->Update("UPDATE user SET hwcounter='$newcounter' WHERE id='$user_id' LIMIT 1");

								} else {
										//echo $myPicosafe->error_message;
										$user_id = "";
								}
				} else {

								if ( $myPicosafe->LoginOTP($token) && crypt( $password,  $encrypted ) == $encrypted  && $fehllogins<6)
								{
										$user_id = $this->app->DB->Select("SELECT id FROM user
											WHERE username='".$username."' AND activ='1' LIMIT 1");

										// Update counter
										$newcounter = $myPicosafe->GetLastValidCounter();
										$this->app->DB->Update("UPDATE user SET hwcounter='$newcounter' WHERE id='$user_id' LIMIT 1");

								} else {
										//echo $myPicosafe->error_message;
										$user_id = "";
								}
				}
			}
			else {
				if($encrypted_md5!=""){
								if (md5($password ) == $encrypted_md5  && $fehllogins<6)
								{
									if($this->app->Conf->WFdbType=="postgre"){
										$user_id = $this->app->DB->Select("SELECT id FROM \"user\"
											WHERE username='".$username."' AND activ='1' LIMIT 1");
									} else {
											$user_id = $this->app->DB->Select("SELECT id FROM user
											WHERE username='".$username."' AND activ='1' LIMIT 1");

									}
								}
								else { $user_id = ""; }
				} else {
						if (crypt( $password,  $encrypted ) == $encrypted  && $fehllogins<6)
								{
									if($this->app->Conf->WFdbType=="postgre"){
										$user_id = $this->app->DB->Select("SELECT id FROM \"user\"
											WHERE username='".$username."' AND activ='1' LIMIT 1");
									} else {
											$user_id = $this->app->DB->Select("SELECT id FROM user
											WHERE username='".$username."' AND activ='1' LIMIT 1");

									}
								}
								else { $user_id = ""; }
				}
      }

      //$password = substr($password, 0, 8); //TODO !!! besseres verfahren!!
      
      //pruefen ob extern login erlaubt ist!!
     
      // wenn keine externerlogin erlaubt ist und verbindung extern
      if($externlogin==0 && $localconnection==0)
      {
				$this->app->Tpl->Set(LOGINERRORMSG,"Es ist kein externer Login mit diesem Account erlaubt.");  
				$this->app->Tpl->Parse(PAGE,"login.tpl");
      }
      else if(is_numeric($user_id))
      { 

        $this->app->DB->Insert("INSERT INTO useronline (user_id, sessionid, ip, login, time)
          VALUES ('".$user_id."','".$this->session_id."','".$_SERVER[REMOTE_ADDR]."','1',NOW())");

				$this->app->DB->Select("UPDATE user SET fehllogins=0
        	WHERE username='".$username."' LIMIT 1");

				$this->app->erp->calledOnceAfterLogin($type);

        $module=$this->app->Secure->GetGET("module");
        $action=$this->app->Secure->GetGET("action");

        $this->app->erp->Startseite();

	exit;
      }
      else if ($fehllogins>=6)
      {
				$this->app->Tpl->Set(LOGINERRORMSG,"Max. Anzahl an Fehllogins erreicht. Bitte wenden Sie sich an Ihren Administrator.");  
				$this->app->Tpl->Parse(PAGE,"login.tpl");
      }
      else
      { 

			if($this->app->Conf->WFdbType=="postgre")
       $this->app->DB->Select("UPDATE \"user\" SET fehllogins=fehllogins+1 WHERE username='".$username."'");
			else
       	$this->app->DB->Select("UPDATE user SET fehllogins=fehllogins+1 WHERE username='".$username."' LIMIT 1");

				$this->app->Tpl->Set(LOGINERRORMSG,"Benutzername oder Passwort falsch.");  
				$this->app->Tpl->Parse(PAGE,"login.tpl");
      }
    }
  }

  function Logout($msg="",$logout=false)
  {
		if($logout)
      	$this->app->Tpl->Parse(PAGE,"sessiontimeout.tpl");

	  $username = $this->app->User->GetName();
    $this->app->DB->Delete("DELETE FROM useronline WHERE user_id='".$this->app->User->GetID()."'");
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['database']="";


		if(!$logout)
		{
    	header("Location: ".$this->app->http."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
    	exit;
		}
    //$this->app->Tpl->Set(LOGINERRORMSG,$msg);  
    //$this->app->Tpl->Parse(PAGE,"login.tpl");
  }


  function CreateAclDB()
  {

  }


	function mOTP($pin,$otp,$initsecret)
	{


		$maxperiod = 3*60; // in seconds = +/- 3 minutes
		//$time=gmdate("U");
    date_default_timezone_set('UTC');    
		$time=time();

		$time = $time + (3600*2);

		for($i = $time - $maxperiod; $i <= $time + $maxperiod; $i++)
		{
			$md5 = substr(md5(substr($i,0,-1).trim($initsecret).trim($pin)),0,6);
			if($otp == $md5) { 
				return(true);
			}
		}
		return(false);
	}

}
?>

<?php
	$config['postinstall'] = true;

	/* ----------------- STEP 1 ----------------- */	
	$setup[1]['configfile'] = "user.inc.php";
	$setup[1]['description'] = 'Um dieses Setup auszuf&uuml;hren muss der Ordner <i>conf</i> Schreibrechte besitzen. Wenn diese passen klicken Sie auf ok (Diese Meldung dann ignorieren).';
	$setup[1]['action'] = "CheckDirRights";
	
	/* ----------------- STEP 2 ----------------- */
	
	$setup[2]['description'] = 'Datenbank-Einstellungen';
	$setup[2]['configfile'] = "user.inc.php";

	$setup[2]['fields']['WFdbhost']['text'] = "Host";
	$setup[2]['fields']['WFdbhost']['default'] = "localhost";
	$setup[2]['fields']['WFdbname'] = "Datenbank";
	$setup[2]['fields']['WFdbuser'] = "Benutzername";
	$setup[2]['fields']['WFdbpass'] = "Passwort";

	$setup[2]['action'] = "CheckDatabase";

	/* ----------------- STEP 3 ----------------- */
	
	$setup[3]['description'] = 'Schritt 2 - Sonstiges';
	$setup[3]['configfile'] = "user.inc.php";

	$setup[3]['fields']['WFuserdata']['text'] = "Userdata-Ordner";
	$setup[3]['fields']['WFuserdata']['default'] = str_replace("www\setup","userdata",str_replace("www/setup","userdata",getcwd()));


	$setup[3]['fields']['MainData']['invisible'] = "true";
	$setup[3]['fields']['MainData']['readonly'] = "true";
	$setup[3]['fields']['MainData']['sql'] = "../../database/main.sql";

	$setup[3]['fields']['InitialData']['invisible'] = "true";
	$setup[3]['fields']['InitialData']['readonly'] = "true";
	$setup[3]['fields']['InitialData']['sql'] = "../../database/initial.sql";

	/* ----------------- STEP 4 ----------------- */

	$setup[4]['description'] = 'Schritt 3 - Testdaten';
	$setup[4]['configfile'] = "user.inc.php";

	$setup[4]['fields']['MailTpl']['text'] = "E-Mail Templates einspielen (empfohlen)";
	$setup[4]['fields']['MailTpl']['type'] = "checkbox";
	$setup[4]['fields']['MailTpl']['value'] = "1";
	$setup[4]['fields']['MailTpl']['readonly'] = "true";
	$setup[4]['fields']['MailTpl']['sql'] = "../../database/emailtemplates.sql";

	$setup[4]['fields']['DhlZones']['text'] = "DHL-Zonen einspielen";
	$setup[4]['fields']['DhlZones']['type'] = "checkbox";
	$setup[4]['fields']['DhlZones']['value'] = "1";
	$setup[4]['fields']['DhlZones']['readonly'] = "true";
	$setup[4]['fields']['DhlZones']['sql'] = "../../database/dhlzones.sql";
	
	$setup[4]['fields']['Testdata']['text'] = "Mustershop-Datens&auml;tze einspielen";
	$setup[4]['fields']['Testdata']['type'] = "checkbox";
	$setup[4]['fields']['Testdata']['value'] = "1";
	$setup[4]['fields']['Testdata']['readonly'] = "true";
	$setup[4]['fields']['Testdata']['sql'] = "../../database/shopdata.sql";
	
	$setup[4]['fields']['ArticleData']['text'] = "Beispielartikel einspielen (nur mit Mustershop-Option)";
	$setup[4]['fields']['ArticleData']['type'] = "checkbox";
	$setup[4]['fields']['ArticleData']['value'] = "1";
	$setup[4]['fields']['ArticleData']['readonly'] = "true";
	$setup[4]['fields']['ArticleData']['sql'] = "../../database/testarticles.sql";

	/* ------------------------- Functions ------------------------- */

 	function CheckDirRights()
	{
		$rights = substr(sprintf('%o', fileperms('../../conf')), -3, 1);
		if($rights!='7')
			return "Der Ordner conf besitzt unzureichende Schreibrechte";
		return "";
	}

	function CheckDatabase()
	{
	  global $db;
		$db = mysqli_connect($_POST['WFdbhost'], $_POST['WFdbuser'], $_POST['WFdbpass'],$_POST['WFdbname']);

		if(!$db) return 'Verbindung zum Server nicht m&ouml;glich - m&ouml;glicherweise ist Host, Benutzername oder Passwort falsch geschrieben'; 
		//if(!mysqli_select_db($db,$_POST['WFdbname'], $db)) return 'Verbindung zur Datenbank nicht m&ouml;glich - m&ouml;glicherweise ist der Datenbankname falsch geschrieben';

		return '';
	}

	function CheckMail()
	{
		$smtp_conn = fsockopen($_POST['WFMailHost'], $_POST['WFMailPort'], $errno, $errstr, 30);
		
		if(empty($smtp_conn)) 
			return "Verbindung zum Server nicht m&ouml;glich<br>$errstr";


		return '';//'Konnte E-Mail nicht finden';
	}

	function CheckOther()
	{
		return '';
	}

	function PostInstall()
	{
		// Copy main.conf.php.tpl to main.conf.php.tpl
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
			copy('..\..\conf\main.conf.php.tpl','..\..\conf\main.conf.php');
		else
			copy("../../conf/main.conf.php.tpl","../../conf/main.conf.php");
	}
?>

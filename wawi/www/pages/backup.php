<?php

class Backup  {
  var $app;
  
  function Backup($app) 
	{
    $this->app=&$app;

    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
      $this->app->Tpl->Set(SUBHEADING,": ".$this->app->DB->Select("SELECT nummer FROM artikel WHERE id=$id LIMIT 1"));

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","BackupList");
    $this->app->ActionHandler("create","BackupCreate");
    $this->app->ActionHandler("download","BackupDownload");
    $this->app->ActionHandler("full","BackupFull");
    $this->app->ActionHandler("makefull","BackupMakeFull");
    $this->app->ActionHandler("delete","BackupDelete");
    $this->app->ActionHandler("recover","BackupRecover");
    $this->app->ActionHandler("reset","BackupReset");

		$this->host = $this->app->Conf->WFdbhost;
    $this->database = $this->app->Conf->WFdbname;
    $this->user = $this->app->Conf->WFdbuser;
    $this->password = $this->app->Conf->WFdbpass;
		$this->pfad = "../backup/snapshots/";

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }

	function BackupList()
  {
    $this->app->Tpl->Set(UEBERSCHRIFT,"Backup");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Backup");

    $this->app->erp->MenuEintrag("index.php?module=backup&action=create", "DB-Snapshot anlegen");
    $this->app->erp->MenuEintrag("index.php?module=backup&action=download", "DB-Backup herunterladen");
    $this->app->erp->MenuEintrag("index.php?module=backup&action=full", "System-Backup herunterladen");
    //$this->app->erp->MenuEintrag("index.php?module=backup&action=reset", "Datenbank zur&uuml;cksetzen");
    $this->app->erp->MenuEintrag("index.php?module=backup&action=list", "Zur&uuml;ck zur &Uuml;bersicht");


    $msg = base64_decode($this->app->Secure->GET["msg"]);
    $this->app->Tpl->Set(MESSAGE, $msg);

    $this->app->YUI->TableSearch(TAB1,"backuplist");

    $this->app->Tpl->Set(TABTEXT,"Backup");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");

  }

	function BackupCreate()
  {
		$this->app->Tpl->Set(UEBERSCHRIFT,"DB-Backup anlegen");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"DB-Backup anlegen");
		
		$this->app->erp->MenuEintrag("index.php?module=backup&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
 
		$name = $this->app->Secure->GetPOST("name");
		$submit = $this->app->Secure->GetPOST("submit");

		if($submit!="")
		{
			if($name!="")
			{
				$adresse = $this->app->User->GetAdresse();
				$name = preg_replace("/[^a-zA-Z0-9_]/" , "" , $name);
				$dateiname = date("Y-m-d_").$name.".sql";
				$pfad = $this->pfad.$dateiname;

				$exists = $this->app->DB->Select("SELECT '1' FROM backup WHERE dateiname='$dateiname' LIMIT 1");

				// puefe ob es pad gibt
				if(!is_dir($this->pfad)) mkdir($this->pfad,0777,true);
				
				if($exists=='1')
					$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Ein Backup mit diesem Namen existiert bereits.</div>");
				else if (!is_dir($this->pfad))
					$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Der Snapshot Ordner kann nicht anleget werden.</div>");
				else
				{
					$this->app->DB->Insert("INSERT INTO backup (adresse, name, dateiname, datum) VALUES ('$adresse','$name','$dateiname',NOW())");

					//Erstelle Backup
					system("mysqldump -h{$this->host} -u{$this->user} -p{$this->password} --database {$this->database} > $pfad");

					$msg = base64_encode("<div class=\"error2\">Das Datenbank-Backup wurde erfolgreich erstellt.</div>");
					header("Location: ./index.php?module=backup&action=list&msg=$msg");
					exit;
				}
			}else
				$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Sie m&uuml;ssen einen Namen f&uuml;r das Datenbank-Backup eingeben.</div>");				
		} 

		$this->app->Tpl->Parse(TAB1,"backup_create.tpl");
    $this->app->Tpl->Set(TABTEXT,"DB-Backup anlegen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
	}

	function BackupDownload()
	{
		$name = date("Y-m-d_")."DB-Backup.sql";
		$pfad = $this->pfad.$name;

		// Pfad anlegen falls er nicht existiert
		if(!is_dir($this->pfad)) {
			mkdir($this->pfad,0777,true);
		}

		//Erstelle Backup
    system("mysqldump -h{$this->host} -u{$this->user} -p{$this->password} --database {$this->database} > $pfad");

		// Daten als download raushauen
		header("Content-Disposition: attachment; filename=$name");
		readfile($pfad); //readfile will stream the file.
    system("rm $pfad");
		exit;

	}

	function BackupFull()
	{
    $this->app->erp->MenuEintrag("index.php?module=backup&action=list", "Zur&uuml;ck zur &Uuml;bersicht");

	  $this->app->Tpl->Parse(PAGE,"backup_full.tpl");
	}

	function BackupMakeFull()
	{
		$name = date("Ymd")."_WAWISION_SYSTEMBACKUP";
    $pfad = $this->pfad.$name;

    // Pfad anlegen falls er nicht existiert
    if(!is_dir($pfad)) mkdir($pfad,0777,true);
  
    //Erstelle Backup
    system("mysqldump -h{$this->host} -u{$this->user} -p{$this->password} --database {$this->database} > $pfad/".date("Y-m-d_")."Datenbank.sql");

    // Kopiere userdata
    system("cp -R ../userdata $pfad");

    // erzeuge tar
    system("tar cfvz {$this->pfad}$name.tar.gz $pfad/ $1>/dev/null");

    // Backup-Datei wieder loeschen
    system("rm -R $pfad");

/*
    header("Content-Description: File Transfer");
    header("Content-Type: application/otrkey");    
    header("Content-Length: " . filesize("$pfad.tar")); 
    header("Content-Disposition: attachment; filename=$name.tar;");
    header("Content-Transfer-Encoding: binary"); 
    readfile("$pfad.tar");
*/

		define('MP_BOUNDARY', '--'.sha1(microtime(true)));
		header('Content-Type: multipart/x-mixed-replace; boundary="'.MP_BOUNDARY.'"');
		flush();

		echo "Content-Type: application/otrkey\r\n";
    echo "Content-Length: ".filesize("$pfad.tar.gz")."\r\n"; 
		echo "Content-Disposition: attachment; filename=$name.tar.gz\r\n";
		echo "\r\n";
    readfile("$pfad.tar.gz");
		echo MP_BOUNDARY;
		flush();
    
		system("rm $pfad.tar.gz");

		echo "Content-Type: text/html\r\n";
		echo "\r\n";
		echo '<html><script type="text/javascript">parent.location.href="./index.php?module=backup&action=list";</script></html>';
		echo MP_BOUNDARY.'--';
		flush();
	}	

	function BackupDelete()
	{
		$id = $this->app->Secure->GetGET("id");

		$error = false;

		if(is_numeric($id))
		{	
			$dateiname = $this->app->DB->Select("SELECT dateiname FROM backup WHERE id='$id' LIMIT 1");

			if($dateiname!="")
			{
				$pfad = $this->pfad.$dateiname;
				system("rm $pfad");
				
				$this->app->DB->Delete("DELETE FROM backup WHERE id='$id' LIMIT 1");
				$msg = base64_encode("<div class=\"error2\">Das Backup wurde erfolgreich gel&ouml;scht.</div>");
			}else
				$error = true;
				
		}else
			$error = true;

		if($error)
			$msg = base64_encode("<div class=\"error\">Das Backup konnte nicht gel&ouml;scht werden.</div>");

		header("Location: ./index.php?module=backup&action=list&msg=$msg");
		exit;
	}

	function BackupRecover()
	{
		$id = $this->app->Secure->GetGET("id");
		
		if(is_numeric($id))
    {
      $dateiname = $this->app->DB->Select("SELECT dateiname FROM backup WHERE id='$id' LIMIT 1");
			$pfad = $this->pfad.$dateiname;			

			if(file_exists($pfad))
      {
				// Backup-Tabelle extra sichern
				system("mysqldump -h{$this->host} -u{$this->user} -p{$this->password} {$this->database} backup > {$this->pfad}backup_temp.sql");

				//Backup einspielen
				system("mysql -u{$this->user} -p{$this->password} -D{$this->database} < $pfad");

				// gesicherte Backup-Tabelle einspielen
				system("mysql -u{$this->user} -p{$this->password} -D{$this->database} < {$this->pfad}backup_temp.sql");
				
				// Backup-Tabelle loeschen
				system("rm {$this->pfad}backup_temp.sql");

				// Benutzer soll angemeldet bleiben
				$session = session_id();
				$adresse = $this->app->User->GetAdresse();
				$ip = $_SERVER['REMOTE_ADDR'];
				$this->app->DB->Update("UPDATE useronline SET login='0' WHERE user_id='$adresse'");
				$this->app->DB->Insert("INSERT INTO useronline (user_id, login, sessionid, ip, time)
																VALUES ('$adresse', '1', '$session', '$ip', NOW())");
	
			   system("php5 ./../upgradedbonly.php");
				//TODO:	Was ist wenn der Benutzer in der Zwischenzeit sein Passwort geaendert hat?	


				$msg = base64_encode("<div class=\"error2\">Das Backup wurde erfolgreich wiederhergestellt</div>");
			}else
				$msg = base64_encode("<div class=\"error\">'$dateiname' konnte nicht gefunden werden.</div>");
		}else
			$msg = base64_encode("<div class=\"error\">Backup-ID konnte nicht gefunden werden.</div>");

		header("Location: ./index.php?module=backup&action=list&msg=$msg");
    exit;
	}

	function BackupReset()
	{
		$this->app->Tpl->Set(UEBERSCHRIFT,"Datenbank zur&uuml;cksetzen");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Datenbank zur&uuml;cksetzen");
		
		$this->app->erp->MenuEintrag("index.php?module=backup&action=list", "Zur&uuml;ck zur &Uuml;bersicht");

		$submit = $this->app->Secure->GetPOST("submit");
		
		if($submit!="")
		{
			$adresse = $this->app->User->GetAdresse();
			$dateiname = date("Y-m-d_His_")."AutomaticResetBackup.sql";
			$pfad = $this->pfad.$dateiname;

			$this->app->DB->Insert("INSERT INTO backup (adresse, name, dateiname, datum) VALUES ('$adresse','Automatic Reset-Backup','$dateiname',NOW())");

      //Erstelle Backup
      system("mysqldump -h{$this->host} -u{$this->user} -p{$this->password} --database {$this->database} > $pfad");

			// Backup-Tabelle extra sichern
      system("mysqldump -h{$this->host} -u{$this->user} -p{$this->password} {$this->database} backup > {$this->pfad}backup_temp.sql");


			// Leere alle Tabellen
			$tables = $this->app->DB->SelectArr("SHOW TABLES");
			
			for($i=0;$i<count($tables);$i++)
				$this->app->DB->Select("TRUNCATE ".$tables[$i][key($tables[$i])]);
			
			// gesicherte Backup-Tabelle einspielen
      system("mysql -u{$this->user} -p{$this->password} -D{$this->database} < {$this->pfad}backup_temp.sql");

      // Backup-Tabelle loeschen
      system("rm {$this->pfad}backup_temp.sql");

			$sql = 'INSERT INTO `adresse` (`id`, `typ`, `marketingsperre`, `trackingsperre`, `rechnungsadresse`, `sprache`, `name`, `abteilung`, `unterabteilung`, `ansprechpartner`, `land`, `strasse`, `ort`, 
							`plz`, `telefon`, `telefax`, `mobil`, `email`, `ustid`, `ust_befreit`, `passwort_gesendet`, `sonstiges`, `adresszusatz`, `kundenfreigabe`, `steuer`, `logdatei`, `kundennummer`, 
							`lieferantennummer`, `mitarbeiternummer`, `konto`, `blz`, `bank`, `inhaber`, `swift`, `iban`, `waehrung`, `paypal`, `paypalinhaber`, `paypalwaehrung`, `projekt`, `partner`, `geloescht`, 
							`firma`) VALUES (NULL, \'\', \'\', \'\', \'\', \'\', \'Administrator\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW(), 
							\'\', \'\',	\'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'1\', \'\', \'\', \'1\');';	

			$this->app->DB->Insert($sql);

			$sql = 'INSERT INTO `firma` (`id`, `name`, `standardprojekt`) VALUES (NULL, \'Musterfirma\', \'1\');';

			$this->app->DB->Insert($sql);

			$sql = 'INSERT INTO `user` (`id`, `username`, `password`, `repassword`, `description`, `settings`, `parentuser`, `activ`, `type`, `adresse`, `standarddrucker`, `firma`, `logdatei`) 
							VALUES (NULL, \'admin\', ENCRYPT(\'admin\'), \'\', NULL, \'\', NULL, \'1\', \'admin\', \'1\', \'\', \'1\', NOW());';

			$this->app->DB->Insert($sql);

			$sql = 'INSERT INTO `projekt` (`id`, `name`, `abkuerzung`, `verantwortlicher`, `beschreibung`, `sonstiges`, `aktiv`, `farbe`, `autoversand`, `checkok`, `checkname`, `zahlungserinnerung`, 
							`zahlungsmailbedinungen`, `folgebestaetigung`, `kundenfreigabe_loeschen`, `autobestellung`, `firma`, `logdatei`) VALUES (NULL, \'Hauptprojekt\', \'HAUPTPROJEKT\', \'\', \'\', \'\', \'\', \'\'								 , \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'1\', \'\');';

			$this->app->DB->Insert($sql);

			$sql = "INSERT INTO `firmendaten` (`id`, `firma`, `absender`, `sichtbar`, `barcode`, `schriftgroesse`, `betreffszeile`, `dokumententext`, `tabellenbeschriftung`, `tabelleninhalt`, `zeilenuntertext`, 
							`freitext`, `infobox`, `spaltenbreite`, `footer_0_0`, `footer_0_1`, `footer_0_2`, `footer_0_3`, `footer_0_4`, `footer_0_5`, `footer_1_0`, `footer_1_1`, `footer_1_2`, `footer_1_3`, `footer_1_4`,							 `footer_1_5`, `footer_2_0`, `footer_2_1`, `footer_2_2`, `footer_2_3`, `footer_2_4`, `footer_2_5`, `footer_3_0`, `footer_3_1`, `footer_3_2`, `footer_3_3`, `footer_3_4`, `footer_3_5`, 
							`footersichtbar`, `hintergrund`, `logo`, `logo_type`, `briefpapier`, `briefpapier_type`, `benutzername`, `passwort`, `host`, `port`, `mailssl`, `signatur`, `email`, `absendername`,
							`bcc1`, `bcc2`, `firmenfarbe`, `name`, `strasse`, `plz`, `ort`, `steuernummer`, `datum`, `projekt`) VALUES
							(1, 1, 'Musterfirma GmbH | Musterweg 5 | 12345 Musterstadt', 1, 0, 7, 9, 9, 9, 9, 7, 9, 8, 0, 'Sitz der Gesellschaft / Lieferanschrift', 'Musterfirma GmbH', 'Musterweg 5', 
							'D-12345 Musterstadt', 'Telefon +49 123 12 34 56 7', 'Telefax +49 123 12 34 56 78', 'Bankverbindung', 'Musterbank', 'Konto 123456789', 'BLZ 72012345', '', '', 'IBAN DE1234567891234567891', 
							'BIC/SWIFT DETSGDBWEMN', 'Ust-IDNr. DE123456789', 'E-Mail: info@musterfirma-gmbh.de', 'Internet: http://www.musterfirma.de', '', 'Gesch&auml;ftsf&uuml;hrer', 'Max Musterman', 
							'Handelsregister: HRB 12345', 'Amtsgericht: Musterstadt', '', '', 0, 'kein', '', '', '', '', 'musterman', 'passwort', 'smtp.server.de', '25', 1, 
							'LS0NCk11c3RlcmZpcm1hIEdtYkgNCk11c3RlcndlZyA1DQpELTEyMzQ1IE11c3RlcnN0YWR0DQoNClRlbCArNDkgMTIzIDEyIDM0IDU2IDcNCkZheCArNDkgMTIzIDEyIDM0IDU2IDc4DQoNCk5hbWUgZGVyIEdlc2VsbHNjaGFmdDogTXVzdGVyZmlybWEgR21iSA0KU2l0eiBkZXIgR2VzZWxsc2NoYWZ0OiBNdXN0ZXJzdGFkdA0KDQpIYW5kZWxzcmVnaXN0ZXI6IE11c3RlcnN0YWR0LCBIUkIgMTIzNDUNCkdlc2Now6RmdHNmw7xocnVuZzogTWF4IE11c3Rlcm1hbg0KVVN0LUlkTnIuOiBERTEyMzQ1Njc4OQ0KDQpBR0I6IGh0dHA6Ly93d3cubXVzdGVyZmlybWEuZGUvDQo=', 'info@server.de', 'Meine Firma', '', '', '', 'Musterfirma GmbH', 'Musterweg 5', '12345', 'Musterstadt', '111/11111/11111', '0000-00-00 00:00:00', 1);";
			
			$this->app->DB->Insert($sql);




			$msg = base64_encode("<div class=\"error2\">Die Datenbank wurde erfolgreich zur&uuml;ckgesetzt.</div>");
			header("Location: ./index.php?module=backup&action=list&msg=$msg");
			exit;
		}


    $this->app->Tpl->Parse(TAB1,"backup_reset.tpl");
    $this->app->Tpl->Set(TABTEXT,"Datenbank zur&uuml;cksetzen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");

	}

  function BackupMenu()
  {
    $id = $this->app->Secure->GetGET("id");
		/*
    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Backup</a>&nbsp;");
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
		*/
	}
}

?>

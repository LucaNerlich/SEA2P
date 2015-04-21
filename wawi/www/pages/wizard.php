<?php
class Wizard  {
  var $app;
  
  function Wizard($app) 
	{
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","WizardList");
    $this->app->ActionHandler("adresse","WizardAdresse");
    $this->app->ActionHandler("create","WizardCreate");

    $this->app->ActionHandlerListen($app);
    
		$this->app = $app;
  }

	function WizardList()
  {
/*
    $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel-Assistent");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Artikel-Assistent");

		$this->BuildMenu($_COOKIE["wizard_typ"]);

    $msg = base64_decode($this->app->Secure->GET["msg"]);
    $this->app->Tpl->Set(MESSAGE, $msg);

    $this->app->Tpl->Set(TABTEXT,"Artikel-Assistent");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
*/
		header("Location: index.php?module=artikel&action=list");
		exit;

  }

	function GetAnredeSelect($selected="")
	{
		$anrede = array("firma" => "Firma",
										"person" => "Person",
										"student" => "Student",
										"hochschule" => "Hochschule",
										"ausbildungsbetrieb" => "Ausbildungsbetrieb");

		foreach($anrede AS $key=>$value)
		{
			$select = (($selected==$key) ? "selected" : "");
			$out .= "<option value=\"$key\" $select>$value</option>";
		}
		return $out;
	}

	function WizardAdresse()
	{
		$this->app->erp->MenuEintrag("index.php?module=wizard&action=create&step=preise", "Zur&uuml;ck zur Preis&uuml;bersicht");

		$typ = $this->app->Secure->GetGET("typ");
		$submit = $this->app->Secure->GetPOST("submit"); 
		$cancel = $this->app->Secure->GetPOST("cancel"); 

		$anrede = $this->app->Secure->GetPOST("anrede"); 
		$name = $this->app->Secure->GetPOST("name");
		$telefon = $this->app->Secure->GetPOST("telefon");
		$ansprechpartner = $this->app->Secure->GetPOST("ansprechpartner");
		$telefax = $this->app->Secure->GetPOST("telefax");
		$abteilung = $this->app->Secure->GetPOST("abteilung");
		$email = $this->app->Secure->GetPOST("email");
		$unterabteilung = $this->app->Secure->GetPOST("unterabteilung");
		$mobil = $this->app->Secure->GetPOST("mobil");
		$adresszusatz = $this->app->Secure->GetPOST("adresszusatz");
		$strasse = $this->app->Secure->GetPOST("strasse");
		$plz = $this->app->Secure->GetPOST("plz");
		$ort = $this->app->Secure->GetPOST("ort");
		$land = $this->app->Secure->GetPOST("land");

		if(!($typ=="kunde" || $typ=="lieferant"))
			$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Der Adress-Typ konnte nicht ermittelt werden.</div>");

		if($submit!="" && ($typ=="kunde" || $typ=="lieferant"))
		{
			if($name!="")
			{
				// Adresse anlegen
				$this->app->DB->Insert("INSERT INTO adresse(typ,name,abteilung,unterabteilung,ansprechpartner,land,strasse,ort,plz,telefon,telefax,mobil,email,adresszusatz)
																VALUES('$anrede','$name','$abteilung','$unterabteilung','$ansprechpartner','$land','$strasse','$ort','$plz','$telefon','$telefax','$mobil','$email','$adresszusatz')");


				// Rolle anlegen
				$adresse = $this->app->DB->GetInsertID();

				$this->app->erp->EventAPIAdd("EventAdresseCreate",$adresse,"adresse","create",$kommentar="Wizard");

				$subjekt = (($typ=='kunde') ? "Kunde" : "Lieferant");
				$this->app->erp->AddRolleZuAdresse($adresse, $subjekt, '', 'Projekt', '1');
				
				if($anrede=="firma")
					$this->app->DB->Update("UPDATE adresse SET firma='".$this->app->User->GetFirma()."' WHERE id='$adresse' LIMIT 1");

				header("Location: ./index.php?module=wizard&action=create&step=preise");
      	exit;
			}else
				$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Geben Sie bitte einen Namen ein.</div>");

		}

		if($cancel!="")
		{
			header("Location: ./index.php?module=wizard&action=create&step=preise");
			exit;
		}

		$this->app->Tpl->Set("NAME", $name);
		$this->app->Tpl->Set("TELEFON", $telefon);
		$this->app->Tpl->Set("ANSPRECHPARTNER", $ansprechpartner);
		$this->app->Tpl->Set("TELEFAX", $telefax);
		$this->app->Tpl->Set("ABTEILUNG", $abteilung);
		$this->app->Tpl->Set("EMAIL", $email);
		$this->app->Tpl->Set("UNTERABTEILUNG", $unterabteilung);
		$this->app->Tpl->Set("MOBIL", $mobil);
		$this->app->Tpl->Set("ADRESSZUSATZ", $adresszusatz);
		$this->app->Tpl->Set("STRASSE", $strasse);
		$this->app->Tpl->Set("PLZ", $plz);
		$this->app->Tpl->Set("ORT", $ort);

		$this->app->Tpl->Set(ANREDE, $this->GetAnredeSelect($anrede));
		$this->app->Tpl->Set(LAND, $this->app->erp->SelectLaenderliste($land));
		$this->app->Tpl->Set(TABTEXT,(($typ=="kunde") ? "Neuen Kunden anlegen" : "Neuen Lieferanten anlegen"));
    $this->app->Tpl->Parse(TAB1,"wizard_adresse.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
	}

	function WizardCreate()
  {
		$this->app->Tpl->Set(UEBERSCHRIFT,"Artikel anlegen");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Artikel anlegen");
		
		$this->BuildMenu($_COOKIE["wizard_typ"]);

		$submit = $this->app->Secure->GetPOST("submit");		
		$back = $this->app->Secure->GetPOST("back");		
		$step = $this->app->Secure->GetGET("step");

		// new ist Anfangsstatus
		if($_COOKIE["wizard_typ"]=="" && $step=="")
		{
			setcookie("wizard_typ", "new");
			header("Location: ./index.php?module=wizard&action=create&step=1");
			exit;
		}

		// =============================================== Erlaubte Aktionen ============================================
		$valid = $this->GetActions(); 
		if(!(is_array($valid[$_COOKIE["wizard_typ"]]) && in_array($step, $valid[$_COOKIE["wizard_typ"]])))
		{
			$this->app->Tpl->Set(TABTEXT,"Aktion nicht m&ouml;glich");
			$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Diese Aktion  ist f&uuml;r diesen Warentyp nicht m&ouml;glich</div>");
		}else
		{
			// ==============================================================================================================
			switch($step)
			{
				// --------------------------------------------- Weiterleitung ----------------------------------------------
				case '':
					header("Location: ./index.php?module=wizard&action=create&step=1");
					exit;
				break;
				// ----------------------------------------------- STEP 1 ---------------------------------------------------
				case 1:
					$ware = $this->app->Secure->GetPOST("ware");
					$produktion = $this->app->Secure->GetPOST("produktion");
					$dienst = $this->app->Secure->GetPOST("dienst");
					$miete = $this->app->Secure->GetPOST("miete");
					$porto = $this->app->Secure->GetPOST("porto");
					$sonstiges = $this->app->Secure->GetPOST("sonstiges");

					$name = $this->app->Secure->GetPOST("name");				
					$nummer = $this->app->Secure->GetPOST("nummer");				
					$projekt = $this->app->Secure->GetPOST("projekt");				
					$standardlieferant = $this->app->Secure->GetPOST("standardlieferant");				

					if($ware!="" || $produktion!="" || $dienst!="" || $miete!="" || $porto!="" || $sonstiges!="")
					{
						setcookie('wizard_name', $name);
						setcookie('wizard_nummer', $nummer);
						setcookie('wizard_projekt', $projekt);
						setcookie('wizard_standardlieferant', $standardlieferant);

						if($name=="")	
							$error = "Sie m&uuml;ssen einen Artikelnamen eingeben.<br>";

						if(is_numeric($nummer) && $this->app->DB->Select("SELECT '1' FROM `artikel` WHERE nummer='$nummer' LIMIT 1")=='1') 
							$error .= "Die eingegebene Artikel-Nummer wird bereits verwendet.<br>";
	
						if($error=="")
						{
							if($ware!="") setcookie('wizard_typ', "produkt");		
							if($produktion!="") setcookie('wizard_typ', "produktion");		
							if($dienst!="") setcookie('wizard_typ', "fremdleistung");		
							if($miete!="") setcookie('wizard_typ', "gebuehr");		
							if($porto!="")
							{
								setcookie('wizard_typ', "produkt");		
								setcookie('wizard_porto', "1");
							}else
								setcookie('wizard_porto', "0");

							if($sonstiges!="") setcookie('wizard_typ', "material");
					
							// Temporaeren Artikel anlegen
							if(!is_numeric($_COOKIE["wizard_artikel"]))
							{
								$this->app->DB->Insert("INSERT INTO artikel(typ) VALUES ('wizard_temp')");
								$artikel = $this->app->DB->GetInsertID();

								setcookie("wizard_artikel", $artikel);
							}

							// Weiterleitung zur naechsten Action
							$this->PageForward($_COOKIE["wizard_typ"], "1");
						}else
							$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">$error</div>");	
					}

					$this->app->YUI->AutoComplete("lieferant", "lieferant");
					$this->app->YUI->AutoComplete("projekt", "projektname",1);

					$this->app->Tpl->Set(NAME, $_COOKIE['wizard_name']);
					$this->app->Tpl->Set(NUMMER, $_COOKIE['wizard_nummer']);
					$this->app->Tpl->Set(PROJEKT, $_COOKIE['wizard_projekt']);
					$this->app->Tpl->Set(STANDARDLIEFERANT, $_COOKIE['wizard_standardlieferant']);

					$this->app->Tpl->SET(BACKBUTTON,"disabled");
					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('1'));
					$this->app->Tpl->Parse(TAB1,"wizard_step1.tpl");
				break;
				// ------------------------------------------------ Text ----------------------------------------------------
				case 'text':
					$kurztext = $this->app->Secure->GetPOST("kurztext");
					$uebersichtstext = $this->app->Secure->POST["uebersichtstext"];


					$submit = $this->app->Secure->GetPOST("submit");
					$back = $this->app->Secure->GetPOST("back");

					if($submit!="")
					{
						setcookie("wizard_kurztext", base64_encode($kurztext));
						setcookie("wizard_uebersichtstext", base64_encode($uebersichtstext));

						// Weiterleitung zur naechsten Action
            $this->PageForward($_COOKIE["wizard_typ"], "text");
					}

					if($back!="")
					{
						// Weiterleitung zur letzten Action
            $this->PageBack($_COOKIE["wizard_typ"], "text");
					}

					$this->app->Tpl->Set(KURZTEXT, base64_decode($_COOKIE["wizard_kurztext"]));
					$this->app->Tpl->Set(UEBERSICHTSTEXT, base64_decode($_COOKIE["wizard_uebersichtstext"]));

					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('text'));
          $this->app->Tpl->Parse(CONTENT,"wizard_text.tpl");
				break;
				// ----------------------------------------------- STEP 2 ---------------------------------------------------
				case 2:
					$stueckliste = ($this->app->Secure->GetPOST("stuecklisteOn")==1) ? 1 : 0;
					$jitliste = ($this->app->Secure->GetPOST("JitlisteOn")==1) ? 1 : 0;
					$lagerartikel = ($this->app->Secure->GetPOST("LagerartikelOn")==1) ? 1 : 0;
					$charge = ($this->app->Secure->GetPOST("charge")==1) ? 1 : 0;
					$endmontage = ($this->app->Secure->GetPOST("endmontage")==1) ? 1 : 0;
					if($this->app->Secure->GetPOST("SnOpt0")!="") $seriennummer = "vomprodukt";
					if($this->app->Secure->GetPOST("SnOpt1")!="") $seriennummer = "eigene";
					if($this->app->Secure->GetPOST("SnOpt2")!="") $seriennummer = "keine";
					$ust = ($this->app->Secure->GetPOST("ust")==1) ? 1 : 0;

					$submit = $this->app->Secure->GetPOST("submit");
					$back = $this->app->Secure->GetPOST("back");
	
					if($back!="")
					{
						// Weiterleitung zur letzten Action
						$this->PageBack($_COOKIE["wizard_typ"], "2");
					}

					if($submit!="")
					{
						$_SESSION['wizard_stueckliste'] = $stueckliste;
						setcookie("wizard_stueckliste", $stueckliste);
						setcookie("wizard_jitliste", $jitliste);
						setcookie("wizard_lagerartikel", $lagerartikel);
						setcookie("wizard_charge", $charge);
						setcookie("wizard_endmontage", $endmontage);
						setcookie("wizard_seriennummer", $seriennummer);
						setcookie("wizard_ust", $ust);

            // Weiterleitung zur naechsten Action
						$this->PageForward($_COOKIE["wizard_typ"], "2");
					}

					if($_COOKIE['wizard_stueckliste']==1) $this->app->Tpl->Set("STUECKLISTEONCHECKED", "checked"); else $this->app->Tpl->Set("STUECKLISTEOFCHECKED", "checked");
					if($_COOKIE['wizard_jitliste']==1) $this->app->Tpl->Set("JITLISTEONCHECKED", "checked"); else $this->app->Tpl->Set("JITLISTEOFCHECKED", "checked");
					if($_COOKIE['wizard_lagerartikel']==1) $this->app->Tpl->Set("LAGERARTIKELONCHECKED", "checked"); else $this->app->Tpl->Set("LAGERARTIKELOFCHECKED", "checked");

					($_COOKIE['wizard_charge']==1) ? $this->app->Tpl->Set("CHARGE", "checked") : $this->app->Tpl->Set("CHARGEDISABLED", "disabled"); 
					($_COOKIE['wizard_endmontage']==1) ? $this->app->Tpl->Set("ENDMONTAGE", "checked") : $this->app->Tpl->Set("ENDMONTAGEDISABLED", "disabled"); 

					if($_COOKIE['wizard_seriennummer']=="vomprodukt") $this->app->Tpl->Set("SNOPT0CHECKED", "checked");
					elseif($_COOKIE['wizard_seriennummer']=="eigene") $this->app->Tpl->Set("SNOPT1CHECKED", "checked");
					else $this->app->Tpl->Set("SNOPT2CHECKED", "checked");
					
					if($_COOKIE['wizard_ust']==1) $this->app->Tpl->Set("USTCHECKED", "checked");
					
					$this->app->Tpl->Set(THEME,$this->app->Conf->WFconf[defaulttheme]);
					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('2'));
					$this->app->Tpl->Parse(CONTENT,"wizard_step2.tpl");	
				break;
				// --------------------------------------------- EK/VK-Preise -----------------------------------------------
				case 'preise':
					$ek = $this->app->Secure->GetPOST("ek");
					$vk = $this->app->Secure->GetPOST("vk");
				
					$std = $this->app->Secure->GetGET("std");
					$ekdelete = $this->app->Secure->GetGET("ekdelete");
					$vkdelete = $this->app->Secure->GetGET("vkdelete");
				
					$speichern = $this->app->Secure->GetPOST("preisesubmit");

          $submit = $this->app->Secure->GetPOST("submit");
          $back = $this->app->Secure->GetPOST("back");

					if($speichern!="")
					{
						$this->saveEkVk($ek, $vk);
    				header("Location: ./index.php?module=wizard&action=create&step=preise");
						exit;
					}

					if($submit!="")
					{
						$this->saveEkVk($ek, $vk);
   
		        // Weiterleitung zur naechsten Action
            $this->PageForward($_COOKIE["wizard_typ"], "preise");
					}

					if($back!="")
					{
						$this->saveEkVk($ek, $vk);
						
						// Weiterleitung zur letzten Action
            $this->PageBack($_COOKIE["wizard_typ"], "preise");
					}

					// ------------------------------------ EK ------------------------------------	
					// Extrahiere Standardlieferanten aus Step 1
					$stdLieferId = $this->getNumber($_COOKIE["wizard_standardlieferant"]);

					// EK-Standard Lieferanten aendern
					if(is_numeric($std))
					{
						$lieferId = $this->getNumber($std);
						if($lieferId!=$stdLieferId)
							setcookie('wizard_andererstandardlieferant', '1');
						else
							setcookie('wizard_andererstandardlieferant', '0');


						$tmpdata = unserialize(base64_decode($_COOKIE["wizard_ek"]));
						if(is_array($tmpdata[$std]))
						{
							for($i=0;$i<count($tmpdata);$i++) $tmpdata[$i][standardlieferant] = 0;
							$tmpdata[$std][standardlieferant] = 1;
							setcookie("wizard_ek", base64_encode(serialize($tmpdata)));
							header("Location: ./index.php?module=wizard&action=create&step=preise");
							exit;
						}
					}

					// EK-Preis loeschen
					if(is_numeric($ekdelete))
					{
						$tmpdata = unserialize(base64_decode($_COOKIE["wizard_ek"]));
						if(is_array($tmpdata[$ekdelete]))
		        { 
			        unset($tmpdata[$ekdelete]);
							$tmpdata = array_values($tmpdata);
							setcookie("wizard_ek", base64_encode(serialize($tmpdata)));
					    header("Location: ./index.php?module=wizard&action=create&step=preise");
						  exit;
						}
					}

					// Daten aus Cookie wiederherstellen
					$ekdata = unserialize(base64_decode($_COOKIE["wizard_ek"]));
			
					for($i=0; $i<count($ekdata), $i<5; $i++)
					{
						if(is_array($ekdata[$i]) && $ekdata[$i][standardlieferant]==1)
							$standardlieferant = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ack.png\" >";
						else
							$standardlieferant = "";

						$ekpreise .= "<tr align=\"center\">
													<td><input type=\"hidden\" name=\"ek[$i][standardlieferant]\" value=\"".((is_array($ekdata[$i])) ? $ekdata[$i][standardlieferant] : "")."\">".($i+1)."</td>
													<td>[ADRESSE{$i}START]<input type=\"text\" name=\"ek[$i][lieferant]\"  id=\"adresse$i\" value=\"".((is_array($ekdata[$i])) ? $ekdata[$i][lieferant] : "")."\">[ADRESSE{$i}ENDE]</td>
													<td><input type=\"text\" name=\"ek[$i][lieferbez]\" value=\"".((is_array($ekdata[$i])) ? $ekdata[$i][lieferbez] : "")."\"></td>
													<td><input type=\"text\" name=\"ek[$i][liefernummer]\" value=\"".((is_array($ekdata[$i])) ? $ekdata[$i][liefernummer] : "")."\"></td>
													<td><input type=\"text\" name=\"ek[$i][menge]\" value=\"".((is_array($ekdata[$i])) ? $ekdata[$i][menge] : "")."\" size=\"4\" id=\"ekmenge$i\"></td>
													<td><input type=\"text\" name=\"ek[$i][preis]\" value=\"".((is_array($ekdata[$i])) ? $ekdata[$i][preis] : "")."\" size=\"6\" 
																onfocus=\"setActive('ek','$i')\" onblur=\"fill()\" id=\"ekpreis$i\"></td>
													<td>$standardlieferant</td>
													<td>".((is_array($ekdata[$i]) & $ekdata[$i][lieferant]!="") ? "<a href=\"./index.php?module=wizard&action=create&step=preise&std=$i\">
															<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/right.png\"></a>
															<a href=\"./index.php?module=wizard&action=create&step=preise&ekdelete=$i\">
															<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.png\"></a>" : " - ")."</td>
													</tr>";
						$this->app->YUI->AutoComplete("adresse$i","lieferant");
					}

					$this->app->Tpl->Set(EKPREISE, $ekpreise);
	
					// ------------------------------------ VK ------------------------------------	
					// VK-Preis loeschen
		      if(is_numeric($vkdelete))
			    { 
				    $tmpdata = unserialize(base64_decode($_COOKIE["wizard_vk"]));
	          if(is_array($tmpdata[$vkdelete]))
		        {
			        unset($tmpdata[$vkdelete]);
							$tmpdata = array_values($tmpdata);
				      setcookie("wizard_vk", base64_encode(serialize($tmpdata)));
					    header("Location: ./index.php?module=wizard&action=create&step=preise");
						  exit;
						}
					}

					// Daten aus Cookie wiederherstellen
		      $vkdata = unserialize(base64_decode($_COOKIE["wizard_vk"]));

					for($i=0; $i<count($vkdata), $i<5; $i++)
					{
						$vkpreise .= "<tr align=\"center\">
													<td>".($i+1)."</td>
													<td>[KUNDE{$i}START]<input type=\"text\" name=\"vk[$i][kunde]\" id=\"kunde$i\" value=\"".((is_array($vkdata[$i])) ? $vkdata[$i][kunde] : "")."\" size=\"40\">[KUNDE{$i}ENDE]</td>
													<td><input type=\"text\" name=\"vk[$i][menge]\" value=\"".((is_array($vkdata[$i])) ? $vkdata[$i][menge] : "")."\" size=\"4\" id=\"vkmenge$i\"></td>
													<td><input type=\"text\" name=\"vk[$i][preis]\" value=\"".((is_array($vkdata[$i])) ? $vkdata[$i][preis] : "")."\" size=\"6\" 
																onfocus=\"setActive('vk','$i')\" onblur=\"fill()\" id=\"vkpreis$i\"></td>
													<td>".((is_array($vkdata[$i]) && $vkdata[$i][menge]!="" && $vkdata[$i][preis]!="") ? "<a href=\"./index.php?module=wizard&action=create&step=preise&vkdelete=$i\">
							                <img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.png\"></a>" : " - ")."</td>
												 </tr>";
						$this->app->YUI->AutoComplete("kunde$i","kunde");
					}
					$this->app->Tpl->Set(VKPREISE, $vkpreise);

					if($_COOKIE['wizard_typ']=='fremdleistung' || $_COOKIE['wizard_porto']=='1' ||$_COOKIE['wizard_stueckliste']=='1')
						$this->app->Tpl->Set(EKBOX, 'none');

					// Extra-Speichern-Button
					$this->app->Tpl->Set(EXTRA, "&nbsp;<input type=\"submit\" name=\"preisesubmit\" value=\"Speichern\">");

					$this->app->Tpl->Set(ARTIKELNAME,$_COOKIE['wizard_name']);
					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('preise'));
					$this->app->Tpl->Parse(CONTENT,"wizard_preise.tpl");	
				break;
				// --------------------------------------------- Stueckliste ----------------------------------------------
				case 'stueckliste':
					$liste = $this->app->Secure->GetPOST("liste");
					$rows = $this->app->Secure->GetGET("rows");
					$delete = $this->app->Secure->GetGET("delete");
					$speichern = $this->app->Secure->GetPOST("stuecklistesubmit");
					$addRows = $this->app->Secure->GetPOST("addRows");
					$submit = $this->app->Secure->GetPOST("submit");
          $back = $this->app->Secure->GetPOST("back");

					$data = unserialize(base64_decode($_COOKIE["wizard_stuecklisteElemente"]));
					
					if($rows=="")
						$rows = ((count($data) >= 10) ? count($data) : 10);  

					if(is_numeric($delete))
					{
						if(is_array($data[$delete]))
							unset($data[$delete]);
						$this->saveStueckliste($data);
            header("Location: ./index.php?module=wizard&action=create&step=stueckliste");
            exit;
					}

					if($speichern!="")
					{
						$this->saveStueckliste($liste);
						header("Location: ./index.php?module=wizard&action=create&step=stueckliste");
						exit;
					}					
	
					if($addRows!="")
					{
						$this->saveStueckliste($liste);
						$rows += 5;
            header("Location: ./index.php?module=wizard&action=create&step=stueckliste&rows=$rows");
            exit;
					}

					if($submit!="")
          {
						$this->saveStueckliste($liste);
						// Weiterleitung zur naechsten Action
            $this->PageForward($_COOKIE["wizard_typ"], "stueckliste");
          }

					if($back!="")
          {
						// Weiterleitung zur letzten Action
            $this->PageBack($_COOKIE["wizard_typ"], "stueckliste");
          }

					for($i=0; $i<$rows; $i++)
					{
						$artikel = ((isset($data[$i][artikel])==true) ? $data[$i][artikel] : "");  
						$menge = ((isset($data[$i][menge])==true) ? $data[$i][menge] : "");  

						$out .= "<tr>
											<td>[ARTIKEL{$i}START]<input type=\"text\" name=\"liste[$i][artikel]\" value=\"$artikel\" onblur=\"fill($i,document.wizardform.elements['menge$i'])\" id=\"artikel$i\" size=\"130\">[ARTIKEL{$i}ENDE]</td>
											<td><input type=\"text\" name=\"liste[$i][menge]\" value=\"$menge\" size=\"5\" id=\"menge$i\"></td>
											<td>".((is_array($data[$i])==true)? "<a href=\"./index.php?module=wizard&action=create&step=stueckliste&delete=$i\">
                            <img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.png\"></a>" : "")."</td></tr>";

						$this->app->YUI->AutoComplete("artikel$i","artikelnummer");
					}
					$this->app->Tpl->Set(STUECKLISTE, $out);

					$this->app->Tpl->Set(EXTRA, "&nbsp;<input type=\"submit\" name=\"stuecklistesubmit\" value=\"Speichern\">");
					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('stueckliste'));
          $this->app->Tpl->Parse(CONTENT,"wizard_stueckliste.tpl");
				break;
				// --------------------------------------------- Kategorien -----------------------------------------------
				case 'onlineshop': 			
					$shop = $this->app->Secure->GetPOST("shop");	setcookie("wizard_shop", $shop);
					$neu = ($this->app->Secure->GetPOST("neu")==1) ? 1 : 0;		setcookie("wizard_neu", $neu);
					$topseller = ($this->app->Secure->GetPOST("topseller")==1) ? 1 : 0;	setcookie("wizard_topseller", $topseller);
					$startseite = ($this->app->Secure->GetPOST("startseite")==1) ? 1 : 0;	setcookie("wizard_startseite", $startseite);	
					$wichtig = ($this->app->Secure->GetPOST("wichtig")==1) ? 1 : 0;	setcookie("wizard_wichtig", $wichtig);
					$partnersperre = ($this->app->Secure->GetPOST("partnersperre")==1) ? 1 : 0;	setcookie("wizard_partnersperre", $partnersperre);
					$kategorien = $this->app->Secure->GetPOST("cat");	

					$msg = base64_decode($this->app->Secure->GetGET("msg"));

					$kategorien_neu_DE = $this->app->Secure->GetPOST("newcatDE");
					$kategorien_neu_EN = $this->app->Secure->GetPOST("newcatEN");
			
					$kategorien_submit = $this->app->Secure->GetPOST("cat_submit");
					$submit = $this->app->Secure->GetPOST("submit");
  				$back = $this->app->Secure->GetPOST("back");
				
					// kategorien umformatieren
					foreach($kategorien as $key => $value)
						$fKategorien[] = $key;
					setcookie("wizard_kategorien", base64_encode(serialize($fKategorien)));

					if($submit!="")
					{
						// Kategorien zwischenspeichern
						for($i=0;$i<4;$i++)
            {
							if($kategorien_neu_DE[$i]!="" || $kategorien_neu_EN[$i]!="")
								$tmpKat[] = array('DE' => "{$kategorien_neu_DE[$i]}", 'EN' => "{$kategorien_neu_EN[$i]}");
						}
						setcookie("wizard_tmpKategorien", base64_encode(serialize($tmpKat)));
	
						// Weiterleitung zur naechsten Action
            $this->PageForward($_COOKIE["wizard_typ"], "onlineshop");
					}

          if($back!="")
          {
						// Kategorien zwischenspeichern
            for($i=0;$i<4;$i++)
            {
              if($kategorien_neu_DE[$i]!="" || $kategorien_neu_EN[$i]!="")
                $tmpKat[] = array('DE' => "{$kategorien_neu_DE[$i]}", 'EN' => "{$kategorien_neu_EN[$i]}");
            }
            setcookie("wizard_tmpKategorien", base64_encode(serialize($tmpKat)));

						// Weiterleitung zur letzten Action
            $this->PageBack($_COOKIE["wizard_typ"], "onlineshop");
					}
	
					// Neue Kategorien erstellen
					if($kategorien_submit!="" && is_numeric($shop))
					{
						// Temporaere Kategorien koennen geloescht werden
						setcookie("wizard_tmpKategorien", "", time()-86400);
						
						for($i=0;$i<4;$i++)
						{
							if($kategorien_neu_DE[$i]!="" || $kategorien_neu_EN[$i]!="")
							{
								$this->app->DB->Insert("INSERT INTO artikelgruppen (bezeichnung, bezeichnung_en, shop, aktiv)
								    										VALUES ('$kategorien_neu_DE[$i]', '$kategorien_neu_EN[$i]', '$shop', '1')");
								$markKatId[] = $this->app->DB->GetInsertID();
							}
						}
						// Speichere die zu markierenden Kategorien
						setcookie("wizard_markKategorien", base64_encode(serialize($markKatId)));

						$msg = base64_encode("<div class=\"error2\">Die neuen Kategorien wurden abgespeichert. Sie k&ouml;nnen nun unter dem entsprechendem Shop ausgew&auml;hlt werden.</div>");

						header("Location: ./index.php?module=wizard&action=create&step=onlineshop&msg=$msg");
						exit;
					}

					// Radio-Felder fuer Onlineshops
					$shops = $this->app->DB->SelectArr("SELECT id, bezeichnung FROM shopexport");
					for($i=0;$i<count($shops);$i++)
					{
						$selected = ($_COOKIE["wizard_shop"]==$shops[$i][id]) ? "checked"  : "";
						$shopout .= "<input type=\"radio\" name=\"shop\" value=\"{$shops[$i][id]}\" onclick=\"showShopCategories({$shops[$i][id]})\" $selected>{$shops[$i][bezeichnung]}<br>";
					}
					$this->app->Tpl->Set(SHOPS, $shopout);	

					// Checkboxen fuer Kategorien
					$kat = unserialize(base64_decode($_COOKIE["wizard_kategorien"]));	
					$markKatId = unserialize(base64_decode($_COOKIE["wizard_markKategorien"]));
					for($i=0;$i<count($shops);$i++)
					{
						$category= $this->app->DB->SelectArr("SELECT id, bezeichnung, bezeichnung_en, shop FROM artikelgruppen WHERE shop='{$shops[$i][id]}' AND aktiv='1' ORDER BY bezeichnung");
						$javascript .= "if(id=={$shops[$i][id]}){ content='"; 
		
						$encoded = "";
						for($k=0;$k<count($category);$k++)
						{
							$markierung = ((in_array($category[$k][id], $markKatId)==true) ? "<font color=\"green\">(neu)</font>" : "");			
							$selected = (((in_array($category[$k][id], $kat)==true)||(in_array($category[$k][id], $markKatId)==true)) ? "checked" : "");

							$box = "<input type=\"checkbox\" name=\"cat[{$category[$k][id]}]\" value=\"1\" $selected>"
											.(($category[$k][bezeichnung]!="") ? $category[$k][bezeichnung] : $category[$k][bezeichnung_en]);
							if($k%2==0)
								$encoded .= " <tr><td width=\"50%\">$box &nbsp;$markierung</td> ";
							else
								$encoded .= "<td width=\"50%\">$box &nbsp;$markierung</td></tr> ";

							if(count($category)%2==1 && $k==count($category)-1)
								$encoded .= "<td></td></tr>";
						}
						if(count($category)==0)
							$encoded .= ' <b>Keine Kategorien vorhanden.</b>';
					
						$javascript .= base64_encode($encoded)."';}";
					}
					$this->app->Tpl->Set(JAVASCRIPT, $javascript);

					
					if($_COOKIE['wizard_shop']=="") 
						$this->app->Tpl->Set("SHOPCHECKED", "checked");
					else 
						$this->app->Tpl->Set(VORAUSWAHL, $_COOKIE['wizard_shop']);
					if($_COOKIE['wizard_neu']==1) $this->app->Tpl->Set("NEUCHECKED", "checked");
					if($_COOKIE['wizard_topseller']==1) $this->app->Tpl->Set("TOPSELLERCHECKED", "checked");
					if($_COOKIE['wizard_startseite']==1) $this->app->Tpl->Set("STARTSEITECHECKED", "checked");
					if($_COOKIE['wizard_wichtig']==1) $this->app->Tpl->Set("WICHTIGCHECKED", "checked");
					if($_COOKIE['wizard_partnersperre']==1) $this->app->Tpl->Set("PARTNERSPERRECHECKED", "checked");
	
					// Nicht abgepeicherte Kategorien wiederherstellen
					$tmpKat = unserialize(base64_decode($_COOKIE['wizard_tmpKategorien']));
					for($i=0;$i<count($tmpKat),$i<4;$i++)
					{
						if(isset($tmpKat[$i]) && is_array($tmpKat[$i]))
						{
							$this->app->Tpl->Set("NEWCAT{$i}DE", $tmpKat[$i][DE]);
							$this->app->Tpl->Set("NEWCAT{$i}EN", $tmpKat[$i][EN]);
						}
					}
					
					$this->app->Tpl->Set(MESSAGE,$msg);
					$this->app->Tpl->Set(THEME,$this->app->Conf->WFconf[defaulttheme]);
					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('onlineshop'));
					$this->app->Tpl->Parse(CONTENT,"wizard_onlineshop.tpl");	
				break;
				// ----------------------------------------------- Bilder --------------------------------------------------
				case 'bilder';
					$titel = $this->app->Secure->GetPOST("titel");
					$beschreibung = $this->app->Secure->GetPOST("beschreibung");
					$bildtyp = $this->app->Secure->GetPOST("bildtyp");
					$stdbild = $this->app->Secure->GetPOST("stdbild");
					$bildersubmit = $this->app->Secure->GetPOST("bildersubmit");
					$standard = $this->app->Secure->GetGET("standard");
					$delete= $this->app->Secure->GetGET("delete");

					$submit = $this->app->Secure->GetPOST("submit");
          $back = $this->app->Secure->GetPOST("back");

	        if($submit!="")
          {
            // Weiterleitung zur naechsten Action
            $this->PageForward($_COOKIE["wizard_typ"], "bilder");
          }

          if($back!="")
          {
						// Weiterleitung zur letzten Action
            $this->PageBack($_COOKIE["wizard_typ"], "bilder");
          }

					// Bild als Standard setzen
					if(is_numeric($standard))
					{
						$this->app->DB->Update("UPDATE artikel SET standardbild='$standard' WHERE id='{$_COOKIE['wizard_artikel']}' LIMIT 1");
						header("Location: ./index.php?module=wizard&action=create&step=bilder");
					}

					// Bild loeschen
					if(is_numeric($delete))
					{
						$this->app->DB->Update("UPDATE datei SET geloescht='1' WHERE id='$delete' LIMIT 1");
						header("Location: ./index.php?module=wizard&action=create&step=bilder");
					}

					// Datei hochladen
					if($bildersubmit!="")
					{
						if($_FILES['upload']['tmp_name']=="")
							$error = "Keine Datei ausgew&auml;hlt.<br>";

						if(!is_numeric($_COOKIE['wizard_artikel']))
							$error .= "Es konnte keine Artikel-ID gefunden werden. Wiederholen Sie bitte Schritt 1.";

						if($error=="")
						{
							$fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'],$titel,$beschreibung,"",$_FILES['upload']['tmp_name'],$this->app->User->GetName());	
							$this->app->erp->AddDateiStichwort($fileid,$bildtyp,"Artikel",$_COOKIE['wizard_artikel']);
	
							// Als Standardbild setzen
							if($stdbild=='1') $this->app->DB->Update("UPDATE artikel SET standardbild='$fileid' WHERE id='{$_COOKIE['wizard_artikel']}' LIMIT 1");
						}else
							$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">$error</div>");
					}

					// Tabelle anzeigen
					$dateien = $this->app->DB->SelectArr("SELECT d.id, d.titel, d.beschreibung, ds.subjekt FROM datei_stichwoerter AS ds
																								LEFT JOIN datei AS d ON d.id = ds.datei 
																								WHERE (subjekt='Gruppenbild' OR subjekt='Shopbild') 
																								AND objekt='Artikel' AND parameter='{$_COOKIE['wizard_artikel']}' AND d.geloescht='0'");

					for($i=0;$i<count($dateien);$i++)
					{
						$daten = $this->app->DB->SelectArr("SELECT dateiname FROM datei_version
																								WHERE datei='{$dateien[$i][id]}' ORDER BY version DESC LIMIT 1");
						$std = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='{$_COOKIE['wizard_artikel']}' LIMIT 1");
						$standardbild = (($std==$dateien[$i][id]) ? "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ack.png\" >" : "");
						$out .= "<tr>
											<td>{$daten[0][dateiname]}</td><td>{$dateien[$i][titel]}</td><td>{$dateien[$i][subjekt]}</td><td>$standardbild</td>
											<td>".((is_numeric($dateien[$i][id])) ? 
														 "<a href=\"./index.php?module=wizard&action=create&step=bilder&standard={$dateien[$i][id]}\">
                              <img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/right.png\"></a>
                              <a href=\"./index.php?module=wizard&action=create&step=bilder&delete={$dateien[$i][id]}\">
                              <img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.png\"></a>" : " - ")."</td></tr>";
					}
					if(count($dateien)==0)
						$out = "<tr><td colspan=\"5\">Keine Bilder vorhanden</td></tr>";

					$this->app->Tpl->Set(TABLE, $out);


					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('bilder'));
					$this->app->Tpl->Parse(CONTENT,"wizard_bilder.tpl");	
				break;
				// ----------------------------------------------- FINISH ---------------------------------------------------
				case 'finish':
          $submit = $this->app->Secure->GetPOST("submit");
          $back = $this->app->Secure->GetPOST("back");

          if($submit!="" && is_numeric($_COOKIE['wizard_artikel']))
          {
						$projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='{$_COOKIE['wizard_projekt']}' LIMIT 1");
						$kurztext = base64_decode($_COOKIE['wizard_kurztext']);
						$uebersichtstext = base64_decode($_COOKIE['wizard_uebersichtstext']);
						$ust = (($_COOKIE['wizard_ust']=='1') ? 'ermaessigt' : '');
						$standardlieferant = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".($this->getNumber($_COOKIE['wizard_standardlieferant']))."' LIMIT 1");
						$standardlieferant = (($this->getNumber($_COOKIE['wizard_standardlieferant'])=='') ? '' : $standardlieferant);

						// Artikel anlegen
						$this->app->DB->Update("UPDATE artikel SET typ='{$_COOKIE['wizard_typ']}', projekt='$projekt', name_de='{$_COOKIE['wizard_name']}',
																		kurztext_de='$kurztext', uebersicht_de='$uebersichtstext', seriennummern='{$_COOKIE['wizard_seriennummer']}', endmontage='{$_COOKIE['wizard_endmontage']}',
																		stueckliste='{$_COOKIE['wizard_stueckliste']}', juststueckliste='{$_COOKIE['wizard_jitliste']}', lagerartikel='{$_COOKIE['wizard_lagerartikel']}', 
																		porto='{$_COOKIE['wizard_porto']}', chargenverwaltung='{$_COOKIE['wizard_charge']}', umsatzsteuer='$ust', neu='{$_COOKIE['wizard_neu']}',
																		topseller='{$_COOKIE['wizard_topseller']}', startseite='{$_COOKIE['wizard_startseite']}', wichtig='{$_COOKIE['wizard_wichtig']}', 
																		partnerprogramm_sperre= '{$_COOKIE['wizard_partnersperre']}', firma='".$this->app->User->GetFirma()."', shop='{$_COOKIE['wizard_shop']}', adresse='$standardlieferant',
																		lieferzeit='lager' 
																		WHERE id='{$_COOKIE['wizard_artikel']}' LIMIT 1");


						//Artikel-ID eintragen
						if(is_numeric($_COOKIE['wizard_nummer']))
						{
							$existiert = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='{$_COOKIE['wizard_nummer']}' LIMIT 1");
							if(!is_numeric($existiert))
								$this->app->DB->Update("UPDATE artikel SET nummer='{$_COOKIE['wizard_nummer']}' WHERE id='{$_COOKIE['wizard_artikel']}' LIMIT 1");
						}

						// Einkaufspreise
						$ek = unserialize(base64_decode($_COOKIE['wizard_ek']));
						for($i=0;$i<count($ek);$i++)
						{
							$adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".($this->getNumber($ek[$i][lieferant]))."' LIMIT 1");
							$standard = (($ek[$i][standardlieferant]=='1') ? '1' : '0');
							$firma = $this->app->User->GetFirma();
							$this->app->DB->Insert("INSERT INTO einkaufspreise (artikel, adresse, objekt, preis, waehrung, ab_menge, bestellnummer, bezeichnunglieferant, standard, firma)
																			VALUES ('{$_COOKIE['wizard_artikel']}','{$adresse}','Standard','{$ek[$i][preis]}','EUR','{$ek[$i][menge]}','{$ek[$i][liefernummer]}',
																			'{$ek[$i][lieferbez]}',$standard,'$firma')");

						}

						// Verkaufspreise
						$vk = unserialize(base64_decode($_COOKIE['wizard_vk']));
						for($i=0;$i<count($vk);$i++)
            {
	
							if($this->getNumber($vk[$i][kunde])>0)
							{
								$adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".($this->getNumber($vk[$i][kunde]))."' LIMIT 1");
							}
							else $adresse=0;
							$firma = $this->app->User->GetFirma();
							$this->app->DB->Insert("INSERT INTO verkaufspreise (artikel, objekt, adresse, preis, waehrung, ab_menge, firma)
																			VALUES ('{$_COOKIE['wizard_artikel']}','Standard','{$adresse}','{$vk[$i][preis]}','EUR', '{$vk[$i][menge]}','$firma')");

						}

						// stueckliste
					$data = unserialize(base64_decode($_COOKIE["wizard_stuecklisteElemente"]));
					$rows = count($data);
					for($i=0; $i<$rows; $i++)
          {
            $artikel = ((isset($data[$i][artikel])==true) ? $data[$i][artikel] : "");
            $menge = ((isset($data[$i][menge])==true) ? $data[$i][menge] : "");
						$firma = $this->app->User->GetFirma();
						
						$tmp = trim($artikel);
						$nummer = substr($tmp, 0, 6);
						$artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND firma='$firma' LIMIT 1");
						if($artikelid>0)
            $this->app->DB->Insert("INSERT INTO stueckliste (id,sort,stuecklistevonartikel,menge,artikel,firma)
                                      VALUES ('','".($i+1)."','{$_COOKIE['wizard_artikel']}','$menge','$artikelid','$firma')");
          }



						// Kategorien und Shop
						$kat = unserialize(base64_decode($_COOKIE[wizard_kategorien]));				
						for($i=0;$i<count($kat);$i++)
						{
							$artid = $this->app->DB->Select("SELECT id FROM artikel_artikelgruppe WHERE artikel='{$_COOKIE['wizard_artikel']}' AND artikelgruppe='{$kat[$i]}' LIMIT 1");
							if(!is_numeric($artid))
								$this->app->DB->Insert("INSERT INTO artikel_artikelgruppe (artikel, artikelgruppe) VALUES ('{$_COOKIE['wizard_artikel']}', '{$kat[$i]}')");
						}	

						$shopid = $this->app->DB->Select("SELECT id FROM artikel_shop WHERE artikel='{$_COOKIE['wizard_artikel']}' AND shop='{$_COOKIE['wizard_shop']}' LIMIT 1");
						if(is_numeric($_COOKIE['wizard_shop']) && !is_numeric($shopid))
							$this->app->DB->Insert("INSERT INTO artikel_shop(artikel, shop) VALUES ('{$_COOKIE['wizard_artikel']}', '{$_COOKIE['wizard_shop']}')");


						$this->app->erp->EventAPIAdd("EventArtikelCreate",$_COOKIE['wizard_artikel'],"artikel","create",$kommentar="Wizard");

						//Cookies vernichten
						setcookie("wizard_name", "", time()-86400);
						setcookie("wizard_typ", "", time()-86400);
						setcookie("wizard_porto", "", time()-86400);
						setcookie("wizard_artikel", "", time()-86400);
						setcookie("wizard_kurztext", "", time()-86400);
						setcookie("wizard_uebersichtstext", "", time()-86400);
						setcookie("wizard_stueckliste", "", time()-86400);
						setcookie("wizard_jitliste", "", time()-86400);
						setcookie("wizard_lagerartikel", "", time()-86400);
						setcookie("wizard_charge", "", time()-86400);
						setcookie("wizard_endmontage", "", time()-86400);
						setcookie("wizard_seriennummer", "", time()-86400);
						setcookie("wizard_ust", "", time()-86400);
						setcookie("wizard_neu", "", time()-86400);
						setcookie("wizard_topseller", "", time()-86400);
						setcookie("wizard_startseite", "", time()-86400);
						setcookie("wizard_wichtig", "", time()-86400);
						setcookie("wizard_partnersperre", "", time()-86400);
						setcookie("wizard_kategorien", "", time()-86400);
						setcookie("wizard_nummer", "", time()-86400);
						setcookie("wizard_projekt", "", time()-86400);
						setcookie("wizard_standardlieferant", "", time()-86400);
						setcookie("wizard_ek", "", time()-86400);
						setcookie("wizard_vk", "", time()-86400);
						setcookie("wizard_shop", "", time()-86400);
						setcookie("wizard_andererstandardlieferant", "", time()-86400);
						setcookie("wizard_tmpKategorien", "", time()-86400);
						setcookie("wizard_markKategorien", "", time()-86400);
						setcookie("wizard_stuecklisteElemente", "", time()-86400);
						unset($_SESSION['wizard_stueckliste']);

						// Und zurueck zur Uebersicht
						header("Location: ./index.php?module=artikel&action=edit&id={$_COOKIE['wizard_artikel']}");
						exit;
          }

          if($back!="")
          {
            // Weiterleitung zur letzten Action
            $this->PageBack($_COOKIE["wizard_typ"], "finish");
          }

					// Welche div's sollen angezeigt werden?
					$valid = $this->GetActions();
					$div = array('1' => 'box1', 'text' => 'box2', '2' => 'box3', 'preise' => 'box4', 'onlineshop' => 'box5', 'bilder' => 'box6');
					foreach($div as $key => $value)
					{
						if(!in_array($key, $valid[$_COOKIE['wizard_typ']]))
							$this->app->Tpl->Set(strtoupper($value), "none");

					}

					//################## Allgemeine Informationen ##################
					$this->app->Tpl->Set(NAME , $_COOKIE['wizard_name']);
					$this->app->Tpl->Set(ART , $this->GetArtName($_COOKIE['wizard_typ']));
					$nummer = ((is_numeric($_COOKIE['wizard_nummer'])) ? $_COOKIE['wizard_nummer'] : "Wird automatisch erzeugt");
					$this->app->Tpl->Set(NUMMER , $nummer);
					$this->app->Tpl->Set(PROJEKT , $_COOKIE['wizard_projekt']);
					$this->app->Tpl->Set(LIEFERANT , $_COOKIE['wizard_standardlieferant']);

					//################## Artikelbeschreibung ##################
					$this->app->Tpl->Set(KURZTEXT , base64_decode($_COOKIE['wizard_kurztext']));
					$this->app->Tpl->Set(UEBERSICHTSTEXT , base64_decode($_COOKIE['wizard_uebersichtstext']));

					//################## Lageroptionen ##################
					$this->app->Tpl->Set(STUECKLISTE , (($_COOKIE['wizard_stueckliste']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(JITLISTE , (($_COOKIE['wizard_jitliste']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(LAGERARTIKEL , (($_COOKIE['wizard_lagerartikel']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(CHARGE , (($_COOKIE['wizard_charge']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(ENDMONTAGE , (($_COOKIE['wizard_endmontage']==1) ? 'ja' : 'nein'));	
					$this->app->Tpl->Set(SERIENNUMMER , $this->GetSeriennummer($_COOKIE['wizard_seriennummer']));
					$this->app->Tpl->Set(UST, (($_COOKIE['wizard_ust']==1) ? 'ja' : 'nein'));

					//################## EK/VK Preise ##################
					$ek = unserialize(base64_decode($_COOKIE['wizard_ek']));
					for($i=0;$i<count($ek);$i++)
					{
						$ek_out .= "<tr>
													<td>{$ek[$i][lieferant]}</td>
													<td>{$ek[$i][lieferbez]}</td>
													<td>{$ek[$i][liefernummer]}</td>
													<td>{$ek[$i][menge]}</td>
													<td>{$ek[$i][preis]}</td>
													<td>".(($ek[$i][standardlieferant]==1) ? "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ack.png\">" : "")."</td>
												</tr>";	

					}
					if(count($ek)==0)
						$this->app->Tpl->Set(EKPREISE, "<tr><td colspan=\"6\">Keine Daten vorhanden</td></tr>");
					else
						$this->app->Tpl->Set(EKPREISE, $ek_out);

					$vk = unserialize(base64_decode($_COOKIE['wizard_vk']));
					for($i=0;$i<count($vk);$i++)
          {
						$vk_out .= "<tr>
													<td>{$vk[$i][kunde]}</td>
													<td>{$vk[$i][menge]}</td>
													<td>{$vk[$i][preis]}</td>
												</tr>";
					}
					if(count($vk)==0)
            $this->app->Tpl->Set(VKPREISE, "<tr><td colspan=\"6\">Keine Daten vorhanden</td></tr>");
          else
            $this->app->Tpl->Set(VKPREISE, $vk_out);

					//################## Shopoptionen ##################
					if(is_numeric($_COOKIE['wizard_shop']))
						$shop = $this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id='{$_COOKIE['wizard_shop']}' LIMIT 1");
					else $shop = "Kein Shopartikel";
					
					$this->app->Tpl->Set(ONLINESHOP, $shop);
					$this->app->Tpl->Set(NEU, (($_COOKIE['wizard_neu']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(TOPSELLER, (($_COOKIE['wizard_topseller']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(STARTSEITE, (($_COOKIE['wizard_startseite']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(WICHTIG, (($_COOKIE['wizard_wichtig']==1) ? 'ja' : 'nein'));
					$this->app->Tpl->Set(SPERRE, (($_COOKIE['wizard_partnersperre']==1) ? 'ja' : 'nein'));


					$kat = unserialize(base64_decode($_COOKIE['wizard_kategorien']));
					for($i=0;$i<count($kat);$i++)
					{
						$kat_name = $this->app->DB->SelectArr("SELECT bezeichnung, bezeichnung_en FROM artikelgruppen 
																									 WHERE id='{$kat[$i]}' AND shop='{$_COOKIE['wizard_shop']}' 
																									 AND aktiv='1' LIMIT 1");
						$kat_name = (($kat_name[0][bezeichnung]=="") ? $kat_name[0][bezeichnung_en] : $kat_name[0][bezeichnung]);
						$kat_out .= $kat_name.((($i+1)==count($kat)) ? "" : " | " );
						if(($i+1)%10==0) $kat_out .= "<br>";
					}
					$this->app->Tpl->Set(KATEGORIEN, $kat_out);

					//################## Bilder ##################
					$dateien = $this->app->DB->SelectArr("SELECT d.id, d.titel, d.beschreibung, ds.subjekt FROM datei_stichwoerter AS ds
                                                LEFT JOIN datei AS d ON d.id = ds.datei 
                                                WHERE (subjekt='Gruppenbild' OR subjekt='Shopbild') 
                                                AND objekt='Artikel' AND parameter='{$_COOKIE['wizard_artikel']}' AND d.geloescht='0'");

					for($i=0;$i<count($dateien);$i++)
          {
            $daten = $this->app->DB->SelectArr("SELECT dateiname FROM datei_version
                                                WHERE datei='{$dateien[$i][id]}' ORDER BY version DESC LIMIT 1");
            $std = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='{$_COOKIE['wizard_artikel']}' LIMIT 1");
            $standardbild = (($std==$dateien[$i][id]) ? "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ack.png\" >" : "");
            $bilder_out .= "<tr><td>{$daten[0][dateiname]}</td><td>{$dateien[$i][titel]}</td><td>{$dateien[$i][subjekt]}</td><td>$standardbild</td></tr>";
          }
          if(count($dateien)==0)
            $bilder_out = "<tr><td colspan=\"5\">Keine Bilder vorhanden</td></tr>";
          $this->app->Tpl->Set(BILDER, $bilder_out);


					$this->app->Tpl->Set(BACKBUTTONTEXT,"Fertigstellen");
					$this->app->Tpl->Set(TABTEXT,"Artikel anlegen - ".$this->getStepText('finish'));
					$this->app->Tpl->Parse(CONTENT,"wizard_finish.tpl");	
				break;
				// ----------------------------------------------- DEFAULT --------------------------------------------------
				default:
					$this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Sie haben einen ung&uuml;ltigen Schritt aufgerufen.</div>");	
					$this->app->Tpl->SET(BACKBUTTON,"disabled");
					$this->app->Tpl->SET(SUBMITBUTTON,"disabled");
				break;
			}
		}

		if($step!="finish")	$this->app->Tpl->Set(BACKBUTTONTEXT,"Weiter");
		if($step!="1")$this->app->Tpl->Parse(TAB1,"wizardform.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
	}

	function isEmpty($var)
	{
		if($var!='') return $var;
	}

	function PageForward($typ, $step, $stueckliste='0')
  {
    $valid = $this->GetActions($stueckliste);
    $firstpage = "1";
    $lastpage = "finish";

		//echo "COOKIE: {$_COOKIE["wizard_stueckliste"]}<br>";
		//print_r($valid);

    if(in_array($step, $valid[$typ]))
      $next = array_search($step, $valid[$typ]) + 1;

    $ref = ((isset($valid[$typ][$next])==true) ? $valid[$typ][$next] : $lastpage);

    header("Location: ./index.php?module=wizard&action=create&step=$ref");
    exit;
  }
	
	function PageBack($typ, $step, $stueckliste='0')
  {
    $valid = $this->GetActions($stueckliste);
    $firstpage = "1";
    $lastpage = "finish";
   

 
    if(in_array($step, $valid[$typ]))
      $next = array_search($step, $valid[$typ]) - 1;

		//$s = array_search($step, $valid[$typ]) - 1;

		//echo "STEP: $step, TYP: $typ";
		//echo "SEARCH: $s, NEXT: $next, VALID: {$valid[$typ][$next]}";
                 
    $ref = ((isset($valid[$typ][$next])==true) ? $valid[$typ][$next] : $lastpage);

    header("Location: ./index.php?module=wizard&action=create&step=$ref");
    exit;
  }

	function saveEkVk($ek, $vk)
	{
		$stdLieferId = $this->getNumber($_COOKIE["wizard_standardlieferant"]);

		// ----------------------------------- EINKAUFSPREISE -----------------------------------
		for($i=0;$i<count($ek);$i++)
    {
    	// Komma-Preise umwandeln
      if($ek[$i][preis]!="")  $ek[$i][preis] = str_replace(",",".",$ek[$i][preis]);

      // Leere Eintraege finden, ansonsten id Filtern
      if($ek[$i][lieferant]=="") $ek[$i]="";
      else
      {
      	$lid = $this->getNumber($ek[$i][lieferant]);
        if(!is_numeric($lid)) $ek[$i]="";
      }
    }

    // und loeschen
    $count_ek = count($ek);
    for($i=0;$i<$count_ek;$i++)
    {
	    if(!is_array($ek[$i]))
  	    unset($ek[$i]);
    }

    // Index wiederherstellen
    $ek = array_values($ek);

		$markiert=false;
	  // In Step 1 als Standardlieferant gesetzter Lieferant setzen
    if(is_numeric($stdLieferId) && $_COOKIE['wizard_andererstandardlieferant']==0)
    {
    	for($i=0;$i<count($ek);$i++)
      {
      	if($ek[$i][lieferant]!="")
        {
        	$lieferId = $this->getNumber($ek[$i][lieferant]);
          if($lieferId==$stdLieferId && $markiert==false)
					{
          	$ek[$i][standardlieferant]=1;
          	$markiert=true;
					}else
            $ek[$i][standardlieferant]=0;
        }
      }
    }

    setcookie("wizard_ek", base64_encode(serialize($ek)));

		// ----------------------------------- VERKAUFSPREISE -----------------------------------
		for($i=0;$i<count($vk);$i++)
    {
    	// Komma-Preise umwandeln
      if($vk[$i][preis]!="") $vk[$i][preis] = str_replace(",",".",$vk[$i][preis]);

      // Leere Eintraege finden
      if($vk[$i][menge]=="" || $vk[$i][preis]=="")
      	$vk[$i]="";
      else
			{
      	// Setze 'Alle' fuer leere Kundenfelder
        if($vk[$i][kunde]=="" || $vk[$i][kunde]=="Alle")
        	$vk[$i][kunde]="Alle";
        else
				{
        	$lid_found = $this->getNumber($vk[$i][kunde]);
          if(!is_numeric($lid_found)) $vk[$i]="";
        }
      }
    }

    // und loeschen
    $count_vk = count($vk);
    for($i=0;$i<$count_vk;$i++)
    {
    	if(!is_array($vk[$i]))
      	unset($vk[$i]);
    }

    // Index wiederherstellen
    $vk = array_values($vk);

    setcookie("wizard_vk", base64_encode(serialize($vk)));
	}

	function saveStueckliste($liste)
	{
		for($i=0;$i<count($liste);$i++)
		{
			if(!isset($liste[$i][artikel]) || $liste[$i][artikel]=="")
				$liste[$i]="";
			else
			{
				$artikelNr = $this->getNumber($liste[$i][artikel]);
				if(!is_numeric($artikelNr))
					$liste[$i]="";
			}
		}

		// Leere Eintraege loeschen
		$countListe = count($liste);
		for($i=0;$i<$countListe;$i++)
		{
			if(!is_array($liste[$i]))
				unset($liste[$i]);
		}

		// Index wiederherstellen
		$liste = array_values($liste);

		setcookie("wizard_stuecklisteElemente", base64_encode(serialize($liste)));
	}

	function GetActions()
	{
		$valid = array("new" =>						array('','1','text'),
									 "produkt" =>       array('','1','text','2','preise','onlineshop','bilder','finish'),
									 "produktion" =>    array('','1','text','2','preise','finish'),
									 "fremdleistung" => array('','1','text','preise','finish'),
									 "gebuehr" =>       array('','1','text','preise','finish'),
									 "material" =>      array('','1','text','preise','finish'));

		// Checkbox 'Stueckliste' aktiv
		if($_SESSION['wizard_stueckliste']=='1')
		{
			$valid[produkt] = array('','1','text','2','stueckliste','preise','onlineshop','bilder','finish');
			$valid[produktion] = array('','1','text','2','stueckliste','preise','finish');
		}

		// Porto ist ein Sonderfall
		if($_COOKIE["wizard_porto"]==1)
			$valid[produkt] = array('1','text','preise','finish');

		return $valid;
	}

	function GetArtName($art)
	{
		$type = array('produkt' => 'Ware f&uuml;r Verkauf',
									'produktion' => 'Produktionsmaterial',
									'fremdleistung' => 'Dienst-/Fremdleistung',
									'gebuehr' => 'Geb&uuml;hr / Miete',
									'material' => 'Sonstiges');

		if($typ=="produkt" && $_COOKIE['wizard_porto']==1)
			return 'Porto';
		else
			return $type[$art];
	}

	function getStepNames()
	{
		$steps = array('1' => 'Allgemeine Informationen',
									 'text' => 'Artikel beschreiben',
									 '2' => 'Lageroptionen',
									 'stueckliste' => 'St&uuml;ckliste erstellen',
									 'preise' => 'EK/VK Preise',
									 'onlineshop' => 'Shopoptionen',
									 'bilder' => 'Artikel-Bilder',
									 'finish' => 'Artikel&uuml;bersicht');
		return $steps;
	}

	function getStepText($step)
	{
		$steps = $this->getStepNames();
		return $steps[$step];
	}

	function GetSeriennummer($art)
	{
		$type = array('vomprodukt' => 'Originale beibehalten',
									'eigene' => 'Neue erzeugen',
									'keine' => 'Keine Seriennummer');
		return $type[$art];

	}

	function BuildMenu($typ)
	{
		if($typ!='new' && $typ!='')
		{
			$actions = $this->GetActions();
			$steps = $this->getStepNames();

			$valid = $actions[$typ];
			unset($valid[0]);

			foreach($valid as $value)
				$this->app->erp->MenuEintrag("index.php?module=wizard&action=create&step=$value", $steps[$value]);				

		
			$this->app->erp->MenuEintrag("index.php?module=artikel&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
		}
	}

	function getNumber($input)
	{
		$number = '';
    preg_match("/[0-9]+/", $input, $number);
		if(is_numeric($number[0]))
			return $number[0];
		return null;
	}

}
?>

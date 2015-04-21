<?php

class Linkeditor
{

  function Linkeditor(&$app)
  {
    $this->app=&$app; 
  
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list", "LinkeditorList");
    $this->app->ActionHandler("massedit", "LinkeditorMassEdit");
    $this->app->ActionHandler("delete", "LinkeditorEdit");
    $this->app->ActionHandler("status", "LinkeditorStatus");
    $this->app->ActionHandler("help", "LinkeditorHelp");
    $this->app->ActionHandler("deleterule", "LinkeditorDeleteRule");
  
    $this->app->DefaultActionHandler("edit");
    
    $this->app->ActionHandlerListen($app);
  }

	function SearchForLinks($textfield) {
		$links = array();
		if($textfield!='') {
			$page = str_get_html(htmlspecialchars_decode($textfield, ENT_QUOTES));
			$tmp_links = $page->find('a');
			foreach($tmp_links as $link) {
				if($link->href!='') $links[] = $link->href;
			}
		}
		return $links;
	}

	function ApplyRules($tmp_links) {
    $active_rules = $this->app->DB->SelectArr("SELECT * FROM linkeditor WHERE active='1' ORDER BY rule");
    $links = array();

    for($l=0;$l<count($tmp_links);$l++) {
      $curLink = $tmp_links[$l];
      $newLink = '';
			$fromMarked = '';
			$toMarked = '';
      for($r=0;$r<count($active_rules);$r++) {
        $pattern = "/{$active_rules[$r]['rule']}/";
        $found = preg_match($pattern, $curLink, $matches, PREG_OFFSET_CAPTURE);
        if($found>0) {
          $newLink = str_replace($matches[0][0], $active_rules[$r]['replacewith'], $curLink);
          $fromMarked = str_replace($matches[0][0], '<span class="found">'.$matches[0][0].'</span>', $curLink);

          $toMarked = str_replace($active_rules[$r]['replacewith'], '<span class="replaced">'.$active_rules[$r]['replacewith'].'</span>', $newLink);
        }
      }
      $links[] = array('from'=>$curLink, 'to'=>$newLink, 'from_marked'=>$fromMarked, 'to_marked'=>$toMarked);
    }
		return $links;
	}

	function AddRule()
	{
		$rule_regex = $this->app->Secure->POST['rule_regex'];
    $rule_replace = $this->app->Secure->GetPOST('rule_replace');
    $rule_submit = $this->app->Secure->GetPOST('rule_submit');

		if($rule_submit!='' && $rule_regex!='') {
      $this->app->DB->Insert("INSERT INTO linkeditor (rule, replacewith) VALUES ('$rule_regex', '$rule_replace')");
    }
	}

	function ListRules($ref)
	{
		$ref = base64_encode($ref);
		$rules = $this->app->DB->SelectArr("SELECT * FROM linkeditor ORDER BY rule");
    for($i=0;$i<count($rules);$i++) {
      $checked = (($rules[$i]['active']=='1') ? 'checked' : '');
      $color = (($i%2==0) ? '#fff' : '#e0e0e0');
      $rules_out .= "<tr style=\"background-color:$color;\">
                         <td>".htmlspecialchars($rules[$i]['rule'])."</td>
                         <td>".htmlspecialchars($rules[$i]['replacewith'])."</td>
                         <td><input type=\"checkbox\" id=\"rule_active$i\" $checked onclick=\"SetStatus(this, {$rules[$i]['id']})\"></td>
                         <td align=\"center\">
                            <input type=\"button\" value=\"Löschen\" onclick=\"DeleteDialog('./index.php?module=linkeditor&action=deleterule&id={$rules[$i]['id']}&ref=$ref');\">
                         </td></tr>";
    }
		return $rules_out;
	}

	function LinkeditorMassEdit()
	{
		$this->app->Tpl->Set(UEBERSCHRIFT,"Linkeditor");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Linkeditor");
    $this->app->Tpl->Set(TABTEXT,"Masseneditor");
		$this->LinkeditorMenu();

		$replace = $this->app->Secure->GetPOST('replace');
		$replace_submit = $this->app->Secure->GetPOST('replace_submit');
		$shop = $this->app->Secure->GetGET('shop');
	
		if($shop!='') {
			$shopid = $this->app->DB->Select("SELECT id FROM shopexport WHERE bezeichnung='$shop' LIMIT 1");
			if(is_numeric($shopid)) {
				$articles = $this->app->DB->SelectArr("SELECT id, uebersicht_de, uebersicht_en, beschreibung_de, beschreibung_en, links_de, links_en 
																							 FROM artikel WHERE shop='$shopid' ORDER BY nummer");
				for($i=0;$i<count($articles);$i++) {
						$uebersicht_links = $this->SearchForLinks($articles[$i]['uebersicht_de']);
						$uebersicht_links_en = $this->SearchForLinks($article[$i]['uebersicht_en']);

						$beschreibung_links = $this->SearchForLinks($article[$i]['beschreibung_de']);
						$beschreibung_links_en = $this->SearchForLinks($article[$i]['beschreibung_en']);

						$links_links = $this->SearchForLinks($article[$i]['links_de']);
						$links_links_en = $this->SearchForLinks($article[$i]['links_en']);

						$tmp_links = $this->ApplyRules(array_merge($uebersicht_links, $uebersicht_links_en, $beschreibung_links, $beschreibung_links_en, $links_links, $links_links_en));

						if(is_array($tmp_links) && count($tmp_links)>0) {
							$links[$articles[$i]['id']] = $tmp_links;
					}
				}
			}
		}

		//print_r($links);

		if($replace_submit!='' && is_array($replace)) {
			foreach($replace as $key=>$value) {
				$params = explode('-', $key);
				$artId = $params[0];
				$linkId = $params[1];

				if(isset($links[$artId][$linkId]) && $links[$artId][$linkId]['to']!='') {
					$link = $links[$artId][$linkId];

					$article = $this->app->DB->SelectArr("SELECT uebersicht_de, uebersicht_en, beschreibung_de, beschreibung_en, links_de, links_en 
                                              FROM artikel WHERE id='$artId' LIMIT 1");
					$uebersicht_new = htmlspecialchars_decode($article[0]['uebersicht_de'], ENT_QUOTES);
					$uebersicht_new_en = htmlspecialchars_decode($article[0]['uebersicht_en'], ENT_QUOTES);

					$beschreibung_new = htmlspecialchars_decode($article[0]['beschreibung_de'], ENT_QUOTES);
					$beschreibung_new_en = htmlspecialchars_decode($article[0]['beschreibung_en'], ENT_QUOTES);

					$links_new = htmlspecialchars_decode($article[0]['links_de'], ENT_QUOTES);
					$links_new_en = htmlspecialchars_decode($article[0]['links_en'], ENT_QUOTES);

					$uebersicht_new = str_replace($links[$artId][$linkId]['from'], $links[$artId][$linkId]['to'], $uebersicht_new);
          $uebersicht_new_en = str_replace($links[$artId][$linkId]['from'], $links[$artId][$linkId]['to'], $uebersicht_new_en);

          $beschreibung_new = str_replace($links[$artId][$linkId]['from'], $links[$artId][$linkId]['to'], $beschreibung_new);
          $beschreibung_new_en = str_replace($links[$artId][$linkId]['from'], $links[$artId][$linkId]['to'], $beschreibung_new_en);

          $links_new = str_replace($links[$artId][$linkId]['from'], $links[$artId][$linkId]['to'], $links_new);
          $links_new_en = str_replace($links[$artId][$linkId]['from'], $links[$artId][$linkId]['to'], $links_new_en);	

					$this->app->DB->Update("UPDATE artikel SET uebersicht_de='".htmlspecialchars($uebersicht_new)."', uebersicht_en='".htmlspecialchars($uebersicht_new_en)."', 
																	beschreibung_de='".htmlspecialchars($beschreibung_new)."', beschreibung_en='".htmlspecialchars($beschreibung_new_en)."', 
																	links_de='".htmlspecialchars($links_new)."', links_en='".htmlspecialchars($links_new_en)."' WHERE id='$artId' LIMIT 1");

				}
			}
			header('Location: ./index.php?module=linkeditor&action=massedit&shop='.$shop);
      exit;
		}


		$index = 0;
		foreach($links as $key=>$value) {
			$article_data = $this->app->DB->SelectArr("SELECT name_de, nummer FROM artikel WHERE id='$key' LIMIT 1");
			for($i=0;$i<count($value); $i++) {	
				$color = (($index%2==0) ? '#fff' : '#e0e0e0');
				$from_text = (($value[$i]['from_marked']!='') ? $value[$i]['from_marked'] : $value[$i]['from']);
				$orderno = (($i==0) ? "<a href=\"./index.php?module=artikel&action=edit&id=$key\" alt=\"{$article_data[$i]['name_de']}\" 
                             title=\"{$article_data[0]['name_de']}\">{$article_data[0]['nummer']}</a>" : "");
				$links_out .= "<tr style=\"background-color:$color;\">
                      <td>$orderno</td>
                      <td>$from_text</td>
                      <td>{$value[$i]['to_marked']}</td>
                      <td align=\"center\"><input type=\"checkbox\" name=\"replace[$key-$i]\"></td>
                     </tr>";
				$index++;
			}
    }


		$this->app->YUI->AutoComplete('shop',"shopname");
    $this->app->Tpl->Set('LINKS', $links_out);
    $this->app->Tpl->Set('SHOP', $shop);
		$this->app->Tpl->Set('RULES', $this->ListRules("./index.php?module=linkeditor&action=massedit"));
		$this->app->Tpl->Parse('TAB1',"linkeditor_massedit.tpl");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
	}

	function LinkeditorMenu()
	{
		$this->app->erp->MenuEintrag('index.php?module=linkeditor&action=list','Einzeleditor');
		$this->app->erp->MenuEintrag('index.php?module=linkeditor&action=massedit','Masseneditor');
		$this->app->erp->MenuEintrag('index.php?module=linkeditor&action=help','Hilfe');
	}

	function LinkeditorHelp()
	{
		$this->app->Tpl->Set(UEBERSCHRIFT,"Linkeditor");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Linkeditor");
    $this->app->Tpl->Set(TABTEXT,"Hilfe");
		$this->LinkeditorMenu();

		
		$this->app->Tpl->Parse('TAB1',"linkeditor_help.tpl");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
	}

	function LinkeditorList()
	{
		$this->app->Tpl->Set(UEBERSCHRIFT,"Linkeditor");
		$this->app->Tpl->Set(KURZUEBERSCHRIFT,"Linkeditor");
    $this->app->Tpl->Set(TABTEXT,"Einzeleditor");
		$this->LinkeditorMenu();

		$id = $this->app->Secure->GetGET('id');
		
		$replace = $this->app->Secure->GetPOST('replace');
		$replace_submit = $this->app->Secure->GetPOST('replace_submit');

		$this->AddRule();

		$article = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id='$id' LIMIT 1");

		// search links
		$uebersicht_links = $this->SearchForLinks($article[0]['uebersicht_de']);
		$uebersicht_links_en = $this->SearchForLinks($article[0]['uebersicht_en']);

		$beschreibung_links = $this->SearchForLinks($article[0]['beschreibung_de']);
		$beschreibung_links_en = $this->SearchForLinks($article[0]['beschreibung_en']);

		$links_links = $this->SearchForLinks($article[0]['links_de']);
		$links_links_en = $this->SearchForLinks($article[0]['links_en']);

		$tmp_links = array_merge($uebersicht_links, $uebersicht_links_en, $beschreibung_links, $beschreibung_links_en, $links_links, $links_links_en);

		$links = $this->ApplyRules($tmp_links);

		if($replace_submit!='' && is_array($replace) && is_numeric($id)) {
			$uebersicht_new = htmlspecialchars_decode($article[0]['uebersicht_de'], ENT_QUOTES);
			$uebersicht_new_en = htmlspecialchars_decode($article[0]['uebersicht_en'], ENT_QUOTES);

			$beschreibung_new = htmlspecialchars_decode($article[0]['beschreibung_de'], ENT_QUOTES);
			$beschreibung_new_en = htmlspecialchars_decode($article[0]['beschreibung_en'], ENT_QUOTES);

			$links_new = htmlspecialchars_decode($article[0]['links_de'], ENT_QUOTES);
			$links_new_en = htmlspecialchars_decode($article[0]['links_en'], ENT_QUOTES);

			foreach($replace as $key=>$value) {
				$search = $links[$key]['from'];
				$replacewith = $links[$key]['to'];
			
				if($replacewith!='') { 
					$uebersicht_new = str_replace($search, $replacewith, $uebersicht_new);
					$uebersicht_new_en = str_replace($search, $replacewith, $uebersicht_new_en);

					$beschreibung_new = str_replace($search, $replacewith, $beschreibung_new);
					$beschreibung_new_en = str_replace($search, $replacewith, $beschreibung_new_en);
							
					$links_new = str_replace($search, $replacewith, $links_new);
					$links_new_en = str_replace($search, $replacewith, $links_new_en);
				}
			}

			$this->app->DB->Update("UPDATE artikel SET uebersicht_de='".htmlspecialchars($uebersicht_new)."', uebersicht_en='".htmlspecialchars($uebersicht_new_en)."', 
                                beschreibung_de='".htmlspecialchars($beschreibung_new)."', beschreibung_en='".htmlspecialchars($beschreibung_new_en)."', 
                                links_de='".htmlspecialchars($links_new)."', links_en='".htmlspecialchars($links_new_en)."' WHERE id='$id' LIMIT 1");

			header('Location: ./index.php?module=linkeditor&action=list&id='.$id);
			exit;
		}

		for($i=0;$i<count($links);$i++) {
			$color = (($i%2==0) ? '#fff' : '#e0e0e0');
			$from_text = (($links[$i]['from_marked']!='') ? $links[$i]['from_marked'] : $links[$i]['from']);
			$links_out .= "<tr style=\"background-color:$color;\">
											<td>$from_text</td>
											<td>{$links[$i]['to_marked']}</td>
											<td align=\"center\"><input type=\"checkbox\" name=\"replace[$i]\" checked></td>
										 </tr>";
		}
		$this->app->Tpl->Set('LINKS', $links_out);

	
		// List Articles	
		$_SESSION['bookmarked'] = array(48, 80, 7, 81, 2, 11, 54);
		for($i=0;$i<count($_SESSION['bookmarked']); $i++){
			$active = (($id==$_SESSION['bookmarked'][$i]) ? 'class="active"' : ''); 
			$name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='{$_SESSION['bookmarked'][$i]}' LIMIT 1");
			$link_out .= "<a href=\"./index.php?module=linkeditor&action=list&id={$_SESSION['bookmarked'][$i]}\" $active>$name</a>";
		}
		$this->app->Tpl->Set('ARTICLELINKS', $link_out);


		// List Rules
		$this->app->Tpl->Set('RULES', $this->ListRules("./index.php?module=linkeditor&action=list&id=$id"));

		$this->app->Tpl->Set('LEUEBERSICHT', $article[0]['uebersicht_de']);
		$this->app->Tpl->Set('LEBESCHREIBUNG', $article[0]['beschreibung_de']);
		$this->app->Tpl->Set('LELINKS', $article[0]['links_de']);
		$this->app->Tpl->Parse(TAB1,"linkeditor_list.tpl");
		$this->app->Tpl->Parse(PAGE,"tabview.tpl");
	}

	function LinkeditorDeleteRule() 
	{
		$id = $this->app->Secure->GetGET('id');
		$ref = base64_decode($this->app->Secure->GetGET('ref'));

		if(is_numeric($id)) {
			$this->app->DB->Delete("DELETE FROM linkeditor WHERE id='$id' LIMIT 1");
		}
		header("Location: $ref");
		exit;
	}
	
	function LinkeditorStatus()
	{
		$id = $this->app->Secure->GetGET('id');
		$status = $this->app->Secure->GetGET('status');

		if(is_numeric($id) && ($status=='1' || $status=='0')) {
			$this->app->DB->Update("UPDATE linkeditor SET active='$status' WHERE id='$id' LIMIT 1");
		}
		exit;
	}
}
?>

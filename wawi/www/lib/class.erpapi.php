<?php

function parse_csv($str)
{
    //match all the non-quoted text and one series of quoted text (or the end of the string)
    //each group of matches will be parsed with the callback, with $matches[1] containing all the non-quoted text,
    //and $matches[3] containing everything inside the quotes
    $str = preg_replace_callback('/([^"]*)("((""|[^"])*)"|$)/s', 'parse_csv_quotes', $str);

    //remove the very last newline to prevent a 0-field array for the last line
    $str = preg_replace('/\n$/', '', $str);

    //split on LF and parse each line with a callback
    return array_map('parse_csv_line', explode("\n", $str));
}

//replace all the csv-special characters inside double quotes with markers using an escape sequence
function parse_csv_quotes($matches)
{
    //anything inside the quotes that might be used to split the string into lines and fields later,
    //needs to be quoted. The only character we can guarantee as safe to use, because it will never appear in the unquoted text, is a CR
    //So we're going to use CR as a marker to make escape sequences for CR, LF, Quotes, and Commas.
    $str = str_replace("\r", "\rR", $matches[3]);
    $str = str_replace("\n", "\rN", $str);
    $str = str_replace('""', "\rQ", $str);
    $str = str_replace(',', "\rC", $str);

    //The unquoted text is where commas and newlines are allowed, and where the splits will happen
    //We're going to remove all CRs from the unquoted text, by normalizing all line endings to just LF
    //This ensures us that the only place CR is used, is as the escape sequences for quoted text
    return preg_replace('/\r\n?/', "\n", $matches[1]) . $str;
}

//split on comma and parse each field with a callback
function parse_csv_line($line)
{
    return array_map('parse_csv_field', explode(',', $line));
}

//restore any csv-special characters that are part of the data
function parse_csv_field($field) {
    $field = str_replace("\rC", ',', $field);
    $field = str_replace("\rQ", '"', $field);
    $field = str_replace("\rN", "\n", $field);
    $field = str_replace("\rR", "\r", $field);
    return $field;
}


function code2utf($num)
{
  if ($num < 128) return chr($num);
  if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num &
        63) + 128);
  if ($num < 65536) return chr(($num >> 12) + 224) .
    chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
  if ($num < 2097152) return chr(($num >> 18) + 240) .
    chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num
          & 63) + 128);
  return '';
}

class erpAPI 
{
  var $commonreadonly=0;
  var $menucounter=0;
  var $mail_error=0;

  function erpAPI(&$app)
  {
    $this->app=$app;
  }

  function StartseiteMenu()
  {
    $this->MenuEintrag("index.php?module=welcome&action=start","Startseite");
    $this->MenuEintrag("index.php?module=welcome&action=pinwand","Pinwand");
    $this->MenuEintrag("index.php?module=aufgaben&action=list","Aufgaben");
    $this->MenuEintrag("index.php?module=kalender&action=list","Kalender");
    $this->MenuEintrag("index.php?module=zeiterfassung&action=create","Zeiterfassung buchen");
    $this->MenuEintrag("index.php?module=zeiterfassung&action=listuser","Eigene Zeiterfassung &Uuml;bersicht");
  }

  function EventAPIAdd($eventname,$parameter,$module,$action,$kommentar="")
  {
    $module_call_from = $this->app->Secure->GetGET("module");
    if($this->Firmendaten("api_enable")=="1" && $module_call_from !="api")
    {
      $this->app->DB->Insert("INSERT INTO event_api (id,cachetime,eventname,parameter,retries,module,action,kommentar)
          VALUES ('',NOW(),'$eventname','$parameter','0','$module','$action','$kommentar')");
      $tmpid =  $this->app->DB->GetInsertID();
      $this->EventCall($tmpid);
    } else {
      return false;
    }       
  }


  function EventCall($id)
  {
    $eventname = $this->app->DB->Select("SELECT eventname FROM event_api WHERE id='$id' LIMIT 1");
    $parameter = $this->app->DB->Select("SELECT parameter FROM event_api WHERE id='$id' LIMIT 1");
    $module = $this->app->DB->Select("SELECT module FROM event_api WHERE id='$id' LIMIT 1");
    $action = $this->app->DB->Select("SELECT action FROM event_api WHERE id='$id' LIMIT 1");

    switch($module)
    {
      case "adresse":
        $xml = $this->XMLAdresse($parameter);
        break;

      case "artikel":
        $xml = $this->XMLArtikel($parameter);
        break;

      case "auftrag":
        $xml = $this->XMLAuftrag($parameter);
        break;

      case "gruppe":
        $xml = $this->XMLGruppe($parameter);
        break;

      case "adresse_kontakt":
        $xml = $this->XMLAdresseKontakt($parameter);
        break;

      default:
        $this->app->DB->Delete("DELETE FROM event_api WHERE id='$id' LIMIT 1");
        return false;
    }       

    $hash = $this->generateHash();  
    //$result = $this->EventSendRequest($eventname,$xml,$hash,"&id=".$parameter);
    //Hack

    $result = $this->EventSendRequest($eventname,$xml,$hash,"&id=".$parameter,$result_body);
    switch($eventname)
    {
      case "EventAuftragEdit":
        //print_r($result_body);
        if($result_body['xml']['belegnr']!="")
        {
          $this->app->DB->Update("UPDATE auftrag SET belegnr='".$result_body['xml']['belegnr']."' WHERE id='".$parameter."' AND id > 0 LIMIT 1");
          $this->app->DB->Delete("DELETE FROM event_api WHERE id='$id' LIMIT 1");
        }
        break;
      default:
        $result = true;
    } 

    if($result===false)
    {
      $this->app->DB->Update("UPDATE event_api SET retries=retries+1 WHERE id='$id' LIMIT 1");
      return false;
    }       else {
      $this->app->DB->Delete("DELETE FROM event_api WHERE id='$id' LIMIT 1");
    }
    return true;

  }

  function EventSendRequest($methodname,$xml,$hash,$parameter="",&$result_body="")
  {

    $xml ='<?xml version="1.0" encoding="UTF-8"?>
      <request>
      <status>
      <function>'.$methodname.'</function>
      </status>
      <xml>'.$xml.'</xml>
      </request>';

    //$url = 'http://dev.eproo.net:8125/wawision/trunk/www/index.php?module=api&action='.$methodname.'&hash='.$hash.$parameter;
    $url = $this->GetPlainText($this->Firmendaten("api_eventurl"));
    if(strpos($url,'?')===false)
      $url = $url."?hash=".$hash.$parameter;
    else
      $url = $url."&hash=".$hash.$parameter;

    $data = array('xml' => $xml);

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data),
          ),
        );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if($result===false) return false;

    $deXml = simplexml_load_string($result);
    $deJson = json_encode($deXml);
    $xml_array = json_decode($deJson,TRUE);

    $result_body = $xml_array;
    $xml_array['xml']['status']['messageCode'] = strtolower($xml_array['xml']['status']['messageCode']);

    if($xml_array['xml']['status']['messageCode']==0)
      return true;
    else return false;      

    return $result;
  }

  function generateHash()
  {
    $initKey = $this->Firmendaten("api_initkey");
    $remoteDomain = $this->Firmendaten("api_remotedomain");
    $date = gmdate('dmY');

    $hash = "";

    for($i = 0; $i <= 200; $i++)
      $hash = sha1($hash . $initKey . $remoteDomain . $date);

    return $hash;
  }


  function XMLGruppe($id)
  {
    $arr = $this->app->DB->SelectArr("SELECT * FROM gruppen WHERE id='$id' LIMIT 1");
    $arr = $arr[0];

    foreach($arr as $key=>$value)
    {
      if(is_array($value))$value="";
      $result .="<".$key.">".$this->GetPlainText($value)."</".$key.">";
    }
    return $result;
  }

  function XMLAdresseKontakt($id)
  {
    $address_arr = $this->app->DB->SelectArr("SELECT * FROM adresse_kontakte WHERE id='$id' LIMIT 1");
    $address_arr = $address_arr[0];

    foreach($address_arr as $key=>$value)
    {
      if(is_array($value))$value="";
      $result .="<".$key.">".$this->GetPlainText($value)."</".$key.">";
    }
    return $result;
  }

  function XMLAdresse($id)
  {
    $address_arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$id' LIMIT 1");
    $address_arr = $address_arr[0];

    foreach($address_arr as $key=>$value)
    {
      if(is_array($value))$value="";
      $result .="<".$key.">".$this->GetPlainText($value)."</".$key.">";
    }
    return $result;
  }

  function XMLAuftrag($id)
  {
    $order_arr = $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE id='$id' LIMIT 1");
    $order_arr = $order_arr[0];

    foreach($order_arr as $key=>$value)
    {
      if(is_array($value))$value="";
      $result .="<".$key.">".$this->GetPlainText($value)."</".$key.">";
    }

    $order_arr = $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$id' AND explodiert_parent<=0");

    $result .="<artikelliste>";
    for($i=0;$i<count($order_arr);$i++)
    {
      $result .="<position>";
      foreach($order_arr[$i] as $key=>$value)
      {
        if(is_array($value))$value="";
        $result .="<".$key.">".$this->GetPlainText($value)."</".$key.">";
      }
      $result .="</position>";
    }
    $result .="</artikelliste>";


    return $result;
  }

  function XMLBenutzer($id)
  {
    $address_arr = $this->app->DB->SelectArr("SELECT * FROM user WHERE id='$id' LIMIT 1");
    $address_arr = $address_arr[0];

    foreach($address_arr as $key=>$value)
    {
      if(is_array($value))$value="";
      if($key=="startseite")
        $result .="<".$key.">".$this->base64_url_encode($value)."</".$key.">";
      else
        $result .="<".$key.">".$this->GetPlainText($value)."</".$key.">";
    }
    return $result;
  }

  function XMLArtikel($id)
  {
    $artikel_arr = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id='$id' LIMIT 1");
    $artikel_arr = $artikel_arr[0];

    foreach($artikel_arr as $key=>$value)
    {
      if(is_array($value))$value="";
      $result .="<".$key.">".$this->GetPlainText($value)."</".$key.">";
    }

    // stueckliste
    $arr_stueckliste = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE stuecklistevonartikel='$id')");
    if(count($arr_stueckliste)>0 && $artikel_arr["stueckliste"]=="1")
    {
      $result .="<stueckliste_artikel>";
      for($i=0;$i<count($arr_stueckliste);$i++)
      {
        $arr_stueckliste[$i]['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$arr_stueckliste[$i]['artikel']."' LIMIT 1");
        $arr_stueckliste[$i]['projekt'] = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='".$arr_stueckliste[$i]['artikel']."' LIMIT 1");
        $arr_stueckliste[$i]['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr_stueckliste[$i]['projekt']."' LIMIT 1");

        $result .="<artikel>";
        $result .="<menge>".$arr_stueckliste[$i]['menge']."</ab_menge>";
        $result .="<nummer>".$arr_stueckliste[$i]['nummer']."</nummer>";
        $result .="<artikel>".$arr_stueckliste[$i]['artikel']."</artikel>";
        $result .="<projekt>".$arr_stueckliste[$i]['projekt']."</projekt>";
        $result .="</artikel>";
      }
      $result .="</stueckliste_artikel>";
    }

    // einkaufspreise
    $arr_einkauf = $this->app->DB->SelectArr("SELECT * FROM einkaufspreise WHERE artikel='$id' AND (gueltig_bis >= NOW() OR gueltig_bis='0000-00-00')");
    if(count($arr_einkauf)>0)
    {
      $result .="<einkaufspreise>";
      for($i=0;$i<count($arr_einkauf);$i++)
      {
        $arr_einkauf[$i]['lieferantennummer'] = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='".$arr_einkauf[$i]['adresse']."' LIMIT 1");
        $arr_einkauf[$i]['projekt'] = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='".$arr_einkauf[$i]['adresse']."' LIMIT 1");
        $arr_einkauf[$i]['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr_einkauf[$i]['projekt']."' LIMIT 1");

        $result .="<staffelpreis>";
        $result .="<ab_menge>".$arr_einkauf[$i]['ab_menge']."</ab_menge>";
        $result .="<preis>".$arr_einkauf[$i]['preis']."</preis>";
        $result .="<waehrung>".$arr_einkauf[$i]['waehrung']."</waehrung>";
        $result .="<lieferantennummer>".$arr_einkauf[$i]['lieferantennummer']."</lieferantennummer>";
        $result .="<projekt>".$arr_einkauf[$i]['projekt']."</projekt>";
        $result .="<bestellnummer>".$this->GetPlainText($arr_einkauf[$i]['bestellnummer'])."</bestellnummer>";
        $result .="<bezeichnunglieferant>".$this->GetPlainText($arr_einkauf[$i]['bezeichnunglieferant'])."</bezeichnunglieferant>";
        $result .="</staffelpreis>";
      }
      $result .="</einkaufspreise>";
    }

    // verkaufspreise
    $arr_verkauf = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise WHERE artikel='$id' AND (gueltig_bis >= NOW() OR gueltig_bis='0000-00-00' ) AND geloescht!='1'");
    if(count($arr_verkauf)>0)
    {
      $result .="<verkaufspreise>";
      for($i=0;$i<count($arr_verkauf);$i++)
      {
        $arr_verkauf[$i]['kundennummer'] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$arr_verkauf[$i]['adresse']."' LIMIT 1");
        $arr_verkauf[$i]['projekt'] = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='".$arr_verkauf[$i]['adresse']."' LIMIT 1");
        $arr_verkauf[$i]['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr_verkauf[$i]['projekt']."' LIMIT 1");

        $result .="<staffelpreis>";
        $result .="<ab_menge>".$arr_verkauf[$i]['ab_menge']."</ab_menge>";
        $result .="<preis>".$arr_verkauf[$i]['preis']."</preis>";
        $result .="<vpe>".$arr_verkauf[$i]['vpe']."</vpe>";
        if($arr_verkauf[$i]['vpe_menge'] > 0)
          $result .="<vpe_menge>".$arr_verkauf[$i]['vpe_menge']."</vpe_menge>";

        $result .="<waehrung>".$arr_verkauf[$i]['waehrung']."</waehrung>";

        if($arr_verkauf[$i]['kundennummer']!="")
        {
          $result .="<kundennummer>".$arr_verkauf[$i]['kundennummer']."</kundennummer>";
          $result .="<projekt>".$arr_verkauf[$i]['projekt']."</projekt>";
          if($arr_verkauf[$i]['kundenartikelnummer']!="")
            $result .="<kundenartikelnummer>".$arr_verkauf[$i]['projekt']."</kundenartikelnummer>";
        }
        $result .="</staffelpreis>";
      }
      $result .="</verkaufspreise>";
    }
    return $result;
  }

  function AbgleichBenutzerVorlagen()
  {
    // alle vorlagen ind ei Leute kopieren
    $user = $this->app->DB->SelectArr("SELECT * FROM user");        
    for($i=0;$i<count($user);$i++)
    {
      $user[$i]['vorlage'] = strtolower($user[$i]['vorlage']); 
      $id_vorlage = $this->app->DB->Select("SELECT id FROM uservorlage WHERE LOWER(bezeichnung)='".$user[$i]['vorlage']."' LIMIT 1");

      $this->app->DB->Update("REPLACE INTO userrights (user, module,action,permission) (SELECT '".$user[$i]['id']."',module, action,permission 
        FROM uservorlagerights WHERE vorlage='".$id_vorlage."')");              
    }       



  }


  function UrlOrigin($s, $use_forwarded_host=false)
  {
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
  }

  function UmlauteEntfernen($text)
  {
    $text = $this->ReadyForPDF($text);
    $text = str_replace ("ä", "ae", $text);
    $text = str_replace ("Ä", "Ae", $text);
    $text = str_replace ("ö", "oe", $text);
    $text = str_replace ("Ö", "Oe", $text);
    $text = str_replace ("ü", "ue", $text);
    $text = str_replace ("Ü", "Ue", $text);
    $text = str_replace ("ß", "ss", $text);
    $text = str_replace ("&", "u", $text);
    return $text;
  }


  function UserDevice()
  {
    if (strstr($_SERVER['HTTP_USER_AGENT'],'iPhone'))
      return "smartphone";
    else return "desktop";
  }

  function Startseite()
  {
    if($this->app->User->GetID()!="")
    { 
      $startseite = $this->app->DB->Select("SELECT startseite FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      $startseite = $this->ReadyForPDF($startseite);

      if($this->UserDevice()=="desktop")
      {
        // wenn die GPS Stechuhr da ist
        if($startseite!="")
          header("Location: $startseite");
        else
          header("Location: index.php?module=welcome&action=start");
        exit;
      } else
      {
        header("Location: index.php?module=welcome&action=start\r\n");
        exit;
      }
    }
  }

  function getFirstDayOfWeek($year, $weeknr)
  {
    $offset = date('w', mktime(0,0,0,1,1,$year));
    $offset = ($offset < 5) ? 1-$offset : 8-$offset;
    $monday = mktime(0,0,0,1,1+$offset,$year);

    return date('Y-m-d',strtotime('+' . ($weeknr - 1) . ' weeks', $monday)); 
  }

  function IsWindows()
  {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
      return true;
    else return false;
  }

  function GetTMP()
  {
    $userdata = $this->app->Conf->WFuserdata;
    if ($this->IsWindows()) {
      $tmp = $userdata."\\tmp\\";
    } else {
      $tmp = $userdata."/tmp/";
    }
    if(!is_dir($tmp))
      mkdir($tmp);

    return $tmp;
  }

  function GetUSERDATA()
  {
    return $this->app->Conf->WFuserdata;
  }


  function LieferscheinAuslagern($lieferschein,$anzeige_lagerplaetze_in_lieferschein=false)
  {
    $artikelarr = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE lieferschein='$lieferschein'");      

    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein'");
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein'");

    for($i=0;$i<count($artikelarr);$i++)
    {
      $beschreibung = $artikelarr[$i]['beschreibung'];
      $artikel = $artikelarr[$i]['artikel'];
      $menge = $artikelarr[$i]['menge'];
      $subid = $artikelarr[$i]['id'];
      $lager_string = "";

      $this->LagerArtikelZusammenfassen($artikel);

      $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='$artikel' LIMIT 1");

      $regal = 0;

      if($lagerartikel > 0)
      {
        // lager platz suchen eins abziehen und namen in lieferschein   
        $regal = $this->app->DB->Select("SELECT lpi.lager_platz FROM lager_platz_inhalt lpi 
            LEFT JOIN lager_platz lp ON lpi.lager_platz=lp.id WHERE lpi.artikel='$artikel' AND lpi.menge >='$menge' 
            AND lp.autolagersperre!='1' LIMIT 1");

        if($regal > 0)
        {
          $regal_name = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$regal' LIMIT 1");
          $this->LagerAuslagernRegal($artikel,$regal,$menge,$projekt,"Lieferschein $belegnr");
          $lager_string .= $regal_name."($menge) ";
        } else {
          $timeout=0;
          $restmenge = $menge;    
          $lager_string = "";
          while(1)
          {       
            $timeout++;
            if($timeout > 1000) break;

            // Hole nach und nach bis alles da ist
            $lager_max = $this->app->DB->SelectArr("SELECT lpi.lager_platz, lpi.menge FROM lager_platz_inhalt lpi 
                LEFT JOIN lager_platz lp ON lpi.lager_platz=lp.id WHERE lpi.artikel='$artikel' AND lpi.menge > 0
                AND lp.autolagersperre!='1' ORDER by lpi.menge DESC LIMIT 1");

            if(($restmenge > $lager_max[0]['menge']) && ($lager_max[0]['menge'] > 0))
            {
              // es werden mehr gebraucht als im lager sind
              $this->LagerAuslagernRegal($artikel,$lager_max[0]['lager_platz'],$lager_max[0]['menge'],$projekt,"Lieferschein $belegnr");
              $regal_name = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='".$lager_max[0]['lager_platz']."' LIMIT 1");
              $lager_string .= $regal_name."(".$lager_max[0]['menge'].") ";
              $restmenge = $restmenge - $lager_max[0]['menge'];
            } else if( ($lager_max[0]['menge'] >= $restmenge) && ($restmenge > 0)  ) {
              // es sind genuegend lager 
              $this->LagerAuslagernRegal($artikel,$lager_max[0]['lager_platz'],$restmenge,$projekt,"Lieferschein $belegnr");
              $regal_name = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='".$lager_max[0]['lager_platz']."' LIMIT 1");
              $lager_string .= $regal_name."(".$restmenge.") ";
              break;
            }
          }
        }               

        if($lager_string=="") $beschreibung .="\r\nLager: manuell";
        else $beschreibung .="\r\nLager: $lager_string";
      }

      $geliefert = $menge;

      if($anzeige_lagerplaetze_in_lieferschein)
        $this->app->DB->Update("UPDATE lieferschein_position SET geliefert='$geliefert',beschreibung='$beschreibung' WHERE id='$subid' LIMIT 1");
      else
        $this->app->DB->Update("UPDATE lieferschein_position SET geliefert='$geliefert' WHERE id='$subid' LIMIT 1");
    }       
  }       

  function base64_url_encode($input) {
    return strtr(base64_encode($input), '+/=', '-_,');
  }

  function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_,', '+/='));
  }

  function ClearCookies()
  {
    if(count($_COOKIE) > 0)
    {
      foreach($_COOKIE as $key=>$value)
      {
        if($key!=str_replace("SpryMedia","",$key))
          setcookie ($key, time() - 3600);
      }
    }
  }

  function ManuelEcho($text)
  {
    echo $this->ClearDataBeforeOutput($text);
    exit;
  }

  function ClearDataBeforeOutput($text)
  {
    $text = str_replace('form action=""','form action="#"',$text);
    $text = str_replace('NONBLOCKINGZERO','',$text);
    return $text;
  }

  function AdresseAnschriftString($adresse)
  {
    $tmp="";
    if($adresse > 0)
    {
      $result = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");

      if($result[0]['name']!="") $tmp .= $result[0]['name']."\\n";
      if($result[0]['ansprechpartner']!="") $tmp .= $result[0]['ansprechpartner']."\\n";
      if($result[0]['abteilung']!="") $tmp .= $result[0]['abteilung']."\\n";
      if($result[0]['unterabteilung']!="") $tmp .= $result[0]['unterabteilung']."\\n";
      if($result[0]['adresszusatz']!="") $tmp .= $result[0]['adresszusatz']."\\n";
      if($result[0]['strasse']!="") $tmp .= $result[0]['strasse']."\\n";
      $tmp .= $result[0]['land']."-".$result[0]['plz']." ".$result[0]['ort'];
    }
    return $this->ReadyForPDF($tmp);
  }

  function AnzeigeEinkaufLager($artikel)
  {

    $einkauf = $this->GetEinkaufspreis($artikel,1);
    if($einkauf <=0) $einkauf = "-"; else $einkauf = $einkauf." &euro;";

    $lager = $this->ArtikelAnzahlLager($artikel);
    $reserviert = $this->ArtikelAnzahlReserviert($artikel);
    $verfuegbar = $lager - $reserviert;

    return "<table>
      <tr><td>Einkaufspreis:</td><td>$einkauf </td></tr>
      <tr><td>Lagerbestand:</td><td>$lager</td></tr>
      <tr><td>Reserviert:</td><td>$reserviert</td></tr>
      <tr><td>Verfügbar:</td><td>$verfuegbar</td></tr>
      </table>
      ";      
  }

  function DokumentAbschickenPopup()
  {
    $module = $this->app->Secure->GetGET("module");
    return "var horizontalPadding = 30;
    var verticalPadding = 30; $('<iframe id=\"externalSite\" class=\"externalSite\" src=\"index.php?module=$module&action=abschicken&id=%value%\" width=\"1000\"/>').dialog({
title: 'Abschicken',
      autoOpen: true,
      width:1000,
      height: 700,
      modal: true,
      resizable: true,
      close: function(ev, ui) {window.location.href='index.php?module=$module&action=edit&id=%value%';}
  }).width(1000 - horizontalPadding).height(700 - verticalPadding);";
  }

  function calledOnceAfterLogin($type)
  {
    $check = $this->app->DB->Select("SELECT settings FROM user WHERE id='1'");

    if($check=="firstinstall")
    {
      $this->UpgradeDatabase();
      $this->app->DB->Update("UPDARE user SET settings='' WHERE id='1'");
    }
    /*
    // artikel zusammenfassen
    $artikel = $this->app->DB->SelectArr("SELECT id FROM artikel WHERE lagerartikel='1'");
    for($i=0;$i<count($artikel);$i++)
    {
    $this->LagerArtikelZusammenfassen($artikel[$i]['id']);
    }
     */
    $this->app->User->SetParameter("lohnabrechnung_von","");
    $this->app->User->SetParameter("lohnabrechnung_bis","");

    $this->CheckGPSStechuhr();

  }

  function CheckGPSStechuhr()
  {
    $module=$this->app->Secure->GetGET("module");

    if($this->app->DB->Select("SELECT gpsstechuhr FROM user WHERE id='".$this->app->User->GetID()."'")>0)
    {
      $check = $this->app->DB->Select("SELECT id FROM gpsstechuhr 
          WHERE user='".$this->app->User->GetID()."' AND adresse='".$this->app->User->GetAdresse()."' 
          AND DATE_FORMAT(zeit,'%Y-%m-%d')= DATE_FORMAT( NOW( ) , '%Y-%m-%d' ) LIMIT 1");

      if($this->ModulVorhanden("gpsstechuhr") && $check <= 0 && $module!="gpsstechuhr" )
      {
        header("Location: index.php?module=gpsstechuhr&action=create\r\n");
        exit;
      }
    }
  }       

  function ParseFormVars($fields)
  {
    foreach($fields as $key)
    {
      $this->app->Tpl->Set(strtoupper($key),$this->app->Secure->GetPOST($key));
    }
  }

  function ParseUserVars($type,$id,$text)
  {

    $result = $this->app->DB->SelectArr("SELECT * FROM $type WHERE id='$id' LIMIT 1");

    if($type!="auftrag" && $type!="bestellung")
    {
      $result[0]['internet'] = $this->app->DB->Select("SELECT internet FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['abweichendelieferadresse']=$this->app->DB->Select("SELECT abweichendelieferadresse FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['liefername']=$this->app->DB->Select("SELECT liefername FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferabteilung']=$this->app->DB->Select("SELECT lieferabteilung FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferunterabteilung']=$this->app->DB->Select("SELECT lieferunterabteilung FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferadresszusatz']=$this->app->DB->Select("SELECT lieferadresszusatz FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferansprechpartner']=$this->app->DB->Select("SELECT lieferansprechpartner FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferstrasse']=$this->app->DB->Select("SELECT lieferstrasse FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferplz']=$this->app->DB->Select("SELECT lieferplz FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferland']=$this->app->DB->Select("SELECT lieferland FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
      $result[0]['lieferort'] = $this->app->DB->Select("SELECT lieferort FROM auftrag WHERE id='".$result[0]['auftragid']."' LIMIT 1");
    }

    if ($type=="lieferschein")
    {
      $rechnungsid = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein='$id' LIMIT 1");
      if($rechnungsid > 0)
      {
        $resultrechnung = $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE id='$rechnungsid' LIMIT 1");
        if($resultrechnung[0]['name']!="")
          $rechnungsadresse .= $resultrechnung[0]['name']."\r\n"; 
        if($resultrechnung[0]['abteilung']!="")
          $rechnungsadresse .= $resultrechnung[0]['abteilung']."\r\n";    
        if($resultrechnung[0]['unterabteilung']!="")
          $rechnungsadresse .= $resultrechnung[0]['unterabteilung']."\r\n";       
        if($resultrechnung[0]['strasse']!="")
          $rechnungsadresse .= $resultrechnung[0]['strasse']."\r\n";      
        if($resultrechnung[0]['adresszusatz']!="")
          $rechnungsadresse .= $resultrechnung[0]['adresszusatz']."\r\n"; 
        if($resultrechnung[0]['ansprechpartner']!="")
          $rechnungsadresse .= $resultrechnung[0]['ansprechpartner']."\r\n";      
        if($resultrechnung[0]['plz']!="")
          $rechnungsadresse .= $resultrechnung[0]['land']."-".$resultrechnung[0]['plz']." ".$resultrechnung[0]['ort']."\r\n";     
      }                       

      if($rechnungsid <=0)
        $text = str_replace('{RECHNUNGSADRESSE}',"",$text);     
      else                    
        $text = str_replace('{RECHNUNGSADRESSE}',"Rechnungsadresse: \r\n".$rechnungsadresse,$text);     
    }


    foreach($result[0] as $key=>$value)
      $result[0][$key]=str_replace('NONBLOCKINGZERO','',$result[0][$key]);

    $result[0]['anschreiben'] = $this->app->DB->Select("SELECT anschreiben FROM `$type` WHERE id='".$id."' LIMIT 1");

    $result[0]['kundennummer'] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$result[0]['adresse']."' LIMIT 1");
    $result[0]['verbandsnummer'] = $this->app->DB->Select("SELECT verbandsnummer FROM adresse WHERE id='".$result[0]['adresse']."' LIMIT 1");
    $result[0]['verband'] = $this->GetVerbandName($this->GetVerband($result[0]['adresse']));

    $tmp = $this->app->DB->Select("SELECT freifeld1 FROM adresse WHERE id='".$result[0]['adresse']."' LIMIT 1");
    $text = str_replace('{FREIFELD1}',$tmp,$text);

    $tmp = $this->app->DB->Select("SELECT freifeld2 FROM adresse WHERE id='".$result[0]['adresse']."' LIMIT 1");
    $text = str_replace('{FREIFELD2}',$tmp,$text);

    $tmp = $this->app->DB->Select("SELECT freifeld3 FROM adresse WHERE id='".$result[0]['adresse']."' LIMIT 1");
    $text = str_replace('{FREIFELD3}',$tmp,$text);
    /*
       $text = str_replace('{INTERNET}',$result[0]['internet'],$text);
       $text = str_replace('{IHREBESTELLNUMMER}',$result[0]['ihrebestellnummer'],$text);
       $text = str_replace('{BEARBEITER}',$result[0]['bearbeiter'],$text);
       $text = str_replace('{VERSANDART}',$result[0]['versandart'],$text);
     */

    if($result[0]['anschreiben']=="")
      $result[0]['anschreiben'] = $this->app->DB->Select("SELECT anschreiben FROM adresse WHERE id='".$result[0]['adresse']."' LIMIT 1");

    if($result[0]['anschreiben']!="")       
      $text = str_replace('{ANSCHREIBEN}',$result[0]['anschreiben'],$text);   
    else
      $text = str_replace('{ANSCHREIBEN}',"Sehr geehrte Damen und Herren",$text);     

    if($result[0]['belegnr']!="")   
      $text = str_replace('{BELEGNR}',$result[0]['belegnr'],$text);   
    else
      $text = str_replace('{BELEGNR}',"",$text);      

    if($result[0]['belegnr']!="")   
      $text = str_replace('{BELEGNUMMER}',$result[0]['belegnr'],$text);       
    else
      $text = str_replace('{BELEGNUMMER}',"",$text);  

    if($result[0]['kundennummer']!="")      
      $text = str_replace('{KUNDENNUMMER}',$result[0]['kundennummer'],$text); 
    else
      $text = str_replace('{KUNDENNUMMER}',"",$text); 

    if($result[0]['verbandsnummer']!="")
      $text = str_replace('{VERBANDSNUMMER}',$result[0]['verbandsnummer'],$text);
    else
      $text = str_replace('{VERBANDSNUMMER}',"Keine Nummer",$text);

    if($result[0]['verband']!="")
      $text = str_replace('{VERBAND}',$result[0]['verband'],$text);
    else
      $text = str_replace('{VERBAND}',"Kein Verband",$text);


    if($result[0]['lieferdatum']!="0000-00-00" && $result[0]['lieferdatum']!="")
    {
      $ddate = $result[0]['lieferdatum'];
      $result[0]['lieferdatum'] = $this->app->String->Convert($result[0]['lieferdatum'],"%1-%2-%3","%3.%2.%1");
      $duedt = explode("-", $ddate);
      $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
      $week  = date('W/Y', $date);
      $text = str_replace('{LIEFERTERMIN}',$result[0]['lieferdatum'],$text);  
      $text = str_replace('{LIEFERWOCHE}',$week,$text);       
    }
    else
    {
      $text = str_replace('{LIEFERTERMIN}',"sofort",$text);   
      $text = str_replace('{LIEFERWOCHE}',"sofort",$text);
    }

    if($result[0]['gueltigbis']!="0000-00-00" && $result[0]['gueltigbis']!="")
    {
      $ddate = $result[0]['gueltigbis'];
      $result[0]['gueltigbis'] = $this->app->String->Convert($result[0]['gueltigbis'],"%1-%2-%3","%3.%2.%1");
      $duedt = explode("-", $ddate);
      $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
      $week  = date('W/Y', $date);
      $text = str_replace('{GUELTIGBIS}',$result[0]['gueltigbis'],$text);     
      $text = str_replace('{GUELTIGBISWOCHE}',$week,$text);   
    }
    else
    {
      $text = str_replace('{GUELTIGBIS}',"",$text);   
      $text = str_replace('{GUELTIGBISWOCHE}',"",$text);
    }


    if($result[0]['abweichendelieferadresse']=="1")
    {
      $liefertext ="";

      if($result[0]['liefername']!="")
        $liefertext .= $result[0]['liefername']."\r\n";
      if($result[0]['lieferabteilung']!="")
        $liefertext .= $result[0]['lieferabteilung']."\r\n";
      if($result[0]['lieferunterabteilung']!="")
        $liefertext .= $result[0]['lieferunterabteilung']."\r\n";
      if(trim($result[0]['lieferadresszusatz'])!="")
        $liefertext .= $result[0]['lieferadresszusatz']."\r\n";

      if(trim($result[0]['lieferansprechpartner']) !="")
        $liefertext .= $result[0]['lieferansprechpartner']."\r\n";  

      if($result[0]['lieferstrasse']!="")
        $liefertext .= $result[0]['lieferstrasse']."\r\n";  
      if($result[0]['lieferplz']!="")
        $liefertext .= $result[0]['lieferland']."-".$result[0]['lieferplz']." ".$result[0]['lieferort']."\r\n"; 

      if($liefertext=="")
        $text = str_replace('{LIEFERADRESSE}',"entspricht Rechnungsadresse",$text); 
      else
        $text = str_replace('{LIEFERADRESSE}',$liefertext,$text); 

      $text = str_replace('{LIEFERNAME}',$result[0]['liefername'],$text); 
      $text = str_replace('{LIEFERABTEILUNG}',$result[0]['lieferabteilung'],$text); 
      $text = str_replace('{LIEFERUNTERABTEILUNG}',$result[0]['lieferunterabteilung'],$text); 
      $text = str_replace('{LIEFERLAND}',$result[0]['lieferland'],$text); 
      $text = str_replace('{LIEFERSTRASSE}',$result[0]['lieferstrasse'],$text); 
      $text = str_replace('{LIEFERORT}',$result[0]['lieferort'],$text); 
      $text = str_replace('{LIEFERPLZ}',$result[0]['lieferplz'],$text); 
      $text = str_replace('{LIEFERADRESSZUSATZ}',$result[0]['lieferadresszusatz'],$text); 
      $text = str_replace('{LIEFERANSPRECHPARTNER}',$result[0]['lieferansprechpartner'],$text); 
    } else {
      if($result[0]['name']!="")
        $liefertext .= $result[0]['name']."\r\n";       
      if($result[0]['abteilung']!="")
        $liefertext .= $result[0]['abteilung']."\r\n";  
      if($result[0]['unterabteilung']!="")
        $liefertext .= $result[0]['unterabteilung']."\r\n";     
      if($result[0]['strasse']!="")
        $liefertext .= $result[0]['strasse']."\r\n";    
      if($result[0]['adresszusatz']!="")
        $liefertext .= $result[0]['adresszusatz']."\r\n";       
      if($result[0]['ansprechpartner']!="")
        $liefertext .= $result[0]['ansprechpartner']."\r\n";    
      if($result[0]['plz']!="")
        $liefertext .= $result[0]['land']."-".$result[0]['plz']." ".$result[0]['ort']."\r\n";   


      if($type=="bestellung")
        $text = str_replace('{LIEFERADRESSE}',"entspricht Rechnungsadresse",$text);     
      else                    
        $text = str_replace('{LIEFERADRESSE}',$liefertext,$text);       


      $text = str_replace('{LIEFERNAME}',$result[0]['name'],$text);   
      $text = str_replace('{LIEFERABTEILUNG}',$result[0]['abteilung'],$text); 
      $text = str_replace('{LIEFERUNTERABTEILUNG}',$result[0]['unterabteilung'],$text);       
      $text = str_replace('{LIEFERLAND}',$result[0]['land'],$text);   
      $text = str_replace('{LIEFERSTRASSE}',$result[0]['strasse'],$text);     
      $text = str_replace('{LIEFERORT}',$result[0]['ort'],$text);     
      $text = str_replace('{LIEFERPLZ}',$result[0]['plz'],$text);     
      $text = str_replace('{LIEFERADRESSZUSATZ}',$result[0]['adresszusatz'],$text);   
      $text = str_replace('{LIEFERANSPRECHPARTNER}',$result[0]['ansprechpartner'],$text);     
    }       

    $result[0]['datum'] = $this->app->String->Convert($result[0]['datum'],"%1-%2-%3","%3.%2.%1");

    foreach($result[0] as $key_i=>$value_i)
      $text = str_replace('{'.strtoupper($key_i).'}',$result[0][$key_i],$text);



    return $text;
  }

  function CheckBearbeiter($id,$module)
  {
    $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM $module WHERE id='$id' LIMIT 1");
    if($bearbeiter=="" || $bearbeiter==0)
    {
      // pruefe ob es innendienst verantwortlichen gib
      $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
      $innendienst = $this->app->DB->Select("SELECT innendienst FROM adresse WHERE id='$adresse' LIMIT 1");
      $innendienst_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$innendienst' LIMIT 1");

      if($innendienst_name!="")
        $this->app->DB->Update("UPDATE $module SET bearbeiter='".$innendienst_name."' WHERE id='$id' LIMIT 1");
      else
      {
        if($this->app->DB->Select("SELECT bearbeiter FROM $module WHERE id='$id' LIMIT 1")=="")
          $this->app->DB->Update("UPDATE $module SET bearbeiter='".$this->app->User->GetName()."' WHERE id='$id' LIMIT 1");
      }
    }
    else if (is_numeric($bearbeiter))
    {
      $bearbeiter = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$bearbeiter."' LIMIT 1");
      $this->app->DB->Update("UPDATE $module SET bearbeiter='".$bearbeiter."' WHERE id='$id' LIMIT 1");
    }
  }


  function EnableTab($tab)
  {
    $this->app->Tpl->Add(ADDITIONALJAVASCRIPT,"<script type=\"text/javascript\">
        $(document).ready(function() { 
          $('a[href=\"#$tab\"]').click(); 
          });</script>");
  }


  function CheckVertrieb($id,$module)
  {

    //$vertrieb = $this->app->DB->Select("SELECT vertrieb FROM $module WHERE id='$id' LIMIT 1");
    $vertrieb = $this->app->DB->Select("SELECT vertriebid FROM $module WHERE id='$id' LIMIT 1");
    if($vertrieb<=0 || $vertrieb=="")
    {
      // pruefe ob es innendienst verantwortlichen gib
      $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
      $vertrieb = $this->app->DB->Select("SELECT vertrieb FROM adresse WHERE id='$adresse' LIMIT 1");
      $vertrieb_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$vertrieb' LIMIT 1");

      if($vertrieb_name!="" && $vertrieb_name!="0")
      {
        $this->app->DB->Update("UPDATE $module SET vertriebid='$vertrieb',vertrieb='".$vertrieb_name."' WHERE id='$id' LIMIT 1");
      }
      else
      {
        $checktmp = $this->app->DB->Select("SELECT vertrieb FROM $module WHERE id='$id' LIMIT 1");
        if($checktmp=="" || $checktmp==0)
        {
          $this->app->DB->Update("UPDATE $module SET vertrieb='".$this->app->User->GetName()."' WHERE id='$id' LIMIT 1");
        }
      }
    }

    else if (is_numeric($vertrieb))
    {
      $vertrieb_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$vertrieb."' LIMIT 1");
      //                      if($vertrieb==0) $vertrieb=$this->app->User->GetName();
      $this->app->DB->Update("UPDATE $module SET vertrieb='".$vertrieb_name."' WHERE id='$id' LIMIT 1");
    }

  }


  function CheckBuchhaltung($id,$module)
  {
    $buchhaltung = $this->app->DB->Select("SELECT buchhaltung FROM $module WHERE id='$id' LIMIT 1");
    if($buchhaltung=="")
      $this->app->DB->Update("UPDATE $module SET buchhaltung='".$this->app->User->GetName()."' WHERE id='$id' LIMIT 1");
    else if (is_numeric($buchhaltung))
    {
      $buchhaltung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$buchhaltung."' LIMIT 1");
      $this->app->DB->Update("UPDATE $module SET bearbeiter='".$buchhaltung."' WHERE id='$id' LIMIT 1");
    }
  }


  function ProduktionenNeuberechnen()
  {
    // auftraege berechnen
    $auftraege = $this->app->DB->SelectArr("SELECT * FROM produktion WHERE status='freigegeben' AND inbearbeitung=0 ORDER By datum");   

    for($i=0;$i<count($auftraege); $i++)
    {
      $this->ProduktionNeuberechnen($auftraege[$i][id]);
      $this->ProduktionEinzelnBerechnen($auftraege[$i][id]);
    }
  }

  function MessageHandlerStandardForm()
  {
    $module = $this->app->Secure->GetGET("module");
    $id = $this->app->Secure->GetGET("id");

    if($this->app->Secure->GetPOST("speichern")!="")
    {
      if($this->app->Secure->GetGET("msg")=="")
      {
        $msg = $this->app->Secure->GetGET("msg");
        $msg = $msg.$this->app->Tpl->Get(MESSAGE);
        $msg = base64_encode($msg);
      } else {
        $msg = $this->app->Secure->GetGET("msg");
      }

      header("Location: index.php?module=$module&action=edit&id=$id&msg=$msg");
      exit;
    }


  }


  function superentities( $str ){
    // get rid of existing entities else double-escape

    $str = html_entity_decode(stripslashes($str),ENT_QUOTES| ENT_HTML5,'UTF-8');
    //              $str = str_replace("'","&apos;",$str);
    //                              return $str; 
    $ar = preg_split('/(?<!^)(?!$)/u', $str );  // return array of every multi-byte character
    foreach ($ar as $c){
      $o = ord($c);
      if ( (strlen($c) > 1) || /* multi-byte [unicode] */
          ($o <32 || $o > 126) || /* <- control / latin weirdos -> */
          ($o >33 && $o < 35) ||/* quotes + ambersand */
          ($o >35 && $o < 40) ||/* quotes + ambersand */
          ($o >59 && $o < 63) /* html */
         ) {
        // convert to numeric entity
        //$c = @mb_encode_numericentity($c,array (0x0, 0xffff, 0, 0xffff), 'UTF-8');
        $c = $this->convertToHtml($c);
      }
      $str2 .= $c;
    }
    return $str2;
  }

  function convertToHtml($str) {
    //                  $str = utf8_decode($str);
    //              $trans_tbl = get_html_translation_table (HTML_ENTITIES,ENT_HTML5);
    if (version_compare(PHP_VERSION, '5.3.4') >= 0) {
      $trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'UTF-8'));
    } else {
      $trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_COMPAT));
      if (!empty($trans_tbl)) {
        foreach ($trans_tbl as $key => $entry) {
          $trans_tbl[$key] = utf8_encode($entry);
        } 
      }
    }

    // MS Word strangeness..
    // smart single/ double quotes:
    $trans_tbl[chr(39)] = '&apos;';
    $trans_tbl[chr(145)] = '\'';
    $trans_tbl[chr(146)] = '\'';
    $trans_tbl[chr(147)] = '&quot;';
    $trans_tbl[chr(148)] = '&quot;';
    $trans_tbl[chr(142)] = '&eacute;';
    //&#65279;
    //$trans_tbl[$this->unicode_chr(65279)] = "BENE";
    //$str = str_replace("\xFF\xFE", "BENE", $str);


    return strtr ($str, $trans_tbl); 
  } 



  function InitialSetup()
  {
    //pruefe ob es bereits daten gibt
    //$this->app->DB->Select("LOCK TABLES adresse WRITE;");
    if($this->app->DB->Select("SELECT COUNT(id) FROM adresse WHERE geloescht!=1")<=0)
    {
      $mitarbeiternummer = $this->GetNextMitarbeiternummer();

      $sql = 'INSERT INTO `adresse` (`id`, `typ`, `marketingsperre`, `trackingsperre`, `rechnungsadresse`, `sprache`, `name`, `abteilung`, `unterabteilung`, `ansprechpartner`, `land`, `strasse`, `ort`, `plz`, `telefon`, `telefax`, `mobil`, `email`, `ustid`, `ust_befreit`, `passwort_gesendet`, `sonstiges`, `adresszusatz`, `kundenfreigabe`, `steuer`, `logdatei`, `kundennummer`, `lieferantennummer`, `mitarbeiternummer`, `konto`, `blz`, `bank`, `inhaber`, `swift`, `iban`, `waehrung`, `paypal`, `paypalinhaber`, `paypalwaehrung`, `projekt`, `partner`, `geloescht`, `firma`) VALUES (NULL, \'\', \'\', \'\', \'\', \'\', \'Administrator\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW(), \'\', \'\', \''.$mitarbeiternummer.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'1\', \'\', \'\', \'1\');';
      $this->app->DB->InsertWithoutLog($sql);
      $adresse = $this->app->DB->GetInsertID();

      $sql = "INSERT INTO `adresse_rolle` (`id`, `adresse`, `projekt`, `subjekt`, `praedikat`, `objekt`, `parameter`, `von`, `bis`) VALUES
        ('', '$adresse', 0, 'Mitarbeiter', '', '', '', NOW(), '0000-00-00');";
      $this->app->DB->InsertWithoutLog($sql);

      $sql = 'INSERT INTO `firma` (`id`, `name`, `standardprojekt`) VALUES (NULL, \'Musterfirma\', \'1\');';
      $this->app->DB->InsertWithoutLog($sql);

      $sql = 'INSERT INTO `user` (`id`, `username`, `password`, `repassword`, `description`, `settings`, `parentuser`, `activ`, `type`, `adresse`, `standarddrucker`, `firma`, `logdatei`,`externlogin`) VALUES (NULL, \'admin\', ENCRYPT(\'admin\'), \'\', NULL, \'\', NULL, \'1\', \'admin\', \''.$adresse.'\', \'\', \'1\', NOW(),\'1\');';
      $this->app->DB->InsertWithoutLog($sql);


      $sql = 'INSERT INTO `projekt` (`id`, `name`, `abkuerzung`, `verantwortlicher`, `beschreibung`, `sonstiges`, `aktiv`, `farbe`, `autoversand`, `checkok`, `checkname`, `zahlungserinnerung`, `zahlungsmailbedinungen`, `folgebestaetigung`, `kundenfreigabe_loeschen`, `autobestellung`, `firma`, `logdatei`) VALUES (NULL, \'Hauptprojekt\', \'HAUPTPROJEKT\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'1\', \'\');';
      $this->app->DB->InsertWithoutLog($sql);
    }
    //$this->app->DB->Select("UNLOCK TABLES;");
  }

  function ValidLager($lager)
  {
    $result = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$lager' LIMIT 1");
    if($result > 0)
      return 1;
    else return 0;

  }

  function ValidArtikelnummer($artikel)
  {
    if($artikel<=0 || $artikel=="")
      return 0;

    $result = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikel' LIMIT 1");
    if($result > 0)
      return 1;

    $result = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel' LIMIT 1");
    if($result > 0)
      return 1;

    $result = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$artikel' LIMIT 1");
    if($result > 0)
      return 1;

    return 0;
  }


  function ProjektleiterRechte()
  {

    // alle projekte Wo Mitarbeiter ist
    $result = $this->app->DB->SelectArr("SELECT parameter FROM adresse_rolle WHERE subject='Projektleiter' AND (bis='0000-00-00' OR bis < NOW()) AND adresse='".$this->app->User->GetAdresse()."'");
    //if($sql!="" && count($result)>0) $sql .= " OR ";

    for($i=0;$i<count($result);$i++)
    {
      $sql .= "p.id='".$result[$i][parameter]."'";
      if($i < count($result) - 1)
        $sql .= " OR ";
    }

    if($sql!="")    
      return " AND ($sql) ";
    else
      return "";
  }



  function ProjektRechte($prefix="p.id")
  {

    // alle oeffentlichen projekte
    $result = $this->app->DB->SelectArr("SELECT id FROM projekt WHERE oeffentlich='1'");
    for($i=0;$i<count($result);$i++)
    {
      $sql .= $prefix."='".$result[$i][id]."'";
      if($i < count($result) - 1)
        $sql .= " OR ";
    }

    // alle projekte Wo Mitarbeiter ist
    $result = $this->app->DB->SelectArr("SELECT parameter FROM adresse_rolle WHERE (bis='0000-00-00' OR bis < NOW()) AND adresse='".$this->app->User->GetAdresse()."'");
    if($sql!="" && count($result)>0) $sql .= " OR ";

    for($i=0;$i<count($result);$i++)
    {
      $sql .= $prefix."='".$result[$i][parameter]."'";
      if($i < count($result) - 1)
        $sql .= " OR ";
    }

    // wenn mitarbeiter projektleiter für alles     dann darf man alles sehen
    $resultalle = $this->app->DB->Select("SELECT COUNT(parameter) FROM adresse_rolle WHERE (bis='0000-00-00' OR bis < NOW()) AND adresse='".$this->app->User->GetAdresse()."' AND (parameter='' OR parameter='0')");


    //if($sql=="") return "";

    if($this->app->User->GetType()=="admin" || $resultalle > 0) 
      return "";
    else
      return " AND ($sql) ";
  }


  function StandardVersandart($projekt="")
  {
    $tmp = $this->Firmendaten("versandart");
    return $tmp;
  }

  function StandardZahlungsweise($projekt="")
  {
    $tmp = $this->Firmendaten("zahlungsweise");
    if($tmp=="") $tmp="rechnung";
    return $tmp;
  }

  function ZahlungsZielSkonto($projekt="")
  {
    $tmp = $this->Firmendaten("zahlungszielskonto");
    if($tmp <= 0)
      return 0;
    else return $tmp;
  }


  function ZahlungsZielTageSkonto($projekt="")
  {
    $tmp = $this->Firmendaten("zahlungszieltageskonto");
    if($tmp <= 0)
      return 0;
    else return $tmp;
  }

  function ZahlungsZielTage($projekt="")
  {
    $tmp = $this->Firmendaten("zahlungszieltage");
    if($tmp <=0)
      return 0;
    else return $tmp;
  }

  function ModulVorhanden($module)
  {
    if(is_file("pages/".$module.".php"))
    {
      return true;
    } 
    else return false;
  }


  function RechteVorhanden($module,$action)
  {
    if($this->app->User->GetType()=="admin") { 
      // wenn das Modul exisitiert
      if(is_file("pages/".$module.".php"))
      {
        return true;
      } 
    }

    if(is_file("pages/".$module.".php"))
      $result = $this->app->DB->Select("SELECT id FROM userrights WHERE module='$module' AND action='$action' AND permission='1' AND user='".$this->app->User->GetID()."' LIMIT 1");

    if($result > 0)
      return true;
    else
      return false;
  }


  function fixeUmlaute($text) {                  
    $umlaute = $this->getUmlauteArray();                  
    foreach ($umlaute as $key => $value){  
      $text = str_replace($key,$value,$text);
    } 
    return $text;
  }


  function getUmlauteArray() { return array( 'Ã¼'=>'ü', 'Ã¤'=>'ä', 'Ã¶'=>'ö', 'Ã–'=>'Ö', 'Ã?'=>'ß','ÃŸ'=>'ß', 'Ã '=>'à', 'Ã¡'=>'á', 'Ã¢'=>'â', 'Ã£'=>'ã', 'Ã¹'=>'ù', 'Ãº'=>'ú', 'Ã»'=>'û', 'Ã™'=>'Ù', 'Ãš'=>'Ú', 'Ã›'=>'Û', 'Ãœ'=>'Ü', 'Ã²'=>'ò', 'Ã³'=>'ó', 'Ã´'=>'ô', 'Ã¨'=>'è', 'Ã©'=>'é', 'Ãª'=>'ê', 'Ã«'=>'ë', 'Ã€'=>'À', 'Ã<81>'=>'Á', 'Ã‚'=>'Â', 'Ãƒ'=>'Ã', 'Ã„'=>'Ä', 'Ã…'=>'Å', 'Ã‡'=>'Ç', 'Ãˆ'=>'È', 'Ã‰'=>'É', 'ÃŠ'=>'Ê', 'Ã‹'=>'Ë', 'ÃŒ'=>'Ì', 'Ã<8d>'=>'Í', 'ÃŽ'=>'Î', 'Ã<8f>'=>'Ï', 'Ã‘'=>'Ñ', 'Ã’'=>'Ò', 'Ã“'=>'Ó', 'Ã”'=>'Ô', 'Ã•'=>'Õ', 'Ã˜'=>'Ø', 'Ã¥'=>'å', 'Ã¦'=>'æ', 'Ã§'=>'ç', 'Ã¬'=>'ì', 'Ã­'=>'í', 'Ã®'=>'î', 'Ã¯'=>'ï', 'Ã°'=>'ð', 'Ã±'=>'ñ', 'Ãµ'=>'õ', 'Ã¸'=>'ø', 'Ã½'=>'ý', 'Ã¿'=>'ÿ', 'â‚¬'=>'€' );
  }


  function ConvertForDBUTF8($string)
  {
    //$string = $this->unicode_decode($string);
    return htmlentities($string,ENT_QUOTES);
    //return htmlentities(utf8_encode($string),ENT_QUOTES);
    //              return html_entity_decode($string, ENT_QUOTES, 'UTF-8'); //uahlungseingang
  }

  function ConvertForDB($string)
  {
    return htmlentities(utf8_decode($string),ENT_QUOTES);
    //return htmlentities(utf8_encode($string),ENT_QUOTES);
    //              return html_entity_decode($string, ENT_QUOTES, 'UTF-8'); //uahlungseingang
  }


  function ConvertForTableSearch($string)
  {
    $string = $this->unicode_decode($string);
    $cmd = $this->app->Secure->GetGET("cmd");
    if($cmd=="kontoauszuege")       
      return trim(html_entity_decode($string, ENT_QUOTES, 'UTF-8')); //uahlungseingang
    else
      return ($string);
  }

  function make_clickable($text)
  {
    return preg_replace('@(?<![.*">])\b(?:(?:https?|ftp|file)://|[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i', '<a href="\0">\0</a>', $text);
  }

  function unicode_decode($content) {
    $ISO10646XHTMLTrans = array(
        "&"."#34;" => "&quot;",
        "&"."#38;" => "&amp;",
        "&"."#39;" => "&apos;",
        "&"."#60;" => "&lt;",
        "&"."#62;" => "&gt;",
        "&"."#128;" => "&euro;",
        "&"."#160;" => "",
        "&"."#161;" => "&iexcl;",
        "&"."#162;" => "&cent;",
        "&"."#163;" => "&pound;",
        "&"."#164;" => "&curren;",
        "&"."#165;" => "&yen;",
        "&"."#166;" => "&brvbar;",
        "&"."#167;" => "&sect;",
        "&"."#168;" => "&uml;",
        "&"."#169;" => "&copy;",
        "&"."#170;" => "&ordf;",
        "&"."#171;" => "&laquo;",
        "&"."#172;" => "&not;",
        "&"."#173;" => "­",
        "&"."#174;" => "&reg;",
        "&"."#175;" => "&macr;",
        "&"."#176;" => "&deg;",
        "&"."#177;" => "&plusmn;",
        "&"."#178;" => "&sup2;",
        "&"."#179;" => "&sup3;",
        "&"."#180;" => "&acute;",
        "&"."#181;" => "&micro;",
        "&"."#182;" => "&para;",
        "&"."#183;" => "&middot;",
        "&"."#184;" => "&cedil;",
        "&"."#185;" => "&sup1;",
        "&"."#186;" => "&ordm;",
        "&"."#187;" => "&raquo;",
        "&"."#188;" => "&frac14;",
        "&"."#189;" => "&frac12;",
        "&"."#190;" => "&frac34;",
        "&"."#191;" => "&iquest;",
        "&"."#192;" => "&Agrave;",
        "&"."#193;" => "&Aacute;",
        "&"."#194;" => "&Acirc;",
        "&"."#195;" => "&Atilde;",
        "&"."#196;" => "&Auml;",
        "&"."#197;" => "&Aring;",
        "&"."#198;" => "&AElig;",
        "&"."#199;" => "&Ccedil;",
        "&"."#200;" => "&Egrave;",
        "&"."#201;" => "&Eacute;",
        "&"."#202;" => "&Ecirc;",
        "&"."#203;" => "&Euml;",
        "&"."#204;" => "&Igrave;",
        "&"."#205;" => "&Iacute;",
        "&"."#206;" => "&Icirc;",
        "&"."#207;" => "&Iuml;",
        "&"."#208;" => "&ETH;",
        "&"."#209;" => "&Ntilde;",
        "&"."#210;" => "&Ograve;",
        "&"."#211;" => "&Oacute;",
        "&"."#212;" => "&Ocirc;",
        "&"."#213;" => "&Otilde;",
        "&"."#214;" => "&Ouml;",
        "&"."#215;" => "&times;",
        "&"."#216;" => "&Oslash;",
        "&"."#217;" => "&Ugrave;",
        "&"."#218;" => "&Uacute;",
        "&"."#219;" => "&Ucirc;",
        "&"."#220;" => "&Uuml;",
        "&"."#221;" => "&Yacute;",
        "&"."#222;" => "&THORN;",
        "&"."#223;" => "&szlig;",
        "&"."#224;" => "&agrave;",
        "&"."#225;" => "&aacute;",
        "&"."#226;" => "&acirc;",
        "&"."#227;" => "&atilde;",
        "&"."#228;" => "&auml;",
        "&"."#229;" => "&aring;",
        "&"."#230;" => "&aelig;",
        "&"."#231;" => "&ccedil;",
        "&"."#232;" => "&egrave;",
        "&"."#233;" => "&eacute;",
        "&"."#234;" => "&ecirc;",
        "&"."#235;" => "&euml;",
        "&"."#236;" => "&igrave;",
        "&"."#237;" => "&iacute;",
        "&"."#238;" => "&icirc;",
        "&"."#239;" => "&iuml;",
        "&"."#240;" => "&eth;",
        "&"."#241;" => "&ntilde;",
        "&"."#242;" => "&ograve;",
        "&"."#243;" => "&oacute;",
        "&"."#244;" => "&ocirc;",
        "&"."#245;" => "&otilde;",
        "&"."#246;" => "&ouml;",
        "&"."#247;" => "&divide;",
        "&"."#248;" => "&oslash;",
        "&"."#249;" => "&ugrave;",
        "&"."#250;" => "&uacute;",
        "&"."#251;" => "&ucirc;",
        "&"."#252;" => "&uuml;",
        "&"."#253;" => "&yacute;",
        "&"."#254;" => "&thorn;",
        "&"."#255;" => "&yuml;",
        "&"."#338;" => "&OElig;",
        "&"."#339;" => "&oelig;",
        "&"."#352;" => "&Scaron;",
        "&"."#353;" => "&scaron;",
        "&"."#376;" => "&Yuml;",
        "&"."#402;" => "&fnof;",
        "&"."#710;" => "&circ;",
        "&"."#732;" => "&tilde;",
        "&"."#913;" => "&Alpha;",
        "&"."#914;" => "&Beta;",
        "&"."#915;" => "&Gamma;",
        "&"."#916;" => "&Delta;",
        "&"."#917;" => "&Epsilon;",
        "&"."#918;" => "&Zeta;",
        "&"."#919;" => "&Eta;",
        "&"."#920;" => "&Theta;",
        "&"."#921;" => "&Iota;",
        "&"."#922;" => "&Kappa;",
        "&"."#923;" => "&Lambda;",
        "&"."#924;" => "&Mu;",
        "&"."#925;" => "&Nu;",
        "&"."#926;" => "&Xi;",
        "&"."#927;" => "&Omicron;",
        "&"."#928;" => "&Pi;",
        "&"."#929;" => "&Rho;",
        "&"."#931;" => "&Sigma;",
        "&"."#932;" => "&Tau;",
        "&"."#933;" => "&Upsilon;",
        "&"."#934;" => "&Phi;",
        "&"."#935;" => "&Chi;",
        "&"."#936;" => "&Psi;",
        "&"."#937;" => "&Omega;",
        "&"."#945;" => "&alpha;",
        "&"."#946;" => "&beta;",
        "&"."#947;" => "&gamma;",
        "&"."#948;" => "&delta;",
        "&"."#949;" => "&epsilon;",
        "&"."#950;" => "&zeta;",
        "&"."#951;" => "&eta;",
        "&"."#952;" => "&theta;",
        "&"."#953;" => "&iota;",
        "&"."#954;" => "&kappa;",
        "&"."#955;" => "&lambda;",
        "&"."#956;" => "&mu;",
        "&"."#957;" => "&nu;",
        "&"."#958;" => "&xi;",
        "&"."#959;" => "&omicron;",
        "&"."#960;" => "&pi;",
        "&"."#961;" => "&rho;",
        "&"."#962;" => "&sigmaf;",
        "&"."#963;" => "&sigma;",
        "&"."#964;" => "&tau;",
        "&"."#965;" => "&upsilon;",
        "&"."#966;" => "&phi;",
        "&"."#967;" => "&chi;",
        "&"."#968;" => "&psi;",
        "&"."#969;" => "&omega;",
        "&"."#977;" => "&thetasym;",
        "&"."#978;" => "&upsih;",
        "&"."#982;" => "&piv;",
        "&"."#8194;" => "&ensp;",
        "&"."#8195;" => "&emsp;",
        "&"."#8201;" => "&thinsp;",
        "&"."#8204;" => "&zwnj;",
        "&"."#8205;" => "&zwj;",
        "&"."#8206;" => "&lrm;",
        "&"."#8207;" => "&rlm;",
        "&"."#8211;" => "&ndash;",
        "&"."#8212;" => "&mdash;",
        "&"."#8216;" => "&lsquo;",
        "&"."#8217;" => "&rsquo;",
        "&"."#8218;" => "&sbquo;",
        "&"."#8220;" => "&ldquo;",
        "&"."#8221;" => "&rdquo;",
        "&"."#8222;" => "&bdquo;",
        "&"."#8224;" => "&dagger;",
        "&"."#8225;" => "&Dagger;",
        "&"."#8226;" => "&bull;",
        "&"."#8230;" => "&hellip;",
        "&"."#8240;" => "&permil;",
        "&"."#8242;" => "&prime;",
        "&"."#8243;" => "&Prime;",
        "&"."#8249;" => "&lsaquo;",
        "&"."#8250;" => "&rsaquo;",
        "&"."#8254;" => "&oline;",
        "&"."#8260;" => "&frasl;",
        "&"."#8364;" => "&euro;",
        "&"."#8465;" => "&image;",
        "&"."#8472;" => "&weierp;",
        "&"."#8476;" => "&real;",
        "&"."#8482;" => "&trade;",
        "&"."#8501;" => "&alefsym;",
        "&"."#8592;" => "&larr;",
        "&"."#8593;" => "&uarr;",
        "&"."#8594;" => "&rarr;",
        "&"."#8595;" => "&darr;",
        "&"."#8596;" => "&harr;",
        "&"."#8629;" => "&crarr;",
        "&"."#8656;" => "&lArr;",
        "&"."#8657;" => "&uArr;",
        "&"."#8658;" => "&rArr;",
        "&"."#8659;" => "&dArr;",
        "&"."#8660;" => "&hArr;",
        "&"."#8704;" => "&forall;",
        "&"."#8706;" => "&part;",
        "&"."#8707;" => "&exist;",
        "&"."#8709;" => "&empty;",
        "&"."#8711;" => "&nabla;",
        "&"."#8712;" => "&isin;",
        "&"."#8713;" => "&notin;",
        "&"."#8715;" => "&ni;",
        "&"."#8719;" => "&prod;",
        "&"."#8721;" => "&sum;",
        "&"."#8722;" => "&minus;",
        "&"."#8727;" => "&lowast;",
        "&"."#8730;" => "&radic;",
        "&"."#8733;" => "&prop;",
        "&"."#8734;" => "&infin;",
        "&"."#8736;" => "&ang;",
        "&"."#8743;" => "&and;",
        "&"."#8744;" => "&or;",
        "&"."#8745;" => "&cap;",
        "&"."#8746;" => "&cup;",
        "&"."#8747;" => "&int;",
        "&"."#8756;" => "&there4;",
        "&"."#8764;" => "&sim;",
        "&"."#8773;" => "&cong;",
        "&"."#8776;" => "&asymp;",
        "&"."#8800;" => "&ne;",
        "&"."#8801;" => "&equiv;",
        "&"."#8804;" => "&le;",
        "&"."#8805;" => "&ge;",
        "&"."#8834;" => "&sub;",
        "&"."#8835;" => "&sup;",
        "&"."#8836;" => "&nsub;",
        "&"."#8838;" => "&sube;",
        "&"."#8839;" => "&supe;",
        "&"."#8853;" => "&oplus;",
        "&"."#8855;" => "&otimes;",
        "&"."#8869;" => "&perp;",
        "&"."#8901;" => "&sdot;",
        "&"."#8968;" => "&lceil;",
        "&"."#8969;" => "&rceil;",
        "&"."#8970;" => "&lfloor;",
        "&"."#8971;" => "&rfloor;",
        "&"."#9001;" => "&lang;",
        "&"."#9002;" => "&rang;",
        "&"."#9674;" => "&loz;",
        "&"."#9824;" => "&spades;",
        "&"."#9827;" => "&clubs;",
        "&"."#9829;" => "&hearts;",
        "&"."#9830;" => "&diams;"
          );


    reset($ISO10646XHTMLTrans);
    while(list($UnicodeChar, $XHTMLEquiv) = each($ISO10646XHTMLTrans)) {
      $content = str_replace($UnicodeChar, $XHTMLEquiv, $content);
    }

    //      $content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');

    // return translated
    return($content);
  }

  function html_entity_decode_utf8($string)
  {
    static $trans_tbl;
    $string = preg_replace('~&#x([0-9a-f]+);~ei','code2utf(hexdec(“\\1″))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'code2utf(\\1)',$string);

    if (!isset($trans_tbl))
    {
      $trans_tbl = array();
      foreach (get_html_translation_table(HTML_ENTITIES) as
          $val=>$key)
        $trans_tbl[$key] = utf8_encode($val);
    }
    return strtr($string, $trans_tbl);
  }

  function GetPlainText($string)
  {
    $string = str_replace("NONBLOCKINGZERO","&#65279;",$string);
    return htmlspecialchars(trim(html_entity_decode($string, ENT_QUOTES, 'UTF-8')));
  }

  function ReadyForPDF($string)
  {
    //return $string;
    $string = str_replace("&rsquo;","'",$string);
    $string = str_replace("NONBLOCKINGZERO","",$string);
    return trim(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
  }


  function ColorPicker() {
    $colors = array('#004704','#C40046','#832BA8','#FF8128','#7592A0');

    $out = "<option value=\"\" style=\"background-color: #FFFFFF\" onclick=\"this.parentElement.style.background='#FFFFFF'\">Keine</option>";
    for($i=0;$i<count($colors);$i++)
      $out .= "<option value=\"{$colors[$i]}\" style=\"background-color: {$colors[$i]}\" onclick=\"this.parentElement.style.background='{$colors[$i]}'\">&nbsp;</option>";

    return $out;
  }

  function hex_dump($data, $newline="\n")
  {
    static $from = '';
    static $to = '';

    static $width = 16; # number of bytes per line

      static $pad = '.'; # padding for non-visible characters

      if ($from==='')
      {
        for ($i=0; $i<=0xFF; $i++)
        {
          $from .= chr($i);
          $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
        }
      }

    $hex = str_split(bin2hex($data), $width*2);
    $chars = str_split(strtr($data, $from, $to), $width);

    $offset = 0;
    foreach ($hex as $i => $line)
    {
      echo sprintf('%6X',$offset).' : '.implode(' ', str_split($line,2)) . ' [' . $chars[$i] . ']' . $newline;
      $offset += $width;
    }
  }

  function KalenderList($parsetarget)
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Kalender");
    $this->app->Tpl->Set(TABTEXT,"Kalender");

    $submit = $this->app->Secure->GetPOST("submitForm");
    $mode = $this->app->Secure->GetPOST("mode");
    $eventid = $this->app->Secure->GetPOST("eventid");

    $titel = $this->app->Secure->GetPOST("titel");
    $datum = $this->app->Secure->GetPOST("datum");
    $datum_bis = $this->app->Secure->GetPOST("datum_bis");
    $allday = $this->app->Secure->GetPOST("allday");
    $public = $this->app->Secure->GetPOST("public");
    $von = $this->app->Secure->GetPOST("von");
    $bis = $this->app->Secure->GetPOST("bis");
    $beschreibung = $this->app->Secure->GetPOST("beschreibung");
    $ort = $this->app->Secure->GetPOST("ort");

    $personen = $this->app->Secure->GetPOST("personen");
    $color = $this->app->Secure->GetPOST("color");

    if($submit!="") {
      $von_datum =  $this->app->String->Convert("$datum $von", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
      $bis_datum =  $this->app->String->Convert("$datum_bis $bis", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");

      if($allday=='1') {
        $von_datum = $this->app->String->Convert("$datum 00:00", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
        $bis_datum = $this->app->String->Convert("$datum_bis 00:00", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
        //$bis_datum = $datum_bis;
      }


      if($mode=="new") {
        $this->app->DB->Insert("INSERT INTO kalender_event (ort,bezeichnung,beschreibung, von, bis, allDay, color, public) 
            VALUES ('$ort','$titel', '$beschreibung','$von_datum', '$bis_datum', '$allday', '$color', '$public')");
        $event = $this->app->DB->GetInsertID();
      }

      if($mode=="edit" && is_numeric($eventid)) {
        $this->app->DB->Update("UPDATE kalender_event SET ort='$ort',bezeichnung='$titel', beschreibung='$beschreibung',von='$von_datum', bis='$bis_datum', 
            allDay='$allday', color='$color', public='$public' WHERE id='$eventid' LIMIT 1");
        $this->app->DB->Delete("DELETE FROM kalender_user WHERE event='$eventid'");
        $event = $eventid;
      }

      if($mode=="delete" && is_numeric($eventid)) {
        $this->app->DB->Delete("DELETE FROM kalender_event WHERE id='$eventid' LIMIT 1");
        $this->app->DB->Delete("DELETE FROM kalender_user WHERE event='$eventid'");
      }

      if($mode=="copy" && is_numeric($eventid)) {
        $cData = $this->app->DB->SelectArr("SELECT * FROM kalender_event WHERE id='$eventid' LIMIT 1");
        $this->app->DB->Insert("INSERT INTO kalender_event (bezeichnung, von, bis, allDay, color, public) 
            VALUES ('{$cData[0]['bezeichnung']}', '{$cData[0]['von']}', '{$cData[0]['bis']}', 
              '{$cData[0]['allDay']}', '{$cData[0]['color']}', '{$cData[0]['public']}')");
        $event = $this->app->DB->GetInsertID();
      }

      // Schreibe Personen  
      if(is_numeric($event) && is_array($personen) && count($personen) && $mode!="delete") {
        for($p=0;$p<count($personen);$p++)
          $this->app->DB->Insert("INSERT INTO kalender_user (event, userid) VALUES ('$event', '{$personen[$p]}')");
      }
    }

    // Personen Auswahl
    $user = $this->app->User->GetID();
    if($this->app->Conf->WFdbType=="postgre")
      $users = $this->app->DB->SelectArr("SELECT u.id, a.name FROM \"user\" LEFT JOIN adresse a ON a.id=adresse WHERE activ='1' AND kalender_ausblenden!=1 ORDER BY username");
    else
      $users = $this->app->DB->SelectArr("SELECT u.id, a.name as description FROM user u LEFT JOIN adresse a ON a.id=u.adresse WHERE u.activ='1' AND u.kalender_ausblenden!=1 ORDER BY u.username");
    for($i=0; $i<count($users);$i++){
      $select = (($user==$users[$i]['id']) ? "selected" : "");
      $user_out .= "<option value=\"{$users[$i]['id']}\" $select>{$users[$i]['description']}</option>";
    }
    $this->app->Tpl->Set('PERSONEN', $user_out);


    $this->app->Tpl->Set('COLORS', $this->ColorPicker());
    $this->app->Tpl->Parse($parsetarget,"kalender.tpl");

  }

  function NavigationStock()
  {
    $navarray[menu][web][0][first]  = array('wawision','welcome','main');
    $navarray[menu][web][0][sec][]  = array('Anmelden','welcome','login');

    //admin menu
    $menu = 0;
    $navarray[menu][admin][++$menu][first]  = array('Stammdaten','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Adressen','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Artikel','artikel','list');
    $navarray[menu][admin][$menu][sec][] = array('Projekte','projekt','list');

    $navarray[menu][admin][++$menu][first]  = array('Einkauf','auftrag','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Preisanfrage','preisanfrage','list');
    $navarray[menu][admin][$menu][sec][]  = array('Bestellung','bestellung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Bestellvorschlag','lager','ausgehend');

    //$navarray[menu][admin][$menu][sec][]  = array('Sammelbestellung','bestellung','sammel');

    $navarray[menu][admin][++$menu][first]  = array('Wareneingang','wareneingang','paketannahme');
    $navarray[menu][admin][$menu][sec][]  = array('Paket Annahme','wareneingang','paketannahme');

    if($this->Firmendaten("wareneingang_kamera_waage")=="1")
      $navarray[menu][admin][$menu][sec][]  = array('Paket Distribution','wareneingang','distribution');

    //$navarray[menu][admin][$menu][sec][]  = array('Retoursendung','wareneingang','rma');

    $navarray[menu][admin][++$menu][first]  = array('Buchhaltung','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Verbindlichkeiten','verbindlichkeit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Kassenbuch','kasse','list');

    $navarray[menu][admin][++$menu][first]  = array('Verwaltung','rechnung','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Artikel &Uuml;bersetzungen','uebersetzung','main');
    // $navarray[menu][admin][$menu][sec][]  = array('Massenartikel','massenartikel','edit');
    //  $navarray[menu][admin][$menu][sec][]  = array('Versand starten','versanderzeugen','offene');
    //$navarray[menu][admin][$menu][sec][]  = array('Import St&uuml;ckliste','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fertigung planen','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Pflichtenheft Tool','rechnung','list');
    // $navarray[menu][admin][$menu][sec][]  = array('Shop Import','shopimport','list');
    // $navarray[menu][admin][$menu][sec][]  = array('Shop Export','shopexport','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Artikel Reservierung','versanderzeugen','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Dateien','dateien','list'); 
    //$navarray[menu][admin][$menu][sec][]  = array('Scanner','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Gesetzliches E-Mail Backup','emailbackup','list');
    //  $navarray[menu][admin][$menu][sec][]  = array('Kassenbuch','kasse','list');
    //   $navarray[menu][admin][$menu][sec][]  = array('RMA Lieferungen','wareneingang','rmalist');
    $navarray[menu][admin][$menu][sec][]  = array('Etikettendrucker','etikettendrucker','list');


    $navarray[menu][admin][++$menu][first] = array('Lager','lager','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lieferschein','lieferschein','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lagerverwaltung','lager','list');
    $navarray[menu][admin][$menu][sec][]  = array('Inventur','inventur','list');
    //  $navarray[menu][admin][$menu][sec][]   = array('Reservierungen','lager','reservierungen');
    //  $navarray[menu][admin][$menu][sec][]   = array('Lager Kalkulation','lager','ausgehend');
    //   $navarray[menu][admin][$menu][sec][]   = array('Produktionslager','lager','produktionslager');
    //$navarray[menu][admin][$menu][sec][]  = array('Lagerlampen','artikel','lagerlampe');
    $navarray[menu][admin][$menu][sec][]  = array('Mindesthaltbarkeit','mhdwarning','list');
    $navarray[menu][admin][$menu][sec][]  = array('Chargenverwaltung','chargen','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lagerentnahme','lager','buchenauslagern&cmd=umlagern');
    $navarray[menu][admin][$menu][sec][]   = array('Zwischenlager','lager','buchenzwischenlager');
    //$navarray[menu][admin][$menu][sec][]   = array('Artikel f&uuml;r Lieferungen','lager','artikelfuerlieferungen');


    $navarray[menu][admin][++$menu][first]  = array('Administration','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','einstellungen','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Ger&auml;teverwaltung','geraete','list');
    $navarray[menu][admin][$menu][sec][]  = array('Backup','backup','list','recover','delete','reset');
    //$navarray[menu][admin][$menu][sec][]  = array('Updates / Plugins','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Netzwerk','netzwerk','list');

    $navarray[menu][admin][++$menu][first]  = array('Mein Bereich','welcome','main');
    $startseite = $this->app->DB->Select("SELECT startseite FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($startseite!="")
      $navarray[menu][admin][$menu][sec][]  = array('Startseite','welcome','startseite');


    $navarray[menu][admin][$menu][sec][]  = array('Dashboard','welcome','start');
    //    $navarray[menu][admin][$menu][sec][]  = array('Startseite','welcome','start');
    //$navarray[menu][admin][$menu][sec][]  = array('Tickets','ticket','list');
    $navarray[menu][admin][$menu][sec][]  = array('Kalender','kalender','list');
    $navarray[menu][admin][$menu][sec][]  = array('Pinwand','welcome','pinwand');
    $navarray[menu][admin][$menu][sec][]  = array('Wiki','wiki','list');
    $navarray[menu][admin][$menu][sec][]  = array('Aufgaben','aufgaben','list');
    //  $navarray[menu][admin][$menu][sec][]  = array('E-Mail Archiv','webmail','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zeiterfassung','zeiterfassung','create');
    //$navarray[menu][admin][$menu][sec][]  = array('Urlaub','urlaub','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Krankheit / Fehltage','krankheit','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fahrtenbuch','krankheit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','welcome','settings');
    $navarray[menu][admin][$menu][sec][]  = array('Abmelden','welcome','logout');

    $navarray[menu][admin][++$menu][first]  = array('Direktzugriff','a','b');

    //return $navarray[menu][admin];
    return $this->CalculateNavigation($navarray);

  }



  function NavigationOSS()
  {

    $navarray[menu][web][0][first]  = array('wawision','welcome','main');
    $navarray[menu][web][0][sec][]  = array('Anmelden','welcome','login');

    // admin menu
    $menu = 0;
    $navarray[menu][admin][++$menu][first]  = array('Stammdaten','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Adressen','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Artikel','artikel','list');
    $navarray[menu][admin][$menu][sec][] = array('Projekte','projekt','list');

    $navarray[menu][admin][++$menu][first]  = array('Verkauf','auftrag','list');
    $navarray[menu][admin][$menu][sec][]  = array('Angebot','angebot','list');
    $navarray[menu][admin][$menu][sec][]  = array('Auftrag','auftrag','list');
    //$this->WFconf[menu][admin][$menu][sec][]  = array('Auftragsuche','auftrag','search');

    $navarray[menu][admin][++$menu][first]  = array('Einkauf','auftrag','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Preisanfrage','preisanfrage','list');
    $navarray[menu][admin][$menu][sec][]  = array('Bestellung','bestellung','list');

    // $navarray[menu][admin][$menu][sec][]  = array('Bestellvorschlag','bestellvorschlag','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Sammelbestellung','bestellung','sammel');

    $navarray[menu][admin][++$menu][first]  = array('Wareneingang','wareneingang','paketannahme');
    $navarray[menu][admin][$menu][sec][]  = array('Paket Annahme','wareneingang','paketannahme');

    if($this->Firmendaten("wareneingang_kamera_waage")=="1")
      $navarray[menu][admin][$menu][sec][]  = array('Paket Distribution','wareneingang','distribution');

    //$navarray[menu][admin][$menu][sec][]  = array('Retoursendung','wareneingang','rma');

    $navarray[menu][admin][++$menu][first]  = array('Buchhaltung','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Rechnungen','rechnung','list');
    //  $navarray[menu][admin][$menu][sec][]  = array('Zahlungseingang','zahlungseingang','list');
    //   $navarray[menu][admin][$menu][sec][]  = array('Verbindlichkeiten','verbindlichkeit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Gutschrift/'.$this->Firmendaten("bezeichnungstornorechnung"),'gutschrift','list');
    //   $navarray[menu][admin][$menu][sec][]  = array('Abolauf','rechnungslauf','rechnungslauf');
    $navarray[menu][admin][$menu][sec][]  = array('Mahnwesen','mahnwesen','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Kontoblatt','kontoblatt','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Lastschriften','rechnung','lastschrift');
    //    $navarray[menu][admin][$menu][sec][]  = array('Ausgabe melden','buchhaltung','ausgabemelden');
    //    $navarray[menu][admin][$menu][sec][]  = array('Lohnabrechnung','lohnabrechnung','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Stornierungen','stornierungen','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Briefe f&uuml;r Post','stornierungen','list');

    $navarray[menu][admin][++$menu][first]  = array('Marketing','marketing','list');
    $navarray[menu][admin][$menu][sec][]  = array('Verkaufszahlen','verkaufszahlen','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Partner Auszahlungen','partner','list');
    //   $navarray[menu][admin][$menu][sec][]  = array('Kampangen','marketing','kampangen');
    //   $navarray[menu][admin][$menu][sec][]  = array('Marketing Plan','marketing','list');
    $navarray[menu][admin][$menu][sec][]  = array('Quick-Mailing','mailing','list');
    //   $navarray[menu][admin][$menu][sec][]  = array('Katalog','katalog','list');



    $navarray[menu][admin][++$menu][first]  = array('Verwaltung','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Artikel &Uuml;bersetzungen','uebersetzung','main');
    //$navarray[menu][admin][$menu][sec][]  = array('Massenartikel','massenartikel','edit');
    //$navarray[menu][admin][$menu][sec][]  = array('Import St&uuml;ckliste','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fertigung planen','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Pflichtenheft Tool','rechnung','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Import','shopimport','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Export','shopexport','list');
    $navarray[menu][admin][$menu][sec][]  = array('Import/Export Zentrale','importvorlage','uebersicht');
    //$navarray[menu][admin][$menu][sec][]  = array('Dateien','dateien','list'); 
    //$navarray[menu][admin][$menu][sec][]  = array('Scanner','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Gesetzliches E-Mail Backup','emailbackup','list');


    //    $navarray[menu][admin][$menu][sec][]  = array('Artikel &Uuml;bersetzungen','uebersetzung','main');
    // $navarray[menu][admin][$menu][sec][]  = array('Massenartikel','massenartikel','edit');
    //  $navarray[menu][admin][$menu][sec][]  = array('Versand starten','versanderzeugen','offene');
    //$navarray[menu][admin][$menu][sec][]  = array('Import St&uuml;ckliste','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fertigung planen','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Pflichtenheft Tool','rechnung','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Import','shopimport','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Export','shopexport','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Artikel Reservierung','versanderzeugen','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Dateien','dateien','list'); 
    //$navarray[menu][admin][$menu][sec][]  = array('Scanner','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Gesetzliches E-Mail Backup','emailbackup','list');
    //  $navarray[menu][admin][$menu][sec][]  = array('Kassenbuch','kasse','list');
    //   $navarray[menu][admin][$menu][sec][]  = array('RMA Lieferungen','wareneingang','rmalist');


    $navarray[menu][admin][++$menu][first] = array('Lager','lager','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lieferschein','lieferschein','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lagerverwaltung','lager','list');
    //  $navarray[menu][admin][$menu][sec][]   = array('Reservierungen','lager','reservierungen');
    //  $navarray[menu][admin][$menu][sec][]   = array('Lager Kalkulation','lager','ausgehend');
    //   $navarray[menu][admin][$menu][sec][]   = array('Produktionslager','lager','produktionslager');
    //    $navarray[menu][admin][$menu][sec][]  = array('Lagerlampen','artikel','lagerlampe');
    $navarray[menu][admin][$menu][sec][]  = array('Mindesthaltbarkeit','mhdwarning','list');
    $navarray[menu][admin][$menu][sec][]  = array('Chargenverwaltung','chargen','list');
    $navarray[menu][admin][$menu][sec][]   = array('Ein- und auslagern','lager','bucheneinlagern');
    $navarray[menu][admin][$menu][sec][]   = array('Zwischenlager','lager','buchenzwischenlager');
    $navarray[menu][admin][$menu][sec][]   = array('Artikel f&uuml;r Lieferungen','lager','artikelfuerlieferungen');


    $navarray[menu][admin][++$menu][first]  = array('Administration','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','einstellungen','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Ger&auml;teverwaltung','geraete','list');
    $navarray[menu][admin][$menu][sec][]  = array('Backup','backup','list','recover','delete','reset');
    //$navarray[menu][admin][$menu][sec][]  = array('Updates / Plugins','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Netzwerk','netzwerk','list');

    $navarray[menu][admin][++$menu][first]  = array('Mein Bereich','welcome','main');
    $startseite = $this->app->DB->Select("SELECT startseite FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($startseite!="")
      $navarray[menu][admin][$menu][sec][]  = array('Startseite','welcome','startseite');


    $navarray[menu][admin][$menu][sec][]  = array('Dashboard','welcome','start');
    //    $navarray[menu][admin][$menu][sec][]  = array('Startseite','welcome','start');
    //$navarray[menu][admin][$menu][sec][]  = array('Tickets','ticket','list');
    $navarray[menu][admin][$menu][sec][]  = array('Kalender','kalender','list');
    $navarray[menu][admin][$menu][sec][]  = array('Pinwand','welcome','pinwand');
    $navarray[menu][admin][$menu][sec][]  = array('Wiki','wiki','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Aufgaben','aufgaben','list');
    //  $navarray[menu][admin][$menu][sec][]  = array('E-Mail Archiv','webmail','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zeiterfassung','zeiterfassung','create');
    //$navarray[menu][admin][$menu][sec][]  = array('Urlaub','urlaub','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Krankheit / Fehltage','krankheit','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fahrtenbuch','krankheit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','welcome','settings');
    $navarray[menu][admin][$menu][sec][]  = array('Abmelden','welcome','logout');

    //return $navarray[menu][admin];
    return $this->CalculateNavigation($navarray);

  }

  function NavigationFREELANCER()
  {
    // admin menu
    $menu = 0;
    $navarray[menu][admin][++$menu][first]  = array('Stammdaten','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Adressen','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Artikel','artikel','list');
    $navarray[menu][admin][$menu][sec][] = array('Projekte','projekt','list');

    $navarray[menu][admin][++$menu][first]  = array('Verkauf','auftrag','list');
    $navarray[menu][admin][$menu][sec][]  = array('Angebot','angebot','list');
    $navarray[menu][admin][$menu][sec][]  = array('Auftrag','auftrag','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Auftragsuche','auftrag','search');

    $navarray[menu][admin][++$menu][first]  = array('Einkauf','auftrag','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Preisanfrage','preisanfrage','list');
    $navarray[menu][admin][$menu][sec][]  = array('Bestellung','bestellung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Sammelbestellung','bestellung','sammel');


    $navarray[menu][admin][++$menu][first]  = array('Buchhaltung','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Rechnungen','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zahlungseingang','zahlungseingang','list');
    $navarray[menu][admin][$menu][sec][]  = array('Gutschrift / '.$this->Firmendaten("bezeichnungstornorechnung"),'gutschrift','list');
    $navarray[menu][admin][$menu][sec][]  = array('Abolauf','rechnungslauf','rechnungslauf');
    $navarray[menu][admin][$menu][sec][]  = array('Mahnwesen','mahnwesen','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Ausgabe melden','buchhaltung','ausgabemelden');
    $navarray[menu][admin][$menu][sec][]  = array('Verbindlichkeiten','verbindlichkeit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Kassenbuch','kasse','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Briefe f&uuml;r Post','stornierungen','list');

    $navarray[menu][admin][++$menu][first]  = array('Marketing','marketing','list');
    $navarray[menu][admin][$menu][sec][]  = array('Verkaufszahlen','verkaufszahlen','list');

    $navarray[menu][admin][++$menu][first]  = array('Verwaltung','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zeitkonten','zeiterfassung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Berichte','berichte','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Import','shopimport','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Export','shopexport','list');
    $navarray[menu][admin][$menu][sec][]  = array('Etikettendrucker','etikettendrucker','list');

    $navarray[menu][admin][++$menu][first] = array('Lager','lager','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lieferschein','lieferschein','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lagerverwaltung','lager','list');
    $navarray[menu][admin][$menu][sec][]   = array('Ein- und auslagern','lager','bucheneinlagern');


    $navarray[menu][admin][++$menu][first]  = array('Administration','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','einstellungen','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Ger&auml;teverwaltung','geraete','list');
    $navarray[menu][admin][$menu][sec][]  = array('Backup','backup','list','recover','delete','reset');
    //$navarray[menu][admin][$menu][sec][]  = array('Updates / Plugins','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Netzwerk','netzwerk','list');

    $navarray[menu][admin][++$menu][first]  = array('Mein Bereich','welcome','main');


    $startseite = $this->app->DB->Select("SELECT startseite FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($startseite!="")
      $navarray[menu][admin][$menu][sec][]  = array('Startseite','welcome','startseite');

    $navarray[menu][admin][$menu][sec][]  = array('Dashboard','welcome','start');
    $navarray[menu][admin][$menu][sec][]  = array('Kalender','kalender','list');
    $navarray[menu][admin][$menu][sec][]  = array('Pinwand','welcome','pinwand');
    $navarray[menu][admin][$menu][sec][]  = array('Aufgaben','aufgaben','list');
    $navarray[menu][admin][$menu][sec][]  = array('E-Mail Archiv','webmail','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zeiterfassung','zeiterfassung','create');
    //$navarray[menu][admin][$menu][sec][]  = array('Urlaub','urlaub','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Krankheit / Fehltage','krankheit','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fahrtenbuch','krankheit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Wiki','wiki','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','welcome','settings');
    $navarray[menu][admin][$menu][sec][]  = array('Abmelden','welcome','logout');


    return $this->CalculateNavigation($navarray);
  }

  function CalculateNavigation($navarray)
  {

    $type = $this->app->User->GetType();
    //if($type=="admin") return $navarray[menu][admin];

    $permissions_module = $this->app->DB->SelectArr("SELECT module,action FROM userrights WHERE user='".$this->app->User->GetID()."' AND permission='1'");

    $permission_module_new="";

    for($i=0;$i<count($permissions_module);$i++)
    {
      if(is_file("./pages/".$permissions_module[$i]["module"].".php"))
        $permission_module_new[] = $permissions_module[$i]["module"]."_".$permissions_module[$i]["action"];
    }

    $menu = 0;
    $menu_no=1;
    foreach($navarray[menu][admin] as $key=>$value){
      //echo "haupt:".$value[first][0]."<br>";

      $menu++;
      if(count($value[sec])>0){
        foreach($value[sec] as $secnav){
          //echo $secnav[0]." ".$secnav[1]." ".$secnav[2]."<br>";
          $und_pos = stripos ( $secnav[2] , '&');
          if($und_pos>0)
            $secnav_check =  substr ( $secnav[2] , 0,stripos ( $secnav[2] , '&') );
          else
            $secnav_check = $secnav[2];
          if(@in_array($secnav[1]."_".$secnav_check,$permission_module_new) || ($type=="admin" && is_file("./pages/".$secnav[1].".php")))
          {
            $navarray[menu][tmp][$menu][sec][]  = array($secnav[0],$secnav[1],$secnav[2]);
            $menu_no=0;
          }
        }
      }
      if($menu_no==0)
        $navarray[menu][tmp][$menu][first] = array($value[first][0],$value[first][1],'main');
      $menu_no=1;

    }

    return $navarray[menu][tmp];
  }



  function NavigationCOM()
  {
    // admin menu
    $menu = 0;
    $navarray[menu][admin][++$menu][first]  = array('Stammdaten','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Adressen','adresse','list');
    $navarray[menu][admin][$menu][sec][]  = array('Artikel','artikel','list');
    $navarray[menu][admin][$menu][sec][] = array('Projekte','projekt','list');

    $navarray[menu][admin][++$menu][first]  = array('Verkauf','auftrag','list');
    $navarray[menu][admin][$menu][sec][]  = array('Anfrage','anfrage','list');
    $navarray[menu][admin][$menu][sec][]  = array('Angebot','angebot','list');
    $navarray[menu][admin][$menu][sec][]  = array('Auftrag','auftrag','list');
    $navarray[menu][admin][$menu][sec][]  = array('POS','pos','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Auftragsuche','auftrag','search');

    $navarray[menu][admin][++$menu][first]  = array('Einkauf','auftrag','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Preisanfrage','preisanfrage','list');
    $navarray[menu][admin][$menu][sec][]  = array('Bestellung','bestellung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Bestellvorschlag','lager','ausgehend');
    $navarray[menu][admin][$menu][sec][]  = array('Produktion','produktion','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Sammelbestellung','bestellung','sammel');

    $navarray[menu][admin][++$menu][first]  = array('Wareneingang','wareneingang','paketannahme');
    $navarray[menu][admin][$menu][sec][]  = array('Paket Annahme','wareneingang','paketannahme');

    if($this->Firmendaten("wareneingang_kamera_waage")=="1")
      $navarray[menu][admin][$menu][sec][]  = array('Paket Distribution','wareneingang','distribution');
    //$navarray[menu][admin][$menu][sec][]  = array('Retoursendung','wareneingang','rma');

    $navarray[menu][admin][++$menu][first]  = array('Buchhaltung','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Rechnungen','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zahlungseingang','zahlungseingang','list');
    $navarray[menu][admin][$menu][sec][]  = array('Reisekosten','reisekosten','list');
    $navarray[menu][admin][$menu][sec][]  = array('Arbeitsnachweis','arbeitsnachweis','list');
    $navarray[menu][admin][$menu][sec][]  = array('Gutschrift / '.$this->Firmendaten("bezeichnungstornorechnung"),'gutschrift','list');
    $navarray[menu][admin][$menu][sec][]  = array('Abolauf','rechnungslauf','rechnungslauf');
    $navarray[menu][admin][$menu][sec][]  = array('Mahnwesen','mahnwesen','list');
    $navarray[menu][admin][$menu][sec][]  = array('Finanzbuchhaltung','kontoblatt','list');
    $navarray[menu][admin][$menu][sec][]  = array('Finanzbuchhaltung Export','buchhaltungexport','list');
    $navarray[menu][admin][$menu][sec][]  = array('SEPA Zahlungsverkehr','zahlungsverkehr','ueberweisung');
    $navarray[menu][admin][$menu][sec][]  = array('Automatischer Rechnungsdruck','autorechnungsdruck','list');
    $navarray[menu][admin][$menu][sec][]  = array('Verbandsabrechnungen','verband','offene');
    $navarray[menu][admin][$menu][sec][]  = array('Vertreterabrechnungen','vertreter','list');
    if($this->Firmendaten("modul_mlm")=="1")
      $navarray[menu][admin][$menu][sec][]  = array('Multilevel','multilevel','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Ausgabe melden','buchhaltung','ausgabemelden');
    $navarray[menu][admin][$menu][sec][]  = array('Lohnabrechnung','lohnabrechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Verbindlichkeiten','verbindlichkeit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Stornierungen','stornierungen','list');
    $navarray[menu][admin][$menu][sec][]  = array('Kassenbuch','kasse','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Briefe f&uuml;r Post','stornierungen','list');

    $navarray[menu][admin][++$menu][first]  = array('Marketing','marketing','list');
    $navarray[menu][admin][$menu][sec][]  = array('Verkaufszahlen','verkaufszahlen','list');
    $navarray[menu][admin][$menu][sec][]  = array('Umsatzstatistik','umsatzstatistik','kunde');
    $navarray[menu][admin][$menu][sec][]  = array('Aktionscodes','aktionscodes','list');
    $navarray[menu][admin][$menu][sec][]  = array('Wiedervorlage','wiedervorlage','list');
    $navarray[menu][admin][$menu][sec][]  = array('Partner Auszahlungen','partner','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Kampangen','marketing','kampangen');
    //$navarray[menu][admin][$menu][sec][]  = array('Marketing Plan','marketing','list');
    $navarray[menu][admin][$menu][sec][]  = array('Quick-Mailing','mailing','list');
    $navarray[menu][admin][$menu][sec][]  = array('Katalog','katalog','list');



    $navarray[menu][admin][++$menu][first]  = array('Verwaltung','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Artikel &Uuml;bersetzungen','uebersetzung','main');
    //$navarray[menu][admin][$menu][sec][]  = array('Massenartikel','massenartikel','edit');
    $navarray[menu][admin][$menu][sec][]  = array('Inventur','inventur','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zeitkonten','zeiterfassung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Berichte','berichte','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Import St&uuml;ckliste','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fertigung planen','rechnung','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Pflichtenheft Tool','rechnung','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Import','shopimport','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Shop Export','shopexport','list');
    $navarray[menu][admin][$menu][sec][]  = array('Import/Export Zentrale','importvorlage','uebersicht');
    $navarray[menu][admin][$menu][sec][]  = array('Hilfsprogramme','hilfsprogramme','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Dateien','dateien','list'); 
    //$navarray[menu][admin][$menu][sec][]  = array('Scanner','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Gesetzliches E-Mail Backup','emailbackup','list');

    $navarray[menu][admin][$menu][sec][]  = array('RMA Lieferungen','rma','list');
    $navarray[menu][admin][$menu][sec][]  = array('Service & Support','service','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Linkeditor','linkeditor','list');
    $navarray[menu][admin][$menu][sec][]  = array('Etikettendrucker','etikettendrucker','list');


    $navarray[menu][admin][++$menu][first] = array('Lager','lager','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lieferschein','lieferschein','list');
    $navarray[menu][admin][$menu][sec][]   = array('Lagerverwaltung','lager','list');
    $navarray[menu][admin][$menu][sec][]   = array('Reservierungen','lager','reservierungen');

    $navarray[menu][admin][$menu][sec][]  = array('Versandzentrum','versanderzeugen','offene');
    $navarray[menu][admin][$menu][sec][]  = array('Kommissionieraufkleber','kommissionieraufkleber','list');
    //    $navarray[menu][admin][$menu][sec][]   = array('Lager Kalkulation','lager','ausgehend');
    //    $navarray[menu][admin][$menu][sec][]   = array('Produktionslager','lager','produktionslager');
    //    $navarray[menu][admin][$menu][sec][]  = array('Lagerlampen','artikel','lagerlampe');
    $navarray[menu][admin][$menu][sec][]  = array('Mindesthaltbarkeit','mhdwarning','list');
    $navarray[menu][admin][$menu][sec][]  = array('Chargenverwaltung','chargen','list');
    $navarray[menu][admin][$menu][sec][]   = array('Ein- und auslagern','lager','bucheneinlagern');
    $navarray[menu][admin][$menu][sec][]   = array('Zwischenlager','lager','buchenzwischenlager');
    $navarray[menu][admin][$menu][sec][]   = array('Artikel f&uuml;r Lieferungen','lager','artikelfuerlieferungen');
    $navarray[menu][admin][$menu][sec][]   = array('Artikel f&uuml;r Produktionen','lager','artikelfuerlieferungen&cmd=produktion');


    $navarray[menu][admin][++$menu][first]  = array('Administration','rechnung','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','einstellungen','list');
    //    $navarray[menu][admin][$menu][sec][]  = array('Ger&auml;teverwaltung','geraete','list');
    $navarray[menu][admin][$menu][sec][]  = array('Backup','backup','list','recover','delete','reset');
    //$navarray[menu][admin][$menu][sec][]  = array('Updates / Plugins','ticket','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Netzwerk','netzwerk','list');

    $navarray[menu][admin][++$menu][first]  = array('Mein Bereich','welcome','main');
    $startseite = $this->app->DB->Select("SELECT startseite FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    if($startseite!="")
      $navarray[menu][admin][$menu][sec][]  = array('Startseite','welcome','startseite');


    $navarray[menu][admin][$menu][sec][]  = array('Dashboard','welcome','start');
    $navarray[menu][admin][$menu][sec][]  = array('Tickets','ticket','list');
    $navarray[menu][admin][$menu][sec][]  = array('Kalender','kalender','list');
    $navarray[menu][admin][$menu][sec][]  = array('Pinwand','welcome','pinwand');
    $navarray[menu][admin][$menu][sec][]  = array('Aufgaben','aufgaben','list');
    $navarray[menu][admin][$menu][sec][]  = array('E-Mail Archiv','webmail','list');
    $navarray[menu][admin][$menu][sec][]  = array('Zeiterfassung','zeiterfassung','create');
    //$navarray[menu][admin][$menu][sec][]  = array('Urlaub','urlaub','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Krankheit / Fehltage','krankheit','list');
    //$navarray[menu][admin][$menu][sec][]  = array('Fahrtenbuch','krankheit','list');
    $navarray[menu][admin][$menu][sec][]  = array('Wiki','wiki','list');
    $navarray[menu][admin][$menu][sec][]  = array('Einstellungen','welcome','settings');
    $navarray[menu][admin][$menu][sec][]  = array('Abmelden','welcome','logout');

    return $this->CalculateNavigation($navarray);
  }

  /*

     $type = $this->app->User->GetType();
     if($type=="admin") return $navarray[menu][admin];

     $permissions_module = $this->app->DB->SelectArr("SELECT module,action FROM userrights WHERE user='".$this->app->User->GetID()."' AND permission='1'");


     for($i=0;$i<count($permissions_module);$i++)
     $permission_module_new[] = $permissions_module[$i]["module"]."_".$permissions_module[$i]["action"];

     $menu = 0;
     $menu_no=1;
     foreach($navarray[menu][admin] as $key=>$value){
//echo "haupt:".$value[first][0]."<br>";

$menu++;
if(count($value[sec])>0){
foreach($value[sec] as $secnav){
//echo $secnav[0]." ".$secnav[1]." ".$secnav[2]."<br>";
$und_pos = stripos ( $secnav[2] , '&');
if($und_pos>0)
$secnav_check =  substr ( $secnav[2] , 0,stripos ( $secnav[2] , '&') );
else
$secnav_check = $secnav[2];
if(@in_array($secnav[1]."_".$secnav_check,$permission_module_new))
{
$navarray[menu][tmp][$menu][sec][]  = array($secnav[0],$secnav[1],$secnav[2]);
$menu_no=0;
}
}
}
if($menu_no==0)
$navarray[menu][tmp][$menu][first] = array($value[first][0],$value[first][1],'main');
$menu_no=1;

}

return $navarray[menu][tmp];
}
   */

function Branch()
{
  include("../version.php");

  return $version;
}

function Version()
{
  include("../version.php");

  $version = split('_',$version);

  return $version[0];
}


function RevisionPlain()
{
  include("../version.php");
  return $version_revision;
}


function Revision()
{
  include("../version.php");
  return "3.1.".$version_revision;
}


function WikiPage($page)
{
  $content = $this->app->DB->Select("SELECT content FROM wiki WHERE name='$page' LIMIT 1");
  $str = $this->ReadyForPDF($content);
  $wikiparser = new WikiParser();
  if (preg_match('/(<[^>].*?>)/e', $str))
  {
    $str=preg_replace('#(href)="([^:"]*)(?:")#','$1="index.php?module=wiki&action=list&name=$2"',$str);
    $content = $str;
  } else {
    $content = $wikiparser->parse($content);
    //$index = $wikiparser->BuildIndex();
  }
  return $content;
}

function Config2Array($config)
{
  $entries = explode (';', $config);
  // $entries enthält alle key => value paare
  foreach ($entries as $pair) {
    preg_match("/(.+)=>(.+)$/", $pair, $matches);
    $array[$matches[1]] = $matches[2];
  }  

  return array_filter($array);
}


function LiveImport($konto)
{
  $zugangsdaten = $this->app->DB->Select("SELECT liveimport FROM konten WHERE id='$konto' LIMIT 1");
  $zugangsdaten = html_entity_decode($zugangsdaten,ENT_QUOTES,"UTF-8");
  $zugangsdaten = $this->Config2Array($zugangsdaten);

  $kontotyp = $this->app->DB->Select("SELECT type FROM konten WHERE id='$konto' LIMIT 1");

  if(is_file("plugins/liveimport/$kontotyp/$kontotyp.php"))
  {
    include("plugins/liveimport/$kontotyp/$kontotyp.php");
    $tmp = new $kontotyp();
    return $tmp->Import($zugangsdaten);
  } else return "";

}

function AutoresponderBlacklist($mail)
{
  $this->app->DB->Delete("DELETE FROM autoresponder_blacklist WHERE cachetime < date_sub(NOW(), INTERVAL 1 DAY)");

  $check = $this->app->DB->Select("SELECT mailaddress FROM autoresponder_blacklist WHERE mailaddress='$mail' LIMIT 1");

  if($check == $mail)
    return 1;

  $this->app->DB->Insert("INSERT INTO autoresponder_blacklist (mailaddress,cachetime) VALUES ('$mail',NOW())");  
  return 0;
}


function LieferadresseButton($adresse)
{
  $this->app->Tpl->Set(POPUPWIDTH,"1000");
  $this->app->Tpl->Set(LIEFERADRESSEPOPUP,'
      <script>
      $(function() {
        $("#mehr2").button();
        });

      function closeIframe()
      {
      $(\'.externalSite\').dialog(\'close\');
      return false;
      }

      </script>

      <a id="mehr2" style="font-size: 8pt; " href="index.php?module=adresse&action=lieferadressepopup&id='.$adresse.'&iframe=true" class="popup" title="Lieferadresse einf&uuml;gen">Lieferadresse aus Stammdaten</a>');
  //"<input type=\"button\" value=\"Ansprechpartner einf&uuml;gen\">");

}


function GetNavigationSelect($shop)
{


  $oberpunkte = $this->app->DB->SelectArr("SELECT id, bezeichnung, bezeichnung_en, plugin,pluginparameter FROM shopnavigation WHERE parent=0  AND shop='$shop' ORDER BY position");

  $tmp = array();
  foreach($oberpunkte as $punkt)
  {
    $tmp["{$punkt["id"]}"]=$punkt["bezeichnung"];
    $unterpunkte = $this->app->DB->SelectArr("SELECT id, bezeichnung, bezeichnung_en, plugin,pluginparameter FROM shopnavigation WHERE parent='".$punkt["id"]."' AND shop='$shop' ORDER BY position");

    foreach($unterpunkte as $upunkt)
      $tmp["{$upunkt["id"]}"]="&nbsp;&nbsp;&nbsp;".$upunkt["bezeichnung"];
  }

  return $tmp;
}



function AnsprechpartnerButton($adresse)
{
  $this->app->Tpl->Set(POPUPWIDTH,"1000");
  $this->app->Tpl->Set(ANSPRECHPARTNERPOPUP,'
      <script>
      $(function() {
        $("#mehr").button();
        });

      function closeIframe()
      {
      $(\'.externalSite\').dialog(\'close\');
      return false;
      }

      </script>

      <a id="mehr" style="font-size: 8pt; " href="index.php?module=adresse&action=ansprechpartnerpopup&id='.$adresse.'&iframe=true" class="popup" title="Ansprechpartner einf&uuml;gen">Ansprechpartner aus Stammdaten</a>');
  //"<input type=\"button\" value=\"Ansprechpartner einf&uuml;gen\">");

}

function ArtikelAnzahlLagerPlatz($artikel,$lager_platz)
{
  $result =  $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$lager_platz'");
  if($result <=0) $result=0;
  return $result;
}

function ArtikelAnzahlLagerOhneNachschublager($artikel)
{
  return $this->app->DB->Select("SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz
      WHERE lpi.artikel='$artikel' AND lp.autolagersperre!='1'");
}


function ArtikelAnzahlLagerNurNachschublager($artikel)
{
  return $this->app->DB->Select("SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz
      WHERE lpi.artikel='$artikel' AND lp.autolagersperre='1'");
}


function ArtikelAnzahlLager($artikel)
{
  return $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel'");
}


function ArtikelAnzahlOffene($artikel)
{
  return $this->app->DB->Select("SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$artikel' AND a.status='freigegeben'");
  //              return $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$artikel'");
}



function ArtikelAnzahlReserviert($artikel)
{
  return $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$artikel'");
}


function MaxArtikelbezeichnung($delta=0)
{

  return 50+$delta;
}

function CleanString($value)
{
  $value=trim($value);
  $value = $this->app->Secure->stripallslashes($value);
  $value = $this->app->Secure->smartstripslashes($value);
  //$value = htmlspecialchars($value,ENT_QUOTES);
  // $value = str_replace('"','&Prime;',$value);
  // $value = str_replace("'",'&prime;',$value);
  //$value = $this->ConvertForDB($value);
  $value = $this->ConvertForDB($value);
  return $value;
}


function CleanDataBeforImport($data)
{
  if(is_array($data))
  {
    foreach($data as $key=>$value)
    {
      if(!is_array($data[$key]))
        $data[$key] = $this->CleanString($value);
    }

    return $data;

  } else {
    $data = $this->CleanString($data);
    return $data;   
  }

}

function GetStandardProjekt()
{
  return $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
}

function Standardprojekt($table,$id)
{
  $projekt = $this->app->DB->Select("SELECT projekt FROM `$table` WHERE id='$id' LIMIT 1");
  if($projekt<1)
  {
    $standardprojekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
    $this->app->DB->Update("UPDATE `$table` SET projekt='".$standardprojekt."' WHERE id='".$id."' LIMIT 1");
  }
}

function UpgradeDatabase()
{

  $this->app->DB->Update("ALTER TABLE `adresse` CHANGE `rechnung_vorname` `rechnung_vorname` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_name` `rechnung_name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_titel` `rechnung_titel` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_typ` `rechnung_typ` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_strasse` `rechnung_strasse` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_ort` `rechnung_ort` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_land` `rechnung_land` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_abteilung` `rechnung_abteilung` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_unterabteilung` `rechnung_unterabteilung` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_adresszusatz` `rechnung_adresszusatz` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_telefon` `rechnung_telefon` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_telefax` `rechnung_telefax` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_anschreiben` `rechnung_anschreiben` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_email` `rechnung_email` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_plz` `rechnung_plz` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `rechnung_ansprechpartner` `rechnung_ansprechpartner` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");

  $this->CheckColumn("public","int(1)","kalender_event");
  $this->CheckColumn("vorname","varchar(255)","adresse");
  $this->CheckColumn("kennung","varchar(255)","adresse");
  $this->CheckColumn("sachkonto","varchar(20)","adresse","DEFAULT '' NOT NULL");
  $this->CheckColumn("folgebestaetigungsperre","tinyint(1)","adresse","DEFAULT '0' NOT NULL");

  $this->CheckColumn("konto","int(11)","kasse","DEFAULT '1' NOT NULL");
  $this->CheckColumn("nummer","int(11)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("wert","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("steuersatz","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_brutto_normal","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_steuer_normal","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_brutto_ermaessigt","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_steuer_ermaessigt","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_brutto_befreit","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_steuer_befreit","DECIMAL(10,2)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("tagesabschluss","tinyint(1)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("storniert","tinyint(1)","kasse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("storniert_grund","VARCHAR(255)","kasse","DEFAULT '' NOT NULL");
  $this->CheckColumn("storniert_bearbeiter","VARCHAR(64)","kasse","DEFAULT '' NOT NULL");
  $this->CheckColumn("sachkonto","VARCHAR(64)","kasse","DEFAULT '' NOT NULL");
  $this->CheckColumn("bemerkung","TEXT","kasse","DEFAULT '' NOT NULL");
  $this->CheckColumn("belegdatum","DATE","kasse");

  $this->CheckColumn("reserviertdatum","DATE","lager_reserviert");

  $this->CheckColumn("freifeld1","TEXT","adresse");
  $this->CheckColumn("freifeld2","TEXT","adresse");
  $this->CheckColumn("freifeld3","TEXT","adresse");
  $this->CheckColumn("filiale","TEXT","adresse");

  $this->CheckColumn("rma","INT(1)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("transaktionsnummer","VARCHAR(255)","auftrag","DEFAULT '' NOT NULL");
  $this->CheckColumn("vorabbezahltmarkieren","INT(1)","auftrag","DEFAULT '0' NOT NULL");

  $this->CheckColumn("vertrieb","int(11)","adresse");
  $this->CheckColumn("innendienst","int(11)","adresse");
  $this->CheckColumn("verbandsnummer","VARCHAR(255)","adresse");
  $this->CheckColumn("abweichendeemailab","varchar(64)","adresse");
  $this->CheckColumn("portofrei_aktiv","DECIMAL(10,2)","adresse");
  $this->CheckColumn("portofrei_aktiv","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("portofreiab","DECIMAL(10,2)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("infoauftragserfassung","TEXT","adresse","DEFAULT '' NOT NULL");
  $this->CheckColumn("mandatsreferenz","varchar(255)","adresse","DEFAULT '' NOT NULL");
  $this->CheckColumn("mandatsreferenzdatum","DATE","adresse");
  $this->CheckColumn("mandatsreferenzaenderung","TINYINT(1)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("glaeubigeridentnr","varchar(255)","adresse","DEFAULT '' NOT NULL");
  $this->CheckColumn("kreditlimit","DECIMAL(10,2)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("tour","INT(11)","adresse","DEFAULT '0' NOT NULL");

  $this->CheckColumn("zahlungskonditionen_festschreiben","int(1)","adresse");
  $this->CheckColumn("rabatte_festschreiben","int(1)","adresse");

  $this->CheckColumn("autodruck_rz","INT(1)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("explodiert_parent_artikel","INT(11)","lieferschein_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("explodiert_parent_artikel","INT(11)","rechnung_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("explodiert_parent_artikel","INT(11)","gutschrift_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("autodruck_periode","INT(1)","rechnung","DEFAULT '1' NOT NULL");
  $this->CheckColumn("autodruck_done","INT(1)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("autodruck_anzahlverband","INT(11)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("autodruck_anzahlkunde","INT(11)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("autodruck_mailverband","INT(1)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("autodruck_mailkunde","INT(1)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("dta_datei_verband","INT(11)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("dta_datei_verband","INT(11)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("manuell_vorabbezahlt","DATE","gutschrift");
  $this->CheckColumn("manuell_vorabbezahlt_hinweis","VARCHAR(128)","gutschrift","DEFAULT '' NOT NULL");
  $this->CheckColumn("nicht_umsatzmindernd","TINYINT(1)","gutschrift","DEFAULT '0' NOT NULL");

  $this->CheckColumn("dta_datei","INT(11)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("dta_datei","INT(11)","gutschrift","DEFAULT '0' NOT NULL");

  $this->CheckTable("device_jobs");
  $this->CheckColumn("id","int(11)","device_jobs","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("deviceidsource","VARCHAR(64)","device_jobs","DEFAULT ''");
  $this->CheckColumn("deviceiddest","VARCHAR(64)","device_jobs","DEFAULT ''");
  $this->CheckColumn("job","LONGTEXT","device_jobs","DEFAULT '' NOT NULL ");
  $this->CheckColumn("zeitstempel","DATETIME","device_jobs");
  $this->CheckColumn("abgeschlossen","tinyint(1)","device_jobs","DEFAULT '0' NOT NULL ");
  $this->CheckColumn("art","VARCHAR(64)","device_jobs","DEFAULT ''");

  $this->CheckColumn("versendet_am_zeitstempel","DATETIME","versand");



  $this->CheckTable("eigenschaften");
  $this->CheckColumn("id","int(11)","eigenschaften","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("artikel","INT(11)","eigenschaften","DEFAULT '0' NOT NULL");
  $this->CheckColumn("art","INT(11)","eigenschaften","DEFAULT '0' NOT NULL");
  $this->CheckColumn("bezeichnung","VARCHAR(64)","eigenschaften","DEFAULT ''");
  $this->CheckColumn("beschreibung","TEXT","eigenschaften","DEFAULT ''");
  $this->CheckColumn("menge","DECIMAL(10,4)","eigenschaften","DEFAULT '0'");
  $this->CheckColumn("einheit","VARCHAR(64)","eigenschaften","DEFAULT ''");
  $this->CheckColumn("menge2","DECIMAL(10,4)","eigenschaften","DEFAULT '0'");
  $this->CheckColumn("einheit2","VARCHAR(64)","eigenschaften","DEFAULT ''");
  $this->CheckColumn("menge3","DECIMAL(10,4)","eigenschaften","DEFAULT '0'");
  $this->CheckColumn("einheit3","VARCHAR(64)","eigenschaften","DEFAULT ''");
  $this->CheckColumn("wert","VARCHAR(64)","eigenschaften","DEFAULT ''");
  $this->CheckColumn("wert2","VARCHAR(64)","eigenschaften","DEFAULT ''");
  $this->CheckColumn("wert3","VARCHAR(64)","eigenschaften","DEFAULT ''");

  $this->CheckTable("artikeleigenschaften");
  $this->CheckColumn("id","int(11)","artikeleigenschaften","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("bezeichnung","VARCHAR(16)","artikeleigenschaften","DEFAULT '' NOT NULL ");
  $this->CheckColumn("projekt","INT(11)","artikeleigenschaften","DEFAULT '0' NOT NULL");
  $this->CheckColumn("geloescht","tinyint(1)","artikeleigenschaften","DEFAULT '0' NOT NULL");


  $this->CheckTable("artikelkategorien");
  $this->CheckColumn("id","int(11)","artikelkategorien","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("bezeichnung","VARCHAR(64)","artikelkategorien","DEFAULT '' NOT NULL ");
  $this->CheckColumn("next_nummer","VARCHAR(128)","artikelkategorien","DEFAULT '' NOT NULL ");
  $this->CheckColumn("projekt","INT(11)","artikelkategorien","DEFAULT '0' NOT NULL");
  $this->CheckColumn("geloescht","tinyint(1)","artikelkategorien","DEFAULT '0' NOT NULL");
  $this->CheckColumn("externenummer","tinyint(1)","artikelkategorien","DEFAULT '0' NOT NULL");



  $this->CheckTable("kontorahmen");
  $this->CheckColumn("id","int(11)","kontorahmen","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("sachkonto","VARCHAR(16)","kontorahmen","DEFAULT '' NOT NULL ");
  $this->CheckColumn("beschriftung","VARCHAR(64)","kontorahmen","DEFAULT '' NOT NULL ");
  $this->CheckColumn("bemerkung","TEXT","kontorahmen","DEFAULT '' NOT NULL ");
  $this->CheckColumn("ausblenden","tinyint(1)","kontorahmen","DEFAULT '0' NOT NULL");

  $this->CheckTable("etiketten");
  $this->CheckColumn("id","int(11)","etiketten","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("name","VARCHAR(64)","etiketten","DEFAULT '' NOT NULL ");
  $this->CheckColumn("xml","TEXT","etiketten","DEFAULT '' NOT NULL ");
  $this->CheckColumn("bemerkung","TEXT","etiketten","DEFAULT '' NOT NULL ");
  $this->CheckColumn("ausblenden","tinyint(1)","etiketten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("verwendenals","VARCHAR(64)","etiketten","DEFAULT '' NOT NULL ");

  $this->CheckColumn("labelbreite","INT(11)","etiketten","DEFAULT '50' NOT NULL ");
  $this->CheckColumn("labelhoehe","INT(11)","etiketten","DEFAULT '18' NOT NULL ");
  $this->CheckColumn("labelabstand","INT(11)","etiketten","DEFAULT '3' NOT NULL ");
  $this->CheckColumn("labeloffsetx","INT(11)","etiketten","DEFAULT '0' NOT NULL ");
  $this->CheckColumn("labeloffsety","INT(11)","etiketten","DEFAULT '6' NOT NULL ");


  $this->CheckTable("versandpakete");
  $this->CheckColumn("id","int(11)","versandpakete","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("versand","INT(11)","versandpakete","DEFAULT '0' NOT NULL");
  $this->CheckColumn("nr","INT(11)","versandpakete","DEFAULT '0' NOT NULL");
  $this->CheckColumn("tracking","VARCHAR(255)","versandpakete","DEFAULT '' NOT NULL ");
  $this->CheckColumn("versender","VARCHAR(255)","versandpakete","DEFAULT '' NOT NULL ");
  $this->CheckColumn("gewicht","VARCHAR(10)","versandpakete","DEFAULT '' NOT NULL ");
  $this->CheckColumn("bemerkung","TEXT","versandpakete","DEFAULT '' NOT NULL ");



  $this->CheckTable("kasse_log");
  $this->CheckColumn("id","int(11)","kasse_log","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("kasseid","INT(11)","kasse_log","DEFAULT '0' NOT NULL");
  $this->CheckColumn("user","INT(11)","kasse_log","DEFAULT '0' NOT NULL");
  $this->CheckColumn("beschreibung","VARCHAR(255)","kasse_log","DEFAULT '' NOT NULL");
  $this->CheckColumn("betrag","DECIMAL(10,2)","kasse_log","DEFAULT '0' NOT NULL");
  $this->CheckColumn("wert","DECIMAL(10,2)","kasse_log","DEFAULT '0' NOT NULL");

  $this->CheckTable("service");
  $this->CheckColumn("id","int(11)","service","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("adresse","INT(11)","service","DEFAULT '0' NOT NULL");
  $this->CheckColumn("zuweisen","INT(11)","service","DEFAULT '0' NOT NULL");
  $this->CheckColumn("ansprechpartner","VARCHAR(255)","service","DEFAULT '' NOT NULL");
  $this->CheckColumn("nummer","VARCHAR(64)","service","UNIQUE");
  $this->CheckColumn("prio","VARCHAR(10)","service","DEFAULT 'niedrig' NOT NULL ");
  $this->CheckColumn("eingangart","VARCHAR(10)","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("datum","DATETIME","service");
  $this->CheckColumn("erledigenbis","DATE","service");
  $this->CheckColumn("betreff","VARCHAR(255)","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("beschreibung_html","LONGTEXT","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("internebemerkung","LONGTEXT","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("antwortankunden","LONGTEXT","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("angelegtvonuser","INT(11)","service","DEFAULT '0' NOT NULL ");
  $this->CheckColumn("status","VARCHAR(20)","service","DEFAULT 'angelegt' NOT NULL ");
  $this->CheckColumn("artikel","INT(11)","service","DEFAULT '0' NOT NULL");
  $this->CheckColumn("seriennummer","VARCHAR(255)","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("antwortpermail","TINYINT(1)","service","DEFAULT '0' NOT NULL");

  $this->CheckColumn("antwortankundenempfaenger","VARCHAR(64)","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("antwortankundenkopie","VARCHAR(64)","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("antwortankundenblindkopie","VARCHAR(64)","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("antwortankundenbetreff","VARCHAR(64)","service","DEFAULT '' NOT NULL ");
  $this->CheckColumn("antwortankunden","TEXT","service","DEFAULT '' NOT NULL ");

  $this->CheckTable("dta_datei_verband");
  $this->CheckColumn("id","int(11)","dta_datei_verband","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("datum","DATE","dta_datei_verband");
  $this->CheckColumn("bemerkung","TEXT","dta_datei_verband","DEFAULT '' NOT NULL");
  $this->CheckColumn("dateiname","VARCHAR(255)","dta_datei_verband","DEFAULT '' NOT NULL");
  $this->CheckColumn("email","VARCHAR(255)","dta_datei_verband","DEFAULT '' NOT NULL");
  $this->CheckColumn("betreff","VARCHAR(255)","dta_datei_verband","DEFAULT '' NOT NULL");
  $this->CheckColumn("nachricht","TEXT","dta_datei_verband","DEFAULT '' NOT NULL");
  $this->CheckColumn("datum_versendet","DATE","dta_datei_verband");
  $this->CheckColumn("status","VARCHAR(255)","dta_datei_verband","DEFAULT '' NOT NULL");
  $this->CheckColumn("verband","INT(11)","dta_datei_verband","DEFAULT '0' NOT NULL");
  $this->CheckColumn("projekt","INT(11)","dta_datei_verband","DEFAULT '0' NOT NULL");
  $this->CheckColumn("variante","INT(11)","dta_datei_verband","DEFAULT '0' NOT NULL");
  $this->CheckColumn("partnerid","VARCHAR(255)","dta_datei_verband","DEFAULT '' NOT NULL");
  $this->CheckColumn("kundennummer","VARCHAR(255)","dta_datei_verband","DEFAULT '' NOT NULL");

  $this->CheckTable("zahlungsavis");
  $this->CheckColumn("id","int(11)","zahlungsavis","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("datum","DATE","zahlungsavis");
  $this->CheckColumn("adresse","INT(11)","zahlungsavis","DEFAULT '0' NOT NULL");
  $this->CheckColumn("versendet","tinyint(1)","zahlungsavis","DEFAULT '0' NOT NULL");
  $this->CheckColumn("versendet_am","DATE","zahlungsavis");
  $this->CheckColumn("versendet_per","VARCHAR(64)","zahlungsavis","DEFAULT '' NOT NULL");
  $this->CheckColumn("ersteller","VARCHAR(64)","zahlungsavis","DEFAULT '' NOT NULL");
  $this->CheckColumn("bic","VARCHAR(64)","zahlungsavis","DEFAULT '' NOT NULL");
  $this->CheckColumn("iban","VARCHAR(64)","zahlungsavis","DEFAULT '' NOT NULL");
  $this->CheckColumn("projekt","INT(11)","zahlungsavis","DEFAULT '0' NOT NULL");
  $this->CheckColumn("bemerkung","VARCHAR(255)","zahlungsavis","DEFAULT '' NOT NULL");
  $this->CheckColumn("dta_datei","INT(11)","zahlungsavis","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag","DECIMAL(10,2)","zahlungsavis","DEFAULT '0' NOT NULL");

  $this->CheckTable("zahlungsavis_rechnung");
  $this->CheckColumn("id","int(11)","zahlungsavis_rechnung","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("zahlungsavis","INT(11)","zahlungsavis_rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rechnung","INT(11)","zahlungsavis_rechnung","DEFAULT '0' NOT NULL");

  $this->CheckTable("zahlungsavis_gutschrift");
  $this->CheckColumn("id","int(11)","zahlungsavis_gutschrift","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("zahlungsavis","INT(11)","zahlungsavis_gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gutschrift","INT(11)","zahlungsavis_gutschrift","DEFAULT '0' NOT NULL");


  $this->CheckTable("logfile");
  $this->CheckColumn("id","int(11)","logfile","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("meldung","TEXT","logfile","DEFAULT '' NOT NULL");
  $this->CheckColumn("dump","TEXT","logfile","DEFAULT '' NOT NULL");
  $this->CheckColumn("module","VARCHAR(64)","logfile","DEFAULT '' NOT NULL");
  $this->CheckColumn("action","VARCHAR(64)","logfile","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(64)","logfile","DEFAULT '' NOT NULL");
  $this->CheckColumn("funktionsname","VARCHAR(64)","logfile","DEFAULT '' NOT NULL");
  $this->CheckColumn("datum","DATETIME","logfile");


  $this->CheckTable("protokoll");
  $this->CheckColumn("id","int(11)","protokoll","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("meldung","TEXT","protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("dump","TEXT","protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("module","VARCHAR(64)","protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("action","VARCHAR(64)","protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(64)","protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("funktionsname","VARCHAR(64)","protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("datum","DATETIME","protokoll");
  $this->CheckColumn("parameter","int(11)","protokoll","DEFAULT '0' NOT NULL");
  $this->CheckColumn("argumente","TEXT","protokoll","DEFAULT '' NOT NULL");



  $this->CheckTable("adapterbox_log");
  $this->CheckColumn("id","int(11)","adapterbox_log","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("ip","VARCHAR(64)","adapterbox_log","DEFAULT '' NOT NULL");
  $this->CheckColumn("meldung","VARCHAR(64)","adapterbox_log","DEFAULT '' NOT NULL");
  $this->CheckColumn("seriennummer","VARCHAR(64)","adapterbox_log","DEFAULT '' NOT NULL");
  $this->CheckColumn("device","VARCHAR(64)","adapterbox_log","DEFAULT '' NOT NULL");
  $this->CheckColumn("datum","DATETIME","adapterbox_log");



  $this->CheckTable("stechuhr");
  $this->CheckColumn("id","int(11)","stechuhr","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("datum","DATETIME","stechuhr");
  $this->CheckColumn("adresse","INT(11)","stechuhr","DEFAULT '0' NOT NULL");
  $this->CheckColumn("user","INT(11)","stechuhr","DEFAULT '0' NOT NULL");
  $this->CheckColumn("kommen","tinyint(1)","stechuhr","DEFAULT '0' NOT NULL");
  $this->CheckColumn("uebernommen","tinyint(1)","stechuhr","DEFAULT '0' NOT NULL");

  $this->CheckColumn("deckungsbeitragcalc","TINYINT(1)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("deckungsbeitrag","DECIMAL(10,2)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("erloes_netto","DECIMAL(10,2)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("umsatz_netto","DECIMAL(10,2)","auftrag","DEFAULT '0' NOT NULL");

  $this->CheckColumn("deckungsbeitragcalc","TINYINT(1)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("deckungsbeitrag","DECIMAL(10,2)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("erloes_netto","DECIMAL(10,2)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("umsatz_netto","DECIMAL(10,2)","angebot","DEFAULT '0' NOT NULL");

  $this->CheckColumn("deckungsbeitragcalc","TINYINT(1)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("deckungsbeitrag","DECIMAL(10,2)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("umsatz_netto","DECIMAL(10,2)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("erloes_netto","DECIMAL(10,2)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mahnwesenfestsetzen","tinyint(1)","rechnung","DEFAULT '0' NOT NULL");

  $this->CheckColumn("deckungsbeitragcalc","TINYINT(1)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("deckungsbeitrag","DECIMAL(10,2)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("erloes_netto","DECIMAL(10,2)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("umsatz_netto","DECIMAL(10,2)","gutschrift","DEFAULT '0' NOT NULL");


  $this->CheckTable("umsatzstatistik");
  $this->CheckColumn("user","INT(11)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("adresse","INT(11)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("objekt","VARCHAR(64)","umsatzstatistik","DEFAULT '' NOT NULL");
  $this->CheckColumn("belegnr","VARCHAR(64)","umsatzstatistik","DEFAULT '' NOT NULL");
  $this->CheckColumn("kundennummer","VARCHAR(64)","umsatzstatistik","DEFAULT '' NOT NULL");
  $this->CheckColumn("name","VARCHAR(64)","umsatzstatistik","DEFAULT '' NOT NULL");
  $this->CheckColumn("parameter","INT(11)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_netto","DECIMAL(10,2)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_brutto","DECIMAL(10,2)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("erloes_netto","DECIMAL(10,2)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("deckungsbeitrag","DECIMAL(10,2)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("datum","DATE","umsatzstatistik");
  $this->CheckColumn("waehrung","VARCHAR(3)","umsatzstatistik","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("gruppe","INT(11)","umsatzstatistik","DEFAULT '0' NOT NULL");
  $this->CheckColumn("projekt","INT(11)","umsatzstatistik","DEFAULT '0' NOT NULL");

  $this->CheckTable("vertreterumsatz");
  $this->CheckColumn("vertriebid","INT(11)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("userid","INT(11)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("adresse","INT(11)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("objekt","VARCHAR(64)","vertreterumsatz","DEFAULT '' NOT NULL");
  $this->CheckColumn("belegnr","VARCHAR(64)","vertreterumsatz","DEFAULT '' NOT NULL");
  $this->CheckColumn("name","VARCHAR(64)","vertreterumsatz","DEFAULT '' NOT NULL");
  $this->CheckColumn("parameter","INT(11)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_netto","DECIMAL(10,2)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_brutto","DECIMAL(10,2)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("erloes_netto","DECIMAL(10,2)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("deckungsbeitrag","DECIMAL(10,2)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("datum","DATE","vertreterumsatz");
  $this->CheckColumn("waehrung","VARCHAR(3)","vertreterumsatz","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("gruppe","INT(11)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("projekt","INT(11)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("provision","DECIMAL(10,2)","vertreterumsatz","DEFAULT '0' NOT NULL");
  $this->CheckColumn("provision_summe","DECIMAL(10,2)","vertreterumsatz","DEFAULT '0' NOT NULL");



  $this->CheckTable("autorechnungsdruck");
  $this->CheckColumn("id","int(11)","autorechnungsdruck","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("datum","DATE","autorechnungsdruck");
  $this->CheckColumn("erstellt_von","varchar(255)","autorechnungsdruck","DEFAULT '' NOT NULL");
  $this->CheckColumn("art","int(1)","autorechnungsdruck","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gruppe","int(11)","autorechnungsdruck","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gesperrt","int(1)","autorechnungsdruck","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gemailt","int(1)","autorechnungsdruck","DEFAULT '0' NOT NULL");

  $this->CheckTable("autorechnungsdruck_rechnung");
  $this->CheckColumn("id","int(11)","autorechnungsdruck_rechnung","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("autorechnungsdruck","int(11)","autorechnungsdruck_rechnung");
  $this->CheckColumn("rechnung","int(11)","autorechnungsdruck_rechnung");

  $this->CheckColumn("lieferdatum","DATE","angebot");
  $this->CheckColumn("lieferdatum","DATE","auftrag");
  $this->CheckColumn("tatsaechlicheslieferdatum","DATE","auftrag");
  $this->CheckColumn("liefertermin_ok","int(1)","auftrag","DEFAULT '1' NOT NULL");
  $this->CheckColumn("teillieferung_moeglich","int(1)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("kreditlimit_ok","int(1)","auftrag","DEFAULT '1' NOT NULL");
  $this->CheckColumn("kreditlimit_freigabe","int(1)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("liefersperre_ok","int(1)","auftrag","DEFAULT '1' NOT NULL");
  $this->CheckColumn("teillieferungvon","int(11)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("teillieferungnummer","int(11)","auftrag","DEFAULT '0' NOT NULL");

  $this->CheckColumn("vertriebid","INT(11)","anfrage");
  $this->CheckColumn("vertriebid","INT(11)","angebot");
  $this->CheckColumn("vertriebid","INT(11)","auftrag");
  $this->CheckColumn("vertriebid","INT(11)","rechnung");
  $this->CheckColumn("vertriebid","INT(11)","lieferschein");
  $this->CheckColumn("vertriebid","INT(11)","gutschrift");

  $this->CheckColumn("aktion","varchar(64)","anfrage","DEFAULT '' NOT NULL");
  $this->CheckColumn("aktion","varchar(64)","angebot","DEFAULT '' NOT NULL");
  $this->CheckColumn("aktion","varchar(64)","auftrag","DEFAULT '' NOT NULL");
  $this->CheckColumn("aktion","varchar(64)","rechnung","DEFAULT '' NOT NULL");
  $this->CheckColumn("aktion","varchar(64)","gutschrift","DEFAULT '' NOT NULL");



  $this->CheckColumn("vertrieb","VARCHAR(255)","anfrage","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(255)","anfrage","DEFAULT '' NOT NULL");
  $this->CheckColumn("vertrieb","VARCHAR(255)","angebot","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(255)","angebot","DEFAULT '' NOT NULL");
  $this->CheckColumn("vertrieb","VARCHAR(255)","auftrag","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(255)","auftrag","DEFAULT '' NOT NULL");
  $this->CheckColumn("vertrieb","VARCHAR(255)","rechnung","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(255)","rechnung","DEFAULT '' NOT NULL");
  $this->CheckColumn("vertrieb","VARCHAR(255)","lieferschein","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(255)","lieferschein","DEFAULT '' NOT NULL");
  $this->CheckColumn("vertrieb","VARCHAR(255)","gutschrift","DEFAULT '' NOT NULL");
  $this->CheckColumn("bearbeiter","VARCHAR(255)","gutschrift","DEFAULT '' NOT NULL");

  $this->CheckColumn("provision","DECIMAL(10,2)","angebot");
  $this->CheckColumn("provision_summe","DECIMAL(10,2)","angebot");
  $this->CheckColumn("provision","DECIMAL(10,2)","auftrag");
  $this->CheckColumn("provision_summe","DECIMAL(10,2)","auftrag");
  $this->CheckColumn("provision","DECIMAL(10,2)","rechnung");
  $this->CheckColumn("provision_summe","DECIMAL(10,2)","rechnung");
  $this->CheckColumn("provision","DECIMAL(10,2)","gutschrift");
  $this->CheckColumn("provision_summe","DECIMAL(10,2)","gutschrift");

  $this->CheckColumn("beschreibung","text","kalender_event");
  $this->CheckColumn("ort","text","kalender_event");
  $this->CheckColumn("mlmaktiv","int(1)","adresse");
  $this->CheckColumn("mlmvertragsbeginn","DATE","adresse");
  $this->CheckColumn("mlmlizenzgebuehrbis","DATE","adresse");
  $this->CheckColumn("mlmfestsetzenbis","DATE","adresse");
  $this->CheckColumn("rechnungsdatum","DATE","verbindlichkeit");
  $this->CheckColumn("rechnungsfreigabe","TINYINT(1)","verbindlichkeit","DEFAULT '0' NOT NULL");
  $this->CheckColumn("kostenstelle","VARCHAR(255)","verbindlichkeit");
  $this->CheckColumn("beschreibung","VARCHAR(255)","verbindlichkeit");
  $this->CheckColumn("sachkonto","VARCHAR(64)","verbindlichkeit");
  $this->CheckColumn("art","VARCHAR(64)","verbindlichkeit","DEFAULT '' NOT NULL");
  $this->CheckColumn("verwendungszweck","VARCHAR(255)","verbindlichkeit");
  $this->CheckColumn("dta_datei","INT(11)","verbindlichkeit","DEFAULT '0' NOT NULL");
  $this->CheckColumn("frachtkosten","DECIMAL(10,2)","verbindlichkeit","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mlmfestsetzen","int(1)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mlmmindestpunkte","int(1)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mlmwartekonto","Decimal(10,2)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mlmdirektpraemie","DECIMAL(10,2)","artikel");
  $this->CheckColumn("keineeinzelartikelanzeigen","TINYINT(1)","artikel","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mindesthaltbarkeitsdatum","int(1)","artikel","DEFAULT '0' NOT NULL");
  $this->CheckColumn("letzteseriennummer","VARCHAR(255)","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("individualartikel","int(1)","artikel","DEFAULT '0' NOT NULL");
  $this->CheckColumn("keinrabatterlaubt","int(1)","artikel");
  $this->CheckColumn("rabatt","int(1)","artikel","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rabatt_prozent","DECIMAL(10,2)","artikel");
  $this->CheckColumn("konto","INT(11)","dta_datei","DEFAULT '0' NOT NULL");

  $this->CheckColumn("geraet","tinyint(1)","artikel","DEFAULT '0' NOT NULL");
  $this->CheckColumn("serviceartikel","tinyint(1)","artikel","DEFAULT '0' NOT NULL");

  $this->CheckColumn("abweichende_rechnungsadresse","int(1)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rechnung_vorname","varchar(255)","adresse");
  $this->CheckColumn("rechnung_name","varchar(255)","adresse");
  $this->CheckColumn("rechnung_titel","varchar(255)","adresse");
  $this->CheckColumn("rechnung_typ","varchar(255)","adresse");
  $this->CheckColumn("rechnung_strasse","varchar(255)","adresse");
  $this->CheckColumn("rechnung_ort","varchar(255)","adresse");
  $this->CheckColumn("rechnung_plz","varchar(255)","adresse");
  $this->CheckColumn("rechnung_ansprechpartner","varchar(255)","adresse");
  $this->CheckColumn("rechnung_land","varchar(255)","adresse");
  $this->CheckColumn("rechnung_abteilung","varchar(255)","adresse");
  $this->CheckColumn("rechnung_unterabteilung","varchar(255)","adresse");
  $this->CheckColumn("rechnung_adresszusatz","varchar(255)","adresse");
  $this->CheckColumn("rechnung_telefon","varchar(255)","adresse");
  $this->CheckColumn("rechnung_telefax","varchar(255)","adresse");
  $this->CheckColumn("rechnung_anschreiben","varchar(255)","adresse");
  $this->CheckColumn("rechnung_email","varchar(255)","adresse");

  $this->CheckColumn("wiedervorlage","int(1)","warteschlangen","DEFAULT '0' NOT NULL");

  $this->CheckColumn("geburtstag","DATE","adresse");
  $this->CheckColumn("rolledatum","DATE","adresse");
  $this->CheckColumn("liefersperre","int(1)","adresse");
  $this->CheckColumn("liefersperregrund","text","adresse");
  $this->CheckColumn("mlmpositionierung","varchar(255)","adresse");
  $this->CheckColumn("steuernummer","varchar(255)","adresse");
  $this->CheckColumn("steuerbefreit","int(1)","adresse");
  $this->CheckColumn("mlmmitmwst","int(1)","adresse");
  $this->CheckColumn("mlmabrechnung","varchar(255)","adresse");
  $this->CheckColumn("mlmwaehrungauszahlung","varchar(255)","adresse");
  $this->CheckColumn("mlmauszahlungprojekt","int(11)","adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("sponsor","int(11)","adresse");
  $this->CheckColumn("geworbenvon","int(11)","adresse");
  $this->CheckColumn("passwordmd5","varchar(255)","user");
  $this->CheckColumn("projekt_bevorzugen","tinyint(1)","user","DEFAULT '0' NOT NULL");
  $this->CheckColumn("email_bevorzugen","tinyint(1)","user","DEFAULT '1' NOT NULL");
  $this->CheckColumn("projekt","INT(11)","user","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rfidtag","varchar(64)","user","DEFAULT '' NOT NULL");
  $this->CheckColumn("vorlage","varchar(255)","user");
  $this->CheckColumn("kalender_passwort","varchar(255)","user");
  $this->CheckColumn("kalender_ausblenden","int(1)","user","DEFAULT '0' NOT NULL");
  $this->CheckColumn("kalender_aktiv","int(1)","user");
  $this->CheckColumn("gpsstechuhr","int(1)","user");
  $this->CheckColumn("standardetikett","int(11)","user","DEFAULT '0' NOT NULL");
  $this->CheckColumn("standardfax","int(11)","user","DEFAULT '0' NOT NULL");
  $this->CheckColumn("internebezeichnung","varchar(255)","user");
  $this->CheckColumn("logfile","text","adresse");
  $this->CheckColumn("kalender_aufgaben","int(1)","adresse");     
  $this->CheckColumn("adresse","int(11)","arbeitsnachweis_position");     
  $this->CheckColumn("dateiid","int(11)","dokumente_send");       
  $this->CheckColumn("autolagersperre","int(1)","lager_platz","DEFAULT '0' NOT NULL");    
  $this->CheckColumn("verbrauchslager","int(1)","lager_platz","DEFAULT '0' NOT NULL");    
  $this->CheckColumn("sperrlager","int(1)","lager_platz","DEFAULT '0' NOT NULL"); 
  $this->CheckColumn("verrechnungskontoreisekosten","int(11)","adresse","DEFAULT '0' NOT NULL");  

  $this->CheckColumn("status","varchar(255)","zeiterfassung");    
  $this->CheckColumn("gps","varchar(1024)","zeiterfassung");      
  $this->CheckColumn("arbeitsnachweispositionid","int(11)","zeiterfassung","DEFAULT '0' NOT NULL");       
  $this->CheckColumn("importtrennzeichen","varchar(255)","konten");       
  $this->CheckColumn("codierung","varchar(255)","konten");        
  $this->CheckColumn("importerstezeilenummer","int(11)","konten");        
  $this->CheckColumn("importdatenmaskierung","varchar(255)","konten");    
  $this->CheckColumn("glaeubiger","varchar(64)","konten");        

  $this->CheckColumn("importfelddatum","varchar(255)","konten");  
  $this->CheckColumn("importfelddatumformat","varchar(255)","konten");    
  $this->CheckColumn("importfelddatumformatausgabe","varchar(255)","konten");     

  $this->CheckColumn("importfeldbetrag","varchar(255)","konten"); 
  $this->CheckColumn("importfeldbetragformat","varchar(255)","konten");   

  $this->CheckColumn("importfeldbuchungstext","varchar(255)","konten");   
  $this->CheckColumn("importfeldbuchungstextformat","varchar(255)","konten");     

  $this->CheckColumn("importfeldwaehrung","varchar(255)","konten");       
  $this->CheckColumn("importfeldwaehrungformat","varchar(255)","konten"); 

  $this->CheckColumn("importfeldhabensollkennung","varchar(10)","konten","DEFAULT '' NOT NULL");        
  $this->CheckColumn("importfeldkennunghaben","varchar(10)","konten","DEFAULT '' NOT NULL");        
  $this->CheckColumn("importfeldkennungsoll","varchar(10)","konten","DEFAULT '' NOT NULL");        
  $this->CheckColumn("importextrahabensoll","tinyint(1)","konten","DEFAULT '0' NOT NULL");        
  $this->CheckColumn("importfeldhaben","varchar(10)","konten","DEFAULT '' NOT NULL");        
  $this->CheckColumn("importfeldsoll","varchar(10)","konten","DEFAULT '' NOT NULL");        

  $this->CheckColumn("importletztenzeilenignorieren","int(11)","konten","DEFAULT '0' NOT NULL");        

  $this->CheckColumn("wert","text","stueckliste","DEFAULT '' NOT NULL");  
  $this->CheckColumn("bauform","text","stueckliste","DEFAULT '' NOT NULL");       

  $this->CheckColumn("artikelporto","int(11)","shopexport","DEFAULT '0' NOT NULL");       
  $this->CheckColumn("artikelnachnahme","int(11)","shopexport","DEFAULT '0' NOT NULL");   
  $this->CheckColumn("artikelimport","int(1)","shopexport","DEFAULT '0' NOT NULL");       
  $this->CheckColumn("artikelimporteinzeln","int(1)","shopexport","DEFAULT '0' NOT NULL");        
  $this->CheckColumn("autoabgleicherlaubt","int(1)","artikel","DEFAULT '0' NOT NULL");    
  $this->CheckColumn("pseudopreis","DECIMAL(10,2)","artikel");    
  $this->CheckColumn("freigabenotwendig","int(1)","artikel","DEFAULT '0' NOT NULL");      
  $this->CheckColumn("freigaberegel","varchar(255)","artikel","DEFAULT '' NOT NULL");     

  $this->CheckColumn("demomodus","tinyint(1)","shopexport","DEFAULT '0' NOT NULL");       

  $this->CheckColumn("aktiv","int(1)","shopexport","DEFAULT '1' NOT NULL");       
  $this->CheckColumn("lagerexport","int(1)","shopexport","DEFAULT '1' NOT NULL"); 
  $this->CheckColumn("artikelexport","int(1)","shopexport","DEFAULT '1' NOT NULL");       

  $this->CheckColumn("tomail","varchar(255)","drucker","DEFAULT '' NOT NULL");    
  $this->CheckColumn("tomailtext","text","drucker","DEFAULT '' NOT NULL");        
  $this->CheckColumn("tomailsubject","text","drucker","DEFAULT '' NOT NULL");     
  $this->CheckColumn("adapterboxip","varchar(255)","drucker","DEFAULT '' NOT NULL");      
  $this->CheckColumn("adapterboxseriennummer","varchar(255)","drucker","DEFAULT '' NOT NULL");    
  $this->CheckColumn("adapterboxpasswort","varchar(255)","drucker","DEFAULT '' NOT NULL");        
  $this->CheckColumn("anbindung","varchar(255)","drucker","DEFAULT '' NOT NULL"); 
  $this->CheckColumn("art","int(1)","drucker","DEFAULT '0' NOT NULL");    
  $this->CheckColumn("faxserver","int(1)","drucker","DEFAULT '0' NOT NULL");      

  $this->CheckColumn("anzeige_verrechnungsart","int(1)","arbeitsnachweis","DEFAULT '0' NOT NULL");

  $this->CheckColumn("ust_befreit","int(1)","arbeitsnachweis","NOT NULL");
  $this->CheckColumn("ust_befreit","int(1)","reisekosten","NOT NULL");
  $this->CheckColumn("ust_befreit","int(1)","lieferschein","NOT NULL");
  $this->CheckColumn("ust_befreit","int(1)","produktion","NOT NULL");
  $this->CheckColumn("ust_befreit","int(1)","bestellung","NOT NULL");

  $this->CheckColumn("keinsteuersatz","int(1)","angebot");
  $this->CheckColumn("anfrageid","int(11)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("anfrageid","int(11)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gruppe","int(11)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gruppe","int(11)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gruppe","int(11)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gruppe","int(11)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("shopextid","varchar(1024)","auftrag","DEFAULT '' NOT NULL");
  $this->CheckColumn("shopextstatus","varchar(1024)","auftrag","DEFAULT '' NOT NULL");

  $this->CheckColumn("nachbestellt","int(1)","artikel");
  $this->CheckColumn("ean","varchar(1024)","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("mlmpunkte","int(11)","artikel");
  $this->CheckColumn("mlmbonuspunkte","int(11)","artikel");
  $this->CheckColumn("mlmdirektpraemie","DECIMAL(10,2)","artikel");
  $this->CheckColumn("mlmkeinepunkteeigenkauf","int(1)","artikel");

  $this->CheckColumn("bonuspunktekomplett","DECIMAL(10,2)","mlm_baum_cache","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gesamtauszahlung","DECIMAL(10,2)","mlm_baum_cache","DEFAULT '0' NOT NULL");
  $this->CheckColumn("ur_lizenznehmer","int(11)","mlm_positionen","DEFAULT '0' NOT NULL");

  $this->CheckColumn("shop2","int(11)","artikel");
  $this->CheckColumn("shop3","int(11)","artikel");

  $this->CheckColumn("punkte","int(11)","rechnung_position");
  $this->CheckColumn("bonuspunkte","int(11)","rechnung_position");
  $this->CheckColumn("mlmdirektpraemie","DECIMAL(10,2)","rechnung_position");
  $this->CheckColumn("mlm_abgerechnet","int(1)","rechnung_position");

  $this->CheckColumn("punkte","int(11)","angebot_position");
  $this->CheckColumn("bonuspunkte","int(11)","angebot_position");
  $this->CheckColumn("mlmdirektpraemie","DECIMAL(10,2)","angebot_position");

  $this->CheckColumn("punkte","int(11)","auftrag_position");
  $this->CheckColumn("bonuspunkte","int(11)","auftrag_position");
  $this->CheckColumn("mlmdirektpraemie","DECIMAL(10,2)","auftrag_position");

  $this->CheckColumn("keinrabatterlaubt","int(1)","angebot_position");
  $this->CheckColumn("keinrabatterlaubt","int(1)","auftrag_position");
  $this->CheckColumn("keinrabatterlaubt","int(1)","rechnung_position");
  $this->CheckColumn("keinrabatterlaubt","int(1)","gutschrift_position");


  $this->CheckColumn("punkte","int(11)","rechnung");
  $this->CheckColumn("bonuspunkte","int(11)","rechnung");
  $this->CheckColumn("provdatum","DATE","rechnung");


  $this->CheckColumn("ihrebestellnummer","varchar(255)","rechnung");
  $this->CheckColumn("ihrebestellnummer","varchar(255)","lieferschein");
  $this->CheckColumn("ihrebestellnummer","varchar(255)","auftrag");
  $this->CheckColumn("ihrebestellnummer","varchar(255)","gutschrift");

  $this->CheckColumn("anschreiben","varchar(255)","anfrage");
  $this->CheckColumn("anschreiben","varchar(255)","angebot");
  $this->CheckColumn("anschreiben","varchar(255)","auftrag");
  $this->CheckColumn("anschreiben","varchar(255)","rechnung");
  $this->CheckColumn("anschreiben","varchar(255)","lieferschein");
  $this->CheckColumn("anschreiben","varchar(255)","gutschrift");
  $this->CheckColumn("anschreiben","varchar(255)","bestellung");
  $this->CheckColumn("anschreiben","varchar(255)","produktion");
  $this->CheckColumn("anschreiben","varchar(255)","arbeitsnachweis");
  $this->CheckColumn("anschreiben","varchar(255)","anfrage");

  $this->CheckColumn("usereditid","int(11)","anfrage");
  $this->CheckColumn("usereditid","int(11)","angebot");
  $this->CheckColumn("usereditid","int(11)","auftrag");
  $this->CheckColumn("usereditid","int(11)","rechnung");
  $this->CheckColumn("usereditid","int(11)","lieferschein");
  $this->CheckColumn("usereditid","int(11)","gutschrift");
  $this->CheckColumn("usereditid","int(11)","bestellung");
  $this->CheckColumn("usereditid","int(11)","produktion");
  $this->CheckColumn("usereditid","int(11)","arbeitsnachweis");
  $this->CheckColumn("usereditid","int(11)","reisekosten");
  $this->CheckColumn("usereditid","int(11)","inventur");
  $this->CheckColumn("usereditid","int(11)","artikel");
  $this->CheckColumn("usereditid","int(11)","adresse");

  $this->CheckColumn("useredittimestamp","timestamp","anfrage");
  $this->CheckColumn("useredittimestamp","timestamp","angebot");
  $this->CheckColumn("useredittimestamp","timestamp","auftrag");
  $this->CheckColumn("useredittimestamp","timestamp","rechnung");
  $this->CheckColumn("useredittimestamp","timestamp","lieferschein");
  $this->CheckColumn("useredittimestamp","timestamp","gutschrift");
  $this->CheckColumn("useredittimestamp","timestamp","bestellung");
  $this->CheckColumn("useredittimestamp","timestamp","produktion");
  $this->CheckColumn("useredittimestamp","timestamp","arbeitsnachweis");
  $this->CheckColumn("useredittimestamp","timestamp","reisekosten");
  $this->CheckColumn("useredittimestamp","timestamp","inventur");
  $this->CheckColumn("useredittimestamp","timestamp","artikel");
  $this->CheckColumn("useredittimestamp","timestamp","adresse");


  $this->CheckColumn("realrabatt","DECIMAL(10,2)","anfrage");
  $this->CheckColumn("realrabatt","DECIMAL(10,2)","angebot");
  $this->CheckColumn("realrabatt","DECIMAL(10,2)","auftrag");
  $this->CheckColumn("realrabatt","DECIMAL(10,2)","rechnung");
  $this->CheckColumn("realrabatt","DECIMAL(10,2)","gutschrift");

  $this->CheckColumn("rabatt","DECIMAL(10,2)","anfrage");
  $this->CheckColumn("rabatt","DECIMAL(10,2)","angebot");
  $this->CheckColumn("rabatt","DECIMAL(10,2)","auftrag");
  $this->CheckColumn("rabatt","DECIMAL(10,2)","rechnung");
  $this->CheckColumn("rabatt","DECIMAL(10,2)","gutschrift");
  $this->CheckColumn("rabatt","DECIMAL(10,2)","adresse");
  $this->CheckColumn("provision","DECIMAL(10,2)","adresse");
  $this->CheckColumn("rabattinformation","TEXT","adresse");

  $this->CheckColumn("einzugsdatum","DATE","auftrag");
  $this->CheckColumn("einzugsdatum","DATE","rechnung");

  $this->CheckColumn("grundrabatt","DECIMAL(10,2)","anfrage_position");
  $this->CheckColumn("grundrabatt","DECIMAL(10,2)","angebot_position");
  $this->CheckColumn("grundrabatt","DECIMAL(10,2)","auftrag_position");
  $this->CheckColumn("grundrabatt","DECIMAL(10,2)","rechnung_position");
  $this->CheckColumn("grundrabatt","DECIMAL(10,2)","gutschrift_position");


  $this->CheckColumn("rabattsync","INT(1)","anfrage_position");
  $this->CheckColumn("rabattsync","INT(1)","angebot_position");
  $this->CheckColumn("rabattsync","INT(1)","auftrag_position");
  $this->CheckColumn("rabattsync","INT(1)","rechnung_position");
  $this->CheckColumn("rabattsync","INT(1)","gutschrift_position");


  for($ij=1;$ij<=15;$ij++)
  {
    $this->CheckColumn("bestellung$ij","INT(1)","verbindlichkeit","DEFAULT '0' NOT NULL");
    $this->CheckColumn("bestellung".$ij."betrag","DECIMAL(10,2)","verbindlichkeit","DEFAULT '0' NOT NULL");
    $this->CheckColumn("bestellung".$ij."bemerkung","VARCHAR(255)","verbindlichkeit","DEFAULT '' NOT NULL");
  }


  for($ij=1;$ij<=5;$ij++)
  {
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","anfrage");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","angebot");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","auftrag");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","rechnung");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","adresse");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","gutschrift");

    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","anfrage_position");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","angebot_position");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","auftrag_position");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","rechnung_position");
    $this->CheckColumn("rabatt$ij","DECIMAL(10,2)","gutschrift_position");
  }




  $this->CheckColumn("shop","int(11)","auftrag","DEFAULT '0' NOT NULL");

  $this->CheckColumn("forderungsverlust_datum","DATE","rechnung");
  $this->CheckColumn("forderungsverlust_betrag","DECIMAL(10,2)","rechnung");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","rechnung","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","rechnung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","rechnung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","rechnung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","rechnung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","rechnung","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(3)","dta","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(3)","verbindlichkeit","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("verbindlichkeit","INT(11)","dta","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rechnung","INT(11)","dta","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mandatsreferenzaenderung","TINYINT(1)","dta","DEFAULT '0' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","angebot","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","angebot","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","angebot","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","angebot","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","angebot","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","angebot","DEFAULT 'EUR' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","auftrag","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","auftrag","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","auftrag","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","auftrag","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","auftrag","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","auftrag","DEFAULT 'EUR' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","gutschrift","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","gutschrift","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","gutschrift","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","gutschrift","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","gutschrift","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","gutschrift","DEFAULT 'EUR' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","bestellung","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","bestellung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","bestellung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","bestellung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","bestellung","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","bestellung","DEFAULT 'EUR' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","produktion","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","produktion","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","produktion","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","produktion","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","produktion","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","produktion","DEFAULT 'EUR' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","inventur","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","inventur","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","inventur","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","inventur","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","inventur","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","inventur","DEFAULT 'EUR' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","anfrage","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","anfrage","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","anfrage","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","anfrage","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","anfrage","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","anfrage","DEFAULT 'EUR' NOT NULL");



  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","reisekosten","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","reisekosten","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","reisekosten","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","reisekosten","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","reisekosten","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(255)","reisekosten","DEFAULT 'EUR' NOT NULL");


  $this->CheckColumn("breite_position","INT(11)","firmendaten","DEFAULT '10' NOT NULL");
  $this->CheckColumn("breite_menge","INT(11)","firmendaten","DEFAULT '10' NOT NULL");
  $this->CheckColumn("breite_nummer","INT(11)","firmendaten","DEFAULT '20' NOT NULL");
  $this->CheckColumn("breite_einheit","INT(11)","firmendaten","DEFAULT '15' NOT NULL");

  $this->CheckColumn("skonto_ueberweisung_ueberziehen","INT(11)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","firmendaten","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","firmendaten","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","firmendaten","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","firmendaten","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","firmendaten","DEFAULT '7.0' NOT NULL");

  $this->CheckColumn("steuersatz_normal","DECIMAL(10,2)","projekt","DEFAULT '19.0' NOT NULL");
  $this->CheckColumn("steuersatz_zwischen","DECIMAL(10,2)","projekt","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_ermaessigt","DECIMAL(10,2)","projekt","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_starkermaessigt","DECIMAL(10,2)","projekt","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("steuersatz_dienstleistung","DECIMAL(10,2)","projekt","DEFAULT '7.0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(3)","projekt","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("eigenesteuer","INT(1)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("druckerlogistikstufe1","INT(11)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("druckerlogistikstufe2","INT(11)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("selbstabholermail","TINYINT(1)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("eanherstellerscan","TINYINT(1)","projekt","DEFAULT '0' NOT NULL");

  $this->CheckColumn("kleinunternehmer","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mahnwesenmitkontoabgleich","int(1)","firmendaten","DEFAULT '1' NOT NULL");
  $this->CheckColumn("porto_berechnen","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("immernettorechnungen","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schnellanlegen","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("bestellvorschlaggroessernull","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("versand_gelesen","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("versandart","varchar(64)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("zahlungsweise","varchar(64)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("zahlung_lastschrift_konditionen","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("breite_artikelbeschreibung","tinyint(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("devicekey","varchar(255)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("deviceserials","TEXT","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("deviceenable","tinyint(1)","firmendaten","DEFAULT '0' NOT NULL");

  $this->CheckColumn("etikettendrucker_wareneingang","int(11)","firmendaten","DEFAULT '0' NOT NULL");

  $this->CheckColumn("waehrung","VARCHAR(255)","firmendaten","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("footer_breite1","int(11)","firmendaten","DEFAULT '50' NOT NULL");
  $this->CheckColumn("footer_breite2","int(11)","firmendaten","DEFAULT '35' NOT NULL");
  $this->CheckColumn("footer_breite3","int(11)","firmendaten","DEFAULT '60' NOT NULL");
  $this->CheckColumn("footer_breite4","int(11)","firmendaten","DEFAULT '40' NOT NULL");
  $this->CheckColumn("boxausrichtung","VARCHAR(255)","firmendaten","DEFAULT 'R' NOT NULL");
  $this->CheckColumn("lizenz","TEXT","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("schluessel","TEXT","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("branch","VARCHAR(255)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("version","VARCHAR(255)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("standard_datensaetze_datatables","int(11)","firmendaten","DEFAULT '10' NOT NULL");
  $this->CheckColumn("auftrag_bezeichnung_vertrieb","VARCHAR(64)","firmendaten","DEFAULT 'Vertrieb' NOT NULL");
  $this->CheckColumn("auftrag_bezeichnung_bearbeiter","VARCHAR(64)","firmendaten","DEFAULT 'Bearbeiter' NOT NULL");
  $this->CheckColumn("auftrag_bezeichnung_bestellnummer","VARCHAR(64)","firmendaten","DEFAULT 'Ihre Bestellnummer' NOT NULL");
  $this->CheckColumn("bezeichnungkundennummer","VARCHAR(64)","firmendaten","DEFAULT 'Kundennummer' NOT NULL");
  $this->CheckColumn("bezeichnungstornorechnung","VARCHAR(64)","firmendaten","DEFAULT 'Stornorechnung' NOT NULL");
  $this->CheckColumn("bestellungohnepreis","tinyint(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mysql55","tinyint(1)","firmendaten","DEFAULT '1' NOT NULL");


  $this->CheckColumn("rechnung_gutschrift_ansprechpartner","int(1)","firmendaten","DEFAULT '1' NOT NULL");

  $this->CheckColumn("api_initkey","VARCHAR(1024)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("api_remotedomain","VARCHAR(1024)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("api_eventurl","VARCHAR(1024)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("api_enable","INT(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("api_cleanutf8","tinyint(1)","firmendaten","DEFAULT '1' NOT NULL");
  $this->CheckColumn("api_importwarteschlange","INT(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("api_importwarteschlange_name","VARCHAR(255)","firmendaten","DEFAULT '' NOT NULL");
  //$this->CheckColumn("api_webid_url_adresse","VARCHAR(1024)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("wareneingang_zwischenlager","INT(1)","firmendaten","DEFAULT '1' NOT NULL");

  $this->CheckColumn("modul_mlm","INT(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("modul_verband","INT(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("modul_mhd","INT(1)","firmendaten","DEFAULT '0' NOT NULL");

  $this->CheckColumn("mhd_warnung_tage","int(11)","firmendaten","DEFAULT '3' NOT NULL");

  $this->CheckColumn("mlm_mindestbetrag","DECIMAL(10,2)","firmendaten","DEFAULT '50.0' NOT NULL");
  $this->CheckColumn("mlm_anzahlmonate","int(11)","firmendaten","DEFAULT '11' NOT NULL");
  $this->CheckColumn("mlm_letzter_tag","DATE","firmendaten","");
  $this->CheckColumn("mlm_erster_tag","DATE","firmendaten","");
  $this->CheckColumn("mlm_letzte_berechnung","DATETIME","firmendaten","");
  //              $this->CheckColumn("mlm_zentrale","int(1)","adresse","DEFAULT '0' NOT NULL");

  $this->CheckColumn("mlm_01","DECIMAL(10,2)","firmendaten","DEFAULT '15' NOT NULL");
  $this->CheckColumn("mlm_02","DECIMAL(10,2)","firmendaten","DEFAULT '20' NOT NULL");
  $this->CheckColumn("mlm_03","DECIMAL(10,2)","firmendaten","DEFAULT '28' NOT NULL");
  $this->CheckColumn("mlm_04","DECIMAL(10,2)","firmendaten","DEFAULT '32' NOT NULL");
  $this->CheckColumn("mlm_05","DECIMAL(10,2)","firmendaten","DEFAULT '36' NOT NULL");
  $this->CheckColumn("mlm_06","DECIMAL(10,2)","firmendaten","DEFAULT '40' NOT NULL");
  $this->CheckColumn("mlm_07","DECIMAL(10,2)","firmendaten","DEFAULT '44' NOT NULL");
  $this->CheckColumn("mlm_08","DECIMAL(10,2)","firmendaten","DEFAULT '44' NOT NULL");
  $this->CheckColumn("mlm_09","DECIMAL(10,2)","firmendaten","DEFAULT '44' NOT NULL");
  $this->CheckColumn("mlm_10","DECIMAL(10,2)","firmendaten","DEFAULT '44' NOT NULL");
  $this->CheckColumn("mlm_11","DECIMAL(10,2)","firmendaten","DEFAULT '50' NOT NULL");
  $this->CheckColumn("mlm_12","DECIMAL(10,2)","firmendaten","DEFAULT '54' NOT NULL");
  $this->CheckColumn("mlm_13","DECIMAL(10,2)","firmendaten","DEFAULT '45' NOT NULL");
  $this->CheckColumn("mlm_14","DECIMAL(10,2)","firmendaten","DEFAULT '48' NOT NULL");
  $this->CheckColumn("mlm_15","DECIMAL(10,2)","firmendaten","DEFAULT '60' NOT NULL");

  $this->CheckColumn("mlm_01_punkte","int(11)","firmendaten","DEFAULT '2999' NOT NULL");
  $this->CheckColumn("mlm_02_punkte","int(11)","firmendaten","DEFAULT '3000' NOT NULL");
  $this->CheckColumn("mlm_03_punkte","int(11)","firmendaten","DEFAULT '5000' NOT NULL");
  $this->CheckColumn("mlm_04_punkte","int(11)","firmendaten","DEFAULT '10000' NOT NULL");
  $this->CheckColumn("mlm_05_punkte","int(11)","firmendaten","DEFAULT '15000' NOT NULL");
  $this->CheckColumn("mlm_06_punkte","int(11)","firmendaten","DEFAULT '25000' NOT NULL");
  $this->CheckColumn("mlm_07_punkte","int(11)","firmendaten","DEFAULT '50000' NOT NULL");
  $this->CheckColumn("mlm_08_punkte","int(11)","firmendaten","DEFAULT '100000' NOT NULL");
  $this->CheckColumn("mlm_09_punkte","int(11)","firmendaten","DEFAULT '150000' NOT NULL");
  $this->CheckColumn("mlm_10_punkte","int(11)","firmendaten","DEFAULT '200000' NOT NULL");
  $this->CheckColumn("mlm_11_punkte","int(11)","firmendaten","DEFAULT '250000' NOT NULL");
  $this->CheckColumn("mlm_12_punkte","int(11)","firmendaten","DEFAULT '300000' NOT NULL");
  $this->CheckColumn("mlm_13_punkte","int(11)","firmendaten","DEFAULT '350000' NOT NULL");
  $this->CheckColumn("mlm_14_punkte","int(11)","firmendaten","DEFAULT '400000' NOT NULL");
  $this->CheckColumn("mlm_15_punkte","int(11)","firmendaten","DEFAULT '450000' NOT NULL");

  $this->CheckColumn("mlm_01_mindestumsatz","int(11)","firmendaten","DEFAULT '50' NOT NULL");
  $this->CheckColumn("mlm_02_mindestumsatz","int(11)","firmendaten","DEFAULT '50' NOT NULL");
  $this->CheckColumn("mlm_03_mindestumsatz","int(11)","firmendaten","DEFAULT '50' NOT NULL");
  $this->CheckColumn("mlm_04_mindestumsatz","int(11)","firmendaten","DEFAULT '50' NOT NULL");
  $this->CheckColumn("mlm_05_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_06_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_07_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_08_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_09_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_10_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_11_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_12_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_13_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_14_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");
  $this->CheckColumn("mlm_15_mindestumsatz","int(11)","firmendaten","DEFAULT '100' NOT NULL");



  $this->CheckColumn("standardaufloesung","int(11)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("standardversanddrucker","int(11)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("standardetikettendrucker","int(11)","firmendaten","DEFAULT '0' NOT NULL");


  $this->CheckColumn("keinsteuersatz","int(1)","auftrag");
  $this->CheckColumn("keinsteuersatz","int(1)","rechnung");
  $this->CheckColumn("keinsteuersatz","int(1)","gutschrift");

  $this->CheckColumn("freifeld1","text","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld2","text","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld3","text","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld4","text","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld5","text","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld6","text","artikel","DEFAULT '' NOT NULL");

  $this->CheckColumn("einheit","varchar(255)","artikel","DEFAULT '' NOT NULL");
  $this->CheckColumn("einheit","varchar(255)","angebot_position","DEFAULT '' NOT NULL");
  $this->CheckColumn("einheit","varchar(255)","auftrag_position","DEFAULT '' NOT NULL");
  $this->CheckColumn("einheit","varchar(255)","rechnung_position","DEFAULT '' NOT NULL");
  $this->CheckColumn("einheit","varchar(255)","gutschrift_position","DEFAULT '' NOT NULL");
  $this->CheckColumn("einheit","varchar(255)","lieferschein_position","DEFAULT '' NOT NULL");
  $this->CheckColumn("einheit","varchar(255)","bestellung_position","DEFAULT '' NOT NULL");

  $this->CheckColumn("bestellungohnepreis","tinyint(1)","bestellung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("lieferantenretoure","tinyint(1)","lieferschein","DEFAULT '0' NOT NULL");
  $this->CheckColumn("lieferantenretoureinfo","TEXT","lieferschein","DEFAULT '' NOT NULL");
  $this->CheckColumn("lieferant","INT(11)","lieferschein","DEFAULT '0' NOT NULL");

  $this->CheckColumn("optional","int(1)","angebot_position","DEFAULT '0' NOT NULL");

  $this->CheckColumn("adresse","int(11)","lager_bewegung");

  $this->CheckColumn("geloescht","int(1)","arbeitspaket");
  $this->CheckColumn("vorgaenger","int(11)","arbeitspaket");
  $this->CheckColumn("kosten_geplant","decimal(10,4)","arbeitspaket");
  $this->CheckColumn("artikel_geplant","int(11)","arbeitspaket");

  $this->CheckColumn("adresse_abrechnung","int(11)","zeiterfassung");
  $this->CheckColumn("abrechnen","int(1)","zeiterfassung");
  $this->CheckColumn("ist_abgerechnet","int(1)","zeiterfassung");
  $this->CheckColumn("gebucht_von_user","int(11)","zeiterfassung");
  $this->CheckColumn("ort","varchar(1024)","zeiterfassung");
  $this->CheckColumn("abrechnung_dokument","varchar(1024)","zeiterfassung");
  $this->CheckColumn("dokumentid","int(11)","zeiterfassung");

  $this->CheckColumn("verrechnungsart","varchar(255)","zeiterfassung");

  $this->CheckColumn("reservierung","int(1)","projekt");
  $this->CheckColumn("verkaufszahlendiagram","int(1)","projekt");
  $this->CheckColumn("oeffentlich","int(1)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("shopzwangsprojekt","int(1)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("kunde","int(11)","projekt");
  $this->CheckColumn("dpdkundennr","varchar(255)","projekt");
  $this->CheckColumn("dhlkundennr","varchar(255)","projekt");
  $this->CheckColumn("dhlformat","TEXT","projekt");
  $this->CheckColumn("dpdformat","TEXT","projekt");
  $this->CheckColumn("paketmarke_einzeldatei","int(1)","projekt");
  $this->CheckColumn("dpdpfad","varchar(1024)","projekt");
  $this->CheckColumn("dhlpfad","varchar(1024)","projekt");
  $this->CheckColumn("upspfad","varchar(255)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("dhlintodb","tinyint(1)","projekt","DEFAULT '0' NOT NULL");

  $this->CheckColumn("dhlintodb","tinyint(1)","projekt","DEFAULT '0' NOT NULL");

  $this->CheckColumn("intraship_enabled","tinyint(1)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("intraship_drucker","INT(11)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("intraship_testmode","tinyint(1)","projekt","DEFAULT '0' NOT NULL");
  $this->CheckColumn("intraship_user","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_signature","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_ekp","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_api_user","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_api_password","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_company_name","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_street_name","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_street_number","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_zip","varchar(12)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_country","varchar(64)","projekt","DEFAULT 'germany' NOT NULL");
  $this->CheckColumn("intraship_city","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_email","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_phone","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_internet","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_contact_person","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_account_owner","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_account_number","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_bank_code","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_bank_name","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_iban","varchar(64)","projekt","DEFAULT '' NOT NULL");
  $this->CheckColumn("intraship_bic","varchar(64)","projekt","DEFAULT '' NOT NULL");

  $this->CheckColumn("intraship_WeightInKG","INT(11)","projekt","DEFAULT '5' NOT NULL");
  $this->CheckColumn("intraship_LengthInCM","INT(11)","projekt","DEFAULT '50' NOT NULL");
  $this->CheckColumn("intraship_WidthInCM","INT(11)","projekt","DEFAULT '50' NOT NULL");
  $this->CheckColumn("intraship_HeightInCM","INT(11)","projekt","DEFAULT '50' NOT NULL");
  $this->CheckColumn("intraship_PackageType","VARCHAR(8)","projekt","DEFAULT 'PL' NOT NULL");

  $this->CheckColumn("abrechnungsart","varchar(255)","projekt");
  $this->CheckColumn("kommissionierverfahren","varchar(255)","projekt");
  $this->CheckColumn("wechselaufeinstufig","int(11)","projekt");
  $this->CheckColumn("projektuebergreifendkommisionieren","int(1)","projekt");
  $this->CheckColumn("absendeadresse","varchar(255)","projekt");
  $this->CheckColumn("absendename","varchar(255)","projekt");
  $this->CheckColumn("absendesignatur","varchar(255)","projekt");
  $this->CheckColumn("autodruckrechnung","int(1)","projekt");
  $this->CheckColumn("autodruckversandbestaetigung","int(1)","projekt");
  $this->CheckColumn("automailversandbestaetigung","int(1)","projekt");
  $this->CheckColumn("autodrucklieferschein","int(1)","projekt");
  $this->CheckColumn("automaillieferschein","int(1)","projekt");
  $this->CheckColumn("autodruckstorno","int(1)","projekt");
  $this->CheckColumn("autodruckanhang","int(1)","projekt");
  $this->CheckColumn("automailanhang","int(1)","projekt");

  $this->CheckColumn("autodruckerrechnung","int(11)","projekt","DEFAULT '1' NOT NULL");
  $this->CheckColumn("autodruckerlieferschein","int(11)","projekt","DEFAULT '1' NOT NULL");
  $this->CheckColumn("autodruckeranhang","int(11)","projekt","DEFAULT '1' NOT NULL");

  $this->CheckColumn("autodruckrechnungmenge","int(11)","projekt","DEFAULT '1' NOT NULL");
  $this->CheckColumn("autodrucklieferscheinmenge","int(11)","projekt","DEFAULT '1' NOT NULL");

  $this->CheckColumn("stornorechnung","int(1)","gutschrift");
  $this->CheckColumn("startseite","int(1)","aufgabe");
  $this->CheckColumn("oeffentlich","int(1)","aufgabe");
  $this->CheckColumn("emailerinnerung","int(1)","aufgabe");
  $this->CheckColumn("emailerinnerung_tage","int(11)","aufgabe");

  $this->CheckColumn("note_x","int(11)","aufgabe");
  $this->CheckColumn("note_y","int(11)","aufgabe");
  $this->CheckColumn("note_z","int(11)","aufgabe");
  $this->CheckColumn("note_color","VARCHAR(255)","aufgabe");
  $this->CheckColumn("pinwand","int(1)","aufgabe");

  $this->CheckColumn("vorankuendigung","int(11)","aufgabe");
  $this->CheckColumn("status","varchar(255)","aufgabe");

  $this->CheckColumn("angebotid","int(11)","auftrag");
  $this->CheckColumn("internetseite","text","adresse");
  $this->CheckColumn("anlass","text","reisekosten");
  $this->CheckColumn("von","DATE","reisekosten");
  $this->CheckColumn("bis","DATE","reisekosten");
  $this->CheckColumn("von_zeit","varchar(255)","reisekosten");
  $this->CheckColumn("bis_zeit","varchar(255)","reisekosten");

  $this->CheckColumn("name","varchar(255)","inventur");
  $this->CheckColumn("name","varchar(255)","anfrage");
  $this->CheckColumn("geliefert","int(11)","anfrage_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("vpe","varchar(255)","anfrage_position","DEFAULT '' NOT NULL");
  $this->CheckColumn("einheit","varchar(255)","anfrage_position","DEFAULT '' NOT NULL");
  $this->CheckColumn("lieferdatum","date","anfrage_position");
  $this->CheckColumn("bearbeiterid","int(1)","anfrage","NOT NULL");

  $this->CheckColumn("schreibschutz","int(1)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","bestellung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","lieferschein","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","arbeitsnachweis","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","reisekosten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","produktion","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","inventur","DEFAULT '0' NOT NULL");
  $this->CheckColumn("schreibschutz","int(1)","anfrage","DEFAULT '0' NOT NULL");


  $this->CheckColumn("pdfarchiviert","int(1)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","bestellung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","lieferschein","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","arbeitsnachweis","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","reisekosten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviert","int(1)","produktion","DEFAULT '0' NOT NULL");

  $this->CheckColumn("pdfarchiviertversion","int(11)","rechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","gutschrift","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","angebot","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","auftrag","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","bestellung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","lieferschein","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","arbeitsnachweis","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","reisekosten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("pdfarchiviertversion","int(11)","produktion","DEFAULT '0' NOT NULL");

  $this->CheckColumn("typ","varchar(255)","rechnung","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","gutschrift","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","angebot","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","auftrag","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","bestellung","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","lieferschein","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","arbeitsnachweis","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","reisekosten","DEFAULT 'firma' NOT NULL");
  $this->CheckColumn("typ","varchar(255)","produktion","DEFAULT 'firma' NOT NULL");


  $this->CheckColumn("verbindlichkeiteninfo","varchar(255)","bestellung","DEFAULT '' NOT NULL");

  $this->CheckColumn("importgroup","bigint","kontoauszuege");


  $this->CheckColumn("beschreibung_de","text","artikelgruppen");
  $this->CheckColumn("beschreibung_en","text","artikelgruppen");

  $this->CheckColumn("internebemerkung","text","gutschrift");
  $this->CheckColumn("internebemerkung","text","rechnung");
  $this->CheckColumn("internebemerkung","text","lieferschein");

  $this->CheckColumn("ohne_briefpapier","int(1)","rechnung");
  $this->CheckColumn("ohne_briefpapier","int(1)","lieferschein");
  $this->CheckColumn("ohne_briefpapier","int(1)","angebot");
  $this->CheckColumn("ohne_briefpapier","int(1)","auftrag");
  $this->CheckColumn("ohne_briefpapier","int(1)","bestellung");
  $this->CheckColumn("ohne_briefpapier","int(1)","gutschrift");

  $this->CheckColumn("flattenedparts","longblob","emailbackup_mails");
  $this->CheckColumn("attachment","longblob","emailbackup_mails");
  $this->CheckColumn("geloescht","int(1)","emailbackup_mails", "DEFAULT '0' NOT NULL");
  $this->CheckColumn("warteschlange","int(1)","emailbackup_mails", "DEFAULT '0' NOT NULL");

  $this->CheckColumn("email","VARCHAR(64)","emailbackup","DEFAULT '' NOT NULL");


  $this->CheckColumn("internebemerkung","text","kontoauszuege");
  $this->CheckColumn("importfehler","int(1)","kontoauszuege");
  $this->CheckColumn("projekt","int(11)","firmendaten");
  $this->CheckColumn("externereinkauf","int(1)","firmendaten");
  $this->CheckColumn("schriftart","varchar(255)","firmendaten");
  $this->CheckColumn("knickfalz","int(1)","firmendaten");
  $this->CheckColumn("artikeleinheit","int(1)","firmendaten");
  $this->CheckColumn("artikeleinheit_standard","varchar(255)","firmendaten");

  $this->CheckColumn("abstand_name_beschreibung","int(11)","firmendaten"," DEFAULT '4' NOT NULL");
  $this->CheckColumn("abstand_boxrechtsoben_lr","int(11)","firmendaten"," DEFAULT '0' NOT NULL");

  $this->CheckColumn("zahlungsweise","varchar(255)","firmendaten","NOT NULL");
  $this->CheckColumn("zahlungszieltage","int(11)","firmendaten","DEFAULT '14' NOT NULL");
  $this->CheckColumn("zahlungszielskonto","int(11)","firmendaten","NOT NULL");
  $this->CheckColumn("zahlungszieltageskonto","int(11)","firmendaten","NOT NULL");

  $this->CheckColumn("zahlung_rechnung","int(1)","firmendaten"," DEFAULT '1' NOT NULL");
  $this->CheckColumn("zahlung_vorkasse","int(1)","firmendaten"," DEFAULT '1' NOT NULL");
  $this->CheckColumn("zahlung_nachnahme","int(1)","firmendaten"," DEFAULT '1' NOT NULL");
  $this->CheckColumn("zahlung_kreditkarte","int(1)","firmendaten","DEFAULT '1' NOT NULL");
  $this->CheckColumn("zahlung_einzugsermaechtigung","int(1)","DEFAULT '1' firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_paypal","int(1)","firmendaten","DEFAULT '1' NOT NULL");
  $this->CheckColumn("zahlung_bar","int(1)","firmendaten","DEFAULT '1' NOT NULL");
  $this->CheckColumn("zahlung_lastschrift","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("zahlung_amazon","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("zahlung_ratenzahlung","int(1)","firmendaten"," DEFAULT '1' NOT NULL");

  $this->CheckColumn("zahlung_rechnung_sofort_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_rechnung_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_vorkasse_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_lastschrift_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_nachnahme_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_bar_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_paypal_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_amazon_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_kreditkarte_de","text","firmendaten","NOT NULL");
  $this->CheckColumn("zahlung_ratenzahlung_de","text","firmendaten","NOT NULL");

  $this->CheckColumn("briefpapier2","longblob","firmendaten");
  $this->CheckColumn("briefpapier2vorhanden","int(1)","firmendaten");


  $this->CheckColumn("liveimport","text","konten");
  $this->CheckColumn("liveimport_online","int(1)","konten");

  $this->CheckColumn("zahlungsmailcounter","int(1)","auftrag");

  $this->CheckColumn("ansprechpartner","varchar(255)","angebot");
  $this->CheckColumn("mobil","varchar(255)","ansprechpartner");
  $this->CheckColumn("bezeichnung","varchar(255)","produktion");

  $this->CheckColumn("ticketqueue","varchar(255)","emailbackup");
  $this->CheckColumn("ticketprojekt","varchar(255)","emailbackup");

  $this->CheckTable("autoresponder_blacklist");
  $this->CheckColumn("cachetime","timestamp","autoresponder_blacklist");
  $this->CheckColumn("mailaddress","varchar(512)","autoresponder_blacklist");

  $this->CheckTable("event_api");
  $this->CheckColumn("id","int(11)","event_api","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("cachetime","timestamp","event_api");
  $this->CheckColumn("eventname","varchar(255)","event_api");
  $this->CheckColumn("parameter","varchar(255)","event_api");
  $this->CheckColumn("module","varchar(255)","event_api");
  $this->CheckColumn("action","varchar(255)","event_api");
  $this->CheckColumn("retries","int(11)","event_api");
  $this->CheckColumn("kommentar","varchar(255)","event_api");


  $this->CheckTable("gpsstechuhr");
  $this->CheckColumn("id","int(11)","gpsstechuhr","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("adresse","int(11)","gpsstechuhr");
  $this->CheckColumn("user","int(11)","gpsstechuhr");
  $this->CheckColumn("koordinaten","varchar(512)","gpsstechuhr");
  $this->CheckColumn("zeit","datetime","gpsstechuhr");

  $this->CheckTable("kostenstellen");
  $this->CheckColumn("id","int(11)","kostenstellen","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("nummer","varchar(20)","kostenstellen");
  $this->CheckColumn("beschreibung","varchar(512)","kostenstellen");
  $this->CheckColumn("internebemerkung","text","kostenstellen");

  $this->CheckTable("lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("id","int(11)","lager_mindesthaltbarkeitsdatum","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("datum","DATE","lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("mhddatum","DATE","lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("artikel","int(11)","lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("menge","DECIMAL(10,4)","lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("lager_platz","int(11)","lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("zwischenlagerid","int(11)","lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("charge","varchar(1024)","lager_mindesthaltbarkeitsdatum");
  $this->CheckColumn("internebemerkung","text","lager_mindesthaltbarkeitsdatum");

  $this->CheckTable("lager_charge");
  $this->CheckColumn("id","int(11)","lager_charge","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("charge","varchar(1024)","lager_charge");
  $this->CheckColumn("datum","DATE","lager_charge");
  $this->CheckColumn("artikel","int(11)","lager_charge");
  $this->CheckColumn("menge","DECIMAL(10,4)","lager_charge");
  $this->CheckColumn("lager_platz","int(11)","lager_charge");
  $this->CheckColumn("zwischenlagerid","int(11)","lager_charge");
  $this->CheckColumn("internebemerkung","text","lager_charge");

  $this->CheckTable("lager_differenzen");
  $this->CheckColumn("id","int(11)","lager_differenzen","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("artikel","int(11)","lager_differenzen");
  $this->CheckColumn("eingang","DECIMAL(10,4)","lager_differenzen");
  $this->CheckColumn("ausgang","DECIMAL(10,4)","lager_differenzen");
  $this->CheckColumn("berechnet","DECIMAL(10,4)","lager_differenzen");
  $this->CheckColumn("bestand","DECIMAL(10,4)","lager_differenzen");
  $this->CheckColumn("differenz","DECIMAL(10,4)","lager_differenzen");
  $this->CheckColumn("user","int(11)","lager_differenzen");


  $this->CheckTable("lager_seriennummern");
  $this->CheckColumn("id","int(11)","lager_seriennummern","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("artikel","int(11)","lager_seriennummern");
  $this->CheckColumn("lager_platz","int(11)","lager_seriennummern");
  $this->CheckColumn("zwischenlagerid","int(11)","lager_seriennummern");
  $this->CheckColumn("seriennummer","text","lager_seriennummern");
  $this->CheckColumn("charge","varchar(1024)","lager_seriennummern");
  $this->CheckColumn("mhddatum","DATE","lager_seriennummern");
  $this->CheckColumn("internebemerkung","text","lager_seriennummern");

  $this->CheckTable("adresse_import");
  $this->CheckColumn("id","int(11)","adresse_import","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("typ","varchar(20)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("name","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("ansprechpartner","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("abteilung","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("unterabteilung","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("adresszusatz","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("strasse","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("plz","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("ort","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("land","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("telefon","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("telefax","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("email","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("mobil","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("internetseite","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("ustid","varchar(255)","adresse_import","DEFAULT '' NOT NULL");
  $this->CheckColumn("user","INT(11)","adresse_import","DEFAULT '0' NOT NULL");
  $this->CheckColumn("adresse","INT(11)","adresse_import","DEFAULT '0' NOT NULL");
  $this->CheckColumn("angelegt_am","DATETIME","adresse_import");
  $this->CheckColumn("abgeschlossen","tinyint(1)","adresse_import","DEFAULT '0' NOT NULL");

  $this->CheckTable("berichte");
  $this->CheckColumn("id","int(11)","berichte","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("name","varchar(20)","berichte");
  $this->CheckColumn("beschreibung","text","berichte");
  $this->CheckColumn("internebemerkung","text","berichte");
  $this->CheckColumn("struktur","text","berichte");
  $this->CheckColumn("spaltennamen","varchar(1024)","berichte");
  $this->CheckColumn("spaltenbreite","varchar(1024)","berichte");
  $this->CheckColumn("spaltenausrichtung","varchar(1024)","berichte");

  $this->CheckTable("mlm_positionierung");
  $this->CheckColumn("id","int(11)","mlm_positionierung","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("adresse","int(11)","mlm_positionierung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("positionierung","varchar(255)","mlm_positionierung","DEFAULT '' NOT NULL");
  $this->CheckColumn("datum","date","mlm_positionierung");
  $this->CheckColumn("erneuert","date","mlm_positionierung");
  $this->CheckColumn("temporaer","tinyint(1)","mlm_positionierung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rueckgaengig","tinyint(1)","mlm_positionierung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mlm_abrechnung","int(11)","mlm_positionierung","DEFAULT '0' NOT NULL");

  $this->CheckTable("mlm_abrechnung");
  $this->CheckColumn("id","int(11)","mlm_abrechnung","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("von","date","mlm_abrechnung");
  $this->CheckColumn("bis","date","mlm_abrechnung");
  $this->CheckColumn("betrag_netto","DECIMAL(10,2)","mlm_abrechnung");
  $this->CheckColumn("betrag_netto","DECIMAL(10,2)","mlm_abrechnung");
  $this->CheckColumn("punkte","DECIMAL(10,2)","mlm_abrechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("bonuspunkte","DECIMAL(10,2)","mlm_abrechnung","DEFAULT '0' NOT NULL");
  $this->CheckColumn("anzahl","int(11)","mlm_abrechnung","DEFAULT '0' NOT NULL");


  $this->CheckTable("mlm_abrechnung_adresse");
  $this->CheckColumn("id","int(11)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("adresse","int(11)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("belegnr","int(11)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("betrag_netto","DECIMAL(10,2)","mlm_abrechnung_adresse");
  $this->CheckColumn("mitsteuer","INT(1)","mlm_abrechnung_adresse");
  $this->CheckColumn("mlmabrechnung","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("alteposition","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("neueposition","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("abrechnung","int(11)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("waehrung","VARCHAR(3)","mlm_abrechnung_adresse","DEFAULT 'EUR' NOT NULL");
  $this->CheckColumn("punkte","DECIMAL(10,2)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("bonuspunkte","DECIMAL(10,2)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rechnung_name","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("rechnung_strasse","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("rechnung_ort","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("rechnung_plz","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("rechnung_land","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("steuernummer","VARCHAR(64)","mlm_abrechnung_adresse");
  $this->CheckColumn("steuersatz","DECIMAL(10,2)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL");
  $this->CheckColumn("projekt","int(11)","mlm_abrechnung_adresse","DEFAULT '0' NOT NULL");


  $this->CheckTable("mlm_abrechnung_log");
  $this->CheckColumn("id","int(11)","mlm_abrechnung_log","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("adresse","int(11)","mlm_abrechnung_log","DEFAULT '0' NOT NULL");
  $this->CheckColumn("abrechnung","int(11)","mlm_abrechnung_log","DEFAULT '0' NOT NULL");
  $this->CheckColumn("meldung","VARCHAR(255)","mlm_abrechnung_log","DEFAULT '' NOT NULL");



  $this->CheckTable("mlm_wartekonto");
  $this->CheckColumn("id","int(11)","mlm_wartekonto","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("adresse","int(11)","mlm_wartekonto","DEFAULT '0' NOT NULL");
  $this->CheckColumn("artikel","int(11)","mlm_wartekonto","DEFAULT '0' NOT NULL");
  $this->CheckColumn("bezeichnung","varchar(255)","mlm_wartekonto","DEFAULT '' NOT NULL");
  $this->CheckColumn("beschreibung","text","mlm_wartekonto","DEFAULT '' NOT NULL");
  $this->CheckColumn("betrag","DECIMAL(10,2)","mlm_wartekonto");
  $this->CheckColumn("abrechnung","int(11)","mlm_wartekonto","DEFAULT '0' NOT NULL");
  $this->CheckColumn("autoabrechnung","tinyint(1)","mlm_wartekonto","DEFAULT '0' NOT NULL");
  $this->CheckColumn("abgerechnet","tinyint(1)","mlm_wartekonto","DEFAULT '0' NOT NULL");

  $this->CheckTable("wiedervorlage");
  $this->CheckColumn("id","int(11)","wiedervorlage","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("adresse","int(11)","wiedervorlage","DEFAULT '0' NOT NULL");
  $this->CheckColumn("projekt","int(11)","wiedervorlage","DEFAULT '0' NOT NULL");
  $this->CheckColumn("adresse_mitarbeier","int(11)","wiedervorlage","DEFAULT '0' NOT NULL");
  $this->CheckColumn("bezeichnung","varchar(255)","wiedervorlage","DEFAULT '' NOT NULL");
  $this->CheckColumn("beschreibung","text","wiedervorlage","DEFAULT '' NOT NULL");
  $this->CheckColumn("ergebnis","text","wiedervorlage","DEFAULT '' NOT NULL");
  $this->CheckColumn("betrag","DECIMAL(10,2)","wiedervorlage");
  $this->CheckColumn("erinnerung","DATETIME","wiedervorlage");
  $this->CheckColumn("erinnerung_per_mail","int(1)","wiedervorlage");
  $this->CheckColumn("erinnerung_empfaenger","TEXT","wiedervorlage");
  $this->CheckColumn("link","TEXT","wiedervorlage");
  $this->CheckColumn("module","varchar(255)","wiedervorlage");
  $this->CheckColumn("action","varchar(255)","wiedervorlage");
  $this->CheckColumn("parameter","id(11)","wiedervorlage");
  $this->CheckColumn("oeffentlich","id(1)","wiedervorlage");
  $this->CheckColumn("status","varchar(255)","wiedervorlage");

  $this->CheckTable("wiedervorlage_protokoll");
  $this->CheckColumn("id","int(11)","wiedervorlage_protokoll","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("vorgaengerid","int(11)","wiedervorlage_protokoll");
  $this->CheckColumn("wiedervorlageid","int(11)","wiedervorlage_protokoll");
  $this->CheckColumn("adresse_mitarbeier","int(11)","wiedervorlage_protokoll","DEFAULT '0' NOT NULL");
  $this->CheckColumn("erinnerung_alt","DATETIME","wiedervorlage_protokoll");
  $this->CheckColumn("erinnerung_neu","DATETIME","wiedervorlage_protokoll");
  $this->CheckColumn("bezeichnung","varchar(255)","wiedervorlage_protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("beschreibung","text","wiedervorlage_protokoll","DEFAULT '' NOT NULL");
  $this->CheckColumn("ergebnis","text","wiedervorlage_protokoll","DEFAULT '' NOT NULL");


  $this->CheckTable("verrechnungsart");
  $this->CheckColumn("id","int(11)","verrechnungsart","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("nummer","varchar(20)","verrechnungsart");
  $this->CheckColumn("beschreibung","varchar(512)","verrechnungsart");
  $this->CheckColumn("internebemerkung","text","verrechnungsart");

  $this->CheckTable("gruppen");
  $this->CheckColumn("id","int(11)","gruppen","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("name","varchar(512)","gruppen");
  $this->CheckColumn("art","varchar(512)","gruppen");
  $this->CheckColumn("kennziffer","VARCHAR(255)","gruppen");
  $this->CheckColumn("internebemerkung","text","gruppen");
  $this->CheckColumn("grundrabatt","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("rabatt1","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("rabatt2","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("rabatt3","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("rabatt4","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("rabatt5","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("sonderrabatt_skonto","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("provision","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("kundennummer","VARCHAR(255)","gruppen");
  $this->CheckColumn("partnerid","VARCHAR(255)","gruppen");
  $this->CheckColumn("dta_aktiv","TINYINT(1)","gruppen","DEFAULT '0' NOT NULL");
  $this->CheckColumn("dta_periode","TINYINT(2)","gruppen","DEFAULT '0' NOT NULL");
  $this->CheckColumn("dta_dateiname","VARCHAR(255)","gruppen","DEFAULT '' NOT NULL");
  $this->CheckColumn("dta_mail","VARCHAR(255)","gruppen","DEFAULT '' NOT NULL");
  $this->CheckColumn("dta_mail_betreff","VARCHAR(255)","gruppen","DEFAULT '' NOT NULL");
  $this->CheckColumn("dta_mail_text","TEXT","gruppen","DEFAULT '' NOT NULL");
  $this->CheckColumn("dtavariablen","TEXT","gruppen","DEFAULT '' NOT NULL");
  $this->CheckColumn("dta_variante","INT(11)","gruppen","DEFAULT '0' NOT NULL");

  $this->CheckColumn("bonus1","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus1_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus2","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus2_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus3","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus3_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus4","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus4_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus5","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus5_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus6","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus6_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus7","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus7_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus8","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus8_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus9","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus9_ab","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus10","DECIMAL(10,2)","gruppen");
  $this->CheckColumn("bonus10_ab","DECIMAL(10,2)","gruppen");

  $this->CheckColumn("bonus1","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus1_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus2","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus2_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus3","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus3_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus4","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus4_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus5","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus5_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus6","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus6_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus7","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus7_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus8","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus8_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus9","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus9_ab","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus10","DECIMAL(10,2)","adresse");
  $this->CheckColumn("bonus10_ab","DECIMAL(10,2)","adresse");



  $this->CheckColumn("zahlungszieltage","int(11)","gruppen","DEFAULT '14' NOT NULL");
  $this->CheckColumn("zahlungszielskonto","DECIMAL(10,2)","gruppen","NOT NULL");
  $this->CheckColumn("zahlungszieltageskonto","int(11)","gruppen","NOT NULL");

  $this->CheckColumn("portoartikel","int(11)","gruppen");
  $this->CheckColumn("portofreiab","DECIMAL(10,2)","gruppen","DEFAULT '0' NOT NULL");

  $this->CheckColumn("erweiterteoptionen","INT(1)","gruppen");
  $this->CheckColumn("zentralerechnung","INT(1)","gruppen");
  $this->CheckColumn("zentralregulierung","INT(1)","gruppen");
  $this->CheckColumn("gruppe","INT(1)","gruppen");
  $this->CheckColumn("preisgruppe","INT(1)","gruppen");
  $this->CheckColumn("verbandsgruppe","INT(1)","gruppen");

  $this->CheckColumn("rechnung_name","VARCHAR(255)","gruppen");
  $this->CheckColumn("rechnung_strasse","VARCHAR(255)","gruppen");
  $this->CheckColumn("rechnung_ort","VARCHAR(255)","gruppen");
  $this->CheckColumn("rechnung_plz","VARCHAR(255)","gruppen");
  $this->CheckColumn("rechnung_abteilung","VARCHAR(255)","gruppen");
  $this->CheckColumn("rechnung_land","VARCHAR(255)","gruppen");
  $this->CheckColumn("rechnung_email","VARCHAR(255)","gruppen");

  $this->CheckColumn("rechnung_periode","int(11)","adresse");
  $this->CheckColumn("rechnung_anzahlpapier","int(11)","adresse");
  $this->CheckColumn("rechnung_permail","int(1)","adresse");
  $this->CheckColumn("rechnung_email","varchar(255)","adresse");

  $this->CheckColumn("rechnung_periode","int(11)","gruppen");
  $this->CheckColumn("rechnung_anzahlpapier","int(11)","gruppen");
  $this->CheckColumn("rechnung_permail","int(1)","gruppen");

  $this->CheckColumn("webid","int(11)","gruppen");
  $this->CheckColumn("webid","int(11)","artikel");
  $this->CheckColumn("webid","VARCHAR(1024)","auftrag_position");

  $this->CheckTable("reisekostenart");
  $this->CheckColumn("id","int(11)","reisekostenart","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("nummer","varchar(20)","reisekostenart");
  $this->CheckColumn("beschreibung","varchar(512)","reisekostenart");
  $this->CheckColumn("internebemerkung","text","reisekostenart");


  $this->CheckTable("artikeleinheit");
  $this->CheckColumn("id","int(11)","artikeleinheit","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("einheit_de","varchar(255)","artikeleinheit");
  $this->CheckColumn("internebemerkung","text","artikeleinheit");

  $this->CheckTable("importvorlage");
  $this->CheckColumn("id","int(11)","importvorlage","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("bezeichnung","varchar(255)","importvorlage");
  $this->CheckColumn("ziel","varchar(255)","importvorlage");
  $this->CheckColumn("internebemerkung","text","importvorlage");
  $this->CheckColumn("fields","text","importvorlage");
  $this->CheckColumn("letzterimport","datetime","importvorlage");
  $this->CheckColumn("mitarbeiterletzterimport","varchar(255)","importvorlage");
  $this->CheckColumn("importtrennzeichen","varchar(255)","importvorlage");        
  $this->CheckColumn("importerstezeilenummer","int(11)","importvorlage"); 
  $this->CheckColumn("importdatenmaskierung","varchar(255)","importvorlage");     
  $this->CheckColumn("importzeichensatz","varchar(255)","importvorlage"); 


  $this->CheckTable("exportvorlage");
  $this->CheckColumn("id","int(11)","exportvorlage","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("bezeichnung","varchar(255)","exportvorlage");
  $this->CheckColumn("ziel","varchar(255)","exportvorlage");
  $this->CheckColumn("internebemerkung","text","exportvorlage");
  $this->CheckColumn("fields","text","exportvorlage");
  $this->CheckColumn("fields_where","text","exportvorlage");
  $this->CheckColumn("letzterexport","datetime","exportvorlage");
  $this->CheckColumn("mitarbeiterletzterexport","varchar(255)","exportvorlage");
  $this->CheckColumn("exporttrennzeichen","varchar(255)","exportvorlage");        
  $this->CheckColumn("exporterstezeilenummer","int(11)","exportvorlage"); 
  $this->CheckColumn("exportdatenmaskierung","varchar(255)","exportvorlage");     
  $this->CheckColumn("exportzeichensatz","varchar(255)","exportvorlage"); 


  // accordion
  $this->CheckTable("accordion");
  $this->CheckColumn("id","int(11)","accordion");
  $this->CheckColumn("name","varchar(255)","accordion");
  $this->CheckColumn("target","varchar(255)","accordion");
  $this->CheckColumn("position","int(2)","accordion");

  //inhalt
  $this->CheckColumn("kurztext","text","inhalt");
  $this->CheckColumn("title","varchar(255)","inhalt");
  $this->CheckColumn("description","varchar(512)","inhalt");
  $this->CheckColumn("keywords","varchar(512)","inhalt");
  $this->CheckColumn("inhaltstyp","varchar(255)","inhalt");
  $this->CheckColumn("sichtbarbis","datetime","inhalt");
  $this->CheckColumn("datum","date","inhalt");
  $this->CheckColumn("template","varchar(255)","inhalt");
  $this->CheckColumn("finalparse","varchar(255)","inhalt");
  $this->CheckColumn("navigation","varchar(255)","inhalt");

  $this->CheckColumn("hwtoken","int(1)","user");
  $this->CheckColumn("hwkey","varchar(255)","user");
  $this->CheckColumn("hwcounter","int(11)","user");
  $this->CheckColumn("hwdatablock","varchar(255)","user");
  $this->CheckColumn("motppin","varchar(255)","user");
  $this->CheckColumn("motpsecret","varchar(255)","user");
  $this->CheckColumn("externlogin","int(1)","user");

  //wiki
  $this->CheckTable("wiki");
  $this->CheckColumn("name","varchar(255)","wiki");
  $this->CheckColumn("content","text","wiki");
  $this->CheckColumn("lastcontent","text","wiki");


  //tabelle backup
  $this->CheckTable("backup");
  $this->CheckColumn("adresse","int(11)","backup");
  $this->CheckColumn("name","varchar(255)","backup");
  $this->CheckColumn("dateiname","varchar(255)","backup");
  $this->CheckColumn("datum","datetime","backup");

  //Tabelle artikel_shop
  $this->CheckTable("artikel_shop");
  $this->CheckColumn("artikel","int(11)","artikel_shop");
  $this->CheckColumn("shop","int(11)","artikel_shop");
  $this->CheckColumn("checksum","text","artikel_shop");

  // Tabelle dokumente
  $this->CheckTable("dokumente");
  $this->CheckColumn("id","int(11)","dokumente");
  $this->CheckColumn("adresse_from","int(11)","dokumente");
  $this->CheckColumn("adresse_to","int(11)","dokumente");
  $this->CheckColumn("typ","varchar(24)","dokumente");
  $this->CheckColumn("von","varchar(512)","dokumente");
  $this->CheckColumn("firma","varchar(512)","dokumente");
  $this->CheckColumn("ansprechpartner","varchar(512)","dokumente");
  $this->CheckColumn("an","varchar(512)","dokumente");
  $this->CheckColumn("email_an","varchar(255)","dokumente");
  $this->CheckColumn("firma_an","varchar(255)","dokumente");
  $this->CheckColumn("adresse","varchar(255)","dokumente");
  $this->CheckColumn("plz","varchar(16)","dokumente");
  $this->CheckColumn("ort","varchar(255)","dokumente");
  $this->CheckColumn("land","varchar(32)","dokumente");
  $this->CheckColumn("datum","date","dokumente");
  $this->CheckColumn("betreff","varchar(1023)","dokumente");
  $this->CheckColumn("content","text","dokumente");
  $this->CheckColumn("signatur","tinyint(1)","dokumente");
  $this->CheckColumn("send_as","varchar(24)","dokumente");
  $this->CheckColumn("email","varchar(255)","dokumente");
  $this->CheckColumn("printer","int(2)","dokumente");
  $this->CheckColumn("fax","int(2)","dokumente");
  $this->CheckColumn("sent","int(1)","dokumente");
  $this->CheckColumn("deleted","int(1)","dokumente");
  $this->CheckColumn("created","datetime","dokumente");


  // Tabelle linkeditor
  $this->CheckTable("linkeditor");
  $this->CheckColumn("id","int(4)","linkeditor");
  $this->CheckColumn("rule","varchar(1024)","linkeditor");
  $this->CheckColumn("replacewith","varchar(1024)","linkeditor");
  $this->CheckColumn("active","varchar(1)","linkeditor");


  // Tabelle userrights
  $this->CheckTable("userrights");
  $this->CheckColumn("id","int(11)","userrights");
  $this->CheckColumn("user","int(11)","userrights");
  $this->CheckColumn("module","varchar(64)","userrights");
  $this->CheckColumn("action","varchar(64)","userrights");
  $this->CheckColumn("permission","int(1)","userrights");


  // Tabelle userrights
  $this->CheckTable("uservorlagerights");
  $this->CheckColumn("id","int(11)","uservorlagerights","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("vorlage","int(11)","uservorlagerights");
  $this->CheckColumn("module","varchar(64)","uservorlagerights");
  $this->CheckColumn("action","varchar(64)","uservorlagerights");
  $this->CheckColumn("permission","int(1)","uservorlagerights");

  // Tabelle userrights
  $this->CheckTable("uservorlage");
  $this->CheckColumn("id","int(11)","uservorlage","DEFAULT '0' NOT NULL AUTO_INCREMENT");
  $this->CheckColumn("bezeichnung","VARCHAR(255)","uservorlage");
  $this->CheckColumn("beschreibung","TEXT","uservorlage");



  $this->CheckTable("newsletter_blacklist");
  $this->CheckColumn("email","varchar(255)","newsletter_blacklist");

  // Tabelle artikel
  $this->CheckColumn("herstellernummer","varchar(255)","artikel");
  $this->CheckColumn("restmenge","int(1)","artikel");
  $this->CheckColumn("lieferzeitmanuell_en","varchar(255)","artikel");
  $this->CheckColumn("variante","int(1)","artikel");
  $this->CheckColumn("variante_von","int(11)","artikel");

  //firmendaten
  $this->CheckColumn("email","varchar(255)","firmendaten");
  $this->CheckColumn("absendername","varchar(255)","firmendaten");
  $this->CheckColumn("bcc1","varchar(255)","firmendaten");
  $this->CheckColumn("bcc2","varchar(255)","firmendaten");
  $this->CheckColumn("firmenfarbe","varchar(255)","firmendaten");
  $this->CheckColumn("name","varchar(255)","firmendaten");
  $this->CheckColumn("strasse","varchar(255)","firmendaten");
  $this->CheckColumn("plz","varchar(255)","firmendaten");
  $this->CheckColumn("ort","varchar(255)","firmendaten");
  $this->CheckColumn("steuernummer","varchar(255)","firmendaten");
  $this->CheckColumn("brieftext","varchar(255)","firmendaten");
  $this->CheckColumn("startseite_wiki","varchar(255)","firmendaten");
  $this->CheckColumn("artikel_suche_kurztext","int(1)","firmendaten");
  $this->CheckColumn("adresse_freitext1_suche","int(1)","firmendaten","DEFAULT '0' NOT NULL");
  $this->CheckColumn("iconset_dunkel","tinyint(1)","firmendaten","DEFAULT '0' NOT NULL");

  $this->CheckColumn("eigenernummernkreis","int(11)","projekt");
  $this->CheckColumn("next_angebot","varchar(255)","projekt");
  $this->CheckColumn("next_auftrag","varchar(255)","projekt");
  $this->CheckColumn("next_rechnung","varchar(255)","projekt");
  $this->CheckColumn("next_lieferschein","varchar(255)","projekt");
  $this->CheckColumn("next_arbeitsnachweis","varchar(255)","projekt");
  $this->CheckColumn("next_reisekosten","varchar(255)","projekt");
  $this->CheckColumn("next_bestellung","varchar(255)","projekt");
  $this->CheckColumn("next_gutschrift","varchar(255)","projekt");
  $this->CheckColumn("next_kundennummer","varchar(255)","projekt");
  $this->CheckColumn("next_lieferantennummer","varchar(255)","projekt");
  $this->CheckColumn("next_mitarbeiternummer","varchar(255)","projekt");
  $this->CheckColumn("next_waren","varchar(255)","projekt");
  $this->CheckColumn("next_produktion","varchar(255)","projekt");
  $this->CheckColumn("next_sonstiges","varchar(255)","projekt");
  $this->CheckColumn("next_reisekosten","varchar(255)","projekt");
  $this->CheckColumn("next_produktion","varchar(255)","projekt");
  $this->CheckColumn("next_anfrage","varchar(255)","projekt");
  $this->CheckColumn("next_artikelnummer","varchar(255)","projekt");

  $this->CheckColumn("warnung_doppelte_nummern","INT(1)","firmendaten","DEFAULT '1' NOT NULL");

  $this->CheckColumn("next_angebot","varchar(255)","firmendaten");
  $this->CheckColumn("next_auftrag","varchar(255)","firmendaten");
  $this->CheckColumn("next_rechnung","varchar(255)","firmendaten");
  $this->CheckColumn("next_lieferschein","varchar(255)","firmendaten");
  $this->CheckColumn("next_arbeitsnachweis","varchar(255)","firmendaten");
  $this->CheckColumn("next_reisekosten","varchar(255)","firmendaten");
  $this->CheckColumn("next_bestellung","varchar(255)","firmendaten");
  $this->CheckColumn("next_gutschrift","varchar(255)","firmendaten");
  $this->CheckColumn("next_kundennummer","varchar(255)","firmendaten");
  $this->CheckColumn("next_lieferantennummer","varchar(255)","firmendaten");
  $this->CheckColumn("next_mitarbeiternummer","varchar(255)","firmendaten");
  $this->CheckColumn("next_waren","varchar(255)","firmendaten");
  $this->CheckColumn("next_produktion","varchar(255)","firmendaten");
  $this->CheckColumn("next_sonstiges","varchar(255)","firmendaten");
  $this->CheckColumn("next_reisekosten","varchar(255)","firmendaten");
  $this->CheckColumn("next_produktion","varchar(255)","firmendaten");
  $this->CheckColumn("next_anfrage","varchar(255)","firmendaten");
  $this->CheckColumn("next_artikelnummer","varchar(255)","firmendaten","DEFAULT '' NOT NULL");

  $this->CheckColumn("seite_von_ausrichtung","varchar(255)","firmendaten");
  $this->CheckColumn("seite_von_sichtbar","int(1)","firmendaten");

  $this->CheckColumn("parameterundfreifelder","int(1)","firmendaten");
  $this->CheckColumn("freifeld1","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld2","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld3","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld4","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld5","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("freifeld6","text","firmendaten","DEFAULT '' NOT NULL");


  $this->CheckColumn("firmenfarbehell","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("firmenfarbedunkel","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("firmenfarbeganzdunkel","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("navigationfarbe","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("navigationfarbeschrift","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("unternavigationfarbe","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("unternavigationfarbeschrift","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("firmenlogo","longblob","firmendaten");
  $this->CheckColumn("firmenlogotype","varchar(255)","firmendaten");
  $this->CheckColumn("firmenlogoaktiv","int(1)","firmendaten");

  $this->CheckColumn("projektnummerimdokument","int(1)","firmendaten");
  $this->CheckColumn("mailanstellesmtp","int(1)","firmendaten");
  $this->CheckColumn("herstellernummerimdokument","int(1)","firmendaten");
  $this->CheckColumn("standardmarge","int(11)","firmendaten");

  $this->CheckColumn("steuer_erloese_inland_normal","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_aufwendung_inland_normal","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_erloese_inland_ermaessigt","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_aufwendung_inland_ermaessigt","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_erloese_inland_steuerfrei","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_aufwendung_inland_steuerfrei","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_erloese_inland_innergemeinschaftlich","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_aufwendung_inland_innergemeinschaftlich","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_erloese_inland_eunormal","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_aufwendung_inland_eunormal","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_erloese_inland_export","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_aufwendung_inland_import","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("steuer_anpassung_kundennummer","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");

  for($ki=1;$ki<=15;$ki++)
  {
    $this->CheckColumn("steuer_art_".$ki,"VARCHAR(30)","firmendaten","DEFAULT '' NOT NULL");
    $this->CheckColumn("steuer_art_".$ki."_normal","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
    $this->CheckColumn("steuer_art_".$ki."_ermaessigt","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
    $this->CheckColumn("steuer_art_".$ki."_steuerfrei","VARCHAR(10)","firmendaten","DEFAULT '' NOT NULL");
  }

  $this->CheckColumn("rechnung_header","text","firmendaten");
  $this->CheckColumn("lieferschein_header","text","firmendaten");
  $this->CheckColumn("angebot_header","text","firmendaten");
  $this->CheckColumn("auftrag_header","text","firmendaten");
  $this->CheckColumn("rechnung_header","text","firmendaten");
  $this->CheckColumn("gutschrift_header","text","firmendaten");
  $this->CheckColumn("bestellung_header","text","firmendaten");
  $this->CheckColumn("arbeitsnachweis_header","text","firmendaten");
  $this->CheckColumn("provisionsgutschrift_header","text","firmendaten");

  $this->CheckColumn("rechnung_footer","text","firmendaten");
  $this->CheckColumn("lieferschein_footer","text","firmendaten");
  $this->CheckColumn("angebot_footer","text","firmendaten");
  $this->CheckColumn("auftrag_footer","text","firmendaten");
  $this->CheckColumn("rechnung_footer","text","firmendaten");
  $this->CheckColumn("gutschrift_footer","text","firmendaten");
  $this->CheckColumn("bestellung_footer","text","firmendaten");
  $this->CheckColumn("arbeitsnachweis_footer","text","firmendaten");
  $this->CheckColumn("provisionsgutschrift_footer","text","firmendaten");

  $this->CheckColumn("rechnung_ohnebriefpapier","int(1)","firmendaten");
  $this->CheckColumn("lieferschein_ohnebriefpapier","int(1)","firmendaten");
  $this->CheckColumn("angebot_ohnebriefpapier","int(1)","firmendaten");
  $this->CheckColumn("auftrag_ohnebriefpapier","int(1)","firmendaten");
  $this->CheckColumn("rechnung_ohnebriefpapier","int(1)","firmendaten");
  $this->CheckColumn("gutschrift_ohnebriefpapier","int(1)","firmendaten");
  $this->CheckColumn("bestellung_ohnebriefpapier","int(1)","firmendaten");
  $this->CheckColumn("arbeitsnachweis_ohnebriefpapier","int(1)","firmendaten");

  $this->CheckColumn("eu_lieferung_vermerk","text","firmendaten","DEFAULT '' NOT NULL");
  $this->CheckColumn("export_lieferung_vermerk","text","firmendaten","DEFAULT '' NOT NULL");

  $this->CheckColumn("abstand_adresszeileoben","int(11)","firmendaten");
  $this->CheckColumn("abstand_boxrechtsoben","int(11)","firmendaten");
  $this->CheckColumn("abstand_betreffzeileoben","int(11)","firmendaten");
  $this->CheckColumn("abstand_artikeltabelleoben","int(11)","firmendaten");

  $this->CheckColumn("rabatt","int(11)","angebot_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rabatt","int(11)","rechnung_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rabatt","int(11)","auftrag_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("rabatt","int(11)","gutschrift_position","DEFAULT '0' NOT NULL");

  $this->CheckColumn("wareneingang_kamera_waage","int(1)","firmendaten");
  $this->CheckColumn("layout_iconbar","int(1)","firmendaten");

  $this->CheckColumn("artikelnummerninfotext","int(1)","bestellung");
  $this->CheckColumn("abgeschlossen","int(1)","bestellung_position");

  $this->CheckColumn("doppel","int(1)","rechnung");
  $this->CheckColumn("schreibbar","int(1)","konten");

  $this->CheckColumn("ansprechpartner","varchar(255)","bestellung");

  $this->CheckColumn("ansprechpartner","varchar(255)","lieferadressen");
  $this->CheckColumn("standardlieferadresse","tinyint(1)","lieferadressen","DEFAULT '0' NOT NULL");

  $this->CheckColumn("keinetrackingmail","int(1)","auftrag");
  $this->CheckColumn("keinetrackingmail","int(1)","versand");
  $this->CheckColumn("weitererlieferschein","int(1)","versand","DEFAULT '0' NOT NULL");
  $this->CheckColumn("anzahlpakete","int(11)","versand","DEFAULT '0' NOT NULL");
  $this->CheckColumn("gelesen","int(1)","versand","DEFAULT '0' NOT NULL");

  $this->CheckColumn("paketmarkegedruckt","int(1)","versand","DEFAULT '0' NOT NULL");
  $this->CheckColumn("papieregedruckt","int(1)","versand","DEFAULT '0' NOT NULL");
  $this->CheckColumn("parent","int(11)","datev_buchungen");

  $this->CheckColumn("inventur","int(11)","lager_platz_inhalt");

  $this->CheckColumn("startseite","varchar(1024)","user");
  $this->CheckColumn("webid","varchar(1024)","adresse");
  $this->CheckColumn("titel","varchar(1024)","adresse");
  $this->CheckColumn("anschreiben","varchar(1024)","adresse");
  $this->CheckColumn("titel","varchar(1024)","ansprechpartner");
  $this->CheckColumn("anschreiben","varchar(1024)","ansprechpartner");
  $this->CheckColumn("ansprechpartner_land","varchar(255)","ansprechpartner");



  $this->CheckColumn("geloescht","int(1)","shopexport");
  $this->CheckColumn("multiprojekt","int(1)","shopexport","DEFAULT '0' NOT NULL");

  $this->CheckColumn("produktioninfo","text","artikel");
  $this->CheckColumn("sonderaktion","text","artikel");
  $this->CheckColumn("sonderaktion_en","text","artikel");
  $this->CheckColumn("anabregs_text","text","artikel");
  $this->CheckColumn("restmenge","int(1)","artikel");
  $this->CheckColumn("autobestellung","int(1)","artikel","DEFAULT '0' NOT NULL");
  $this->CheckColumn("autolagerlampe","int(1)","artikel","DEFAULT '0' NOT NULL");
  $this->CheckColumn("mitarbeiter","int(11)","reisekosten_position","DEFAULT '0' NOT NULL");
  $this->CheckColumn("produktion","int(1)","artikel");
  $this->CheckColumn("herstellernummer","varchar(255)","artikel");
  $this->CheckColumn("datumproduktion","date","produktion");
  $this->CheckColumn("reservierart","varchar(255)","produktion");
  $this->CheckColumn("auslagerart","varchar(255)","produktion");

  $this->CheckColumn("kundenartikelnummer","varchar(255)","verkaufspreise");
  $this->CheckColumn("art","varchar(255)","verkaufspreise","DEFAULT 'Kunde' NOT NULL");
  $this->CheckColumn("gruppe","int(11)","verkaufspreise");

  $this->CheckColumn("allDay","int(1)","kalender_event");
  $this->CheckColumn("color","varchar(7)","kalender_event");

  $this->CheckColumn("ganztags","int(1)","aufgabe","DEFAULT '1' NOT NULL");

  $this->UpdateColumn("abgabe_bis","datetime","aufgabe");

  // Tabelle linkeditor
  $this->CheckTable("adresse_kontakte");
  $this->CheckColumn("id","int(11)","adresse_kontakte");
  $this->CheckColumn("adresse","int(11)","adresse_kontakte");
  $this->CheckColumn("bezeichnung","varchar(1024)","adresse_kontakte");
  $this->CheckColumn("kontakt","varchar(1024)","adresse_kontakte");

  $this->CheckColumn("gesamtstunden_max","int(11)","projekt");
  $this->CheckColumn("auftragid","int(11)","projekt");
  $this->CheckColumn("auftragid","int(11)","arbeitspaket");

  $this->CheckColumn("arbeitsnachweis","int(11)","zeiterfassung");
  $this->CheckColumn("nachbestelltexternereinkauf","int(1)","produktion_position");
  $this->CheckColumn("nachbestelltexternereinkauf","int(1)","auftrag_position");

  $this->CheckColumn("dhlzahlungmandant","varchar(3)","projekt","NOT NULL COMMENT 'DHL Zahlungsmandant ID'");
  $this->CheckColumn("dhlretourenschein","int(1)","projekt","NOT NULL COMMENT 'Retourenschein drucken 1=ja;0=nein'");

  $this->app->DB->Select("ALTER TABLE `emailbackup_mails` CHANGE `checksum` `checksum` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `lieferschein_position` CHANGE `geliefert` `geliefert` FLOAT NOT NULL");

  $this->app->DB->Select("ALTER TABLE `auftrag_position` CHANGE `geliefert_menge` `geliefert_menge` FLOAT NOT NULL");
  $this->app->DB->Select("ALTER TABLE `zwischenlager` CHANGE `menge` `menge` FLOAT NOT NULL; ");

  $this->app->DB->Select("ALTER TABLE user ADD DEFAULT '1' FOR activ");

  $this->app->DB->Select("ALTER TABLE `einkaufspreise` CHANGE `vpe` `vpe` VARCHAR( 64 ) NOT NULL DEFAULT '1'");
  $this->app->DB->Select("ALTER TABLE `verkaufspreise` CHANGE `vpe` `vpe` VARCHAR( 64 ) NOT NULL DEFAULT '1'");

  $this->app->DB->Select("ALTER TABLE `wiki` CHANGE `content` `content` LONGTEXT NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `wiki` CHANGE `lastcontent` `content` LONGTEXT NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `dta_datei` CHANGE `datum` `datum` DATETIME NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `dta` CHANGE `blz` `blz` VARCHAR(64) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `dta` CHANGE `konto` `konto` VARCHAR(64) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `lager_charge` CHANGE `menge` `menge` INT(11) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `lager_mindesthaltbarkeitsdatum` CHANGE `menge` `menge` INT(11) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `artikel` CHANGE `mlmpunkte` `mlmpunkte` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `artikel` CHANGE `mlmbonuspunkte` `mlmbonuspunkte` DECIMAL( 10, 2 ) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `rechnung` CHANGE `auftrag` `auftrag` VARCHAR( 255 ) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `artikel` CHANGE `punkte` `punkte` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `artikel` CHANGE `bonuspunkte` `bonuspunkte` DECIMAL( 10, 2 ) NOT NULL ");


  $this->app->DB->Select("ALTER TABLE `angebot_position` CHANGE `rabatt` `rabatt` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `auftrag_position` CHANGE `rabatt` `rabatt` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `rechnung_position` CHANGE `rabatt` `rabatt` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `gutschrift_position` CHANGE `rabatt` `rabatt` DECIMAL( 10, 2 ) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `angebot_position` CHANGE `punkte` `punkte` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `angebot_position` CHANGE `bonuspunkte` `bonuspunkte` DECIMAL( 10, 2 ) NOT NULL ");


  $this->app->DB->Select("ALTER TABLE `auftrag_position` CHANGE `punkte` `punkte` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `auftrag_position` CHANGE `bonuspunkte` `bonuspunkte` DECIMAL( 10, 2 ) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `rechnung_position` CHANGE `punkte` `punkte` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `rechnung_position` CHANGE `bonuspunkte` `bonuspunkte` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `mlm_positionen` CHANGE `punkte` `punkte` DECIMAL( 10, 2 ) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `mlm_baum_cache` CHANGE `punkte` `punkte` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `mlm_baum_cache` CHANGE `bonuspunkte` `punkte` DECIMAL( 10, 2 ) NOT NULL ");


  $this->app->DB->Select("ALTER TABLE `artikel` CHANGE `webid` `webid` VARCHAR(1024) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `gruppen` CHANGE `webid` `webid` VARCHAR(1024) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `arbeitspaket` CHANGE `zeit_geplant` `zeit_geplant` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `projekt` CHANGE `gesamtstunden_max` `gesamtstunden_max` DECIMAL( 10, 2 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `zeiterfassung` CHANGE `kostenstelle` `kostenstelle` VARCHAR(255) NOT NULL ");
  //$this->app->DB->Select("ALTER TABLE `lager_platz_inhalt` ADD INDEX ( `artikel` , `menge` ) ;");
  //$this->app->DB->Select("ALTER TABLE `bestellung_position` ADD INDEX ( `artikel` , `menge` , `geliefert` , `status` ) ;");
  $this->app->DB->Select("ALTER TABLE `produktion_position` CHANGE `produktion` `produktion` INT( 11 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `produktion_position` CHANGE `artikel` `artikel` INT( 11 ) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `projekt` CHANGE `sonstiges` `sonstiges` TEXT NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `projekt` CHANGE `absendesignatur` `absendesignatur` TEXT NOT NULL ");

  //$this->app->DB->Select("ALTER TABLE `produktion_position` ADD INDEX ( `geliefert` , `geliefert_menge` ) ;");


  //$this->app->DB->Select("ALTER TABLE `emailbackup` ADD INDEX ( `adresse` ) ;");
  //$this->app->DB->Select("ALTER TABLE `emailbackup_mails` ADD INDEX ( `webmail`,`spam` ) ;");
  //$this->app->DB->Select("ALTER TABLE `produktion_position` ADD INDEX ( `produktion` , `artikel` , `menge` , `status` ) ;");    

  $this->app->DB->Select("ALTER TABLE `auftrag_position` CHANGE `auftrag` `auftrag` INT( 11 ) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `auftrag_position` CHANGE `artikel` `artikel` INT( 11 ) NOT NULL ");
  //$this->app->DB->Select("ALTER TABLE `auftrag_position` ADD INDEX ( `auftrag` , `artikel` , `menge` , `status` , `geliefert` , `geliefert_menge` ) ;");

  $this->app->DB->Select("ALTER TABLE `dokumente_send` CHANGE `ansprechpartner` `ansprechpartner` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `auftrag` DROP COLUMN teillieferung_von_auftrag");

  $this->app->DB->Select("ALTER TABLE `auftrag` CHANGE `zahlungszielskonto` `zahlungszielskonto` DECIMAL(10,2) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `angebot` CHANGE `zahlungszielskonto` `zahlungszielskonto` DECIMAL(10,2) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `rechnung` CHANGE `zahlungszielskonto` `zahlungszielskonto` DECIMAL(10,2) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `gutschrift` CHANGE `zahlungszielskonto` `zahlungszielskonto` DECIMAL(10,2) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `bestellung` CHANGE `zahlungszielskonto` `zahlungszielskonto` DECIMAL(10,2) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `verbindlichkeit` CHANGE `skonto` `skonto` DECIMAL(10,2) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `auftrag` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `angebot` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `rechnung` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `lieferschein` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `gutschrift` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `bestellung` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `reisekosten` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `arbeitsnachweis` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `produktion` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `inventur` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `anfrage` CHANGE `belegnr` `belegnr` VARCHAR(255) NOT NULL ");

  $this->app->DB->Select("ALTER TABLE `adresse` CHANGE `kundennummer` `kundennummer` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `adresse` CHANGE `lieferantennummer` `lieferantennummer` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `adresse` CHANGE `mitarbeiternummer` `mitarbeiternummer` VARCHAR(255) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `projekt` CHANGE `beschreibung` `beschreibung` TEXT NOT NULL ");

  $this->app->DB->Select("ALTER TABLE kostenstellen DROP PRIMARY KEY, ADD PRIMARY KEY ( `id` )");
  $this->app->DB->Select("ALTER TABLE verrechnungsart DROP PRIMARY KEY, ADD PRIMARY KEY ( `id` )");

  $this->app->DB->Select("ALTER TABLE `angebot_position` CHANGE `angebot` `angebot` INT(11) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `gutschrift_position` CHANGE `gutschrift` `gutschrift` INT(11) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `rechnung_position` CHANGE `rechnung` `rechnung` INT(11) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `reisekosten_position` CHANGE `reisekosten` `reisekosten` INT(11) NOT NULL ");
  $this->app->DB->Select("ALTER TABLE `arbeitsnachweis_position` CHANGE `arbeitsnachweis` `arbeitsnachweis` INT(11) NOT NULL ");



  $this->app->DB->Update("ALTER TABLE `dta` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `vz1` `vz1` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `vz2` `vz2` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `vz3` `vz3` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `status` `status` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `waehrung` `waehrung` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'EUR'");

  $this->FirmenDatenStandard();

}


function CheckTable($table)
{
  $found = false;
  $tables = mysql_list_tables ($this->app->Conf->WFdbname); 
  while (list ($temp) = mysql_fetch_array ($tables)) {
    if ($temp == $table) {
      $found = true;
    }
  }
  if($found==false)
  {
    $sql = "CREATE TABLE `$table` (`id` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`)) ENGINE = MyISAM DEFAULT CHARSET=utf8"; 
    $this->app->DB->Update($sql);
  }       


}

function UpdateColumn($column,$type,$table,$default="NOT NULL")
{
  //ALTER TABLE `aufgabe` CHANGE `abgabe_bis` `abgabe_bis` DATETIME NOT NULL 
  $fields = mysql_list_fields( $this->app->Conf->WFdbname, $table);
  $columns = mysql_num_fields($fields);
  for ($i = 0; $i < $columns; $i++) {$field_array[] = mysql_field_name($fields, $i);}
  if (in_array($column, $field_array))
  {
    //$result = mysqli_query($this->app->DB->connection,('ALTER TABLE '.$table.' ADD '.$column.' '.$type.' '.$default.';');
    $result = mysqli_query($this->app->DB->connection,'ALTER TABLE `'.$table.'` CHANGE `'.$column.'` `'.$column.'` '.$type.' '.$default.';');
  }

}

function CheckColumn($column,$type,$table,$default="")
{
  $fields = mysql_list_fields( $this->app->Conf->WFdbname, $table);
  $columns = mysql_num_fields($fields);
  for ($i = 0; $i < $columns; $i++) {$field_array[] = mysql_field_name($fields, $i);}
  if (!in_array($column, $field_array))
  {
    $result = mysqli_query($this->app->DB->connection,'ALTER TABLE '.$table.' ADD '.$column.' '.$type.' '.$default.';');
  }
}



function IstWerktag($datum)
{
  if($this->IstFreierTag($datum)) return false;
  else return true;
}

function IstFreierTag($datum) {

  $tmp = explode('-',$datum);
  $jahr = $tmp[0];
  $monat = $tmp[1];
  $tag = $tmp[2];

  // Parameter in richtiges Format bringen
  if(strlen($tag) == 1) {
    $tag = "0$tag";
  }
  if(strlen($monat) == 1) {
    $monat = "0$monat";
  }

  // Wochentag berechnen
  $datum = getdate(mktime(0, 0, 0, $monat, $tag, $jahr));
  $wochentag = $datum['wday'];

  // Prüfen, ob Wochenende
  if($wochentag == 0 || $wochentag == 6) {
    return true;
  }

  // Feste Feiertage werden nach dem Schema ddmm eingetragen
  $feiertage[] = "0101"; // Neujahrstag
  $feiertage[] = "0105"; // Tag der Arbeit
  $feiertage[] = "0310"; // Tag der Deutschen Einheit
  $feiertage[] = "2512"; // Erster Weihnachtstag
  $feiertage[] = "2612"; // Zweiter Weihnachtstag

  // Bewegliche Feiertage berechnen
  $tage = 60 * 60 * 24;
  $ostersonntag = easter_date($jahr);
  $feiertage[] = date("dm", $ostersonntag - 2 * $tage);  // Karfreitag
  $feiertage[] = date("dm", $ostersonntag + 1 * $tage);  // Ostermontag
  $feiertage[] = date("dm", $ostersonntag + 39 * $tage); // Himmelfahrt
  $feiertage[] = date("dm", $ostersonntag + 50 * $tage); // Pfingstmontag
  $feiertage[] = date("dm", $ostersonntag + 60 * $tage); // Frohnleichnahm

  // Prüfen, ob Feiertag
  $code = $tag.$monat;
  if(in_array($code, $feiertage)) {
    return true;
  } else {
    return false;
  }
}

function NeueArtikelNummer($artikelart="",$firma="",$projekt="")
{
  return $this->GetNextArtikelnummer($artikelart,$firma,$projekt);
}


function ProduktionName($id,$link="")
{

  $tmp = $this->app->DB->Select("SELECT
      (SELECT CONCAT(ar.name_de,' (',a.belegnr,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1) as bezeichnung

       FROM  produktion a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id='$id'");

      if($link!="")
      $tmp = str_replace('%value%',$tmp,$link);

      return $tmp;    

      }


      function EtikettenDrucker($kennung,$anzahl,$tabelle,$id,$variables="",$xml="",$druckercode="")
      {
      switch($kennung)
      {
        case "artikel_klein":
          $xml ='
            <label>
            <barcode y="1" x="3" size="8" type="2">{NUMMER}</barcode>
            <line x="3" y="10" size="4">NR {NUMMER}</line>
            <line x="3" y="13" size="4">{NAME_DE}</line>
            </label>
            ';
          break;

        case "lagerplatz_klein":
          $xml ='
            <label>
            <barcode y="1" x="3" size="8" type="2">{ID}</barcode>
            <line x="3" y="10" size="4">Lager: {KURZBEZEICHNUNG}</line>
            </label>
            ';
          break;

        case "etikettendrucker_einfach":
          $xml ='
            <label>
            <line x="3" y="2" size="4">{BEZEICHNUNG1}</line>
            <line x="3" y="10" size="4">{BEZEICHNUNG2}</line>
            </label>
            ';
          break;

        case "kommissionieraufkleber":
          $xml = '<label>
            <barcode x="2" y="0" size="8" type="2">{LIEFERSCHEIN}</barcode>
            <line x="2" y="8" size="3">---------------------------</line>
            <line x="2" y="10" size="4">{IHREBESTELLNUMMER}</line>
            <line x="2" y="15" size="1">{FIRMA}</line>
            
            </label>';
          break;
      }

      $tmpxml = $this->app->erp->ReadyForPDF($this->app->DB->Select("SELECT xml FROM etiketten WHERE verwendenals='".$kennung."' LIMIT 1"));

      if(is_numeric($kennung))
        $tmpxml = $this->app->erp->ReadyForPDF($this->app->DB->Select("SELECT xml FROM etiketten WHERE id='".$kennung."' LIMIT 1"));
      else
        $kennung = $this->app->DB->Select("SELECT id FROM etiketten WHERE verwendenals='".$kennung."' LIMIT 1");

      if($tmpxml!="") {
        // standard etiketten werte laden
        $xml = $tmpxml;
        $labelbreite = $this->app->DB->Select("SELECT labelbreite FROM etiketten WHERE id='".$kennung."' LIMIT 1");
        $labelhoehe = $this->app->DB->Select("SELECT labelhoehe FROM etiketten WHERE id='".$kennung."' LIMIT 1");
        $labelabstand = $this->app->DB->Select("SELECT labelabstand FROM etiketten WHERE id='".$kennung."' LIMIT 1");
        $labeloffsetx = $this->app->DB->Select("SELECT labeloffsetx FROM etiketten WHERE id='".$kennung."' LIMIT 1");
        $labeloffsety = $this->app->DB->Select("SELECT labeloffsety FROM etiketten WHERE id='".$kennung."' LIMIT 1");
      } else {
        // standard etiketten werte laden
        $labelbreite = 50;
        $labelhoehe = 18;
        $labelabstand = 3;
        $labeloffsetx=0;
        $labeloffsety=6;
      }

      $xmlconfig = "<settings width=\"$labelbreite\" height=\"$labelhoehe\" distance=\"$labelabstand\" offsetx=\"$labeloffsetx\" offsety=\"$labeloffsety\" />";
      $xml = str_replace("<label>","<label>".$xmlconfig,$xml);

  switch($tabelle)
  {
    case "artikel": 
      $tmp = $this->app->DB->SelectArr("SELECT *,nummer as artikelnummer, name_de as artikel_name_de FROM artikel WHERE id='$id' LIMIT 1");

			$projekt = $tmp[0]['projekt'];
			if($tmp[0]['umsatzsteuer']=="ermaessigt")
				$steuer = ($this->GetStandardSteuersatzErmaessigt($projekt) + 100)/100.0;
			else
				$steuer = ($this->GetStandardSteuersatzNormal($projekt) + 100)/100.0;

			$tmp[0]['verkaufspreisbrutto']=number_format($this->GetVerkaufspreis($id,1)*$steuer,2,',','.');

      break;
    case "lager_platz": 
      $tmp = $this->app->DB->SelectArr("SELECT *,id as lager_platz_id, kurzbezeichnung as lager_platz_name FROM lager_platz WHERE id='$id' LIMIT 1");
      $tmp[0]['id'] = str_pad($tmp[0]['id'], 7, '0', STR_PAD_LEFT);
      break;
  }

  if(count($tmp)>0)
  {
    foreach($tmp[0] as $key=>$value)
    {
      $value = $this->UmlauteEntfernen($value);
      $xml = str_replace("{".strtoupper($key)."}",$value,$xml);
    }
  }

  if(count($variables)>0)
  {
    foreach($variables as $key=>$value)
    {
      $value = $this->UmlauteEntfernen($value);
      $xml = str_replace("{".strtoupper($key)."}",$value,$xml);
    }
  }

        if($druckercode <=0)
          $this->app->printer->Drucken($this->Firmendaten("standardetikettendrucker"),$xml,"",$anzahl);
        else
          $this->app->printer->Drucken($druckercode,$xml,"",$anzahl);
      }

function ArtikelLagerInfo($artikel)
{

  $summe = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel'");
  $reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$artikel' AND datum >= NOW()");

  $auftraege = $this->app->DB->Select("SELECT SUM(ap.menge) menge,ap.bezeichnung FROM auftrag_position ap LEFT JOIN artikel a ON a.id=ap.artikel WHERE a.id='$artikel' AND a.lagerartikel=1");

  $liefern= $this->app->DB->Select("SELECT SUM(ap.menge) menge,ap.bezeichnung FROM auftrag_position ap, auftrag aa, artikel a WHERE a.id=ap.artikel AND aa.id = ap.auftrag AND a.id='$artikel' AND a.lagerartikel=1 AND aa.status='freigegeben'");

  $rest = $summe - $liefern;
  if($reserviert=="") $reserviert =0;
  if($liefern <=0) $liefern=0;

  return "Lagerbestand: $summe &nbsp;| &nbsp;Reserviert: $reserviert &nbsp;|&nbsp;Noch liefern: $liefern&nbsp;|&nbsp;Restbestand: $rest<br><br>";

}

function LagerAusgehend($parsetarget,$ohne_in_bestellung=true,$produktion=false)
{

  $produktionid = $this->app->Secure->GetGET("produktionid");

  if($produktionid>0){
    $this->app->Tpl->Add(KURZUEBERSCHRIFT,": ".$this->ProduktionName($produktionid));
    $filter_produktionid = " AND pro.id='$produktionid' ";
  }

  $nachbestellt = $this->app->Secure->GetPOST("nachbestellt");
  $nachbestellt_entfernen = $this->app->Secure->GetPOST("nachbestellt_entfernen");
  if($nachbestellt!="")
  {
    $position_nachbestellt = $this->app->Secure->GetPOST("position");
    if($position_nachbestellt > 0)
      $this->app->DB->Update("UPDATE produktion_position SET nachbestelltexternereinkauf='1' WHERE id='$position_nachbestellt' LIMIT 1");
  }

  if($nachbestellt_entfernen!="")
  {
    $position_nachbestellt = $this->app->Secure->GetPOST("position");
    if($position_nachbestellt > 0)
      //$this->app->DB->Update("UPDATE artikel SET nachbestellt='0' WHERE id='$artikel_nachbestellt' LIMIT 1");
      $this->app->DB->Update("UPDATE produktion_position SET nachbestelltexternereinkauf='0' WHERE id='$position_nachbestellt' LIMIT 1");
  }

  $htmltable = new HTMLTable(0,"100%","",3,1);
  $htmltable2 = new HTMLTable(0,"100%","",3,1);
  $htmltable3 = new HTMLTable(0,"100%","",3,1);

  //if($produktion) 
  if($this->Firmendaten("externereinkauf")==1 && $produktionid > 0) 
    $headings=array("Artikel","Nummer","Standard Lieferant","Projekt","Lager","bedarf","bestellt","reserv.","Min/Diff","Fehlende","Position");
  else
  {
    if($produktionid > 0)
      $headings=array("Artikel","Nummer","Letzter Lieferant","Projekt","Lager","in Produktion","bestellt","reserv.","Min/Diff","Fehlende");
    else
      $headings=array("Artikel","Nummer","Standard Lieferant","Projekt","Lager","im Auftrag","bestellt","reserv.","Min/Diff","Fehlende");
  }
  //$headings=array("Artikel","Nummer","Hersteller","Letzter Lieferant","Projekt","Lager","bedarf","bestellt","reserv.","Fehlende");


  $htmltable->AddRowAsHeading($headings);
  $htmltable2->AddRowAsHeading($headings);
  $htmltable3->AddRowAsHeading($headings);

  $htmltable->ChangingRowColors('#e0e0e0','#fff');
  $htmltable2->ChangingRowColors('#e0e0e0','#fff');
  $htmltable3->ChangingRowColors('#e0e0e0','#fff');

  if($produktion) {
    $artikelarr = $this->app->DB->SelectArr("SELECT a.name_de,a.hersteller,pr.nachbestelltexternereinkauf as nachbestellt,
        a.endmontage,a.nummer,p.abkuerzung,a.stueckliste,a.id,a.adresse FROM produktion_position pr 
        LEFT JOIN artikel a ON a.id=pr.artikel LEFT JOIN projekt p ON a.projekt=p.id 
        LEFT JOIN produktion pro ON pro.id=pr.produktion
        WHERE a.geloescht=0 AND a.firma='".$this->app->User->GetFirma()."' 
        AND a.lagerartikel=1 AND pro.status='freigegeben' ".$this->ProjektRechte()." $filter_produktionid GROUP by a.id ");
  }
  else {

    $artikelarr = $this->app->DB->SelectArr("SELECT a.name_de,a.hersteller,'' as nachbestellt,
        a.endmontage,a.nummer,p.abkuerzung,a.stueckliste,a.lagerartikel,a.id,a.adresse FROM auftrag_position pr 
        LEFT JOIN artikel a ON a.id=pr.artikel LEFT JOIN projekt p ON a.projekt=p.id 
        LEFT JOIN auftrag pro ON pro.id=pr.auftrag
        WHERE a.geloescht=0 AND a.firma='".$this->app->User->GetFirma()."'  ".$this->ProjektRechte()."
        AND a.lagerartikel=1 AND pro.status='freigegeben' $filter_produktionid GROUP by a.id ");
  }

  $artikelarr2 = $this->app->DB->SelectArr("SELECT a.name_de,a.hersteller,'' as nachbestellt,
      a.endmontage,a.nummer,p.abkuerzung,a.stueckliste,a.lagerartikel,a.id,a.adresse FROM artikel a LEFT JOIN projekt p ON a.projekt=p.id 
      WHERE a.mindestlager > 0 AND a.geloescht!='1' ".$this->ProjektRechte()." GROUP by a.id ");
  $artikelarr = array_merge($artikelarr,$artikelarr2);
  $artikelarr = array_map('unserialize', array_unique(array_map('serialize', $artikelarr)));

  foreach ($artikelarr as $key => $row) {
    $dates[$key]  = $row['nummer']; 
    // of course, replace 0 with whatever is the date field's index
  }

  array_multisort($dates, SORT_ASC, $artikelarr);
  //              $this->StartMessung();  

  for($i=0;$i<count($artikelarr);$i++)
  {
    //      $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$artikelarr[$i][projekt]}' LIMIT 1");
    $projekt = $artikelarr[$i][abkuerzung];

    $name_de = $this->LimitChar($artikelarr[$i][name_de],35);
    $hersteller = $artikelarr[$i][hersteller];
    $adresse = $artikelarr[$i][adresse];
    $stueckliste = $artikelarr[$i][stueckliste];
    $lagerartikel = $artikelarr[$i][lagerartikel];
    $name =  $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
    $nummer = $this->LimitChar($artikelarr[$i][nummer],10);
    $id= $artikelarr[$i][id];
    $endmontage= $artikelarr[$i][endmontage];
    $name = $this->LimitChar($name,20);

    /*
    // 300 ms
    $letzter_lieferant = $this->app->DB->Select("SELECT a.name FROM bestellung_position bp 
    LEFT JOIN bestellung b ON b.id=bp.bestellung LEFT JOIN adresse a ON a.id=b.adresse 
    WHERE bp.artikel='$id' ORDER by b.datum DESC LIMIT 1");

    $letzter_lieferant = $this->LimitChar($letzter_lieferant,30);
     */
    $letzter_lieferant = $name;
    // AKTUELL 1 Sekunde
    $lager = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$id'");
    $inbestellung = 0; //$this->app->DB->Select("SELECT SUM(menge) FROM bestellung_position WHERE artikel='$id' AND geliefert < menge");

    // ganz alte auftraege stornieren!!!!! oder auftraege aelter als 3 monate ausblenden!!!!

    // AKTUELL 1 Sek
    if($produktion)
    {
      $auftraege = $this->app->DB->Select("SELECT SUM(ap.menge -ap.geliefert_menge) 
          FROM produktion_position ap, produktion a WHERE ap.artikel='$id' AND a.id=ap.produktion
          AND (a.status!='abgeschlossen' AND a.status!='storniert' AND a.status!='gestartet' AND a.status!='angelegt') AND ap.geliefert!=1");
    } else {
      $auftraege = $this->app->DB->Select("SELECT SUM(ap.menge -ap.geliefert_menge) 
          FROM auftrag_position ap, auftrag a WHERE ap.artikel='$id' AND a.id=ap.auftrag 
          AND (a.status!='abgeschlossen' AND a.status!='storniert' AND a.status!='angelegt') AND ap.geliefert!=1 ");
    }


    // Aktuell ca 1 sekunde
    $reservierungen= $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$id' AND datum >= NOW()");
    $mindestlager = $this->ArtikelMindestlager($id);

    //TODO HACK
    if($mindestlager<=1) $mindestlager=0;


    if($lager=="") $lager=0;
    if($auftraege<=0) $auftraege=0;
    if($reservierungen=="") $reservierungen=0;

    $fehlende = ($lager - $reservierungen + $inbestellung ) - $auftraege; //stornierungen raus z.b. grasshopper

    // ca 4 Sekunden

    $bestellung_array = $this->app->DB->SelectArr("SELECT SUM(if(b.status='versendet',bp.menge,0)) as versendet,
        SUM(if(b.status='angelegt' || b.status='freigegeben',bp.menge,0)) as angelegt,
        SUM(bp.geliefert) as geliefert FROM bestellung_position bp, bestellung b
        WHERE b.id=bp.bestellung AND bp.artikel='$id'
        AND bp.geliefert < bp.menge
        AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen=0)");

    //print_r($bestellung_array);

    $inbestellung = $bestellung_array[0]["versendet"];
    $angelegte_inbestellung = $bestellung_array[0]["angelegt"];
    $inbestellunggeliefert = $bestellung_array[0]["geliefert"];

    //$inbestellung = $inbestellung - $inbestellunggeliefert;

    // ca 4 Sekunden

    //print_r($bestellung_array);
    // alle produktionen wo dieser artikel dabei ist
    $tmp_produktionen = $this->app->DB->SelectArr("SELECT distinct pp.produktion FROM produktion_position pp 
        LEFT JOIN produktion p ON p.id=pp.produktion WHERE pp.artikel='$id' AND p.status='freigegeben'");


    //                      print_r($tmp_produktionen);
    $produktionen = "";

    for($ij=0;$ij<count($tmp_produktionen);$ij++)
    {
      $produktionen .="<li>".$this->ProduktionName($tmp_produktionen[$ij][produktion],
          "<a href=\"index.php?module=lager&action=ausgehend&cmd=produktion&produktionid={$tmp_produktionen[$ij][produktion]}\" style=\"font-weight:normal; font-size:82%;\">%value%</a>")."</li>";
    }


    if(($fehlende < 0 && $stueckliste!="1") || ($fehlende < 0 && $stueckliste=="1" && $lagerartikel=="1") ||  (($fehlende + $inbestellung) < $mindestlager && $mindestlager > 0)  ) //keine Stueckliste
    {
      if(($fehlende + $inbestellung) < 0) $color = "red"; 
      else if (($fehlende + $inbestellung) < $mindestlager && $mindestlager > 0) $color="blue"; 
      else $color="";

      if(($ohne_in_bestellung && $color!="red" && $color!="blue") || (!$ohne_in_bestellung && ($color=="red" || $color=="blue"))) {
        //if(($inbestellung  >= abs($fehlende)) && ($color=="red" || $color=="blue") && $inbestellung > 0 ) {
        //       $color="";

        $htmltable2->NewRow();


        $htmltable2->AddCol("<font color=$color>".$name_de."<ul>$produktionen</ul></font>");
        $htmltable2->AddCol("<a href=\"index.php?module=artikel&action=lager&id=$id\" target=\"_blank\"><font color=$color>".$nummer."</font></a>");
        //                                              $htmltable2->AddCol("<font color=$color>".$hersteller."</font>");
        $htmltable2->AddCol("<font color=$color>".$letzter_lieferant."</font>");
        $htmltable2->AddCol("<font color=$color>".$projekt."</font>");
        $htmltable2->AddCol("<font color=$color>".$lager."</font>");

        $htmltable2->AddCol($this->MengeFormat($auftraege));
        if($angelegte_inbestellung>0)
          $htmltable2->AddCol($this->MengeFormat($inbestellung)." (<font color=\"green\">".$this->MengeFormat($angelegte_inbestellung)."</font>)");
        else
          $htmltable2->AddCol($this->MengeFormat($inbestellung));

        $htmltable2->AddCol($this->MengeFormat($reservierungen));

        $htmltable2->AddCol($mindestlager);

        if($fehlende < 0)
          $htmltable2->AddCol($this->MengeFormat(abs($fehlende)));
        else
          $htmltable2->AddCol("-");

        if($this->Firmendaten("externereinkauf")==1 && $produktionid > 0) 
          $htmltable2->AddCol("<!--<input type=\"button\" value=\"bestellen\" onclick=\"confirm('Artikel als bestellt markieren?');\"><br>-->
              <input type=\"button\" value=\"als eingetroffen\nmarkieren\" onclick=\"confirm('Artikel als bestellt markieren?');\">");

      } else {
        // das wird nur in tabelle 1 angezeigt
        if( $artikelarr[$i][nachbestellt]!=1 || $this->Firmendaten("externereinkauf")!=1){

          $htmltable->NewRow();

          $htmltable->AddCol("<font color=$color>".$name_de."<ul>$produktionen</ul></font>");
          $htmltable->AddCol("<a href=\"index.php?module=artikel&action=lager&id=$id\" target=\"_blank\"><font color=$color>".$nummer."</font></a>");
          //                                              $htmltable->AddCol("<font color=$color>".$hersteller."</font>");
          $htmltable->AddCol("<font color=$color>".$letzter_lieferant."</font>");
          $htmltable->AddCol("<font color=$color>".$projekt."</font>");
          $htmltable->AddCol("<font color=$color>".$lager."</font>");

          $htmltable->AddCol($this->MengeFormat($auftraege));
          if($angelegte_inbestellung>0)
            $htmltable->AddCol($this->MengeFormat($inbestellung)." (<font color=\"green\">".$this->MengeFormat($angelegte_inbestellung)."</font>)");
          else
            $htmltable->AddCol($this->MengeFormat($inbestellung));

          $htmltable->AddCol($this->MengeFormat($reservierungen));

          if($color=="blue"){
            $diff_mindestlager = $mindestlager - $lager + $auftraege -$inbestellung;
            if($diff_mindestlager > 0) $diff_mindestlager = "<font color=blue>$diff_mindestlager</font>";
            else $diff_mindestlager="-";
            $htmltable->AddCol("$mindestlager/$diff_mindestlager");
          } else {
            $htmltable->AddCol("$mindestlager/0");
          }
          //$htmltable->AddCol($mindestlager." hm ".$mindestlager - $lager + $auftraege -$inbestellung);

          if($fehlende < 0)
            $htmltable->AddCol($this->MengeFormat(abs($fehlende)));
          else
            $htmltable->AddCol("-");

          if($color=="red")
          {
            $lieferanten_array_red[$adresse]++;
          } 

          //        if($this->Firmendaten("bestellvorschlaggroessernull")=="0" || ($this->Firmendaten("bestellvorschlaggroessernull")=="1" && ($fehlende <0)))
          $lieferanten_array[$adresse]++;

          if($this->Firmendaten("externereinkauf")==1 && $produktionid > 0){ 
            // hole Position
            $tmp_position = $this->app->DB->Select("SELECT id FROM produktion_position WHERE produktion='$produktionid' AND artikel='{$artikelarr[$i][id]}' LIMIT 1");

            $htmltable->AddCol("<!--<input type=\"button\" value=\"bestellen\" onclick=\"confirm('Artikel als bestellt markieren?');\"><br>-->
                <form action=\"\" method=\"post\"><input type=\"submit\" name=\"nachbestellt\" value=\"als bestellt\nmarkieren\"><input type=\"hidden\" name=\"position\" value=\"".$tmp_position."\"></form>");
          }
        }
      }

      if($artikelarr[$i][nachbestellt]=="1")
      {

        //$lieferanten_array[$adresse]++;
        $htmltable3->NewRow();

        $htmltable3->AddCol("<font>".$name_de."<ul>$produktionen</ul></font>");
        $htmltable3->AddCol("<a href=\"index.php?module=artikel&action=lager&id=$id\" target=\"_blank\"><font>".$nummer."</font></a>");
        $htmltable3->AddCol("<font>".$hersteller."</font>");
        $htmltable3->AddCol("<font>".$letzter_lieferant."</font>");
        $htmltable3->AddCol("<font>".$projekt."</font>");
        $htmltable3->AddCol("<font>".$lager."</font>");
        $htmltable3->AddCol($auftraege);
        if($angelegte_inbestellung>0)
          $htmltable3->AddCol($inbestellung." (<font color=\"green\">$angelegte_inbestellung</font>)");
        else
          $htmltable3->AddCol($inbestellung);

        $htmltable3->AddCol($reservierungen);

        if($bold)
          $htmltable3->AddCol("<b><font color=red>".abs($fehlende)."</font></b>");
        else
        {
          $htmltable3->AddCol(abs($fehlende));
        }
        $tmp_position = $this->app->DB->Select("SELECT id FROM produktion_position WHERE produktion='$produktionid' AND 
            artikel='{$artikelarr[$i][id]}' LIMIT 1");

        $htmltable3->AddCol("<!--<input type=\"button\" value=\"bestellen\" onclick=\"confirm('Artikel als bestellt markieren?');\"><br>-->
            <form action=\"#tabs-2\" method=\"post\"><input type=\"submit\" name=\"nachbestellt_entfernen\" value=\"erledigt\"><input type=\"hidden\" name=\"position\" value=\"".$tmp_position."\"></form>");
      }
      } 
    }
    //              $this->EndeMessung();   
    //              $this->ErgebnisMessung();       

    //    $lieferanten_array = array_unique ( $lieferanten_array );
    asort($lieferanten_array);
    $lieferanten_array = array_reverse($lieferanten_array,true);
    //print_r($lieferanten_array);
    $this->app->Tpl->Set(TAB3,"<table border=\"0\" width=100% cellspacing=1 cellpadding=3>");
    $this->app->Tpl->Add(TAB3,"<tr style=\"background-color:#e0e0e0;\"><td><b>Lieferantennummer</b></td><td><b>Lieferant</b></td><td><b>Menge</b></td><td><b>Aktion</b></td></tr>");


    if(count($lieferanten_array)>0)
    {
      $counter=1;
      foreach($lieferanten_array as $key=>$value)
      {
        $counter++;
        if($key > 0)
        {
          if($lieferanten_array_red[$key] > 0)$color="red"; else $color="";
          if($counter % 2) $style="background-color:#e0e0e0;"; else $style="background-color:#fff;";

          if($lieferanten_array_red[$key] > 0) $add_string = " + <font color=red>".$lieferanten_array_red[$key]."</font>";
          else $add_string = "";

          $value = $value - $lieferanten_array_red[$key];

          $data = $this->app->DB->SelectArr("SELECT name,lieferantennummer FROM adresse WHERE id='".$key."' LIMIT 1");
          $this->app->Tpl->Add(TAB3,"<tr style=\"$style\" class=\"gentable\"><td><font color=$color>".$data[0]['lieferantennummer']."</font></td><td><font color=$color>".$data[0]['name']."</font></td><td>".$value.$add_string."</td><td><a href=\"index.php?module=bestellvorschlag&action=list&adresseid=$key\" target=\"_blank\">Bestellvorschlag</a></td></tr>");
        }
      }

    }
    $this->app->Tpl->Add(TAB3,"</table>");


    $this->app->Tpl->Set(TAB1,$htmltable->Get());

    if($this->Firmendaten("externereinkauf")==1) 
    {
      $this->app->Tpl->Set(TAB2,$htmltable3->Get());
    } else {
      $this->app->Tpl->Set(TAB2,$htmltable2->Get());
    }
    /*
       if($produktion){
       $this->app->Tpl->Add(TAB1,"<table width=100%><tr><td align=right><i>Bitte Knopf \"als bestellt markieren\" nur nutzen, wenn Einkauf nicht &uuml;ber WaWision get&auml;tigt wird.</i></td></tr></table>");
       $this->app->Tpl->Add(TAB2,"<table width=100%><tr><td align=right><i>Bitte Knopf \"als eingetroffen markieren\" nur nutzen, wenn Wareneingang nicht &uuml;ber WaWision get&auml;tigt wird.</i></td></tr></table>");
       }
     */
  }


  function MengeFormat($menge)
  {
    if($menge<=0) return "";
    else if($menge == round($menge)) return round($menge);
    else return number_format($menge,2, '.', '');
  }

  function LimitChar($string,$length,$minword=3)
  {
    if(strlen($string) > $length) {
      $string = substr($string,0,$length)."...";
      $string_ende = strrchr($string, " ");
      $string = str_replace($string_ende," ...", $string);
    }
    return $string;
  }


  function LimitWord($word,$count,$sign="...")
  {
    $length = strlen($word);

    $parts= explode("\n", wordwrap($word, $count, "\n"));
    $word = $parts[0];

    if(strlen($word) < $length)
      $word.=$sign;

    return $word;
  }


  function MenuEintrag($link,$beschreibung,$mark=false)
  {
    $query = parse_url($link);
    $queryParts = explode('&', $query[query]);

    $params = array();
    foreach ($queryParts as $param) {
      $item = explode('=', $param);
      $params[$item[0]] = $item[1];
    }

    //Alle menüs ohne rechte ausblenden
    if(!$this->RechteVorhanden($params[module],$params[action])) return false;

    $this->menucounter++;

    if($beschreibung=="Freigabe" || $beschreibung=="Abschlie&szlig;en")
    {
      $this->app->Tpl->Add(TABS,"<a href=\"$link\" style=\"font-weight:bold;font-size:1.2em; margin: 3px; 
          background-color:rgb(255,223,233); color:[TPLFIRMENFARBEGANZDUNKEL];
border: 1px solid [TPLFIRMENFARBEGANZDUNKEL]; padding:4px;\">$beschreibung</a>");
    } else {

      //$this->app->Tpl->Add(TABS,"<img src=\"./themes/new/images/simpleForm_arrow.gif\" width=\"13\" height=\"9\" /> <a  href=\"$link\">$beschreibung</a><br>");
      //              if($beschreibung=="Zur&uuml;ck zur &Uuml;bersicht") return;
      $action = $this->app->Secure->GetGET("action");
      $module = $this->app->Secure->GetGET("module");
      $cmd = $this->app->Secure->GetGET("cmd");
      $id = $this->app->Secure->GetGET("id");

      if("index.php?module=$module&action=$action&cmd=$cmd&id=$id"==$link) $mark=1;
      else if("index.php?module=$module&action=$action&cmd=$cmd"==$link) $mark=1;
      else if("index.php?module=$module&action=$action&id=$id"==$link && $cmd=="") $mark=1;
      else if("index.php?module=$module&action=$action"==$link &&$cmd=="") $mark=1;

      if(strpos($beschreibung,"Neu")!==false)
      {
        $this->app->Tpl->Set(TABSADD,"<a style=\"color:white;font-size:9pt\" href=\"$link\">NEU</a>");
      }
      else if(strpos($beschreibung,"zur")!==false)
      {
        $this->app->Tpl->Set(TABSBACK,"$link");
      }
      else {
        if($mark)
          $this->app->Tpl->Add(TABS,"<a href=\"$link\" style=\"font-weight:bold;font-size:1.1em;  margin-left: 4px;
              background-color:[TPLFIRMENFARBEHELL]; color:[TPLFIRMENFARBEGANZDUNKEL]; border-bottom: 1px solid [TPLFIRMENFARBEHELL];
              border-left: 0px solid [TPLFIRMENFARBEGANZDUNKEL]; padding:6px;\">$beschreibung</a>");
        else
          $this->app->Tpl->Add(TABS,"<a href=\"$link\" style=\"font-weight:normal;font-size:1.2em; margin: 0px 0px 4px 4px; color:[TPLFIRMENFARBEHELL]; 
              border-top: 1px solid [TPLFIRMENFARBEHELL]; border-left: 1px solid [TPLFIRMENFARBEHELL];border-right: 1px solid [TPLFIRMENFARBEHELL]; padding:5px;\">$beschreibung</a>");
      }
    }


    //if($this->menucounter == 10) $this->app->Tpl->Add(TABS,"<br><br>");
  }


  function SaldoAdresseAuftrag($adresse)
  {
    return $this->app->DB->Select("SELECT SUM(gesamtsumme) FROM auftrag WHERE adresse='$adresse' AND status='freigegeben' LIMIT 1");
  }       

  function UmsatzAdresseAuftragJahr($adresse)
  {
    return $this->app->DB->Select("SELECT 
        SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1)))
        FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id WHERE (a.status='freigegeben' OR a.status='abgeschlossen') 
        AND DATE_FORMAT(a.datum,'%Y')=DATE_FORMAT(NOW(),'%Y') AND a.adresse='$adresse'");
    //"SELECT SUM(gesamtsumme) FROM auftrag WHERE adresse='$adresse' AND status='freigegeben' 
    //                      AND DATE_FORMAT(datum,'%Y')=DATE_FORMAT(NOW(),'%Y') LIMIT 1");
  }       


  function SaldoAdresse($adresse)
  {
    if(!is_numeric($adresse))
      return 0;
    // summe der zahlungseingaenge

    $summe_zahlungseingaenge  = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang WHERE adresse='$adresse'");

    // summe der zahlungsausgaenge
    $summe_zahlungsausgaenge  = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungsausgang WHERE adresse='$adresse'");

    // summe der rechnungen
    $summe_rechnungen  = $this->app->DB->Select("SELECT SUM(soll) FROM rechnung WHERE adresse='$adresse'");

    // summe skonto rechnungen
    $summe_rechnungen_skonto  = $this->app->DB->Select("SELECT SUM(skonto_gegeben) FROM rechnung WHERE adresse='$adresse'");

    // summe der gutschriften
    $summe_gutschriften  = $this->app->DB->Select("SELECT SUM(soll) FROM gutschrift WHERE adresse='$adresse'");

    return $summe_rechnungen - $summe_zahlungseingaenge - $summe_gutschriften + $summe_zahlungsausgaenge + $summe_rechnungen_skonto; 
  }


  function AuftragSaldo($id)
  {
    $rechnungArr = $this->app->DB->SelectArr("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, belegnr, gesamtsumme FROM auftrag WHERE id='$id' LIMIT 1");

    $auftragid = $id;

    // suche rechnungen fuer auftrag 
    $rechnungen = $this->app->DB->SelectArr("SELECT id FROM rechnung WHERE auftragid='$id'");
    for($i=0;$i<count($rechnungen);$i++)
    {
      $filter .=" OR (ke.objekt='rechnung' AND ke.parameter='{$rechnungen[$i][id]}')";
    }



    $eingangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, k.id as kontoauszuege, ke.betrag as betrag FROM kontoauszuege_zahlungseingang ke
        LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='auftrag' AND ke.parameter='$auftragid') $filter");

    for($i=0;$i<count($eingangArr);$i++)
      $einnahmen += $eingangArr[$i][betrag];

    $ausgangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m') as datum, ke.betrag as betrag FROM kontoauszuege_zahlungsausgang ke
        LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='auftrag' AND ke.parameter='$auftragid') $filter");

    for($i=0;$i<count($ausgangArr);$i++)
      $ausgaben += $ausgangArr[$i][betrag];
    /*
    // gutschriften
    for($i=0;$i<count($rechnungen);$i++)
    {
    $einnahmen += $this->app->DB->Select("SELECT SUM(soll) FROM gutschrift WHERE rechnungid='{$rechnungen[$i][id]}'");
    }
     */      

    return((($rechnungArr[0][gesamtsumme])*-1) + $einnahmen - $ausgaben);
  }



  function GutschriftSaldo($id)
  {

    $gutschriftArr = $this->app->DB->SelectArr("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, belegnr, soll FROM gutschrift WHERE id='$id' LIMIT 1");
    $rechnungid = $this->app->DB->Select("SELECT rechnungid FROM gutschrift WHERE id='$id' LIMIT 1");
    $auftragid = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id='$rechnungid' LIMIT 1");

    $eingangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, k.id as kontoauszuege, ke.betrag as betrag FROM kontoauszuege_zahlungseingang ke 
        LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='gutschrift' AND ke.parameter='$id') OR (ke.objekt='auftrag' AND ke.parameter='$auftragid' AND ke.parameter>0)
        OR (ke.objekt='rechnung' AND ke.parameter='$rechnungid'  AND ke.parameter>0)");

    for($i=0;$i<count($eingangArr);$i++)
      $einnahmen += $eingangArr[$i][betrag];

    $gutschriften = $this->app->DB->SelectArr("SELECT belegnr, DATE_FORMAT(datum,'%d.%m.%Y') as datum,soll FROM gutschrift WHERE rechnungid='$id'");

    for($i=0;$i<count($gutschriften);$i++)
      $einnahmen += $gutschriften[$i][soll];

    $ausgangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m') as datum, ke.betrag as betrag FROM kontoauszuege_zahlungsausgang ke 
        LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='gutschrift' AND ke.parameter='$id') OR (ke.objekt='rechnung' AND ke.parameter='$rechnungid'  AND ke.parameter>0)                     OR (ke.objekt='auftrag' AND ke.parameter='$auftragid'  AND ke.parameter>0)");


    for($i=0;$i<count($ausgangArr);$i++)
      $ausgaben += $ausgangArr[$i][betrag];

    return($einnahmen - $ausgaben);

    // eigentlich sollte es aber so sein:
    //return((($gutschriftArr[0][soll])*-1) + $einnahmen - $ausgaben);

  }



  function RechnungSaldo($id)
  {
    //manuell vergebenen skonti
    $rechnungArr = $this->app->DB->SelectArr("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, belegnr, soll, zahlungszielskonto, skonto_gegeben FROM rechnung WHERE id='$id' LIMIT 1");
    for($i=0;$i<count($rechnungArr);$i++)
    {
      $einnahmen += $rechnungArr[$i]['skonto_gegeben'];
      //echt berechnete skonti mit einbeziehen!!!
      $skonto += ($rechnungArr[$i]['soll']/100) * $rechnungArr[$i]['zahlungszielskonto'];
    }


    $auftragid = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id='$id' LIMIT 1");
    // wenn es keinen auftrag gab
    if($auftragid > 0) 
    {
      $eingangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, 
          DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, k.id as kontoauszuege, ke.betrag as betrag FROM kontoauszuege_zahlungseingang ke
          LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id 
          WHERE (ke.objekt='rechnung' AND ke.parameter='$id') OR (ke.objekt='auftrag' AND ke.parameter='$auftragid')");
    } else {
      $eingangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, 
          DATE_FORMAT(ke.datum,'%d.%m.%Y') as datum, k.id as kontoauszuege, ke.betrag as betrag FROM kontoauszuege_zahlungseingang ke
          LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='rechnung' AND ke.parameter='$id') ");
    }
    for($i=0;$i<count($eingangArr);$i++)
      $einnahmen += $eingangArr[$i][betrag];

    $gutschriften = $this->app->DB->SelectArr("SELECT belegnr, DATE_FORMAT(datum,'%d.%m.%Y') as datum,soll FROM gutschrift WHERE rechnungid='$id'");

    for($i=0;$i<count($gutschriften);$i++)
      $einnahmen += $gutschriften[$i][soll];

    if($auftragid > 0) 
    {
      $ausgangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m') as datum, 
          ke.betrag as betrag FROM kontoauszuege_zahlungsausgang ke
          LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id 
          WHERE (ke.objekt='rechnung' AND ke.parameter='$id') OR (ke.objekt='auftrag' AND ke.parameter='$auftragid')");
    } else {
      $ausgangArr = $this->app->DB->SelectArr("SELECT ko.bezeichnung as konto, DATE_FORMAT(ke.datum,'%d.%m') as datum, 
          ke.betrag as betrag FROM kontoauszuege_zahlungsausgang ke
          LEFT JOIN kontoauszuege k ON ke.kontoauszuege=k.id LEFT JOIN konten ko ON k.konto=ko.id WHERE (ke.objekt='rechnung' AND ke.parameter='$id')");
    }

    for($i=0;$i<count($ausgangArr);$i++)
      $ausgaben += $ausgangArr[$i][betrag];


    $result = ((($rechnungArr[0][soll])*-1) + $einnahmen - $ausgaben);

    $result = round($result,4);

    // pruefe ob skonto genommen worden ist
    if(round($result)!=0) {
      if(round($result + $skonto,4) == 0)
      {
        $result = $result + $skonto;
      }
    }

    // keine Rundungsfehler
    if($result <= 0.01 && $result >= -0.01) $result=0;

    return $result;
  }


  function AuftragExplodieren($auftrag,$typ="")
  { 
    if($typ=="produktion") {
      $auftraege = $this->app->DB->SelectArr("SELECT * FROM produktion WHERE (status='freigegeben' OR status='angelegt') AND id='$auftrag'"); 
    }else {
      $auftraege = $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE (status='freigegeben' OR status='angelegt') AND id='$auftrag'");
    }


    $adresse = $auftraege[0][adresse];
    $projekt = $auftraege[0][projekt];
    $status= $auftraege[0][status];
    if($status!='freigegeben' && $status!='angelegt')
      return;

    if($typ=="produktion")
      $artikelarr= $this->app->DB->SelectArr("SELECT * FROM produktion_position WHERE produktion='$auftrag' AND geliefert_menge < menge AND geliefert=0");
    else
      $artikelarr= $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$auftrag' AND geliefert_menge < menge AND geliefert=0");

    $treffer=0;
    // Lager Check
    $positionen_vorhanden = 0;
    $artikelzaehlen=0;
    //echo "{$auftraege[0][internet]} Adresse:$adresse Auftrag $auftrag";

    //echo "auftrag $auftrag anzahl:".count($artikelarr)."<br>";

    for($k=0;$k<count($artikelarr); $k++)
    {
      $menge = $artikelarr[$k][menge] - $artikelarr[$k][gelieferte_menge];
      $artikel = $artikelarr[$k][artikel];
      $artikel_position_id = $artikelarr[$k][id];
      // pruefe artikel 12 menge 4
      $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='$artikel' LIMIT 1");
      //if($artikelarr[$k][nummer]!="200000" && $artikelarr[$k][nummer]!="200001"  && $artikelarr[$k][nummer]!="200002" && $lagerartikel==1)
      //if($lagerartikel==1)
      //if($artikelarr[$k][nummer]!="200000" && $artikelarr[$k][nummer]!="200001"  && $artikelarr[$k][nummer]!="200002" )
      {
        //echo "Artikel $artikel Menge $menge";
        // schaue ob es ein JUST Stuecklisten artikel ist der nicht explodiert ist
        $just_stueckliste = $this->app->DB->Select("SELECT juststueckliste FROM artikel WHERE id='$artikel' LIMIT 1");
        if($typ=="produktion")
        {
          $just_stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$artikel' LIMIT 1");
          $explodiert = $this->app->DB->Select("SELECT explodiert FROM produktion_position WHERE id='$artikel_position_id' LIMIT 1");
          $menge = $this->app->DB->Select("SELECT menge  FROM produktion_position WHERE id='$artikel_position_id' LIMIT 1");
          $sort = $this->app->DB->Select("SELECT sort FROM produktion_position WHERE id='$artikel_position_id' LIMIT 1");
        } else {
          $explodiert = $this->app->DB->Select("SELECT explodiert FROM auftrag_position WHERE id='$artikel_position_id' LIMIT 1");
          $menge = $this->app->DB->Select("SELECT menge  FROM auftrag_position WHERE id='$artikel_position_id' LIMIT 1");
          $sort = $this->app->DB->Select("SELECT sort FROM auftrag_position WHERE id='$artikel_position_id' LIMIT 1");
        }
        $artikel_von_stueckliste = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE stuecklistevonartikel='$artikel'"); 

        // mengen anpassung

        if($just_stueckliste=="1" && $explodiert=="1")// && $max=="9898989")
        {
          foreach($artikel_von_stueckliste as $key=>$value)
          {
            $menge_st =$value[menge]*$menge;
            if($typ=="produktion")
              $this->app->DB->Update("UPDATE produktion_position SET menge='{$menge_st}' WHERE explodiert_parent='$artikel_position_id' AND artikel='{$value[artikel]}'");
            else
              $this->app->DB->Update("UPDATE auftrag_position SET menge='{$menge_st}' WHERE explodiert_parent='$artikel_position_id' AND artikel='{$value[artikel]}'");
          }
        }
        // darunter war ein else if
        if($just_stueckliste=="1" && $explodiert=="0")
        {
          $treffer++;
          //hole artikel von stueckliste

          // schiebe alle artikel nach hinten
          $erhoehe_sort = count($artikel_von_stueckliste);
          if($typ=="produktion")
            $this->app->DB->Update("UPDATE produktion_position SET sort=sort+$erhoehe_sort WHERE produktion='$auftrag' AND sort > $sort");
          else
            $this->app->DB->Update("UPDATE auftrag_position SET sort=sort+$erhoehe_sort WHERE auftrag='$auftrag' AND sort > $sort");

          foreach($artikel_von_stueckliste as $key=>$value)
          {
            $sort++;
            $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='{$value[artikel]}' LIMIT 1");
            if($typ=="produktion")
            {
              $exolpdodiert_id = $this->AddAuftragPositionNummer($auftrag,$nummer,$value[menge]*$menge,$projekt,1,false,"produktion");
              $this->app->DB->Update("UPDATE produktion_position SET explodiert_parent='$artikel_position_id',sort='$sort' WHERE id='$exolpdodiert_id' LIMIT 1");
            } else {
              $exolpdodiert_id = $this->AddAuftragPositionNummer($auftrag,$nummer,$value[menge]*$menge,$projekt,1);
              $this->app->DB->Update("UPDATE auftrag_position  SET explodiert_parent='$artikel_position_id',sort='$sort' WHERE id='$exolpdodiert_id' LIMIT 1");
            }
          }

          if($typ=="produktion")
            $this->app->DB->Update("UPDATE produktion_position SET explodiert='1' WHERE id='$artikel_position_id' LIMIT 1");
          else
            $this->app->DB->Update("UPDATE auftrag_position SET explodiert='1' WHERE id='$artikel_position_id' LIMIT 1");
        }
        else {



        }
      }
    }

    //achtung wenn selber artikel wieder in stueckliste ist dreht sich das programm dusselig hier!
    //    if($treffer >0)
    //      $this->AuftragExplodieren($auftrag,$typ);
  }

  function ProduktionEinzelnBerechnen($auftrag)
  {
    $this->AuftragReservieren($auftrag,"produktion");
    $produktion = $auftrag;

    $auftraege = $this->app->DB->SelectArr("SELECT * FROM produktion WHERE id='$produktion'");
    $adresse = $auftraege[0][adresse];
    $artikelarr= $this->app->DB->SelectArr("SELECT * FROM produktion_position WHERE produktion='$produktion' AND geliefert_menge < menge AND geliefert=0");

    //pruefe ob es mindestens eine reservierung gibt
    $reservierte = $this->app->DB->Select("SELECT COUNT(id) FROM lager_reserviert WHERE objekt='produktion' AND parameter='$auftrag'");

    if($reservierte >0)
      $this->app->DB->Update("UPDATE produktion SET reserviert_ok='1' WHERE id='$produktion' LIMIT 1");
    else
      $this->app->DB->Update("UPDATE produktion SET reserviert_ok='0' WHERE id='$produktion' LIMIT 1");

    // Lager Check
    $positionen_vorhanden = 0;
    $artikelzaehlen=0;
    //echo "{$auftraege[0][internet]} Adresse:$adresse Auftrag $produktion";
    for($k=0;$k<count($artikelarr); $k++)
    {
      $menge = $artikelarr[$k][menge] - $artikelarr[$k][gelieferte_menge];
      $artikel = $artikelarr[$k][artikel];
      $artikel_position_id = $artikelarr[$k][id];
      // pruefe artikel 12 menge 4
      // lagerartikel??

      $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");
      $stueckliste= $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");
      $juststueckliste= $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");

      //if($artikelarr[$k][nummer]!="200000" && $artikelarr[$k][nummer]!="200001"  && $artikelarr[$k][nummer]!="200002" && $lagerartikel==1)
      //echo $artikelarr[$k][artikel]." ";
      if($lagerartikel==1 && $stueckliste!="1")
      {
        if($this->LagerCheck($adresse,$artikel,$menge,"produktion",$produktion)>0) $positionen_vorhanden++;
        $artikelzaehlen++;
      }
    }

    //echo "$positionen_vorhanden $artikelzaehlen<hr>";

    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($produktion)) {
        if($positionen_vorhanden==$artikelzaehlen)
          $this->app->DB->Update("UPDATE produktion SET lager_ok='1' WHERE id='$produktion' LIMIT 1");
        else
          $this->app->DB->Update("UPDATE produktion SET lager_ok='0' WHERE id='$produktion' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM produktion WHERE id='$produktion' LIMIT 1");
      }
    } else {
      if($positionen_vorhanden==$artikelzaehlen)
        $this->app->DB->Update("UPDATE produktion SET lager_ok='1' WHERE id='$produktion' LIMIT 1");
      else
        $this->app->DB->Update("UPDATE produktion SET lager_ok='0' WHERE id='$produktion' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM produktion WHERE id='$produktion' LIMIT 1");
    }

    // projekt check start
    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($projekt)) {
        $projektcheck = $this->app->DB->Select("SELECT checkok FROM projekt WHERE id='$projekt' LIMIT 1");
        $projektcheckname = $this->app->DB->Select("SELECT checkname FROM projekt WHERE id='$projekt' LIMIT 1");
        $projektportocheck = $this->app->DB->Select("SELECT portocheck FROM projekt WHERE id='$projekt' LIMIT 1");
      }
    } else {
      $projektcheck = $this->app->DB->Select("SELECT checkok FROM projekt WHERE id='$projekt' LIMIT 1");
      $projektcheckname = $this->app->DB->Select("SELECT checkname FROM projekt WHERE id='$projekt' LIMIT 1");
      $projektportocheck = $this->app->DB->Select("SELECT portocheck FROM projekt WHERE id='$projekt' LIMIT 1");
    }

    if($projektcheck=="1")
    {
      //echo "projekt check $projektcheckname notwendig";
      include_once (dirname(__FILE__)."/../plugins/class.".$projektcheckname.".php");         

      $tmp = new unishop($this->app);

      if($this->app->Conf->WFdbType=="postgre") {
        if(is_numeric($produktion)) {   
          if($tmp->CheckOK($produktion))
            $this->app->DB->Update("UPDATE produktion SET check_ok='1' WHERE id='$produktion' LIMIT 1");
          else
            $this->app->DB->Update("UPDATE produktion SET check_ok='0' WHERE id='$produktion' LIMIT 1");
        }
      } else {
        if($tmp->CheckOK($produktion))
          $this->app->DB->Update("UPDATE produktion SET check_ok='1' WHERE id='$produktion' LIMIT 1");
        else
          $this->app->DB->Update("UPDATE produktion SET check_ok='0' WHERE id='$produktion' LIMIT 1");
      }
    }
    else {
      if($this->app->Conf->WFdbType=="postgre") {
        if(is_numeric($produktion))
          $this->app->DB->Update("UPDATE produktion SET check_ok='1' WHERE id='$produktion' LIMIT 1");
      } else
        $this->app->DB->Update("UPDATE produktion SET check_ok='1' WHERE id='$produktion' LIMIT 1");
    }


    // autopruefung anstubsen
    //$this->AutoUSTPruefung($adresse);

    // UST Check
    // pruefe adresse 23 ust innerhalb 3 tagen vorhanden? wenn nicht schaue ob selber ordern kann wenn ja ordern und auf gruen

    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($adresse))
        $ustprf = $this->app->DB->Select("SELECT id FROM ustprf WHERE to_char(datum_online,'YYYY-MM-DD')=to_char(NOW(),'YYYY-MM-DD') AND adresse='$adresse' AND status='erfolgreich' LIMIT 1");
    } else {
      $ustprf = $this->app->DB->Select("SELECT id FROM ustprf WHERE DATE_FORMAT(datum_online,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') AND adresse='$adresse' AND status='erfolgreich' LIMIT 1");
    }


    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($produktion)) {
        $ustid = $this->app->DB->Select("SELECT ustid FROM produktion WHERE id='$produktion' LIMIT 1");
        $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM produktion WHERE id='$produktion' LIMIT 1");
        $land = $this->app->DB->Select("SELECT land FROM produktion WHERE id='$produktion' LIMIT 1");
      }
    } else {
      $ustid = $this->app->DB->Select("SELECT ustid FROM produktion WHERE id='$produktion' LIMIT 1");
      $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM produktion WHERE id='$produktion' LIMIT 1");
      $land = $this->app->DB->Select("SELECT land FROM produktion WHERE id='$produktion' LIMIT 1");
    }

    if($ust_befreit==0)
    {
      if($this->app->Conf->WFdbType=="postgre") {
        if(is_numeric($produktion)) 
          $this->app->DB->Update("UPDATE produktion SET ust_ok='1' WHERE id='$produktion' LIMIT 1");
      } else {
        $this->app->DB->Update("UPDATE produktion SET ust_ok='1' WHERE id='$produktion' LIMIT 1");
      }

    } 
    /*
       else if($ust_befreit==1)
       {
       if($ustprf >0 && $ustid!="")
       $this->app->DB->Update("UPDATE produktion SET ust_ok='1' WHERE id='$produktion' LIMIT 1");
       else
       $this->app->DB->Update("UPDATE produktion SET ust_ok='0' WHERE id='$produktion' LIMIT 1");
       } else {
       if($this->Export($land))
       $this->app->DB->Update("UPDATE produktion SET ust_ok='1' WHERE id='$produktion' LIMIT 1");
       else
       $this->app->DB->Update("UPDATE produktion SET ust_ok='0' WHERE id='$produktion' LIMIT 1");
       }
     */
    // Porto Check
    // sind versandkosten im produktion
    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($produktion)) {
        $porto = $this->app->DB->Select("SELECT ap.id FROM produktion_position ap, artikel a WHERE ap.produktion='$produktion' AND ap.artikel=a.id AND a.porto=1 AND ap.preis >= 0
            AND a.id=ap.artikel LIMIT 1");
        $keinporto = $this->app->DB->Select("SELECT keinporto FROM produktion WHERE id='$produktion' LIMIT 1");
        $selbstabholer = $this->app->DB->Select("SELECT versandart FROM produktion WHERE id='$produktion' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM produktion WHERE id='$produktion' LIMIT 1");
      }
    } else {
      $porto = $this->app->DB->Select("SELECT ap.id FROM produktion_position ap, artikel a WHERE ap.produktion='$produktion' AND ap.artikel=a.id AND a.porto=1 AND ap.preis >= 0
          AND a.id=ap.artikel LIMIT 1");
      $keinporto = $this->app->DB->Select("SELECT keinporto FROM produktion WHERE id='$produktion' LIMIT 1");
      $selbstabholer = $this->app->DB->Select("SELECT versandart FROM produktion WHERE id='$produktion' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM produktion WHERE id='$produktion' LIMIT 1");
    }

    // portocheck bei projekt

    if($selbstabholer=="selbstabholer" || $selbstabholer=="keinversand") $keinporto=1;

    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($produktion)) {
        if($projektportocheck==1) {
          if($porto > 0)
            $this->app->DB->Update("UPDATE produktion SET porto_ok='1' WHERE id='$produktion' LIMIT 1");
          else
            $this->app->DB->Update("UPDATE produktion SET porto_ok='0' WHERE id='$produktion' LIMIT 1");
        } else {
          //projekt hat kein portocheck porto ist immer ok
          $this->app->DB->Update("UPDATE produktion SET porto_ok='1' WHERE id='$produktion' LIMIT 1");
        }

        if($keinporto==1 || $selbstabholer=="selbstabholer") {
          $this->app->DB->Update("UPDATE produktion SET porto_ok='1' WHERE id='$produktion' LIMIT 1");
          $this->app->DB->Update("UPDATE produktion_position ap, artikel a SET ap.preis='0' WHERE ap.produktion='$produktion' AND a.id=ap.artikel AND a.porto='1'");
        }
      }
    } else {
      if($projektportocheck==1) {
        if($porto > 0)
          $this->app->DB->Update("UPDATE produktion SET porto_ok='1' WHERE id='$produktion' LIMIT 1");
        else
          $this->app->DB->Update("UPDATE produktion SET porto_ok='0' WHERE id='$produktion' LIMIT 1");
      } else {
        //projekt hat kein portocheck porto ist immer ok
        $this->app->DB->Update("UPDATE produktion SET porto_ok='1' WHERE id='$produktion' LIMIT 1");
      }

      if($keinporto==1 || $selbstabholer=="selbstabholer") {
        $this->app->DB->Update("UPDATE produktion SET porto_ok='1' WHERE id='$produktion' LIMIT 1");
        $this->app->DB->Update("UPDATE produktion_position ap, artikel a SET ap.preis='0' WHERE ap.produktion='$produktion' AND a.id=ap.artikel AND a.porto='1'");
      }
    }


    //Vorkasse Check
    //ist genug geld da? zusammenzaehlen der kontoauszuege_zahlungseingang
    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($produktion) && is_numeric($adresse))
        $summe_eingang = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang WHERE objekt='produktion' AND parameter='$produktion' AND adresse='$adresse'");
      if(is_numeric($produktion)) {
        $produktion_gesamtsumme = $this->app->DB->Select("SELECT gesamtsumme FROM produktion WHERE id='$produktion' LIMIT 1");
        $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM produktion WHERE id='$produktion' LIMIT 1");
        $zahlungsweise = strtolower($zahlungsweise);
        if($summe_eingang>=$produktion_gesamtsumme || ($zahlungsweise=="rechnung" || $zahlungsweise=="amazon" || $zahlungsweise=="nachnahme" || $zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift" || $zahlungsweise=="bar"))
        { 
          $this->app->DB->Update("UPDATE produktion SET vorkasse_ok='1' WHERE id='$produktion' LIMIT 1");
        } else {
          $this->app->DB->Update("UPDATE produktion SET vorkasse_ok='0' WHERE id='$produktion' LIMIT 1");
        }

        $nachnahme = $this->app->DB->Select("SELECT COUNT(ap.id) FROM produktion_position ap, artikel a WHERE ap.produktion='$produktion' AND ap.artikel=a.id AND a.porto=1 AND ap.preis >= 0 AND a.id=ap.artikel");
        if($zahlungsweise=="nachnahme" && $nachnahme <2)
          $this->app->DB->Update("UPDATE produktion SET nachnahme_ok='0' WHERE id='$produktion' LIMIT 1");
        else
          $this->app->DB->Update("UPDATE produktion SET nachnahme_ok='1' WHERE id='$produktion' LIMIT 1");
      }
    } else {
      $summe_eingang = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang WHERE objekt='produktion' AND parameter='$produktion' AND adresse='$adresse'");
      $produktion_gesamtsumme = $this->app->DB->Select("SELECT gesamtsumme FROM produktion WHERE id='$produktion' LIMIT 1");
      $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM produktion WHERE id='$produktion' LIMIT 1");
      $zahlungsweise = strtolower($zahlungsweise);
      if($summe_eingang>=$produktion_gesamtsumme || ($zahlungsweise=="rechnung" || $zahlungsweise=="amazon"  || $zahlungsweise=="nachnahme" || $zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift" || $zahlungsweise=="bar"))
      { 
        $this->app->DB->Update("UPDATE produktion SET vorkasse_ok='1' WHERE id='$produktion' LIMIT 1");
      } else {
        $this->app->DB->Update("UPDATE produktion SET vorkasse_ok='0' WHERE id='$produktion' LIMIT 1");
      }
      $nachnahme = $this->app->DB->Select("SELECT COUNT(ap.id) FROM produktion_position ap, artikel a WHERE ap.produktion='$produktion' AND ap.artikel=a.id AND a.porto=1 AND ap.preis >= 0 AND a.id=ap.artikel");
      if($zahlungsweise=="nachnahme" && $nachnahme <2)
        $this->app->DB->Update("UPDATE produktion SET nachnahme_ok='0' WHERE id='$produktion' LIMIT 1");
      else
        $this->app->DB->Update("UPDATE produktion SET nachnahme_ok='1' WHERE id='$produktion' LIMIT 1");

    }
  }


  function AuftragEinzelnBerechnen($auftrag,$festreservieren=false)
  {
    $this->AuftragExplodieren($auftrag);

    //$this->BerechneDeckungsbeitrag($auftrag,"auftrag");

    $this->LoadSteuersaetzeWaehrung($auftrag,"auftrag");

    // reservieren nur wenn es manuell gemacht wurde oder im auftrag fest steht
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $reservierung = $this->app->DB->Select("SELECT reservierung FROM projekt WHERE id='$projekt' LIMIT 1");
    if($reservierung=="1" || $festreservieren)
      $this->AuftragReservieren($auftrag);

    $this->AuftragAutoversandBerechnen($auftrag);
  }

  function AuftragAutoversandBerechnen($auftrag)
  {
    $auftraege = $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE id='$auftrag'");    
    $adresse = $auftraege[0][adresse];
    $artikelarr= $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$auftrag' AND geliefert_menge < menge AND geliefert=0");



    //pruefe ob es mindestens eine reservierung gibt
    $reservierte = $this->app->DB->Select("SELECT COUNT(id) FROM lager_reserviert WHERE adresse='$adresse' AND datum>=NOW() AND objekt!='lieferschein'");
    if($reservierte >0)
    {
      $this->app->DB->Update("UPDATE auftrag SET reserviert_ok='1' WHERE id='$auftrag' LIMIT 1");
    } else 
      $this->app->DB->Update("UPDATE auftrag SET reserviert_ok='0' WHERE id='$auftrag' LIMIT 1");


    // liefertermin
    $liefertermincheck = $this->app->DB->Select("SELECT id FROM auftrag WHERE (tatsaechlicheslieferdatum<=NOW() OR tatsaechlicheslieferdatum IS NULL OR tatsaechlicheslieferdatum='0000-00-00') AND id='$auftrag'");
    if($liefertermincheck >0)
    {
      $this->app->DB->Update("UPDATE auftrag SET liefertermin_ok='1' WHERE id='$auftrag' LIMIT 1");
    } else 
      $this->app->DB->Update("UPDATE auftrag SET liefertermin_ok='0' WHERE id='$auftrag' LIMIT 1");

    //liefersperre 
    $liefersperre = $this->app->DB->Select("SELECT liefersperre FROM adresse WHERE id='$adresse'");
    if($liefersperre >0)
    {
      $this->app->DB->Update("UPDATE auftrag SET liefersperre_ok='0' WHERE id='$auftrag' LIMIT 1");
    } else 
      $this->app->DB->Update("UPDATE auftrag SET liefersperre_ok='1' WHERE id='$auftrag' LIMIT 1");


    //kreditlimit 
    $kreditlimit_freigabe = $this->app->DB->Select("SELECT kreditlimit_freigabe FROM auftrag WHERE id='$auftrag' LIMIT 1");

    if($this->KundeHatZR($adresse) || $this->KreditlimitCheck($adresse)==true || $kreditlimit_freigabe=="1")
    {
      $this->app->DB->Update("UPDATE auftrag SET kreditlimit_ok='1' WHERE id='$auftrag' LIMIT 1");
    } else 
      $this->app->DB->Update("UPDATE auftrag SET kreditlimit_ok='0' WHERE id='$auftrag' LIMIT 1");



    // Lager Check
    $positionen_vorhanden = 0;
    $artikelzaehlen=0;
    //echo "{$auftraege[0][internet]} Adresse:$adresse Auftrag $auftrag";
    for($k=0;$k<count($artikelarr); $k++)
    {
      $menge = $artikelarr[$k][menge] - $artikelarr[$k][gelieferte_menge];
      $artikel = $artikelarr[$k][artikel];
      $artikel_position_id = $artikelarr[$k][id];
      // pruefe artikel 12 menge 4
      // lagerartikel??
      $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");
      $stueckliste= $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");
      $juststueckliste= $this->app->DB->Select("SELECT juststueckliste FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");
      //if($artikelarr[$k][nummer]!="200000" && $artikelarr[$k][nummer]!="200001"  && $artikelarr[$k][nummer]!="200002" && $lagerartikel==1)
      //echo $artikelarr[$k][artikel]." ";
      if($lagerartikel==1)
      {
        //echo "HUHUH";
        if($this->LagerCheck($adresse,$artikel,$menge,"auftrag",$auftrag)>0) $positionen_vorhanden++;
        $artikelzaehlen++;
      }
    }


    $this->app->DB->Update("UPDATE auftrag SET teillieferung_moeglich='0' WHERE id='$auftrag' LIMIT 1");
    //echo "$positionen_vorhanden $artikelzaehlen<hr>";
    if($positionen_vorhanden==$artikelzaehlen)
      $this->app->DB->Update("UPDATE auftrag SET lager_ok='1' WHERE id='$auftrag' LIMIT 1");
    else {
      $this->app->DB->Update("UPDATE auftrag SET lager_ok='0' WHERE id='$auftrag' LIMIT 1");
      if($positionen_vorhanden > 0 && $artikelzaehlen > 0)
      {
        $this->app->DB->Update("UPDATE auftrag SET teillieferung_moeglich='1' WHERE id='$auftrag' LIMIT 1");
      }

    }       

    // projekt check start
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $projektcheck = $this->app->DB->Select("SELECT checkok FROM projekt WHERE id='$projekt' LIMIT 1");
    $projektcheckname = $this->app->DB->Select("SELECT checkname FROM projekt WHERE id='$projekt' LIMIT 1");
    $projektportocheck = $this->app->DB->Select("SELECT portocheck FROM projekt WHERE id='$projekt' LIMIT 1");


    if($projektcheck=="1")
    {
      //echo "projekt check $projektcheckname notwendig";
      include_once (dirname(__FILE__)."/../plugins/class.".$projektcheckname.".php");         

      $tmp = new unishop($this->app);
      if($tmp->CheckOK($auftrag))
        $this->app->DB->Update("UPDATE auftrag SET check_ok='1' WHERE id='$auftrag' LIMIT 1");
      else
        $this->app->DB->Update("UPDATE auftrag SET check_ok='0' WHERE id='$auftrag' LIMIT 1");

    }
    else
      $this->app->DB->Update("UPDATE auftrag SET check_ok='1' WHERE id='$auftrag' LIMIT 1");


    // autopruefung anstubsen
    //$this->AutoUSTPruefung($adresse);

    // UST Check
    // pruefe adresse 23 ust innerhalb 3 tagen vorhanden? wenn nicht schaue ob selber ordern kann wenn ja ordern und auf gruen

    $ustprf = $this->app->DB->Select("SELECT id FROM ustprf WHERE DATE_FORMAT(datum_online,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') AND adresse='$adresse' AND status='erfolgreich' LIMIT 1");
    $ustid = $this->app->DB->Select("SELECT ustid FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $land = $this->app->DB->Select("SELECT land FROM auftrag WHERE id='$auftrag' LIMIT 1");


    if($ust_befreit==0)
    {
      $this->app->DB->Update("UPDATE auftrag SET ust_ok='1' WHERE id='$auftrag' LIMIT 1");
    } 

    // Porto Check
    // sind versandkosten im auftrag
    $porto = $this->app->DB->Select("SELECT ap.id FROM auftrag_position ap, artikel a WHERE ap.auftrag='$auftrag' AND ap.artikel=a.id AND a.porto=1 AND ap.preis >= 0
        AND a.id=ap.artikel LIMIT 1");
    $keinporto = $this->app->DB->Select("SELECT keinporto FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $selbstabholer = $this->app->DB->Select("SELECT versandart FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$auftrag' LIMIT 1");


    // portocheck bei projekt



    if($selbstabholer=="selbstabholer" || $selbstabholer=="keinversand") $keinporto=1;

    if($projektportocheck==1)
    {
      if($porto > 0)
        $this->app->DB->Update("UPDATE auftrag SET porto_ok='1' WHERE id='$auftrag' LIMIT 1");
      else
        $this->app->DB->Update("UPDATE auftrag SET porto_ok='0' WHERE id='$auftrag' LIMIT 1");
    } else {
      //projekt hat kein portocheck porto ist immer ok
      $this->app->DB->Update("UPDATE auftrag SET porto_ok='1' WHERE id='$auftrag' LIMIT 1");
    }



    if($keinporto==1 || $selbstabholer=="selbstabholer")
    {
      $this->app->DB->Update("UPDATE auftrag SET porto_ok='1' WHERE id='$auftrag' LIMIT 1");
      //$this->app->DB->Update("UPDATE auftrag_position ap, artikel a SET ap.preis='0' WHERE ap.auftrag='$auftrag' AND a.id=ap.artikel AND a.porto='1'");
    }


    //Vorkasse Check
    //ist genug geld da? zusammenzaehlen der kontoauszuege_zahlungseingang
    $summe_eingang = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang WHERE objekt='auftrag' AND parameter='$auftrag' AND adresse='$adresse'");
    $auftrag_gesamtsumme = $this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $vorabbezahltmarkieren = $this->app->DB->Select("SELECT vorabbezahltmarkieren FROM auftrag WHERE id='$auftrag' LIMIT 1");

    $zahlungsweise = strtolower($zahlungsweise);
    if($summe_eingang>=$auftrag_gesamtsumme || ($zahlungsweise=="rechnung" || $zahlungsweise=="amazon" || $zahlungsweise=="nachnahme" || $zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift" || $zahlungsweise=="bar") || $auftrag_gesamtsumme==0 || $vorabbezahltmarkieren=="1")
    {
      $this->app->DB->Update("UPDATE auftrag SET vorkasse_ok='1' WHERE id='$auftrag' LIMIT 1");
    } else {
      $this->app->DB->Update("UPDATE auftrag SET vorkasse_ok='0' WHERE id='$auftrag' LIMIT 1");
    }

    //nachnahme gebuehr check!!!!
    //$nachnahme = $this->app->DB->Select("SELECT id FROM auftrag_position WHERE auftrag='$auftrag' AND nummer='200001' LIMIT 1");
    $nachnahme = $this->app->DB->Select("SELECT COUNT(ap.id) FROM auftrag_position ap, artikel a WHERE ap.auftrag='$auftrag' AND ap.artikel=a.id AND a.porto=1 AND ap.preis >= 0
        AND a.id=ap.artikel");

    if($zahlungsweise=="nachnahme" && $nachnahme <2)
      $this->app->DB->Update("UPDATE auftrag SET nachnahme_ok='0' WHERE id='$auftrag' LIMIT 1");
    else
      $this->app->DB->Update("UPDATE auftrag SET nachnahme_ok='1' WHERE id='$auftrag' LIMIT 1");
  }




  function EUR($betrag)
  {
    return number_format($betrag,2,",",".");
  }

  function KreditlimitCheck($adresse)
  {
    $kreditlimit = $this->app->DB->Select("SELECT kreditlimit FROM adresse WHERE id='$adresse' LIMIT 1");
    if($kreditlimit <=0) return true;
    // offene Rechnungen
    $rechnungen = $this->SaldoAdresse($adresse)*-1;

    $auftraege = $this->SaldoAdresseAuftrag($adresse);

    if($kreditlimit >= ($rechnungen+$auftraege))
      return true;
    else
      return false;
  }       

  function ReplaceBetrag($db,$value,$fromform)
  { 
    // wenn ziel datenbank
    if($db)
    {
      return str_replace(',','.',$value);
    }
    // wenn ziel formular
    else
    {
      //return $abkuerzung;
      return str_replace('.',',',$value);
    }
  }

  function ReplaceAdresse($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(id,' ',name) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;

      $tmp = trim($value);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE id='$rest' AND geloescht=0 LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    {
      return $id;
    }
    // wenn ziel formular
    else
    {
      return $abkuerzung;
    }
  }

  function ReplaceMitarbeiter($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;

      $tmp = trim($value);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$rest' AND mitarbeiternummer!='' AND geloescht=0 LIMIT 1");
      if($id <=0) $id=0;

    }

    // wenn ziel datenbank
    if($db)
    {
      return $id;
    }
    // wenn ziel formular
    else
    {
      return $abkuerzung;
    }
  }


  function ReplaceArtikel($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;

      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel WHERE id='$id' AND geloescht=0 LIMIT 1");
      if($id==0 || $id=="") $abkuerzung ="";
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      // wenn nummer keine DB id ist!
      $tmp = trim($value);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$rest' AND geloescht=0 AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      if($id <=0) $id=0;

    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }


  function ReplaceDecimal($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'.') > 0) $dbformat = 1;

    // wenn ziel datenbank
    if($db)
    { 
      if($dbformat) return $value;
      else { 
        if($value!="")
          return str_replace(',','.',$value);
        else return "";
      }
    }
    // wenn ziel formular
    else
    { 
      if($dbformat) { if($value!="") return $value; else return "";}
      else return $value;
    }
  }




  function ReplaceDatum($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) $dbformat = 1;

    // wenn ziel datenbank
    if($db)
    { 
      if($dbformat) return $value;
      else { 
        if($value!="")
          return $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
        else return "";
      }
    }
    // wenn ziel formular
    else
    { 
      if($dbformat) { if($value!="") return $this->app->String->Convert($value,"%1-%2-%3","%3.%2.%1");  else return "";}
      else return $value;
    }
  }



  function ReplaceAngebot($db,$value,$fromform)
  { 
    return $this->ReplaceANABRELSGSBE("angebot",$db,$value,$fromform);
  }

  function ReplaceLieferschein($db,$value,$fromform)
  { 
    return $this->ReplaceANABRELSGSBE("lieferschein",$db,$value,$fromform);
  }

  function ReplaceAuftrag($db,$value,$fromform)
  { 
    return $this->ReplaceANABRELSGSBE("auftrag",$db,$value,$fromform);
  }

  function ReplaceRechnung($db,$value,$fromform)
  { 
    return $this->ReplaceANABRELSGSBE("rechnung",$db,$value,$fromform);
  }

  function ReplaceBestellung($db,$value,$fromform)
  { 
    return $this->ReplaceANABRELSGSBE("bestellung",$db,$value,$fromform);
  }



  function ReplaceANABRELSGSBE($table,$db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT belegnr as name FROM $table WHERE id='$id' LIMIT 1");
      if($id==0 || $id=="") $abkuerzung ="";
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $tmp = trim($value);
      //$id =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$rest' AND geloescht=0 AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      $id =  $this->app->DB->Select("SELECT id FROM $table WHERE belegnr='$tmp' AND belegnr!='' LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }

  }



  function ReplaceKostenstelle($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(nummer,' ',beschreibung) FROM kostenstelle WHERE nummer='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT nummer FROM kostenstelle WHERE CONCAT(nummer,' ',beschreibung)='$value' LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }


  function ReplaceGruppe($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(name,' ',kennziffer) as name FROM gruppen WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM gruppen WHERE CONCAT(name,' ',kennziffer)='$value' OR kennziffer='$value' LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }


  function ReplaceProjekt($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$id' LIMIT 1");
      if($id<=0) $abkuerzung='';
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$value' LIMIT 1");
      if($value=='') $id=0;
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }

  function ReplaceLieferantennummer($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) { // wenn es eine id ist!
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT lieferantennummer as name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      if($id==0 || $id=="") $abkuerzung ="";
    } else {
      $abkuerzung = $value;
      $tmp = trim($value);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$rest' AND lieferantennummer!='' AND geloescht=0 AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }

  // Split 
  function FirstTillSpace($string)
  {
    $tmp = trim($string);
    $rest = explode(" ",$tmp);
    return $rest[0];
  }


  function ReplaceKundennummer($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) { // wenn es eine id ist!
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT kundennummer as name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      if($id==0 || $id=="") $abkuerzung ="";
    } else {
      $abkuerzung = $value;
      $tmp = trim($value);
      //$rest = substr($tmp, 0, 5);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$rest' AND kundennummer!='' AND geloescht=0 AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }


  function ReplaceKunde($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) as name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      if($id==0 || $id=="") $abkuerzung ="";
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $tmp = trim($value);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$rest' AND kundennummer!='' AND geloescht=0 AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      if($id <=0) $id=0;
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }



  function ReplaceLieferant($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(lieferantennummer,' ',name) as name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      if($id==0 || $id=="") $abkuerzung ="";
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $tmp = trim($value);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$rest' AND lieferantennummer!='' AND geloescht=0 AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      if($id <=0) $id=0;

    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }


  function CheckSamePage()
  {
    $id = $this->app->Secure->GetGET("id");
    $check_id  = strstr($_SERVER['HTTP_REFERER'], 'id=');
    if($check_id!="id=".$id)
      return true;
    else 
      return false;
  }

  function SeitenSperrAuswahl($ueberschrift,$meldung)
  {
    /* $this->app->Tpl->Set(SPERRMELDUNG,  '$("a#inline").fancybox({
       \'modal\': true,
       \'autoDimensions\': false,
       \'width\': 500,
       \'height\': 300
       });
       $(\'#inline\').click();');

       $this->app->Tpl->Set(SPERRMELDUNGNACHRICHT,'<a id="inline" href="#data"></a>
       <div style="display:none"><div id="data"><h2>'.$ueberschrift.'</h2><hr>von Benedikt Sauter<br><br><div class="info">'.$meldung.'</div>
       <br><br><center><a href="'.$_SERVER['HTTP_REFERER'].'">Jetzt Zur&uuml;ck zum letzten Schritt</a>&nbsp;|&nbsp;
       <a href="javascript:;" onclick="$.fancybox.close();">Bitte Fenster dennoch freigeben</a></center></div></div>');
     */

    $this->app->Tpl->Set(SPERRMELDUNG,  '
        // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
        $( "#dialog:ui-dialog" ).dialog( "destroy" );

        $( "#dialog-message" ).dialog({
modal: true,
buttons: {
Ok: function() {
$( this ).dialog( "close" );
}
}
});
        ');


    $this->app->Tpl->Set(SPERRMELDUNGNACHRICHT,'
        <div id="dialog-message" title="'.$ueberschrift.'">
        <p style="font-size: 9pt">
        '.$meldung.'
        </p>
        </div>
        ');
    }

function SeitenSperrInfo($meldung)
{
  $this->app->Tpl->Set(SPERRMELDUNG,  '$("a#inline").fancybox({
        \'hideOnContentClick\': true,
        \'autoDimensions\': false,
        \'width\': 500,
        \'height\': 300
        });
      $(\'#inline\').click();');

  $this->app->Tpl->Set(SPERRMELDUNGNACHRICHT,'<a id="inline" href="#data"></a>
      <div style="display:none"><div id="data"><h2>Infomeldung</h2><hr><br><br>'.$meldung.'</div></div>');

}

function AddArtikel($felder)
{
  $this->app->DB->Insert("INSERT INTO artikel (id) VALUES ('')");
  $id = $this->app->DB->GetInsertID();
  if($felder['firma']<=0)
    $felder['firma'] = $this->app->DB->Select("SELECT MAX(id) FROM firma LIMIT 1");

  if($felder['projekt']<=0)
    $felder['projekt'] = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$felder['firma']."' LIMIT 1");

  if($felder['firma']<=0) $felder['firma']=1;
  if($felder['projekt']<=0) $felder['projekt']=1;

  // so starten, dass alle uebertragen werden
  if($felder['cache_lagerplatzinhaltmenge']<=0) $felder['cache_lagerplatzinhaltmenge']=-999;

  $this->app->DB->UpdateArr("artikel",$id,"id",$felder);
  return $id;
}


function AddVerkaufspreisGruppe($artikel,$abmenge,$gruppe,$preis,$waehrung="EUR",$kundenartikelnummer="")
{
  if($abmenge<=0)$abmenge=1;

  $gruppe = $this->app->DB->Select("SELECT id FROM gruppen WHERE id='$gruppe' LIMIT 1");
  if($gruppe <=0)
    return;

  if($gruppe > 0)
    $check = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE ab_menge='".$abmenge."' AND gruppe='".$gruppe."' AND artikel='$artikel'  AND art='Gruppe'
        AND (gueltig_bis='0000-00-00' OR gueltig_bis >= NOW()) AND geloescht!='1' AND adresse <= 0 LIMIT 1");
  else
    $check = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE ab_menge='".$abmenge."' AND (gruppe='' OR gruppe='0') AND artikel='$artikel'  AND art='Gruppe'
        AND (gueltig_bis='0000-00-00' OR gueltig_bis >= NOW()) AND geloescht!='1' AND adresse <= 0 LIMIT 1");

  // soll man preis als ungueltig markieren?
  if($check > 0)
  {
    // noch nie dagewesen jetzt anlegen
    // ist der preis anders?
    $preis_alt = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$check' LIMIT 1");
    if($preis!=$preis_alt)
    {
      $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$check' LIMIT 1");
      $this->app->DB->Insert("INSERT INTO verkaufspreise (id,gruppe,artikel,angelegt_am,
        ab_menge,waehrung,preis,firma,kundenartikelnummer,adresse,art) 
          VALUES ('','$gruppe','$artikel',NOW(),'$abmenge','$waehrung','$preis','".$this->app->User->GetFirma()."','".$kundenartikelnummer."',0,'Gruppe')");
    } else {
      $this->app->DB->Update("UPDATE verkaufspreise SET kundenartikelnummer='$kundenartikelnummer', geloescht='$geloescht'
          WHERE id='$check' LIMIT 1");
    }
  } else {
    $this->app->DB->Insert("INSERT INTO verkaufspreise (id,gruppe,artikel,angelegt_am,
      ab_menge,waehrung,preis,firma,kundenartikelnummer,adresse,art) 
        VALUES ('','$gruppe','$artikel',NOW(),'$abmenge','$waehrung','$preis','".$this->app->User->GetFirma()."','".$kundenartikelnummer."',0,'Gruppe')");
  }
}

function AddVerkaufspreis($artikel,$abmenge,$adresse,$preis,$waehrung="EUR",$kundenartikelnummer="")
{
  if($adresse==="")
    return;

  if($abmenge<=0)$abmenge=1;

  if($adresse > 0)
  {
    $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE id='$adresse' LIMIT 1");
    if($adresse <=0)
      return;
  }

  if($adresse > 0)
    $check = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE ab_menge=".$abmenge." AND adresse=".$adresse." AND artikel=$artikel AND art='Kunde'
        AND (gueltig_bis='0000-00-00' OR gueltig_bis >= NOW()) AND geloescht!='1' LIMIT 1");
  else
    $check = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE ab_menge=".$abmenge." AND (adresse='' OR adresse=0) AND artikel=$artikel AND art='Kunde'
        AND (gueltig_bis='0000-00-00' OR gueltig_bis >= NOW()) AND geloescht!=1 LIMIT 1");

  // soll man preis als ungueltig markieren?
  if($check > 0)
  {
    // noch nie dagewesen jetzt anlegen
    // ist der preis anders?
    $preis_alt = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$check' LIMIT 1");
    if($preis!=$preis_alt)
    {
      $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$check' LIMIT 1");
      $this->app->DB->Insert("INSERT INTO verkaufspreise (id,adresse,artikel,angelegt_am,
        ab_menge,waehrung,preis,firma,kundenartikelnummer,art) 
          VALUES ('','$adresse','$artikel',NOW(),'$abmenge','$waehrung','$preis','".$this->app->User->GetFirma()."','".$kundenartikelnummer."','Kunde')");
    } else {
      $this->app->DB->Update("UPDATE verkaufspreise SET kundenartikelnummer='$kundenartikelnummer', geloescht='$geloescht'
          WHERE id='$check' LIMIT 1");
    }
  } else {
    $this->app->DB->Insert("INSERT INTO verkaufspreise (id,adresse,artikel,angelegt_am,
      ab_menge,waehrung,preis,firma,kundenartikelnummer,art) 
        VALUES ('','$adresse','$artikel',NOW(),'$abmenge','$waehrung','$preis','".$this->app->User->GetFirma()."','".$kundenartikelnummer."','Kunde')");
  }
}


function AddEinkaufspreis($artikel,$abmenge,$adresse,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung="",$vpe="")
{
  if($abmenge<=0) $abmenge=1;

  if($waehrung=="") $waehrung="EUR";

  $check = $this->app->DB->Select("SELECT id FROM einkaufspreise WHERE ab_menge='".$abmenge."' AND adresse='".$adresse."' AND artikel='$artikel' 
      AND (gueltig_bis='0000-00-00' OR gueltig_bis >= NOW()) AND geloescht!='1' LIMIT 1");
  // soll man preis als ungueltig markieren?
  if($check > 0)
  {
    // noch nie dagewesen jetzt anlegen
    // ist der preis anders?
    $preis_alt = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE id='$check' LIMIT 1");
    if($preis!=$preis_alt)
    {
      $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$check' LIMIT 1");
      //$this->AddEinkaufspreis($artikel,$abmenge,$adresse,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
      $this->app->DB->Insert("INSERT INTO einkaufspreise (id,adresse,artikel,bestellnummer,bezeichnunglieferant,      preis_anfrage_vom,
        ab_menge,waehrung,preis,firma,vpe) VALUES 
          ('','$adresse','$artikel','$bestellnummer','$bezeichnunglieferant',NOW(),'$abmenge','$waehrung','$preis','".$this->app->User->GetFirma()."','$vpe')");


    } else {
      $this->app->DB->Update("UPDATE einkaufspreise SET bestellnummer='$bestellnummer', bezeichnunglieferant='$bezeichnunglieferant'
          WHERE id='$check' LIMIT 1");
    }
  } else {
    //$this->AddEinkaufspreis($artikel,$abmenge,$adresse,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
    $this->app->DB->Insert("INSERT INTO einkaufspreise (id,adresse,artikel,bestellnummer,bezeichnunglieferant,      preis_anfrage_vom,
      ab_menge,waehrung,preis,firma,vpe) VALUES 
        ('','$adresse','$artikel','$bestellnummer','$bezeichnunglieferant',NOW(),'$abmenge','$waehrung','$preis','".$this->app->User->GetFirma()."','$vpe')");

  }

}

function EinkaufspreisBetrag($artikel,$menge,$lieferant,$projekt="")
{
  $id = $artikel;
  $adresse = $lieferant;

  $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
  $nummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') LIMIT 1");
  $projekt = $this->app->DB->Select("SELECT p.abkuerzung FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id='$id' LIMIT 1");
  $projekt_id = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");
  $ab_menge = $this->app->DB->Select("SELECT ab_menge FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') LIMIT 1");
  $ek = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') LIMIT 1");

  return $ek;
}


function Einkaufspreis($artikel,$menge,$lieferant,$projekt="")
{
  $id = $artikel;
  $adresse = $lieferant;

  $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
  $nummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') LIMIT 1");
  $projekt = $this->app->DB->Select("SELECT p.abkuerzung FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id='$id' LIMIT 1");
  $projekt_id = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");
  $ab_menge = $this->app->DB->Select("SELECT ab_menge FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') LIMIT 1");
  $ek = $this->app->DB->Select("SELECT id FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') LIMIT 1");

  return $ek;
}



function ArtikelVorschlagNachbestellen($artikel,$tage,$lager,$auftrag,$promonat,$inbestellung)
{
  /*
     $lager = $this->ArtikelImLager($artikel);
     $auftrag  = $this->ArtikelImAuftrag($artikel);

     $promonat = $this->ArtikelMittelwertMonat($artikel)/30*$tage; // bedarf fuer den zeitraum

     $inbestellung = $this->ArtikelBestellung($artikel);
   */
  $promonat = $promonat/30*$tage;

  if( $lager - $auftrag <= $promonat)
  {
    // falls noch nie was bestell worden ist
    if($promonat<=0)
      $ret = $auftrag - $inbestellung;
    else  
      $ret = $auftrag + $promonat - $inbestellung - $lager;
  }

  // mindest bestellmenge
  $mindestbestellung = $this->app->DB->Select("SELECT mindestbestellung FROM artikel WHERE id='$artikel' LIMIT 1");
  $mindestlager = $this->ArtikelMindestlager($artikel);

  // im lager sind zu wenig
  //   if($mindestlager > 0 &&( ($lager - $auftrag) < $mindestlager) && (($inbestellung +$lager - $auftrag) <= $mindestlager))
  //     $ret = $mindestlager;

  if($ret < $mindestlager && $ret > 0)
    $ret = $mindestlager;

  if($ret > 0 && $ret < $mindestbestellung)
    $ret = $mindestbestellung;


  if($ret <= 0) return ""; else return $ret;
}

function ArtikelBestellung($artikel)
{

  //$summe_in_bestellung  = $this->app->DB->Select("SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp WHERE bp.artikel='$artikel' AND bp.geliefert < bp.menge AND bp.abgeschlossen!='1'");
  $summe_in_bestellung  = $this->app->DB->Select("SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel='$artikel' AND bp.geliefert < bp.menge AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen!=1) AND b.status!='abgeschlossen' AND b.status!='freigegeben' AND b.status!='angelegt' AND b.status!='storniert'");


  if($summe_in_bestellung <= 0)
    return 0;

  return $summe_in_bestellung;
}

function ArtikelBestellungNichtVersendet($artikel)
{
  //$summe_in_bestellung  = $this->app->DB->Select("SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp WHERE bp.artikel='$artikel' AND bp.geliefert < bp.menge AND bp.abgeschlossen!='1'");
  $summe_in_bestellung  = $this->app->DB->Select("SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel='$artikel' AND bp.geliefert < bp.menge AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen!=1) AND (b.status='freigegeben' OR b.status='angelegt')");

  if($summe_in_bestellung <= 0)
    return 0;

  return $summe_in_bestellung;
}



function ArtikelVerkaufGesamt($artikel)
{

  $summe_im_auftrag  = $this->app->DB->Select("SELECT SUM(menge) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$artikel' AND a.status='abgeschlossen'");
  if($summe_im_auftrag<=0) $summe_im_auftrag=0;
  return $summe_im_auftrag;
}

function ArtikelInProduktion($artikel)
{

  $summe_im_auftrag  = $this->app->DB->Select("SELECT SUM(menge) FROM produktion_position ap LEFT JOIN produktion a ON a.id=ap.produktion WHERE ap.artikel='$artikel' 
      AND a.status='freigegeben'");

  //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND datum>=NOW() AND objekt!='lieferschein'");
  //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND datum>=NOW()");
  return $summe_im_auftrag;

}


function ArtikelImAuftrag($artikel)
{

  $summe_im_auftrag  = $this->app->DB->Select("SELECT SUM(menge) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$artikel' AND a.status='freigegeben'");

  //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND datum>=NOW() AND objekt!='lieferschein'");
  //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND datum>=NOW()");
  return $summe_im_auftrag;

}



function ArtikelImLager($artikel)
{

  $summe_im_lager = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel'");

  //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND datum>=NOW() AND objekt!='lieferschein'");
  //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND datum>=NOW()");
  return $summe_im_lager;

}


function VerbindlichkeitErweiterteBestellung($id)
{
  for($i=1;$i<=15;$i++)
  {
    $alleids[] = $this->app->DB->SelectArr("SELECT bestellung$i as bestellung FROM verbindlichkeit WHERE id='$id' AND bestellung$i > 0");
  }

  for($i=0;$i<count($alleids);$i++)
  {

    for($ij=0;$ij<count($alleids[$i]);$ij++)
    {
      $result[] = $alleids[$i][$ij]['bestellung'];
    }

  }
  return array_unique($result);
}


function BestellungErweiterteVerbindlichkeiten($id)
{
  for($i=1;$i<=15;$i++)
  {
    $alleids[] = $this->app->DB->SelectArr("SELECT id, bestellung{$i}betrag as betrag FROM verbindlichkeit WHERE bestellung$i='$id'");
  }

  for($i=0;$i<count($alleids);$i++)
  {

    for($ij=0;$ij<count($alleids[$i]);$ij++)
    {
      $result[$alleids[$i][$ij]['id']] = $alleids[$i][$ij]['betrag'];
    }

  }
  return $result;
}

function Bestellvorschlag($artikel,$basis_monate=3)
{
  if($basis_monate=="p") $basis_monate=0; // nur offene produktionen!
  $bedarf_in_zeitraum = $this->ArtikelMittelwertMonat($artikel,$basis_monate);
  return $bedarf_in_zeitraum;
}


// berechnet den mittelwert der letzten 6 monate wie oft der artikel verkauft worden ist pro monat
function ArtikelMittelwertMonat($artikel,$basis_monate=3)
{
  $check = $this->app->DB->Select("SELECT COUNT(id) FROM auftrag");
  $check =0;
  if($check >0)
  {
    // wieviel wurden komplett verkauft
    $gesamt =  $this->app->DB->Select("SELECT SUM(menge) FROM auftrag_position ap, auftrag a WHERE a.id=ap.auftrag 
        AND ap.artikel='$artikel' AND a.status='abgeschlossen' AND DATE_SUB(CURDATE(),INTERVAL $basis_monate MONTH) <= a.datum");
  } else {
    $gesamt =  $this->app->DB->Select("SELECT SUM(menge) FROM lager_bewegung WHERE artikel='$artikel' AND eingang='0' AND DATE_SUB(CURDATE(),INTERVAL $basis_monate MONTH) <=zeit");
  }

  /*
     $monate_gesamt  =  count($this->app->DB->SelectArr("SELECT COUNT(ap.menge), EXTRACT(MONTH FROM a.datum) as monat  FROM auftrag_position ap, auftrag a 
     WHERE a.id=ap.auftrag 
     AND ap.artikel='$artikel' AND a.status='abgeschlossen' AND DATE_SUB(CURDATE(),INTERVAL $basis_monate MONTH) <= a.datum"));
   */
  //echo "-".$monate_gesamt;

  return round($gesamt/$basis_monate);

  //    return $this->app->DB->Select("SELECT ROUND(SUM(menge)/12) FROM auftrag_position ap, auftrag a WHERE a.id=ap.auftrag 
  //      AND EXTRACT(YEAR FROM a.datum)=EXTRACT(YEAR FROM NOW()) AND ap.artikel='$artikel'");
}

function ArtikelMittelwertTag($artikel)
{
  return round($this->ArtikelMittelwertMonat($artikel)/30);
}

function get_emails ($str)
{
  $emails = array();
  $pattern="/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i"; 
  //preg_match_all("/\b\w+\@w+[\-\.\w+]+\b/", $str, $output);
  preg_match_all($pattern, $str, $output);
  foreach($output[0] as $email) array_push ($emails, trim(strtolower($email)));
  if (count ($emails) >= 1) return $emails;
  else return false;
}

function MahnwesenSend($rechnungsid,$drucker="")
{


  $mahnwesen = $this->app->DB->Select("SELECT mahnwesen FROM rechnung WHERE id='$rechnungsid' LIMIT 1");
  $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$rechnungsid' LIMIT 1");

  switch($mahnwesen)
  {
    case "zahlungserinnerung":
      $mailversand = $this->GetKonfiguration("mahnwesen_ze_versand");
      $mailversandtext = $this->GetKonfiguration("textz");
      break;
    case "mahnung1":
      $mailversand = $this->GetKonfiguration("mahnwesen_m1_versand");
      $mailversandtext = $this->GetKonfiguration("textm1");
      break;
    case "mahnung2":
      $mailversand = $this->GetKonfiguration("mahnwesen_m2_versand");
      $mailversandtext = $this->GetKonfiguration("textm2");
      break;
    case "mahnung3":
      $mailversand = $this->GetKonfiguration("mahnwesen_m3_versand");
      $mailversandtext = $this->GetKonfiguration("textm3");
      break;
    case "inkasso":
      $mailversand = $this->GetKonfiguration("mahnwesen_ik_versand");
      $mailversandtext = $this->GetKonfiguration("texti");
      break;
    default:
  }

  $email = $this->app->DB->Select("SELECT email FROM rechnung WHERE id='$rechnungsid' LIMIT 1");
  $to_name = $this->app->DB->Select("SELECT name FROM rechnung WHERE id='$rechnungsid' LIMIT 1");

  $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$rechnungsid' LIMIT 1");

  if($email=="" || $email ==$this->GetFirmaMail())
    $mailversand = 0;

  $Brief = new RechnungPDF($this->app,$projekt);

  if($mailversand)
  {
    // text von datenbank 
    //echo "rechnung $rechnungsid $email <br>";
    $Brief->GetRechnung($rechnungsid);
    $tmpbrief= $Brief->displayTMP();
    $body = $this->MahnwesenBody($rechnungsid,$mahnwesen);
    if($this->MahnwesenBetrag($rechnungsid)>0)
    {
      $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$email,$to_name,"Buchhaltung: Ihre offene Rechnung $belegnr",$body,array($tmpbrief),$projekt);
    } else {
      $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$this->GetFirmaMail(),$this->GetFirmaName(),"Buchhaltung: SYSTEMFEHLER! Mahnbetrag <=0 0 bei Rechnung $belegnr",$body,array($tmpbrief),$projekt);
    } 
    $per = "email";
  } else {
    //echo "rechnung $rechnungsid brief an drucker $drucker<br>";
    $Brief->GetRechnung($rechnungsid,$mahnwesen);
    $tmpbrief= $Brief->displayTMP();
    $this->app->printer->Drucken($drucker,$tmpbrief);

    unlink($tmpbrief);
    /* kein doppel drucken
       $Brief2 = new RechnungPDF(&$this->app);
       $Brief2->GetRechnung($rechnungsid,"doppel");
       $tmpbrief= $Brief2->displayTMP();
       $this->app->printer->Drucken($drucker,$tmpbrief);
       unlink($tmpbrief);
     */
    $per = "brief";
  }
  // protokoll eintrag in rechnung protokoll ob per brief oder mail wann was rausging!

  $this->RechnungProtokoll($rechnungsid,ucfirst($mahnwesen)." versendet am ".date('d.m.Y')." ($per)");

  // status aendern fuer mahnung
  $this->app->DB->Update("UPDATE rechnung SET mahnwesen='$mahnwesen', mahnwesen_datum=NOW(),versendet_mahnwesen='1' WHERE id='$rechnungsid'");

}


function MahnwesenBetrag($id)
{
  $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");

  // OfferNo, customerId, OfferDate

  $soll = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
  $ist = $this->app->DB->Select("SELECT ist FROM rechnung WHERE id='$id' LIMIT 1");


  return $soll - $ist;

}


function MahnwesenBody($id,$als)
{
  $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");

  // OfferNo, customerId, OfferDate

  $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM rechnung WHERE id='$id' LIMIT 1");
  $auftrag= $this->app->DB->Select("SELECT auftrag FROM rechnung WHERE id='$id' LIMIT 1");
  $buchhaltung= $this->app->DB->Select("SELECT buchhaltung FROM rechnung WHERE id='$id' LIMIT 1");
  $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM rechnung WHERE id='$id' LIMIT 1");
  $lieferscheinid = $lieferschein;
  $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
  $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM rechnung WHERE id='$id' LIMIT 1");
  $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");
  $datum_sql = $this->app->DB->Select("SELECT datum FROM rechnung WHERE id='$id' LIMIT 1");
  $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
  $doppel = $this->app->DB->Select("SELECT doppel FROM rechnung WHERE id='$id' LIMIT 1");
  $freitext = $this->app->DB->Select("SELECT freitext FROM rechnung WHERE id='$id' LIMIT 1");
  $ustid = $this->app->DB->Select("SELECT ustid FROM rechnung WHERE id='$id' LIMIT 1");
  $soll = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
  $ist = $this->app->DB->Select("SELECT ist FROM rechnung WHERE id='$id' LIMIT 1");
  $land = $this->app->DB->Select("SELECT land FROM rechnung WHERE id='$id' LIMIT 1");
  $mahnwesen_datum = $this->app->DB->Select("SELECT mahnwesen_datum FROM rechnung WHERE id='$id' LIMIT 1");
  $mahnwesen_datum_deutsch = $this->app->DB->Select("SELECT DATE_FORMAT(mahnwesen_datum,'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");
  $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$id' LIMIT 1");
  $zahlungsstatus = $this->app->DB->Select("SELECT zahlungsstatus FROM rechnung WHERE id='$id' LIMIT 1");
  $zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltage FROM rechnung WHERE id='$id' LIMIT 1");
  $zahlungszieltageskonto = $this->app->DB->Select("SELECT zahlungszieltageskonto FROM rechnung WHERE id='$id' LIMIT 1");
  $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM rechnung WHERE id='$id' LIMIT 1");

  $zahlungdatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltage DAY),'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");

  $zahlungsweise = strtolower($zahlungsweise);

  if($als=="zahlungserinnerung")
  {
    $body = $this->GetKonfiguration("textz");
    $tage = $this->GetKonfiguration("mahnwesen_m1_tage");
    $footer = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
  }
  else if($als=="mahnung1")
  {
    $body = $this->GetKonfiguration("textm1");
    $mahngebuehr = $this->GetKonfiguration("mahnwesen_m1_gebuehr");
    $tage = $this->GetKonfiguration("mahnwesen_m2_tage");
    $footer = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
  }
  else if($als=="mahnung2")
  {
    $body = $this->GetKonfiguration("textm2");
    $tage = $this->GetKonfiguration("mahnwesen_m3_tage");
    $mahngebuehr = $this->GetKonfiguration("mahnwesen_m2_gebuehr");
    $footer = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
  }
  else if($als=="mahnung3")
  {
    $body = $this->GetKonfiguration("textm3");
    $tage = $this->GetKonfiguration("mahnwesen_ik_tage");
    $mahngebuehr = $this->GetKonfiguration("mahnwesen_m3_gebuehr");
    $footer = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
  }
  else if($als=="inkasso")
  {
    $body = $this->GetKonfiguration("texti");
    //$tage = $this->GetKonfiguration("mahnwesen_ik_tage");
    $tage = 3; //eigentlich vorbei
    $mahngebuehr = $this->GetKonfiguration("mahnwesen_ik_gebuehr");
    $footer = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
  }
  else
  {
    $body = "Sehr geehrte Damen und Herren,\n\nanbei Ihre Rechnung.";
    $footer = "$freitext"."\n\n".$zahlungsweisetext."\n\nDieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.\n$steuer";
  }

  $datummahnung= $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD('$mahnwesen_datum', INTERVAL $tage DAY),'%d.%m.%Y')");
  $datumrechnungzahlungsziel= $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD('$datum_sql', INTERVAL $zahlungszieltage DAY),'%d.%m.%Y')");

  $tage_ze = $zahlungszieltage + $this->GetKonfiguration("mahnwesen_m1_tage");
  $datumzahlungserinnerung= $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD('$datum_sql', INTERVAL $tage_ze DAY),'%d.%m.%Y')");

  // checkstamp $this->CheckStamp("jhdskKUHsiusakiakuhsd"); // errechnet aus laufzeit und kundenid // wenn es nicht drinnen ist darf es nicht gehen

  if($mahngebuehr=="" || !is_numeric($mahngebuehr))
    $mahngebuehr = 0;

  //$offen= "11,23";


  $body = str_replace("{RECHNUNG}",$belegnr,$body);
  $body = str_replace("{DATUMRECHNUNG}",$datum,$body);
  $body = str_replace("{TAGE}",$tage,$body);
  $body = str_replace("{OFFEN}",number_format($soll - $ist,2),$body);
  $body = str_replace("{SOLL}",$soll,$body);
  $body = str_replace("{SUMME}",number_format($soll - $ist + $mahngebuehr,2),$body);
  $body = str_replace("{IST}",$ist,$body);
  $body = str_replace("{DATUM}",$datummahnung,$body);
  $body = str_replace("{MAHNGEBUEHR}",$mahngebuehr,$body);
  $body = str_replace("{MAHNDATUM}",$mahnwesen_datum_deutsch,$body);


  // Im Protokoll suchen Datum von Zahlungserinnerung, Mahnung 1, Mahnung 2, Mahnung 3

  $mahnung1 = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM rechnung_protokoll WHERE rechnung='$id'
      AND grund LIKE 'Mahnung1 versendet%' ORDER by Zeit DESC LIMIT 1");

  $mahnung2 = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM rechnung_protokoll WHERE rechnung='$id'
      AND grund LIKE 'Mahnung2 versendet%' ORDER by Zeit DESC LIMIT 1");

  $mahnung3 = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM rechnung_protokoll WHERE rechnung='$id'
      AND grund LIKE 'Mahnung3 versendet%' ORDER by Zeit DESC LIMIT 1");

  /*$datumzahlungerinnerungversendet = 
    $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM rechnung_protokoll WHERE rechnung='$id'
    AND grund LIKE 'Zahlungserinnerung versendet%' ORDER by Zeit DESC LIMIT 1");
   */


  $body = str_replace("{DATUMMAHNUNG1}",$mahnung1,$body);
  $body = str_replace("{DATUMMAHNUNG2}",$mahnung2,$body);
  $body = str_replace("{DATUMMAHNUNG3}",$mahnung3,$body);

  $body = str_replace("{DATUMZAHLUNGSERINNERUNG}",$datumzahlungserinnerung,$body);
  $body = str_replace("{DATUMRECHNUNGZAHLUNGSZIEL}",$datumrechnungzahlungsziel,$body);

  return $body;
}


function AufragZuDTA($auftrag,$rechnung="1")
{
  $arr = $this->app->DB->Select("SELECT belegnr, bank_inhaber, bank_konto, bank_blz, gesamtsumme, adresse, name FROM auftrag WHERE id='$auftrag'");


  if($rechnung=="1")
  {
    $arr[0][vz1] = "RE ".$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE auftrag='{$arr[0][belegnr]}' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
  } else
    $arr[0][vz1] = "";

  $this->app->DB->Insert("INSERT INTO dta (id,adresse,datum,name,konto,blz,betrag,vz1,firma)
      VALUES('','{$arr[0][adresse]}',NOW(),'{$arr[0][name]}','{$arr[0][konto]}','{$arr[0][blz]}','{$arr[0][betrag]}','{$arr[0][vz1]}','".$this->app->User->GetFirma()."')");

}

function PaketmarkeDPDEmbedded($parsetarget,$sid="",$zusatz="")
{
  if($sid=="")
    $sid= $this->app->Secure->GetGET("sid");

  if($zusatz=="express")
    $this->app->Tpl->Set(ZUSATZ,"Express");

  if($zusatz=="export")
    $this->app->Tpl->Set(ZUSATZ,"Export");


  $id = $this->app->Secure->GetGET("id");
  $drucken = $this->app->Secure->GetPOST("drucken");
  $anders = $this->app->Secure->GetPOST("anders");
  $land = $this->app->Secure->GetGET("land");
  $tracking_again = $this->app->Secure->GetGET("tracking_again");


  $versandmit= $this->app->Secure->GetPOST("versandmit");
  $trackingsubmit= $this->app->Secure->GetPOST("trackingsubmit");
  $versandmitbutton = $this->app->Secure->GetPOST("versandmitbutton");
  $tracking= $this->app->Secure->GetPOST("tracking");
  $versand = "dpd";  

  // mit trackingnummer   
  if($trackingsubmit!="")
  {

    if($sid=="versand")
    {
      // falche tracingnummer bei DPD da wir in der Funktion PaketmarkeDPDEmbedded sind
      if(strlen($tracking) < 14 || strlen($tracking) > 30)
      {
        header("Location: index.php?module=versanderzeugen&action=frankieren&id=$id&land=$land&tracking_again=1");
        exit;
      }
      else
      {
        $tracking = substr($tracking,8);
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
        //TODO anpassung, damit die Nummer an schluss entfernt wird
        $kundennummerdpd = $this->app->DB->Select("SELECT dpdkundennr FROM projekt WHERE id='$projekt' LIMIT 1");
        $tracking = str_replace($kundennummerdpd,"",$tracking);
        $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versand', tracking='$tracking',versendet_am=NOW(),
            versendet_am_zeitstempel=NOW(), abgeschlossen='1',logdatei=NOW() WHERE id='$id' LIMIT 1");

        //versand mail an kunden
        $this->Versandmail($id);

        header("Location: index.php?module=versanderzeugen&action=offene");
      }
      exit;
    } else {
      //direkt aus dem Lieferschein
      if($id > 0)
      {
        $adresse = $this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id='$id' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
        $kundennummerdpd = $this->app->DB->Select("SELECT dpdkundennr FROM projekt WHERE id='$projekt' LIMIT 1");
        //TODO anpassung, damit die Nummer an schluss entfernt wird
        $tracking = str_replace($kundennummerdpd,"",$tracking);
        $this->app->DB->Update("INSERT INTO versand (id,versandunternehmen, tracking,
          versendet_am,abgeschlossen,lieferschein,freigegeben,firma,adresse,projekt)
            VALUES ('','$versand','$tracking',NOW(),1,'$id',1,'".$this->app->User->GetFirma()."','$adresse','$projekt') ");
      }


    }
  }

  if($versandmitbutton!="")
  {

    if($sid=="versand")
    {
      $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versandmit',
          versendet_am=NOW(),versendet_am_zeitstempel=NOW(),abgeschlossen='1' WHERE id='$id' LIMIT 1");

      //versand mail an kunden
      $this->Versandmail($id);

      header("Location: index.php?module=versanderzeugen&action=offene");
      exit;
    }
  }




  if($anders!="")
  {
    //header("Location: index.php?module=paketmarke&action=tracking");
    $this->app->Tpl->Add(TAB1,
        "<br><br><table width=\"60%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><br>
        <h2>Methode:</h2><br><br>
        <form action=\"\" method=\"post\">Versandart: <input name=\"versandmit\" type=\"text\" id=\"tracking\"><script type=\"text/javascript\">document.getElementById(\"tracking\").focus(); </script>&nbsp;<input type=\"submit\" name=\"versandmitbutton\" value=\"Speichern\"></form>
        <br><br>
        </td></tr></table>
        <br><br>
        ");


  }
  else if($drucken!="" || $tracking_again=="1")
  {
    if($tracking_again!="1")
    {
      $nachnahme = $this->app->Secure->GetPOST("nachnahme");
      $betrag= $this->app->Secure->GetPOST("betrag");
      $versichert = $this->app->Secure->GetPOST("versichert");
      $extraversichert = $this->app->Secure->GetPOST("extraversichert");
      $name= $this->app->Secure->GetPOST("name");
      $name2= $this->app->Secure->GetPOST("name2");
      $name3= $this->app->Secure->GetPOST("name3");
      $land= $this->app->Secure->GetPOST("land");
      $plz= $this->app->Secure->GetPOST("plz");
      $ort= $this->app->Secure->GetPOST("ort");
      $strasse = $this->app->Secure->GetPOST("strasse");
      $hausnummer= $this->app->Secure->GetPOST("hausnummer");

      $rechnungsnummer = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1");
      $rechnungsnummer = "RE ".$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$rechnungsnummer' LIMIT 1");

      $kg = $this->app->Secure->GetPOST("kg1");
      $anzahl = $this->app->Secure->GetPOST("anzahl");
      if($kg=="") $kg=2;
      // speichern
      $this->app->DB->Update("UPDATE versand SET gewicht='$kg',anzahlpakete='$anzahl',paketmarkegedruckt=1 WHERE id='$id' LIMIT 1");

      if($nachnahme=="" && $versichert=="" && $extraversichert=="")
      {
        if($zusatz=="express")
          $this->DPDPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,1,101,"express");
        else if($zusatz=="export")
          $this->DPDPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,1,101,"export");
        else
          $this->DPDPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);

      } else if ($nachnahme=="1" && $versichert=="" && $extraversichert=="")
      {
        $this->DPDPaketmarkeNachnahme($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
      } 
      /*                      

                              {
                              $this->EasylogPaketmarke2500($name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);
                              } else if ($versichert=="1" && $extraversichert=="" && $nachnahme=="1")
                              {
                              $this->EasylogPaketmarkeNachnahme2500($name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
                              } else if ($versichert=="" && $extraversichert=="1" && $nachnahme=="1")
                              {
                              $this->EasylogPaketmarkeNachnahme25000($name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
                              } else if ($extraversichert=="1" && $versichert=="" && $nachnahme=="")
                              {
                              $this->EasylogPaketmarke25000($name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);
                              }
       */
    }
    //insert into versand oder update


    //insert into versand oder update
    if($anzahl > 1)
    {
      if($zusatz=="express" || $zusatz=="export") {
        $produkt_zusatz_nr=101; 
        $produkt_zusatz_id=1; 
      }
      else { 
        $produkt_zusatz_nr=""; 
        $produkt_zusatz_id=""; 
      }
      for($anzahli=2;$anzahli<=$anzahl; $anzahli++)
      {

        $kg = $this->app->Secure->GetPOST("kg".$anzahli);                                               
        if($kg <=0) $kg = 2;
        $this->DPDPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$produkt_zusatz_id,$produkt_zusatz_nr,$zusatz); //TODO 2kg
        $this->app->DB->Insert("INSERT INTO versandpakete (id,versand,gewicht,nr,versender) VALUES ('','$id','$kg','$anzahli','".$this->app->User->GetName()."')");
      }
    }

    if($tracking_again=="1")
    {
      $error_message_tracking = "<div class=\"error\">Fehlerhafte Trackingnummer!</div>";
    }

    //header("Location: index.php?module=paketmarke&action=tracking");
    $this->app->Tpl->Add(TAB1,
        "<br>$error_message_tracking<br><table width=\"60%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><br>
        <h2>Tracking-Nummer Scannen:</h2><br><br>
        <form action=\"\" method=\"post\">Tracking-Nummer: <input name=\"tracking\" type=\"text\" id=\"tracking\"><script type=\"text/javascript\">document.getElementById(\"tracking\").focus(); </script>&nbsp;<input type=\"submit\" name=\"trackingsubmit\" value=\"Speichern\"></form>
        <br><br>
        </td></tr></table>
        <br><br>
        ");

  } else {

    if($sid=="rechnung")
      $rechnung = $id;
    else $rechnung ="";

    if($sid=="versand")
    { 
      $tid = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1"); 
      $rechnung  = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1"); 
      $sid = "lieferschein";
    } else {
      $tid = $id;
    }


    if($sid=="rechnung" || $sid=="lieferschein" || $sid=="adresse")
    {
      $name = $this->app->DB->Select("SELECT name FROM $sid WHERE id='$tid' LIMIT 1");
      $name2 = $this->app->DB->Select("SELECT adresszusatz FROM $sid WHERE id='$tid' LIMIT 1");
      if($name2=="")
      {
        $name2 = $this->app->DB->Select("SELECT abteilung FROM $sid WHERE id='$tid' LIMIT 1");
        $abt=1;
      }
      $name3 = $this->app->DB->Select("SELECT ansprechpartner FROM $sid WHERE id='$tid' LIMIT 1");
      if($name3=="" && $abt!=1)
        $name3 = $this->app->DB->Select("SELECT abteilung FROM $sid WHERE id='$tid' LIMIT 1");

      $ort = $this->app->DB->Select("SELECT ort FROM $sid WHERE id='$tid' LIMIT 1");
      $plz = $this->app->DB->Select("SELECT plz FROM $sid WHERE id='$tid' LIMIT 1");
      $land = $this->app->DB->Select("SELECT land FROM $sid WHERE id='$tid' LIMIT 1");
      $strasse = $this->app->DB->Select("SELECT strasse FROM $sid WHERE id='$tid' LIMIT 1");
      $hausnummer = $this->ExtractStreetnumber($strasse);

      $strasse = str_replace($hausnummer,"",$strasse);
      $strasse = str_replace('.',"",$strasse);

      if($strasse=="")
      {
        $strasse = $hausnummer;
        $hausnummer = "";
      }

    }

    // wenn rechnung im spiel entweder durch versand oder direkt rechnung
    if($rechnung >0)
    {

      $zahlungsweise =  $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$rechnung' LIMIT 1");
      $soll =  $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$rechnung' LIMIT 1");
      $this->app->Tpl->Set(BETRAG,$soll);

      if($zahlungsweise=="nachnahme")
        $this->app->Tpl->Set(NACHNAHME,"checked");

      if($soll >= 500 && $soll <= 2500)
        $this->app->Tpl->Set(VERSICHERT,"checked");

      if($soll > 2500)
        $this->app->Tpl->Set(EXTRAVERSICHERT,"checked");

    }
    $this->app->Tpl->Set(NAME,$name);
    $this->app->Tpl->Set(NAME2,$name2);
    $this->app->Tpl->Set(NAME3,$name3);
    $this->app->Tpl->Set(ORT,$ort);
    $this->app->Tpl->Set(PLZ,$plz);
    $this->app->Tpl->Set(STRASSE,$strasse);
    $this->app->Tpl->Set(HAUSNUMMER,$hausnummer);
    $this->app->Tpl->Set(LAND,$land);


    $anzahl = $this->app->Secure->GetGET("anzahl");

    if($anzahl <= 0) $anzahl=1;

    $this->app->Tpl->Set(ANZAHL,$anzahl);

    if($anzahl==1)
    {
      $i=1;
      $this->app->Tpl->Add(GEWICHT,'<tr><td nowrap>Gewicht Paket:</td><td><input type="text" name="kg'.$i.'" size="5" value="2">&nbsp;<i>in kg</i></td></tr>');
    }       
    else {
      for($i=1;$i<=$anzahl;$i++)
        $this->app->Tpl->Add(GEWICHT,'<tr><td nowrap>Gewicht Paket '.$i.':</td><td><input type="text" name="kg'.$i.'" size="5" value="2">&nbsp;<i>in kg</i></td></tr>');
    }


    if($tracking_again!="1")
      $this->app->Tpl->Parse($parsetarget,"paketmarke_dpd.tpl");
  }
}

function PaketmarkeDHLEmbedded($parsetarget,$sid="",$zusatz="")
{
  $id = $this->app->Secure->GetGET("id");
  // entscheiden ob Intraship oder Easylog anhang Projekt einstellung
  // wenn sid==versand dann steht die id in der vesandtabelle
  if($sid=="versand")
  {
    $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
  } else {
    // ansonsten ist es die id vom lieferschein
    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
  }

  //TODO pruefe ob i oder easylog
  $intraship_enabled = $this->app->DB->Select("SELECT intraship_enabled FROM projekt WHERE id='$projekt' LIMIT 1"); 

  if($intraship_enabled=="1")
    $this->Paketmarke($parsetarget,$sid,$zusatz,"Intraship");
  else
    $this->Paketmarke($parsetarget,$sid,$zusatz,"DHL");

}

function Paketmarke($parsetarget,$sid="",$zusatz="",$typ="DHL")
{
  if($sid=="")
    $sid= $this->app->Secure->GetGET("sid");

  if($zusatz=="express")
    $this->app->Tpl->Set(ZUSATZ,"Express");

  if($zusatz=="export")
    $this->app->Tpl->Set(ZUSATZ,"Export");

  $id = $this->app->Secure->GetGET("id");
  $drucken = $this->app->Secure->GetPOST("drucken");
  $anders = $this->app->Secure->GetPOST("anders");
  $land = $this->app->Secure->GetGET("land");
  $tracking_again = $this->app->Secure->GetGET("tracking_again");


  $versandmit= $this->app->Secure->GetPOST("versandmit");
  $trackingsubmit= $this->app->Secure->GetPOST("trackingsubmit");
  $versandmitbutton = $this->app->Secure->GetPOST("versandmitbutton");
  $tracking= $this->app->Secure->GetPOST("tracking");
  $trackingsubmitcancel= $this->app->Secure->GetPOST("trackingsubmitcancel");

  if($typ=="DHL" || $typ=="dhl")
    $versand = "dhl";  
  else if($typ=="Intraship")
    $versand = "intraship";

  if($trackingsubmit!="" || $trackingsubmitcancel!="")
  {

    if($sid=="versand")
    {
      // falche tracingnummer bei DHL da wir in der Funktion PaketmarkeDHLEmbedded sind
      if((strlen($tracking) < 12 || strlen($tracking) > 20) && $trackingsubmitcancel=="" && ($typ=="DHL" || $typ=="Intraship"))
      {
        header("Location: index.php?module=versanderzeugen&action=frankieren&id=$id&land=$land&tracking_again=1");
        exit;
      }
      else
      {
        $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versand', tracking='$tracking',
            versendet_am=NOW(),versendet_am_zeitstempel=NOW(), abgeschlossen='1' WHERE id='$id' LIMIT 1");

        //versand mail an kunden
        $this->Versandmail($id);

        $weiterespaket=$this->app->Secure->GetPOST("weiterespaket");
        $lieferscheinkopie=$this->app->Secure->GetPOST("lieferscheinkopie");
        if($weiterespaket=="1")
        {
          if($lieferscheinkopie=="1") $lieferscheinkopie=0; else $lieferscheinkopie=1;
          //$this->app->erp->LogFile("Lieferscheinkopie $lieferscheinkopie");
          $all = $this->app->DB->SelectArr("SELECT * FROM versand WHERE id='$id' LIMIT 1");               
          $this->app->DB->Insert("INSERT INTO versand (id,adresse,rechnung,lieferschein,versandart,projekt,bearbeiter,versender,versandunternehmen,firma,
            keinetrackingmail,gelesen,paketmarkegedruckt,papieregedruckt,weitererlieferschein) 
              VALUES ('','{$all[0]['adresse']}','{$all[0]['rechnung']}','{$all[0]['lieferschein']}','{$all[0]['versandart']}','{$all[0]['projekt']}',
                '{$all[0]['bearbeiter']}','{$all[0]['versender']}','{$all[0]['versandunternehmen']}',
                '{$all[0]['firma']}','{$all[0]['keinetrackingmail']}','{$all[0]['gelesen']}',0,$lieferscheinkopie,1)");

          $newid = $this->app->DB->GetInsertID();
          header("Location: index.php?module=versanderzeugen&action=einzel&id=$newid");
        } else {
          header("Location: index.php?module=versanderzeugen&action=offene");
        }
      }
      exit;
    } else {
      //direkt aus dem Lieferschein
      if($id > 0)
      {
        $adresse = $this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id='$id' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
        $kg = $this->app->Secure->GetPOST("kg1");
        if($kg=="") $kg=2;

        $this->app->DB->Update("INSERT INTO versand (id,versandunternehmen, tracking,
          versendet_am,abgeschlossen,lieferschein,
          freigegeben,firma,adresse,projekt,gewicht,paketmarkegedruckt,anzahlpakete)
            VALUES ('','$versand','$tracking',NOW(),1,'$id',1,'".$this->app->User->GetFirma()."','$adresse','$projekt','$kg','1','1') ");
        header("Location: index.php?module=lieferschein&action=paketmarke&id=$id");
        exit;   
      }
    }       
  }

  if($versandmitbutton!="")
  {

    if($sid=="versand")
    {
      $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versandmit',
          versendet_am=NOW(),versendet_am_zeitstempel=NOW(),abgeschlossen='1' WHERE id='$id' LIMIT 1");

      //versand mail an kunden
      $this->Versandmail($id);

      header("Location: index.php?module=versanderzeugen&action=offene");
      exit;
    }
  }

  if($sid=="versand")
  {
    // wenn paketmarke bereits gedruckt nur tracking scannen
    $paketmarkegedruckt = $this->app->DB->Select("SELECT paketmarkegedruckt FROM versand WHERE id='$id' LIMIT 1");

    if($paketmarkegedruckt>=1)
      $tracking_again=1;
  }

  // wenn anders ausgewaehlt
  if($anders!="")
  {
    //header("Location: index.php?module=paketmarke&action=tracking");
    $this->app->Tpl->Add(TAB1,
        "<br><br><table width=\"60%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><br>
        <h2>Methode:</h2><br><br>
        <form action=\"\" method=\"post\">Versandart: <input name=\"versandmit\" type=\"text\" id=\"tracking\"><script type=\"text/javascript\">document.getElementById(\"tracking\").focus(); </script>&nbsp;<input type=\"submit\" name=\"versandmitbutton\" value=\"Speichern\">
        <br><br>
        <center>
        <br>
        <!--<input type=\"button\" name=\"paketmarkedrucken\" value=\"Doch als DHL Paketmarke drucken\" onclick=\"window.location.href='index.php?module=versanderzeugen&action=korrektur&id=$id'\">-->
        </center>
        </form>

        <br><br>
        </td></tr></table>
        <br><br>
        ");


  }
  else if($drucken!="" || $tracking_again=="1")
  {



    if($tracking_again!="1")
    {
      $nachnahme = $this->app->Secure->GetPOST("nachnahme");
      $betrag= $this->app->Secure->GetPOST("betrag");
      $versichert = $this->app->Secure->GetPOST("versichert");
      $extraversichert = $this->app->Secure->GetPOST("extraversichert");
      $name= $this->app->Secure->GetPOST("name");
      $name2= $this->app->Secure->GetPOST("name2");
      $name3= $this->app->Secure->GetPOST("name3");
      $land= $this->app->Secure->GetPOST("land");
      $plz= $this->app->Secure->GetPOST("plz");
      $ort= $this->app->Secure->GetPOST("ort");
      $strasse = $this->app->Secure->GetPOST("strasse");
      $hausnummer= $this->app->Secure->GetPOST("hausnummer");

      $rechnungsnummer = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1");
      $rechnungsnummer = "RE ".$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$rechnungsnummer' LIMIT 1");

      $kg = $this->app->Secure->GetPOST("kg1");
      $anzahl = $this->app->Secure->GetPOST("anzahl");

      if($kg=="") $kg=2;

      $this->app->DB->Update("UPDATE versand SET gewicht='$kg',paketmarkegedruckt=1,anzahlpakete='$anzahl' WHERE id='$id' LIMIT 1");

      switch($typ)
      {
        case "DHL":
          if($nachnahme=="" && $versichert=="" && $extraversichert=="")
          {
            $this->EasylogPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);
          } else if ($nachnahme=="1" && $versichert=="" && $extraversichert=="")
          {
            $this->EasylogPaketmarkeNachnahme($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
          } else if ($versichert=="1" && $extraversichert=="" && $nachnahme=="")
          {
            $this->EasylogPaketmarke2500($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);
          } else if ($versichert=="1" && $extraversichert=="" && $nachnahme=="1")
          {
            $this->EasylogPaketmarkeNachnahme2500($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
          } else if ($versichert=="" && $extraversichert=="1" && $nachnahme=="1")
          {
            $this->EasylogPaketmarkeNachnahme25000($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
          } else if ($extraversichert=="1" && $versichert=="" && $nachnahme=="")
          {
            $this->EasylogPaketmarke25000($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);
          }
        break;
        case "Intraship":
          if($nachnahme=="1")
            $this->IntrashipPaketmarkeNachnahme($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
          else
            $this->IntrashipPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);
        break;

      }
    }
    //insert into versand oder update
    if($anzahl > 1)
    {
      for($anzahli=2;$anzahli<=$anzahl; $anzahli++)
      {

        $kg = $this->app->Secure->GetPOST("kg".$anzahli);                                               
        if($kg <=0) $kg = 2;
        //$this->DPDPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$produkt_zusatz_id,$produkt_zusatz_nr,$zusatz); //TODO 2kg
        switch($typ)
        {
          case "DHL":
            $this->EasylogPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg); //TODO 1
          break;
          case "Intraship":
            $this->IntrashipPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg); //TODO 1
          break;
 
        }
        $this->app->DB->Insert("INSERT INTO versandpakete (id,versand,gewicht,nr,versender) VALUES ('','$id','$kg','$anzahli','".$this->app->User->GetName()."')");
      }

    }


    if($tracking_again=="1")
    {
      //$error_message_tracking = "<div class=\"error\">Bitte korrekte Trackingnummer erfassen!</div>";
    }

    if($sid=="versand")
      $paketmarkedrucken = "<input type=\"button\" name=\"paketmarkedrucken\" value=\"Paketemarke nochmal drucken\" onclick=\"window.location.href='index.php?module=versanderzeugen&action=korrektur&id=$id'\">";
    else $paketmarkedrucken="";

    //header("Location: index.php?module=paketmarke&action=tracking");
    $this->app->Tpl->Add(TAB1,
        "<br>$error_message_tracking<br><table width=\"60%\" style=\"background-color: #fff; border: solid 1px #000;\" align=\"center\">
        <tr>
        <td align=\"center\">
        <br><br>
        <h2>Tracking-Nummer Scannen:</h2><br><br>
        <form action=\"\" method=\"post\">Tracking-Nummer: <input name=\"tracking\" type=\"text\" id=\"tracking\"><script type=\"text/javascript\">document.getElementById(\"tracking\").focus(); </script>&nbsp;<input type=\"submit\" name=\"trackingsubmit\" value=\"Speichern\">&nbsp;
        <br><br>
        <center>
        <table>
        <tr><td><input type=\"checkbox\" name=\"weiterespaket\" value=\"1\" onclick=\"document.getElementById('tracking').focus()\"></td><td>&nbsp;Weitere Paketmarke f&uuml;r diese Lieferung erstellen.</td></tr>
        <tr><td><input type=\"checkbox\" name=\"lieferscheinkopie\" value=\"1\" onclick=\"document.getElementById('tracking').focus()\"></td><td>&nbsp;Zus&auml;tzlichen Lieferschein drucken.</td></tr>
        </table>
        </center>
        <br>
        <center>
        <br>
        $paketmarkedrucken
        <input type=\"submit\" name=\"trackingsubmitcancel\" value=\"Keine Tracking-Nummer erfassen\">
        </center>
        </form>
        <br><br>
        </td></tr></table>
        <br><br>
        ");


  } else {


    if($sid=="rechnung")
      $rechnung = $id;
    else $rechnung ="";

    if($sid=="versand")
    { 
      $tid = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1"); 
      $rechnung  = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1"); 
      $sid = "lieferschein";
    } else {
      $tid = $id;
    }


    if($sid=="rechnung" || $sid=="lieferschein" || $sid=="adresse")
    {
      $name = $this->app->DB->Select("SELECT name FROM $sid WHERE id='$tid' LIMIT 1");
      $name2 = $this->app->DB->Select("SELECT adresszusatz FROM $sid WHERE id='$tid' LIMIT 1");
      if($name2=="")
      {
        $name2 = $this->app->DB->Select("SELECT abteilung FROM $sid WHERE id='$tid' LIMIT 1");
        $abt=1;
      }
      $name3 = $this->app->DB->Select("SELECT ansprechpartner FROM $sid WHERE id='$tid' LIMIT 1");
      if($name3=="" && $abt!=1)
        $name3 = $this->app->DB->Select("SELECT abteilung FROM $sid WHERE id='$tid' LIMIT 1");

      $ort = $this->app->DB->Select("SELECT ort FROM $sid WHERE id='$tid' LIMIT 1");
      $plz = $this->app->DB->Select("SELECT plz FROM $sid WHERE id='$tid' LIMIT 1");
      $land = $this->app->DB->Select("SELECT land FROM $sid WHERE id='$tid' LIMIT 1");
      $strasse = $this->app->DB->Select("SELECT strasse FROM $sid WHERE id='$tid' LIMIT 1");
      $hausnummer = $this->ExtractStreetnumber($strasse);

      $strasse = str_replace($hausnummer,"",$strasse);
      $strasse = str_replace('.',"",$strasse);

      if($strasse=="")
      {
        $strasse = $hausnummer;
        $hausnummer = "";
      }

    }

    // wenn rechnung im spiel entweder durch versand oder direkt rechnung
    if($rechnung >0)
    {

      $zahlungsweise =  $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$rechnung' LIMIT 1");
      $soll =  $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$rechnung' LIMIT 1");
      $this->app->Tpl->Set(BETRAG,$soll);

      if($zahlungsweise=="nachnahme")
        $this->app->Tpl->Set(NACHNAHME,"checked");

      if($soll >= 500 && $soll <= 2500)
        $this->app->Tpl->Set(VERSICHERT,"checked");

      if($soll > 2500)
        $this->app->Tpl->Set(EXTRAVERSICHERT,"checked");

    }
    $this->app->Tpl->Set(NAME,$name);
    $this->app->Tpl->Set(NAME2,$name2);
    $this->app->Tpl->Set(NAME3,$name3);
    $this->app->Tpl->Set(ORT,$ort);
    $this->app->Tpl->Set(PLZ,$plz);
    $this->app->Tpl->Set(STRASSE,$strasse);
    $this->app->Tpl->Set(HAUSNUMMER,$hausnummer);
    $this->app->Tpl->Set(LAND,$land);

    $anzahl = $this->app->Secure->GetGET("anzahl");

    if($anzahl <= 0) $anzahl=1;

    $this->app->Tpl->Set(ANZAHL,$anzahl);

    if($anzahl==1)
    {
      $i=1;
      $this->app->Tpl->Add(GEWICHT,'<tr><td nowrap>Gewicht Paket:</td><td><input type="text" name="kg'.$i.'" size="5" value="2">&nbsp;<i>in kg</i></td></tr>');
    }       
    else {
      for($i=1;$i<=$anzahl;$i++)
        $this->app->Tpl->Add(GEWICHT,'<tr><td nowrap>Gewicht Paket '.$i.':</td><td><input type="text" name="kg'.$i.'" size="5" value="2">&nbsp;<i>in kg</i></td></tr>');
    }

    if($tracking_again!="1")
    {
      switch($typ)
      {
        case "DHL":
          $this->app->Tpl->Parse($parsetarget,"paketmarke_dhl.tpl");
        break;
        case "Intraship":
          $this->app->Tpl->Parse($parsetarget,"paketmarke_intraship.tpl");
        break;
 
      }
    }
  }
}




function ExtractStreetnumber($street)
{            
  //    preg_match("/([a-zA-Z\s\.\-\ß]+)\s(.*[0-9]+.*)/is", $street, $adresse);
  //  preg_match("/^([a-z\s]+)\s([0-9]+\s?[a-z]?)$/i", $street, $adresse);
  // The regular expression that gets the first set of digits in the address

  $address = $street;
  $number = preg_replace('/^.*?(\d+).*$/i', '$1', $address);


  // suche erstes vorkommen
  $pos = strpos($address,$number);

  $result = substr  ( $address, $pos);


  return $result;
}  

function DPDPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren=1,$produkt = 101,$service="")
{
  //$service =""; 
  $this->DPDPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service);
}


function DPDPaketmarkeNachnahme($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$nachnahmetext)
{
  $verfahren = 1; //NATIONAL
  $produkt = 101; //EUROPACK NATINONAL
  $service =""; //Nachnahme
  $betrag = str_replace(".",",",$betrag);
  $this->DPDPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service,$betrag,$nachnahmetext);
}


function IntrashipPaketmarkeNachnahme($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer)
{
  $this->IntrashipPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$rechnungsnummer);
}

function IntrashipPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg)
{
  $this->IntrashipPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg);
}

function IntrashipPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag="",$rechnungsnummer="")
{
 $module = $this->app->Secure->GetGET("module");
  //TODO Workarrond fuer lieferschein
  if($module=="lieferschein")
  {
    $lieferschein = $id;
  }
  else {
    $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
    if($lieferschein <=0) $lieferschein=$id;
  }

  $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

  $data = $this->app->DB->SelectArr("SELECT * FROM projekt WHERE id='$projekt' LIMIT 1");
  $data = $data[0];

  // your customer and api credentials from/for dhl
  $credentials = array(
    'user' => $data['intraship_user'],
    'signature' => $data['intraship_signature'],
    'ekp' => $data['intraship_ekp'],
    'api_user'  => $data['intraship_api_user'],
    'api_password'  => $data['intraship_api_password'],
    'log' => true
    );

  // your company info
  $info = array(
    'company_name'    => $data['intraship_company_name'],
    'street_name'     => $data['intraship_street_name'],
    'street_number'   => $data['intraship_street_number'],
    'zip'             => $data['intraship_zip'],
    'country'         => $data['intraship_country'],
    'city'            => $data['intraship_city'],
    'email'           => $data['intraship_email'],
    'phone'           => $data['intraship_phone'],
    'internet'        => $data['intraship_internet'],
    'contact_person'  => $data['intraship_contact_person']
  );
  // receiver details
  $customer_details = array(
    'name1'    => $name,
    'name2'     => $name2,
    'c/o'           => $name3,
    'street_name'   => $strasse,
    'street_number' => $hausnummer,
//    'country'       => 'germany',
    'country_code'       => $land,
    'zip'           => $plz,
    'city'          => $ort,
    'ordernumber'   => 'RE '.$rechnungsnummer,
    'weight'        => $kg
  );

  $dhl = new DHLBusinessShipment($credentials, $info);

  if($betrag > 0)
  {
    $bank_details = array(
      'account_owner' => $data['intraship_account_owner'],
      'account_number' => $data['intraship_account_number'],
      'bank_code' => $data['intraship_bank_code'],
      'bank_name' => $data['intraship_bank_name'],
      'note' => $data['intraship_note'],
      'iban' => $data['intraship_iban'],
      'bic' => $data['intraship_bic']
    );

    $cod_details = array(
      'amount'=>str_replace(",",".",$betrag),
      'currency'=>'EUR'
    );

    $shipment_details['WeightInKG'] = $data['intraship_WeightInKG'];
    $shipment_details['LengthInCM'] = $data['intraship_LengthInCM'];
    $shipment_details['WidthInCM'] = $data['intraship_WidthInCM'];
    $shipment_details['HeightInCM'] = $data['intraship_HeightInCM'];
    $shipment_details['PackageType'] = $data['intraship_PackageType'];

    $response = $dhl->createNationalShipment($customer_details,$shipment_details,$bank_details,$cod_details,$rechnungsnummer);
  } else {
    $customer_details['ordernumber']="";
    $response = $dhl->createNationalShipment($customer_details,$shipment_details);
  }

  if($response)
  {
    //$response['label_url']
    //$response['shipment_number']
    $tmppdf = $this->DownloadFile($response['label_url'],"Intraship_Versand_".$id."_");
    $this->Protokoll("Erfolg Paketmarke Drucker ".$data['intraship_drucker']," Datei: $tmppdf URL: ".$response['label_url']);
    $this->app->printer->Drucken($data['intraship_drucker'],$tmppdf); 
    unlink($tmppdf);
  } else {
    $dump = $this->VarAsString($dhl->errors);
    $this->Protokoll("Fehler Intraship API beim Erstellen Label fuer Versand $id LS $lieferschein",$dump);
  }
}

 

function EasylogPaketmarkeStandard($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg)
{
  $verfahren = 1;
  $produkt = 101;
  $service =""; 
  $this->EasylogPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service);
}


function EasylogPaketmarkeNachnahme($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$nachnahmetext)
{
  $verfahren = 1; //NATIONAL
  $produkt = 101; //EUROPACK NATINONAL
  $service =""; //Nachnahme
  $betrag = str_replace(".",",",$betrag);
  $this->EasylogPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service,$betrag,$nachnahmetext);
}

function EasylogPaketmarkeNachnahme2500($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$nachnahmetext)
{
  $verfahren = 1; //NATIONAL
  $produkt = 101; //EUROPACK NATINONAL
  $service ="119"; //Nachnahme
  $betrag = str_replace(".",",",$betrag);
  $this->EasylogPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service,$betrag,$nachnahmetext);
}

function EasylogPaketmarkeNachnahme25000($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$betrag,$nachnahmetext)
{
  $verfahren = 1; //NATIONAL
  $produkt = 101; //EUROPACK NATINONAL
  $service ="120"; //Nachnahme
  $betrag = str_replace(".",",",$betrag);
  $this->EasylogPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service,$betrag,$nachnahmetext);
}




function EasylogPaketmarke2500($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg)
{
  $verfahren = 1; //NATIONAL
  $produkt = 101; //EUROPACK NATINONAL
  $service ="119"; //bis 2500 eur versicherichert
  $this->EasylogPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service);
}

function EasylogPaketmarke25000($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg)
{
  $verfahren = 1; //NATIONAL
  $produkt = 101; //EUROPACK NATINONAL
  $service ="120"; //bis 25000 eur versicherichert
  $this->EasylogPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service);
}

function DPDPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service,$betrag="",$nachnahmetext="")
{

  $rechnung = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1");
  $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");

  $module = $this->app->Secure->GetGET("module");
  //TODO Workarrond fuer lieferschein
  if($module=="lieferschein")
  { 
    $lieferschein = $id;
  }
  else {
    $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
    if($lieferschein <=0) $lieferschein=$id;
  }

  $auftrag = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
  $auftragbelegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftrag' LIMIT 1");
  $internetnummer = $this->app->DB->Select("SELECT internetnummer FROM auftrag WHERE id='$auftrag' LIMIT 1");
  $ihrebestellnummer = $this->app->DB->Select("SELECT ihrebestellnummer FROM auftrag WHERE id='$auftrag' LIMIT 1");
  $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftrag' LIMIT 1");
  $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
  $email = $this->app->DB->Select("SELECT email FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
  $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
  $dpdkundennr     = $this->app->DB->Select("SELECT dpdkundennr FROM projekt WHERE id='$projekt' LIMIT 1");
  $dpdpfad         = $this->app->DB->Select("SELECT dpdpfad FROM projekt WHERE id='$projekt' LIMIT 1");
  $dpdformat       = $this->app->DB->Select("SELECT dpdformat FROM projekt WHERE id='$projekt' LIMIT 1");
  $paketmarke_einzeldatei = $this->app->DB->Select("SELECT paketmarke_einzeldatei FROM projekt WHERE id='$projekt' LIMIT 1");

  if($paketmarke_einzeldatei=="1")
    $dpdpfad = $dpdpfad."/dpd_".date('Ymd')."_".time()."_".$adresse."_".$lieferschein.".csv";

  if($rechnung > 0)
    $Referenz_2="RG".$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$rechnung' LIMIT 1");
  else
    $Referenz_2="L".$this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

  $lieferscheinnummer = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

  $Referenz=$adresse;
  $Referenz_1=$kundennummer;
  $Email=$email;

  $name = $this->ReadyForPDF($name);
  $name2 = $this->ReadyForPDF($name2);
  $name3 = $this->ReadyForPDF($name3);
  $strasse = $this->ReadyForPDF($strasse);
  $hausnummer = $this->ReadyForPDF($hausnummer);
  $plz = $this->ReadyForPDF($plz);
  $ort = $this->ReadyForPDF($ort);
  $land = $this->ReadyForPDF($land);
  $verfahren = $this->ReadyForPDF($verfahren);
  $nachnahmetext = $this->ReadyForPDF($nachnahmetext);
  $service = $this->ReadyForPDF($service);


  if($paketmarke_einzeldatei=="1")
    $datei = fopen( $dpdpfad, "w");
  else
    $datei = fopen( $dpdpfad, "a+");

  $GEWICHT=$kg;
  $STRASSE=$strasse;
  $HAUSNUMMER=$hausnummer;
  $WAEHRUNG="EUR";
  $PLZ =$plz;
  $ORT=$ort;
  $LAND=$land;
  $NACHNAHMEBETRAG=$betrag;
  $NN_Verwendungszweck=$auftragbelegnr;//$nachnahmetext;
  $DPDSENDUNDS_ID=$auftragbelegnr;

  if($land=="DE" && $nachnahmetext!="")
  {
    $DPDVERSANDART="NP, NN";
    $NN_Inkasso="BAR";
  }
  else
    $DPDVERSANDART="NP";

  $ADRESSE_2=$name2;
  $ANSCHRIFTEXTRATEXT=$name3;
  $FIRMA = $name;

  $DPDKUNDEN_ID=$dpdkundennr;

  $VORNAME = "";
  $NACHNAME = "";

  $Telefon="";
  $DPDANZAHL_DER_PAKETE="1";

  if($service=="express") $service = "GPT";
  else $service="GLS";

  //IMP;02251442001;471231;4386238 178734 Ellmauer;Porta Möbel;;Philipp-Reis-Str. 23-25;DE;63477;Maintal;1;GLS;

  if($dpdformat=="")
  {
    fwrite($datei,utf8_decode("{$DPDKUNDEN_ID}|{$FIRMA}|{$VORNAME} {$NACHNAME}|{$ANSCHRIFTEXTRATEXT}|{$STRASSE} {$HAUSNUMMER}|{$ADRESSE_2}|{$PLZ}|{$ORT}|{$LAND}|{$DPDVERSANDART}|{$NACHNAHMEBETRAG}|{$WAEHRUNG}|{$NN_Inkasso}|{$NN_Verwendungszweck}|{$Telefon}|{$Email}|{$Referenz}|{$Referenz_1}|{$Referenz_2}|{$DPDSENDUNDS_ID}|{$DPDANZAHL_DER_PAKETE}|{$GEWICHT}|{$Flex_Benachrichtigungstyp_1}|{$Flex_Kontaktdaten_1}|{$Flex_Bena_Ereignis_1}|{$Flex_Proak_Ben_Sprache}\r\n"));
  } else {

    $dpdformat = str_replace("{NAME}",$name,$dpdformat);
    $dpdformat = str_replace("{NAME2}",$name2,$dpdformat);
    $dpdformat = str_replace("{NAME3}",$name3,$dpdformat);
    $dpdformat = str_replace("{STRASSE}",$strasse,$dpdformat);
    $dpdformat = str_replace("{HAUSNUMMER}",$hausnummer,$dpdformat);
    $dpdformat = str_replace("{PLZ}",$plz,$dpdformat);
    $dpdformat = str_replace("{ORT}",$ort,$dpdformat);
    $dpdformat = str_replace("{LAND}",$land,$dpdformat);
    $dpdformat = str_replace("{GEWICHT}",$gewicht,$dpdformat);
    $dpdformat = str_replace("{VERFAHREN}",$verfahren,$dpdformat);
    $dpdformat = str_replace("{PRODUKT}",$produkt,$dpdformat);
    $dpdformat = str_replace("{SERVICE}",$service,$dpdformat);
    $dpdformat = str_replace("{BETRAG}",$betrag,$dpdformat);
    $dpdformat = str_replace("{NACHNAHMETEXT}",$nachnahmetext,$dpdformat);

    $dpdformat = str_replace("{LIEFERSCHEINNUMMER}",$lieferscheinnummer,$dpdformat);
    $dpdformat = str_replace("{KUNDENNUMMER}",$kundennummer,$dpdformat);
    $dpdformat = str_replace("{INTERNETNUMMER}",$internetnummer,$dpdformat);
    $dpdformat = str_replace("{IHREBESTELLNUMMER}",$ihrebestellnummer,$dpdformat);

    fwrite($datei,utf8_decode($dpdformat)."\r\n");
  }

  fclose($datei);
}



function EasylogPaketmarke($id,$name,$name2,$name3,$strasse,$hausnummer,$plz,$ort,$land,$kg,$verfahren,$produkt,$service,$betrag="",$nachnahmetext="")
{
  $name = $this->ReadyForPDF($name);
  $name2 = $this->ReadyForPDF($name2);
  $name3 = $this->ReadyForPDF($name3);
  $strasse = $this->ReadyForPDF($strasse);
  $hausnummer = $this->ReadyForPDF($hausnummer);
  $plz = $this->ReadyForPDF($plz);
  $ort = $this->ReadyForPDF($ort);
  $land = $this->ReadyForPDF($land);
  $verfahren = $this->ReadyForPDF($verfahren);
  $nachnahmetext = $this->ReadyForPDF($nachnahmetext);
  $service = $this->ReadyForPDF($service);

  $module = $this->app->Secure->GetGET("module");
  //TODO Workarrond fuer lieferschein
  if($module=="lieferschein")
  {
    $lieferschein = $id;
  }
  else {
    $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
    if($lieferschein <=0) $lieferschein=$id;
  }

  $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

  $dlhkundennr     = $this->app->DB->Select("SELECT dhlkundennr FROM projekt WHERE id='$projekt' LIMIT 1");
  $dhlpfad         = $this->app->DB->Select("SELECT dhlpfad FROM projekt WHERE id='$projekt' LIMIT 1");

  if($dhlpfad=="")
    $dhlpfad=$this->app->Conf->WFeasylog;

  //    $easylog_path = $this->app->Conf->WFeasylog;

  $datei = fopen( $dhlpfad, "a+");
  if($betrag!="")
    $betrag = "134=$betrag"; //frueher war das 114 mit Beleg, 134 = beleg los
  $anzahl = 1;

  if($land=="AT")
  {
    $verfahren = 53; // früher 1;
    $produkt= 5301; // früher war es "666";
    $teilnahme = "01-int"; //früher "02-AT";
  } else if ($land!="DE")
  {
    //WELT
    $verfahren = 53;
    $produkt= 5301;
    $teilnahme = "01-int";
  } else {
    //Deutschland
    $teilnahme = "01-nat";
    $verfahren = 1;
  }

  fwrite($datei,utf8_decode("$name;$name2;$name3;$strasse;$hausnummer;$plz;$ort;$land;$verfahren;$produkt;$service;$kg;$betrag;EUR;$anzahl;$nachnahmetext;$teilnahme\r\n"));
  fclose($datei);
}


function DatevVerbindlichkeitExport($von,$bis,$export=0,$sort="rechnungsdatum")
{
  function encodeToUtf8($string) {
    return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
  }

  $verbindlichkeiten = $this->app->DB->SelectArr("SELECT rechnung as belegnr, adresse, verwendungszweck,waehrung,
      DATE_FORMAT(rechnungsdatum,'%d.%m.%Y') as datum2, betrag
      FROM verbindlichkeit WHERE firma='".$this->app->User->GetFirma()."' AND 
      rechnungsdatum >='$von' AND rechnungsdatum<='$bis' ORDER by $sort");

  //FROM verbindlichkeit WHERE rechnungsfreigabe=1 AND freigabe=1 AND firma='".$this->app->User->GetFirma()."' AND 

  foreach($verbindlichkeiten as $key=>$value)
  {

    $value[name] = $this->app->DB->Select("SELECT LEFT(name,10) FROM adresse WHERE id='".$value[adresse]."' LIMIT 1");
    $value[land] = $this->app->DB->Select("SELECT land FROM adresse WHERE id='".$value[adresse]."' LIMIT 1");
    $value[ustid] = $this->app->DB->Select("SELECT ustid FROM adresse WHERE id='".$value[adresse]."' LIMIT 1");

    $tmp[datum2] = $value[datum2];
    $tmp[betrag] = $value[betrag];
    $tmp[waehrung] = $value[waehrung];
    $tmp[konto] = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='".$value[adresse]."' LIMIT 1");
    $tmp[belegfeld1] = $value[belegnr]; 
    $tmp[buchungstext] = $this->ReadyForPDF($this->UmlauteEntfernen($value[name]." ".$value[verwendungszweck]." ".$value[belegnr])); 
    //        $tmp[skonto_gegeben] = 0; // $value[skonto_gegeben]; 
    $tmp[land] = $value[land]; 
    $tmp[ustid] = str_replace(' ','',$value[ustid]); 

    //TODO DATEV
    if($value[ust_befreit]==1 && $value[ustid]!="")
      $tmp[gegenkonto]=$this->Firmendaten("steuer_aufwendung_inland_innergemeinschaftlich");//4125; 
    else if(($value[ust_befreit]==1 && $value[ustid]=="") || $value[ust_befreit]==2)
      $tmp[gegenkonto]=$this->Firmendaten("steuer_aufwendung_inland_import");//4120; //Drittland
    else if($value[ust_befreit]==0 && $value[land]=="DE")
      $tmp[gegenkonto]=$this->Firmendaten("steuer_aufwendung_inland_normal");//5400; //DE
    else 
      $tmp[gegenkonto]=$this->Firmendaten("steuer_aufwendung_inland_eunormal");//4315;  //privat EU

    $buchungenArr[]=$tmp;
    /*
       if($value[skonto_gegeben] > 0)
       {
       $tmp[datum2] = $value[datum2];
       $tmp[betrag] = -$value[skonto_gegeben];
       $tmp[konto] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$value[adresse]."' LIMIT 1"); //kundennummer
       $tmp[belegfeld1] = $value[belegnr];
       $tmp[buchungstext] = "SKONTO ".$this->ReadyForPDF($value[name]);
       $tmp[skonto_gegeben] = 0; // $value[skonto_gegeben];
       $tmp[land] = $value[land];
       $tmp[ustid] = str_replace(' ','',$value[ustid]); 

       if($value[ust_befreit]==1 && $value[ustid]!="")
       $tmp[gegenkonto]=4125;
       else if(($value[ust_befreit]==1 && $value[ustid]=="") || $value[ust_befreit]==2)
       $tmp[gegenkonto]=4120; //Drittland
       else if($value[ust_befreit]==0 && $value[land]=="DE")
       $tmp[gegenkonto]=4400; //DE
       else
       $tmp[gegenkonto]=4315;  //privat EU
       $buchungenArr[]=$tmp;

       }
     */
  }

  // Wir werden eine Datei ausgeben
  header('Content-Type: application/text; charset=utf-8');

  // Es wird downloaded.pdf benannt
  $datum=$von."_".$bis; 
  header('Content-Disposition: attachment; filename="'.$datum.'_DATEV_VERBINDLICHKEITEN_FORMAT3.csv"');

  for($i=0;$i<count($buchungenArr);$i++)
  {
    //if($buchungenArr[$i][skonto_gegeben]=="") $buchungenArr[$i][skonto_gegeben] =0;

    $betrag = $buchungenArr[$i][betrag];// - $buchungenArr[$i][skonto_gegeben];


    // ust id ersten beiden buchstaben abschneiden
    $buchungenArr[$i][ustid] = trim(ereg_replace("^[A-Za-z]{2}", "", $buchungenArr[$i][ustid])); 

    echo "\"".$buchungenArr[$i][datum2]."\";";
    echo "\"";

    if($buchungenArr[$i][haben]==1)
      echo "-";

    $buchungenArr[$i][betrag] = str_replace('.',',',$betrag);

    echo $buchungenArr[$i][betrag]."\";";
    //echo "\"".$buchungenArr[$i][skonto_gegeben]."\";";

    echo "\"".$buchungenArr[$i][konto]."\";";
    echo "\"".($buchungenArr[$i][belegfeld1])."\";";
    echo "\"".$this->ReadyForPDF(utf8_decode(substr($buchungenArr[$i][buchungstext],0,60)))."\";";
    echo "\"".$buchungenArr[$i][land]."\";";
    echo "\"".$buchungenArr[$i][ustid]."\";";
    echo "\"".$buchungenArr[$i][gegenkonto]."\";";
    echo "\"".$buchungenArr[$i][waehrung]."\"";
    echo "\r\n";

  }
  exit;
}


function DatevEinnahmenExport($von,$bis,$export="0",$sort="datum",$projekt="")
{
  function encodeToUtf8($string) {
    return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
  }

  if($projekt > 0)
  {
    $rechnungen = $this->app->DB->SelectArr("SELECT id,name,adresse,belegnr,waehrung,DATE_FORMAT(datum,'%d.%m.%Y') as datum2, soll as betrag, ust_befreit,land,ustid,skonto_gegeben FROM rechnung WHERE firma='".$this->app->User->GetFirma()."' AND 
        datum >='$von' AND datum<='$bis' AND belegnr>0 AND projekt='$projekt' ORDER by $sort");
  } else {
    $rechnungen = $this->app->DB->SelectArr("SELECT id,name,adresse,belegnr,waehrung,DATE_FORMAT(datum,'%d.%m.%Y') as datum2, soll as betrag, ust_befreit,land,ustid,skonto_gegeben FROM rechnung WHERE firma='".$this->app->User->GetFirma()."' AND 
        datum >='$von' AND datum<='$bis' AND belegnr>0 ORDER by $sort");
  }

  foreach($rechnungen as $key=>$value)
  {

    if($value[waehrung]=="") $value[waehrung]="EUR";

    $tmp[datum2] = $value[datum2];
    $tmp[betrag] = $value[betrag];
    $tmp[waehrung] = $value[waehrung];
    $tmp[konto] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$value[adresse]."' LIMIT 1"); //kundennummer
    $tmp[konto] = str_replace($this->Firmendaten("steuer_anpassung_kundennummer"),"",$tmp[konto]);
    $tmp[belegfeld1] = $value[belegnr]; 
    $tmp[buchungstext] = $this->ReadyForPDF($value[name]); 
    $tmp[skonto_gegeben] = 0; // $value[skonto_gegeben]; 
    $tmp[land] = $value[land]; 
    $tmp[ustid] = str_replace(' ','',$value[ustid]); 

    //TODO DATEV
    if($value[ust_befreit]==1 && $value[ustid]!="")
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_innergemeinschaftlich");//4125; 
    else if(($value[ust_befreit]==1 && $value[ustid]=="") || $value[ust_befreit]==2)
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_export");//4120; //Drittland
    else if($value[ust_befreit]==0 && ($value[land]=="DE" || $value[land]==""))
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_normal"); //4400 //DE
    else 
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_eunormal");//4315;  //privat EU

    // wenn andere steuerstaetze dabei sind dann diese auch mit nehmen
    $ermaessigt_summe = $this->RechnungZwischensummeSteuersaetzeBrutto2($value[id]);

    $ermaessigt = false;
    if($ermaessigt_summe > 0 && $value[ust_befreit]==0 && ($value[land]=="DE" || $value[land]=="")) // z.B. 7% erloese extra
    {
      $standard_summe = $tmp[betrag];
      $tmp[betrag]=round($tmp[betrag]-$ermaessigt_summe,2);
      // erst jetzt passt alles hinzufuegen zu expoer

      if($tmp[betrag] !=0) // keine null euro rechnungen
        $buchungenArr[]=$tmp; // standard buchung

      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_ermaessigt");
      $tmp[betrag] = round($ermaessigt_summe,2);
      $ermaessigt = true;
    } 

    // Falls eigene konto beim Kunden hinterlegt ist dieses nehmen
    $sachkonto = trim($this->app->DB->Select("SELECT sachkonto FROM adresse WHERE id='".$value[adresse]."' LIMIT 1")); //kundennummer
    if(strlen($sachkonto) > 4)
      $tmp[gegenkonto] = $sachkonto;

    // nur 0 anzeigen wenn es keinen ermassigten gibt 
    $buchungenArr[]=$tmp;

    /*
       if($value[skonto_gegeben] > 0)
       {
    //$tmp[betrag] = $standard_summe - $ermaessigt_summe;
    $tmp[datum2] = $value[datum2];
    $tmp[betrag] = -$value[skonto_gegeben];
    $tmp[konto] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$value[adresse]."' LIMIT 1"); //kundennummer
    $tmp[belegfeld1] = $value[belegnr];
    $tmp[buchungstext] = "SKONTO ".$this->ReadyForPDF($value[name]);
    $tmp[skonto_gegeben] = 0; // $value[skonto_gegeben];
    $tmp[land] = $value[land];
    $tmp[ustid] = str_replace(' ','',$value[ustid]); 

    if($value[ust_befreit]==1 && $value[ustid]!="")
    $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_innergemeinschaftlich"); // 4125
    else if(($value[ust_befreit]==1 && $value[ustid]=="") || $value[ust_befreit]==2)
    $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_export");//4120; //Drittland
    else if($value[ust_befreit]==0 && $value[land]=="DE")
    $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_normal");//4400; //DE
    else
    $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_eunormal")// 4315;  //privat EU
    $buchungenArr[]=$tmp;

    }
     */
  }

  if($projekt > 0)
  {
    $gutschriften= $this->app->DB->SelectArr("SELECT  id,name,adresse,waehrung,belegnr,DATE_FORMAT(datum,'%d.%m.%Y') as datum2, soll as betrag, ust_befreit,land,ustid FROM gutschrift WHERE firma='".$this->app->User->GetFirma()."' AND 
        datum >='$von' AND datum<='$bis'  AND belegnr > 0 AND status!='storniert' AND projekt='$projekt' ORDER by $sort");
  } else {
    $gutschriften= $this->app->DB->SelectArr("SELECT  id,name,adresse,waehrung,belegnr,DATE_FORMAT(datum,'%d.%m.%Y') as datum2, soll as betrag, ust_befreit,land,ustid FROM gutschrift WHERE firma='".$this->app->User->GetFirma()."' AND 
        datum >='$von' AND datum<='$bis'  AND belegnr > 0 AND status!='storniert' ORDER by $sort");


  }

  foreach($gutschriften as $key=>$value)
  {
    if($value[waehrung]=="") $value[waehrung]="EUR";

    $tmp[datum2] = $value[datum2];
    $tmp[betrag] = $value[betrag];
    $tmp[waehrung] = $value[waehrung];
    $tmp[haben] = 1;
    $tmp[konto] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$value[adresse]."' LIMIT 1"); //kundennummer
    $tmp[konto] = str_replace($this->Firmendaten("steuer_anpassung_kundennummer"),"",$tmp[konto]);
    $tmp[belegfeld1] = $value[belegnr]; 
    $tmp[buchungstext] = $this->ReadyForPDF($value[name]); 
    $tmp[land] = $value[land]; 
    $tmp[ustid] = str_replace(' ','',$value[ustid]); 

    if($value[ust_befreit]==1 && $value[ustid]!="")
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_innergemeinschaftlich"); // 4125; 
    else if(($value[ust_befreit]==1 && $value[ustid]=="") || $value[ust_befreit]==2)
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_export");//4120; //Drittland
    else if($value[ust_befreit]==0 && $value[land]=="DE")
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_normal");//4400; //DE
    else 
      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_eunormal");// 4315;  //privat EU

    // wenn andere steuerstaetze dabei sind dann diese auch mit nehmen
    $ermaessigt_summe = $this->GutschriftZwischensummeSteuersaetzeBrutto2($value[id]);
    if($ermaessigt_summe > 0 && $value[ust_befreit]==0 && ($value[land]=="DE" || $value[land]=="")) // z.B. 7% erloese extra
    {
      $standard_summe = $tmp[betrag];
      $tmp[betrag]=round($tmp[betrag]-$ermaessigt_summe,2);
      // erst jetzt passt alles hinzufuegen zu expoer

      if($tmp[betrag] !=0) // keine null euro rechnungen
        $buchungenArr[]=$tmp; // standard buchung

      $tmp[gegenkonto]=$this->Firmendaten("steuer_erloese_inland_ermaessigt");

      $tmp[betrag] = round($ermaessigt_summe,2);
    }
    // Falls eigene konto beim Kunden hinterlegt ist dieses nehmen
    $sachkonto = trim($this->app->DB->Select("SELECT sachkonto FROM adresse WHERE id='".$value[adresse]."' LIMIT 1")); //kundennummer
    if(strlen($sachkonto) > 4)
      $tmp[gegenkonto] = $sachkonto;

    $buchungenArr[]=$tmp;
  }
  // Wir werden eine PDF Datei ausgeben
  header('Content-Type: application/text; charset=utf-8');

  // Es wird downloaded.pdf benannt
  $datum=$von."_".$bis; 
  header('Content-Disposition: attachment; filename="'.$datum.'_DATEV_EINNAHMEN_FORMAT3.csv"');

  for($i=0;$i<count($buchungenArr);$i++)
  {
    //if($buchungenArr[$i][skonto_gegeben]=="") $buchungenArr[$i][skonto_gegeben] =0;

    $betrag = $buchungenArr[$i][betrag];// - $buchungenArr[$i][skonto_gegeben];


    // ust id ersten beiden buchstaben abschneiden
    $buchungenArr[$i][ustid] = trim(ereg_replace("^[A-Za-z]{2}", "", $buchungenArr[$i][ustid])); 

    echo "\"".$buchungenArr[$i][datum2]."\";";
    echo "\"";

    if($buchungenArr[$i][haben]==1)
      echo "-";

    $buchungenArr[$i][betrag] = str_replace('.',',',$betrag);

    echo $buchungenArr[$i][betrag]."\";";
    //echo "\"".$buchungenArr[$i][skonto_gegeben]."\";";

    echo "\"".$buchungenArr[$i][konto]."\";";
    echo "\"".($buchungenArr[$i][belegfeld1])."\";";
    echo "\"".$this->ReadyForPDF(utf8_decode(substr($buchungenArr[$i][buchungstext],0,60)))."\";";
    echo "\"".$buchungenArr[$i][land]."\";";
    echo "\"".$buchungenArr[$i][ustid]."\";";
    echo "\"".$buchungenArr[$i][gegenkonto]."\";";
    echo "\"".$buchungenArr[$i][waehrung]."\"";

    echo "\r\n";

  }
  exit;
}

function DatenBuchhaltungZM()
{
  //SELECT datum,soll,ustid FROM `rechnung` WHERE ustid!='' AND datum >='2010-07-01' AND datum <='2010-09-30' AND land!='DE'
}



function DatevBuchhaltungExport($von,$bis,$export,$sort="gegenkonto",$defaultgegenkonto="")
{
  // alles aus der tabelle datev_buchungen

  // fuer 2010

  $sql ="SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum2, umsatz as betrag, haben, konto, buchungstext,gegenkonto, belegfeld1, belegfeld2 FROM datev_buchungen
    WHERE datum<='$bis' AND datum>='$von' AND gegenkonto!='7777' AND gegenkonto!='77777' AND gegenkonto!='6301' Order by konto,datum DESC";

  $buchungenArr = $this->app->DB->SelectArr($sql);
  // Wir werden eine PDF Datei ausgeben
  header('Content-Type: application/text; charset=utf-8');

  // Es wird downloaded.pdf benannt
  $datum=$von."_".$bis;
  header('Content-Disposition: attachment; filename="'.$datum.'_DATEV_BUCHUNGEN_FORMAT3.csv"');

  for($i=0;$i<count($buchungenArr);$i++)
  {
    if($buchungenArr[$i][gegenkonto]=="") $buchungenArr[$i][gegenkonto]="1370";

    $buchungenArr[$i][buchungstext] = $this->ReadyForPDF((substr($buchungenArr[$i][buchungstext],0,60)));
    echo "\"".$buchungenArr[$i][datum2]."\";";
    echo "\"";

    if($buchungenArr[$i][haben]==0)
      echo "-";

    $buchungenArr[$i][betrag] = str_replace('.',',',$buchungenArr[$i][betrag]);

    echo $buchungenArr[$i][betrag]."\";";

    //TODO DATEV
    echo "\"".$buchungenArr[$i][konto]."\";";

    echo "\"".($buchungenArr[$i][belegfeld1])."\";";
    echo "\"".$this->ReadyForPDF(utf8_decode($buchungenArr[$i][buchungstext]))."\";";
    echo "\"".$leerland."\";";
    echo "\"".$leerustid."\";";
    echo "\"".$buchungenArr[$i][gegenkonto]."\"";
    echo "\r\n";
  }
  exit;

}



function DatevBuchhaltungExportALT($von,$bis,$export,$sort="gegenkonto",$defaultgegenkonto="")
{
  // alles aus der tabelle datev_buchungen


  $sql ="SELECT DATE_FORMAT(buchung,'%d.%m.%Y') as datum2, if(soll>0,soll,haben) as betrag, if(soll>0,'1','0') as haben, konto, 
    if(buchungstext='',vorgang,buchungstext) as buchungstext, gegenkonto, belegfeld1, '' as belegfeld2,id FROM kontoauszuege
      WHERE buchung<='$bis' AND buchung>='$von'  Order by konto, id";


  //  $sql ="SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum2, umsatz as betrag, haben, konto, buchungstext,gegenkonto, belegfeld1, belegfeld2 FROM datev_buchungen
  //    WHERE firma='".$this->app->User->GetFirma()."' AND datum<='$bis' AND datum>='$von' Order by $sort,datum,kontoauszug DESC";

  $buchungenArr = $this->app->DB->SelectArr($sql);
  // Wir werden eine PDF Datei ausgeben
  header('Content-type: application/text');

  // Es wird downloaded.pdf benannt
  $datum=$von."_".$bis;
  header('Content-Disposition: attachment; filename="'.$datum.'_DATEV_BUCHUNGEN.csv"');

  for($i=0;$i<count($buchungenArr);$i++)
  {

    //konto 1 - 6 austauschen mit richtigen nummern!
    if($buchungenArr[$i][konto]<20)
      $buchungenArr[$i][konto] = $this->app->DB->Select("SELECT datevkonto FROM konten WHERE id='".$buchungenArr[$i][konto]."' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    echo "\"".$buchungenArr[$i][datum2]."\";";
    /*
       if($buchungenArr[$i][gegenkonto]>70000)
       $buchungenArr[$i][gegenkonto] = 5400;

       if($buchungenArr[$i][gegenkonto]==22222)
       $buchungenArr[$i][gegenkonto] = 4400;
     */
    echo "\"";

    if($buchungenArr[$i][haben]==1)
      echo "-";

    $buchungenArr[$i][betrag] = str_replace('.',',',$buchungenArr[$i][betrag]);

    echo $buchungenArr[$i][betrag]."\";";

    echo "\"".$buchungenArr[$i][konto]."\";";
    //if($buchungenArr[$i][gegenkonto]<=0 || $buchungenArr[$i][gegenkonto]<0) {


    // sollte nie wieder passieren
    if($buchungenArr[$i][gegenkonto]<=0 && 0) {
      // versuch gegenkonto zu finden
      $gegenkonto = $this->app->DB->Select("SELECT gegenkonto FROM datev_buchungen WHERE kontoauszug='{$buchungenArr[$i][id]}' LIMIT 1");
      $ausgang = $this->app->DB->Select("SELECT adresse FROM kontoauszuege_zahlungsausgang WHERE kontoauszuege='{$buchungenArr[$i][id]}' LIMIT 1");

      $ausgang_parameter = $this->app->DB->Select("SELECT parameter FROM kontoauszuege_zahlungsausgang WHERE kontoauszuege='{$buchungenArr[$i][id]}' LIMIT 1");
      $ausgang_objekt = $this->app->DB->Select("SELECT objekt FROM kontoauszuege_zahlungsausgang WHERE kontoauszuege='{$buchungenArr[$i][id]}' LIMIT 1");

      $eingang = $this->app->DB->Select("SELECT adresse FROM kontoauszuege_zahlungseingang WHERE kontoauszuege='{$buchungenArr[$i][id]}' LIMIT 1");

      $eingang_parameter = $this->app->DB->Select("SELECT parameter FROM kontoauszuege_zahlungseingang WHERE kontoauszuege='{$buchungenArr[$i][id]}' LIMIT 1");
      $eingang_objekt = $this->app->DB->Select("SELECT objekt FROM kontoauszuege_zahlungseingang WHERE kontoauszuege='{$buchungenArr[$i][id]}' LIMIT 1");


      $kundennummer_ausgang = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$ausgang' LIMIT 1");
      $kundennummer_eingang = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$eingang' LIMIT 1");

      if($gegenkonto>0)
      {
        echo "\"".($buchungenArr[$i][belegfeld1])."\";";
        echo "\"".($buchungenArr[$i][buchungstext])."\";";
        echo "\"".$gegenkonto."\"";
      }
      else if($eingang >0)
      {
        $buchungstext = $this->app->DB->Select("SELECT CONCAT(name,' ',kundennummer) FROM adresse WHERE id='$eingang' LIMIT 1");
        $belegfeld1 = $this->app->DB->Select("SELECT belegnr FROM $eingang_objekt WHERE id='$eingang_parameter' LIMIT 1");
        echo "\"".($belegfeld1)."\";";
        echo "\"".($buchungstext)."\";";
        echo "\"".$kundennummer_eingang."\"";
      } 
      else if($ausgang >0)
      {
        $buchungstext = $this->app->DB->Select("SELECT CONCAT(name,' ',kundennummer) FROM adresse WHERE id='$ausgang' LIMIT 1");
        $belegfeld1 = $this->app->DB->Select("SELECT belegnr FROM $ausgang_objekt WHERE id='$ausgang_parameter' LIMIT 1");
        echo "\"".($belegfeld1)."\";";
        echo "\"".($buchungstext)."\";";
        echo "\"".$kundennummer_ausgang."\"";
      } else {
        $buchungenArr[$i][gegenkonto]=$defaultgegenkonto;
        echo "\"".($buchungenArr[$i][belegfeld1])."\";";
        echo "\"".($buchungenArr[$i][buchungstext])."\";";
        echo "\"".$buchungenArr[$i][gegenkonto]."\"";


      }

    } else 
    {
      //$buchungenArr[$i][gegenkonto]=$defaultgegenkonto;
      echo "\"".($buchungenArr[$i][belegfeld1])."\";";
      echo "\"".($buchungenArr[$i][buchungstext])."\";";
      echo "\"".$buchungenArr[$i][gegenkonto]."\"";
    }
    echo "\r\n";
  }
  exit;

  }


  function DatevAbgleich()
  {
    //alles was bei zahlungseingang passiert muss hier ausgewertet werden! aktuell fehlen kundenrechnungen, auftragsguthaben

    //hole alle nicht datev_abgeschlossenen zahlungen der kontoauszuege

    //auftragsguthaben
    //rechnung betreff=rechnungsnummer
    //verbindlichkeit
  }


  function SetKonfiguration($name,$dezimal=false)
  {

    $this->app->DB->Delete("DELETE FROM konfiguration WHERE name='$name' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    if($dezimal)
      $value = str_replace(',','.',$this->app->Secure->GetPOST($name));
    else
      $value = $this->app->Secure->GetPOST($name);

    $this->app->DB->Insert("INSERT INTO konfiguration (name,wert,firma,adresse) VALUES ('$name','$value','".$this->app->User->GetFirma()."','".$this->app->User->GetAdresse()."')");

  }


  function GetKonfiguration($name)
  {

    return $this->app->DB->Select("SELECT wert FROM konfiguration WHERE name='$name' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
  }


  function Folgebestaetigung($adresse)
  {

    $sperre = $this->app->DB->Select("SELECT folgebestaetigungsperre FROM adresse WHERE id='$adresse' LIMIT 1");
    if($sperre=="1") return 1;

    // hole alle freigebeben auftraege
    $auftragarr = $this->app->DB->SelectArr("SELECT id,belegnr,ihrebestellnummer,DATE_FORMAT(datum,'%d.%m.%Y') as datum2, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum2,
      liefertermin_ok  
      FROM auftrag WHERE adresse='$adresse' AND status='freigegeben' AND (lager_ok!=1 OR liefertermin_ok!=1) ORDER by lieferdatum");

    $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
  
    for($iauftrag=0;$iauftrag<count($auftragarr);$iauftrag++)
    {
      $auftrag = $auftragarr[$iauftrag]['id']; 

      $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$auftrag' LIMIT 1");
      $folgebestaetigung = $this->app->DB->Select("SELECT folgebestaetigung FROM projekt WHERE id='$projekt' LIMIT 1");    
      if($folgebestaetigung!=1)
        continue;

      if($auftragarr[$iauftrag]['lieferdatum2']=="00.00.0000") $auftragarr[$iauftrag]['lieferdatum2'] = "sofort";

      if($auftragarr[$iauftrag]['liefertermin_ok']!="1")
      {
        $artikeltabelleliefertermin .= "\r\n-Auftrag ".$auftragarr[$iauftrag]['belegnr']." vom ".$auftragarr[$iauftrag]['datum2']." Bestellung/Kommission: ".$auftragarr[$iauftrag]['ihrebestellnummer']." (geplanter Liefertermin: ".$auftragarr[$iauftrag]['lieferdatum2'].")\n";
      }
      else { 
        $artikeltabelle .= "\r\n-Auftrag ".$auftragarr[$iauftrag]['belegnr']." vom ".$auftragarr[$iauftrag]['datum2']." Bestellung/Kommission: ".$auftragarr[$iauftrag]['ihrebestellnummer']." (geplanter Liefertermin: ".$auftragarr[$iauftrag]['lieferdatum2'].")\n";
      } 
      //$to = $this->app->DB->Select("SELECT email FROM auftrag WHERE id='$auftrag' LIMIT 1");
      //$to_name = $this->app->DB->Select("SELECT name FROM auftrag WHERE id='$auftrag' LIMIT 1");

      $arr = $this->app->DB->SelectArr("SELECT ap.nummer, ap.bezeichnung, ap.menge, (SELECT SUM(lp.menge) FROM lager_platz_inhalt lp WHERE lp.artikel=ap.artikel) as lager, 
        (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=ap.artikel AND lr.datum>=NOW() AND lr.objekt!='lieferschein') as reserviert, 
        if(((SELECT SUM(lp.menge) FROM lager_platz_inhalt lp WHERE lp.artikel=ap.artikel) - (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=ap.artikel AND lr.datum>=NOW() AND lr.objekt!='lieferschein') - ap.menge)>=0,'',
          ((SELECT SUM(lp.menge) FROM lager_platz_inhalt lp WHERE lp.artikel=ap.artikel) - (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=ap.artikel AND lr.datum>=NOW() AND lr.objekt!='lieferschein') - ap.menge)
          ) as fehlend 
        FROM auftrag_position ap LEFT JOIN artikel a ON a.id=ap.artikel WHERE ap.auftrag='$auftrag' AND a.lagerartikel=1");

      foreach($arr as $value)
      {
        $artikel = $value[bezeichnung];
        $nummer = $value[nummer];
        $menge  = $value[menge];
        $lager  = $value[lager];
        $reserviert= $value[reserviert];

        if(($lager-$reserviert < $menge) && $auftragarr[$iauftrag]['liefertermin_ok']=="1")
        {
          $artikeltabelle .= "--Artikel: ".$artikel." (Nummer: $nummer) Menge: ".$menge."\n";
        }
        else 
        {
          $artikeltabelleliefertermin .= "--Artikel: ".$artikel." (Nummer: $nummer) Menge: ".$menge."\n";
        }
      }
    }

    if($artikeltabelle!="") $artikeltabelle ="Rückstand:\r\n".$artikeltabelle."\r\n";
    if($artikeltabelleliefertermin!="") $artikeltabelle .="Offene Aufträge:\r\n".$artikeltabelleliefertermin;
   
    $artikeltabelle = $this->app->erp->ReadyForPDF($artikeltabelle); 

    if($artikeltabelle!="")
    {
      $text = 'Lieber Kunde,

anbei übersenden wir Ihnen eine Liste mit den aktuell offenen Aufträgen (Rückstand und Aufträge mit Liefertermin):

'.$artikeltabelle.'

Bei Fragen zu Lieferungen wenden Sie sich gerne an unser Kundensupport-Center.';

      $betreff = "Folgebestätigung für offene Aufträge";
      //$to = "sauter@embedded-projects.net";
  
      if($to!="" && $to_name!="")
        $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$to,$to_name,$betreff,$text,"",$projekt);
      //echo $text;
    }

  }

  function LieferdatumEinkauf($artikelid)
  {
    return '0000-00-00';
    //return '2010-04-21';
  }




  function MahnwesenBezahltcheck()
  {
    //abbruch wenn die Option nicht gesetzt ist
    if($this->Firmendaten("mahnwesenmitkontoabgleich")!="1") return;

    // klappere rechnungen mit gutschriften ab und setzte diese auf bezahlt wenn die summe passt.
    // bzw. mahne restbetrag an
    $arrRechnungen = $this->app->DB->SelectArr("SELECT r.datum, r.belegnr, r.soll, r.ist as ist, r.projekt,
        r.mahnwesen_datum, r.zahlungsweise, r.zahlungszieltage, r.zahlungsstatus, r.mahnwesen, r.versendet_mahnwesen, r.id, r.auftragid, r.skonto_gegeben
        FROM rechnung r WHERE (r.zahlungsstatus!='bezahlt' OR r.ist!=r.soll) AND r.belegnr!=0 AND r.mahnwesen_gesperrt!=1 AND r.mahnwesenfestsetzen!=1");

    for($i=0;$i < count($arrRechnungen); $i++)
    {

      $rechnungsbelegnr = $arrRechnungen[$i][belegnr];
      $rechnungssoll = $arrRechnungen[$i][soll];
      $rechnungskonto = $arrRechnungen[$i][skonto_gegeben];
      $rechnungid= $arrRechnungen[$i][id];
      $auftragid = $arrRechnungen[$i][auftragid];
      $projekt = $arrRechnungen[$i][projekt];

      //$check = $this->app->DB->Select("SELECT id FROM gutschrift WHERE rechnungid='$rechnungid' AND status!='angelegt' LIMIT 1");
      //$summegutschrift  = $this->app->DB->Select("SELECT SUM(soll) FROM gutschrift WHERE rechnungid='$rechnungid' AND status!='angelegt' ");

      $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='bezahlt' WHERE id='$rechnungid' AND ist=soll AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      $tmpsaldo = $this->RechnungSaldo($rechnungid) + 0;

      // 0 oder mehr als 0 sogar      
      if($tmpsaldo >= 0)
      {
        if($tmpsaldo == 0)
          $this->app->DB->Update("UPDATE rechnung SET ist=soll, zahlungsstatus='bezahlt' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
        else
          $this->app->DB->Update("UPDATE rechnung SET ist=soll+$tmpsaldo, zahlungsstatus='bezahlt' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }
      else
      {
        $this->app->DB->Update("UPDATE rechnung SET ist=soll+".$tmpsaldo.", zahlungsstatus='offen' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }


      /*
         if($check > 0)
         {
      //echo "es gibt eine gs fuer $rechnungsbelegnr";
      if($summegutschrift >= $rechnungssoll)
      {
      $this->app->DB->Update("UPDATE rechnung SET ist=soll, zahlungsstatus='bezahlt' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }
      }
       */
      /*
         if($rechnungssoll==0)
         $this->app->DB->Update("UPDATE rechnung SET ist=soll, zahlungsstatus='bezahlt' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

      // pruefe ob passender auftrag mit gleicher summe bezahlt ist, dann ist rechnung auch bezahlt bzw buche auch die summen, wenn autrag zu wenig geld hat
      // pruefe ob es bezahlten auftrag gibt

      $id_zahlung = $this->app->DB->Select("SELECT id FROM kontoauszuege_zahlungseingang WHERE objekt='auftrag' AND parameter='$auftragid' LIMIT 1");
      if($id_zahlung > 0)
      {
      $betrag = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang 
      WHERE objekt='auftrag' AND parameter='$auftragid' AND parameter > 0 ") + $summegutschrift;

      $this->app->DB->Update("UPDATE rechnung SET ist='$betrag' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

      // Status anpassen
      $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='bezahlt' WHERE id='$rechnungid' AND ist=soll AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }

      $id_zahlung = $this->app->DB->Select("SELECT id FROM kontoauszuege_zahlungseingang WHERE objekt='rechnung' AND parameter='$rechnungid' LIMIT 1");
      if($id_zahlung > 0)
      {
      $betrag = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang 
      WHERE objekt='rechnung' AND parameter='$rechnungid' AND parameter > 0 ") + $summegutschrift;

      $this->app->DB->Update("UPDATE rechnung SET ist='$betrag'  WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

      $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='bezahlt' WHERE id='$rechnungid' AND ist=soll AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }
       */

      $tage_rechnung  = $this->app->DB->Select("SELECT DATEDIFF(NOW(),'{$arrRechnungen[$i][datum]}')");

      // letzter status versendet
      $tage  = $this->app->DB->Select("SELECT DATEDIFF(NOW(),'{$arrRechnungen[$i][mahnwesen_datum]}')");

      $tage_ze= $arrRechnungen[$i][zahlungszieltage];

      if($tage_ze==0) $tage_ze = $this->ZahlungsZielTage($projekt);

      $tage_m1 = $this->GetKonfiguration("mahnwesen_m1_tage");
      $tage_m2 = $this->GetKonfiguration("mahnwesen_m2_tage");
      $tage_m3 = $this->GetKonfiguration("mahnwesen_m3_tage");
      $tage_ik = $this->GetKonfiguration("mahnwesen_ik_tage");
      // bei wechsel immer eintrag in Rechnungs protokoll machen!!!!

      // versendet_mahnwesen auf '' setzten 


      // zahlungserinnerung
      if($arrRechnungen[$i][mahnwesen]=="" && ($tage_rechnung > $tage_ze) && $tage_ze > 0)
      {
        $this->app->DB->Update("UPDATE rechnung SET mahnwesen='zahlungserinnerung', mahnwesen_datum=NOW(),versendet_mahnwesen='0' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

      } elseif ($arrRechnungen[$i][mahnwesen]=="zahlungserinnerung" && $tage >= ($tage_m1) && $tage_m1 > 0)
      {
        $this->app->DB->Update("UPDATE rechnung SET mahnwesen='mahnung1', mahnwesen_datum=NOW(),versendet_mahnwesen='0' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      } elseif ($arrRechnungen[$i][mahnwesen]=="mahnung1" && $tage > ($tage_m2) && $tage_m2 > 0)
      {
        $this->app->DB->Update("UPDATE rechnung SET mahnwesen='mahnung2', mahnwesen_datum=NOW(),versendet_mahnwesen='0' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      } elseif ($arrRechnungen[$i][mahnwesen]=="mahnung2" && $tage > ($tage_m3) && $tage_m3 > 0)
      {
        $this->app->DB->Update("UPDATE rechnung SET mahnwesen='mahnung3', mahnwesen_datum=NOW(),versendet_mahnwesen='0' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

      } elseif ($arrRechnungen[$i][mahnwesen]=="mahnung3" && $tage > ($tage_ik) && $tage_ik > 0)
      {
        $this->app->DB->Update("UPDATE rechnung SET mahnwesen='inkasso', mahnwesen_datum=NOW(),versendet_mahnwesen='0' WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      } else {
        if($arrRechnungen[$i][versendet_mahnwesen]!=1)
          $this->app->DB->Update("UPDATE rechnung SET mahnwesen_datum=NOW() WHERE id='$rechnungid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }
    }
  }



  function GetGeschaeftsBriefText($subjekt,$sprache="",$projekt="")
  {

    if($sprache!="deutsch" && $sprache!="englisch")
      $sprache = "deutsch";

    if($projekt > 0)
      $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='$subjekt' AND sprache='$sprache' AND projekt='$projekt' LIMIT 1");

    if($text == "")
      $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='$subjekt' AND sprache='$sprache' AND (projekt='0' OR projekt='')  LIMIT 1");

    if($text == "")
      $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='$subjekt' AND sprache='$sprache' LIMIT 1");

    $text = str_replace('{FIRMA}',$this->Firmendaten("name"),$text);

    return $text;
  }

  function GetGeschaeftsBriefBetreff($subjekt,$sprache="",$projekt="")
  {

    if($sprache!="deutsch" && $sprache!="englisch")
      $sprache = "deutsch";

    if($projekt > 0)
      $text = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='$subjekt' AND sprache='$sprache' AND projekt='$projekt' LIMIT 1");

    if($text == "")
      $text = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='$subjekt' AND sprache='$sprache' AND (projekt='0' OR projekt='')  LIMIT 1");

    if($text == "")
      $text = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='$subjekt' AND sprache='$sprache' LIMIT 1");

    $betreff = str_replace('{FIRMA}',$this->Firmendaten("name"),$betreff);
    return $text;
  }

  function Stornomail($auftrag)
  {
    $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $stornomail = $this->app->DB->Select("SELECT stornomail FROM projekt WHERE id='$projekt' LIMIT 1");

    // KEINE STORNOMAIL
    if($stornomail!=1)
      return;


    $to = $this->app->DB->Select("SELECT email FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM auftrag WHERE id='$auftrag' LIMIT 1");

    $parameter = $auftrag;

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftrag' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $keinestornomail = $this->app->DB->Select("SELECT keinestornomail FROM auftrag WHERE id='$auftrag' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    if($belegnr>0 && $keinestornomail==0)
    {
      $text = $this->GetGeschaeftsBriefText('Stornierung','deutsch',$projekt);
      $betreff = $this->GetGeschaeftsBriefBetreff('Stornierung','deutsch',$projekt);
      //              $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='Stornierung' AND sprache='deutsch' LIMIT 1");
      //              $betreff = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='Stornierung' AND sprache='deutsch' LIMIT 1");

      $text= str_replace('{AUFTRAG}',$belegnr,$text);
      $text= str_replace('{GESAMT}',$this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$parameter' LIMIT 1"),$text);
      $text= str_replace('{DATUM}',$this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM auftrag WHERE id='$parameter' LIMIT 1"),$text);
      $text = str_replace('{FIRMA}',$this->Firmendaten("name"),$text);

      if($to!="" && $to_name!="")
        $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$to,$to_name,$betreff,$text,"",$projekt);

    } 
  }

  function ExportlinkZahlungsmail()
  {
    $exports = $this->app->DB->SelectArr("SELECT * FROM exportlink_sent WHERE mail='0'");

    for($i=0;$i<count($exports);$i++)
      //for($i=0;$i<5;$i++)
    {
      // mail
      $adresse = $exports[$i][adresse];
      $reg= $exports[$i][reg];
      $artikelid = $exports[$i][objekt];

      $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' LIMIT 1");
      $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
      $artikel = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikelid' LIMIT 1");

      $projekt=1;

      //      $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='AlternativArtikel' AND sprache='deutsch' AND (projekt='$projekt' OR projekt='0') LIMIT 1");
      //     $betreff = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='AlternativArtikel' AND sprache='deutsch' AND (projekt='$projekt' OR projekt='0') LIMIT 1");


      $text = $this->GetGeschaeftsBriefText('AlternativArtikel','deutsch',$projekt);
      $betreff = $this->GetGeschaeftsBriefBetreff('AlternativArtikel','deutsch',$projekt);

      $betreff = str_replace('[ARTIKEL]',$artikel,$betreff);
      $text= str_replace('[ARTIKEL]',$artikel,$text);
      $text = str_replace('[AUFTRAG]',$auftrag,$text);

      $text = str_replace('[REG]',$reg,$text);


      $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$to,$to_name,$betreff,$text,"",$projekt);
      echo $to_name." <".$to.">\r\n";

      $this->app->DB->Update("UPDATE exportlink_sent SET mail=1 WHERE reg='$reg' LIMIT 1");

    }
  }


  function AuftragZahlungsmail($id="",$force=0)
  {
    if(!is_numeric($id))
      $id = $this->app->Secure->GetGET("id");
    else $intern=1;

    $belegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
    $adresse= $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");

    $summeimauftrag = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang WHERE objekt='auftrag' AND parameter='$id'");
    $auftragssumme = $this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
    $zahlungsmail = $this->app->DB->Select("SELECT zahlungserinnerung FROM projekt WHERE id='$projekt' LIMIT 1");

    // sind vorbedingungen erfuellt?
    $vorbedinungen = 0;
    $zahlungsmailbedinungen = $this->app->DB->Select("SELECT zahlungsmailbedinungen FROM projekt WHERE id='$projekt' LIMIT 1");

    if(preg_match("/lager_ok/",$zahlungsmailbedinungen))
    {
      $lager_ok = $this->app->DB->Select("SELECT lager_ok FROM auftrag WHERE id='$id' LIMIT 1");
      if($lager_ok==0)
        $vorbedinungen++;
    }

    // Kundencheck
    if(preg_match("/check_ok/",$zahlungsmailbedinungen))
    {
      $check_ok = $this->app->DB->Select("SELECT check_ok FROM auftrag WHERE id='$id' LIMIT 1");
      if($check_ok==0)
        $vorbedinungen++;
    }

    //echo "zahlungsmail $zahlungsmail $vorbedinungen $auftragssumme $belegnr\r\n ";
    if(($zahlungsmail > 0) && ($vorbedinungen==0) && ($auftragssumme>0) && ($id>0))
    {
      //echo "verschickt";
      $this->Zahlungsmail($adresse,$auftragssumme-$summeimauftrag,$id,$force);
    }

    if($intern!=1)
    {
      header("Location: index.php?module=auftrag&action=edit&id=$id");
      exit;
    }

  }


  function AufgabenMail($aufgabe)
  {
    $arraufgabe = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE id='$aufgabe' LIMIT 1");

    $adresse = $arraufgabe[0]["adresse"];

    $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    $aufgabe_name = $arraufgabe[0]["aufgabe"];
    $beschreibung = $arraufgabe[0]["beschreibung"];
    $datum = $arraufgabe[0]["abgabe_bis"];


    $text = "Aufgabe: $aufgabe_name\r\n";
    $text .= "Abgabe bis: $datum\r\n";
    $text .= "Beschreibung: $beschreibung\r\n";

    $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$to,$to_name,"Erinnerung Aufgabe: ".$aufgabe_name,$text);
  }


  function Zahlungsmail($adresse,$rest="",$auftragid="",$force=0)
  {
    if(!is_numeric($auftragid))
      return;

    $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    //$to = ""; //DEBUG
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
    $internetnummer = $this->app->DB->Select("SELECT internet FROM auftrag WHERE id='$auftragid' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$auftragid' LIMIT 1");
    $zahlungsmail = $this->app->DB->Select("SELECT zahlungserinnerung FROM projekt WHERE id='$projekt' LIMIT 1");
    $zahlungsmailcounter = $this->app->DB->Select("SELECT zahlungsmailcounter FROM auftrag WHERE id='$auftragid' LIMIT 1");
    $check_adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftragid' LIMIT 1");


    // wenn der auftrag dem Kunden nicht gehört
    if($adresse!=$check_adresse) return;

    $gesamt = $this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$auftragid' LIMIT 1");

    if($rest!="")
    {

      //Falls projekt mail vorhanden sonst globalen firmen standard
      if($gesamt-$rest==0)
      {
        $text = $this->GetGeschaeftsBriefText('ZahlungMiss','deutsch',$projekt);
        $betreff = $this->GetGeschaeftsBriefBetreff('ZahlungMiss','deutsch',$projekt);
      } else {
        $text = $this->GetGeschaeftsBriefText('ZahlungDiff','deutsch',$projekt);
        $betreff = $this->GetGeschaeftsBriefBetreff('ZahlungDiff','deutsch',$projekt);
      }

      $text= str_replace('{REST}',$rest,$text);
      $betreff = str_replace('{FIRMA}',$this->Firmendaten("name"),$betreff);
      $text = str_replace('{FIRMA}',$this->Firmendaten("name"),$text);

      if($internetnummer>0)
        $text= str_replace('{AUFTRAG}',$internetnummer,$text);
      else
        $text= str_replace('{AUFTRAG}',$belegnr,$text);

      $text= str_replace('{GESAMT}',$this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$auftragid' LIMIT 1"),$text);
      $gesamtsummecheck = $rest;
      $text= str_replace('{DATUM}',$this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM auftrag WHERE id='$auftragid' LIMIT 1"),$text);

    } else 
    {
      //TODO nette mail wenn kunde keine vorkasse macht, warum er das nicht macht etc.
      $text = $this->GetGeschaeftsBriefText('ZahlungOK','deutsch',$projekt);
      $betreff = $this->GetGeschaeftsBriefBetreff('ZahlungOK','deutsch',$projekt);

      $gesamtsumme = $this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$auftragid' LIMIT 1");

      $text= str_replace('{AUFTRAG}',$belegnr,$text);
      $text= str_replace('{GESAMT}',$gesamtsumme,$text);
      $gesamtsummecheck = $gesamtsumme;
      $text= str_replace('{DATUM}',$this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM kontoauszuege_zahlungseingang WHERE objekt='auftrag' AND parameter='$auftragid' LIMIT 1"),$text);
    }

    $zahlungsmailauftrag = $this->app->DB->Select("SELECT zahlungsmail FROM auftrag WHERE id='$auftragid' LIMIT 1");

    //$tage = ceil((mktime($zahlungsmailauftrag) - time())/60/60/24);
    $tage = $this->app->DB->Select("SELECT DATEDIFF(NOW(),'$zahlungsmailauftrag')");

    //echo "Tage $tage $to_name mail $zahlungsmail datum $zahlungsmailauftrag<br>";

    if($to!="" && $to_name!="" && $zahlungsmail=="1" && ($tage > 7 || $zahlungsmailauftrag=="0000-00-00" || $force==1))
    {
      $zahlungsmailcounter++;
      $this->app->DB->Update("UPDATE auftrag SET zahlungsmail=NOW(),zahlungsmailcounter='$zahlungsmailcounter' WHERE id='$auftragid' LIMIT 1");
      if($gesamtsummecheck>1)
      {
        if($zahlungsmailcounter<2)
          $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$to,$to_name,$betreff,$text,"",$projekt);
        // automatisch Reservierungen entfernen
        else {
          if($tage > 14) {
            $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),
                $this->GetFirmaMail(),"Buchhaltung","Systemmeldung: Offenen Auftrag $belegnr kl&auml;ren oder stornieren",$text,"",$projekt);
          }
        }
      }
      else { 
        $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$this->GetFirmaMail(),"Buchhaltung","Systemmeldung: Bitte Skonto geben",$text,"",$projekt);
      }
    }

  }


  function Rechnungsmail($id)
  {
    // $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='Versand' AND sprache='deutsch' LIMIT 1");
    // $betreff = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='Versand' AND sprache='deutsch' LIMIT 1");
    $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
    $to = $this->app->DB->Select("SELECT email FROM rechnung WHERE id='$id'  LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM rechnung WHERE id='$id' LIMIT 1");
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");

    $text = $this->GetGeschaeftsBriefText('Rechnung','deutsch',$projekt);
    $betreff = $this->GetGeschaeftsBriefBetreff('Rechnung','deutsch',$projekt);


    //   $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='Rechnung' AND sprache='deutsch' LIMIT 1");
    //    $betreff = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='Rechnung' AND sprache='deutsch' LIMIT 1");
    $text = str_replace('{NAME}',$to_name,$text);
    $text = str_replace('{BELEGNR}',$belegnr,$text);

    $betreff = str_replace('{NAME}',$to_name,$betreff);
    $betreff = str_replace('{BELEGNR}',$belegnr,$betreff);



    if($to!="" && $to_name!="")
    {
      $Brief = new RechnungPDF($this->app,$projekt);
      $Brief->GetRechnung($id);
      $tmpfile = $Brief->displayTMP();

      //$this->DokumentSendShow(TAB1,"rechnung",$rechnung,$adresse);
      // temp datei wieder loeschen

      $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$to,$to_name,$betreff,$text,array($tmpfile),$projekt);
      $this->RechnungProtokoll($id,"Rechnung versendet");

      unlink($tmpfile);

      // als versendet markieren
      $this->app->DB->Update("UPDATE rechnung SET status='versendet', versendet='1',schreibschutz='1' WHERE id='$id' LIMIT 1");
    }
  }

  function DokumentAbschicken()
  {
    $id = $this->app->Secure->GetGET("id");
    $frame = $this->app->Secure->GetGET("frame");

    $typ = $this->app->Secure->GetGET("module");

    if($frame=="")
    {
      $this->app->BuildNavigation=false;
      $this->app->Tpl->Set(TABTEXT,"Abschicken");

      $status = $this->app->DB->Select("SELECT status FROM $typ WHERE id='$id' LIMIT 1");
      $adresse = $this->app->DB->Select("SELECT adresse FROM $typ WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM $typ WHERE id='$id' LIMIT 1");

      if($projekt=="" || $projekt==0)
        $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");

      if($status !='angelegt')
      {
        //$this->app->Tpl->Set(TAB1,"<div class=\"warning\">Die Rechnung ist freigegeben und wurde noch nicht versendet!</div>");  
        $this->DokumentMask(TAB1,$typ,$id,$adresse,$projekt);
        //$this->RechnungProtokoll($id,"Rechnung per Mail versendet");
      } else
      {
        switch($typ)
        {
          case "rechnung": $this->app->Tpl->Set(TAB1,"<div class=\"error\">Die Rechnung wurde noch nicht freigegeben!</div>"); break;
          case "angebot": $this->app->Tpl->Set(TAB1,"<div class=\"error\">Das Angebot wurde noch nicht freigegeben!</div>"); break;
          case "auftrag": $this->app->Tpl->Set(TAB1,"<div class=\"error\">Der Auftrag wurde noch nicht freigegeben!</div>"); break;
          case "lieferschein": $this->app->Tpl->Set(TAB1,"<div class=\"error\">Der Lieferschein wurde noch nicht freigegeben!</div>"); break;
          case "bestellung": $this->app->Tpl->Set(TAB1,"<div class=\"error\">Die Bestellung wurde noch nicht freigegeben!</div>"); break;
          case "gutschrift": $this->app->Tpl->Set(TAB1,"<div class=\"error\">Die Gutschrift wurde noch nicht freigegeben!</div>"); break;
          case "arbeitsnachweis": $this->app->Tpl->Set(TAB1,"<div class=\"error\">Der Arbeitsnachweis wurde noch nicht freigegeben!</div>"); break;
        }
      }

      $id = $this->app->Tpl->Set(ID,$id);

      $this->app->Tpl->Parse(PAGE,"emptytab.tpl");

    } else {
      echo "<iframe width=\"100%\" height=\"600\" src=\"index.php?module=$typ&action=abschicken&id=$id\" frameborder=\"0\"></iframe>";
      exit;
    }

  }


  function Versandmail($id)
  {
    //    $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='Versand' AND sprache='deutsch' LIMIT 1");
    //    $betreff = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE subjekt='Versand' AND sprache='deutsch' LIMIT 1");

    $adresse = $this->app->DB->Select("SELECT adresse FROM versand WHERE id='$id' LIMIT 1");
    $lieferscheinid = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
    $auftrag = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
    $auftragbelegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $auftraginternet = $this->app->DB->Select("SELECT internet FROM auftrag WHERE id='$auftrag' LIMIT 1");
    $ihrebestellnummer = $this->app->DB->Select("SELECT ihrebestellnummer FROM auftrag WHERE id='$auftrag' LIMIT 1");

    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
    $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    $text = $this->GetGeschaeftsBriefText('Versand','deutsch',$projekt);
    $betreff = $this->GetGeschaeftsBriefBetreff('Versand','deutsch',$projekt);


    // wenn Ansprechpartner 
    $to_lieferschein_name = $this->app->DB->Select("SELECT ansprechpartner FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
    $to_lieferschein_email = $this->app->DB->Select("SELECT email FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");

    if($to_lieferschein_email!=""){
      $to = $to_lieferschein_email;

      if($to_lieferschein_name!="")
        $to_name = $to_lieferschein_name;
    }

    $trackingsperre = $this->app->DB->Select("SELECT trackingsperre FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");


    $tracking = $this->app->DB->Select("SELECT tracking FROM versand WHERE id='$id' LIMIT 1");
    $keinetrackingmail = $this->app->DB->Select("SELECT keinetrackingmail FROM versand WHERE id='$id' LIMIT 1");
    $versandunternehmen = $this->app->DB->Select("SELECT versandunternehmen FROM versand WHERE id='$id' LIMIT 1");

    $text = str_replace('{BELEGNR}',$auftragbelegnr,$text);
    $betreff = str_replace('{BELEGNR}',$auftragbelegnr,$betreff);

    $text = str_replace('{INTERNET}',$auftraginternet,$text);
    $betreff = str_replace('{INTERNET}',$auftraginternet,$betreff);

    $text = str_replace('{IHREBESTELLNUMMER}',$ihrebestellnummer,$text);
    $betreff = str_replace('{IHREBESTELLNUMMER}',$ihrebestellnummer,$betreff);



    if($versandunternehmen=="dhl" || $versandunternehmen=="dhlpremium" || $versandunternehmen=="intraship")
    {
      $text = str_replace('{VERSAND}','DHL Versand: '.$tracking.' (http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc='.$tracking.')',$text);
      $notsend = 0;
    }
    else if ($versandunternehmen=="dpd")
    {
          $text = str_replace('{VERSAND}','DPD Versand: '.$tracking.' (https://tracking.dpd.de/parcelstatus/?locale=de_DE&query='.$tracking.')',$text);
            $notsend = 0;
    }       
    else if ($versandunternehmen=="rma")
    {
            $notsend = 1;
    }
    else if($versandunternehmen=="selbstabholer")
    {
            $notsend = 0;
            // selbstabholer

            $text = $this->GetGeschaeftsBriefText('Selbstabholer','deutsch',$projekt);
            $betreff = $this->GetGeschaeftsBriefBetreff('Selbstabholer','deutsch',$projekt);

            //$text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE subjekt='Selbstabholer' AND sprache='deutsch' LIMIT 1");
            $text = str_replace('{VERSAND}','',$text);

            // nur wenn option gesetzt ist
            $selbstabholermail = $this->app->DB->Select("SELECT selbstabholermail FROM projekt WHERE id='$projekt' LIMIT 1");
            if($selbstabholermail!="1") $notsend = 1;
     } else {
              // bei allen anderen lieferarten keine mail
              $notsend = 1;
     }

     if($this->Projektdaten($projekt,"automailversandbestaetigung")!="1")
        $notsend = 1;

          $text = str_replace('{NAME}',$to_name,$text);

          if($to!="" && $to_name!="" && $trackingsperre!=1 && $notsend==0 && $keinetrackingmail!=1)
          {
            $this->MailSend($this->GetFirmaMail(),$this->GetFirmaName(),$to,$to_name,$betreff,$text,"",$projekt);
          }
  }

  function Projektdaten($projekt,$feld)
  {
    return $this->app->DB->Select("SELECT $feld FROM projekt WHERE id='$projekt' LIMIT 1");
  }

  function DumpVar($variable)
  {
    ob_start();
    var_dump($variable);
    $result = ob_get_clean();
    file_put_contents($this->GetTMP()."/log", "$result\r\n", FILE_APPEND | LOCK_EX);
  }

  function VarAsString($variable)
  {
    ob_start();
    var_dump($variable);
    $result = ob_get_clean();
    return $result;
  }
  
  function CreatePath($path) {
    if (file_exists($path))
    {
      return true;
    }
    $nextDirectoryPath = substr($path, 0, strrpos($path, '/', -2) + 1 );
 
    if($this->CreatePath($nextDirectoryPath) && is_writable($nextDirectoryPath))
    {
      return mkdir($path);
    }
    return false;
  }



  function Protokoll($meldung="",$dump="",$module="",$action="",$functionname="")
  {
    if($module=="") $module = $this->app->Secure->GetGET("module");
    if($action=="") $action = $this->app->Secure->GetGET("action");
    if($id=="") $id = $this->app->Secure->GetGET("id");
    if($functionname=="")
    {
      if (strnatcmp(phpversion(),'5.0.0') >= 0)
      {
        $backtrace = debug_backtrace();
        $functionname = $backtrace[1]['function'];
				if($functionname!="Run")
        	$argumente = base64_encode(print_r($backtrace[1]['args'],true));
      }
    }

    $this->app->DB->Insert("INSERT INTO protokoll (id,module,action,meldung,dump,datum,bearbeiter,funktionsname,parameter,argumente) 
        VALUES ('','$module','$action','$meldung','$dump',NOW(),'".$this->app->User->GetName()."','$functionname','$id','$argumente')");

    return $this->app->DB->GetInsertID();
  }

  function LogFile($meldung,$dump="",$module="",$action="",$functionname="")
  {
    if($module=="") $module = $this->app->Secure->GetGET("module");
    if($action=="") $action = $this->app->Secure->GetGET("action");

    if($functionname=="")
    {
      if (strnatcmp(phpversion(),'5.0.0') >= 0)
      {
        $backtrace = debug_backtrace();
        $functionname = $backtrace[1]['function'];
      }
    }


    $this->app->DB->Insert("INSERT INTO logfile (id,module,action,meldung,dump,datum,bearbeiter,funktionsname) 
        VALUES ('','$module','$action','$meldung','$dump',NOW(),'".$this->app->User->GetName()."','$functionname')");
    return $this->app->DB->GetInsertID();
  }


  function KundeUpdate($adresse,$typ,$name,$abteilung,$unterabteilung,$ansprechpartner,$adresszusatz,$strasse,$land,$plz,$ort,$email,$telefon,$telefax,$ustid,$partner,$projekt)
  {
    //echo "Upate";
    $fields = array('typ','name','abteilung','unterabteilung','ansprechpartner','adresszusatz','strasse','land','plz',
        'ort','email','telefon','telefax','ustid','partner','projekt');

    foreach($fields as $key)
    {
      $check = $this->app->DB->Select("SELECT $key FROM adresse WHERE id='$adresse' LIMI 1");
      if($check!=${$key})
      {
        //                              echo "UPDATE adresse SET $key='".${$key}."' WHERE id='$adresse' LIMIT 1";
        $this->app->DB->Update("UPDATE adresse SET $key='".${$key}."' WHERE id='$adresse' LIMIT 1");
        // Protokoll    

        //echo "UPDATE adresse SET logfile=CONCAT(logfile,'Update Feld $key: $check (alt) ".${$key}." (neu)') WHERE id='$adresse' LIMIT 1";
        $logfile = $this->app->DB->Select("SELECT `logfile` FROM adresse WHERE id='$adresse' LIMIT 1");
        $this->app->DB->Update("UPDATE adresse SET `logfile`='".$logfile." Update Feld $key alt:$check neu:".${$key}.";' WHERE id='$adresse' LIMIT 1");
      }

    }
    return $adresse;
  }

  function KundeAnlegen($typ,$name,$abteilung,$unterabteilung,$ansprechpartner,$adresszusatz,$strasse,$land,$plz,$ort,$email,$telefon,$telefax,$ustid,$partner,$projekt)
  {
    $this->app->DB->Insert("INSERT INTO adresse (id,typ,name,abteilung,unterabteilung,ansprechpartner,adresszusatz,strasse,land,plz,ort,email,telefon,telefax,ustid,partner,projekt,firma)
        VALUES('','$typ','$name','$abteilung','$unterabteilung','$ansprechpartner','$adresszusatz','$strasse','$land','$plz','$ort','$email','$telefon','$telefax','$ustid','$partner','$projekt','".$this->app->User->GetFirma()."')");
    $adresse = $this->app->DB->GetInsertID();


    //adresse Kundennummer verpassen
    $this->KundennummerVergeben($adresse);

    $this->AddRolleZuAdresse($adresse, "Kunde", "von", "Projekt", $projekt);
    return $adresse;
  }

  function Steuerbefreit($land,$ustid)
  {
    if($land=="DE")
      return false;

    foreach($this->GetUSTEU() as $euland)
    { 
      if($land==$euland && $ustid!="")
        return true;
      else if ($land==$euland && $ustid=="")
        return false;
    }

    // alle anderen laender sind export!
    return true;
  }


  function ImportAuftrag($adresse,$warenkorb,$projekt,$shop="")
  {
    //abweichende lieferadresse gibt es diese schon? wenn nicht zusätzlich in DB anlegen
    //$warenkorb[abweichendelieferadresse];

    // geldformat sollte sein 1000000.99

    //CreateAuftrag
    $auftrag = $this->CreateAuftrag();
    $this->AuftragProtokoll($auftrag,"Auftrag importiert vom Shop");


    $this->LoadAuftragStandardwerte($auftrag,$adresse);
    /*
    //wenn komma und punkt vorhanden
    if(strpos($string,',')!==false && strpos($string,'.')!==false)
    {
    $warenkorb[gesamtsumme] = str_replace(".","",$warenkorb[gesamtsumme]);
    $warenkorb[gesamtsumme] = str_replace(",",".",$warenkorb[gesamtsumme]);
    } 

     */
    if(strpos($warenkorb[gesamtsumme],',')!==false)
    {
      $warenkorb[gesamtsumme] = str_replace(",",".",$warenkorb[gesamtsumme]);
    } 


    $vertrieb = "Online-Shop"; 
    /*
       foreach($warenkorb as $key=>$value)
       {
       if($key!="articlelist")
       $warenkorb[$key] = $this->ConvertForDB($warenkorb[$key]);
       }
     */
    if($warenkorb[lieferung]=="selbstabholer")
      $versand = "selbstabholer"; 
    else
      $versand = "versandunternehmen"; 

    if($warenkorb[lieferung]=="dhlpremium")
      $versand = "dhlpremium";

    if($warenkorb[lieferung]=="packstation")
      $versand = "packstation";

    if(strlen($warenkorb[lieferadresse_name])>3)
      $warenkorb[abweichendelieferadresse]="1";
    else
      $warenkorb[abweichendelieferadresse]="0";


    //belegnummer fuer auftrag erzeugen
    //    $belegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM auftrag WHERE firma='".$this->app->User->GetFirma()."'");
    //    if($belegnr <= 0) $belegnr = 200000; else $belegnr = $belegnr + 1;

    $belegnr = $this->GetNextNummer("auftrag",$projekt);

    if($warenkorb[bestellnummer]!="") $warenkorb[bestellnummer] = "Ihre Bestellnummer: ".$warenkorb[bestellnummer];

    if($this->Steuerbefreit($warenkorb[land],$warenkorb[ustid]))
      $ust_befreit=1;
    else
      $ust_befreit=0;

    if($this->Export($warenkorb[land]))
      $ust_befreit=2;


    // E-Mail Adresse auf aktuellesten Stand bringen
    if($warenkorb[email]!="")
      $this->app->DB->Update("UPDATE adresse SET email='".$warenkorb[email]."' WHERE id='".$adresse."' LIMIT 1");

    if($warenkorb[zahlungsweise]=="Amazoncba") $warenkorb[zahlungsweise]="amazon";


    $this->app->DB->Update("UPDATE auftrag SET
        belegnr='$belegnr',
        datum='{$warenkorb[bestelldatum]}',
        ustid='{$warenkorb[ustid]}',
        ust_befreit='{$ust_befreit}',
        internet='{$warenkorb[onlinebestellnummer]}',
				transaktionsnummer='{$warenkorb[transaktionsnummer]}',
        versandart='{$versand}',                               
        vertrieb='{$vertrieb}',
        zahlungsweise='{$warenkorb[zahlungsweise]}',
        freitext='{$warenkorb[bestellnummer]}',
        bank_inhaber='{$warenkorb[kontoinhaber]}',
        bank_institut='{$warenkorb[bank]}',
        bank_blz='{$warenkorb[blz]}',
        bank_konto='{$warenkorb[kontonummer]}',
        autoversand='1',
        abweichendelieferadresse='{$warenkorb[abweichendelieferadresse]}',
        ansprechpartner='{$warenkorb[ansprechpartner]}',
        liefername='{$warenkorb[lieferadresse_name]}',
        lieferland='{$warenkorb[lieferadresse_land]}',
        lieferstrasse='{$warenkorb[lieferadresse_strasse]}',
        lieferabteilung='{$warenkorb[lieferadresse_abteilung]}',
        lieferunterabteilung='{$warenkorb[lieferadresse_unterabteilung]}',
        lieferansprechpartner='{$warenkorb[lieferadresse_ansprechpartner]}',
        lieferort='{$warenkorb[lieferadresse_ort]}',
        lieferplz='{$warenkorb[lieferadresse_plz]}',
        lieferadresszusatz='{$warenkorb[lieferadresse_adresszusatz]}',
        packstation_inhaber='{$warenkorb[packstation_inhaber]}',
        packstation_station='{$warenkorb[packstation_nummer]}',
        packstation_ident='{$warenkorb[packstation_postidentnummer]}',
        packstation_plz='{$warenkorb[packstation_plz]}',
        packstation_ort='{$warenkorb[packstation_ort]}',
        partnerid='{$warenkorb[affiliate_ref]}',
        kennen='{$warenkorb[kennen]}',
        status='freigegeben',
        projekt='$projekt',
        shop='$shop',
        gesamtsumme='{$warenkorb[gesamtsumme]}' WHERE id='$auftrag'");

    if(is_numeric($shop)){
      $shoptyp = $this->app->DB->Select("SELECT typ FROM shopexport WHERE id='$shop' LIMIT 1");
      $artikelimport = $this->app->DB->Select("SELECT artikelimport FROM shopexport WHERE id='$shop' LIMIT 1");
      $artikelimporteinzeln = $this->app->DB->Select("SELECT artikelimporteinzeln FROM shopexport WHERE id='$shop' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id='$shop' LIMIT 1");
      $multiprojekt = $this->app->DB->Select("SELECT multiprojekt FROM shopexport WHERE id='$shop' LIMIT 1");
    }       

    //artikelpositionen buchen
    foreach($warenkorb[articlelist] as $key=>$value)
    {
      // wenn es das produkt in dem projekt gibt
      $artikelimporteinzelngesetzt = $this->app->DB->Select("SELECT autoabgleicherlaubt FROM artikel WHERE nummer='{$value[articleid]}' AND projekt='$projekt' LIMIT 1");

      // pruefe ob auftrag auf anderes projekt gestellt werden muss
      if($multiprojekt=="1")
      {
        $artikelprojekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE nummer='{$value[articleid]}' LIMIT 1");// AND //TODO BENE
        $projekt = $artikelprojekt;
      }
      else
        $artikelprojekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE nummer='{$value[articleid]}' AND projekt='$projekt' LIMIT 1");// AND //TODO BENE

      //(shop='$shop' OR shop2='$shop' OR shop3='$shop') LIMIT 1");

      $zwangsprojekt = $this->app->DB->Select("SELECT shopzwangsprojekt FROM projekt WHERE id='$artikelprojekt' LIMIT 1");

      if($zwangsprojekt==1)
      {
        $this->app->DB->Update("UPDATE auftrag SET projekt='$artikelprojekt' WHERE id='$auftrag'");
      }

      //$j_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='{$value[articleid]}' AND (shop='$shop' OR shop2='$shop' OR shop3='$shop') LIMIT 1"); 
      if($multiprojekt=="1")
        $j_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='{$value[articleid]}' LIMIT 1");  //TODO BENE
      else
        $j_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='{$value[articleid]}' AND projekt='$projekt' LIMIT 1");  //TODO BENE

      $check = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE artikel='$j_id' 
          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW()) AND ab_menge=1 
          AND ((objekt='Standard' AND adresse=0) OR (objekt='' AND adresse=0)) AND geloescht=0 LIMIT 1");
      if($check > 0)
      {
        //$this->AddAuftragPositionNummer($auftrag,$value[articleid],$value[quantity],$projekt,"",$value[price]);
        //netto preis errechnen
        //if($value[tax]=="0" || $value[tax]=="") $preis = $value[price];
        //  else $preis = ($value[price]/(100+$value[taxtype]))*100;

        //$this->DumpVar("Dump: Projekt $projekt Shop $shop autoabgleicherlaubt $artikelimporteinzelngesetzt artikelimporteinzeln $artikelimporteinzeln Artikel {$value[articleid]}");

        if($this->Steuerbefreit($warenkorb['land'],$warenkorb['ustid']))
        {
          if($artikelimport || ($artikelimporteinzeln && $artikelimporteinzelngesetzt))
          {
            //$this->DumpVar("FallAB");
            //TODO normal oder ermaessigt
            $this->AddPositionManuellPreisNummer("auftrag",$auftrag, $projekt,$value[articleid],$value[quantity],$value[name],
                $value[price],"normal",0,$shop);
          }
          else {
            //$this->DumpVar("FallCD");
            //pruefen ob es versandkosten sind $warenkorb[land] // $preis
            $this->AddAuftragPositionNummer($auftrag,$value[articleid],$value[quantity],$projekt,"",true);
          }
        }
        else
        {
          //pruefen ob es versandkosten sind $warenkorb[land] // $preis
          if($artikelimport || ($artikelimporteinzeln && $artikelimporteinzelngesetzt))
          {
            //TODO normal oder ermaessigt
            //$this->DumpVar("FallEF");
            $this->AddPositionManuellPreisNummer("auftrag",$auftrag, $projekt, $value[articleid],$value[quantity],$value[name],
                $value[price]/$this->GetSteuersatzNormal(true,$auftrag,"auftrag"),"normal",0,$shop);
          } else {
            //pruefen ob es versandkosten sind $warenkorb[land] // $preis
            //$this->DumpVar("FallGH");
            $this->AddAuftragPositionNummer($auftrag,$value[articleid],$value[quantity],$projekt,"");
          }
        }
        $this->AddAuftragPositionNummerPartnerprogramm($auftrag,$value[articleid],$value[quantity],$projekt,$warenkorb[affiliate_ref]);
      }
      else
      {
        $j_nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer='{$value[articleid]}' LIMIT 1");
        $j_name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE nummer='{$value[articleid]}' LIMIT 1");
        $j_projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='{$projekt}' LIMIT 1");
        $j_menge = $value[quantity];
        $j_preis = $value[price];
        if($this->Steuerbefreit($warenkorb['land'],$warenkorb['ustid']))
        {
          //pruefen ob es versandkosten sind $warenkorb[land] // $preis
          if($artikelimport || ($artikelimporteinzeln && $artikelimporteinzelngesetzt))
          {
            //$this->DumpVar("Fall5");
            //TODO normal oder ermaessigt
            $this->AddPositionManuellPreisNummer("auftrag",$auftrag, $projekt,$value[articleid],$value[quantity],$value[name],
                $value[price],"normal",0,$shop);
          }
        }
        else
        {
          //pruefen ob es versandkosten sind $warenkorb[land] // $preis
          if($artikelimport || ($artikelimporteinzeln && $artikelimporteinzelngesetzt))
          {
            //$this->DumpVar("Fall6");
            //TODO normal oder ermaessigt
            $this->AddPositionManuellPreisNummer("auftrag",$auftrag, $projekt, $value[articleid],$value[quantity],$value[name],
                $value[price]/$this->GetSteuersatzNormal(true,$auftrag,"auftrag"),"normal",0,$shop);
          }
        }
        $this->AddAuftragPositionNummerPartnerprogramm($auftrag,$value[articleid],$value[quantity],$projekt,$warenkorb[affiliate_ref]);

        $this->AuftragProtokoll($auftrag,"Artikel nach Import nicht in interner Datenbank gefunden (Artikel: $j_nummer $j_name  Menge: $j_menge Preis: $j_preis) bzw. kein Verkaufspreis vorhanden.");
      }

    }

    if($shoptyp=="shopware4")
    {
      if($warenkorb[zahlungsweise]=="nachnahme")
      {
        $artikelnachnahme = $this->app->DB->Select("SELECT artikelnachnahme FROM shopexport WHERE id='$shop' LIMIT 1");
        $nachnahme = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikelnachnahme' LIMIT 1");
        $nachnahmepreis = $this->GetVerkaufspreis($artikelnachnahme,1); 
        $tmpposid = $this->AddPositionManuellPreis("auftrag",$auftrag, $artikelnachnahme,1,$nachnahme,$nachnahmepreis,"normal");
        if($tmpposid>0)
          $this->app->DB->Update("UPDATE auftrag_position SET keinrabatterlaubt=1 WHERE id='$tmpposid' LIMIT 1");

      } else {
        $nachnahmepreis=0;
      }

      //porto und nachnahme
      $artikelporto = $this->app->DB->Select("SELECT artikelporto FROM shopexport WHERE id='$shop' LIMIT 1");
      $versandname = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikelporto' LIMIT 1");
      //$this->DumpVar("Fall Porto Preis ".$warenkorb[versandkostennetto]);
      $tmpposid = $this->AddPositionManuellPreis("auftrag",$auftrag, $artikelporto,1,$versandname,$warenkorb[versandkostennetto]-$nachnahmepreis,"normal");
      if($tmpposid>0)
        $this->app->DB->Update("UPDATE auftrag_position SET keinrabatterlaubt=1 WHERE id='$tmpposid' LIMIT 1");
    }
    $shoptyp="";



    // wenn reservierung aktiviert ist
    $reservierung = $this->app->DB->Select("SELECT reservierung FROM projekt WHERE id='$projekt' LIMIT 1");
    if($reservierung>=1)
      $this->AuftragReservieren($auftrag);

    //    $this->AuftragNeuberechnen($auftrag);
    $this->AuftragEinzelnBerechnen($auftrag);

    return $auftrag;
  }


  function KundennummerVergeben($adresse,$projekt="")
  {
    $id = $adresse;
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    if($projekt=="")
      $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");

    if($kundennummer==0 || $kundennummer==""){
      // pruefe ob rolle kunden vorhanden
      $check = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE adresse='$id' AND subjekt='Kunde' LIMIT 1");
      if($check!="")
      {
        $kundennummer = $this->GetNextKundennummer($projekt);
        $this->app->DB->Update("UPDATE adresse SET kundennummer='$kundennummer' WHERE id='$id' AND (kundennummer='0' OR kundennummer='') LIMIT 1");
        return $kundennummer;
      } 
    }
  }


  function AdresseUSTCheck($adresse)
  {
    //wenn land DE

    $land = $this->app->DB->Select("SELECT land FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    if($land =="DE" || $land=="")
      return 0;

    foreach($this->GetUSTEU() as $euland)
    { 
      if($land==$euland)
        return 1;
    }

    // alle anderen laender sind export!
    return 2;


    //wenn land EU
    /*

       $ustid = $this->app->DB->Select("SELECT ustid FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
       if($ustid!="" && $land!="DE")
       return $this->AutoUSTPruefung($adresse);
     */

    return 1;
    // 0 wenn keine erfolgreiche pruefung heute da ist
  }


  function AutoUSTPruefung($adresse)
  {

    // schaue obs heute bereits eine pruefung gegeben hat die erfolgreich war
    $ustcheck = $this->app->DB->Select("SELECT id FROM ustprf WHERE DATE_FORMAT(datum_online,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') AND status='erfolgreich' AND adresse='$adresse' LIMIT 1");
    if($ustcheck>0 && is_numeric($ustcheck))
      return 1;


    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1"); 
    $ustid = $this->app->DB->Select("SELECT ustid FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1"); 
    $land = $this->app->DB->Select("SELECT land FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1"); 
    $ort = $this->app->DB->Select("SELECT ort FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1"); 
    $plz = $this->app->DB->Select("SELECT plz FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1"); 
    $strasse  = $this->app->DB->Select("SELECT strasse FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1"); 

    if($land=="DE" || $ustid=="") return 0;


    $ustcheck = $this->app->DB->Select("SELECT id FROM ustprf WHERE status='' AND adresse='$adresse' LIMIT 1");
    if(!($ustcheck>0 && is_numeric($ustcheck))) 
    {
      $this->app->DB->Insert("INSERT INTO ustprf (id,adresse,name,ustid,land,ort,plz,rechtsform,strasse,datum_online,bearbeiter)
          VALUES('','$adresse','$name','$ustid','$land','$ort','$plz','$rechtsform','$strasse',NOW(),'".$this->app->User->GetName()."')");
      $ustprf_id = $this->app->DB->GetInsertID();
    }
    else
      $ustprf_id = $ustcheck;


    //$this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$ustprf_id.',"'.date("Y-m-d H:i:s").'","AUTO Pr&uuml;fung gestartet", "'.$this->app->User->GetName().'")');

    $ustid = str_replace(" ","",$ustid);
    $ust = $ustid;
    $result = 0;

    if(!$this->CheckUSTFormat($ust)){
      //$this->app->Tpl->Set(MESSAGE,"<div class=\"error\">UST-Nr. bzw. Format fuer Land ist nicht korrekt</div>");
      $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$ustprf_id.',"'.date("Y-m-d H:i:s").'","UST-Nr. bzw. Format fuer Land ist nicht korrekt", "'.$this->app->User->GetName().'")');
    }else{
      //$UstStatus = $this->CheckUst("DE263136143","SE556459933901","Wind River AB","Kista","Finlandsgatan 52","16493","nein");        

      $UstStatus = $this->CheckUst("DE263136143", $ust, $name, $ort, $strasse, $plz, $druck="nein");
      if(is_array($UstStatus))
      {
        $tmp = new USTID();
        $msg = $tmp->errormessages($UstStatus['ERROR_CODE']);

        if($UstStatus['ERROR_CODE']==200)
        {
          $this->app->DB->Delete("DELETE FROM ustprf_protokoll WHERE ustprf_id='$ustprf_id' AND bemerkung='UST g&uuml;ltig aber Name, Ort oder PLZ wird anders geschrieben'");
          $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$ustprf_id.',"'.date("Y-m-d H:i:s").'","UST g&uuml;ltig aber Name, Ort oder PLZ wird anders geschrieben", "'.$this->app->User->GetName().'")');
        }
        else
          $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$ustprf_id.',"'.date("Y-m-d H:i:s").'","'.$UstStatus['ERROR_CODE'].'('.$msg.')", "'.$this->app->User->GetName().'")');

      } else if($UstStatus==1){

        //$this->app->Tpl->Set(STATUS,"<div style=\"background-color: green;\">Vollst&auml;ndig</div>");
        $result = 1;

        // jetzt brief bestellen! 
        $UstStatus = $this->CheckUst("DE263136143", $ust, $firmenname, $ort, $strasse, $plz, $druck="ja");
        $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$ustprf_id.',"'.date("Y-m-d H:i:s").'","Online-Abfrage OK + Brief bestellt", "'.$this->app->User->GetName().'")');
        $this->app->DB->Update('UPDATE ustprf SET datum_online=NOW(), status="erfolgreich" WHERE id='.$ustprf_id.'');
      } else {
        $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$ustprf_id.',"'.date("Y-m-d H:i:s").'","'.$UstStatus.'", "'.$this->app->User->GetName().'")');
        $this->app->DB->Update('UPDATE ustprf SET datum_online=NOW(), status="allgemeiner fehler" WHERE id='.$ustprf_id.'');
      }
    }


    return $result;
  }

  function ArtikelMindestlager($artikel)
  {
    // Fall ein 100R in vielen Listen dann muss man alle listen mit mindestmengen nehmen
    // Fall das ist eine 
    $mindestlager =  $this->app->DB->Select("SELECT mindestlager FROM artikel WHERE id='$artikel' LIMIT 1");
    if($mindestlager > 0)
    {
      return $mindestlager;
    } else {
      return 0;
    }
  }

  function AddChargeLagerOhneBewegung($artikel,$menge,$lagerplatz,$datum,$charge="",$internebemerkung="",$zid="")
  {
    for($i=0;$i<$menge;$i++)
    {
      $this->app->DB->Insert("INSERT INTO lager_charge (id,artikel,menge,lager_platz,datum,internebemerkung,charge,zwischenlagerid) VALUES ('','$artikel','1','$lagerplatz','$datum','$internebemerkung','$charge','$zid')");
    }

    //return $this->app->DB->GetInsertID();
  }


  function AddMindesthaltbarkeitsdatumLagerOhneBewegung($artikel,$menge,$lagerplatz,$mhd,$charge="",$zid="")
  {
    if ($mhd == '' || $mhd==0 || $mhd=='0000-00-00') {
      return false;
    }
    for($i=0;$i<$menge;$i++)
    {
      $this->app->DB->Insert("INSERT INTO lager_mindesthaltbarkeitsdatum (id,artikel,menge,lager_platz,datum,internebemerkung,charge,zwischenlagerid,mhddatum) VALUES ('','$artikel','1','$lagerplatz',NOW(),'$internebemerkung','$charge','$zid','$mhd')");
    }

    //return $this->app->DB->GetInsertID();
  }


  function AddChargeLager($artikel,$menge,$lagerplatz,$datum,$charge="",$internebemerkung="",$zid="")
  {
    for($i=0;$i<$menge;$i++)
    {
      $this->app->DB->Insert("INSERT INTO lager_charge (id,artikel,menge,lager_platz,datum,internebemerkung,charge,zwischenlagerid) VALUES ('','$artikel','1','$lagerplatz','$datum','$internebemerkung','$charge','$zid')");
    }
    $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,artikel,menge,lager_platz) VALUES ('','$artikel','$menge','$lagerplatz')");

    $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz, artikel, menge,vpe, eingang,zeit,referenz, bearbeiter,projekt,firma,logdatei)
        VALUES('','$lagerplatz','$artikel','$menge','$vpe','1',NOW(),'Charge $charge eingelagert','".$this->app->User->GetName()."','$projekt','".$this->app->User->GetFirma()."',NOW())");

    //return $this->app->DB->GetInsertID();
  }


  function AddMindesthaltbarkeitsdatumLager($artikel,$menge,$lagerplatz,$mhd,$charge="",$zid="")
  {
    if ($mhd == '' || $mhd==0 || $mhd=='0000-00-00') {
      return false;
    }
    for($i=0;$i<$menge;$i++)
    {
      $this->app->DB->Insert("INSERT INTO lager_mindesthaltbarkeitsdatum (id,artikel,menge,lager_platz,datum,internebemerkung,charge,zwischenlagerid,mhddatum) VALUES ('','$artikel','1','$lagerplatz',NOW(),'$internebemerkung','$charge','$zid','$mhd')");
    }
    $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,artikel,menge,lager_platz) VALUES ('','$artikel','$menge','$lagerplatz')");

    if($charge!="") $charge = " Charge: $charge";

    $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz, artikel, menge,vpe, eingang,zeit,referenz, bearbeiter,projekt,firma,logdatei)
        VALUES('','$lagerplatz','$artikel','$menge','$vpe','1',NOW(),'MHD eingelagert $charge','".$this->app->User->GetName()."','$projekt','".$this->app->User->GetFirma()."',NOW())");


    //return $this->app->DB->GetInsertID();
  }


  function AddSeriennummerLager($artikel,$lagerplatz,$seriennummer,$internebemerkung="",$zid="",$mhd="",$charge="")
  {
    $this->app->DB->Insert("INSERT INTO lager_seriennummern (id,artikel,lager_platz,seriennummer,internebemerkung,zwischenlagerid,mhddatum,charge) VALUES ('','$artikel','$lagerplatz','$seriennummer','$internebemerkung','$zid','$mhd','$charge')");

    return $this->app->DB->GetInsertID();
  }

  function LagerEinlagerVomZwischenlager($zwischenlagerid,$menge,$regal,$projekt,$grund="")
  {
    $artikel = $this->app->DB->Select("SELECT artikel FROM zwischenlager WHERE id='$zwischenlagerid' LIMIT 1");
    $referenz  = $this->app->DB->Select("SELECT grund FROM zwischenlager WHERE id='$zwischenlagerid' LIMIT 1");
    $vpe = $this->app->DB->Select("SELECT vpe FROM zwischenlager WHERE id='$zwischenlagerid' LIMIT 1");
    $bestellung = $this->app->DB->Select("SELECT parameter FROM zwischenlager WHERE id='$zwischenlagerid' LIMIT 1");

    //if($zwischenlager=="" || $zwischenlagerid==0)
    //  return;

    // Bewegung
    $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz, artikel, menge,vpe, eingang,zeit,referenz, bearbeiter,projekt,firma,logdatei)
        VALUES('','$regal','$artikel','$menge','$vpe','1',NOW(),'$referenz:$grund','".$this->app->User->GetName()."','$projekt','".$this->app->User->GetFirma()."',NOW())");

    // inhalt buchen
    $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,lager_platz,artikel,menge,vpe,bearbeiter,bestellung,projekt,firma,logdatei)
        VALUES ('','$regal','$artikel','$menge','$vpe','".$this->app->User->GetName()."','$bestellung','$projekt','".$this->app->User->GetFirma()."',NOW())");

    $this->app->DB->Update("UPDATE lager_seriennummern SET lager_platz='$regal',zwischenlagerid='0' WHERE zwischenlagerid='$zwischenlagerid'");
    $this->app->DB->Update("UPDATE lager_mindesthaltbarkeitsdatum SET lager_platz='$regal',zwischenlagerid='0' WHERE zwischenlagerid='$zwischenlagerid'");
    $this->app->DB->Update("UPDATE lager_charge SET lager_platz='$regal',zwischenlagerid='0' WHERE zwischenlagerid='$zwischenlagerid'");

    //zwischen lager entfernen
    // menge abziehen
    $menge_db = $this->app->DB->Select("SELECT menge FROM zwischenlager WHERE id='$zwischenlagerid' LIMIT 1");
    if($menge_db - $menge > 0)
    {
      $tmp = $menge_db - $menge;
      $this->app->DB->Update("UPDATE zwischenlager SET menge='$tmp' WHERE id='$zwischenlagerid' LIMIT 1");
    } else {
      $this->app->DB->Update("DELETE FROM zwischenlager WHERE id='$zwischenlagerid' LIMIT 1");
    } 

    /*
    // wenn standardlager leer vom dem artikel buchen!! 
    $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1"); 
    if($lagerplatz=="" || $lagerplatz==0) 
    $this->app->DB->Update("UPDATE artikel SET lager_platz='$regal' WHERE id='$artikel' LIMIT 1");
     */


    $this->LagerArtikelZusammenfassen($artikel);
  }

  function LagerAutoAuslagernArtikel($artikel,$menge,$grund)
  {
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='".$artikel."' LIMIT 1");     

    if($lager_platz > 0)
      $tmparr[] = $this->app->DB->SelectArr("SELECT lager_platz,menge FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$lager_platz' ORDER by menge DESC");          

    // alle anderen regale  
    if($lager_platz > 0)
      $tmparr[] = $this->app->DB->SelectArr("SELECT lager_platz,menge FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz!='$lager_platz' ORDER by menge DESC");         
    else
      $tmparr[] = $this->app->DB->SelectArr("SELECT lager_platz,menge FROM lager_platz_inhalt WHERE artikel='$artikel' ORDER by menge DESC");         

    // build common array
    for($i=0;$i<count($tmparr);$i++)
    {
      for($j=0;$j<count($tmparr[$i][$j]);$j++)
      {
        $lager_platz_id[] = array('lager_platz'=>$tmparr[$i][$j]['lager_platz'],'menge'=>$tmparr[$i][$j]['menge']);
      }       
    }       

    // auslagern solange notwendige
    for($i=0;count($lager_platz_id);$i++)
    {
      $regal = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id='".$lager_platz_id[$i]['id']."' LIMIT 1"); 
      if($lager_platz_id[$i]['menge']>=$menge)
      {
        // in dem regal ist genug restmenge nehmen
        $this->LagerAuslagernRegal($artikel,$lager_platz_id[$i]['lager_platz'],$menge,$projekt,$grund);
        $this->LagerAuslagernRegalMHDCHARGESRN($artikel,$lager_platz_id[$i]['lager_platz'],$menge,$projekt,$grund);
        break;
      } else {
        // komplettes regal und menge abziegen
        $this->LagerAuslagernRegal($artikel,$lager_platz_id[$i]['lager_platz'],$lager_platz_id[$i]['menge'],$projekt,$grund);
        $this->LagerAuslagernRegalMHDCHARGESRN($artikel,$lager_platz_id[$i]['lager_platz'],$lager_platz_id[$i]['menge'],$projekt,$grund);
        $menge = $menge - $lager_platz_id[$i]['menge'];
      }       
    }       

  }       

  function LagerAuslagernRegal($artikel,$regal,$menge,$projekt,$grund,$importer="")
  {
    if($importer==1)
      $username = "Import";
    else
      $username = $this->app->User->GetName();

    if($menge > 0)  
    {
      // Bewegung buchen
      $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,vpe,eingang,zeit,referenz,bearbeiter,projekt,firma) VALUES 
          ('','$regal','$artikel','$menge','','0',NOW(),'$grund','" . $username. "','$projekt','')");

      // lager in diesem Regal anpassen
      $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge=menge-$menge WHERE artikel='$artikel' AND lager_platz='$regal' LIMIT 1");
    }
    else
    {
      $this->Protokoll("Menge $menge fuer Artikel $artikel und lager_platz $regal konnte nicht entnommen werden");
    }

    $this->LagerArtikelZusammenfassen($artikel);
  }


  function LagerAuslagernRegalMHDCHARGESRN($artikel,$regal,$menge,$projekt,$grund,$importer="")
  {

    $mhd = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id='$artikel' LIMIT 1");
    $mhd_menge = $menge;

    if($mhd=="1")
    {
      $timeout=0;
      while($mhd_menge > 0)
      {
        $check = $this->app->DB->SelectArr("SELECT id,menge FROM lager_mindesthaltbarkeitsdatum WHERE artikel='$artikel' AND lager_platz='$regal' ORDER by datum LIMIT 1");
        if($check[0]['menge']<=$mhd_menge)
        {
          // kann komplett geloescht werden       
          $mhd_menge = $mhd_menge - $check[0]['menge'];
          $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id='".$check[0]['id']."' AND id > 0 LIMIT 1");
        } else {
          //sonst update mit menge
          $this->app->DB->Update("UPDATE lager_mindesthaltbarkeitsdatum SET menge='".($check[0]['menge'] - $mhd_menge)."' WHERE id='".$check[0]['id']."'");
        }
        $timeout++;
        if($timeout > $menge) break;
      }
    }

    $chargenverwaltung = $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$artikel' LIMIT 1");
    $charge_menge = $menge;
    if($chargenverwaltung=="2")
    {
      $timeout=0;
      while($charge_menge > 0)
      {
        $check = $this->app->DB->SelectArr("SELECT id,menge FROM lager_charge WHERE artikel='$artikel' AND lager_platz='$regal' ORDER by id LIMIT 1");
        if($check[0]['menge']<=$charge_menge)
        {
          // kann komplett geloescht werden       
          $charge_menge = $charge_menge - $check[0]['menge'];
          $this->app->DB->Delete("DELETE FROM lager_charge WHERE id='".$check[0]['id']."' AND id > 0 LIMIT 1");
        } else {
          //sonst update mit menge
          $this->app->DB->Update("UPDATE lager_charge SET menge='".($check[0]['menge'] - $mhd_menge)."' WHERE id='".$check[0]['id']."'");
        }
        $timeout++;
        if($timeout > $menge) break;
      }
    }       

    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$artikel' LIMIT 1");
    $srn_menge = $menge;
    if($seriennummern=="vomprodukteinlagern")
    {
      $timeout=0;
      while($srn_menge > 0)
      {
        $check = $this->app->DB->Select("SELECT id FROM lager_seriennummern WHERE artikel='$artikel' AND lager_platz='$regal' ORDER by id LIMIT 1");
        if($check > 0)
        {
          // kann komplett geloescht werden       
          $srn_menge--;
          $this->app->DB->Delete("DELETE FROM lager_seriennummern WHERE id='".$check."' AND id > 0 LIMIT 1");
        } 
        $timeout++;
        if($timeout > $menge) break;
      }
    }       


    $this->LagerArtikelZusammenfassen($artikel);
  }


  function CreateLagerplatz($lager,$lagerplatz_name,$firma="1")
  {
    $lagerplatz_name = trim($lagerplatz_name);
    // pruefe ob es Lagerplatz bereits gibt
    $check_id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lagerplatz_name' AND lager='$lager'");

    if($check_id <= 0)
    {
      $this->app->DB->Insert("INSERT INTO lager_platz (id,lager,kurzbezeichnung,firma) VALUES ('','$lager','$lagerplatz_name','$firma')");    
      $check_id = $this->app->DB->GetInsertID();
    }       
    return $check_id;
  }


  function LagerID($lager)
  {

    if(is_numeric($lager) && $lager > 0)
    {
      $lager = $lager +0;
      $lager = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$lager' LIMIT 1");
      if($lager > 0)
        return $lager;
    }

    if($lager !="")
    {
      $id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lager' LIMIT 1");
      return $id;
    } else return "";
  }

  function IsArtikelInZwischenlager($artikel)
  {
    $check = $this->app->DB->Select("SELECT id FROM zwischenlager WHERE richtung='Eingang' AND artikel='$artikel' LIMIT 1");
    if($check > 0)
      return true;
    else 
      return false; 
  }

  function ArtikelIDWennEAN($artikelnummer)
  {
    if($artikelnummer!="")
    {

      // artikelnummer hat hoechste Prio
      $nummer = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikelnummer' LIMIT 1");
      if($nummer > 0)
        return $nummer;

      // dann ean nummer
      $ean = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$artikelnummer' LIMIT 1");
      if($ean > 0)
        return $ean;

      // und zum Schluss Hersteller
      $herstellernummer = $this->app->DB->Select("SELECT id FROM artikel WHERE herstellernummer='$artikelnummer' LIMIT 1");
      if($herstellernummer > 0)
        return $herstellernummer;

      $id = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikelnummer' LIMIT 1");
      if($id > 0)
        return $id;
      else 
        return 0;
    }
    return 0;
  }


  function LagerEinlagern($artikel,$menge,$regal,$projekt,$grund="",$importer="")
  {
    $vpe = 'einzeln'; //TODO

    if($importer==1)
      $username = "Import";
    else
      $username = $this->app->User->GetName();

    if($menge > 0 && is_numeric($menge))
    {
      // Bewegung
      $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz, artikel, menge,vpe, eingang,zeit,referenz, bearbeiter,projekt,firma,logdatei)
          VALUES('','$regal','$artikel','$menge','$vpe','1',NOW(),'$grund','".$username."','$projekt','',NOW())");

      // inhalt buchen
      $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,lager_platz,artikel,menge,vpe,bearbeiter,bestellung,projekt,firma,logdatei)
          VALUES ('','$regal','$artikel','$menge','$vpe','".$username."','','$projekt','',NOW())");
    }
    /*
    // wenn standardlager leer vom dem artikel buchen!! 
    $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1"); 
    if($lagerplatz=="" || $lagerplatz==0) 
    $this->app->DB->Update("UPDATE artikel SET lager_platz='$regal' WHERE id='$artikel' LIMIT 1");
     */
    $this->LagerArtikelZusammenfassen($artikel);
  }

  function LagerEinlagernDifferenz($artikel,$menge,$regal,$projekt,$grund="",$importer="")
  {
    $grund = "Differenz: ".$grund;
    $this->LagerEinlagern($artikel,$menge,$regal,$projekt,$grund,$importer);
  }


  function LagerArtikelZusammenfassen($artikel)
  {
    //$this->LagerSync($artikel);
    // all einzeln buchungen in einem Baum zusammenfassen           

    if($artikel > 0) {
      $result = $this->app->DB->SelectArr("SELECT lager_platz,SUM(menge) as gesamt,projekt,firma FROM lager_platz_inhalt WHERE artikel='$artikel' GROUP by lager_platz");
      $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE artikel='".$artikel."'");
      //echo "DELETE FROM lager_platz_inhalt WHERE artikel='".$artikel."';";
      for($i=0;$i<count($result);$i++)
      {
        $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,lager_platz,artikel,menge,projekt,firma) VALUES ('','".$result[$i]['lager_platz']."','$artikel',
          '".$result[$i]['gesamt']."','".$result[$i]['projekt']."','".$result[$i]['firma']."');");
      }
    }

    $this->app->DB->Delete("DELETE lager_platz_inhalt FROM lager_platz_inhalt WHERE menge<='0'");

    // loesche verbrauchslager
    // aber ebenfalls chargen, seriennummern und mhd        
    $this->app->DB->Delete("DELETE lager_platz_inhalt FROM lager_platz_inhalt LEFT JOIN lager_platz ON lager_platz.id=lager_platz_inhalt.lager_platz 
        WHERE lager_platz.verbrauchslager='1'");
    //WHERE lager_platz_inhalt.artikel='".$artikel."' AND lager_platz.verbrauchslager='1'");

    $this->app->DB->Delete("DELETE lager_charge FROM lager_charge LEFT JOIN lager_platz ON lager_platz.id=lager_charge.lager_platz
        WHERE lager_platz.verbrauchslager='1'");

    $this->app->DB->Delete("DELETE lager_seriennummer FROM lager_seriennummer 
        LEFT JOIN lager_platz ON lager_platz.id=lager_seriennummer.lager_platz
        WHERE lager_platz.verbrauchslager='1'");

    $this->app->DB->Delete("DELETE lager_mindesthaltbarkeitsdatum FROM lager_mindesthaltbarkeitsdatum 
        LEFT JOIN lager_platz ON lager_platz.id=lager_mindesthaltbarkeitsdatum.lager_platz
        WHERE lager_platz.verbrauchslager='1'");
  }


  // pruefe ob es artikel noch im lager gibt bzw. ob es eine reservierung gibt
  function LagerFreieMenge($artikel)
  {
    $summe_im_lager = $this->app->DB->Select("SELECT SUM(li.menge) FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz WHERE li.artikel='$artikel'");
    //AND lp.autolagersperre!='1'");

    //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND datum>=NOW() AND objekt!='lieferschein'");
    $artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND (datum>=NOW() OR datum='0000-00-00')");

    $restmenge = $summe_im_lager - $artikel_reserviert;
    if($restmenge > 0)
      return $restmenge;
    else return 0;

  }

  function ArtikelAnzahlLagerStueckliste($id)
  {
    // gehe stueckliste durch und schaue ob es maximal artikel ist 
    $artikel = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE stuecklistevonartikel='$id'");
    $stueck = 0;
    $kleinste_max_menge = 0;
    for($i=0;$i<count($artikel);$i++)
    {
      $artikelid = $artikel[$i]['artikel'];
      $mengeimlage = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikelid'");
      $mengereserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$artikelid'");

      $mengefrei = $mengeimlage - $mengereserviert;

      $max_menge = floor($mengefrei/$artikel[$i]['menge']);
      $collect[] = $max_menge;

    } 
    if(is_array($collect))
    {
      sort($collect);
      $stueck = $collect[0];
    } else
      $stueck = 0;

    if($stueck <= 0) $stueck=0;

    return $stueck;
  }

  // alle frei verfuergbaren Artikel
  function ArtikelAnzahlVerfuegbar($artikelid)
  {
    $lagerartikel[$ij]['juststueckliste'] = $this->app->DB->Select("SELECT juststueckliste FROM artikel WHERE id='$artikelid' LIMIT 1");

  }


  function LagerSync($artikelid, $print_echo=false)
  {
    $ij=0;
    $lagerartikel[$ij]['id'] = $artikelid;

    $lagerartikel[$ij]['juststueckliste'] = $this->app->DB->Select("SELECT juststueckliste FROM artikel WHERE id='$artikelid' LIMIT 1");
    $lagerartikel[$ij]['name_de'] = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikelid' LIMIT 1");
    $lagerartikel[$ij]['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikelid' LIMIT 1");
    $lagerartikel[$ij]['cache_lagerplatzinhaltmenge'] = $this->app->DB->Select("SELECT cache_lagerplatzinhaltmenge FROM artikel WHERE id='$artikelid' LIMIT 1");

    if($lagerartikel[$ij]['juststueckliste'])
      $lagernd = $this->ArtikelAnzahlLagerStueckliste($lagerartikel[$ij]['id']);
    else
      $lagernd = $this->ArtikelAnzahlLager($lagerartikel[$ij]['id']);

    $reserviert = $this->ArtikelAnzahlReserviert($lagerartikel[$ij]['id']);

    $offen = $this->ArtikelAnzahlOffene($lagerartikel[$ij]['id']);

    if($offen > $reserviert) $reserviert = $offen;

    $alter_status = $lagerartikel[$ij]['cache_lagerplatzinhaltmenge'];

    if (($lagernd-$reserviert) > 0)
      $neuer_status = "gruen (Menge ".($lagernd-$reserviert).")";
    else $neuer_status = "red";

    //      echo $lagerartikel[$ij]['name_de']." Lagernd: ".($lagernd-$reserviert)." Cache: ".$lagerartikel[$ij]['cache_lagerplatzinhaltmenge']."\r\n";

    if($lagerartikel[$ij]['cache_lagerplatzinhaltmenge'] != ($lagernd-$reserviert))//$alter_status!=$neuer_status)
    {
      // Lagerbestand sollte auch online gestellt werden
      //$this->app->DB->Update("UPDATE artikel SET lieferzeit='$neuer_status',                                                                 cache_lagerplatzinhaltmenge='".($lagernd-$reserviert)."',lieferzeitmanuell='',
      $this->app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='".($lagernd-$reserviert)."'
          WHERE id='".$lagerartikel[$ij]['id']."' LIMIT 1");

      $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='".$lagerartikel[$ij]['id']."' LIMIT 1");

      if($shop > 0){
        $this->app->remote->RemoteSendArticleList($shop,array($lagerartikel[$ij]['id']));
      }

      $shop2 = $this->app->DB->Select("SELECT shop2 FROM artikel WHERE id='".$lagerartikel[$ij]['id']."' LIMIT 1");
      if($shop2 > 0)
        $this->app->remote->RemoteSendArticleList($shop2,array($lagerartikel[$ij]['id']));

      $shop3 = $this->app->DB->Select("SELECT shop3 FROM artikel WHERE id='".$lagerartikel[$ij]['id']."' LIMIT 1");
      if($shop3 > 0)
        $this->app->remote->RemoteSendArticleList($shop3,array($lagerartikel[$ij]['id']));

      $message .= "Artikel: ".$lagerartikel[$ij]['name_de']." (".$lagerartikel[$ij]['nummer'].") Neuer Status: ".$neuer_status."\r\n";
      if($print_echo)
        echo "*** UPDATE ".$lagerartikel[$ij]['name_de']." Lagernd: ".($lagernd-$reserviert)."\r\n";
    } else {
      //if($print_echo)
      //          echo $lagerartikel[$ij]['name_de']." Lagernd: ".($lagernd-$reserviert)."\r\n";
    }
    return $message;
  }


  // pruefe ob es artikel noch im lager gibt bzw. ob es eine reservierung gibt
  function LagerCheck($adresse,$artikel,$menge,$objekt="",$parameter="")
  {
    $summe_im_lager = $this->app->DB->Select("SELECT SUM(li.menge) FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz WHERE li.artikel='$artikel'
        AND lp.autolagersperre!=1");

    //$artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND datum>=NOW() AND objekt!='lieferschein'");
    $artikel_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='".$artikel."' AND (datum>=NOW() OR datum='0000-00-00')");

    if($objekt!="")
    {
      $artikel_fuer_adresse_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert 
          WHERE artikel='".$artikel."' AND menge >='$menge' AND adresse='$adresse' AND objekt='$objekt' AND parameter='$parameter' AND (datum>=NOW() OR datum='0000-00-00')");
    } else { 
      $artikel_fuer_adresse_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert 
          WHERE artikel='".$artikel."' AND menge >='$menge' AND adresse='$adresse' AND (datum>=NOW() OR datum='0000-00-00') AND objekt!='lieferschein'");
    }

    //    echo "im Lager: $summe_im_lager reservier: $artikel_reserviert reseveiert fuer adresse: $artikel_fuer_adresse_reserviert";

    if($artikel_fuer_adresse_reserviert>0 && $summe_im_lager >= $artikel_reserviert)
      return 1;

    if(($summe_im_lager - $artikel_reserviert) >= $menge)
    {
      return 1;
    }
    else { return 0; }
  }

  function AngebotSuche($parsetarget)
  {
    $treffer = $this->app->Secure->GetPOST("treffer");
    if($treffer > 0 ) {
      $_SESSION[angebottreffer] = $treffer;
      $_SESSION[page] = 1;
    }
    else
      if($_SESSION[angebottreffer] <= 0)
        $_SESSION[angebottreffer] = 10;

    $this->app->Tpl->Set(TREFFER,$_SESSION[angebottreffer]);


    $suchwort= $this->app->Secure->GetPOST("suchwort");
    $name= $this->app->Secure->GetPOST("name");
    $plz= $this->app->Secure->GetPOST("plz");
    $angebot= $this->app->Secure->GetPOST("angebot");
    $kundennummer= $this->app->Secure->GetPOST("kundennummer");

    $_SESSION[angebotsuchwort]=$suchwort; //$this->app->Tpl->Set(SUCHWORT,$_SESSION[angebotsuchwort]);
    $_SESSION[angebotname]=$name; $this->app->Tpl->Set(NAME,$_SESSION[angebotname]);
    $_SESSION[angebotplz]=$plz; $this->app->Tpl->Set(PLZ,$_SESSION[angebotplz]); 
    $_SESSION[angebotangebot]=$angebot; $this->app->Tpl->Set(ANGEBOT,$_SESSION[angebotangebot]);
    $_SESSION[angebotkundennummer]=$kundennummer; $this->app->Tpl->Set(KUNDENNUMMER,$_SESSION[angebotkundennummer]);

    $suche = $this->app->Secure->GetPOST("suche");

    //$this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");

    if(($_SESSION[angebotsuchwort]!="" || $_SESSION[angebotname]!="" || $_SESSION[angebotplz]!="" || $_SESSION[angebotangebot]!="" || $_SESSION[angebotkundennummer]!="") && $suche!=""){

      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      if($suchwort!="")
      {
        return "SELECT DATE_FORMAT(a.datum,'%d.%m.%y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Angebot, ad.kundennummer as kunde, a.name, p.abkuerzung as projekt, a.status, a.id
          FROM angebot a, projekt p, adresse ad WHERE
          (a.plz LIKE '%$suchwort%' OR a.name LIKE '%$suchwort%' OR a.belegnr LIKE '%$suchwort%') 
          AND p.id=a.projekt AND a.adresse=ad.id
          order by a.datum DESC, a.id DESC";
      } else {
        return "SELECT DATE_FORMAT(a.datum,'%d.%m.%y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Angebot, ad.kundennummer as kunde, a.name, p.abkuerzung as projekt, a.status, a.id
          FROM angebot a, projekt p, adresse ad WHERE 
          (ad.kundennummer LIKE '%{$_SESSION[angebotkundennummer]}%' AND a.plz LIKE '%{$_SESSION[angebotplz]}%' 
           AND a.name LIKE '%{$_SESSION[angebotname]}%' AND a.belegnr LIKE '%{$_SESSION[angebotangebot]}%' ) 
          AND p.id=a.projekt AND a.adresse=ad.id
          order by a.datum DESC, a.id DESC";

      }

      /*
         return ("SELECT DISTINCT a.nummer, a.name_de as Artikel, p.abkuerzung, a.id FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE 
         a.name_de LIKE '%$name%' AND
         a.nummer LIKE '$nummer%'AND
         p.abkuerzung LIKE '%$projekt%'
         AND geloescht='0'
         ORDER by a.id DESC");
       */
      //      SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
      //      FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt='Kunde' ORDER by a.id DESC

    } else {

      return "SELECT DATE_FORMAT(a.datum,'%d.%m.%y') as vom, if(a.belegnr,a.belegnr,'ohne Nummer') as Angebot, ad.kundennummer as kunde, a.name, p.abkuerzung as projekt, a.status, a.id
        FROM angebot a, projekt p, adresse ad WHERE p.id=a.projekt AND a.adresse=ad.id order by a.datum DESC, a.id DESC";

    }
    //$this->app->Tpl->Set(INHALT,"");
  }



  function WebmailSuche($parsetarget,$rolle)
  {
    $suche = $this->app->Secure->GetPOST("suche");

    $name = $this->app->Secure->GetPOST("name"); $this->app->Tpl->Set(SUCHENAME,$name);
    $nummer = $this->app->Secure->GetPOST("nummer"); $this->app->Tpl->Set(SUCHENUMMER,$nummer);
    $typ = $this->app->Secure->GetPOST("typ");

    $this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");

    $projekt = $this->app->Secure->GetPOST("projekt");  $this->app->Tpl->Set(SUCHEPROJEKT,$projekt);
    $limit = $this->app->Secure->GetPOST("limit"); if($limit=="" || $limit ==0) $limit=10; $this->app->Tpl->Set(SUCHELIMIT,$limit);

    if($name!="" && $suche!="")
    {
      $_SESSION[name_webmailsuche] = $name;
    } elseif ($suche!="" && $name=="")
    $_SESSION[name_webmailsuche] = "";

    if($name=="" && $suche!="")
    {
      $_SESSION[name_artikel] = $name;
    } 

    $_SESSION[nummer] = $nummer;
    $_SESSION[projekt] = $projekt;

    $adresse = $this->app->User->getAdresse();

    if($name==""){ $name = $_SESSION[name_webmailsuche];  $this->app->Tpl->Set(SUCHENAME,$name);}
    if($nummer==""){$nummer= $_SESSION[nummer]; $this->app->Tpl->Set(SUCHENUMMER,$nummer);}
    if($projekt==""){$projekt= $_SESSION[projekt]; $this->app->Tpl->Set(SUCHEPROJEKT,$projekt);}

    if($name!="" || $nummer!="" || $projekt!="") $suche ="suche";


    $this->app->Tpl->Parse($parsetarget,"webmailsuche.tpl");
    if(($name!="" || $nummer!="" || $projekt!="") && $suche!=""){


      return("SELECT DATE_FORMAT(e.empfang,'%d.%m.%Y %H:%i') as zeit,CONCAT(LEFT(e.subject,30),'...') as betreff, e.sender,e.id
          FROM     emailbackup_mails e
          WHERE    e.webmail IN (SELECT id FROM emailbackup WHERE emailbackup.adresse = '$adresse') 
          AND e.sender LIKE '%$name%' AND
          e.subject LIKE '$nummer%'
          ORDER BY e.empfang DESC");

      //p.abkuerzung LIKE '%$projekt%'

      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      /*
         return("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as zeit,CONCAT(LEFT(tn.betreff,30),'...') as betreff, t.kunde, p.abkuerzung,
         tn.id FROM ticket_nachricht tn LEFT JOIN ticket t ON t.schluessel=tn.ticket LEFT JOIN projekt p ON p.id=t.projekt WHERE 
         t.kunde LIKE '%$name%' AND
         tn.betreff LIKE '$nummer%'AND
         p.abkuerzung LIKE '%$projekt%'
         ORDER by tn.zeit DESC");
       */
      //      SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
      //      FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt='Kunde' ORDER by a.id DESC
      //       $table->Display(INHALT,"auftrag","kundeuebernehmen","In Auftrag einf&uuml;gen");
    } else {
      return "SELECT DATE_FORMAT(e.empfang,'%d.%m.%Y %H:%i') as zeit,CONCAT(LEFT(e.subject,30),'...') as betreff, e.sender,e.id
        FROM     emailbackup_mails e
        WHERE    webmail IN (SELECT id FROM emailbackup WHERE emailbackup.adresse = '$adresse')
        ORDER BY e.empfang DESC";

      //      return "SELECT DATE_FORMAT(e.empfang,'%d.%m.%Y %H:%i') as zeit,CONCAT(LEFT(e.subject,30),'...') as betreff, e.sender, 
      //      e.id FROM emailbackup_mails e WHERE 
      //     ORDER by e.empfang DESC";
    }



    //$this->app->Tpl->Set(INHALT,"");
  }


  function TicketArchivSuche($parsetarget,$rolle)
  {
    $suche = $this->app->Secure->GetPOST("suche");

    $name = $this->app->Secure->GetPOST("name"); $this->app->Tpl->Set(SUCHENAME,$name);
    $nummer = $this->app->Secure->GetPOST("nummer"); $this->app->Tpl->Set(SUCHENUMMER,$nummer);
    $typ = $this->app->Secure->GetPOST("typ");

    $this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");

    $projekt = $this->app->Secure->GetPOST("projekt");  $this->app->Tpl->Set(SUCHEPROJEKT,$projekt);
    $limit = $this->app->Secure->GetPOST("limit"); if($limit=="" || $limit ==0) $limit=10; $this->app->Tpl->Set(SUCHELIMIT,$limit);

    if($name!="" && $suche!="")
    {
      $_SESSION[name_ticketarchiv] = $name;
    } elseif ($suche!="" && $name=="")
    $_SESSION[name_ticketarchiv] = "";


    if($name=="" && $suche!="")
    {
      $_SESSION[name_artikel] = $name;
    } 

    $_SESSION[nummer] = $nummer;
    $_SESSION[projekt] = $projekt;


    if($name==""){ $name = $_SESSION[name_ticketarchiv];  $this->app->Tpl->Set(SUCHENAME,$name);}
    if($nummer==""){$nummer= $_SESSION[nummer]; $this->app->Tpl->Set(SUCHENUMMER,$nummer);}
    if($projekt==""){$projekt= $_SESSION[projekt]; $this->app->Tpl->Set(SUCHEPROJEKT,$projekt);}

    if($name!="" || $nummer!="" || $projekt!="") $suche ="suche";


    $this->app->Tpl->Parse($parsetarget,"ticketsuchearchiv.tpl");
    if(($name!="" || $nummer!="" || $projekt!="") && $suche!=""){


      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      return("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as zeit,CONCAT(LEFT(tn.betreff,30),'...') as betreff, t.kunde, p.abkuerzung,
          tn.id FROM ticket_nachricht tn LEFT JOIN ticket t ON t.schluessel=tn.ticket LEFT JOIN projekt p ON p.id=t.projekt WHERE 
          t.kunde LIKE '%$name%' AND
          tn.betreff LIKE '$nummer%'AND
          p.abkuerzung LIKE '%$projekt%'
          ORDER by tn.zeit DESC");

      //      SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
      //      FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt='Kunde' ORDER by a.id DESC
      //       $table->Display(INHALT,"auftrag","kundeuebernehmen","In Auftrag einf&uuml;gen");
    } else {
      return "SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as zeit,CONCAT(LEFT(tn.betreff,30),'...') as betreff, t.kunde, 
        tn.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket AND tn.status='beantwortet' AND t.zugewiesen=1 
        AND inbearbeitung!='1'
        ORDER by tn.zeitausgang DESC";
    }



    //$this->app->Tpl->Set(INHALT,"");
  }

  function ArtikelSuche($parsetarget,$rolle)
  {
    $suche = $this->app->Secure->GetPOST("suche");
    $suchwort= $this->app->Secure->GetPOST("suchwort");
    /* Auftrage fuer manuelle freigabe */

    $name = $this->app->Secure->GetPOST("name"); $this->app->Tpl->Set(SUCHENAME,$name);
    $nummer = $this->app->Secure->GetPOST("nummer"); $this->app->Tpl->Set(SUCHENUMMER,$nummer);
    $typ = $this->app->Secure->GetPOST("typ");

    $this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");

    $projekt = $this->app->Secure->GetPOST("projekt");  $this->app->Tpl->Set(SUCHEPROJEKT,$projekt);
    $limit = $this->app->Secure->GetPOST("limit"); if($limit=="" || $limit ==0) $limit=10; $this->app->Tpl->Set(SUCHELIMIT,$limit);

    if($name!="" && $suche!="")
    {
      $_SESSION[name_artikel] = $name;
    } elseif ($suche!="" && $name=="")
    $_SESSION[name_artikel] = "";


    if($name=="" && $suche!="")
    {
      $_SESSION[name_artikel] = $name;
    } 

    $_SESSION[nummer] = $nummer;
    $_SESSION[projekt] = $projekt;


    if($name==""){ $name = $_SESSION[name_artikel];  $this->app->Tpl->Set(SUCHENAME,$name);}
    if($nummer==""){$nummer= $_SESSION[nummer]; $this->app->Tpl->Set(SUCHENUMMER,$nummer);}
    if($projekt==""){$projekt= $_SESSION[projekt]; $this->app->Tpl->Set(SUCHEPROJEKT,$projekt);}

    if($name!="" || $nummer!="" || $projekt!="") $suche ="suche";


    $this->app->Tpl->Parse($parsetarget,"artikelsuche.tpl");
    if(($name!="" || $nummer!="" || $projekt!="" || $suchwort!="") && $suche!=""){
      if($suchwort!="")
      {

        return ("SELECT DISTINCT a.nummer, a.name_de as Artikel, p.abkuerzung, a.id FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE 
            (a.name_de LIKE '%$suchwort%' OR
             a.nummer LIKE '%$suchwort%') 
            AND geloescht='0'
            ORDER by a.id DESC");

      } else {
        return ("SELECT DISTINCT a.nummer, a.name_de as Artikel, p.abkuerzung, a.id FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE 
            a.name_de LIKE '%$name%' AND
            a.nummer LIKE '%$nummer%' AND
            p.abkuerzung LIKE '%$projekt%'
            AND a.geloescht='0'
            ORDER by a.id DESC");
      }

      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");

      //      SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
      //      FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt='Kunde' ORDER by a.id DESC
      //       $table->Display(INHALT,"auftrag","kundeuebernehmen","In Auftrag einf&uuml;gen");
    } else {

      return "SELECT DISTINCT a.nummer, a.name_de as Artikel, p.abkuerzung, a.id FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht='0'
        ORDER by a.id DESC";

    }
    //$this->app->Tpl->Set(INHALT,"");
  }

  function AdressSuche($parsetarget,$rolle)
  {
    $suche = $this->app->Secure->GetPOST("suche");
    /* Auftrage fuer manuelle freigabe */
    if($rolle!="")
      $this->app->Tpl->Set(SUBHEADING,"$rolle suchen");
    else
      $this->app->Tpl->Set(SUBHEADING,"Adresse suchen");

    $name = $this->app->Secure->GetPOST("name"); $this->app->Tpl->Set(SUCHENAME,$name);
    $typ = $this->app->Secure->GetPOST("typ");
    $ansprechpartner = $this->app->Secure->GetPOST("ansprechpartner");  $this->app->Tpl->Set(SUCHEANSPRECHPARTNER,$ansprechpartner);
    $abteilung= $this->app->Secure->GetPOST("abteilung");
    $unterabteilung= $this->app->Secure->GetPOST("unterabteilung");
    $adresszusatz= $this->app->Secure->GetPOST("adresszusatz");
    $email= $this->app->Secure->GetPOST("email");
    $telefon= $this->app->Secure->GetPOST("telefon");
    $telefax= $this->app->Secure->GetPOST("telefax");
    $ustid= $this->app->Secure->GetPOST("ustid");
    $land= $this->app->Secure->GetPOST("land");
    $plz= $this->app->Secure->GetPOST("plz");  $this->app->Tpl->Set(SUCHEPLZ,$plz);
    $ort= $this->app->Secure->GetPOST("ort");  $this->app->Tpl->Set(SUCHEORT,$ort);
    $strasse= $this->app->Secure->GetPOST("strasse");  $this->app->Tpl->Set(SUCHESTRASSE,$strasse);
    $kundennummer= $this->app->Secure->GetPOST("kundennummer");  $this->app->Tpl->Set(KUNDENNUMMER,$kundennummer);

    if($name!="" && $suche!="")
    {
      $_SESSION[name] = $name;
    } elseif ($suche!="" && $name=="")
    $_SESSION[name] = "";


    if($name=="" && $suche!="")
    {
      $_SESSION[name] = $name;
    } 

    $_SESSION[ort] = $ort;
    $_SESSION[plz] = $plz;


    if($name==""){ $name = $_SESSION[name];  $this->app->Tpl->Set(SUCHENAME,$name);}
    if($ort==""){$ort= $_SESSION[ort]; $this->app->Tpl->Set(SUCHEORT,$ort);}
    if($plz==""){$plz= $_SESSION[plz]; $this->app->Tpl->Set(SUCHEPLZ,$plz);}

    if($name!="" || $ort!="" || $plz!="") $suche ="suche";

    $this->app->Tpl->Parse($parsetarget,"kundensuche.tpl");

    if(($name!="" || $kundennummer!="" || $strasse!="" || $ort!="" || $plz!="") && $suche!=""){
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      return ("SELECT DISTINCT a.kundennummer, a.name, a.ort, a.telefon, a.email, a.id FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE 
          a.name LIKE '%$name%' AND 
          a.ansprechpartner LIKE '%$ansprechpartner%' AND 
          a.ort LIKE '%$ort%' AND 
          a.strasse LIKE '%$strasse%' AND 
          a.kundennummer LIKE '%$kundennummer%' AND 
          a.plz LIKE '%$plz%' AND a.geloescht=0 ORDER by a.id DESC");
      //a.plz LIKE '%$plz%' AND r.subjekt='$rolle' ORDER by a.id DESC");

      //      SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
      //      FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt='Kunde' ORDER by a.id DESC
      //       $table->Display(INHALT,"auftrag","kundeuebernehmen","In Auftrag einf&uuml;gen");
    } else {

      return "SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
        FROM adresse a WHERE a.geloescht=0 ORDER by a.name ASC";
      //FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt='$rolle' ORDER by a.id DESC";

    }
    //$this->app->Tpl->Set(INHALT,"");
  }

  function string2array ($string, $template){
#search defined dividers
    preg_match_all ("|%(.+)%|U", $template, $template_matches);
#replace dividers with "real dividers"
    $template = preg_replace ("|%(.+)%|U", "(.+)", $template);
#search matches
    preg_match ("|" . $template . "|", $string, $string_matches);
#[template_match] => $string_match
    foreach ($template_matches[1] as $key => $value){
      $output[$value] = $string_matches[($key + 1)];
    }
    return $output;
  }


  function ImportKontoauszug($cvs,$konto)
  {
    //array vorbereiten
    $type = $this->app->DB->Select("SELECT `type` FROM konten WHERE id='$konto' LIMIT 1");
    $erstezeile = $this->app->DB->Select("SELECT erstezeile FROM konten WHERE id='$konto' LIMIT 1");

    //$erstezeile = html_entity_decode($erstezeile,ENT_QUOTES,"UTF-8");//str_replace("&Prime;",'"',$erstezeile);
    $erstezeile = $this->ReadyForPDF($erstezeile);

    // group all imports
    $stamp = time();

    switch($type)
    {
      case "deutschebank":
        $db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);     
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!
        if($erstezeile != utf8_encode($db_array[0]))
        {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekter CVS von der Deutsche Bank Homepage!</div>");
          break;
        }  

        $doppelte = 0;
        $erfolgreiche = 0; 
        for($i=5;$i<count($db_array)-2;$i++)
        {
          $zeile = $db_array[$i];
          $datensatz = split(';',$zeile);

          $buchung = $this->app->String->Convert($datensatz[0],"%1.%2.%3","%3-%2-%1"); 
          $vorgang = mysqli_real_escape_string($this->app->DB->connection,str_replace('"','',$datensatz[3].' '.$datensatz[4]));
          $soll = str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[13])));
          $haben = str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[14])));
          $waehrung = $datensatz[15];
          $gebuehr = 0;

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));

          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");

          if($check > 0)  
          {
            //echo $buchung." ".$vorgang."<br>";
            //echo "datensatz gibt schon";
            $doppelte++;
          } else {
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");


            $erfolgreiche++;
          }
        }
        break;
      case "vrbank":



        $db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);     
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!
        if($erstezeile != utf8_encode($db_array[0]))
        {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekter CVS von der VR Net Bank Homepage!</div>");
          break;
        }  

        $cvs = str_replace('"','',$cvs);
        $tmp = str_getcsv( $cvs, ';','');

        $i=33;
        $doppelte = 0;
        $erfolgreiche = 0; 

        while($i<count($tmp)-19) // ohne letzte beiden zeilen
        {
          if($i>33)
          {
            $buchung = $this->app->String->Convert(substr($tmp[$i],-10),"%1.%2.%3","%3-%2-%1");
            $haben_erkennung=substr($tmp[$i+9],0,-11);
          }
          else
          {
            //      echo $tmp[$i];
            $buchung = $this->app->String->Convert($tmp[$i],"%1.%2.%3","%3-%2-%1");
            $haben_erkennung=str_replace('"','',substr($tmp[$i+9],0,-11));
          }
          if(trim($haben_erkennung)=="H")
          {
            $haben = str_replace('"','',$tmp[$i+8]);
            $soll = 0;
          } else {
            $soll = str_replace('"','',$tmp[$i+8]);
            $haben = 0;
          }

          //echo "<br>HH".$buchung." ".$haben_erkennung." $haben $soll<br><br>";

          // zaehle 10 felder

          //              echo str_replace('"','',$tmp[$i]);
          //echo "<br>HH".$buchung."<br>";
          $vorgang = mysqli_real_escape_string($this->app->DB->connection,utf8_encode($tmp[$i+6]));

          //              $vorgang ="";

          $haben = str_replace(',','.',str_replace('.','',$haben));
          $soll = str_replace(',','.',str_replace('.','',$soll));
          $waehrung = 'EUR';
          $gebuehr = 0;


          $i=$i+9;

          //"Buchungstag";"Valuta";"Auftraggeber/Zahlungsempfänger";"Empfänger/Zahlungspflichtiger";"Konto-Nr.";"BLZ";"Vorgang/Verwendungszweck";"Währung";"Umsatz";" "

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));

          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");

          if($check > 0)  
          {
            //echo $buchung." ".$vorgang."<br>";
            //echo "datensatz gibt schon";
            $doppelte++;
          } else {
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");


            $erfolgreiche++;
          }
        }
        break;
      case "postbank_201205":
        $db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!

        if($erstezeile != utf8_encode($db_array[0]))
        {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekter CVS von der Post Bank Homepage!</div>");
          break;
        } 

        $doppelte = 0;
        $erfolgreiche = 0;
        //for($i=11;$i<count($db_array)-1;$i++) // vor dem 28.01 war es immer an pos 11 aber dann ab 10 warum auch immer
        for($i=10;$i<count($db_array)-1;$i++)
        {
          $zeile = $db_array[$i];
          $datensatz = split("\t",$zeile);

          $buchung = $this->app->String->Convert($datensatz[0],"%1.%2.%3","%3-%2-%1");
          $vorgang = mysqli_real_escape_string($this->app->DB->connection,utf8_encode($datensatz[2]." ".$datensatz[3]." ".$datensatz[4]));

          // wenn minus aus soll sonst haben
          if(preg_match("/-/",$datensatz[6]))
          {
            $soll = str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[6])));
            $haben = 0;
          }
          else {
            $soll = 0;
            $haben = str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[6])));
          }

          $waehrung = 'EUR';
          $gebuehr = 0;

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));

          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");

          if($check > 0)
          {
            //echo $buchung." ".$vorgang."<br>";
            //echo "datensatz gibt schon";
            $doppelte++;
          } else {
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");


            $erfolgreiche++;
          }
        }
        break;

      case "postbank":
        $db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);     
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!


        if(utf8_decode($erstezeile) != ($db_array[0]))
        {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekter CVS von der Post Bank Homepage!</div>");
          break;
        }  

        $doppelte = 0;
        $erfolgreiche = 0; 
        //$cvs = str_replace('"','',$cvs);
        /*
         */
        //        $tmp = str_getcsv( $cvs, ';','');

        //for($i=11;$i<count($db_array)-1;$i++) // vor dem 28.01 war es immer an pos 11 aber dann ab 10 warum auch immer
        for($i=8;$i<count($db_array)-1;$i++)
        {
          $zeile = $db_array[$i];
          $zeile = str_replace('"','',$zeile);
          $datensatz = split(";",$zeile);

          $buchung = $this->app->String->Convert($datensatz[0],"%1.%2.%3","%3-%2-%1"); 
          $vorgang = mysqli_real_escape_string($this->app->DB->connection,utf8_encode(trim($datensatz[2])." ".trim($datensatz[3])." ".trim($datensatz[4])));

          // wenn minus aus soll sonst haben
          if(preg_match("/-/",$datensatz[6]))
          {
            $soll = substr(str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[6]))),0,-2);
            $haben = 0;
          }
          else {
            $soll = 0;
            $haben = substr(str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[6]))),0,-2);
          }

          $waehrung = 'EUR';
          $gebuehr = 0;

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));

          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");

          if($check > 0)  
          {
            //echo "datensatz gibt schon<br>";
            //echo $buchung." ".$vorgang."<br>";
            $doppelte++;
          } else {
            //echo $buchung." ".$vorgang."<br>";
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");


            $erfolgreiche++;
          }
        }
        break;
      case "paypal":
        $db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);     
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!

        if($erstezeile != utf8_encode(substr($db_array[0],0,strlen($erstezeile))))
        {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekter CVS von der Post Bank Homepage!</div>");
          break;
        }  

        $doppelte = 0;
        $erfolgreiche = 0; 
        for($i=1;$i<count($db_array)-1;$i++)
        {
          $zeile = $db_array[$i];

          $datensatz = split('","',$zeile);

          $datensatz[0] = trim(str_replace('"','',$datensatz[0]));
          $buchung = $this->app->String->Convert($datensatz[0],"%1.%2.%3","%3-%2-%1"); 
          //$vorgang = utf8_encode($datensatz[3]." ".$datensatz[16]." ".$datensatz[13]); //eigentlich neu
          $vorgang = trim(mysqli_real_escape_string($this->app->DB->connection,utf8_encode($datensatz[3]." ".$datensatz[15]." ".$datensatz[12]))); // altes format

          // wenn minus aus soll sonst haben
          if(preg_match("/-/",$datensatz[7]))
          {
            $soll = str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[7])));
            $haben = 0;
          }
          else {
            $soll = 0;
            $haben = str_replace(',','.',str_replace('.','',str_replace('-','',$datensatz[7])));
          }

          $waehrung = $datensatz[6];
          $gebuehr = str_replace(',','.',str_replace('.','',$datensatz[8]));

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));
          //echo "ort $buchung $pruefsumme";
          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");


          // schaue ob es pruefsumme ein tag frueher oder spaeter gibt (wegen Zeitproblem)
          if($check <= 0){
            $prevdate = $this->app->DB->Select("SELECT DATE_ADD('$buchung',INTERVAL 1 DAY)");
            $pruefsumme = md5(serialize(array($prevdate,$vorgang,$soll,$haben,$waehrung)));
            //echo "zuvor $newdate $pruefsumme";
            $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");
          }

          if($check <= 0){
            $nextdate = $this->app->DB->Select("SELECT DATE_SUB('$buchung',INTERVAL 1 DAY)");
            $pruefsumme = md5(serialize(array($nextdate,$vorgang,$soll,$haben,$waehrung)));
            //echo "spaeter $nextdate $pruefsumme";
            $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");
          }

          if($check <= 0 & $vorgang!="")
            $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE vorgang='$vorgang' AND buchung='$buchung' 
                AND soll='$soll' AND haben='$haben' AND waehrung='$waehrung' AND konto='$konto' LIMIT 1");


          if($check <= 0 & $vorgang!="")
            $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE vorgang='$vorgang' AND buchung='$prevdate' 
                AND soll='$soll' AND haben='$haben' AND waehrung='$waehrung' AND konto='$konto' LIMIT 1");

          if($check <= 0 & $vorgang!="")
            $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE vorgang='$vorgang' AND buchung='$nextdate' 
                AND soll='$soll' AND haben='$haben' AND waehrung='$waehrung' AND konto='$konto' LIMIT 1");


          //              $datensatz[12]

          // sobald es die Tranaktion schon gibt kann man diese ignorieren und vergessen
          if($datensatz[12] !="")
            $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE vorgang LIKE '%{$datensatz[12]}%' AND konto='$konto' LIMIT 1");

          if($check > 0)  
          {
            // echo "<br>datensatz gibt schon";
            // echo $buchung." ".$vorgang."<br>";
            // $doppelte++;
          } else {
            //          echo "<br>datensatz FEHLT";
            //          echo $buchung." ".$vorgang." $haben $soll<br>";
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");

            $erfolgreiche++;
          }
        }
        break;

      case "kreditkarte":
        $db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);     
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!

        if($erstezeile != utf8_encode(substr($db_array[0],0,strlen($erstezeile))))
        {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekter CVS von der Post Bank Homepage!</div>");
          break;
        }  

        $doppelte = 0;
        $erfolgreiche = 0; 
        for($i=1;$i<count($db_array)-1;$i++)
        {
          $zeile = $db_array[$i];

          $datensatz = split('","',$zeile);
          //2010-02-22 15:19:13
          $buchung = $this->app->String->Convert($datensatz[0],"%1-%2-%3 %4:%5:%6","%1-%2-%3");
          $buchung = str_replace('"','',$buchung);
          //$vorgang = utf8_decode($datensatz[7]." ".$datensatz[8]." ".$datensatz[1]." ".$datensatz[31]);
          $vorgang = mysqli_real_escape_string($this->app->DB->connection,utf8_decode($datensatz[7]." ".$datensatz[8]." ".$datensatz[1]));

          // wenn minus aus soll sonst haben
          if($datensatz[5]=="REFUND_CAP")
          {
            $soll = str_replace(',','',str_replace('-','',$datensatz[2]));
            $haben = 0;
          }
          else {
            $soll = 0;
            $haben = str_replace(',','',str_replace('-','',$datensatz[2]));
          }


          $waehrung = $datensatz[3];
          $gebuehr = 0;

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));

          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");

          if($check > 0)  
          {
            //echo $buchung." ".$vorgang."<br>";
            //echo $buchung." ".$pruefsumme."<br>";
            //echo "datensatz gibt schon";
            $doppelte++;
          } else {
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");

            $erfolgreiche++;
          }
        }
        break;
      case "kasse":
        $db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);     
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!
        /*if(($erstezeile != utf8_encode(substr($db_array[0],0,strlen($erstezeile)))) || $erstezeile=="")
          {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekter CVS von der Kasse!</div>");
          break;
          }*/

        $doppelte = 0;
        $erfolgreiche = 0; 
        for($i=1;$i<count($db_array)-1;$i++)
        {
          $zeile = $db_array[$i];

          $datensatz = split('","',$zeile);
          $buchung = $this->app->String->Convert($datensatz[1],"%1.%2.%3","%3-%2-%1");
          $buchung = str_replace('"','',$buchung);
          $vorgang = mysqli_real_escape_string($this->app->DB->connection,utf8_encode($datensatz[2]));


          if($datensatz[3]=="Einnahme" || $datensatz[3]=="einnahme")
          {
            $haben = $datensatz[4];
            $soll="";
          }
          else
          {
            $soll = $datensatz[4];
            $haben="";
          }

          $waehrung = $datensatz[5];
          $waehrung= str_replace('"','',$waehrung);
          $gebuehr = 0; //str_replace(',','.',str_replace('.','',$datensatz[8]));

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));

          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");

          if($check > 0)  
          {
            //echo $buchung." ".$vorgang."<br>";
            //echo "datensatz gibt schon";
            $doppelte++;
          } else {
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");
            $erfolgreiche++;
          }
        }
        break;
      case "konto":
        $kontoimportinfo = $this->app->DB->SelectArr("SELECT * FROM konten WHERE id='$konto' LIMIT 1");
        switch($kontoimportinfo[0][importtrennzeichen])
        {
            case "semikolon": $trennzeichen =';'; break;
            case "komma": $trennzeichen =','; break;
            default: $trennzeichen =','; break;
        }
        switch($kontoimportinfo[0][importdatenmaskierung])
        {
          case "gaensefuesschen": $maskierung = '"'; break;
        }

        //$db_array = str_getcsv ( $cvs, "\n");
        $db_array = parse_csv ( $cvs);


        //$db_array = preg_split("/(\r\n)+|(\n|\r)+/",$cvs);     
        //pruefen ob es eine korrekte datei ist sonst abbrechen!!!
        $codierung = $this->app->DB->Select("SELECT codierung FROM konten WHERE id='$konto' LIMIT 1");

        switch($codierung)
        {
          case "utf8_encode": 
            for($idbarray=0;$idbarray<count($db_array);$idbarray++)
            {
              $db_array[$idbarray]=utf8_encode($db_array[$idbarray]);
            }
            break;
        }
/*
        $vglerstezeile = strtok($cvs, "\n");//$db_array[0];
        if(is_array($vglerstezeile)) $vglerstezeile=$db_array[0][0];

        if($erstezeile != (substr($vglerstezeile,0,strlen($erstezeile))))
        {
          $this->app->Tpl->Set(ERROR,"<div class=\"error\">Die Datei ist keine korrekte csv-Datei f&uuml;r dieses Konto (Erste Zeile ist unterschiedlich)!</div>");
          break;
        }  
*/
        $kontoimportinfo = $this->app->DB->SelectArr("SELECT * FROM konten WHERE id='$konto' LIMIT 1");
        $doppelte = 0;
        $erfolgreiche = 0; 

        if($kontoimportinfo[0][importletztenzeilenignorieren] > 0)
          $max_count_db_array = count($db_array)-($kontoimportinfo[0][importletztenzeilenignorieren] + 1);
        else
          $max_count_db_array = count($db_array)-1;

        //startszeile TODO
        for($i=$kontoimportinfo[0][importerstezeilenummer]-1;$i<=$max_count_db_array;$i++)
        {
          $datensatz = $db_array[$i];
    //      if(is_array($zeile)) $zeile = $db_array[$i][0];


/*
          unset($datensatz);
          switch($kontoimportinfo[0][importtrennzeichen])
          {
            case "semikolon": $datensatz = explode(';',$zeile); break;
            case "komma": $datensatz = explode(',',$zeile); break;
            default: $this->app->Tpl->Set(ERROR,"<div class=\"error\">Trennzeichen nicht definiert!</div>");
          }

          // alle Maskierungen am Anfang und Ende
          for($y=0;$y<count($datensatz);$y++)
          {
            switch($kontoimportinfo[0][importdatenmaskierung])
            {
              case "gaensefuesschen": $datensatz[$y] = str_replace('"','',$datensatz[$y]);break;
            }
          }
*/

          //Datum   
          $lieferdatum = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
          $buchung = $this->app->String->Convert($datensatz[$kontoimportinfo[0][importfelddatum]-1],
          $kontoimportinfo[0][importfelddatumformat],$kontoimportinfo[0][importfelddatumformatausgabe]);

          $fieldstmp = explode("+",$kontoimportinfo[0][importfeldbuchungstext]);
//print_r($datensatz);
//exit;

          $vorgang = "";
          for($fx=0;$fx<count($fieldstmp);$fx++)
          {
            $vorgang .= $datensatz[$fieldstmp[$fx]-1]." ";
          }

          $vorgang = mysqli_real_escape_string($this->app->DB->connection,utf8_encode($vorgang));

          $haben = "";
          $soll="";

          if($kontoimportinfo[0][importextrahabensoll]=="1")
          {
            $this->LogFile("1");
            $haben = $datensatz[$kontoimportinfo[0][importfeldhaben]-1];  
            $soll = $datensatz[$kontoimportinfo[0][importfeldsoll]-1];  
 
            if(strpos($haben,',')!==false && strpos($haben,'.')!==false)
              $haben = str_replace('.','',$haben);
            $haben = str_replace(',','.',$haben);
  
            if(strpos($soll,',')!==false && strpos($soll,'.')!==false)
              $soll = str_replace('.','',$soll);
            $soll = str_replace(',','.',$soll);

          } else {
            $this->LogFile("2");
            $betrag = $datensatz[$kontoimportinfo[0][importfeldbetrag]-1];
            // enn es ein komma gibt mach alle punkte platt und dann am schluss komma zu punkt
            // betrag formatieren
            if(strpos($betrag,',')!==false && strpos($betrag,'.')!==false)
              $betrag = str_replace('.','',$betrag);
            $betrag = str_replace(',','.',$betrag);

            // wenn betrag extra haben und soll Kennung habt. Ansonsten einfache positiv = haben negativ = soll
            if($kontoimportinfo[0][importfeldhabensollkennung] !="")
            {
            $this->LogFile("3");
              $kennung_habensoll = $datensatz[$kontoimportinfo[0][importfeldhabensollkennung]-1];
              if($kennung_habensoll==$kontoimportinfo[0][importfeldkennunghaben])
                $haben = $betrag;
              else if ( $kennung_habensoll==$kontoimportinfo[0][importfeldkennungsoll])
                $soll=str_replace('-','',$betrag);
              else
                $soll=str_replace('-','',$betrag);
            } else {
            $this->LogFile("4");
              if($betrag > 0)
                $haben = $betrag;
              else $soll = str_replace('-','',$betrag);
            }
          }

          $waehrung = $datensatz[$kontoimportinfo[0][importfeldwaehrung]-1];

          //        $gebuehr = 0; //str_replace(',','.',str_replace('.','',$datensatz[8]));

          $pruefsumme = md5(serialize(array($buchung,$vorgang,$soll,$haben,$waehrung)));

          $check = $this->app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");

          if($check > 0)  
          {
            //echo $buchung." ".$vorgang."<br>";
            //echo "datensatz gibt schon";
            $doppelte++;
          } else {
            $this->app->DB->Insert("INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,bearbeiter,pruefsumme,importgroup)
                VALUE ('','$konto','$buchung','$vorgang','$soll','$haben','$gebuehr','$waehrung',0,'".$this->app->User->GetName()."','$pruefsumme','$stamp')");
            $erfolgreiche++;
          }
        }
        break;




      default:;
    }

    if($doppelte>0)$this->app->Tpl->Set(ERROR,"<div class=\"error\">$doppelte Eintr&auml;ge wurden nicht importiert da sie bereits vorhanden waren!</div>");
    if($erfolgreiche>0)$this->app->Tpl->Add(ERROR,"<div class=\"info\">$erfolgreiche Eintr&auml;ge importiert.</div>");


  }
  function SelectLaenderliste($selected="")
  {
    if($selected=="") $selected="DE";

    $laender = array(
        //'Afghanistan'  => 'AF',
        //'&Auml;gypten'  => 'EG',
        //'Albanien'  => 'AL',
        //'Algerien'  => 'DZ',
        //'Andorra'  => 'AD',
        //'Angola'  => 'AO',
        //'Anguilla'  => 'AI',
        //'Antarktis'  => 'AQ',
        //'Antigua und Barbuda'  => 'AG',
        //'&Auml;quatorial Guinea'  => 'GQ',
        //'Argentinien'  => 'AR',
        //'Armenien'  => 'AM',
        //'Aruba'  => 'AW',
        //'Aserbaidschan'  => 'AZ',
        //'&Auml;thiopien'  => 'ET',
        //'Australien'  => 'AU',
        //'Bahamas'  => 'BS',
        //'Bahrain'  => 'BH',
        //'Bangladesh'  => 'BD',
        //'Barbados'  => 'BB',
      'Belgien'  => 'BE',
      //'Belize'  => 'BZ',
      //'Benin'  => 'BJ',
      //'Bermudas'  => 'BM',
      //'Bhutan'  => 'BT',
      //'Birma'  => 'MM',
      //'Bolivien'  => 'BO',
      //'Bosnien-Herzegowina'  => 'BA',
      //'Botswana'  => 'BW',
      //'Bouvet Inseln'  => 'BV',
      //'Brasilien'  => 'BR',
      //'Britisch-Indischer Ozean'  => 'IO',
      //'Brunei'  => 'BN',
      'Bulgarien'  => 'BG',
      //'Burkina Faso'  => 'BF',
      //'Burundi'  => 'BI',
      //'Chile'  => 'CL',
      //'China'  => 'CN',
      //'Christmas Island'  => 'CX',
      //'Cook Inseln'  => 'CK',
      //'Costa Rica'  => 'CR',
      'D&auml;nemark'  => 'DK',
      'Deutschland'  => 'DE',
      //'Djibuti'  => 'DJ',
      //'Dominika'  => 'DM',
      //'Dominikanische Republik'  => 'DO',
      //'Ecuador'  => 'EC',
      //'El Salvador'  => 'SV',
      //'Elfenbeink&uuml;ste'  => 'CI',
      //'Eritrea'  => 'ER',
      'Estland'  => 'EE',
      //'Falkland Inseln'  => 'FK',
      //'F&auml;r&ouml;er Inseln'  => 'FO',
      //'Fidschi'  => 'FJ',
      'Finnland'  => 'FI',
      'Frankreich'  => 'FR',
      //'Franz&ouml;sisch Guyana'  => 'GF',
      //'Franz&ouml;sisch Polynesien'  => 'PF',
      //'Franz&ouml;sisches S&uuml;d-Territorium'  => 'TF',
      //'Gabun'  => 'GA',
      //'Gambia'  => 'GM',
      //'Georgien'  => 'GE',
      //'Ghana'  => 'GH',
      //'Gibraltar'  => 'GI',
      //'Grenada'  => 'GD',
      'Griechenland'  => 'GR',
      //'Gr&ouml;nland'  => 'GL',
      'GroÃritannien'  => 'UK',
      'GroÃritannien (UK)'  => 'GB',
      //'Guadeloupe'  => 'GP',
      //'Guam'  => 'GU',
      //'Guatemala'  => 'GT',
      //'Guinea'  => 'GN',
      //'Guinea Bissau'  => 'GW',
      //'Guyana'  => 'GY',
      //'Haiti'  => 'HT',
      //'Heard und McDonald Islands'  => 'HM',
      //'Honduras'  => 'HN',
      //'Hong Kong'  => 'HK',
      //'Indien'  => 'IN',
      //'Indonesien'  => 'ID',
      //'Irak'  => 'IQ',
      //'Iran'  => 'IR',
      'Irland'  => 'IE',
      //'Island'  => 'IS',
      //'Israel'  => 'IL',
      'Italien'  => 'IT',
      //'Jamaika'  => 'JM',
      //'Japan'  => 'JP',
      //'Jemen'  => 'YE',
      //'Jordanien'  => 'JO',
      //'Jugoslawien'  => 'YU',
      //'Kaiman Inseln'  => 'KY',
      //'Kambodscha'  => 'KH',
      //'Kamerun'  => 'CM',
      //'Kanada'  => 'CA',
      //'Kap Verde'  => 'CV',
      //'Kasachstan'  => 'KZ',
      //'Kenia'  => 'KE',
      //'Kirgisistan'  => 'KG',
      //'Kiribati'  => 'KI',
      //'Kokosinseln'  => 'CC',
      //'Kolumbien'  => 'CO',
      //'Komoren'  => 'KM',
      //'Kongo'  => 'CG',
      //'Kongo, Demokratische Republik'  => 'CD',
      //'Kroatien'  => 'HR',
      //'Kuba'  => 'CU',
      //'Kuwait'  => 'KW',
      //'Laos'  => 'LA',
      //'Lesotho'  => 'LS',
      'Lettland'  => 'LV',
      //'Libanon'  => 'LB',
      //'Liberia'  => 'LR',
      //'Libyen'  => 'LY',
      'Liechtenstein'  => 'LI',
      'Litauen'  => 'LT',
      'Luxemburg'  => 'LU',
      //'Macao'  => 'MO',
      //'Madagaskar'  => 'MG',
      //'Malawi'  => 'MW',
      //'Malaysia'  => 'MY',
      //'Malediven'  => 'MV',
      //'Mali'  => 'ML',
      'Malta'  => 'MT',
      //'Marianen'  => 'MP',
      //'Marokko'  => 'MA',
      //'Marshall Inseln'  => 'MH',
      //'Martinique'  => 'MQ',
      //'Mauretanien'  => 'MR',
      //'Mauritius'  => 'MU',
      //'Mayotte'  => 'YT',
      //'Mazedonien'  => 'MK',
      //'Mexiko'  => 'MX',
      //'Mikronesien'  => 'FM',
      //'Mocambique'  => 'MZ',
      //'Moldavien'  => 'MD',
      //'Monaco'  => 'MC',
      //'Mongolei'  => 'MN',
      //'Montserrat'  => 'MS',
      //'Namibia'  => 'NA',
      //'Nauru'  => 'NR',
      //'Nepal'  => 'NP',
      //'Neukaledonien'  => 'NC',
      //'Neuseeland'  => 'NZ',
      //'Nicaragua'  => 'NI',
      'Niederlande'  => 'NL',
      //'Niederl&auml;ndische Antillen'  => 'AN',
      //'Niger'  => 'NE',
      //'Nigeria'  => 'NG',
      //'Niue'  => 'NU',
      //'Nord Korea'  => 'KP',
      //'Norfolk Inseln'  => 'NF',
      'Norwegen'  => 'NO',
      //'Oman'  => 'OM',
      '&Ouml;sterreich'  => 'AT',
      //'Pakistan'  => 'PK',
      //'Pal&auml;stina'  => 'PS',
      //'Palau'  => 'PW',
      //'Panama'  => 'PA',
      //'Papua Neuguinea'  => 'PG',
      //'Paraguay'  => 'PY',
      //'Peru'  => 'PE',
      //'Philippinen'  => 'PH',
      //'Pitcairn'  => 'PN',
      'Polen'  => 'PL',
      'Portugal'  => 'PT',
      //'Puerto Rico'  => 'PR',
      //'Qatar'  => 'QA',
      //'Reunion'  => 'RE',
      //'Ruanda'  => 'RW',
      'Rum&auml;nien'  => 'RO',
      //'Ru&szlig;land'  => 'RU',
      //'Saint Lucia'  => 'LC',
      //'Sambia'  => 'ZM',
      //'Samoa'  => 'AS',
      //'Samoa'  => 'WS',
      //'San Marino'  => 'SM',
      //'Sao Tome'  => 'ST',
      //'Saudi Arabien'  => 'SA',
      'Schweden'  => 'SE',
      'Schweiz'  => 'CH',
      //'Senegal'  => 'SN',
      //'Seychellen'  => 'SC',
      //'Sierra Leone'  => 'SL',
      //'Singapur'  => 'SG',
      'Slowakei'  => 'SK',
      'Slowenien'  => 'SI',
      //'Solomon Inseln'  => 'SB',
      //'Somalia'  => 'SO',
      //'South Georgia, South Sandwich Isl.'  => 'GS',
      'Spanien'  => 'ES',
      //'Sri Lanka'  => 'LK',
      //'St. Helena'  => 'SH',
      //'St. Kitts Nevis Anguilla'  => 'KN',
      //'St. Pierre und Miquelon'  => 'PM',
      //'St. Vincent'  => 'VC',
      //'S&uuml;d Korea'  => 'KR',
      //'S&uuml;dafrika'  => 'ZA',
      //'Sudan'  => 'SD',
      //'Surinam'  => 'SR',
      //'Svalbard und Jan Mayen Islands'  => 'SJ',
      //'Swasiland'  => 'SZ',
      //'Syrien'  => 'SY',
      //'Tadschikistan'  => 'TJ',
      //'Taiwan'  => 'TW',
      //'Tansania'  => 'TZ',
      //'Thailand'  => 'TH',
      //'Timor'  => 'TP',
      //'Togo'  => 'TG',
      //'Tokelau'  => 'TK',
      //'Tonga'  => 'TO',
      //'Trinidad Tobago'  => 'TT',
      //'Tschad'  => 'TD',
      'Tschechische Republik'  => 'CZ',
      //'Tunesien'  => 'TN',
      //'T&uuml;rkei'  => 'TR',
      //'Turkmenistan'  => 'TM',
      //'Turks und Kaikos Inseln'  => 'TC',
      //'Tuvalu'  => 'TV',
      //'Uganda'  => 'UG',
      //'Ukraine'  => 'UA',
      'Ungarn'  => 'HU',
      //'Uruguay'  => 'UY',
      //'Usbekistan'  => 'UZ',
      //'Vanuatu'  => 'VU',
      //'Vatikan'  => 'VA',
      //'Venezuela'  => 'VE',
      //'Vereinigte Arabische Emirate'  => 'AE',
      //'Vereinigte Staaten von Amerika'  => 'US',
      //'Vietnam'  => 'VN',
      //'Virgin Island (Brit.)'  => 'VG',
      //'Virgin Island (USA)'  => 'VI',
      //'Wallis et Futuna'  => 'WF',
      //'Wei&szlig;ru&szlig;land'  => 'BY',
      //'Westsahara'  => 'EH',
      //'Zentralafrikanische Republik'  => 'CF',
      //'Zimbabwe'  => 'ZW',
      'Zypern'  => 'CY'
        );

    foreach ($laender as $land => $kuerzel) {
      $options = $options."<option value=\"$kuerzel\"";
      if ($selected == $kuerzel) $options = $options." selected";
      $options = $options.">$land</option>\n";
    }
    return $options;
  }

  function GetSelectLaenderliste()
  {
    $laender = array(
        'Deutschland'  => 'DE',
        'Afghanistan'  => 'AF',
        '&Auml;gypten'  => 'EG',
        'Albanien'  => 'AL',
        'Algerien'  => 'DZ',
        'Andorra'  => 'AD',
        'Angola'  => 'AO',
        'Anguilla'  => 'AI',
        'Antarktis'  => 'AQ',
        'Antigua und Barbuda'  => 'AG',
        '&Auml;quatorial Guinea'  => 'GQ',
        'Argentinien'  => 'AR',
        'Armenien'  => 'AM',
        'Aruba'  => 'AW',
        'Aserbaidschan'  => 'AZ',
        '&Auml;thiopien'  => 'ET',
        'Australien'  => 'AU',
        'Bahamas'  => 'BS',
        'Bahrain'  => 'BH',
        'Bangladesh'  => 'BD',
        'Barbados'  => 'BB',
        'Belgien'  => 'BE',
        'Belize'  => 'BZ',
        'Benin'  => 'BJ',
        'Bermudas'  => 'BM',
        'Bhutan'  => 'BT',
        'Birma'  => 'MM',
        'Bolivien'  => 'BO',
        'Bosnien-Herzegowina'  => 'BA',
        'Botswana'  => 'BW',
        'Bouvet Inseln'  => 'BV',
        'Brasilien'  => 'BR',
        'Britisch-Indischer Ozean'  => 'IO',
        'Brunei'  => 'BN',
        'Bulgarien'  => 'BG',
        'Burkina Faso'  => 'BF',
        'Burundi'  => 'BI',
        'Chile'  => 'CL',
        'China'  => 'CN',
        'Christmas Island'  => 'CX',
        'Cook Inseln'  => 'CK',
        'Costa Rica'  => 'CR',
        'D&auml;nemark'  => 'DK',
        'Deutschland'  => 'DE',
        'Djibuti'  => 'DJ',
        'Dominika'  => 'DM',
        'Dominikanische Republik'  => 'DO',
        'Ecuador'  => 'EC',
        'El Salvador'  => 'SV',
        'Elfenbeink&uuml;ste'  => 'CI',
        'Eritrea'  => 'ER',
        'Estland'  => 'EE',
        'Falkland Inseln'  => 'FK',
        'F&auml;r&ouml;er Inseln'  => 'FO',
        'Fidschi'  => 'FJ',
        'Finnland'  => 'FI',
        'Frankreich'  => 'FR',
        'Franz&ouml;sisch Guyana'  => 'GF',
        'Franz&ouml;sisch Polynesien'  => 'PF',
        'Franz&ouml;sisches S&uuml;d-Territorium'  => 'TF',
        'Gabun'  => 'GA',
        'Gambia'  => 'GM',
        'Georgien'  => 'GE',
        'Ghana'  => 'GH',
        'Gibraltar'  => 'GI',
        'Grenada'  => 'GD',
        'Griechenland'  => 'GR',
        'Gr&ouml;nland'  => 'GL',
        'Großbritannien'  => 'UK',
        'Großbritannien (UK)'  => 'GB',
        'Guadeloupe'  => 'GP',
        'Guam'  => 'GU',
        'Guatemala'  => 'GT',
        'Guinea'  => 'GN',
        'Guinea Bissau'  => 'GW',
        'Guyana'  => 'GY',
        'Haiti'  => 'HT',
        'Heard und McDonald Islands'  => 'HM',
        'Honduras'  => 'HN',
        'Hong Kong'  => 'HK',
        'Indien'  => 'IN',
        'Indonesien'  => 'ID',
        'Irak'  => 'IQ',
        'Iran'  => 'IR',
        'Irland'  => 'IE',
        'Island'  => 'IS',
        'Israel'  => 'IL',
        'Italien'  => 'IT',
        'Jamaika'  => 'JM',
        'Japan'  => 'JP',
        'Jemen'  => 'YE',
        'Jordanien'  => 'JO',
        'Jugoslawien'  => 'YU',
        'Kaiman Inseln'  => 'KY',
        'Kambodscha'  => 'KH',
        'Kamerun'  => 'CM',
        'Kanada'  => 'CA',
        'Kap Verde'  => 'CV',
        'Kasachstan'  => 'KZ',
        'Kenia'  => 'KE',
        'Kirgisistan'  => 'KG',
        'Kiribati'  => 'KI',
        'Kokosinseln'  => 'CC',
        'Kolumbien'  => 'CO',
        'Komoren'  => 'KM',
        'Kongo'  => 'CG',
        'Kongo, Demokratische Republik'  => 'CD',
        'Kroatien'  => 'HR',
        'Kuba'  => 'CU',
        'Kuwait'  => 'KW',
        'Laos'  => 'LA',
        'Lesotho'  => 'LS',
        'Lettland'  => 'LV',
        'Libanon'  => 'LB',
        'Liberia'  => 'LR',
        'Libyen'  => 'LY',
        'Liechtenstein'  => 'LI',
        'Litauen'  => 'LT',
        'Luxemburg'  => 'LU',
        'Macao'  => 'MO',
        'Madagaskar'  => 'MG',
        'Malawi'  => 'MW',
        'Malaysia'  => 'MY',
        'Malediven'  => 'MV',
        'Mali'  => 'ML',
        'Malta'  => 'MT',
        'Marianen'  => 'MP',
        'Marokko'  => 'MA',
        'Marshall Inseln'  => 'MH',
        'Martinique'  => 'MQ',
        'Mauretanien'  => 'MR',
        'Mauritius'  => 'MU',
        'Mayotte'  => 'YT',
        'Mazedonien'  => 'MK',
        'Mexiko'  => 'MX',
        'Mikronesien'  => 'FM',
        'Mocambique'  => 'MZ',
        'Moldavien'  => 'MD',
        'Monaco'  => 'MC',
        'Mongolei'  => 'MN',
        'Montserrat'  => 'MS',
        'Namibia'  => 'NA',
        'Nauru'  => 'NR',
        'Nepal'  => 'NP',
        'Neukaledonien'  => 'NC',
        'Neuseeland'  => 'NZ',
        'Nicaragua'  => 'NI',
        'Niederlande'  => 'NL',
        'Niederl&auml;ndische Antillen'  => 'AN',
        'Niger'  => 'NE',
        'Nigeria'  => 'NG',
        'Niue'  => 'NU',
        'Nord Korea'  => 'KP',
        'Norfolk Inseln'  => 'NF',
        'Norwegen'  => 'NO',
        'Oman'  => 'OM',
        '&Ouml;sterreich'  => 'AT',
        'Pakistan'  => 'PK',
        'Pal&auml;stina'  => 'PS',
        'Palau'  => 'PW',
        'Panama'  => 'PA',
        'Papua Neuguinea'  => 'PG',
        'Paraguay'  => 'PY',
        'Peru'  => 'PE',
        'Philippinen'  => 'PH',
        'Pitcairn'  => 'PN',
        'Polen'  => 'PL',
        'Portugal'  => 'PT',
        'Puerto Rico'  => 'PR',
        'Qatar'  => 'QA',
        'Reunion'  => 'RE',
        'Ruanda'  => 'RW',
        'Rum&auml;nien'  => 'RO',
        'Ru&szlig;land'  => 'RU',
        'Saint Lucia'  => 'LC',
        'Sambia'  => 'ZM',
        'Samoa'  => 'AS',
        'Samoa'  => 'WS',
        'San Marino'  => 'SM',
        'Sao Tome'  => 'ST',
        'Saudi Arabien'  => 'SA',
        'Schweden'  => 'SE',
        'Schweiz'  => 'CH',
        'Senegal'  => 'SN',
        'Seychellen'  => 'SC',
        'Sierra Leone'  => 'SL',
        'Singapur'  => 'SG',
        'Slowakei -slowakische Republik-'  => 'SK',
        'Slowenien'  => 'SI',
        'Solomon Inseln'  => 'SB',
        'Somalia'  => 'SO',
        'South Georgia, South Sandwich Isl.'  => 'GS',
        'Spanien'  => 'ES',
        'Sri Lanka'  => 'LK',
        'St. Helena'  => 'SH',
        'St. Kitts Nevis Anguilla'  => 'KN',
        'St. Pierre und Miquelon'  => 'PM',
        'St. Vincent'  => 'VC',
        'S&uuml;d Korea'  => 'KR',
        'S&uuml;dafrika'  => 'ZA',
        'Sudan'  => 'SD',
        'Surinam'  => 'SR',
        'Svalbard und Jan Mayen Islands'  => 'SJ',
        'Swasiland'  => 'SZ',
        'Syrien'  => 'SY',
        'Tadschikistan'  => 'TJ',
        'Taiwan'  => 'TW',
        'Tansania'  => 'TZ',
        'Thailand'  => 'TH',
        'Timor'  => 'TP',
        'Togo'  => 'TG',
        'Tokelau'  => 'TK',
        'Tonga'  => 'TO',
        'Trinidad Tobago'  => 'TT',
        'Tschad'  => 'TD',
        'Tschechische Republik'  => 'CZ',
        'Tunesien'  => 'TN',
        'T&uuml;rkei'  => 'TR',
        'Turkmenistan'  => 'TM',
        'Turks und Kaikos Inseln'  => 'TC',
        'Tuvalu'  => 'TV',
        'Uganda'  => 'UG',
        'Ukraine'  => 'UA',
        'Ungarn'  => 'HU',
        'Uruguay'  => 'UY',
        'Usbekistan'  => 'UZ',
        'Vanuatu'  => 'VU',
        'Vatikan'  => 'VA',
        'Venezuela'  => 'VE',
        'Vereinigte Arabische Emirate'  => 'AE',
        'Vereinigte Staaten von Amerika'  => 'US',
        'Vietnam'  => 'VN',
        'Virgin Island (Brit.)'  => 'VG',
        'Virgin Island (USA)'  => 'VI',
        'Wallis et Futuna'  => 'WF',
        'Wei&szlig;ru&szlig;land'  => 'BY',
        'Westsahara'  => 'EH',
        'Zentralafrikanische Republik'  => 'CF',
        'Zimbabwe'  => 'ZW',
        'Zypern'  => 'CY'
          );

    $Values = array();
    while(list($Key,$Val) = each($laender))
      $Values[$Val] = $Key;

    return $Values;
  }


  function GetFirmaBCC1()
  {
    $email = $this->app->DB->Select("SELECT bcc1 FROM firmendaten WHERE firma='1' LIMIT 1");
    return $email;
  }

  function GetFirmaBCC2()
  {
    $email = $this->app->DB->Select("SELECT bcc2 FROM firmendaten WHERE firma='1' LIMIT 1");
    return $email;
  }

  function GetFirmaMail()
  {
    $email = $this->app->DB->Select("SELECT email FROM firmendaten WHERE firma='1' LIMIT 1");
    return $email;
  }

  function GetFirmaName()
  {
    $name = $this->app->DB->Select("SELECT name FROM firma WHERE id='1' LIMIT 1");
    return $name;
  }

  function GetSelectEmail($selected="")
  {
    $own = $this->app->User->GetEmail();
    $email_addr = $this->app->DB->SelectArr('SELECT email FROM firmendaten ORDER BY email');

    $emails = array();

    if($this->app->User->GetField("email_bevorzugen")=="1")
    {
      if($own!='')
        $emails[] = $own;
    }

    foreach($email_addr AS $mail)
      $emails[] = $mail['email'];


    if($this->app->User->GetField("email_bevorzugen")!="1")
    {
      if($own!='')
        $emails[] = $own;
    }

    for($i=0;$i<count($emails);$i++)
    {
      if($emails[$i]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$emails[$i]}\" $mark>{$emails[$i]}</option>";
    }
    return $tpl;
  }

  function GetSelectDokumentKunde($typ,$adresse,$select)
  {

    $typ_bezeichnung = ucfirst($typ);
    $result = $this->app->DB->SelectArr("SELECT CONCAT('$typ_bezeichnung ',if(status='angelegt','ENTWURF',belegnr),' (Status: ',status,') vom ',DATE_FORMAT(datum,'%d.%m.%Y')) as 
        result,id  FROM $typ WHERE adresse='$adresse' ORDER by datum DESC");
    for($i=0;$i<count($result);$i++)
    {
      $tmp .= "<option value=\"".$result[$i]['id']."\">".$result[$i]['result']."</option>";
    }

    return $tmp;

  }


  function GetSelectAuftragKunde($adresse,$select="")
  {
    return $this->GetSelectDokumentKunde("auftrag",$adresse,$select);
  }

  function GetSelectRechnungKunde($adresse,$select="")
  {

    return $this->GetSelectDokumentKunde("rechnung",$adresse,$select);
  }

  function GetSelectArbeitsnachweisKunde($adresse,$select="")
  {
    return $this->GetSelectDokumentKunde("arbeitsnachweis",$adresse,$select);
  }




  function GetSelectAnsprechpartner($adresse, $selected="")
  {
    $first = $this->app->DB->Select("SELECT CONCAT(ansprechpartner,' &lt;',email,'&gt;') FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $firstname = $this->app->DB->Select("SELECT ansprechpartner FROM adresse WHERE id='$adresse' LIMIT 1");
    if($firstname=="") $first = $this->app->DB->Select("SELECT CONCAT(name,' &lt;',email,'&gt;') FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    $others = $this->app->DB->SelectArr("SELECT id, CONCAT(name,' (',bereich,')',' &lt;',email,'&gt;') as name FROM ansprechpartner WHERE adresse='$adresse'");

    $tpl ="<option value=\"0\">$first</option>";

    for($i=0;$i<count($others);$i++)
    {
      if($others[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$others[$i][id]}\" $mark>{$others[$i][name]}</option>";
    }
    return $tpl;
  }

  function GetVorgaenger($projekt,$disableid="")
  {
    $user = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE projekt='".$projekt."' AND id!='$disableid' AND art!='material'");
    //    $user = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE projekt='".$projekt."'");
    $tpl[0]="keinen";
    for($i=0;$i<count($user);$i++)
    {
      //if($user[$i][id]==$selected) $mark="selected"; else $mark="";
      //$tpl .="<option value=\"{$user[$i][id]}\" $mark>{$user[$i][aufgabe]}</option>";
      $tpl[$user[$i][id]]=$user[$i][aufgabe];
    }
    return $tpl;
  }


  function GetReisekostenartAssoc()
  {
    $user = $this->app->DB->SelectArr("SELECT * FROM reisekostenart ORDER by nummer");
    for($i=0;$i<count($user);$i++)
    {
      //$tmp[(string)$user[$i][id]] = "{$user[$i][nummer]}- {$user[$i][beschreibung]}";
      $tmp[(string)$user[$i][id]] = "{$user[$i][nummer]}- {$user[$i][beschreibung]}";
    }
    return $tmp;
  }

  function GetSelectBezahltWie()
  {
    $tmp = $this->GetBezahltWieAssoc();

    foreach($tmp as $key=>$value)
      $result .= "<option value=\"$key\">$value</option>";
    return $result;
  }


  function GetSelectEtiketten($art,$selected="")
  {
    $user = $this->app->DB->SelectArr("SELECT * FROM etiketten WHERE verwendenals='$art' ORDER by name");
    for($i=0;$i<count($user);$i++)
    {
      if($user[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$user[$i][id]}\" $mark>{$user[$i][name]}</option>";
    }
    return $tpl;
  }


  function GetSelectReisekostenart($selected="")
  {
    $user = $this->app->DB->SelectArr("SELECT * FROM reisekostenart ORDER by nummer");
    for($i=0;$i<count($user);$i++)
    {
      if($user[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$user[$i][id]}\" $mark>{$user[$i][nummer]}- {$user[$i][beschreibung]}</option>";
    }
    return $tpl;
  }

  function GetSelectUserVorlage($selected="",$disableid="")
  {
    $user = $this->app->DB->SelectArr("SELECT * FROM uservorlage WHERE id!='$disableid' ORDER by bezeichnung");
    for($i=0;$i<count($user);$i++)
    {
      if($user[$i][id]==$selected) $mark=" selected"; else $mark="";
      $tpl .="<option value=\"{$user[$i][id]}\"$mark>{$user[$i][bezeichnung]}</option>";
    }
    return $tpl;
  }

  function GetSelectUser($selected="",$disableid="")
  {
    $user = $this->app->DB->SelectArr("SELECT * FROM user WHERE firma='".$this->app->User->GetFirma()."' AND id!='$disableid'");
    for($i=0;$i<count($user);$i++)
    {
      $user[$i][description] = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$user[$i][adresse]."' LIMIT 1");
      if($user[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$user[$i][id]}\" $mark>{$user[$i][description]}</option>";
    }
    return $tpl;
  }


  function GetSelectBICKonto($selected="")
  {

    $drucker = $this->app->DB->SelectArr("SELECT id,bezeichnung FROM konten WHERE firma='".$this->app->User->GetFirma()."' AND (type='konto' OR type='deutschebank' OR type='postbank') AND swift!=''");
    for($i=0;$i<count($drucker);$i++)
    {
      if($drucker[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$drucker[$i][id]}\" $mark>{$drucker[$i][bezeichnung]}</option>";
    }
    return $tpl;
  }


  function GetSelectKonto($selected="")
  {

    $drucker = $this->app->DB->SelectArr("SELECT id,bezeichnung FROM konten WHERE firma='".$this->app->User->GetFirma()."'");
    for($i=0;$i<count($drucker);$i++)
    {
      if($drucker[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$drucker[$i][id]}\" $mark>{$drucker[$i][bezeichnung]}</option>";
    }
    return $tpl;
  }

  function GetIPAdapterbox($id)
  {
    return $this->app->DB->Select("SELECT adapterboxip FROM drucker WHERE id='$id' LIMIT 1");
  }

  function GetSelectEtikettenDrucker($selected="")
  {
    //if($selected=="")
    //  $selected = $this->app->DB->Select("SELECT standardetikett FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");

    //$check = $this->app->DB->Select("SELECT id FROM drucker WHERE id='$selected' AND aktiv='1' LIMIT 1"); 
    //if($check!=$selected) $selected="";

    //if($selected=="")
    //  $selected = $this->Firmendaten("standardetikettendrucker");

    if($selected=="0" || $selected=="")
      $tpl .="<option value=\"0\" selected>-- kein --</option>";
    else
      $tpl .="<option value=\"0\">-- kein --</option>";

    $drucker = $this->app->DB->SelectArr("SELECT id, name FROM  drucker WHERE firma='".$this->app->User->GetFirma()."' AND aktiv='1' AND art='2'");
    for($i=0;$i<count($drucker);$i++)
    {
      if($drucker[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$drucker[$i][id]}\" $mark>{$drucker[$i][name]}</option>";
    }
    return $tpl;
  }

  function GetSelectFax($selected="")
  {
    if($selected=="")
      $selected = $this->app->DB->Select("SELECT standardfax FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");

    $check = $this->app->DB->Select("SELECT id FROM drucker WHERE id='$selected' AND aktiv='1' LIMIT 1"); 
    if($check!=$selected) $selected="";

    if($selected=="")
      $selected = $this->Firmendaten("standardfax");

    //$tpl .="<option value=\"0\">-- kein --</option>";
    $drucker = $this->app->DB->SelectArr("SELECT id, name FROM  drucker WHERE firma='".$this->app->User->GetFirma()."' AND aktiv='1' AND art='1'");
    for($i=0;$i<count($drucker);$i++)
    {
      if($drucker[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$drucker[$i][id]}\" $mark>{$drucker[$i][name]}</option>";
    }
    return $tpl;
  }

  function GetDrucker()
  {
    //$tpl .="<option value=\"0\">-- kein --</option>";
    $drucker = $this->app->DB->SelectArr("SELECT id, name FROM  drucker WHERE firma='".$this->app->User->GetFirma()."' AND aktiv='1' AND art='0'");
    for($i=0;$i<count($drucker);$i++)
    {
      //if($drucker[$i][id]==$selected) $mark="selected"; else $mark="";
      //$tpl .="<option value=\"{$drucker[$i][id]}\" $mark>{$drucker[$i][name]}</option>";
      $result[$drucker[$i][id]]=$drucker[$i][name];
    }
    return $result;
  }

  function GetSelectDrucker($selected="")
  {
    if($selected=="")
      $selected = $this->app->DB->Select("SELECT standarddrucker FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");

    $check = $this->app->DB->Select("SELECT id FROM  drucker WHERE id='$selected' AND aktiv='1' LIMIT 1"); 
    if($check!=$selected) $selected="";

    if($selected=="")
      $selected = $this->Firmendaten("standardversanddrucker");

    //$tpl .="<option value=\"0\">-- kein --</option>";
    $drucker = $this->app->DB->SelectArr("SELECT id, name FROM  drucker WHERE firma='".$this->app->User->GetFirma()."' AND aktiv='1' AND art='0'");
    for($i=0;$i<count($drucker);$i++)
    {
      if($drucker[$i][id]==$selected) $mark="selected"; else $mark="";
      $tpl .="<option value=\"{$drucker[$i][id]}\" $mark>{$drucker[$i][name]}</option>";
    }
    return $tpl;
  }

  function Grusswort($sprache)
  {

    //abhaenig von Zeit usw.. passende Grußformel
    /*
       return "Grüße aus dem sonnigen Ausgburg.";
       return "Grüße aus Ausgburg.";
       return "Frohe Ostern.";
       return "Schöne Feierabend.";
       return "Frohe Weihnachten.";
       return "Schönes Wochenende.";
     */
    if($sprache=="englisch") return "\nKind regards,";
    return "\nMit freundlichen Grüßen";

  }

  function DokumentSendVorlage($id)
  {
    $betreff = $this->app->DB->Select("SELECT betreff FROM dokumente_send WHERE id='$id' LIMIT 1");
    $text  = $this->app->DB->Select("SELECT text FROM dokumente_send WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set(BETREFF,$betreff);
    $this->app->Tpl->Set(TEXT,$text);
  }

  function Geschaeftsbriefvorlage($sprache,$subjekt,$projekt="",$name="",$id="")
  {
    $lowersubjekt = strtolower($subjekt);   
    if($lowersubjekt=="angebot" || $lowersubjekt=="auftrag" ||$lowersubjekt=="bestellung" ||$lowersubjekt=="lieferschein" ||$lowersubjekt=="rechnung" ||$lowersubjekt=="gutschrift" || $lowersubjekt=="arbeitsnachweis"){
      if($id > 0)
        $belegnr = $this->app->DB->Select("SELECT belegnr FROM $lowersubjekt WHERE id='$id' LIMIT 1");
    }
    /*
       if($projekt > 0)
       {
       $betreff = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE sprache = '$sprache' AND subjekt='$subjekt' AND projekt='$projekt' LIMIT 1");
       $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE sprache = '$sprache' AND subjekt='$subjekt' AND projekt='$projekt' LIMIT 1");
       } else {
       $betreff = $this->app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE sprache = '$sprache' AND subjekt='$subjekt' AND (projekt='0' OR projekt='') LIMIT 1");
       $text = $this->app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE sprache = '$sprache' AND subjekt='$subjekt' AND (projekt='0' OR projekt='') LIMIT 1");
       }
     */      

    $text = $this->GetGeschaeftsBriefText($subjekt,$sprache,$projekt);
    $betreff = $this->GetGeschaeftsBriefBetreff($subjekt,$sprache,$projekt);

    $text = str_replace('{NAME}',$name,$text);
    $text = str_replace('{BELEGNR}',$belegnr,$text);

    $betreff = str_replace('{NAME}',$name,$betreff);
    $betreff = str_replace('{BELEGNR}',$belegnr,$betreff);

    $this->app->Tpl->Set(BETREFF,$betreff);
    $this->app->Tpl->Set(TEXT,$text);
    return array("betreff"=>$betreff,"text"=>$text);
  }

  function GetAnsprechpartner($data)
  {
    // $data: 'Admin <admin@test.de>'
    // return id, name, email

    $first = strpos($data, '<');
    $last = strpos($data, '>');

    $name = trim(substr($data, 0, $first));
    $email = trim(substr($data, $first+1, $last-($first+1)));

    $id = $this->app->DB->Select("SELECT id FROM adresse WHERE email='$mail' LIMIT 1"); 
    if(!(is_numeric($id) && $id<1))
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE name='$name' LIMIT 1");

    if(!is_numeric($id)) $id = 0;

    return array('id'=>$id, 'name'=>$name, 'email'=>$email);
  }

  function CommonReadonly()
  {
    $this->commonreadonly=1;
    //$this->app->Tpl->Set(COMMONREADONLYINPUT,"readonly disabled style=\"background-color:rgb(255, 230, 213);\"");
    //$this->app->Tpl->Set(COMMONREADONLYSELECT,"disabled=\"disabled\" style=\"background-color:rgb(255, 230, 213);\"");
    $this->app->Tpl->Set(COMMONREADONLYINPUT,"readonly disabled style=\"background-color:#eee; border-color:#ddd;\"");
    $this->app->Tpl->Set(COMMONREADONLYSELECT,"disabled=\"disabled\" style=\"background-color:#eee;\"");

  }

  function DokumentMask($parsetarget,$typ,$id,$adresse,$projekt="",$popup=false)
  {

    $this->app->Tpl->Set(SID,$id);
    $this->app->Tpl->Set(TYP,$typ);


    //echo "typ $typ<br>id $id adresse $adresse<br><br>";

    $betreff = $this->app->Secure->GetPOST("betreff");
    $projekt_submit = $this->app->Secure->GetPOST("projekt");
    $ansprechpartner = $this->app->Secure->POST["ansprechpartner"];
    $text = $this->app->Secure->GetPOST("text");
    $art = $this->app->Secure->GetPOST("senden");


    list($name, $email) = explode('<', trim($ansprechpartner,'>'));

    $partnerinfo['email'] = $email;
    $partnerinfo['name'] = $name;

    //$partnerinfo = $this->GetAnsprechpartner($ansprechpartner);
    //$ansprechpartner = $partnerinfo['id'];

    if($projekt=="" && $projekt_submit!="")
      $projekt = $projekt_submit;

    // hole standard projekt von adresse
    if($projekt=="")
      $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");


    if($typ!="brieffax")
      $projektbriefpapier = $this->app->DB->Select("SELECT projekt FROM $typ WHERE id='$id' LIMIT 1");

    // anschreiben
    //if($typ=="bestellung" || $typ=="lieferschein" || $typ=="rechnung" || $typ=="angebot" || ($typ=="brieffax" && $art!="email") )
    if($typ=="bestellung" || $typ=="angebot" || $typ=="arbeitsnachweis" ||$typ=="reisekosten" || $typ=="lieferschein" || $typ=="auftrag" || ($typ=="brieffax" && $art!="email") )
    {
      /*
      // update status freigegebn auf versendet
      $Brief = new BriefPDF(&$this->app);

      if($art == "fax")
      $Brief->GetBriefTMP($adresse,$betreff,$text,1);
      else
      $Brief->GetBriefTMP($adresse,$betreff,$text);

      if($art !="email")
      $tmpbrief= $Brief->displayTMP();
       */
    }

    // eigentliches dokument
    if($typ=="bestellung")
    {
      // sende 
      $Brief = new BestellungPDF($this->app,$projektbriefpapier);
      $Brief->GetBestellung($id);
      $tmpfile = $Brief->displayTMP();
    }
    // eigentliches dokument
    if($typ=="angebot")
    {
      // sende 
      $Brief = new AngebotPDF($this->app,$projektbriefpapier);
      $Brief->GetAngebot($id);
      $tmpfile = $Brief->displayTMP();
    }
    // eigentliches dokument
    if($typ=="lieferschein")
    {
      // sende 
      $Brief = new LieferscheinPDF($this->app,$projektbriefpapier);
      $Brief->GetLieferschein($id);
      $tmpfile = $Brief->displayTMP();
    }
    // eigentliches dokument
    if($typ=="arbeitsnachweis")
    {
      // sende 
      $Brief = new ArbeitsnachweisPDF($this->app,$projektbriefpapier);
      $Brief->GetArbeitsnachweis($id);
      $tmpfile = $Brief->displayTMP();
    }
    // eigentliches dokument
    if($typ=="reisekosten")
    {
      // sende 
      $Brief = new ReisekostenPDF($this->app,$projektbriefpapier);
      $Brief->GetReisekosten($id);
      $tmpfile = $Brief->displayTMP();
    }


    // eigentliches dokument
    if($typ=="auftrag")
    {
      // sende 
      $Brief = new AuftragPDF($this->app,$projektbriefpapier);
      $Brief->GetAuftrag($id);
      $tmpfile = $Brief->displayTMP();
    }
    // eigentliches dokument
    if($typ=="rechnung")
    {
      // sende 
      $Brief = new RechnungPDF($this->app,$projektbriefpapier);
      $Brief->GetRechnung($id);
      $tmpfile = $Brief->displayTMP();
    }

    // eigentliches dokument
    if($typ=="gutschrift")
    {
      // sende 
      $Brief = new GutschriftPDF($this->app,$projektbriefpapier);
      $Brief->GetGutschrift($id);
      $tmpfile = $Brief->displayTMP();
    }


    if($art == "email")
      $dateien = array($tmpfile);
    else
    {
      if($typ=="brieffax")
        $dateien = array($tmpbrief);
      else
        $dateien = array($tmpbrief,$tmpfile);
    }
    if($art == "brief") $drucker = $this->app->Secure->GetPOST("drucker_brief");
    else if($art == "fax") $drucker = $this->app->Secure->GetPOST("drucker_fax");
    else if ($art == "email") $drucker = $this->app->Secure->GetPOST("email_from");

    if($this->app->Secure->GetPOST("submit")!="")
    {

      //echo "SENDEN";
      if($art=="fax")
        $ret = $this->DokumentSend($adresse,$typ,$id,$art,$betreff,$text,$dateien,$drucker,$ansprechpartner,$projekt, $this->app->Secure->GetPOST("faxnummer"),"");
      else
        $ret = $this->DokumentSend($adresse,$typ,$id,$art,$betreff,$text,$dateien,$drucker,$ansprechpartner,$projekt, $partnerinfo['email'], $partnerinfo['name']);
      /*
      // NEU ANLEGEN ODER UPDATE
      if($typ=="brieffax")
      {
      $check = $this->app->DB->Select("SELECT id FROM dokumente_send WHERE text='$text' AND betreff='$betreff' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1");
      } else {
      $check = $this->app->DB->Select("SELECT id FROM dokumente_send WHERE dokument='$typ' AND parameter='$id' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1"); // GEHT bei BE RE LS
      }
       */

      //Datei anlegen
      $fileid = $this->CreateDatei($Brief->filename,$module,"","",$tmpfile,$this->app->User->GetName());

      $this->AddDateiStichwort($fileid,$typ,$typ,$id,$without_log=false);


      if(is_numeric($check) && $check >0)
      {
        /*
           if($typ=="brieffax")
           {        
        // das dokument gibt es so bereits 1:1 hier braucht man nichts machen
        //echo "DAS DOKUMENT GIBT ES UNVERSENDET SO";
        $this->app->DB->Update("UPDATE dokumente_send SET versendet=1 WHERE id='$check' LIMIT 1");
        }
        else
        {
        $this->app->DB->Update("UPDATE dokumente_send SET betreff='$betreff', text='$text',versendet=1 WHERE dokument='$typ' AND parameter='$id' AND geloescht=0 AND versendet=0 LIMIT 1");  // GEHT bei RE, LS ..
        }
         */
      } else {
        if($typ=="brieffax")
        {
          $this->app->DB->Insert("INSERT INTO dokumente_send 
              (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet,dateiid) VALUES ('','$typ',NOW(),'".$this->app->User->GetName()."',
                '$adresse','$parameter','$art','$betreff','$text','$projekt','$ansprechpartner',1,'$fileid')");
          $tmpid = $this->app->DB->GetInsertID();
          $this->app->DB->Update("UPDATE dokumente_send SET parameter='$tmpid' WHERE id='$tmpid' LIMIT 1");
          //echo "INSERT brieffax dokument";
        } else {
          //echo "anlegen begleitschreiben RE, LS";
          //TODO ANSPRECHPARTNER
          $this->app->DB->Insert("INSERT INTO dokumente_send 
              (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet,dateiid) VALUES ('','$typ',NOW(),'".$this->app->User->GetName()."',
                '$adresse','$id','$art','$betreff','$text','$projekt','$ansprechpartner',1,'$fileid')");
          $tmpid = $this->app->DB->GetInsertID();
        }

      }

      //if($typ=="brieffax")
      //  $id = $this->app->DB->GetInsertID();

      if($ret == "")
      {
        $this->app->Tpl->Set($parsetarget,"<div class=\"info\">Dokument wurde erfolgreich versendet</div>");

        /* Status gezielt von Dokument aendern */
        if($typ=="bestellung")
        {
          $this->app->DB->Update("UPDATE bestellung SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',status='versendet',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->BestellungProtokoll($id,"Bestellung versendet");
          //TODO ARCHIVIEREN
        } 
        else if($typ=="angebot")
        {
          $this->app->DB->Update("UPDATE angebot SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',status='versendet',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->AngebotProtokoll($id,"Angebot versendet");
          //TODO ARCHIVIEREN
        } 
        else if($typ=="lieferschein")
        {
          $this->app->DB->Update("UPDATE lieferschein SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',status='versendet',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->LieferscheinProtokoll($id,"Lieferschein versendet");
          //TODO ARCHIVIEREN
        } 
        else if($typ=="arbeitsnachweis")
        {
          $this->app->DB->Update("UPDATE arbeitsnachweis SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',status='versendet',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->ArbeitsnachweisProtokoll($id,"Arbeitsnachweis versendet");
          //TODO ARCHIVIEREN
        } 
        else if($typ=="reisekosten")
        {
          $this->app->DB->Update("UPDATE reisekosten SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',status='versendet',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->ReisekostenProtokoll($id,"Reisekosten versendet");
          //TODO ARCHIVIEREN
        } 


        else if($typ=="auftrag")
        {
          $this->app->DB->Update("UPDATE auftrag SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->AuftragProtokoll($id,"Auftrag versendet");
          //TODO ARCHIVIEREN
        } 
        else if ($typ=="rechnung")
        {
          $this->app->DB->Update("UPDATE rechnung SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',status='versendet',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->RechnungProtokoll($id,"Rechnung versendet");
          //TODO ARCHIVIEREN
        }
        else if ($typ=="gutschrift")
        {
          $this->app->DB->Update("UPDATE gutschrift SET versendet=1, versendet_am=NOW(),
              versendet_per='$art',versendet_durch='".$this->app->User->GetName()."',status='versendet',schreibschutz='1' WHERE id='$id' LIMIT 1");
          $this->GutschriftProtokoll($id,"Gutschrift versendet");
          //TODO ARCHIVIEREN
        }

      }
      else
        $this->app->Tpl->Set($parsetarget,"<div class=\"error\">$ret</div>");
    } elseif ($this->app->Secure->GetPOST("speichern")!="") {
      //echo "SPEICHERN";
      // Nur speichern
      $action =  $this->app->Secure->GetGET("action");
      $module =  $this->app->Secure->GetGET("module");

      if($module=="adresse")
      {
        $check = $this->app->DB->Select("SELECT id FROM dokumente_send WHERE dokument='brieffax' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1");
      } else {
        $check = $this->app->DB->Select("SELECT id FROM dokumente_send WHERE dokument='$typ' AND parameter='$id' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1"); // GEHT bei BE RE LS
      } 

      if($module=="adresse")
      {
        $typ="brieffax";
        if(is_numeric($check))
        {
          $this->app->DB->Insert("UPDATE  dokumente_send  SET betreff='$betreff',text='$text',bearbeiter='".$this->app->User->GetName()."' WHERE id='$check' LIMIT 1");
          $this->app->Tpl->Set(MESSAGE,"<div class=\"info\">Die &Auml;nderungen wurden gespeichert.</div>");
        } else {
          $this->app->DB->Insert("INSERT INTO dokumente_send 
              (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet) VALUES ('','$typ',NOW(),'".$this->app->User->GetName()."',
                '$adresse','$parameter','$art','$betreff','$text','$projekt','$ansprechpartner',0)");
          $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Es wurde ein neues Dokument wurde angelegt, da das alte Dokument bereits versendet worden ist.</div>");
        }
      } else {

        if(is_numeric($check))
        {
          $this->app->DB->Update("UPDATE  dokumente_send  SET betreff='$betreff',text='$text',bearbeiter='".$this->app->User->GetName()."' WHERE id='$check' LIMIT 1");
          $this->app->Tpl->Set(MESSAGE,"<div class=\"info\">Die &Auml;nderungen wurden gespeichert.</div>");
        } else {
          $parameter = $this->app->Secure->GetGET("id");
          $this->app->DB->Insert("INSERT INTO dokumente_send 
              (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet) VALUES ('','$typ',NOW(),'".$this->app->User->GetName()."',
                '$adresse','$parameter','$art','$betreff','$text','$projekt','$ansprechpartner',0)");
          $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Es wurde ein neues Dokument wurde angelegt, da das alte Dokument bereits versendet worden ist.</div>");
        }
      }

    }elseif($this->app->Secure->GetPOST("download")!="") {
      header("Location: index.php?module=adresse&action=briefpdf&sid=$id&id=$adresse");
      exit;
    }

    $tmp_fax = $this->app->DB->Select("SELECT telefax FROM $typ WHERE id='$id' LIMIT 1");
    $tmp_fax = str_replace('+','00',$tmp_fax);
    $n = preg_match_all("/[0-9]/", $tmp_fax, $treffer);
    for($i=0;$i<$n;$i++){
      $nummer = $nummer . $treffer[0][$i];
      if($n%2 == 1 && $i%2 == 0 && $i < $n-1){
        $nummer = $nummer . "";
      }elseif($n%2 == 0 && $i%2 == 1 && $i < $n-1){
        $nummer = $nummer . "";
      }
    }

    $this->app->Tpl->Set(FAXNUMMER,$nummer);


    $this->app->Tpl->Set(DRUCKER,$this->GetSelectDrucker());
    $this->app->Tpl->Set(FAX,$this->GetSelectFax());
    $this->app->Tpl->Set(EMAILEMPFAENGER,$this->GetSelectEmail());
    $projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt'");
    $this->app->Tpl->Set(PROJEKT,$projektabkuerzung);
    //$this->app->Tpl->Set(PROJEKT,$this->GetProjektSelect($projekt));
    //        $this->app->Tpl->Set(ANSPRECHPARTNER,$this->GetSelectAnsprechpartner($adresse,$projekt));
    $tmp_mail = $this->app->DB->Select("SELECT email FROM $typ WHERE id='$id' LIMIT 1");
    $tmp_name = $this->app->DB->Select("SELECT ansprechpartner FROM $typ WHERE id='$id' LIMIT 1");
    if($tmp_name=="")
      $tmp_name = $this->app->DB->Select("SELECT name FROM $typ WHERE id='$id' LIMIT 1");

    //$this->app->Tpl->Set(ANSPRECHPARTNER,$tmp_name." <".$tmp_mail.">");
    $this->app->Tpl->Set(ANSPRECHPARTNER,$this->GetAdresseMail($adresse,$id,$typ));
    //$this->app->YUI->AutoComplete('ansprechpartner', 'emailname');        
    $this->app->YUI->AutoComplete('projekt', 'projektname',1);      

    $projekt = $this->app->DB->Select("SELECT projekt FROM $typ WHERE id='$id' LIMIT 1");
    $this->DokumentSendShow($parsetarget,$typ,$id,$adresse,$tmpfile,$popup,$projekt);

    // temp datei wieder loeschen
    unlink($tmpfile);
    unlink($tmpbrief);
  }

  function GetAdresseMail($adresse,$id="",$tabelle="") {
    // hole adresse aus feld ansprechpartner

    $tmp_mail = $this->app->DB->Select("SELECT email FROM $tabelle WHERE id='$id' LIMIT 1");
    $tmp_name = $this->app->DB->Select("SELECT ansprechpartner FROM $tabelle WHERE id='$id' LIMIT 1");        

    if($tmp_name=="")
      $tmp_name = $this->app->DB->Select("SELECT name FROM $tabelle WHERE id='$id' LIMIT 1");

    $data[0]['name']=$tmp_name;
    $data[0]['email']=$tmp_mail;

    // doppelte eintraege loeschen  

    if($data[0]['email']==$tmp_mail)
    {
      $result = "<option value=\"{$data[0]['name']} <{$data[0]['email']}>\" selected>{$data[0]['name']} &lt;{$data[0]['email']}&gt;</option>";
    }       
    else
      $result = "<option value=\"{$data[0]['name']} <{$data[0]['email']}>\">{$data[0]['name']} &lt;{$data[0]['email']}&gt;</option>";

    //              $data = $this->app->DB->SelectArr("SELECT name, email FROM adresse WHERE id='$adresse' LIMIT 1");


    $data = $this->app->DB->SelectArr("SELECT name, email FROM ansprechpartner WHERE adresse='$adresse' ORDER by name");

    for($i=0;$i<count($data);$i++)
    {
      if($data[$i]['email']==$selected_mail)
        $result .= "<option value=\"{$data[$i]['name']} <{$data[$i]['email']}\" selected>{$data[$i]['name']} &lt;{$data[$i]['email']}&gt;</option>";
      else
        $result .= "<option value=\"{$data[$i]['name']} <{$data[$i]['email']}>\">{$data[$i]['name']} &lt;{$data[$i]['email']}&gt;</option>";
    }

    return $result;
  }


  function DokumentSendShow($parsetarget,$dokument,$id,$adresse,$attachments="",$popup=false,$projekt="")
  {
    $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $name2 = $this->app->DB->Select("SELECT name FROM $dokument WHERE id='$id' LIMIT 1");

    $testdata = $this->app->DB->SelectArr("SELECT betreff WHERE dokument='".$dokument."' AND parameter='$id' AND parameter!=0 ORDER BY zeit DESC LIMIT 1");

    if($sprache=="") $sprache="deutsch";

    switch($dokument)
    {
      case "bestellung":
        if($tmp_data[0][betreff]!="")
          $this->DokumentSendVorlage($id);
        else
        {
          $this->Geschaeftsbriefvorlage($sprache,"Bestellung",$projekt,$name2,$id); 
          $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
          $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        }
        break;
      case "angebot":
        $this->Geschaeftsbriefvorlage($sprache,"Angebot",$projekt,$name2,$id); 
        $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
        $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        break;
      case "lieferschein":
        $this->Geschaeftsbriefvorlage($sprache,"Lieferschein",$projekt,$name2,$id); 
        $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
        $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        break;
      case "rechnung":
        $this->Geschaeftsbriefvorlage($sprache,"Rechnung",$projekt,$name2,$id); 
        $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
        $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        break;
      case "gutschrift":
        $this->Geschaeftsbriefvorlage($sprache,"Gutschrift",$projekt,$name2,$id); 
        $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
        $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        break;

      case "auftrag":
        $this->Geschaeftsbriefvorlage($sprache,"Auftrag",$projekt,$name2,$id); 
        $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
        $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        break;
      case "arbeitsnachweis":
        $this->Geschaeftsbriefvorlage($sprache,"Auftrag",$projekt,$name2,$id); 
        $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
        $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        break;

      case "brieffax":
        if($testdata!="")
          $this->DokumentSendVorlage($id);
        else
        {
          $this->Geschaeftsbriefvorlage($sprache,"Korrespondenz",$projekt,$name2); 
          $this->app->Tpl->Add(TEXT,"\n\n".$this->Grusswort($sprache));
          $this->app->Tpl->Add(TEXT,"\n\n".$this->app->User->GetName());
        }
        break;

      default: ;
    }

    $module = $this->app->Secure->GetGET("module");
    $id = $this->app->Secure->GetGET("id");

    if($module=="adresse")
    {
      //echo "Fall 1";
      // genau das eine dokument
      $tmp = $this->app->DB->SelectArr("SELECT DATE_FORMAT(zeit,'%d.%m.%Y %H:%i') as datum, dateiid, text, betreff, ansprechpartner, id, adresse, bearbeiter,art, dokument, parameter, versendet FROM dokumente_send WHERE dokument='".$dokument."' 
          AND id='$id' AND parameter!=0  AND versendet=1 ORDER by zeit DESC");
      //echo ("SELECT DATE_FORMAT(zeit,'%d.%m.%Y %H:%i') as zeit, text, betreff, id, adresse, bearbeiter,art, dokument, parameter, versendet FROM dokumente_send WHERE dokument='".$dokument."' 
      //       AND id='$id' parameter!=0  AND versendet=1 ORDER by zeit");

    }
    else
    {
      // alle passenden dokumente
      $tmp = $this->app->DB->SelectArr("SELECT DATE_FORMAT(zeit,'%d.%m.%Y %H:%i') as datum, text, dateiid, ansprechpartner, betreff, id, adresse, versendet, parameter, dokument, bearbeiter,art FROM dokumente_send WHERE dokument='".$dokument."' AND parameter='$id'  AND parameter!=0 ORDER by zeit DESC");
      //echo "Fall 2";

    } 

    if(count($tmp)>0)
    {
      $this->app->Tpl->Set(HISTORIE,"<table align=\"left\" width=780>");
      $this->app->Tpl->Add(HISTORIE,"<tr valign=\"top\"><td style=\"font-size: 8pt\"><b>Zeit</b></td><td style=\"font-size: 8pt\"><b>An</b></td><td style=\"font-size: 8pt\"><b>Von</b></td>
          <td style=\"font-size: 8pt\"><b>Art</b></td>
          <td style=\"font-size: 8pt\"><b>Anschreiben</b></td><td style=\"font-size: 8pt\"><b>Dokument</b></td></tr>");
      for($i=0;$i<count($tmp);$i++)
      {

        if($tmp[$i]['versendet']==0) $tmp[$i]['versendet'] = "nein"; else $tmp[$i]['versendet'] = "ja";
        //$tmp_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$tmp[$i]['adresse']}' AND geloescht=0 LIMIT 1");
        if(is_numeric($tmp[$i]['ansprechpartner']))
          $tmp_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$tmp[$i]['ansprechpartner']."'");
        else
          $tmp_name = htmlentities($tmp[$i]['ansprechpartner'],ENT_QUOTES, "UTF-8");

        //$tmp_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$tmp[$i]['adresse']}' AND geloescht=0 LIMIT 1");

        if($tmp[$i]['dateiid'] > 0) $tmppdf = '<a href="index.php?module=dateien&action=send&id='.$tmp[$i]['dateiid'].'"><img src="./themes/[THEME]/images/pdf.png" border="0"></a>';
        else $tmppdf="";

        $this->app->Tpl->Add(HISTORIE,'<tr valign="top"><td style="font-size: 8pt">'.$tmp[$i]['datum'].'</td>
            <td style="font-size: 8pt">'.$tmp_name.'</td><td style="font-size: 8pt">'.$tmp[$i]['bearbeiter'].'</td>
            <td style="font-size: 8pt">'.ucfirst($tmp[$i]['art']).'</td>
            <td style="font-size: 8pt" align="center"><a href="index.php?module=adresse&action=briefpdf&type='.$module.'&typeid='.$id.'&sid='.$tmp[$i]['id'].'"><img src="./themes/[THEME]/images/pdf.png" border="0"></a></td>
            <td style="font-size: 8pt" align="center">'.$tmppdf.'</td>
            </tr>');
      }
      $this->app->Tpl->Add(HISTORIE,"</table>");

    } else { $this->app->Tpl->Set(HISTORIE,"<div class=\"info\">Dieses Dokument wurde noch nicht versendet!</div>"); }


    for($i=0;$i<count($attachments);$i++)
    {
      $this->app->Tpl->Add(ANHAENGE,"<a href=\"\">".basename($attachments)."</a>&nbsp;");
    }
    if(count($attachments)==0) $this->app->Tpl->Add(ANHAENGE,"keine Ah&auml;nge vorhanden");

    if(count($tmp)>0)
    {
      $tmp[0][betreff] = str_replace('{FIRMA}',$this->Firmendaten("name"),$tmp[0][betreff]);
      $tmp[0][text] = str_replace('{FIRMA}',$this->Firmendaten("name"),$tmp[0][text]);
      $this->app->Tpl->Set(BETREFF,$tmp[0][betreff]);
      $this->app->Tpl->Set(TEXT,$tmp[0][text]);
    }


    $tmp = new EasyTable($this->app);
    $tmp->Query("SELECT zeit,bearbeiter,grund FROM ".$dokument."_protokoll WHERE $dokument='$id' ORDER by zeit DESC");
    $tmp->DisplayNew(PROTOKOLL,"Protokoll","noAction");



    $this->app->Tpl->Set(EMPFAENGER,$name);
    $pTemplate = (($popup==true) ? 'dokument_absenden_popup.tpl' : 'dokument_absenden.tpl');
    $this->app->Tpl->Parse($parsetarget, $pTemplate);
  }

  //art=email,betreff,text,dateien, email_to, email_name_to
  function DokumentSend($adresse,$dokument, $parameter, $art,$betreff,$text,$dateien,$drucker="",$ansprechpartner="",$projekt="",$email_to="", $email_name_to="")
  {

    // $ret muss geleert werden wenn Dokument erfolgreich versendet wurde!!
    $ret = "Versandart $art noch nicht implementiert!";

    switch($art)
    {
      case "email": // signatur + dokument als anhang
        $ret = "";
        if($email_to!='') {
          $to = $email_to;
          $to_name = $email_name_to;
        }else{
          if($ansprechpartner!=0)
          {
            $to = $this->app->DB->Select("SELECT email FROM ansprechpartner WHERE id='$ansprechpartner' LIMIT 1");
            $to_name = $this->app->DB->Select("SELECT name FROM ansprechpartner WHERE id='$ansprechpartner' LIMIT 1");
          } else 
          {
            $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
            $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
          }
        }
        // wenn emailadresse from email from user name von benutzer sonst firmenname
        if($drucker==$this->app->User->GetEmail())
          $from_name = $this->app->User->GetName();
        else
          $from_name = $this->app->User->GetFirmaName();

        if($from_name=="")
          $from_name=$this->GetFirmaName();

        if($drucker=="")
          $drucker=$this->GetFirmaMail(); 


        if($dokument=="auftrag")
        {
          $abweichendeemailab = $this->app->DB->Select("SELECT abweichendeemailab FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
          if($abweichendeemailab!="") $to = $abweichendeemailab;
        }

        if($this->MailSend($drucker,$from_name,$to,$to_name,$betreff,$text,$dateien,$projekt))
          $ret = "";
        else
          $ret = "Die E-Mail konnte nicht versendet werden! (".$this->mail_error.")";

        break;

      case "brief":
        foreach($dateien as $key=>$value)
          $this->app->printer->Drucken($drucker,$value);
        $ret = "";  
        break;

      case "fax":
        foreach($dateien as $key=>$value)
          $this->app->printer->Drucken($drucker,$value,$email_to);
        $ret = "";  
        break;

      case "telefon":
        $ret = "";
        break;
      case "sonstiges":
        $ret = "";
        break;

    }
    /*
       $module = $this->app->Secure->GetGET("module");
    // UPDATE auf versendet
    if($ret =="")
    {
    echo "insert 3";
    if($module=="adresse") {
    $dokument="brieffax";
    echo "if 1";
    $check = $this->app->DB->Select("SELECT id FROM dokumente_send WHERE id='$parameter' AND dokument='$dokument' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1");
    } else 
    {
    echo "if 2";
    // nur wenn es das dokument noch nicht gibt mit versendet=0
    $check = $this->app->DB->Select("SELECT id FROM dokumente_send WHERE parameter='$parameter' AND dokument='$dokument' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1");
    }
    if($check<=0 || $check=="")
    {
    echo "insert 3 new";

    $this->app->DB->Insert("INSERT INTO dokumente_send 
    (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,versendet) VALUES ('','$dokument',NOW(),'".$this->app->User->GetName()."',
    '$adresse','$parameter','$art','$betreff','$text','$projekt','$ansprechpartner',1)");

    $tmpid = $this->app->DB->GetInsertID();
    if($parameter==0 || $parameter=="")
    $this->app->DB->Update("UPDATE dokumente_send SET parameter='$tmpid' WHERE id='$tmpid' LIMIT 1");

    } else {
    echo "insert 3 update";
    if($module=="adresse")
    {
    $this->app->DB->Update("UPDATE dokumente_send SET versendet=1 WHERE id='$parameter' AND dokument='$dokument' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1");
    }
    else
    {
    $this->app->DB->Update("UPDATE dokumente_send SET versendet=1 WHERE parameter='$parameter' AND dokument='$dokument' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1");
    }

    //echo ("UPDATE dokumente_send SET versendet=1 WHERE parameter='$parameter' AND dokument='$dokument' AND geloescht=0 AND versendet=0 ORDER by id DESC LIMIT 1");
    }
    }
     */
    return $ret;
  }


  function NewEvent($beschreibung, $kategorie, $obejekt="",$parameter="")
  {

    $bearbeiter = $this->app->User->GetName();

    $this->app->DB->Insert("INSERT INTO event (id,beschreibung,kategorie,zeit,objekt,parameter,bearbeiter)
        VALUES('','$beschreibung','$kategorie',NOW(),'$objekt','$parameter','$bearbeiter')");

  }

  function UpdateChecksumShopartikel($projekt)
  {
    $tmp = $this->app->DB->SelectArr("SELECT id FROM artikel WHERE shop > 0");
    for($i=0;$i<count($tmp);$i++)
      $this->UpdateArtikelChecksum($tmp[$i][id],$projekt);
  }

  function UpdateArtikelChecksum($artikel,$projekt)
  {
    $tmp = $this->app->DB->SelectArr("SELECT typ,
        nummer, projekt, inaktiv, warengruppe, name_de, name_en, kurztext_de, ausverkauft,
        kurztext_en , beschreibung_de, beschreibung_en,standardbild, herstellerlink, hersteller, uebersicht_de,uebersicht_en,links_de,links_en, startseite_de, startseite_en,
        lieferzeit , lieferzeitmanuell, wichtig,  gewicht, gesperrt,    sperrgrund,  gueltigbis,umsatzsteuer,  klasse,  adresse, shop, firma, neu,topseller,startseite,
        (SELECT MAX(preis) FROM verkaufspreise WHERE 
         artikel='$artikel' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND ab_menge = 1 AND (adresse='0' OR adresse='')) as preis
        FROM artikel WHERE id='$artikel' LIMIT 1");

    //        artikel='$artikel' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND ab_menge = 1 AND (objekt='Standard' OR objekt='')) as preis
    serialize($tmp);

    $checksum = md5(serialize($tmp));

    $this->app->DB->Update("UPDATE artikel SET checksum='$checksum' WHERE id='$artikel' LIMIT 1");
  }

  function GetStandardMarge()
  {
    return $this->Firmendaten("standardmarge");
  }


  function GetStandardStundensatz()
  {
    return 57.62;
  }

  function GetProjektSelectMitarbeiter($adresse)
  {
    // Adresse ist Mitglied von Projekt xx
    // gibt man kein parameter an soll alles zurueck
    // entsprechen weitere parameter filtern die ausgabe
    $arr = $this->app->DB->SelectArr("SELECT adresse FROM bla bla where rolle=mitarbeiter von projekt xxx");
    foreach($arr as $value)
    {
      if($selected==$value) $tmp = "selected"; else $tmp="";
      $ret .= "<option value=\"$value\" $tmp>$value</option>";
    }
    return $ret;


  }

  function GetArtikelPreisvorlageProjekt($kunde,$projekt,$artikel,$menge)
  {
    //HACK!
    return $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE projekt='$projekt' AND artikel='$artikel'"); 
  }

  // do not use this function!
  function GetAuftragSteuersatz($auftrag)
  {
    //ermitteln aus Land und UST-ID Prüfung
    return 1.19;
  }

  function GetSteuersatzAssoc($id,$typ)
  {

    $tmp[0] = "0 %";
    $tmp[$this->GetSteuersatzErmaessigt(false,$id,$typ)] = $this->GetSteuersatzErmaessigt(false,$id,$typ)." %";
    $tmp[$this->GetSteuersatzNormal(false,$id,$typ)] = $this->GetSteuersatzNormal(false,$id,$typ)." %";

    return $tmp;
  }

  function GetSteuersatz($id,$typ)
  {

    $tmp[] = 0;
    $tmp[] = $this->GetSteuersatzErmaessigt(false,$id,$typ);
    $tmp[] = $this->GetSteuersatzNormal(false,$id,$typ);

    return $tmp;
  }

  function GetSelectSteuersatz($selected,$id,$typ)
  {
    $tmp = $this->GetSteuersatz($id,$typ);
    //if($value==
    foreach($tmp as $key=>$value)
    {
      if($selected==$value) $tmp = "selected"; else $tmp="";
      $ret .= "<option value=\"$value\" $tmp>$value %</option>";
    }
    return $ret;
  }


  function GetSteuersatzNormal($komma,$id,$typ)
  {
    if($typ=="provisionsgutschrift")
      $steuersatz = $this->app->DB->Select("SELECT steuersatz FROM mlm_abrechnung_adresse WHERE id='$id' LIMIT 1");
    else
      $steuersatz = $this->app->DB->Select("SELECT steuersatz_normal FROM $typ WHERE id='$id' LIMIT 1");

    if($komma)
      return ($steuersatz/100.0)+1.0; //1.19
    else
      return $steuersatz;
  }

  function GetSteuersatzErmaessigt($komma="",$id,$typ)
  {
    $steuersatz = $this->app->DB->Select("SELECT steuersatz_ermaessigt FROM $typ WHERE id='$id' LIMIT 1");
    if($komma)
      return ($steuersatz/100.0)+1.0; //1.19
    else
      return $steuersatz;
    /*
       if($komma)
       return 1.07;
       else
       return 7;
     */
  }



  function GetKreditkarten()
  {

    return array('MasterCard','Visa','American Express');
  }

  function GetKreditkartenSelect($selected)
  {
    foreach($this->GetKreditkarten() as $value)
    {
      if($selected==$value) $tmp = "selected"; else $tmp="";
      $ret .= "<option value=\"$value\" $tmp>$value</option>";
    }
    return $ret;
  }


  function GetKundeSteuersatz($kunde)
  {


  }

  function AddUSTIDPruefungKunde($kunde)
  {
    //gebunden an eine adresse


  }

  function GetVersandkosten($projekt)
  {

    return 3.32;
  }


  function AddArtikelProduktion($artikel,$menge,$produktion)
  {
    $table = "produktion_position";
    $module = "produktion";

    $bezeichnung = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
    $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
    // an letzter stelle artikel einfuegen mit standard preis vom auftrag
    $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
        VALUES ('','$produktion','$artikel','$beschreibung','$bezeichnung','$nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe')");

    $this->ProduktionEinzelnBerechnen($produktion);
    $this->ProduktionNeuberechnen($produktion);
  }


  function AuftraegeBerechnen()
  {
    $auftraege = $this->app->DB->SelectArr("SELECT id FROM auftrag WHERE status='freigegeben' AND inbearbeitung=0 ORDER By datum");
    for($i=0;$i<count($auftraege); $i++)
    {
      //$this->app->erp->AuftragNeuberechnen($auftraege[$i][id]);
      $this->app->erp->AuftragEinzelnBerechnen($auftraege[$i][id]);
    }
  }


  function AddArtikelAuftrag($artikel,$auftrag)
  {
    // an letzter stelle artikel einfuegen mit standard preis vom auftrag

  }

  function DelArtikelAuftrag($id)
  {
    //loesche artikel von auftrag und schiebe positionen nach


  }


  function GetAuftragStatus($auftrag)
  {



  }


  function Export($land)
  {
    if($land=="DE" || $land=="")
      return false;


    foreach($this->GetUSTEU() as $euland)
    {
      if($land==$euland)
        return false;
    }

    // alle anderen laender sind export!
    return true;
  }


  function GetUSTEU()
  {
    return
      array('BE','IT','RO',
          'BG','LV','SE',
          'DK','LT','SK',
          'DE','LU','SI',
          'EE','MT','ES',
          'FI','NL','CZ',
          'FR','AT','HU',
          'GR','PL','GB',
          'IE','PT','CY');
  }


  function CheckUSTFormat($ust)
  {
    $land = substr($ust,0,2);
    $nummer = substr($ust,2);

    switch($land)
    {
      case "BE":
        //zehn, nur Ziffern; (alte neunstellige USt-IdNrn. werden durch Voranstellen der Ziffer Ø ergänzt)
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land."0".$nummer;
        else if(is_numeric($nummer) && strlen($nummer)==10)
          return $land.$nummer;
        else
          return 0;
        break;

      case "BG":
        //   neun oder zehn, nur Ziffern
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else if(is_numeric($nummer) && strlen($nummer)==10)
          return $land.$nummer;
        else
          return 0;
        break;

      case "DK":
        //acht, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==8)
          return $land.$nummer;
        else return 0;
        break;

      case "DE":
        //neun, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else return 0;
        break;

      case "EE":
        //neun, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else return 0;
        break;

      case "FI":
        //acht, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==8)
          return $land.$nummer;
        else return 0;
        break;

      case "FR":
        //elf, nur Ziffern bzw. die erste und / oder die zweite Stelle kann ein Buchstabe sein
        if(is_numeric($nummer) && strlen($nummer)==11)
          return $land.$nummer;
        else if(ctype_digit(substr($nummer,0,1)) &&  is_numeric(substr($nummer,1)) && strlen($nummer)==11)
          return $land.$nummer;
        else if(ctype_digit(substr($nummer,0,2)) &&  is_numeric(substr($nummer,2)) && strlen($nummer)==11)
          return $land.$nummer;
        else return 0;
        break;

      case "EL":
        //neun, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else return 0;
        break;


      case "IE":
        //acht, die zweite Stelle kann und die letzte Stelle muss ein Buchstabe sein
        if(ctype_digit(substr($nummer,7,1)) &&  is_numeric(substr($nummer,0,7)) && strlen($nummer)==8)
          return $land.$nummer;
        else if(ctype_digit(substr($nummer,7,1)) && ctype_digit(substr($nummer,1,1)) && is_numeric(substr($nummer,0,7)) && strlen($nummer)==8)
          return $land.$nummer;
        else return 0;
        break;

      case "IT":
        //elf, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==11)
          return $land.$nummer;
        else return 0;
        break;


      case "LV":
        //elf, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==11)
          return $land.$nummer;
        else return 0;
        break;

      case "LT":
        //neu oder zwoelf, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else if(is_numeric($nummer) && strlen($nummer)==12)
          return $land.$nummer;
        else return 0;
        break;

      case "LU":
        //acht, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==8)
          return $land.$nummer;
        else return 0;
        break;

      case "MT":
        //acht, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==8)
          return $land.$nummer;
        else return 0;
        break;

      case "AT":
        //neun, nur ziffern die erste Stelle muss U sein
        if(is_numeric(substr($nummer,1,8)) && $nummer[0]=="U" && strlen($nummer)==9)
          return $land.$nummer;
        else return 0;
        break;

      case "NL":
        //neun, nur ziffern die erste Stelle muss U sein
        if(is_numeric(substr($nummer,0,9)) && $nummer[9]=="B" && strlen($nummer)==12)
          return $land.$nummer;
        else return 0;
        break;



      case "PL":
        //zehn, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==10)
          return $land.$nummer;
        else return 0;
        break;

      case "PT":
        //neun, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else return 0;
        break;


      case "RO":
        //maximal zehn, nur ziffern, erste stelle !=0
        if(is_numeric($nummer) && strlen($nummer)>=10 && $nummer[0]!=0)
          return $land.$nummer;
        else return 0;
        break;

      case "SE":
        //zwölf, nur Ziffern, die beiden letzten Stellen bestehen immer aus der Ziffernkombination „Ø1“
        if(is_numeric($nummer) && strlen($nummer)==12 && $nummer[10] == 0 && $nummer[11]==1)
          return $land.$nummer;
        else return 0;
        break;


      case "SK":
        //zehn, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==10)
          return $land.$nummer;
        else return 0;
        break;

      case "SI":
        //acht, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==8)
          return $land.$nummer;
        else return 0;
        break;

      case "ES":
        //neun, die erste und die letzte Stelle bzw. die erste oder die letzte Stelle kann ein Buchstabe sein
        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else if(is_numeric(substr($nummer,1,7)) && strlen($nummer)==9 && ctype_digit(substr($nummer,0,1)) && ctype_digit(substr($nummer,8,1)) )
          return $land.$nummer;
        else if(is_numeric(substr($nummer,1,8)) && strlen($nummer)==9 && ctype_digit(substr($nummer,0,1)))
          return $land.$nummer;
        else if(is_numeric(substr($nummer,0,8)) && strlen($nummer)==9 && ctype_digit(substr($nummer,8,1)))
          return $land.$nummer;
        else return 0;
        break;

      case "CZ":
        //   acht, neun oder zehn, nur Ziffern
        if(is_numeric($nummer) && strlen($nummer)>=8 && strlen($nummer)<=10)
          return $land.$nummer;
        else return 0;
        break;

      case "HU":
        //acht, nur ziffern
        if(is_numeric($nummer) && strlen($nummer)==8)
          return $land.$nummer;
        else return 0;
        break;

      case "GB":
        //neu oder zwoelf, nur ziffern, für Verwaltungen und Gesundheitswesen: fünf, die ersten zwei Stellen GD oder HA

        if(is_numeric($nummer) && strlen($nummer)==9)
          return $land.$nummer;
        else if(is_numeric($nummer) && strlen($nummer)==12)
          return $land.$nummer;
        else if(is_numeric(substr($nummer,2,3)) && $nummer[0]=="G" && $nummer[1]=="D")
          return $land.$nummer;
        else if(is_numeric(substr($nummer,2,3)) && $nummer[0]=="H" && $nummer[1]=="A")
          return $land.$nummer;
        else return 0;
        break;


      case "CY":
        //neun, die letzte Stelle muss ein Buchstaben sein
        if(is_numeric(substr($nummer,0,8)) && strlen($nummer)==9 && ctype_digit(substr($nummer,8,1)))
          return $land.$nummer;
        else return 0;
        break;


    }

  }


  function CheckUst($ust1,$ust2, $firmenname, $ort, $strasse, $plz, $druck="nein")
  {
    $tmp = new USTID();
    //$status = $tmp->check("DE263136143","SE556459933901","Wind River AB","Kista","Finlandsgatan 52","16493","ja");
    $status = $tmp->check($ust1, $ust2, $firmenname, $ort, $strasse, $plz, $druck,$onlinefehler);
    if($tmp->answer['Erg_Name'] == 'A')$tmp->answer['Erg_Name'] = '';     
    if($tmp->answer['Erg_Ort'] == 'A')$tmp->answer['Erg_Ort'] = '';     
    if($tmp->answer['Erg_Str'] == 'A')$tmp->answer['Erg_Str'] = '';     
    if($tmp->answer['Erg_PLZ'] == 'A')$tmp->answer['Erg_PLZ'] = '';     

    $erg = array(
        'ERG_NAME' => $tmp->answer['Erg_Name'],
        'ERG_ORT' => $tmp->answer['Erg_Ort'],
        'ERG_STR' => $tmp->answer['Erg_Str'],
        'ERG_PLZ' => $tmp->answer['Erg_PLZ'],
        'ERROR_CODE' => $tmp->answer['ErrorCode']);

    $error = 0;
    //1 wenn UST-ID. korrekt
    if($status == 1){
      if($tmp->answer['Erg_Name'] == 'B')$error++;
      if($tmp->answer['Erg_Ort'] == 'B')$error++;
      if($tmp->answer['Erg_Str'] == 'B')$error++;
      if($tmp->answer['Erg_PLZ'] == 'B')$error++;

      if($error > 0)
        return $erg;
      else{
        //Brief bestellen 
        //$status = $tmp->check($ust1, $ust2, $firmenname, $ort, $strasse, $plz, "ja"); 
        return 1;
      }

    } else{
      //return "<h1>Meldung dringend melden: Status $status ($onlinefehler)</h1>";
      if(is_array($tmp->answer))
      {
        return $tmp->answer;
      }
      else return $onlinefehler;
    }
    //echo $tmp->check("DE2631361d3","SE556459933901","Wind River AB","Kista","Finlandsgatan 52","16493","ja");

  }

  function CreateTicket($projekt,$quelle,$kunde,$mailadresse,$betreff,$text,$timestamp="",$medium="email")
  {
    $i=rand(300,700);
    while(1)
    {
      $testschluessel = date('Ymd').sprintf("%04d",$i++);
      $check = $this->app->DB->Select("SELECT schluessel FROM ticket WHERE schluessel='$testschluessel' LIMIT 1");
      if($check=="") break;
    }

    $warteschlange = $this->app->DB->Select("SELECT ticketqueue FROM emailbackup WHERE benutzername='$quelle' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM emailbackup WHERE benutzername='$quelle' LIMIT 1");

    $sql = "INSERT INTO ticket (`id`, `schluessel`, `zeit`, `projekt`, `quelle`, `status`, `kunde`, `mailadresse`, `prio`, `betreff`,`warteschlange`)
      VALUES (NULL, '$testschluessel', FROM_UNIXTIME($timestamp), '$projekt', '$quelle', 'offen', '$kunde', '$mailadresse', 
          '3','$betreff','$warteschlange');";
    $this->app->DB->InsertWithoutLog($sql);


    $sql = "INSERT INTO `ticket_nachricht` (`id`, `ticket`, `zeit`,`text`,`betreff`,`medium`,`verfasser`, `mail`) 
      VALUES (NULL, '$testschluessel', FROM_UNIXTIME($timestamp), '$text','$betreff','$medium','$kunde', '$mailadresse');";

    $this->app->DB->InsertWithoutLog($sql);
    $id = $this->app->DB->GetInsertID();
    //  als rueckgabe ticketnachricht
    return $id;
  }


  function MailSendNoBCCHTML($from,$from_name,$to,$to_name,$betreff,$text,$files="",$cc="",$bcc="")
  { 
    $from_name = $this->ClearDataBeforeOutput($from_name); 
    $to_name = $this->ClearDataBeforeOutput($to_name); 
    //$to = ""; // testmail
    $betreff  =  $this->ReadyForPDF($betreff);
    $text =  $this->ReadyForPDF($text);

    $this->app->mail->ClearData();

    for($i=0;$i<count($cc);$i++)
    {
      if($cc[$i]!="" && $cc[$i]!=$to)
        $this->app->mail->AddCC($cc[$i]);
    }

    for($i=0;$i<count($bcc);$i++)
    {
      if($bcc[$i]!="" && $bcc[$i]!=$to)
        $this->app->mail->AddBCC($bcc[$i]);
    }

    $this->app->mail->From       = $from;
    $this->app->mail->FromName   = utf8_decode($from_name);

    $this->app->mail->Subject    = utf8_decode($betreff);
    $this->app->mail->AddAddress($to, utf8_decode($to_name));

    $this->app->mail->Body = utf8_decode(str_replace('\r\n',"\n",$text).nl2br($this->Signatur()));

    $this->app->mail->IsHTML(true);

    for($i=0;$i<count($files);$i++)
      $this->app->mail->AddAttachment($files[$i]);

    if(!$this->app->mail->Send()) {
      $error =  "Mailer Error: " . $this->app->mail->ErrorInfo;
      return 0;
    } else {
      $error = "Message sent!";
      return 1;
    }


  }



  function MailSendNoBCC($from,$from_name,$to,$to_name,$betreff,$text,$files="")
  { 
    $from_name = $this->ClearDataBeforeOutput($from_name); 
    $to_name = $this->ClearDataBeforeOutput($to_name); 
    //$to = ""; // testmail
    $betreff  =  $this->ReadyForPDF($betreff);
    $text =  $this->ReadyForPDF($text);


    $this->app->mail->ClearData();

    $this->app->mail->From       = $from;
    $this->app->mail->FromName   = utf8_decode($from_name);

    $this->app->mail->Subject    = utf8_decode($betreff);
    $this->app->mail->AddAddress($to, utf8_decode($to_name));

    $this->app->mail->Body = utf8_decode(str_replace('\r\n',"\n",$text).$this->Signatur());


    for($i=0;$i<count($files);$i++)
      $this->app->mail->AddAttachment($files[$i]);

    if(!$this->app->mail->Send()) {
      $error =  "Mailer Error: " . $this->app->mail->ErrorInfo;
      return 0;
    } else {
      $error = "Message sent!";
      return 1;
    }
  }

  function MailSend($from,$from_name,$to,$to_name,$betreff,$text,$files="",$projekt="",$signature=true)
  {
    $from_name = $this->ClearDataBeforeOutput($from_name);
    $to_name = $this->ClearDataBeforeOutput($to_name);
    $to_name = $this->ReadyForPDF($to_name);
    $from_name = $this->ReadyForPDF($from_name);
    $this->app->mail->ClearData();

    if($projekt > 0 && $this->Projektdaten($projekt,"absendeadresse")!="")
      $this->app->mail->From       = $this->Projektdaten($projekt,"absendeadresse");
    else
      $this->app->mail->From       = $from;

    if($projekt > 0 && $this->Projektdaten($projekt,"absendename")!="")
      $this->app->mail->FromName   = utf8_decode($this->ReadyForPDF($this->Projektdaten($projekt,"absendename")));
    else
      $this->app->mail->FromName   = utf8_decode($from_name);

    $betreff  =  $this->ReadyForPDF($betreff);
    $text =  $this->ReadyForPDF($text);

    $this->app->mail->Subject    = utf8_decode($betreff);
    $this->app->mail->AddAddress($to, utf8_decode($to_name));


    if($signature)
    {
      if($projekt > 0 && $this->Projektdaten($projekt,"absendesignatur")!="")
        $this->app->mail->Body = utf8_decode(str_replace('\r\n',"\n",$text)."\r\n\r\n".$this->ReadyForPDF($this->Projektdaten($projekt,"absendesignatur")));
      else
        $this->app->mail->Body = utf8_decode(str_replace('\r\n',"\n",$text).$this->Signatur());
    } else {
      $this->app->mail->Body = utf8_decode(str_replace('\r\n',"\n",$text));
    }

    $bcc1 = $this->app->DB->Select("SELECT bcc1 FROM firmendaten WHERE firma='1' LIMIT 1");
    $bcc2 = $this->app->DB->Select("SELECT bcc2 FROM firmendaten WHERE firma='1' LIMIT 1");

    if($bcc1!="") $this->app->mail->AddBCC($bcc1);
    if($bcc2!="") $this->app->mail->AddBCC($bcc2);

    for($i=0;$i<count($files);$i++)
      $this->app->mail->AddAttachment($files[$i]);

    if(!$this->app->mail->Send()) {
      $this->mail_error =  "Mailer Error: " . $this->app->mail->ErrorInfo;
      return 0;
    } else {
      $this->mail_error = "";
      return 1;
    }


  }

  function TicketMail($message)
  {
    $tmp = $this->app->DB->SelectArr("SELECT * FROM ticket_nachricht WHERE id='$message' LIMIT 1");

    $projekt = $this->app->DB->Select("SELECT projekt FROM ticket WHERE schluessel='".$tmp[0]['ticket']."' 
        AND schluessel!='' LIMIT 1");

    //              move_uploaded_file($_FILES['datei']['tmp_name'], "upload/datei.txt");
    if(is_file($_FILES['datei']['tmp_name']))
    {
      $this->app->mail->AddAttachment($_FILES['datei']['tmp_name'],$_FILES['datei']['name']);
    }


    if($projekt > 0 && $this->Projektdaten($projekt,"absendeadresse")!="")
      $this->app->mail->From       = $this->Projektdaten($projekt,"absendeadresse");
    else
      $this->app->mail->From       = $this->GetFirmaMail();

    if($projekt > 0 && $this->Projektdaten($projekt,"absendename")!="")
      $this->app->mail->FromName   = utf8_decode($this->ReadyForPDF($this->Projektdaten($projekt,"absendename")));
    else
      $this->app->mail->FromName   = utf8_decode($this->GetFirmaName()); 

    //$this->app->mail->AddBCC('');
    $bcc1 = $this->app->DB->Select("SELECT bcc1 FROM firmendaten WHERE firma='1' LIMIT 1");
    $bcc2 = $this->app->DB->Select("SELECT bcc2 FROM firmendaten WHERE firma='1' LIMIT 1");

    if($bcc1!="") $this->app->mail->AddBCC($bcc1);
    if($bcc2!="") $this->app->mail->AddBCC($bcc2);



    $tmp[0]['betreff'] =  $this->ReadyForPDF($tmp[0]['betreff']);

    $this->app->mail->Subject    = utf8_decode("RE: ".$tmp[0]['betreff']." Ticket #".$tmp[0]['ticket']);
    $this->app->mail->AddAddress($tmp[0]['mail'], $tmp[0]['verfasser']);

    $tmp[0]['textausgang'] =  $this->ReadyForPDF($tmp[0]['textausgang']);

    $text = $tmp[0]['textausgang'];
    if($projekt > 0 && $this->Projektdaten($projekt,"absendesignatur")!="")
      $this->app->mail->Body = utf8_decode(str_replace('\r\n',"\n",$text)."\r\n\r\n".$this->ReadyForPDF($this->Projektdaten($projekt,"absendesignatur")));
    else
      $this->app->mail->Body = utf8_decode(str_replace('\r\n',"\n",$text).$this->Signatur());


    if(!$this->app->mail->Send()) {
      $error =  "Mailer Error: " . $this->app->mail->ErrorInfo;
      $this->app->DB->Update("UPDATE ticket_nachricht SET status='beantwortet',versendet='0' WHERE id=".$message);
      echo $error;
      return 0;
    } else {
      $error = "Message sent!";
      echo $error;
      $this->app->DB->Update("UPDATE ticket_nachricht SET status='beantwortet',versendet='1' WHERE id=".$message);
      return 1;
    }
  }

  function isMailAdr($mailadr){
    if(!eregi("^[_a-z0-9!#$%&\\'*+-\/=?^_`.{|}~]+(\.[_a-z0-9!#$%&\'*+-\\/=?^_`.{|}~]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $mailadr))
      return 0;
    else
      return 1;
  }

  function filterMailAdr($mailadr){
    eregi("(&lt;)+.*(&gt;)+", $mailadr, $matches);
    $mailadr = str_replace("&lt;", "",$matches[0]);
    $mailadr = str_replace("&gt;", "",$mailadr);
    if($this->isMailAdr($mailadr))
      return $mailadr;
    else
      return 0;
  }


  function FirmendatenSet($field,$value)
  {
    $firmendatenid = $this->app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1"); 
    $this->app->DB->Update("UPDATE firmendaten SET ".$field."='$value' WHERE id='".$firmendatenid."'");      
  }

  function Firmendaten($field)
  {
    $firmendatenid = $this->app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1"); 
    return $this->app->DB->Select("SELECT ".$field." FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");      
  }

  function Signatur()
  {

    $firmendatenid = $this->app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");                                                                                                                                                           
    $signatur = $this->app->DB->Select("SELECT signatur FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");      

    return "\r\n\r\n".base64_decode($this->ReadyForPDF($signatur));
  }


  function GetDatevKonten($konto="")
  {
    $konten = array(''=>'','1370'=>'Durchlaufende Posten','6800'=>'Porto');

    foreach($konten  as $key=>$value)
    {
      if($konto==$key) $selected="selected"; else $selected="";
      $tmp = str_replace("*","&nbsp;",str_pad ($key, 8,"*"));
      $ret .="<option value=\"$key\" $selected>$tmp $value</option>";
    }
    return $ret;
  }


  function GetQuelleTicket()
  {
    return array('Telefon','Fax','Brief','Selbstabholer');
  }


  function GetPrioTicketSelect($prio)
  {
    $prios = array('5'=>'sehr niedrig','4'=>'niedrig','3'=>'normal','2'=>'wichtig','1'=>'sehr wichtig');

    foreach($prios as $key=>$value)
    {
      if($prio==$key) $selected="selected"; else $selected="";
      $ret .="<option value=\"$key\" $selected>$value</option>";
    }
    return $ret;
  }


  function GetZeiterfassungArt()
  {
    return array('arbeit'=>'Arbeit','pause'=>'Pause','urlaub'=>'Urlaub','krankheit'=>'Krankheit','ueberstunden'=>'Freizeitausleich','feiertag'=>'Feiertag');
  }

  function GetVPE()
  {
    return array('einzeln'=>'Einzeln','tray'=>'Tray','rolle'=>'Rolle','stueckgut'=>'St&uuml;ckgut','stange'=>'Stange','palette'=>'Palette');
  }

  function GetUmsatzsteuerklasse()
  {
    return array('normal'=>'normal','ermaessigt'=>'erm&auml;&szlig;gt');
  }

  function GetEtikett()
  {
    return array('klein'=>'Artikelaufkleber klein','gross'=>'Artikelaufkleber gro&szlig;');
  }


  function GetWaehrung()
  {
    return array('EUR'=>'EUR','USD'=>'USD','CAD'=>'CAD');
  }


  function GetLager($mitstandardlager=false)
  {
    if($this->Firmendaten("wareneingang_zwischenlager")=="1")
    {
      $tmp['zwischenlager'] = "Zwischenlager";
      if($mitstandardlager)
        $tmp['standardlager'] = "Standardlager";
    }
    else {
      if($mitstandardlager)
        $tmp['standardlager'] = "Standardlager";

      $tmp['zwischenlager'] = "Zwischenlager";
    }
    $result = $this->app->DB->SelectArr("SELECT lp.id, CONCAT(l.bezeichnung,'->',lp.kurzbezeichnung) as kurzbezeichnung FROM lager_platz lp LEFT JOIN lager l ON lp.lager=l.id WHERE l.firma='1' AND lp.kurzbezeichnung!='' ORDER by l.bezeichnung,lp.kurzbezeichnung");

    for($i=0;$i<count($result);$i++)
      $tmp[$result[$i][id]]=$result[$i][kurzbezeichnung];

    return $tmp;
  }

  function GetArtikelart()
  {
    return array('produkt'=>'Produkt','material'=>'Material','dienstleistung'=>'Dienstleistung','muster'=>'Muster',
        'gebuehr'=>'Geb&uuml;hr','betriebsstoff'=>'Betriebsstoff','buerobedarf'=>'B&uuml;robedarf',
        'inventar'=>'Inventar','porto'=>'Porto','literatur'=>'Literatur');
  }


  function GetWarteschlangeTicketWiedervorlage()
  {

    $tmp = $this->app->DB->SelectArr("SELECT warteschlange,label FROM warteschlangen WHERE wiedervorlage='1' ORDER by warteschlange DESC");

    for($i=0;$i<count($tmp);$i++)
    {
      $tmp_array[$tmp[$i]['label']]=$tmp[$i]['warteschlange'];
    }

    return $tmp_array;
    //return array('verwaltung'=>'Verwaltung','technik'=>'Technik','buchhaltung'=>'Buchhaltung','unishop'=>'Uni-Shop','wawision'=>'waWision','journal'=>'Journal','bewerbung'=>'Bewerbung');
  }

  function GetWarteschlangeTicket()
  {
    $tmp = $this->app->DB->SelectArr("SELECT warteschlange,label FROM warteschlangen WHERE wiedervorlage!='1' ORDER by warteschlange DESC");

    for($i=0;$i<count($tmp);$i++)
    {
      $tmp_array[$tmp[$i]['label']]=$tmp[$i]['warteschlange'];
    }

    return $tmp_array;
  }

  function GetWarteschlangeTicketAlle()
  {
    $tmp = $this->app->DB->SelectArr("SELECT warteschlange,label FROM warteschlangen ORDER by warteschlange DESC");

    for($i=0;$i<count($tmp);$i++)
    {
      $tmp_array[$tmp[$i]['label']]=$tmp[$i]['warteschlange'];
    }

    return $tmp_array;
  }



  function GetWarteschlangeTicketSelect($warteschlange)
  {
    $prios = $this->GetWarteschlangeTicketAlle();

    foreach($prios as $key=>$value)
    {
      if($warteschlange==$key) $selected="selected"; else $selected="";
      $ret .="<option value=\"$key\" $selected>$value</option>";
    }
    return $ret;
  }


  function StartMessung()
  {
    $this->start_messung = $this->uniqueTimeStamp();
  }

  function EndeMessung()
  {
    $this->ende_messung = $this->uniqueTimeStamp();
  }

  function ErgebnisMessung()
  {
    $differenz = $this->ende_messung-$this->start_messung;
    $differenz = $differenz/10; // warum auch immer
    $differenz = (int)$differenz;

    echo "Die Ausführung dauerte $differenz ms"; 
  }

  function uniqueTimeStamp() {
    $milliseconds = microtime();
    $timestring = explode(" ", $milliseconds);
    $sg = $timestring[1];
    $mlsg = substr($timestring[0], 2, 4);
    $timestamp = $sg.$mlsg;
    return $timestamp; 
  } 

  function GetWartezeitTicket($zeit)
  {
    $timestamp = strToTime($zeit, null);


    $td = $this->makeDifferenz($timestamp,time());
    return $td['day'][0] . ' ' . $td['day'][1] . ', ' . $td['std'][0] . ' ' . $td['std'][1] . 
      ', ' . $td['min'][0] . ' ' . $td['min'][1];// . ', ' . $td['sec'][0] . ' ' . $td['sec'][1];
  }

  function makeDifferenz($first, $second){

    if($first > $second)
      $td['dif'][0] = $first - $second;
    else
      $td['dif'][0] = $second - $first;

    $td['sec'][0] = $td['dif'][0] % 60; // 67 = 7

    $td['min'][0] = (($td['dif'][0] - $td['sec'][0]) / 60) % 60; 

    $td['std'][0] = (((($td['dif'][0] - $td['sec'][0]) /60)- 
          $td['min'][0]) / 60) % 24;

    $td['day'][0] = floor( ((((($td['dif'][0] - $td['sec'][0]) /60)- 
              $td['min'][0]) / 60) / 24) );

    $td = $this->makeString($td);

    return $td;

  }


  function makeString($td){

    if ($td['sec'][0] == 1)
      $td['sec'][1] = 'Sekunde';
    else 
      $td['sec'][1] = 'Sekunden';

    if ($td['min'][0] == 1)
      $td['min'][1] = 'Minute';
    else 
      $td['min'][1] = 'Minuten';

    if ($td['std'][0] == 1)
      $td['std'][1] = 'Stunde';
    else 
      $td['std'][1] = 'Stunden';

    if ($td['day'][0] == 1)
      $td['day'][1] = 'Tag';
    else 
      $td['day'][1] = 'Tage';

    return $td;

  }


  function GetProjektSelect($projekt,$color_selected="")
  {

    $sql = "SELECT id,name,farbe FROM projekt order by id";
    $tmp = $this->app->DB->SelectArr($sql);
    for($i=0;$i<count($tmp);$i++)
    {
      if($tmp[$i]['farbe']=="") $tmp[$i]['farbe']="white";
      if($projekt==$tmp[$i]['id']){
        $options = $options."<option value=\"{$tmp[$i]['id']}\" selected 
          style=\"background-color:{$tmp[$i]['farbe']};\">{$tmp[$i]['name']}</option>";
        $color_selected = $tmp[$i]['farbe'];
      }
      else
        $options = $options."<option value=\"{$tmp[$i]['id']}\" 
          style=\"background-color:{$tmp[$i]['farbe']};\">{$tmp[$i]['name']}</option>";
    }
    return $options;

  }

  function GetAdressName($id)
  {
    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($id))
        $result = $this->app->DB->SelectArr("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $result = $this->app->DB->SelectArr("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    }

    //return $result[0][vorname]." ".$result[0][name];
    return $result[0][name];
  }

  function GetAdressSubject()
  {
    return array('Kunde','Lieferant','Mitarbeiter','Externer Mitarbeiter','Projektleiter');
  }

  function GetAdressPraedikat()
  {
    return array('','von','fuer','ist');
  }

  function GetAdressObjekt()
  {
    return array('','Projekt');
  }


  function GetVersandartAuftrag()
  {
    //return array('versandunternehmen'=>'Standard Versandunternehmen','selbstabholer'=>'Selbstabholer','keinversand'=>'Kein Versand',
    return array('selbstabholer'=>'Selbstabholer','keinversand'=>'Kein Versand',
        'DHL'=>'DHL','DPD'=>'DPD',
        'express_dpd'=>'Express DPD','export_dpd'=>'Export DPD','spedition'=>'Spedition');
  }


  function GetVersandartLieferant()
  {
    return array('DHL','DPD','Hermes','UPS','GLS','Post','Spedition','Selbstabholer','Packstation');
  }

  function GetArtikelgruppe($projekt="")
  {

    if($projekt > 0)
    {
        $result = $this->app->DB->SelectArr("SELECT id,bezeichnung FROM artikelkategorien WHERE projekt='$projekt' AND geloescht!=1");
        // gibt es keine projekt gruppen dann die ohne projekt verwenden
        if(count($result)<=0)
          $result = $this->app->DB->SelectArr("SELECT id,bezeichnung FROM artikelkategorien WHERE geloescht!=1 AND projekt <=0");
    } else
    {
        $result = $this->app->DB->SelectArr("SELECT id,bezeichnung FROM artikelkategorien WHERE geloescht!=1 AND projekt <=0");
    }


    if(count($result) > 0)
    {
     
      for($i=0;$i<count($result);$i++)
      {
        $tmp[$result[$i]['id']."_kat"]=$result[$i]['bezeichnung'];
      }

      return $tmp;

    } else {
      return array("produkt"=>"Ware f&uuml;r Verkauf (700000)",
        "module"=>"Module / Hardware (600000)",
        "produktion"=>"Produktionsmaterial (400000)",
        "material"=>"Sonstiges (100000)",
        "fremdleistung"=>"Fremdleistung (100000)",
        "gebuehr"=>"Geb&uuml;hr / Miete (100000)");
    }
  }


  function GetVerrechnungskontenReisekosten()
  {
    $tmp = $this->app->DB->SelectArr("SELECT CONCAT(bezeichnung,' (',datevkonto,')') as bezeichnung,id FROM konten WHERE `type` 
      LIKE 'verrechnung%' AND type!='verrechnungskontofirma' AND firma='".$this->app->User->GetFirma()."'");

        $result[0]="Standardeinstellung";

        for($i=0;$i<count($tmp);$i++)
        {
        $result[$tmp[$i][id]] = $tmp[$i][bezeichnung];
        }
        return $result;
        }


        function GetZahlungsstatus()
        {
        return array('offen','bezahlt');
        }

        function GetZahlungsweiseGutschrift()
        {
        //    return array('ueberweisung'=>'&Uuml;berweisung','bar'=>'Bar','paypal'=>'PayPal');

          $tmp['ueberweisung']="&Uuml;berweisung";

          if($this->Firmendaten("zahlung_kreditkarte"))
            $tmp['kreditkarte']="Kreditkarte";

          if($this->Firmendaten("zahlung_lastschrift"))
            $tmp['lastschrift']="Verrechnen mit Lastschriften";


          if($this->Firmendaten("zahlung_bar"))
            $tmp['bar']="Barzahlung";

          if($this->Firmendaten("zahlung_paypal"))
            $tmp['paypal']="Paypal";

          if($this->Firmendaten("zahlung_amazon"))
            $tmp['amazon']="Amazon Payments";


          return $tmp;

        }

    function GetZahlungsweise()
    {

      if($this->Firmendaten("zahlung_rechnung"))
        $tmp['rechnung']="Rechnung";

      if($this->Firmendaten("zahlung_vorkasse"))
        $tmp['vorkasse']="Vorkasse";

      if($this->Firmendaten("zahlung_nachnahme"))
        $tmp['nachnahme']="Nachnahme";

      if($this->Firmendaten("zahlung_kreditkarte"))
        $tmp['kreditkarte']="Kreditkarte";

      if($this->Firmendaten("zahlung_bar"))
        $tmp['bar']="Barzahlung";

      if($this->Firmendaten("zahlung_paypal"))
        $tmp['paypal']="Paypal";

      if($this->Firmendaten("zahlung_amazon"))
        $tmp['amazon']="Amazon Payments";

      if($this->Firmendaten("zahlung_lastschrift"))
        $tmp['lastschrift']="Lastschrift";

      if($this->Firmendaten("zahlung_ratenzahlung"))
        $tmp['ratenzahlung']="Ratenzahlung";

      return $tmp;
      //              return array('rechnung'=>'Rechnung','vorkasse'=>'Vorkasse','nachnahme'=>'Nachnahme','kreditkarte'=>'Kreditkarte','einzugsermaechtigung'=>'Einzugsermaechtigung','bar'=>'Bar','paypal'=>'PayPal','lastschrift'=>'Lastschrift');
    }

    function GetTypSelect()
    {
      return array('firma'=>'Firma','herr'=>'Herr','frau'=>'Frau');
    }

    function GetArtikelWarengruppe()
    {
      //return array('SMD','THT','EBG','BGP');
      $tmp = array('','Bauteil','Eval-Board','Adapter','Progammer','Ger&auml;t','Kabel','Software','Dienstleistung','Spezifikation');
      sort($tmp);
      return $tmp;
    }

    function GetBezahltWieAssoc()
    {
      return array('privat'=>"Privat",'firma_bar'=>"Firma (Kasse/Bar)",'firma_ecr'=>"Firma (EC-Karte)",'firma_cc'=>"Firma (Kreditkarte)",'firma_sonst'=>"Firma (Sonstige)");
    }

    function GetStatusArbeitsnachweis()
    {
      return array('offen','freigegeben','versendet');
    }


    function GetStatusAnfrage()
    {
      return array('offen','abgeschlossen');
    }

    function GetStatusInventur()
    {
      return array('offen','abgeschlossen');
    }

    function GetStatusReisekosten()
    {
      return array('offen','freigegeben','versendet','buchhaltung');
    }


    function GetStatusLieferschein()
    {
      return array('offen','freigegeben','versendet');
    }


    function GetStatusAuftrag()
    {
      return array('offen','freigegeben','abgeschlossen');
    }


    function GetStatusProduktion()
    {
      return array('offen','freigegeben','gestartet','abgeschlossen');
    }

    function GetStatusAngebot()
    {
      return array('offen','freigegeben','bestellt','angemahnt','empfangen');
    }


    function GetStatusGutschrift()
    {
      return array('offen','freigegeben','bezahlt');
    }

    function GetStatusRechnung()
    {
      return array('offen','freigegeben','gestellt','zahlungserinnerung','mahnung');
    }

    function GetFirmaFieldsCheckbox()
    {
      return array('versand_gelesen','zahlung_rechnung','zahlung_vorkasse','zahlung_bar','zahlung_lastschrift','zahlung_paypal','zahlung_amazon',
          'zahlung_kreditkarte','zahlung_nachnahme','zahlung_ratenzahlung','knickfalz','firmenlogoaktiv',
          'standardaufloesung','immernettorechnungen','bestellvorschlaggroessernull','schnellanlegen','kleinunternehmer','api_enable','api_importwarteschlange','warnung_doppelte_nummern','wareneingang_zwischenlager','bestellungohnepreis','zahlung_lastschrift_konditionen','mysql55','porto_berechnen','breite_artikelbeschreibung','deviceenable',
          'iconset_dunkel','api_cleanutf8','mahnwesenmitkontoabgleich');
    }


    function GetFirmaFields()
    {
      $fields =  array('zahlung_rechnung_de','zahlung_kreditkarte_de','breite_position','breite_menge','breite_nummer','breite_einheit',
          'zahlung_vorkasse_de','zahlung_nachnahme_de','zahlung_lastschrift_de','zahlung_bar_de','zahlung_paypal_de','zahlung_amazon_de','zahlung_ratenzahlung_de',
          'zahlung_rechnung_sofort_de','firmenfarbehell','firmenfarbeganzdunkel','firmenfarbedunkel','navigationfarbe','navigationfarbeschrift','unternavigationfarbe','unternavigationfarbeschrift','api_importwarteschlange_name',
          'zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','api_initkey','api_remotedomain','api_eventurl','steuer_erloese_inland_normal','devicekey','deviceserials',
          'steuer_aufwendung_inland_normal',
          'steuer_erloese_inland_ermaessigt',
          'steuer_aufwendung_inland_ermaessigt',
          'steuer_erloese_inland_steuerfrei',
          'steuer_aufwendung_inland_steuerfrei',
          'steuer_erloese_inland_innergemeinschaftlich',
          'steuer_aufwendung_inland_innergemeinschaftlich',
          'steuer_erloese_inland_eunormal',
          'steuer_aufwendung_inland_eunormal',
          'steuer_erloese_inland_export',
          'etikettendrucker_wareneingang',
          'steuer_aufwendung_inland_import','versandart','zahlungsweise','bezeichnungstornorechnung','steuer_anpassung_kundennummer'
          );

      for($ki=1;$ki<=15;$ki++)
      {
        $fields[]='steuer_art_'.$ki;
        $fields[]='steuer_art_'.$ki.'_normal';
        $fields[]='steuer_art_'.$ki.'_ermaessigt';
        $fields[]='steuer_art_'.$ki.'_steuerfrei';
      }
      return $fields;
    }

    function GetMLMAuszahlungWaehrung()
    {
      return array('EUR','CHF');
    }

    function GetWaehrungUmrechnungskurs($von,$nach)
    {
      if($von=="EUR" && $nach=="CHF")
        return 1.2; 
      else return 1;
    }

    function GetMLMAbrechnung()
    {
      return array('sammelueberweisung'=>'Sammel&uuml;berweisung','manuell'=>'Manuelle Auszahlung');
    }

    function GetMLMPositionierung()
    {
      return array('1'=>'1. Junior Consultant',
          '2'=>'2. Consultant',
          '3'=>'3. Associate',
          '4'=>'4. Manager',
          '5'=>'5. Senior Manager',
          '6'=>'6. General Director',
          '7'=>'7. General Manager',
          '8'=>'8. Chief Manager',
          '9'=>'9. Vice President',
          '10'=>'10. President',
          '11'=>'11. ',
          '12'=>'12. ',
          '13'=>'13. ',
          '14'=>'14. ',
          '15'=>'15. '
          );
    }

    function GetStatusBestellung()
    {
      return array('offen','freigegeben','bestellt','angemahnt','empfangen');
    }

    function GetSelectAsso($array, $selected)
    {
      foreach($array as $key=>$value)
      {
        if($selected==$key) $tmp = "selected"; else $tmp="";
        $ret .= "<option value=\"$key\" $tmp>$value</option>";
      }
      return $ret;
    }

    function GetSelect($array, $selected)
    {
      foreach($array as $value)
      {
        if($selected==$value) $tmp = "selected"; else $tmp="";
        $ret .= "<option value=\"$value\" $tmp>$value</option>";
      }
      return $ret;
    }

    function CreateAdresse($name,$firma="1")
    {
      $zahlungsweise = $this->StandardZahlungsweise($projekt);
      $this->app->DB->Insert("INSERT INTO adresse (id,name,firma,zahlungsweise) VALUES ('','$name','$firma','$zahlungsweise')");
      return $this->app->DB->GetInsertID();
    }

    function AddRolleZuAdresse($adresse, $subjekt, $praedikat="", $objekt="", $parameter="")
    {
      $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");
      if(strtoupper($objekt)=="PROJEKT")
      {
        $parameter = $this->app->DB->Select("SELECT id FROM projekt WHERE id='$parameter' LIMIT 1");
        if($parameter<=0) $parameter=0;
        if($parameter > 0 ) $projekt=$parameter;
        else {
          $parameter ="";
          $projekt = 0;
        }
      }


      $check = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE 
          subjekt='$subjekt' AND objekt='$objekt' AND praedikat='$praedikat' AND parameter='$parameter' AND adresse='$adresse'  AND (bis >= NOW() OR bis='0000-00-00')  LIMIT 1");

      if($check > 0)
        return $check;

      // Insert ....  
      $sql ="INSERT INTO adresse_rolle (id, adresse, subjekt, praedikat, objekt, parameter,von)
        VALUES ('','$adresse','$subjekt','$praedikat','$objekt','$parameter',NOW())";
      $this->app->DB->Insert($sql);
      $id =  $this->app->DB->GetInsertID();


      $kundennummer = trim($this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1"));
      // wenn adresse zum erstenmal die rolle erhält wird kundennummer bzw. lieferantennummer vergeben
      if($subjekt=="Kunde" && ($kundennummer=="" || is_array($kundennummer)))
      {
        $kundennummer = $this->GetNextKundennummer($projekt);
        $this->app->DB->Update("UPDATE adresse SET kundennummer='$kundennummer' WHERE id='$adresse' AND (kundennummer='0' OR kundennummer='') LIMIT 1");
      }

      $lieferantennummer = trim($this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1"));
      //$this->LogFile("DEBUG subjekt = $subjekt, projekt=$projekt,adresse=$adresse,lieferantennummer=$lieferantennummer");
      if($subjekt=="Lieferant" && ($lieferantennummer=="" || is_array($lieferantennummer)))
      {
        $lieferantennummer = $this->GetNextLieferantennummer($projekt);

        $this->app->DB->Update("UPDATE adresse SET lieferantennummer='$lieferantennummer' WHERE id='$adresse' AND (lieferantennummer='0' OR lieferantennummer='') LIMIT 1");
      }

      $this->app->DB->Delete("DELETE FROM adresse_rolle WHERE von > bis AND bis!='0000-00-00'");
      return $id;
    }


    function UpdateArbeitszeit($id,$adr_id, $vonZeit, $bisZeit, $aufgabe, $beschreibung,$ort, $projekt, $paketauswahl,$art,$kunde="",$abrechnen="0",$verrechnungsart="",$kostenstelle="",$abgerechnet="0",$gps="")
    {
      //Update 

      $tmp = $this->app->DB->SelectArr("SELECT aufgabe, beschreibung, projekt, kostenstelle FROM arbeitspaket WHERE id = $paketauswahl");
      $myArr = $tmp[0];

      if($paketauswahl!=0)
      {
        $kunde = $this->app->DB->Select("SELECT kunde FROM projekt WHERE id='".$myArr["projekt"]."'");
        $projekt = $myArr["projekt"];
      }
      else if($kunde=="NULL")
        $kunde = 0;
      else if($kunde=="")
        $kunde = $this->app->DB->Select("SELECT adresse_abrechnung FROM zeiterfassung WHERE  id='$id'");

      //              if($abrechnen=="")
      //                      $abrechnen = $this->app->DB->Select("SELECT abrechnen FROM zeiterfassung WHERE id='$id'");

      $this->app->DB->Update("UPDATE zeiterfassung SET aufgabe='$aufgabe',adresse='$adr_id',arbeitspaket='$paketauswahl',ort='$ort',beschreibung='$beschreibung', projekt='$projekt',
          von='$vonZeit',bis='$bisZeit',adresse_abrechnung='$kunde',abrechnen='$abrechnen',kostenstelle='$kostenstelle', verrechnungsart='$verrechnungsart', abgerechnet='$abgerechnet', ist_abgerechnet='$abgerechnet',gps='$gps' WHERE id='$id'");      

        // wenn arbeitszeit in arbeistnachweis verwendet wurden ist dann dort auch updaten

        $arbeitsnachweisposid = $this->app->DB->Select("SELECT arbeitsnachweispositionid FROM zeiterfassung WHERE id='$id'");
      if($arbeitsnachweisposid > 0){
        $von = $this->app->DB->Select("SELECT DATE_FORMAT(von,'%H:%i') FROM zeiterfassung WHERE id='$id'");
        $bis = $this->app->DB->Select("SELECT DATE_FORMAT(bis,'%H:%i') FROM zeiterfassung WHERE id='$id'");
        $this->app->DB->Update("UPDATE arbeitsnachweis_position SET bezeichnung='$aufgabe',beschreibung='$beschreibung',ort='$ort', von='$von',bis='$bis',
            adresse='$adr_id' WHERE id='$arbeitsnachweisposid' LIMIT 1");
      }
    }

    function AddArbeitszeit($adr_id, $vonZeit, $bisZeit, $aufgabe, $beschreibung,$ort, $projekt, $paketauswahl,$art,$kunde="",$abrechnen="",$verrechnungsart="",$kostenstelle="",$abgerechnet="0",$gps="")
    {
      $insert = "";
      if($paketauswahl==0){
        if($abrechnen!="1") $abrechnen=0;
        if($projekt<=0) $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$kunde' LIMIT 1");
        if($projekt=="") $projekt=0;
        $insert = 'INSERT INTO zeiterfassung (adresse, von, bis, aufgabe, beschreibung, projekt, buchungsart,art,adresse_abrechnung,abrechnen,gebucht_von_user,ort,kostenstelle,verrechnungsart,abgerechnet,ist_abgerechnet,gps) 
          VALUES ('.$adr_id.',"'.$vonZeit.'","'.$bisZeit.'","'.$aufgabe.'", "'.$beschreibung.'",'.$projekt.', "manuell","'.$art.'","'.$kunde.'","'.$abrechnen.'","'.$this->app->User->GetID().'","'.$ort.'","'.$kostenstelle.'","'.$verrechnungsart.'","'.$abgerechnet.'","'.$abgerechnet.'","'.$gps.'")';
      }else{
        $projekt = $this->app->DB->SelectArr("SELECT aufgabe, beschreibung, projekt, kostenstelle FROM arbeitspaket WHERE id = $paketauswahl");
        $myArr = $projekt[0];

        //if($kunde=="")
        $kunde = $this->app->DB->Select("SELECT kunde FROM projekt WHERE id='".$myArr["projekt"]."'");

        $insert = 'INSERT INTO zeiterfassung (adresse, von, bis, arbeitspaket, aufgabe, beschreibung, projekt, buchungsart,art,gebucht_von_user,ort,adresse_abrechnung,abrechnen,abgerechnet,ist_abgerechnet) VALUES 
          ('.$adr_id.',"'.$vonZeit.'","'.$bisZeit.'",'.$paketauswahl.' , "'.$aufgabe.'", "'.$beschreibung.'",'.$myArr["projekt"].', "AP","'.$art.'","'.$this->app->User->GetID().'","'.$ort.'","'.$kunde.'","'.$abrechnen.'","'.$abgerechnet.'","'.$abgerechnet.'")';
      }
      $this->app->DB->Insert($insert);
      return $this->app->DB->GetInsertID();

      // wenn art=="AP" hole projekt und kostenstelle aus arbeitspaket beschreibung
      // und update zuvor angelegten datensatz
    }


    /**
     * \brief   Anlegen eines Arbeitspakets
     *
     *         Diese Funktion legt ein Arbeitspaket an.
     *
     * \param   aufgabe      Kurzbeschreibung (ein paar Woerter)  
     * \param   beschreibung  Textuelle Beschreibung 
     * \param   projekt      Projekt ID 
     * \param   zeit_geplant  Stundenanzahl Integer Wert
     * \param   kostenstelle  Kostenstelle 
     * \param   initiator            user id des Initiators
     * \param   abgabedatum   Datum fuer Abgabe 
     * \return                Status-Code
     *
     */
    function CreateArbeitspaket($adressse, $aufgabe,$beschreibung,$projekt,$zeit_geplant,$kostenstelle,$initiator,$abgabedatum="")
    {
      if(($abgabe != "") && ($beschreibung != "") && ($projekt != "") && ($zeit_geplant != "") && ($kostenstelle != "") && ($initiator != "")){
        $this->app->DB->Insert('INSERT INTO arbeitspakete                                                                                                                                   (adresse, aufgabe, beschreibung, projekt, zeit_geplant, kostenstelle, initiator, abgabedatum)                                                                VALUES (                                                                                                                                                      '.$adresse.',"'.$aufgabe.'", "'.$beschreibung.'", '.$projekt.', '.$zeit_geplant.','.$kostenstelle.', '.$initiator.',"'.$abgabedatum.'")');
        return 1;
      }else
        return 0;
    }

    function CreateBenutzerVorlage($felder)
    {
      $settings = base64_encode(serialize($felder['settings']));
      $firma = $this->app->User->GetFirma();

      $this->app->DB->Insert("INSERT INTO uservorlage (id,bezeichnung,beschreibung)
          VALUES ('','{$felder['bezeichnung']}', '{$felder['beschreibung']}')");

      $id = $this->app->DB->GetInsertID();

      //standard rechte damit man sich anmelden kann
      $this->app->DB->Update("INSERT INTO uservorlagerights (vorlage, module,action,permission) VALUES ('$id','welcome','login',1)");
      $this->app->DB->Update("INSERT INTO uservorlagerights (vorlage, module,action,permission) VALUES ('$id','welcome','logout',1)");
      $this->app->DB->Update("INSERT INTO uservorlagerights (vorlage, module,action,permission) VALUES ('$id','welcome','start',1)");
      $this->app->DB->Update("INSERT INTO uservorlagerights (vorlage, module,action,permission) VALUES ('$id','welcome','startseite',1)");
      $this->app->DB->Update("INSERT INTO uservorlagerights (vorlage, module,action,permission) VALUES ('$id','welcome','settings',1)");
      return $id;
    }


    function CreateBenutzer($felder)
    {
      $settings = base64_encode(serialize($felder['settings']));
      $firma = $this->app->User->GetFirma();

      $this->app->DB->Insert("INSERT INTO user (username, passwordmd5,password, description, settings, parentuser,activ, type, adresse, fehllogins, standarddrucker, 
        startseite, hwtoken, hwkey, hwcounter, hwdatablock, motppin, motpsecret, externlogin, gpsstechuhr, firma,kalender_passwort,kalender_aktiv)
          VALUES ('{$felder['username']}', MD5('{$felder['password']}'), ENCRYPT('{$felder['password']}'), '{$felder['description']}', '{$settings}', '0','{$felder['activ']}', 
            '{$felder['type']}', '{$felder['adresse']}', '{$felder['fehllogins']}', '{$felder['standarddrucker']}', '{$felder['startseite']}',
            '{$felder['hwtoken']}', '{$felder['hwkey']}', '{$felder['hwcounter']}','{$felder['hwdatablock']}', '{$felder['motppin']}', '{$felder['motpsecret']}', 
            '{$felder['externlogin']}','{$felder['gpsstechuhr']}', '$firma','{$felder['kalender_passwort']}','{$felder['kalender_aktiv']}')");

      $id = $this->app->DB->GetInsertID();

      //standard rechte damit man sich anmelden kann
      $this->app->DB->Update("INSERT INTO userrights (user, module,action,permission) VALUES ('$id','welcome','login',1)");
      $this->app->DB->Update("INSERT INTO userrights (user, module,action,permission) VALUES ('$id','welcome','logout',1)");
      $this->app->DB->Update("INSERT INTO userrights (user, module,action,permission) VALUES ('$id','welcome','start',1)");
      $this->app->DB->Update("INSERT INTO userrights (user, module,action,permission) VALUES ('$id','welcome','startseite',1)");
      $this->app->DB->Update("INSERT INTO userrights (user, module,action,permission) VALUES ('$id','welcome','settings',1)");
      return $id;
    }


    function IsAdresseInGruppe($adresse,$gruppe)
    {

      $check = $this->app->DB->Select("SELECT a.parameter FROM adresse_rolle a WHERE 
          (a.bis='0000-00-00' OR a.bis <=NOW()) AND a.adresse='$adresse' AND a.parameter='$gruppe' AND a.objekt='Gruppe' LIMIT 1");

      if(($check == $gruppe) && $gruppe > 0)
        return true;
      else 
        return false;
    }

    function IsAdresseSubjekt($adresse,$subjekt)
    {
      $id = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE adresse='$adresse' AND subjekt='$subjekt' LIMIT 1");  
      if($id > 0)
        return 1;
      else return 0;
    }

    function AddOffenenVorgang($adresse, $titel, $href, $beschriftung="", $linkremove="")
    {
      $sql = "INSERT INTO offenevorgaenge (id,adresse,titel,href,beschriftung,linkremove) VALUES
        ('','$adresse','$titel','$href','$beschriftung','$linkremove')";
      $this->app->DB->Insert($sql);
    }


    function RenameOffenenVorgangID($id,$titel)
    {
      $sql = "UPDATE offenevorgaenge SET titel='$titel' WHERE id='$id' LIMIT 1";
      $this->app->DB->Update($sql);
    }

    function RemoveOffenenVorgangID($id)
    {
      $sql = "DELETE FROM offenevorgaenge WHERE id='$id' LIMIT 1";
      $this->app->DB->Delete($sql);
    }



    function GetNextNummer($type,$projekt="")
    {
      $checkprojekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id='$projekt' LIMIT 1");

      if($checkprojekt > 0)
        $eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id='$projekt' LIMIT 1");

      if($eigenernummernkreis=="1")
      {
        if($type=="angebot" || $type=="auftrag" || $type=="lieferschein" || $type=="rechnung" || $type=="gutschrift" || $type=="bestellung" || $type=="arbeitsnachweis"
            || $type=="reisekosten" || $type=="produktion" || $type=="anfrage" || $type=="kundennummer" || $type=="mitarbeiternummer" || $type=="lieferantennummer")
        {
          $belegnr = $this->app->DB->Select("SELECT next_$type FROM projekt WHERE id='$projekt' LIMIT 1");
          // neue nummer speichern
          $newbelegnr = $belegnr+1;
          $this->app->DB->Update("UPDATE projekt SET next_$type='$newbelegnr' WHERE id='$projekt' LIMIT 1");
        }

      } else {
        // patch fuer uebergangszeit kann ca. im Oktober 2014 entfernt werden
        switch($type) {
          case "kundennummer":
          case "mitarbeiternummer":
          case "lieferantennummer":
            $belegnr = $this->app->DB->Select("SELECT MAX($type) FROM adresse");
            break;
          default:
            $belegnr = $this->app->DB->Select("SELECT MAX(belegnr) FROM $type WHERE firma='".$this->app->User->GetFirma()."'");
        }
        if($this->Firmendaten("next_$type")=="" && $belegnr!="" && $eigenernummernkreis!="1")
          $this->FirmendatenSet("next_$type",$belegnr + 1);
        // ende patch

        // naechste     
        switch($type)
        {
          case "angebot":
            $belegnr = $this->Firmendaten("next_angebot");
            if($belegnr <= 1) $belegnr = 100000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "auftrag":
            $belegnr = $this->Firmendaten("next_auftrag");
            if($belegnr <= 1) $belegnr = 200000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "rechnung":
            $belegnr = $this->Firmendaten("next_rechnung");
            if($belegnr <= 1) $belegnr = 400000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "gutschrift":
            $belegnr = $this->Firmendaten("next_gutschrift");
            if($belegnr <= 1) $belegnr = 900000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "lieferschein":
            $belegnr = $this->Firmendaten("next_lieferschein");
            if($belegnr <= 1) $belegnr = 300000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "bestellung":
            $belegnr = $this->Firmendaten("next_bestellung");
            if($belegnr <= 1) $belegnr = 100000;
            $newbelegnr = $belegnr + 1;
            break;
          case "arbeitsnachweis":
            $belegnr = $this->Firmendaten("next_arbeitsnachweis");
            if($belegnr <= 1) $belegnr = 300000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "anfrage":
            $belegnr = $this->Firmendaten("next_anfrage");
            if($belegnr <= 1) $belegnr = 300000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "reisekosten":
            $belegnr = $this->Firmendaten("next_reisekosten");
            if($belegnr <= 1) $belegnr = 300000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "produktion":
            $belegnr = $this->Firmendaten("next_produktion");
            if($belegnr <= 1) $belegnr = 300000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "kundennummer":
            $belegnr = $this->Firmendaten("next_kundennummer");
            if($belegnr <= 1) $belegnr = 10000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "lieferantennummer":
            $belegnr = $this->Firmendaten("next_lieferantennummer");
            if($belegnr <= 1) $belegnr = 70000; 
            $newbelegnr = $belegnr + 1;
            break;
          case "mitarbeiternummer":
            $belegnr = $this->Firmendaten("next_mitarbeiternummer");
            if($belegnr <= 0) $belegnr = 90000; 
            $newbelegnr = $belegnr + 1;
            break;


          default: $begelnr="Fehler";
        }

        $this->FirmendatenSet("next_$type",$newbelegnr);
      }
      return $belegnr;
    }


    function GetNextArtikelnummer($artikelart="",$firma="1",$projekt="")
    {
      // neue artikel nummer holen
      //if($firma=="") $firma = $this->app->User->GetFirma();

      $check = str_replace("_kat","",$artikelart);
      $check = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE id='$check' AND geloescht!=1 LIMIT 1");

      if($check > 0)
      {
        $next_nummer_alt = $this->app->DB->Select("SELECT next_nummer FROM artikelkategorien WHERE id='$check' AND geloescht!=1");
        $externenummer = $this->app->DB->Select("SELECT externenummer FROM artikelkategorien WHERE id='$check' AND geloescht!=1");

        if($externenummer!="1")
        {
          if($next_nummer_alt=="") $next_nummer_alt = 100000;

          $neue_nummer = $next_nummer_alt;

          $nurbuchstaben = preg_replace("/[^a-zA-Z]/","",$next_nummer_alt);
          $nurzahlen = preg_replace("/[^0-9]/","",$next_nummer_alt);
          $laenge = strlen($nurzahlen);

          $next_nummer = $nurbuchstaben.str_pad($nurzahlen+1, $laenge  ,'0', STR_PAD_LEFT); 

          $this->app->DB->Update("UPDATE artikelkategorien SET next_nummer='$next_nummer' WHERE id='$check' AND geloescht!=1");
        } else {
          // externe nummer holen
          // TODO pruefen ob es im Projekt ueberladen gehoert
          $eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id='$projekt' LIMIT 1");
          if($eigenernummernkreis=="1")
          {
            $next_nummer = $this->app->DB->Select("SELECT next_artikelnummer FROM projekt WHERE id='$projekt' LIMIT 1");          
            $this->app->DB->Update("UPDATE projekt SET next_artikelnummer=next_artikelnummer+1 WHERE id='$projekt' LIMIT 1");
          } else {
            $next_nummer = $this->Firmendaten("next_artikelnummer");
            $this->FirmendatenSet("next_artikelnummer",$next_nummer+1);
          }

          $nurbuchstaben = preg_replace("/[^a-zA-Z]/","",$next_nummer_alt);
          $neue_nummer = $nurbuchstaben.$next_nummer;
        }
      } else {
        switch($artikelart)
        {
        case "produkt":
          //$neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE firma='".$firma."' AND nummer LIKE '7%'");
          $neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE nummer LIKE '7%'");
          if($neue_nummer=="" || $neue_nummer==0) $neue_nummer = "700000";
          break;
        case "produktion":
          //$neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE firma='".$firma."' AND nummer LIKE '4%'");
          $neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE nummer LIKE '4%'");
          if($neue_nummer=="" || $neue_nummer==0) $neue_nummer = "400000";
          break;
        case "module":
          //$neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE firma='".$firma."' AND nummer LIKE '6%'");
          $neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE nummer LIKE '6%'");
          if($neue_nummer=="" || $neue_nummer==0) $neue_nummer = "600000";
          break;
        default:
          //$neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE firma='".$firma."' AND nummer LIKE '1%'");
          $neue_nummer = $this->app->DB->Select("SELECT MAX(nummer) FROM artikel WHERE nummer LIKE '1%'");
          if(($neue_nummer=="" || $neue_nummer==0)) $neue_nummer = "100000";
        }
        $neue_nummer = $neue_nummer + 1;
      }
      return $neue_nummer;

    }

    function GetNextMitarbeiternummer($projekt="")
    {
      return $this->GetNextNummer("mitarbeiternummer",$projekt);
      /*
         $sql = "SELECT MAX(mitarbeiternummer) FROM adresse WHERE geloescht=0";
         $nummer = $this->app->DB->Select($sql) + 1;
         if($nummer==1)
         $nummer = 90000;
         return $nummer;
       */
    }


    function GetNextKundennummer($projekt="")
    {
      return $this->GetNextNummer("kundennummer",$projekt);
      /*
         $sql = "SELECT MAX(kundennummer) FROM adresse WHERE geloescht=0";
         $nummer = $this->app->DB->Select($sql) + 1;
         if($nummer==1)
         $nummer = 10000;
         return $nummer;
       */
    }

    function GetNextLieferantennummer($projekt="")
    {
      return $this->GetNextNummer("lieferantennummer",$projekt);
      /*
         $sql = "SELECT MAX(lieferantennummer) FROM adresse WHERE geloescht=0";
         $nummer = $this->app->DB->Select($sql) + 1;
         if($nummer==1)
         $nummer = 70000;
         return $nummer;
       */
    }



    function ArbeitsnachweisProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO arbeitsnachweis_protokoll (id,arbeitsnachweis,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }


    function ReisekostenProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO reisekosten_protokoll (id,reisekosten,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }


    function AnfrageProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO anfrage_protokoll (id,anfrage,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }


    function InventurProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO inventur_protokoll (id,inventur,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }

    function LieferscheinProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO lieferschein_protokoll (id,lieferschein,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }

    function ProduktionProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO produktion_protokoll (id,auftrag,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }


    function AuftragProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO auftrag_protokoll (id,auftrag,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }

    function AngebotProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO angebot_protokoll (id,angebot,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }

    function BestellungProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO bestellung_protokoll (id,bestellung,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }

    function RechnungProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO rechnung_protokoll (id,rechnung,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }

    function GutschriftProtokoll($id,$text)
    {
      $this->app->DB->Insert("INSERT INTO gutschrift_protokoll (id,gutschrift,zeit,bearbeiter,grund) VALUES
          ('','$id',NOW(),'".$this->app->User->GetName()."','$text')"); 
    }


    function LoadArbeitsnachweisStandardwerte($id,$adresse,$projekt="")
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','typ');
      foreach($field as $key=>$value)
      {

        if($value=="projekt" && $this->app->Secure->POST[$value]!="")
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }

        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }

      if($projekt!="") {
        $this->app->Secure->POST[projekt] = $projekt;
        $uparr[projekt] = $this->app->Secure->POST[projekt];
      } 

      $uparr[adresse]=$adresse;
      $this->app->DB->UpdateArr("arbeitsnachweis",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
      $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');
      foreach($field as $key=>$value)
      {
        //$uparr[$value] = $arr[0][$value];
        $this->app->Secure->POST[$value] = $arr[0][$value];
      }

      $this->app->DB->UpdateArr("arbeitsnachweis",$id,"id",$uparr);

      //standardprojekt
      //$projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      //$this->app->Secure->POST[projekt] = $projekt;


    }


    function LoadInventurStandardwerte($id,$adresse,$projekt="")
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $field = array('name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit');
      foreach($field as $key=>$value)
      {

        if($value=="projekt" && $this->app->Secure->POST[$value]!="")
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }

        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }

      if($projekt!="") {
        $this->app->Secure->POST[projekt] = $projekt;
        $uparr[projekt] = $this->app->Secure->POST[projekt];
      } 

      $uparr[adresse]=$adresse;
      $this->app->DB->UpdateArr("inventur",$id,"id",$uparr);
      $uparr="";


    }

    function KundeHatZR($adresse)
    {
      $verband = $this->GetVerband($adresse);
      $zr = $this->app->DB->Select("SELECT zentralregulierung FROM gruppen WHERE id='$verband' LIMIT 1");

      if($zr=="1")
        return true;
      else                    
        return false;   
    }       

    function GetVerbandName($gruppe)
    {
      return $this->app->DB->Select("SELECT CONCAT(kennziffer,' ',name) FROM gruppen WHERE id='$gruppe' LIMIT 1");
    }

    function GetVerband($adresse)
    {
      $verband = $this->app->DB->Select("SELECT g.id FROM adresse_rolle a LEFT JOIN gruppen g ON g.id=a.parameter WHERE 
          (a.bis='0000-00-00' OR a.bis >=NOW()) AND a.adresse='$adresse' AND a.objekt='Gruppe' AND g.art='verband' LIMIT 1");
      return $verband;
    }

    function LoadAnfrageStandardwerte($id,$adresse,$projekt="")
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT *,vertrieb as vertriebid FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

      $arr[0]['gruppe'] = $this->GetVerband($adresse);
      //$field = array('name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit');
      $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','gruppe','typ','vertriebid');
      foreach($field as $key=>$value)
      {

        if($value=="projekt" && $this->app->Secure->POST[$value]!="")
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }
      }

      if($projekt!="") {
        $this->app->Secure->POST[projekt] = $projekt;
        $uparr[projekt] = $this->app->Secure->POST[projekt];
      } 

      $uparr[adresse]=$adresse;
      $this->app->DB->UpdateArr("anfrage",$id,"id",$uparr);
      $uparr="";
    }


    function LoadReisekostenStandardwerte($id,$adresse,$projekt="")
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $field = array('name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','typ');
      foreach($field as $key=>$value)
      {

        if($value=="projekt" && $this->app->Secure->POST[$value]!="")
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }

        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }

      if($projekt!="") {
        $this->app->Secure->POST[projekt] = $projekt;
        $uparr[projekt] = $this->app->Secure->POST[projekt];
      } 

      $uparr[adresse]=$adresse;
      $this->app->DB->UpdateArr("reisekosten",$id,"id",$uparr);
      $uparr="";


    }



    function LoadLieferscheinStandardwerte($id,$adresse,$lieferantenretoure=false)
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','typ');


      foreach($field as $key=>$value)
      {
        if($value=="projekt" && $this->app->Secure->POST[$value]!="")
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }

        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }
      $uparr[adresse]=$adresse;

      if($lieferantenretoure) $uparr['lieferant']=$adresse;

      $this->app->DB->UpdateArr("lieferschein",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");

      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {      
        $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        $arr[0]['projekt'] = $uparr['projekt'];
        $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
      }

      $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');
      foreach($field as $key=>$value)
      {
        //$uparr[$value] = $arr[0][$value];
        $this->app->Secure->POST[$value] = $arr[0][$value];
      }
      $this->app->DB->UpdateArr("lieferschein",$id,"id",$uparr);

      $this->LoadStandardLieferadresse($adresse,$id,"lieferschein");
    }


    function LoadProduktionStandardwerte($id,$adresse)
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $field = array('name','vorname','abteilung','ansprechpartner','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','typ');

      $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse'   AND subjekt='Kunde' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

      if($rolle_projekt > 0)
      {
        $arr[0]['projekt'] = $rolle_projekt;
      }


      foreach($field as $key=>$value)
      {
        if($value=="projekt" && $this->app->Secure->POST[$value]!="" && 0) // immer projekt von adresse
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }

        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }
      $uparr[adresse]=$adresse;
      $this->app->DB->UpdateArr("produktion",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
      $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');

      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      { 
        $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        $arr[0]['projekt'] = $uparr['projekt'];
        $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
      }

      if($arr[0]['zahlungsweise']=="" && $this->app->Secure->POST['zahlungsweise']=="")
        $arr[0]['zahlungsweise']="rechnung";
      else if ($this->app->Secure->GetPOST("zahlungsweise")!="")
        $arr[0]['zahlungsweise'] = $this->app->Secure->GetPOST("zahlungsweise");


      if($arr[0]['zahlungszieltage']<=0 && $this->app->Secure->POST['zahlungszieltage']=="")
        $arr[0]['zahlungszieltage']=$this->ZahlungsZielTage();

      else if ($this->app->Secure->POST['zahlungszieltage']!="")
        $arr[0]['zahlungszieltage']=$this->app->Secure->POST['zahlungszieltage'];


      if($arr[0]['versandart']=="" && $this->app->Secure->POST['versandart']=="")
        $arr[0]['versandart']="versandunternehmen";
      else if ($this->app->Secure->POST['versandart']!="")
        $arr[0]['versandart']=$this->app->Secure->POST['versandart'];


      $this->app->Secure->POST['zahlungsweise'] = strtolower($arr[0]['zahlungsweise']);
      $this->app->Secure->POST['zahlungszieltage'] = strtolower($arr[0]['zahlungszieltage']);
      $this->app->Secure->POST['zahlungszieltageskonto'] = strtolower($arr[0]['zahlungszieltageskonto']);
      $this->app->Secure->POST['zahlungszielskonto'] = strtolower($arr[0]['zahlungszielskonto']);
      $this->app->Secure->POST['versandart'] = strtolower($arr[0]['versandart']);


      //if($zahlarr[zahlungsweise]=="") $zahlarr[zahlungsweise]="rechnung";
      //if($zahlarr[zahlungszieltage]=="") $zahlarr[zahlungszieltage]="14";

      //standardprojekt
      /*$projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
        if($projekt=="") $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");

        $this->app->Secure->POST['projekt'] = $projekt;
        $arr[0]['projekt'] = $projekt;*/

      $this->app->DB->UpdateArr("produktion",$id,"id",$arr[0]);
    }


    function InfoAuftragsErfassung($modul,$id)
    {
      $status = $this->app->DB->Select("SELECT status FROM $modul WHERE id='$id' LIMIT 1");
      $adresse = $this->app->DB->Select("SELECT adresse FROM $modul WHERE id='$id' LIMIT 1");
      if($status=="angelegt" || $status=="freigegeben")
      {
        $infoauftragserfassung = $this->app->DB->Select("SELECT infoauftragserfassung FROM adresse WHERE id='$adresse' LIMIT 1");
        if($infoauftragserfassung!="")
        {
          $this->app->Tpl->Set(INFOFUERAUFTRAGSERFASSUNG,"<table width=100% height=100%><tr><td><fieldset><legend>Info f&uuml;r Angebots- und Auftragserfassung</legend>
              <textarea id=\"readonlybox\" rows=12>$infoauftragserfassung</textarea></fieldset></td></tr></table>");
        } else {

          $this->app->Tpl->Set(INFOFUERAUFTRAGSERFASSUNG,"");
        }
      }


    }

    function MarkerUseredit($feld1,$useredittimestamp)
    {
      //    return "CONCAT($feld1,' ',if(TIME_TO_SEC(TIMEDIFF(NOW(), $useredittimestamp)) < 6,'<br><font color=red><b>(in Bearbeitung ',
      //(SELECT a2.name FROM user u2 LEFT JOIN adresse a2 ON a2.id=u2.adresse WHERE u2.id='$usereditid'),')</b></font>',''))";
      $usereditid = str_replace("useredittimestamp","usereditid",$useredittimestamp);
      return "CONCAT($feld1,' ',if(TIME_TO_SEC(TIMEDIFF(NOW(), $useredittimestamp)) < 45,CONCAT('<br><font color=red><b>(in Bearbeitung von ',      (SELECT a2.name FROM user u2 LEFT JOIN adresse a2 ON a2.id=u2.adresse WHERE u2.id=$usereditid LIMIT 1),')</b></font>'),''))";
    }

    function TimeoutUseredit($smodule,$sid,$user)
    {
      $useredittimestamp = $this->app->DB->Select("SELECT useredittimestamp FROM $smodule WHERE id='$sid' LIMIT 1");
      if($useredittimestamp=="0000-00-00 00:00:00" || $useredittimestamp=="")
      {
        $this->app->DB->Select("UPDATE $smodule SET useredittimestamp=NOW(),usereditid='".$user."' WHERE id='$sid' LIMIT 1");
      }

      // nur wenn timediff > 10 

      $timediff = $this->app->DB->Select("SELECT TIME_TO_SEC(TIMEDIFF(NOW(), useredittimestamp)) FROM $smodule WHERE id='$sid' LIMIT 1");
      $timeuser = $this->app->DB->Select("SELECT usereditid FROM $smodule WHERE id='$sid' LIMIT 1");
      if($timeuser == $user)
      {
        $this->app->DB->Select("UPDATE $smodule SET useredittimestamp=NOW() WHERE id='$sid' LIMIT 1");
      } else
      {
        if($timediff>30)
          $this->app->DB->Select("UPDATE $smodule SET useredittimestamp=NOW(),usereditid='$user' WHERE id='$sid' LIMIT 1");
      }
    }

    function DisableModul($modul,$id)
    {
      $user = $this->app->DB->Select("SELECT usereditid FROM $modul WHERE id='$id' LIMIT 1");
      $user_adresse = $this->app->DB->Select("SELECT adresse FROM user WHERE id='$user' LIMIT 1");
      $user_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$user_adresse' LIMIT 1");

      $this->TimeoutUseredit($modul,$id,$this->app->User->GetID());

      $timeuser = $this->app->DB->Select("SELECT usereditid FROM $modul WHERE id='$id' LIMIT 1");

      if($timeuser==$this->app->User->GetID())
      {
        return false;
      } else {

        if($this->RechteVorhanden("welcome","unlock"))
        {
          $id = $this->app->Secure->GetGET("id");
          $open = "<input type=\"button\" value=\"Dokument &uuml;bernehmen\" onclick=\"if(!confirm('Soll diese Oberfl&auml;che wirklich &uuml;bernommen werden? Alle Aktionen werden bei dem angemeldeten Mitarbeiter abgebrochen.')) return false;else window.location.href='index.php?module=welcome&action=unlock&id=$id&gui=$modul';\">";
        }

        $this->app->Tpl->Set(TAB1,"<div class=\"error\">Achtung dieses Dokument wird aktuell durch Mitarbeiter: <b>$user_name</b> bearbeitet! $open</div>");  
        $this->app->Tpl->Parse(PAGE,"tabview.tpl");
        return true;
      }
    }       


    function LoadStandardLieferadresse($adresse,$id,$type)
    {
      $standardlieferadresse = $this->app->DB->SelectArr("SELECT * FROM lieferadressen WHERE adresse='$adresse' AND standardlieferadresse='1' LIMIT 1");

      if($standardlieferadresse[0][id] > 0)
      {
        switch($type)
        {
          case "angebot":
          case "auftrag":
            $this->app->DB->Update("UPDATE $type SET abweichendelieferadresse='1', 
                liefername='".$standardlieferadresse[0]['name']."', 
                lieferabteilung='".$standardlieferadresse[0]['abteilung']."', 
                lieferunterabteilung='".$standardlieferadresse[0]['unterabteilung']."',
                lieferland='".$standardlieferadresse[0]['land']."',
                lieferstrasse='".$standardlieferadresse[0]['strasse']."',
                lieferort='".$standardlieferadresse[0]['ort']."',
                lieferplz='".$standardlieferadresse[0]['plz']."',
                lieferadresszusatz='".$standardlieferadresse[0]['adresszusatz']."',
                lieferansprechpartner='".$standardlieferadresse[0]['ansprechpartner']."' 
                WHERE id='$id' LIMIT 1");
            break;
          case "lieferschein":
            $this->app->DB->Update("UPDATE lieferschein SET 
                name='".$standardlieferadresse[0]['name']."', 
                abteilung='".$standardlieferadresse[0]['abteilung']."', 
                unterabteilung='".$standardlieferadresse[0]['unterabteilung']."',
                land='".$standardlieferadresse[0]['land']."',
                strasse='".$standardlieferadresse[0]['strasse']."',
                ort='".$standardlieferadresse[0]['ort']."',
                plz='".$standardlieferadresse[0]['plz']."',
                adresszusatz='".$standardlieferadresse[0]['adresszusatz']."',
                ansprechpartner='".$standardlieferadresse[0]['ansprechpartner']."' 
                WHERE id='$id' LIMIT 1");
            break;

        }
      }
    }

    function DisableVerband()
    {
      $module = $this->app->Secure->GetGET("module");
      $id = $this->app->Secure->GetGET("id");
      if($module=="angebot" || $module=="auftrag" || $module=="rechnung" || $module=="gutschrift")
      {
        /*
           $rabatt = $this->app->DB->Select("SELECT rabatt FROM $module WHERE id='$id' LIMIT 1");
           $this->app->Tpl->Set(RABATT,"<input type=\"text\" value=\"$rabatt\" size=\"4\" readonly>");
           for($i=1;$i<=5;$i++)
           {
           $rabatt = $this->app->DB->Select("SELECT rabatt$i FROM $module WHERE id='$id' LIMIT 1");
           $this->app->Tpl->Set("RABATT".$i,"<input type=\"text\" value=\"$rabatt\" size=\"4\" readonly>");
           }
         */
        $rabatt = $this->app->DB->Select("SELECT realrabatt FROM $module b LEFT JOIN adresse a ON a.id=b.adresse WHERE b.id='$id' LIMIT 1");
        $gruppe = $this->app->DB->Select("SELECT gruppe FROM $module b WHERE b.id='$id' LIMIT 1");

        $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
        $rabatte_festschreiben = $this->app->DB->Select("SELECT rabatte_festschreiben FROM adresse WHERE id='".$adresse."' LIMIT 1");


        if(($rabatt > 0 || $gruppe > 0) && $this->Firmendaten("modul_verband")=="1")
        {
          if($rabatte_festschreiben=="1" || $gruppe<=0)
            $this->app->Tpl->Set(RABATTANZEIGE," <br><font color=red>Kundenrabatt: $rabatt%</font>");
          else
            $this->app->Tpl->Set(RABATTANZEIGE," <br><font color=red>Verbandsrabatt: $rabatt%</font>");
        } else {
          $this->app->Tpl->Set(STARTDISABLEVERBAND,"<!--");
          $this->app->Tpl->Set(ENDEDISABLEVERBAND,"-->");
        }       

        if(($rabatte_festschreiben!="1" && $gruppe > 0) || $gruppe > 0) // ANZEIGE VERBAND wenn definiert
        {
          $gruppe = $this->app->DB->Select("SELECT gruppe FROM $module WHERE id='$id' LIMIT 1");
          $gruppe_name = $this->app->DB->Select("SELECT CONCAT(name,' ',kennziffer) FROM gruppen WHERE id='$gruppe' LIMIT 1");
          $gruppeinternebemerkung = $this->app->DB->Select("SELECT internebemerkung FROM gruppen WHERE id='$gruppe' LIMIT 1");
          //$gruppeinternebemerkung = strip_tags(html_entity_decode($gruppeinternebemerkung));    
          $this->app->Tpl->Set(VERBANDINFO,"<textarea cols=\"80\" rows=\"15\" id=\"readonlybox2\">$gruppeinternebemerkung</textarea>");
        }  else {
          $rabattinformation = $this->app->DB->Select("SELECT rabattinformation FROM adresse WHERE id='$adresse' LIMIT 1");
          //$rabattinformation = strip_tags(html_entity_decode($rabattinformation));      
          $this->app->Tpl->Set(VERBANDINFO,"<textarea cols=\"80\" rows=\"15\" id=\"readonlybox2\">$rabattinformation</textarea>");
        }

        if($this->RechteVorhanden($module,"deleterabatte"))
        {
          $this->app->Tpl->Set(VERBAND,"<input type=\"text\" value=\"$gruppe_name\" size=\"38\" readonly>&nbsp;<input type=\"button\" 
              onclick=\"if(!confirm('Wirklich die Verbandsinformation neu laden?')) return false; else window.location.href='index.php?module=".$module."&action=updateverband&id=".$id."';\" 
              value=\"Verband neu laden\">");
        } else {
          $this->app->Tpl->Set(VERBAND,"<input type=\"text\" value=\"$gruppe_name\" size=\"38\" readonly>");
        }
      }
    }


    function LoadAuftragStandardwerte($id,$adresse)
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT *,vertrieb as vertriebid,'' as bearbeiter FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

      $arr[0]['gruppe'] = $this->GetVerband($adresse);
      $field = array('anschreiben','name','vorname','abteilung','ansprechpartner','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','gruppe','typ','vertriebid','bearbeiter');


      $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse'   AND subjekt='Kunde' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

      if($rolle_projekt > 0)
      {
        $arr[0]['projekt'] = $rolle_projekt;
      }


      foreach($field as $key=>$value)
      {
        if($value=="projekt" && $this->app->Secure->POST[$value]!="" && 0) // immer projekt von adresse
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }

        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }


      $uparr[adresse]=$adresse;
      $this->app->DB->UpdateArr("auftrag",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");

      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");      
      if($projekt_bevorzugt=="1")
      {
        $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");        
        $arr[0]['projekt'] = $uparr['projekt'];
        $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
      }

      $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');

      if($arr[0]['zahlungsweise']=="")
        $arr[0]['zahlungsweise']="vorkasse";

      if($arr[0]['zahlungszieltage']<=0)
        $arr[0]['zahlungszieltage']=$this->ZahlungsZielTage();
      if($arr[0]['zahlungszieltageskonto']<=0)
        $arr[0]['zahlungszieltageskonto']=$this->ZahlungsZielTageSkonto();

      if($arr[0]['zahlungszielskonto']<=0)
        $arr[0]['zahlungszielskonto']=$this->ZahlungsZielSkonto();

      if($arr[0]['versandart']=="")
        $arr[0]['versandart']="versandunternehmen";

      $this->app->Secure->POST['zahlungsweise'] = strtolower($arr[0]['zahlungsweise']);
      $this->app->Secure->POST['zahlungszieltage'] = strtolower($arr[0]['zahlungszieltage']);
      $this->app->Secure->POST['zahlungszieltageskonto'] = strtolower($arr[0]['zahlungszieltageskonto']);
      $this->app->Secure->POST['zahlungszielskonto'] = strtolower($arr[0]['zahlungszielskonto']);
      $this->app->Secure->POST['versandart'] = strtolower($arr[0]['versandart']);

      $this->LoadStandardLieferadresse($adresse,$id,"auftrag");

      $this->app->DB->UpdateArr("auftrag",$id,"id",$arr[0]);
      $this->RabatteLaden($id,"auftrag",$adresse);
      $this->VerbandZahlungsoptionenLaden($id,"auftrag",$adresse);
      $this->LoadSteuersaetzeWaehrung($id,"auftrag");
    }



    function LoadAngebotStandardwerte($id,$adresse)
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT *,vertrieb as vertriebid,'' as bearbeiter FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $arr[0]['gruppe'] = $this->GetVerband($adresse);

      $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse' AND subjekt='Kunde' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

      if($rolle_projekt > 0)
      {
        $arr[0]['projekt'] = $rolle_projekt;
      }

      $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','ust_befreit','email','telefon','telefax','projekt','ansprechpartner','gruppe','typ','vertriebid','bearbeiter');
      foreach($field as $key=>$value)
      {
        if($value=="projekt" && $this->app->Secure->POST[$value]!="" && 0)
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }
      }
      $uparr['adresse'] = $adresse;
      $this->app->DB->UpdateArr("angebot",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
      $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');


      if($arr[0]['zahlungsweise']=="")
        $arr[0]['zahlungsweise']="rechnung";

      if($arr[0]['zahlungszieltage']<=0)
        $arr[0]['zahlungszieltage']=$this->ZahlungsZielTage();

      if($arr[0]['zahlungszieltageskonto']<=0)
        $arr[0]['zahlungszieltageskonto']=$this->ZahlungsZielTageSkonto();

      if($arr[0]['zahlungszielskonto']<=0)
        $arr[0]['zahlungszielskonto']=$this->ZahlungsZielSkonto();



      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");      
      if($projekt_bevorzugt=="1")
      {
        $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");        
        $arr[0]['projekt'] = $uparr['projekt'];
        $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
      }



      $this->app->Secure->POST['zahlungsweise'] = strtolower($arr[0]['zahlungsweise']);
      $this->app->Secure->POST['zahlungszieltage'] = strtolower($arr[0]['zahlungszieltage']);
      $this->app->Secure->POST['zahlungszieltageskonto'] = strtolower($arr[0]['zahlungszieltageskonto']);
      $this->app->Secure->POST['zahlungszielskonto'] = strtolower($arr[0]['zahlungszielskonto']);
      $this->app->Secure->POST['versandart'] = strtolower($arr[0]['versandart']);
      /*
         foreach($field as $key=>$value)
         {
         $uparr[$value] = $arr[0][$value];
         $this->app->Secure->POST[$value] = $arr[0][$value];
         }
       */
      $this->app->DB->UpdateArr("angebot",$id,"id",$arr[0]);
      $this->LoadStandardLieferadresse($adresse,$id,"angebot");

      $this->LoadSteuersaetzeWaehrung($id,"angebot");
      $this->RabatteLaden($id,"angebot",$adresse);
      $this->VerbandZahlungsoptionenLaden($id,"angebot",$adresse);

      //standardprojekt
      //$projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      //$this->app->Secure->POST[projekt] = $projekt;
    }

    function LoadGutschriftStandardwerte($id,$adresse)
    {
      if($id==0 || $id=="" || $adresse=="" || $adresse=="0") return;

      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT *,vertrieb as vertriebid,'' as bearbeiter FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

      $arr[0]['gruppe'] = $this->GetVerband($adresse);

      $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse' 
          AND subjekt='Kunde' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

      if($rolle_projekt > 0)
      {
        $arr[0]['projekt'] = $rolle_projekt;
      }

      $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','gruppe','typ','vertriebid','bearbeiter');
      foreach($field as $key=>$value)
      {
        if($value=="projekt" && $this->app->Secure->POST[$value]!="" && 0)
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }


        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }

      $uparr[adresse] = $adresse;
      $uparr[ust_befreit] = $this->AdresseUSTCheck($adresse);
      $uparr[zahlungsstatusstatus]="offen";

      $this->app->DB->UpdateArr("gutschrift",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
      $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');

      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      { 
        $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        $arr[0]['projekt'] = $uparr['projekt'];
        $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
      }

      if($arr[0]['zahlungsweise']=="")
        $arr[0]['zahlungsweise']="rechnung";

      if($arr[0]['zahlungszieltage']<=0)
        $arr[0]['zahlungszieltage']=$this->ZahlungsZielTage();

      if($arr[0]['zahlungszieltageskonto']<=0)
        $arr[0]['zahlungszieltageskonto']=$this->ZahlungsZielTageSkonto();

      if($arr[0]['zahlungszielskonto']<=0)
        $arr[0]['zahlungszielskonto']=$this->ZahlungsZielSkonto();

      $this->app->Secure->POST['zahlungsweise'] = strtolower($arr[0]['zahlungsweise']);
      $this->app->Secure->POST['zahlungszieltage'] = strtolower($arr[0]['zahlungszieltage']);
      $this->app->Secure->POST['zahlungszieltageskonto'] = strtolower($arr[0]['zahlungszieltageskonto']);
      $this->app->Secure->POST['zahlungszielskonto'] = strtolower($arr[0]['zahlungszielskonto']);
      $this->app->Secure->POST['versandart'] = strtolower($arr[0]['versandart']);
      /*
         foreach($field as $key=>$value)
         {
         $uparr[$value] = $arr[0][$value];
         $this->app->Secure->POST[$value] = $arr[0][$value];
         }
       */
      $this->app->DB->UpdateArr("gutschrift",$id,"id",$arr[0]);
      $this->LoadSteuersaetzeWaehrung($id,"gutschrift");
      $this->RabatteLaden($id,"gutschrift",$adresse);
      $this->VerbandZahlungsoptionenLaden($id,"gutschrift",$adresse);
    }

    function BerechneProvision($id,$typ)
    {

      // mit rabatt beruecksichtigen
      if($typ=="rechnung" || $typ=="gutschrift")
      {
        //      $einnahmen = $this->ANABREGSNeuberechnenGesamtsumme($id,$typ,true);
        $einnahmen = $this->app->DB->Select("SELECT SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1))) 
            FROM $typ auf LEFT JOIN {$typ}_position ap ON ap.{$typ}=auf.id LEFT JOIN artikel a ON ap.artikel=a.id WHERE auf.id=$id");

        $provision_summe = $einnahmen / 100;

        $this->app->DB->Update("UPDATE $typ SET provision_summe=$provision_summe*provision WHERE id='$id' LIMIT 1");
      }
    }

    function BerechneDeckungsbeitrag($id,$typ)
    {

      // mit rabatt beruecksichtigen
      if($typ=="rechnung" || $typ=="gutschrift" || $typ=="auftrag" || $typ=="angebot")
      {

		$status=$this->app->DB->Select("SELECT status FROM $typ WHERE id='$id' LIMIT 1");
		$deckungsbeitragcalc=$this->app->DB->Select("SELECT deckungsbeitragcalc FROM $typ WHERE id='$id' LIMIT 1");

		//wenn im richtigen status oder noch nicht berechnet
		if(($status!="storniert" && $status!="versendet" && $status!="abgeschlossen") || $deckungsbeitragcalc!="1")
		{
		$belegnr = $this->app->DB->Select("SELECT belegnr FROM $typ WHERE id='$id' LIMIT 1");
		$this->Protokoll(strtoupper($typ)." BELEG $belegnr");	
        	//$einnahmen = $this->ANABREGSNeuberechnenGesamtsumme($id,$typ,true);
        	$einnahmen = $this->app->DB->Select("SELECT SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1))) 
            FROM $typ auf LEFT JOIN {$typ}_position ap ON ap.{$typ}=auf.id LEFT JOIN artikel a ON ap.artikel=a.id WHERE auf.id=$id");

        	// nur echte artikel - keine stuecklisten artikel mit inden Preis nehmen
        	$ausgaben = $this->app->DB->Select("SELECT SUM(ap.menge*IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.artikel=ap.artikel AND (e.gueltig_bis > NOW() OR e.gueltig_bis='0000-00-00') AND e.geloescht!=1 ORDER by e.id DESC LIMIT 1),0)) FROM $typ auf LEFT JOIN {$typ}_position ap ON ap.{$typ}=auf.id LEFT JOIN artikel a ON ap.artikel=a.id WHERE auf.id=$id AND a.stueckliste!='1' AND a.juststueckliste!='1'");

        	// ausgaben allen stuecklisten artikel die summen darunter TODO WHERE a.stueckliste='1' AND a.juststueckliste!='1'

        	$deckungsbeitrag = $einnahmen - $ausgaben;
                if($einnahmen <=0 ) $tmpeinnahmen = $ausgaben; else $tmpeinnahmen = $einnahmen;
        	$deckungsbeitragprozent = ($deckungsbeitrag / $tmpeinnahmen)*100;
        	$this->app->DB->Update("UPDATE $typ SET deckungsbeitragcalc=1,deckungsbeitrag=$deckungsbeitragprozent,erloes_netto=$deckungsbeitrag,umsatz_netto='$einnahmen' WHERE id='$id' LIMIT 1");
		}
      }
    }

    function RabatteLaden($id,$tabelle,$adresse)
    {

      // pruefe und lade vertrieb und bearbeiter
      $this->CheckVertrieb($id,$tabelle);
      $this->CheckBearbeiter($id,$tabelle);

      if($tabelle=="rechnung" || $tabelle=="gutschrift")
        $this->app->DB->Update("UPDATE $tabelle SET dta_datei_verband=0 WHERE id='$id' LIMIT 1");

      $verband  = $this->GetVerband($adresse);
      $rabatte_festschreiben = $this->app->DB->Select("SELECT rabatte_festschreiben FROM adresse WHERE id='$adresse' LIMIT 1");

      if($verband > 0 && $rabatte_festschreiben!="1")
      {

        $provision = $this->app->DB->Select("SELECT provision FROM gruppen WHERE id='$verband' LIMIT 1");
        $grundrabatt = $this->app->DB->Select("SELECT grundrabatt FROM gruppen WHERE id='$verband' LIMIT 1");
        $rabatt1 = $this->app->DB->Select("SELECT rabatt1 FROM gruppen WHERE id='$verband' LIMIT 1");
        $rabatt2 = $this->app->DB->Select("SELECT rabatt2 FROM gruppen WHERE id='$verband' LIMIT 1");
        $rabatt3 = $this->app->DB->Select("SELECT rabatt3 FROM gruppen WHERE id='$verband' LIMIT 1");
        $rabatt4 = $this->app->DB->Select("SELECT rabatt4 FROM gruppen WHERE id='$verband' LIMIT 1");
        $rabatt5 = $this->app->DB->Select("SELECT rabatt5 FROM gruppen WHERE id='$verband' LIMIT 1");
      } else {
        $provision = $this->app->DB->Select("SELECT provision FROM adresse WHERE id='$adresse' LIMIT 1");
        $grundrabatt = $this->app->DB->Select("SELECT rabatt FROM adresse WHERE id='$adresse' LIMIT 1");
        $rabatt1 = $this->app->DB->Select("SELECT rabatt1 FROM adresse WHERE id='$adresse' LIMIT 1");
        $rabatt2 = $this->app->DB->Select("SELECT rabatt2 FROM adresse WHERE id='$adresse' LIMIT 1");
        $rabatt3 = $this->app->DB->Select("SELECT rabatt3 FROM adresse WHERE id='$adresse' LIMIT 1");
        $rabatt4 = $this->app->DB->Select("SELECT rabatt4 FROM adresse WHERE id='$adresse' LIMIT 1");
        $rabatt5 = $this->app->DB->Select("SELECT rabatt5 FROM adresse WHERE id='$adresse' LIMIT 1");
      }       

      $this->app->DB->Update("UPDATE $tabelle SET 
          provision='$provision',
          rabatt1='$rabatt1',
          rabatt2='$rabatt2',
          rabatt3='$rabatt3',
          rabatt4='$rabatt4',
          rabatt5='$rabatt5',
          gruppe='$verband',
          rabatt='$grundrabatt' WHERE id='$id' LIMIT 1");

      $this->app->DB->Update("UPDATE ".$tabelle."_position SET 
          rabatt1='$rabatt1',
          rabatt2='$rabatt2',
          rabatt3='$rabatt3',
          rabatt4='$rabatt4',
          rabatt5='$rabatt5',
          grundrabatt='$grundrabatt',
          rabattsync=0,
          rabatt='0' WHERE auftrag='$id' AND keinrabatterlaubt!=1 LIMIT 1");

    }


    function VerbandZahlungsoptionenLaden($id,$tabelle,$adresse)
    {
      $zahlungskonditionen_festschreiben = $this->app->DB->Select("SELECT zahlungskonditionen_festschreiben FROM adresse WHERE id='$adresse' LIMIT 1");

      $verband  = $this->GetVerband($adresse);
      if($verband > 0 && $zahlungskonditionen_festschreiben!="1")
      {
        $zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltage FROM gruppen WHERE id='$verband' LIMIT 1");
        $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM gruppen WHERE id='$verband' LIMIT 1");
        $zahlungszieltageskonto = $this->app->DB->Select("SELECT zahlungszieltageskonto FROM gruppen WHERE id='$verband' LIMIT 1");

        if($tabelle=="auftrag" || $tabelle=="angebot" || $tabelle=="rechnung" || $tabelle=="gutschrift")
        {
          $this->app->DB->Update("UPDATE $tabelle SET zahlungszieltage='$zahlungszieltage',zahlungszielskonto='$zahlungszielskonto',zahlungszieltageskonto='$zahlungszieltageskonto'
              WHERE id='$id' LIMIT 1");
        }
      } 


      // Provisionen laden
    }

    function LoadRechnungStandardwerte($id,$adresse)
    {
      if($id==0 || $id=="" || $adresse=="" || $adresse=="0") return;

      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT *,vertrieb as vertriebid,'' as bearbeiter FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");


      $arr[0]['gruppe'] = $this->GetVerband($adresse);

      $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse' AND subjekt='Kunde' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

      if($arr[0]['abweichende_rechnungsadresse']=="1")
      {
        $arr = $this->app->DB->SelectArr("SELECT projekt, rechnung_name as name,
            rechnung_abteilung as abteilung,
            rechnung_unterabteilung as unterabteilung,
            rechnung_strasse as strasse,
            rechnung_adresszusatz as adresszusatz,
            rechnung_plz as plz,
            rechnung_ort as ort,
            rechnung_land as land,
            rechnung_telefon as telefon,
            rechnung_telefax as telefax,
            rechnung_vorname as vorname,
            rechnung_typ as typ,
            rechnung_anschreiben as anschreiben
            FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      }

      if($rolle_projekt > 0)
      {
        $arr[0]['projekt'] = $rolle_projekt;
      }
      $field = array('anschreiben','name','abteilung','typ','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','kundennummer','projekt','ust_befreit','gruppe','vertriebid','bearbeiter');

      foreach($field as $key=>$value)
      {
        if($value=="projekt" && $this->app->Secure->POST[$value]!=""&&0)
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }

        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }

      $uparr[adresse] = $adresse;
      $uparr[ust_befreit] = $this->AdresseUSTCheck($adresse);
      $uparr[zahlungsstatusstatus]="offen";

      if($this->Firmendaten("rechnung_ohnebriefpapier")=="1")
        $uparr[ohne_briefpapier] = "1";

      $this->app->DB->UpdateArr("rechnung",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");

      if($arr[0]['abweichende_rechnungsadresse']=="1")
      {
        $arr = $this->app->DB->SelectArr("SELECT projekt, rechnung_name as name,
            rechnung_abteilung as abteilung,
            rechnung_unterabteilung as unterabteilung,
            rechnung_strasse as strasse,
            rechnung_adresszusatz as adresszusatz,
            rechnung_plz as plz,
            rechnung_ort as ort,
            rechnung_land as land,
            rechnung_telefon as telefon,
            rechnung_telefax as telefax,
            rechnung_vorname as vorname,
            rechnung_typ as typ,
            zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,versandart,
            rechnung_anschreiben as anschreiben
            FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      }

      $field = array('zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');

      if($arr[0]['zahlungsweise']=="")
        $arr[0]['zahlungsweise']="rechnung";

      if($arr[0]['zahlungszieltage']<=0)
        $arr[0]['zahlungszieltage']=$this->ZahlungsZielTage();

      if($arr[0]['zahlungszieltageskonto']<=0)
        $arr[0]['zahlungszieltageskonto']=$this->ZahlungsZielTageSkonto();

      if($arr[0]['zahlungszielskonto']<=0)
        $arr[0]['zahlungszielskonto']=$this->ZahlungsZielSkonto();

      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");      
      if($projekt_bevorzugt=="1")
      {
        $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");        
        $arr[0]['projekt'] = $uparr['projekt'];
        $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
      }

      $this->app->Secure->POST['zahlungsweise'] = strtolower($arr[0]['zahlungsweise']);
      $this->app->Secure->POST['zahlungszieltage'] = strtolower($arr[0]['zahlungszieltage']);
      $this->app->Secure->POST['zahlungszieltageskonto'] = strtolower($arr[0]['zahlungszieltageskonto']);
      $this->app->Secure->POST['zahlungszielskonto'] = strtolower($arr[0]['zahlungszielskonto']);
      $this->app->Secure->POST['versandart'] = strtolower($arr[0]['versandart']);
      /*
         foreach($field as $key=>$value)
         {
         $uparr[$value] = $arr[0][$value];
         $this->app->Secure->POST[$value] = $arr[0][$value];
         }
       */
      $this->app->DB->UpdateArr("rechnung",$id,"id",$arr[0]);

      $this->LoadSteuersaetzeWaehrung($id,"rechnung");
      $this->RabatteLaden($id,"rechnung",$adresse);
      $this->VerbandZahlungsoptionenLaden($id,"rechnung",$adresse);
    }



    function LoadBestellungStandardwerte($id,$adresse)
    {
      // standard adresse von lieferant       
      $arr = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $rolle_projekt = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE adresse='$adresse' AND subjekt='Lieferant' AND objekt='Projekt' AND (bis ='0000-00-00' OR bis <= NOW()) LIMIT 1");

      if($rolle_projekt > 0)
      {
        $arr[0]['projekt'] = $rolle_projekt;
      }
      $field = array('anschreiben','name','abteilung','unterabteilung','strasse','adresszusatz','plz','ort','land','ustid','email','telefon','telefax','lieferantennummer','projekt','ust_befreit');
      foreach($field as $key=>$value)
      {
        if($value=="projekt" && $this->app->Secure->POST[$value]!=""&&0)
        {
          $uparr[$value] = $this->app->Secure->POST[$value];
        } else {
          $this->app->Secure->POST[$value] = $arr[0][$value];
          $uparr[$value] = $arr[0][$value];
        }
        //$this->app->Secure->POST[$value] = $arr[0][$value];
        //$uparr[$value] = $arr[0][$value];
      }
      $uparr[adresse] = $adresse;
      $this->app->DB->UpdateArr("bestellung",$id,"id",$uparr);
      $uparr="";

      //liefernantenvorlage
      $arr = $this->app->DB->SelectArr("SELECT 
          kundennummerlieferant as kundennummer,
          zahlungsweiselieferant as zahlungsweise,
          zahlungszieltagelieferant as zahlungszieltage,
          zahlungszieltageskontolieferant as zahlungszieltageskonto,
          zahlungszielskontolieferant as zahlungszielskonto,
          versandartlieferant as versandart
          FROM adresse WHERE id='$adresse' LIMIT 1");

      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      { 
        $uparr['projekt'] = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        $arr[0]['projekt'] = $uparr['projekt'];
        $this->app->Secure->POST['projekt']=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr[0]['projekt']."' AND id > 0 LIMIT 1");
      }

      $field = array('kundennummer','zahlungsweise','zahlungszieltage','zahlungszieltageskonto','zahlungszielskonto','versandart');
      foreach($field as $key=>$value)
      {
        //$uparr[$value] = $arr[0][$value];
        $this->app->Secure->POST[$value] = $arr[0][$value];
      }


      $this->app->DB->UpdateArr("bestellung",$id,"id",$uparr);

      //standardprojekt
      //$projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      //$this->app->Secure->POST[projekt] = $projekt;
    }


    function CreateArbeitsnachweis($adresse="",$projekt="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;

      $belegmax = $this->GetNextNummer("arbeitsnachweis",$projekt);

      $ohnebriefpapier = $this->Firmendaten("arbeitsnachweis_ohnebriefpapier");
      $this->app->DB->Insert("INSERT INTO arbeitsnachweis (id,datum,bearbeiter,firma,belegnr,adresse,ohne_briefpapier,status,anzeige_verrechnungsart) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','".$ohnebriefpapier."','freigegeben',0)");

      $id = $this->app->DB->GetInsertID();
      $this->EventAPIAdd("EventArbeitsnachweisCreate",$id,"arbeitsnachweis","create");
      return $id;
    }


    function CreateInventur($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;

      $belegmax = "";
      //              $ohnebriefpapier = $this->Firmendaten("reisekosten_ohnebriefpapier");
      $ohnebriefpapier = 1;
      $this->app->DB->Insert("INSERT INTO inventur (id,datum,bearbeiter,firma,belegnr,adresse,ohne_briefpapier) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','".$ohnebriefpapier."')");

      $id = $this->app->DB->GetInsertID();
      $this->LoadSteuersaetzeWaehrung($id,"inventur");
      return $id;
    }



    function CreateAnfrage($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;

      $belegmax = "";
      //              $ohnebriefpapier = $this->Firmendaten("reisekosten_ohnebriefpapier");
      $ohnebriefpapier = 1;
      $this->app->DB->Insert("INSERT INTO anfrage (id,datum,bearbeiter,firma,belegnr,adresse,ohne_briefpapier,bearbeiterid) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','".$ohnebriefpapier."',
            '".$this->app->User->GetAdresse()."')");

      $id = $this->app->DB->GetInsertID();
      $this->LoadSteuersaetzeWaehrung($id,"anfrage");
      $this->EventAPIAdd("EventAnfrageCreate",$id,"anfrage","create");
      return $id;
    }



    function CreateReisekosten($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;

      $belegmax = "";
      //              $ohnebriefpapier = $this->Firmendaten("reisekosten_ohnebriefpapier");
      $ohnebriefpapier = 1;
      $this->app->DB->Insert("INSERT INTO reisekosten (id,datum,bearbeiter,firma,belegnr,adresse,ohne_briefpapier) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','".$ohnebriefpapier."')");

      $id = $this->app->DB->GetInsertID();
      $this->LoadSteuersaetzeWaehrung($id,"reisekosten");
      $this->EventAPIAdd("EventReisekostenCreate",$id,"reisekosten","create");
      return $id;
    }


    function AddArbeitsnachweisPositionZeiterfassung($arbeitsnachweis,$zid)
    {
      /*
         $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
         $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1"); 
         $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 
         $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
       */


      $tmp = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(von,'%Y-%m-%d') as datum,DATE_FORMAT(von,'%H:%i') as von,DATE_FORMAT(bis,'%H:%i') as bis FROM zeiterfassung WHERE id='$zid'");
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM arbeitsnachweis_position WHERE arbeitsnachweis='$arbeitsnachweis' LIMIT 1");
      $sort = $sort + 1;

      $adresse = $tmp[0][adresse]; //mitarbeiter
      $bezeichnung = $tmp[0][aufgabe];
      $beschreibung = $tmp[0][beschreibung];
      $ort = $tmp[0][ort];
      $arbeitspaket =$tmp[0][arbeitspaket];
      $datum = $tmp[0][datum];
      $von=$tmp[0][von];
      $bis=$tmp[0][bis];

      $this->app->DB->Insert("INSERT INTO arbeitsnachweis_position (id,arbeitsnachweis,artikel,bezeichnung,beschreibung,ort,arbeitspaket,datum,von,bis,sort,status,projekt,adresse) 
          VALUES ('','$arbeitsnachweis','$artikel','$bezeichnung','$beschreibung','$ort','$arbeitspaket','$datum','$von','$bis','$sort','angelegt','$projekt','$adresse')");
      $tmpid = $this->app->DB->GetInsertID();
      //markieren als erledigt
      $this->app->DB->Update("UPDATE zeiterfassung SET arbeitsnachweis='$arbeitsnachweis',arbeitsnachweispositionid='$tmpid' WHERE id='$zid'");

      //echo ("INSERT INTO lieferschein_position (id,lieferschein,artikel,bezeichnung,nummer,menge,sort,lieferdatum,status,projekt,vpe) 
      //      VALUES ('','$lieferschein','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$sort','$datum','angelegt','$projekt','$vpe')");
    }


    function CreateAufgabe($adresse,$aufgabe)
    {
      $this->app->DB->Insert("INSERT INTO aufgabe (id,adresse,initiator,aufgabe,status) VALUES ('','$adresse','".$this->app->User->GetAdresse()."','$aufgabe','offen')");
      return $this->app->DB->GetInsertID();
    }



    function CreateLieferschein($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;
      if($adresse>0)
        $tmp_projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");

      if($tmp_projekt <= 0)
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
      else
        $projekt = $tmp_projekt;

      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }





      $belegmax = "";
      $ohnebriefpapier = $this->Firmendaten("lieferschein_ohnebriefpapier");
      $this->app->DB->Insert("INSERT INTO lieferschein (id,datum,bearbeiter,firma,belegnr,adresse,ohne_briefpapier,projekt) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','".$ohnebriefpapier."','".$projekt."')");


      $id = $this->app->DB->GetInsertID();
      $this->EventAPIAdd("EventLieferscheinCreate",$id,"lieferschein","create");
      return $id;
    }

    function AddLieferscheinPositionArtikelID($lieferschein, $artikelid,$menge,$bezeichnung,$beschreibung,$datum)
    {
      $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 

      if($bezeichnung=="")
        $bezeichnung = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1"); 

      $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM lieferschein_position WHERE lieferschein='$lieferschein' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO lieferschein_position (id,lieferschein,artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum,status,projekt,vpe) 
          VALUES ('','$lieferschein','$artikel','$bezeichnung','$beschreibung','$bestellnummer','$menge','$sort','$datum','angelegt','$projekt','$vpe')");
    }


    function AddLieferscheinPosition($lieferschein, $verkauf,$menge,$datum)
    {
      $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM lieferschein_position WHERE lieferschein='$lieferschein' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO lieferschein_position (id,lieferschein,artikel,bezeichnung,nummer,menge,sort,lieferdatum,status,projekt,vpe) 
          VALUES ('','$lieferschein','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$sort','$datum','angelegt','$projekt','$vpe')");


      //echo ("INSERT INTO lieferschein_position (id,lieferschein,artikel,bezeichnung,nummer,menge,sort,lieferdatum,status,projekt,vpe) 
      //      VALUES ('','$lieferschein','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$sort','$datum','angelegt','$projekt','$vpe')");
    }


    function DeleteAnfrage($id)
    {
      $this->app->DB->Delete("DELETE FROM anfrage_position WHERE anfrage='$id'");
      $this->app->DB->Delete("DELETE FROM anfrage_protokoll WHERE anfrage='$id'");
      $this->app->DB->Delete("DELETE FROM anfrage WHERE id='$id' LIMIT 1");
    }


    function DeleteInventur($id)
    {
      $this->app->DB->Delete("DELETE FROM inventur_position WHERE inventur='$id'");
      $this->app->DB->Delete("DELETE FROM inventur_protokoll WHERE inventur='$id'");
      $this->app->DB->Delete("DELETE FROM inventur WHERE id='$id' LIMIT 1");
    }


    function DeleteReisekosten($id)
    {
      $this->app->DB->Delete("DELETE FROM reisekosten_position WHERE reisekosten='$id'");
      $this->app->DB->Delete("DELETE FROM reisekosten_protokoll WHERE reisekosten='$id'");
      $this->app->DB->Delete("DELETE FROM reisekosten WHERE id='$id' LIMIT 1");
    }

    function DeleteArbeitsnachweis($id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM arbeitsnachweis_position WHERE arbeitsnachweis='$id'");
      $this->app->DB->Delete("DELETE FROM arbeitsnachweis_protokoll WHERE arbeitsnachweis='$id'");
      $this->app->DB->Delete("DELETE FROM arbeitsnachweis WHERE id='$id' LIMIT 1");

      // freigeben aller Zeiterrfassungen
      $this->app->DB->Update("UPDATE zeiterfassung SET arbeitsnachweis='0',arbeitsnachweispositionid='0',
          abgerechnet='0', ist_abgerechnet='0' WHERE arbeitsnachweis='$id'");
    }

    function DeleteLieferschein($id)
    {
      $this->app->DB->Delete("DELETE FROM lieferschein_position WHERE lieferschein='$id'");
      $this->app->DB->Delete("DELETE FROM lieferschein_protokoll WHERE lieferschein='$id'");
      $this->app->DB->Delete("DELETE FROM lieferschein WHERE id='$id' LIMIT 1");
    }



    function CreateProduktion($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;
      $belegmax = "";

      $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");

      // falls von Benutzer projekt ueberladen werden soll
      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {      
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }

      $this->app->DB->Insert("INSERT INTO produktion (id,datum,bearbeiter,firma,belegnr,autoversand,zahlungsweise,zahlungszieltage,status,projekt,adresse,reservierart,auslagerart) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax',1,'".$this->StandardZahlungsweise($projekt)."','".$this->ZahlungsZielTage($projekt)."','angelegt','$projekt','$adresse','abschluss','einzeln')");
      $id = $this->app->DB->GetInsertID();
      $this->EventAPIAdd("EventProduktionCreate",$id,"produktion","create");
      return $id;
    }


    function CreateAuftrag($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;
      $ohnebriefpapier = $this->Firmendaten("auftrag_ohnebriefpapier");
      $belegmax = "";

      if($adresse>0)
        $tmp_projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");

      if($tmp_projekt <= 0)
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
      else
        $projekt = $tmp_projekt;

      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }


      $firma = $this->app->DB->Select("SELECT MAX(id) FROM firma LIMIT 1");
      //    $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$firma."' LIMIT 1");

      $this->app->DB->Insert("INSERT INTO auftrag (id,datum,bearbeiter,firma,belegnr,autoversand,zahlungsweise,zahlungszieltage,
        zahlungszieltageskonto,zahlungszielskonto,status,projekt,adresse,ohne_briefpapier) 
          VALUES ('',NOW(),'','','$belegmax',1,'".$this->StandardZahlungsweise($projekt)."',
            '".$this->ZahlungsZielTage($projekt)."',
            '".$this->ZahlungsZielTageSkonto($projekt)."',
            '".$this->ZahlungsZielSkonto($projekt)."',
            'angelegt','$projekt','$adresse','".$ohnebriefpapier."')");

      $id = $this->app->DB->GetInsertID();

      $this->CheckVertrieb($id,"auftrag");
      $this->CheckBearbeiter($id,"auftrag");

      $this->EventAPIAdd("EventAuftragCreate",$id,"auftrag","create");

      $this->LoadSteuersaetzeWaehrung($id,"auftrag",$projekt);
      return $id;
    }

    function AddAuftragPositionNummerPartnerprogramm($auftrag,$nummer,$menge,$projekt,$partner)
    {
      $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1"); 
      // nur wenn artikel lager artikel und shop artikel fuer den partner und wenn artikel nicht gesperrt fuer partnerprogramm

      $check_partnerprogramm = $this->app->DB->Select("SELECT id FROM partner WHERE ref='$partner' AND projekt='$projekt' AND geloescht='0' LIMIT 1");

      $check_artikel = $this->app->DB->Select("SELECT partnerprogramm_sperre FROM artikel WHERE id='$artikel' LIMIT 1");


      $artikel_ok = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel' AND geloescht='0' AND projekt='$projekt' 
          AND porto='0' AND ( lagerartikel='1' OR juststueckliste='1' ) LIMIT 1");

      if(is_numeric($check_partnerprogramm) && $check_artikel==0 && $artikel_ok==$artikel)
      {
        $this->app->DB->Insert("INSERT INTO partner_verkauf (id,auftrag,artikel,menge,partner,freigabe,abgerechnet) 
            VALUES ('','$auftrag','$artikel','$menge','$partner',0,0)");
      }

    }


    function AddAuftragPositionNummer($auftrag,$nummer,$menge,$projekt,$nullpreis="",$taxfree=false,$typ="")
    {
      $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1"); 
      $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 

      // $verkaufsid = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE artikel='$artikel' 
      //AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND ab_menge=1 AND geloescht=0 AND (adresse='0' OR adresse='') AND objekt='Standard' LIMIT 1"); 

      $verkaufsid = $this->app->DB->Select("SELECT id FROM verkaufspreise WHERE artikel='$artikel'                                             
          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW()) AND ab_menge=1                                           
          AND ((objekt='Standard' AND adresse=0) OR (objekt='' AND adresse=0)) AND geloescht=0 LIMIT 1");                   

        $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$verkaufsid' LIMIT 1"); 

      if($nullpreis=="1")
        $preis = 0;

      // wenn mehr rein kam als geplant
      //if($taxfree==false)
      //{
      //$preis = $vkpreis;
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel' LIMIT 1");

      $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel' LIMIT 1");

      //} else {
      //  $umsatzsteuer = 0;
      //}

      $vpe  = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkaufsid' LIMIT 1"); 

      if($typ=="produktion")
      {
        $sort = $this->app->DB->Select("SELECT MAX(sort) FROM produktion_position WHERE produktion='$auftrag' LIMIT 1");
        $sort = $sort + 1;
        $this->app->DB->Insert("INSERT INTO produktion_position (id,produktion,artikel,bezeichnung,nummer,menge,preis, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
            VALUES ('','$auftrag','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe')");
      } else {
        $sort = $this->app->DB->Select("SELECT MAX(sort) FROM auftrag_position WHERE auftrag='$auftrag' LIMIT 1");
        $sort = $sort + 1;
        $this->app->DB->Insert("INSERT INTO auftrag_position (id,auftrag,artikel,bezeichnung,nummer,menge,preis, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie) 
            VALUES ('','$auftrag','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");
      }

      return $this->app->DB->GetInsertID();
    }

    function AddPositionManuellPreisNummer($typ,$id,$projekt, $artikelnummer,$menge,$name,$preis,$umsatzsteuer,$rabatt=0,$shop=0,$waehrung='EUR')
    {
      // wenn es Artikel nicht gibt anlegen! bzw. immer updaten wenn name anders ist
      $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikelnummer' AND (projekt='$projekt' OR projekt!='') AND nummer!='' LIMIT 1"); 

      if($artikel <=0)
      {
        //Artikel anlegen
        $artikel = $this->AddArtikel(array('name_de'=>$name,'nummer'=>$artikelnummer,'projekt'=>$projekt,'umsatzsteuer'=>$umsatzsteuer,'lagerartikel'=>1,'shop'=>$shop));
        $this->AddVerkaufspreis($artikel,1,0,$preis,$waehrung);
      } else {
        // update name
        if($name!="")
          $this->app->DB->Update("UPDATE artikel SET name_de='$name' WHERE id='$artikel' LIMIT 1");
      }

      //$waehrung = 'EUR';
      $datum ="";

      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM ".$typ."_position WHERE $typ='$id' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO ".$typ."_position (id,$typ,artikel,bezeichnung,beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,rabatt) 
          VALUES ('','$id','$artikel','$name','$beschreibung','$artikelnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe','$rabatt')");
      return $this->app->DB->GetInsertID();
    }


    function AddPositionManuellPreis($typ,$id, $artikel,$menge,$name,$preis,$umsatzsteuer,$rabatt=0,$waehrung='EUR')
    {

      // wenn es Artikel nicht gibt anlegen! bzw. immer updaten wenn name anders ist

      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 

      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM ".$typ."_position WHERE $typ='$id' LIMIT 1");
      $sort = $sort + 1;

      $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel' LIMIT 1");


      if($typ=="auftrag" || $typ=="angebot" || $typ=="rechnung")
      {
        $this->app->DB->Insert("INSERT INTO ".$typ."_position (id,$typ,artikel,bezeichnung,
          beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,rabatt,punkte,bonuspunkte,mlmdirektpraemie) 
            VALUES ('','$id','$artikel','$name','$beschreibung','$bestellnummer','$menge',
              '$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe','$rabatt','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");
      } else {
        $this->app->DB->Insert("INSERT INTO ".$typ."_position (id,$typ,artikel,bezeichnung,
          beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,rabatt) 
            VALUES ('','$id','$artikel','$name','$beschreibung','$bestellnummer','$menge','$preis',
              '$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe','$rabatt')");
      }
      return $this->app->DB->GetInsertID();
    }


    function AddPositionManuell($typ,$id, $artikel,$menge,$name,$beschreibung,$waehrung='EUR')
    {
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 

      $adresse = $this->app->DB->Select("SELECT adresse FROM $typ WHERE id='$id' LIMIT 1"); 

      if($menge<1)
        $preis = $this->GetVerkaufspreis($artikel,1,$adresse,$waehrung);
      else
        $preis = $this->GetVerkaufspreis($artikel,$menge,$adresse,$waehrung);

      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel' LIMIT 1");
      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM ".$typ."_position WHERE $typ='$id' LIMIT 1");
      $sort = $sort + 1;

      $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel' LIMIT 1");


      if($typ=="auftrag" || $typ=="angebot" || $typ=="rechnung")
      {
        $this->app->DB->Insert("INSERT INTO ".$typ."_position (id,$typ,artikel,bezeichnung,
          beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie) 
            VALUES ('','$id','$artikel','$name','$beschreibung','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt',
              '$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");
      } else {
        $this->app->DB->Insert("INSERT INTO ".$typ."_position (id,$typ,artikel,bezeichnung,
          beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
            VALUES ('','$id','$artikel','$name','$beschreibung','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe')");
      }
      return $this->app->DB->GetInsertID();
    }



    function AddAuftragPosition($auftrag, $verkauf,$menge,$datum)
    {
      $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel' LIMIT 1");
      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM auftrag_position WHERE auftrag='$auftrag' LIMIT 1");
      $sort = $sort + 1;

      $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel' LIMIT 1");
      $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel' LIMIT 1");

      $this->app->DB->Insert("INSERT INTO auftrag_position (id,auftrag,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, 
        status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie) 
          VALUES ('','$auftrag','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe',
            '$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");
    }


    function DeleteProduktion($id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$id' LIMIT 1");
      if(!is_numeric($belegnr) || $belegnr==0)
      {
        $this->app->DB->Delete("DELETE FROM produktion_position WHERE produktion='$id'");
        $this->app->DB->Delete("DELETE FROM produktion_protokoll WHERE produktion='$id'");
        $this->app->DB->Delete("DELETE FROM produktion WHERE id='$id' LIMIT 1");
        $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE objekt='produktion' AND parameter='$id'");
      }
    }

    function DeleteAuftrag($id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      if(!is_numeric($belegnr) || $belegnr==0)
      {
        $this->app->DB->Delete("DELETE FROM auftrag_position WHERE auftrag='$id'");
        $this->app->DB->Delete("DELETE FROM auftrag_protokoll WHERE auftrag='$id'");
        $this->app->DB->Delete("DELETE FROM auftrag WHERE id='$id' LIMIT 1");
        $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE objekt='auftrag' AND parameter='$id'");
      }
    }


    function CreateAngebot($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM angebot WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;

      if($adresse>0)
        $tmp_projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");

      if($tmp_projekt <= 0)
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
      else
        $projekt = $tmp_projekt;

      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }


      $ohnebriefpapier = $this->Firmendaten("angebot_ohnebriefpapier");
      $belegmax = "";
      $this->app->DB->Insert("INSERT INTO angebot (id,datum,gueltigbis,bearbeiter,vertrieb,firma,belegnr,autoversand,zahlungsweise,
        zahlungszieltage,
        zahlungszieltageskonto,
        zahlungszielskonto,
        status,projekt,adresse,ohne_briefpapier ) 
          VALUES ('',NOW(), DATE_ADD(curdate(), INTERVAL 28 DAY),'','','".$this->app->User->GetFirma()."','$belegmax',1,'".$this->StandardZahlungsweise($projekt)."',
            '".$this->ZahlungsZielTage($projekt)."',
            '".$this->ZahlungsZielTageSkonto($projekt)."',
            '".$this->ZahlungsZielSkonto($projekt)."',
            'angelegt','$projekt','$adresse','".$ohnebriefpapier."')");

      $id = $this->app->DB->GetInsertID();

      $this->CheckVertrieb($id,"angebot");
      $this->CheckBearbeiter($id,"angebot");


      $this->EventAPIAdd("EventAngebotCreate",$id,"angebot","create");
      $this->LoadSteuersaetzeWaehrung($id,"angebot",$projekt);
      return $id;
    }


    function AddAdressePosition($adresse, $verkauf,$menge,$startdatum)
    {
      $lieferdatum = $this->app->String->Convert($startdatum,"%1.%2.%3","%3-%2-%1");

      $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $bezeichnung= $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer  FROM artikel WHERE id='$artikel' LIMIT 1");
      $vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE angebot='$angebot' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO abrechnungsartikel (id,artikel,bezeichnung,nummer,menge,preis, sort,
        lieferdatum, steuerklasse, status,projekt,wiederholend,zahlzyklus,adresse,startdatum) 
          VALUES ('','$artikel','$bezeichnung','$bestellnummer','$menge','$preis','$sort','$lieferdatum',
            '$umsatzsteuer','angelegt','$projekt','$wiederholend','$zahlzyklus','$adresse','$startdatum')");

      return $this->app->DB->GetInsertID();
    }


    function AddAngebotPosition($angebot, $verkauf,$menge,$datum)
    {
      $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $beschreibung = $this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id='$artikel' LIMIT 1"); 
      $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer  FROM artikel WHERE id='$artikel' LIMIT 1");
      $vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE angebot='$angebot' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO angebot_position (id,angebot,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$angebot','$artikel','$beschreibung','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe')");
    }




    function CopyBestellung($id)
    {
      // kopiere eintraege aus auftrag_position
      $this->app->DB->Insert("INSERT INTO bestellung (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,einkaeufer,zahlungsweise,zahlungszieltage,'angelegt' as status,
          zahlungszieltageskonto,zahlungszielskonto,firma,'angelegt' as status,abweichendelieferadresse,liefername,lieferabteilung,lieferunterabteilung,ust_befreit,
          lieferland,lieferstrasse,lieferort,lieferplz,lieferadresszusatz,lieferansprechpartner FROM bestellung WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("bestellung",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM bestellung_position WHERE bestellung='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO bestellung_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][bestellung]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("bestellung_position",$newposid,$pos[$i]);


        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->app->DB->Update("UPDATE bestellung_position SET geliefert=0, mengemanuellgeliefertaktiviert=0,abgeschlossen='0',abgerechnet='0' WHERE auftrag='$newid'");
      $this->LoadSteuersaetzeWaehrung($newid,"bestellung");
      return $newid;
    }


    function CopyAuftrag($id)
    {
      // kopiere eintraege aus auftrag_position
      $this->app->DB->Insert("INSERT INTO auftrag (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,freitext,adresse,name,
          abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,ihrebestellnummer,
          versandart,vertrieb,zahlungsweise,zahlungszieltage,lieferdatum,'angelegt' as status,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,provision,provision_summe,
          zahlungszieltageskonto,zahlungszielskonto,firma,'angelegt' as status,abweichendelieferadresse,liefername,lieferabteilung,lieferunterabteilung,ust_befreit,
          lieferland,lieferstrasse,lieferort,lieferplz,lieferadresszusatz,lieferansprechpartner,autoversand FROM auftrag WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("auftrag",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$id' AND explodiert_parent='0'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO auftrag_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][auftrag]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("auftrag_position",$newposid,$pos[$i]);


        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->app->DB->Update("UPDATE auftrag_position SET geliefert=0, geliefert_menge=0,explodiert='0',explodiert_parent='0' WHERE auftrag='$newid'");
      $this->LoadSteuersaetzeWaehrung($newid,"auftrag");
      return $newid;
    }



    function CopyGutschrift($id)
    {

      // kopiere eintraege aus gutschrift_position
      $this->app->DB->Insert("INSERT INTO gutschrift (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,rechnung,rechnungid,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer, bearbeiter,zahlungszieltage,zahlungszieltageskonto,zahlungsweise,ohne_briefpapier,'angelegt' as status,
          zahlungszielskonto,ust_befreit,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,provision,provision_summe,
          firma FROM gutschrift WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("gutschrift",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM gutschrift_position WHERE gutschrift='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO gutschrift_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][gutschrift]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("gutschrift_position",$newposid,$pos[$i]);

        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->LoadSteuersaetzeWaehrung($newid,"gutschrift");
      return $newid;
    }





    function CopyRechnung($id)
    {

      // kopiere eintraege aus rechnung_position
      $this->app->DB->Insert("INSERT INTO rechnung (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,auftrag,auftragid,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,bearbeiter,zahlungszieltage,zahlungszieltageskonto,zahlungsweise,ohne_briefpapier,'angelegt' as status,
          zahlungszielskonto,ust_befreit,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,provision,provision_summe,
          firma FROM rechnung WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("rechnung",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO rechnung_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][rechnung]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("rechnung_position",$newposid,$pos[$i]);

        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->LoadSteuersaetzeWaehrung($newid,"rechnung");
      return $newid;
    }



    function CopyAnfrage($id)
    {
      // kopiere eintraege aus lieferschein_position
      $this->app->DB->Insert("INSERT INTO anfrage (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,freitext,adresse,CONCAT(name,' (Kopie)') as name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,bearbeiter,'angelegt' as status,
          firma FROM anfrage WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("anfrage",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM anfrage_position WHERE anfrage='$id'");

      for($i=0;$i<count($pos);$i++)
      {
        $this->app->DB->Insert("INSERT INTO anfrage_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][anfrage]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("anfrage_position",$newposid,$pos[$i]);
        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->LoadSteuersaetzeWaehrung($newid,"anfrage");
      return $newid;
    }


    function CopyInventur($id)
    {
      // kopiere eintraege aus lieferschein_position
      $this->app->DB->Insert("INSERT INTO inventur (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,freitext,adresse,CONCAT(name,' (Kopie)') as name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,bearbeiter,'angelegt' as status,
          firma FROM inventur WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("inventur",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM inventur_position WHERE inventur='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO inventur_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][inventur]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("inventur_position",$newposid,$pos[$i]);

        // vorraussichtliches lieferdatum anpassen TODO
      }

      return $newid;
    }


    function CopyReisekosten($id)
    {

      // kopiere eintraege aus lieferschein_position
      $this->app->DB->Insert("INSERT INTO reisekosten (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,reisekosten,reisekostenid,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,mitarbeiternummer,reisekostenart,bearbeiter,'angelegt' as status,
          firma FROM reisekosten WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("reisekosten",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM reisekosten_position WHERE reisekosten='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO reisekosten_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][arbeitsnachweis]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("reisekosten_position",$newposid,$pos[$i]);

        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->app->DB->Update("UPDATE reisekosten_position SET abgerechnet=0,exportiert=0,exportiert_am='0000-00-00' WHERE reisekosten='$id'");

      return $newid;
    }



    function CopyArbeitsnachweis($id)
    {

      // kopiere eintraege aus lieferschein_position
      $this->app->DB->Insert("INSERT INTO arbeitsnachweis (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,auftrag,auftragid,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,prefix,arbeitsnachweisart,bearbeiter,versandart,'angelegt' as status,
          firma FROM arbeitsnachweis WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("arbeitsnachweis",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM arbeitsnachweis_position WHERE arbeitsnachweis='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO arbeitsnachweis_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][arbeitsnachweis]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("arbeitsnachweis_position",$newposid,$pos[$i]);

        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->app->DB->Update("UPDATE arbeitsnachweis_position SET abgerechnet=0 WHERE arbeitsnachweis='$id'");

      return $newid;
    }


    function CopyLieferschein($id)
    {

      // kopiere eintraege aus lieferschein_position
      $this->app->DB->Insert("INSERT INTO lieferschein (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,auftrag,auftragid,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,bearbeiter,'angelegt' as status,
          firma FROM lieferschein WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("lieferschein",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE lieferschein='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO lieferschein_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][lieferschein]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("lieferschein_position",$newposid,$pos[$i]);

        // vorraussichtliches lieferdatum anpassen TODO
      }
      $this->app->DB->Update("UPDATE lieferschein_position SET geliefert=0, abgerechnet=0 WHERE lieferschein='$id'");

      return $newid;
    }


    function CopyAngebot($id)
    {

      // kopiere eintraege aus angebot_position
      $this->app->DB->Insert("INSERT INTO angebot (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,anfrage,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,vertrieb,zahlungsweise,zahlungszieltage,ust_befreit,lieferdatum,gueltigbis,'angelegt' as status,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,provision,provision_summe,
          zahlungszieltageskonto,zahlungszielskonto,firma FROM angebot WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("angebot",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM angebot_position WHERE angebot='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO angebot_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][angebot]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("angebot_position",$newposid,$pos[$i]);

        // vorraussichtliches lieferdatum anpassen TODO

      }
      //$this->app->DB->Update("UPDATE angebot_position SET geliefert=0 WHERE angebot='$newid'");

      $this->LoadSteuersaetzeWaehrung($newid,"angebot");
      return $newid;
    }

    function WeiterfuehrenAuftragZuLieferschein($id)
    {

      // pruefe ob auftrag status=angelegt, dann vergebe belegnr
      $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
      $status = $this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
      $checkbelegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      if($status=="angelegt" && $checkbelegnr=="")
      {
        $belegnr = $this->GetNextNummer("auftrag",$projekt);

        $this->app->DB->Update("UPDATE auftrag SET belegnr='$belegnr', status='freigegeben'  WHERE id='$id' LIMIT 1");
        $this->AuftragProtokoll($id,"Auftrag freigegeben");

        // auftrag abschliessen und event senden
        $this->EventAPIAdd("EventAuftragEdit",$id,"auftrag","edit");
      }

      //angebot aus offene Angebote entfernen 
      $this->app->DB->Insert("INSERT INTO lieferschein (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT datum,ihrebestellnummer,projekt,belegnr as auftrag,freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,ansprechpartner,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,vertrieb,zahlungsweise,zahlungszieltage,anschreiben, id as auftragid,vertriebid, bearbeiter,internebemerkung,
          zahlungszieltageskonto,zahlungszielskonto,firma FROM auftrag WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("lieferschein",$newid,$arr[0]);

      $this->app->DB->Update("UPDATE lieferschein SET datum=NOW() WHERE id='$newid' LIMIT 1");

      $abweichendelieferadresse = $this->app->DB->Select("SELECT abweichendelieferadresse FROM auftrag WHERE id='$id' LIMIT 1");
      $tmparrliefer = $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE id='$id' LIMIT 1");
      $versandart = $this->app->DB->Select("SELECT versandart FROM auftrag WHERE id='$id' LIMIT 1");

      //lieferadresse wenn abweichend!!!!
      if($abweichendelieferadresse && $versandart!="packstation")
      {
        //liefername  lieferland  lieferstrasse   lieferort   lieferplz   lieferadresszusatz 
        $this->app->DB->Update("UPDATE lieferschein SET name='{$tmparrliefer[0][liefername]}', abteilung='{$tmparrliefer[0][lieferabteilung]}',
            unterabteilung='{$tmparrliefer[0][lieferunterabteilung]}',strasse='{$tmparrliefer[0][lieferstrasse]}', 
            adresszusatz='{$tmparrliefer[0][lieferadresszusatz]}', plz='{$tmparrliefer[0][lieferplz]}',ort='{$tmparrliefer[0][lieferort]}',land='{$tmparrliefer[0][lieferland]}',ansprechpartner='{$tmparrliefer[0][lieferansprechpartner]}' WHERE id='$newid' LIMIT 1");
      }

      //lieferadresse wenn packstation
      if($versandart=="packstation")
      {
        //packstation_inhaber   packstation_station         packstation_ident   packstation_plz   packstation_ort   
        $this->app->DB->Update("UPDATE lieferschein SET name='{$tmparrliefer[0][packstation_inhaber]}', unterabteilung='',strasse='Packstation Nr. {$tmparrliefer[0][packstation_station]}', adresszusatz='{$tmparrliefer[0][packstation_ident]}', 
            plz='{$tmparrliefer[0][packstation_plz]}',ort='{$tmparrliefer[0][packstation_ort]}' WHERE id='$newid' LIMIT 1");
      }


      $pos = $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$id'");
      for($i=0;$i<count($pos);$i++){

        /* nur lager artikel in den Lieferschein */
        $portoartikel = $this->app->DB->Select("SELECT porto FROM artikel WHERE id='{$pos[$i][artikel]}' LIMIT 1");

        if($portoartikel==0)
        {
          $this->app->DB->Insert("INSERT INTO lieferschein_position (id) VALUES('')");
          $newposid = $this->app->DB->GetInsertID();
          $pos[$i][lieferschein]=$newid;
          if($pos[$i][explodiert]) $pos[$i][bezeichnung] = $pos[$i][bezeichnung]." (Stückliste)";
          if($pos[$i][explodiert_parent] > 0) {
            $pos[$i][bezeichnung] = "*** ".$pos[$i][bezeichnung];
            $pos[$i][explodiert_parent_artikel] = $this->app->DB->Select("SELECT artikel FROM auftrag_position WHERE id='".$pos[$i][explodiert_parent]."' LIMIT 1");
          }

          $this->app->FormHandler->ArrayUpdateDatabase("lieferschein_position",$newposid,$pos[$i]);
        }

      }
      $this->app->DB->Update("UPDATE auftrag SET status='abgeschlossen',schreibschutz='1' WHERE id='$id' LIMIT 1");

      // auftrag freigeben!!!

      return $newid;
    }

    function WeiterfuehrenRechnungZuGutschrift($id)
    {
      //angebot aus offene Angebote entfernen 
      $this->app->DB->Insert("INSERT INTO gutschrift (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,ihrebestellnummer,projekt, belegnr as rechnung,anschreiben,aktion,
          freitext,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,zahlungsweise,zahlungszieltage,ust_befreit, keinsteuersatz, id as rechnungid,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,provision,provision_summe,bearbeiter,
          zahlungszieltageskonto,zahlungszielskonto,firma,waehrung,steuersatz_normal,steuersatz_zwischen,steuersatz_ermaessigt,steuersatz_starkermaessigt,steuersatz_dienstleistung FROM rechnung WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("gutschrift",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO gutschrift_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][gutschrift]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("gutschrift_position",$newposid,$pos[$i]);
      }

      // wenn auftrag vorkasse rechnung als bezahlt markieren wenn genug geld vorhanden
      //   $this->GutschriftNeuberechnen($newid);

      /*
      //summe zahlungseingaenge
      $summe_zahlungseingaenge = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang WHERE objekt='auftrag' AND parameter='$id' AND firma='".$this->app->User->GetFirma()."'");
      $rechnungssumme = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$newid' LIMIT 1");

      //if(($arr[0][zahlungsweise]=="vorkasse" || $arr[0][zahlungsweise]=="paypal" || $arr[0][zahlungsweise]=="kreditkarte") &&  $summe_zahlungseingaenge >= $rechnungssumme)
      if($summe_zahlungseingaenge >= $rechnungssumme)
      {

      if($summe_zahlungseingaenge >= $rechnungssumme) 
      $this->app->DB->Update("UPDATE rechnung SET ist=soll, zahlungsstatus='bezahlt' WHERE id='$newid' AND firma='".$this->app->User->GetFirma()."'");
      else
      $this->app->DB->Update("UPDATE rechnung SET ist='$summe_zahlungseingaenge', zahlungsstatus='' WHERE id='$newid' AND firma='".$this->app->User->GetFirma()."'");
      }  // was ist denn bei rechnung bar oder nachnahme wenn ein auftragsguthaben vorhanden ist

      $this->app->DB->Update("UPDATE auftrag SET status='abgeschlossen' WHERE id='$id' LIMIT 1");

      // auftrag freigeben!!!
       */
      $this->app->DB->Update("UPDATE rechnung SET schreibschutz='1' WHERE id='$id' LIMIT 1");
      return $newid;
    }


    function WeiterfuehrenAuftragZuAnfrage($auftrag)
    {

      $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftrag' LIMIT 1");
      $anfrageid = $this->app->DB->Select("SELECT anfrageid FROM auftrag WHERE id='$auftrag' LIMIT 1");
      //angebot aus offene Angebote entfernen 

      $arr = $this->app->DB->SelectArr("SELECT projekt,aktion, 
          freitext,anschreiben,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,email,telefon,telefax,
          firma FROM auftrag WHERE id='$auftrag' LIMIT 1");

      $this->app->FormHandler->ArrayUpdateDatabase("anfrage",$anfrageid,$arr[0]);

      $this->app->DB->Delete("DELETE FROM anfrage_position WHERE anfrage='$anfrageid'");

      $pos = $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$auftrag'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO anfrage_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();

        $pos[$i][anfrage]=$anfrageid;

        // Hole Verkaufspeis ab menge
        $this->app->FormHandler->ArrayUpdateDatabase("anfrage_position",$newposid,$pos[$i]);
      }
      $this->app->DB->Update("UPDATE auftrag SET schreibschutz='1' WHERE id='$id' LIMIT 1");
      return $newid;
    }

    function WeiterfuehrenAnfrageZuAngebot($id)
    {

      $adresse = $this->app->DB->Select("SELECT adresse FROM anfrage WHERE id='$id' LIMIT 1");
      //angebot aus offene Angebote entfernen 
      $this->app->DB->Insert("INSERT INTO angebot (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum,projekt,aktion,
          freitext,anschreiben,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,
          versandart, id as anfrageid,
          firma FROM anfrage WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("angebot",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM anfrage_position WHERE anfrage='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO angebot_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][angebot]=$newid;

        // Hole Verkaufspeis ab menge
        $pos[$i][preis]=$this->GetVerkaufspreis($pos[$i][artikel],$pos[$i][menge],$adresse);
        $this->app->FormHandler->ArrayUpdateDatabase("angebot_position",$newposid,$pos[$i]);
      }
      $this->app->DB->Update("UPDATE anfrage SET schreibschutz='1' WHERE id='$id' LIMIT 1");
      return $newid;
    }



    function WeiterfuehrenAuftragZuRechnung($id)
    {
      // wenn anfrage vorhanden diese markieren als abgerechnet status='abgerechnet'
      $anfrageid = $this->app->DB->Select("SELECT anfrageid FROM auftrag WHERE id='$id' LIMIT 1");
      $adresseid = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");
      $abweichende_rechnungsadresse = $this->app->DB->Select("SELECT abweichende_rechnungsadresse 
          FROM adresse WHERE id='$adresseid' LIMIT 1");
      if($anfrageid > 0)
        $this->app->DB->Update("UPDATE anfrage SET status='abgerechnet',schreibschutz='0' WHERE id='$anfrageid'");

      // pruefe ob auftrag status=angelegt, dann vergebe belegnr
      $status = $this->app->DB->Select("SELECT status FROM auftrag WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
      $checkbelegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      if($status=="angelegt" && $checkbelegnr=="")
      {
        $belegnr = $this->GetNextNummer("auftrag",$projekt);

        $this->app->DB->Update("UPDATE auftrag SET belegnr='$belegnr', status='freigegeben'  WHERE id='$id' LIMIT 1");
        $this->AuftragProtokoll($id,"Auftrag freigegeben");

        // auftrag abschliessen und event senden
        $this->EventAPIAdd("EventAuftragEdit",$id,"auftrag","edit");
      }


      //angebot aus offene Angebote entfernen 
      $this->app->DB->Insert("INSERT INTO rechnung (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();
      $arr = $this->app->DB->SelectArr("SELECT datum,ihrebestellnummer,projekt,gesamtsumme as soll, belegnr as auftrag, id as auftragid,aktion,
          freitext,anschreiben,adresse,name,abteilung,unterabteilung,ansprechpartner,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,vertrieb,zahlungsweise,zahlungszieltage,ust_befreit,keinsteuersatz,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,provision,provision_summe,bearbeiter,internebemerkung,
          zahlungszieltageskonto,zahlungszielskonto,firma,einzugsdatum,waehrung,steuersatz_normal,steuersatz_zwischen,steuersatz_ermaessigt,steuersatz_starkermaessigt,steuersatz_dienstleistung FROM auftrag WHERE id='$id' LIMIT 1");

      $arr_zahlung = $this->app->DB->SelectArr("SELECT zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,versandart FROM auftrag WHERE id='$id' LIMIT 1"); 



      $this->app->FormHandler->ArrayUpdateDatabase("rechnung",$newid,$arr[0]);


      $lieferscheinid = $this->app->DB->Select("SELECT id FROM lieferschein WHERE auftragid='$id' LIMIT 1");

      if($lieferscheinid>0)
        $this->app->DB->Update("UPDATE rechnung SET lieferschein='$lieferscheinid' WHERE id='$newid' LIMIT 1");

      $this->app->DB->Update("UPDATE rechnung SET datum=NOW() WHERE id='$newid' LIMIT 1");

      $pos = $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO rechnung_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][rechnung]=$newid;

        if($pos[$i][explodiert]) $pos[$i][bezeichnung] = $pos[$i][bezeichnung]." (Stückliste)";
        if($pos[$i][explodiert_parent] > 0) {
          $pos[$i][bezeichnung] = "*** ".$pos[$i][bezeichnung];
          if($pos[$i][explodiert_parent] > 0)
            $pos[$i][explodiert_parent_artikel] = $this->app->DB->Select("SELECT artikel FROM auftrag_position WHERE id='".$pos[$i][explodiert_parent]."'  LIMIT 1");
          else $pos[$i][explodiert_parent_artikel] = 0;

          //if($pos[$i][explodiert_parent_artikel] > 0) 
          //{ echo "huhuh ".$this->app->DB->Select("SELECT artikel FROM auftrag_position WHERE id='".$pos[$i][explodiert_parent]."'  LIMIT 1"); exit; }
        }

        $this->app->FormHandler->ArrayUpdateDatabase("rechnung_position",$newposid,$pos[$i]);
      }

      // wenn adresse abweichende rechnungsadresse hat diese nehmen!
      if($abweichende_rechnungsadresse=="1")
      {
        $this->LoadRechnungStandardwerte($newid,$adresseid);
        $this->app->FormHandler->ArrayUpdateDatabase("rechnung",$newid,$arr_zahlung[0]);
      }


      // wenn auftrag vorkasse rechnung als bezahlt markieren wenn genug geld vorhanden
      $this->RechnungNeuberechnen($newid);
      //summe zahlungseingaenge
      $summe_zahlungseingaenge = $this->app->DB->Select("SELECT SUM(betrag) FROM kontoauszuege_zahlungseingang WHERE objekt='auftrag' AND parameter='$id' AND firma='".$this->app->User->GetFirma()."'");
      $rechnungssumme = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$newid' LIMIT 1");

      //if(($arr[0][zahlungsweise]=="vorkasse" || $arr[0][zahlungsweise]=="paypal" || $arr[0][zahlungsweise]=="kreditkarte") &&  $summe_zahlungseingaenge >= $rechnungssumme)
      if($summe_zahlungseingaenge >= $rechnungssumme)
      {

        if($summe_zahlungseingaenge >= $rechnungssumme) 
          $this->app->DB->Update("UPDATE rechnung SET ist=soll, zahlungsstatus='bezahlt' WHERE id='$newid' AND firma='".$this->app->User->GetFirma()."'");
        else
          $this->app->DB->Update("UPDATE rechnung SET ist='$summe_zahlungseingaenge', zahlungsstatus='' WHERE id='$newid' AND firma='".$this->app->User->GetFirma()."'");
      }  // was ist denn bei rechnung bar oder nachnahme wenn ein auftragsguthaben vorhanden ist

      $this->app->DB->Update("UPDATE auftrag SET status='abgeschlossen',schreibschutz='1' WHERE id='$id' LIMIT 1");

      // auftrag freigeben!!!

      return $newid;
    }

    function WeiterfuehrenAngebotZuAuftrag($id)
    {
      $anfrageid = $this->app->DB->Select("SELECT anfrageid FROM angebot WHERE id='$id' LIMIT 1");
      if($anfrageid > 0)
        $this->app->DB->Update("UPDATE anfrage SET status='beauftrag',schreibschutz='0' WHERE id='$anfrageid' AND status!='abgeschlossen' AND status!='abgerechnet'");

      //angebot aus offene Angebote entfernen 
      $this->app->DB->Insert("INSERT INTO auftrag (id) VALUES ('')");
      $newid = $this->app->DB->GetInsertID();

      $arr = $this->app->DB->SelectArr("SELECT NOW() as datum, projekt,belegnr as angebot,lieferdatum,aktion,
          freitext,anschreiben,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,vertrieb,zahlungsweise,zahlungszieltage, id as angebotid, anfrage as ihrebestellnummer, anfrageid,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,gruppe,vertriebid,provision,provision_summe,bearbeiter,
          zahlungszieltageskonto,zahlungszielskonto,firma,abweichendelieferadresse,liefername,lieferabteilung,lieferunterabteilung,lieferland,lieferstrasse,lieferort,ust_befreit,
          lieferplz,lieferadresszusatz,lieferansprechpartner,ust_befreit,keinsteuersatz,autoversand,keinporto,'angelegt' as status,waehrung,steuersatz_normal,steuersatz_zwischen,steuersatz_ermaessigt,steuersatz_starkermaessigt,steuersatz_dienstleistung FROM angebot WHERE id='$id' LIMIT 1");
      $this->app->FormHandler->ArrayUpdateDatabase("auftrag",$newid,$arr[0]);

      $pos = $this->app->DB->SelectArr("SELECT * FROM angebot_position WHERE angebot='$id'");
      for($i=0;$i<count($pos);$i++){
        $this->app->DB->Insert("INSERT INTO auftrag_position (id) VALUES('')");
        $newposid = $this->app->DB->GetInsertID();
        $pos[$i][auftrag]=$newid;
        $this->app->FormHandler->ArrayUpdateDatabase("auftrag_position",$newposid,$pos[$i]);
      }

      $belegnr = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$newid' LIMIT 1");
      $this->app->DB->Update("UPDATE angebot SET status='beauftragt', schreibschutz='1', auftrag='$belegnr' WHERE id='$id' LIMIT 1");

      // auftrag freigeben!!!

      return $newid;
    }

    function DeleteAngebot($id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
      if(!is_numeric($belegnr) || $belegnr==0)
      {
        $this->app->DB->Delete("DELETE FROM angebot_position WHERE angebot='$id'");
        $this->app->DB->Delete("DELETE FROM angebot_protokoll WHERE angebot='$id'");
        $this->app->DB->Delete("DELETE FROM angebot WHERE id='$id' LIMIT 1");
      }
    }


    function PaketannahmenAbschliessen()
    {
      $arr = $this->app->DB->SelectArr("SELECT id FROM paketannahme WHERE status!='abgeschlossen'"); 
      for($i=0;$i<count($arr);$i++)
      {


      }
    }

    function CreateBestellung($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM bestellung WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;

      if($adresse>0)
        $tmp_projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");

      if($tmp_projekt <= 0)
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
      else
        $projekt = $tmp_projekt;

      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }

      $belegmax = "";
      $ohnebriefpapier = $this->Firmendaten("bestellung_ohnebriefpapier");
      $bestellungohnepreis = $this->Firmendaten("bestellungohnepreis");

      $this->app->DB->Insert("INSERT INTO bestellung (id,datum,bearbeiter,firma,belegnr,adresse,status,artikelnummerninfotext,ohne_briefpapier,bestellungohnepreis,projekt) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','$adresse','angelegt',1,'".$ohnebriefpapier."','".$bestellungohnepreis."','".$projekt."')");
      $id = $this->app->DB->GetInsertID();
      $this->LoadSteuersaetzeWaehrung($id,"bestellung");
      $this->EventAPIAdd("EventBestellungCreate",$id,"bestellung","create");
      return $id;
    }

    function AddBestellungPosition($bestellung, $einkauf,$menge,$datum)
    {
      $artikel = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$einkauf' LIMIT 1"); 
      $bezeichnunglieferant = $this->app->DB->Select("SELECT bezeichnunglieferant FROM einkaufspreise WHERE id='$einkauf' LIMIT 1"); 
      $bestellnummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise WHERE id='$einkauf' LIMIT 1"); 
      $preis = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE id='$einkauf' LIMIT 1"); 
      $projekt = $this->app->DB->Select("SELECT projekt FROM einkaufspreise WHERE id='$einkauf' LIMIT 1"); 
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM einkaufspreise WHERE id='$einkauf' LIMIT 1"); 
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel' LIMIT 1");
      if($umsatzsteuer=="") $umsatzsteuer="normal";
      $vpe = $this->app->DB->Select("SELECT vpe FROM einkaufspreise WHERE id='$einkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM bestellung_position WHERE bestellung='$bestellung' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO bestellung_position (id,bestellung,artikel,bezeichnunglieferant,bestellnummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$bestellung','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe')");
    }


    function DeleteBestellung($id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
      if(!is_numeric($belegnr) || $belegnr==0)
      {
        $this->app->DB->Delete("DELETE FROM bestellung_position WHERE bestellung='$id'");
        $this->app->DB->Delete("DELETE FROM bestellung_protokoll WHERE bestellung='$id'");
        $this->app->DB->Delete("DELETE FROM bestellung WHERE id='$id' LIMIT 1");
      }
    }


    function CreateRechnung($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM bestellung WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;

      if($adresse>0)
        $tmp_projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");

      if($tmp_projekt <= 0)
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
      else
        $projekt = $tmp_projekt;

      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }



      $belegmax = "";
      $ohnebriefpapier = $this->Firmendaten("rechnung_ohnebriefpapier");
      $this->app->DB->Insert("INSERT INTO rechnung (id,datum,bearbeiter,firma,belegnr,zahlungsweise,
        zahlungszieltage,
        zahlungszieltageskonto,
        zahlungszielskonto,

        status,projekt,adresse,auftragid,ohne_briefpapier) 
          VALUES ('',NOW(),'','".$this->app->User->GetFirma()."','$belegmax','".$this->StandardZahlungsweise($projekt)."',
            '".$this->ZahlungsZielTage($projekt)."',
            '".$this->ZahlungsZielTageSkonto($projekt)."',
            '".$this->ZahlungsZielSkonto($projekt)."',
            'angelegt','$projekt','$adresse',0,'".$ohnebriefpapier."')");

      $id = $this->app->DB->GetInsertID();
      $this->CheckVertrieb($id,"rechnung");
      $this->CheckBearbeiter($id,"rechnung");

      $this->LoadSteuersaetzeWaehrung($id,"rechnung",$projekt);
      $this->EventAPIAdd("EventRechnungCreate",$id,"rechnung","create");
      return $id;
    }


		function GetStandardSteuersatzErmaessigt($projekt=0)
		{

      if($projekt <=0)
        $projekt = $this->app->DB->Select("SELECT projekt FROM $typ WHERE id='$id' LIMIT 1");

      $projekt_arr = $this->app->DB->SelectArr("SELECT * FROM projekt WHERE id='$projekt' LIMIT 1");

      if($projekt_arr[0]['eigenesteuer']=="1")
      {
        $steuersatz_ermaessigt = $projekt_arr[0]['steuersatz_ermaessigt'];
      } else {
        $steuersatz_ermaessigt = $this->Firmendaten("steuersatz_ermaessigt");
      }
			return $steuersatz_ermaessigt;
		}

		function GetStandardSteuersatzNormal($projekt=0)
		{

      if($projekt <=0)
        $projekt = $this->app->DB->Select("SELECT projekt FROM $typ WHERE id='$id' LIMIT 1");


      $projekt_arr = $this->app->DB->SelectArr("SELECT * FROM projekt WHERE id='$projekt' LIMIT 1");

      if($projekt_arr[0]['eigenesteuer']=="1")
      {
        $steuersatz_normal = $projekt_arr[0]['steuersatz_normal'];
        $steuersatz_ermaessigt = $projekt_arr[0]['steuersatz_ermaessigt'];
      } else {
        $steuersatz_normal = $this->Firmendaten("steuersatz_normal");
        $steuersatz_ermaessigt = $this->Firmendaten("steuersatz_ermaessigt");
      }
			return $steuersatz_normal;
		}


    function LoadSteuersaetzeWaehrung($id,$typ,$projekt="")
    {

      if($projekt <=0)
        $projekt = $this->app->DB->Select("SELECT projekt FROM $typ WHERE id='$id' LIMIT 1");


      $projekt_arr = $this->app->DB->SelectArr("SELECT * FROM projekt WHERE id='$projekt' LIMIT 1");

      if($projekt_arr[0]['eigenesteuer']=="1")
      {
        $steuersatz_normal = $projekt_arr[0]['steuersatz_normal'];
        $steuersatz_ermaessigt = $projekt_arr[0]['steuersatz_ermaessigt'];
        $waehrung = $projekt_arr[0]['waehrung'];
      } else {
        $steuersatz_normal = $this->Firmendaten("steuersatz_normal");
        $steuersatz_ermaessigt = $this->Firmendaten("steuersatz_ermaessigt");
        $waehrung = $this->Firmendaten("waehrung");
      }

      if($waehrung=="") $waehrung="EUR";

      $this->app->DB->Update("UPDATE $typ SET waehrung='$waehrung',
          steuersatz_normal='$steuersatz_normal',steuersatz_ermaessigt='$steuersatz_ermaessigt' WHERE id='$id' LIMIT 1");
    }


    function CreateGutschrift($adresse="")
    {
      //$belegmax = $this->app->DB->Select("SELECT MAX(belegnr) FROM bestellung WHERE firma='".$this->app->User->GetFirma()."'");
      //if($belegmax==0) $belegmax = 10000;  else $belegmax++;
      if($adresse>0)
        $tmp_projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$adresse' LIMIT 1");

      if($tmp_projekt <= 0)
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");
      else
        $projekt = $tmp_projekt;

      $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      if($projekt_bevorzugt=="1")
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      }


      $belegmax = "";
      $ohnebriefpapier = $this->Firmendaten("gutschrift_ohnebriefpapier");

      $this->app->DB->Insert("INSERT INTO gutschrift (id,datum,bearbeiter,firma,belegnr,zahlungsweise,zahlungszieltage,status,projekt,adresse,ohne_briefpapier) 
          VALUES ('',NOW(),'".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$belegmax','".$this->StandardZahlungsweise($projekt)."','".$this->ZahlungsZielTage($projekt)."','angelegt','$projekt','$adresse','".$ohnebriefpapier."')");

      $id = $this->app->DB->GetInsertID();
      $this->CheckVertrieb($id,"gutschrift");
      $this->CheckBearbeiter($id,"gutschrift");


      $this->LoadSteuersaetzeWaehrung($id,"gutschrift");
      $this->EventAPIAdd("EventGutschriftCreate",$id,"gutschrift","create");
      return $id;
    }


    function AddGutschritPosition($gutschrift, $verkauf,$menge,$datum)
    {
      $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
      $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
      $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer  FROM artikel WHERE id='$artikel' LIMIT 1");
      $vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM gutschrift_position WHERE rechnung='$gutschrift' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO gutschrift_position (id,gutschrift,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$gutschrift','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe')");
    }


    function AddRechnungPositionManuell($rechnung, $artikel,$preis, $menge,$bezeichnung,$beschreibung="")
    {
      $bezeichnunglieferant = $bezeichnung;
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$artikel' LIMIT 1");
      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $waehrung='EUR';
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer  FROM artikel WHERE id='$artikel' LIMIT 1");
      //$vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM rechnung_position WHERE rechnung='$rechnung' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO rechnung_position (id,rechnung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,beschreibung) 
          VALUES ('','$rechnung','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe','$beschreibung')");
    }



    function AddRechnungPosition($rechnung, $verkauf,$menge,$datum)
    {
      $artikel = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
      $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
      $bestellnummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
      $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$verkauf' LIMIT 1");
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel' LIMIT 1");
      $vpe = $this->app->DB->Select("SELECT vpe FROM verkaufspreise WHERE id='$verkauf' LIMIT 1"); 
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM rechnung_position WHERE rechnung='$rechnung' LIMIT 1");
      $sort = $sort + 1;
      $this->app->DB->Insert("INSERT INTO rechnung_position (id,rechnung,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
          VALUES ('','$rechnung','$artikel','$bezeichnunglieferant','$bestellnummer','$menge','$preis','$waehrung','$sort','$datum','$umsatzsteuer','angelegt','$projekt','$vpe')");
    }

    // Produktion und Auftrag reservieren
    function AuftragReservieren($id,$typ="auftrag")
    {
      if($typ=="auftrag" && $id > 0)
        $id_check = $this->app->DB->Select("SELECT id FROM auftrag WHERE status='freigegeben' AND id='$id' LIMIT 1");
      if($typ=="produktion"){
        $id_check = $this->app->DB->Select("SELECT id FROM produktion WHERE status!='storniert' AND status!='abgeschlossen' AND status!='gestartet' AND id='$id' LIMIT 1");
        $reservierart = $this->app->DB->Select("SELECT reservierart FROM produktion WHERE status!='storniert' AND status!='abgeschlossen' AND status!='gestartet' AND id='$id' LIMIT 1");
        $status = $this->app->DB->Select("SELECT status FROM produktion WHERE status!='storniert' AND status!='abgeschlossen' AND status!='gestartet' AND id='$id' LIMIT 1");
      }

      // nicht reservieren wenn auftrag nicht offen ist
      if($id_check!=$id)
        return 0;

      //echo "$typ $reservierart $status";
      // bei produktion erst wenn es erlaubt ist
      if($typ=="produktion" && $reservierart=="freigabe" && $status=="angelegt")
      {
        return 0;
      }
      if($typ=="produktion" && $reservierart=="abschluss" && $status=="angelegt")
      {
        return 0;
      }
      if($typ=="produktion" && $reservierart=="abschluss" && $status=="freigegeben")
      {
        return 0;
      }


      if($typ=="auftrag")
      {
        $artikelarr= $this->app->DB->SelectArr("SELECT * FROM auftrag_position WHERE auftrag='$id' AND geliefert!=1");
        $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$id' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id='$id' LIMIT 1");
        $belegnr= $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$id' LIMIT 1");
      }

      if($typ=="produktion")
      {
        // loesche alle reservierungen
        $this->app->DB->Delete("DELETE FROM  lager_reserviert WHERE parameter='$id' AND objekt='produktion'");  
        $artikelarr= $this->app->DB->SelectArr("SELECT * FROM produktion_position WHERE produktion='$id' AND geliefert!=1");
        $adresse = $this->app->DB->Select("SELECT adresse FROM produktion WHERE id='$id' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM produktion WHERE id='$id' LIMIT 1");
        $belegnr= $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$id' LIMIT 1");
      }

      //schaue artikel fuer artikel an wieviel geliefert wurde und ob bereits reservierungen vorliegen, wenn welche vorliegen auch reservieren auf 9999-01-01
      // Lager Check
      //echo "{$auftraege[0][internet]} Adresse:$adresse Auftrag $auftrag";
      for($k=0;$k<count($artikelarr); $k++)
      { 
        $menge = $artikelarr[$k][menge] - $artikelarr[$k][gelieferte_menge];
        $artikel = $artikelarr[$k][artikel];
        // pruefe artikel 12 menge 4
        $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");
        $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='{$artikelarr[$k][artikel]}' LIMIT 1");
        //if($artikelarr[$k][nummer]!="200000" && $artikelarr[$k][nummer]!="200001")
        if($lagerartikel>=1)
        {

          if($typ=="auftrag")
          {   
            $anzahl_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert 
                WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND objekt='auftrag' AND parameter='$id'");
          }


          if($typ=="produktion")
          {
            $anzahl_reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert 
                WHERE artikel='".$artikel."' AND firma='".$this->app->User->GetFirma()."' AND objekt='produktion' AND parameter='$id'");
          }

          // menge = notwendige menge - bereits reserviert        
          $zu_reservieren = $menge - $anzahl_reserviert;  
          if($zu_reservieren>0)
          {
            if($typ=="auftrag"){

              //if($this->LagerFreieMenge($artikel) <  $zu_reservieren) continue;
              if($this->LagerFreieMenge($artikel) <=0) continue;

              // die restliche menge auf den Auftrag reservieren
              if($this->LagerFreieMenge($artikel) < $zu_reservieren)
                $zu_reservieren = $this->LagerFreieMenge($artikel);

              // schaue ob es artikel in reserivierungen fuer diesen auftrag schon gibt dann erhoehe
              $check = $this->app->DB->Select("SELECT menge FROM lager_reserviert WHERE artikel='$artikel'
                  AND objekt='auftrag' AND parameter='$id' LIMIT 1");

              if($check > 0)
              {
                $this->app->DB->Update("UPDATE lager_reserviert SET menge = menge + '$zu_reservieren' WHERE
                    artikel='$artikel'
                    AND objekt='auftrag' AND parameter='$id'");
              } else {

                $this->app->DB->Insert("INSERT INTO lager_reserviert 
                    (id,adresse,artikel,menge,grund,projekt,firma,bearbeiter,datum,objekt,parameter)
                    VALUES('','$adresse','$artikel','$zu_reservieren','Reservierung f&uuml;r Auftrag $belegnr','$projekt',
                      '".$this->app->User->GetFirma()."','".$this->app->User->GetName()."','999-99-99','auftrag','$id')");
              }

            }
            // nur wenn keine stueckliste in der Produktion
            if($typ=="produktion" && $stueckliste!=1){

              // Reservierungen nur erlauben wenn genügend freie Artikel vorhanden sind!

              if($this->LagerFreieMenge($artikel) <  $zu_reservieren) continue;

              // schaue ob es artikel in reserivierungen fuer diesen auftrag schon gibt dann erhoehe
              // schaue ob es artikel in reserivierungen fuer diesen auftrag schon gibt dann erhoehe
              $check = $this->app->DB->Select("SELECT menge FROM  lager_reserviert WHERE artikel='$artikel'
                  AND objekt='produktion' AND parameter='$id' LIMIT 1");

              if($check > 0)
              {
                $this->app->DB->Update("UPDATE lager_reserviert SET menge = menge + '$zu_reservieren' WHERE
                    artikel='$artikel'
                    AND objekt='produktion' AND parameter='$id'");
              } else {
                $kundename = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
                if($belegnr==0) $belegnr = "Kunde: $kundename";
                $this->app->DB->Insert("INSERT INTO lager_reserviert 
                    (id,adresse,artikel,menge,grund,projekt,firma,bearbeiter,datum,objekt,parameter)
                    VALUES('','$adresse','$artikel','$zu_reservieren','Reservierung f&uuml;r Produktion $belegnr','$projekt',
                      '".$this->app->User->GetFirma()."','".$this->app->User->GetName()."','999-99-99','produktion','$id')");
              }


            }
          }
        }
      }
    }



    function AuftragNeuberechnenAllen()
    {
      $arrAuftrag = $this->app->DB->SelectArr("SELECT id FROM auftrag WHERE status!='abgeschlossen' AND status!='storniert' order by datum");

      for($i=0;$i < count($arrAuftrag); $i++)
      {
        $this->AuftragNeuberechnen($arrAuftrag[$i][id]);
      }   
    }

    function BestellungNeuberechnen($id)
    {

      $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung  WHERE id='$id' LIMIT 1");
      //if(!is_numeric($belegnr) || $belegnr==0)
      {
        $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position WHERE umsatzsteuer!='ermaessigt' AND bestellung='$id'");
        $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position WHERE umsatzsteuer='ermaessigt' AND bestellung='$id'");

        $summeNetto = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position WHERE bestellung='$id'");

        $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM bestellung WHERE id='$id' LIMIT 1");

        if($ust_befreit>0)
        {
          $rechnungsbetrag = $summeNetto;
        } else {
          $rechnungsbetrag = $summeNetto + ($summeV*$this->GetSteuersatzNormal(true,$id,"bestellung")-$summeV)+ ($summeR*$this->GetSteuersatzErmaessigt(true,$id,"bestellung")-$summeR);
        }
        $this->app->DB->Update("UPDATE bestellung SET gesamtsumme='$rechnungsbetrag' WHERE id='$id' LIMIT 1");
      }

    }


    function AngebotNeuberechnen($id)
    {
      $this->ANABREGSNeuberechnen($id,"angebot");
      /*
         $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");
      //if(!is_numeric($belegnr) || $belegnr==0)
      {
      $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position WHERE umsatzsteuer!='ermaessigt' AND angebot='$id'");
      $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position WHERE umsatzsteuer='ermaessigt' AND angebot='$id'");

      $summeNetto = $this->app->DB->Select("SELECT SUM(menge*preis) FROM angebot_position WHERE angebot='$id'");

      $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM angebot WHERE id='$id' LIMIT 1");

      if($ust_befreit>0)
      {
      $rechnungsbetrag = $summeNetto;
      } else {
      $rechnungsbetrag = $summeNetto + ($summeV*1.19-$summeV)+ ($summeR*1.07-$summeR);
      }
      $this->app->DB->Update("UPDATE angebot SET gesamtsumme='$rechnungsbetrag' WHERE id='$id' LIMIT 1");
      }
       */
    }


    function ProduktionNeuberechnen($id)
    {
      if($this->app->Conf->WFdbType=="postgre") {
        if(is_numeric($id)) {
          $belegnr = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$id' LIMIT 1");
          //if(!is_numeric($belegnr) || $belegnr==0)
          {
            $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE umsatzsteuer!='ermaessigt' AND produktion='$id'");
            $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE umsatzsteuer='ermaessigt' AND produktion='$id'");

            $summeNetto = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE produktion='$id'");

            $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM produktion WHERE id='$id' LIMIT 1");

            if($ust_befreit>0)
            {
              $rechnungsbetrag = $summeNetto;
            } else {
              $rechnungsbetrag = $summeNetto + ($summeV*$this->GetSteuersatzNormal(true,$id,"produktion")-$summeV)+ ($summeR*$this->GetSteuersatzErmaessigt(true,$id,"produktion")-$summeR);
            }
            $this->app->DB->Update("UPDATE produktion SET gesamtsumme='$rechnungsbetrag' WHERE id='$id' LIMIT 1");
          }
        }
      } else {
        $belegnr = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$id' LIMIT 1");
        //if(!is_numeric($belegnr) || $belegnr==0)
        {
          $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE umsatzsteuer!='ermaessigt' AND produktion='$id'");
          $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE umsatzsteuer='ermaessigt' AND produktion='$id'");

          $summeNetto = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE produktion='$id'");

          $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM produktion WHERE id='$id' LIMIT 1");

          if($ust_befreit>0)
          {
            $rechnungsbetrag = $summeNetto;
          } else {
            $rechnungsbetrag = $summeNetto + ($summeV*$this->GetSteuersatzNormal(true,$id,"produktion")-$summeV)+ ($summeR*$this->GetSteuersatzErmaessigt(true,$id,"produktion")-$summeR);
          }
          $this->app->DB->Update("UPDATE produktion SET gesamtsumme='$rechnungsbetrag' WHERE id='$id' LIMIT 1");
        }   
      }
    }



    function AuftragNeuberechnen($id)
    {
      $this->ANABREGSNeuberechnen($id,"auftrag");
    }



    function GutschriftNeuberechnen($id)
    {
      $this->ANABREGSNeuberechnen($id,"gutschrift");
    }


    function DeleteGutschrift($id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");
      if(!is_numeric($belegnr) || $belegnr==0)
      {
        $this->app->DB->Delete("DELETE FROM gutschrift_position WHERE gutschrift='$id'");
        $this->app->DB->Delete("DELETE FROM gutschrift_protokoll WHERE gutschrift='$id'");
        $this->app->DB->Delete("DELETE FROM gutschrift WHERE id='$id' LIMIT 1");
      }
    }

    function ANABREGSNeuberechnen($id,$art)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM $art WHERE id='$id' LIMIT 1");
      $adresse =  $this->app->DB->Select("SELECT adresse FROM $art WHERE id='$id' LIMIT 1");
      $status =  $this->app->DB->Select("SELECT status FROM $art WHERE id='$id' LIMIT 1");

      if($art=="auftrag")
      {
        // abweichende lieferadresse name loeschen wenn es keine gibt
        $abweichendelieferadresse = $this->app->DB->Select("SELECT abweichendelieferadresse FROM auftrag WHERE id='$id' LIMIT 1");
        $liefername = $this->app->DB->Select("SELECT liefername FROM auftrag WHERE id='$id' LIMIT 1");
        if($liefername=="" && $abweichendelieferadresse=="1")
          $this->app->DB->Update("UPDATE auftrag SET abweichendelieferadresse=0 WHERE id='$id' LIMIT 1");
      }


      //$ust_befreit = $this->AdresseUSTCheck($adresse);
      //$this->app->DB->Update("UPDATE rechnung SET ust_befreit='$ust_befreit' WHERE id='$id' LIMIT 1");
      if($this->Firmendaten("modul_verband")=="1")
      {
        if($art=="angebot" || $art=="auftrag" || $art=="rechnung" || $art=="gutschrift")
        {
          $grundrabatt = $this->app->DB->Select("SELECT rabatt FROM $art WHERE id='$id' LIMIT 1");
          $rabatt1 = $this->app->DB->Select("SELECT rabatt1 FROM $art WHERE id='$id' LIMIT 1");
          $rabatt2 = $this->app->DB->Select("SELECT rabatt2 FROM $art WHERE id='$id' LIMIT 1");
          $rabatt3 = $this->app->DB->Select("SELECT rabatt3 FROM $art WHERE id='$id' LIMIT 1");
          $rabatt4 = $this->app->DB->Select("SELECT rabatt4 FROM $art WHERE id='$id' LIMIT 1");
          $rabatt5 = $this->app->DB->Select("SELECT rabatt5 FROM $art WHERE id='$id' LIMIT 1");


          if($grundrabatt>0) $rabattarr[] =  ((100-$grundrabatt)/100.0);
          if($rabatt1>0) $rabattarr[] = ((100-$rabatt1)/100.0);
          if($rabatt2>0) $rabattarr[] = ((100-$rabatt2)/100.0);
          if($rabatt3>0) $rabattarr[] = ((100-$rabatt3)/100.0);
          if($rabatt4>0) $rabattarr[] = ((100-$rabatt4)/100.0);
          if($rabatt5>0) $rabattarr[]=  ((100-$rabatt5)/100.0);

          $rabatt=1;
          for($i=0;$i<count($rabattarr);$i++)
          {
            if($rabattarr[$i] > 0 && $rabattarr[$i] < 1) $rabatt = $rabatt * $rabattarr[$i];
          }

          $rabatt=(1-$rabatt)*100;

          $this->app->DB->Update("UPDATE $art SET realrabatt='$rabatt' WHERE id='$id' LIMIT 1");
        }


        // Rabatt Sync starten
        $artikelarr = $this->app->DB->SelectArr("SELECT * FROM ".$art."_position WHERE ".$art."='$id'");        
        $adresse = $this->app->DB->Select("SELECT adresse FROM ".$art." WHERE id='$id'");       

        for($i=0;$i<count($artikelarr);$i++)
        {
          // kopiere rabatte zum ersten mal
          if($artikelarr[$i]['rabattsync']!="1")
          {
            // pruefe ob artikel rabatt bekommen darf
            $check_keinrabatterlaubt = $this->app->DB->Select("SELECT keinrabatterlaubt FROM artikel WHERE id='".$artikelarr[$i]['artikel']."' LIMIT 1");
            $check_porto = $this->app->DB->Select("SELECT porto FROM artikel WHERE id='".$artikelarr[$i]['artikel']."' LIMIT 1");
            $check_rabatt = $this->app->DB->Select("SELECT rabatt FROM artikel WHERE id='".$artikelarr[$i]['artikel']."' LIMIT 1");

            // Keine Rabatte auf Spezialpreise
            if($this->IsSpezialVerkaufspreis($artikelarr[$i]['artikel'],$artikelarr[$i]['menge'],$adresse))
            {
              $check_keinrabatterlaubt="1";
            }

            if($check_keinrabatterlaubt!="1" && $check_porto!="1" && $check_rabatt!="1")
            {
              $this->app->DB->Update("UPDATE ".$art."_position SET rabattsync='1',
                  grundrabatt='$grundrabatt', rabatt1='$rabatt1', rabatt2='$rabatt2', rabatt3='$rabatt3', rabatt4='$rabatt4', rabatt4='$rabatt5',
                  keinrabatterlaubt='0' WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
            } else {
              if($check_porto=="1")
                $this->app->DB->Update("UPDATE ".$art."_position SET rabattsync='1',keinrabatterlaubt='0',rabatt='0' WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
              else
                $this->app->DB->Update("UPDATE ".$art."_position SET rabattsync='1',keinrabatterlaubt='1',rabatt='0' WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
            }
          }

          // rechne rabatt fuer position aus
          $grundrabatt_sub = $this->app->DB->Select("SELECT grundrabatt FROM ".$art."_position WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
          $rabatt1_sub = $this->app->DB->Select("SELECT rabatt1 FROM ".$art."_position WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
          $rabatt2_sub = $this->app->DB->Select("SELECT rabatt2 FROM ".$art."_position WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
          $rabatt3_sub = $this->app->DB->Select("SELECT rabatt3 FROM ".$art."_position WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
          $rabatt4_sub = $this->app->DB->Select("SELECT rabatt4 FROM ".$art."_position WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
          $rabatt5_sub = $this->app->DB->Select("SELECT rabatt5 FROM ".$art."_position WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
          $keinrabatterlaubt_sub = $this->app->DB->Select("SELECT keinrabatterlaubt FROM ".$art."_position WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");

          $rabattarr = array();

          if($grundrabatt_sub>0) $rabattarr[] =  ((100-$grundrabatt_sub)/100.0);
          if($rabatt1_sub>0) $rabattarr[] = ((100-$rabatt1_sub)/100.0);
          if($rabatt2_sub>0) $rabattarr[] = ((100-$rabatt2_sub)/100.0);
          if($rabatt3_sub>0) $rabattarr[] = ((100-$rabatt3_sub)/100.0);
          if($rabatt4_sub>0) $rabattarr[] = ((100-$rabatt4_sub)/100.0);
          if($rabatt5_sub>0) $rabattarr[]=  ((100-$rabatt5_sub)/100.0);

          $rabatt=1;
          for($ij=0;$ij<count($rabattarr);$ij++)
          {
            if($rabattarr[$ij] > 0 && $rabattarr[$ij] < 1) $rabatt = $rabatt * $rabattarr[$ij];
          }

          $rabatt=(1-$rabatt)*100;

          // wenn kein rabatt fuer die Position erlaubt ist                       
          if($keinrabatterlaubt_sub=="1") $rabatt=0;
          $this->app->DB->Update("UPDATE ".$art."_position SET rabatt='$rabatt' WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");


        }
      }
      // rabatt positionen berechnen also die, die auf den gesamten auftrag gehen
      $betrag = $this->ANABREGSNeuberechnenGesamtsummeOhnePortoUndKeinRabatt($id,$art);
      for($i=0;$i<count($artikelarr);$i++)
      {
        $check_rabatt_artikel = $this->app->DB->Select("SELECT rabatt FROM artikel WHERE id='".$artikelarr[$i]['artikel']."' LIMIT 1");
        if($check_rabatt_artikel=="1")
        {
          $check_rabatt_artikel_prozente = $this->app->DB->Select("SELECT rabatt_prozent FROM artikel WHERE id='".$artikelarr[$i]['artikel']."' LIMIT 1");

          if($check_rabatt_artikel_prozente>0)
          {
            $this->app->DB->Update("UPDATE ".$art."_position SET menge='1', preis='".((($betrag/100)*$check_rabatt_artikel_prozente)*-1)."' WHERE id='".$artikelarr[$i]['id']."' LIMIT 1");
          }
        }
      }       

      // porto berechnen
      $portoid = $this->app->DB->Select("SELECT tp.id FROM ".$art."_position tp LEFT JOIN artikel a ON a.id=tp.artikel
          WHERE a.porto='1' AND a.geloescht!='1' AND tp.".$art."='$id' LIMIT 1");
      $portoartikel = $this->app->DB->Select("SELECT artikel FROM ".$art."_position WHERE id='".$portoid."' LIMIT 1");
      $keinrabatterlaubt_sub = $this->app->DB->Select("SELECT keinrabatterlaubt FROM ".$art."_position WHERE id='".$portoid."' LIMIT 1");

      $betrag = $this->ANABREGSNeuberechnenGesamtsummeOhnePorto($id,$art);
      if($portoid > 0 && $keinrabatterlaubt_sub!="1" && $this->Firmendaten("porto_berechnen"))
      {
        $this->app->DB->Update("UPDATE ".$art."_position SET menge='1',preis='".$this->PortoBerechnen($adresse,$betrag,$portoartikel)."' WHERE id='".$portoid ."' LIMIT 1");
      }


      $this->ANABREGSNeuberechnenGesamtsumme($id,$art);

      if($art=="auftrag")
      {
        //tatsaechlicheslieferdatum

        $tatsaechlicheslieferdatum = $this->app->DB->Select("SELECT tatsaechlicheslieferdatum FROM auftrag WHERE id='$id' LIMIT 1");
        $lieferdatum = $this->app->DB->Select("SELECT lieferdatum FROM auftrag WHERE id='$id' LIMIT 1");
        if(($tatsaechlicheslieferdatum=="0000-00-00" || $tatsaechlicheslieferdatum=="") && $lieferdatum!="0000-00-00")
          $this->app->DB->Update("UPDATE auftrag SET tatsaechlicheslieferdatum=DATE_SUB(lieferdatum, INTERVAL 2 DAY) WHERE id='$id' LIMIT 1");


        $this->EventAPIAdd("EventAuftragEdit",$id,"auftrag","edit");
      }

    	$this->BerechneDeckungsbeitrag($id,$art);
    }     

    function PortoBerechnen($adresse,$gesamtsumme,$portoartikel)
    {
      // schaue ob Kunde eine Regel hat       
      $checkportofrei = $this->app->DB->Select("SELECT portofrei_aktiv FROM adresse WHERE id='".$adresse."' LIMIT 1");
      if($checkportofrei=="1")
      {
        $checkportofreiab = $this->app->DB->Select("SELECT portofreiab FROM adresse WHERE id='".$adresse."' LIMIT 1");
        if($gesamtsumme >= $checkportofreiab)
        {
          return 0;
        } else {
          // wenn kundenpreis vorhanden dann den holen sonst verband      
          $tmppreis = $this->GetVerkaufspreisKunde($portoartikel,1,$adresse);
          if($tmppreis > 0)
            return $tmppreis;
          else    
            return $this->GetVerkaufspreis($portoartikel,1,$adresse);
        }
      } else {
        $gruppenarray = $this->GetGruppen($adresse);

        for($gi=0;$gi<count($gruppenarray);$gi++)
        {
          $sql_erweiterung .= " id='".$gruppenarray[$gi]."' ";

          if($gi<count($gruppenarray)-1)
            $sql_erweiterung .= " OR ";
        } 
        $checkgruppeportofrei = $this->app->DB->Select("SELECT id FROM gruppen WHERE ($sql_erweiterung) AND portofrei_aktiv='1' ORDER BY portofreiab LIMIT 1");
        if($checkgruppeportofrei>0)
        {
          $checkgruppeportofreiab = $this->app->DB->Select("SELECT portofreiab FROM gruppen WHERE id='".$checkgruppeportofrei."' LIMIT 1");
          if($gesamtsumme >= $checkgruppeportofreiab)
          {
            return 0;
          } else {
            // hole kunden preis
            // wenn nicht vorhanden dann Standardpreis
            $tmppreis = $this->GetVerkaufspreisKunde($portoartikel,1,$adresse);
            if($tmppreis > 0)
              return $tmppreis;
            else    
              return $this->GetVerkaufspreis($portoartikel,1,$adresse);

            //return $this->GetVerkaufspreis($portoartikel,1,$adresse);
          }       
        }       
        // oder gruppe hat einen versandpreis?
        // billigsten versandpreis bei dem betrag
      }

      // sollte nicht passieren
      return $this->GetVerkaufspreis($portoartikel,1,$adresse);
    }

    function ANABREGSNeuberechnenGesamtsummeOhnePortoUndKeinRabatt($id,$art)
    {
      //inkl. kein rabatt erlaubt
      $summeNetto = $this->app->DB->Select("SELECT SUM(tp.menge*(tp.preis-(tp.preis/100*tp.rabatt))) FROM ".$art."_position tp
          LEFT JOIN artikel a ON a.id=tp.artikel WHERE a.porto!='1' AND (a.rabatt!='1' OR a.rabatt IS NULL) AND a.keinrabatterlaubt!='1' AND tp.".$art."='$id'");
      return $summeNetto;
    }


    function ANABREGSNeuberechnenGesamtsummeOhnePorto($id,$art)
    {
      //inkl. kein rabatt erlaubt
      $summeNetto = $this->app->DB->Select("SELECT SUM(tp.menge*(tp.preis-(tp.preis/100*tp.rabatt))) FROM ".$art."_position tp
          LEFT JOIN artikel a ON a.id=tp.artikel WHERE a.porto!='1' AND (a.rabatt!='1' OR a.rabatt IS NULL) AND tp.".$art."='$id'");
      return $summeNetto;
    }


    function ANABREGSNeuberechnenGesamtsumme($id,$art,$return_netto=false)
    {
      //if(!is_numeric($belegnr) || $belegnr==0)


      if($art=="angebot")
      { 
        $summeV = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM ".$art."_position WHERE umsatzsteuer!='ermaessigt' AND ".$art."='$id' AND optional!=1");
        $summeR = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM ".$art."_position WHERE umsatzsteuer='ermaessigt' AND ".$art."='$id' AND optional!=1");
        $summeNetto = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM ".$art."_position WHERE ".$art."='$id' AND optional!=1");
      } else {
        $summeV = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM ".$art."_position WHERE umsatzsteuer!='ermaessigt' AND ".$art."='$id'");
        $summeR = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM ".$art."_position WHERE umsatzsteuer='ermaessigt' AND ".$art."='$id'");
        $summeNetto = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM ".$art."_position WHERE ".$art."='$id'");
      }


      $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM $art WHERE id='$id' LIMIT 1");

      if($ust_befreit>0)
      {
        $betrag = $summeNetto;
      } else {
        $betrag = $summeNetto + ($summeV*$this->GetSteuersatzNormal(true,$id,$art)-$summeV)+ ($summeR*$this->GetSteuersatzErmaessigt(true,$id,$art)-$summeR);
      }

      if($return_netto) return $betrag;

      if($art=="rechnung" || $art=="gutschrift")
        $this->app->DB->Update("UPDATE $art SET soll='$betrag' WHERE id='$id' LIMIT 1");
      else
        $this->app->DB->Update("UPDATE $art SET gesamtsumme='$betrag' WHERE id='$id' LIMIT 1");




    }


    function RechnungZwischensummeSteuersaetzeBrutto2($id,$art="ermaessigt")
    {
      //$summeV = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer!='ermaessigt' AND rechnung='$id'");
      $summe = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer='ermaessigt' AND rechnung='$id'");
      $ermaessigt_summe = $summe*$this->GetSteuersatzErmaessigt(true,$id,"rechnung");
      return $ermaessigt_summe;
    }


    function RechnungZwischensummeSteuersaetzeBrutto($id,$art="ermaessigt")
    {
      //$summeV = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer!='ermaessigt' AND rechnung='$id'");
      $summe = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer='ermaessigt' AND rechnung='$id'");
      $ermaessigt_summe = $summe*$this->GetSteuersatzErmaessigt(true,$id,"rechnung");
      return number_format($ermaessigt_summe,",");
    }

    function GutschriftZwischensummeSteuersaetzeBrutto2($id,$art="ermaessigt")
    {
      //$summeV = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer!='ermaessigt' AND rechnung='$id'");
      $summe = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM gutschrift_position WHERE umsatzsteuer='ermaessigt' AND gutschrift='$id'");
      $ermaessigt_summe = $summe*$this->GetSteuersatzErmaessigt(true,$id,"gutschrift");
      return $ermaessigt_summe;
    }


    function GutschriftZwischensummeSteuersaetzeBrutto($id,$art="ermaessigt")
    {
      //$summeV = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer!='ermaessigt' AND rechnung='$id'");
      $summe = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM gutschrift_position WHERE umsatzsteuer='ermaessigt' AND gutschrift='$id'");
      $ermaessigt_summe = $summe*$this->GetSteuersatzErmaessigt(true,$id,"gutschrift");
      return number_format($ermaessigt_summe,",");
    }



    function RechnungNeuberechnen($id)
    {
      $this->ANABREGSNeuberechnen($id,"rechnung");
      /*
         $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
         $adresse =  $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
      //$ust_befreit = $this->AdresseUSTCheck($adresse);
      //$this->app->DB->Update("UPDATE rechnung SET ust_befreit='$ust_befreit' WHERE id='$id' LIMIT 1");


      //if(!is_numeric($belegnr) || $belegnr==0)
      {
      $summeV = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer!='ermaessigt' AND rechnung='$id'");
      $summeR = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE umsatzsteuer='ermaessigt' AND rechnung='$id'");

      $summeNetto = $this->app->DB->Select("SELECT SUM(menge*(preis-(preis/100*rabatt))) FROM rechnung_position WHERE rechnung='$id'");

      $ust_befreit = $this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$id' LIMIT 1");

      if($ust_befreit>0)
      {
      $rechnungsbetrag = $summeNetto;
      } else {
      $rechnungsbetrag = $summeNetto + ($summeV*1.19-$summeV)+ ($summeR*1.07-$summeR);
      }

      $this->app->DB->Update("UPDATE rechnung SET soll='$rechnungsbetrag' WHERE id='$id' LIMIT 1");
      }
       */
    }


    function DeleteRechnung($id)
    {
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
      if(!is_numeric($belegnr) || $belegnr==0)
      {
        $this->app->DB->Delete("DELETE FROM rechnung_position WHERE rechnung='$id'");
        $this->app->DB->Delete("DELETE FROM rechnung_protokoll WHERE rechnung='$id'");
        $this->app->DB->Delete("DELETE FROM rechnung WHERE id='$id' LIMIT 1");
      }
    }


    function GetUserKalender($adresse)
    {
      return $this->app->DB->SelectArr("SELECT id, name, farbe FROM kalender WHERE id IN (SELECT kalender FROM kalender_user WHERE adresse = $adresse);");
    }

    function GetAllKalender($adresse="")
    {
      return $this->app->DB->SelectArr("SELECT id, name, farbe".($adresse!=""?", IFNULL((SELECT 1 FROM kalender_user WHERE adresse=$adresse AND kalender_user.kalender=kalender.id),0) zugriff":"")." FROM kalender;");
    }

    function GetUserKalenderIds($adresse)
    {
      $arr = array();
      foreach ($this->GetUserKalender($adresse) as $value)
        array_push($arr,$value["id"]);
      return $arr;
    }

    function GetAllKalenderIds($adresse="")
    {
      $arr = array();
      foreach ($this->GetAllKalender($adresse) as $value)
        array_push($arr,$value["id"]);
      return $arr;
    }

    function GetKalenderSelect($adresse,$selectedKalender=array())
    {
      $arr = $this->GetUserKalender($adresse);
      foreach($arr as $value)
      { 
        $tmp = (in_array($value["id"],$selectedKalender))?" selected=\"selected\"":"";
        $ret .= "<option value=\"".$value["id"]."\"$tmp>".$value["name"]."</option>";
      }
      return $ret;
    }

    function GetKwSelect($selectedKW="")
    {
      foreach(range(1,52) as $kw)
      { 
        $tmp = ($selectedKW==$kw)?" selected=\"selected\"":"";
        $ret .= "<option value=\"$kw\"$tmp>$kw</option>";
      }
      return $ret;
    }

    function GetYearSelect($selectedYear="", $yearsBefore=2, $yearsAfter=10)
    {
      foreach(range(date("Y")-$yearsBefore, date("Y")+$yearsAfter) as $year)
      { 
        $tmp = ($selectedYear==$year)?" selected=\"selected\"":"";
        $ret .= "<option value=\"$year\"$tmp>$year</option>";
      }
      return $ret;
    }

    function DownloadFile($url,$label="tmp")
    {
      $tmpname = tempnam($this->GetTMP(),$label);
      file_put_contents($tmpname, fopen($url, 'r'));
      return $tmpname;
    }

    function CreateDateiOhneInitialeVersion($titel,$beschreibung,$nummer,$ersteller,$without_log=false)
    {
      if(!$without_log)
      {
        $this->app->DB->Insert("INSERT INTO datei (id,titel,beschreibung,nummer,firma) VALUES
            ('','$titel','$beschreibung','$nummer','".$this->app->User->GetFirma()."')");
      } else {
        $this->app->DB->InsertWithoutLog("INSERT INTO datei (id,titel,beschreibung,nummer,firma) VALUES
            ('','$titel','$beschreibung','$nummer',1)");
      }

      $fileid = $this->app->DB->GetInsertID();
      //$this->AddDateiVersion($fileid,$ersteller,$name,"Initiale Version",$datei,$without_log);

      return  $fileid;
    }


    function CreateDatei($name,$titel,$beschreibung,$nummer,$datei,$ersteller,$without_log=false,$path="")
    {
      if(!$without_log)
      {
        $this->app->DB->Insert("INSERT INTO datei (id,titel,beschreibung,nummer,firma) VALUES
            ('','$titel','$beschreibung','$nummer','".$this->app->User->GetFirma()."')");
      } else {
        $this->app->DB->InsertWithoutLog("INSERT INTO datei (id,titel,beschreibung,nummer,firma) VALUES
            ('','$titel','$beschreibung','$nummer',1)");
      }

      $fileid = $this->app->DB->GetInsertID();
      $this->AddDateiVersion($fileid,$ersteller,$name,"Initiale Version",$datei,$without_log,$path);

      return  $fileid;
    }

    function AddDateiVersion($id,$ersteller,$dateiname, $bemerkung,$datei,$without_log=false,$path="")
    {
      // ermittle neue Version
      $version = $this->app->DB->Select("SELECT COUNT(id) FROM datei_version WHERE datei='$id'") + 1;

      // speichere werte ab 
      if(!$without_log)
      {
        $this->app->DB->Insert("INSERT INTO datei_version (id,datei,ersteller,datum,version,dateiname,bemerkung)
            VALUES ('','$id','$ersteller',NOW(),'$version','$dateiname','$bemerkung')");
      } else {
        $this->app->DB->InsertWithoutLog("INSERT INTO datei_version (id,datei,ersteller,datum,version,dateiname,bemerkung)
            VALUES ('','$id','$ersteller',NOW(),'$version','$dateiname','$bemerkung')");
      }
      $versionid = $this->app->DB->GetInsertID();

      //TODO Das ist keine lösung!
      //    if($this->app->Conf->WFdbname=="")
      //      $this->app->Conf->WFdbname="wawision";

      // Pfad anpassen
      if($path=="")
      {
        $path = str_replace("www/index.php", "", $_SERVER['SCRIPT_FILENAME']);
        $path = $path."userdata/dms/";
        $path_only = $path;
        $path = $path.$this->app->Conf->WFdbname;
      }  else { $path_only = $path;  }

      if(!is_dir($path))
      {

        $path_b = $path;
        if (substr(trim($path), -1) == DIRECTORY_SEPARATOR) {
          $path = substr(trim($path), 0, -1);
        }

        system("chmod 777 ".$path);
        $path = $path_b;
        //system("mkdir ".$path);
        mkdir($path);
        system("chmod 777 ".$path);
      }

      if(is_file($datei))
      {
        copy($datei,$path."/".$versionid);
      }
      else if(is_uploaded_file($datei))
        move_uploaded_file($datei,$path."/".$versionid);
      else {
        // ACHTUNG !!!! ANGRIFFSGEFAHR!!!!!
        $handle = fopen ($path."/".$versionid, "wb");
        fwrite($handle, $datei);
        fclose($handle);
      }
    }


    function AddDateiStichwort($id,$subjekt,$objekt,$parameter,$without_log=false)
    {
      if(!$without_log)
      {
        $this->app->DB->Insert("INSERT INTO datei_stichwoerter (id,datei,subjekt,objekt,parameter)
            VALUES ('','$id','$subjekt','$objekt','$parameter')");
      } else {
        $this->app->DB->InsertWithoutLog("INSERT INTO datei_stichwoerter (id,datei,subjekt,objekt,parameter)
            VALUES ('','$id','$subjekt','$objekt','$parameter')");
      }
    }

    function DeleteDateiAll($subjekt,$objekt,$parameter)
    {
      //TODO
    }

    function GetDateiName($id)
    {
      $version = $this->app->DB->Select("SELECT MAX(version) FROM datei_version WHERE datei='$id'");
      $newid = $this->app->DB->Select("SELECT dateiname FROM datei_version WHERE datei='$id' AND version='$version' LIMIT 1");

      return $newid;
    }


    function GetDateiSubjektObjektDateiname($subjekt,$objekt,$parameter,$prefix="")
    {
      $dateien = $this->app->DB->SelectArr("SELECT datei FROM datei_stichwoerter WHERE subjekt='$subjekt' AND objekt='$objekt' AND parameter='$parameter' GROUP by datei");

      for($i=0;$i<count($dateien);$i++)
      {
        $tmpname = tempnam($this->GetTMP(), $prefix);
        $newname = $tmpname."_".$this->GetDateiName($dateien[$i]['datei']);

        copy($this->GetDateiPfad($dateien[$i]['datei']),$newname);

        $tmp[] = $newname;
      }
      return $tmp;
    }

    function GetDateiSubjektObjekt($subjekt,$objekt,$parameter)
    {
      $dateien = $this->app->DB->SelectArr("SELECT datei FROM datei_stichwoerter WHERE subjekt='$subjekt' AND objekt='$objekt' AND parameter='$parameter' GROUP by datei");

      for($i=0;$i<count($dateien);$i++)
      {
        $tmp[] = $this->GetDateiPfad($dateien[$i]['datei']);
      }
      return $tmp;
    }

    function GetDateiPfad($id)
    {
      $version = $this->app->DB->Select("SELECT MAX(version) FROM datei_version WHERE datei='$id'");
      $newid = $this->app->DB->Select("SELECT id FROM datei_version WHERE datei='$id' AND version='$version' LIMIT 1");

      $path = "../userdata/dms/".$this->app->Conf->WFdbname."/".$newid;
      return $path;
    }


    function GetDatei($id)
    {
      $version = $this->app->DB->Select("SELECT MAX(version) FROM datei_version WHERE datei='$id'");
      $newid = $this->app->DB->Select("SELECT id FROM datei_version WHERE datei='$id' AND version='$version' LIMIT 1");

      $path = "../userdata/dms/".$this->app->Conf->WFdbname."/".$newid;

      return file_get_contents($path); 
    }

    function GetDateiSize($id) {
      $version = $this->app->DB->Select("SELECT MAX(version) FROM datei_version WHERE datei='$id'");
      $newid = $this->app->DB->Select("SELECT id FROM datei_version WHERE datei='$id' AND version='$version' LIMIT 1");
      $name = $this->app->DB->Select("SELECT dateiname FROM datei_version WHERE id='$newid' LIMIT 1");

      $path = "../userdata/dms/".$this->app->Conf->WFdbname."/".$newid;

      $size = filesize($path);

      if($size <= 1024)
        return $size." Byte";
      else if($size <= 1024*1024)
        return number_format(($size/1024),2)." KB"; 
      else
        return number_format(($size/1024/1024),2)." MB"; 

    }



    function SendDatei($id,$versionid="") {
      session_write_close();
      ob_end_clean();


      set_time_limit(0);
      $version = $this->app->DB->Select("SELECT MAX(version) FROM datei_version WHERE datei='$id'");
      $newid = $this->app->DB->Select("SELECT id FROM datei_version WHERE datei='$id' AND version='$version' LIMIT 1");

      if($versionid>0)
        $newid = $versionid;

      $name = $this->app->DB->Select("SELECT dateiname FROM datei_version WHERE id='$newid' LIMIT 1");



      $path = "../userdata/dms/".$this->app->Conf->WFdbname."/".$newid;
      //$name=basename($path);

      //filenames in IE containing dots will screw up the
      //filename unless we add this

      if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
        $name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);

      $contenttype= $this->content_type($name);

      //required, or it might try to send the serving     //document instead of the file
      header("Content-Type: $contenttype");
      header("Content-Length: " .(string)(filesize($path)) );
      //header('Content-Disposition: inline; filename="'.$name.'"');
      header('Content-Disposition: attachment; filename="'.$name.'"');

      if($file = fopen($path, 'rb')){
        while( (!feof($file)) && (connection_status()==0) ){
          print(fread($file, 1024*8));
          flush();
        }
        fclose($file);
      }
      return((connection_status()==0) and !connection_aborted());
    }


    function content_type($name) {
      // Defines the content type based upon the extension of the file
      $contenttype  = 'application/octet-stream';
      $contenttypes = array( 'html' => 'text/html',
          'htm'  => 'text/html',
          'txt'  => 'text/plain',
          'gif'  => 'image/gif',
          'jpg'  => 'image/jpeg',
          'png'  => 'image/png',
          'sxw'  => 'application/vnd.sun.xml.writer',
          'sxg'  => 'application/vnd.sun.xml.writer.global',
          'sxd'  => 'application/vnd.sun.xml.draw',
          'sxc'  => 'application/vnd.sun.xml.calc',
          'sxi'  => 'application/vnd.sun.xml.impress',
          'xls'  => 'application/vnd.ms-excel',
          'ppt'  => 'application/vnd.ms-powerpoint',
          'doc'  => 'application/msword',
          'rtf'  => 'text/rtf',
          'zip'  => 'application/zip',
          'mp3'  => 'audio/mpeg',
          'pdf'  => 'application/pdf',
          'tgz'  => 'application/x-gzip',
          'gz'   => 'application/x-gzip',
          'vcf'  => 'text/vcf' );

      $name = ereg_replace("§", " ", $name);
      foreach ($contenttypes as $type_ext => $type_name) {
        if (preg_match ("/$type_ext$/i",  $name)) $contenttype = $type_name;
      }
      return $contenttype;
    } 

    function Wochenplan($adr_id,$parsetarget){
      $this->app->Tpl->Set(SUBSUBHEADING, "Wochenplan");
      $this->app->Tpl->Set(INHALT,"");

      $anzWochentage = 5;
      $startStunde = 6;
      $endStunde = 22;

      $wochentage = $this->getDates($anzWochentage);

      $inhalt = "";
      for($i=$startStunde;$i<=$endStunde;$i++){ // fuelle Zeilen 06:00 bis 22:00
        $zeile = array();
        $zeileCount = 0;
        foreach($wochentage as $tag){ // hole Daten fuer Uhrzeit $i und Datum $tage
          $result = $this->checkCell($tag, $i, $adr_id);
          if($result[0]['aufgabe'] != "")
          {
            if($result[0]['adresse']==0) $color = '#ccc'; else $color='#BCEE68';
            if($result[0]['prio']==1) $color = 'red';

            $zeile[$zeileCount] = '<div style="background-color: '.$color.'">'.$result[0]['aufgabe'].'</div>';
          }
          else
            $zeile[$zeileCount] = "&nbsp;";
          $zeileCount++;
        }
        //print_r($zeile);
        $inhalt = $inhalt.$this->makeRow($zeile, $anzWochentage,$i.":00");
      }
      $this->app->Tpl->Set(WOCHENDATUM, $this->makeRow($wochentage, $anzWochentage));
      $this->app->Tpl->Set(INHALT,$inhalt);

      $this->app->Tpl->Parse($parsetarget,"zeiterfassung_wochenplan.tpl");

      $this->app->Tpl->Add($parsetarget,"<table><tr><td style=\"background-color:#BCEE68\">".$this->app->User->GetName()."</td>
          <td style=\"background-color:red\">Prio: Sehr Hoch (".$this->app->User->GetName().")</td>
          <td style=\"background-color:#ccc\">Allgemein</td></tr></table>");
    }

    function getDates($anzWochentage){
      // hole Datum der Wochentage von Mo bis $anzWochentage
      $montag = $this->app->DB->Select("SELECT DATE_SUB(CURDATE(),INTERVAL WEEKDAY(CURDATE()) day)");
      $week = array();
      for($i=0;$i<$anzWochentage;$i++)
        $week[$i] = $this->app->DB->Select("SELECT DATE_ADD('$montag',INTERVAL $i day)");
      return $week;
    }

    function makeRow($data, $spalten, $erstefrei="frei"){
      // erzeuge eine Zeile in der Tabelle
      // $erstefrei = 1 -> erste Spalte ist leer

      $row = '<tr>';
      if($erstefrei=="frei")
        $row = $row.'<td class="wochenplan">&nbsp;</td>';
      else
        $row = $row.'<td class="wochenplan">'.$erstefrei.'</td>';
      for($i=0;$i<$spalten; $i++)
        $row = $row.'<td class="wochenplan">'.$data[$i].'</td>';
      $row = $row.'</tr>';
      return $row;
    }


    function KundeMitUmsatzsteuer($adresse)
    {
      $land = $this->app->DB->Select("SELECT land FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $ustid = $this->app->DB->Select("SELECT ustid FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      if($land =="DE")
        return true;

      // wenn kunde EU
      foreach($this->GetUSTEU() as $value)
      {
        //echo $value;
        if($value==$land && $ustid!="") return false;
      }

      // alle anderen laender = export
      return false;
    }

    function AuftragMitUmsatzeuer($auftrag)
    {
      if($this->app->DB->Select("SELECT ust_befreit FROM auftrag WHERE id='$auftrag' LIMIT 1") ==0 )
        return true;
      else return false;


      $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftrag' LIMIT 1");
      return $this->KundeMitUmsatzsteuer($adresse);
    }


    function GutschriftMitUmsatzeuer($gutschrift)
    {
      if($this->app->DB->Select("SELECT ust_befreit FROM gutschrift WHERE id='$gutschrift' LIMIT 1")==0)
        return true;
      else return false;

      // if($this->CheckLieferantEU($adresse))
      //  return false;

      // wenn lieferant DE dann mit 19% oder 7% einkaufen
      // wenn lieferant in der EU kann man mit 0% bezahlen 

      // wenn lieferant in der welt sowieso keine steuer sondern zoll

      // wenn wir von privat EU kaufen dann muss mit steuer gekauft werden! (SPAETER KANN ES SEIN)
      return false;
    }


    function RechnungMitUmsatzeuer($rechnung)
    {
      if($this->app->DB->Select("SELECT ust_befreit FROM rechnung WHERE id='$rechnung' LIMIT 1") == 0 )
        return true;
      else return false;

      // if($this->CheckLieferantEU($adresse))
      //  return false;

      // wenn lieferant DE dann mit 19% oder 7% einkaufen
      // wenn lieferant in der EU kann man mit 0% bezahlen 

      // wenn lieferant in der welt sowieso keine steuer sondern zoll

      // wenn wir von privat EU kaufen dann muss mit steuer gekauft werden! (SPAETER KANN ES SEIN)
      return false;
    }


    function AngebotMitUmsatzeuer($angebot)
    {
      if($this->app->DB->Select("SELECT ust_befreit FROM angebot WHERE id='$angebot' LIMIT 1") == 0 )
        return true;
      else return false;

      // if($this->CheckLieferantEU($adresse))
      //  return false;

      // wenn lieferant DE dann mit 19% oder 7% einkaufen
      // wenn lieferant in der EU kann man mit 0% bezahlen 

      // wenn lieferant in der welt sowieso keine steuer sondern zoll

      // wenn wir von privat EU kaufen dann muss mit steuer gekauft werden! (SPAETER KANN ES SEIN)
    }


    function BestellungMitUmsatzeuer($bestellung)
    {
      if($this->app->DB->Select("SELECT ust_befreit FROM bestellung WHERE id='$bestellung' LIMIT 1") == 0 )
        return true;
      else return false;
      /*
         $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$bestellung' LIMIT 1");
         $land = $this->app->DB->Select("SELECT land FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
         if($land =="DE")
         return true;
       */
      // if($this->CheckLieferantEU($adresse))
      //  return false;

      // wenn lieferant DE dann mit 19% oder 7% einkaufen
      // wenn lieferant in der EU kann man mit 0% bezahlen 

      // wenn lieferant in der welt sowieso keine steuer sondern zoll

      // wenn wir von privat EU kaufen dann muss mit steuer gekauft werden! (SPAETER KANN ES SEIN)
      return false;
    }


    function BesteuerungKunde($adresse)
    {
      if($this->AdresseUSTCheck($adresse)==0)
        return "steuer";
      else
        return "";

      // steuer muss gezahlt werden! steuer, euexport, exporr

      // wenn kunde im export muss keine steuer bezahlt werden!

      // wenn kunde  gepruefte ust id hat && lieferung nach EU geht (aber das land verlaesst!)

    }



    function CheckLieferantEU($adresse)
    {
      // lieferant aus der EU
      $land = $this->app->DB->Select("SELECT land FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    }


    function CheckKundeEU($adresse)
    {

    }

    function checkCell($datum, $stunde, $adr_id){
      // ueberprueft ob in der Stunde eine Aufgabe zu erledigen ist
      //echo $datum." ".$stunde."<br>";
      return  $this->app->DB->SelectArr("SELECT aufgabe,adresse,prio
          FROM aufgabe
          WHERE DATE(startdatum) = '$datum'
          AND HOUR(TIME(startzeit)) <= $stunde 
          AND HOUR(TIME(startzeit)) + stunden >= $stunde
          AND (adresse = $adr_id OR adresse = 0)
          OR 
          ((DATE_SUB('$datum', INTERVAL MOD(DATEDIFF('$datum',DATE_FORMAT(startdatum, '%Y:%m:%d')),intervall_tage) day)='$datum'
            AND DATE_SUB('$datum', INTERVAL MOD(DATEDIFF('$datum',DATE_FORMAT(startdatum, '%Y:%m:%d')),intervall_tage) day)
            > abgeschlossen_am
            AND intervall_tage>0 AND (adresse=$adr_id OR adresse=0))
           AND HOUR(TIME(startzeit)) <= $stunde AND HOUR(TIME(startzeit)) + stunden >= $stunde) 
          OR ( DATE (abgabe_bis) = '$datum' AND  abgeschlossen=0 AND adresse = $adr_id AND HOUR(TIME(startzeit)) = $stunde)
          LIMIT 1"); // letztes OR von Bene!
    }

    function WebmailSetReadStatus($mailid,$boolean)
    {
      $this->app->DB->Update("UPDATE emailbackup_mails SET gelesen = ".($boolean?1:0)." WHERE id = $mailid");
    }

    function checkPDF($file,$maxSize=0,$x=0,$y=0)
    {

      return "";  
    }
    function checkFile($file,$filetype,$maxSize=0)
    {
      if($file!="")
      { 
        if(is_array($file))
          $pfad = $file[tmp_name];
        else $pfad = $file;
      }

      $dbtype = mime_content_type($pfad);

      if($dbtype!=$filetype)
        return "Falscher Dateityp! Es wird $filetype erwartet aber $dbtype wurde &uuml;bergeben!";

      else return "";
    }



    function checkImage($file,$maxSize=0,$x=0,$y=0,$typcheck=2)
    {
      // Prueft ein Bild auf Dateigroesse, Hoehe und Breite
      if($file!="")
      { 
        if(is_array($file))
          $pfad = $file[tmp_name];
        else $pfad = $file;
      }
      $typ = GetImageSize($pfad);
      $size = $file[size];


      if($maxSize==0)
        $fileSizeLimit =  16777216; // 16MB in BYTE, 100MB stehen in der upload_max_size
      else
        $fileSizeLimit = $maxSize;

      //if(0 < $typ[2] && $typ[2] < 4)
      if($typ[2]==$typcheck)
      { 
        if($size<$fileSizeLimit)
        { 
          if($typ[0]>$x && $x!=0)
            $error = "Das Bild ist zu breit.";
          if($typ[1]>$y && $y!=0)
            $error = "Das Bild ist zu hoch.";
        }else
          $error = "Die Datei darf eine Gr&ouml;&szlig;e von ".($fileSizeLimit/8388608)." MB nicht &uuml;berschreiten.";
      }else
        $error = "Die Datei muss vom korrekten Typ sein";
      return $error;
    }

    function uploadImageIntoDB($file)
    {
      // Wandelt ein Bild fuer einen LONGBLOB um
      $pfad = $file[tmp_name];
      $typ = GetImageSize($pfad);

      // Bild hochladen
      $filehandle = fopen($pfad,'r');
      $filedata = base64_encode(fread($filehandle, filesize($pfad)));
      $dbtype = $typ['mime'];
      return array("image"=>$filedata,"type"=>$dbtype);
    }


    function GetEinkaufspreis($id,$menge,$adresse="")
    {

      // wenn produktionsartikel
      $produktion = $this->app->DB->Select("SELECT produktion FROM artikel WHERE id='$id' LIMIT 1");
      $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");
      $juststueckliste = $this->app->DB->Select("SELECT juststueckliste FROM artikel WHERE id='$id' LIMIT 1");

      if($produktion) {
        $ek = $this->GetEinkaufspreisProduktionsartikel($id);
      } 
      else if($stueckliste==1 && $juststueckliste!=1)
      {
        $ek = $this->GetEinkaufspreisStueckliste($id);
      }
      else {  
        $ek = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND ab_menge<='$menge' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by ab_menge DESC LIMIT 1");
        if($ek <=0)
        {
          $ek = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE artikel='$id' AND ab_menge<='$menge' 
              AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by preis LIMIT 1");

          if($ek <=0)
          {
            $ek = $this->app->DB->Select("SELECT MIN(preis) FROM einkaufspreise WHERE artikel='$id'  
                AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ");
          }
        }
      }
      return $ek;
    }

    function Wechselkurs($von,$zu,$datum)
    {
      return 1.3;
    }


    function Rabatt($betrag,$rabatt)
    {
      $result = $betrag*(100-$rabatt)/100;
      return number_format($result, 4, '.', '');
    }

    function GetGruppen($adresse)
    {
      $tmp = $this->app->DB->SelectArr("SELECT * FROM adresse_rolle WHERE adresse='$adresse' AND (bis > NOW() OR bis='0000-00-00') AND parameter > 0 AND objekt='Gruppe'");
      for($i=0;$i<count($tmp);$i++)
        $result[]=$tmp[$i]['parameter'];

      return $result;
    }

    function IsSpezialVerkaufspreis($artikel,$menge,$adresse=0,$waehrung='EUR')
    {
      $gruppenarr = $this->GetGruppen($adresse);
      for($i=0;$i<count($gruppenarr);$i++)
      {
        if($gruppenarr[$i]>0)
          $sql_erweiterung .= " OR v.gruppe='".$gruppenarr[$i]."' ";
      }

      $vkarr = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise v WHERE v.ab_menge <= '$menge' AND
          (v.gueltig_bis > NOW() OR v.gueltig_bis='0000-00-00') AND v.artikel='".$artikel."' AND (v.adresse='$adresse' $sql_erweiterung) 
          ORDER by ab_menge ASC, preis DESC LIMIT 1");

      $letzte_menge = 0;//$vkarr[0][ab_menge];
      for($vi=0;$vi<count($vkarr);$vi++)
      {
        if($vkarr[$vi][adresse]==$adresse && $vkarr[$vi][adresse]>0 && $vkarr[$vi][art]=="Kunde")
        {
          return true;
        }
        if($vkarr[$vi][gruppe] > 0 && $vkarr[$vi][art]=="Gruppe")
        {
          return true;
        }
      }

      return false;
    }

    var $preisliste;

    function GeneratePreisliste($artikel,$adresse,$rabatt=0)
    {

      $keinrabatterlaubt = $this->app->DB->Select("SELECT keinrabatterlaubt FROM artikel WHERE id='".$artikel."' LIMIT 1");

      $gruppenarray = $this->GetGruppen($adresse);

      if(count($gruppenarray)>0) $sql_erweiterung = " OR ";
      for($gi=0;$gi<count($gruppenarray);$gi++)
      {
        $sql_erweiterung .= " gruppe='".$gruppenarray[$gi]."' ";

        if($gi<count($gruppenarray)-1)
          $sql_erweiterung .= " OR ";
      }
      // reinsortieren
      //$vkarr = $this->app->DB->SelectArr("SELECT if((v.adresse > 0 OR v.gruppe >0),v.preis,(v.preis*(1-$rabatt))/100.0) as preis,v.* FROM verkaufspreise v WHERE 
      $vkarr = $this->app->DB->SelectArr("SELECT v.*,if((v.adresse > 0 OR v.gruppe > 0),v.preis,(v.preis*(100-$rabatt))/100.0) as rabattpreis FROM verkaufspreise v WHERE 
          (v.gueltig_bis > NOW() OR v.gueltig_bis='0000-00-00') AND v.artikel='".$artikel."' AND (v.adresse='$adresse' $sql_erweiterung OR 
            ((v.adresse='' OR v.adresse='0') AND v.art='Kunde')) ORDER by rabattpreis ASC");

      $letzter_preis = 0;

      // einmal rueckwaerts aufraeumen
      for($vi=0;$vi<count($vkarr);$vi++)
      {
        // rabatt rausrechnen
        if($keinrabatterlaubt!="1") 
          $vkarr[$vi][preis] = $vkarr[$vi][rabattpreis];

        if($vkarr[$vi][preis] > $letzter_preis && (($vkarr[$vi][ab_menge] < $letzte_menge) || $vi==0))
        {
          // preis behalten
          $letzte_menge = $vkarr[$vi][ab_menge];
          $letzter_preis = $vkarr[$vi][preis];
        } else {
          // preis loeschen
          $vkarr[$vi][ab_menge]=0;
          $vkarr[$vi][preis]=0;
        }
      }

      for($vi=0;$vi<count($vkarr);$vi++)
      {
        if($vkarr[$vi][ab_menge] > 0)
          $vkarr2[] = $vkarr[$vi];
      }
      $vkarr = array_reverse($vkarr2);

      // an schluss pruefen und unnötige rausschmeissen
      return $vkarr;
    }

    function GetVerkaufspreisKunde($artikel,$menge,$adresse=0,$waehrung='EUR')
    {
      $vkarr = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise v WHERE v.ab_menge <='$menge' AND 
          (v.gueltig_bis > NOW() OR v.gueltig_bis='0000-00-00') AND v.artikel='".$artikel."' AND v.adresse='$adresse' AND v.art='Kunde'
          ORDER by ab_menge DESC, preis ASC");

      $letzte_menge = 0;  //$vkarr[0][ab_menge];
      $letzter_preis = 99999999999;

      for($vi=0;$vi<count($vkarr);$vi++)
      {
        if($vkarr[$vi][ab_menge] > $letzte_menge && $vkarr[$vi][preis]<$letzter_preis && $menge >= $vkarr[$vi][ab_menge])
        {
          $letzte_menge = $vkarr[$vi][ab_menge];
          $letzter_preis = $vkarr[$vi][preis];
        }
      }

      if($letzter_preis==99999999999)
        $letzter_preis=0;

      return $letzter_preis;
    }

    function GetVerkaufspreis($artikel,$menge,$adresse=0,$waehrung='EUR')
    {
      $gruppenarr = $this->GetGruppen($adresse);
      for($i=0;$i<count($gruppenarr);$i++)
      {
        if($gruppenarr[$i]>0)
          $sql_erweiterung .= " OR gruppe='".$gruppenarr[$i]."' ";
      }

      $vkarr = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise v WHERE v.ab_menge <='$menge' AND 
          (v.gueltig_bis > NOW() OR v.gueltig_bis='0000-00-00') AND v.artikel='".$artikel."' AND (v.adresse='$adresse' $sql_erweiterung OR 
            ((v.adresse='' OR v.adresse='0') AND v.art='Kunde')) ORDER by ab_menge DESC, preis ASC");

      $letzte_menge = 0;  //$vkarr[0][ab_menge];
      $letzter_preis = 99999999999;

      for($vi=0;$vi<count($vkarr);$vi++)
      {
        if($vkarr[$vi][ab_menge] > $letzte_menge && $vkarr[$vi][preis]<$letzter_preis && $menge >= $vkarr[$vi][ab_menge])
        {
          $letzte_menge = $vkarr[$vi][ab_menge];
          $letzter_preis = $vkarr[$vi][preis];
        }
      }

      if($letzter_preis==99999999999)
        $letzter_preis=0;

      return $letzter_preis;
    }


    function GetEinkaufspreisStueckliste($id,$max=false)
    {
      $table = new EasyTable($this->app);
      if($this->app->Conf->WFdbType=="postgre") {
        if(is_numeric($id)) {
          $sql = "SELECT ROUND(SUM( 
            (SELECT MAX(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND e.objekt='Standard')*s.menge)
            ,2)
            FROM stueckliste s
            LEFT JOIN artikel a ON a.id=s.artikel 
            WHERE s.stuecklistevonartikel='$id'";
        }} else {
          $sql = "SELECT FORMAT(SUM( 
            (SELECT MAX(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND e.objekt='Standard')*s.menge)
            ,2)
            FROM stueckliste s
            LEFT JOIN artikel a ON a.id=s.artikel 
            WHERE s.stuecklistevonartikel='$id'";
        }


      $preis_max = $this->app->DB->Select($sql);

      if($this->app->Conf->WFdbType=="postgre") {
        $sql = "SELECT ROUND(SUM(
          (SELECT MAX(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel
          WHERE s.stuecklistevonartikel='$id'";
      } else {
        $sql = "SELECT FORMAT(SUM(
          (SELECT MAX(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel
          WHERE s.stuecklistevonartikel='$id'";
      }

      $preis_max = $preis_max + $this->app->DB->Select($sql);

      if($this->app->Conf->WFdbType=="postgre") {
        $sql = "SELECT ROUND(SUM( 
          (SELECT MIN(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND e.objekt='Standard')*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel 
          WHERE s.stuecklistevonartikel='$id'";
      } else {
        $sql = "SELECT FORMAT(SUM( 
          (SELECT MIN(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND e.objekt='Standard')*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel 
          WHERE s.stuecklistevonartikel='$id'";
      }

      $preis = $this->app->DB->Select($sql);
      if($this->app->Conf->WFdbType=="postgre") {
        $sql = "SELECT ROUND(SUM(
          (SELECT MIN(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel
          WHERE s.stuecklistevonartikel='$id'";
      } else {
        $sql = "SELECT FORMAT(SUM(
          (SELECT MIN(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel
          WHERE s.stuecklistevonartikel='$id'";

      }

      $preis = $preis + $this->app->DB->Select($sql);

      if($max) return $preis_max;
      else return $preis;
    }


    function GetEinkaufspreisProduktionsartikel($id,$max=false)
    {
      $tmp = $this->app->DB->Select("SELECT produktioninfo FROM artikel WHERE id='$id' LIMIT 1");
      $produktion = $this->app->DB->Select("SELECT produktion FROM artikel WHERE id='$id' LIMIT 1");

      if ($produktion=="1" && $tmp!="") 
      {
        $tmp_base64 = unserialize(base64_decode($tmp));

        $keys = array_keys($tmp_base64);

        // START SCHLEIFE
        for($i=0;$i<count($tmp_base64);$i++) {
          $artikelnummer = $tmp_base64[$keys[$i]];
          if($artikelnummer<=0) continue;

          $artikelid  = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$artikelnummer."' LIMIT 1");
          $name_de  = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='".$artikelid."' LIMIT 1");

          $sql = "SELECT FORMAT(SUM( 
            (SELECT MIN(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND e.objekt='Standard')*s.menge)
            ,2)
            FROM stueckliste s
            LEFT JOIN artikel a ON a.id=s.artikel 
            WHERE s.stuecklistevonartikel='$artikelid'";

          $preis = $this->app->DB->Select($sql);
          $sql = "SELECT FORMAT(SUM(
            (SELECT MIN(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
            ,2)
            FROM stueckliste s
            LEFT JOIN artikel a ON a.id=s.artikel
            WHERE s.stuecklistevonartikel='$artikelid'";

          $preis = $preis + $this->app->DB->Select($sql);

          if($this->app->Conf->WFdbType=="postgre") {
            $sql = "SELECT ROUND(SUM( 
              (SELECT MAX(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND e.objekt='Standard')*s.menge)
              ,2)
              FROM stueckliste s
              LEFT JOIN artikel a ON a.id=s.artikel 
              WHERE s.stuecklistevonartikel='$artikelid'";
          } else {
            $sql = "SELECT FORMAT(SUM( 
              (SELECT MAX(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND e.objekt='Standard')*s.menge)
              ,2)
              FROM stueckliste s
              LEFT JOIN artikel a ON a.id=s.artikel 
              WHERE s.stuecklistevonartikel='$artikelid'";
          }

          $preis_max = $this->app->DB->Select($sql);
          $sql = "SELECT FORMAT(SUM(
            (SELECT MAX(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
            ,2)
            FROM stueckliste s
            LEFT JOIN artikel a ON a.id=s.artikel
            WHERE s.stuecklistevonartikel='$artikelid'";

          $preis_max = $preis_max + $this->app->DB->Select($sql);

          $gesamtpreis = $gesamtpreis + $preis;
          $gesamtpreis_max = $gesamtpreis_max + $preis_max;
        }
        //$this->app->Tpl->Add(INHALT,"<div class=\"info\">Gesamtpreis bei Menge 1: <b>$gesamtpreis_max bis $gesamtpreis EUR</b></div>");
      }
      if($max) return $gesamtpreis_max;
      else return $gesamtpreis;
    }


    function uploadFileIntoDB($file)
    {
      // Wandelt ein Bild fuer einen LONGBLOB um
      $pfad = $file[tmp_name];

      $dbtype = mime_content_type($pfad);
      // Bild hochladen
      $filehandle = fopen($pfad,'r');
      $filedata = base64_encode(fread($filehandle, filesize($pfad)));
      return array("file"=>$filedata,"type"=>$dbtype);
    }

    // im format hh:mm
    function ZeitinMenge($zeit)
    {
      $zeit = explode(":", $zeit);

      $komma = round(100/(60/$zeit[1]),0);

      $komma = str_pad($komma, 2 ,'0', STR_PAD_LEFT);

      return $zeit[0].",".$komma;
    }       

    function get_time_difference($start_time_o, $end_time_o){
      $start_time = explode(":", $start_time_o);
      $end_time = explode(":", $end_time_o);

      $start_time_stamp = mktime($start_time[0], $start_time[1], 0, 0, 0, 0);
      $end_time_stamp = mktime($end_time[0], $end_time[1], 0, 0, 0, 0);

      $time_difference = $end_time_stamp - $start_time_stamp;

      return gmdate("H:i", $time_difference);  
    }

    function is_html($str){
      $html = array('A','ABBR','ACRONYM','ADDRESS','APPLET','AREA','B','BASE','BASEFONT','BDO','BIG','BLOCKQUOTE','BODY','BR','BUTTON','CAPTION','CENTER','CITE','CODE','COL','COLGROUP','DD','DEL','DFN','DIR','DIV','DL','DT','EM','FIELDSET','FONT','FORM','FRAME','FRAMESET','H1','H2','H3','H4','H5','H6','HEAD','HR','HTML','I','IFRAME','IMG','INPUT','INS','ISINDEX','KBD','LABEL','LEGEND','LI','LINK','MAP','MENU','META','NOFRAMES','NOSCRIPT','OBJECT','OL','OPTGROUP','OPTION','P','PARAM','PRE','Q','S','SAMP','SCRIPT','SELECT','SMALL','SPAN','STRIKE','STRONG','STYLE','SUB','SUP','TABLE','TBODY','TD','TEXTAREA','TFOOT','TH','THEAD','TITLE','TR','TT','U','UL','VAR');
      if(preg_match_all("~(<\/?)\b(".implode('|',$html).")\b([^>]*>)~i",$str,$c)){
        return TRUE;
      }else{
        return FALSE;
      }
    } 

    function ImportCreateAdresse($data)
    {
      $this->app->DB->Insert("INSERT INTO adresse (id) VALUES ('')");
      $id = $this->app->DB->GetInsertID();

      if($data['firma']=="") $data['firma']=1;
      if($data['projekt']=="") $data['projekt']=1;

      foreach ($data as $key => $value) {
        $this->app->DB->Update("UPDATE adresse SET $key='".$this->ConvertForDBUTF8($value)."' WHERE id='$id' LIMIT 1");
      }
      return $id;
    }

    function ImportGetStandardProjekt()
    {
      $firma = $this->app->DB->Select("SELECT MAX(id) FROM firma ");
      return $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$firma."' LIMIT 1");
    }

    function ImportCreateArtikel($data)
    {
      $this->app->DB->Insert("INSERT INTO artikel (id) VALUES ('')");
      $id = $this->app->DB->GetInsertID();

      if($data['firma']=="") $data['firma']=1;
      if($data['projekt']=="") $data['projekt']=1;

      foreach ($data as $key => $value) {
        $this->app->DB->Update("UPDATE artikel SET $key='".$this->ConvertForDBUTF8($value)."' WHERE id='$id' LIMIT 1");
      }
      return $id;
    }

    function ImportCreateEinkaufspreis($data) {
      $this->app->DB->Insert("INSERT INTO einkaufspreise (id) VALUES ('')");
      $id = $this->app->DB->GetInsertID();

      if($data['firma']=="") $data['firma']=1;
      if($data['projekt']=="") $data['projekt']=1;


      foreach ($data as $key => $value) {
        $this->app->DB->Update("UPDATE einkaufspreise SET $key='".$this->ConvertForDBUFT8($value)."' WHERE id='$id' LIMIT 1");
      }
      return $id;
    }

    function ImportCreateVerkaufspreis($data) {
      $this->app->DB->Insert("INSERT INTO verkaufspreise (id) VALUES ('')");
      $id = $this->app->DB->GetInsertID();

      if($data['firma']=="") $data['firma']=1;
      if($data['projekt']=="") $data['projekt']=1;

      foreach ($data as $key => $value) {
        $this->app->DB->Update("UPDATE verkaufspreise SET $key='".$this->ConvertForDBUTF8($value)."' WHERE id='$id' LIMIT 1");
      }
      return $id;
    }

    function ImportCreateUser($data) {
      $this->app->DB->Insert("INSERT INTO user (id) VALUES ('')");
      $id = $this->app->DB->GetInsertID();

      if($data['firma']=="") $data['firma']=1;
      if($data['projekt']=="") $data['projekt']=1;

      foreach ($data as $key => $value) {
        $this->app->DB->Update("UPDATE user SET $key='".$this->ConvertForDBUTF8($value)."' WHERE id='$id' LIMIT 1");
      }
      return $id;
    }

    function ImportCreateRechnung($data) {
      $this->app->DB->Insert("INSERT INTO rechnung (id) VALUES ('')");
      $id = $this->app->DB->GetInsertID();

      if($data['firma']=="") $data['firma']=1;
      if($data['projekt']=="") $data['projekt']=1;

      foreach ($data as $key => $value) {
        $this->app->DB->Update("UPDATE rechnung SET $key='".$this->ConvertForDBUTF8($value)."' WHERE id='$id' LIMIT 1");
      }
      return $id;
    }

    function FirmenDatenStandard()
    {

      if($this->app->DB->Select("SELECT COUNT(id) FROM firmendaten") > 0) return;

      $this->app->DB->Insert("INSERT INTO `firmendaten` (`id`, `firma`, `absender`, `sichtbar`, `barcode`, `schriftgroesse`, `betreffszeile`, `dokumententext`, `tabellenbeschriftung`, `tabelleninhalt`, `zeilenuntertext`, `freitext`, `infobox`, `spaltenbreite`, `footer_0_0`, `footer_0_1`, `footer_0_2`, `footer_0_3`, `footer_0_4`, `footer_0_5`, `footer_1_0`, `footer_1_1`, `footer_1_2`, `footer_1_3`, `footer_1_4`, `footer_1_5`, `footer_2_0`, `footer_2_1`, `footer_2_2`, `footer_2_3`, `footer_2_4`, `footer_2_5`, `footer_3_0`, `footer_3_1`, `footer_3_2`, `footer_3_3`, `footer_3_4`, `footer_3_5`, `footersichtbar`, `hintergrund`, `logo`, `logo_type`, `briefpapier`, `briefpapier_type`, `benutzername`, `passwort`, `host`, `port`, `mailssl`, `signatur`, `email`, `absendername`, `bcc1`, `bcc2`, `firmenfarbe`, `name`, `strasse`, `plz`, `ort`, `steuernummer`, `startseite_wiki`, `datum`, `projekt`, `brieftext`, `next_angebot`, `next_auftrag`, `next_gutschrift`, `next_lieferschein`, `next_bestellung`, `next_rechnung`, `next_kundennummer`, `next_lieferantennummer`, `next_mitarbeiternummer`, `next_waren`, `next_sonstiges`, `next_produktion`, `breite_position`, `breite_menge`, `breite_nummer`, `breite_einheit`, `skonto_ueberweisung_ueberziehen`, `steuersatz_normal`, `steuersatz_zwischen`, `steuersatz_ermaessigt`, `steuersatz_starkermaessigt`, `steuersatz_dienstleistung`, `kleinunternehmer`, `porto_berechnen`, `immernettorechnungen`, `schnellanlegen`, `bestellvorschlaggroessernull`, `versand_gelesen`, `versandart`, `zahlungsweise`, `zahlung_lastschrift_konditionen`, `breite_artikelbeschreibung`, `waehrung`, `footer_breite1`, `footer_breite2`, `footer_breite3`, `footer_breite4`, `boxausrichtung`, `lizenz`, `schluessel`, `branch`, `version`, `standard_datensaetze_datatables`, `auftrag_bezeichnung_vertrieb`, `auftrag_bezeichnung_bearbeiter`, `auftrag_bezeichnung_bestellnummer`, `bezeichnungkundennummer`, `bezeichnungstornorechnung`, `bestellungohnepreis`, `mysql55`, `rechnung_gutschrift_ansprechpartner`, `api_initkey`, `api_remotedomain`, `api_eventurl`, `api_enable`, `api_importwarteschlange`, `api_importwarteschlange_name`, `wareneingang_zwischenlager`, `modul_mlm`, `modul_verband`, `modul_mhd`, `mhd_warnung_tage`, `mlm_mindestbetrag`, `mlm_anzahlmonate`, `mlm_letzter_tag`, `mlm_erster_tag`, `mlm_letzte_berechnung`, `mlm_01`, `mlm_02`, `mlm_03`, `mlm_04`, `mlm_05`, `mlm_06`, `mlm_07`, `mlm_08`, `mlm_09`, `mlm_10`, `mlm_11`, `mlm_12`, `mlm_13`, `mlm_14`, `mlm_15`, `mlm_01_punkte`, `mlm_02_punkte`, `mlm_03_punkte`, `mlm_04_punkte`, `mlm_05_punkte`, `mlm_06_punkte`, `mlm_07_punkte`, `mlm_08_punkte`, `mlm_09_punkte`, `mlm_10_punkte`, `mlm_11_punkte`, `mlm_12_punkte`, `mlm_13_punkte`, `mlm_14_punkte`, `mlm_15_punkte`, `mlm_01_mindestumsatz`, `mlm_02_mindestumsatz`, `mlm_03_mindestumsatz`, `mlm_04_mindestumsatz`, `mlm_05_mindestumsatz`, `mlm_06_mindestumsatz`, `mlm_07_mindestumsatz`, `mlm_08_mindestumsatz`, `mlm_09_mindestumsatz`, `mlm_10_mindestumsatz`, `mlm_11_mindestumsatz`, `mlm_12_mindestumsatz`, `mlm_13_mindestumsatz`, `mlm_14_mindestumsatz`, `mlm_15_mindestumsatz`, `standardaufloesung`, `standardversanddrucker`, `standardetikettendrucker`, `externereinkauf`, `schriftart`, `knickfalz`, `artikeleinheit`, `artikeleinheit_standard`, `abstand_name_beschreibung`, `abstand_boxrechtsoben_lr`, `zahlungszieltage`, `zahlungszielskonto`, `zahlungszieltageskonto`, `zahlung_rechnung`, `zahlung_vorkasse`, `zahlung_nachnahme`, `zahlung_kreditkarte`, `zahlung_paypal`, `zahlung_bar`, `zahlung_lastschrift`, `zahlung_amazon`, `zahlung_ratenzahlung`, `zahlung_rechnung_sofort_de`, `zahlung_rechnung_de`, `zahlung_vorkasse_de`, `zahlung_lastschrift_de`, `zahlung_nachnahme_de`, `zahlung_bar_de`, `zahlung_paypal_de`, `zahlung_amazon_de`, `zahlung_kreditkarte_de`, `zahlung_ratenzahlung_de`, `briefpapier2`, `briefpapier2vorhanden`, `artikel_suche_kurztext`, `adresse_freitext1_suche`, `warnung_doppelte_nummern`, `next_arbeitsnachweis`, `next_reisekosten`, `next_anfrage`, `seite_von_ausrichtung`, `seite_von_sichtbar`, `parameterundfreifelder`, `freifeld1`, `freifeld2`, `freifeld3`, `freifeld4`, `freifeld5`, `freifeld6`, `firmenfarbehell`, `firmenfarbedunkel`, `firmenfarbeganzdunkel`, `navigationfarbe`, `navigationfarbeschrift`, `unternavigationfarbe`, `unternavigationfarbeschrift`, `firmenlogo`, `firmenlogotype`, `firmenlogoaktiv`, `projektnummerimdokument`, `mailanstellesmtp`, `herstellernummerimdokument`, `standardmarge`, `steuer_erloese_inland_normal`, `steuer_aufwendung_inland_normal`, `steuer_erloese_inland_ermaessigt`, `steuer_aufwendung_inland_ermaessigt`, `steuer_erloese_inland_steuerfrei`, `steuer_aufwendung_inland_steuerfrei`, `steuer_erloese_inland_innergemeinschaftlich`, `steuer_aufwendung_inland_innergemeinschaftlich`, `steuer_erloese_inland_eunormal`, `steuer_aufwendung_inland_eunormal`, `steuer_erloese_inland_export`, `steuer_aufwendung_inland_import`, `steuer_anpassung_kundennummer`, `steuer_art_1`, `steuer_art_1_normal`, `steuer_art_1_ermaessigt`, `steuer_art_1_steuerfrei`, `steuer_art_2`, `steuer_art_2_normal`, `steuer_art_2_ermaessigt`, `steuer_art_2_steuerfrei`, `steuer_art_3`, `steuer_art_3_normal`, `steuer_art_3_ermaessigt`, `steuer_art_3_steuerfrei`, `steuer_art_4`, `steuer_art_4_normal`, `steuer_art_4_ermaessigt`, `steuer_art_4_steuerfrei`, `steuer_art_5`, `steuer_art_5_normal`, `steuer_art_5_ermaessigt`, `steuer_art_5_steuerfrei`, `steuer_art_6`, `steuer_art_6_normal`, `steuer_art_6_ermaessigt`, `steuer_art_6_steuerfrei`, `steuer_art_7`, `steuer_art_7_normal`, `steuer_art_7_ermaessigt`, `steuer_art_7_steuerfrei`, `steuer_art_8`, `steuer_art_8_normal`, `steuer_art_8_ermaessigt`, `steuer_art_8_steuerfrei`, `steuer_art_9`, `steuer_art_9_normal`, `steuer_art_9_ermaessigt`, `steuer_art_9_steuerfrei`, `steuer_art_10`, `steuer_art_10_normal`, `steuer_art_10_ermaessigt`, `steuer_art_10_steuerfrei`, `steuer_art_11`, `steuer_art_11_normal`, `steuer_art_11_ermaessigt`, `steuer_art_11_steuerfrei`, `steuer_art_12`, `steuer_art_12_normal`, `steuer_art_12_ermaessigt`, `steuer_art_12_steuerfrei`, `steuer_art_13`, `steuer_art_13_normal`, `steuer_art_13_ermaessigt`, `steuer_art_13_steuerfrei`, `steuer_art_14`, `steuer_art_14_normal`, `steuer_art_14_ermaessigt`, `steuer_art_14_steuerfrei`, `steuer_art_15`, `steuer_art_15_normal`, `steuer_art_15_ermaessigt`, `steuer_art_15_steuerfrei`, `rechnung_header`, `lieferschein_header`, `angebot_header`, `auftrag_header`, `gutschrift_header`, `bestellung_header`, `arbeitsnachweis_header`, `provisionsgutschrift_header`, `rechnung_footer`, `lieferschein_footer`, `angebot_footer`, `auftrag_footer`, `gutschrift_footer`, `bestellung_footer`, `arbeitsnachweis_footer`, `provisionsgutschrift_footer`, `rechnung_ohnebriefpapier`, `lieferschein_ohnebriefpapier`, `angebot_ohnebriefpapier`, `auftrag_ohnebriefpapier`, `gutschrift_ohnebriefpapier`, `bestellung_ohnebriefpapier`, `arbeitsnachweis_ohnebriefpapier`, `eu_lieferung_vermerk`, `export_lieferung_vermerk`, `abstand_adresszeileoben`, `abstand_boxrechtsoben`, `abstand_betreffzeileoben`, `abstand_artikeltabelleoben`, `wareneingang_kamera_waage`, `layout_iconbar`) VALUES
          (1, 1, 'Musterfirma GmbH | Musterweg 5 | 12345 Musterstadt', 1, 1, 7, 9, 9, 9, 9, 7, 9, 8, 0, 'Sitz der Gesellschaft / Lieferanschrift', 'Musterfirma GmbH', 'Musterweg 5', 'D-12345 Musterstadt', 'Telefon +49 123 12 34 56 7', 'Telefax +49 123 12 34 56 78', 'Bankverbindung', 'Musterbank', 'Konto 123456789', 'BLZ 72012345', '', '', 'IBAN DE1234567891234567891', 'BIC/SWIFT DETSGDBWEMN', 'Ust-IDNr. DE123456789', 'E-Mail: info@musterfirma-gmbh.de', 'Internet: http://www.musterfirma.de', '', 'Geschäftsführer', 'Max Musterman', 'Handelsregister: HRB 12345', 'Amtsgericht: Musterstadt', '', '', 0, 'kein', '', '', '', '', 'musterman', 'passwort', 'smtp.server.de', '25', 1, 'LS0NCk11c3RlcmZpcm1hIEdtYkgNCk11c3RlcndlZyA1DQpELTEyMzQ1IE11c3RlcnN0YWR0DQoNClRlbCArNDkgMTIzIDEyIDM0IDU2IDcNCkZheCArNDkgMTIzIDEyIDM0IDU2IDc4DQoNCk5hbWUgZGVyIEdlc2VsbHNjaGFmdDogTXVzdGVyZmlybWEgR21iSA0KU2l0eiBkZXIgR2VzZWxsc2NoYWZ0OiBNdXN0ZXJzdGFkdA0KDQpIYW5kZWxzcmVnaXN0ZXI6IE11c3RlcnN0YWR0LCBIUkIgMTIzNDUNCkdlc2Now6RmdHNmw7xocnVuZzogTWF4IE11c3Rlcm1hbg0KVVN0LUlkTnIuOiBERTEyMzQ1Njc4OQ0KDQpBR0I6IGh0dHA6Ly93d3cubXVzdGVyZmlybWEuZGUvDQo=', 'info@server.de', 'Meine Firma', '', '', '', 'Musterfirma GmbH', 'Musterweg 5', '12345', 'Musterstadt', '111/11111/11111', '', '0000-00-00 00:00:00', 0, '11', '', '', '', '', '', '', '', '', '', '', '', '', 10, 10, 20, 15, 0, 19.00, 7.00, 7.00, 7.00, 7.00, 0, 0, 0, 1, 0, 0, 'versandunternehmen', 'rechnung', 0, 1, 'EUR', 0, 0, 0, 0, '', '', '', '', '', 0, 'Vertrieb', 'Bearbeiter', 'Ihre Bestellnummer', 'Kundennummer', 'Stornorechnung', 0, 1, 0, '', '', '', 0, 0, '', 0, 0, 0, 0, 3, 50.00, 11, NULL, NULL, NULL, 15.00, 20.00, 28.00, 32.00, 36.00, 40.00, 44.00, 44.00, 44.00, 44.00, 50.00, 54.00, 45.00, 48.00, 60.00, 2999, 3000, 5000, 10000, 15000, 25000, 50000, 100000, 150000, 200000, 250000, 300000, 350000, 400000, 450000, 50, 50, 50, 50, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 14, 2, 10, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'Rechnung zahlbar sofort.', 'Rechnung zahlbar innerhalb {ZAHLUNGSZIELTAGE} Tage bis zum {ZAHLUNGBISDATUM}.', '', '', '', '', '', '', '', '', NULL, 0, 0, 0, 0, NULL, NULL, NULL, 'R', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, 0, 0, 0, 0, 30, '4400', '5400', '4300', '', '', '', '4125', '5425', '4315', '', '4120', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Sehr geehrte Damen und Herren,\r\n\r\nanbei Ihre Rechnung.', 'Sehr geehrte Damen und Herren,\r\n\r\nwir liefern Ihnen:', 'Sehr geehrte Damen und Herren,\r\n\r\nhiermit bieten wir Ihnen an:', 'Sehr geehrte Damen und Herren,\r\n\r\nvielen Dank für Ihren Auftrag.', 'Sehr geehrte Damen und Herren,\r\n\r\nanbei Ihre {ART}:', 'Sehr geehrte Damen und Herren,\r\n\r\nwir bestellen hiermit:', 'Sehr geehrte Damen und Herren,\r\n\r\nwir liefern Ihnen:', '', 'Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.', 'Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.', 'Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.', 'Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.', 'Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.', 'Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.', 'Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.', '', 0, 0, 0, 0, 0, 0, 0, 'Steuerfrei nach § 4 Nr. 1b i.V.m. § 6 a UStG. Ihre USt-IdNr. {USTID} Land: {LAND}', '', 0, 0, 0, 0, 0, 0);");
    }       


}

?>

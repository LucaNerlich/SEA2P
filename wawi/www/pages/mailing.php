<?php
//include ("_gen/mailing.php");

class Mailing {
  var $app;
  
  function Mailing($app) {
    //parent::GenMailing($app);
    $this->app=&$app;

    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
      $this->app->Tpl->Set(SUBHEADING,": ".
        $this->app->DB->Select("SELECT nummer FROM artikel WHERE id=$id LIMIT 1"));

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","MailingCreate");
    $this->app->ActionHandler("edit","MailingEdit");
    $this->app->ActionHandler("list","MailingList");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function MailingCreate()
  {
    $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");

//    parent::MailingCreate();
  }




  function MailingList()
  {
    $emails = $this->app->Secure->GetPOST("emails");
    $betreff= $this->app->Secure->GetPOST("betreff");
    $text= $this->app->Secure->GetPOST("text");
     $text = str_replace("\\\\\\\"","\"",str_replace("\\r\\n","\r\n",$text));
    $submit = $this->app->Secure->GetPOST("submit");


    $tmp = implode(", ",$this->app->erp->get_emails($emails));

    $this->app->Tpl->Set(EMAILS,$tmp);
    $this->app->Tpl->Set(BETREFF,$betreff);
    $this->app->Tpl->Set(TEXT,$text);

    if($submit=="Quick-Mailing Check")
    {
      $this->app->Tpl->Set(SUBMIT,"Senden");

    } else 
      $this->app->Tpl->Set(SUBMIT,"Quick-Mailing Check");

    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\">Mailing</h2></li>");
//    $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Mailing");

    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=mailing&action=quick\">Quick-Mail</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=mailing&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");
    $this->app->erp->MenuEintrag("index.php","Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->Tpl->Set(TABTEXT,"Quick-Mail");
    $this->app->Tpl->Parse(TAB1,"quickmailing.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
   
     


    if($submit=="Senden" && $betreff!="" && $text !="")
    {
      $result =  $this->app->erp->get_emails($emails);

      for($i=0;$i<count($result);$i++)
      //for($i=0;$i<3;$i++)
      {
	$to_name = $result[$i];
	$to  = $result[$i];
	$this->app->erp->MailSend($this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaName(),$to,$to_name,$betreff,$text);
      }

    }  else if ($submit=="Senden")
    {
	echo "pflichtfelder!";
    }
    


//    print_r($result);



 //   parent::MailingList();
  }
  function MailingMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Mailing</a>&nbsp;");
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
  }


  function MailingEdit()
  {
    $this->MailingMenu();
    $this->app->Tpl->Set(TABLE_ADRESSE_KONTAKTHISTORIE,"TDB");
    $this->app->Tpl->Set(TABLE_ADRESSE_ROLLEN,"TDB");

    $this->app->Tpl->Set(TABLE_ADRESSE_USTID,"TDB");

    //parent::MailingEdit();
  }





}

?>

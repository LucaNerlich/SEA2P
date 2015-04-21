<?php


class KorrespondenzPDF extends Briefpapier {
  public $logofile;
  public $sender;
  public $recipient;
  public $letterDetails;
  public $app;
	public $details;


  function KorrespondenzPDF($app)
  {
    $this->app=&$app;
    $this->doctype="brief";
		$this->details = array();
            
    parent::Briefpapier($this->app);
  }

	function SetDetail($id, $value)
	{
		$this->details[$id] = $value;	
	}

	function SetBetreff($betreff)
	{
		$this->doctypeOrig=$betreff;
	}

	function SetAdresse($adresse) 
	{
		$this->setRecipient($adresse);
	}

	function Create()
	{
		$this->setCorrDetails($this->details);

		$datum = $this->app->DB->Select("SELECT DATE_FORMAT(NOW(),'%Y%m%d-%h%i')");
    $this->filename = $datum."_".rand(11111,99999).".pdf";
	}

  function GetBriefTMP($adresse,$betreff,$text,$fax="")
  {
    $this->doctypeOrig=$betreff;
    $this->setRecipientDB($adresse);

    $details['Bearbeiter'] = $this->app->User->GetName();
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    if($kundennummer!="" && $kundennummer!=0)
      $details['Ihre Kundennummer']=$kundennummer;

    $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    $unserekundennummer = $this->app->DB->Select("SELECT kundennummer FROM lieferantvorlage WHERE adresse='$adresse' LIMIT 1"); 

    if($lieferantennummer!="" && $lieferantennummer!=0)
      $details['Lieferantennummer']=$lieferantennummer;

    if($lieferantennummer!="" && $lieferantennummer!=0 && $unserekundennummer!="" && $unserekundennummer!=0)
      $details['Unsere Kundennummer']=$unserekundennummer;


    $telefax= $this->app->DB->Select("SELECT telefax FROM adresse WHERE id='$adresse' LIMIT 1");
    if($telefax!="" && $telefax!=0)
      $details['Ihre Faxnummer']=$telefax;
 
 
    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM dokumente_send WHERE id='$id' LIMIT 1");
      $details['Datum']=$datum;


    
    $this->setCorrDetails($details);

    $this->setBarcode($adresse);

    $this->setLetterDetails(array($betreff,str_replace('\r\n',"\n\n",$text)));

    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(NOW(),'%Y%m%d')");
    //$this->filename = $datum."_".str_replace(' ','',trim($this->recipient['enterprise'])).".pdf";
    $this->filename = $datum.".pdf";

    //$this->setBarcode($id);
  }

  function GetBrief($id)
  {

    $adresse = $this->app->DB->Select("SELECT adresse FROM dokumente_send WHERE id='$id' LIMIT 1");
    $this->setRecipientDB($adresse);

    $details['Bearbeiter'] = $this->app->User->GetName();
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    if($kundennummer!="" && $kundennummer!=0)
      $details['Ihre Kundennummer']=$kundennummer;

    $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    if($lieferantennummer!="" && $lieferantennummer!=0)
      $details['Lieferantennummer']=$lieferantennummer;

    $telefax= $this->app->DB->Select("SELECT telefax FROM adresse WHERE id='$adresse' LIMIT 1");
    if($telefax!="" && $telefax!=0)
      $details['Ihre Faxnummer']=$telefax;
 
    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y') FROM dokumente_send WHERE id='$id' LIMIT 1");
      $details['Datum']=$datum;

    $this->setCorrDetails($details);


    //$this->setRecipient(array("DrakeData","Ralph","Voigt","Theodor-Heuss-Platz 8","86150","Augsburg"));
    $tmp = $this->app->DB->SelectArr("SELECT * FROM dokumente_send WHERE id='$id' LIMIT 1");
    
    $this->setLetterDetails(array($tmp[0]['betreff'],$tmp[0]['text']));
    $this->doctypeOrig=$tmp[0]['betreff'];
    $this->docBody=$tmp[0]['text'];

    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(NOW(),'%Y%m%d') LIMIT 1");
    $this->filename = $datum."_".str_replace(' ','',trim($this->recipient['enterprise'])).".pdf";

    $this->setBarcode($id);
  }

	

  
  /***********************************
   *     user space functions
   ***********************************/
  public function setLetterDetails($rdata){
    $this->letterDetails['subject']     = $rdata[0];
    $this->letterDetails['body']         = $rdata[1];
  }

  /***********************************
   *       public functions
   ***********************************/  
/* 
  public function renderDocument() {
    // prepare page details
    parent::PDF('P','mm','A4');

    $this->AddFont('HelveticaBoldCond','','HLBC____.php');
    $this->AddFont('HelveticaBoldCondItalic','','HLBCO___.php');
    $this->AddFont('HelveticaCond','','HLC_____.php');
    $this->AddFont('HelveticaCondItalic','','HLCO____.php');
    // invoke Header() and Footer() by adding a new page
    $this->AddPage();
    $this->SetDisplayMode("real","single");
      
    $this->SetMargins(15,50);
    $this->SetAutoPageBreak(true,50); 
    $this->AliasNbPages('{nb}');
    
    // render document top to bottom
    if(!empty($this->recipient)) 
      $this->renderRecipient();
      
//    if(!empty($this->sender)) 
//      $this->renderSender();
    
    $this->renderCorrDetails();
  
    $this->renderSubject();
    $this->renderBody();
  }
*/
  public function renderSubject() {
    $this->SetFont('HelveticaBoldCond','',11);
    $this->SetY(90);
    $this->Cell(80,5,$this->letterDetails['subject']);
  }
  
  public function renderBody() {
    $this->SetFont('HelveticaCond','',11);
    $this->SetY(116);
    $this->MultiCell(180,5,$this->letterDetails['body']);
    //$this->Ln(10);
    //$this->Cell(80,5,$this->letterDetails['valediction']);
    //$this->Ln(20);
    //$this->Cell(80,5,$this->sender['firstname']." ".$this->sender['familyname']);
  }
  
  
  
}
?>

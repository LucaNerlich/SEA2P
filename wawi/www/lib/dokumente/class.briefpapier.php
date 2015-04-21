<?php
include_once "class.superfpdf.php";

class Briefpapier extends SuperFPDF {
  public $doctype;
  public $doctypeOrig;
  
  public $logofile;
  public $sender;
  public $recipient;
  public $corrDetails;
  public $boldCorrDetails;
  public $textDetails;
  public $items;

  public $ust_befreit;
  
  public $barcode;
  public $firmendatenid;
	public $absender;
	public $knickfalz;
	public $projekt;

	public $id;
	public $table;

	
  /***********************************
   *     data aggregation functions
   ***********************************/  
  function Briefpapier(&$app,$projekt="") {
 
    //$orientation='P';$unit='mm';$format='A4';
		//parent::PDF_EPS($orientation,$unit,$format);
		$this->projekt = $projekt;

    $this->app=&$app;
		$this->absender = "";
    $this->firmendatenid = $this->app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");
   
    $hintergrund = $this->app->DB->Select("SELECT hintergrund FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
    
    if($hintergrund=="logo")
    {
   	$logo = $this->app->DB->Select("SELECT logo FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1"); 
   	$filename = $this->app->erp->GetTMP().'/'.$this->app->Conf->WFdbname.'_logo.jpg';
   	if ($handle = fopen($filename, "w")) {
   		fwrite($handle, base64_decode($logo));
   		fclose($handle);
   	}
   	$this->logofile = $this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_logo.jpg";
    	$this->briefpapier="";
    }
    else if($hintergrund=="briefpapier")
    {
   		$briefpapier = $this->app->DB->Select("SELECT briefpapier FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1"); 
   		$filename = $this->app->erp->GetTMP().'/'.$this->app->Conf->WFdbname.'_briefpapier.pdf';
   		if ($handle = fopen($filename, "w")) {
   			fwrite($handle, base64_decode($briefpapier));
   			fclose($handle);
   		}
   	
    	$this->briefpapier=$this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_briefpapier.pdf";
   		$this->logofile = ""; 

   		$briefpapier2vorhanden = $this->app->DB->Select("SELECT briefpapier2vorhanden FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1"); 
			$this->briefpapier2vorhanden = $briefpapier2vorhanden;
			if($briefpapier2vorhanden > 0)
			{
				$briefpapier2 = $this->app->DB->Select("SELECT briefpapier2 FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1"); 
   			$filename = $this->app->erp->GetTMP().'/'.$this->app->Conf->WFdbname.'_briefpapier2.pdf';
   			if ($handle = fopen($filename, "w")) {
   				fwrite($handle, base64_decode($briefpapier2));
   				fclose($handle);
   			}
   	
    		$this->briefpapier2=$this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_briefpapier2.pdf";
			}
    }
     else {
   		$this->logofile = ""; 
    	$this->briefpapier="";
    }  
    
    $this->knickfalz = $this->app->DB->Select("SELECT knickfalz  FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
    
    $footersichtbar = $this->app->DB->Select("SELECT footersichtbar  FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
    if($footersichtbar==1) $this->nichtsichtbar_footer = false; else $this->nichtsichtbar_footer = true;
 
    $seite_von_sichtbar = $this->app->DB->Select("SELECT seite_von_sichtbar FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
    if($seite_von_sichtbar==1) $this->seite_von_sichtbar = false; else $this->seite_von_sichtbar = true;

    $this->seite_von_ausrichtung = $this->app->DB->Select("SELECT seite_von_ausrichtung FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    


		$this->abstand_adresszeileoben = $this->app->DB->Select("SELECT abstand_adresszeileoben FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
		$this->abstand_boxrechtsoben = $this->app->DB->Select("SELECT abstand_boxrechtsoben FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
		$this->abstand_boxrechtsoben_lr = $this->app->DB->Select("SELECT abstand_boxrechtsoben_lr FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
		$this->abstand_betreffzeileoben = $this->app->DB->Select("SELECT abstand_betreffzeileoben FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
		$this->abstand_artikeltabelleoben = $this->app->DB->Select("SELECT abstand_artikeltabelleoben FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");

		$this->boxausrichtung = $this->app->DB->Select("SELECT boxausrichtung FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
    
    $sichtbar = $this->app->DB->Select("SELECT sichtbar  FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
    if($sichtbar==1) $this->nichtsichtbar_zeileabsender = false; else $this->nichtsichtbar_zeileabsender =true;
    
    
    $this->barcode_sichtbar = $this->app->DB->Select("SELECT barcode FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
  
		$projekt = $this->projekt; 


		$this->waehrung=$this->app->erp->Firmendaten("waehrung");
		
		

    // kann man herausfinden was fuer ein projekt angegeben ist???
    $speziallieferschein = $this->app->DB->Select("SELECT speziallieferschein FROM projekt WHERE id='$projekt' LIMIT 1");
    $speziallieferscheinbeschriftung = $this->app->DB->Select("SELECT speziallieferscheinbeschriftung FROM projekt WHERE id='$projekt' LIMIT 1");
    $eigenesteuer = $this->app->DB->Select("SELECT eigenesteuer FROM projekt WHERE id='$projekt' LIMIT 1");
		if($eigenesteuer=="1")
			$this->waehrung=$this->app->DB->Select("SELECT waehrung FROM projekt WHERE id='$projekt' LIMIT 1");
    if($speziallieferschein>0)
    {
			$seite1 = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Briefpapier1' AND objekt='Projekt' AND parameter='$projekt' LIMIT 1");	
			$seite2 = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Briefpapier2' AND objekt='Projekt' AND parameter='$projekt' LIMIT 1");	


    	$this->briefpapier=$this->app->erp->GetDateiPfad($seite1);
    	$this->briefpapier2=$this->app->erp->GetDateiPfad($seite2);

			if($seite2>0)
				$this->briefpapier2vorhanden = 1;

    	$this->logofile = "";

		if($speziallieferscheinbeschriftung!=1){
    	$this->nichtsichtbar_zeileabsender = true;
    	$this->nichtsichtbar_footer = true;
    	$this->nichtsichtbar_rechtsoben = true;
		}
    }    
    
    $this->nichtsichtbar_rechtsoben = true;
    $this->nichtsichtbar_summe = false;
		$this->nichtsichtbar_box=false;
		$this->nichtsichtbar_empfaenger=false;

  }




  public function addItem($rdata){
		// add rabatt
		if($rdata['price']!="-")
    	$rdata['tprice'] = $rdata['amount']*($rdata['price']-($rdata['price']/100*$rdata['rabatt'])    );
		else $rdata['tprice']='-';
    $this->items[]=$rdata;
  }
   
  public function setSender($rdata){

		
    $this->sender['enterprise']   = $this->app->erp->ReadyForPDF($rdata[0]);
    $this->sender['firstname']     = $this->app->erp->ReadyForPDF($rdata[1]);
    $this->sender['familyname']   = $this->app->erp->ReadyForPDF($rdata[2]);
    $this->sender['address1']     = $this->app->erp->ReadyForPDF($rdata[3]);
    $this->sender['areacode']     = $this->app->erp->ReadyForPDF($rdata[4]);
    $this->sender['city']         = $this->app->erp->ReadyForPDF($rdata[5]);
    if(isset($rdata[6]))$this->sender['country'] = $this->app->erp->ReadyForPDF($rdata[6]);
  }

  function setRecipientRechnung($id)
  {


  }


  function setRecipientLieferadresse($id,$table)
  {
		$this->id = $id;
		$this->table = $table;

    $tmp = $this->app->DB->SelectArr("SELECT * FROM $table WHERE id='$id' LIMIT 1");
    if($tmp[0]['typ']!="person")
    {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0][name]);

  if($tmp[0][abteilung]!="" && strlen($tmp[0][abteilung]) >1)
      $this->recipient['address2']   =  $this->app->erp->ReadyForPDF($tmp[0][abteilung]);

      	if($tmp[0][ansprechpartner]!="" && strlen($tmp[0][ansprechpartner])>1)
					$this->recipient['firstname']   = $this->app->erp->ReadyForPDF($tmp[0][ansprechpartner]);

			if($this->app->erp->Firmendaten("rechnung_gutschrift_ansprechpartner")!="1" && ($table=="rechnung" || $table=="gutschrift"))
					$this->recipient['firstname']   = "";

          if($tmp[0][unterabteilung]!="")
	$this->recipient['address3'] = $this->app->erp->ReadyForPDF($tmp[0][unterabteilung]);
  
    if($tmp[0][adresszusatz]!="")
	$this->recipient['address4'] = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);

    }

    else {
			$vorname = '';
			if(isset($tmp[0][vorname]) && $tmp[0][vorname]!='' && strlen(trim($tmp[0][vorname]))>0)	
				$vorname = "{$tmp[0][vorname]} ";	

      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($vorname.$tmp[0][name]);
      $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);
    }


    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($tmp[0][strasse]);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($tmp[0][plz]);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($tmp[0][ort]);
    //if($this->recipient['city']!="")
    $this->recipient['country']      = $this->app->erp->ReadyForPDF($tmp[0][land]);
  }


  function setRecipientDB($adresse)
  {
    $tmp = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
    if($tmp[0]['typ']!="person")
    {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0][name]);

      if($tmp[0][abteilung]!="" && strlen($tmp[0][abteilung])>1)
      $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0][abteilung]);

			
			if($this->app->erp->Firmendaten("rechnung_gutschrift_ansprechpartner")=="1")
			{
      	if($tmp[0][ansprechpartner]!="")
					$this->recipient['firstname']   = $this->app->erp->ReadyForPDF($tmp[0][ansprechpartner]);
			}

      if($tmp[0][unterabteilung]!="")
	$this->recipient['address3'] = $this->app->erp->ReadyForPDF($tmp[0][unterabteilung]);
  
    if($tmp[0][adresszusatz]!="")
	$this->recipient['address4'] = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);

    }

    else {
      $this->recipient['enterprise'] = $this->app->erp->ReadyForPDF($tmp[0][name]);
      $this->recipient['address2']   = $this->app->erp->ReadyForPDF($tmp[0][adresszusatz]);
    }


    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($tmp[0][strasse]);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($tmp[0][plz]);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($tmp[0][ort]);
    //if($this->recipient['city']!="")
    $this->recipient['country']      = $this->app->erp->ReadyForPDF($tmp[0][land]);
  }


  public function setRecipient($rdata){
    $this->recipient['enterprise']   = $this->app->erp->ReadyForPDF($rdata[0]);
    $this->recipient['firstname']   = $this->app->erp->ReadyForPDF($rdata[1]);
    $this->recipient['familyname']   = $this->app->erp->ReadyForPDF($rdata[2]);
    $this->recipient['address1']     = $this->app->erp->ReadyForPDF($rdata[3]);
    $this->recipient['areacode']     = $this->app->erp->ReadyForPDF($rdata[4]);
    $this->recipient['city']         = $this->app->erp->ReadyForPDF($rdata[5]);
    if(isset($rdata[3]))$this->recipient['country'] = $this->app->erp->ReadyForPDF($rdata[6]);
  }

  public function setCorrDetails($rdata){
		if($this->app->erp->Firmendaten("projektnummerimdokument")=="1")
			$rdata["Projekt"]=$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$this->projekt."' LIMIT 1");
    
			$this->corrDetails = $rdata;
			

  }
  public function setBoldCorrDetails($rdata){
    $this->boldCorrDetails = $rdata;
  }
  public function setTextDetails($rdata){
    $this->textDetails = $rdata;
  }
  

  public function setTotals($rdata){
    $this->totals   = $rdata;
  }
  
  // Briefpapier festlegen
  public function setStationery($stationeryfile) {
      $this->setSourceFile($stationeryfile);
      $tplidx = $this->ImportPage(1);
      $this->useTemplate($tplidx);    
  }
 /* 
  public function setLogo($logofile) {
    $this->logofile = "./lib/pdf/images/".$logofile;
  }
 */ 
  
  // label settings
  public function setBarcode($barcode) {
    if($this->barcode_sichtbar)
    $this->barcode = $barcode;
    else $this->barcode="";

  }  
  
  
  public function Header() {
		if($this->knickfalz!="1"){
    	$this->Line(0,87,5,87);
    	$this->Line(0,148,7,148);
    	$this->Line(0,192,5,192);
		}
    if($this->logofile!="") 
      $this->Image($this->logofile,15,10,110);
      
    if($this->barcode!=""){
      //$this->Rotate(90);
      $this->Code39(12, 265, $this->barcode, 1, 3);
      //$this->Rotate(0);
    }
    
    if($this->briefpapier!="" && $this->PageNo()<=1)
      $this->setStationery($this->briefpapier);

		// wenn 
		if($this->PageNo() > 1 && $this->briefpapier2!="" && $this->briefpapier2vorhanden=="1")
      $this->setStationery($this->briefpapier2);
		else if ( $this->PageNo() > 1 && $this->briefpapier!="")
      $this->setStationery($this->briefpapier);
  }  
  
  public function Footer() {
    
    $this->SetXY(12,-34);
    $this->SetFont($this->GetFont(),'',8);

	
		if($this->seite_von_sichtbar!="1")	
    	$this->Cell(0,8,'Seite '.$this->PageNo().' von {nb} '.$this->zusatzfooter,0,0,$this->seite_von_ausrichtung);
   
    if($this->nichtsichtbar_footer!=true)
    { 
    	$footerarr = $this->app->DB->SelectArr("SELECT * FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
    	$footerarr = $footerarr[0];

    foreach($footerarr as $key=>$value)
    	$footerarr[$key] = utf8_decode($value);
    
    $this->SetXY(10,-26);
    $this->SetDrawColor(30);
    $this->SetTextColor(30);
    $this->SetFont($this->GetFont(),'',7);
    $this->MultiCell($footerarr['footer_breite1'],3,utf8_encode("  ".$footerarr['footer_0_0']."\n  ".$footerarr['footer_0_1']."\n  ".$footerarr['footer_0_2']."\n  ".$footerarr['footer_0_3']."\n  ".$footerarr['footer_0_4']."\n  ".$footerarr['footer_0_5'].""),'','L');
    $this->SetXY($footerarr['footer_breite1'] + 10,-26); // Breite 1 + 10
    $this->MultiCell($footerarr['footer_breite2'],3,utf8_encode("  ".$footerarr['footer_1_0']."\n  ".$footerarr['footer_1_1']."\n  ".$footerarr['footer_1_2']."\n  ".$footerarr['footer_1_3']."\n  ".$footerarr['footer_1_4']."\n  ".$footerarr['footer_1_5'].""),'','L');
    $this->SetXY($footerarr['footer_breite1'] + $footerarr['footer_breite2'] + 10,-26); //breite 1 + breite 2 + 10
    $this->MultiCell($footerarr['footer_breite3'],3,utf8_encode("  ".$footerarr['footer_2_0']."\n  ".$footerarr['footer_2_1']."\n  ".$footerarr['footer_2_2']."\n  ".$footerarr['footer_2_3']."\n  ".$footerarr['footer_2_4']."\n  ".$footerarr['footer_2_5'].""),'','L');
    $this->SetXY($footerarr['footer_breite1'] + $footerarr['footer_breite2'] + $footerarr['footer_breite3'] + 10,-26); //breite 1 + breite 2 + breite 3 + 10
    $this->MultiCell($footerarr['footer_breite4'],3,utf8_encode("  ".$footerarr['footer_3_0']."\n  ".$footerarr['footer_3_1']."\n  ".$footerarr['footer_3_2']."\n  ".$footerarr['footer_3_3']."\n  ".$footerarr['footer_3_4']."\n  ".$footerarr['footer_3_5'].""),'','L');
    }
  }  
 
	public function archiveDocument()
	{

	 $dir = $this->app->Conf->WFuserdata."/pdfmirror/".$this->app->Conf->WFdbname;
		if(!is_dir($dir)){
				mkdir($dir, 0700,true);
//				echo "fehlt";
		}	
		if(!is_dir($dir."/".$this->table))
			mkdir ($dir."/".$this->table,0700,true);

    $this->Output($dir."/".$this->table."/".$this->id."_".$this->filename,'F');
	}
  
  public function inlineDocument() {
    $this->renderDocument();
//		$this->archiveDocument();
		header('Content-type: application/pdf');
    $this->Output();
    exit;
  }


  public function displayDocument() {
    $this->renderDocument();
		$this->archiveDocument();
    $this->Output($this->filename,'D');
    exit;
  }

  public function displayTMP() {
    $this->renderDocument();
		$this->archiveDocument();
    //$this->Output($this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_".$this->filename,'F');
    //return $this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_".$this->filename;
 		$this->Output($this->app->erp->GetTMP()."/".$this->filename,'F');
    return $this->app->erp->GetTMP()."/".$this->filename;

  }

  public function sendDocument() { 
    $this->renderDocument();
		$this->archiveDocument();
    $this->Output($this->filename,'S'); 
    exit;
  }  
  
  /***********************************
   *       public functions
   ***********************************/  
  // setup relevant data for a invoice
  public function setupTax() {
    // full taxrate
 //   define("USTV",0.19);
    // reduced taxrate
//    define("USTR",0.07);
  }  
  
  
  public function calcTotals() {
    $total=$totalFullTax=$totalReducedTax=0;
    for($i=0;$i<count($this->items);$i++) {
      $total += $this->items[$i]['tprice'];
      if($this->items[$i]['tax']=="USTV") $totalFullTax+= $this->items[$i]['tprice']*USTV;
      else                                $totalReducedTax+= $this->items[$i]['tprice']*USTR;
    }
    return array($total,$totalFullTax,$totalReducedTax);
  }

	function GetFont()
	{
		if($this->app->erp->Firmendaten("schriftart")!="")
			return $this->app->erp->Firmendaten("schriftart");
		else
			return 'Arial';
	}
  
  public function renderDocument() {
    // prepare page details
    parent::SuperFPDF('P','mm','A4');

//		if($this->app->erp->Firmendaten("schriftart")!="")
//			$this->SetFont($this->app->erp->Firmendaten("schriftart"));  

		if($this->app->erp->Firmendaten("schriftart")!="" && $this->app->erp->Firmendaten("schriftart")!="Arial" &&
				$this->app->erp->Firmendaten("schriftart")!="Courier" && $this->app->erp->Firmendaten("schriftart")!="Helvetica"
				&& $this->app->erp->Firmendaten("schriftart")!="Times" && $this->app->erp->Firmendaten("schriftart")!="Arial"){
			$this->AddFont($this->app->erp->Firmendaten("schriftart"),'',strtolower($this->app->erp->Firmendaten("schriftart")).'.php');
			$this->AddFont($this->app->erp->Firmendaten("schriftart"),'I',strtolower($this->app->erp->Firmendaten("schriftart")).'.php');
			$this->AddFont($this->app->erp->Firmendaten("schriftart"),'B',strtolower($this->app->erp->Firmendaten("schriftart")).'.php');
		}

    // invoke Header() and Footer() by adding a new page
    $this->AddPage();
		//$this->setStationery("/home/eproo/eproo-master/app/main/www/lib/dokumente/demo.pdf");
    $this->SetDisplayMode("real","single");
      
    $this->SetMargins(15,50);
    $this->SetAutoPageBreak(true,37); 
    $this->AliasNbPages('{nb}');
    
    // render document top to bottom
		if(!$this->nichtsichtbar_empfaenger)
		{
    if(!empty($this->recipient)) 
      $this->renderRecipient();
		}
    
    if($this->doctype=="deliveryreceipt" && !empty($this->barcode))
    {
      $y = $this->GetY();
      $this->Code39(15, $y+1, $this->barcode, 1, 5);
    } 

    //if(!empty($this->sender)) 
      $this->renderSender();
 
		if(!$this->nichtsichtbar_box)
		{ 
    if(!empty($this->corrDetails) || !empty($this->boldCorrDetails)) 
      $this->renderCorrDetails();
		}
      
    $this->renderDoctype();
    if($this->doctype=="brief")
    	{
//                 $this->Cell(80,5,$this->letterDetails['subject']);                                           
                        $this->SetY(95);                                                                                                 
//                        $this->renderText();
			$this->textDetails['body']=$this->letterDetails['body'];
                   //$this->MultiCell(180,5,$this->letterDetails['body']);                          
                  }
   $this->renderText();

    if(!empty($this->items)) {
      $this->renderItems();
      if($this->doctype!="deliveryreceipt") {
        if($this->GetY()>215) $this->AddPage();
				if(!$this->nichtsichtbar_summe)
					$this->renderTotals();
      }
    }
    
    $this->renderFooter();
  }

  
  public function renderRecipient(){
//    $this->SetY(50);
    $this->SetY(50+$this->abstand_adresszeileoben);
    $this->SetFont($this->GetFont(),'',10);
    if($this->recipient['enterprise']) {
      $this->Cell(80,5,$this->app->erp->ReadyForPDF($this->recipient['enterprise']),0,1);
    }

  if($this->recipient['address2']!="") 
      $this->Cell(80,5,$this->recipient['address2'],0,1);


    if($this->recipient['firstname']!="")
      $this->Cell(80,5,$this->recipient['firstname'],0,1);

      if($this->recipient['address3']!="")
      $this->Cell(80,5,$this->recipient['address3'],0,1);

    if($this->recipient['address4']!="")
      $this->Cell(80,5,$this->recipient['address4'],0,1);


    //$this->Cell(80,5,$this->recipient['firstname']." ".$this->recipient['familyname'],0,1);
    $this->Cell(80,5,$this->recipient['address1'],0,1);




    $this->SetFont($this->GetFont(),'B',11);
    if($this->recipient['country']!="")
      $this->Cell(80,5,$this->recipient['country']."-".$this->recipient['areacode']." ".$this->recipient['city'],0,1);
    else
      $this->Cell(80,5,$this->recipient['areacode']." ".$this->recipient['city'],0,1);
    //$this->SetFont($this->GetFont(),'',9);
    //if(isset($this->recipient['country'])) $this->Cell(80,5,$this->recipient['country'],0,1);
  }

	public function setAbsender($sender)
	{
		$this->absender = $sender;

	}
  
  public function renderSender() {
    $monthlu = array("", "Januar", "Februar", "M�rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    $monthnom = date("n");
    $month = $monthlu[$monthnom];
    $date = date("j").". ".$month." ".date("Y");
  

    if($this->nichtsichtbar_zeileabsender!=true)
    {

//abstand_adresszeileoben
    // line above address field
    $absender = (($this->absender!='') ? $this->absender : $this->app->DB->Select("SELECT absender FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1"));
    $schriftgroesse  = $this->app->DB->Select("SELECT schriftgroesse FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
       
    $this->SetY(43+$this->abstand_adresszeileoben);
    $this->SetFont($this->GetFont(),'',$schriftgroesse);
    //$cellStr = $this->sender['enterprise']." � ".$this->sender['address1']." � ".$this->sender['areacode']." ".$this->sender['city'];
    $cellStr = $this->app->erp->ReadyForPDF($absender);
    $this->Cell($this->GetStringWidth($cellStr)+2,5,$cellStr,'B');
    }
    
    if($this->nichtsichtbar_rechtsoben!=true)
    {
    // detailed sender data
    $lineHeight = 4;
    $xOffset = 131;
    $absatz = 3;
    
    $this->SetXY($xOffset,10);
    $this->SetFont($this->GetFont(),'',9);
    $this->Cell(30,$lineHeight,"Name der Gesellschaft: ",0,0,'R');
    $this->SetFont($this->GetFont(),'B',9);
    $this->Cell(60,$lineHeight,$this->sender['enterprise'],0,2);
    if(isset($this->sender['enterprise2']))
      $this->Cell(60,$lineHeight,$this->sender['enterprise2'],0,2);
      
    $this->SetXY($xOffset,$this->GetY());
    $this->SetFont($this->GetFont(),'',9);
    $this->Cell(30,$lineHeight,"Sitz der Gesellschaft: ",0,0,'R');
    $this->SetFont($this->GetFont(),'B',9);
    $this->Cell(60,$lineHeight,$this->sender['address1'],0,2);
    if(isset($this->sender['address2']))
      $this->Cell(60,$lineHeight,$this->sender['address2'],0,2);
    $this->Cell(60,$lineHeight,$this->sender['areacode']." ".$this->sender['city'],0,2);

    $this->SetXY($xOffset,$this->GetY()+$absatz); //abstand
    $this->SetFont($this->GetFont(),'',9);
    if(isset($this->sender['phone1'])) {
      $this->Cell(30,$lineHeight,"Fon: ",0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['phone1'],0,2);
    }
    if(isset($this->sender['fax'])) {
      $this->SetXY($xOffset,$this->GetY());
      $this->Cell(30,$lineHeight,"Fax: ",0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['fax'],0,2);
    }

    
    $this->SetXY($xOffset, $this->GetY()+$absatz); //abstand
    if(isset($this->sender['email'])) {
      $this->Cell(30,$lineHeight,"Mail: ",0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['email'],0,2);  
    }
      
    if(isset($this->sender['web'])) {
      $this->SetXY($xOffset,$this->GetY());
      $this->Cell(30,$lineHeight,"Web: ",0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['web'],0,2);  
    }
    
    $this->SetXY($xOffset, $this->GetY()+$absatz); //abstand
    if(isset($this->sender['ustid'])) {
      $this->Cell(30,$lineHeight,"UST-ID: ",0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['ustid'],0,2);
    }
    if(isset($this->sender['taxnr'])) {
      $this->SetXY($xOffset,$this->GetY());
      $this->Cell(30,$lineHeight,"Steuer-Nr.: ",0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['taxnr'],0,2);
    }
    if(isset($this->sender['hreg'])) {
      $this->SetXY($xOffset,$this->GetY());
      $this->Cell(30,$lineHeight,"Handelsregister: ",0,0,'R');
      $this->Cell(60,$lineHeight,$this->sender['hreg'],0,2);
    }
    
    $this->SetXY($xOffset,$this->GetY());
    $this->Cell(30,$lineHeight,utf8_encode("Gesch�ftsf�hrung: "),0,0,'R');
    $this->Cell(60,$lineHeight,$this->sender['firstname'].' '.$this->sender['familyname'],0,2);
    
    //$this->SetXY($xOffset, $this->GetY()+$absatz+2); //abstand
    //$this->Cell(30,$lineHeight,"Datum: ",0,0,'R');
    //$this->Cell(60,$lineHeight,utf8_encode($date),0,2);
    } 
  }
  
  
  // kundennummer rechnungsnummer und datum
  public function renderCorrDetails() {

		$breite_spalte_rechts = 30;

		$recht_links_verschieben = $this->abstand_boxrechtsoben_lr;

		$startpos_links_rechts = -83 + $recht_links_verschieben;

    $boldTitleStr = $boldValueStr = $titleStr = $valueStr = "";
    if(isset($this->boldCorrDetails)){
      foreach($this->boldCorrDetails as $title => $value) {
 				$boldTitleStr .= $this->app->erp->ReadyForPDF($title).": \n";
        $boldValueStr .= $this->app->erp->ReadyForPDF($value)."\n";
      }
    }
    if(isset($this->corrDetails)){
      foreach($this->corrDetails as $title => $value) {
				if($value!="")
				{
	        $titleStr .= $this->app->erp->ReadyForPDF($title).": \n";
          $valueStr .= $this->app->erp->ReadyForPDF($value)."\n";	
      	}
      }
    }
   
		if($this->boxausrichtung=="") $this->boxausrichtung="R";

		$pos = strpos($this->boxausrichtung, ';');	
		if($pos > 0)
		{
			$tmp_box = explode(";",$this->boxausrichtung);
			$this->boxausrichtung = $tmp_box[0];
			$this->box_breite1 = $tmp_box[1];
			$this->box_breite2 = $tmp_box[2];
		}
		if($this->box_breite1=="") $this->box_breite1=34;
		if($this->box_breite2=="") $this->box_breite2=38;

    if($boldTitleStr!="")
    {
      $this->SetFont($this->GetFont(),'B',9);
      $this->SetXY($startpos_links_rechts,80);
      $this->MultiCell($this->box_breite1,4,$boldTitleStr,"",$this->boxausrichtung); //TL
      $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,80);
      $this->MultiCell($this->box_breite2,4,$boldValueStr,"",$this->boxausrichtung);   //TR
    } else {
      $this->SetXY($startpos_links_rechts,80+$this->abstand_boxrechtsoben);
      $this->MultiCell($this->box_breite1,0,"","",$this->boxausrichtung); //TL
      $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,80+$this->abstand_boxrechtsoben);
      $this->MultiCell($this->box_breite2,0,"","",$this->boxausrichtung);   //TR
    }

		$this->SetY(80);
    $this->SetY($this->GetY()+$this->abstand_boxrechtsoben);

    $this->SetFont($this->GetFont(),'',9);
    $tempY = $this->GetY();
    $this->SetX($startpos_links_rechts);
    $this->MultiCell($this->box_breite1,4,$titleStr,"",$this->boxausrichtung); //BL
    $this->SetXY($startpos_links_rechts+$breite_spalte_rechts,$tempY);
    $this->MultiCell($this->box_breitexi21,4,$valueStr,"",$this->boxausrichtung); //BR

		$this->SetY(80+$this->abstand_artikeltabelleoben); //Hoehe Box
		//$this->SetY(60);//+$this->abstand_artikeltabelleoben); //Hoehe Box
  }  

  
  public function renderDoctype() {
    //$this->Ln(1);
        
      if($this->doctype=="brief")
      $betreffszeile  = $this->app->DB->Select("SELECT brieftext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");                                                                                       
      	else
    $betreffszeile  = $this->app->DB->Select("SELECT betreffszeile FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");                                                                                       

		$this->SetY(80);//+$this->abstand_artikeltabelleoben); //Hoehe Box
		//$this->SetY(80+$this->abstand_artikeltabelleoben); //Hoehe Box
    $this->SetFont($this->GetFont(),'B',$betreffszeile);
    $this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    $this->Cell(85,6,$this->doctypeOrig);
    $this->SetY($this->GetY()-$this->abstand_betreffzeileoben);

    //$this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
		$this->SetY($this->GetY()+$this->abstand_artikeltabelleoben); //Hoehe Box
  }
  
  public function renderText() {
    if(isset($this->textDetails['body'])) {
      if($this->doctype=="brief")
      $dokumententext  = $this->app->DB->Select("SELECT brieftext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");                                                                                       
      else
      $dokumententext  = $this->app->DB->Select("SELECT dokumententext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");                                                                                       
      $this->SetFont($this->GetFont(),'',$dokumententext);
      if($this->doctype!="brief")
      	$this->Ln(11);
      $this->MultiCell(180,4,$this->app->erp->ReadyForPDF($this->textDetails['body']));
    }
  }  
  
  public function renderFooter() {
    if(isset($this->textDetails['footer'])) {
      $freitext  = $this->app->DB->Select("SELECT freitext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");                                                                                       

			if($this->app->erp->Firmendaten("kleinunternehmer"))
			{
				if($this->textDetails['footer']=="") $this->textDetails['footer']="Als Kleinunternehmer im Sinne von §19 Abs.1 UStG wird Umsatzsteuer nicht berechnet!";
				else $this->textDetails['footer']="\r\nAls Kleinunternehmer im Sinne von § 19 Abs. 1 UStG wird Umsatzsteuer nicht berechnet!";
			}
      $this->SetFont($this->GetFont(),'',$freitext);
//      $this->Ln();
			$y = $this->GetY();

//			if($this->doctype!="deliveryreceipt")
//				$this->SetY($y-5);

      $this->MultiCell(180,4,$this->app->erp->ReadyForPDF($this->textDetails['footer']));
    }
  }
  
  public function renderItems() {

//		if($this->bestellungohnepreis) $this->doctype="deliveryreceipt";
    $posWidth     = $this->app->erp->Firmendaten("breite_position");
    $amWidth     = $this->app->erp->Firmendaten("breite_menge");
    $itemNoWidth = $this->app->erp->Firmendaten("breite_nummer");
    $einheitWidth    = $this->app->erp->Firmendaten("breite_einheit");

    if($this->doctype=="arbeitsnachweis")
		{
    	$itemNoWidth = 20;
			$taxWidth = 40;
      $descWidth   = 95;
		}
    else if($this->doctype!="deliveryreceipt" && $this->doctype!="produktion") {
      $descWidth   = 76;
   		$taxWidth   = 15;
		}
    else
		{
      $itemNoWidth = 30;
      $descWidth   = 91;
   		$taxWidth   = 15;
		}

   	if($this->rabatt=="1") $descWidth = $descWidth - 15;
    $priceWidth = 20;
    $sumWidth   = 20;
    $rabattWidth   = 15;
    // $lineLength = $amWidth + $itemNoWidth + $descWidth + $taxWidth + $priceWidth + $sumWidth;

    $cellhoehe   = 5;

    // render table header
    if(isset($this->textDetails['body'])) $this->Ln();
    else $this->Ln(8);
    $tabellenbeschriftung  = $this->app->DB->Select("SELECT tabellenbeschriftung FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");

    $this->SetFont($this->GetFont(),'B',$tabellenbeschriftung);
    $this->Cell($posWidth,6,'Pos',0,0,'C');

	 	if($this->doctype!="arbeitsnachweis") 
		{

			if($this->doctype=="zahlungsavis")
			{
				$this->Cell($itemNoWidth,6,'Nummer');
    		$this->Cell($descWidth-$einheitWidth+$taxWidth+$priceWidth+$rabattWidth,6,'Beleg');

    		$this->Cell($amWidth,6,'',0,0,'R');
			}
			else {
    		$this->Cell($itemNoWidth,6,'Artikelnr');

				if($this->app->erp->Firmendaten("artikeleinheit")=="1")
    			$this->Cell($descWidth-$einheitWidth,6,'Artikel');
				else
    			$this->Cell($descWidth,6,'Artikel');
    		$this->Cell($amWidth,6,'Menge',0,0,'R');
			}
		} else {
      $this->Cell($taxWidth,6,'Mitarbeiter');
    	$this->Cell($itemNoWidth,6,'Ort');
    	$this->Cell($descWidth,6,'Tätigkeit');
    	$this->Cell($amWidth,6,'Stunden',0,0,'R');
		}

    if($this->doctype!="deliveryreceipt" && $this->doctype!="arbeitsnachweis" && $this->doctype!="produktion" && $this->doctype!="zahlungsavis"){
			if($this->app->erp->Firmendaten("artikeleinheit")=="1")
      	$this->Cell($einheitWidth,6,'Einheit',0,0,'R');

			if($this->app->erp->Firmendaten("kleinunternehmer"))
      	$this->Cell($taxWidth,6,'',0,0,'R');
			else
      	$this->Cell($taxWidth,6,'MwSt.',0,0,'R');

			if($this->app->erp->Firmendaten("artikeleinheit")=="1")
      	$this->Cell($priceWidth,6,utf8_encode('Einzel'),0,0,'R');
			else
      	$this->Cell($priceWidth,6,utf8_encode('Stck'),0,0,'R');

    	if($this->rabatt=="1") {
      $this->Cell($rabattWidth,6,'Rabatt',0,0,'R');
      $this->Cell($sumWidth,6,'Gesamt',0,0,'R');
			} else {
      	$this->Cell($sumWidth,6,'Gesamt',0,0,'R');
			}
    }
		else if ($this->doctype=="zahlungsavis")
		{
      	$this->Cell($sumWidth,6,'Gesamt',0,0,'R');
		}	

    $this->Ln();
    $this->Line($this->GetX(), $this->GetY(), 190, $this->GetY()); 
    $this->Ln(2);
  
    // render table body
    $tabelleninhalt  = $this->app->DB->Select("SELECT tabelleninhalt FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
        
    $this->SetFont($this->GetFont(),'',$tabelleninhalt);
   
    $pos=0; 
    foreach($this->items as $item){

			$item['name'] = $this->app->erp->ReadyForPDF($item['name']);
			$item['desc'] = $this->app->erp->ReadyForPDF($item['desc']);
			$item['itemno'] = $this->app->erp->ReadyForPDF($item['itemno']);
			$item['herstellernummer'] = $this->app->erp->ReadyForPDF($item['herstellernummer']);
			$item['hersteller'] = $this->app->erp->ReadyForPDF($item['hersteller']);

      $cellhoehe  = 3;
      //position
      $this->Cell($posWidth,$cellhoehe,++$pos,0,0,'C');
      //artikelnummer
			if($this->doctype=="arbeitsnachweis")
			{
				$this->Cell($taxWidth,$cellhoehe,trim($item['person']),0);
	
				$zeilenuntertext  = $this->app->DB->Select("SELECT zeilenuntertext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);

				// ort
				$tmpy = $this->GetY();
				$tmpx = $this->GetX();
        $this->MultiCell($itemNoWidth,($zeilenuntertext/2),trim($item['itemno']),0); // 4 = abstand
				$tmpy2 = $this->GetY();
      	//if(isset($item['itemno'])) $this->Cell($itemNoWidth,$cellhoehe,$item['itemno'],0);
      	//else $this->Cell($itemNoWidth);
				$this->SetXY($tmpx+$itemNoWidth,$tmpy);
				//$this->SetY($tmpy2);
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
			}
			else {
//TODO BENE
 				if($this->doctype=="deliveryreceipt" && $this->app->erp->Firmendaten("modul_verband")=="1")
				  $this->SetFont($this->GetFont(),'',$tabelleninhalt+3);
        else
				  $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      	if(isset($item['itemno'])) $this->Cell($itemNoWidth,$cellhoehe,$item['itemno'],0);
      	else $this->Cell($itemNoWidth);
				$this->SetFont($this->GetFont(),'',$tabelleninhalt);
			}

			$position_x   = $this->GetX();
			$position_y   = $this->GetY();

      // Artikel Name
			if($item['tax']!=="hidden")		
			$this->SetFont($this->GetFont(),'B',$tabelleninhalt);

			if($this->app->erp->Firmendaten("artikeleinheit")=="1")
      	$this->MultiCell($descWidth-$einheitWidth,$cellhoehe,$item['name'],0,Alignment.LEFT, false);
			else
      	$this->MultiCell($descWidth,$cellhoehe,$item['name'],0,Alignment.LEFT, false);
			$this->SetFont($this->GetFont(),'',$tabelleninhalt);

      $position_y_end_name   = $this->GetY();

      // wenn vorhanden Artikel Einheit

			if($this->app->erp->Firmendaten("artikeleinheit")=="1")
				$this->SetXY(($position_x + $descWidth-$einheitWidth), $position_y);
			else
				$this->SetXY(($position_x + $descWidth), $position_y);


        
			if($this->doctype=="arbeitsnachweis")
				$this->SetXY(($position_x + $descWidth), $position_y);


      // Menge

			if($this->doctype=="zahlungsavis")
      	$this->Cell($amWidth,$cellhoehe,"",0,0,'R');
			else
      	$this->Cell($amWidth,$cellhoehe,$item['amount'],0,0,'R');

      if($this->doctype!="deliveryreceipt" && $this->doctype!="arbeitsnachweis" && $this->doctype!="produktion") {
				if($this->app->erp->Firmendaten("artikeleinheit")=="1")
				{
					if($item['unit']!="")
						$einheit = $item['unit'];
					else {
						$einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE 
							nummer='".$item['itemno']."' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
						if($einheit=="") $einheit = $this->app->erp->Firmendaten("artikeleinheit_standard");
					}
        	$this->Cell($einheitWidth,$cellhoehe,$this->app->erp->ReadyForPDF($einheit),0,0,'R');
				}

	//			if($item['tax']=="hidden") $item['tax']=="hidden";
        if($item['tax']!=="hidden")
        {          
          if($this->ust_befreit>0) { 
            $item['tax'] = 0;
          } else {
            if($item['tax'] == "normal") $item['tax'] = $this->app->erp->GetSteuersatzNormal(true,$this->id,$this->table)-1; 
              else $item['tax'] = $this->app->erp->GetSteuersatzErmaessigt(true,$this->id,$this->table)-1;
          }
        }

        
        // wenn steuerfrei komplett immer 0 steuer anzeigen
				$item['tmptax'] = $item['tax'] + 1;

        // standard anzeige mit steuer
				if($this->app->erp->Firmendaten("kleinunternehmer")!="1"){
					if($item['tax']==="hidden"){
        		$this->Cell($taxWidth,$cellhoehe,"",0,0,'C');
          } else {
        		$tax = $item['tax']; //= $tax; //="USTV"?0.19:0.07;
        		$tax *= 100; $tax = $tax."%";

						if($this->doctype=="zahlungsavis")
        			$this->Cell($taxWidth,$cellhoehe,"",0,0,'C');
						else
        			$this->Cell($taxWidth,$cellhoehe,$tax,0,0,'C');
					} 
				} else {
					//kleinunternehmer
					$this->Cell($taxWidth,$cellhoehe,"",0,0,'C');
				}


			if($this->doctype!="deliveryreceipt" && $this->doctype!="produktion") {
        // preis pro Artikel

				if($this->doctype!="zahlungsavis")
				{
					if($item['tax']!=="hidden"){
						if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1")
         			$this->Cell($priceWidth,$cellhoehe,number_format($item['price'], 2, ',', ''),0,0,'R');
						else
         			$this->Cell($priceWidth,$cellhoehe,number_format($item['price']*$item['tmptax'], 2, ',', ''),0,0,'R');
        	} else
         		$this->Cell($priceWidth,$cellhoehe,number_format($item['price'], 2, ',', ''),0,0,'R');
				} else {
         		$this->Cell($priceWidth,$cellhoehe,"",0,0,'R');
				}

        // zentale rabatt spalte
				if($this->rabatt=="1") {
					$rabatt_string="";
				
					//rabatt	
					if($item['grundrabatt'] > 0 || $item['rabatt1'] > 0 || $item['rabatt2'] > 0)
					{
						if($item['grundrabatt']>0) $rabatt_string .= $item['grundrabatt']."%\r\n";
						if($item['rabatt1']>0) $rabatt_string .= $item['rabatt1']."%\r\n";
						if($item['rabatt2']>0) $rabatt_string .= $item['rabatt2']."%\r\n";
						if($item['rabatt3']>0) $rabatt_string .= $item['rabatt3']."%\r\n";
						if($item['rabatt4']>0) $rabatt_string .= $item['rabatt4']."%\r\n";
						if($item['rabatt5']>0) $rabatt_string .= $item['rabatt5']."%\r\n";


						$tmpy = $this->GetY();
						$tmpx = $this->GetX();
				
						$this->SetFont($this->GetFont(),'',6);
						if($item['keinrabatterlaubt']=="1" || $item['rabatt']<=0 || $item['rabatt']==="") {
                $rabatt_or_porto = $this->app->DB->Select("SELECT id FROM artikel WHERE 
                  nummer='".$item['itemno']."' AND (porto='1' OR rabatt='1') AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
                if($rabatt_or_porto)
                  $rabatt_string="";
                else {
                    if($this->app->erp->Firmendaten("modul_verband")=="1") $rabatt_string="SNP";
                    else $rabatt_string="";
                }
            }
 
				    if($item['tax']!=="hidden")
         		  $this->MultiCell($rabattWidth,$cellhoehe-1,$rabatt_string,0,0,'L');
            else
         		  $this->MultiCell($rabattWidth,$cellhoehe-1,"",0,0,'L');
    
            
						$this->SetXY($tmpx+$rabattWidth,$tmpy);
	         	$this->SetFont($this->GetFont(),'',$tabelleninhalt);
					} else {
            if($item['rabatt']>0 && $item['keinrabatterlaubt']!="1")
               $this->Cell($rabattWidth,$cellhoehe,$item['rabatt']." %",0,0,'R');
            else
            {
              if($this->app->erp->Firmendaten("modul_verband")=="1")
              {
                $rabatt_or_porto = $this->app->DB->Select("SELECT id FROM artikel WHERE 
                  nummer='".$item['itemno']."' AND (porto='1' OR rabatt='1') AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
                if($rabatt_or_porto)
                  $this->Cell($rabattWidth,$cellhoehe,"",0,0,'R');
                else
                  $this->Cell($rabattWidth,$cellhoehe,"SNP",0,0,'R');
              } else {
                  $this->Cell($rabattWidth,$cellhoehe,"",0,0,'R');
              }
            }
				  }
				} 
				else {
            // anzeige ohne zentrale rabatt spalte
            if ($item['tax']==="hidden"){
						  $this->Cell($priceWidth,$cellhoehe,"",0,0,'R');
					  }
					  else {
						  if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1")
							  $this->Cell($priceWidth,$cellhoehe,number_format($item['tprice'], 2, ',', ''),0,0,'R');
						  else
							  $this->Cell($priceWidth,$cellhoehe,number_format($item['tprice']*$item['tmptax'], 2, ',', ''),0,0,'R');

          	  $this->Cell($rabattWidth,$cellhoehe,"",0,0,'R');
					  }
					}
				} 
        else {
					if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1")
        		$this->Cell($priceWidth,$cellhoehe,number_format($item['price'], 2, ',', ''),0,0,'R');
					else
        		$this->Cell($priceWidth,$cellhoehe,number_format($item['price']*$item['tmptax'], 2, ',', ''),0,0,'R');
				}
        //$this->Cell($sumWidth,$cellhoehe,number_format($item['tprice'], 2, ',', '').' '.$item['currency'],0,0,'R');
   			if($this->rabatt=="1")
        {
        //gesamt preis
				if ($item['tax']==="hidden"){
						$this->Cell($priceWidth,$cellhoehe,"",0,0,'R');
				}
				else {
					if($this->rabatt=="1"){
						if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1")
        			$this->Cell($sumWidth,$cellhoehe,number_format($item['tprice'], 2, ',', ''),0,0,'R');
						else
        			$this->Cell($sumWidth,$cellhoehe,number_format($item['tprice']*$item['tmptax'], 2, ',', ''),0,0,'R');
					}
					else {
						if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1")
        			$this->Cell($sumWidth,$cellhoehe,number_format($item['tprice'], 2, ',', ''),0,0,'R');
						else
        			$this->Cell($sumWidth,$cellhoehe,number_format($item['tprice']*$item['tmptax'], 2, ',', ''),0,0,'R');
					}
				}
				}

			}

      $this->Ln();
			if($this->app->erp->Firmendaten("herstellernummerimdokument")=="1" && $item['herstellernummer']!="")
			{
				if($item['desc']!="")
					$item['desc']=$item['desc']."\r\nPN: ".$item['herstellernummer'];
				else
					$item['desc']="PN: ".$item['herstellernummer'];
			}

      if($item['desc']!="") {
				//Herstellernummer einblenden wenn vorhanden und aktiviert
							
      	$zeilenuntertext  = $this->app->DB->Select("SELECT zeilenuntertext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
				$this->SetY($position_y_end_name+$this->app->erp->Firmendaten("abstand_name_beschreibung"));
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
				if($this->doctype=="arbeitsnachweis") $this->Cell($taxWidth);

 				if($this->doctype=="deliveryreceipt" && $this->app->erp->Firmendaten("modul_verband")=="1")
          $this->SetFont($this->GetFont(),'',$tabelleninhalt+1);


				if($this->app->erp->Firmendaten("artikeleinheit")=="1")
				{
					if($this->app->erp->Firmendaten("breite_artikelbeschreibung"))
        		$this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand
					else
        		$this->MultiCell($descWidth-$einheitWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand //ALT
				}
				else
				{
					if($this->app->erp->Firmendaten("breite_artikelbeschreibung")=="1")
        		$this->MultiCell($descWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand
					else
        		$this->MultiCell($descWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand //ALT
				}
        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);

      	$zeilenuntertext  = $this->app->DB->Select("SELECT zeilenuntertext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
				if($this->doctype=="arbeitsnachweis") $this->Cell($taxWidth);
				if($this->app->erp->Firmendaten("artikeleinheit")=="1")
        	$this->MultiCell($descWidth-$einheitWidth,4,"",0); // 4 = abstand zwischen Artikeln
				else
        	$this->MultiCell($descWidth,4,"",0); // 4 = abstand zwischen Artikeln
        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      } else {

      	$zeilenuntertext  = $this->app->DB->Select("SELECT zeilenuntertext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
				$this->SetY($position_y_end_name);
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
				if($this->doctype=="arbeitsnachweis") $this->Cell($taxWidth);

				if($this->app->erp->Firmendaten("artikeleinheit")=="1")
        	$this->MultiCell($descWidth-$einheitWidth,3,trim($item['desc']),0); // 4 = abstand
				else
        	$this->MultiCell($descWidth,3,trim($item['desc']),0); // 4 = abstand

        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);

			}
    }

    $this->Line($this->GetX(), $this->GetY(), 190, $this->GetY()); 
  }  

  
  /*
    totals (
      totalArticles   - Summe aller Artikelpreise
      modeOfDispatch   - Versandart
      priceOfDispatch  - Versandkosten
      modeOfPayment    - Zahlungsweise
      priceOfPayment  - Kosten der Zahlungsweise
      total           = totalArticles + priceOfDispatch + priceOfPayment
      totalTaxV      - Summe voller Steuersatz
      totalTaxR      - Summe reduzierter Steuersatz
    )
  */
  public function renderTotals() {

    $this->SetY($this->GetY()+1);
  if($this->doctype!="deliveryreceipt" && $this->doctype!="arbeitsnachweis") {
    //$this->Line(110, $this->GetY(), 190, $this->GetY());
    $this->Ln(1);
    $this->SetFont($this->GetFont(),'',9);
    $this->Cell(100,2,'',0);
    if($this->app->erp->Firmendaten("kleinunternehmer")!="1" && $this->doctype!="zahlungsavis"){
    	$this->Cell(30,5,'Gesamt netto',0,0,'L');
    	$this->Cell(40,5,number_format($this->totals['totalArticles'], 2, ',', '').' '.$this->waehrung,0,'L','R');
		} else {
			//kleinunzernehmer
    	$this->Cell(30,5,'',0,0,'L');
    	$this->Cell(40,5,'',0,'L','R');
		}
    $this->Ln();
    
    if(isset($this->totals['modeOfDispatch'])) $versand = 'Versand: '.$this->totals['modeOfDispatch'];
    else $versand = 'Versandkosten: ';
    if(isset($this->totals['priceOfDispatch'])) {
      $this->Cell(100,2,'',0);
      $this->Cell(30,5,$versand,0,'L','L');
      $this->Cell(40,5,number_format($this->totals['priceOfDispatch'], 2, ',', '').' '.$this->waehrung,0,'L','R');
    }
    //$this->Ln();
    
    if(isset($this->totals['priceOfPayment']) && $this->totals['priceOfPayment']!='0.00'){
      $this->Cell(100,2,'',0);
      $this->Cell(30,5,$this->totals['modeOfPayment'],0,'L','L');
      $this->Cell(40,5,number_format($this->totals['priceOfPayment'], 2, ',', '').' '.$this->waehrung,0,'L','R');
      $this->Ln();    
    }

    $this->SetY($this->GetY());
    $this->SetFont($this->GetFont(),'',8);

		
     if(isset($this->totals['totalTaxV']) && $this->totals['totalTaxV']!="0.00"){
      $this->Cell(100,1,'',0);

      if($this->app->erp->Firmendaten("kleinunternehmer")!="1"){
				if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1"){
      		$this->Cell(30,3,'zzgl. MwSt. '.$this->app->erp->GetSteuersatzNormal(false,$this->id,$this->table).' %',0,'L','L');
				}
				else {
      		$this->Cell(30,3,'inkl. MwSt. '.$this->app->erp->GetSteuersatzNormal(false,$this->id,$this->table).' %',0,'L','L');
				}
      	$this->Cell(40,3,number_format($this->totals['totalTaxV'], 2, ',', '').' '.$this->waehrung,0,'L','R');
			} else {
				//kleinunternehmer
      	$this->Cell(30,3,'',0,'L','L');
      	$this->Cell(40,3,'',0,'L','R');
			}
      $this->Ln();
    }
    
    if(isset($this->totals['totalTaxR']) && $this->totals['totalTaxR']!="0.00"){
      $this->Cell(100,1,'',0);

			if($this->app->erp->Firmendaten("kleinunternehmer")!="1"){
				if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1")
      		$this->Cell(30,3,'zzgl. MwSt. '.$this->app->erp->GetSteuersatzErmaessigt(false,$this->id,$this->table).' %',0,'L','L');
				else
      		$this->Cell(30,3,'inkl. MwSt. '.$this->app->erp->GetSteuersatzErmaessigt(false,$this->id,$this->table).' %',0,'L','L');

      	$this->Cell(40,3,number_format($this->totals['totalTaxR'], 2, ',', '').' '.$this->waehrung,0,'L','R');
			} else {
				//kleinunternehmer
      	$this->Cell(30,3,'',0,'L','L');
      	$this->Cell(40,3,"",0,'L','R');
			}

      $this->Ln();
    }

    if(!isset($this->totals['totalTaxR']) && !isset($this->totals['totalTaxV']) && $this->doctype!="zahlungsavis")
    {
      $this->Cell(100,3,'',0);

			if($this->app->erp->Firmendaten("kleinunternehmer")!="1"){
				if($this->anrede=="firma" || $this->doctype=="bestellung" || $this->app->erp->Firmendaten("immernettorechnungen")=="1")
      		$this->Cell(30,3,'zzgl. MwSt. 0.00 %',0,'L','L');
				else
      		$this->Cell(30,3,'inkl. MwSt. 0.00 %',0,'L','L');
      	$this->Cell(40,3,'0,00 '.$this->waehrung,0,'L','R');
			} else {
				//kleinunternehmer
      		$this->Cell(30,3,'',0,'L','L');
      		$this->Cell(40,3,'',0,'L','R');
			}
      $this->Ln();
    }
    $this->SetY($this->GetY()+2);
    //$this->Line(110, $this->GetY(), 190,$this->GetY());
	} 
    
    $this->SetFont($this->GetFont(),'B',9);
    $this->Cell(100,5,'',0);
    if($this->doctype=="offer")
      $this->Cell(30,5,'Angebotssumme',0,'L','L');
    elseif($this->doctype=="creditnote")
      $this->Cell(30,5,'Gutschriftbetrag',0,'L','L');
    else if($this->doctype=="arbeitsnachweis")
      $this->Cell(30,5,'Stunden',0,'L','L');
    else if($this->doctype=="zahlungsavis")
      $this->Cell(30,5,'Summe',0,'L','L');
    else 
      $this->Cell(30,5,'Rechnungsbetrag',0,'L','L');

    if($this->doctype=="arbeitsnachweis")
    	$this->Cell(40,5,$this->totals['total'].' ',0,'L','R');
		else {
			if($this->app->erp->Firmendaten("kleinunternehmer")!="1")
    		$this->Cell(40,5,number_format($this->totals['total'], 2, ',', '').' '.$this->waehrung,0,'L','R');
			else
    		$this->Cell(40,5,number_format($this->totals['totalArticles'], 2, ',', '').' '.$this->waehrung,0,'L','R');
		}

    $this->Ln();
    $this->Line(110, $this->GetY(), 190,$this->GetY());   
    $this->Line(110, $this->GetY()+1, 190,$this->GetY()+1);
    
    $this->SetY($this->GetY()+10);
    }
  
}
?>

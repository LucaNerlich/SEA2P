<?php
define("FPDF_FONTPATH","/home/sauterbe/public_html/eprooSystem-2009-11-21/webroot/lib/fpdf/font/");
require("./lib/fpdf/eproopdf.php");

class etiketten
{

  function etiketten($app)
  {
    $this->app=&$app;
  }

  function Artikel($barcode,$label,$anzahl=65)
  {

    $etikett_breite = 38.1;
    $etikett_hoehe = 21.2;
    $abstand_links = 5.0;
    $abstand_oben = 15.0;
    $abstand_etikett = 2.5;
    $druckerversatz = 1;

    $pdf=new EPROOPDF();

    for($seiten=0;$seiten < $anzahl/65; $seiten++)
    {
      $pdf->AddPage();
      $pdf->SetFont('Arial','B',16);
      for($zeile=0;$zeile<13;$zeile++)
      {
	for($spalte=0;$spalte<5;$spalte++)
	{
	  $pdf->Code39($druckerversatz+$spalte*$etikett_breite+($abstand_links+($abstand_etikett*$spalte)), $zeile*$etikett_hoehe+$abstand_oben, $barcode, $label);
	  //echo $spalte*$etikett_breite+($abstand_links+($abstand_etikett*$spalte))."<br>";
	}
      }
    }
    $pdf->Output($label.".pdf",'I');
  }

  function Druck()
  {
     // echo "jetzt druck ich";
  }
}

?>

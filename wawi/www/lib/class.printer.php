<?php


class Printer
{

  function Printer(&$app)
  {
    $this->app=$app;
  }


  function Drucken($drucker,$dokument,$parameter="",$anzahl="1")
  {
    $befehl = $this->app->DB->Select("SELECT befehl FROM drucker WHERE id='$drucker' 
        AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    $anbindung = $this->app->DB->Select("SELECT anbindung FROM drucker WHERE id='$drucker' 
        AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    if($anbindung=="") $anbindung=="cups";

    switch($anbindung)
    {
      case "cups":
        exec("$befehl $dokument");
        break;
      case "pdf":
        $this->app->erp->CreatePath($befehl);
        copy($dokument,$befehl."/".basename($dokument).".pdf");
        break;

      case "adapterbox":
        // wenn intern
        $deviceiddest = $this->app->DB->Select("SELECT adapterboxseriennummer FROM drucker WHERE id='".$drucker."' LIMIT 1");
        $ip = $this->app->DB->Select("SELECT adapterboxip FROM drucker WHERE id='".$drucker."' LIMIT 1");
        $art = $this->app->DB->Select("SELECT art FROM drucker WHERE id='".$drucker."' LIMIT 1");

        switch($art)
        {
          case 0: $art = "printer"; break;
          case 1: $art = "fax"; break;
          case 2: $art = "labelprinter"; break;
          default: $art = "unknown"; break;
        }

        $job = base64_encode(json_encode(array('label'=>base64_encode($dokument),'amount'=>$anzahl)));//."<amount>".$anzahl."</amount>");


        if($this->app->erp->Firmendaten("deviceenable")=="1")
        {
          if($deviceiddest!="")
            $this->app->DB->Insert("INSERT INTO device_jobs (id,zeitstempel,deviceidsource,deviceiddest,job,art) VALUES ('',NOW(),'000000000','$deviceiddest','$job','$art')");
        } else {
          $xml = $dokument;
          if($ip!="")
            HttpClient::quickPost("http://".$this->app->erp->GetIPAdapterbox($drucker)."/labelprinter.php",array('label'=>$xml,'amount'=>$anzahl));
        }
        break;
      case "email":
        $tomail = $this->app->DB->Select("SELECT tomail FROM drucker WHERE id='$drucker' 
            AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

        $tomailsubject = $this->app->DB->Select("SELECT tomailsubject FROM drucker WHERE id='$drucker' 
            AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

        $tomailsubject = str_replace('{FAX}',$parameter,$tomailsubject);

        $tomailtext = $this->app->DB->Select("SELECT tomailtext FROM drucker WHERE id='$drucker' 
            AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

        if($dokument!="")
        {
          $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaName(),
              $tomail,"",$tomailsubject,$tomailtext,array($dokument),"",true);
        }
        break;
    }
  }

}


?>

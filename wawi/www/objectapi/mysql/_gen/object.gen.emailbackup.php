<?php

class ObjGenEmailbackup
{

  private  $id;
  private  $benutzername;
  private  $passwort;
  private  $server;
  private  $smtp;
  private  $ticket;
  private  $autoresponder;
  private  $geschaeftsbriefvorlage;
  private  $autoresponderbetreff;
  private  $autorespondertext;
  private  $projekt;
  private  $emailbackup;
  private  $adresse;
  private  $firma;
  private  $loeschtage;
  private  $geloescht;
  private  $ticketqueue;
  private  $ticketprojekt;
  private  $email;

  public $app;            //application object 

  public function ObjGenEmailbackup($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM emailbackup WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->benutzername=$result[benutzername];
    $this->passwort=$result[passwort];
    $this->server=$result[server];
    $this->smtp=$result[smtp];
    $this->ticket=$result[ticket];
    $this->autoresponder=$result[autoresponder];
    $this->geschaeftsbriefvorlage=$result[geschaeftsbriefvorlage];
    $this->autoresponderbetreff=$result[autoresponderbetreff];
    $this->autorespondertext=$result[autorespondertext];
    $this->projekt=$result[projekt];
    $this->emailbackup=$result[emailbackup];
    $this->adresse=$result[adresse];
    $this->firma=$result[firma];
    $this->loeschtage=$result[loeschtage];
    $this->geloescht=$result[geloescht];
    $this->ticketqueue=$result[ticketqueue];
    $this->ticketprojekt=$result[ticketprojekt];
    $this->email=$result[email];
  }

  public function Create()
  {
    $sql = "INSERT INTO emailbackup (id,benutzername,passwort,server,smtp,ticket,autoresponder,geschaeftsbriefvorlage,autoresponderbetreff,autorespondertext,projekt,emailbackup,adresse,firma,loeschtage,geloescht,ticketqueue,ticketprojekt,email)
      VALUES('','{$this->benutzername}','{$this->passwort}','{$this->server}','{$this->smtp}','{$this->ticket}','{$this->autoresponder}','{$this->geschaeftsbriefvorlage}','{$this->autoresponderbetreff}','{$this->autorespondertext}','{$this->projekt}','{$this->emailbackup}','{$this->adresse}','{$this->firma}','{$this->loeschtage}','{$this->geloescht}','{$this->ticketqueue}','{$this->ticketprojekt}','{$this->email}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE emailbackup SET
      benutzername='{$this->benutzername}',
      passwort='{$this->passwort}',
      server='{$this->server}',
      smtp='{$this->smtp}',
      ticket='{$this->ticket}',
      autoresponder='{$this->autoresponder}',
      geschaeftsbriefvorlage='{$this->geschaeftsbriefvorlage}',
      autoresponderbetreff='{$this->autoresponderbetreff}',
      autorespondertext='{$this->autorespondertext}',
      projekt='{$this->projekt}',
      emailbackup='{$this->emailbackup}',
      adresse='{$this->adresse}',
      firma='{$this->firma}',
      loeschtage='{$this->loeschtage}',
      geloescht='{$this->geloescht}',
      ticketqueue='{$this->ticketqueue}',
      ticketprojekt='{$this->ticketprojekt}',
      email='{$this->email}'
      WHERE (id='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id="")
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM emailbackup WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->benutzername="";
    $this->passwort="";
    $this->server="";
    $this->smtp="";
    $this->ticket="";
    $this->autoresponder="";
    $this->geschaeftsbriefvorlage="";
    $this->autoresponderbetreff="";
    $this->autorespondertext="";
    $this->projekt="";
    $this->emailbackup="";
    $this->adresse="";
    $this->firma="";
    $this->loeschtage="";
    $this->geloescht="";
    $this->ticketqueue="";
    $this->ticketprojekt="";
    $this->email="";
  }

  public function Copy()
  {
    $this->id = "";
    $this->Create();
  }

 /** 
   Mit dieser Funktion kann man einen Datensatz suchen 
   dafuer muss man die Attribute setzen nach denen gesucht werden soll
   dann kriegt man als ergebnis den ersten Datensatz der auf die Suche uebereinstimmt
   zurueck. Mit Next() kann man sich alle weiteren Ergebnisse abholen
   **/ 

  public function Find()
  {
    //TODO Suche mit den werten machen
  }

  public function FindNext()
  {
    //TODO Suche mit den alten werten fortsetzen machen
  }

 /** Funktionen um durch die Tabelle iterieren zu koennen */ 

  public function Next()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

  public function First()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

 /** dank dieser funktionen kann man die tatsaechlichen werte einfach 
  ueberladen (in einem Objekt das mit seiner klasse ueber dieser steht)**/ 

  function SetId($value) { $this->id=$value; }
  function GetId() { return $this->id; }
  function SetBenutzername($value) { $this->benutzername=$value; }
  function GetBenutzername() { return $this->benutzername; }
  function SetPasswort($value) { $this->passwort=$value; }
  function GetPasswort() { return $this->passwort; }
  function SetServer($value) { $this->server=$value; }
  function GetServer() { return $this->server; }
  function SetSmtp($value) { $this->smtp=$value; }
  function GetSmtp() { return $this->smtp; }
  function SetTicket($value) { $this->ticket=$value; }
  function GetTicket() { return $this->ticket; }
  function SetAutoresponder($value) { $this->autoresponder=$value; }
  function GetAutoresponder() { return $this->autoresponder; }
  function SetGeschaeftsbriefvorlage($value) { $this->geschaeftsbriefvorlage=$value; }
  function GetGeschaeftsbriefvorlage() { return $this->geschaeftsbriefvorlage; }
  function SetAutoresponderbetreff($value) { $this->autoresponderbetreff=$value; }
  function GetAutoresponderbetreff() { return $this->autoresponderbetreff; }
  function SetAutorespondertext($value) { $this->autorespondertext=$value; }
  function GetAutorespondertext() { return $this->autorespondertext; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetEmailbackup($value) { $this->emailbackup=$value; }
  function GetEmailbackup() { return $this->emailbackup; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLoeschtage($value) { $this->loeschtage=$value; }
  function GetLoeschtage() { return $this->loeschtage; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetTicketqueue($value) { $this->ticketqueue=$value; }
  function GetTicketqueue() { return $this->ticketqueue; }
  function SetTicketprojekt($value) { $this->ticketprojekt=$value; }
  function GetTicketprojekt() { return $this->ticketprojekt; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }

}

?>
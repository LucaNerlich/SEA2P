<?php

class ObjGenUser
{

  private  $id;
  private  $username;
  private  $password;
  private  $repassword;
  private  $description;
  private  $settings;
  private  $parentuser;
  private  $activ;
  private  $type;
  private  $adresse;
  private  $fehllogins;
  private  $standarddrucker;
  private  $firma;
  private  $logdatei;
  private  $startseite;
  private  $hwtoken;
  private  $hwkey;
  private  $hwcounter;
  private  $motppin;
  private  $motpsecret;
  private  $externlogin;
  private  $hwdatablock;
  private  $passwordmd5;

  public $app;            //application object 

  public function ObjGenUser($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM user WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->username=$result[username];
    $this->password=$result[password];
    $this->repassword=$result[repassword];
    $this->description=$result[description];
    $this->settings=$result[settings];
    $this->parentuser=$result[parentuser];
    $this->activ=$result[activ];
    $this->type=$result[type];
    $this->adresse=$result[adresse];
    $this->fehllogins=$result[fehllogins];
    $this->standarddrucker=$result[standarddrucker];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->startseite=$result[startseite];
    $this->hwtoken=$result[hwtoken];
    $this->hwkey=$result[hwkey];
    $this->hwcounter=$result[hwcounter];
    $this->motppin=$result[motppin];
    $this->motpsecret=$result[motpsecret];
    $this->externlogin=$result[externlogin];
    $this->hwdatablock=$result[hwdatablock];
    $this->passwordmd5=$result[passwordmd5];
  }

  public function Create()
  {
    $sql = "INSERT INTO user (id,username,password,repassword,description,settings,parentuser,activ,type,adresse,fehllogins,standarddrucker,firma,logdatei,startseite,hwtoken,hwkey,hwcounter,motppin,motpsecret,externlogin,hwdatablock,passwordmd5)
      VALUES(DEFAULT, '{$this->username}', '{$this->password}', ".((is_numeric($this->repassword)) ? $this->repassword : '0').", '{$this->description}', '{$this->settings}', ".((is_numeric($this->parentuser)) ? $this->parentuser : '0').", ".((is_numeric($this->activ)) ? $this->activ : '0').", '{$this->type}', ".((is_numeric($this->adresse)) ? $this->adresse : '0').", ".((is_numeric($this->fehllogins)) ? $this->fehllogins : '0').", ".((is_numeric($this->standarddrucker)) ? $this->standarddrucker : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", '{$this->startseite}', ".((is_numeric($this->hwtoken)) ? $this->hwtoken : '0').", '{$this->hwkey}', ".((is_numeric($this->hwcounter)) ? $this->hwcounter : '0').", '{$this->motppin}', '{$this->motpsecret}', ".((is_numeric($this->externlogin)) ? $this->externlogin : '0').", '{$this->hwdatablock}', '{$this->passwordmd5}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE user SET
      username='{$this->username}',
      password='{$this->password}',
      repassword=".((is_numeric($this->repassword)) ? $this->repassword : '0').",
      description='{$this->description}',
      settings='{$this->settings}',
      parentuser=".((is_numeric($this->parentuser)) ? $this->parentuser : '0').",
      activ=".((is_numeric($this->activ)) ? $this->activ : '0').",
      type='{$this->type}',
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      fehllogins=".((is_numeric($this->fehllogins)) ? $this->fehllogins : '0').",
      standarddrucker=".((is_numeric($this->standarddrucker)) ? $this->standarddrucker : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      startseite='{$this->startseite}',
      hwtoken=".((is_numeric($this->hwtoken)) ? $this->hwtoken : '0').",
      hwkey='{$this->hwkey}',
      hwcounter=".((is_numeric($this->hwcounter)) ? $this->hwcounter : '0').",
      motppin='{$this->motppin}',
      motpsecret='{$this->motpsecret}',
      externlogin=".((is_numeric($this->externlogin)) ? $this->externlogin : '0').",
      hwdatablock='{$this->hwdatablock}',
      passwordmd5='{$this->passwordmd5}'
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

    $sql = "DELETE FROM user WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->username="";
    $this->password="";
    $this->repassword="";
    $this->description="";
    $this->settings="";
    $this->parentuser="";
    $this->activ="";
    $this->type="";
    $this->adresse="";
    $this->fehllogins="";
    $this->standarddrucker="";
    $this->firma="";
    $this->logdatei="";
    $this->startseite="";
    $this->hwtoken="";
    $this->hwkey="";
    $this->hwcounter="";
    $this->motppin="";
    $this->motpsecret="";
    $this->externlogin="";
    $this->hwdatablock="";
    $this->passwordmd5="";
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
  function SetUsername($value) { $this->username=$value; }
  function GetUsername() { return $this->username; }
  function SetPassword($value) { $this->password=$value; }
  function GetPassword() { return $this->password; }
  function SetRepassword($value) { $this->repassword=$value; }
  function GetRepassword() { return $this->repassword; }
  function SetDescription($value) { $this->description=$value; }
  function GetDescription() { return $this->description; }
  function SetSettings($value) { $this->settings=$value; }
  function GetSettings() { return $this->settings; }
  function SetParentuser($value) { $this->parentuser=$value; }
  function GetParentuser() { return $this->parentuser; }
  function SetActiv($value) { $this->activ=$value; }
  function GetActiv() { return $this->activ; }
  function SetType($value) { $this->type=$value; }
  function GetType() { return $this->type; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetFehllogins($value) { $this->fehllogins=$value; }
  function GetFehllogins() { return $this->fehllogins; }
  function SetStandarddrucker($value) { $this->standarddrucker=$value; }
  function GetStandarddrucker() { return $this->standarddrucker; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetStartseite($value) { $this->startseite=$value; }
  function GetStartseite() { return $this->startseite; }
  function SetHwtoken($value) { $this->hwtoken=$value; }
  function GetHwtoken() { return $this->hwtoken; }
  function SetHwkey($value) { $this->hwkey=$value; }
  function GetHwkey() { return $this->hwkey; }
  function SetHwcounter($value) { $this->hwcounter=$value; }
  function GetHwcounter() { return $this->hwcounter; }
  function SetMotppin($value) { $this->motppin=$value; }
  function GetMotppin() { return $this->motppin; }
  function SetMotpsecret($value) { $this->motpsecret=$value; }
  function GetMotpsecret() { return $this->motpsecret; }
  function SetExternlogin($value) { $this->externlogin=$value; }
  function GetExternlogin() { return $this->externlogin; }
  function SetHwdatablock($value) { $this->hwdatablock=$value; }
  function GetHwdatablock() { return $this->hwdatablock; }
  function SetPasswordmd5($value) { $this->passwordmd5=$value; }
  function GetPasswordmd5() { return $this->passwordmd5; }

}

?>
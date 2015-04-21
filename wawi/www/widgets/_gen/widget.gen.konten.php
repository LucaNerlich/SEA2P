<?php 

class WidgetGenkonten
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenkonten($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function kontenDelete()
  {
    
    $this->form->Execute("konten","delete");

    $this->kontenList();
  }

  function Edit()
  {
    $this->form->Edit();
  }

  function Copy()
  {
    $this->form->Copy();
  }

  public function Create()
  {
    $this->form->Create();
  }

  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"SUUUCHEEE");
  }

  public function Summary()
  {
    $this->app->Tpl->Set($this->parsetarget,"grosse Tabelle");
  }

  function Form()
  {
    $this->form = $this->app->FormHandler->CreateNew("konten");
    $this->form->UseTable("konten");
    $this->form->UseTemplate("konten.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!",MSGBEZEICHNUNG);

    $field = new HTMLSelect("type",0,"type");
    $field->AddOption('Konto (CSV-Import)','konto');
    $field->AddOption('Kasse','kasse');
    $field->AddOption('Sonstige Verbindlichkeiten Firma','verrechnungskontofirma');
    $field->AddOption('Verrechnungskonto','verrechnungskonto');
    $field->AddOption('Konto: Deutsche Bank','deutschebank');
    $field->AddOption('Konto: Postbank','postbank');
    $field->AddOption('Konto: VR-Net (Bayern)','vrbank');
    $field->AddOption('Konto: Paypal','paypal');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("aktiv","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keineemail","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibbar","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("inhaber","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("swift","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("iban","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("blz","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("konto","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("glaeubiger","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lastschrift","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("hbci","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("hbcikennung","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("datevkonto","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("erstezeile",5,100);   
    $this->form->NewField($field);

    $field = new HTMLInput("importerstezeilenummer","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldwaehrungformat","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("codierung",0,"codierung");
    $field->AddOption('Keine','');
    $field->AddOption('UTF8 Encode','utf8_encode');
    $this->form->NewField($field);

    $field = new HTMLSelect("importtrennzeichen",0,"importtrennzeichen");
    $field->AddOption(';','semikolon');
    $field->AddOption(',','komma');
    $this->form->NewField($field);

    $field = new HTMLSelect("importdatenmaskierung",0,"importdatenmaskierung");
    $field->AddOption('keine','keine');
    $field->AddOption('&quot;','gaensefuesschen');
    $this->form->NewField($field);

    $field = new HTMLInput("importletztenzeilenignorieren","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfelddatum","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfelddatumformat","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfelddatumformatausgabe","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldbetrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("importextrahabensoll","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldhaben","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldsoll","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldbuchungstext","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldwaehrung","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldhabensollkennung","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldkennunghaben","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("importfeldkennungsoll","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("liveimport_online","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("liveimport",5,50);   
    $this->form->NewField($field);


  }

}

?>
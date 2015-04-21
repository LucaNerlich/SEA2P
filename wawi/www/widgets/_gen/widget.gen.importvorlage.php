<?php 

class WidgetGenimportvorlage
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenimportvorlage($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function importvorlageDelete()
  {
    
    $this->form->Execute("importvorlage","delete");

    $this->importvorlageList();
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
    $this->form = $this->app->FormHandler->CreateNew("importvorlage");
    $this->form->UseTable("importvorlage");
    $this->form->UseTemplate("importvorlage.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","50","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!",MSGBEZEICHNUNG);

    $field = new HTMLSelect("ziel",0,"ziel");
    $field->AddOption('Adresse&nbsp;(min. Angabe: name)','adresse');
    $field->AddOption('Einkauf&nbsp;(min. Angabe: lieferantennummer, herstellernummer oder nummer, ekpreis, ab_menge)','einkauf');
    $field->AddOption('Artikel&nbsp;(min. Angabe: nummer oder name_de)','artikel');
    $field->AddOption('Zeiterfassung&nbsp;(min. Angabe datum_von,zeit_von,datum_bis,zeit_bis,kennung,taetigkeit)','zeiterfassung');
    $this->form->NewField($field);

    $field = new HTMLInput("importerstezeilenummer","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("importtrennzeichen",0,"importtrennzeichen");
    $field->AddOption(';','semikolon');
    $field->AddOption(',','komma');
    $this->form->NewField($field);

    $field = new HTMLSelect("importdatenmaskierung",0,"importdatenmaskierung");
    $field->AddOption('keine','keine');
    $field->AddOption('&quot;','gaensefuesschen');
    $this->form->NewField($field);

    $field = new HTMLTextarea("fields",15,60);   
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",5,50);   
    $this->form->NewField($field);


  }

}

?>
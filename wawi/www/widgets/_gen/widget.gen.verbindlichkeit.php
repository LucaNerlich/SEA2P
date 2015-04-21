<?php 

class WidgetGenverbindlichkeit
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenverbindlichkeit($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function verbindlichkeitDelete()
  {
    
    $this->form->Execute("verbindlichkeit","delete");

    $this->verbindlichkeitList();
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
    $this->form = $this->app->FormHandler->CreateNew("verbindlichkeit");
    $this->form->UseTable("verbindlichkeit");
    $this->form->UseTemplate("verbindlichkeit.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","30","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("adresse","notempty","Pflichtfeld!",MSGADRESSE);

    $field = new HTMLInput("rechnung","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnungsdatum","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlbarbis","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("betrag","text","","20","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("betrag","notempty","Pflichtfeld!",MSGBETRAG);

    $field = new HTMLSelect("waehrung",0,"waehrung");
    $field->AddOption('EUR','EUR');
    $this->form->NewField($field);

    $field = new HTMLInput("skonto","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("summenormal","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("skontobis","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("summeermaessigt","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("umsatzsteuer",0,"umsatzsteuer");
    $field->AddOption('Deutschland','deutschland');
    $field->AddOption('EU-Lieferung','eulieferung');
    $field->AddOption('Import','export');
    $this->form->NewField($field);

    $field = new HTMLInput("verwendungszweck","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("frachtkosten","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kostenstelle","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("freigabe","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rechnungsfreigabe","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("sachkonto","text","","20","","","","","","","0");
    $this->form->NewField($field);



    $field = new HTMLInput("bestellung1","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung1betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung1bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung2","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung2betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung2bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung3","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung3betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung3bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung4","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung4betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung4bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung5","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung5betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung5bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung6","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung6betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung6bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung7","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung7betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung7bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung8","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung8betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung8bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung9","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung9betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung9bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung10","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung10betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung10bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung11","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung11betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung11bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung12","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung12betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung12bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung13","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung13betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung13bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung14","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung14betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung14bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung15","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung15betrag","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung15bemerkung","text","","60","","","","","","","0");
    $this->form->NewField($field);



  }

}

?>
<?php
include ("_gen/widget.gen.produktion_position.php");

class WidgetProduktion_position extends WidgetGenProduktion_position 
{
  private $app;
  function WidgetProduktion_position($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenProduktion_position($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {


  }
}
?>

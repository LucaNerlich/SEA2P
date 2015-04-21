<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); 
	ini_set('display_errors', 1);
	$config_file = 'setup.conf.php';
	$output_folder = '../../conf/';

	if(!is_file($config_file) ) { echo 'Config-File is missing'; return; }
	include_once($config_file);

	#########################################################################
	$max_steps = count(array_filter($setup))-1;
	$step = (($_GET['step']!='') ? $_GET['step'] : 1);
	$submit = $_POST['_SUBMIT'];


	if($step>$max_steps){
		GenerateConfigFiles($output_folder);
		if($config['postinstall']) PostInstall();
		$page = HtmlTemplate("<center><h1>Setup erfolgreich beendet</h1><h2>L&ouml;schen Sie unbedingt den Setup-Ordner</h2></center>");
	}else{	
		$page = GenerateHtml($step, $setup);
		$page = str_replace('[BUTTON]', (($step<=$max_steps)?"<input type=\"submit\" name=\"_SUBMIT\" value=\"Ok\">":""), $page);
	}

	if($submit!='') {
		$configfile = $_POST['_CONFIGFILE'];
		$action = $_POST['_ACTION'];
		unset($_POST['_CONFIGFILE']);
		unset($_POST['_ACTION']);
		unset($_POST['_SUBMIT']);

		$error = ((function_exists($action)) ? $action() : '');
		if($configfile=='')  $error .= "<br>'configfile' for this step is missing";

		if($error=='') {
			// Convert Fields to Session
			foreach($_POST as $key=>$value) 
				$_SESSION['setup'][$configfile][$key] = $value;

			// execute Sql-Files
			$sql_prefix = "sql_";
			foreach($_POST as $key=>$value) {
				if(strlen($key)>strlen($sql_prefix) && substr($key,0,strlen($sql_prefix))==$sql_prefix && 
					$_SESSION['setup'][$configfile][substr($key,strlen($sql_prefix), strlen($key)-strlen($sql_prefix))]!=''){
					unset($_SESSION['setup'][$configfile][$key]);
					if(is_file($value)){
						 	$import = file_get_contents($value);

   						$import = preg_replace ("%/\*(.*)\*/%Us", '', $import);
   						$import = preg_replace ("%^--(.*)\n%mU", '', $import);
   						$import = preg_replace ("%^$\n%mU", '', $import);

							$db= mysqli_connect($_SESSION['setup'][$configfile]['WFdbhost'],$_SESSION['setup'][$configfile]['WFdbuser'],$_SESSION['setup'][$configfile]['WFdbpass']);
							mysqli_select_db($db,$_SESSION['setup'][$configfile]['WFdbname']);
                                                        mysqli_set_charset($db,"utf8");
                                                        mysqli_query($db,"SET SESSION SQL_MODE :=''");
   						//mysql_real_escape_string($import); 
   						$import = explode (";", $import); 

   						foreach ($import as $imp){
    						if ($imp != '' && $imp != ' '){
     							mysqli_query($db,$imp);
    						}
   						}  
							mysqli_close($db);
/*
						if(exec("mysql --user='{$_SESSION['setup'][$configfile]['WFdbuser']}' --password='{$_SESSION['setup'][$configfile]['WFdbpass']}' --host='{$_SESSION['setup'][$configfile]['WFdbhost']}' --database='{$_SESSION['setup'][$configfile]['WFdbname']}' < '$value'", $sql_out, $sql_status)==2)
							$error = "Konnte '$value' nicht ausf&uuml;hren";
						}else
							$error .= "Konnte '$value' nicht finden";
*/
					}
				}
			}

			// remove Readonly-Fields
			$ro_prefix = "ro_";
			foreach($_POST as $key=>$value) {
				if(strlen($key)>strlen($ro_prefix) && substr($key,0,strlen($ro_prefix))==$ro_prefix){
					unset($_SESSION['setup'][$configfile][substr($key,strlen($ro_prefix), strlen($key)-strlen($ro_prefix))]);
					unset($_SESSION['setup'][$configfile][$key]);
				}
			}

			if($error=='') {
				header('Location: ./setup.php?step='.++$step);
				exit;
			}else
				$page = str_replace('[MESSAGE]', "<div class=\"inputerror\">$error</div>", $page);
		}else
			$page = str_replace('[MESSAGE]', "<div class=\"inputerror\">$error</div>", $page);
	}

	$page = str_replace('[MESSAGE]','', $page);
	echo $page;


	function GenerateConfigFiles($output_folder)
	{
		foreach($_SESSION['setup'] as $file=>$vars) {
			$out = "<?php\n";
			foreach($vars as $key=>$value)
				if($value=='true' || $value=='false') 
					$out .= '$this->'.$key.' = '.$value.';'."\n";
				else
					$out .= '$this->'.$key.' = "'.$value.'";'."\n";
			$out .= "?>";
			file_put_contents($output_folder.$file, $out);
		}
		
	}	

	function GenerateHtml($step, $setup)
	{
		if(!array_key_exists($step, $setup)) { return "<h1>Page doesnt exist</h1>"; }

		$html = "";
		if(array_key_exists('description',$setup[$step])) $html .= "<h1>{$setup[$step]['description']}</h1><hr>";
		if(array_key_exists('configfile',$setup[$step])) $html .= "<input type=\"hidden\" name=\"_CONFIGFILE\" value=\"{$setup[$step]['configfile']}\">";
		if(array_key_exists('action',$setup[$step])) $html .= "<input type=\"hidden\" name=\"_ACTION\" value=\"{$setup[$step]['action']}\">";
		
		$fields = '';
		foreach($setup[$step]['fields'] as $key=>$value)
		{
			$name = $key;
			$text = ((array_key_exists('text',$value)) ? $value['text'] : $value);
			$type = ((array_key_exists('type',$value)) ? $value['type'] : "text");
			$note = ((array_key_exists('note',$value)) ? $value['note'] : "");
			$default = ((array_key_exists('default',$value)) ? $value['default'] : "");
			$options = ((array_key_exists('options',$value)) ? $value['options'] : array());
			$fvalue = ((array_key_exists('value',$value)) ? $value['value'] : "");
			$readonly = ((array_key_exists('readonly',$value)) ? $value['readonly'] : "");
			$sql = ((array_key_exists('sql',$value)) ? $value['sql'] : "");
			$invisible = ((array_key_exists('invisible',$value)) ? $value['invisible'] : "");

			if($readonly!="") $ro = "<input type=\"hidden\" name=\"ro_$name\" value=\"$name\">";
			if($sql!="") $mysql = "<input type=\"hidden\" name=\"sql_$name\" value=\"$sql\">";

			if($invisible=="")
			{
				if($type=='text')
					$input = "<input type=\"text\" name=\"$name\" value=\"$default\">";

				if($type=='checkbox')
			  	$input = "<input type=\"checkbox\" name=\"$name\" value=\"$fvalue\">";

				if($type=='select') {
					$opt_out = '';
					foreach($options as $opt_value=>$opt_text){
						$selected = (($default!="" && $default==$opt_value) ? 'selected' : '');
						$opt_out .= "<option value=\"$opt_value\" $selected>$opt_text</option>";
					}
					$input = "<select name=\"$name\">$opt_out</select>";
				}
				$field = "<div class=\"row\"><div>$text</div><div>{$input}{$ro}{$mysql}</div><div>$note</div></div>\n";
			}else
				$field = "<input type=\"hidden\" name=\"$name\" value=\"1\">{$ro}{$mysql}";

			$fields .= $field;
		}
		$html .= "\n[MESSAGE]\n$fields\n[BUTTON]";
	
		$page = HtmlTemplate($html);

		return $page;
	}

	function HtmlTemplate($html)
	{
		return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
		        <html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"setup.css\" />
						<title>Shop-Installer</title>
						</head><body><div id=\"content\"><form action=\"\" method=\"POST\">
						 {$html}
						</form><div id=\"footer\" style=\"font-size:8pt\">Einfach neuste <a href=\"http://shop.wawision.de/sonstige/1-jahr-zugang-updateserver-open-source-version.html?c=164\" target=\"_blank\">Updates (f&uuml;r Open-Source Version)</a> per Klick erhalten.&nbsp;<a href=\"http://www.wawision.de\">embedded projects GmbH &copy; 2009-2015</a></div></div></body></html>";
	}
?>

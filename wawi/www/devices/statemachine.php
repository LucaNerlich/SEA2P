<?php
header_remove(); 
header ("Content-Type:text/xml");

function RunStateMachine($DB,$deviceid)
{
  $deviceid_destination = $_GET['device'];
  $cmd = $_GET['cmd'];

	echo "<xml>";

	switch($cmd)
	{
		case "addJob":
			echo "<cmd>$cmd</cmd>";
			//echo "add job for $deviceid_destination";
			$job = $_POST['job'];

			if($deviceid!="" && $deviceid_destination!="" && $job!="")
			{
				$job = base64_encode($job);
				$DB->Insert("INSERT INTO device_jobs (id,deviceidsource,deviceiddest,job,zeitstempel) VALUES ('','$deviceid','$deviceid_destination','$job',NOW())");
				echo "<result>1</result>";
			} else {
				echo "<result>0</result>";
			}
		break;

		case "getJob":
			echo "<cmd>$cmd</cmd>";
			$tmp = $DB->SelectArr("SELECT id,job,art FROM device_jobs WHERE deviceiddest='$deviceid' AND abgeschlossen!='1' ORDER by zeitstempel");
			$DB->Update("UPDATE device_jobs SET abgeschlossen='1' WHERE id='".$tmp[0]['id']."' LIMIT 1");
			if($tmp[0]['id'] > 0)
			{
				echo "<job>".$tmp[0]['job']."</job>";
				echo "<device>".$tmp[0]['art']."</device>";
				echo "<result>1</result>";
			}
			else
				echo "<result>0</result>";
			$DB->Delete("DELETE FROM device_jobs WHERE abgeschlossen='1'");
		break;

		case "logOut":
			echo "<cmd>$cmd</cmd>";

		break;

		case "state":
			echo "<cmd>$cmd</cmd>";
			if($deviceid_destination!="")
				$tmp = $DB->Select("SELECT COUNT(id) FROM device_jobs WHERE deviceiddest='$deviceid_destination' AND abgeschlossen!='1'");
			else
				$tmp = $DB->Select("SELECT COUNT(id) FROM device_jobs WHERE deviceiddest='$deviceid' AND abgeschlossen!='1'");
			echo "<numberofjobs>$tmp</numberofjobs>";
			echo "<deviceid>$deviceid</deviceid>";
  		//echo "<pre>Deviceid: $deviceid L1 $L1 L2 $L2 L3 $L3</pre>";
		break;
		default:
			echo "<cmd>unkown</cmd>";
  		echo "<pre>DEVICE ID: $deviceid L1 $L1 L2 $L2 L3 $L3</pre>";
	}

	echo "</xml>";
}


?>

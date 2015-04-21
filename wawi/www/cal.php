<?
$event = new ICS("wawision");
$event->AddEvent(1,"2014-05-18 11:00","2014-05-18 21:00","Test 444 Event","This is an event made by Benedikt","Augsburg");
$event->AddEvent(2,"2014-05-18 09:00","2014-05-18 09:30","Test 3 Event","This is an event made by Benedikt","Augsburg");
$event->show();

class ICS {
    var $data;
    var $name;

		function ICS($name)
		{
			$this->name = $name;
		}

    function AddEvent($id,$start,$end,$name,$description,$location) {
        $this->data .= "BEGIN:VEVENT\nDTSTART:".date("Ymd\THis\Z",strtotime($start))."\nDTEND:".date("Ymd\THis\Z",strtotime($end))."\nLOCATION:".$location."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:$id\nDTSTAMP:".date("Ymd\THis\Z")."\nSUMMARY:".$name."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\n";
    }

    function show() {
				$result = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\n".$this->data."END:VCALENDAR";
        header("Content-type:text/calendar");
				if ($_SERVER['PHP_AUTH_USER']!="bene" && $_SERVER['PHP_AUTH_PW']!="passwort") {
		   		header('WWW-Authenticate: Basic realm="My Realm"');
    			header('HTTP/1.0 401 Unauthorized');
				}

        header('Content-Disposition: attachment; filename="'.$this->name.'.ics"');
        Header('Content-Length: '.strlen($result));
        Header('Connection: close');
        echo $result;
    }
}

?>

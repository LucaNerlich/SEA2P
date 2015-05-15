<?php
include '../../config/config.php';
//query("INSERT INTO bike VALUES (NULL,1,NOW(),'TEST','TEST')");

// Monatserster
if (date("j") == 1)
{
	$message = null;
	switch (date("n"))
	{
		case 4:
			$message = "Lieber Kunde,\nes gruent, es wird waermer, die Gruende auf das Fahrrad zu verzichten schwinden. Lassen Sie Ihr Fahrrad durch unsere Profis inspizieren und fit fuer den Sommer machen! Kommen Sie vorbei!";
			break;
		case 10:
			$message = "Lieber Kunde\ndie Tage werden kuerzer und es wird frueher dunkel. Wir checken Ihr Rad, ob es Sie sicher durch die dunkle Jahreszeit bringt. Kommen Sie vorbei!";
			break;
	}
	if ($message != null)
	{
		$kunden = query("SELECT client_id FROM client");
		foreach($kunden as $kunde)
		{
			$topic_id = query("INSERT INTO topics (created_on, active, subject, client_id, type) VALUES (NOW(), null, 'Inspektionstermin'," . $kunde["client_id"] . ",2)");
			query("INSERT INTO message (created_on, client_sent, text, topic_id) VALUES (NOW(),0,'$message',$topic_id)");
		}
	}
}
?>
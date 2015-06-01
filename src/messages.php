<?php
include '../config/config.php';
include '../config/header.php';

if ($_SESSION["state"] == 1)
{
	$message_id = null;
	if (isset($_GET["topic_id"]))
	{
		$message_id = intval($_GET["topic_id"]);
	}
		
	
	if ($message_id != null)
	{
		// Antwort des Kunden eintragen
		if (isset($_POST["response"]))
		{
			$text = trim($_POST["response"]);
			if (isset($_POST["checked"]) && $_POST["checked"] == 1)
			{
				query("UPDATE topics SET active = 0 WHERE topic_id = $message_id");
			}
			if (strlen($text) > 0)
			{
				query("INSERT INTO message (client_sent, text, topic_id) VALUES (" . $_SESSION["user"]["client_id"] . ",'" . escape($text) . "',$message_id)");
				echo message("Die Nachricht wurde erfolgreich verschickt!",1);
			}
			
		}

		$topic = query("SELECT * FROM topics WHERE topic_id = $message_id");
		if (sizeof($topic) > 0)
		{
			$topic = $topic[0];
			
			
			
			
			
			
			$messages = query("SELECT * FROM message WHERE topic_id = " . $message_id);
		}
		else
		{
			$topic = null;
		}
	}
	
	echo '<div class="jumbotron">';
	if ($message_id != null && $topic != null)
	{
		echo "<h1>" . $topic["subject"] . "</h1></div>";
		echo "<div class='panel panel-default'>";
		echo "<table class='table table-striped'>";
		echo "<tr><th>Nachricht</th><th>Datum</th><th>Wer</th></tr>";
		
		foreach ($messages as $message)
		{
// 			echo "<div class='panel-heading'><h2>" . ($messsage["client_sent"]==0?"Wir":"Du") . " am " . $message["created_on"] . "</h2></div>";
// 			echo "<div class='panel-body'>" . $message["text"] . "</div>";

			echo "<tr><td>" . $message["text"] . "</td><td>" .  $message["created_on"] . "</td><td>" . ($message["client_sent"]==0?"Wir":"Du") . "</td></tr>";
		}
		
		echo "<tr><td colspan='3'>";
		
		if ($topic["active"] == 0)
		{
			echo "<i>Dieses Thema wurde als erledigt makiert!</i>";
		}
		else
		{
			echo "<form action='messages.php?topic_id=$message_id' method='POST'>";
			echo "<textarea name='response' placeholder='Deine Antwort'></textarea>";
			echo "<br><input type='checkbox' name='checked' value='1' id='checked'> <label for='checked'>Als erledigt makieren</label>";
			echo "<br><input type='submit' class='submit' value='Antworten'>";
			echo "</form>";
		}
		echo "</td></tr>";
		echo "</table>";
		echo "</div>";
	}
	else
	{
		echo "<h1>Deine Nachrichten</h1></div>";
		$topics = query("SELECT * FROM topics WHERE client_id = " . $_SESSION["user"]["client_id"] . " ORDER BY coalesce(active,0), created_on DESC");
		echo "<div class='panel panel-default'>";
		echo "<table class='table table-striped'>";
		echo "<tr><th>Thema</th><th>Datum</th><th>Typ</th><th>Status</th></tr>";
		
		foreach ($topics as $topic)
		{
			//echo "<div class='panel-heading'><h2>" . $topic["created_on"] . ", Typ: " . ($topic["type"]==1?"Schadenmeldung":"Kontaktaufnahme") . "</h2></div>";
			//echo "<div class='panel-body'><a href='messages.php?topic_id=" . $topic["topic_id"] . "'>" . $topic["subject"] . "</a></div>";
			echo "<tr>
					<td><a href='messages.php?topic_id=" . $topic["topic_id"] . "'>" . $topic["subject"] . "</a></td>
					<td>" . $topic["created_on"] . "</td>
					<td>" . ($topic["type"]==1?"Schadenmeldung":"Kontaktaufnahme") . "</td>
					<td>" . (($topic["active"]==null||$topic["active"] == 0) ? "<img src='../graphic/y.png'>" : "") . "</td>
					</tr>";
		
		}
		echo "</table>";
		echo "</div>";
	}

}
include '../config/footer.php';
?>
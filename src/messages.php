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
		$topic = query("SELECT * FROM topics WHERE topic_id = $message_id");
		if (sizeof($topic) > 0)
		{
			$messags = query("SELECT * FROM message WHERE topic_id = " . $topic["topic_id"]);
		}
		else
		{
			$topic = null;
		}
	}
	
	echo '<div class="jumbotron">';
	if ($topic != null)
	{
		echo "<h1>" . $topic["subject"] . "</h1></div>";
		echo "<div class='panel panel-default'>";
		
		foreach ($messages as $message)
		{
			echo "<div class='panel-heading'><h2>" . ($messsage["client_sent"]==1?"Du":"Wir") . " am " . $message["created_on"] . "</h2></div>";
			echo "<div class='panel-body'>" . $message["text"] . "</div>";
		
		}
		
		echo "</div>";
	}
	else
	{
		echo "<h1>Deine Nachrichten</h1>";
	}

}
include '../config/footer.php';
?>
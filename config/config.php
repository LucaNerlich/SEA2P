<?php
include "database.php";
session_start();
if (!isset($_SESSION["state"])) {
	$_SESSION["state"] = 0; // 0 = Not logged in, 1 = Logged In as Customer, 2 = Logged In as Admin
}

function message($str, $type = 0)
{
	switch ($type)
	{
		case 1:
			$class = "green";
			break;
		case 2:
			$class = "yellow";
			break;
		case 3:
			$class = "red";
			break;
		default:
			$class = "grey";
	}
	
	return "<p class='$class'>$str</p>";
}

function addMessage($str)
{
	if (!isset($_SESSION["messages"]))
	{
		$_SESSION["messages"] = array();
	}
	array_push($_SESSION["messages"],$str);
}

?>
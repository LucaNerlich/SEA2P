<?php
include "database.php";
session_start();
if (!isset($_SESSION["state"])) {
	$_SESSION["state"] = 0; // 0 = Not logged in, 1 = Logged In as Customer, 2 = Logged In as Admin
}

function message($str, $type = 0, $big = false)
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
	
	return "<" . ($big?"p":"span") . " class='$class'>$str</" . ($big?"p":"span") . ">";
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
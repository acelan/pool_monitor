<?php
$action = $_GET['action'];
$file = "data/".$_GET['id'];

if(($action == "save") && ($file != ""))
	file_put_contents($file, json_encode($_POST['data']));

if(($action == "load") && ($file != ""))
{
	$data = file_get_contents($file);
	echo $data;
}
?>

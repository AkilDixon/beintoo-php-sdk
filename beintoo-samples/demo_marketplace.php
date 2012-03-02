<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>

</head>
<body>

<?php 
	//import the beintoo_client.php file
	require_once('../beintoo-php-sdk/beintoo_client.php');
	
	//set your apiKey and the current player's guid
	$apikey = "your_apiKey";
	$guid = "player's current guid";
	
	//init the Beintoo Rest Client object, providing the apyKey
	$client = new BeintooRestClient($apikey);
	
	//call the Marketplace resource, with following parameters:
	// [string] guid       <---- current player's guid
	// [float]  latitute   <---- (optional, can be null)
	// [float]  logitude   <---- (optional, can be null)
	// [bool]   needs html <---- provide true to receive a new tagged html page with marketplace, 
	// 					         else retrive false to obtain only Marketplace object
	// [string] position   <---- choose where to show Marketplace: on left or right screen side.
	$client->render_marketplace($guid, null, null, false, "right");
?>

</body>
</html>
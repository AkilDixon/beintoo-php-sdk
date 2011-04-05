<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//session_start();
require_once 'variables.php';

require '../beintoo-php-sdk/beintoo_client.php';

    $client = new BeintooRestClient($apikey,null);

    $response=$client->player_login(null,$_GET['userext']);

    if (isset($response->user))
    $_SESSION['userExt']=$response->user;

?>

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>phpSDK DEMO </title>

  </head>

  <body  >


    hi user! you are already logged.

  </body>
</html>
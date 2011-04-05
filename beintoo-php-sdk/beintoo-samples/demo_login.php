<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'variables.php';

    require '../beintoo-php-sdk/beintoo_client.php';
    // this is the id on the app side
    $current_user_id='88531467-fbf1-4da8-aa22-a3cd5bb9a4a7:3543304037647835';
    $guid=$current_user_id;
 if (isset($_GET['apikey']))
         $apikey=$_GET['apikey'];

    $client = new BeintooRestClient($apikey);

 
    if (isset($_GET['action']))
     $action=$_GET['action'];



  
    if (strcmp($action,"player_login")==0) {
        $guid=$_GET['guid'];
        $userExt=$_GET['userExt'];
        $response=$client->player_login($_GET['guid'],$_GET['userExt']);
        if (isset($response->user)) {
            $_SESSION['userExt']=$response->user;
        } else {
            unset( $_SESSION['userExt']);
        }
    }



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>phpSDK DEMO </title>
  </head>
    <body>
    <H1> PHP DEMO</H1>

      <?php
      echo " <hr/>";
     if (strcmp($action,"player_getplayer_byguid")==0) {
        $response=$client->player_getplayer_byguid($_GET['guid']);

    }

        if (!isset( $_SESSION['userExt'])) {
        // if response of player_getplayer_byguid contains $response->user it is connected

        echo " Hi player (".$guid.") you are not logged in beintoo.";
        echo "<a href='".$client->getConnectUrl( $_SESSION['guid'],$redirect_uri,NULL,NULL,$logged_uri)."' >CONNECT</a>";
        } else {
            echo "Hi user, you are connected with Beintoo with :<br/>";
            echo "<table><tr>";
            echo "<td>"."<img src='".$_SESSION['userExt']->usersmallimg."' /><br/>"."</td>";
            echo "<td><table><tr><td>user_id: ".$_SESSION['userExt']->id."</td></tr><tr><td>name: ".$_SESSION['userExt']->name."</td></tr><tr><td>bedollars: ".$_SESSION['userExt']->bedollars."</td></tr></table></td>";
            echo "</tr></table>";
           echo "<br/>";
        }
 

        echo "<hr/>";
        if (isset($response)) {
            echo "RESPONSE TO $action :<br/>";
            echo "<pre>";
            var_export($response);
            echo "</pre>";

            }
             echo " <hr/>";
      ?>
try using : guid=demoplayer , apikey=0987654321 <br/>
try using : guid=88531467-fbf1-4da8-aa22-a3cd5bb9a4a7:3543304037647835 , apikey=0987654321 <br/>
try using : guid= a random string , apikey=0987654321 <br/>


    <form name="input" action="demo_login.php" method="get">
        <br /><br />

        Player: <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
        User: <input type="text" name="userExt" value="<?php if (isset($_SESSION['userExt'])) echo  $_SESSION['userExt']->id; ?>" /><br /><br />

        <input type="submit" name="action" value="player_login"  /><br /><br />
    </form>
  </body>
</html>

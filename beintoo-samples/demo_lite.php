<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

	require_once 'variables.php';
    require '../beintoo-php-sdk/beintoo_client.php';
    
    //$guid='88531467-fbf1-4da8-aa22-a3cd5bb9a4a7:3543304037647835';

    if (strcmp($_GET['action'],"logout")==0) {
    	unset($_SESSION['userExt']);
    	unset($guid);
    }
	
    if (isset($_GET['apikey']))
         $apikey=$_GET['apikey'];

    $sandbox = false;
	if (isset($_GET['sandbox']))
    	$sandbox = true;
 	//var_dump($_SERVER);
	$client = new BeintooRestClient($apikey, $sandbox);
	
	
	//PLAYER LOGIN
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "player_login") == 0){
	 		$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
			$userExt = NULL;
		    if (isset($_GET['userExt']))
		         $guid=$_GET['guid'];
	 		$player = $client->player_login($guid, $userExt);
			echo "<pre>";
            var_export($player);
            echo "</pre>";
			return ;
		}
	 
	 //PLAYER BY GUID
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "player_by_guid") == 0){
	 		$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
			try {
					$player = $client->player_getplayer_byguid($guid);
			    } catch (Exception $e ) {
			        print_r("maybe guid is not yet created in beintoo...");
			}			
			echo "<pre>";
            var_export($player);
            echo "</pre>";
			return ;
		}
	 
	 //SET USER
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "user_setuser") == 0){
	 		$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
			try {
					$response=$client->user_setuser($_GET['guid'], $_GET['email'],
                $_GET['address'], $_GET['country'], $_GET['gender'], $_GET['nickname'], $_GET['name'],
                 $_GET['password'], $_GET['sendGreetingsEmail'], $_GET['imageURL'],$_GET['language']
                );
			    } catch (Exception $e ) {
			        print_r("maybe guid is not yet created in beintoo...");
			}			
			echo "<pre>";
            var_export($response);
            echo "</pre>";
			return ;
		}
	 
	  //SUBMIT SCORE
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "player_submitscore") == 0){
	 		$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
		    if (isset($_GET['codeID']))
		         $codeID=$_GET['codeID'];
			try {
				$score = $client->player_submitscore($_GET['codeID'],$guid,
	               $_GET['lastScore'],$_GET['balance'],
	                  $_GET['latitude'],$_GET['longitude'],$_GET['radius']);
			    } catch (Exception $e ) {
			        print_r("Maybe guid is not yet created in beintoo or codeID is not valid");
			}			
			echo "<pre>";
            var_export($score);
            echo "</pre>";
			return ;
		} 
	 
	 
	 //	VGOOD BY GUID
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "get_vgood_by_guid") == 0){
	 		$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
		    if (isset($_GET['codeID']))
		         $codeID=$_GET['codeID'];
			if (isset($_GET['ip'])){
		         $addr=$_GET['ip'];
				}else {
					$addr=$_REQUEST['REMOTE_ADDR'];
				}
			try {
					$response=$client->vgood_getvgood_byguid($_GET['codeID'],$guid,
	                  $_GET['latitude'],$_GET['longitude'],$_GET['radius'],$addr);
				
			    } catch (Exception $e ) {
			        print_r("Maybe guid is not yet created in beintoo ");
			}			
			echo "<pre>";
            var_export($response);
            echo "</pre>";
			return ;
		}   
		
		
		//	VGOOD BY USER
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "get_vgood_by_user") == 0){
	 		$userExt = NULL;
		    if (isset($_GET['userExt']))
		         $userExt=$_GET['userExt'];
		    if (isset($_GET['codeID']))
		         $codeID=$_GET['codeID'];
			if (isset($_GET['ip'])){
		         $addr=$_GET['ip'];
				}else {
					$addr=$_REQUEST['REMOTE_ADDR'];
				}
			try {
				if (!isset($_GET['multiple'])) {
					$response=$client->vgood_getvgood_byuser($_GET['codeID'],$userExt,
                  $_GET['latitude'],$_GET['longitude'],$_GET['radius'],$addr);
				} else {
					$response=$client->vgood_getvgood_byuser_multiple($_GET['codeID'],$userExt,
                  $_GET['latitude'],$_GET['longitude'],$_GET['radius'],$_GET['rows'],$addr);
				}
				
			    } catch (Exception $e ) {
			        print_r("Maybe user is not yet created in beintoo ");
			}			
			echo "<pre>";
            var_export($response);
            echo "</pre>";
			return ;
		}   
	 
	 //	APP LEADERBOARD
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "app_leaderboard") == 0){
	 		$userExt = NULL;
		    if (isset($_GET['userExt']))
		         $userExt=$_GET['userExt'];
		    if (isset($_GET['codeID']))
		         $codeID=$_GET['codeID'];
			try {
					$response=$client->app_leaderboard($codeID,$_GET['rows'],$userExt,$_GET['kind']);
			    } catch (Exception $e ) {
			        print_r("Maybe user is not yet created in beintoo ");
			}			
			echo "<pre>";
            var_export($response);
            echo "</pre>";
			return ;
		}   

		//	ACHIEVEMENT GET
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "achievement_get") == 0){
	 		$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
		    
			try {
					$response=$client->achievement_get($guid);
        			$response=$client->utils_achievements_associative_array($response);
			    } catch (Exception $e ) {
			        print_r("Maybe guid is not yet created in beintoo ");
			}			
			echo "<pre>";
            var_export($response);
            echo "</pre>";
			return ;
		}   
	 
	 //	ACHIEVEMENT UPDATE
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "achievement_update") == 0){
	 		$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
		    
			try {
					$response=$client->achievement_update($_GET['achievementExt'], $guid, 
                		$_GET['percentage'], $_GET['value'], $_GET['increment']);
			    } catch (Exception $e ) {
			        print_r("Parameters not valid ");
			}			
			echo "<pre>";
            var_export($response);
            echo "</pre>";
			return ;
		} 
	//	Render Marketplace
	 if(isset($_GET['action']) && strcmp($_GET['action'], "render_marketplace") == 0){
			$guid = NULL;
		    if (isset($_GET['guid']))
		         $guid=$_GET['guid'];
		    	echo "dentro";
			try {
					/* $response= */$client->render_marketplace($guid, NULL,NULL,false, true, "left");
			    } catch (Exception $e ) {
			        print_r("Parameters not valid ");
			}			
			//echo $response;
            
		}   
	
	//	GENERATE AN ERROR
	 if(isset($_GET['async_action']) && strcmp($_GET['async_action'], "generate_error") == 0){
	 		$client_exp=new BeintooRestClient("oqiaudfosdijfsvlfzxjvnc_WRANG_API_KEY",$sandbox);
	        try {
	        $response=$client_exp->player_login("123null","123null");
	        } catch (BeintooApiException $e) {
	            
				echo "<pre>";
		            var_export($e);
	            echo "</pre>";
	       }
		    
			return ;
		}   
	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title>phpSDK DEMO </title>
        
                  
        <style type="text/css">
            body {
                font-family:'Rokkitt', serif;
                padding-left: 20px;
                padding-right: 20px;
            }
            body h1 {
                font-family:'Rokkitt', serif;
                
            }
            body h3 {
                font-family:'Rokkitt', serif;
                margin-top: 20px;
                margin-bottom: 5px;
                border-bottom: 1px solid #CCC;
				border-bottom-width: 1px;
				border-bottom-style: solid;
				border-bottom-color: #CCC;
            }
            
            dl dt {
            	padding-top: 10px;
            }
            
            table {
            	width: 100%;
            }
            
            td div {
            	max-width: 600px;
            	max-height: 500px;
            	overflow-y: scroll;
            }
            
            div table tr td {
            	width: 50%;
            }
           
           .closee {
           	height: 30px;
           	overflow: hidden;
           } 
           
           .open {
           	height: 100%;
            overflow: auto;
            background-color: powderblue;
           }
           
           
            
            .forms {
             height: 400px;
             }
           
    /* 
    .forms div {
            height: 30px;
            overflow: hidden;}
    .forms:hover div {
            height: 30px; }
            
    .forms:hover div:active {
            height: 100%;
            overflow: auto;
            background-color: powderblue; */
     
    }
    
    
        </style>
        
        
        </script>
        <script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
        <script type="text/javascript">
        
            function MyApplication() {
                this.counter = 0;
                this.map = new GMap2(document.getElementById("map_canvas"));
                this.map.setCenter(new GLatLng(40.7219, -73.93), 5);
                var myEventListener = GEvent.bind(this.map, "click", this, function(overlay, latlng) {
                
                    if (latlng) {
                        //this.map.addOverlay(new GMarker(latlng));
                        // this.counter++;
                    
                        var locDiv = document.getElementById("message");
                        //locDiv.innerHTML = "cooridnates lng: "+latlng.lng()+"lat"+latlng.lat();
                        var locinputlat = document.getElementById("l1");
                        var locinputlong = document.getElementById("l2");
                        locinputlong.value=latlng.lng();
                        locinputlat.value=latlng.lat();
                        locinputlat = document.getElementById("lu1");
                        locinputlong = document.getElementById("lu2");
                        locinputlong.value=latlng.lng();
                        locinputlat.value=latlng.lat();
                        locinputlat = document.getElementById("lcp1");
                        locinputlong = document.getElementById("lcp2");
                        locinputlong.value=latlng.lng();
                        locinputlat.value=latlng.lat();
                    
                    } //else if (overlay instanceof GMarker) {
                    // This code is never executed as the event listener is
                    // removed the second time this event is triggered
                    //  this.removeOverlay(marker)
                    //}
                
                    //  GEvent.removeListener(myEventListener);
                
                });
            }
        
            function initialize() {
                var application = new MyApplication();
            }
            
            // $(document).ready(function() {
				// $.ajax({
				// type: "GET",
				// url: "demo.php",
				// data: { async: "1", async_action: "get_player" },
				// success: function(response){
				// $(".player-status").html(response);
				// }
				// });
            // });
            
            function get_async_action (form) {
            	//alert($("#"+form).serialize());
            	$.ajax({
				type: "GET",
				url: "demo.php",
				data: $("#"+form).serialize(),
				success: function(response){
				$("."+form+"_response").html("<pre><h3>Response</h3></pre>"+response);
				}
				});
            
            }
                    
        </script>
    </head>

  <body   onload="initialize()" >

      <img src="http://documentation.beintoo.com/wp-content/themes/beintoo/images/logobeintoo.png">
      <h1> BEINTOO PHP DEMO</h1>
      <p id ="description">This demo is a step-by-step guide that demonstrates how API calls work</p>
       
      <dl>
      	<a href="#player_login">
		<dt>Player login</dt>
      	</a>
		<dd>- The first step to do integrating our sdk is logging in the user by calling player_login function</dd>

      	<a href="#player_by_guid">
		<dt>Get player by a guid</dt>
      	</a>
		<dd>- If you know the guid of a player you can directly get it by guid, unlike player_login it will not create a new player if the guid doesnt exists</dd>
		
      	<a href="#user_setuser">
		<dt>user_setuser</dt>
      	</a>
		<dd>- Retrieve a user by userExt.</dd>
      	
      	
      	<a href="#submit_score">
		<dt>Player submitscore</dt>
      	</a>
		<dd>- You can submit score related to the user experience</dd>
		
      	<a href="#vgood_by_guid">
		<dt>Get vgood by guid</dt>
      	</a>
		<dd>- You can reward you users. For example each time he complete a mission or post something</dd>
      	
      	<a href="#vgood_by_user">
		<dt>Get vgood by a user</dt>
      	</a>
		<dd>- The same as vgood_by_guid but requiring a userExt.</dd>
      	
      	
      	<a href="#app_leaderboard">
		<dt>Leaderboard</dt>
      	</a>
		<dd>- Get Leaderboard</dd>
      	
      	
      	<a href="#achievement_get">
		<dt>achievement_get</dt>
      	</a>
		<dd>- Retrieve an achievement.</dd>
      	
      	<a href="#achievement_update">
		<dt>achievement_update</dt>
      	</a>
		<dd>- Update an Achievement.</dd>
      	
      	
      	<a href="#generate_error">
		<dt>generate an error</dt>
      	</a>
		<dd>- Generate an error with a wron apikey.</dd>
		
		
	</dl>
      	
	<p>We are looking forward for your feedback as well as code contributions! If you added some cool functionality to one of our demos, we'd love to see it to consider it for the next releases.</p>
	
	
<div class="forms" >
	
	
   	<div id="player_login" class="close">
   		<h3>player_login</h3>
   		<table><tr>
   			<td>
		   		<form id="player_login_form" name="input" action="demo.php" method="get">
		        <input type="hidden" name="async_action" value="player_login" />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        Player (guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        User: <input type="text" name="userExt" value="<?php if (isset($_SESSION['userExt'])) echo  $_SESSION['userExt']->id; ?>" /><br /><br />
		        <input type="button" value="Player Login" onclick="get_async_action('player_login_form');"  /><br /><br />
		    	</form>
   			</td>
   			<td><div class="player_login_form_response"></div></td>
   		</tr>
   		</table>
   	</div>

        
    <div id="player_by_guid" class="close">
    	<h3>player_getplayer_byguid</h3>
    	<table><tr>
   			<td>
		    	<form id="player_by_guid_form" name="input" action="demo.php" method="get">
		    	<input type="hidden" name="async_action" value="player_by_guid" />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        Player (guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        <input type="button" value="Player Login by Guid" onclick="get_async_action('player_by_guid_form');"  /><br /><br />
		    	</form>
		    </td>
   			<td><div class="player_by_guid_form_response"></div></td>
   		</tr>
   		</table>
 	</div>
	
	<div id="user_setuser" class="close">
    	<h3>user_setuser</h3>
    	<table><tr>
   			<td>
		        <form id="user_setuser_form" name="input" action="demo.php" method="get">
		        <input type="hidden" name="async_action" value="user_setuser" />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        Player(guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
		        email: <input   type="text" name="email"  /><br /><br />
		        address: <input   type="text" name="address"  /><br /><br />
		        country: <input   type="text" name="country"  /><br /><br />
		        nickname: <input   type="text" name="nickname"  /><br /><br />
		        name: <input   type="text" name="name"  /><br /><br />
		        password: <input   type="text" name="password"  /><br /><br />
		        imageURL: <input   type="text" name="imageURL"  /><br /><br />
		        sendEmail: <input type="checkbox" name="sendGreetingsEmail" value="1" checked="1" /><br /><br />
		        gender: <input type="radio" name="gender" value="1" /> Male    <input type="radio" name="gender" value="2" checked="1" /> Female <input type="radio" name="gender" value="" /> None<br /><br />
		        language: <input   type="text" name="language"  /><br /><br />
		        
		        <input type="button" value="Set User" onclick="get_async_action('user_setuser_form');"  /><br /><br />
		    	</form>
		    </td>
   			<td><div class="user_setuser_form_response"></div></td>
   		</tr>
   		</table>
  	</div>

 
	
  	<div id="submit_score" class="close"> 
      	<h3>player_submitscore</h3>
      	<table><tr>
   			<td>
		    	<form id="player_submitscore_form" name="input" action="demo.php" method="get">
		    	<input type="hidden" name="async_action" value="player_submitscore"  /><br /><br />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        Latitude: <input   type="text" name="latitude" /><br /><br />
		        Longitude: <input   type="text" name="longitude" /><br /><br />
		        Radius: <input    type="text" name="radius" value="1000" /><br /><br />
		        lastScore: <input   type="text" name="lastScore" value="100" /><br /><br />
		        balance: <input   type="text" name="balance"  /><br /><br />
		        Player (guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        codeID: <input type="text" name="codeID" value="" /><br /><br />
		        <input type="button" value="Submit Score" onclick="get_async_action('player_submitscore_form');"  /><br /><br />
		    	</form>
		    </td>
   			<td><div class="player_submitscore_form_response"></div></td>
   		</tr>
   		</table>
  	</div>
    



    <div id="vgood_by_guid" class="close">
    	<h3>vgood_getvgood_byguid</h3>
    	<table><tr>
   			<td>
				<form id="getvgood_by_guid_form" name="input" action="demo.php" method="get">
				<input type="hidden" name="async_action" value="get_vgood_by_guid"  /><br /><br />
			    Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
			    Latitude: <input id="l1" type="text" name="latitude" /><br /><br />
			    Longitude: <input id="l2"  type="text" name="longitude" /><br /><br />
			    Radius: <input id="l3"  type="text" name="radius" value="1000" /><br /><br />
			    Player (guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
			    apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
			    codeID: <input type="text" name="codeID" value="" /><br /><br />
			    ip: <input type="text" name="ip" value="" /><br /><br />
			    <input type="hidden" name="allowBanner" value="true" /><br /><br />
			    <input type="button" value="Get Vgood by Guid" onclick="get_async_action('getvgood_by_guid_form');"  /><br /><br />
    			</form>
    		</td>
   			<td><div class="getvgood_by_guid_form_response"></div></td>
   		</tr>
   		</table>
  	</div>
  	
    
    <div id="vgood_by_user" class="close"> 
    	<h3>vgood_getvgood_byuser</h3>
    	<table><tr>
   			<td>
		    	<form id="vgood_by_user_form" name="input" action="demo.php" method="get">
		    	<input type="hidden" name="async_action" value="get_vgood_by_user"  /><br /><br />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        Latitude: <input id="lu1" type="text" name="latitude" /><br /><br />
		        Longitude: <input id="lu2"  type="text" name="longitude" /><br /><br />
		        Radius: <input id="l3"  type="text" name="radius" value="1000" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        User: <input type="text" name="userExt" value="<?php if (isset($_SESSION['userExt'])) echo  $_SESSION['userExt']->id; ?>" /><br /><br />
		        ip: <input type="text" name="ip" value="" /><br /><br />
		        rows: <input type="text" name="rows" value="3" /><br /><br />
		        Multiple: <input type="checkbox" name="multiple" value="1" /><br /><br />
		        <input type="button" value="Get Vgood by User" onclick="get_async_action('vgood_by_user_form');"  /><br /><br />
    			</form>
    		</td>
   			<td><div class="vgood_by_user_form_response"></div></td>
   		</tr>
   		</table>
 	</div>
                      
    
    <div id="app_leaderboard" class="close">
    	<h3>app_leaderboard</h3>
    	<table><tr>
   			<td>
		        <form id="app_leaderboard_form" name="input" action="demo.php" method="get">
		        <input type="hidden" name="async_action" value="app_leaderboard"  /><br /><br />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        codeID: <input type="text" name="codeID" value="<?php echo $codeID; ?>" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        User: <input type="text" name="userExt" value="<?php if (isset($_SESSION['userExt'])) echo  $_SESSION['userExt']->id; ?>" /><br /><br />
		        kind: <input type="text" name="kind" value="STANDARD" /><br /><br />
		        rows: <input type="text" name="rows" value="20" /><br /><br />
		        <input type="button" value="Get Leaderboard" onclick="get_async_action('app_leaderboard_form');"  /><br /><br />
	 		   	</form>
	 		</td>
   			<td><div class="app_leaderboard_form_response"></div></td>
   		</tr>
   		</table>
     </div>
              
              
   	
   	
   	
   	<div id="achievement_get" class="close">
   		<h3>achievement_get</h3>
   		<table><tr>
   			<td>
		   		<form id="achievement_get_form" name="input" action="demo.php" method="get">
		   		<input type="hidden" name="async_action" value="achievement_get"  /><br /><br />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        Player (guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        <input type="button" value="Get Achievement" onclick="get_async_action('achievement_get_form');"  /><br /><br />
		    	</form>
		    </td>
		    <td><div class="achievement_get_form_response"></div></td>
		   </tr>
		  </table>
 	</div>
 	
    
    <div id="achievement_update" class="close">
    	<h3>achievement_update</h3>
   		<table><tr>
   			<td>
		        <form id="achievement_update_form" name="input" action="demo.php" method="get">
		        <input type="hidden" name="async_action" value="achievement_update"  /><br /><br />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        Player (guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
		        apikey: <input type="text" name="apikey" value="<?php echo $apikey; ?>" /><br /><br />
		        achievement_id: <input   type="text" name="achievementExt" value="" /><br /><br />
		        percentage: <input   type="text" name="percentage" value="5" /><br /><br />
		        value: <input   type="text" name="value" value="20" /><br /><br />
		        increment (boolean): <input   type="text" name="increment" value="false" /><br /><br />
		        (please set value OR percentage)<br/>
		        <input type="button" value="Update achievement" onclick="get_async_action('achievement_update_form');"  /><br /><br />
		    	</form>
		   </td>
		    <td><div class="achievement_update_form_response"></div></td>
		   </tr>
		  </table>
   	</div>
    
        <div id="render_marketplace" class="close">
    	<h3>Render Marketplace</h3>
    	<table><tr>
   			<td>
		    	<form id="render_marketplace_form" name="input" action="demo.php" method="get">
		    	<input type="hidden" name="action" value="render_marketplace"  /><br /><br />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        Player (guid): <input type="text" name="guid" value="<?php echo $guid; ?>" /><br /><br />
		        <input type="submit" value="Render Marketplace" /><br /><br />				
		    	</form>
		     </td>
		    <td><div class="render_marketplace_form_response"></div></td>
		   </tr>
		  </table>
   	</div>


	
   	
    <div id="generate_error" class="close">
    	<h3>generate an error</h3>
    	<table><tr>
   			<td>
		    	<form id="generate_error_form" name="input" action="demo.php" method="get">
		    	<input type="hidden" name="async_action" value="generate_error"  /><br /><br />
		        Sandbox: <input type="checkbox" name="sandbox" value="1" checked="1" /><br /><br />
		        <input type="button" value="Generate error" onclick="get_async_action('generate_error_form');"  /><br /><br />
		    	</form>
		     </td>
		    <td><div class="generate_error_form_response"></div></td>
		   </tr>
		  </table>
   	</div>
                             
</div>
  </body>
</html>

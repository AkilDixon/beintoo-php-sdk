<?php

if (!function_exists('curl_init')) {
    throw new Exception('Beintoo needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Beintoo needs the JSON PHP extension.');
}

class BeintooApiException extends Exception
{
  protected $result;

  public function __construct($result) {
    $this->result = $result;

    $code = isset($result['messageID']) ? $result['messageID'] : 0;

    if (isset($result['message'])) {
      // OAuth 2.0 Draft 10 style
      $msg = $result['message'];
    } else if (isset($result['error']) && is_array($result['error'])) {
      // OAuth 2.0 Draft 00 style
      $msg = $result['error']['message'];
    } else if (isset($result['error_msg'])) {
      // Rest server style
      $msg = $result['error_msg'];
    } else {
      $msg = 'Unknown Error: '.var_export($result,true);
    }

    parent::__construct($msg, $code);
  }

  public function getResult() {
    return $this->result;
  }

  public function getType() {
    if (isset($this->result['error'])) {
      $error = $this->result['error'];
      if (is_string($error)) {
        // OAuth 2.0 Draft 10 style
        return $error;
      } else if (is_array($error)) {
        // OAuth 2.0 Draft 00 style
        if (isset($error['type'])) {
          return $error['type'];
        }
      }
    }
    return 'Exception';
  }

  public function __toString() {
    $str = $this->getType() . ': ';
    if ($this->code != 0) {
      $str .= $this->code . ': ';
    }
    return $str . $this->message;
  }
}



class BeintooRestClient {
    const APIHEADER_VERSION ='X-BEINTOO-SDK-VERSION';
    const VERSION = '1.2.2-php';
    // developer config
    var $debug = FALSE;   // if TRUE the class becomes very verbose
    var $manage_exception = FALSE;   // if FALSE the class throws exceptions
    
	//
    
    var $sandbox = false;
    var $restserver_url = "https://api.beintoo.com/api/rest/";
    var $player_resource = "player";
    var $vgood_resource = "vgood";
    var $user_resource = "user";
    var $shorten_resource = "shorten";
    var $app_resource = "app";
    var $achievement_resource = "achievement";

    var $apikey = NULL;
    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_VERBOSE => FALSE,
        CURLOPT_USERAGENT => 'beintoo-php-sdk-0.1',
        CURLOPT_HTTPHEADER => array('Accept: application/json'),
        CURLOPT_HEADER => 0
    );

    function BeintooRestClient($apikey=NULL, $sandbox=false) {
        $this->apikey = $apikey;
        if (isset($sandbox) && $sandbox == true) {
            //$this->restserver_url = $this->restserver_url_sandbox;
            $this->sandbox=true;
            
        }
    }

    /**
     * This call returns the url to connect the player to a beintoo user
     * @param <type> $apikey
     * @param <type> $guid
     * @param <type> $redirect_uri
     * @param string $display
     * @param string $signup
     * @param string $logged_uri
     * @return string
     */
    public function getConnectUrl($guid, $redirect_uri, $display=NULL, $signup=NULL, $logged_uri=NULL) {
        if ($guid != NULL) {
            $url = "http://www.beintoo.com/connect.html?guid=" . $guid . "&apikey=" . $this->apikey . "&redirect_uri=" . $redirect_uri;
            if (isset($display) &&  $display!=NULL ) {
                $display = "&display=" . $display;
                $url = "http://www.beintoo.com/connect.html?guid=" . $guid . $display . "&apikey=" . $this->apikey . "&redirect_uri=" . $redirect_uri;
            }
            if (isset($signup) &&  $signup!=NULL ) {
                $signup = "&signup=" . $signup;
                $url = "http://www.beintoo.com/connect.html?guid=" . $guid . $display . $signup . "&apikey=" . $this->apikey . "&redirect_uri=" . $redirect_uri;
            }
            if (isset($logged_uri) &&  $logged_uri!=NULL) {
                $logged_uri = "&logged_uri=" . $logged_uri;
                $url = "http://www.beintoo.com/connect.html?guid=" . $guid . $display . $signup . "&apikey=" . $this->apikey . "&redirect_uri=" . $redirect_uri . $logged_uri;
            }
            return $url;
        } else {
            return NULL;
        }
    }

    protected function _get($url, $gets=NULL, $headers=NULL)  {
        $start = microtime(TRUE);
        $all_headers = array_merge($headers, BeintooRestClient::$CURL_OPTS[CURLOPT_HTTPHEADER]);

        $all_headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $all_headers[] =  BeintooRestClient::APIHEADER_VERSION .": ".BeintooRestClient::VERSION;
        if (isset($gets)) {
            $url = $url . "?" . http_build_query($gets);
        }

        if ($this->sandbox) {
            $all_headers[]='sandbox: true';
        }
        if ($this->debug) {
            var_dump($url);
            var_dump($all_headers);
        }
        $process = curl_init($url);
        curl_setopt_array($process, BeintooRestClient::$CURL_OPTS);
        curl_setopt($process, CURLOPT_HTTPHEADER, $all_headers);

        if (!$result = curl_exec($process)) {
            trigger_error(curl_error($process));
            throw new BeintooApiException($result);
        }
        $httpCode = curl_getinfo($process, CURLINFO_HTTP_CODE);


        $stop = microtime(TRUE);
        if ($this->debug == TRUE)
            print_r(" TIME " . ($stop - $start)." CODE:".$httpCode);

        if ($result === false || $httpCode!=200) {
            $e = new BeintooApiException(array(
                        'error_code' => curl_errno($process),
                        'error' => array(
                            'message' => curl_error($process),
                            'type' => 'CurlException',
                            'content' => $result
                        ),
                    ));
            curl_close($process);
            throw $e;
        }

        if (is_array($result) && isset($result['error_code'])) {
            curl_close($process);
            throw new BeintooApiException($result);
        }

 
        curl_close($process);
        $result = json_decode($result);

        return $result;
    }

    protected function _post($url, $data=NULL, $headers=NULL) {

        $all_headers = array_merge($headers, BeintooRestClient::$CURL_OPTS[CURLOPT_HTTPHEADER]);
        //$all_headers = array_merge($headers, $this->headers);
        $all_headers[] = 'Content-type: application/x-www-form-urlencoded';
        $data = http_build_query($data);
        if ($this->sandbox) {
            $all_headers[]='sandbox: true';
        }
        if ($this->debug) {
            var_dump($url);
            var_dump($all_headers);
        }
        $process = curl_init($url);
        curl_setopt_array($process, BeintooRestClient::$CURL_OPTS);
        curl_setopt($process, CURLOPT_HTTPHEADER, $all_headers);
        curl_setopt($process, CURLOPT_POST, TRUE);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);

        $return = curl_exec($process);
        curl_close($process);
        $return = json_decode($return);
        return $return;
    }

    /**
     *
     * @param <type> $guid : the id of the user on app side
     * @param <type> $userExt the id of beintoo user if the app have it in its db
     * @return <type> Player object
     */
    function player_login($guid, $userExt ) {
        try {
            if (isset($this->apikey) && $this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($userExt) && $userExt != NULL)
                $params_header[] = 'userExt: ' . $userExt;
            if ( isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;
 
            $reply = $this->_get($this->restserver_url . $this->player_resource . "/login",
                            $params_get,
                            $params_header
            );
            if ($this->debug) {
                var_dump($reply);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }

    function player_logout($guid) {
        try {
            if (isset( $this->apikey) && $this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;


            $params_get["language"] = 1;

            $reply = $this->_get($this->restserver_url . $this->player_resource . "/logout",
                            $params_get,
                            $params_header
            );
            if ($this->debug) {
                var_dump($reply);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
            
        }
        return $reply;
    }

    function player_getplayer_byguid($guid) {
        try {
            if ($this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;

            $reply = $this->_get($this->restserver_url . $this->player_resource . "/byguid/" . $guid,
                            NULL,
                            $params_header
            );
            if ($this->debug) {
                var_dump($reply);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
            
        }
        return $reply;
    }

    function user_setuser($guid, $email, $address, $country, $gender, $nickname, $name,$password,$sendGreetingsEmail,$imageURL,$language) {
        try {
            if ($this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;
            if (!isset($email) || $email == NULL) {
                $result['error_msg']="email address required.";
                throw new BeintooApiException($result);
            }

            if (isset($email) && $email != NULL)
                $params_get["email"] = $email;
            if (isset($address) && $address != NULL)
                $params_get["address"] = $address;
            if (isset($country) && $country != NULL)
                $params_get["country"] = $country;
            if (isset($gender) && $gender != NULL)
                $params_get["gender"] = $gender;
            if (isset($nickname) && $nickname != NULL) {
                $params_get["nickname"] = $nickname;
            } else {
                $temp=explode("@", $email);
                if (isset($temp[0]) && $temp[0] != NULL) {
                    // creating a default one
                    $params_get["nickname"] = $temp[0];
                }
            } 
            if (isset($name) && $name != NULL)
                $params_get["name"] = $name;
            if (isset($sendGreetingsEmail) && $sendGreetingsEmail == FALSE)
                $params_get["sendGreetingsEmail"] = $sendGreetingsEmail;
            if (isset($password) && $password != NULL)
                $params_get["password"] = $password;
            if (isset($language) && $language != NULL)
                $params_get["language"] = $language;
            if (isset($imageURL) && $imageURL != NULL)
                $params_get["imageURL"] = $imageURL;

            $reply = $this->_post($this->restserver_url . $this->user_resource . "/set",
                            $params_get,
                            $params_header
            );
            if ($this->debug) {
                var_dump($reply);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }
    

    function user_shorten($codeID,$guid,$originalUrl) {
        try {
            if ($this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($codeID) && $codeID!=NULL)
                $params_header[] = 'codeID: ' . $codeID;
            if (!isset($originalUrl) || $originalUrl == NULL) {
                $result['error_msg']="NO URL to be shortened";
                throw new BeintooApiException($result);
                }
            if ((!isset($guid) || $guid == NULL)  ){
                $result['error_msg']="NO Guid Provided";
                throw new BeintooApiException($result);
                }
            if (isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;
            if (isset($originalUrl) && $originalUrl != NULL)
                $params_get["url"] = $originalUrl;

            $reply = $this->_post($this->restserver_url . $this->shorten_resource ,
                            $params_get,
                            $params_header
            );
            if ($this->debug) {
                var_dump($reply);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }

    
    function player_submitscore($codeID, $guid, $lastScore=NULL, $balance=NULL, $latitude=NULL, $longitude=NULL, $radius=NULL,$ip_address=NULL) {


        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;
            if (isset($codeID) && $codeID!=NULL)
                $params_header[] = 'codeID: ' . $codeID;
            if (isset($ip_address) && $ip_address!=null)
                $params_header[] = 'ipAddr: ' . $ip_address;

            if (isset($lastScore) && $lastScore != NULL) {
                $params_get["lastScore"] = $lastScore;
                if ($lastScore==0) {
                    $params_get["lastScore"]="0";
                }
            }
            if (isset($balance) && $balance != NULL) {
                $params_get["balance"] = $balance;
                if ($balance == 0) {
                    $params_get["balance"] = "0";
                }
            }

            if (isset($latitude) && $latitude!=NULL ) {
                $params_get["latitude"] = $latitude;
            }
            if (isset($longitude) && $longitude!=NULL ) {
                $params_get["longitude"] = $longitude;
            }
            if (isset($radius) && $radius!=NULL ) {
                $params_get["radius"] = $radius;
            }

            $reply = $this->_get($this->restserver_url . $this->player_resource . "/submitscore",
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }
    
    function vgood_getvgood_byguid_multiple($codeID, $guid, $latitude=NULL, $longitude=NULL, $radius=NULL, $rows=NULL, $ip_address=NULL) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($guid == NULL) {
                $result['error_msg']="NO GUID";
                throw new BeintooApiException($result);
            }
            if (isset($codeID)  && $codeID != NULL)
                $params_header[] = 'codeID: ' . $codeID;
            if (isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;
            if (isset($ip_address) && $ip_address != NULL)
                $params_header[] = 'ipAddr: ' . $ip_address;

            if (isset($latitude) && $latitude!=NULL ) {
                $params_get["latitude"] = $latitude;
            }
            if (isset($longitude) && $longitude!=NULL ) {
                $params_get["longitude"] = $longitude;
            }
            if (isset($radius) && $radius!=NULL ) {
                $params_get["radius"] = $radius;
            }
            if (isset($rows) && $rows!=NULL ) {
                $params_get["rows"] = $rows;
            }
            //var_dump($params_get);
            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/byguid/" . $guid,
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }

    function vgood_assign($codeID,$userExt, $vgoodExt) {
      try {
            if (isset($this->apikey) && $this->apikey!=NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if (!isset($userExt) || $userExt == NULL) {
                $result['error_msg']="NO USEREXT";
                throw new BeintooApiException($result);
                }
             if (!isset($vgoodExt) ||  $vgoodExt == NULL) {
                $result['error_msg']="NO VGOODEXT";
                throw new BeintooApiException($result);
                }
                if (isset($codeID) && $codeID!=NULL)
                $params_header[] = 'codeID: ' . $codeID;

            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/accept/".$vgoodExt."/"  . $userExt,
                            $params_get,
                            NULL);
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log(var_export($e,true));
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }


    function vgood_getvgood_byguid($codeID, $guid, $latitude=NULL, $longitude=NULL, $radius=NULL, $ip_address=NULL) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($guid == NULL) {
                $result['error_msg']="NO GUID";
                throw new BeintooApiException($result);
            }
            if (isset($codeID) && $codeID!=NULL)
                $params_header[] = 'codeID: ' . $codeID;
            if (isset($guid) && $guid !=NULL)
                $params_header[] = 'guid: ' . $guid;
            if (isset($ip_address) && $ip_address!=NULL)
                $params_header[] = 'ipAddr: ' . $ip_address;

            if (isset($latitude) && $latitude!=NULL ) {
                $params_get["latitude"] = $latitude;
            }
            if (isset($longitude) && $longitude!=NULL ) {
                $params_get["longitude"] = $longitude;
            }
            if (isset($radius) && $radius!=NULL ) {
                $params_get["radius"] = $radius;
            }
            $params_get["language"] = 1;
            //var_dump($params_get);
            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/get/byguid/" . $guid,
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }

    function vgood_getvgood_byuser($codeID, $userExt, $latitude=NULL, $longitude=NULL, $radius=NULL, $ip_address=NULL) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($userExt == NULL) {
                $result['error_msg']="NO USEREXT";
                throw new BeintooApiException($result);
                }
            if (isset($codeID) && $codeID!=NULL)
                $params_header[] = 'codeID: ' . $codeID;

            if (isset($latitude) && $latitude!=NULL ) {
                $params_get["latitude"] = $latitude;
            }
            if (isset($longitude) && $longitude!=NULL ) {
                $params_get["longitude"] = $longitude;
            }
            if (isset($radius) && $radius!=NULL ) {
                $params_get["radius"] = $radius;
            }
            $params_get["language"] = 1;
            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/get/byuser/" . $userExt,
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log(var_export($e,true));
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }


    function vgood_getvgood_byuser_multiple($codeID, $userExt, $latitude=NULL, $longitude=NULL, $radius=NULL, $rows=NULL,$ip_address=NULL) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($userExt == NULL) {
                $result['error_msg']="NO USEREXT";
                throw new BeintooApiException($result);
                }
            if (isset($codeID) && $codeID!=NULL)
                $params_header[] = 'codeID: ' . $codeID;

            if (isset($latitude) && $latitude!=NULL ) {
                $params_get["latitude"] = $latitude;
            }
            if (isset($longitude) && $longitude!=NULL ) {
                $params_get["longitude"] = $longitude;
            }
            if (isset($radius) && $radius!=NULL ) {
                $params_get["radius"] = $radius;
            }
            if (isset($rows) && $rows!=NULL ) {
                $params_get["rows"] = $rows;
            }
            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/byuser/" . $userExt,
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log(var_export($e,true));
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }


    /*function beta_checkin_places($userExt, $latitude=NULL, $longitude=NULL, $radius=NULL, $onlyVgooded=true) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($latitude) && $latitude!=NULL ) {
                $params_get["latitude"] = $latitude;
            }
            if (isset($longitude) && $longitude!=NULL  ) {
                $params_get["longitude"] = $longitude;
            }
            if (isset($radius) && $radius!=NULL ) {
                $params_get["radius"] = $radius;
            }
            if (isset($onlyVgooded) && $onlyVgooded!=NULL && ($onlyVgooded==TRUE || $onlyVgooded==FALSE) ) {
                $params_get["onlyVgooded"] = $onlyVgooded;
            }
            //var_dump($params_get);
            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/checkin/places/" . $userExt,
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }
*/
    function render_vgood($vgood, $html=FALSE) {
        if ($html) {
            echo <<<EOT
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="stylesheet" href="http://static.beintoo.com/pages/css/beintoo.css" type="text/css" />
</head>
<body>

EOT;
        } else {
            echo '<link rel="stylesheet" href="popup.css" type="text/css" />';
        }
        if (strlen($vgood->description)>203) {
            $description=substr($vgood->description,0,200)."...";
        } else {
                       $description=$vgood->description;
 
        }
        echo <<<EOT
        <div id="overlay-bx" class="overlay-bx">
		<div class="popup-box">
			<div class="inn1">

                               <div class="inn2">
					<h2 class="head-pop">Beintoo</h2>
					<p>Congratulations, you got this reward: </p>
					<img class="img-prod" src="$vgood->imageSmallUrl" alt="" />
					<div class="bx-pop">
						<h3>$vgood->name </h3>
						<p class="tx1">$description</p>
						<a class="botV" href="$vgood->getRealURL" ><span>Get Coupon</span></a>
						<p class="tx2">Your prize is available FOR FREE on your Beintoo Profile.You can download your coupon whenever you want</p>
					</div>
				</div>
                                <div style="text-align: right;padding-bottom: 15px;">
                                <a href="#" class="close" onclick="document.getElementById('overlay-bx').style.display='none';">close</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <br/>
                                </div>
                        </div>

EOT;

  
        if (isset($vgood->whoAlsoConverted)) {
            echo"<br/><p class='r_title'>Who also got the coupon: </p>";
            echo "<div class='bx-pop'><table><tr>";

            foreach ($vgood->whoAlsoConverted as $key => $value) {
                echo "<td>" . "<img src='" . $value->usersmallimg . "' /><br/>" . "</td>";
                echo "<td><table><tr><td><p class='tx1'>" . $value->name . "</p></td></tr><tr><td><p class='tx1'>" . $value->bedollars . "</p></td></tr></table></td>";
            }
            echo "</tr></table></div>";
        }
        
        echo "</div></div>";


        if ($html)
            echo "</body></html>";
    }
	
	function render_marketplace($guid, $lat=NULL, $long=NULL, $html, $needs_jQuery_Import, $position) {
		$url_frame = 'http://www.beintoo.com/m/marketplace.html?apikey=' . $this->apikey . '&guid=' . $guid . '&force_view=mobile';
        if ($html == true) {
            echo "
				<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
				<head>";
        }
	    if ($needs_jQuery_Import === true) 
			echo "<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js' > </script>";
			
			
		echo"<script type='text/javascript' src='http://widgets.beintoo.com/marketplace/beintoo_marketplace.js' > </script>";
	    
		if ($html == true) {
	            echo "
					</head> <body> ";
	    }
	
	    echo <<<EOT
				<script type='text/javascript'>
	               $('head').append("<link href='http://widgets.beintoo.com/marketplace/iframe.css' media='screen' rel='stylesheet' type='text/css' />");
	        Beintoo.iframe_url = '$url_frame';
			Beintoo.position = '$position';
			</script> 
EOT;

        if ($html == true) {
            echo "</body></html>";
        }
    
	}
    
    function app_topscore($codeID=NULL, $rows=20,$userExt=null,$kind='STANDARD') {
        
        // FRIENDS CLOSEST
        try {
            if (isset($this->apikey)  && $this->apikey!=NULL )
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($codeID))
                $params_header[] = 'codeID: ' . $codeID;
            if (isset($kind) && (strcmp($kind, 'STANDARD')!=0) && (!isset($userExt) || $userExt==null) ) {
                $result['error_msg']="NO USEREXT , USEREXT is required if kind != STANDARD";
                throw new BeintooApiException($result);
            }
            if (isset($userExt) && $userExt != NULL) {
                  $params_header[] = 'userExt: ' . $userExt;
            }

            $reply = $this->_get($this->restserver_url . $this->app_resource . "/leaderboard",
                            $params_get,
                            $params_header
            );
            if ($this->debug) {
                var_dump($reply);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }

    
     function achievement_get($guid=NULL) {
      try {
            if (isset($this->apikey) && $this->apikey!=NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            
            if ( isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;
       

            $reply = $this->_get($this->restserver_url . $this->achievement_resource . "/",
                            NULL,
                            $params_header);
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log(var_export($e,true));
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }
    /*
     * returns an associative array with keys the id of achievements, it is simpler to use
     */
    function utils_achievements_associative_array($achievements_response) {
        //////////////////////////////////////////////////////////
        $output=array();
        foreach ($achievements_response as $key=>$value) {
                $new_key=$value->achievement->id;
            $output[$new_key]=$value;
    
        }
        return $output;
    }
     
    function achievement_update( $achievementExt, $guid, $percentage=NULL, $value=NULL, $increment=TRUE) {
        try {
            if (isset($this->apikey) && $this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;

             
            if ($achievementExt == NULL || !isset($achievementExt)) {
                $result['error_msg'] = "NO ACHIEVEMENTEXT";
                throw new BeintooApiException($result);
            }
            if (!isset($guid) ||$guid == NULL) {
                $result['error_msg'] = "NO guid";
                throw new BeintooApiException($result);
            }
            if (isset($percentage) && $percentage != NULL) {
                $params_get["percentage"] = $percentage;
            }
            if (isset($value) && $value != NULL) {
                $params_get["value"] = $value;
            }
            if (!isset($params_get["value"]) && !isset($params_get["percentage"])) {
                $result['error_msg'] = "YOU HAVE TO SET PERCENTAGE OR VALUE";
                throw new BeintooApiException($result);
            }
            if (isset($increment) && $increment != NULL) {
                $params_get["increment"] = $increment;
            }
            $reply = $this->_post($this->restserver_url . $this->achievement_resource . "/" . $achievementExt, $params_get, $params_header);
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log(var_export($e, true));
            if ($this->debug) {
                var_dump($e);
            }
            if (!$this->manage_exception) {
                throw $e;
            }
        }
        return $reply;
    }

    
    
}
?>
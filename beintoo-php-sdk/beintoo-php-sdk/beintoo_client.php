<?php

if (!function_exists('curl_init')) {
    throw new Exception('Beintoo needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Beintoo needs the JSON PHP extension.');
}

class BeintooRestClient {
    const VERSION = '0.1b';

    var $debug = TRUE;
    var $restserver_url_sandbox = "https://sandbox-elb.beintoo.com/api/rest/";
    var $restserver_url_production = "https://api.beintoo.com/api/rest/";
    var $restserver_url = "https://api.beintoo.com/api/rest/";
    var $player_resource = "player";
    var $vgood_resource = "vgood";
    var $user_resource = "user";
    var $app_resource = "app";
    var $apikey = NULL;
    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'beintoo-php-sdk-0.1',
        CURLOPT_HTTPHEADER => array('Accept: application/json'),
        CURLOPT_HEADER => 0
    );

    function BeintooRestClient($apikey=NULL, $sandbox=false) {
        $this->apikey = $apikey;
        if (isset($sandbox) && $sandbox == true) {
            $this->restserver_url = $this->restserver_url_sandbox;
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

    protected function _get($url, $gets=NULL, $headers=NULL) {
        $start = microtime(TRUE);
        $all_headers = array_merge($headers, BeintooRestClient::$CURL_OPTS[CURLOPT_HTTPHEADER]);

        $all_headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        if (isset($gets)) {
            $url = $url . "?" . http_build_query($gets);
        }
        if ($this->debug) {
            var_dump($url);
            var_dump($all_headers);
        }
        $process = curl_init($url);

        curl_setopt_array($process, BeintooRestClient::$CURL_OPTS);
        curl_setopt($process, CURLOPT_HTTPHEADER, $all_headers);
        $return = curl_exec($process);
        curl_close($process);
        $return = json_decode($return);
        $stop = microtime(TRUE);
        if ($this->debug == TRUE)
            print_r(" TIME " . ($stop - $start));
        return $return;
    }

    protected function _post($url, $data=NULL, $headers=NULL) {

        $all_headers = array_merge($headers, BeintooRestClient::$CURL_OPTS[CURLOPT_HTTPHEADER]);
        //$all_headers = array_merge($headers, $this->headers);
        $all_headers[] = 'Content-type: application/x-www-form-urlencoded';
        $data = http_build_query($data);
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
    function player_login($guid, $userExt, $publicname=NULL) {
        try {
            if ($this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($userExt != NULL)
                $params_header[] = 'userExt: ' . $userExt;
            if ($guid != NULL)
                $params_header[] = 'guid: ' . $guid;
            // TODO move this in parameters
            $params_get["language"] = 1;
            if (isset($publicname)) {
                $params_get["publicname"] = $publicname;
            }
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
                var_dump($expression);
            }
        }
        return $reply;
    }

    function player_logout($guid) {
        try {
            if ($this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($guid != NULL)
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
                var_dump($expression);
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
                var_dump($expression);
            }
        }
        return $reply;
    }

    function user_setuser($guid, $email, $address, $country, $gender, $nickname, $name,$password,$sendGreetingsEmail,$imageURL) {
        try {
            if ($this->apikey != NULL)
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($guid) && $guid != NULL)
                $params_header[] = 'guid: ' . $guid;


            if (isset($email) && $email != NULL)
                $params_get["email"] = $email;
            if (isset($address) && $address != NULL)
                $params_get["address"] = $address;
            if (isset($country) && $country != NULL)
                $params_get["country"] = $country;
            if (isset($gender) && $gender != NULL)
                $params_get["gender"] = $gender;
            if (isset($nickname) && $nickname != NULL)
                $params_get["nickname"] = $nickname;
            if (isset($name) && $name != NULL)
                $params_get["name"] = $name;
            if (isset($sendGreetingsEmail) && $sendGreetingsEmail == FALSE)
                $params_get["sendGreetingsEmail"] = $sendGreetingsEmail;
            if (isset($password) && $password != NULL)
                $params_get["password"] = $password;
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
                var_dump($expression);
            }
        }
        return $reply;
    }

    function player_submitscore($codeID, $guid, $lastScore=NULL, $balance=NULL, $latitude=NULL, $longitude=NULL, $radius=NULL,$ip_address=NULL) {


        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($guid))
                $params_header[] = 'guid: ' . $guid;
            if (isset($codeID))
                $params_header[] = 'codeID: ' . $codeID;
            if (isset($ip_address) && $ip_address!=null)
                $params_header[] = 'ipAddr: ' . $ip_address;

            if (isset($lastScore)) {
                $params_get["lastScore"] = $lastScore;
            }
            if (isset($balance)) {
                $params_get["balance"] = $balance;
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
            $params_get["language"] = 1;

            $reply = $this->_get($this->restserver_url . $this->player_resource . "/submitscore",
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($expression);
            }
        }
        return $reply;
    }

    function vgood_getvood_byguid($codeID, $guid, $latitude=NULL, $longitude=NULL, $radius=NULL, $ip_address=NULL) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($guid == NULL) {
                                echo " 1 THERE IS A PROBLEM IN GUID";
                return NULL;
            }
            if (isset($codeID))
                $params_header[] = 'codeID: ' . $codeID;
            if (isset($guid))
                $params_header[] = 'guid: ' . $guid;
            if (isset($ip_address))
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
                var_dump($expression);
            }
        }
        return $reply;
    }

    function vgood_getvood_byuser($codeID, $userExt, $latitude=NULL, $longitude=NULL, $radius=NULL, $ip_address=NULL) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if ($userExt == NULL) {
                return NULL;
            }
            if (isset($codeID))
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
            //var_dump($params_get);
            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/get/byuser/" . $userExt,
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($expression);
                var_dump($e);
            }
        }
        return $reply;
    }

    function beta_checkin_places($userExt, $latitude=NULL, $longitude=NULL, $radius=NULL, $onlyVgooded=true) {

        try {

            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($latitude)) {
                $params_get["latitude"] = $latitude;
            }
            if (isset($longitude)) {
                $params_get["longitude"] = $longitude;
            }
            if (isset($radius)) {
                $params_get["radius"] = $radius;
            }
            if (isset($onlyVgooded)) {
                $params_get["onlyVgooded"] = $onlyVgooded;
            }
            $params_get["language"] = 1;
            //var_dump($params_get);
            $reply = $this->_get($this->restserver_url . $this->vgood_resource . "/checkin/places/" . $userExt,
                            $params_get,
                            $params_header
            );
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($expression);
            }
        }
        return $reply;
    }

    function render_vgood($vgood, $html=false) {
        if ($html) {
            echo <<<EOT
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style type="text/css">
<!-- -->
body {
	background-color: #FFFFFF; /* Background color */
	color: #4D4D4D; /* Foreground color used for text */
	font-family: 'LucidaSansRegular';
	font-size: 12px;
	margin: 0; /* Amount of negative space around the outside of the body */
	padding: 0; /* Amount of negative space around the inside of the body */
}

a {
color : #A7A9AC ;
font-family: 'LucidaSansRegular';

}

.r_title {
font-weight: bold;
font-size: 14px;

font-family: 'LucidaSansRegular';

}

</style></head><body>

EOT;
        }
        echo <<<EOT
<div id="reward">
<table id="prize">
<tr>
<td>
EOT;

        echo "<img width='50' height='50' src='" . $vgood->imageSmallUrl . "'>";
        echo "</td><td>";
        echo "<p class='r_title'>Congratulations! You won : " . $vgood->name . "</p><p class='description' >" . $vgood->description . "</p>";
        echo "<table id='m_links' ><tr><td>";
        if (isset($vgood->getRealURL))
            echo "<a href='#' onclick='window.open(\"" . $vgood->getRealURL . "\",\"_blank\",\"width=320,height=600\");return false;' ><img src='http://static.beintoo.com/popup_img/get_coupon.png' /></a>";
        echo "</td><td>";
        if (isset($vgood->refuseURL))
            echo "<a href='#' onclick='window.open(\"" . $vgood->refuseURL . "\",\"_blank\",\"width=320,height=600\");return false;' >Refuse</a>";
        echo "</td><td>";
//if (isset($vgood->showURL))
//echo "<a href='#' onclick='window.open(\"".$vgood->showURL."\",\"_blank\",\"width=320,height=600\");return false;' >Show</a>";
//echo "</td><td>";
        if (isset($vgood->acceptURL))
            echo "<a href='#' onclick='window.open(\"" . $vgood->acceptURL . "\",\"_blank\",\"width=320,height=600\");return false;' >Accept</a>";
        echo "</td></tr></table>";

        echo <<<EOT
</td>
</tr>
<tr><td></td><td>Click on Show to see your prize and accept it. You will see it in your Beintoo profile. <br/> You can then decide to use it whenever you want , by clicking on Get It Real.</td></tr>
</table>
</div>

EOT;

        if (isset($vgood->whoAlsoConverted)) {
            echo"<br/><p class='r_title'>Who also got the coupon: </p><br/>";
            echo "<table><tr>";

            foreach ($vgood->whoAlsoConverted as $key => $value) {
                echo "<td>" . "<img src='" . $value->userimg . "' /><br/>" . "</td>";
                echo "<td><table><tr><td>" . $value->name . "</td></tr><tr><td>" . $value->bedollars . "</td></tr></table></td>";
            }
            echo "</tr></table>";
        }

        if ($html)
            echo "</body></html>";
    }

    function app_topscore($codeID=NULL, $rows=20) {
        try {
            if (isset($this->apikey))
                $params_header[] = 'apikey: ' . $this->apikey;
            if (isset($codeID))
                $params_header[] = 'codeID: ' . $codeID;



            $reply = $this->_get($this->restserver_url . $this->app_resource . "/topscore",
                            $params_get,
                            $params_header
            );
            if ($this->debug) {
                var_dump($reply);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            if ($this->debug) {
                var_dump($expression);
            }
        }
        return $reply;
    }

}

?>
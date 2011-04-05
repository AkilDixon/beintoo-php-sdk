<?php

require_once 'variables.php';
  require '../beintoo-php-sdk/beintoo_client.php';
    // this is the id on the app side

    $client = new BeintooRestClient($apikey);


    $json=json_decode('{
          "getRealURL": "http://localhost:8080/m/prize.html?action=GETREAL&amp;vid=cghvncbvc&amp;apikey=1234567890",
          "acceptURL": "http://localhost:8080/m/prize.html?action=ACCEPT&amp;vid=cghvncbvc&amp;apikey=1234567890",
          "refuseURL": "http://localhost:8080/m/prize.html?action=REFUSE&amp;vid=cghvncbvc&amp;apikey=1234567890",
          "showURL": "http://localhost:8080/m/prize.html?action=SHOW&amp;vid=cghvncbvc&amp;apikey=1234567890",
          "description": "a brand new Iphone 4g, the discount is from 20% to 90%",
          "descriptionSmall": "this is a short description...",
          "id": "cghvncbvc",
          "imageUrl": "http://static.beintoo.com/test_img/good001b.jpg",
          "imageSmallUrl": "http://static.beintoo.com/test_img/good001.jpg",
          "startdate": "1-gen-2010 0.00.00",
          "enddate": "1-gen-2020 0.00.00",
          "name": "Iphone 4g",
          "whoAlsoConverted": [
                {
                      "id": "86wtrgfv",
                      "language": {
                            "id": 1,
                            "name": "English"
                      },
                      "name": "Antonio Tomarchio",
                      "nickname": "Sprangator",
                      "gender": 1,
                      "userimg": "http://graph.facebook.com/1468361500/picture?type=square",
                      "isverified": true,
                      "lastupdate": "7-feb-2011 20.55.51",
                      "level": 1,
                      "bedollars": 1000,
                      "bescore": 0.9,
                      "shares": 0,
                      "uconversions": 0,
                      "aconversions": 0,
                      "interactions": 0
                },
				{
                      "id": "59f7b2d4be81b31daadc71d4c8cbdd5016845188",
                      "language": {
                            "id": 1,
                            "name": "English"
                      },
                      "name": "Ferdinando Messina",
                      "nickname": "fmessi",
                      "gender": 1,
                      "userimg": "http://beintoostatic.s3.amazonaws.com/images/profile/thumb/c33f8bc6-39e6-4978-9eed-21034fd38f2d3.jpg",
                      "isverified": true,
                      "lastupdate": "7-feb-2011 20.55.51",
                      "level": 1,
                      "bedollars": 1000,
                      "bescore": 0.9,
                      "shares": 0,
                      "uconversions": 0,
                      "aconversions": 0,
                      "interactions": 0
                },
				{
                      "id": "5e7rydfgf",
                      "language": {
                            "id": 1,
                            "name": "English"
                      },
                      "name": "Maria Popova",
                      "nickname": "Fragolina",
                      "gender": 1,
                      "userimg": "http://beintoostatic.s3.amazonaws.com/images/profile/thumb/c33f8bc6-39e6-4978-9eed-21034fd38f2d3.jpg",
                      "isverified": true,
                      "lastupdate": "7-feb-2011 20.55.51",
                      "level": 1,
                      "bedollars": 1000,
                      "bescore": 0.9,
                      "shares": 0,
                      "uconversions": 0,
                      "aconversions": 0,
                      "interactions": 0
                }
          ]
    }');

    
$client->render_vgood($json);

?>

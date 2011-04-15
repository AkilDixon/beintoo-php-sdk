Beintoo PHP SDK
================

The [Beintoo Platform](http://documentation.beintoo.com/) is
a set of APIs that make your application more cool. Read more about
(http://business.beintoo.com/) on the Beintoo developer site.

This repository contains the open source PHP SDK that allows you to utilize the
above on your website. Except as otherwise noted, the Beintoo PHP SDK
is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Usage
-----

    <?php

    require './beintoo_client.php';

    $client = new BeintooRestClient($apikey);


To make [API] calls:


      $result = $client->player_login(...);


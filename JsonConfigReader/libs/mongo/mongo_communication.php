<?php
require 'vendor/autoload.php';
require(__DIR__ . '/../../conf/config.php');

/**Connecto to MongoDb */

$client = new MongoDB\Client("mongodb://" . $mongoUsername . ":" . $mongoPassword . "@" . $mongoServerName . ":27017");

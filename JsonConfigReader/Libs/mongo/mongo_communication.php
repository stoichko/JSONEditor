<?php
require 'vendor/autoload.php';
require(__DIR__ . '/../../ConfigurationSettings/config.php');

/**Connecto to MongoDb */

$client = new MongoDB\Client("mongodb://" . $mongoUsername . ":" . $mongoPassword . "@" . $mongoServerName . ":27017");


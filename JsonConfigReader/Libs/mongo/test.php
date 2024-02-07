<?php

header('Access-Control-Allow-Origin: *');

header("Access-Control-Allow-Credentials: true");

header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

header('Access-Control-Max-Age: 1000');

date_default_timezone_set('Europe/Berlin');



$data = file_get_contents('php://input');

file_put_contents("test_json.json", $data);

if (empty($data)) {

    echo ("Nothing");
} else

    print_r($data);

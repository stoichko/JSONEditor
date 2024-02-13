<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
date_default_timezone_set('Europe/Berlin');



$sn = (int)$_GET['serial'];

/** MSSQL  */
require '../../mssql/mssqlClass v1.0.php';
$mssql = new msssql;
$data = $mssql->JsonRead($sn, "Basic");
// print_r($data_post);
$data_post = json_decode($data);
$data_post = json_encode($data_post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
echo '<pre>' . preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $data_post) . '</pre>';

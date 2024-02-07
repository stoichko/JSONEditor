<?php

require "mongo_read_insert_update.php";

$liDiesetNumber = (int)$_GET['liDiesetNumber'];
$filename = $liDiesetNumber . ".json";

$read = dieset_mongo_read($liDiesetNumber);

//replace tabs with two spaces
//10.04.2023
$json_indented_by_4 = json_encode($read, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
$file_indented_by_2 = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $json_indented_by_4);
$file_indented_by_2 = str_replace("\n", "\r\n", $file_indented_by_2);

file_put_contents($filename, $file_indented_by_2);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($filename));
header('Pragma: public');

readfile($filename);
unlink($filename);

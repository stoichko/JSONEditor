<?php
//TODO : Do we use?
$servername = "MU-SQL02Q\QI8";
$database = "JEDI";
$username = "JEDI";
$password = "v5tG60Tdk1c0bqA6";
$table = "X2G_RX40_test";

try {
     $conn = new PDO("sqlsrv:server=$servername;Database=$database;ConnectionPooling=0", $username, $password,
         array(
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
         )
     );
 
     
     $sql = "SELECT * FROM ".$table;
 
     foreach ($conn->query($sql) as $row) {
         print_r($row);
     } 
 
 
 } catch (PDOException $e) {
     echo ("Error connecting to SQL Server: " . $e->getMessage());
 }


?>
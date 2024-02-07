<?php
$time_start = microtime(true);

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
 
     
     //$sql = "SELECT json,version FROM ".$table." where materialNumber = 812345676 and version = 10";
     $sql = "SELECT json,version FROM ".$table." where materialNumber = 812345676";
     //$sql = "SELECT * FROM ".$table;
     $i=0;
     $conn->query($sql);
 
     foreach ($conn->query($sql) as $row) {
        $i++;
         //print_r($row);
     } 

     echo $i;
 
 
 } catch (PDOException $e) {
     echo ("Error connecting to SQL Server: " . $e->getMessage());
 }


$time_end = microtime(true);
$execution_time = $time_end - $time_start;

//execution time of the script
echo '<br /><b>Total Execution Time:</b> '.round($execution_time,4).' seconds <br /><br />';

 ?>
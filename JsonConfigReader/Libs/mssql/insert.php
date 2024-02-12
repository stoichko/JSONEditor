<?php
require(__DIR__ . '/../../conf/config.php');

print_r($mssqlServerName);

try {
     $conn = new PDO("sqlsrv:server=$mssqlServerName;Database=$mssqlDatabaseName;ConnectionPooling=0", $mssqlUsername, $mssqlPassword,
         array(
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
         )
     );
     

     $materialNumber = 109626659;
     $version = 0;
     $json = '
     
     [  
        {  
          "name": "sravan kumar",  
          "gender": "Male",  
          "subject": "Software engineering"  
        },  
        {  
          "name": "sudheer",  
          "gender": "Male",  
          "subject": "deep learning"    
        },  
        {  
          "name": "radha",  
          "gender": "Female",  
          "subject": "Software engineering"  
        },  
        {  
          "name": "vani",  
          "gender": "Female",  
          "subject": "Software engineering"  
        }
      ]
     
     ';
     
     $data = [
        'materialNumber' => $materialNumber,
        'version' => $version,
        'JSON' => $json,
    ];
    $sql = "INSERT INTO $mssqlTable (materialNumber, version, JSON) VALUES (:materialNumber, :version, :JSON)";
    $stmt= $conn->prepare($sql);
    $stmt->execute($data);
    echo "Insert done";
 
 } catch (PDOException $e) {
     echo ("Error connecting to SQL Server: " . $e->getMessage());
 }
?>
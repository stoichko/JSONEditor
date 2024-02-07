<?php


class msssql
{
    private $table = "X2G_RX40_test";

    private function communication()
    {
        $servername = "MU-SQL02Q\QI8";
        $database = "JEDI";
        $username = "JEDI";
        $password = "v5tG60Tdk1c0bqA6";

        $conn = new PDO(
            "sqlsrv:server=$servername;Database=$database;ConnectionPooling=0",
            $username,
            $password,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );

        return $conn;
    }

    public function inserRecord($materialNumber, $JSON)
    {
        // take version
        $version = $this->lastVersion($materialNumber);
        $version++;

        // skip checking if its first version
        if ($version != 0) {
            $oldJSON = $this->readRecord($materialNumber); // last version json
            $newJSON = json_decode($JSON, true); // new version json
            unset($newJSON['stHeader']['dtCreated']); // remove date and time
            $newJSON = json_encode($newJSON);
            // print_r($oldJSON);
            // print_r($newJSON);

            if ($oldJSON == $newJSON) {
                echo 'The new JSON is the same as last version. No new version will be created in MSSQL';
                return false; // no insert if they are the same
            }
        }

        $conn = $this->communication();
        try {

            $data = [
                'materialNumber' => $materialNumber,
                'version' => $version,
                'JSON' => $JSON,
            ];
            $sql = "INSERT INTO $this->table (materialNumber, version, JSON) VALUES (:materialNumber, :version, :JSON)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($data);
            echo "Insert done";
        } catch (PDOException $e) {
            echo ("Error connecting to SQL Server: " . $e->getMessage());
        }
    }

    private function lastVersion($materialNumber,)
    {
        $conn = $this->communication();

        $sql = "SELECT version FROM " . $this->table . " WHERE materialNumber = " . $materialNumber;
        $result = [];
        foreach ($conn->query($sql) as $row) {
            // print_r($row);
            $result[] = (int)$row['version'];
        }
        // print_r($result);

        if (empty($result)) {
            $result[] = -1;
        }
        return max($result);
    }

    private function readRecord($materialNumber)
    {
        $conn = $this->communication();
        $version = $this->lastVersion($materialNumber);

        $sql = "SELECT JSON FROM " . $this->table . " WHERE materialNumber = " . $materialNumber . " AND version = " . $version;
        $result = $conn->query($sql)->fetch();
        if (empty($result)) {
            return $result;
        }

        $oldJSON = json_decode($result['JSON'], true);
        unset($oldJSON['stHeader']['dtCreated']);
        // print_r($oldJSON);

        return json_encode($oldJSON);
    }
}

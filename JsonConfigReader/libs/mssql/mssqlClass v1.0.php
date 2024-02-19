<?php

require(__DIR__ . '/../../conf/config.php');

class msssql
{
    // private const dsuTable = "X2G_RX40_dieJsons";
    // private const rfidTable = "X2G_RX40_rfidJsons";
    // private const diesetTable = "X2G_RX40_diesetJsons";
    // private const machineTable = "X2G_RX40_machineJsons";
    // private const basicDiesetTable = "X2G_RX40_basicDiesetJsons";
    private const dsuTable = "X2G_RX40_dieJsons_test";
    private const rfidTable = "X2G_RX40_rfidJsons_test";
    private const diesetTable = "X2G_RX40_diesetJsons_test";
    private const machineTable = "X2G_RX40_machineJsons_test";
    private const basicDiesetTable = "X2G_RX40_basicDiesetJsons_test";

    private function tableSelect($formName)
    {

        /** Choosing MSSQL table name
         * $formName comes from third party code
         */
        switch ($formName) {
            case 'DSU':
                return self::dsuTable;
                // last version() - mNum
                break;

            case 'RFID':
                return self::rfidTable;
                break;
                // last version() - mNum 

            case 'Dieset':
                return self::diesetTable;
                break;
                // last version() - sNum

            case 'Machine':
                return self::machineTable;
                break;
                // last version() - sNum

            case 'Basic':
                return self::basicDiesetTable;
                break;


            default:
                return die("FATAL ERROR: MSSQL table not found for " . $formName . ". Please select between DSU/RFID/Dieset/Machine/Basic");
                break;
        }
    }

    private function colSelect($mssqlTable)
    {
        if ($mssqlTable == self::diesetTable || $mssqlTable == self::machineTable || $mssqlTable == self::basicDiesetTable) {
            $column = "sNum";
        } else {
            $column = "mNum";
        }
        return $column;
    }

    private function lastVersion($identifier, $mssqlTable)
    {
        $conn = $this->communication();
        $column = $this->colSelect($mssqlTable);

        $sql = "SELECT dVer FROM " . $mssqlTable . " WHERE " . $column . " = " . $identifier;
        $result = [];

        foreach ($conn->query($sql) as $row) {
            print_r($row);
            $result[] = (int)$row['dVer'];
        }
        // print_r($result . '<br/>');

        if (empty($result)) {
            $result[] = 0;
        }
        return max($result);
    }

    private function communication()
    {
        global $mssqlServerName, $mssqlDatabaseName, $mssqlUsername, $mssqlPassword;
        $conn = new PDO(
            "sqlsrv:server=$mssqlServerName;Database=$mssqlDatabaseName;ConnectionPooling=0",
            $mssqlUsername,
            $mssqlPassword,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );

        return $conn;
    }

    public function inserRecord($identifier, $JSON, $formName)
    {
        //choose table
        $mssqlTable = $this->tableSelect($formName);

        // take version
        $version = $this->lastVersion($identifier, $mssqlTable);

        // skip checking if its first version
        if ($version > 0) {
            $oldJSON = $this->readRecord($identifier, $mssqlTable, $version); // last version json
            $newJSON = json_decode($JSON, true); // new version json
            unset($newJSON['stHeader']['dtCreated']); // remove created date - everityme takes current date and time
            unset($newJSON['stHeader']['uiDataVersion']); // ignore data version because its dimanicaly created
            unset($newJSON['stHeader']['szCreator']); // ignore creator - if someone else press update without editing
            $newJSON = json_encode($newJSON);
            // print_r($oldJSON);
            // print_r($newJSON);

            if ($oldJSON == $newJSON) {
                echo 'The new JSON is the same as last version. No new version will be created in MSSQL';
                return false; // no insert if they are the same
            }
            // die('jsons are different');
        }
        // die('no previous version found');

        /** generating new dVer and upload in MSSQL */
        $newJSON = json_decode($JSON, true);
        $version += 1; // new version number
        $newJSON['stHeader']['uiDataVersion'] = $version; //generating next data version INSIDE json
        $newJSON = json_encode($newJSON); // return information to Json file
        $conn = $this->communication();

        try {
            $data = [
                'jsonFile' => $newJSON,
            ];
            $sql = "INSERT INTO $mssqlTable (jsonFile) VALUES (:jsonFile)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($data);
            echo "Insert done";
        } catch (PDOException $e) {
            echo ("Error connecting to SQL Server: " . $e->getMessage());
        }
        return $newJSON;
    }

    private function readRecord($identifier, $mssqlTable, $version)
    {
        $conn = $this->communication();
        $column = $this->colSelect($mssqlTable);

        $sql = "SELECT jsonFile FROM " . $mssqlTable . " WHERE " . $column . " = " . $identifier . " AND dVer = " . $version;
        $result = $conn->query($sql)->fetch();
        if (empty($result)) {
            return $result;
        }

        $oldJSON = json_decode($result['jsonFile'], true);
        unset($oldJSON['stHeader']['dtCreated']); // remove created date
        unset($oldJSON['stHeader']['uiDataVersion']); // ignore data version because its dimanicaly created
        unset($oldJSON['stHeader']['szCreator']); // ignore creator - if someone else press update without editing
        // print_r($oldJSON);

        return json_encode($oldJSON);
    }

    public function JsonRead($identifier, $formName)
    {
        $conn = $this->communication();
        $table = $this->tableSelect($formName);
        $column = $this->colSelect($table);
        $version = $this->lastVersion($identifier, $table);

        print_r($version);

        if ($version < 0) {
            die('Json is not found in MSSQl');
        }

        $sql = "SELECT jsonFile FROM " . $table . " WHERE " . $column . " = " . $identifier . " AND dVer = " . $version;
        $result = $conn->query($sql)->fetch();
        // return $result[0];
        return $result;
    }
}

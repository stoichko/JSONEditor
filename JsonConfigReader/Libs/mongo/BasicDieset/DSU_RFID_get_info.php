<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
date_default_timezone_set('Europe/Berlin');


function connect_db()
{
    require_once('../../config.php');

    $conn = new PDO("mysql:host=" . $wordPressDbHost . ";dbname=" . $wordPressDbName, $wordPressDbUser, $wordPressDbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
}
function basic_dieset_DSU($conn, $MN)
{
    $wp_table = "wp_gf_entry_meta";
   
        $sql = "SELECT entry_id FROM `wp_gf_entry_meta` WHERE form_id = 12 AND meta_value LIKE '%$MN%'AND meta_key = 302";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        unset($entry_ids);
        while ($row = $stmt->fetch()) {
            //*Checking if the PN is active /revision or deleted *//
            $idtest = $row['entry_id'];
            $sql2 = "SELECT status  FROM `wp_gf_entry` where form_id = 12 and id = $idtest";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute();
            $row2 = $stmt2->fetch();

            if ($row2['status'] === "active") {

                $entry_ids[] = $row['entry_id'];
            }
        }
        if (empty($entry_ids)) {

            unset($entry_ids);

            // continue;
        } else {
            $entry_ids = array_unique($entry_ids);
            echo($entry_ids);
        }




            $sql = "SELECT meta_value FROM `wp_gf_entry_meta` WHERE form_id = 12 AND meta_key = 162 AND entry_id = $entry_ids";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch()) {


                $dieTypesDSU[] = $row['meta_value'];
                // var_dump($dieTypesDSU);
            }


            $sql = "SELECT meta_value FROM `wp_gf_entry_meta` WHERE form_id = 12 AND meta_key = 19 AND entry_id = $id";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch()) {


                $materialNumberDSU[] = $row['meta_value'];
            }

            $sql = "SELECT meta_value FROM `wp_gf_entry_meta` WHERE form_id = 12 AND meta_key = 22 AND entry_id = $id";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch()) {


                $storageDeviceDSU[] = $row['meta_value'];
            }

   
    if (empty($materialNumberDSU)) {
        return 0;
    } else {
        return array($materialNumberDSU, $dieTypesDSU, $storageDeviceDSU);
    }
}
// function basic_dieset_RFID($conn, $aliProjektNumbers)
// {
//     //SELECT meta_value FROM wp_gf_entry_meta WHERE meta_key= 272 AND form_id = 8 - fomrat type
//     $wp_table = "wp_gf_entry_meta";
//     foreach ($aliProjektNumbers as $PN) {

//         $sql = "SELECT entry_id FROM `wp_gf_entry_meta` WHERE form_id = 25 AND meta_value LIKE '%$PN%'AND meta_key = 175";
//         $stmt = $conn->prepare($sql);
//         $stmt->execute();
//         unset($entry_ids);

//         while ($row = $stmt->fetch()) {


//             //*Checking if the PN is active /revision or deleted *//
//             $idtest = $row['entry_id'];
//             $sql2 = "SELECT status  FROM `wp_gf_entry` where form_id = 25 and id = $idtest";
//             $stmt2 = $conn->prepare($sql2);
//             $stmt2->execute();
//             $row2 = $stmt2->fetch();
//             // var_dump($row2);

//             if ($row2['status'] === "active") {

//                 $entry_ids[] = $row['entry_id'];
//             }
//         }
//         if (empty($entry_ids)) {
//             continue;
//         } else {
//             // var_dump($entry_ids);
//             $entry_ids = array_unique($entry_ids);
//             // var_dump($entry_ids);
//         }


//         // $entry_ids = array_unique($entry_ids);
//         foreach ($entry_ids as $id) {
//             $sql = "SELECT meta_value FROM `wp_gf_entry_meta` WHERE form_id = 25 AND meta_key = 162 AND entry_id = $id";
//             $stmt = $conn->prepare($sql);
//             $stmt->execute();

//             while ($row = $stmt->fetch()) {


//                 $dieTypesRFID[] = $row['meta_value'];
//             }

//             $sql = "SELECT meta_value FROM `wp_gf_entry_meta` WHERE form_id = 25 AND meta_key = 19 AND entry_id = $id";
//             $stmt = $conn->prepare($sql);
//             $stmt->execute();
//             while ($row = $stmt->fetch()) {


//                 $materialNumberRFID[] = $row['meta_value'];
//             }

//             $sql = "SELECT meta_value FROM `wp_gf_entry_meta` WHERE form_id = 25 AND meta_key = 22 AND entry_id = $id";
//             $stmt = $conn->prepare($sql);
//             $stmt->execute();
//             while ($row = $stmt->fetch()) {


//                 $storageDeviceRFID[] = $row['meta_value'];
//             }
//         }
//     }
//     if (empty($materialNumberRFID)) {
//         return 0;
//     } else {
//         return array($materialNumberRFID, $dieTypesRFID, $storageDeviceRFID);
//     }
// }

// function creatJSON($aliProjektNumbers)
// {
//     $aliProjektNumbers = explode(", ", $aliProjektNumbers);
//     $conn = connect_db();
//     $DSU = basic_dieset_DSU($conn, $aliProjektNumbers);
//     $RFID = basic_dieset_RFID($conn, $aliProjektNumbers);
//     if (!empty($DSU)) {
//         foreach ($DSU[0] as $MatNum) {

//             $MatNums[] = (int)$MatNum;
//         }
//         foreach ($DSU[1] as $DieType) {

//             $DieTypes[] = $DieType;
//         }
//         foreach ($DSU[2] as $StorageDevice) {

//             $StorageDevices[] = $StorageDevice;
//         }
//     }
//     if (!empty($RFID)) {
//         foreach ($RFID[0] as $MatNum) {

//             $MatNums[] = (int)$MatNum;
//         }
//         foreach ($RFID[1] as $DieType) {

//             $DieTypes[] = $DieType;
//         }
//         foreach ($RFID[2] as $StorageDevice) {

//             $StorageDevices[] = $StorageDevice;
//         }
//     }
//     $result = array(
//         'aliMaterialNumbers' => $MatNums,
//         'aszDieTypes' => $DieTypes,
//         'aszStorageDevices' => $StorageDevices
//     );

//     return $result;
// }
function creatJSON($MN)
{
    $conn = connect_db();
    $DSU = basic_dieset_DSU($conn, $MN);
    // $RFID = basic_dieset_RFID($conn, $aliProjektNumbers);
    if (!empty($DSU)) {
        foreach ($DSU[0] as $MatNum) {

            $MatNums[] = (int)$MatNum;
        }
        foreach ($DSU[1] as $DieType) {

            $DieTypes[] = $DieType;
        }
        foreach ($DSU[2] as $StorageDevice) {

            $StorageDevices[] = $StorageDevice;
        }
    }
    if (!empty($RFID)) {
        foreach ($RFID[0] as $MatNum) {

            $MatNums[] = (int)$MatNum;
        }
        foreach ($RFID[1] as $DieType) {

            $DieTypes[] = $DieType;
        }
        foreach ($RFID[2] as $StorageDevice) {

            $StorageDevices[] = $StorageDevice;
        }
    }
    $result = array(
        'aliMaterialNumbers' => $MatNums,
        'aszDieTypes' => $DieTypes,
        'aszStorageDevices' => $StorageDevices
    );

    return $result;
}
echo ("<pre>");
print_r(creatJSON("164636"));
echo ("</pre>");

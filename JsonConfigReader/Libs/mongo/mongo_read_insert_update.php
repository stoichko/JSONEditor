<?php


require "mongo_communication.php";
/*Tools for all*/

function json_formating($data)
{

    $decode = json_decode($data, true);

    /**if filed staty with "a" => is array => convert string into array with separator comma */
    foreach ($decode as $field_key => $value) {


        //* 'al' means Array of Long; 'ai' means Array of int
        switch (substr($field_key, 0, 2)) {

            case 'al':
                $str_arr = array_map('intval', explode(',', $value));
                $decode[$field_key] = $str_arr;
                break;

            case 'ai':

                if (substr($field_key, 0, 10) == 'aiActuator') {
                    if ($value == "") {
                        $str_arr = [0, 0, 0];
                        $decode[$field_key] = $str_arr;
                        break;
                    } else
                        $res = str_replace(array('\'', '"', '[', ']', '{', '}'), '', $value);

                    $arstr = explode(",", $res);
                    $str_arr = array();
                    foreach ($arstr as $value) {

                        array_push($str_arr, intval(preg_replace('/[^0-9]+\d:/', '', $value), 10));
                    }

                    $decode[$field_key] = $str_arr;

                    break;

                    // $decode[$field_key] = $str_arr;
                    // break;
                }
                $str_arr = array_map('intval', explode(',', $value));
                if (count($str_arr) == 1) {
                    // $decode[$field_key] = reset($str_arr);
                    //**for array of one  */
                    $decode[$field_key] = $str_arr;
                    break;
                } else {
                    $decode[$field_key] = $str_arr;

                    break;
                }

            case 'as':

                // print($field_key);
                if (substr($field_key, 0, 3) == 'asi') {

                    $str_arr = array_map('intval', explode(',', $value));

                    if (count($str_arr) == 1) {
                        $decode[$field_key] = reset($str_arr);
                        break;
                    } else {

                        $decode[$field_key] = $str_arr;

                        break;
                    }
                } else {
                    $decode[$field_key] = strval($value);
                    // print($field_key."    ".PHP_EOL);
                }
                break;
        }
        /**"bEndOfFile" field change the value to boolean */
        if ($field_key == 'bEndOfFile')
            $decode[$field_key] = true;



        /** special field that must be INT for Machine_Json */
        if (substr($field_key, 0, 10) == 'adiMaxLoad') {
            $decode[$field_key] = intval($value);
        }

        //** This if statemant is not nessesary if evrything is ok we can change it whit else for all other "as" case */
        if ($field_key == 'aszLowerWebCompatibility') {
            $decode[$field_key] = array_map('strval', explode(', ', $value));
        } elseif ($field_key == 'aszHeating') {
            $decode[$field_key] = array_map('strval', explode(', ', $value));
        } elseif ($field_key == 'aszSealing') {
            $decode[$field_key] = array_map('strval', explode(', ', $value));
        } elseif ($field_key == 'aszForming') {
            $decode[$field_key] = array_map('strval', explode(', ', $value));
            //Cooling field is added at 7.03.2023
        } elseif ($field_key == 'aszCooling') {
            $decode[$field_key] = array_map('strval', explode(', ', $value));
        }
        if (substr($field_key, 0, 1) == "i") {
            $decode[$field_key] = intval($value);
        }
    }


    return json_encode($decode);
}

function dieset_json_asz_combine($data)
{
    $decode = json_decode($data, true);

    // if array key starts with 'asz' and contain '_' create new field and combine all with this name
    foreach ($decode as $field_key => $value) {

        if (substr($field_key, 0, 3) == 'asz' && strpos($field_key, '_') !== false) {
            $new_key = substr($field_key, 0, strpos($field_key, '_'));

            // if new key doesnt exist add the value in [0], if exist => [1]
            if (!array_key_exists($new_key, $decode)) {
                // replace 0 with "" 
                if ($value == 0) $decode[$new_key][0] = "";
                else $decode[$new_key][0] = $value;
            } else $decode[$new_key][1] = $value;

            // now we have new record that contain combined values. So we don't need more keys with '_' 
            unset($decode[$field_key]);
        }
    }


    return json_encode($decode);
}

function check_zero_valie($value_form, $type)
{

    if (empty($decode['aszFormingModule']) and $type == "string") {

        return "";
    }

    if (empty($decode['aszFormingModule']) and $type == "num") {

        return 0;
    }
}

// function to check for nulls
function check_null($var, $type = "st")
{
    if (is_null($var)) {

        if ($type == "st") {
            $var = "";
            return $var;
        } elseif ($type == "i") {
            $var = 0;
            return $var;
            // $var = unset($var); 
        }
        /*handling null array*/ elseif ($type == "ai") {
            $var = [0];
            return $var;
            // $var = unset($var); 
        }
    }

    return $var;
}
//function to check if array is empty 
function check_empty_array($arr, $type = "i")
{
    $arr = array_filter($arr);
    if (empty($arr)) {
        if ($type == "i") {
            $arr = [0];
        } elseif ($type == "sz") {
            $arr = [""];
        }
    }


    return $arr;
}
/** If key starts whit asz is null make it ["",""] */
function check_asz_empty($aszArr, $rows = 2)
{
    if (empty($aszArr)) {
        $aszArr = [];
        while ($rows > 0) {
            array_push($aszArr, "");
            $rows = $rows - 1;
        }

        return $aszArr;
    }
    return $aszArr;
}

/*RFID tools*/
function RFID_dictionary($data)
{
    $decode = json_decode($data, true);

    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";
    $stHeader = array(

        'uiStructureVersion' => check_null($decode['uiStructureVersion'], "i"),
        'uiDataVersion' => check_null($decode['uiDataVersion'], "i"),
        'szCreator' => check_null($decode['szCreator']),
        'dtCreated' => check_null($decode['dtCreated']),
        'szState' => check_null($decode['szState']),
    );
    $stIdentification = array(
        'liMaterialNumber' => $decode['liMaterialNumber'],
        'szCustomerDescription' => check_null($decode['szCustomerDescription']),
    );
    $stGeneralProperties = array(
        'szDieType' => check_null($decode['szDieType']),
        'szStorageDevice' => check_null($decode['szStorageDevice'],),

    );
    $stFormatDescription = array(
        'aliFormatNumber' => check_null($decode['aliFormatNumber'], "ai"),
        'aiFormatIndex' => check_null($decode['aiFormatIndex'], "ai"),
        'iNominalMachineWidth' => check_null($decode['iNominalMachineWidth'], "i"),
        'rFormatCutOffLength' => check_null($decode['rFormatCutOffLength'], "i"),
    );
    $stVarioDescription = array(
        'aliVarioNumber' => check_null($decode['aliVarioNumber'], "ai"),
        'aiVarioIndex' => check_null($decode['aiVarioIndex'], "ai"),
        'rVarioCutOffLength' => check_null($decode['rVarioCutOffLength'], "i"),
        'iTracks' => check_null($decode['iTracks'], "i"),
        'iRows' => check_null($decode['iRows'], "i"),
        'aliSinglePackDrawing' => check_null($decode['aliSinglePackDrawing'], "ai"),
        'rPackWidth' => check_null($decode['rPackWidth'], "i"),
        'rPackLength' => check_null($decode['rPackLength'], "i"),
        'rPackFootprint' => round(check_null($decode['rPackFootprint'], "i"), 1),
        'rFormingRatio' => check_null($decode['rFormingRatio'], "i"),
        'rFormingRatioOffset' => check_null($decode['rFormingRatioOffset'], "i"),
    );
    $stTechnicalData = array(
        'diEffectiveProcessArea' => check_null($decode['diEffectiveProcessArea'], "i"),
    );
    $stFooter = array(
        'bEndOfFile' => true
    );
    $result = array(
        'stHeader' => $stHeader,
        'stIdentification' => $stIdentification,
        'stGeneralProperties' => $stGeneralProperties,
        'stFormatDescription' => $stFormatDescription,
        'stVarioDescription' => $stVarioDescription,
        // 'stHeatingParameters' => $stHeatingParameters,
        'stTechnicalData' => $stTechnicalData,
        'stFooter' => $stFooter,
        // 'stFooter' => true,
    );

    // echo "<pre>";
    // print_r(json_encode($result));
    // file_put_contents("test_dsu.json",json_encode($result));
    // // echo "</pre>";
    return json_encode($result);
}
function RFID_mongo_insert_record($json)
{
    global $client, $db;
    $dbFolder = "TFP";
    $collection = $client->$db->$dbFolder;
    // print_r($collection);
    $collection->insertOne($json);
    // echo "insert in ".$dbFolder." successfully!";

}

function RFID_mongo_read($matNumber)
{
    global $client, $db;
    $dbFolder = 'TFP';
    $collection = $client->$db->$dbFolder;

    $search_mongo = array(
        "stIdentification.liMaterialNumber" => (int)$matNumber,
    );
    $cursor = $collection->find($search_mongo)->toArray();
    // print_r($search_mongo);
    // print_r($cursor);
    // die();

    if (empty($cursor)) {
        // die();
        exit("No info found for MaterialNumber = " . $matNumber);
    }

    /** reading data  */

    $read_result = json_decode(json_encode($cursor));
    unset($read_result[0]->_id); // remove oid on dieset
    return $read_result[0];
    // print_r(json_encode($read_result));
    // die();
}

function RFID_mongo_check_existing($matNumber)
{

    global $client, $db;
    $dbFolder = 'TFP';
    $collection = $client->$db->$dbFolder;
    $search_mongo = array(
        "stIdentification.liMaterialNumber" => (int)$matNumber
    );

    $cursor = $collection->find($search_mongo)->toArray();

    return $cursor;
}

function RFID_mongo_update_record($matNumber, $json)
{
    global $client, $db;
    $dbFolder = 'TFP';
    $collection = $client->$db->$dbFolder;
    $collection->updateOne(
        ["stIdentification.liMaterialNumber" => (int)$matNumber],
        ['$set' => $json]
    );
    // echo "Update in " . $dbFolder . " successfully!";
}


/*Dieset tools*/
function dieset_dictionary($data)
{

    // $decode = [];

    $decode = json_decode($data, true);

    //print_r($decode['aszFormingModule']);
    //echo "test1234";





    $stHeader = array_filter(array(
        'uiStructureVersion' => check_null($decode['uiStructureVersion'], "i"),
        'uiDataVersion' => check_null($decode['uiDataVersion'], "i"),
        'szCreator' => check_null($decode['szCreator']),
        'dtCreated' => check_null($decode['dtCreated']),
        'szState' => check_null($decode['szState']),
    ));

    $stIdentification = array(
        'liDiesetNumber' => check_null($decode['liDiesetNumber']),
        'szCustomerDescription' => check_null($decode['szCustomerDescription']),
    );

    $stFormatDescription = array(
        'aliFormatNumber' => check_null($decode['aliFormatNumber'], "ai"),
        'aiFormatIndex' => check_null($decode['aiFormatIndex'], "ai"),
        'iNominalMachineWidth' => check_null($decode['iNominalMachineWidth'], "i"),
        'rFormatCutOffLength' => check_null($decode['rFormatCutOffLength'], "i"),
    );

    $stVarioDescription = array(
        'aliVarioNumber' => check_null($decode['aliVarioNumber'], "ai"),
        'aiVarioIndex' => check_null($decode['aiVarioIndex'], "ai"),
        'rVarioCutOffLength' => check_null($decode['rVarioCutOffLength'], "i"),
        'iTracks' => check_null($decode['iTracks'], "i"),
        'iRows' => check_null($decode['iRows'], "i"),
        'aliSinglePackDrawing' => check_null($decode['aliSinglePackDrawing'], "ai"),
        'rPackWidth' => check_null($decode['rPackWidth'], "i"),
        'rPackLength' => check_null($decode['rPackLength'], "i"),
        'rPackFootprint' => round(check_null($decode['rPackFootprint'], "i"), 1),
        'rFormingRatio' => check_null($decode['rFormingRatio'], "i"),
        'rFormingRatioOffset' => check_null($decode['rFormingRatioOffset'], "i"),
    );

    $stMaterialNumbers = array(
        'aszFormingTopSection' => check_asz_empty($decode['aszFormingTopSection']),
        'aszFormingBottomSection' => check_asz_empty($decode['aszFormingBottomSection']),
        'aszFormingModule' => check_asz_empty($decode['aszFormingModule']),
        'aszFormingModule2' => check_asz_empty($decode['aszFormingModule2']),
        'aszFormingPlate' => check_asz_empty($decode['aszFormingPlate']),
        'aszFormingSlideInPart' => check_asz_empty($decode['aszFormingSlideInPart']),
        /* Request for delete
            'aszFormingPlugHoldingPlate' => $decode['aszFormingPlugHoldingPlate'],
            'aszFormingPlugHoldingPlate2' => $decode['aszFormingPlugHoldingPlate2'],*/
        'aszFormingGridOrChangeFrame' => check_asz_empty($decode['aszFormingGridOrChangeFrame']),
        'aszFormingUWTopSection' => check_asz_empty($decode['aszFormingUWTopSection']),
        'aszFormingUWBottomSection' => check_asz_empty($decode['aszFormingUWBottomSection']),
        'aszFormingUWModule' => check_asz_empty($decode['aszFormingUWModule']),
        'aszFormingUWModule2' => check_asz_empty($decode['aszFormingUWModule2']),
        'aszFormingUWPlate' => check_asz_empty($decode['aszFormingUWPlate']),
        'aszFormingUWSlideInPart' => check_asz_empty($decode['aszFormingUWSlideInPart']),
        /**request for dell 
            'aszFormingUWPlugHoldingPlate' => check_asz_empty($decode['aszFormingUWPlugHoldingPlate']),
            'aszFormingUWPlugHoldingPlate2' => check_asz_empty($decode['aszFormingUWPlugHoldingPlate2']), */
        'aszFormingUWGridOrChangeFrame' => check_asz_empty($decode['aszFormingUWGridOrChangeFrame']),
        'aszSealingTopSection' => check_asz_empty($decode['aszSealingTopSection']),
        'aszSealingBottomSection' => check_asz_empty($decode['aszSealingBottomSection']),
        'aszSealingSlideInPart' => check_asz_empty($decode['aszSealingSlideInPart']),
        'aszSealingGrid' => check_asz_empty($decode['aszSealingGrid']),
        'aszPreSealing1TopSection' => check_asz_empty($decode['aszPreSealing1TopSection']),
        'aszPreSealing1BottomSection' => check_asz_empty($decode['aszPreSealing1BottomSection']),
        'aszPreSealing2TopSection' => check_asz_empty($decode['aszPreSealing2TopSection']),
        'aszPreSealing2BottomSection' => check_asz_empty($decode['aszPreSealing2BottomSection']),
        'aszCrossCuttingTopSection' => check_asz_empty($decode['aszCrossCuttingTopSection']),
        'aszCrossCuttingTopSection2' => check_asz_empty($decode['aszCrossCuttingTopSection2']),
        'aszCrossCuttingTopSection3' => check_asz_empty($decode['aszCrossCuttingTopSection3']),
        'aszCrossCuttingTopSection4' => check_asz_empty($decode['aszCrossCuttingTopSection4']),
        'aszCrossCuttingBottomSection' => check_asz_empty($decode['aszCrossCuttingBottomSection']),
        'aszCrossCuttingBottomSection2' => check_asz_empty($decode['aszCrossCuttingBottomSection2']),
        'aszCrossCuttingBottomSection3' => check_asz_empty($decode['aszCrossCuttingBottomSection3']),
        'aszCrossCuttingBottomSection4' => check_asz_empty($decode['aszCrossCuttingBottomSection4']),
        'aszLongitudinalCuttingTopSection' => check_asz_empty($decode['aszLongitudinalCuttingTopSection']),
        'aszLongitudinalCuttingBottomSection' => check_asz_empty($decode['aszLongitudinalCuttingBottomSection']),

        'aszCompleteCutting' => check_asz_empty($decode['aszCompleteCutting']),
        'aszCompleteCutting2' => check_asz_empty($decode['aszCompleteCutting2']),
        'aszCompleteCutting3' => check_asz_empty($decode['aszCompleteCutting3']),
        'aszCompleteCutting4' => check_asz_empty($decode['aszCompleteCutting4']),
        'aszFormCuttingTopSection' => check_asz_empty($decode['aszFormCuttingTopSection']),
        'aszFormCuttingBottomSection' => check_asz_empty($decode['aszFormCuttingBottomSection']),
        // update 15.03.2023
        // 'aszCompleteCuttingBottomSection3' => check_asz_empty($decode['aszCompleteCuttingBottomSection3']),
        // 'aszCompleteCuttingBottomSection4' => check_asz_empty($decode['aszCompleteCuttingBottomSection4']),

        'aszSuctionUnitCenterTrim' => check_asz_empty($decode['aszSuctionUnitCenterTrim']),

        'aszLoadingForm' => check_asz_empty($decode['aszLoadingForm']),
        //Cooling field is added at 7.03.2023
        'aszCoolingForm' => check_asz_empty($decode['aszCoolingForm']),
    );

    $stTechnicalData = array(
        'aszLowerWebCompatibility' => check_asz_empty($decode['aszLowerWebCompatibility'], 1),
    );

    $stFooter = array(
        'bEndOfFile' => $decode['bEndOfFile']
    );

    $result = array(
        'stHeader' => $stHeader,
        'stIdentification' => $stIdentification,
        'stFormatDescription' => $stFormatDescription,
        'stVarioDescription' => $stVarioDescription,
        'stMaterialNumbers' => $stMaterialNumbers,
        'stTechnicalData' => $stTechnicalData,
        'stFooter' => $stFooter,
    );

    return json_encode($result);
}

function dieset_check_exsisting($diesetNumber)
{
    global $client, $db;
    $dbFolder = 'TFP_dieset';
    $collection = $client->$db->$dbFolder;
    $search_mongo = array(
        "stIdentification.liDiesetNumber" => (int)$diesetNumber
    );

    $cursor = $collection->find($search_mongo)->toArray();

    return $cursor;
}

function dieset_mongo_insert_record($json)
{
    global $client, $db;
    $dbFolder = "TFP_dieset";
    $collection = $client->$db->$dbFolder;
    // print_r($collection);
    $collection->insertOne($json);
    // echo "insert in ".$dbFolder." successfully!";

}

function dieset_mongo_update_record($diesetNumber, $json)
{
    global $client, $db;
    $dbFolder = 'TFP_dieset';
    $collection = $client->$db->$dbFolder;
    $collection->updateOne(
        ["stIdentification.liDiesetNumber" => (int)$diesetNumber],
        ['$set' => $json]
    );
}

function dieset_mongo_read($diesetNumber)
{
    global $client, $db;

    $dbFolder = 'TFP_dieset';
    $collection = $client->$db->$dbFolder;

    $search_mongo = array(
        "stIdentification.liDiesetNumber" => (int)$diesetNumber,
    );

    $cursor = $collection->find($search_mongo)->toArray();
    // print_r($search_mongo);
    // die();

    if (empty($cursor)) {
        // die();
        exit("No info found for MaterialNumber = " . $diesetNumber);
    }

    /** reading data  */


    $read_result = json_decode(json_encode($cursor));



    unset($read_result[0]->_id); // remove oid on dieset

    return $read_result[0];
    // print_r(json_encode($read_result));
    // die();

}


/*DSU tools*/
function dsu_dictionary($data)
{
    $decode = json_decode($data, true);

    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";

    // asiSensorPartition 
    $decode['asiSensorPartition'] = check_empty_array(array(
        $decode['asiSensorPartition1'],
        $decode['asiSensorPartition2'],
        $decode['asiSensorPartition3'],
        $decode['asiSensorPartition4'],
    ));

    //asiCableBlocksPerHeatingArea 

    $decode['asiCableBlocksPerHeatingArea'] = check_empty_array(array(
        $decode['asiCableBlocksPerHeatingArea1'],
        $decode['asiCableBlocksPerHeatingArea2'],
        $decode['asiCableBlocksPerHeatingArea3'],
        $decode['asiCableBlocksPerHeatingArea4'],
    ));
    //asiActuatorPartition 
    $decode['asiActuatorPartition'] = check_empty_array(array(
        ($decode['asiActuatorPartition1']),
        ($decode['asiActuatorPartition2']),
        ($decode['asiActuatorPartition3']),
        ($decode['asiActuatorPartition4'])
    ));
    //asiMonitoringCableBlocksPerHeatingArea  
    $decode['asiMonitoringCableBlocksPerHeatingArea'] = check_empty_array(array(
        $decode['asiMonitoringCableBlocksPerHeatingArea1'],
        $decode['asiMonitoringCableBlocksPerHeatingArea2'],
        $decode['asiMonitoringCableBlocksPerHeatingArea3'],
        $decode['asiMonitoringCableBlocksPerHeatingArea4']
    ));
    //aiActuatorPower 
    $decode['aiActuatorPower'] = check_empty_array(array(
        ($decode['aiActuatorPower' . '1']),
        ($decode['aiActuatorPower' . '2']),
        ($decode['aiActuatorPower' . '3']),
        ($decode['aiActuatorPower' . '4'])
    ));
    //asiMonitoringSensorPartition  
    $decode['asiMonitoringSensorPartition'] = check_empty_array(array(
        $decode['asiMonitoringSensorPartition' . '1'],
        $decode['asiMonitoringSensorPartition' . '2'],
        $decode['asiMonitoringSensorPartition' . '3'],
        $decode['asiMonitoringSensorPartition' . '4']
    ));
    //aszDiaphragmType  
    $decode['aszDiaphragmType'] = check_empty_array(array(
        $decode['aszDiaphragmType' . '1'],
        $decode['aszDiaphragmType' . '2'],
        $decode['aszDiaphragmType' . '3'],
    ), "sz");
    //adiEffectiveProcessArea  
    $decode['adiEffectiveProcessArea'] = check_empty_array(array_filter(array(
        $decode['adiEffectiveProcessArea' . '1'],
        $decode['adiEffectiveProcessArea' . '2'],
        $decode['adiEffectiveProcessArea' . '3'],
    )));
    //adiEffectiveDiaphragmArea  
    $decode['adiEffectiveDiaphragmArea'] = check_empty_array(array_filter(array(
        $decode['adiEffectiveDiaphragmArea' . '1'],
        $decode['adiEffectiveDiaphragmArea' . '2'],
        $decode['adiEffectiveDiaphragmArea' . '3']
    )));
    //adiTotalSpringForce  
    $decode['adiTotalSpringForce'] = check_empty_array(array(
        $decode['adiTotalSpringForce' . '1'],
        $decode['adiTotalSpringForce' . '2'],
        $decode['adiTotalSpringForce' . '3'],
    ));
    //aiMaxDiaphragmPressure
    $decode['aiMaxDiaphragmPressure'] = check_empty_array(array(
        $decode['iMaxDiaphragmPressure' . '1'],
        $decode['iMaxDiaphragmPressure' . '2'],
        $decode['iMaxDiaphragmPressure' . '3'],

    ));
    // print_r($decode['asiSensorPartition']);
    // die();


    $stHeader = array(

        'uiStructureVersion' => check_null($decode['uiStructureVersion'], "i"),
        'uiDataVersion' => check_null($decode['uiDataVersion'], "i"),
        'szCreator' => check_null($decode['szCreator']),
        'dtCreated' => check_null($decode['dtCreated']),
        'szState' => check_null($decode['szState']),
    );
    $stIdentification = array(
        'liMaterialNumber' => $decode['liMaterialNumber'],
        'szCustomerDescription' => check_null($decode['szCustomerDescription']),
    );
    $stGeneralProperties = array(
        'szDieType' => check_null($decode['szDieType']),
        'szStorageDevice' => check_null($decode['szStorageDevice'],),

    );
    $stFormatDescription = array(
        'aliFormatNumber' => check_null($decode['aliFormatNumber'], "ai"),
        'aiFormatIndex' => check_null($decode['aiFormatIndex'], "ai"),
        'iNominalMachineWidth' => check_null($decode['iNominalMachineWidth'], "i"),
        'rFormatCutOffLength' => check_null($decode['rFormatCutOffLength'], "i"),
    );
    $stVarioDescription = array(
        'aliVarioNumber' => check_null($decode['aliVarioNumber'], "ai"),
        'aiVarioIndex' => check_null($decode['aiVarioIndex'], "ai"),
        'rVarioCutOffLength' => check_null($decode['rVarioCutOffLength'], "i"),
        'iTracks' => check_null($decode['iTracks'], "i"),
        'iRows' => check_null($decode['iRows'], "i"),
        'aliSinglePackDrawing' => check_null($decode['aliSinglePackDrawing'], "ai"),
        'rPackWidth' => check_null($decode['rPackWidth'], "i"),
        'rPackLength' => check_null($decode['rPackLength'], "i"),
        'rPackFootprint' => round(check_null($decode['rPackFootprint'], "i"), 1),
        'rFormingRatio' => check_null($decode['rFormingRatio'], "i"),
        'rFormingRatioOffset' => check_null($decode['rFormingRatioOffset'], "i"),
    );
    $stHeatingParameters = array(
        'asiCableBlocksPerHeatingArea' => $decode['asiCableBlocksPerHeatingArea'],
        'asiSensorPartition' => $decode['asiSensorPartition'],
        'asiActuatorPartition' => $decode['asiActuatorPartition'],
        'aiActuatorPower' => $decode['aiActuatorPower'],
        'iExternalPreheatingTemperature' => check_null($decode['iExternalPreheatingTemperature'], "i"),
        'iMaximumTemperature' => check_null($decode['iMaximumTemperature'], "i"),
        'szHeatingPlateType' => check_null($decode['szHeatingPlateType']),
        'asiMonitoringCableBlocksPerHeatingArea' => $decode['asiMonitoringCableBlocksPerHeatingArea'],
        'asiMonitoringSensorPartition' => $decode['asiMonitoringSensorPartition'],

    );
    $stTechnicalData = array(
        'aszDiaphragmType' => $decode['aszDiaphragmType'],
        'adiEffectiveProcessArea' => $decode['adiEffectiveProcessArea'],
        'adiEffectiveDiaphragmArea' => $decode['adiEffectiveDiaphragmArea'],
        'adiTotalSpringForce' => $decode['adiTotalSpringForce'],
        'aiMaxDiaphragmPressure' => $decode['aiMaxDiaphragmPressure'],
        'diSealLiftingVolume' => round(check_null($decode['diSealLiftingVolume'], "i"), 1),
    );

    $stFooter = array(
        'bEndOfFile' => $decode['bEndOfFile']
    );
    $result = array(
        'stHeader' => $stHeader,
        'stIdentification' => $stIdentification,
        'stGeneralProperties' => $stGeneralProperties,
        'stFormatDescription' => $stFormatDescription,
        'stVarioDescription' => $stVarioDescription,
        'stHeatingParameters' => $stHeatingParameters,
        'stTechnicalData' => $stTechnicalData,
        'stFooter' => $stFooter,
    );

    // echo "<pre>";
    // print_r(json_encode($result));
    // file_put_contents("test_dsu.json",json_encode($result));
    // // echo "</pre>";
    return json_encode($result);
}
function dsu_check_exsisting($MaterialNumber)
{
    global $client, $db;
    $dbFolder = 'TFP_dsu';
    $collection = $client->$db->$dbFolder;
    $search_mongo = array(
        "stIdentification.liMaterialNumber" => (int)$MaterialNumber
    );

    $cursor = $collection->find($search_mongo)->toArray();

    return $cursor;
}

function dsu_mongo_insert_record($json)
{
    global $client, $db;
    $dbFolder = "TFP_dsu";
    $collection = $client->$db->$dbFolder;
    // print_r($collection);
    $collection->insertOne($json);
    // echo "insert in ".$dbFolder." successfully!";

}

function dsu_mongo_update_record($MaterialNumber, $json)
{
    global $client, $db;
    $dbFolder = 'TFP_dsu';
    $collection = $client->$db->$dbFolder;
    $collection->updateOne(
        ["stIdentification.liMaterialNumber" => (int)$MaterialNumber],
        ['$set' => $json]
    );
}

function dsu_mongo_read($MaterialNumber)
{
    global $client, $db;

    $dbFolder = 'TFP_dsu';
    $collection = $client->$db->$dbFolder;

    $search_mongo = array(
        "stIdentification.liMaterialNumber" => (int)$MaterialNumber,
    );

    $cursor = $collection->find($search_mongo)->toArray();
    // print_r($search_mongo);
    // die();

    if (empty($cursor)) {
        // die();
        exit("No info found for MaterialNumber = " . $MaterialNumber);
    }

    /** reading data  */

    $read_result = json_decode(json_encode($cursor));
    unset($read_result[0]->_id); // remove oid on dieset
    return $read_result[0];
    // print_r(json_encode($read_result));
    // die();

}


/*Machine tools*/
function machine_dictionary($data)
{
    $decode = json_decode($data, true);
    $stHeader = array(
        'uiStructureVersion' => $decode['uiStructureVersion'],
        'uiDataVersion' => $decode['uiDataVersion'],
        'szCreator' => $decode['szCreator'],
        'dtCreated' => $decode['dtCreated'],
        'szState' => check_null($decode['szState']),
    );

    // $stIdentification = array(

    //     'szMachineName' => $decode['szMachineName'],
    //     'szMachineType' => $decode['szMachineType'],
    //     'liSerialNumber' => $decode['liSerialNumber'],
    //     'szMachineDescription' => check_null($decode['szMachineDescription']),
    // );
    $stIdentification = array(

        'szMachineName' => "RX40_" . $decode['liSerialNumber'],
        'szMachineType' => $decode['szMachineType'],
        'liSerialNumber' => $decode['liSerialNumber'],
        'szMachineDescription' => check_null($decode['szMachineDescription']),
    );

    $stDiesets = check_empty_array(array(
        'aliInitialDiesets' => check_null($decode['aliInitialDiesets'], "ai"),

    ));

    $stAdditionalParts = check_empty_array(array(
        'aliAdditionalHeatings' => check_null($decode['aliAdditionalHeatings'], "ai"),
    ));

    $stTechnicalData = array(
        'iNominalMachineWidth' => check_null($decode['iNominalMachineWidth'], "i"),
        'iInternalOperatingVoltage' => check_null($decode['iInternalOperatingVoltage'], "i"),
        'aszLowerWebCompatibility' => check_asz_empty($decode['aszLowerWebCompatibility'], 1),

    );


    $decode['aszStationAssignment'] = (array(
        $decode['aszStationAssignment' . '1'],
        $decode['aszStationAssignment' . '2'],
        $decode['aszStationAssignment' . '3'],
        $decode['aszStationAssignment' . '4'],
        $decode['aszStationAssignment' . '5'],
        $decode['aszStationAssignment' . '6']
    ));
    $decode['adiMaxLoad'] = check_empty_array(array(
        $decode['adiMaxLoad' . '1'],
        $decode['adiMaxLoad' . '2'],
        $decode['adiMaxLoad' . '3'],
        $decode['adiMaxLoad' . '4'],
        $decode['adiMaxLoad' . '5'],
        $decode['adiMaxLoad' . '6']
    ));
    $stLiftingDevices = array(
        "aszStationAssignment" =>  check_empty_array($decode['aszStationAssignment']),
        "adiMaxLoad" => check_empty_array($decode['adiMaxLoad'])
    );

    $stProcesses = check_empty_array(array(
        'aszHeating' => check_asz_empty($decode['aszHeating'], 1),
        'aszForming' => check_asz_empty($decode['aszForming'], 1),
        'aszSealing' => check_asz_empty($decode['aszSealing'], 1),

    ));

    $stFooter = array(
        'bEndOfFile' => $decode['bEndOfFile']
    );

    $result = array(
        'stHeader' => $stHeader,
        'stIdentification' => $stIdentification,
        'stDiesets' => $stDiesets,
        'stAdditionalParts' => $stAdditionalParts,
        'stTechnicalData' => $stTechnicalData,
        'stLiftingDevices' => $stLiftingDevices,
        'stProcesses' => $stProcesses,
        'stFooter' => $stFooter,

    );

    return json_encode($result);
}

function machine_check_exsisting($liSerialNumber)
{
    global $client, $db;
    $dbFolder = 'TFP_machine';
    $collection = $client->$db->$dbFolder;
    $search_mongo = array(
        "stIdentification.liSerialNumber" => (int)$liSerialNumber
    );

    $cursor = $collection->find($search_mongo)->toArray();

    return $cursor;
}

function machine_mongo_insert_record($json)
{
    global $client, $db;
    $dbFolder = "TFP_machine";
    $collection = $client->$db->$dbFolder;
    // print_r($collection);
    $collection->insertOne($json);
    // echo "insert in ".$dbFolder." successfully!";

}

function machine_mongo_update_record($liSerialNumber, $json)
{
    global $client, $db;
    $dbFolder = 'TFP_machine';
    $collection = $client->$db->$dbFolder;
    $collection->updateOne(
        ["stIdentification.liSerialNumber" => (int)$liSerialNumber],
        ['$set' => $json]
    );
}

function machine_mongo_read($liSerialNumber)
{
    global $client, $db;

    $dbFolder = 'TFP_machine';
    $collection = $client->$db->$dbFolder;

    $search_mongo = array(
        "stIdentification.liSerialNumber" => (int)$liSerialNumber,
    );

    $cursor = $collection->find($search_mongo)->toArray();
    // print_r($search_mongo);
    // die();

    if (empty($cursor)) {
        // die();
        exit("No info found for SerialNumber = " . $liSerialNumber);
    }

    /** reading data  */

    $read_result = json_decode(json_encode($cursor));
    unset($read_result[0]->_id); // remove oid on dieset
    return $read_result[0];
    // print_r(json_encode($read_result));
    // die();

}

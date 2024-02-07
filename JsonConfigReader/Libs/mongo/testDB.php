<?php

$time_start = microtime(true);
/** MSSQL  */
require 'C:\wwwroot\TFP_Jedi\mssql\mssqlClass.php';
$mssql = new msssql;


for($i=0;$i<3000;$i++){
$SerialNumber = rand(1,9)."1234567".rand(1,9);
$data_new = '{"stHeader":{"uiStructureVersion":2,"uiDataVersion":1,"szCreator":"stoyanom","dtCreated":"2022-12-06T12:16:07","szState":"new"},"stIdentification":{"liMaterialNumber":'.rand(1,100).',"szCustomerDescription":""},"stGeneralProperties":{"szDieType":"for_top_hoc_fsp","szStorageDevice":"MUSD100"},"stFormatDescription":{"aliFormatNumber":[0],"aiFormatIndex":[0],"iNominalMachineWidth":0,"rFormatCutOffLength":0},"stVarioDescription":{"aliVarioNumber":[0],"aiVarioIndex":[0],"rVarioCutOffLength":0,"iTracks":0,"iRows":0,"aliSinglePackDrawing":[0],"rPackWidth":0,"rPackLength":0,"rPackFootprint":0,"rFormingRatio":0,"rFormingRatioOffset":0},"stHeatingParameters":{"asiCableBlocksPerHeatingArea":[0],"asiSensorPartition":[0],"asiActuatorPartition":[0],"aiActuatorPower":[[123,123,123],[123,123,123]],"iExternalPreheatingTemperature":60,"iMaximumTemperature":0,"szHeatingPlateType":"","asiMonitoringCableBlocksPerHeatingArea":[0],"asiMonitoringSensorPartition":[0]},"stTechnicalData":{"aszDiaphragmType":[""],"adiEffectiveProcessArea":[324],"adiEffectiveDiaphragmArea":[0],"adiTotalSpringForce":[0],"aiMaxDiaphragmPressure":[0],"diSealLiftingVolume":0},"stFooter":{"bEndOfFile":true}}';
$mssql->inserRecord($SerialNumber, $data_new);



}

$time_end = microtime(true);
$execution_time = $time_end - $time_start;

//execution time of the script
echo '<br /><b>Total Execution Time:</b> '.round($execution_time,4).' seconds <br /><br />';



?>



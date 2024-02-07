<?php

function getUrl( $url, $username = false , $password = false ) {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HEADER, FALSE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    
    if( $username && $password ) {
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); 
    }
    
    $buffer = curl_exec($ch); 
    curl_close($ch); 
    
    return $buffer;
}


function searcharray($value, $key, $array) {
    foreach ($array as $k => $val) {
        if ($val[$key] == $value) {
            return $k;
        }
    }
    return null;
}


if(isset($_POST['id']))
    $id=$_POST['id'];
else 
    $id=$_GET['id'];

if(isset($_POST['type']))
    $input_type=$_POST['type'];
else 
    $input_type=$_GET['type'];

if(isset($_POST['name']))
    $name=$_POST['name'];
else 
    $name=$_GET['name'];

// strings
$classification = 'classification';
$bom = 'bom';



$data;
$type = [];
$type[] = "bom";
$type[] = "classification";

$value_field = [];
$value_field[] = "name";
$value_field[] = "value";
$value_field[] = "count";

//$description = explode(",",$description_source);

// $username = "kozarevr";

// $password = "raider8710/RADO5";

//
//WE DONT USE THIS
//
$username = "greshka";
$password = "greshk2";
$url_classiffication = "http://mu-webpdmapip:4445/api/Keytech/GetClassification?itemName=".$id;
$url_bom = "http://mu-webpdmapip:4445/api/Keytech/GetBom?itemName=".$id;
$array_field_search = "description";

$get_keytech_content_bom =  getUrl($url_bom,$username,$password);
$get_keytech_content_classiffication =  getUrl($url_classiffication,$username,$password);

$merged_json = json_encode(array_merge(json_decode($get_keytech_content_bom, true),json_decode($get_keytech_content_classiffication , true)));



$json_array = json_decode($merged_json,true);
// echo ('<pre>');
// print_r($json_array); // JSON BOM and SAP
// print_r($description);
// print_r($type);
// print_r($value_field);
// print_r($json_array[$type[0]]); // BOM
// print_r($json_array[$type[1]]); // SAP



switch ($input_type) {
    case $bom:
        // print_r($json_array[$type[0]]); // SAP
        $key = array_search($name, array_column($json_array[$type[0]], 'description')); //searching name's key
        // print_r($key); // check key
        /** if name is not found in classification */
        if(empty($key) && $key!==0) {
            echo json_encode('name not found');
            exit();
        }
        $data = $json_array[$type[0]][$key]['name']; // print only array fuled where is searvhed name
        break;
        
        case $classification:
            // print_r($json_array[$type[1]]); // SAP
            $key = array_search($name, array_column($json_array[$type[1]], 'name')); //searching name's key
            // print_r($key); // check key
            /** if name is not found in classification */
            if(empty($key)) {
                echo json_encode('name not found');
                exit();
            }
            $data = $json_array[$type[1]][$key]['value']; // print only array fuled where is searvhed name
            break;
            
            default:
            echo ('invalid type. Type must be '.$bom.' or '.$classification);
            exit();
            break;
}
        
        // echo ('</pre>');
        echo json_encode($data);


    
        
        
?>
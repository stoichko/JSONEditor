<?php
/** By ID names retuns the value 
 * 
 * 
 * 
 */

/** take ID - search in this ID */

if (isset($_POST['id']))
    $id = $_POST['id'];

$input_search = "name";


if (isset($_POST['names']))
    $names = $_POST['names'];


function connect($id)
{
    require_once('../../config.php');
    $url_classiffication = $keytechFormatSketchUrlClassiffication . $id;

    $get_keytech_content =  getUrl($url_classiffication, $keytechFormatSketchUsername, $keytechFormatSketchPassword);
    $dummy = (array)json_decode($get_keytech_content);
    //convert object to array
    foreach ($dummy['classification'] as $obj) {
        $get_keytech_content_classiffication[] = (array) $obj;
    }

    return $get_keytech_content_classiffication;
}



function getUrl($url, $username = false, $password = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    if ($username && $password) {
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    }

    $buffer = curl_exec($ch);
    curl_close($ch);

    return $buffer;
}

// strings
$classification = 'classification';

$arr_names = explode(',', $names);
$outside_api = connect($id); //array beacuse outside api returns a object.
// print_r($outside_api);
$data = [];
foreach ($arr_names as $value) {
    $key = array_search($value, array_column($outside_api, $input_search));
    $arr_classification_result = [
        $value => $outside_api[$key]["value"]
    ];
    // print_r($arr_classification_result);
    $data = array_merge($data, $arr_classification_result);
}

if (empty($key)) {
    echo json_encode('name not found');
    exit();
}

echo json_encode($data);

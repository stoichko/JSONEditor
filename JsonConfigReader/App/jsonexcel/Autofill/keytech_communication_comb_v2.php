<?php
/** api to read information from keytech */

/** take ID - search in this ID */
if (isset($_POST['id']))
    $id = $_POST['id'];
else
    $id = $_GET['id'];

/** take type - 
 * can be "bom" or "classification" -
 *  what we are looking for */
if (isset($_POST['type']))
    $input_type = $_POST['type'];
else $input_type = $_GET['type'];

/**search by what parameter - 
 * can be "name" or "description" */
if (isset($_POST['search_by']))
    $input_search = $_POST['search_by'];
else $input_search = $_GET['search_by'];

/** names - string of names/description(deppends ot search_by) separated by comma that we are looking for. 
 * Can be more than 1!!!,
 *   "name1,name2,name3" or 
 * "description1,description2"*/
if (isset($_POST['names']))
    $names = $_POST['names'];
else $names = $_GET['names'];

/** field to return 
 * for bom - name or description or count
 * for classification - value
 */
if (isset($_POST['return']))
    $output_result = $_POST['return'];
else $output_result = $_GET['return'];

function connect($id, $input_type)
{
    require('\\\\muwo-jedi-q.multivac.int\\wwwroot\\JSONEditor\\JsonConfigReader\\ConfigurationSettings\\config.php');
    $url_classiffication = $keytechUrl_Classiffication . $id;
    $url_bom = $keytechUrl_Bom . $id;

    if ($input_type == 'bom') {
        $get_keytech_content =  getUrl($url_bom, $keytechUsername, $keytechPassword);
        $dummy = (array)json_decode($get_keytech_content);
        //convert object to array
        $get_keytech_content_bom = array();
        echo $get_keytech_content_bom;
        foreach ($dummy['bom'] as $obj) {
            $get_keytech_content_bom[] = (array) $obj;
        }

        return $get_keytech_content_bom;
    } else {
        $get_keytech_content =  getUrl($url_classiffication, $keytechUsername, $keytechPassword);
        $dummy = (array)json_decode($get_keytech_content);
        //convert object to array
        $get_keytech_content_bom = array();
        foreach ($dummy['classification'] as $obj) {
            $get_keytech_content_classiffication[] = (array) $obj;
        }

        return $get_keytech_content_classiffication;
    }
}

connect($id,$input_type);

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

$classification = 'classification';
$bom = 'bom';

$arr_names = explode(',', $names);

switch ($input_type) {
    case $bom:

        $outside_api = connect($id, $input_type); //array beacuse outside api returns a object.
        $data = [];
        foreach ($arr_names as $value) {
            $key = array_search($value, array_column($outside_api, $input_search));
            $arr_bom_result = [
                $value => $outside_api[$key][$output_result]
            ];
            array_push($data, $arr_bom_result);
        }
        $data = $data[0];

        /** if name is not found in classification */
        if (empty($key) && $key !== 0) {
            echo json_encode('name not found');
            exit();
        }

        break;

    case $classification:
        $outside_api = connect($id, $input_type); //array beacuse outside api returns a object.
        $data = [];
        foreach ($arr_names as $value) {
            $key = array_search($value, array_column($outside_api, $input_search));
            $arr_classification_result = [
                $value => $outside_api[$key][$output_result]
            ];
            $data = array_merge($data, $arr_classification_result);
        }

        if (empty($key) && $key !== 0) {
            echo json_encode('name not found');
            exit();
        }
        break;

    default:
        echo ('invalid type. Type must be ' . $bom . ' or ' . $classification);
        exit();
        break;
}

echo json_encode($data);

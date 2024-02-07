<?php

//$data = file_get_contents('php://input');
$data = file_get_contents("basic_dieset_json_raw_new.json");
require "DSU_RFID_get_info.php";
// /** create entry in wordpress */


// require '../../wp-load.php';
// $formId = 23;
// $consumer_key    = 'ck_aebc0960590806fbd427ec58978d790c24bb1eae';
// $consumer_secret = 'cs_34fb540b5bb9e296e892c5689aa453ca54d8346d';
// $url             = "http://muwo-jedi-q.multivac.int/wp-json/gf/v2/forms/$formId/entries";
// $method          = 'POST';
// $args            = array();

// // Use helper to get oAuth authentication parameters in URL.
// // Download helper library from: https://s22280.pcdn.co/wp-content/uploads/2017/01/class-oauth-request.php_.zip
// require_once( 'class-oauth-request.php' );
// $oauth = new OAuth_Request( $url, $consumer_key, $consumer_secret, $method, $args );
 
// // Form to be created.
// $form = array( 'title' => 'Form title' );
$data_decode = json_decode($data, true);

print_r(creatJSON($$data_decode));
    
die();


$arr = array(1234,111,444,"pp");
$entries = array(
    
    'date_created' => '2023-11-14 08:08:08',
    'is_starred'   => 0,
    'is_read'      => 1,
    'created_by'   => 18,
    "63"           => 'API_User',
    "64"           => 999999,
    '15'           => '2023-11-14 08:08:08',
    "113" =>  $arr
    
);


 
// Send request.
$response = wp_remote_request( $oauth->get_url(),
    array(
        'method'  => $method,
        // 'body'    => json_encode( $form ),
        'body'    => json_encode( $entries ),
        'headers' => array( 'Content-type' => 'application/json' ),
    )
);
 
 
$result = wp_remote_retrieve_response_code( $response );
$resp_remote = wp_remote_retrieve_body( $response );


// Check the response code.
if ( $result != 200 || ( empty( $resp_remote ) ) )
{
    // If not a 200, HTTP request failed.
    echo $result;
    echo $resp_remote;
    die( '<br />There was an error attempting to access the API. 1' );
}

die();


//json encode array
$entry_json = json_encode( $entries );
// Define the URL that will be accessed.
// $url = "http://muwo-jedi-q.multivac.int/wp-json/gf/v2/forms/23/entries";
//print_r($url);
 

//API_User akaunta
$api_key = 'ck_aebc0960590806fbd427ec58978d790c24bb1eae';
$private_key = 'cs_34fb540b5bb9e296e892c5689aa453ca54d8346d';


$headers  = array( 'Authorization' => 'Basic ' . base64_encode( $api_key.":".$private_key ) );
$response = wp_remote_get( 'http://muwo-jedi-q.multivac.int/wp-json/gf/v2/forms/23/entries', array( 'headers' => $headers ) );

 
// Make the request to the API.
//$response = wp_remote_get( 'http://muwo-jedi-q.multivac.int/wp-json/gf/v2/forms/23/entries', $args );
 
$result = wp_remote_retrieve_response_code( $response );
$resp_remote = wp_remote_retrieve_body( $response );

// Check the response code.
if ( $result != 200 || ( empty( $resp_remote ) ) ){
// If not a 200, HTTP request failed.
echo $result;
echo $resp_remote;

die( '<br />There was an error attempting to access the API.' );
}
 
// Result is in the response body and is json encoded.
$body = json_decode( wp_remote_retrieve_body( $response ), true );
 
// Check the response body.
if( $body['status'] == 202 ){
die( "Could not retrieve forms." );
}
 
// Entries retrieved successfully.
$entries = $body['response'];






/** send the POST data to 3-th party from form */


<?php


/***********************************TESTING PAGE************************************ */


// require "mongo_read_insert_update.php";
$data = file_get_contents("json_tfp_raw.json");
function json_formating ($data){
    $true = 1;
    $false = 0;
    $decode = json_decode($data, true);
    /**if filed staty with "a" => is array => convert string into array with separator comma */
    foreach ($decode as $field_key => $value) {
        
        //* 'al' means Array of Long; 'ai' means Array of int
        switch (substr($field_key,0,2)) {
            case 'al':
                $str_arr = array_map('intval', explode(',', $value));
                $decode[$field_key] = $str_arr;
                break;
            
            case 'ai':
                $str_arr = array_map('intval', explode(',', $value));
                $decode[$field_key] = $str_arr;
                break;            
        }
        if($field_key == 'bEndOfFile'){
            if($value == 'true'){
                $decode[$field_key] = true;
            } else $decode[$field_key] = false;
        } 
    }


    return json_encode($decode);
}

print_r (json_formating($data));


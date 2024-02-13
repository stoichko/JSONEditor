<?php
// Require the jsonReaderService.php to use getValueFromKey function
require_once __DIR__ . '/../app/Services/jsonReaderService.php';

////The base configuration for WordPress
$wordPressDbName = JsonReader::getValueFromKey("dev","wordpressCredentials","DB_NAME");
$wordPressDbUser = JsonReader::getValueFromKey("dev","wordpressCredentials","DB_USER");
$wordPressDbPassword = JsonReader::getValueFromKey("dev","wordpressCredentials","DB_PASSWORD");
$wordPressDbHost = JsonReader::getValueFromKey("dev","wordpressCredentials","DB_HOST");
$wordPressDbCharset = JsonReader::getValueFromKey("dev","wordpressCredentials","DB_CHARSET");
$wordPressDbCollate = JsonReader::getValueFromKey("dev","wordpressCredentials","DB_COLLATE");
$wordPressDbAuthKey = JsonReader::getValueFromKey("dev","wordpressCredentials","AUTH_KEY");
$wordPressDbSecureAuthKey = JsonReader::getValueFromKey("dev","wordpressCredentials","SECURE_AUTH_KEY");
$wordPressLoggedInKey = JsonReader::getValueFromKey("dev","wordpressCredentials","LOGGED_IN_KEY");
$wordPressNonceKey= JsonReader::getValueFromKey("dev","wordpressCredentials","NONCE_KEY");
$wordPressAuthSalt = JsonReader::getValueFromKey("dev","wordpressCredentials","AUTH_SALT");
$wordPressSecureAuthSalt = JsonReader::getValueFromKey("dev","wordpressCredentials","SECURE_AUTH_SALT");
$wordPressLoggedInSalt = JsonReader::getValueFromKey("dev","wordpressCredentials","LOGGED_IN_SALT");
$wordPressNonceSalt = JsonReader::getValueFromKey("dev","wordpressCredentials","NONCE_SALT");

//Mongo Credentials
$mongoServerName = JsonReader::getValueFromKey("dev","mongoCredentials","servername");
$mongoUsername = JsonReader::getValueFromKey("dev","mongoCredentials","username");
$mongoPassword = JsonReader::getValueFromKey("dev","mongoCredentials","password");
$mongoDb = JsonReader::getValueFromKey("dev","mongoCredentials","db");

//MSSQL Credentials
$mssqlServerName = JsonReader::getValueFromKey("dev","mssql","servername");
$mssqlDatabaseName = JsonReader::getValueFromKey("dev","mssql","database");
$mssqlUsername = JsonReader::getValueFromKey("dev","mssql","username");
$mssqlPassword = JsonReader::getValueFromKey("dev","mssql","password");
$mssqlTable = JsonReader::getValueFromKey("dev","mssql","table");

//Keytech Communication
 $keytechUsername =  JsonReader::getValueFromKey("dev","keytech","keytech_communication_comb_v2.php.username");
 $keytechPassword = JsonReader::getValueFromKey("dev","keytech","keytech_communication_comb_v2.php.password");
 $keytechUrl_Classiffication = JsonReader::getValueFromKey("dev","keytech","url_classiffication");
 $keytechUrl_Bom = JsonReader::getValueFromKey("dev","keytech","url_bom");

 //Keytech Format Sketch
 $keytechFormatSketchUsername = JsonReader::getValueFromKey("dev","keytech","keytech_format_sketch.username");
 $keytechFormatSketchPassword = JsonReader::getValueFromKey("dev","keytech","keytech_format_sketch.password");
 $keytechFormatSketchUrlClassiffication = JsonReader::getValueFromKey("dev","keytech","url_classiffication")
?>
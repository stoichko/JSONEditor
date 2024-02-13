<?php
// api.php
require_once 'Services/jsonReaderService';

if (isset($_GET['action']) && $_GET['action'] === 'getValueForKey') {
    $site = isset($_GET['site']) ? $_GET['site'] : null;
    $creds = isset($_GET['creds']) ? $_GET['creds'] : null;
    $keyPath = isset($_GET['keyPath']) ? $_GET['keyPath'] : null;

    echo JsonReader::getValueFromKey($site, $creds, $keyPath);
    exit();
    
}
?>

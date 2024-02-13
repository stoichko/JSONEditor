<?php
//Reads the data from jsom file /
class JsonReader {
    /**
 * Retrieve a value from a JSON file based on Environment, Area , and Key Path of the value.
 * Usually keypath have the same name as the key we are looking value for.
 * 
 * @param string $site      Environment.
 * @param string $creds      Area.
 * @param string $keyPath    Key Path
 *
 * @return string|false      The retrieved value or "No match" if not found.ö
 */
    public static function getValueFromKey($site,$creds,$keyPath) : string {
        
        $filePath = file_get_contents(__DIR__ . '/../../conf/appSettings.json');
        $data = json_decode($filePath, true);

        if (isset($data[$site]) && is_array($data[$site])) {
            // Check if $creds exists and is an array
            if (isset($data[$site][$creds]) && is_array($data[$site][$creds])) {
                // Check if $keyPath exists
                if (isset($data[$site][$creds][$keyPath])) {
                    $value = $data[$site][$creds][$keyPath];

                    return $value;
                }
            }
        }

        return 'No match';
    }

/**
 * Returns current version of the appSettings.json file
 */
    public static function getVersion() : string {
        $filePath = file_get_contents(__DIR__ . '/../../conf/appSettings.json');
        $data = json_decode($filePath, true);

        return $data['version'];
    }
}
?>

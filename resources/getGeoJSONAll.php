<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require_once 'config.php';
include('db.php');
include('rowsToGeoJson.php');
$name = $_GET['name'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_REGEXP, array(
    "options" => array("regexp" => "/^.{1,3}$/")
));

$db = new db();
$json='';

if ($name == 'kloosters') {
    $cache_file = CACHEPATH . 'kloosters.json';
    if (file_exists($cache_file) && CACHE_ENABLED) {
        $json = file_get_contents($cache_file);
    } else {
        $json = json_encode(createMinimalGeoJSON($db->getAllKloosterLocations(), 'klooster'));
        file_put_contents($cache_file, $json);
    }
} elseif ($name == 'single') {
    if ($id) {
        $cache_file = CACHEPATH . $id . '.json';
        if (file_exists($cache_file) && CACHE_ENABLED) {
            $json = file_get_contents($cache_file);
        } else {
            $json_object = mergeGeoJSON(
                    createMinimalGeoJSON($db->getKloosterLocations($id), 'klooster'),
                    createMinimalGeoJSON($db->getUithovenByKloosterId($id), 'uithof')
            );
            if ($json_object) {
                $json=json_encode($json_object);
                file_put_contents($cache_file, $json);
            }
        }
    }
} elseif ($name == 'uithoven') {
    $cache_file = CACHEPATH . 'uithoven.json';
    if (file_exists($cache_file) && CACHE_ENABLED) {
        $json = file_get_contents($cache_file);
    } else {
        $json = json_encode(createMinimalGeoJSON($db->getUithoven(), 'uithof'));
        file_put_contents($cache_file, $json);
    }
} elseif ($name == 'kapittels') {
    $cache_file = CACHEPATH . 'kapittels.json';
    if (file_exists($cache_file) && CACHE_ENABLED) {
        $json = file_get_contents($cache_file);
    } else {
        $json = json_encode(createMinimalGeoJSON($db->getKapittels(), 'kapittel'));
        file_put_contents($cache_file, $json);
    }
}
header('Content-Type: application/json');
echo $json;

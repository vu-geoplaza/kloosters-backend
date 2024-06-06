<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

include('db.php');
include('rowsToGeoJson.php');
$language = filter_input(INPUT_GET, 'language', FILTER_VALIDATE_REGEXP, array(
    "options" => array("regexp" => "/^[a-z]{2}$/")
));
$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_REGEXP, array(
    "options" => array("regexp" => "/^\d{3,4}$/")
));
$type = $_GET['type'];
$db = new db();

if ($type=='kloosters_by_year') {
    $cache_file = CACHEPATH . $year . '-donwload.json';
    if (file_exists($cache_file) && CACHE_ENABLED) {
        $json = file_get_contents($cache_file);
    } else {
        $list=[];
        $l = $db->getByPeriod($year, $year);
        foreach ($l as $r){
            $id=$r['idL'];
            $data = $db->getInfo($id, $language);
            $data['locations']=$db->getLocationsArrayById($id);
            $data['AM']=$db->lookupAM($data['AM']);
            array_push($list, $data);
        }
        $json = json_encode(createMaximalGeoJSON($list, 'klooster', $language));
        file_put_contents($cache_file, $json);
    }
} elseif ($type=='kloosters') {
    $cache_file = CACHEPATH . 'kloosters-donwload.json.json';
    if (file_exists($cache_file) && CACHE_ENABLED) {
        $json = file_get_contents($cache_file);
    } else {
        $list=[];
        $l = $db->getAllKloosterLocations();
        foreach ($l as $r){
            $id=$r['idL'];
            $data = $db->getInfo($id, $language);
            $data['locations']=$db->getLocationsArrayById($id);
            $data['AM']=$db->lookupAM($data['AM']);
            array_push($list, $data);
        }
        $json = json_encode(createMaximalGeoJSON($list,'klooster', $language));
        file_put_contents($cache_file, $json);
    }
} elseif ($type=='uithoven'){
    $cache_file = CACHEPATH . 'uithoven-donwload.json.json';
    if (file_exists($cache_file) && CACHE_ENABLED) {
        $json = file_get_contents($cache_file);
    } else {
        $list = $db->getAllUithofInfo($language);
        $json = json_encode(createMaximalGeoJSON($list,'uithof', $language));
        file_put_contents($cache_file, $json);
    }
} elseif ($type=='kapittels') {
    $cache_file = CACHEPATH . 'kapittels-donwload.json.json';
    if (file_exists($cache_file) && CACHE_ENABLED) {
        $json = file_get_contents($cache_file);
    } else {
        $list = $db->getAllKapittelInfo($language);
        $json = json_encode(createMaximalGeoJSON($list,'kapittel', $language));
        file_put_contents($cache_file, $json);
    }
}
header("Content-disposition: attachment; filename=\"$type$year.json\"");
header('Content-Type: application/json');
echo pretty_json($json);

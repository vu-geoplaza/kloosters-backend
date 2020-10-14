<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

include 'config.php';
include('db.php');
include('rowsToGeoJson.php');
$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_REGEXP, array(
    "options" => array("regexp" => "/^\d{3,4}$/")
));

$db = new db();

$cache_file = CACHEPATH . $year . '.json';
if (file_exists($cache_file) && CACHE_ENABLED) {
    $json=file_get_contents($cache_file);
} else {
    $list = $db->getByPeriod($year, $year);
    $data = createMinimalGeoJSON($list,'klooster');

    $list = $db->getKapittelsByPeriod($year);
    $ka_data = createMinimalGeoJSON($list,'kapittel');
    foreach($ka_data->features as $feature){
        array_push($data->features, $feature);
    }
    $json = json_encode($data);
    file_put_contents($cache_file, $json);
}
header('Content-Type: application/json');
echo $json;

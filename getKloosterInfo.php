<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include('db.php');
include('rowsToGeoJson.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_REGEXP, array(
    "options" => array("regexp" => "/^.{1,5}$/")
));
$language = filter_input(INPUT_GET, 'language', FILTER_VALIDATE_REGEXP, array(
    "options" => array("regexp" => "/^[a-z]{2}$/")
));
$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_REGEXP, array(
    "options" => array("regexp" => "/^\d{4}$/")
));
$type = $_GET['type'];

$db = new db();
if ($type=='uithof'){
    $data = $db->getUithofInfo($id, $language);
}elseif ($type=='kapittel'){
    $data = $db->getKapittelInfo($id, $language);
} else {
    $data = $db->getInfo($id, $language);
    $data['locations']=$db->getLocationsArrayById($id);
    $data['AM']=$db->lookupAM($data['AM']);
}


header('Content-Type: application/json');
$list[0]=$data;
echo json_encode(createMaximalGeoJSON($list,$type, $language, $year));



//echo '</pre>';
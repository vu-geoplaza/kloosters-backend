<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

include 'config.php';
include('db.php');
include('rowsToGeoJson.php');
$pdata = file_get_contents('php://input');
$post = json_decode($pdata, true);
$begin = $post['begin'];
$end = $post['end'];

$db = new db();

$cache_file = CACHEPATH . $begin . '.json';
if (file_exists($cache_file) && CACHE_ENABLED) {
    $json=file_get_contents($cache_file);
} else {
    $list = $db->getByPeriod($begin, $end);
    $json = json_encode(createMinimalGeoJSON($list,'klooster'));
    file_put_contents($cache_file, $json);
}
header('Content-Type: application/json');
echo $json;

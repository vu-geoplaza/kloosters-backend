<?php
Function createMaximalGeoJSON($l, $type, $langcode, $year = 2000)
{
    $kloosterlijst_baseurl = 'https://www2.fgw.vu.nl/oz/kloosterlijst/';
    $lang = json_decode(file_get_contents(__DIR__ . '/lang.json'));
    $geo = new stdClass();
    $geo->type = "FeatureCollection";
    $geo->attribution = 'Goudriaan, K. (2017). Kloosterkaart en Kloosterlijst [Data set]. Retrieved on ' . date("F j, Y") . ', from https://geoplaza.vu.nl/projects/kloosters';
    $geo->features = array();
    $n = 0;
    foreach ($l as $row) {
        $geo->features[$n] = new stdClass();
        $geo->features[$n]->type = "Feature";
        $geo->features[$n]->geometry = new stdClass();
        $geo->features[$n]->properties = new stdClass();
        $geo->features[$n]->geometry->type = "Point";
        $geo->features[$n]->properties->type = $type;
        if ($type == 'klooster') {
            $tmpArr = array();
            $i = 0;
            foreach ($row['locations'] as $locArray) {
                $locstring = $locArray['lat'] . ' , ' . $locArray['lon'] . ' (' . $locArray['year'] . ')';
                if ($locArray['year'] <= $year) {
                    $geo->features[$n]->geometry->coordinates[0] = $locArray['lon'];
                    $geo->features[$n]->geometry->coordinates[1] = $locArray['lat'];
                    $current_location = $locstring;
                }
                array_push($tmpArr, $locstring);
            }
            $locations = array();
            foreach ($tmpArr as $l) {
                if ($l === $current_location) {
                    // highlight current location
                    array_push($locations, $l . ' *');
                } else {
                    array_push($locations, $l);
                }
            }
            $row['CO'] = implode('<br>', $locations);
            $row['CL'] = $current_location;
            $geo->features[$n]->properties->id = $row['ID'];
            foreach ($row as $key => $value) {
                if (isset($lang->{$key})) {
                    $field = $lang->{$key}->{$langcode};
                    $geo->features[$n]->properties->{$field} = $value;
                }
            }
            $geo->features[$n]->properties->kl_url = $kloosterlijst_baseurl . 'kdetails.php?ID=' . $row['ID'];
            $geo->features[$n]->properties->photo_url = $kloosterlijst_baseurl . 'foto/' . $row['ID'] . '.JPG';
            $geo->features[$n]->properties->photo_caption = $row['FO'];
        } else if ($type == 'kapittel') {
            $geo->features[$n]->properties->id = $row['Idnr'];
            $geo->features[$n]->geometry->coordinates[0] = (double)floatval(str_replace(",", ".", $row['Lengte_dec']));
            $geo->features[$n]->geometry->coordinates[1] = (double)floatval(str_replace(",", ".", $row['Breedte_dec']));
            if ($langcode == 'en') {
                $geo->features[$n]->geometry->coordinates[0] = (double)floatval(str_replace(",", ".", $row['Longitude_dec']));
                $geo->features[$n]->geometry->coordinates[1] = (double)floatval(str_replace(",", ".", $row['Latitude_dec']));
            }
            foreach ($row as $key => $value) {
                $geo->features[$n]->properties->{$key} = $value;
            }
        } else if ($type == 'uithof') {
            $geo->features[$n]->geometry->coordinates[0] = (double)floatval(str_replace(",", ".", $row['Lengte_dec']));
            $geo->features[$n]->geometry->coordinates[1] = (double)floatval(str_replace(",", ".", $row['Breedte_dec']));
            if ($langcode == 'en') {
                $geo->features[$n]->geometry->coordinates[0] = (double)floatval(str_replace(",", ".", $row['Longitude_dec']));
                $geo->features[$n]->geometry->coordinates[1] = (double)floatval(str_replace(",", ".", $row['Latitude_dec']));
            }
            $geo->features[$n]->properties->id = $row['id_ur'];
            foreach ($row as $key => $value) {
                $geo->features[$n]->properties->{$key} = $value;
            }
        }
        $n++;
    }


    return $geo;
}

Function createMinimalGeoJSON($l, $type)
{
    $geo = new stdClass();
    $geo->type = "FeatureCollection";
    /*
    $geo->crs = new stdClass();
    $geo->crs->properties = new stdClass();
    $geo->crs->properties->name = 'urn:ogc:def:crs:EPSG::4326';
    $geo->crs->type = 'name';
    */
    $geo->features = array();
    $n = 0;
    foreach ($l as $row) {
        if ($type == 'klooster') {
            $lon = (double)$row['lon'];
            $lat = (double)$row['lat'];
        } else {
            $lon = (double)floatval(str_replace(",", ".", $row['Lengte_dec']));
            $lat = (double)floatval(str_replace(",", ".", $row['Breedte_dec']));
        }
        if ($lon > 0) { // skip features in "0 island"
            $geo->features[$n] = new stdClass();
            $geo->features[$n]->type = "Feature";
            $geo->features[$n]->geometry = new stdClass();
            $geo->features[$n]->geometry->type = "Point";
            $geo->features[$n]->properties = new stdClass();
            $geo->features[$n]->properties->type = $type;
            $geo->features[$n]->geometry->coordinates[0] = $lon;
            $geo->features[$n]->geometry->coordinates[1] = $lat;
            if ($lon > $lat) { // typos
                $geo->features[$n]->geometry->coordinates[0] = $lat;
                $geo->features[$n]->geometry->coordinates[1] = $lon;
            }
            if ($type == 'klooster') {
                $geo->features[$n]->properties->id = $row['idL'];
                $geo->features[$n]->properties->val = $row['VAL'];
                $geo->features[$n]->properties->name_nl = $row['TI'];
                $geo->features[$n]->properties->name_en = $row['TIE'];
                $r = cleanKR($row['KR']);
                $o = cleanST($row['ST'], $r);
                $geo->features[$n]->properties->ordenaam = $o;
                $geo->features[$n]->properties->regel = $r;
                $geo->features[$n]->properties->type = "klooster";
            } else if ($type == 'kapittel') {
                $geo->features[$n]->properties->id = $row['Idnr'];
                $geo->features[$n]->properties->ordenaam = "kapittel";
                $geo->features[$n]->properties->regel = "Kapittels";
                $geo->features[$n]->properties->type = "kapittel";
                $geo->features[$n]->properties->name_nl = $row['Plaats'].', '.$row['Locatie'];
                $geo->features[$n]->properties->name_en = $row['Plaats'].', '.$row['Locatie']; // use english table!
                //$geo->features[$n]->properties->s = $row['Sticht'];
                //$geo->features[$n]->properties->o = $row['Opheffing'];
            } else if ($type == 'uithof') {
                $geo->features[$n]->properties->klooster_id = $row['idnr_klooster'];
                $geo->features[$n]->properties->id = $row['id_ur'];
            }
            $n++;
        }
    }
    return $geo;
}

function mergeGeoJSON($json, $json2)
{
    foreach ($json2->features as $feature) {
        array_push($json->features, $feature);
    }
    if (count($json->features) == 0) {
        return false;
    } else {
        return $json;
    }
}

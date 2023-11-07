<?php

require_once 'config.php';


/**
 * Description of db
 *
 * @author peter
 */
class db
{

    function __construct()
    {
        $this->dbh = new PDO("mysql:host=" . DBHOST . ";dbname=" . DB . ";port=". DBPORT, DBNAME, DBPW) or die('connection failed');
    }

    /**
     * Do the query with parameters Orde, Regel, Start and End year. In practice we use the same value start and end.
     *
     *
     * @param string $begin Start of period
     * @param string $end End of period
     * @return array Array of database result rows.
     */
    Function getByPeriod($begin, $end)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");

        $cond = '';
        $param = array(':begin' => $begin, ':end' => $end);
        $sql = "
          SELECT L.idL,L.VAL,O.ST,O.KR,L.lon,L.lat,K.TI,KE.TI AS TIE
          FROM Kloosterlijst K
              INNER JOIN (
                  SELECT ll.* FROM Lokatie ll
                  INNER JOIN (
                      SELECT idL,MAX(VAL) VAL
                      FROM Lokatie
                      WHERE VAL <= :end
                      GROUP BY idL
                    ) gl ON ll.idL=gl.idL AND ll.VAL=gl.VAL
              ) L ON L.idL=K.id
              INNER JOIN (
                SELECT oo.* FROM Orde oo
                INNER JOIN (
                    SELECT ID, MAX(VAO) VAO
                    FROM Orde
                    WHERE VAO <= :end
                    GROUP BY ID
                ) go ON oo.id=go.id AND oo.VAO=go.VAO
              ) O ON O.ID=K.id
              INNER JOIN (  
                SELECT ke.* FROM KloosterlijstEng ke
              ) KE ON KE.id=K.id
          WHERE L.VAL <= :end AND K.LV >= :begin ORDER BY L.idL";
        $sth = $this->dbh->prepare($sql);
        $sth->execute($param);
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    function getKapittels()
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        $sql = "SELECT * FROM Kapittels";
        error_log($sql);
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    function getKapittelsByPeriod($year)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        $sql = "SELECT * FROM Kapittels nl JOIN KapittelsEng en on nl.Idnr=en.Idnr WHERE nl.`Sticht Exact`<=$year AND nl.`Opheffing Exact`>=$year";
        error_log($sql);
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    function getUithoven()
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        $sql = "SELECT * FROM Uithoven";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    function getUithovenByKloosterId($kloosterId)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        $sql = "SELECT * FROM Uithoven WHERE idnr_klooster='$kloosterId'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    function getUithoven2($id)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        //'H27 /O23'
        $sql = "SELECT * FROM Uithoven WHERE idnr_klooster LIKE '%" . $id . "%'";
        error_log($sql);
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllInfo($language)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        if ($language == 'en') {
            $sql = "SELECT * FROM KloosterlijstEng";
        } else {
            $sql = "SELECT * FROM Kloosterlijst";
        }
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array(":id" => $id));
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res[0];
    }

    function getInfo($id, $language)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        if ($language == 'en') {
            $sql = "SELECT * FROM KloosterlijstEng WHERE id=:id";
        } else {
            $sql = "SELECT * FROM Kloosterlijst WHERE id=:id";
        }
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array(":id" => $id));
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res[0];
    }

    function getAllUithofInfo($language)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        if ($language == 'en') {
            $sql = "SELECT * FROM UithovenEng";
        } else {
            $sql = "SELECT * FROM Uithoven";
        }
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    function getUithofInfo($id, $language)
    {
        error_log('******************** id: ' . $id);
        $this->dbh->exec("SET CHARACTER SET utf8");
        if ($language == 'en') {
            $sql = "SELECT * FROM UithovenEng WHERE id_ur=:id";
        } else {
            $sql = "SELECT * FROM Uithoven WHERE id_ur=:id";
        }
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array(":id" => $id));
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res[0];
    }

    function getAllKapittelInfo($language)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        if ($language == 'en') {
            $sql = "SELECT * FROM KapittelsEng";
        } else {
            $sql = "SELECT * FROM Kapittels";
        }
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    function getKapittelInfo($id, $language)
    {
        error_log('******************** id: ' . $id);
        $this->dbh->exec("SET CHARACTER SET utf8");
        if ($language == 'en') {
            $sql = "SELECT * FROM KapittelsEng WHERE Idnr=:id";
        } else {
            $sql = "SELECT * FROM Kapittels WHERE Idnr=:id";
        }
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array(":id" => $id));
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res[0];
    }

    Function getAllKloosterLocations()
    {
        $sql = "select * from Lokatie";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    Function getKloosterLocations($id)
    {
        $sql = "select * from Lokatie where idL='$id'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    Function getLocationsStringById($id)
    {
        $sql = "select * from Lokatie where idL='$id'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute($param);
        $locations = $sth->fetchAll(PDO::FETCH_ASSOC);

        if (count($locations) > 1) {
            foreach ($locations as $row) {
                $lon = decToDeg((double)$row['lon'], false);
                $lat = decToDeg((double)$row['lat'], true);
                $year = $row['VAL'];
                $coords[$n] = "$lat, $lon ($year)";
                $n++;
            }
            $co = implode('<br>', $coords);
        } else {
            $row = $locations[0];
            $lon = decToDeg((double)$row['lon'], false);
            $lat = decToDeg((double)$row['lat'], true);
            $year = $row['VAL'];
            $co = "$lat, $lon";
        }
        return $co;
    }

    Function getLocationsArrayById($id)
    {
        $sql = "select * from Lokatie where idL='$id' order by VAL ASC";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $locArray = array();
        $locations = $sth->fetchAll(PDO::FETCH_ASSOC);
        $n = 0;
        foreach ($locations as $row) {
            $locArray[$n]['lon'] = (double)$row['lon'];
            $locArray[$n]['lat'] = (double)$row['lat'];
            $locArray[$n]['year'] = (integer)$row['VAL'];
            //$locArray[$n]['coords'] = $locArray[$n]['lon'] . ", " . $locArray[$n]['lat'] . " (" . $locArray[$n]['year'] . ")";
            $n++;
        }

        return $locArray;
    }

    function lookupAM($val)
    {
        if (strpos($val, 'zie') > -1) { // e.g. "zie A23"
            preg_match("/[A-Z]\d{2}/", $val, $match); // parse ID
            $d = $this->getInfo($match[0], $dbh);
            return $d['AM'];
        }
        return $val;
    }

    Function getLocationsById($id)
    {
        $this->dbh->exec("SET CHARACTER SET utf8");
        $sql = "select * from Lokatie where idL='$id'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    Function getFirstOrdeById($id)
    {
        $sql = "select o1.ID,o1.ST,o1.VAO from Orde o1 inner join (
            Select ID,MIN(VAO) VAO From Orde Group By ID
            ) o2 ON o1.ID=o2.ID AND o1.VAO=o2.VAO WHERE o1.ID='$id'";
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(PDO::FETCH_ASSOC);
        return $res;
    }


}

/**
 * Clean some of the "regel" categories
 *
 * @param type $KR
 * @return string The normalized string
 */
function cleanKR($KR)
{
    switch ($KR) {
        case 'derde regel van Francisus':
        case 'derde regel van Francsicus':
        case 'derde regel van Franciscus ?':
        case 'derde regel van Francisciscus':
            $r = 'derde regel van Franciscus';
            break;
        case 'derde regel van Augustinus':
            $r = 'regel van Augustinus';
            break;
        case 'regel van Benedictus ?':
            $r = 'regel van Benedictus';
            break;
        case 'regel van Aken ?':
        case 'regel van Aken';
            $r = 'geen';
            break;
        case 'gen':
            $r = 'geen';
            break;
        case 'regel onbekend':
            $r = 'onbekend'; //???
            break;
        case NULL:
            $r = 'geen'; //???
            break;
        case 'n.v.t.':
            $r = 'geen';
            break;
        case 'begijnen':
            $r = 'geen';
            break;
        default:
            $r = $KR;
    }
    return $r;
}

/**
 *
 * @param type $ST
 * @param type $r regel
 * @return type
 */
function cleanST($ST, $r)
{
    switch ($ST) {
        case 'tweede orde van Sint Dominicus': // I doubt it, but well
        case 'tertiarissen van Sint Dominicus 1447':
        case 'tertiarissen van Sint-Dominicus':
            $o = 'tertiarissen van Sint Dominicus';
            break;
        case 'victorinnen 1246*':
            $o = 'victorinnen';
            break;
        case 'celebroeders':
            $o = 'cellebroeders*';
            break;
        case 'Duitse orde':
            $o = 'Duitse Orde';
            break;
        case 'mannelijke en vrouwelijke religieuzen':
            $o = "broeders en zusters";
            break;
        case 'cellebroeders':
        case 'cellezusters':
        case 'hospitaalzusters':
        case 'seculiere kanunniken':
            if (($r == 'geen') || ($r == 'onbekend')) {
                $o = $ST . '*';
            } else {
                $o = $ST;
            }
            break;
        case 'reguliere kanunniken':
        case 'regulieren (reguliere kanunniken)':
            $o = 'regulieren'; //navragen bij Koen
            break;
        case 'monniken en clerici':
        case 'kluizenaars':
            $o = 'monniken';
            break;
        case 'devoten':
            $o = 'broeders des gemenen levens';
            break;
        case 'bogarden':
            $o = 'begarden';
            break;
        case "uithof van Mari\xebngaarde (H27)":
        case 'uithof van Mariëngaarde (H27)':
            $o = 'norbertijnen';
            break;
        case 'zusters van de Bisweide':
        case 'zuster':
        case 'zusters (cellezusters?)':
            $o = 'zusters';
            break;
        case 'kapucijnen':
            $o = 'capucijnen';
            break;
        case 'voorlopige status':
        case 'conceptionistinnen':
        case 'congregatie van seculiere priesters':
            $o = 'overig';
            break;
        default:
            $o = $ST;
    }
    return $o;
}

function pretty_json($json)
{

    $result = '';
    $pos = 0;
    $strLen = strlen($json);
    $indentStr = ' ';
    $newLine = "\n";
    $prevChar = '';
    $outOfQuotes = true;

    for ($i = 0; $i <= $strLen; $i++) {

// Grab the next character in the string.
        $char = substr($json, $i, 1);

// Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

// If this character is the end of an element,
// output a new line and indent the next line.
        } else if (($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos--;
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

// Add the character to the result string.
        $result .= $char;

// If the last character was the beginning of an element,
// output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}

function decToDeg($coord, $lat)
{
    $ispos = $coord >= 0;
    $coord = abs($coord);
    $deg = floor($coord);
    $coord = ($coord - $deg) * 60;
    $min = floor($coord);
    $sec = floor(($coord - $min) * 60);
    if ($lat) {
        $c = sprintf("%d&deg;%d'%d\"%s", $deg, $min, $sec, $ispos ? 'N' : 'S');
    } else {
        $c = sprintf("%d&deg;%d'%d\"%s", $deg, $min, $sec, $ispos ? 'E' : 'W');
    }
    return $c;
}

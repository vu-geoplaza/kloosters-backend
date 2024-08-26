<?php
// do not commit
Define('DBHOST',getenv('MYSQL_HOST'));
Define('DB',getenv('MYSQL_DATABASE'));
Define('DBNAME',getenv('MYSQL_USER'));
Define('DBPW',getenv('MYSQL_PASSWORD'));
Define('DBPORT',3306);

Define('BRON1', 'Goudriaan, K. (2017). Kloosterlijst en Kloosterkaart ');
Define('BRON2', ' [Data set]. Retrieved ');
Define('BRON3', ', from http://geoplaza.vu.nl/projects/kloosters');
Define('LICENSE', '<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY-NC-SA 4.0</a>');

Define('LIJSTIDURL','kloosterlijst/nl/kdetails.php?ID=');
Define('LIJSTFOTOURL','kloosterlijst/foto/');
Define('KLOOSTERLIJST_BASE_URL','kloosterlijst/');

Define('KAARTIDURL','index.html?id=');
Define('SYMBOLURL','resources/svg/');

define('CACHEPATH', '/tmp/');
define('CACHE_ENABLED', true);
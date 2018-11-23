<?php

require_once '../config.class.php';
require_once '../parameter.class.php';

$pdo = new PDO(Config::get('dsn'), Config::get('dbuser'), Config::get('dbpass'));
$sql = 'update twitter set tag = ? where id_str = ? limit 1';

$array[] = Parameter::getString('tag') == '' ? NULL : Parameter::getString('tag');
$array[] = Parameter::getString('id');

$sth = $pdo->prepare($sql);

$sth->execute($array);

header('Access-Control-Allow-Origin: *');

echo 'ok';

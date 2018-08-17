<?php

require_once 'config.class.php';

$pdo = new PDO(Config::get('dsn'), Config::get('dbuser'), Config::get('dbpass'));
$sql = 'update twitter set tag = ? where id_str = ? limit 1';

$array[] = $_REQUEST['tag'];
$array[] = $_REQUEST['id'];

$sth = $pdo->prepare($sql);

$sth->execute($array);

header('Access-Control-Allow-Origin: *');

echo 'ok';

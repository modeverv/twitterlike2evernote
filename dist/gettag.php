<?php

require_once '../config.class.php';

$pdo = new PDO(Config::get('dsn'), Config::get('dbuser'), Config::get('dbpass'));
$sth = $pdo->prepare('select distinct tag from twitter where tag is not null');

$sth->execute();

$result = $sth->fetchAll();

$json = [];


foreach ($result as $row){
    $json[] = $row['tag'];
}

header('Access-Control-Allow-Origin: *');

echo json_encode($json,JSON_PRETTY_PRINT);


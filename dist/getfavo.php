<?php

require_once '../config.class.php';
require_once '../parameter.class.php';

$pdo = new PDO(Config::get('dsn'), Config::get('dbuser'), Config::get('dbpass'));
$sql = 'select * from twitter';

$array = [];
$tag = Parameter::getString('tag');
if (null != $tag) {
    if ($tag = "::::NULL::::") {
    $sql .= " where tag IS NULL";
    }else{
    $sql .= " where tag = ?";
    $array[] = $tag;
    }
}

$sql .= " order by created_at desc";

//var_dump($sql);exit();

$sth = $pdo->prepare($sql);

$sth->execute($array);

$result = $sth->fetchAll();
$json = [];

foreach ($result as $row) {
    $row['body'] = json_decode($row['body']);
    $json[] = $row;
}

header('Access-Control-Allow-Origin: *');
header('content-type: application/json; charset=utf-8');
echo json_encode($json);

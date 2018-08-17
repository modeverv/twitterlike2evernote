<?php

require_once 'config.class.php';

$pdo = new PDO(Config::get('dsn'), Config::get('dbuser'), Config::get('dbpass'));
$sql = 'select * from twitter';

//$sql = "select * from twitter where txt like '%レシピ%'";

$array = [];
if (isset($_REQUEST['tag'])) {
    $sql .= " where tag = ?";
    $array[] = $_REQUEST['tag'];
}
$sth = $pdo->prepare($sql);

$sth->execute($array);

$result = $sth->fetchAll();
$json = [];

foreach ($result as $row) {
    $row['body'] = json_decode($row['body']);
    $json[] = $row;
}

header('Access-Control-Allow-Origin: *');

echo json_encode($json);

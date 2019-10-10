<?php
require_once '../config.class.php';
require_once '../parameter.class.php';
$pdo = new PDO(Config::get('dsn'), Config::get('dbuser'), Config::get('dbpass'));
$sql = 'select * from twitter';
//$sql .= " where tag IS NULL";
$sql .= " order by created_at DESC LIMIT 300";
//var_dump($sql);exit();
$sth = $pdo->prepare($sql);
$sth->execute($array);
$result = $sth->fetchAll();
$json = [];
foreach ($result as $row) {
    $row['body'] = json_decode($row['body']);
    $json[] = $row;
}
//echo("<pre>");var_dump($json);exit;
header("Content-Type: application/xml; charset=UTF-8");
echo <<<RSS
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:webfeeds="http://webfeeds.org/rss/1.0" xmlns:note="https://note.mu" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/" version="2.0">
  <channel>
    <title>twifavo</title>
    <description>冴美のお気に入り</description>
    <language>ja</language>
RSS;
foreach($json as $e){
    echo "<item>\n";
    echo "<title>" . $e["body"]->text. "</title>\n";
    echo "<description><![CDATA[[" . $e["body"]->text . "]]></description>\n";
    echo "<content:encoded><![CDATA[[<p>" . $e["body"]->text . "</p>\n";
    if(isset($e["body"]->entities->media)){
        foreach($e["body"]->entities->media as $m){
            //echo "<media>" . $m->media_url_https . "</media>\n";
            echo "<img src='". $m->media_url_https ."'/>\n";
        }
    }
    echo "]]></content:encoded>\n";
    echo "<pubDate>" . $e["body"]->created_at . "</pubDate>\n";
    echo "<link>" . "https://twitter.com/" . $e["body"]->user->screen_name . "/status/" . $e["body"]->id_str . "</link>\n";
    echo "</item>\n";
}
echo "</channel></rss>";

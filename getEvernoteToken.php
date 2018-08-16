<?php
require 'vendor/autoload.php';

//set this to false to use in production
$sandbox = false;

$oauth_handler = new \Evernote\Auth\OauthHandler($sandbox);

$key = 'penguinsuras226';
$secret = '78505819ae49a079';
$callback = 'http://host/pathto/evernote-cloud-sdk-php/sample/oauth/index.php';

$oauth_data = $oauth_handler->authorize($key, $secret, $callback);
var_dump($oauth_data);
echo "\nOauth Token : " . $oauth_data['oauth_token'];

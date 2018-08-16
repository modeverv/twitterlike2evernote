<?php
require 'vendor/autoload.php';
require_once 'config.class.php';

$sandbox = true;
$china = false;
$client = new \Evernote\Client(\Config::get('evernote_token'), $sandbox, null, null, $china);
// Create the resource
//$resource = new \Evernote\Model\Resource(
//    'https://dadfpmh61h9tr.cloudfront.net/2018/08/16/1534426103777_thum_167354_20180816_35.jpg',
//    'image/jpeg', 100, 100);
// Get a preformatted enml media tag (something like '<en-media type="%mime%" hash="%hash%" />')
//$enml_media_tag = $resource->getEnmlMediaTag();

// Create the note
$note = new \Evernote\Model\Note();
//$note->addResource($resource);
$note->title = 'Test note';
$note->content = new \Evernote\Model\EnmlNoteContent(
    <<<ENML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">
<en-note>
<h1>aaaaaaa</h1>
</en-note>
ENML
);
$enml_media_tag = "";
// Upload the note
$client->uploadNote($note);

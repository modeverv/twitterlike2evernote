<?php
error_reporting(E_ALL);

require_once "config.class.php";
require_once "twitter.class.php";

// 設定
$api_key = Config::get('twitter_api_key');
$api_secret = Config::get('twitter_api_secret');
$access_token = Config::get('twitter_access_token');
$access_token_secret = Config::get('twitter_access_token_secret');
try{
  $pdo = new PDO(Config::get('dsn'), Config::get('dbuser'), Config::get('dbpass'),array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}catch(Exception $e){
  var_dump($e->getMessage());
}
var_dump($api_key);
var_dump($api_secret);
//var_dump(Config::get('dsn'));
//var_dump(Config::get('dbuser'));
//var_dump(Config::get('dbpass'));
//var_dump($pdo);
//$pdo->prepare("select count(*) as cou from twitter where id_str = '12343'");
//$sth->execute([$tweet_obj->id]);
//$result = $sth->fetchAll();


//exit();

$request_url = 'https://api.twitter.com/1.1/favorites/list.json'; // エンドポイント
$request_method = 'GET';
// パラメータA (オプション)
$params_a = array(
//    "user_id" => "1528352858",
    "screen_name" => Config::get('twitter_screen_name'),
    "count" => "10000",
//        "since_id" => "643299864344788992",
    //        "max_id" => "643299864344788992",
    "include_entities" => "true",
);
if (isset($argv[1])) {
    $params_a['max_id'] = $argv[1];
    echo "max_id is set $argv[1]\n";
}

// キーを作成する (URLエンコードする)
$signature_key = rawurlencode($api_secret) . '&' . rawurlencode($access_token_secret);

// パラメータB (署名の材料用)
$params_b = array(
    'oauth_token' => $access_token,
    'oauth_consumer_key' => $api_key,
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_nonce' => microtime(),
    'oauth_version' => '1.0',
);

// パラメータAとパラメータBを合成してパラメータCを作る
$params_c = array_merge($params_a, $params_b);

// 連想配列をアルファベット順に並び替える
ksort($params_c);

// パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
$request_params = http_build_query($params_c, '', '&');

// 一部の文字列をフォロー
$request_params = str_replace(array('+', '%7E'), array('%20', '~'), $request_params);

// 変換した文字列をURLエンコードする
$request_params = rawurlencode($request_params);

// リクエストメソッドをURLエンコードする
// ここでは、URL末尾の[?]以下は付けないこと
$encoded_request_method = rawurlencode($request_method);

// リクエストURLをURLエンコードする
$encoded_request_url = rawurlencode($request_url);

// リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
$signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params;

// キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
$hash = hash_hmac('sha1', $signature_data, $signature_key, true);

// base64エンコードして、署名[$signature]が完成する
$signature = base64_encode($hash);

// パラメータの連想配列、[$params]に、作成した署名を加える
$params_c['oauth_signature'] = $signature;

// パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
$header_params = http_build_query($params_c, '', ',');

// リクエスト用のコンテキスト
$context = array(
    'http' => array(
        'method' => $request_method, // リクエストメソッド
        'header' => array( // ヘッダー
            'Authorization: OAuth ' . $header_params,
        ),
    ),
);

// パラメータがある場合、URLの末尾に追加
if ($params_a) {
    $request_url .= '?' . http_build_query($params_a);
}

// オプションがある場合、コンテキストにPOSTフィールドを作成する (GETの場合は不要)
//    if( $params_a ) {
//        $context['http']['content'] = http_build_query( $params_a ) ;
//    }

// cURLを使ってリクエスト
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $request_url);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context['http']['method']); // メソッド
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // curl_execの結果を文字列で返す
curl_setopt($curl, CURLOPT_HTTPHEADER, $context['http']['header']); // ヘッダー
//    if( isset( $context['http']['content'] ) && !empty( $context['http']['content'] ) ) {        // GETの場合は不要
//        curl_setopt( $curl , CURLOPT_POSTFIELDS , $context['http']['content'] ) ;    // リクエストボディ
//    }
curl_setopt($curl, CURLOPT_TIMEOUT, 5); // タイムアウトの秒数
$res1 = curl_exec($curl);
$res2 = curl_getinfo($curl);
curl_close($curl);

// 取得したデータ
$json = substr($res1, $res2['header_size']); // 取得したデータ(JSONなど)
$header = substr($res1, 0, $res2['header_size']); // レスポンスヘッダー (検証に利用したい場合にどうぞ)

// [cURL]ではなく、[file_get_contents()]を使うには下記の通りです…
// $json = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;
var_dump($header);
var_dump($api_key);
var_dump($api_secret);
var_dump(Config::get('twitter_screen_name'));
$twitter = new Twitter($json, $pdo);
$twitter->set_to_db();

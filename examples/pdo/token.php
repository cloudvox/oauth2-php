<?php

/**
 * @file
 * Sample token endpoint.
 *
 * Obviously not production-ready code, just simple and to the point.
 *
 * In reality, you'd probably use a nifty framework to handle most of the crud for you.
 */

require 'OAuth2/Storage/StoragePdo.php';
require_once 'OAuth2/Server/Server.php';
require_once 'OAuth2/Exception/ServerException.php';

$dsn = 'mysql:dbname=testdb;host=127.0.0.1';
$user = 'dbuser';
$password = 'dbpass';

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


$oauth = new OAuth2\Server\Server(new OAuth2\Storage\StoragePdo($db));
try {
    $oauth->grantAccessToken();
} catch (OAuth2\Exception\ServerException $oauthError) {
    $oauthError->sendHttpResponse();
}

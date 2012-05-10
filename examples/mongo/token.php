<?php

/**
 * @file
 * Sample token endpoint.
 *
 * Obviously not production-ready code, just simple and to the point.
 *
 * In reality, you'd probably use a nifty framework to handle most of the crud for you.
 */

require 'OAuth2/Server/MongoServer.php';
require 'OAuth2/Excepton/ServerException.php';

$oauth = new OAuth2\Server\MongoServer();
try {
    $oauth->grantAccessToken();
} catch (OAuth2\Exception\ServerException $oauthError) {
    $oauthError->sendHttpResponse();
}

<?php

/**
 * @file
 * Sample token endpoint.
 *
 * Obviously not production-ready code, just simple and to the point.
 *
 * In reality, you'd probably use a nifty framework to handle most of the crud for you.
 */

require 'OAuth2/MongoServer.php';

$oauth = new OAuth2_MongoServer();
try {
    $oauth->grantAccessToken();
} catch (\OAuth2\Server\ServerException $oauthError) {
    $oauthError->sendHttpResponse();
}

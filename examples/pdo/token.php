<?php

/**
 * @file
 * Sample token endpoint.
 *
 * Obviously not production-ready code, just simple and to the point.
 *
 * In reality, you'd probably use a nifty framework to handle most of the crud for you.
 */

require 'OAuth2/StoragePdo.php';

$oauth = new OAuth2_Server(new OAuth2_StoragePdo($db));
try {
    $oauth->grantAccessToken();
} catch (OAuth2_ServerException $oauthError) {
    $oauthError->sendHttpResponse();
}

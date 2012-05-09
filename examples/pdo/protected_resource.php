<?php

/**
 * @file
 * Sample protected resource.
 *
 * Obviously not production-ready code, just simple and to the point.
 *
 * In reality, you'd probably use a nifty framework to handle most of the crud for you.
 */

require 'OAuth2/StoragePdo.php';

try {
    $oauth = new \OAuth2\Server\Server(new OAuth2_StoragePdo($db));
    $token = $oauth->getBearerToken();
    $oauth->verifyAccessToken($token);
} catch (\OAuth2\Server\ServerException $oauthError) {
    $oauthError->sendHttpResponse();
}

// With a particular scope, you'd do:
// $oauth->verifyAccessToken("scope_name");


?>

<html>
    <head>
        <title>Hello!</title>
    </head>
    <body>
        <p>This is a secret.</p>
    </body>
</html>

<?php

/**
 * @file
 * Sample authorize endpoint.
 *
 * Obviously not production-ready code, just simple and to the point.
 *
 * In reality, you'd probably use a nifty framework to handle most of the crud for you.
 */

require 'OAuth2/MongoServer.php';

$oauth = new OAuth2_MongoServer();

if ($_POST) {
    $oauth->finishClientAuthorization($_POST["accept"] == "Yep", $_POST);
}

try {
    $auth_params = $oauth->getAuthorizeParams();
} catch (OAuth2_ServerException $oauthError) {
    $oauthError->sendHttpResponse();
}

?>
<html>
<head>
<title>Authorize</title>
</head>
<body>
<form method="post" action="authorize.php">
      <?php foreach ($auth_params as $k => $v) { ?>
      <input type="hidden" name="<?php echo $k ?>"
    value="<?php echo $v ?>" />
      <?php } ?>
      Do you authorize the app to do its thing?
      <p><input type="submit" name="accept" value="Yep" /> <input
    type="submit" name="accept" value="Nope" /></p>
</form>
</body>
</html>

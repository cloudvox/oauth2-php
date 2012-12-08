<?php
namespace OAuth2\Storage;

/**
 * @category OAuth2
 * @package  OAuth2
 */
use OAuth2\Storage\StorageInterface;
use OAuth2\Server;

/**
 * @category OAuth2
 * @package  OAuth2
 * Storage engines that support the "Authorization Code" grant type should implement this interface
 *
 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.1
 */
interface GrantCodeInterface extends StorageInterface {

    /**
     * The Authorization Code grant type supports a response type of "code".
     *
     * @var string
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-1.4.1
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.2
     */
    const RESPONSE_TYPE_CODE = Server::RESPONSE_TYPE_AUTH_CODE;

    /**
     * Fetch authorization code data (probably the most common grant type).
     * Retrieve the stored data for the given authorization code.
     * Required for Server::GRANT_TYPE_AUTH_CODE.
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.1
     *
     * @param string $code Authorization code to be check with.
     *
     * @return array An associative array as below, and null if the code is invalid:
     * - client_id: Stored client identifier.
     * - redirect_uri: Stored redirect URI.
     * - expires: Stored expiration in unix timestamp.
     * - scope: (optional) Stored scope values in space-separated string.
     *
     */
    public function getAuthCode($code);

    /**
     * Take the provided authorization code values and store them somewhere.
     *
     * This function should be the storage counterpart to getAuthCode().
     *
     * If storage fails for some reason, we're not currently checking for any sort of success/failure, so you should
     * bail out of the script and provide a descriptive fail message.
     *
     * Required for Server::GRANT_TYPE_AUTH_CODE.
     *
     * @param string $code Authorization code to be stored.
     * @param mixed $client_id Client identifier to be stored.
     * @param mixed $user_id User identifier to be stored.
     * @param string $redirect_uri Redirect URI to be stored.
     * @param int $expires Expiration to be stored.
     * @param array|null $scope (optional) Scopes to be stored in space-separated string.
     * @return mixed
     */
    public function setAuthCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null);

}

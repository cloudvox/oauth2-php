<?php
namespace OAuth2\Storage;

/**
 *
 *
 *
 * @category OAuth2
 * @package  OAuth2
 */
/**
 * @category OAuth2
 * @package  OAuth2
 *
 * All storage engines need to implement this interface in order to use OAuth2 server
 *
 */
interface StorageInterface {

    /**
     * Make sure that the client credentials is valid.
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-3.1
     *
     * @param mixed $client_id Client identifier to be check with.
     * @param string $client_secret (optional) If a secret is required, check that they've given the right one.
     *
     * @return bool true if the client credentials are valid, and MUST return false if it isn't.
     *
     *
     */
    public function checkClientCredentials($client_id, $client_secret = null);

    /**
     * Get client details corresponding client_id.
     *
     * OAuth says we should store request URIs for each registered client. Implement this function to grab the stored
     * URI for a given client id.
     *
     * @param mixed $client_id Client identifier to be check with.
     *
     * @return array Client details. Only mandatory item is the "registered redirect URI", and MUST return false if
     * the given client does not exist or is invalid.
     *
     */
    public function getClientDetails($client_id);

    /**
     * Look up the supplied oauth_token from storage.
     *
     * We need to retrieve access token data as we create and verify tokens.
     *
     * @param string $oauth_token oauth_token to be check with.
     * @return array An associative array as below, and return null if the supplied oauth_token is invalid:
     * - client_id: Stored client identifier.
     * - expires: Stored expiration in unix timestamp.
     * - scope: (optional) Stored scope values in space-separated string.
     *
     */
    public function getAccessToken($oauth_token);

    /**
     * Store the supplied access token values to storage.
     *
     * We need to store access token data as we create and verify tokens.
     *
     * @param string $oauth_token oauth_token to be stored.
     * @param mixed $client_id Client identifier to be stored.
     * @param mixed $user_id User identifier to be stored.
     * @param int $expires Expiration to be stored.
     * @param array $scope (optional) Scopes to be stored in space-separated string.
     *
     */
    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null);

    /**
     * Check restricted grant types of corresponding client identifier.
     *
     * If you want to restrict clients to certain grant types, override this function.
     *
     * @param string $client_id Client identifier to be check with.
     * @param string $grant_type Grant type to be check with, would be one of the values contained in
     * OAuth2::GRANT_TYPE_REGEXP.
     *
     * @return bool true if the grant type is supported by this client identifier, and false if it isn't.
     *
     */
    public function checkRestrictedGrantType($client_id, $grant_type);
}

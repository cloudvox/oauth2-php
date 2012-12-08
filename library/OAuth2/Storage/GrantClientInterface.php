<?php
namespace OAuth2\Storage;

/**
 * @category OAuth2
 * @package  OAuth2
 */
use OAuth2\Storage\StorageInterface;

/**
 * @category OAuth2
 * @package  OAuth2
 *
 * Storage engines that support the "Client Credentials" grant type should implement this interface
 *
 *
 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.4
 */
interface GrantClientInterface extends StorageInterface {

    /**
     * Required for OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS.
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.4.2
     * @param mixed $client_id Client identifier to be check with.
     * @param string $client_secret (optional) If a secret is required, check that they've given the right one.
     * @return bool
     *
     * true if the client credentials are valid, and MUST return false if it isn't. When using "client credentials"
     * grant mechanism and you want to verify the scope of a user's access, return an associative array with the scope
     * values as below. We'll check the scope you provide against the requested scope before providing an access token:
     * <code>
     * return array(
     * 'scope' => <stored scope values (space-separated string)>,
     * );
     * </code>
     *
     */
    public function checkClientCredentialsGrant($client_id, $client_secret);
}

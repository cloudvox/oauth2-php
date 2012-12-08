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
 * Storage engines that want to support refresh tokens should implement this interface.
 *
 *
 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-6
 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-1.5
 */
interface RefreshTokensInterface extends StorageInterface {

    /**
     * Grant refresh access tokens.
     * Retrieve the stored data for the given refresh token.
     * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param string $refresh_token Refresh token to be check with.
     * @return array An associative array as below, and null if the refresh_token is invalid:
     * - client_id: Stored client identifier.
     * - expires: Stored expiration unix timestamp.
     * - scope: (optional) Stored scope values in space-separated string.
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-6
     *
     * @ingroup oauth2_section_6
     */
    public function getRefreshToken($refresh_token);

    /**
     *
     * Take the provided refresh token values and store them somewhere.
     *
     * This function should be the storage counterpart to getRefreshToken().
     *
     * If storage fails for some reason, we're not currently checking for any sort of success/failure, so you should
     * bail out of the script and provide a descriptive fail message.
     *
     * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param string  $refresh_token
     * @param mixed $client_id
     * @param mixed $user_id
     * @param int $expires
     * @param array|null $scope
     * @return mixed
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null);

    /**
     * Expire a used refresh token.
     *
     * This is not explicitly required in the spec, but is almost implied. After granting a new refresh token, the old
     * one is no longer useful and so should be forcibly expired in the data store so it can't be used again.
     *
     * If storage fails for some reason, we're not currently checking for any sort of success/failure, so you should
     * bail out of the script and provide a descriptive fail message.
     *
     * @param string $refresh_token
     * Refresh token to be expires.
     *
     */
    public function unsetRefreshToken($refresh_token);
}

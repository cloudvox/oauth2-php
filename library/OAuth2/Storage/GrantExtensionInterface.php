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
 * Storage engines that support the "Extensible" grant types should implement this interface
 *
 *
 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.5
 */
interface GrantExtensionInterface extends StorageInterface {

    /**
     * Check any extended grant types.
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-1.4.5
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.2
     *
     * @param string $uri URI of the grant type definition
     * @param array $inputData Unfiltered input data. The source is *not* guaranteed to be POST (but is likely to be).
     * @param array $authHeaders Authorization headers
     * @return bool false if the authorization is rejected or not support. true or an associative array if you want to
     * verify the scope:
     * <code>
     * return array(
     * 'scope' => <stored scope values (space-separated string)>,
     * );
     * </code>
     */
    public function checkGrantExtension($uri, array $inputData, array $authHeaders);
}

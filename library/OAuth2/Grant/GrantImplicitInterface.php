<?php
namespace OAuth2\Grant;

/**
 * @category OAuth2
 * @package  OAuth2
 */
use OAuth2\Storage\StorageInterface;
use OAuth2\Server;

/**
 * @category OAuth2
 * @package  OAuth2
 * Storage engines that support the "Implicit"
 * grant type should implement this interface
 *
 * @author Dave Rochwerger <catch.dave@gmail.com>
 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.2
 */
interface GrantImplicitInterface extends StorageInterface {

    /**
     * The Implicit grant type supports a response type of "token".
     *
     * @var string
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-1.4.2
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-4.2
     */
    const RESPONSE_TYPE_TOKEN = Server::RESPONSE_TYPE_ACCESS_TOKEN;
}

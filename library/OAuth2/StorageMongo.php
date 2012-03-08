<?php

/**
 * @file
 * Sample OAuth2 Library Mongo DB Implementation.
 *
 */

require 'OAuth2/Server.php';
require 'OAuth2_Storage.php';
require 'OAuth2/GrantCodeInterface.php';
require 'OAuth2/RefreshTokensInterface.php';

/**
 * WARNING: This example file has not been kept up to date like the PDO example has.
 * FIXME: Update the Mongo examples
 *
 * Mongo storage engine for the OAuth2 Library.
 */
class OAuth2_StorageMongo implements OAuth2_GrantCodeInterface, OAuth2_RefreshTokensInterface
{

    /**
     * Change this to something unique for your system
     * @var string
     */
    const SALT = 'CHANGE_ME!';

    /**
     * @var MongoDB
     */
    private $db;

    /**
     * Implements OAuth2_Server::__construct().
     */
    public function __construct(MongoDB $db = null) {
        $this->db = $db;
    }

    /**
     * Handle MongoException cases.
     */
    private function handleException($e) {
        echo 'Database error: ' . $e->getMessage();
        exit;
    }

    /**
     * Little helper function to add a new client to the database.
     *
     * @param $client_id
     * Client identifier to be stored.
     * @param $client_secret
     * Client secret to be stored.
     * @param $redirect_uri
     * Redirect URI to be stored.
     */
    public function addClient($client_id, $client_secret, $redirect_uri) {
        $this->db->clients->insert(
            array("_id" => $client_id,
            "pw" => $this->hash($client_secret, $client_id),
            "redirect_uri" => $redirect_uri)
        );
    }

    /**
     * Implements OAuth2_StorageInterface::checkClientCredentials().
     *
     */
    public function checkClientCredentials($client_id, $client_secret = null) {
        $result = $this->db->clients->findOne(
            array("_id" => $client_id, "pw" => $client_secret)
        );
        return $this->checkPassword($client_secret, $result['client_secret'], $client_id);
    }

    /**
     * Implements OAuth2_StorageInterface::getRedirectUri().
     */
    public function getClientDetails($client_id) {
        return $this->db->clients->findOne(array("_id" => $client_id), array("redirect_uri"));
    }

    /**
     * Implements OAuth2_StorageInterface::getAccessToken().
     */
    public function getAccessToken($oauth_token) {
        return $this->db->tokens->findOne(array("_id" => $oauth_token));
    }

    /**
     * Implements OAuth2_StorageInterface::setAccessToken().
     */
    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null) {
        $this->db->tokens->insert(
            array("_id" => $oauth_token, "client_id" => $client_id,
            "expires" => $expires, "scope" => $scope)
        );
    }

    /**
     * @see OAuth2_StorageInterface::getRefreshToken()
     */
    public function getRefreshToken($refresh_token) {
        return $this->getToken($refresh_token, true);
    }

    /**
     * @see OAuth2_StorageInterface::setRefreshToken()
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id,
    $expires, $scope = null) {
        return $this->setToken(
            $refresh_token, $client_id, $user_id, $expires, $scope, true
        );
    }

    /**
     * @see OAuth2_StorageInterface::unsetRefreshToken()
     * @throws MongoException
     */
    public function unsetRefreshToken($refresh_token) {
        try {
            $this->db->tokens->remove(array('refresh_token' => $refresh_token));
        } catch (MongoException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Implements OAuth2_StorageInterface::getAuthCode().
     */
    public function getAuthCode($code) {
        $stored_code = $this->db->auth_codes->findOne(array("_id" => $code));
        return $stored_code !== null ? $stored_code : false;
    }

    /**
     * Implements OAuth2_StorageInterface::setAuthCode().
     */
    public function setAuthCode($code, $client_id, $user_id, $redirect_uri,
    $expires, $scope = null) {
        $this->db->auth_codes->insert(
            array('_id' => $code, 'client_id' => $client_id,
            'redirect_uri' => $redirect_uri, 'expires' => $expires,
            'scope' => $scope)
        );
    }

    /**
     * @see OAuth2_StorageInterface::checkRestrictedGrantType()
     */
    public function checkRestrictedGrantType($client_id, $grant_type) {
        return true; // Not implemented
    }

    /**
     * Change/override this to whatever your own password hashing method is.
     *
     * @param string $secret
     * @return string
     */
    protected function hash($client_secret, $client_id) {
        return hash('blowfish', $client_id . $client_secret . self::SALT);
    }

    /**
     * Checks the password.
     * Override this if you need to
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $actualPassword
     */
    protected function checkPassword($try, $client_secret, $client_id) {
        return $try == $this->hash($client_secret, $client_id);
    }
}

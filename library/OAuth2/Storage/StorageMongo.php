<?php
/**
 *
 *
 *
 * @category OAuth2
 * @package  OAuth2
 */
namespace OAuth2\Storage;
use OAuth2\Grant\GrantCodeInterface,
    OAuth2\RefreshTokensInterface,
    MongoException,
    MongoDB;
/**
 * @category OAuth2
 * @package  OAuth2
 * Mongo storage engine for the OAuth2 Library.
 */
class StorageMongo implements GrantCodeInterface, RefreshTokensInterface
{
    const RESPONSE_TYPE_TOKEN = 'RESPONSE_TYPE_TOKEN';
    /**
     *
     *
     * @var string
     */
    public static $CRYPT = '$2a$';
    /**
     *
     *
     * @var string
     */
    public static $LOAD = '15$';

    /**
     * @var MongoDB
     */
    private $db;

    /**
     * Implements Server::__construct().
     */
    public function __construct(MongoDB $db = null) {
        $this->db = $db;
    }
    /**
     * @return string
     */
    protected function _getSalt()
    {
        return substr(
            str_replace('+', '.', base64_encode(sha1(microtime(true), true)))
            , 0, 22
        );
    }
    /**
     *
     *
     * @param string $password
     * @return string
     */
    protected function _crypt($client_secret)
    {
        return crypt($client_secret, self::$CRYPT . self::$LOAD . $this->_getSalt());
    }
    /**
     *
     *
     * @param string $password
     * @param string $credential
     * @return boolean
     */
    protected function _isValid($client_secret, $pw)
    {
        return ($pw == crypt($client_secret, $pw));
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
        $this->db->clients->save(
            array("_id" => $client_id,
            "pw" => $this->_crypt($client_secret),
            "redirect_uri" => $redirect_uri)
        );
    }

    /**
     * Implements StorageInterface::checkClientCredentials().
     *
     */
    public function checkClientCredentials($client_id, $client_secret = null) {
        $result = $this->db->clients->findOne(
            array("_id" => $client_id)
        );
        return $this->checkPassword($result['pw'], $client_secret);
    }

    /**
     * Implements StorageInterface::getRedirectUri().
     */
    public function getClientDetails($client_id) {
        $data = $this->db->clients->findOne(array("_id" => $client_id), array("redirect_uri"));
        $data['client_id'] = $data['_id'];
        unset($data['_id']);
        return $data;
    }

    /**
     * Implements StorageInterface::getAccessToken().
     */
    public function getAccessToken($oauth_token) {
        return $this->db->tokens->findOne(array("_id" => $oauth_token));
    }

    /**
     * Implements StorageInterface::setAccessToken().
     */
    public function setAccessToken($oauth_token, $client_id, $user_id,
    $expires, $scope = null) {
        $this->db->tokens->insert(
            array("_id" => $oauth_token, "client_id" => $client_id,
            "expires" => $expires, "scope" => $scope)
        );
    }

    /**
     * @see StorageInterface::getRefreshToken()
     */
    public function getRefreshToken($refresh_token) {
        $data = $this->db->tokens->findOne(array("_id" => $refresh_token));
        $data['refresh_token'] = $data['_id'];
        unset($data['_id']);
        return $data;
    }

    /**
     * @see StorageInterface::setRefreshToken()
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id,
    $expires, $scope = null) {
        return $this->db->tokens->save(array(
            '_id' => $refresh_token, 'client_id' => $client_id,
            'user_id' => $user_id, 'expires' => $expires, 'scope' => $scope
        ), array('safe' => 1));
    }

    /**
     * @see StorageInterface::unsetRefreshToken()
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
     * Implements StorageInterface::getAuthCode().
     */
    public function getAuthCode($code) {
        $stored_code = $this->db->auth_codes->findOne(array("_id" => $code));
        return $stored_code !== null ? $stored_code : false;
    }

    /**
     * Implements StorageInterface::setAuthCode().
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
     * @see StorageInterface::checkRestrictedGrantType()
     */
    public function checkRestrictedGrantType($client_id, $grant_type) {
        return true; // Not implemented
    }

    /**
     * Checks the password.
     * Override this if you need to
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $actualPassword
     */
    protected function checkPassword($try, $client_secret) {
        return $this->_isValid($client_secret, $try);
    }
}

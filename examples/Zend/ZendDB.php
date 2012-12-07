<?php
namespace MyProject\Auth;

/**
 * @package     MyProject
 * @category    Auth2
 * @subcategory Storage
 */
use OAuth2\Storage\StorageInterface;
use Zend_Db_Adapter_Abstract;
use Zend_Db_Exception;

/**
 *
 * @package     MyProject
 * @category    Auth2
 * @subcategory Storage
 */
class ZendDb implements StorageInterface
{

    /**
     * Database table names
     *
     * @var array
     */
    protected $_tableMap
        = array(
            'clients' => 'clients',
            'auth_code' => 'auth_code',
            'access_tokens' => 'access_tokens',
            'refresh_tokens' => 'refresh_tokens'
        );

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_database;

    /**
     *
     */
    public function __construct($database = null)
    {
        $this->_database = $database;
    }

    // @codeCoverageIgnoreEnd

    /**
     * @return array
     */
    public function getTableMap()
    {
        return $this->_tableMap;
    }

    /**
     * @param array $tableMap
     *
     * @return ZendDb
     */
    public function setTableMap(array $tableMap)
    {
        $this->_tableMap = $tableMap;
        return $this;
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDb()
    {
        return $this->_database;
    }

    /**
     * @param Zend_Db_Adapter_Abstract $database
     *
     * @return ZendDb
     */
    public function setDb(Zend_Db_Adapter_Abstract $database)
    {
        $this->_database = $database;
        return $this;
    }

    /**
     *
     */
    protected function handleException(\Exception $except)
    {
        // @codeCoverageIgnoreStart
        throw $except;
        // @codeCoverageIgnoreEnd
    }

    /**
     *
     * @param string $clientId     Client identifier to be stored.
     * @param string $clientSecret Client secret to be stored.
     * @param string $redirectUri  Redirect URI to be stored.
     *
     * @return ZendDb
     */
    public function addClient($clientId, $clientSecret, $redirectUri)
    {
        try {
            $clientSecret = $this->hash($clientSecret);
            $data = array(
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri
            );
            $this->getDb()->insert($this->_tableMap['clients'], $data);
        } catch (Zend_Db_Exception $except) {
            // @codeCoverageIgnoreStart
            $this->handleException($except);
            // @codeCoverageIgnoreEnd
        }
        return $this;
    }

    /**
     * @param string      $clientId
     * @param null|string $clientSecret
     *
     * @return bool
     */
    public function checkClientCredentials($clientId, $clientSecret = null)
    {
        try {
            $sql = $this->getDb()->select()
                ->from($this->_tableMap['clients'], 'client_secret')
                ->where($this->getDb()->quoteInto('client_id = ?', $clientId));

            $hashedSecret = $this->getDb()->fetchOne($sql);

            if ($clientSecret === null) {
                return $hashedSecret !== false;
            }

            return $this->checkPassword($clientSecret, $hashedSecret);
            // @codeCoverageIgnoreStart
        } catch (Zend_Db_Exception $except) {
            $this->handleException($except);
        }
        return false;
    }

    // @codeCoverageIgnoreEnd

    /**
     * @param $clientId
     *
     * @return bool|string|void
     */
    public function getClientDetails($clientId)
    {
        try {
            $sql = $this->getDb()->select()
                ->from($this->_tableMap['clients'], 'redirect_uri')
                ->where($this->getDb()->quoteInto('client_id = ?', $clientId));

            $redirect_uri = $this->getDb()->fetchOne($sql);

            if ($redirect_uri === false) {
                return $redirect_uri !== false;
            }
            // @codeCoverageIgnoreStart
        } catch (Zend_Db_Exception $except) {
            $this->handleException($except);
        }
        return null;
    }

    // @codeCoverageIgnoreEnd

    /**
     * @param string $oauthToken
     *
     * @return array|null
     */
    public function getAccessToken($oauthToken)
    {
        return $this->getToken($oauthToken, false);
    }

    /**
     * @param string $oauthToken
     * @param string $clientId
     * @param string $userId
     * @param string $expires
     * @param null   $scope
     *
     * @return ZendDb
     */
    public function setAccessToken($oauthToken, $clientId, $userId, $expires, $scope = null)
    {
        return $this->setToken($oauthToken, $clientId, $userId, $expires, $scope, false);
    }

    /**
     * @param $oauthToken
     */
    public function revokeAccessToken($oauthToken)
    {
        $this->getDb()->update(
            'tokens',
            array('expires' => 0),
            $this->getDb()->quoteInto('token = ?', $oauthToken)
        );
    }

    /**
     * @deprecated
     *
     * @param string $clientId
     * @param string $grantType
     *
     * @return bool
     */
    public function checkRestrictedGrantType($clientId, $grantType)
    {
        $grantType = null;
        $clientId = null;
        return true;
    }

    /**
     * Creates a refresh or access token
     *
     * @param string $token - Access or refresh token id
     * @param string $clientId
     * @param mixed  $userId
     * @param int    $expires
     * @param string $scope
     * @param bool   $isRefresh
     *
     * @return ZendDb
     */
    protected function setToken($token, $clientId, $userId, $expires, $scope, $isRefresh = true)
    {
        try {
            $data = array(
                'token' => $token, 'client_id' => $clientId, 'expires' => $expires, 'scope' => $scope,
                'user_id' => $userId
            );

            $this->getDb()->insert(
                $isRefresh ? $this->_tableMap['refresh_tokens'] : $this->_tableMap['access_tokens'],
                $data
            ); // @codeCoverageIgnoreStart
        } catch (Zend_Db_Exception $except) {
            $this->handleException($except);
        }
        return $this;
    }

    // @codeCoverageIgnoreEnd

    /**
     * @param      $token
     * @param bool $isRefresh
     *
     * @return mixed|null
     */
    protected function getToken($token, $isRefresh = true)
    {
        try {
            $tableName = $isRefresh
                ? $this->_tableMap['refresh_tokens']
                :
                $this->_tableMap['access_tokens'];
            $tokenName = $isRefresh ? 'token' : 'token';

            $sql = $this->getDb()->select()
                ->from(
                    $tableName,
                    array($tokenName, 'client_id', 'pci_id', 'expires', 'scope')
                )->where($this->getDb()->quoteInto('token = ?', $token));

            $result = $this->getDb()->fetchRow($sql);

            return $result !== false ? $result : null;
            // @codeCoverageIgnoreStart
        } catch (Zend_Db_Exception $except) {
            $this->handleException($except);
        }
        return null;
    }

    /**
     * @param $password
     *
     * @return string
     */
    protected function hash($password)
    {
        $crypt = new \MyProject_Auth_Credential;
        $crypt->setSalt($crypt->generateSalt());
        return $crypt->crypt($password);
    }

    /**
     * @param $try
     * @param $clientSecret
     *
     * @return bool
     */
    protected function checkPassword($try, $clientSecret)
    {
        $crypt = new \MyProject_Auth_Credential;
        $result = $crypt->isValid($try, $clientSecret);
        return $result;
    }
}


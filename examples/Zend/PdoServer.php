<?php
namespace MyProject\Auth;

/**
 * @package
 * @category
 * @subcategory
 * Date: 9/13/12T3:09 PM
 */
use OAuth2\Server\Server;
use OAuth2\Exception\Exception as OAuth2Exception;
use \Zend_Log;

/**
 * @package
 * @category
 * @subcategory
 */
class PdoServer extends Server
{
    /**
     * @var \MyProject\Auth\ZendDb
     */
    protected $storage;
    /**
     * @var Zend_Log
     */
    protected $log;

    /**
     * @return Zend_Log
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param Zend_Log $log
     *
     * @return PdoServer
     */
    public function setLog(Zend_Log $log)
    {
        $this->log = $log;
        return $this;
    }

    /**
     * @param $clientId
     * @param $clientSecret
     * @param $token
     *
     * @return array
     * @throws \OAuth2\Exception\Exception
     */
    public function revokeToken($clientId, $clientSecret, $token)
    {
        $response = array(
            'date'        => date(DATE_ATOM),
            'action'      => __METHOD__,
            'client_id'   => $clientId,
            'requestor'   => getenv('USER'),
            'remote_addr' => @$_SERVER['REMOTE_ADDR']
        );
        if ($this->getStorage()->checkClientCredentials($clientId, $clientSecret)) {
            if ($log = $this->getLog()) {
                $log->log(json_encode($response), Zend_Log::ALERT);
            }
            $this->getStorage()->revokeAccessToken($token);
        } else {
            $exception = new OAuth2Exception(
                'credentials check failed please check your password and try again.' . PHP_EOL .
                'This event has been logged and reported:' . PHP_EOL .
                \Zend_Json::prettyPrint(json_encode($response))
            );
            if ($log = $this->getLog()) {
                $log->log((string)$exception, Zend_Log::CRIT);
            }
            throw $exception;
        }
        return $response;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param null   $scope
     *
     * @return string
     * @throws \OAuth2\Exception\Exception
     */
    public function grantImplicitToken($clientId, $clientSecret, $scope = null)
    {
        if (is_array($scope)) {
            $scope = implode(' ', $scope);
        }
        $response = array(
            'date'        => date(DATE_ATOM),
            'action'      => __METHOD__,
            'client_id'   => $clientId,
            'expires'     => strtotime('2020-12-31T23:59:59-00:00'),
            'scope'       => $scope,
            'requestor'   => getenv('USER'),
            'remote_addr' => @$_SERVER['REMOTE_ADDR']
        );
        if ($this->getStorage()->checkClientCredentials($clientId, $clientSecret)) {
            $response['token'] = $this->genAccessToken();
            if ($log = $this->getLog()) {
                $log->log(json_encode($response), Zend_Log::INFO);
            }
            $this->getStorage()->setAccessToken(
                $response['token'],
                $clientId,
                null,
                $response['expires'],
                $scope,
                false
            );
        } else {
            $exception = new OAuth2Exception(
                'credentials check failed please check your password and try again.' . PHP_EOL .
                'This event has been logged and reported:' . PHP_EOL .
                \Zend_Json::prettyPrint(json_encode($response))
            );
            if ($log = $this->getLog()) {
                $log->log((string)$exception, Zend_Log::EMERG);
            }
            throw $exception;
        }
        return $response;
    }

    /**
     * @param $clientId
     * @param $clientSecret
     *
     * @return bool
     */
    public function checkCredentials($clientId, $clientSecret)
    {
        $response = array(
            'date'        => date(DATE_ATOM),
            'action'      => __METHOD__,
            'client_id'   => $clientId,
            'requestor'   => getenv('USER'),
            'remote_addr' => @$_SERVER['REMOTE_ADDR']
        );
        if ($this->getStorage()->checkClientCredentials($clientId, $clientSecret)) {
            if ($log = $this->getLog()) {
                $log->log('AUTHENTICATION SUCCESS' . PHP_EOL . json_encode($response), Zend_Log::INFO);
            }
            return true;
        } else {
            if ($log = $this->getLog()) {
                $log->log(
                    'AUTHENTICATION FAILURE' . PHP_EOL . \Zend_Json::PrettyPrint(json_encode($response)),
                    Zend_Log::CRIT
                );
            }
        }
        return false;
    }

    /**
     * @return ZendDb|\OAuth2\Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }
}


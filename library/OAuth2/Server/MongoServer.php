<?php
/**
 * @category OAuth2
 * @package  OAuth2
 */
namespace OAuth2\Server;
use OAuth2\Server\Server,
    OAuth2\Storage\StorageMongo;
/**
 * @category OAuth2
 * @package  OAuth2
 */
class MongoServer extends Server
{
    /**
     *
     * @var StorageMongo
     */
    protected $storage;
    public function __construct(StorageMongo $db, $options = array())
    {
        parent::__construct($db, $options);
    }

    /**
     *
     * @param unknown_type $client_id
     * @param unknown_type $client_secret
     * @param unknown_type $redirect_uri
     */
    public function addClient($client_id, $client_secret, $redirect_uri){
        $this->storage->addClient($client_id, $client_secret, $redirect_uri);
    }
    /**
     *
     * @see Server::grantAccessToken()
     */
    public function grantAccessToken($input = null, $authHeaders = null)
    {
        $data = parent::grantAccessToken($input, $authHeaders);
        $data['refresh_token'] = $data['_id'];
        unset($data['_id']);
        return $data;
    }
}

<?php

class OAuth2_MongoServer extends OAuth2_Server
{
    /**
     *
     * @var OAuth2_StorageMongo
     */
    protected $storage;
    public function __construct(OAuth2_StorageMongo $db)
    {
        parent::__construct($db);
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
    public function grantAccessToken($input)
    {
        $data = parent::grantAccessToken($input);
        $data['refresh_token'] = $data['_id'];
        unset($data['_id']);
        return $data;
    }
}

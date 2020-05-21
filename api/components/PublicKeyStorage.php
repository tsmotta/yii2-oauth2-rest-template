<?php

namespace api\components;

/**
 * Description of PublicKeyStorage
 *
 * @author Thiago
 */

class PublicKeyStorage implements \OAuth2\Storage\PublicKeyInterface{


    private $pbk =  null;
    private $pvk =  null; 

    public function __construct()
    {
        $pvkText =  file_get_contents(dirname(__FILE__).'/keys/privkey.pem');        
        $this->pvk = openssl_get_privatekey($pvkText, 'gogogo');
        $this->pbk =  file_get_contents(dirname(__FILE__).'/keys/pubkey.pem'); 
    }

    public function getPublicKey($client_id = null){ 
        return  $this->pbk;
    }

    public function getPrivateKey($client_id = null){
        return  $this->pvk;
    }

    public function getEncryptionAlgorithm($client_id = null){
        return "RS256";
    }

}
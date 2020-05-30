<?php

namespace api\models;

use yii\db\ActiveRecord;

/**
 * Description of Client
 *
 * @author Thiago
 */
class Client extends ActiveRecord implements \OAuth2\Storage\ClientCredentialsInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%oauth_clients}}';
    }
    
    /**
     * @inheritdoc
     */
    private function getClient($client_id)
    {
        return \filsh\yii2\oauth2server\models\OauthClients::findOne(['client_id' => $client_id]);
    }

    /**
     * @inheritdoc
     */
    public function getClientDetails($client_id)
    {
        $client = $this->getClient($client_id);
        if (empty($client)) return [];

        return [
            "redirect_uri" => $client->redirect_uri,      // REQUIRED redirect_uri registered for the client
            "client_id"    => $client->client_id,         // OPTIONAL the client id
            "grant_types"  => $client->grant_types,       // OPTIONAL an array of restricted grant types
            "user_id"      => $client->user_id,           // OPTIONAL the user identifier associated with this client
            "scope"        => $client->scope,             // OPTIONAL the scopes allowed for this client
        ];
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        if (isset($_GET['sid'])) {
            return intval($_GET['sid']);
        } 
        elseif (!$this->isGuest) {
            $user = self::findOne($this->id);
            if (!empty($user->store_id)) {
                return $user->store_id;
            }
        }
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getClientScope($client_id)
    {
        $client = $this->getClient($client_id);
        if (empty($client)) return null;

        return $client->scope;
    }

    /**
     * @inheritdoc
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $client = $this->getClient($client_id);
        if (empty($client)) return false;

        $validGrantTypes = explode(" ", $client->grant_types);
        return in_array($grant_type, $validGrantTypes);
    }

    /**
     * @inheritdoc
     */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $client = $this->getClient($client_id);
        if (empty($client)) return false;

        return ($client->client_secret == $client_secret);
    }

    /**
     * @inheritdoc
     */
    public function isPublicClient($client_id)
    {
        return empty($this->getClient($client_id));
    }
}

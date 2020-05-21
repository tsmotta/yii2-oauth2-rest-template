<?php
/**
 * Copyright 2016 Universidade Federal do Rio Grande do Sul
 */

namespace api\models;

use Yii;
use filsh\yii2\oauth2server\models\OauthAccessTokens;
use OAuth2\Storage\AccessTokenInterface;

class AccessToken extends OauthAccessTokens implements AccessTokenInterface
{
    /**
     * @inheritdoc
     */
    public function getAccessToken($oauth_token)
    {
        $model = self::findOne(['access_token' => $oauth_token]);
        $token = [];

        if ($model) {
            $token['expires'] = strtotime($model->expires);
            $token['client_id'] = $model->client_id;
            $token['user_id'] = $model->user_id;
            $token['scope'] = $model->scope;
            return $token;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
    {
        $expires = date('Y-m-d H:i:s', $expires);

        if ($this->getAccessToken($oauth_token)) {
            $model = self::findOne(['access_token' => $oauth_token]);
            $model->client_id = $client_id;
            $model->expires = $expires;
            $model->user_id = $user_id;
            $model->scope = $scope;
            $model->update();
        } else {
            $model = new self();
            $model->access_token = $oauth_token;
            $model->client_id = $client_id;
            $model->expires = $expires;
            $model->user_id = $user_id;
            $model->scope = $scope;
            $model->insert();
        }
    }

    /**
     * @inheritdoc
     */
    public function unsetAccesstoken($token)
    {
        $model = self::findOne(['access_token' => $token]);
        return $model->delete();
    }

    /**
     * @inheritdoc
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }
}

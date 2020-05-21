<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\models;

use \filsh\yii2\oauth2server\models\OauthRefreshTokens;

/**
 * Description of RefreshToken
 *
 * @author Thiago
 */
class RefreshToken extends OauthRefreshTokens implements \OAuth2\Storage\RefreshTokenInterface
{
    public function getRefreshToken($refresh_token) {
        
        $model = self::findOne(['refresh_token' => $refresh_token]);
        $token = [];
        if($model)
        {
            $token['refresh_token'] = $model->refresh_token;
            $token['client_id'] = $model->client_id;
            $token['user_id'] = $model->user_id;
            $token['expires'] = strtotime($model->expires);
            $token['scope'] = $model->scope;
            return $token;
        }
        
        return null;

    }

    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null) {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);
        
        $model = new RefreshToken();
        $model->refresh_token = $refresh_token;
        $model->client_id = $client_id;
        $model->user_id = $user_id;
        $model->expires = $expires;
        $model->scope = $scope;
        
        return $model->insert();
    }

    public function unsetRefreshToken($refresh_token) {
        $model = self::findOne(['refresh_token' => $refresh_token]);
        return $model->delete();
    }

}

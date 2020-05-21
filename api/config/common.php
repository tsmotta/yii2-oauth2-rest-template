<?php

Yii::setAlias('api', dirname(__DIR__));
$params = require(__DIR__ . '/params.php');
$config = [
    'version' => "0.0.1",
    'basePath' => dirname(__DIR__),
    'timeZone' => 'America/Sao_Paulo',
    'vendorPath' => YII_ENV == 'dev' ? dirname(__DIR__) . '/miaula/vendor' : '',
    'bootstrap' => ['log', 'oauth2'],
    'modules' => [
        'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',
            'tokenParamName' => 'accessToken',
            'tokenAccessLifetime' => 3600 * 24,
            'useJwtToken' => true,
            'storageMap' => [
                'user_credentials' => 'api\models\User',
                'client_credentials' => 'api\models\Client',
//                'access_token' => 'api\models\AccessToken',
                'refresh_token' => 'api\models\RefreshToken',
                'public_key' => 'api\components\PublicKeyStorage',
                'access_token' => 'OAuth2\Storage\JwtAccessToken',
            ],
            'grantTypes' => [
                'client_credentials' => [
                    'class' => 'OAuth2\GrantType\ClientCredentials',
                    'allow_public_clients' => false
                ],
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials'
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ]
            ],
        ],
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];


if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;

<?php

return YII_ENV == 'dev' ? [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1:3308;dbname=dbapi',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
        ] : [// production db connection
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1:3308;dbname=dbapi',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];

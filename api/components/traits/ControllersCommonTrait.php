<?php

/**
 * Trait that contains needed behaviors for protect controller by OAuth2

 * @author Ihor Karas <ihor@karas.in.ua>
 * Date: 20.04.15
 * Time: 18:54
 */

namespace api\components\traits;

use Yii;
use api\components\filters\OAuth2AccessFilter;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use api\models\Client;
use api\models\User;

trait ControllersCommonTrait
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // replace to top contentNegotiator filter for displaying errors in correct format
        $content_negotiator = $behaviors['contentNegotiator'];
        unset($behaviors['contentNegotiator']);
        $content_negotiator['formats'] = Yii::$app->params['formats'];

        $behaviors = ArrayHelper::merge(
            [
                'contentNegotiator' => $content_negotiator,
                'oauth2access' => [// should be before "authenticator" filter
                    'class' => OAuth2AccessFilter::className()
                ],
//                'authenticator' => [
//                    'class' => CompositeAuth::className(),
//                    'authMethods' => [
//                        ['class' => HttpBearerAuth::className()],
//                        ['class' => QueryParamAuth::className(), 'tokenParam' => 'accessToken'],
//                    ]
//                ],
                'exceptionFilter' => [
                    'class' => ErrorToExceptionFilter::className()
                ],
            ],
            $behaviors,
            [
                'access' => [// need to set after contentNegotiator filter for caching errors
                    'class' => AccessControl::className(),
                    'rules' => $this->accessRules(),
                    'ruleConfig' => ['class' => 'api\components\AccessRule'],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => $this->verbs(),
                ],
            ]
        );

        return $behaviors;
    }

    /**
     * Access rules for access behavior
     * @return array
     */
    public function accessRules()
    {
        return [];
    }

    /**
     * Access rules for access behavior
     * @return array
     */
    public function verbs()
    {
        return [];
    }

    public function getUserData($type = '')
    {
        if (Yii::$app->hasModule('oauth2')) {
            /** @var \filsh\yii2\oauth2server\Module $oauth_module */
            $oauth_server = Yii::$app->getModule('oauth2')->getServer();
            $data = $oauth_server->getAccessTokenData(\OAuth2\Request::createFromGlobals());
            if (!empty($type) && isset($data[$type])) {
                return $data[$type];
            }
            return $data;
        }
        return null;
    }
    
    public function getClient()
    {
        $id = $this->getUserData('client_id');
        if (!empty($id)) {
            return Client::findOne($id);
        }
        return null;
    }
    
    public function getUser()
    {
        $id = $this->getUserData('user_id');
        if (!empty($id)) {
            return User::findOne($id);
        }
        return null;
    }
    
}

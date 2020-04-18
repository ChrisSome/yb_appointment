<?php
namespace center\controllers;

use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use common\models\Redis;

/**
 * 访问控制总控制器，所有的控制器的父类
 * @todo 忽略列表需要补充完全
 */
class ValidateController extends Controller
{

    // 忽略列表，列表中不做权限验证
    private $ignoreList = [
        'appointment/import-mobile/download',
        'report/welcome/index',
        'api/appointment/demo',
        'api/appointment/apply'
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied by default
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'test' : null,
                'width' => 120,
                'height' => 46,
                'transparent' => true,
                'maxLength' => 6,
                'minLength' => 6,
            ],
        ];
    }


    /**
     * 在程序执行之前，对访问的方法进行权限验证.
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        //如果没有配置 两个参数，则直接略过云端验证
        if(!empty(Yii::$app->params['dbConfig']['products_key']) && !empty(Yii::$app->params['dbConfig']['products_password'])){
            $this->actionAbsoluteTimeout();
        }

        $this->actionTimeOut();

        //如果未登录，则直接返回
        if (Yii::$app->user->isGuest) {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            header("Location:".$http_type.$_SERVER['HTTP_HOST']);
        }

        //获取路径
        $path = Yii::$app->request->pathInfo;

        //忽略列表
        if (in_array($path, $this->ignoreList)) {
            return true;
        }

        if (Yii::$app->user->can($path)) {
            return true;
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'message 401'));
        }
    }

    public function actionTimeOut()
    {
        if (!Yii::$app->user->getId()) {
            $session = Yii::$app->session;
            $url = 'returnUrl';
            if (Yii::$app->request->url == '/site/logout') {
                $session[$url] = '';
            } else {
                $session[$url] = Yii::$app->request->url;
            }
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'login timeout'));
            Yii::$app->user->logout();
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            header("Location:".$http_type.$_SERVER['HTTP_HOST']);
        }

        return true;
    }

    /**
     * 如果验证token不通过，时间(5分钟)到期就直接退出
     * 如果有token就验证token过期时间，快要过期就重新获取一下token
     * @return bool|\yii\web\Response
     */
    public function actionAbsoluteTimeout(){
        $absoluteTimeout = Yii::$app->session['noAuthLicenseTime'];
        if ($absoluteTimeout) {
            if(time()-$absoluteTimeout>300){
                $session = Yii::$app->session;
                $url = 'returnUrl';
                if (Yii::$app->request->url == '/site/logout') {
                    $session[$url] = '';
                } else {
                    $session[$url] = Yii::$app->request->url;
                }
                Yii::$app->user->logout();
                $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                header("Location:".$http_type.$_SERVER['HTTP_HOST']);
            }else{
                $token = Yii::$app->session['access_token'];
                if(empty($token)){
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'login timeout by reason', ['message'=>Yii::$app->session['cloud_err']]));
                }
            }
        }
        return true;
    }
}

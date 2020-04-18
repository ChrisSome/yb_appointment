<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/../../common/config/cloud_api.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php'),
    //引入 api 以及 app 接口地址
    require(__DIR__ . '/api_params.php'),
    require(__DIR__ . '/app_params.php')
);

return [
    'id' => 'app-center',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    //'sourceLanguage' => 'No-Country-language',
    'timeZone' => 'Asia/Shanghai',
    'controllerNamespace' => 'center\controllers',
    'modules' => [
        //日志模块
        'log' => [
            'class' => 'center\modules\log\Module',
        ],

        //权限模块
        'auth' => [
            'class' => 'center\modules\auth\Module',
        ],
        //核心模块
        'appointment' => [
            'class' => 'center\modules\appointment\Module',
        ],
        //设置模块
        'setting' => [
            'class' => 'center\modules\setting\Module',
        ],

        //report
        'report' => [
            'class' => 'center\modules\report\Module',
        ],
        //core
        'core' => [
            'class' => 'center\modules\core\Module',
        ],
        //接口模块
        'api' => [
            'class' => 'center\modules\api\Module',
        ],
    ],
    'components' => [
        'user' => [
            'class' => 'center\models\CustomUser',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/index'],
            'authTimeout' => 3600 //服务器保存session 时间
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            //'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => ['demo']],
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<modules:\w+>/<controller:\w+>/<action:\w+>' => '<modules>/<controller>/<action>',
            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'errorHandler' => [
            'errorAction' => '/report/welcome/error',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'timeFormat' => 'php:H:i:s',
        ],
    ],
    'params' => $params,
];

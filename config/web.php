<?php

$params = require(__DIR__ . '/params.php');
Yii::setAlias('@storage', dirname(__DIR__) . '/web/storage/');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'homeUrl' => '/',
    'components' => [
        'request' => [
            'cookieValidationKey' => '4Qtk8zBE-HVa8nz3vY3VI_5udEG8K4MK',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'messenger/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
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
        'db'         => require(__DIR__ . '/db.php'),
        'urlManager' => [
	        'enablePrettyUrl' => true,
	        'showScriptName'  => false,
	        'rules'           => [
		        ''            => 'messenger/index',
		        /*'<controller:\w+>/<id:\d+>'              => '<controller>/view',
		        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
		        '<controller:\w+>/<action:\w+>'          => '<controller>/<action>',*/
	        ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;

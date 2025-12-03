<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'igfr_portal',
    'name' => 'IGFR Portal',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-key', // ⚠️ change to a strong random string
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'authTimeout' => 900, // 15 minutes session timeout
            'loginUrl' => ['/ef/default/login'],
        ],

        'errorHandler' => [
            'class' => 'yii\web\ErrorHandler',
            'errorAction' => '/site/error',
        ],

        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'transport' => [
                'scheme' => 'smtp',
                'host' => 'smtp.gmail.com',
                'username' => 'help@ict.go.ke',
                'password' => 'vdmmqwzwxgeledly', // ⚠️ consider using environment variable
                'port' => 587,
                'encryption' => 'tls',
            ],
            'messageConfig' => [
                'from' => ['no-reply@ict.go.ke' => 'FiscalBridge System'],
            ],
            'useFileTransport' => false,
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

        'db' => $db,

        // ✅ Unified URL Manager
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                // ----------------------------
                // LETTER MODULE ROUTES
                // ----------------------------

                // Core letter routes
                'backend/letter' => 'backend/letter/index',
                'backend/letter/create' => 'backend/letter/create',
                'backend/letter/update/<id:\d+>' => 'backend/letter/update',
                'backend/letter/delete/<id:\d+>' => 'backend/letter/delete',
                'backend/letter/view/<id:\d+>' => 'backend/letter/view',

                // Workflow routes
                'backend/letter/action'   => 'backend/letter/action',
                'backend/letter/assign'   => 'backend/letter/assign',
                'backend/letter/complete' => 'backend/letter/complete',

                // ✅ Added missing workflow routes
                'backend/letter/assign-division/<id:\d+>' => 'backend/letter/assign-division',
                'backend/letter/assign-officer/<id:\d+>'  => 'backend/letter/assign-officer',
                'backend/letter/revert/<id:\d+>'          => 'backend/letter/revert',
                'backend/letter/forward/<id:\d+>'         => 'backend/letter/forward',

                // LetterAction routes
                'backend/letter-action/<action:\w+>/<id:\d+>' => 'backend/letter-action/<action>',
                'backend/letter-action/<action:\w+>' => 'backend/letter-action/<action>',

                // Backend dashboard
                'backend' => 'backend/default/index',

                // Divisions routes
                'divisions' => 'backend/division/index',

                // Default
                '' => '/ef/default/dashboard',
            ],
        ],

        // ✅ Workflow configuration
        'workflowSource' => [
            'class' => 'raoul2000\workflow\source\file\WorkflowFileSource',
        ],
    ],

    'params' => $params,

    'modules' => [
        'ef' => [
            'class' => 'app\modules\ef\Ef',
        ],
        //'backend' => [
        //    'class' => 'app\modules\backend\Backend',
        //],
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
    ],
];

// ✅ Developer tools for local environment
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;

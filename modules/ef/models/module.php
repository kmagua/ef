<?php

namespace app\modules\ef;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\ef\controllers';

    public function init()
    {
        parent::init();

        // Set custom layout path for EF module
        Yii::$app->layoutPath = '@app/modules/ef/views/layouts';
        Yii::$app->layout = 'main'; // will load ef/views/layouts/main.php
    }
}

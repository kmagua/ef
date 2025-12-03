<?php

namespace app\modules\ef;

/**
 * backend module definition class
 */
class Ef extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\ef\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->layout = 'layout.php';
        // custom initialization code goes here
    }
}

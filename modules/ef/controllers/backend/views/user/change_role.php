<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$this->title = 'Role Change';
/* @var $this yii\web\View */
/* @var $model app\models\ScoreItem */
/* @var $form yii\widgets\ActiveForm */
$usr_roles = yii\helpers\ArrayHelper::map(app\models\UserRole::find()->all(), 'id', 'role_name');
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
        
    <?= $form->field($model, 'roles')->widget(Select2::classname(), [
        'data' => $usr_roles,
        'language' => 'en',
        'options' => ['placeholder' => 'Select a role'],
        'pluginOptions' => [
            'allowClear' => true, 'multiple'=>true
        ],
    ]);
    
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

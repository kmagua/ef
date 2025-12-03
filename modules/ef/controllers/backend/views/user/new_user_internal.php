<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Internal User Registration';
$usr_roles = yii\helpers\ArrayHelper::map(app\models\UserRole::find()->where('role_name <> "county"')->all(), 'id', 'role_name');
$counties = yii\helpers\ArrayHelper::map(\app\models\County::find()->all(), 'id', 'county_name');
?>
<div class="user-form">
    <h3><?= $this->title ?></h3>
    <?php $form = ActiveForm::begin(); ?>    

    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Email') ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'roles')->widget(Select2::classname(), [
        'data' => $usr_roles,
        'language' => 'en',
        
        'options' => ['placeholder' => 'Select a role'],
        'pluginOptions' => [
            'allowClear' => true, 'multiple'=>true
        ],
        'pluginEvents' => [
            "change" => "$('#user-roles').find(':selected')",
        ]
    ]);    
    ?>
    
    <?= $form->field($model, 'county_id')->widget(Select2::classname(), [
        'data' => $counties,
        'language' => 'en',        
        'options' => ['placeholder' => 'Select a role'],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ])->label('County'); ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
    

    <div class="form-group">
        <div class="col-md-6">
       <?= Html::submitButton('Register', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

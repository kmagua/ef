<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
print_r($model->errors);
$counties = yii\helpers\ArrayHelper::map(\app\models\County::find()->all(), 'id', 'county_name');
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= ''//$form->field($model, 'kra_pin_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    
    <?php if($model->county_id != ''){ ?>
    <?= $form->field($model, 'county_id')->widget(Select2::classname(), [
        'data' => $counties,
        'language' => 'en',        
        'options' => ['placeholder' => 'Select a county'],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ])->label('County'); } ?>
    

    <?= $form->field($model, 'status')->dropDownList([1 => 'Active', 0 => 'Inactive']) ?>
    <div class="form-group">
        <div class="col-md-6">
       <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>


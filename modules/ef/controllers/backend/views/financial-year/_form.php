<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\FinancialYear $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="financial-year-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'financial_year')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'en',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => [
            'class' => 'form-control'
        ]
    ]) ?>
    
    <?= $form->field($model, 'end_date')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'en',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => [
            'class' => 'form-control'
        ]
    ]) ?>

    <?= $form->field($model, 'is_active')->dropDownList([1=> 'Active', 0=>'Inactive']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

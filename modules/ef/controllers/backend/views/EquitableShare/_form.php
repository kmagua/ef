<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\EquitableRevenueShare $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equitable-revenue-share-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'county_id')->textInput() ?>

    <?= $form->field($model, 'allocation_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_amt')->textInput() ?>

    <?= $form->field($model, 'actual_amt')->textInput() ?>

    <?= $form->field($model, 'balance_bf')->textInput() ?>

    <?= $form->field($model, 'osr_projected')->textInput() ?>

    <?= $form->field($model, 'osr_actual')->textInput() ?>

    <?= $form->field($model, 'fy')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

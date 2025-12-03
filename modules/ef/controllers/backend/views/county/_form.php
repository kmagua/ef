<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\County $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="county-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'RegionId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CountyName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CountyCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'size')->textInput() ?>

    <?= $form->field($model, 'population')->textInput() ?>

    <?= $form->field($model, 'poverty_index')->textInput() ?>

    <?= $form->field($model, 'gcp')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

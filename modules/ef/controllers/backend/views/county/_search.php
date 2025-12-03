<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\CountySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="county-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'CountyId') ?>

    <?= $form->field($model, 'CountyName') ?>

    <?= $form->field($model, 'CountyCode') ?>

    <?= $form->field($model, 'size') ?>

    <?= $form->field($model, 'population') ?>

    <?php // echo $form->field($model, 'poverty_index') ?>

    <?php // echo $form->field($model, 'gcp') ?>

    <?php // echo $form->field($model, 'region_code') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

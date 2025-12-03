<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\EquitableShareSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equitable-revenue-share-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'county_id') ?>

    <?= $form->field($model, 'allocation_code') ?>

    <?= $form->field($model, 'project_id') ?>

    <?= $form->field($model, 'project_amt') ?>

    <?php // echo $form->field($model, 'actual_amt') ?>

    <?php // echo $form->field($model, 'balance_bf') ?>

    <?php // echo $form->field($model, 'osr_projected') ?>

    <?php // echo $form->field($model, 'osr_actual') ?>

    <?php // echo $form->field($model, 'fy') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

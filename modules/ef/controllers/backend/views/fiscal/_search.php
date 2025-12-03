<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\FiscalSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fiscal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'countyid') ?>

    <?= $form->field($model, 'fy') ?>

    <?= $form->field($model, 'development_budgement') ?>

    <?= $form->field($model, 'recurrent_budget') ?>

    <?php // echo $form->field($model, 'total_revenue') ?>

    <?php // echo $form->field($model, 'actual_revenue') ?>

    <?php // echo $form->field($model, 'recurrent_expenditure') ?>

    <?php // echo $form->field($model, 'development_expenditure') ?>

    <?php // echo $form->field($model, 'target_osr') ?>

    <?php // echo $form->field($model, 'actual_osr') ?>

    <?php // echo $form->field($model, 'personal_emoluments') ?>

    <?php // echo $form->field($model, 'pending_bills') ?>

    <?php // echo $form->field($model, 'date_created') ?>

    <?php // echo $form->field($model, 'added_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

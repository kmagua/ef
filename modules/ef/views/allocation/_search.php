<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\AllocationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="allocation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'financial_year') ?>

    <?= $form->field($model, 'base_year') ?>

    <?= $form->field($model, 'audited_revenues') ?>

    <?= $form->field($model, 'ef_allocation') ?>

    <?php // echo $form->field($model, 'ef_entitlement') ?>

    <?php // echo $form->field($model, 'amount_reflected_dora') ?>

    <?php // echo $form->field($model, 'date_added') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

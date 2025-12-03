<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoProjectsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equalization-two-projects-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'county') ?>

    <?= $form->field($model, 'constituency') ?>

    <?= $form->field($model, 'ward') ?>

    <?= $form->field($model, 'marginalised_area') ?>

    <?php // echo $form->field($model, 'project_description') ?>

    <?php // echo $form->field($model, 'sector') ?>

    <?php // echo $form->field($model, 'project_budget') ?>

    <?php // echo $form->field($model, 'financial_year') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

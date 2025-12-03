<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationFundProject $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equalization-fund-project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'project_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'county')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'constituency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sector')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'budget_2018_19')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contract_sum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'percent_completion')->textInput() ?>

    <?= $form->field($model, 'funding_source')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

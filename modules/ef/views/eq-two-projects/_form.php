<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoProjects $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equalization-two-projects-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'county')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'constituency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ward')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'marginalised_area')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sector')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_budget')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'financial_year')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

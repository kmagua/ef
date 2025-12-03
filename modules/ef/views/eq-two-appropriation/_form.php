<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoAppropriation $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equalization-two-appropriation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'county')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'constituency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ward')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'marginalised_areas')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'allocation_ksh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'financial_year')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\LetterAction $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="letter-action-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'action_name')->dropDownList([ 'Assign' => 'Assign', 'Mark Complete' => 'Mark Complete', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'assigned_to')->textInput() ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action_by')->textInput() ?>

    <?= $form->field($model, 'file_upload')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_actioned')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

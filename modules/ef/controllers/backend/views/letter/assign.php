<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\LetterAction $model */
/** @var yii\widgets\ActiveForm $form */
$users = \yii\helpers\ArrayHelper::map(\app\models\User::find()->all(), 'id', 'user_names');
?>

<div class="letter-action-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'action_name')->dropDownList($st, ['prompt' => '']) ?>
    
    <?= $form->field($model, 'assigned_to')->widget(Select2::classname(), [
        'data' => $users,
        'language' => 'en',
        'options' => ['placeholder' => 'Select an assignee ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'file_upload_file')->fileInput(['class' => 'form-control']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success',
            'onclick' => 'saveDataForm(this); return false;']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

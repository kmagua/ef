<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentLibrary $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="document-library-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'document_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'document_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'financial_year')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'document_upload_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'keywords')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'upload_date')->textInput() ?>

    <?= $form->field($model, 'uploaded_by')->textInput() ?>

    <?= $form->field($model, 'publish_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'published_date')->textInput() ?>

    <?= $form->field($model, 'published_by')->textInput() ?>

    <?= $form->field($model, 'document_date')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'deleted_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

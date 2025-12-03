<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentLibrary $model */
/** @var yii\widgets\ActiveForm $form */

$fys = \yii\helpers\ArrayHelper::map(app\modules\backend\models\FinancialYear::find()->all(), 'id', 'financial_year');
$dts = \yii\helpers\ArrayHelper::map(app\modules\backend\models\DocumentType::find()->all(), 'id', 'document_type');
?>

<div class="document-library-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'document_name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'document_type')->widget(Select2::classname(), [
        'data' => $dts,
        'language' => 'en',
        'options' => ['placeholder' => 'Select a document type ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <?= $form->field($model, 'financial_year')->widget(Select2::classname(), [
        'data' => $fys,
        'language' => 'en',
        'options' => ['placeholder' => 'Select a Financial year ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <?= $form->field($model, 'document_upload_path_file')->fileInput(['class' => 'form-control']) ?>

    <?= $form->field($model, 'keywords')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($model, 'document_date')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'en',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => [
            'class' => 'form-control'
        ]
    ]) ?>
    
    <?= $form->field($model, 'applicable_to')->dropDownList(['IGFR' => 'IGFR', 'Equalization Fund'], 
            ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

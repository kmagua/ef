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

<style>
    :root {
        --primary-color: #008a8a;
        --primary-dark: #006666;
        --primary-light: #e0f7fa;
        --success-color: #28a745;
        --text-dark: #333;
        --text-light: #666;
        --border-color: #dee2e6;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
        --shadow-focus: 0 0 0 3px rgba(0, 138, 138, 0.1);
    }

    .document-library-form {
        max-width: 900px;
        margin: 0 auto;
    }

    /* Form Fields */
    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        display: block;
        font-size: 0.95rem;
    }

    .form-group label::after {
        content: ' *';
        color: #dc3545;
    }

    .form-group label.optional::after {
        content: '';
    }

    .form-control {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-focus);
        outline: none;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    /* File Input Styling */
    .file-input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .file-input-wrapper input[type="file"] {
        padding: 12px;
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-input-wrapper input[type="file"]:hover {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }

    .file-input-hint {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 5px;
        display: block;
    }

    /* Select2 Styling */
    .select2-container--krajee .select2-selection {
        border: 2px solid var(--border-color) !important;
        border-radius: 8px !important;
        min-height: 45px !important;
        transition: all 0.3s ease !important;
    }

    .select2-container--krajee.select2-container--focus .select2-selection {
        border-color: var(--primary-color) !important;
        box-shadow: var(--shadow-focus) !important;
    }

    .select2-container--krajee .select2-selection__rendered {
        padding-top: 8px !important;
        padding-left: 12px !important;
    }

    /* Date Picker Styling */
    .hasDatepicker {
        border: 2px solid var(--border-color) !important;
        border-radius: 8px !important;
        padding: 12px 15px !important;
    }

    .hasDatepicker:focus {
        border-color: var(--primary-color) !important;
        box-shadow: var(--shadow-focus) !important;
    }

    /* Help Text */
    .help-block {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 5px;
    }

    /* Error Messages */
    .help-block-error {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid var(--border-color);
        flex-wrap: wrap;
    }

    .btn-modern {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow);
        font-size: 0.95rem;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
    }

    .btn-primary-modern:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        color: #fff;
    }

    .btn-secondary-modern {
        background: #6c757d;
        color: #fff;
    }

    .btn-secondary-modern:hover {
        background: #5a6268;
        color: #fff;
    }

    /* Field Icons */
    .field-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
        pointer-events: none;
    }

    .form-group {
        position: relative;
    }

    /* Required Field Indicator */
    .required-indicator {
        color: #dc3545;
        margin-left: 3px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="document-library-form">
    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'modern-form'
        ]
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'document_name', [
                'template' => '<label>{label}</label>{input}{hint}{error}',
            ])->textInput([
                'maxlength' => true,
                'placeholder' => 'Enter document name',
                'class' => 'form-control'
            ])->hint('Enter a descriptive name for the document') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'document_type', [
                'template' => '<label>{label}</label>{input}{hint}{error}',
            ])->widget(Select2::classname(), [
                'data' => $dts,
                'language' => 'en',
                'options' => [
                    'placeholder' => 'Select document type...',
                    'class' => 'form-control'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->hint('Choose the type of document') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'financial_year', [
                'template' => '<label>{label}</label>{input}{hint}{error}',
            ])->widget(Select2::classname(), [
                'data' => $fys,
                'language' => 'en',
                'options' => [
                    'placeholder' => 'Select financial year...',
                    'class' => 'form-control'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->hint('Select the financial year') ?>
        </div>
    </div>

    <?php 
    // Hide category field for EF portal - automatically set to 'equalization_fund'
    echo $form->field($model, 'category')->hiddenInput(['value' => 'equalization_fund'])->label(false);
    ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'document_upload_path_file', [
                'template' => '<label>{label}</label><div class="file-input-wrapper">{input}</div>{hint}{error}',
            ])->fileInput([
                'class' => 'form-control',
                'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png'
            ])->hint('Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, JPEG, PNG (Max size: 10MB)') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'document_date', [
                'template' => '<label>{label} <span class="text-muted">(Optional)</span></label>{input}{hint}{error}',
            ])->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'en',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'Select document date'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => '2000:2030',
                ]
            ])->hint('Date when the document was created') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'keywords', [
                'template' => '<label>{label} <span class="text-muted">(Optional)</span></label>{input}{hint}{error}',
            ])->textarea([
                'rows' => 4,
                'placeholder' => 'Enter keywords separated by commas (e.g., budget, allocation, report)',
                'class' => 'form-control'
            ])->hint('Enter keywords to help users find this document easily') ?>
        </div>
    </div>

    <div class="form-actions">
        <?= Html::submitButton('<i class="fas fa-save"></i> Upload Document', [
            'class' => 'btn btn-modern btn-primary-modern'
        ]) ?>
        
        <?= Html::a('<i class="fas fa-times"></i> Cancel', ['index'], [
            'class' => 'btn btn-modern btn-secondary-modern'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

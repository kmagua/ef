<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Fiscal $model */
/** @var yii\widgets\ActiveForm $form */
$fys = ArrayHelper::map(app\modules\backend\models\FinancialYear::find()->all(), 'financial_year', 'financial_year');
$cnt = ArrayHelper::map(app\modules\backend\models\County::find()->all(), 'CountyId', 'CountyName');
$projs = ArrayHelper::map(app\modules\backend\models\Projects::find()->all(), 'id', 'project_name');
$allocs = ArrayHelper::map(app\modules\backend\models\AllocationType::find()->all(), 'id', 'description');
?>

<div class="fiscal-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <div class="row">
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'countyid')->widget(Select2::classname(), [
                'data' => $cnt,
                'language' => 'en',
                'options' => ['placeholder' => 'Select ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
        </div>
        
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'fy')->widget(Select2::classname(), [
                'data' => $fys,
                'language' => 'en',
                'options' => ['placeholder' => 'Select ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'development_budgement')->textInput() ?>
        </div>
        
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'recurrent_budget')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'total_revenue')->textInput() ?>
        </div>
        
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'actual_revenue')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'recurrent_expenditure')->textInput() ?>
        </div>
        
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'development_expenditure')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'target_osr')->textInput() ?>
        </div>
        
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'actual_osr')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'personal_emoluments')->textInput() ?>
        </div>
        
        <div class="col-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'pending_bills')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

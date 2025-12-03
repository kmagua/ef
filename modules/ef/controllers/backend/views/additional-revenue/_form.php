<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\AdditionalRevenue $model */
/** @var yii\widgets\ActiveForm $form */
$fys = ArrayHelper::map(app\modules\backend\models\FinancialYear::find()->all(), 'financial_year', 'financial_year');
$cnt = ArrayHelper::map(app\modules\backend\models\County::find()->all(), 'CountyId', 'CountyName');
$projs = ArrayHelper::map(app\modules\backend\models\Projects::find()->all(), 'id', 'project_name');
$allocs = ArrayHelper::map(app\modules\backend\models\AllocationType::find()->all(), 'id', 'description');
?>

<div class="additional-revenue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fy')->widget(Select2::classname(), [
        'data' => $fys,
        'language' => 'en',
        'options' => ['placeholder' => 'Select ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <?= $form->field($model, 'county_id')->widget(Select2::classname(), [
        'data' => $cnt,
        'language' => 'en',
        'options' => ['placeholder' => 'Select ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
        'data' => $projs,
        'language' => 'en',
        'options' => ['placeholder' => 'Select ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <?= $form->field($model, 'project_amt')->textInput() ?>

    <?= $form->field($model, 'actual_amt')->textInput() ?>

    <?= $form->field($model, 'allocation_code')->widget(Select2::classname(), [
        'data' => $allocs,
        'language' => 'en',
        'options' => ['placeholder' => 'Select ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

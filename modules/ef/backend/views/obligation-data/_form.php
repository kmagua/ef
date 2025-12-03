<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/** @var yii\web\View $this */
/** @var app\modules\backend\models\ObligationData $model */
/** @var yii\widgets\ActiveForm $form */
$fys = ArrayHelper::map(app\modules\backend\models\FinancialYear::find()->all(), 'financial_year', 'financial_year');
?>

<div class="obligation-data-form">
    
    <h3><?= 'Obligation Name: <u>' .  Html::encode($model->obligation->description) . '</u>' ?></h3>

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'fy')->widget(Select2::classname(), [
        'data' => $fys,
        'language' => 'en',
        'options' => ['placeholder' => 'Select ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <?= $form->field($model, 'amt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success',
            'onclick' => 'saveDataForm(this); return false;']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

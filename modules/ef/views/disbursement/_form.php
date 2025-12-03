<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\Disbursement $model */
/** @var yii\widgets\ActiveForm $form */
$years = yii\helpers\ArrayHelper::map(\app\modules\backend\models\FinancialYear::find()->all(), '', '');
?>

<div class="disbursement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'county')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sector')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fiscal_year')->dropDownList($years) ?>

    <?= $form->field($model, 'amount_disbursed')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

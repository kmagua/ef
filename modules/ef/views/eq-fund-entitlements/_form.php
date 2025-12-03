<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualisationFundEntitlements $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equalisation-fund-entitlements-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'financial_year')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'base_year_most_recent_audited_revenue')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'audited_approved_revenue_ksh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ef_entitlement_ksh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount_reflected_in_dora_ksh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transfers_into_ef')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'arrears')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

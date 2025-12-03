<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualisationFundEntitlementsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equalisation-fund-entitlements-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'financial_year') ?>

    <?= $form->field($model, 'base_year_most_recent_audited_revenue') ?>

    <?= $form->field($model, 'audited_approved_revenue_ksh') ?>

    <?= $form->field($model, 'ef_entitlement_ksh') ?>

    <?php // echo $form->field($model, 'amount_reflected_in_dora_ksh') ?>

    <?php // echo $form->field($model, 'transfers_into_ef') ?>

    <?php // echo $form->field($model, 'arrears') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

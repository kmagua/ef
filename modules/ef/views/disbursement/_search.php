<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\DisbursementSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="disbursement-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-3 col-sm-12 col-md-3">
            <?= $form->field($model, 'county')->dropDownList(
                \yii\helpers\ArrayHelper::map(
                    \app\modules\ef\models\EqualizationFundProject::find()->select('county')->distinct()->all(),
                    'county',
                    'county'
                ),
                ['prompt' => 'Select County']
            ) ?>
        </div>
        <div class="col-3 col-sm-12 col-md-3">
            <?= $form->field($model, 'sector') ?>
        </div>
        <div class="col-3 col-sm-12 col-md-3">
            <?= $form->field($model, 'fiscal_year') ?>
        </div>
        <div class="col-3 col-sm-12 col-md-3">
            <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>
        </div>
    </div>

    

    

    

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'date_disbursed') ?>

    

    <?php ActiveForm::end(); ?>

</div>

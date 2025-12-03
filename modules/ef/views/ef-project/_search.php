<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationFundProjectSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="equalization-fund-project-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'project_name') ?>

<?= $form->field($model, 'county')->dropDownList(
    \yii\helpers\ArrayHelper::map(
        \app\modules\ef\models\EqualizationFundProject::find()->select('county')->distinct()->all(),
        'county',
        'county'
    ),
    ['prompt' => 'Select County']
) ?>

    <?= $form->field($model, 'constituency') ?>

    <?= $form->field($model, 'sector') ?>

    <?php // echo $form->field($model, 'budget_2018_19') ?>

    <?php // echo $form->field($model, 'contract_sum') ?>

    <?php // echo $form->field($model, 'percent_completion') ?>

    <?php // echo $form->field($model, 'funding_source') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

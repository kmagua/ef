<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ExternalEntity $model */
/** @var yii\widgets\ActiveForm $form */
$mdas = \yii\helpers\ArrayHelper::map(
    \app\modules\backend\models\ExternalEntity::find()->where(['type' => 'Government MDA'])->all(), 'id', 'entity_name');

?>

<div class="external-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'entity_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList([ 'Government MDA' => 'Government MDA', 'Non-Governmental' => 'Non-Governmental', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'parent_mda')->widget(Select2::classname(), [
        'data' => $mdas,
        'language' => 'en',
        'options' => ['placeholder' => 'Select an MDA ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'po_box')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'physical_address')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

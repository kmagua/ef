<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\Allocation $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="allocation-form container mt-4">

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Allocation Details</h5>
        </div>

        <div class="card-body">

            <?php $form = ActiveForm::begin([
                'options' => ['class' => 'needs-validation'],
            ]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'financial_year')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'base_year')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'audited_revenues')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ef_allocation')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'ef_entitlement')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'amount_reflected_dora')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'date_added')->input('date') ?>
                </div>
            </div>

            <div class="form-group mt-3">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success px-4']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary px-4 ms-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>

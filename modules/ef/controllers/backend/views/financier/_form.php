<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Financier $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="financier-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'financier_name')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

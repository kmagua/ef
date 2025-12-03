<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Letter $model */
/** @var yii\widgets\ActiveForm $form */
$entities = \yii\helpers\ArrayHelper::map(\app\modules\backend\models\ExternalEntity::find()->all(), 'id', 'entity_name');
$counties = \yii\helpers\ArrayHelper::map(\app\modules\backend\models\County::find()->all(), 'CountyId', 'CountyName');
$users = \yii\helpers\ArrayHelper::map(\app\models\User::find()->all(), 'id', 'user_names');
?>

<div class="letter-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'entity_id')->widget(Select2::classname(), [
        'data' => $entities,
        'language' => 'en',
        'options' => [
            'placeholder' => 'Select an Enity ...',
            'onchange' => 'showHideCounty(this.value); return true;'
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    
    
    <?= $form->field($model, 'from_county')->widget(Select2::classname(), [
        'data' => $counties,
        'language' => 'en',
        'options' => ['placeholder' => 'Select a County ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    
    <?= $form->field($model, 'letter_date')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'en',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => [
            'class' => 'form-control'
        ]
    ]) ?>
    
    <?= $form->field($model, 'reference_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'letter_upload')->fileInput(['class' => 'form-control']) ?>
    
    <?= $form->field($model, 'assign_to')->widget(Select2::classname(), [
        'data' => $users,
        'language' => 'en',
        'options' => ['placeholder' => 'Select an assignee ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
<?php
$this->registerJs(
    "
    function showHideCounty(sel_val)
    {
        if(Number(sel_val) == 6){
            $('div.field-letter-from_county').show('slow');
        }else{
            $('#letter-from_county').val('');
            $('#letter-from_county').trigger('change.select2');
            $('div.field-letter-from_county').hide('slow');
        }
    }
"
    ,
    \yii\web\View::POS_END,
    'county-field-handler'
);
?>
</div>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Fiscal $model */

$this->title = 'FRP for ' . $model->county->CountyName . ' - ' . $model->fy;
$this->params['breadcrumbs'][] = ['label' => 'Fiscals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="fiscal-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'county.CountyName',
            'fy',
            [
                'attribute' => 'development_budgement',
                'value' => function ($model) {
                    return isset($model->development_budgement) ? Yii::$app->formatter->asDecimal($model->development_budgement, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'recurrent_budget',
                'value' => function ($model) {
                    return isset($model->recurrent_budget) ? Yii::$app->formatter->asDecimal($model->recurrent_budget, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'total_revenue',
                'value' => function ($model) {
                    return isset($model->total_revenue) ? Yii::$app->formatter->asDecimal($model->total_revenue, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'actual_revenue',
                'value' => function ($model) {
                    return isset($model->actual_revenue) ? Yii::$app->formatter->asDecimal($model->actual_revenue, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'recurrent_expenditure',
                'value' => function ($model) {
                    return isset($model->recurrent_expenditure) ? Yii::$app->formatter->asDecimal($model->recurrent_expenditure, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'development_expenditure',
                'value' => function ($model) {
                    return isset($model->development_expenditure) ? Yii::$app->formatter->asDecimal($model->development_expenditure, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'target_osr',
                'value' => function ($model) {
                    return isset($model->target_osr) ? Yii::$app->formatter->asDecimal($model->target_osr, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'actual_osr',
                'value' => function ($model) {
                    return isset($model->actual_osr) ? Yii::$app->formatter->asDecimal($model->actual_osr, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'personal_emoluments',
                'value' => function ($model) {
                    return isset($model->personal_emoluments) ? Yii::$app->formatter->asDecimal($model->personal_emoluments, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'attribute' => 'pending_bills',
                'value' => function ($model) {
                    return isset($model->pending_bills) ? Yii::$app->formatter->asDecimal($model->pending_bills, 0) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw', 
            ],
            [
                'label' => 'Compliance to Fiscal Principle on Development',
                'value' => function ($model) {
                    if($model->development_expenditure != '' && $model->total_budget != ''){
                        return number_format(($model->development_expenditure/$model->total_budget) *  100, 2).'%';
                    }
                    return null;
                },
                'format' => 'raw', 
            ],
            [
                'label' => 'Compliance to Fiscal Principle on Personal Emoluments',
                'value' => function ($model) {
                    if($model->personal_emoluments != '' && $model->total_revenue != ''){
                        return number_format(($model->personal_emoluments/$model->total_revenue) *  100, 2) .'%';
                    }
                    return null;
                },
                'format' => 'raw', 
            ],
            [
                'label' => 'Compliance to Fiscal Principle on Recurrent Expenditure',
                'value' => function ($model) {
                    if($model->recurrent_expenditure != '' && $model->total_revenue != ''){
                        return number_format($model->recurrent_expenditure/$model->total_revenue, 2);
                    }
                    return null;
                },
                'contentOptions' => [
                    'style' => ($model->recurrent_expenditure/$model->total_revenue > 1)? 'background-color:red !important':
                        'background-color:green !important',                        
                    
                ],
                'format' => 'raw', 
            ],
            [
                'label' => 'Actual Revenue vs Total Revenue',
                'value' => function ($model) {
                    if($model->actual_revenue != '' && $model->total_revenue != ''){
                        return number_format(($model->actual_revenue/$model->total_revenue)*100, 2) . '%';
                    }
                    return null;
                },                
                'format' => 'raw', 
            ],
            [
                'label' => 'County Effort in OSR Collection',
                'value' => function ($model) {
                    if($model->actual_osr != '' && $model->target_osr != ''){
                        return number_format(($model->actual_osr/$model->target_osr)*100, 2) . '%';
                    }
                    return null;
                },                
                'format' => 'raw', 
            ],            
            'date_created',
            'addedBy.user_names',
        ],
    ]) ?>

</div>

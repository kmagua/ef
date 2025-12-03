<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\EquitableRevenueShare $model */

$this->title = 'Equitable share for ' . $model->county->CountyName . ', f/y:'  . $model->fy;
$this->params['breadcrumbs'][] = ['label' => 'Equitable Revenue Shares', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="equitable-revenue-share-view">

    <h3><?= Html::encode($this->title) ?></h3>

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
           [
    'attribute' => 'project_amt',
    'value' => function ($model) {
        // This will format the number with 2 decimal places and include commas as thousand separators
        return Yii::$app->formatter->asDecimal($model->project_amt, 2);
    },
    'format' => 'raw', // This ensures that the formatting is applied as is
],

          //  'actual_amt',
          //  'balance_bf',
           // 'osr_projected',
           // 'osr_actual',
            'fy',
        ],
    ]) ?>

</div>

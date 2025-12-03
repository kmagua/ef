<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\Allocation $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Allocations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="allocation-view">

 <h1 style="
        color: #fff !important;
        background: linear-gradient(135deg, #008a8a, #00aaaa) !important;
        padding: 20px;
        border-radius: 8px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        font-size: 1.75rem;
        letter-spacing: 1.2px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        text-transform: uppercase;
        text-align: center;
    ">
        <?= Html::encode($this->title) ?>
    </h1>

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
            'id',
            'financial_year',
            'base_year',
            'audited_revenues',
            'ef_allocation',
            'ef_entitlement',
            'amount_reflected_dora',
            'date_added',
        ],
    ]) ?>

</div>

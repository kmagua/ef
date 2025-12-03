<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualisationFundEntitlements $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equalisation Fund Entitlements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="equalisation-fund-entitlements-view">

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
            'id',
            'financial_year',
            'base_year_most_recent_audited_revenue',
            'audited_approved_revenue_ksh',
            'ef_entitlement_ksh',
            'amount_reflected_in_dora_ksh',
            'transfers_into_ef',
            'arrears',
        ],
    ]) ?>

</div>

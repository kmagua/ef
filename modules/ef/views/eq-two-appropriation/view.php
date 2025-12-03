<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoAppropriation $model */

$this->title = "Appropriation #{$model->id}";
$this->params['breadcrumbs'][] = ['label' => 'Equalization Two Appropriations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Google Poppins Font
$this->registerCssFile(
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
    ['rel' => 'stylesheet']
);
?>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #eef3f2;
}

/* Container */
.equalization-view-container {
    background: #ffffff;
    padding: 28px;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    max-width: 1050px;
    margin: auto;
}

/* Header */
.header-banner {
    background: linear-gradient(135deg, #009688, #004d40);
    padding: 28px;
    border-radius: 12px;
    color: #fff !important;
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
    font-size: 1.9rem;
    letter-spacing: 1px;
    text-shadow: 0 2px 4px rgba(0,0,0,.35);
}

/* Action Buttons */
.action-btn {
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    margin-right: 8px;
}

.update-btn {
    background: #1976d2;
    color: white !important;
}
.update-btn:hover {
    background: #0d47a1;
}

.back-btn {
    background: #455a64;
    color: white !important;
}
.back-btn:hover {
    background: #263238;
}

/* Delete */
.delete-btn {
    background: #e53935;
    color: white !important;
}
.delete-btn:hover {
    background: #b71c1c;
}

/* Summary Card */
.summary-card {
    background: #e0f2f1;
    padding: 20px;
    border-radius: 12px;
    border-left: 5px solid #009688;
    margin-bottom: 30px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

.summary-card h4 {
    margin: 0;
    color: #004d40;
    font-size: 1rem;
    font-weight: 700;
}

.summary-item {
    margin-top: 10px;
    font-weight: 600;
    color: #004d40;
    font-size: 0.95rem;
}

.badge-tag {
    background: #b2dfdb;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    color: #004d40;
    font-size: 0.85rem;
}

/* DetailView Table */
.detail-view table {
    border-radius: 12px;
    overflow: hidden;
    width: 100%;
}

.detail-view th {
    background-color: #00695c !important;
    color: #ffffff !important;
    font-weight: 600;
    width: 28%;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
}

.detail-view td {
    background-color: #f1f8e9;
    font-weight: 500;
    white-space: normal !important;
    line-height: 1.5rem;
    font-size: 0.92rem;
}

.row-divider {
    height: 10px;
}

</style>

<div class="equalization-view-container">

    <!-- HEADER -->
    <div class="header-banner">
        <?= Html::encode($this->title) ?>
    </div>

    <!-- ACTION BUTTONS -->
    <p class="text-center">
        <?= Html::a('Update', ['update', 'id' => $model->id], [
            'class' => 'action-btn update-btn'
        ]) ?>

      

        <?= Html::a('Back to List', ['index'], [
            'class' => 'action-btn back-btn'
        ]) ?>
    </p>

    <!-- SUMMARY CARD -->
    <div class="summary-card">
        <h4>Quick Summary</h4>

        <div class="summary-item">
            <strong>County:</strong> 
            <span class="badge-tag"><?= $model->county ?></span>
        </div>

        <div class="summary-item">
            <strong>Appropriation Amount:</strong> 
            <span class="badge-tag">Ksh <?= number_format($model->allocation_ksh, 2) ?></span>
        </div>
    </div>

    <!-- DETAILS TABLE -->
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-bordered detail-view'],
        'attributes' => [
            [
                'attribute' => 'county',
                'label' => 'County',
                'value' => Html::tag('span', $model->county, ['class' => 'badge-tag']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'constituency',
                'label' => 'Constituency',
                'value' => Html::tag('span', $model->constituency, ['class' => 'badge-tag']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'ward',
                'label' => 'Ward',
                'value' => Html::tag('span', $model->ward, ['class' => 'badge-tag']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'marginalised_areas',
                'label' => 'Marginalised Areas',
                'value' => $model->marginalised_areas,
            ],
            [
                'attribute' => 'allocation_ksh',
                'label' => 'Allocation (Ksh)',
                'value' => 'Ksh ' . number_format($model->allocation_ksh, 2),
                'contentOptions' => ['class' => 'fw-bold text-success'],
            ],
        ],
    ]) ?>

</div>

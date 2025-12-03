<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = "Project #{$model->id}";
$this->params['breadcrumbs'][] = ['label' => 'Equalization Two Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap'
);
?>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #eef3f2;
}

/* Main wrapper */
.project-container {
    max-width: 900px;
    margin: 25px auto;
    background: white;
    padding: 25px 30px;
    border-radius: 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.10);
}

/* Title */
.project-title {
    background: linear-gradient(135deg, #009688, #00bfa5);
    color: white;
    padding: 16px;
    font-size: 1.7rem;
    font-weight: 700;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 22px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.25);
}

/* Buttons Row */
.btn-row {
    text-align: center;
    margin-bottom: 18px;
}

.btn-row .btn {
    padding: 10px 18px;
    font-weight: 600;
    border-radius: 8px;
    margin: 0 6px;
    font-size: 0.9rem;
}

/* Update */
.btn-update {
    background: #1976d2;
    color: white;
}
.btn-update:hover {
    background: #0f4fa3;
}

/* Back button */
.btn-back {
    background: #b2dfdb;
    color: #004d40;
}
.btn-back:hover {
    background: #9ad6cf;
    color: #004d40;
}

/* DetailView styling */
.detail-view th {
    background: #00695c !important;
    color: white !important;
    width: 240px;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    padding: 10px;
    vertical-align: top;
}

.detail-view td {
    background: #fafafa;
    font-size: 0.95rem;
    font-weight: 600;
    color: #004d40;
    padding: 10px;
    word-wrap: break-word !important;
    white-space: normal !important;
    max-width: 550px;
}

/* Badges */
.info-badge {
    background: #d9f5f1;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #00796b;
    display: inline-block;
}

/* Description Box */
.description-box {
    background: #f1fdfb;
    border-left: 5px solid #00bfa5;
    padding: 12px;
    border-radius: 6px;
    font-size: 0.95rem;
    line-height: 1.5;
    color: #004d40;
    white-space: normal !important;
    word-wrap: break-word !important;
}

/* Budget box */
.budget-box {
    padding: 10px 14px;
    background: #e3f7e8;
    border-left: 6px solid #2e7d32;
    font-weight: 700;
    font-size: 1rem;
    color: #1b5e20;
    border-radius: 6px;
}

</style>

<div class="project-container">

    <div class="project-title">
        <?= Html::encode($this->title) ?>
    </div>

    <div class="btn-row">
        <?= Html::a('Update Project', ['update', 'id' => $model->id], ['class' => 'btn btn-update']) ?>
        <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-back']) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-bordered detail-view'],
        'attributes' => [

            [
                'attribute' => 'county',
                'format' => 'raw',
                'value' => '<span class="info-badge">'.$model->county.'</span>',
            ],

            [
                'attribute' => 'constituency',
                'format' => 'raw',
                'value' => '<span class="info-badge">'.$model->constituency.'</span>',
            ],

            [
                'attribute' => 'ward',
                'format' => 'raw',
                'value' => '<span class="info-badge">'.$model->ward.'</span>',
            ],

            'marginalised_area',

            [
                'attribute' => 'project_description',
                'format' => 'raw',
                'value' =>
                    '<div class="description-box">' .
                    nl2br(Html::encode($model->project_description)) .
                    '</div>',
            ],

            [
                'attribute' => 'sector',
                'format' => 'raw',
                'value' => '<span class="info-badge">'.$model->sector.'</span>',
            ],

            [
                'attribute' => 'project_budget',
                'format' => 'raw',
                'value' =>
                    '<div class="budget-box">Ksh ' .
                    number_format($model->project_budget, 2) .
                    '</div>',
            ],
        ],
    ]) ?>

</div>

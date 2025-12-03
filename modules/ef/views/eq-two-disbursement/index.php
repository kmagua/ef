<?php

use app\modules\ef\models\EqualizationTwoDisbursement;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\grid\ActionColumn;

/* ---------------------------
   FILTER DATASETS
---------------------------- */
$counties = ArrayHelper::map(
    EqualizationTwoDisbursement::find()->select('county')->distinct()->orderBy('county')->all(),
    'county', 'county'
);

/* ---------------------------
   TITLE & FONTS
---------------------------- */
$this->title = strtoupper('2nd Marginalization Policy - Disbursements');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
?>

<style>
/* GLOBAL */
body {
    font-family: 'Poppins', sans-serif;
    background: #eef3f2;
}

/* PAGE WRAPPER */
.disbursement-index {
    background: #fff;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.10);
}

/* TITLE */
.disbursement-index h1 {
    background: linear-gradient(135deg, #00695c, #00bfa5);
    color: #ffffff !important;
    padding: 26px;
    border-radius: 14px;
    text-align: center;
    font-weight: 800;
    font-size: 2rem;
    letter-spacing: 1px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
}

/* SUMMARY CARDS */
.summary-card {
    background: #e0f7fa;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.08);
    margin-bottom: 16px;
}
.summary-card h4 {
    margin: 0;
    font-size: .9rem;
    color: #006064;
    font-weight: 600;
    text-transform: uppercase;
}
.summary-card .value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #004d40;
}

/* SEARCH FORM */
.search-form {
    background: #e0f2f1;
    padding: 22px;
    border-radius: 14px;
    border-left: 6px solid #00a58a;
    margin-bottom: 28px;
    box-shadow: 0px 2px 10px rgba(0,0,0,0.05);
}

.select2-container .select2-selection--single {
    height: 45px !important;
    padding-top: 6px;
    font-size: 14px;
    border: 1px solid #008f7a !important;
}

.select2-selection__placeholder {
    color: #009688 !important;
    font-weight: 500;
}

/* TABLE HEADER (PURE WHITE TEXT) */
.table thead th {
    background: #004d40 !important;
    color: #ffffff !important;
    font-weight: 800 !important;
    padding: 14px !important;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    text-align: center !important;
    white-space: nowrap;
    text-transform: uppercase;
}

/* FORCE WHITE LINK INSIDE THE HEADER */
.grid-view table thead th a {
    color: #ffffff !important;
    font-weight: 800 !important;
    text-decoration: none !important;
}
.grid-view table thead th a:hover {
    color: #e6e6e6 !important;
}

/* TABLE BODY */
.table tbody td {
    padding: 12px;
    font-size: 0.95rem;
    color: #003b3b !important;
}
.table tbody tr:hover {
    background: #d7f8ea !important;
}

/* ACTION BUTTONS */
.action-btn {
    padding: 7px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: white !important;
    text-decoration: none;
}

.view-btn { background: #1976d2; }
.view-btn:hover { background: #0d47a1; }

.edit-btn { background: #f9a825; }
.edit-btn:hover { background: #c68400; }
</style>

<div class="disbursement-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    /* SUMMARY AGGREGATES */
    $globalTotal = EqualizationTwoDisbursement::find()->sum('total_disbursement');
    $globalCount = EqualizationTwoDisbursement::find()->count();
    $pageTotal = array_sum(array_column($dataProvider->getModels(), 'total_disbursement'));
    ?>

    <!-- SUMMARY CARDS -->
    <div class="row mb-3">

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>Total Records</h4>
                <div class="value"><?= number_format($globalCount) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>Total Disbursed</h4>
                <div class="value">Ksh <?= number_format($globalTotal, 2) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>Filtered Records</h4>
                <div class="value"><?= count($dataProvider->getModels()) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>Filtered Disbursed</h4>
                <div class="value">Ksh <?= number_format($pageTotal, 2) ?></div>
            </div>
        </div>

    </div>

    <p><?= Html::a('Create Disbursement', ['create'], ['class' => 'btn btn-success mb-2']) ?></p>

    <!-- SEARCH FORM -->
    <div class="search-form">

        <?php $form = ActiveForm::begin(['method' => 'get']); ?>

        <div class="row">

            <!-- MULTIPLE COUNTY SELECTION -->
            <div class="col-md-4">
                <?= $form->field($searchModel, 'county')->widget(Select2::class, [
                    'data' => $counties,
                    'options' => ['placeholder' => 'Select County...', 'multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

        </div>

        <div class="text-end mt-2">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary px-4']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary px-4']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>


    <!-- MAIN TABLE -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-striped table-hover'],

        'columns' => [

            ['class' => 'yii\grid\SerialColumn'],

            'county',

            [
                'attribute' => 'approved_budget',
                'contentOptions' => ['class' => 'text-end fw-bold'],
                'value' => fn($m) => number_format($m->approved_budget, 2),
            ],

            [
                'attribute' => 'total_disbursement',
                'contentOptions' => ['class' => 'text-end fw-bold'],
                'value' => fn($m) => number_format($m->total_disbursement, 2),
            ],

            [
                'class' => ActionColumn::class,
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => fn($url) => Html::a('View', $url, ['class' => 'action-btn view-btn']),
                    'update' => fn($url) => Html::a('Edit', $url, ['class' => 'action-btn edit-btn']),
                ],
            ],
        ],
    ]); ?>

</div>

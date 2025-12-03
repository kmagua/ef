<?php

use app\modules\ef\models\EqualizationTwoAppropriation;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\grid\ActionColumn;

/* ---------------------
    FILTER PRELOAD
---------------------- */
$selectedCounty = $searchModel->county;
$selectedConstituency = $searchModel->constituency;

$counties = ArrayHelper::map(
    EqualizationTwoAppropriation::find()->select('county')->distinct()->orderBy('county')->all(),
    'county', 'county'
);

$constituencies = $selectedCounty
    ? ArrayHelper::map(
        EqualizationTwoAppropriation::find()->select('constituency')->where(['county' => $selectedCounty])
            ->distinct()->orderBy('constituency')->all(),
        'constituency', 'constituency'
    ) : [];

$wards = $selectedConstituency
    ? ArrayHelper::map(
        EqualizationTwoAppropriation::find()->select('ward')->where(['constituency' => $selectedConstituency])
            ->distinct()->orderBy('ward')->all(),
        'ward', 'ward'
    ) : [];

/* ---------------------
    TITLES
---------------------- */
$this->title = strtoupper('Equalization Two Appropriations');
$this->params['breadcrumbs'][] = $this->title;

/* ---------------------
    LOAD FONTS
---------------------- */
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

?>

<style>

body {
    font-family: 'Poppins', sans-serif;
    background: #eef3f2;
}

/* WRAPPER */
.equalization-index {
    background: #fff;
    padding: 26px;
    border-radius: 14px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.10);
}

/* TITLE */
.equalization-index h1 {
    background: linear-gradient(135deg, #00695c, #00bfa5);
    color: #ffffff !important;
    padding: 24px;
    border-radius: 12px;
    text-align: center;
    font-weight: 700;
    font-size: 1.9rem;
    letter-spacing: 1px;
    text-shadow: 0px 2px 4px rgba(0,0,0,0.3);
}

/* SUMMARY CARDS */
.summary-card {
    background: #e0f7fa;
    padding: 14px 16px;
    border-radius: 10px;
    box-shadow: 0 2px 7px rgba(0,0,0,0.06);
    margin-bottom: 16px;
}
.summary-card h4 {
    margin: 0;
    font-size: .85rem;
    text-transform: uppercase;
    color: #006064;
}
.summary-card .value {
    font-size: 1.35rem;
    font-weight: 700;
    color: #004d40;
}

/* SEARCH FORM */
.search-form {
    background: #e0f2f1;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 28px;
    border-left: 5px solid #009688;
}

/* SELECT2 */
.select2-selection__rendered {
    padding-top: 6px !important;
}
.select2-container .select2-selection--single {
    height: 40px !important;
}

/* TABLE HEADER */
.table thead th {
    background: #004d40 !important;
    color: #ffffff !important;
    text-transform: uppercase;
    font-weight: 700;
    padding: 12px;
    font-size: 0.85rem;
    text-align: center;
    white-space: nowrap;
}

/* TABLE BODY */
.table tbody td {
    vertical-align: middle !important;
    font-size: .92rem;
}
.table tbody tr:hover {
    background: #d8f6e0 !important;
}
/* FULL FORCE WHITE HEADER TEXT (COVERS ALL CASES) */
.grid-view table thead th,
.grid-view table thead th a,
.table thead th,
.table thead th a {
    color: #ffffff !important;
    text-decoration: none !important;
}

/* OPTIONAL – hover should stay white too */
.grid-view table thead th a:hover,
.table thead th a:hover {
    color: #f2f2f2 !important;
}

/* WRAP TEXT FOR ALL CELLS */
.table td, .table th {
    white-space: normal !important;
    word-wrap: break-word !important;
}

/* COUNTY BADGE */
.county-label {
    background: #00695c;
    padding: 6px 12px;
    border-radius: 20px;
    color: white;
    font-weight: 600;
}

/* FOOTER */
.table tfoot td {
    background: #004d40 !important;
    color: white !important;
    font-weight: 700;
    text-align: right;
}

/* ACTION BUTTONS */
.action-btn {
    padding: 7px 14px;
    border-radius: 7px;
    color: white !important;
    font-size: 13px;
    margin: 0 4px;
    text-decoration: none;
}

/* View */
.view-btn {
    background: #1976d2;
}
.view-btn:hover { background: #0f4fa3; }

/* Edit */
.edit-btn {
    background: #f9a825;
}
.edit-btn:hover { background: #c78800; }

</style>


<div class="equalization-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    /* GLOBAL TOTALS */
    $globalTotal = EqualizationTwoAppropriation::find()->sum('allocation_ksh');
    $globalCount = EqualizationTwoAppropriation::find()->count();

    /* FILTERED TOTAL */
    $pageTotal = array_sum(array_column($dataProvider->getModels(), 'allocation_ksh'));
    ?>

    <!-- SUMMARY CARDS -->
    <div class="row mb-3">

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>All Records</h4>
                <div class="value"><?= number_format($globalCount) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>All Budget</h4>
                <div class="value">Ksh <?= number_format($globalTotal, 2) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>Filtered Count</h4>
                <div class="value"><?= number_format(count($dataProvider->getModels())) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <h4>Filtered Total</h4>
                <div class="value">Ksh <?= number_format($pageTotal, 2) ?></div>
            </div>
        </div>

    </div>

    <p><?= Html::a('Create Equalization Two Appropriation', ['create'], ['class' => 'btn btn-success mb-2']) ?></p>

    <div class="search-form">

        <?php $form = ActiveForm::begin(['method' => 'get']); ?>

        <div class="row">

            <div class="col-md-4">
                <?= $form->field($searchModel, 'county')->widget(Select2::class, [
                    'data' => $counties,
                    'options' => ['placeholder' => 'Select County', 'onchange' => 'this.form.submit()'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($searchModel, 'constituency')->widget(Select2::class, [
                    'data' => $constituencies,
                    'options' => [
                        'placeholder' => 'Select Constituency',
                        'disabled' => empty($constituencies),
                        'onchange' => 'this.form.submit()'
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($searchModel, 'ward')->widget(Select2::class, [
                    'data' => $wards,
                    'options' => [
                        'placeholder' => 'Select Ward',
                        'disabled' => empty($wards),
                        'onchange' => 'this.form.submit()'
                    ],
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
        'showFooter' => true,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],

        'columns' => [

            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'county',
                'format' => 'raw',
                'value' => fn($m) => "<span class='county-label'>{$m->county}</span>",
                'contentOptions' => ['style' => 'white-space:normal;']
            ],

            'constituency',
            'ward',
            'marginalised_areas',

            [
                'attribute' => 'allocation_ksh',
                'value' => fn($m) => number_format($m->allocation_ksh, 2),
                'contentOptions' => ['class' => 'text-end fw-bold'],
                'footer' => number_format($pageTotal, 2),
            ],

            [
                'class' => ActionColumn::class,
                'header' => 'Actions',

                'template' => '{view} {update}',

                'buttons' => [

                    'view' => fn($url) =>
                        Html::a('View', $url, ['class' => 'action-btn view-btn']),

                    'update' => fn($url) =>
                        Html::a('Edit', $url, ['class' => 'action-btn edit-btn']),
                ]
            ],

        ],
    ]); ?>

</div>

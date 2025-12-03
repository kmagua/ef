<?php

use app\modules\ef\models\Disbursement;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\DisbursementSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = strtoupper('Disbursements with Sector');
$this->params['breadcrumbs'][] = $this->title;

/* Load Poppins font */
$this->registerCssFile(
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'
);

/* ------------------------------------------------------
   GROUP BY SECTOR
---------------------------------------------------------*/
$rows = $dataProvider->getModels();
$grouped = [];
$totalDisbursed = 0;

foreach ($rows as $row) {
    $sector = $row->sector ?: 'Unknown';

    if (!isset($grouped[$sector])) {
        $grouped[$sector] = [
            'sector' => $sector,
            'total_disbursed' => 0,
            'count' => 0,
        ];
    }

    $grouped[$sector]['total_disbursed'] += (float)$row->amount_disbursed;
    $grouped[$sector]['count']++;
    $totalDisbursed += (float)$row->amount_disbursed;
}

$groupedProvider = new ArrayDataProvider([
    'allModels' => array_values($grouped),
    'pagination' => false
]);

?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #eef2f3;
        color: #333;
    }

    .disbursement-index {
        background: #ffffff;
        padding: 35px;
        border-radius: 14px;
        box-shadow: 0px 8px 22px rgba(0,0,0,0.10);
    }

    /* PAGE TITLE */
    .disbursement-index h1 {
        background: linear-gradient(135deg, #008a8a, #00baba);
        color: white !important;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        font-weight: 700;
        font-size: 26px;
        letter-spacing: 1px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }

    /* SEARCH BOX */
    .search-form {
        background: #e7f7f7;
        padding: 18px;
        border-radius: 10px;
        border-left: 5px solid #008a8a;
        margin-bottom: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    }

    .select2-selection {
        border: 1px solid #008a8a !important;
        border-radius: 6px !important;
        padding: 4px;
        min-height: 42px;
    }

    .select2-selection__choice {
        background-color: #008a8a !important;
        color: white !important;
        border-radius: 4px !important;
        margin-top: 4px !important;
    }

    /* TABLE HEADERS — WHITE TEXT */
    .table thead th {
        background-color: #008a8a !important;
        color: #ffffff !important;      /* ← WHITE TEXT */
        font-weight: 700;
        text-transform: uppercase;
        padding: 12px;
        text-align: center;
        border-color: #007070 !important;
    }

    .table thead th a {
        color: #ffffff !important;      /* ← WHITE LINKS */
    }

    /* TABLE BODY */
    .table tbody td {
        padding: 12px;
        font-size: 15px;
        color: #003b3b !important;
    }

    .table tbody tr:hover {
        background: #dff5f5 !important;
    }

    /* FOOTER */
    .table tfoot td {
        background: #008a8a !important;
        color: white !important;
        font-weight: bold;
        text-align: right;
    }

    /* BUTTONS */
    .btn-primary {
        background-color: #008a8a;
        border-color: #006f6f;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #006f6f;
    }

    .btn-outline-secondary {
        border-color: #008a8a;
        color: #008a8a;
        font-weight: 600;
    }

    .btn-outline-secondary:hover {
        background-color: #008a8a;
        color: white;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .disbursement-index {
            padding: 20px;
        }
        .table {
            overflow-x: auto;
            display: block;
        }
    }
</style>


<div class="disbursement-index">

<h1><?= Html::encode($this->title) ?></h1>

<!-- ====================== FILTER FORM ====================== -->
<div class="search-form">
<?php $form = ActiveForm::begin(['method' => 'get']); ?>

<div class="row">

    <!-- COUNTY -->
    <div class="col-md-4">
        <?= $form->field($searchModel, 'county')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                Disbursement::find()->select('county')->distinct()->orderBy('county')->all(),
                'county', 'county'
            ),
            'options' => ['placeholder' => 'Select County...', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>
    </div>

    <!-- SECTOR -->
    <div class="col-md-4">
        <?= $form->field($searchModel, 'sector')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                Disbursement::find()->select('sector')->distinct()->orderBy('sector')->all(),
                'sector', 'sector'
            ),
            'options' => ['placeholder' => 'Select Sector...', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>
    </div>

    <!-- FISCAL YEAR -->
    <div class="col-md-4">
        <?= $form->field($searchModel, 'fiscal_year')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                Disbursement::find()->select('fiscal_year')->distinct()->orderBy('fiscal_year')->all(),
                'fiscal_year', 'fiscal_year'
            ),
            'options' => ['placeholder' => 'Select Fiscal Year...', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>
    </div>

</div>

<div class="mt-3 text-end">
    <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
    <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
</div>


<!-- ====================== GROUPED SUMMARY ====================== -->
<h2 style="color:#008a8a; font-weight:700;">Sector Summary</h2>

<?= GridView::widget([
    'dataProvider' => $groupedProvider,
    'summary' => '',
    'showFooter' => true,
    'tableOptions' => ['class' => 'table table-bordered table-striped table-hover'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'sector',
            'label' => 'Sector',
            'contentOptions' => ['style' => 'font-weight:600; color:#008a8a;'],
            'footer' => '<strong>Total by Sector</strong>',
        ],

        [
            'attribute' => 'total_disbursed',
            'label' => 'Total Disbursed',
            'value' => fn($model) => number_format($model['total_disbursed'], 2),
            'contentOptions' => ['class' => 'text-end', 'style' => 'color:#008a8a; font-weight:600;'],
            'footer' => '<strong>' . number_format($totalDisbursed, 2) . '</strong>',
        ],

        [
            'attribute' => 'count',
            'label' => 'Entries',
            'contentOptions' => ['class' => 'text-center fw-bold'],
            'footer' => '<strong>' . array_sum(array_column($groupedProvider->getModels(), 'count')) . '</strong>',
        ],
    ],
]); ?>



<!-- ====================== DISBURSEMENT DETAILS ====================== -->
<h2 style="color:#008a8a; font-weight:700; margin-top:30px;">Disbursement Details</h2>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'tableOptions' => ['class' => 'table table-bordered table-striped'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'county',
        'sector',
        'fiscal_year',

        [
            'attribute' => 'amount_disbursed',
            'label' => 'Amount Disbursed',
            'value' => fn($model) => number_format($model->amount_disbursed, 2),
            'contentOptions' => ['style' => 'font-weight:bold; color:#008a8a; text-align:right;'],
            'footer' => '<strong>Total: ' . number_format($totalDisbursed, 2) . '</strong>',
        ],
    ],
]); ?>

</div>

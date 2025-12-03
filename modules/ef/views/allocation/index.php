<?php

use app\modules\ef\models\Allocation;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = strtoupper('Equalisation Fund Allocations from its Establishment');
$this->params['breadcrumbs'] = []; // Clear any default breadcrumbs
$this->params['breadcrumbs'][] = ['label' => 'Return to Dashboard', 'url' => ['/ef/default/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #eef2f3;
    }

    .allocation-index {
        background: white;
        padding: 35px;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .allocation-index h1 {
        background: linear-gradient(135deg, #009a9a, #007272);
        padding: 22px;
        border-radius: 12px;
        text-align: center;
        color: white;
        font-weight: 700;
    }

    /* Search form */
    .search-form {
        background: white;
        padding: 22px;
        border-left: 5px solid #009a9a;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    }

    .btn-primary {
        background: #009a9a !important;
        border-color: #006f6f !important;
        font-weight: 600;
    }
    .btn-primary:hover {
        background: #007272 !important;
    }

    .btn-outline-secondary {
        border-color: #009a9a !important;
        color: #009a9a !important;
        font-weight: bold;
    }
    .btn-outline-secondary:hover {
        background: #009a9a !important;
        color: white !important;
    }

    /* SUMMARY TABLE */
    .summary-table th {
        background: #009a9a !important;
        color: #ffffff !important;
        font-size: 1rem;
        font-weight: 800;
        padding: 14px;
        letter-spacing: .5px;
        border-bottom: 2px solid #007272 !important;
    }

    .summary-table td {
        background: #006f6f !important;
        color: #ffffff !important;
        font-size: 1.1rem;
        font-weight: 700;
        padding: 14px;
        border-top: 2px solid #004f4f !important;
    }

    /* GRID HEADER FIX – strong white text */
    .table thead th {
        background: #009a9a !important;
        color: #ffffff !important;
        font-weight: 900 !important;
        padding: 14px !important;
        font-size: 1rem;
        border-color: #007272 !important;
        text-align: center !important;
        letter-spacing: 0.6px;
        text-shadow: 0px 1px 2px rgba(0,0,0,0.35);
    }

    /* Make sure clickable sort links are also white */
    .table thead th a {
        color: #ffffff !important;
        font-weight: 900;
        text-decoration: none;
    }

    .table tbody td {
        padding: 10px;
        font-size: .95rem;
        color: #004747 !important;
        font-weight: 500;
        background: white;
        border-color: #d6d6d6;
    }

    .table tbody tr:hover {
        background: #e6f6f6 !important;
    }

    /* NEW: Row border glow on hover */
    tbody tr:hover td {
        box-shadow: inset 0 0 6px rgba(0,140,140,0.5);
    }

    /* NEW: Rounded header corners */
    .table thead th:first-child {
        border-top-left-radius: 10px;
    }
    .table thead th:last-child {
        border-top-right-radius: 10px;
    }
/* FORCE WHITE TEXT FOR ALLOCATIONS SUMMARY VALUES */
.summary-table tbody td {
    background: #006f6f !important;
    color: #ffffff !important;
    font-size: 1.2rem;
    font-weight: 700;
    text-shadow: 0px 1px 2px rgba(0,0,0,0.35);
}

    /* Chart styling */
    #allocationsBarChart {
        background: #ffffff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.10);
    }
</style>


<div class="allocation-index">
    <div style="margin-bottom: 20px;">
        
    </div>
    <h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('<i class="fa fa-file-pdf"></i> Generate Report', ['generate-report'], ['class' => 'btn btn-success']) ?>
</p>

<!-- SEARCH FORM -->
<div class="search-form">
<?php $form = ActiveForm::begin(['method' => 'get']); ?>

<div class="row">

    <!-- FINANCIAL YEAR MULTI -->
    <div class="col-md-4">
        <?= $form->field($searchModel, 'financial_year')->widget(Select2::class, [
            'data' => ArrayHelper::map(
                Allocation::find()->select('financial_year')->distinct()->orderBy('financial_year')->all(),
                'financial_year', 'financial_year'
            ),
            'options' => ['placeholder' => 'Select Financial Year...', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>

    <!-- BASE YEAR MULTI -->
    <div class="col-md-4">
        <?= $form->field($searchModel, 'base_year')->widget(Select2::class, [
            'data' => ArrayHelper::map(
                Allocation::find()->select('base_year')->distinct()->orderBy('base_year')->all(),
                'base_year', 'base_year'
            ),
            'options' => ['placeholder' => 'Select Base Year...', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>
</div>

<!-- SUMMARY -->
<h3 class="text-success fw-bold mt-4">Allocations Summary</h3>

<table class="table table-bordered text-center summary-table">
    <thead>
        <tr>
            <th>Total Audited Revenues</th>
            <th>Total EF Allocation</th>
            <th>Total EF Entitlement</th>
            <th>Total Amount (DORA)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= number_format(array_sum(array_column($dataProvider->getModels(), 'audited_revenues')), 2) ?></td>
            <td><?= number_format(array_sum(array_column($dataProvider->getModels(), 'ef_allocation')), 2) ?></td>
            <td><?= number_format(array_sum(array_column($dataProvider->getModels(), 'ef_entitlement')), 2) ?></td>
            <td><?= number_format(array_sum(array_column($dataProvider->getModels(), 'amount_reflected_dora')), 2) ?></td>
        </tr>
    </tbody>
</table>

<!-- GRID -->
<h2 class="mt-4 text-success fw-bold">Allocation Details</h2>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'tableOptions' => ['class' => 'table table-bordered table-striped'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        ['attribute' => 'financial_year', 'contentOptions' => ['class' => 'text-center fw-bold text-success']],
        ['attribute' => 'base_year', 'contentOptions' => ['class' => 'text-center fw-bold text-success']],
        [
            'attribute' => 'audited_revenues',
            'value' => fn($m) => number_format($m->audited_revenues, 2),
            'contentOptions' => ['class' => 'text-end fw-bold text-success']
        ],
        [
            'attribute' => 'ef_allocation',
            'value' => fn($m) => number_format($m->ef_allocation, 2),
            'contentOptions' => ['class' => 'text-end fw-bold text-success']
        ],
        [
            'attribute' => 'ef_entitlement',
            'value' => fn($m) => number_format($m->ef_entitlement, 2),
            'contentOptions' => ['class' => 'text-end fw-bold text-success']
        ],
        [
            'attribute' => 'amount_reflected_dora',
            'value' => fn($m) => number_format($m->amount_reflected_dora, 2),
            'contentOptions' => ['class' => 'text-end fw-bold text-success']
        ],

        [
            'class' => ActionColumn::class,
            'urlCreator' => fn($action, $model) => Url::toRoute([$action, 'id' => $model->id]),
            'contentOptions' => ['class' => 'text-center'],
        ],
    ],
]); ?>
</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
$financialYears = array_column($dataProvider->getModels(), 'financial_year');
$allocations = array_column($dataProvider->getModels(), 'ef_allocation');
?>

<div class="row mt-5">
    <div class="col-md-10 mx-auto">
        <h3 class="text-success fw-bold text-center">Allocations Over Financial Years</h3>
        <canvas id="allocationsBarChart"></canvas>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    new Chart(document.getElementById("allocationsBarChart"), {
        type: "bar",
        data: {
            labels: <?= json_encode($financialYears) ?>,
            datasets: [{
                label: "EF Allocation",
                data: <?= json_encode($allocations) ?>,
                backgroundColor: "#00b3b3",
                borderColor: "#007272",
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: { responsive: true }
    });
});
</script>

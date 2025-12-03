<?php

use app\modules\ef\models\Disbursement;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\DisbursementSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sector Disbursement Analytics';
$this->params['breadcrumbs'][] = $this->title;

// Process Data for Charts
$disbursements = $dataProvider->getModels();
$countyData = [];
$sectorData = [];
$sectorBreakdown = [];
$totalDisbursed = 0;

// Group data by County & Sector
foreach ($disbursements as $disbursement) {
    $county = $disbursement->county;
    $sector = $disbursement->sector;
    $amount = $disbursement->amount_disbursed;

    // Store total per county
    if (!isset($countyData[$county])) {
        $countyData[$county] = 0;
        $sectorBreakdown[$county] = [];
    }
    $countyData[$county] += $amount;

    // Store sector breakdown per county
    if (!isset($sectorBreakdown[$county][$sector])) {
        $sectorBreakdown[$county][$sector] = 0;
    }
    $sectorBreakdown[$county][$sector] += $amount;

    // Store total per sector
    if (!isset($sectorData[$sector])) {
        $sectorData[$sector] = 0;
    }
    $sectorData[$sector] += $amount;

    $totalDisbursed += $amount;
}

// Prepare Data for Donut Chart (Total per County)
$countyLabels = array_keys($countyData);
$countyValues = array_values($countyData);
$countyColors = array_map(fn() => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), $countyLabels);

// Prepare Data for Stacked Bar Chart (Sectors Most Disbursed)
$sectorLabels = array_keys($sectorData);
$sectorValues = array_values($sectorData);
$sectorColors = array_map(fn() => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), $sectorLabels);

// Convert Sector Breakdown into JSON for drill-down
$sectorBreakdownJson = json_encode($sectorBreakdown);

?>

<div class="disbursement-index">
  <h1 style="
    font-size: 28px; 
    font-weight: bold; 
    text-transform: uppercase; 
    background: #008a8a !important; 
    padding: 15px; 
    border-radius: 8px;
    display: inline-block;
    position: relative;
    margin-bottom: 20px;
">
    <span style="
        background: linear-gradient(to right, #ffffff, #e0e0e0); 
        -webkit-background-clip: text; 
        -webkit-text-fill-color: transparent; 
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4); 
        letter-spacing: 1px;">
        <?= Html::encode($this->title) ?>
    </span>
</h1>


  <!-- Search Form -->
<div class="search-form bg-white p-4 rounded shadow-sm">
    <?php $form = ActiveForm::begin(['method' => 'get']); ?>
    <div class="row align-items-end">
        <div class="col-md-5">
            <?= $form->field($searchModel, 'county')->dropDownList(
                ArrayHelper::map(Disbursement::find()->select('county')->distinct()->all(), 'county', 'county'),
                ['prompt' => 'Select County', 'class' => 'form-control custom-select']
            ) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($searchModel, 'sector')->dropDownList(
                ArrayHelper::map(Disbursement::find()->select('sector')->distinct()->all(), 'sector', 'sector'),
                ['prompt' => 'Select Sector', 'class' => 'form-control custom-select']
            ) ?>
        </div>
        <div class="col-md-2">
            <br>
            <?= Html::submitButton('<i class="fas fa-search"></i> Search', [
                'class' => 'btn btn-primary w-100 fw-bold',
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerCss("
    /* Search Form Styles */
    .search-form {
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .search-form .form-control {
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #ced4da;
    }

    .search-form .custom-select {
        background-color: white;
    }

    .search-form .btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        padding: 10px;
        font-size: 16px;
        transition: 0.3s ease-in-out;
    }

    .search-form .btn-primary:hover {
        background: linear-gradient(135deg, #0056b3, #003f7f);
        transform: scale(1.05);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .search-form .row {
            flex-direction: column;
        }
        .search-form .col-md-5, .search-form .col-md-2 {
            width: 100%;
            margin-bottom: 10px;
        }
    }
");
?>


  <!-- Donut Chart -->
<h2 class="mt-4 text-dark">Total Disbursement per County</h2>
<canvas id="countyDonutChart"></canvas>
<button id="resetDonutChart" class="btn btn-sm btn-outline-secondary mt-2">Reset to Counties</button>

<!-- Stacked Bar Chart -->
<h2 class="mt-4 text-dark">Top Funded Sectors</h2>
<canvas id="sectorBarChart"></canvas>

<!-- GridView Table -->
<h2 class="mt-4 text-dark">Disbursement Breakdown</h2>
<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => array_map(fn($sector, $total) => ['sector' => $sector, 'total_disbursed' => $total], array_keys($sectorData), $sectorData),
    ]),
    'showFooter' => true,
    'tableOptions' => ['class' => 'table table-striped table-bordered'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'sector',
            'label' => 'Sector',
            'contentOptions' => ['class' => 'fw-bold text-primary'],
            'footer' => '<strong>Total for the Sectors per County</strong>',
            'footerOptions' => ['class' => 'fw-bold text-dark text-end'],
        ],
        [
            'attribute' => 'total_disbursed',
            'label' => 'Total Disbursed',
            'value' => fn($model) => 'KES ' . number_format($model['total_disbursed'], 2),
            'contentOptions' => ['class' => 'fw-bold text-end text-success'],
            'footer' => '<strong>KES ' . number_format(array_sum($sectorData), 2) . '</strong>',
            'footerOptions' => ['class' => 'fw-bold text-end text-success'],
        ],
    ],
]); ?>

<?php
// Convert Data to JSON for JS
$countyLabelsJson = json_encode($countyLabels);
$countyValuesJson = json_encode($countyValues);
$countyColorsJson = json_encode($countyColors);

$sectorLabelsJson = json_encode($sectorLabels);
$sectorValuesJson = json_encode($sectorValues);
$sectorColorsJson = json_encode($sectorColors);

// JS for Charts with Drill-down & Reset Button
$this->registerJs(<<<JS

    var sectorBreakdown = $sectorBreakdownJson;
    var countyChart;
    var defaultLabels = $countyLabelsJson;
    var defaultValues = $countyValuesJson;
    var defaultColors = $countyColorsJson;

    function loadCountyChart(labels, values, colors) {
        var ctx = document.getElementById('countyDonutChart').getContext('2d');
        if (countyChart) countyChart.destroy();
        countyChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: tooltipItem => {
                                var total = values.reduce((a, b) => a + b, 0);
                                var percentage = ((tooltipItem.raw / total) * 100).toFixed(1);
                                return tooltipItem.label + ': KES ' + tooltipItem.raw.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    },
                    title: { display: true, text: 'Total Disbursement' }
                },
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        var index = elements[0].index;
                        var selectedCounty = labels[index];
                        if (sectorBreakdown[selectedCounty]) {
                            var sectorLabels = Object.keys(sectorBreakdown[selectedCounty]);
                            var sectorValues = Object.values(sectorBreakdown[selectedCounty]);
                            loadCountyChart(sectorLabels, sectorValues, $sectorColorsJson);
                        }
                    }
                }
            }
        });
    }

    // Initial Donut Chart Load
    loadCountyChart(defaultLabels, defaultValues, defaultColors);

    // Reset Button Handler
    document.getElementById('resetDonutChart').addEventListener('click', function() {
        loadCountyChart(defaultLabels, defaultValues, defaultColors);
    });

    // Stacked Bar Chart (Top Funded Sectors)
    var ctx2 = document.getElementById('sectorBarChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: $sectorLabelsJson,
            datasets: [{
                label: 'Total Disbursed',
                data: $sectorValuesJson,
                backgroundColor: $sectorColorsJson,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { 
                x: { stacked: true }, 
                y: { 
                    stacked: true, 
                    ticks: { callback: value => 'KES ' + value.toLocaleString() } 
                } 
            },
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: tooltipItem => {
                            var total = $sectorValuesJson.reduce((a, b) => a + b, 0);
                            var percentage = ((tooltipItem.raw / total) * 100).toFixed(1);
                            return tooltipItem.label + ': KES ' + tooltipItem.raw.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                },
                title: { display: true, text: 'Top Funded Sectors' }
            }
        }
    });

JS);
?>

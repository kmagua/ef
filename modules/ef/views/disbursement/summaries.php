<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\ef\models\Disbursement;
$this->title = 'Disbursement Analytics & Summaries';
?>
<br>
<br>
<!-- Poppins Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<h3 
  class="text-center text-uppercase fw-bold" 
  style="
    color: #fff !important; 
    background: linear-gradient(135deg, #008a8a, #00aaaa) !important;
    padding: 20px;
    border-radius: 8px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    font-size: 1.75rem;
    letter-spacing: 1.2px;
    margin-bottom: 0;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  "
>
    <?= Html::encode($this->title) ?>
</h3>



<!-- Filter Form -->
<div class="search-form card shadow-sm p-4 mb-4">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <div class="row">
        <!-- County Filter -->
        <div class="col-md-3">
            <?= $form->field($searchModel, 'county')->dropDownList(
                ArrayHelper::map(
                    Disbursement::find()->select('county')->distinct()->all(),
                    'county',
                    'county'
                ),
                ['prompt' => 'Select County', 'class' => 'form-control', 'id' => 'countyFilter']
            ) ?>
        </div>
        <!-- Sector Filter (static values; update if dynamic data available) -->
        <div class="col-md-3">
            <?= $form->field($searchModel, 'sector')->dropDownList(
                ['Education' => 'Education', 'Health' => 'Health', 'Infrastructure' => 'Infrastructure', 'Agriculture' => 'Agriculture'],
                ['prompt' => 'Select Sector', 'class' => 'form-control', 'id' => 'sectorFilter']
            ) ?>
        </div>
        <!-- Chart Type Selector -->
        <div class="col-md-3">
           <?= $form->field($searchModel, 'chartType')->dropDownList(
    [
        'bar' => 'Bar Chart', 
        'line' => 'Line Chart', 
        'radar' => 'Radar Chart', 
        'pie' => 'Pie Chart', 
        'doughnut' => 'Doughnut Chart',
        'polarArea' => 'Polar Area Chart'
    ],
    ['prompt' => 'Select Chart Type', 'class' => 'form-control', 'id' => 'chartTypeFilter']
) ?>

        </div>
        <!-- Buttons -->
        <div class="col-md-3 d-flex align-items-end">
            <div>
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary me-2', 'id' => 'searchButton']) ?>
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary', 'id' => 'resetButton']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Disbursement Analytics Visualization -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card shadow-lg border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-primary">
                        <i class="bi bi-bar-chart"></i> Disbursement Overview
                    </h5>
                    <button class="btn btn-success" id="downloadChart">
                        <i class="bi bi-download"></i> Download Chart
                    </button>
                </div>
                <canvas id="disbursementChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Disbursement Summary Cards -->
<div class="row" id="countyCards">
    <?php foreach($total_per_county as $tpc): 
        $url = Url::to(['/ef/disbursement/per-county', 'cnt'=> $tpc->county]);
        $progress = min(100, ($tpc->amount_disbursed / 10000000) * 100);
    ?>
    <div class="col-xl-4 col-md-6 col-sm-12 mb-4 county-card" 
         data-county="<?= Html::encode($tpc->county) ?>" 
         data-sector="<?= isset($tpc->sector) ? strtolower($tpc->sector) : '' ?>">
        <a href="<?= $url ?>" class="text-decoration-none">
            <div class="card shadow-lg border-0 rounded-lg transition-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-primary fw-bold"><?= Html::encode($tpc->county) ?></h5>
                            <p class="mb-2 text-muted small">
                                <i class="bi bi-cash-stack"></i> Total Disbursed: 
                                <strong class="text-success">KSH <?= number_format($tpc->amount_disbursed, 2) ?></strong>
                            </p>
                        </div>
                        <div>
                            <div class="icon-circle bg-success text-white">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="progress mt-3" style="height: 7px;">
                        <div class="progress-bar bg-gradient-success" role="progressbar" 
                             style="width: <?= $progress ?>%;" 
                             aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" 
                           title="Estimated Fund Usage based on 10M threshold"></i>
                        <span class="ms-1"><?= round($progress, 2) ?>% utilized</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php
// Prepare data for charts
$countyNames = json_encode(array_column($total_per_county, 'county'));
$disbursedAmounts = json_encode(array_map(fn($amt) => number_format($amt, 2, '.', ''), array_column($total_per_county, 'amount_disbursed')));

$script = <<< JS
// Function to generate dynamic colors for charts
function dynamicColors() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    return 'rgba(' + r + ', ' + g + ', ' + b + ', 0.7)';
}

// Global variable to hold the chart instance
var disbursementChartInstance;

// Function to render the chart based on selected chart type
function renderChart() {
    var ctx = document.getElementById('disbursementChart').getContext('2d');
    // If chart already exists, destroy it to re-render
    if (disbursementChartInstance) {
        disbursementChartInstance.destroy();
    }
    
    // Get the selected chart type (default to 'bar' if none selected)
    var selectedChartType = $('#chartTypeFilter').val() || 'bar';
    
    // Prepare an array of colors for each data point
    var colorsArray = [];
    for (var i = 0; i < $countyNames.length; i++) {
        colorsArray.push(dynamicColors());
    }
    
    var chartData = {
        labels: $countyNames,
        datasets: [{
            label: 'Total Amount Disbursed (KSH)',
            data: $disbursedAmounts,
            backgroundColor: (selectedChartType === 'pie' || selectedChartType === 'doughnut') ? colorsArray : colorsArray,
            borderColor: colorsArray.map(function(color){ return color.replace('0.7', '1'); }),
            borderWidth: 1,
            fill: false,
            tension: 0.1
        }]
    };
    
    var config = {
        type: selectedChartType,
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return ' KSH ' + Number(tooltipItem.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: (selectedChartType === 'bar' || selectedChartType === 'line') ? {
                y: {
                    ticks: {
                        callback: function(value) {
                            return 'KSH ' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: "rgba(200, 200, 200, 0.2)"
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            } : {}
        }
    };
    
    disbursementChartInstance = new Chart(ctx, config);
}

// Initial chart render
renderChart();

// Change chart type when the chart type dropdown changes
$('#chartTypeFilter').change(function() {
    renderChart();
});

// Download chart as an image
$('#downloadChart').click(function() {
    var link = document.createElement('a');
    link.href = document.getElementById('disbursementChart').toDataURL('image/png');
    link.download = 'disbursement_chart.png';
    link.click();
});

// Function to filter summary cards by county and sector
function filterCards() {
    var selectedCounty = $('#countyFilter').val().toLowerCase();
    var selectedSector = $('#sectorFilter').val().toLowerCase();
    
    $('.county-card').each(function() {
        var cardCounty = $(this).data('county').toLowerCase();
        var cardSector = $(this).data('sector') ? $(this).data('sector').toLowerCase() : '';
        
        var matchesCounty = (selectedCounty === "" || cardCounty === selectedCounty);
        var matchesSector = (selectedSector === "" || cardSector === selectedSector);
        
        if (matchesCounty && matchesSector) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

// Update filtering when the county or sector filter changes or when Search is clicked
$('#countyFilter, #sectorFilter').change(function() {
    filterCards();
});
$('#searchButton').click(function(e) {
    e.preventDefault();
    filterCards();
});

// Reset filters functionality
$('#resetButton').click(function(e) {
    e.preventDefault();
    $('#countyFilter').val('');
    $('#sectorFilter').val('');
    $('#chartTypeFilter').val('bar');
    filterCards();
    renderChart();
});

// Initialize Bootstrap tooltips
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
JS;
$this->registerJs($script);
?>
<script>
$script = <<< JS
function dynamicColors() {
    var r = Math.floor(Math.random() * 200);
    var g = Math.floor(Math.random() * 200);
    var b = Math.floor(Math.random() * 200);
    return 'rgba(' + r + ', ' + g + ', ' + b + ', 0.7)';
}

var disbursementChartInstance;

function renderChart() {
    var ctx = document.getElementById('disbursementChart').getContext('2d');
    if (disbursementChartInstance) {
        disbursementChartInstance.destroy();
    }
    
    var selectedChartType = $('#chartTypeFilter').val() || 'bar';
    var colorsArray = [];
    for (var i = 0; i < $countyNames.length; i++) {
        colorsArray.push(dynamicColors());
    }

    var commonOptions = {
        responsive: true,
        plugins: {
            datalabels: {
                color: '#333',
                anchor: 'end',
                align: 'top',
                formatter: function(value) {
                    return 'KSH ' + Number(value).toLocaleString();
                },
                font: { weight: 'bold' }
            },
            legend: { display: true, position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return ' KSH ' + Number(tooltipItem.raw).toLocaleString();
                    }
                }
            }
        },
        animation: {
            duration: 1500,
            easing: 'easeOutBounce'
        }
    };

    var config;

    if (selectedChartType === 'stackedBar') {
        config = {
            type: 'bar',
            data: {
                labels: $countyNames,
                datasets: [{
                    label: 'Total Disbursed',
                    data: $disbursedAmounts,
                    backgroundColor: colorsArray,
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            },
            plugins: [ChartDataLabels]
        };
    } else if (selectedChartType === 'radar') {
        config = {
            type: 'radar',
            data: {
                labels: $countyNames,
                datasets: [{
                    label: 'Total Disbursed',
                    data: $disbursedAmounts,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    pointBackgroundColor: colorsArray,
                }]
            },
            options: commonOptions,
            plugins: [ChartDataLabels]
        };
    } else if (selectedChartType === 'polarArea') {
        config = {
            type: 'polarArea',
            data: {
                labels: $countyNames,
                datasets: [{
                    data: $disbursedAmounts,
                    backgroundColor: colorsArray,
                }]
            },
            options: commonOptions,
            plugins: [ChartDataLabels]
        };
    } else {
        config = {
            type: selectedChartType,
            data: {
                labels: $countyNames,
                datasets: [{
                    label: 'Total Disbursed',
                    data: $disbursedAmounts,
                    backgroundColor: (selectedChartType === 'pie' || selectedChartType === 'doughnut') ? colorsArray : colorsArray,
                    borderColor: colorsArray.map(color => color.replace('0.7', '1')),
                    borderWidth: 1,
                    fill: selectedChartType === 'line' ? true : false,
                    tension: 0.4
                }]
            },
            options: commonOptions,
            plugins: [ChartDataLabels]
        };
    }

    disbursementChartInstance = new Chart(ctx, config);
}

// Initial render
renderChart();

// Trigger chart re-render
$('#chartTypeFilter').change(function() {
    renderChart();
});

// Download as Image
$('#downloadChart').click(function() {
    var link = document.createElement('a');
    link.href = document.getElementById('disbursementChart').toDataURL('image/png');
    link.download = 'disbursement_chart.png';
    link.click();
});

// Optional: Export to PDF
$('#downloadPDF').click(function() {
    var canvas = document.getElementById('disbursementChart');
    var imgData = canvas.toDataURL('image/png');
    var pdf = new jsPDF();
    pdf.addImage(imgData, 'PNG', 10, 10, 180, 120);
    pdf.save('disbursement_chart.pdf');
});

JS;
$this->registerJs($script);
?>
</script>
<!-- Custom Styles -->
<style>
/* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
}

/* Search Form Styling */
.search-form {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
}
.search-form .form-control {
    border: 2px solid #28a745;
    border-radius: 8px;
    padding: 10px;
    font-size: 1rem;
    transition: all 0.3s ease-in-out;
}
.search-form .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
}
.search-form .btn {
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease-in-out;
}

/* Card Styling */
.card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease-in-out;
    overflow: hidden;
    background: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 12px 24px rgba(0, 0, 0, 0.2);
}
.card h5 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0;
}
.card p {
    font-size: 0.9rem;
    color: #6c757d;
}

/* Icon Circle Styling */
.icon-circle {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.8rem;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    position: absolute;
    top: 15px;
    right: 15px;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    cursor: pointer;
}
.icon-circle i {
    font-size: 1.5rem;
}
.icon-circle:hover {
    transform: scale(1.1);
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
}
.icon-circle:hover::after {
    content: "Click to View More Data";
    position: absolute;
    top: 60px;
    right: -30px;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 5px 10px;
    font-size: 0.75rem;
    border-radius: 5px;
    white-space: nowrap;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.3s ease-in-out;
}
.icon-circle::after {
    opacity: 0;
    visibility: hidden;
}

/* Progress Bar Styling */
.progress {
    height: 8px;
    background: #e9ecef;
    border-radius: 20px;
    overflow: hidden;
    margin-top: 10px;
}
.progress-bar {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 20px;
}

/* Utilization Percentage */
.small.text-muted {
    font-size: 0.85rem;
    font-weight: 600;
    color: #28a745;
}
</style>

<?php

use app\modules\ef\models\EqualisationFundEntitlements;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

 $this->title = 'Equalisation Fund Entitlements Analytics';
 $this->params['breadcrumbs'][] = $this->title;

 $this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

// Calculate aggregates for charts
 $globalTotal = EqualisationFundEntitlements::find()->sum('ef_entitlement_ksh');
 $globalCount = EqualisationFundEntitlements::find()->count();
 $globalAudited = EqualisationFundEntitlements::find()->sum('audited_approved_revenue_ksh');
 $globalReflected = EqualisationFundEntitlements::find()->sum('amount_reflected_in_dora_ksh');
 $globalTransfers = EqualisationFundEntitlements::find()->sum('transfers_into_ef');
 $globalArrears = EqualisationFundEntitlements::find()->sum('arrears');

// Get data by financial year
 $yearlyData = [];
 $years = ArrayHelper::map(
    EqualisationFundEntitlements::find()->select('financial_year')->distinct()->orderBy('financial_year')->all(),
    'financial_year', 'financial_year'
);

foreach ($years as $year) {
    $yearData = EqualisationFundEntitlements::find()->where(['financial_year' => $year])->one();
    if ($yearData) {
        $yearlyData[] = [
            'year' => $year,
            'audited' => $yearData->audited_approved_revenue_ksh,
            'entitlement' => $yearData->ef_entitlement_ksh,
            'reflected' => $yearData->amount_reflected_in_dora_ksh,
            'transfers' => $yearData->transfers_into_ef,
            'arrears' => $yearData->arrears,
        ];
    }
}
?>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #eef2f3;
    color: #333;
}

.analytics-box {
    background: #fff;
    padding: 26px;
    border-radius: 14px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.10);
    margin-bottom: 30px;
}

.analytics-title {
    background: linear-gradient(135deg, #00695c, #00bfa5);
    color: #fff;
    padding: 18px;
    border-radius: 12px;
    text-align: center;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: .5px;
    margin-bottom: 30px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.table thead th {
    background: linear-gradient(135deg, #004d40, #00695c) !important;
    color: white !important;
    text-transform: uppercase;
    text-align: center;
    font-weight: 700;
    border: none !important;
}
.table tbody td {
    text-align: center;
    font-size: 14px;
    padding: 12px 8px;
}

.chart-box {
    background: white;
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    position: relative;
    border: 1px solid #e0e0e0;
}

.chart-title {
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    color: #00695c;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0f2f1;
}

.download-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #00695c, #00bfa5);
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.download-btn:hover {
    background: linear-gradient(135deg, #004d40, #00695c);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
}

.stats-card h3 {
    color: white !important;
}

.stats-card {
    background: linear-gradient(135deg, #00695c, #00bfa5);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: transform 0.3s;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-card h3 {
    font-size: 32px;
    margin: 10px 0;
    font-weight: 700;
}

.stats-card p {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
    font-weight: 500;
}

.chart-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 22px;
    font-weight: 700;
    color: #00695c;
    margin: 30px 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0f2f1;
}

/* Custom scrollbar for tables */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #00838f;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #006064;
}
</style>

<div class="analytics-box">

   <h1 class="analytics-title" style="color: white !important;">Equalisation Fund Entitlements Analytics</h1>

    <!-- Stats Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stats-card">
                <p>Total Audited Revenue</p>
                <h3><?= number_format($globalAudited, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <p>Total Entitlements</p>
                <h3><?= number_format($globalTotal, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <p>Total Reflected in DORA</p>
                <h3><?= number_format($globalReflected, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <p>Total Transfers</p>
                <h3><?= number_format($globalTransfers, 2) ?></h3>
            </div>
        </div>
    </div>

    <h2 class="section-title">Entitlements Overview</h2>
    
    <!-- CHARTS -->
    <div class="chart-section">
        <!-- PIE CHART -->
        <div class="chart-box">
            <div class="chart-title">Revenue vs Entitlements (Pie)</div>
            <button class="download-btn" onclick="downloadChart('pieChart')">Download</button>
            <div class="chart-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- DOUGHNUT CHART -->
        <div class="chart-box">
            <div class="chart-title">Revenue vs Entitlements (Doughnut)</div>
            <button class="download-btn" onclick="downloadChart('doughnutChart')">Download</button>
            <div class="chart-container">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </div>

    <h2 class="section-title">Financial Year Analysis</h2>
    
    <div class="chart-section">
        <!-- BAR CHART -->
        <div class="chart-box">
            <div class="chart-title">Entitlements by Financial Year</div>
            <button class="download-btn" onclick="downloadChart('barChart')">Download</button>
            <div class="chart-container">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- HORIZONTAL BAR CHART -->
        <div class="chart-box">
            <div class="chart-title">Revenue vs Entitlements by Year</div>
            <button class="download-btn" onclick="downloadChart('horizontalBarChart')">Download</button>
            <div class="chart-container">
                <canvas id="horizontalBarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- STACKED BAR CHART -->
        <div class="chart-box">
            <div class="chart-title">Fund Allocation by Year</div>
            <button class="download-btn" onclick="downloadChart('stackedBarChart')">Download</button>
            <div class="chart-container">
                <canvas id="stackedBarChart"></canvas>
            </div>
        </div>
    </div>

    <h2 class="section-title">Trend Analysis</h2>
    
    <div class="chart-section">
        <!-- LINE CHART -->
        <div class="chart-box">
            <div class="chart-title">Entitlements Trend Over Time</div>
            <button class="download-btn" onclick="downloadChart('lineChart')">Download</button>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- AREA CHART -->
        <div class="chart-box">
            <div class="chart-title">Cumulative Entitlements Trend</div>
            <button class="download-btn" onclick="downloadChart('areaChart')">Download</button>
            <div class="chart-container">
                <canvas id="areaChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- RADAR CHART -->
        <div class="chart-box">
            <div class="chart-title">Fund Distribution (Radar)</div>
            <button class="download-btn" onclick="downloadChart('radarChart')">Download</button>
            <div class="chart-container">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- POLAR AREA CHART -->
        <div class="chart-box">
            <div class="chart-title">Entitlements Distribution</div>
            <button class="download-btn" onclick="downloadChart('polarChart')">Download</button>
            <div class="chart-container">
                <canvas id="polarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- BUBBLE CHART -->
        <div class="chart-box">
            <div class="chart-title">Revenue vs Entitlements Relationship</div>
            <button class="download-btn" onclick="downloadChart('bubbleChart')">Download</button>
            <div class="chart-container">
                <canvas id="bubbleChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- SCATTER PLOT -->
        <div class="chart-box">
            <div class="chart-title">Revenue vs Entitlement Rate Correlation</div>
            <button class="download-btn" onclick="downloadChart('scatterChart')">Download</button>
            <div class="chart-container">
                <canvas id="scatterChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- COMBO CHART -->
        <div class="chart-box">
            <div class="chart-title">Annual Fund Analysis</div>
            <button class="download-btn" onclick="downloadChart('comboChart')">Download</button>
            <div class="chart-container">
                <canvas id="comboChart"></canvas>
            </div>
        </div>
    </div>

    <h2 class="section-title">Financial Year Comparison</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Financial Year</th>
                    <th>Audited Revenue (Ksh)</th>
                    <th>EF Entitlement (Ksh)</th>
                    <th>Amount in DORA (Ksh)</th>
                    <th>Transfers (Ksh)</th>
                    <th>Arrears (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($yearlyData as $row): ?>
                    <tr>
                        <td><?= $row['year'] ?></td>
                        <td><?= number_format($row['audited'], 2) ?></td>
                        <td><?= number_format($row['entitlement'], 2) ?></td>
                        <td><?= number_format($row['reflected'], 2) ?></td>
                        <td><?= number_format($row['transfers'], 2) ?></td>
                        <td><?= number_format($row['arrears'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Prepare data for all charts
const yearLabels = <?= json_encode(array_column($yearlyData, 'year')) ?>;
const yearAudited = <?= json_encode(array_column($yearlyData, 'audited')) ?>;
const yearEntitlements = <?= json_encode(array_column($yearlyData, 'entitlement')) ?>;
const yearReflected = <?= json_encode(array_column($yearlyData, 'reflected')) ?>;
const yearTransfers = <?= json_encode(array_column($yearlyData, 'transfers')) ?>;
const yearArrears = <?= json_encode(array_column($yearlyData, 'arrears')) ?>;

// Calculate rates
const entitlementRates = yearAudited.map((audited, i) => 
    audited > 0 ? (yearEntitlements[i] / audited) * 100 : 0
);

// Define a professional color palette
const colorPalette = {
    primary: '#00695c',
    secondary: '#00bfa5',
    accent: '#4db6ac',
    light: '#b2dfdb',
    dark: '#004d40',
    success: '#00897b',
    warning: '#ffa000',
    danger: '#e53935',
    info: '#039be5'
};

// ================= PIE CHART ===================
const pieChart = new Chart(document.getElementById("pieChart"), {
    type: "pie",
    data: {
        labels: ["Audited Revenue", "EF Entitlements"],
        datasets: [{
            data: [<?= $globalAudited ?>, <?= $globalTotal ?>],
            backgroundColor: [colorPalette.primary, colorPalette.accent],
            borderColor: "#ffffff",
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// ================= DOUGHNUT CHART ===================
const doughnutChart = new Chart(document.getElementById("doughnutChart"), {
    type: "doughnut",
    data: {
        labels: ["Audited Revenue", "EF Entitlements", "Reflected in DORA", "Transfers", "Arrears"],
        datasets: [{
            data: [<?= $globalAudited ?>, <?= $globalTotal ?>, <?= $globalReflected ?>, <?= $globalTransfers ?>, <?= $globalArrears ?>],
            backgroundColor: [
                colorPalette.primary, 
                colorPalette.secondary, 
                colorPalette.accent, 
                colorPalette.success, 
                colorPalette.warning
            ],
            borderColor: "#ffffff",
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// ================= BAR CHART =====================
const barChart = new Chart(document.getElementById("barChart"), {
    type: "bar",
    data: {
        labels: yearLabels,
        datasets: [{
            label: "EF Entitlements (Ksh)",
            data: yearEntitlements,
            backgroundColor: colorPalette.accent,
            borderColor: colorPalette.primary,
            borderWidth: 1,
            borderRadius: 6,
            hoverBackgroundColor: colorPalette.secondary
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { 
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return `Entitlements: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        }
    }
});

// ================= HORIZONTAL BAR CHART =====================
const horizontalBarChart = new Chart(document.getElementById("horizontalBarChart"), {
    type: "bar",
    data: {
        labels: yearLabels,
        datasets: [
            {
                label: "Audited Revenue",
                data: yearAudited,
                backgroundColor: colorPalette.primary,
                borderColor: colorPalette.dark,
                borderWidth: 1,
                borderRadius: 4
            },
            {
                label: "EF Entitlements",
                data: yearEntitlements,
                backgroundColor: colorPalette.accent,
                borderColor: colorPalette.secondary,
                borderWidth: 1,
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        scales: {
            x: { 
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            y: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        }
    }
});

// ================= STACKED BAR CHART =====================
const stackedBarChart = new Chart(document.getElementById("stackedBarChart"), {
    type: "bar",
    data: {
        labels: yearLabels,
        datasets: [
            {
                label: "Reflected in DORA",
                data: yearReflected,
                backgroundColor: colorPalette.primary,
                borderColor: colorPalette.dark,
                borderWidth: 1,
                borderRadius: 4
            },
            {
                label: "Transfers",
                data: yearTransfers,
                backgroundColor: colorPalette.accent,
                borderColor: colorPalette.secondary,
                borderWidth: 1,
                borderRadius: 4
            },
            {
                label: "Arrears",
                data: yearArrears,
                backgroundColor: colorPalette.warning,
                borderColor: colorPalette.danger,
                borderWidth: 1,
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true,
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    }
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        }
    }
});

// ================= LINE CHART =====================
const lineChart = new Chart(document.getElementById("lineChart"), {
    type: "line",
    data: {
        labels: yearLabels,
        datasets: [{
            label: "EF Entitlements (Ksh)",
            data: yearEntitlements,
            backgroundColor: "rgba(0, 105, 92, 0.1)",
            borderColor: colorPalette.primary,
            borderWidth: 3,
            pointBackgroundColor: colorPalette.primary,
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { 
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return `Entitlements: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        }
    }
});

// ================= AREA CHART =====================
const areaChart = new Chart(document.getElementById("areaChart"), {
    type: "line",
    data: {
        labels: yearLabels,
        datasets: [{
            label: "Cumulative Entitlements (Ksh)",
            data: yearEntitlements.map((sum => value => sum += value)(0)),
            backgroundColor: "rgba(0, 191, 165, 0.3)",
            borderColor: colorPalette.secondary,
            borderWidth: 3,
            pointBackgroundColor: colorPalette.secondary,
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { 
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return `Cumulative: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        }
    }
});

// ================= RADAR CHART =====================
const radarChart = new Chart(document.getElementById("radarChart"), {
    type: "radar",
    data: {
        labels: ["Audited Revenue", "EF Entitlements", "Reflected in DORA", "Transfers", "Arrears"],
        datasets: [{
            label: "Fund Distribution",
            data: [
                <?= $globalAudited ?>,
                <?= $globalTotal ?>,
                <?= $globalReflected ?>,
                <?= $globalTransfers ?>,
                <?= $globalArrears ?>
            ],
            backgroundColor: "rgba(0, 105, 92, 0.2)",
            borderColor: colorPalette.primary,
            borderWidth: 2,
            pointBackgroundColor: colorPalette.primary,
            pointBorderColor: "#fff",
            pointBorderWidth: 2,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                ticks: {
                    backdropColor: 'transparent',
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 10
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                pointLabels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return `${context.label}: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        }
    }
});

// ================= POLAR AREA CHART =====================
const polarChart = new Chart(document.getElementById("polarChart"), {
    type: "polarArea",
    data: {
        labels: yearLabels.slice(0, 6), // Limit to 6 years for readability
        datasets: [{
            data: yearEntitlements.slice(0, 6),
            backgroundColor: [
                "rgba(0, 105, 92, 0.7)",
                "rgba(0, 191, 165, 0.7)",
                "rgba(77, 182, 172, 0.7)",
                "rgba(0, 77, 64, 0.7)",
                "rgba(0, 137, 123, 0.7)",
                "rgba(0, 105, 92, 0.7)"
            ],
            borderColor: "#ffffff",
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                ticks: {
                    backdropColor: 'transparent',
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 10
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                pointLabels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return `${context.label}: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        }
    }
});

// ================= BUBBLE CHART =====================
const bubbleData = yearLabels.map((year, i) => ({
    x: yearAudited[i],
    y: yearEntitlements[i],
    r: entitlementRates[i] / 5 // Scale down for better visualization
}));

const bubbleChart = new Chart(document.getElementById("bubbleChart"), {
    type: "bubble",
    data: {
        datasets: [{
            label: "Year Data",
            data: bubbleData,
            backgroundColor: "rgba(0, 105, 92, 0.6)",
            borderColor: colorPalette.primary,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                title: {
                    display: true,
                    text: "Audited Revenue (Ksh)",
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 14,
                        weight: 'bold'
                    }
                },
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            y: {
                title: {
                    display: true,
                    text: "EF Entitlements (Ksh)",
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 14,
                        weight: 'bold'
                    }
                },
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const index = context.dataIndex;
                        return [
                            `Year: ${yearLabels[index]}`,
                            `Audited: ${yearAudited[index].toLocaleString()}`,
                            `Entitlements: ${yearEntitlements[index].toLocaleString()}`,
                            `Rate: ${entitlementRates[index].toFixed(1)}%`
                        ];
                    }
                }
            }
        }
    }
});

// ================= SCATTER PLOT =====================
const scatterData = yearLabels.map((year, i) => ({
    x: yearAudited[i],
    y: entitlementRates[i]
}));

const scatterChart = new Chart(document.getElementById("scatterChart"), {
    type: "scatter",
    data: {
        datasets: [{
            label: "Year Data",
            data: scatterData,
            backgroundColor: colorPalette.accent,
            borderColor: colorPalette.primary,
            borderWidth: 1,
            pointRadius: 8,
            pointHoverRadius: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                title: {
                    display: true,
                    text: "Audited Revenue (Ksh)",
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 14,
                        weight: 'bold'
                    }
                },
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            y: {
                title: {
                    display: true,
                    text: "Entitlement Rate (%)",
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 14,
                        weight: 'bold'
                    }
                },
                beginAtZero: true,
                max: 100,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const index = context.dataIndex;
                        return [
                            `Year: ${yearLabels[index]}`,
                            `Audited: ${yearAudited[index].toLocaleString()}`,
                            `Rate: ${entitlementRates[index].toFixed(1)}%`
                        ];
                    }
                }
            }
        }
    }
});

// ================= COMBO CHART =====================
const comboChart = new Chart(document.getElementById("comboChart"), {
    type: "bar",
    data: {
        labels: yearLabels,
        datasets: [
            {
                label: "Audited Revenue (Ksh)",
                data: yearAudited,
                backgroundColor: "rgba(77, 182, 172, 0.5)",
                borderColor: colorPalette.accent,
                borderWidth: 1,
                borderRadius: 4,
                order: 2
            },
            {
                label: "Entitlement Rate (%)",
                data: entitlementRates,
                type: "line",
                backgroundColor: "rgba(0, 0, 0, 0)",
                borderColor: colorPalette.primary,
                borderWidth: 3,
                pointBackgroundColor: colorPalette.primary,
                pointBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: false,
                order: 1,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { 
                beginAtZero: true,
                position: 'left',
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            },
            y1: {
                beginAtZero: true,
                max: 100,
                position: 'right',
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    },
                    callback: function(value) {
                        return value + '%';
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Poppins', sans-serif"
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        family: "'Poppins', sans-serif",
                        size: 12
                    },
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Poppins', sans-serif",
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    family: "'Poppins', sans-serif",
                    size: 13
                },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        if (context.datasetIndex === 0) {
                            return `Audited: ${context.raw.toLocaleString()}`;
                        } else {
                            return `Rate: ${context.raw.toFixed(1)}%`;
                        }
                    }
                }
            }
        }
    }
});

// Function to download chart as image
function downloadChart(chartId) {
    const chart = Chart.getChart(chartId);
    const url = chart.toBase64Image();
    const link = document.createElement('a');
    link.download = chartId + '.png';
    link.href = url;
    link.click();
}
</script>
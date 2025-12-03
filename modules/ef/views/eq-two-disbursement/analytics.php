<?php
use yii\helpers\Html;

 $this->title = 'Disbursement Analytics';
 $this->params['breadcrumbs'][] = $this->title;

 $this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
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
    background: linear-gradient(135deg, #006064, #00838f);
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
    color: #006064;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0f2f1;
}

.download-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #006064, #00838f);
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
    background: linear-gradient(135deg, #006064, #00838f);
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
    color: #006064;
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

   <h1 class="analytics-title" style="color: white !important;">2nd Marginalization Policy - Disbursement Analytics</h1>


    <!-- Stats Cards -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="stats-card">
                <p>Total Approved Budget</p>
                <h3><?= number_format($grandApproved, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <p>Total Disbursed</p>
                <h3><?= number_format($grandDisbursed, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <p>Disbursement Rate</p>
                <h3><?= $grandApproved > 0 ? round(($grandDisbursed / $grandApproved) * 100, 2) : 0 ?>%</h3>
            </div>
        </div>
    </div>

    <h2 class="section-title">Budget Overview</h2>
    
    <!-- CHARTS -->
    <div class="chart-section">
        <!-- PIE CHART -->
        <div class="chart-box">
            <div class="chart-title">Budget vs Disbursement (Pie)</div>
            <button class="download-btn" onclick="downloadChart('pieChart')">Download</button>
            <div class="chart-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- DOUGHNUT CHART -->
        <div class="chart-box">
            <div class="chart-title">Budget vs Disbursement (Doughnut)</div>
            <button class="download-btn" onclick="downloadChart('doughnutChart')">Download</button>
            <div class="chart-container">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </div>

    <h2 class="section-title">County Analysis</h2>
    
    <div class="chart-section">
        <!-- BAR CHART -->
        <div class="chart-box">
            <div class="chart-title">County Disbursement Comparison</div>
            <button class="download-btn" onclick="downloadChart('barChart')">Download</button>
            <div class="chart-container">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- HORIZONTAL BAR CHART -->
        <div class="chart-box">
            <div class="chart-title">County Disbursement Rates</div>
            <button class="download-btn" onclick="downloadChart('horizontalBarChart')">Download</button>
            <div class="chart-container">
                <canvas id="horizontalBarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- STACKED BAR CHART -->
        <div class="chart-box">
            <div class="chart-title">County Budget vs Disbursement</div>
            <button class="download-btn" onclick="downloadChart('stackedBarChart')">Download</button>
            <div class="chart-container">
                <canvas id="stackedBarChart"></canvas>
            </div>
        </div>
    </div>

    <h2 class="section-title">Advanced Analytics</h2>
    
    <div class="chart-section">
        <!-- LINE CHART -->
        <div class="chart-box">
            <div class="chart-title">Disbursement Trend Over Time</div>
            <button class="download-btn" onclick="downloadChart('lineChart')">Download</button>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- AREA CHART -->
        <div class="chart-box">
            <div class="chart-title">Cumulative Disbursement Trend</div>
            <button class="download-btn" onclick="downloadChart('areaChart')">Download</button>
            <div class="chart-container">
                <canvas id="areaChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- RADAR CHART -->
        <div class="chart-box">
            <div class="chart-title">County Disbursement Rates (Radar)</div>
            <button class="download-btn" onclick="downloadChart('radarChart')">Download</button>
            <div class="chart-container">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- POLAR AREA CHART -->
        <div class="chart-box">
            <div class="chart-title">County Disbursement Distribution</div>
            <button class="download-btn" onclick="downloadChart('polarChart')">Download</button>
            <div class="chart-container">
                <canvas id="polarChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- BUBBLE CHART -->
        <div class="chart-box">
            <div class="chart-title">Budget vs Disbursement Relationship</div>
            <button class="download-btn" onclick="downloadChart('bubbleChart')">Download</button>
            <div class="chart-container">
                <canvas id="bubbleChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- SCATTER PLOT -->
        <div class="chart-box">
            <div class="chart-title">Budget vs Disbursement Rate Correlation</div>
            <button class="download-btn" onclick="downloadChart('scatterChart')">Download</button>
            <div class="chart-container">
                <canvas id="scatterChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <!-- COMBO CHART -->
        <div class="chart-box">
            <div class="chart-title">Monthly Disbursement Analysis</div>
            <button class="download-btn" onclick="downloadChart('comboChart')">Download</button>
            <div class="chart-container">
                <canvas id="comboChart"></canvas>
            </div>
        </div>
    </div>

    <h2 class="section-title">County Comparison</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>County</th>
                    <th>Approved Budget</th>
                    <th>Total Disbursement</th>
                    <th>Disbursement %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($countyTotals as $row): ?>
                    <tr>
                        <td><?= $row['county'] ?></td>
                        <td><?= number_format($row['approved_budget'], 2) ?></td>
                        <td><?= number_format($row['total_disbursement'], 2) ?></td>
                        <td>
                            <?= $row['approved_budget'] > 0 
                                ? round(($row['total_disbursement'] / $row['approved_budget']) * 100, 2)
                                : 0 
                            ?>%
                        </td>
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
const countyLabels = <?= json_encode(array_column($countyTotals, 'county')) ?>;
const countyDisbursements = <?= json_encode(array_column($countyTotals, 'total_disbursement')) ?>;
const countyApproved = <?= json_encode(array_column($countyTotals, 'approved_budget')) ?>;
const countyRates = countyApproved.map((approved, i) => 
    approved > 0 ? (countyDisbursements[i] / approved) * 100 : 0
);

// Generate sample trend data (in a real app, this would come from your backend)
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const trendData = months.map(() => Math.floor(Math.random() * 1000000) + 500000);
const cumulativeData = trendData.map((sum => value => sum += value)(0));

// Define a professional color palette
const colorPalette = {
    primary: '#006064',
    secondary: '#00838f',
    accent: '#00acc1',
    light: '#4dd0e1',
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
        labels: ["Approved Budget", "Disbursed"],
        datasets: [{
            data: [<?= $grandApproved ?>, <?= $grandDisbursed ?>],
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
        labels: ["Approved Budget", "Disbursed"],
        datasets: [{
            data: [<?= $grandApproved ?>, <?= $grandDisbursed ?>],
            backgroundColor: [colorPalette.dark, colorPalette.success],
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
        labels: countyLabels,
        datasets: [{
            label: "Total Disbursement (Ksh)",
            data: countyDisbursements,
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
                        return `Disbursement: ${context.raw.toLocaleString()}`;
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
        labels: countyLabels,
        datasets: [{
            label: "Disbursement Rate (%)",
            data: countyRates,
            backgroundColor: countyRates.map(rate => {
                if (rate >= 80) return colorPalette.success;
                if (rate >= 60) return colorPalette.accent;
                if (rate >= 40) return colorPalette.warning;
                return colorPalette.danger;
            }),
            borderColor: colorPalette.primary,
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        scales: {
            x: { 
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
                        return `Rate: ${context.raw.toFixed(1)}%`;
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
        labels: countyLabels.slice(0, 8), // Limit to 8 counties for readability
        datasets: [
            {
                label: "Approved Budget",
                data: countyApproved.slice(0, 8),
                backgroundColor: colorPalette.primary,
                borderColor: colorPalette.dark,
                borderWidth: 1,
                borderRadius: 4
            },
            {
                label: "Disbursed",
                data: countyDisbursements.slice(0, 8),
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
        labels: months,
        datasets: [{
            label: "Monthly Disbursement (Ksh)",
            data: trendData,
            backgroundColor: "rgba(0, 96, 100, 0.1)",
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
                        return `Disbursement: ${context.raw.toLocaleString()}`;
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
        labels: months,
        datasets: [{
            label: "Cumulative Disbursement (Ksh)",
            data: cumulativeData,
            backgroundColor: "rgba(0, 131, 143, 0.3)",
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
        labels: countyLabels.slice(0, 8), // Limit to 8 counties for readability
        datasets: [{
            label: "Disbursement Rate (%)",
            data: countyRates.slice(0, 8),
            backgroundColor: "rgba(0, 96, 100, 0.2)",
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
                max: 100,
                ticks: {
                    stepSize: 20,
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
                        return `Rate: ${context.raw.toFixed(1)}%`;
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
        labels: countyLabels.slice(0, 6), // Limit to 6 counties for readability
        datasets: [{
            data: countyDisbursements.slice(0, 6),
            backgroundColor: [
                "rgba(0, 96, 100, 0.7)",
                "rgba(0, 131, 143, 0.7)",
                "rgba(0, 172, 193, 0.7)",
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
const bubbleData = countyLabels.map((county, i) => ({
    x: countyApproved[i],
    y: countyDisbursements[i],
    r: countyRates[i] / 5 // Scale down for better visualization
}));

const bubbleChart = new Chart(document.getElementById("bubbleChart"), {
    type: "bubble",
    data: {
        datasets: [{
            label: "County Data",
            data: bubbleData,
            backgroundColor: "rgba(0, 96, 100, 0.6)",
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
                    text: "Approved Budget (Ksh)",
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
                    text: "Disbursement (Ksh)",
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
                            `County: ${countyLabels[index]}`,
                            `Approved: ${countyApproved[index].toLocaleString()}`,
                            `Disbursed: ${countyDisbursements[index].toLocaleString()}`,
                            `Rate: ${countyRates[index].toFixed(1)}%`
                        ];
                    }
                }
            }
        }
    }
});

// ================= SCATTER PLOT =====================
const scatterData = countyLabels.map((county, i) => ({
    x: countyApproved[i],
    y: countyRates[i]
}));

const scatterChart = new Chart(document.getElementById("scatterChart"), {
    type: "scatter",
    data: {
        datasets: [{
            label: "County Data",
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
                    text: "Approved Budget (Ksh)",
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
                    text: "Disbursement Rate (%)",
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
                            `County: ${countyLabels[index]}`,
                            `Approved: ${countyApproved[index].toLocaleString()}`,
                            `Rate: ${countyRates[index].toFixed(1)}%`
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
        labels: months,
        datasets: [
            {
                label: "Monthly Disbursement (Ksh)",
                data: trendData,
                backgroundColor: "rgba(0, 172, 193, 0.5)",
                borderColor: colorPalette.accent,
                borderWidth: 1,
                borderRadius: 4,
                order: 2
            },
            {
                label: "Trend Line",
                data: trendData,
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
                order: 1
            }
        ]
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
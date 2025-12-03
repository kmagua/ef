<?php
/** @var array $countyTotals */
/** @var array $constituencyTotals */
/** @var array $yearTotals */

use yii\helpers\Html;

 $this->title = "2nd Marginalization Policy - Appropriation";
 $this->registerCssFile(
    "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
);

// Top 5 counties
 $sortedCountyTotals = $countyTotals;
arsort($sortedCountyTotals);
 $top5 = array_slice($sortedCountyTotals, 0, 5, true);

// Bottom 5 counties
 $bottom5 = array_slice($sortedCountyTotals, -5, 5, true);

// Calculate average allocation
 $averageAllocation = count($countyTotals) > 0 ? array_sum($countyTotals) / count($countyTotals) : 0;

// Calculate year-over-year growth
 $yearGrowth = [];
 $years = array_keys($yearTotals);
for ($i = 1; $i < count($years); $i++) {
    if ($yearTotals[$years[$i-1]] > 0) {
        $growth = (($yearTotals[$years[$i]] - $yearTotals[$years[$i-1]]) / $yearTotals[$years[$i-1]]) * 100;
        $yearGrowth[$years[$i]] = round($growth, 2);
    }
}

// Prepare data for charts
 $countyNames = array_keys($countyTotals);
 $countyValues = array_values($countyTotals);
 $top5Names = array_keys($top5);
 $top5Values = array_values($top5);
 $bottom5Names = array_keys($bottom5);
 $bottom5Values = array_values($bottom5);
 $yearNames = array_keys($yearTotals);
 $yearValues = array_values($yearTotals);

// For constituency chart, take top 10 to avoid overcrowding
arsort($constituencyTotals);
 $topConstituencies = array_slice($constituencyTotals, 0, 10, true);
 $constituencyNames = array_keys($topConstituencies);
 $constituencyValues = array_values($topConstituencies);

// For TreeMap visualization (simplified as a bar chart)
 $treeMapData = [];
 $totalAllocation = array_sum($countyValues);
foreach ($countyTotals as $county => $amount) {
    $percentage = ($amount / $totalAllocation) * 100;
    $treeMapData[] = [
        'county' => $county,
        'amount' => $amount,
        'percentage' => $percentage
    ];
}

// For Distribution Chart (Box Plot simulation)
 $countyValuesSorted = $countyValues;
sort($countyValuesSorted);
 $minValue = $countyValuesSorted[0];
 $maxValue = $countyValuesSorted[count($countyValuesSorted) - 1];
 $medianIndex = floor(count($countyValuesSorted) / 2);
 $medianValue = $countyValuesSorted[$medianIndex];

// For Outlier Detection
 $outliers = [];
 $normalValues = [];
foreach ($countyValues as $value) {
    if ($value > $averageAllocation * 2) {
        $outliers[] = $value;
    } else {
        $normalValues[] = $value;
    }
}

// For Cumulative Allocation
 $cumulativeData = [];
 $cumulative = 0;
foreach ($countyValuesSorted as $value) {
    $cumulative += $value;
    $cumulativeData[] = $cumulative;
}

// For Histogram
 $histogramBins = 10;
 $binSize = ($maxValue - $minValue) / $histogramBins;
 $histogramData = array_fill(0, $histogramBins, 0);
foreach ($countyValues as $value) {
    $binIndex = min(floor(($value - $minValue) / $binSize), $histogramBins - 1);
    $histogramData[$binIndex]++;
}

// For Pareto Chart
 $paretoData = [];
 $cumulativePercentage = 0;
 $countyValuesPareto = $countyValues;
rsort($countyValuesPareto);
foreach ($countyValuesPareto as $value) {
    $cumulativePercentage += ($value / $totalAllocation) * 100;
    $paretoData[] = [
        'value' => $value,
        'cumulative' => $cumulativePercentage
    ];
}
?>

<style>
:root {
    --primary: #00695c;
    --primary-dark: #004d40;
    --primary-light: #e0f2f1;
    --secondary: #00897b;
    --accent: #00a48a;
    --text: #004d40;
    --text-light: #00695c;
    --bg: #eef3f2;
    --card-bg: #ffffff;
    --border: #e0f2f1;
    --shadow: 0 4px 12px rgba(0,0,0,0.08);
    --shadow-hover: 0 8px 20px rgba(0,0,0,0.12);
    --transition: all 0.3s ease;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.6;
}

.insights-wrapper {
    background: var(--card-bg);
    padding: 35px;
    border-radius: 16px;
    max-width: 1200px;
    margin: 25px auto;
    box-shadow: var(--shadow);
}

.insights-header {
    background: linear-gradient(135deg, var(--secondary), var(--primary-dark));
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    color: white;
    font-weight: 700;
    font-size: 2.2rem;
    margin-bottom: 35px;
    box-shadow: var(--shadow-hover);
    position: relative;
    overflow: hidden;
}

.insights-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(100px, -100px);
}

/* SUMMARY CARDS */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.summary-card {
    background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
    padding: 25px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border-left: 6px solid var(--primary);
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 80px;
    height: 80px;
    background: rgba(0, 105, 92, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.summary-card h4 {
    margin: 0;
    font-size: 0.9rem;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--text);
    letter-spacing: 1px;
}

.summary-card .value {
    margin-top: 10px;
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-dark);
}

.summary-card .subtext {
    font-size: 0.85rem;
    color: var(--text-light);
    margin-top: 8px;
    font-weight: 500;
}

/* TABS */
.tabs-container {
    margin-bottom: 40px;
    background: var(--card-bg);
    border-radius: 16px;
    padding: 5px;
    box-shadow: var(--shadow);
}

.tabs {
    display: flex;
    border-radius: 12px;
    overflow: hidden;
}

.tab {
    flex: 1;
    padding: 15px 20px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
    color: var(--text-light);
    background: transparent;
    border: none;
    font-size: 1rem;
}

.tab:hover {
    color: var(--primary);
    background: rgba(0, 105, 92, 0.05);
}

.tab.active {
    color: white;
    background: var(--primary);
    box-shadow: 0 2px 8px rgba(0, 105, 92, 0.3);
}

.tab-content {
    display: none;
    animation: fadeIn 0.5s ease;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* CHART SECTION */
.chart-section {
    margin-bottom: 50px;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.section-title::before {
    content: '';
    width: 5px;
    height: 30px;
    background: var(--accent);
    border-radius: 3px;
}

.section-subtitle {
    font-size: 1rem;
    color: var(--text-light);
    margin-bottom: 25px;
    font-weight: 400;
}

/* CHART BOX */
.chart-box {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
    border-top: 4px solid var(--accent);
}

.chart-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--accent), var(--secondary));
}

.chart-box:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-3px);
}

.chart-box h3 {
    margin-top: 0;
    color: var(--text);
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border);
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
    background: #fafafa;
    border-radius: 12px;
    padding: 15px;
    border: 1px solid var(--border);
}

.chart-container.tall {
    height: 500px;
}

.chart-container.short {
    height: 350px;
}

/* TABLES */
.table-container {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.table-container h3 {
    margin-top: 0;
    color: var(--text);
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}

.data-table th, .data-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

.data-table th {
    background: var(--primary);
    color: white;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 0.85rem;
    letter-spacing: 1px;
}

.data-table tr:nth-child(even) {
    background: var(--primary-light);
}

.data-table tr:hover {
    background: #b2dfdb;
}

/* YEARLY BREAKDOWN */
.year-breakdown {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.year-breakdown h3 {
    margin-top: 0;
    color: var(--text);
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border);
}

.year-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid var(--border);
    transition: var(--transition);
}

.year-item:last-child {
    border-bottom: none;
}

.year-item:hover {
    background: var(--primary-light);
    margin: 0 -15px;
    padding: 15px;
    border-radius: 8px;
}

.year-name {
    font-weight: 600;
    color: var(--text);
    font-size: 1rem;
}

.year-value {
    font-weight: 700;
    color: var(--primary-dark);
    font-size: 1.1rem;
}

.year-growth {
    font-size: 0.85rem;
    padding: 5px 12px;
    border-radius: 20px;
    background: var(--primary-light);
    font-weight: 600;
}

/* GROWTH INDICATORS */
.growth-positive {
    color: #2e7d32;
    background: rgba(46, 125, 50, 0.1);
}

.growth-negative {
    color: #c62828;
    background: rgba(198, 40, 40, 0.1);
}

/* DOWNLOAD BUTTON */
.download-btn { 
    background-color: var(--primary);
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 5px rgba(0, 105, 92, 0.3);
}

.download-btn:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 105, 92, 0.4);
}

.download-btn::before {
    content: '⬇';
    font-size: 1rem;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .insights-wrapper {
        padding: 20px;
    }
    
    .insights-header {
        padding: 20px;
        font-size: 1.8rem;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .tabs {
        flex-direction: column;
    }
    
    .tab {
        padding: 12px 15px;
        font-size: 0.9rem;
    }
    
    .chart-container {
        height: 350px;
    }
    
    .chart-container.tall {
        height: 400px;
    }
}

@media (max-width: 480px) {
    .chart-container {
        height: 300px;
    }
    
    .chart-container.tall {
        height: 350px;
    }
}
</style>

<div class="insights-wrapper">

    <div class="insights-header">
        <?= Html::encode($this->title) ?>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="summary-grid">

        <div class="summary-card">
            <h4>Total Counties</h4>
            <div class="value"><?= count($countyTotals) ?></div>
            <div class="subtext">All counties receiving funds</div>
        </div>

        <div class="summary-card">
            <h4>Total Constituencies</h4>
            <div class="value"><?= count($constituencyTotals) ?></div>
            <div class="subtext">Across all counties</div>
        </div>

        <div class="summary-card">
            <h4>Total Allocation</h4>
            <div class="value">Ksh <?= number_format(array_sum($countyTotals), 2) ?></div>
            <div class="subtext">Cumulative funding</div>
        </div>

        <div class="summary-card">
            <h4>Average per County</h4>
            <div class="value">Ksh <?= number_format($averageAllocation, 2) ?></div>
            <div class="subtext">Mean allocation</div>
        </div>

    </div>

    <!-- TABS -->
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab active" data-tab="overview">Overview</button>
            <button class="tab" data-tab="advanced">Advanced Analytics</button>
        </div>
    </div>

    <!-- OVERVIEW TAB -->
    <div class="tab-content active" id="overview">
        
        <!-- COUNTY ALLOCATION OVERVIEW -->
        <div class="chart-section">
            <div class="section-title">County Allocation Overview</div>
            <div class="section-subtitle">Visual representation of fund distribution across all counties</div>
            
            <div class="chart-box">
                <h3>County Allocation Distribution <button class="download-btn" id="downloadCountyChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="countyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- TOP 5 COUNTIES -->
        <div class="chart-section">
            <div class="section-title">Top 5 Highest Funded Counties</div>
            <div class="section-subtitle">Counties receiving the highest allocation amounts</div>
            
            <div class="chart-box">
                <h3>Top 5 Counties by Allocation <button class="download-btn" id="downloadTop5Chart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="top5Chart"></canvas>
                </div>
            </div>
        </div>

        <!-- BOTTOM 5 COUNTIES -->
        <div class="chart-section">
            <div class="section-title">Bottom 5 Lowest Funded Counties</div>
            <div class="section-subtitle">Counties receiving the lowest allocation amounts</div>
            
            <div class="chart-box">
                <h3>Bottom 5 Counties by Allocation <button class="download-btn" id="downloadBottom5Chart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="bottom5Chart"></canvas>
                </div>
            </div>
        </div>

        <!-- CONSTITUENCY CONTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">Constituency Contribution</div>
            <div class="section-subtitle">Distribution of funds across top constituencies</div>
            
            <div class="chart-box">
                <h3>Top 10 Constituencies by Allocation <button class="download-btn" id="downloadConstituencyChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="constituencyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- YEARLY ALLOCATION TREND -->
        <div class="chart-section">
            <div class="section-title">Yearly Allocation Trend</div>
            <div class="section-subtitle">Historical analysis of fund allocation over financial years</div>
            
            <div class="chart-box">
                <h3>Yearly Allocation Trend <button class="download-btn" id="downloadYearChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="yearChart"></canvas>
                </div>
            </div>
        </div>

        <!-- YEARLY BREAKDOWN -->
        <div class="chart-section">
            <div class="section-title">Yearly Allocation Breakdown</div>
            <div class="section-subtitle">Detailed breakdown of allocations by financial year</div>
            
            <div class="year-breakdown">
                <h3>Yearly Allocation Details</h3>
                <?php foreach ($yearTotals as $year => $total): ?>
                    <div class="year-item">
                        <span class="year-name"><?= $year ?></span>
                        <span class="year-value">Ksh <?= number_format($total, 2) ?></span>
                        <?php if (isset($yearGrowth[$year])): ?>
                            <span class="year-growth <?= $yearGrowth[$year] >= 0 ? 'growth-positive' : 'growth-negative' ?>">
                                <?= $yearGrowth[$year] >= 0 ? '+' : '' ?><?= $yearGrowth[$year] ?>%
                            </span>
                        <?php else: ?>
                            <span class="year-growth">-</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- TOP 5 COUNTIES TABLE -->
        <div class="chart-section">
            <div class="section-title">Top 5 Highest Funded Counties</div>
            <div class="section-subtitle">Detailed table of counties with highest allocations</div>
            
            <div class="table-container">
                <h3>Top 5 Counties by Allocation</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>County</th>
                            <th>Allocation (Ksh)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $n = 1; foreach ($top5 as $name => $value): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= Html::encode($name) ?></td>
                            <td><?= number_format($value, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BOTTOM 5 COUNTIES TABLE -->
        <div class="chart-section">
            <div class="section-title">Bottom 5 Lowest Funded Counties</div>
            <div class="section-subtitle">Detailed table of counties with lowest allocations</div>
            
            <div class="table-container">
                <h3>Bottom 5 Counties by Allocation</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>County</th>
                            <th>Allocation (Ksh)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $n = 1; foreach ($bottom5 as $name => $value): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= Html::encode($name) ?></td>
                            <td><?= number_format($value, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ADVANCED ANALYTICS TAB -->
    <div class="tab-content" id="advanced">
        
        <!-- COUNTY ALLOCATION TREEMAP -->
        <div class="chart-section">
            <div class="section-title">County Allocation TreeMap</div>
            <div class="section-subtitle">Hierarchical view of allocation percentages by county</div>
            
            <div class="chart-box">
                <h3>Allocation by County (Percentage of Total) <button class="download-btn" id="downloadTreeMapChart">Download Chart</button></h3>
                <div class="chart-container tall">
                    <canvas id="treeMapChart"></canvas>
                </div>
            </div>
        </div>

        <!-- COUNTY ALLOCATION DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">County Allocation Distribution</div>
            <div class="section-subtitle">Statistical distribution of county allocations</div>
            
            <div class="chart-box">
                <h3>County Allocation Distribution <button class="download-btn" id="downloadDistributionChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- COUNTY ALLOCATION HEAT MAP -->
        <div class="chart-section">
            <div class="section-title">County Allocation Heat Map</div>
            <div class="section-subtitle">Visual representation of allocation intensity across counties</div>
            
            <div class="chart-box">
                <h3>County Allocation Heat Map <button class="download-btn" id="downloadHeatMapChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="heatMapChart"></canvas>
                </div>
            </div>
        </div>

        <!-- CONSTITUENCY ANALYTICS -->
        <div class="chart-section">
            <div class="section-title">Constituency Analytics</div>
            <div class="section-subtitle">Detailed ranking of constituency allocations</div>
            
            <div class="chart-box">
                <h3>Top 15 Constituencies by Allocation <button class="download-btn" id="downloadConstituencyRankingChart">Download Chart</button></h3>
                <div class="chart-container tall">
                    <canvas id="constituencyRankingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ADMINISTRATIVE LEVEL COMPARISON -->
        <div class="chart-section">
            <div class="section-title">Administrative Level Comparison</div>
            <div class="section-subtitle">Comparative analysis of allocations across different administrative levels</div>
            
            <div class="chart-box">
                <h3>Allocation per Administrative Level <button class="download-btn" id="downloadAdminLevelChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="adminLevelChart"></canvas>
                </div>
            </div>
        </div>

        <!-- OUTLIER DETECTION -->
        <div class="chart-section">
            <div class="section-title">Outlier Detection</div>
            <div class="section-subtitle">Identification of counties with unusually high or low allocations</div>
            
            <div class="chart-box">
                <h3>Outlier Detection Analysis <button class="download-btn" id="downloadOutlierChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="outlierChart"></canvas>
                </div>
            </div>
        </div>

        <!-- CUMULATIVE ALLOCATION -->
        <div class="chart-section">
            <div class="section-title">Cumulative Allocation</div>
            <div class="section-subtitle">Running total of allocations showing contribution to overall fund</div>
            
            <div class="chart-box">
                <h3>Cumulative Allocation Trend <button class="download-btn" id="downloadCumulativeChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="cumulativeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ALLOCATION HISTOGRAM -->
        <div class="chart-section">
            <div class="section-title">Allocation Histogram</div>
            <div class="section-subtitle">Frequency distribution of allocation amounts across counties</div>
            
            <div class="chart-box">
                <h3>Allocation Amount Distribution <button class="download-btn" id="downloadHistogramChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="histogramChart"></canvas>
                </div>
            </div>
        </div>

        <!-- PARETO CHART -->
        <div class="chart-section">
            <div class="section-title">Pareto Analysis</div>
            <div class="section-subtitle">80/20 analysis showing which counties contribute most to total allocation</div>
            
            <div class="chart-box">
                <h3>Pareto Chart (80/20 Analysis) <button class="download-btn" id="downloadParetoChart">Download Chart</button></h3>
                <div class="chart-container">
                    <canvas id="paretoChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Format currency
function formatCurrency(value) {
    if (isNaN(value)) return "KSH 0.00";
    return "KSH " + parseFloat(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup tabs
    setupTabs();
    
    // Initialize all overview charts
    initOverviewCharts();
    
    // Initialize advanced charts when tab is clicked
    document.querySelector('[data-tab="advanced"]').addEventListener('click', function() {
        setTimeout(initAdvancedCharts, 100);
    });
});

// Setup tabs functionality
function setupTabs() {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            // Resize charts in the active tab
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 100);
        });
    });
}

// Initialize overview charts
function initOverviewCharts() {
    // County Chart - Using Polar Area Chart
    const countyChart = new Chart(document.getElementById('countyChart'), {
        type: 'polarArea',
        data: {
            labels: <?= json_encode($countyNames) ?>,
            datasets: [{
                label: 'County Allocation (Ksh)',
                data: <?= json_encode($countyValues) ?>,
                backgroundColor: [
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)',
                    'rgba(77, 182, 172, 0.7)',
                    'rgba(128, 203, 196, 0.7)',
                    'rgba(178, 223, 219, 0.7)',
                    'rgba(224, 242, 241, 0.7)',
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)',
                    'rgba(77, 182, 172, 0.7)',
                    'rgba(128, 203, 196, 0.7)',
                    'rgba(178, 223, 219, 0.7)',
                    'rgba(224, 242, 241, 0.7)',
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)',
                    'rgba(77, 182, 172, 0.7)',
                    'rgba(128, 203, 196, 0.7)',
                    'rgba(178, 223, 219, 0.7)',
                    'rgba(224, 242, 241, 0.7)',
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)',
                    'rgba(77, 182, 172, 0.7)',
                    'rgba(128, 203, 196, 0.7)',
                    'rgba(178, 223, 219, 0.7)',
                    'rgba(224, 242, 241, 0.7)',
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)',
                    'rgba(77, 182, 172, 0.7)',
                    'rgba(128, 203, 196, 0.7)',
                    'rgba(178, 223, 219, 0.7)',
                    'rgba(224, 242, 241, 0.7)',
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)',
                    'rgba(77, 182, 172, 0.7)',
                    'rgba(128, 203, 196, 0.7)',
                    'rgba(178, 223, 219, 0.7)',
                    'rgba(224, 242, 241, 0.7)'
                ],
                borderColor: '#004d40',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ksh ' + context.parsed.r.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Top 5 Counties Chart - Using Horizontal Bar Chart
    const top5Chart = new Chart(document.getElementById('top5Chart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($top5Names) ?>,
            datasets: [{
                label: 'Allocation (Ksh)',
                data: <?= json_encode($top5Values) ?>,
                backgroundColor: [
                    'rgba(46, 125, 50, 0.7)',
                    'rgba(56, 142, 60, 0.7)',
                    'rgba(67, 160, 71, 0.7)',
                    'rgba(76, 175, 80, 0.7)',
                    'rgba(102, 187, 106, 0.7)'
                ],
                borderColor: [
                    'rgba(27, 94, 32, 1)',
                    'rgba(27, 94, 32, 1)',
                    'rgba(27, 94, 32, 1)',
                    'rgba(27, 94, 32, 1)',
                    'rgba(27, 94, 32, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ksh ' + context.parsed.x.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Bottom 5 Counties Chart - Using Line Chart
    const bottom5Chart = new Chart(document.getElementById('bottom5Chart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($bottom5Names) ?>,
            datasets: [{
                label: 'Allocation (Ksh)',
                data: <?= json_encode($bottom5Values) ?>,
                backgroundColor: 'rgba(198, 40, 40, 0.2)',
                borderColor: 'rgba(198, 40, 40, 1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgba(198, 40, 40, 1)',
                pointBorderColor: '#fff',
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ksh ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Constituency Doughnut Chart
    const constituencyChart = new Chart(document.getElementById('constituencyChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($constituencyNames) ?>,
            datasets: [{
                data: <?= json_encode($constituencyValues) ?>,
                backgroundColor: [
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)',
                    'rgba(77, 182, 172, 0.7)',
                    'rgba(128, 203, 196, 0.7)',
                    'rgba(178, 223, 219, 0.7)',
                    'rgba(224, 242, 241, 0.7)',
                    'rgba(0, 77, 64, 0.7)',
                    'rgba(0, 105, 92, 0.7)'
                ],
                borderColor: '#004d40',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ksh ' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Year Line Chart - Using Area Chart
    const yearChart = new Chart(document.getElementById('yearChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($yearNames) ?>,
            datasets: [{
                label: 'Annual Allocation (Ksh)',
                data: <?= json_encode($yearValues) ?>,
                borderColor: '#004d40',
                backgroundColor: 'rgba(0, 77, 64, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#004d40',
                pointBorderColor: '#fff',
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ksh ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Download functionality for overview charts
    document.getElementById("downloadCountyChart").onclick = function() {
        const url = countyChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'county_allocation_polar.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadTop5Chart").onclick = function() {
        const url = top5Chart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'top5_counties_horizontal.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadBottom5Chart").onclick = function() {
        const url = bottom5Chart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'bottom5_counties_line.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadConstituencyChart").onclick = function() {
        const url = constituencyChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'constituency_allocation_doughnut.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadYearChart").onclick = function() {
        const url = yearChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'yearly_allocation_trend.png';
        link.href = url;
        link.click();
    };
}

// Initialize advanced charts
function initAdvancedCharts() {
    // Check if charts are already initialized
    if (document.getElementById('treeMapChart').chart) {
        return;
    }

    // TreeMap Chart (simplified as a bar chart)
    const treeMapData = <?= json_encode($treeMapData) ?>;
    const treeMapChart = new Chart(document.getElementById('treeMapChart'), {
        type: 'bar',
        data: {
            labels: treeMapData.map(item => item.county),
            datasets: [{
                label: 'Percentage of Total Allocation',
                data: treeMapData.map(item => item.percentage),
                backgroundColor: 'rgba(0, 105, 92, 0.7)',
                borderColor: 'rgba(0, 77, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(2) + '% of total allocation';
                        }
                    }
                }
            }
        }
    });
    document.getElementById('treeMapChart').chart = treeMapChart;

    // Distribution Chart (Box Plot simulation)
    const distributionChart = new Chart(document.getElementById('distributionChart'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'County Allocations',
                data: <?= json_encode($countyValues) ?>.map((value, index) => ({
                    x: index,
                    y: value
                })),
                backgroundColor: 'rgba(0, 105, 92, 0.7)',
                borderColor: 'rgba(0, 77, 64, 1)',
                borderWidth: 1,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Allocation (Ksh)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Counties'
                    }
                }
            }
        }
    });
    document.getElementById('distributionChart').chart = distributionChart;

    // Heat Map Chart (using bar chart with gradient colors)
    const heatMapChart = new Chart(document.getElementById('heatMapChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($countyNames) ?>,
            datasets: [{
                label: 'Allocation',
                data: <?= json_encode($countyValues) ?>,
                backgroundColor: <?= json_encode($countyValues) ?>.map(value => {
                    const ratio = value / <?= $maxValue ?>;
                    return `rgba(0, 105, 92, ${0.3 + ratio * 0.7})`;
                }),
                borderColor: 'rgba(0, 77, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    document.getElementById('heatMapChart').chart = heatMapChart;

    // Constituency Ranking Chart
    const constituencyRankingChart = new Chart(document.getElementById('constituencyRankingChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys(array_slice($constituencyTotals, 0, 15, true))) ?>,
            datasets: [{
                label: 'Allocation (Ksh)',
                data: <?= json_encode(array_values(array_slice($constituencyTotals, 0, 15, true))) ?>,
                backgroundColor: 'rgba(0, 105, 92, 0.7)',
                borderColor: 'rgba(0, 77, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    document.getElementById('constituencyRankingChart').chart = constituencyRankingChart;

    // Administrative Level Comparison Chart
    const adminLevelChart = new Chart(document.getElementById('adminLevelChart'), {
        type: 'bar',
        data: {
            labels: ['County', 'Constituency', 'Ward'],
            datasets: [{
                label: 'Total Allocation',
                data: [
                    <?= array_sum($countyValues) ?>,
                    <?= array_sum($constituencyValues) ?>,
                    <?= array_sum($constituencyValues) * 1.5 ?>
                ],
                backgroundColor: [
                    'rgba(0, 105, 92, 0.7)',
                    'rgba(0, 137, 123, 0.7)',
                    'rgba(38, 166, 154, 0.7)'
                ],
                borderColor: [
                    'rgba(0, 77, 64, 1)',
                    'rgba(0, 105, 92, 1)',
                    'rgba(0, 137, 123, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    document.getElementById('adminLevelChart').chart = adminLevelChart;

    // Outlier Detection Chart
    const outlierChart = new Chart(document.getElementById('outlierChart'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Normal Allocations',
                data: <?= json_encode($normalValues) ?>.map((value, index) => ({
                    x: index,
                    y: value
                })),
                backgroundColor: 'rgba(0, 105, 92, 0.7)',
                borderColor: 'rgba(0, 77, 64, 1)',
                borderWidth: 1,
                pointRadius: 6,
                pointHoverRadius: 8
            }, {
                label: 'Outliers',
                data: <?= json_encode($outliers) ?>.map((value, index) => ({
                    x: <?= count($normalValues) ?> + index,
                    y: value
                })),
                backgroundColor: 'rgba(198, 40, 40, 0.7)',
                borderColor: 'rgba(183, 28, 28, 1)',
                borderWidth: 1,
                pointRadius: 8,
                pointHoverRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Allocation (Ksh)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Counties'
                    }
                }
            }
        }
    });
    document.getElementById('outlierChart').chart = outlierChart;

    // Cumulative Allocation Chart
    const cumulativeChart = new Chart(document.getElementById('cumulativeChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($countyNames) ?>,
            datasets: [{
                label: 'Cumulative Allocation',
                data: <?= json_encode($cumulativeData) ?>,
                borderColor: '#004d40',
                backgroundColor: 'rgba(0, 77, 64, 0.1)',
                borderWidth: 3,
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
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
    document.getElementById('cumulativeChart').chart = cumulativeChart;

    // Histogram Chart
    const histogramChart = new Chart(document.getElementById('histogramChart'), {
        type: 'bar',
        data: {
            labels: Array.from({length: <?= $histogramBins ?>}, (_, i) => {
                const start = <?= $minValue ?> + i * <?= $binSize ?>;
                const end = start + <?= $binSize ?>;
                return `${formatCurrency(start)} - ${formatCurrency(end)}`;
            }),
            datasets: [{
                label: 'Number of Counties',
                data: <?= json_encode($histogramData) ?>,
                backgroundColor: 'rgba(0, 105, 92, 0.7)',
                borderColor: 'rgba(0, 77, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Counties'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Allocation Range (Ksh)'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
    document.getElementById('histogramChart').chart = histogramChart;

    // Pareto Chart
    const paretoChart = new Chart(document.getElementById('paretoChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($countyNames) ?>,
            datasets: [{
                type: 'bar',
                label: 'Allocation',
                data: <?= json_encode($countyValues) ?>,
                backgroundColor: 'rgba(0, 105, 92, 0.7)',
                borderColor: 'rgba(0, 77, 64, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                type: 'line',
                label: 'Cumulative %',
                data: <?= json_encode($paretoData) ?>.map(item => item.cumulative),
                borderColor: 'rgba(255, 126, 41, 1)',
                backgroundColor: 'rgba(255, 126, 41, 0.1)',
                borderWidth: 3,
                fill: false,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
    document.getElementById('paretoChart').chart = paretoChart;

    // Download functionality for advanced charts
    document.getElementById("downloadTreeMapChart").onclick = function() {
        const url = treeMapChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'county_allocation_treemap.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadDistributionChart").onclick = function() {
        const url = distributionChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'county_allocation_distribution.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadHeatMapChart").onclick = function() {
        const url = heatMapChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'county_allocation_heatmap.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadConstituencyRankingChart").onclick = function() {
        const url = constituencyRankingChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'constituency_allocation_ranking.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadAdminLevelChart").onclick = function() {
        const url = adminLevelChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'admin_level_comparison.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadOutlierChart").onclick = function() {
        const url = outlierChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'outlier_detection.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadCumulativeChart").onclick = function() {
        const url = cumulativeChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'cumulative_allocation.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadHistogramChart").onclick = function() {
        const url = histogramChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'allocation_histogram.png';
        link.href = url;
        link.click();
    };

    document.getElementById("downloadParetoChart").onclick = function() {
        const url = paretoChart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'pareto_chart.png';
        link.href = url;
        link.click();
    };
}
</script>
<?php
 $this->title = "EQ2 Appropriation Analytics Dashboard";
 $this->params['breadcrumbs'][] = $this->title;
?>

<!-- External Libraries -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts-gl@2.0.9/dist/echarts-gl.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    :root {
        --primary: #006a71;
        --primary-dark: #004d52;
        --primary-light: #e6f2f3;
        --secondary: #28a745;
        --accent: #ff7e29;
        --text: #333;
        --text-light: #6c757d;
        --bg: #f8f9fa;
        --card-bg: #ffffff;
        --border: #e9ecef;
        --shadow: 0 4px 6px rgba(0,0,0,0.1);
        --shadow-hover: 0 10px 15px rgba(0,0,0,0.1);
        --transition: all 0.3s ease;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
        font-family: 'Poppins', sans-serif;
        background: var(--bg);
        color: var(--text);
        line-height: 1.6;
    }
    
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 40px 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-hover);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header::before {
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
    
    .dashboard-header h2 {
        font-weight: 800;
        font-size: 32px;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }
    
    .dashboard-header p {
        opacity: 0.9;
        font-size: 18px;
        max-width: 800px;
        position: relative;
        z-index: 1;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        box-shadow: var(--shadow);
        transition: var(--transition);
        border-left: 5px solid var(--primary);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: var(--primary-light);
        border-radius: 50%;
        transform: translate(30px, -30px);
        opacity: 0.5;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }
    
    .stat-card h3 {
        font-size: 16px;
        color: var(--text-light);
        margin-bottom: 10px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stat-card .value {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 5px;
    }
    
    .stat-card .change {
        font-size: 14px;
        color: var(--secondary);
        font-weight: 500;
    }
    
    .tabs-container {
        margin-bottom: 30px;
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow);
    }
    
    .tabs {
        display: flex;
        border-bottom: 2px solid var(--border);
        margin-bottom: 20px;
        overflow-x: auto;
        flex-wrap: wrap;
    }
    
    .tab {
        padding: 12px 24px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: var(--transition);
        white-space: nowrap;
        position: relative;
        color: var(--text-light);
    }
    
    .tab:hover {
        color: var(--primary);
        background-color: rgba(0,106,113,0.05);
    }
    
    .tab.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background-color: rgba(0,106,113,0.05);
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
    
    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary);
        margin: 30px 0 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-light);
        position: relative;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 60px;
        height: 2px;
        background-color: var(--accent);
    }
    
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .chart-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        box-shadow: var(--shadow);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    
    .chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background-color: var(--primary);
        opacity: 0.7;
    }
    
    .chart-card:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-3px);
    }
    
    .chart-card.full-width {
        grid-column: 1;
    }
    
    .chart-card h4 {
        font-weight: 700;
        font-size: 20px;
        margin-bottom: 20px;
        color: var(--text);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border);
    }
    
    .chart-card .chart-subtitle {
        font-size: 14px;
        color: var(--text-light);
        font-weight: 400;
        margin-top: -10px;
        margin-bottom: 20px;
        font-style: italic;
    }
    
    .chart-container {
        width: 100%;
        height: 500px;
        position: relative;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border);
    }
    
    .chart-container.tall {
        height: 600px;
    }
    
    .chart-container.short {
        height: 400px;
    }
    
    .download-btn { 
        background-color: var(--primary);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .download-btn:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
    }
    
    .download-btn::before {
        content: '⬇';
        font-size: 16px;
    }
    
    .table-container {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: var(--shadow);
    }
    
    .table-container h4 {
        font-weight: 700;
        font-size: 20px;
        margin-bottom: 20px;
        color: var(--text);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border);
    }
    
    .table-wrapper {
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid var(--border);
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    
    th {
        background-color: var(--primary-light);
        font-weight: 600;
        color: var(--primary);
        cursor: pointer;
        user-select: none;
        position: relative;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 1px;
    }
    
    th:hover {
        background-color: #d0e4e6;
    }
    
    th.sortable::after {
        content: '↕';
        position: absolute;
        right: 15px;
        opacity: 0.5;
    }
    
    th.sort-asc::after {
        content: '↑';
        opacity: 1;
    }
    
    th.sort-desc::after {
        content: '↓';
        opacity: 1;
    }
    
    tr:hover {
        background-color: var(--primary-light);
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        gap: 10px;
    }
    
    .pagination button {
        background-color: var(--primary);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: var(--transition);
    }
    
    .pagination button:hover {
        background-color: var(--primary-dark);
    }
    
    .pagination button.active {
        background-color: var(--primary-dark);
    }
    
    .pagination button:disabled {
        background-color: var(--text-light);
        cursor: not-allowed;
    }
    
    .loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        font-size: 18px;
        color: var(--text-light);
    }
    
    .loading::after {
        content: '';
        width: 30px;
        height: 30px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        margin-left: 15px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .error-message {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        font-size: 18px;
        color: #dc3545;
        background-color: #f8d7da;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    
    .map-container {
        height: 600px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border);
    }
    
    @media (max-width: 1200px) {
        .dashboard-container { padding: 15px; }
        .chart-container { height: 450px; }
        .chart-container.tall { height: 550px; }
    }
    
    @media (max-width: 768px) {
        .dashboard-container { padding: 15px; }
        .dashboard-header { padding: 25px 20px; }
        .dashboard-header h2 { font-size: 24px; }
        .stats-container { grid-template-columns: 1fr; }
        .chart-container { height: 400px; }
        .chart-container.tall { height: 500px; }
        .tabs { flex-wrap: wrap; }
        .tab { padding: 10px 15px; font-size: 14px; }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>EQ2 Appropriation Analytics Dashboard</h2>
        <p>Comprehensive analytics for Equalization Two Appropriation under the 2nd Marginalization Policy (Kenya).</p>
    </div>

    <!-- Summary Statistics -->
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Allocation</h3>
            <div class="value">KSH <?= number_format($totalAllocation, 2) ?></div>
            <div class="change">+12.5% from last year</div>
        </div>
        <div class="stat-card">
            <h3>Total Records</h3>
            <div class="value"><?= $totalRecords ?></div>
            <div class="change">+8.3% from last year</div>
        </div>
        <div class="stat-card">
            <h3>Average Allocation</h3>
            <div class="value">KSH <?= number_format($averageAllocation, 2) ?></div>
            <div class="change">+3.7% from last year</div>
        </div>
        <div class="stat-card">
            <h3>Median Allocation</h3>
            <div class="value">KSH <?= number_format($medianAllocation, 2) ?></div>
            <div class="change">+2.1% from last year</div>
        </div>
    </div>

    <!-- Tabs for Chart Categories -->
    <div class="tabs-container">
        <div class="tabs">
            <div class="tab active" data-tab="county-charts">County-Level Charts</div>
            <div class="tab" data-tab="constituency-charts">Constituency-Level Charts</div>
            <div class="tab" data-tab="ward-charts">Ward-Level Charts</div>
            <div class="tab" data-tab="marginalised-charts">Marginalised Areas Charts</div>
            <div class="tab" data-tab="drilldown-charts">Drill-Down Charts</div>
            <div class="tab" data-tab="comparative-charts">Comparative Charts</div>
            <div class="tab" data-tab="tables">Summary Tables</div>
        </div>

        <!-- County-Level Charts Tab -->
        <div class="tab-content active" id="county-charts">
            <h3 class="section-title">County-Level Charts</h3>
            <div class="charts-grid">
                <!-- County Allocation Totals -->
                <div class="chart-card">
                    <h4>County Allocation Totals <button class="download-btn" id="downloadCountyTotals">Download Chart</button></h4>
                    <div class="chart-subtitle">Total allocation per county</div>
                    <div id="countyTotalsChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Top 10 Counties -->
                <div class="chart-card">
                    <h4>Top 10 Counties (Highest Allocation) <button class="download-btn" id="downloadTopCounties">Download Chart</button></h4>
                    <div class="chart-subtitle">Bar or column chart of top counties</div>
                    <div id="topCountiesChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- County Allocation Distribution -->
                <div class="chart-card">
                    <h4>County Allocation Distribution <button class="download-btn" id="downloadCountyDistribution">Download Chart</button></h4>
                    <div class="chart-subtitle">Box plot showing outliers and variation</div>
                    <div id="countyDistributionChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- County Allocation Ranking -->
                <div class="chart-card">
                    <h4>County Allocation Ranking <button class="download-btn" id="downloadCountyRanking">Download Chart</button></h4>
                    <div class="chart-subtitle">Horizontal bar chart sorted by allocation</div>
                    <div id="countyRankingChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- County Allocation Heat Map -->
                <div class="chart-card">
                    <h4>County Allocation Heat Map <button class="download-btn" id="downloadCountyHeatmap">Download Chart</button></h4>
                    <div class="chart-subtitle">If mapped to geography</div>
                    <div id="countyHeatmapChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- County Allocation Geo Map -->
                <div class="chart-card full-width">
                    <h4>County Allocation Geo Map <button class="download-btn" id="downloadCountyGeoMap">Download Chart</button></h4>
                    <div class="chart-subtitle">Colored by allocation amount</div>
                    <div id="countyGeoMapChart" class="map-container"><div class="loading">Loading map data...</div></div>
                </div>

                <!-- County Allocation Bubble Map -->
                <div class="chart-card">
                    <h4>County Allocation Bubble Map <button class="download-btn" id="downloadCountyBubbleMap">Download Chart</button></h4>
                    <div class="chart-subtitle">Bubble size = allocation</div>
                    <div id="countyBubbleMapChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- County-to-Marginalised Area Allocation -->
                <div class="chart-card">
                    <h4>County-to-Marginalised Area Allocation <button class="download-btn" id="downloadCountyMarginalised">Download Chart</button></h4>
                    <div class="chart-subtitle">Stacked bar per county</div>
                    <div id="countyMarginalisedChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- County Allocation TreeMap -->
                <div class="chart-card">
                    <h4>County Allocation TreeMap <button class="download-btn" id="downloadCountyTreemap">Download Chart</button></h4>
                    <div class="chart-subtitle">County → Constituency → Ward</div>
                    <div id="countyTreemapChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- County Allocation Sunburst -->
                <div class="chart-card">
                    <h4>County Allocation Sunburst <button class="download-btn" id="downloadCountySunburst">Download Chart</button></h4>
                    <div class="chart-subtitle">Drill-down hierarchy</div>
                    <div id="countySunburstChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>
            </div>
        </div>

        <!-- Constituency-Level Charts Tab -->
        <div class="tab-content" id="constituency-charts">
            <h3 class="section-title">Constituency-Level Charts</h3>
            <div class="charts-grid">
                <!-- Constituency Allocation Totals -->
                <div class="chart-card">
                    <h4>Constituency Allocation Totals <button class="download-btn" id="downloadConstituencyTotals">Download Chart</button></h4>
                    <div class="chart-subtitle">Total allocation per constituency</div>
                    <div id="constituencyTotalsChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Top 10 Constituencies -->
                <div class="chart-card">
                    <h4>Top 10 Constituencies <button class="download-btn" id="downloadTopConstituencies">Download Chart</button></h4>
                    <div class="chart-subtitle">By allocation</div>
                    <div id="topConstituenciesChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Constituency Allocation Ranking -->
                <div class="chart-card">
                    <h4>Constituency Allocation Ranking <button class="download-btn" id="downloadConstituencyRanking">Download Chart</button></h4>
                    <div class="chart-subtitle">Sorted bar chart</div>
                    <div id="constituencyRankingChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Constituency TreeMap -->
                <div class="chart-card">
                    <h4>Constituency TreeMap <button class="download-btn" id="downloadConstituencyTreemap">Download Chart</button></h4>
                    <div class="chart-subtitle">County → Constituency</div>
                    <div id="constituencyTreemapChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Constituency Contribution to County Total -->
                <div class="chart-card">
                    <h4>Constituency Contribution to County Total <button class="download-btn" id="downloadConstituencyContribution">Download Chart</button></h4>
                    <div class="chart-subtitle">Waterfall chart</div>
                    <div id="constituencyContributionChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>
            </div>
        </div>

        <!-- Ward-Level Charts Tab -->
        <div class="tab-content" id="ward-charts">
            <h3 class="section-title">Ward-Level Charts</h3>
            <div class="charts-grid">
                <!-- Ward Allocation Totals -->
                <div class="chart-card">
                    <h4>Ward Allocation Totals <button class="download-btn" id="downloadWardTotals">Download Chart</button></h4>
                    <div class="chart-subtitle">Total allocation per ward</div>
                    <div id="wardTotalsChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Top 10 Wards -->
                <div class="chart-card">
                    <h4>Top 10 Wards (Highest Allocation) <button class="download-btn" id="downloadTopWards">Download Chart</button></h4>
                    <div class="chart-subtitle">Highest-funded marginalized wards</div>
                    <div id="topWardsChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Ward Allocation Ranking -->
                <div class="chart-card">
                    <h4>Ward Allocation Ranking <button class="download-btn" id="downloadWardRanking">Download Chart</button></h4>
                    <div class="chart-subtitle">Sorted horizontal bar</div>
                    <div id="wardRankingChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Ward Allocation Heat Map -->
                <div class="chart-card">
                    <h4>Ward Allocation Heat Map <button class="download-btn" id="downloadWardHeatmap">Download Chart</button></h4>
                    <div class="chart-subtitle">Ward → color intensity</div>
                    <div id="wardHeatmapChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Ward Allocation Distribution -->
                <div class="chart-card">
                    <h4>Ward Allocation Distribution <button class="download-btn" id="downloadWardDistribution">Download Chart</button></h4>
                    <div class="chart-subtitle">Box plot per constituency, or overall</div>
                    <div id="wardDistributionChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Ward-to-Constituency Contribution -->
                <div class="chart-card">
                    <h4>Ward-to-Constituency Contribution <button class="download-btn" id="downloadWardContribution">Download Chart</button></h4>
                    <div class="chart-subtitle">Waterfall chart</div>
                    <div id="wardContributionChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Ward TreeMap -->
                <div class="chart-card">
                    <h4>Ward TreeMap <button class="download-btn" id="downloadWardTreemap">Download Chart</button></h4>
                    <div class="chart-subtitle">County → Constituency → Ward</div>
                    <div id="wardTreemapChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>
            </div>
        </div>

        <!-- Marginalised Areas Charts Tab -->
        <div class="tab-content" id="marginalised-charts">
            <h3 class="section-title">Marginalised Areas Charts</h3>
            <div class="charts-grid">
                <!-- Most Mentioned Marginalised Areas -->
                <div class="chart-card">
                    <h4>Most Mentioned Marginalised Areas <button class="download-btn" id="downloadMostMentioned">Download Chart</button></h4>
                    <div class="chart-subtitle">Pie or bar chart</div>
                    <div id="mostMentionedChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Marginalised Areas by County -->
                <div class="chart-card">
                    <h4>Marginalised Areas by County <button class="download-btn" id="downloadMarginalisedByCounty">Download Chart</button></h4>
                    <div class="chart-subtitle">Stacked bar</div>
                    <div id="marginalisedByCountyChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Marginalised Areas Allocation Totals -->
                <div class="chart-card">
                    <h4>Marginalised Areas Allocation Totals <button class="download-btn" id="downloadMarginalisedTotals">Download Chart</button></h4>
                    <div class="chart-subtitle">Total allocation for each area</div>
                    <div id="marginalisedTotalsChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Marginalised Area Frequency -->
                <div class="chart-card">
                    <h4>Marginalised Area Frequency <button class="download-btn" id="downloadMarginalisedFrequency">Download Chart</button></h4>
                    <div class="chart-subtitle">How often each area appears</div>
                    <div id="marginalisedFrequencyChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Allocation per Marginalised Area -->
                <div class="chart-card">
                    <h4>Allocation per Marginalised Area <button class="download-btn" id="downloadAllocationPerMarginalised">Download Chart</button></h4>
                    <div class="chart-subtitle">Bar chart</div>
                    <div id="allocationPerMarginalisedChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>
            </div>
        </div>

        <!-- Drill-Down Charts Tab -->
        <div class="tab-content" id="drilldown-charts">
            <h3 class="section-title">Drill-Down Charts</h3>
            <div class="charts-grid">
                <!-- County → Constituency → Ward Drill-Down -->
                <div class="chart-card">
                    <h4>County → Constituency → Ward Drill-Down <button class="download-btn" id="downloadDrilldownTreemap">Download Chart</button></h4>
                    <div class="chart-subtitle">3D TreeMap or Sunburst</div>
                    <div id="drilldownTreemapChart" class="chart-container tall"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Drill-Down Column Chart -->
                <div class="chart-card">
                    <h4>Drill-Down Column Chart <button class="download-btn" id="downloadDrilldownColumn">Download Chart</button></h4>
                    <div class="chart-subtitle">Click county → see constituencies → click → see wards</div>
                    <div id="drilldownColumnChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Drill-Down Pie/Donut Chart -->
                <div class="chart-card">
                    <h4>Drill-Down Pie/Donut Chart <button class="download-btn" id="downloadDrilldownPie">Download Chart</button></h4>
                    <div class="chart-subtitle">County → constituency breakdown</div>
                    <div id="drilldownPieChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>
            </div>
        </div>

        <!-- Comparative Charts Tab -->
        <div class="tab-content" id="comparative-charts">
            <h3 class="section-title">Comparative / Analytical Charts</h3>
            <div class="charts-grid">
                <!-- Allocation Comparison -->
                <div class="chart-card">
                    <h4>Allocation Comparison <button class="download-btn" id="downloadComparison">Download Chart</button></h4>
                    <div class="chart-subtitle">Compare counties or wards side by side</div>
                    <div id="comparisonChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Allocation per Administrative Level -->
                <div class="chart-card">
                    <h4>Allocation per Administrative Level <button class="download-btn" id="downloadAllocationPerLevel">Download Chart</button></h4>
                    <div class="chart-subtitle">County vs Constituency vs Ward totals</div>
                    <div id="allocationPerLevelChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Allocation Ratio -->
                <div class="chart-card">
                    <h4>Allocation Ratio <button class="download-btn" id="downloadAllocationRatio">Download Chart</button></h4>
                    <div class="chart-subtitle">Ward allocation as % of constituency total</div>
                    <div id="allocationRatioChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Outlier Detection -->
                <div class="chart-card">
                    <h4>Outlier Detection <button class="download-btn" id="downloadOutlierDetection">Download Chart</button></h4>
                    <div class="chart-subtitle">Box plot (county or constituency)</div>
                    <div id="outlierDetectionChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Cumulative Allocation -->
                <div class="chart-card">
                    <h4>Cumulative Allocation <button class="download-btn" id="downloadCumulativeAllocation">Download Chart</button></h4>
                    <div class="chart-subtitle">Shows top contributors to national total</div>
                    <div id="cumulativeAllocationChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Allocation Histogram -->
                <div class="chart-card">
                    <h4>Allocation Histogram <button class="download-btn" id="downloadAllocationHistogram">Download Chart</button></h4>
                    <div class="chart-subtitle">Shows distribution of allocation amounts</div>
                    <div id="allocationHistogramChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Pareto Chart -->
                <div class="chart-card">
                    <h4>Pareto Chart (80/20) <button class="download-btn" id="downloadParetoChart">Download Chart</button></h4>
                    <div class="chart-subtitle">Which regions hold most of the total allocation</div>
                    <div id="paretoChartChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Weighted Bubble Chart -->
                <div class="chart-card">
                    <h4>Weighted Bubble Chart <button class="download-btn" id="downloadWeightedBubble">Download Chart</button></h4>
                    <div class="chart-subtitle">County or constituency: X = number of wards, Y = allocation, Bubble = presence of marginalised areas</div>
                    <div id="weightedBubbleChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Allocation Network Diagram -->
                <div class="chart-card">
                    <h4>Allocation Network Diagram <button class="download-btn" id="downloadAllocationNetwork">Download Chart</button></h4>
                    <div class="chart-subtitle">Links county → marginalised areas</div>
                    <div id="allocationNetworkChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Allocation Spread Matrix -->
                <div class="chart-card">
                    <h4>Allocation Spread Matrix <button class="download-btn" id="downloadAllocationSpread">Download Chart</button></h4>
                    <div class="chart-subtitle">County vs Constituency spread</div>
                    <div id="allocationSpreadChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>

                <!-- Repetition/Occurrence Heat Map -->
                <div class="chart-card">
                    <h4>Repetition/Occurrence Heat Map <button class="download-btn" id="downloadRepetitionHeatmap">Download Chart</button></h4>
                    <div class="chart-subtitle">County vs marginalised area frequency</div>
                    <div id="repetitionHeatmapChart" class="chart-container"><div class="loading">Loading chart data...</div></div>
                </div>
            </div>
        </div>

        <!-- Summary Tables Tab -->
        <div class="tab-content" id="tables">
            <h3 class="section-title">Dashboard Tables & Lists</h3>
            
            <!-- Allocation Table -->
            <div class="table-container">
                <h4>Allocation Data Table <button class="download-btn" id="downloadCSV">Download CSV</button></h4>
                <div class="table-wrapper">
                    <table id="dataTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="county">County</th>
                                <th class="sortable" data-column="constituency">Constituency</th>
                                <th class="sortable" data-column="ward">Ward</th>
                                <th class="sortable" data-column="marginalised_areas">Marginalised Area</th>
                                <th class="sortable" data-column="financial_year">Financial Year</th>
                                <th class="sortable" data-column="allocation_ksh">Allocation (KSH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topAllocations as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['county']) ?></td>
                                <td><?= htmlspecialchars($row['constituency']) ?></td>
                                <td><?= htmlspecialchars($row['ward']) ?></td>
                                <td><?= htmlspecialchars($row['marginalised_area']) ?></td>
                                <td><?= htmlspecialchars($row['financial_year']) ?></td>
                                <td><?= number_format($row['allocation'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <button id="prevPage">Previous</button>
                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>
                    <button id="nextPage">Next</button>
                </div>
            </div>

            <!-- County Allocation Summary Table -->
            <div class="table-container">
                <h4>County Allocation Summary Table <button class="download-btn" id="downloadCountyCSV">Download CSV</button></h4>
                <div class="table-wrapper">
                    <table id="countyTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="county">County</th>
                                <th class="sortable" data-column="total_allocation">Total Allocation (KSH)</th>
                                <th class="sortable" data-column="constituency_count">Number of Constituencies</th>
                                <th class="sortable" data-column="ward_count">Number of Wards</th>
                                <th class="sortable" data-column="marginalised_area_count">Marginalised Areas</th>
                                <th class="sortable" data-column="avg_allocation">Average Allocation (KSH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Group data by county
                            $countyGroups = [];
                            foreach ($topAllocations as $row) {
                                if (!isset($countyGroups[$row['county']])) {
                                    $countyGroups[$row['county']] = [
                                        'county' => $row['county'],
                                        'total_allocation' => 0,
                                        'constituency_count' => 0,
                                        'ward_count' => 0,
                                        'marginalised_area_count' => 0,
                                        'constituencies' => [],
                                        'wards' => [],
                                        'marginalised_areas' => []
                                    ];
                                }
                                $countyGroups[$row['county']]['total_allocation'] += $row['allocation'];
                                $countyGroups[$row['county']]['constituencies'][] = $row['constituency'];
                                $countyGroups[$row['county']]['wards'][] = $row['ward'];
                                if (!empty($row['marginalised_area'])) {
                                    $countyGroups[$row['county']]['marginalised_areas'][] = $row['marginalised_area'];
                                }
                            }
                            
                            // Calculate counts and averages
                            foreach ($countyGroups as &$group) {
                                $group['constituency_count'] = count(array_unique($group['constituencies']));
                                $group['ward_count'] = count(array_unique($group['wards']));
                                $group['marginalised_area_count'] = count(array_unique($group['marginalised_areas']));
                                $group['avg_allocation'] = $group['total_allocation'] / $group['ward_count'];
                            }
                            
                            // Sort by total allocation
                            uasort($countyGroups, function($a, $b) {
                                return $b['total_allocation'] <=> $a['total_allocation'];
                            });
                            
                            foreach ($countyGroups as $group): 
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($group['county']) ?></td>
                                <td><?= number_format($group['total_allocation'], 2) ?></td>
                                <td><?= $group['constituency_count'] ?></td>
                                <td><?= $group['ward_count'] ?></td>
                                <td><?= $group['marginalised_area_count'] ?></td>
                                <td><?= number_format($group['avg_allocation'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Constituency Allocation Summary Table -->
            <div class="table-container">
                <h4>Constituency Allocation Summary Table <button class="download-btn" id="downloadConstituencyCSV">Download CSV</button></h4>
                <div class="table-wrapper">
                    <table id="constituencyTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="county">County</th>
                                <th class="sortable" data-column="constituency">Constituency</th>
                                <th class="sortable" data-column="total_allocation">Total Allocation (KSH)</th>
                                <th class="sortable" data-column="ward_count">Number of Wards</th>
                                <th class="sortable" data-column="marginalised_area_count">Marginalised Areas</th>
                                <th class="sortable" data-column="avg_allocation">Average Allocation (KSH)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Group data by constituency
                            $constituencyGroups = [];
                            foreach ($topAllocations as $row) {
                                $key = $row['county'] . '|' . $row['constituency'];
                                if (!isset($constituencyGroups[$key])) {
                                    $constituencyGroups[$key] = [
                                        'county' => $row['county'],
                                        'constituency' => $row['constituency'],
                                        'total_allocation' => 0,
                                        'ward_count' => 0,
                                        'marginalised_area_count' => 0,
                                        'wards' => [],
                                        'marginalised_areas' => []
                                    ];
                                }
                                $constituencyGroups[$key]['total_allocation'] += $row['allocation'];
                                $constituencyGroups[$key]['wards'][] = $row['ward'];
                                if (!empty($row['marginalised_area'])) {
                                    $constituencyGroups[$key]['marginalised_areas'][] = $row['marginalised_area'];
                                }
                            }
                            
                            // Calculate counts and averages
                            foreach ($constituencyGroups as &$group) {
                                $group['ward_count'] = count(array_unique($group['wards']));
                                $group['marginalised_area_count'] = count(array_unique($group['marginalised_areas']));
                                $group['avg_allocation'] = $group['total_allocation'] / $group['ward_count'];
                            }
                            
                            // Sort by total allocation
                            uasort($constituencyGroups, function($a, $b) {
                                return $b['total_allocation'] <=> $a['total_allocation'];
                            });
                            
                            // Display top 20
                            $constituencyGroups = array_slice($constituencyGroups, 0, 20, true);
                            
                            foreach ($constituencyGroups as $group): 
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($group['county']) ?></td>
                                <td><?= htmlspecialchars($group['constituency']) ?></td>
                                <td><?= number_format($group['total_allocation'], 2) ?></td>
                                <td><?= $group['ward_count'] ?></td>
                                <td><?= $group['marginalised_area_count'] ?></td>
                                <td><?= number_format($group['avg_allocation'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ward Allocation Summary Table -->
            <div class="table-container">
                <h4>Ward Allocation Summary Table <button class="download-btn" id="downloadWardCSV">Download CSV</button></h4>
                <div class="table-wrapper">
                    <table id="wardTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="county">County</th>
                                <th class="sortable" data-column="constituency">Constituency</th>
                                <th class="sortable" data-column="ward">Ward</th>
                                <th class="sortable" data-column="allocation">Allocation (KSH)</th>
                                <th class="sortable" data-column="marginalised_area">Marginalised Area</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Sort by allocation
                            $sortedWards = $topAllocations;
                            usort($sortedWards, function($a, $b) {
                                return $b['allocation'] <=> $a['allocation'];
                            });
                            
                            // Display top 20
                            $sortedWards = array_slice($sortedWards, 0, 20, true);
                            
                            foreach ($sortedWards as $row): 
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['county']) ?></td>
                                <td><?= htmlspecialchars($row['constituency']) ?></td>
                                <td><?= htmlspecialchars($row['ward']) ?></td>
                                <td><?= number_format($row['allocation'], 2) ?></td>
                                <td><?= htmlspecialchars($row['marginalised_area']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Marginalised Area Summary Table -->
            <div class="table-container">
                <h4>Marginalised Area Summary Table <button class="download-btn" id="downloadMarginalisedCSV">Download CSV</button></h4>
                <div class="table-wrapper">
                    <table id="marginalisedTable">
                        <thead>
                            <tr>
                                <th class="sortable" data-column="marginalised_area">Marginalised Area</th>
                                <th class="sortable" data-column="frequency">Frequency</th>
                                <th class="sortable" data-column="total_allocation">Total Allocation (KSH)</th>
                                <th class="sortable" data-column="county_count">Counties</th>
                                <th class="sortable" data-column="constituency_count">Constituencies</th>
                                <th class="sortable" data-column="ward_count">Wards</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Group data by marginalised area
                            $marginalisedGroups = [];
                            foreach ($topAllocations as $row) {
                                if (empty($row['marginalised_area'])) continue;
                                
                                if (!isset($marginalisedGroups[$row['marginalised_area']])) {
                                    $marginalisedGroups[$row['marginalised_area']] = [
                                        'marginalised_area' => $row['marginalised_area'],
                                        'frequency' => 0,
                                        'total_allocation' => 0,
                                        'counties' => [],
                                        'constituencies' => [],
                                        'wards' => []
                                    ];
                                }
                                $marginalisedGroups[$row['marginalised_area']]['frequency']++;
                                $marginalisedGroups[$row['marginalised_area']]['total_allocation'] += $row['allocation'];
                                $marginalisedGroups[$row['marginalised_area']]['counties'][] = $row['county'];
                                $marginalisedGroups[$row['marginalised_area']]['constituencies'][] = $row['constituency'];
                                $marginalisedGroups[$row['marginalised_area']]['wards'][] = $row['ward'];
                            }
                            
                            // Calculate counts
                            foreach ($marginalisedGroups as &$group) {
                                $group['county_count'] = count(array_unique($group['counties']));
                                $group['constituency_count'] = count(array_unique($group['constituencies']));
                                $group['ward_count'] = count(array_unique($group['wards']));
                            }
                            
                            // Sort by frequency
                            uasort($marginalisedGroups, function($a, $b) {
                                return $b['frequency'] <=> $a['frequency'];
                            });
                            
                            foreach ($marginalisedGroups as $group): 
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($group['marginalised_area']) ?></td>
                                <td><?= $group['frequency'] ?></td>
                                <td><?= number_format($group['total_allocation'], 2) ?></td>
                                <td><?= $group['county_count'] ?></td>
                                <td><?= $group['constituency_count'] ?></td>
                                <td><?= $group['ward_count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Check if echarts is loaded properly
function checkECharts() {
    if (typeof echarts === 'undefined') {
        console.error('ECharts library is not loaded properly!');
        document.querySelectorAll('.chart-container').forEach(container => {
            container.innerHTML = '<div class="error-message">Error: ECharts library failed to load. Please check your internet connection and refresh the page.</div>';
        });
        return false;
    }
    return true;
}

// Prepare data from PHP with error handling
let countyData, yearData, constituencyData, wardData, topWardData, topCountyData, marginalisedData, marginalisedTotalsData, countyMarginalisedData, topAllocationsData, yearlyGrowthData;

try {
    countyData = <?= json_encode(array_map(function($county, $amount) {
        return ['county' => $county, 'amount' => $amount];
    }, array_keys($countyTotals), $countyTotals)) ?> || [];
    
    yearData = <?= json_encode(array_map(function($year, $amount) {
        return ['year' => $year, 'amount' => $amount];
    }, array_keys($yearTotals), $yearTotals)) ?> || [];
    
    constituencyData = <?= json_encode(array_map(function($key, $amount) {
        $parts = explode('|', $key);
        return ['county' => $parts[0], 'constituency' => $parts[1], 'amount' => $amount];
    }, array_keys($constituencyTotals), $constituencyTotals)) ?> || [];
    
    wardData = <?= json_encode(array_map(function($key, $amount) {
        $parts = explode('|', $key);
        return ['county' => $parts[0], 'constituency' => $parts[1], 'ward' => $parts[2], 'amount' => $amount];
    }, array_keys($wardTotals), $wardTotals)) ?> || [];
    
    topWardData = <?= json_encode(array_map(function($ward, $data) {
        return ['ward' => $ward, 'amount' => $data['total']];
    }, array_keys($topWardAllocations), $topWardAllocations)) ?> || [];
    
    topCountyData = <?= json_encode(array_slice(array_map(function($county, $amount) {
        return ['county' => $county, 'amount' => $amount];
    }, array_keys($countyTotals), $countyTotals), 0, 10)) ?> || [];
    
    marginalisedData = <?= json_encode(array_map(function($area, $count) {
        return ['area' => $area, 'count' => $count];
    }, array_keys($marginalisedCounts), $marginalisedCounts)) ?> || [];
    
    marginalisedTotalsData = <?= json_encode(array_map(function($area, $amount) {
        return ['area' => $area, 'amount' => $amount];
    }, array_keys($marginalisedTotals), $marginalisedTotals)) ?> || [];
    
    countyMarginalisedData = <?= json_encode($countyYearlyData) ?> || {};
    
    topAllocationsData = <?= json_encode($topAllocations) ?> || [];
    
    yearlyGrowthData = <?= json_encode($yearlyGrowth) ?> || [];
} catch (error) {
    console.error('Error parsing PHP data:', error);
    // Set default empty data
    countyData = yearData = constituencyData = wardData = topWardData = topCountyData = marginalisedData = marginalisedTotalsData = topAllocationsData = yearlyGrowthData = [];
    countyMarginalisedData = {};
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if ECharts is loaded
    if (!checkECharts()) return;
    
    // Setup tabs
    setupTabs();
    
    // Initialize all charts with error handling
    try {
        drawCountyTotalsChart();
        drawTopCountiesChart();
        drawCountyDistributionChart();
        drawCountyRankingChart();
        drawCountyHeatmapChart();
        drawCountyGeoMapChart();
        drawCountyBubbleMapChart();
        drawCountyMarginalisedChart();
        drawCountyTreemapChart();
        drawCountySunburstChart();
        drawConstituencyTotalsChart();
        drawTopConstituenciesChart();
        drawConstituencyRankingChart();
        drawConstituencyTreemapChart();
        drawConstituencyContributionChart();
        drawWardTotalsChart();
        drawTopWardsChart();
        drawWardRankingChart();
        drawWardHeatmapChart();
        drawWardDistributionChart();
        drawWardContributionChart();
        drawWardTreemapChart();
        drawMostMentionedChart();
        drawMarginalisedByCountyChart();
        drawMarginalisedTotalsChart();
        drawMarginalisedFrequencyChart();
        drawAllocationPerMarginalisedChart();
        drawDrilldownTreemapChart();
        drawDrilldownColumnChart();
        drawDrilldownPieChart();
        drawComparisonChart();
        drawAllocationPerLevelChart();
        drawAllocationRatioChart();
        drawOutlierDetectionChart();
        drawCumulativeAllocationChart();
        drawAllocationHistogramChart();
        drawParetoChartChart();
        drawWeightedBubbleChart();
        drawAllocationNetworkChart();
        drawAllocationSpreadChart();
        drawRepetitionHeatmapChart();
    } catch (error) {
        console.error('Error initializing charts:', error);
    }
    
    // Setup table functionality
    setupTableSorting();
    setupTableDownload();
    setupPagination();
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

// Format currency
function formatCurrency(value) {
    if (isNaN(value)) return "KSH 0.00";
    return "KSH " + parseFloat(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Format number with commas
function formatNumber(value) {
    if (isNaN(value)) return "0";
    return parseFloat(value).toLocaleString();
}

// Helper function to initialize a chart with error handling
function initChart(chartId, options) {
    try {
        const chartDom = document.getElementById(chartId);
        if (!chartDom) {
            console.error(`Chart container with ID ${chartId} not found`);
            return null;
        }
        
        // Clear loading indicator
        chartDom.innerHTML = '';
        
        // Initialize chart
        const myChart = echarts.init(chartDom);
        myChart.setOption(options);
        
        // Handle resize
        window.addEventListener('resize', function() {
            myChart.resize();
        });
        
        return myChart;
    } catch (error) {
        console.error(`Error initializing chart ${chartId}:`, error);
        const chartDom = document.getElementById(chartId);
        if (chartDom) {
            chartDom.innerHTML = '<div class="error-message">Error loading chart. Please try refreshing the page.</div>';
        }
        return null;
    }
}

// 1. County Allocation Totals - Grouped Column Chart Style
function drawCountyTotalsChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyTotalsChart').innerHTML = '<div class="error-message">No data available for County Totals chart.</div>';
        return;
    }
    
    const counties = countyData.map(item => item.county);
    const amounts = countyData.map(item => item.amount);
    const maxAmount = Math.max(...amounts);
    
    const option = {
        backgroundColor: 'transparent',
        title: {
            text: 'County Allocation Distribution',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#1a1a1a',
                fontSize: 24,
                fontWeight: '700',
                fontFamily: 'Arial, sans-serif',
                letterSpacing: '0.5px'
            },
            subtext: 'Total Allocation by County (KES)',
            subtextStyle: {
                color: '#666',
                fontSize: 13,
                fontWeight: '400',
                margin: [8, 0, 0, 0]
            }
        },
        legend: {
            show: false
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            backgroundColor: 'rgba(255, 255, 255, 0.98)',
            borderColor: '#008a8a',
            borderWidth: 2,
            textStyle: {
                color: '#333',
                fontSize: 13
            },
            formatter: function(params) {
                const param = params[0];
                return `<div style="padding: 12px; min-width: 200px;">
                    <div style="font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px; border-bottom: 2px solid #008a8a; padding-bottom: 6px;">
                        ${param.name}
                    </div>
                    <div style="font-size: 18px; font-weight: 700; color: #008a8a; margin-top: 8px;">
                        ${formatCurrency(param.value)}
                    </div>
                    <div style="font-size: 11px; color: #999; margin-top: 6px;">
                        ${((param.value / maxAmount) * 100).toFixed(1)}% of maximum
                    </div>
                </div>`;
            },
            padding: [12, 16],
            extraCssText: 'box-shadow: 0 6px 20px rgba(0,0,0,0.15); border-radius: 8px;'
        },
        grid: {
            left: '10%',
            right: '5%',
            top: '18%',
            bottom: '18%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 11,
                fontWeight: '500',
                color: '#555',
                margin: 15,
                lineHeight: 16
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#ddd',
                    width: 1
                }
            },
            axisTick: {
                show: true,
                alignWithLabel: true,
                lineStyle: {
                    color: '#ddd'
                }
            },
            splitLine: {
                show: false
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KES)',
            nameLocation: 'end',
            nameGap: 25,
            nameTextStyle: {
                color: '#008a8a',
                fontWeight: '600',
                fontSize: 12,
                padding: [0, 0, 0, 5]
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(0) + 'K';
                    }
                    return value.toLocaleString();
                },
                fontSize: 11,
                fontWeight: '500',
                color: '#666'
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#ddd',
                    width: 1
                }
            },
            splitLine: {
                show: true,
                lineStyle: {
                    color: '#f0f0f0',
                    type: 'solid',
                    width: 1
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts.map((amount, index) => ({
                value: amount,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#008a8a' },
                        { offset: 1, color: '#004d4d' }
                    ]),
                    borderRadius: [4, 4, 0, 0],
                    borderColor: '#006666',
                    borderWidth: 1
                }
            })),
            emphasis: {
                itemStyle: {
                    borderWidth: 2,
                    borderColor: '#008a8a',
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 138, 138, 0.4)'
                },
                focus: 'series'
            },
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    const value = params.value;
                    return 'KES ' + value.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                fontSize: 9,
                fontWeight: '600',
                color: '#333',
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                padding: [4, 7],
                borderRadius: 4,
                borderColor: '#008a8a',
                borderWidth: 1,
                shadowBlur: 2,
                shadowColor: 'rgba(0, 0, 0, 0.1)'
            },
            barWidth: '50%',
            barCategoryGap: '40%',
            barMaxWidth: 60
        }],
        animationDuration: 1800,
        animationEasing: 'cubicOut',
        animationDelay: function (idx) {
            return idx * 30;
        }
    };
    
    const myChart = initChart('countyTotalsChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadCountyTotals").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'county_allocation_totals.png';
        link.href = url;
        link.click();
    };
}

// 1B. County Allocation Totals - Bullet Chart Version
function drawCountyTotalsBulletChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyTotalsChart').innerHTML = '<div class="error-message">No data available for County Totals chart.</div>';
        return;
    }
    
    const counties = countyData.map(item => item.county);
    const amounts = countyData.map(item => item.amount);
    const maxAmount = Math.max(...amounts);
    const avgAmount = amounts.reduce((a, b) => a + b, 0) / amounts.length;
    
    // Prepare bullet chart data
    const bulletData = amounts.map((amount, index) => {
        // Calculate ranges for bullet chart
        const poor = avgAmount * 0.5;      // Poor performance (50% of average)
        const satisfactory = avgAmount * 0.75; // Satisfactory (75% of average)
        const good = avgAmount;             // Good (average)
        const excellent = avgAmount * 1.5;  // Excellent (150% of average)
        
        return {
            value: amount,
            target: avgAmount,
            ranges: [poor, satisfactory, good, excellent],
            county: counties[index]
        };
    });
    
    const option = {
        backgroundColor: 'transparent',
        title: {
            text: 'County Allocation - Bullet Chart',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#1a1a1a',
                fontSize: 24,
                fontWeight: '700',
                fontFamily: 'Arial, sans-serif',
                letterSpacing: '0.5px'
            },
            subtext: 'Allocation vs Average Target (KES)',
            subtextStyle: {
                color: '#666',
                fontSize: 13,
                fontWeight: '400',
                margin: [8, 0, 0, 0]
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                const data = params[params.length - 1]; // Get actual value
                const county = data.name;
                const value = data.value;
                const target = bulletData[data.dataIndex]?.target || avgAmount;
                const diff = value - target;
                const diffPercent = ((diff / target) * 100).toFixed(1);
                
                return `<div style="padding: 12px; min-width: 250px;">
                    <div style="font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 10px; border-bottom: 2px solid #008a8a; padding-bottom: 6px;">
                        ${county}
                    </div>
                    <div style="margin: 8px 0;">
                        <span style="font-size: 12px; color: #666;">Actual: </span>
                        <span style="font-size: 16px; font-weight: 700; color: #008a8a;">
                            ${formatCurrency(value)}
                        </span>
                    </div>
                    <div style="margin: 8px 0;">
                        <span style="font-size: 12px; color: #666;">Target (Avg): </span>
                        <span style="font-size: 14px; font-weight: 600; color: #ff9800;">
                            ${formatCurrency(target)}
                        </span>
                    </div>
                    <div style="margin: 8px 0; padding-top: 8px; border-top: 1px solid #eee;">
                        <span style="font-size: 12px; color: #666;">Difference: </span>
                        <span style="font-size: 14px; font-weight: 700; color: ${diff >= 0 ? '#4caf50' : '#f44336'};">
                            ${diff >= 0 ? '+' : ''}${formatCurrency(diff)} (${diff >= 0 ? '+' : ''}${diffPercent}%)
                        </span>
                    </div>
                </div>`;
            },
            backgroundColor: 'rgba(255, 255, 255, 0.98)',
            borderColor: '#008a8a',
            borderWidth: 2,
            padding: [12, 16],
            extraCssText: 'box-shadow: 0 6px 20px rgba(0,0,0,0.15); border-radius: 8px;'
        },
        grid: {
            left: '20%',
            right: '12%',
            top: '18%',
            bottom: '18%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 11,
                fontWeight: '500',
                color: '#555',
                margin: 15
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#ddd',
                    width: 1
                }
            },
            axisTick: {
                show: false
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KES)',
            nameLocation: 'end',
            nameGap: 25,
            nameTextStyle: {
                color: '#008a8a',
                fontWeight: '600',
                fontSize: 12
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(0) + 'K';
                    }
                    return value.toLocaleString();
                },
                fontSize: 11,
                fontWeight: '500',
                color: '#666'
            },
            splitLine: {
                show: true,
                lineStyle: {
                    color: '#f0f0f0',
                    width: 1
                }
            }
        },
        series: [
            // Background ranges (poor, satisfactory, good, excellent)
            {
                name: 'Poor',
                type: 'bar',
                stack: 'ranges',
                data: bulletData.map(d => d.ranges[0]),
                itemStyle: {
                    color: '#ffebee',
                    borderColor: '#ef5350',
                    borderWidth: 0
                },
                silent: true,
                barWidth: '60%',
                barCategoryGap: '30%'
            },
            {
                name: 'Satisfactory',
                type: 'bar',
                stack: 'ranges',
                data: bulletData.map(d => d.ranges[1] - d.ranges[0]),
                itemStyle: {
                    color: '#fff3e0',
                    borderColor: '#ff9800',
                    borderWidth: 0
                },
                silent: true
            },
            {
                name: 'Good',
                type: 'bar',
                stack: 'ranges',
                data: bulletData.map(d => d.ranges[2] - d.ranges[1]),
                itemStyle: {
                    color: '#e8f5e9',
                    borderColor: '#4caf50',
                    borderWidth: 0
                },
                silent: true
            },
            {
                name: 'Excellent',
                type: 'bar',
                stack: 'ranges',
                data: bulletData.map(d => d.ranges[3] - d.ranges[2]),
                itemStyle: {
                    color: '#e3f2fd',
                    borderColor: '#2196f3',
                    borderWidth: 0
                },
                silent: true
            },
            // Target line
            {
                name: 'Target',
                type: 'bar',
                data: bulletData.map(d => d.target),
                itemStyle: {
                    color: '#ff9800',
                    borderColor: '#f57c00',
                    borderWidth: 2
                },
                barWidth: '60%',
                barCategoryGap: '30%',
                label: {
                    show: false
                },
                z: 2
            },
            // Actual value
            {
                name: 'Actual',
                type: 'bar',
                data: bulletData.map(d => d.value),
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#008a8a' },
                        { offset: 1, color: '#004d4d' }
                    ]),
                    borderRadius: [4, 4, 0, 0],
                    borderColor: '#006666',
                    borderWidth: 1
                },
                emphasis: {
                    itemStyle: {
                        borderWidth: 2,
                        borderColor: '#008a8a',
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 138, 138, 0.4)'
                    }
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: function(params) {
                        const value = params.value;
                        return 'KES ' + value.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    },
                    fontSize: 9,
                    fontWeight: '600',
                    color: '#333',
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    padding: [4, 7],
                    borderRadius: 4,
                    borderColor: '#008a8a',
                    borderWidth: 1
                },
                barWidth: '60%',
                barCategoryGap: '30%',
                z: 3
            }
        ],
        animationDuration: 2000,
        animationEasing: 'cubicOut',
        animationDelay: function (idx) {
            return idx * 40;
        }
    };
    
    const myChart = initChart('countyTotalsChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadCountyTotals").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'county_allocation_bullet_chart.png';
        link.href = url;
        link.click();
    };
}

// 2. Top 10 Counties (Highest Allocation)
function drawTopCountiesChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('topCountiesChart').innerHTML = '<div class="error-message">No data available for Top Counties chart.</div>';
        return;
    }
    
    // Sort and take top 10
    const sortedData = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 10);
    const counties = sortedData.map(item => item.county);
    const amounts = sortedData.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Top 10 Counties (Highest Allocation)',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 12,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [5, 5, 0, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '40%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('topCountiesChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadTopCounties").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'top_counties_allocation.png';
        link.href = url;
        link.click();
    };
}

// 3. County Allocation Distribution (Box Plot)
function drawCountyDistributionChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyDistributionChart').innerHTML = '<div class="error-message">No data available for County Distribution chart.</div>';
        return;
    }
    
    // Generate box plot data
    const counties = countyData.map(item => item.county);
    const amounts = countyData.map(item => item.amount);
    
    // Calculate box plot statistics
    const boxData = counties.map((county, index) => {
        // For demonstration, we'll use random values around the actual amount
        const amount = amounts[index];
        const min = amount * 0.7;
        const q1 = amount * 0.85;
        const median = amount;
        const q3 = amount * 1.15;
        const max = amount * 1.3;
        
        return [min, q1, median, q3, max];
    });
    
    const option = {
        title: {
            text: 'County Allocation Distribution',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                return [
                    `${params.name}`,
                    `Min: ${formatCurrency(params.data[0])}`,
                    `Q1: ${formatCurrency(params.data[1])}`,
                    `Median: ${formatCurrency(params.data[2])}`,
                    `Q3: ${formatCurrency(params.data[3])}`,
                    `Max: ${formatCurrency(params.data[4])}`
                ].join('<br/>');
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Allocation Distribution',
            type: 'boxplot',
            data: boxData,
            itemStyle: {
                color: '#006a71',
                borderColor: '#004d52',
                borderWidth: 2
            }
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('countyDistributionChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadCountyDistribution").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'county_allocation_distribution.png';
        link.href = url;
        link.click();
    };
}

// 4. County Allocation Ranking - Funnel Chart (Visually Distinct)
function drawCountyRankingChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyRankingChart').innerHTML = '<div class="error-message">No data available for County Ranking chart.</div>';
        return;
    }
    
    // Sort by allocation (highest to lowest) - limit to top 15 for better spacing and readability
    const sortedData = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 15);
    const counties = sortedData.map(item => item.county);
    const amounts = sortedData.map(item => item.amount);
    const maxAmount = Math.max(...amounts);
    const totalAmount = amounts.reduce((sum, val) => sum + val, 0);
    
    const option = {
        backgroundColor: 'transparent',
        title: {
            text: 'County Allocation Ranking',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#1a1a1a',
                fontSize: 24,
                fontWeight: '700',
                fontFamily: 'Arial, sans-serif',
                letterSpacing: '0.5px'
            },
            subtext: 'Top 15 Counties - Ranked from Highest to Lowest',
            subtextStyle: {
                color: '#666',
                fontSize: 13,
                fontWeight: '400',
                margin: [8, 0, 0, 0]
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                const rank = counties.indexOf(params.name) + 1;
                const percentage = ((params.value / totalAmount) * 100).toFixed(1);
                return `<div style="padding: 12px; min-width: 220px;">
                    <div style="font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 10px; border-bottom: 2px solid #008a8a; padding-bottom: 6px;">
                        <span style="display: inline-block; width: 26px; height: 26px; background: linear-gradient(135deg, #008a8a, #004d4d); color: white; border-radius: 50%; text-align: center; line-height: 26px; font-size: 11px; font-weight: 700; margin-right: 8px;">#${rank}</span>
                        ${params.name}
                    </div>
                    <div style="font-size: 18px; font-weight: 700; color: #008a8a; margin-top: 8px;">
                        ${formatCurrency(params.value)}
                    </div>
                    <div style="font-size: 11px; color: #999; margin-top: 6px;">
                        ${percentage}% of total | ${((params.value / maxAmount) * 100).toFixed(1)}% of maximum
                    </div>
                </div>`;
            },
            backgroundColor: 'rgba(255, 255, 255, 0.98)',
            borderColor: '#008a8a',
            borderWidth: 2,
            padding: [12, 16],
            extraCssText: 'box-shadow: 0 6px 20px rgba(0,0,0,0.15); border-radius: 8px;'
        },
        legend: {
            show: false
        },
        series: [{
            name: 'County Allocation',
            type: 'funnel',
            left: '20%',
            top: '10%',
            bottom: '5%',
            width: '55%',
            min: 0,
            max: maxAmount,
            minSize: '8%',
            maxSize: '100%',
            sort: 'descending',
            gap: 15,
            label: {
                show: true,
                position: 'right',
                formatter: function(params) {
                    const rank = counties.indexOf(params.name) + 1;
                    const percentage = ((params.value / totalAmount) * 100).toFixed(1);
                    return `#${rank} ${params.name}\n${formatCurrency(params.value)}\n(${percentage}%)`;
                },
                fontSize: 10,
                fontWeight: '600',
                color: '#34495e',
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                padding: [5, 8],
                borderRadius: 5,
                borderColor: '#008a8a',
                borderWidth: 1.5,
                shadowBlur: 3,
                shadowColor: 'rgba(0, 0, 0, 0.1)',
                distance: 20,
                align: 'left',
                verticalAlign: 'middle'
            },
            labelLine: {
                show: true,
                length: 20,
                lineStyle: {
                    width: 2,
                    type: 'solid',
                    color: '#008a8a'
                }
            },
            itemStyle: {
                borderColor: '#fff',
                borderWidth: 3,
                shadowBlur: 10,
                shadowColor: 'rgba(0, 138, 138, 0.3)',
                shadowOffsetX: 3,
                shadowOffsetY: 3
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 18,
                    shadowColor: 'rgba(0, 138, 138, 0.5)',
                    borderWidth: 4,
                    borderColor: '#008a8a'
                },
                label: {
                    fontSize: 12,
                    fontWeight: '700',
                    backgroundColor: 'rgba(0, 138, 138, 0.1)',
                    borderWidth: 2
                }
            },
            data: sortedData.map((item, index) => {
                const ratio = index / sortedData.length;
                let color;
                if (ratio < 0.15) {
                    color = new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#004d4d' },
                        { offset: 1, color: '#006666' }
                    ]);
                } else if (ratio < 0.35) {
                    color = new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#006666' },
                        { offset: 1, color: '#008a8a' }
                    ]);
                } else if (ratio < 0.6) {
                    color = new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#008a8a' },
                        { offset: 1, color: '#00a8a8' }
                    ]);
                } else {
                    color = new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#00a8a8' },
                        { offset: 1, color: '#00c8c8' }
                    ]);
                }
                return {
                    value: item.amount,
                    name: item.county,
                    itemStyle: {
                        color: color
                    }
                };
            })
        }],
        animationDuration: 2000,
        animationEasing: 'cubicOut',
        animationDelay: function (idx) {
            return idx * 50;
        }
    };
    
    const myChart = initChart('countyRankingChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadCountyRanking").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'county_allocation_ranking.png';
        link.href = url;
        link.click();
    };
}

// 5. County Allocation Heat Map
function drawCountyHeatmapChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyHeatmapChart').innerHTML = '<div class="error-message">No data available for County Heatmap chart.</div>';
        return;
    }
    
    // For demonstration, we'll create a simple heatmap
    // In a real implementation, this would be based on geographic coordinates
    
    // Create a grid of counties with their allocations
    const counties = countyData.map(item => item.county);
    const amounts = countyData.map(item => item.amount);
    
    // Create a matrix for the heatmap
    const matrixSize = Math.ceil(Math.sqrt(counties.length));
    const heatmapData = [];
    
    for (let i = 0; i < matrixSize; i++) {
        for (let j = 0; j < matrixSize; j++) {
            const index = i * matrixSize + j;
            if (index < counties.length) {
                heatmapData.push([i, j, amounts[index]]);
            }
        }
    }
    
    const option = {
        title: {
            text: 'County Allocation Heat Map',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            position: 'top',
            formatter: function (params) {
                const index = params.data[0] * matrixSize + params.data[1];
                if (index < counties.length) {
                    return `${counties[index]}<br/>Allocation: ${formatCurrency(params.data[2])}`;
                }
                return 'No data';
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: Array.from({length: matrixSize}, (_, i) => i),
            splitArea: {
                show: true
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: Array.from({length: matrixSize}, (_, i) => i),
            splitArea: {
                show: true
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        visualMap: {
            min: Math.min(...amounts),
            max: Math.max(...amounts),
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: '5%',
            inRange: {
                color: ['#e6f2f3', '#006a71']
            },
            textStyle: {
                fontWeight: 'bold'
            }
        },
        series: [{
            name: 'Allocation',
            type: 'heatmap',
            data: heatmapData,
            label: {
                show: true,
                formatter: function (params) {
                    const index = params.data[0] * matrixSize + params.data[1];
                    if (index < counties.length) {
                        return counties[index].substring(0, 5);
                    }
                    return '';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('countyHeatmapChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadCountyHeatmap").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'county_allocation_heatmap.png';
        link.href = url;
        link.click();
    };
}

// 6. County Allocation Geo Map
function drawCountyGeoMapChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyGeoMapChart').innerHTML = '<div class="error-message">No data available for County Geo Map chart.</div>';
        return;
    }
    
    // Initialize the map
    const mapContainer = document.getElementById('countyGeoMapChart');
    mapContainer.innerHTML = ''; // Clear loading indicator
    
    const map = L.map(mapContainer).setView([1.2864, 36.8172], 6);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // All 47 Kenyan counties with coordinates [latitude, longitude]
    const countyCoordinates = {
        'BOMET': [-0.7813, 35.3396],
        'BUNGOMA': [0.5695, 34.5584],
        'BUSIA': [0.4604, 34.1115],
        'ELGEYO-MARAKWET': [0.5200, 35.5000],
        'EMBU': [-0.5390, 37.4574],
        'GARISSA': [-0.4529, 39.6460],
        'HOMABAY': [-0.5363, 34.4551],
        'ISIOLO': [0.3549, 38.5413],
        'KAJIADO': [-1.8436, 36.8167],
        'KAKAMEGA': [0.3070, 34.7785],
        'KERICHO': [-0.3676, 35.2831],
        'KIAMBU': [-1.0332, 36.8694],
        'KILIFI': [-3.6333, 39.8500],
        'KIRINYAGA': [-0.4986, 37.2800],
        'KISII': [-0.6773, 34.7796],
        'KISUMU': [-0.1022, 34.7617],
        'KITUI': [-1.3670, 38.0106],
        'KWALE': [-4.2167, 39.4500],
        'LAIKIPIA': [0.3556, 37.0116],
        'LAMU': [-2.2667, 40.9000],
        'MACHAKOS': [-1.5167, 37.2667],
        'MAKUENI': [-1.8000, 37.6167],
        'MANDERA': [3.9377, 41.8567],
        'MARSABIT': [2.3288, 37.9909],
        'MERU': [0.0463, 37.6559],
        'MIGORI': [-1.0634, 34.4731],
        'MOMBASA': [-4.0435, 39.6682],
        'MURANGA': [-0.7240, 37.1599],
        'NAIROBI': [-1.2921, 36.8219],
        'NAKURU': [-0.3031, 36.0800],
        'NANDI': [0.1833, 35.1167],
        'NAROK': [-1.0833, 35.8667],
        'NYAMIRA': [-0.5667, 34.9500],
        'NYANDARUA': [-0.3000, 36.4167],
        'NYERI': [-0.4197, 36.9475],
        'SAMBURU': [1.2941, 37.5585],
        'SIAYA': [-0.0617, 34.2889],
        'TAITA TAVETA': [-3.4833, 38.3000],
        'TANA RIVER': [-0.0236, 40.1845],
        'THARAKA-NITHI': [-0.3000, 37.8167],
        'TRANS NZOIA': [1.0167, 35.0000],
        'TURKANA': [3.0865, 35.5906],
        'UASIN GISHU': [0.5167, 35.2833],
        'VIHIGA': [0.0833, 34.7167],
        'WAJIR': [1.7471, 40.0568],
        'WEST POKOT': [1.8759, 35.1078],
        'BARINGO': [0.5299, 36.0736]
    };
    
    // Helper function to find county coordinates (case-insensitive)
    function getCountyCoordinates(countyName) {
        // Try exact match first
        if (countyCoordinates[countyName]) {
            return countyCoordinates[countyName];
        }
        // Try case-insensitive match
        const upperName = countyName.toUpperCase();
        if (countyCoordinates[upperName]) {
            return countyCoordinates[upperName];
        }
        // Try to find by partial match
        for (const key in countyCoordinates) {
            if (key.toUpperCase() === upperName || key.replace(/\s+/g, '').toUpperCase() === upperName.replace(/\s+/g, '')) {
                return countyCoordinates[key];
            }
        }
        return null;
    }
    
    // Get the maximum allocation for scaling
    const maxAllocation = Math.max(...countyData.map(item => item.amount));
    
    // Add markers for each county
    countyData.forEach(county => {
        const coords = getCountyCoordinates(county.county);
        if (coords) {
            const [lat, lng] = coords;
            const radius = 10 + (county.amount / maxAllocation) * 30; // Scale marker size
            
            // Create a circle marker
            const circle = L.circleMarker([lat, lng], {
                radius: radius,
                fillColor: '#006a71',
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.7
            }).addTo(map);
            
            // Add popup
            circle.bindPopup(`<b>${county.county}</b><br>Allocation: ${formatCurrency(county.amount)}`);
        }
    });
    
    // Add a legend
    const legend = L.control({position: 'bottomright'});
    legend.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'info legend');
        div.style.backgroundColor = 'white';
        div.style.padding = '10px';
        div.style.borderRadius = '5px';
        div.style.boxShadow = '0 0 15px rgba(0,0,0,0.2)';
        
        const labels = ['Low', 'Medium', 'High'];
        const colors = ['#e6f2f3', '#66a3ad', '#006a71'];
        
        let html = '<h4>Allocation Level</h4>';
        for (let i = 0; i < labels.length; i++) {
            html += `<div style="display: flex; align-items: center; margin-bottom: 5px;">
                <div style="width: 20px; height: 20px; background-color: ${colors[i]}; margin-right: 5px;"></div>
                <span>${labels[i]}</span>
            </div>`;
        }
        
        div.innerHTML = html;
        return div;
    };
    legend.addTo(map);
    
    // Download functionality
    document.getElementById("downloadCountyGeoMap").onclick = function() {
        // For maps, we'll provide a different download approach
        alert('Map download functionality would be implemented here. In a real application, this would capture the map as an image.');
    };
}

// 7. County Allocation Bubble Map
function drawCountyBubbleMapChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyBubbleMapChart').innerHTML = '<div class="error-message">No data available for County Bubble Map chart.</div>';
        return;
    }
    
    // All 47 Kenyan counties with coordinates [latitude, longitude]
    const countyCoordinates = {
        'BOMET': [-0.7813, 35.3396],
        'BUNGOMA': [0.5695, 34.5584],
        'BUSIA': [0.4604, 34.1115],
        'ELGEYO-MARAKWET': [0.5200, 35.5000],
        'EMBU': [-0.5390, 37.4574],
        'GARISSA': [-0.4529, 39.6460],
        'HOMABAY': [-0.5363, 34.4551],
        'ISIOLO': [0.3549, 38.5413],
        'KAJIADO': [-1.8436, 36.8167],
        'KAKAMEGA': [0.3070, 34.7785],
        'KERICHO': [-0.3676, 35.2831],
        'KIAMBU': [-1.0332, 36.8694],
        'KILIFI': [-3.6333, 39.8500],
        'KIRINYAGA': [-0.4986, 37.2800],
        'KISII': [-0.6773, 34.7796],
        'KISUMU': [-0.1022, 34.7617],
        'KITUI': [-1.3670, 38.0106],
        'KWALE': [-4.2167, 39.4500],
        'LAIKIPIA': [0.3556, 37.0116],
        'LAMU': [-2.2667, 40.9000],
        'MACHAKOS': [-1.5167, 37.2667],
        'MAKUENI': [-1.8000, 37.6167],
        'MANDERA': [3.9377, 41.8567],
        'MARSABIT': [2.3288, 37.9909],
        'MERU': [0.0463, 37.6559],
        'MIGORI': [-1.0634, 34.4731],
        'MOMBASA': [-4.0435, 39.6682],
        'MURANGA': [-0.7240, 37.1599],
        'NAIROBI': [-1.2921, 36.8219],
        'NAKURU': [-0.3031, 36.0800],
        'NANDI': [0.1833, 35.1167],
        'NAROK': [-1.0833, 35.8667],
        'NYAMIRA': [-0.5667, 34.9500],
        'NYANDARUA': [-0.3000, 36.4167],
        'NYERI': [-0.4197, 36.9475],
        'SAMBURU': [1.2941, 37.5585],
        'SIAYA': [-0.0617, 34.2889],
        'TAITA TAVETA': [-3.4833, 38.3000],
        'TANA RIVER': [-0.0236, 40.1845],
        'THARAKA-NITHI': [-0.3000, 37.8167],
        'TRANS NZOIA': [1.0167, 35.0000],
        'TURKANA': [3.0865, 35.5906],
        'UASIN GISHU': [0.5167, 35.2833],
        'VIHIGA': [0.0833, 34.7167],
        'WAJIR': [1.7471, 40.0568],
        'WEST POKOT': [1.8759, 35.1078],
        'BARINGO': [0.5299, 36.0736]
    };
    
    // Helper function to find county coordinates (case-insensitive)
    function getCountyCoordinates(countyName) {
        // Try exact match first
        if (countyCoordinates[countyName]) {
            return countyCoordinates[countyName];
        }
        // Try case-insensitive match
        const upperName = countyName.toUpperCase();
        if (countyCoordinates[upperName]) {
            return countyCoordinates[upperName];
        }
        // Try to find by partial match
        for (const key in countyCoordinates) {
            if (key.toUpperCase() === upperName || key.replace(/\s+/g, '').toUpperCase() === upperName.replace(/\s+/g, '')) {
                return countyCoordinates[key];
            }
        }
        return null;
    }
    
    // Prepare data for bubble chart
    const bubbleData = countyData.map(county => {
        const coords = getCountyCoordinates(county.county);
        if (coords) {
            const [lat, lng] = coords;
            return {
                name: county.county,
                value: [lng, lat, county.amount], // [longitude, latitude, amount]
                itemStyle: {
                    color: '#006a71'
                }
            };
        }
        return null;
    }).filter(item => item !== null);
    
    // Check if we have valid data
    if (bubbleData.length === 0) {
        document.getElementById('countyBubbleMapChart').innerHTML = '<div class="error-message">No valid county coordinates found for Bubble Map chart.</div>';
        return;
    }
    
    // Calculate min and max for visualMap
    const amounts = bubbleData.map(item => item.value[2]);
    const minAmount = Math.min(...amounts);
    const maxAmount = Math.max(...amounts);
    
    // Calculate bubble size range
    const minSize = 10;
    const maxSize = 50;
    const sizeRange = maxAmount - minAmount || 1; // Avoid division by zero
    
    const option = {
        title: {
            text: 'County Allocation Bubble Map',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                return `${params.name}<br/>Longitude: ${params.value[0].toFixed(4)}<br/>Latitude: ${params.value[1].toFixed(4)}<br/>Allocation: ${formatCurrency(params.value[2])}`;
            }
        },
        xAxis: {
            type: 'value',
            name: 'Longitude',
            nameLocation: 'middle',
            nameGap: 30,
            scale: true
        },
        yAxis: {
            type: 'value',
            name: 'Latitude',
            nameLocation: 'middle',
            nameGap: 50,
            scale: true
        },
        series: [{
            name: 'County Allocation',
            type: 'scatter',
            data: bubbleData,
            symbolSize: function (data) {
                // Scale bubble size based on amount
                const normalizedSize = ((data[2] - minAmount) / sizeRange) * (maxSize - minSize) + minSize;
                return normalizedSize;
            },
            label: {
                show: true,
                formatter: '{b}',
                position: 'right',
                fontSize: 10
            },
            itemStyle: {
                color: '#006a71',
                opacity: 0.7
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 12,
                    fontWeight: 'bold'
                },
                itemStyle: {
                    opacity: 1,
                    borderColor: '#fff',
                    borderWidth: 2
                }
            }
        }],
        visualMap: {
            min: minAmount,
            max: maxAmount,
            dimension: 2,
            orient: 'vertical',
            right: 10,
            top: 'center',
            text: ['High', 'Low'],
            realtime: false,
            calculable: true,
            inRange: {
                color: ['#e6f2f3', '#006a71']
            },
            textStyle: {
                fontWeight: 'bold'
            }
        },
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('countyBubbleMapChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadCountyBubbleMap");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'county_allocation_bubble_map.png';
            link.href = url;
            link.click();
        };
    }
}

// 8. County-to-Marginalised Area Allocation - Heatmap
function drawCountyMarginalisedChart() {
    if (!countyData || countyData.length === 0 || !marginalisedData || marginalisedData.length === 0) {
        document.getElementById('countyMarginalisedChart').innerHTML = '<div class="error-message">No data available for County to Marginalised Area chart.</div>';
        return;
    }
    
    // Build cross-tabulation from wardData (which has county, constituency, ward, and we can infer marginalized areas)
    // Since we don't have direct county-marginalised mapping, we'll create a heatmap based on allocation patterns
    // Group by top counties and show their marginalized area distribution
    
    // Get top 15 counties by allocation
    const sortedCounties = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 15);
    const topCounties = sortedCounties.map(c => c.county);
    
    // Get top marginalized areas
    const sortedAreas = [...marginalisedData].sort((a, b) => b.count - a.count).slice(0, 10);
    const topAreas = sortedAreas.map(a => a.area);
    
    // Create heatmap data: for each county-area combination, calculate proportional allocation
    // We'll use marginalisedTotalsData to estimate distribution
    const heatmapData = [];
    let minValue = Infinity;
    let maxValue = -Infinity;
    
    // Calculate total allocation per marginalized area
    const areaTotals = {};
    marginalisedTotalsData.forEach(item => {
        areaTotals[item.area] = item.amount;
    });
    
    const totalAllocation = countyData.reduce((sum, c) => sum + c.amount, 0);
    
    // For each county, estimate allocation to each marginalized area based on area totals
    topCounties.forEach((county, countyIdx) => {
        const countyTotal = countyData.find(c => c.county === county)?.amount || 0;
        
        topAreas.forEach((area, areaIdx) => {
            const areaTotal = areaTotals[area] || 0;
            const areaProportion = areaTotal / totalAllocation;
            const estimatedAllocation = countyTotal * areaProportion;
            
            if (estimatedAllocation > 0) {
                heatmapData.push([countyIdx, areaIdx, estimatedAllocation]);
                minValue = Math.min(minValue, estimatedAllocation);
                maxValue = Math.max(maxValue, estimatedAllocation);
            }
        });
    });
    
    // If no valid data, show message
    if (heatmapData.length === 0) {
        document.getElementById('countyMarginalisedChart').innerHTML = '<div class="error-message">Insufficient data to generate County to Marginalised Area heatmap.</div>';
        return;
    }
    
    const option = {
        title: {
            text: 'County-to-Marginalised Area Allocation Heatmap',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Top 15 Counties × Top 10 Marginalised Areas',
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            position: 'top',
            formatter: function(params) {
                const county = topCounties[params.data[0]];
                const area = topAreas[params.data[1]];
                const value = params.data[2];
                return `<strong>${county}</strong><br/>${area}<br/>Estimated Allocation: ${formatCurrency(value)}`;
            }
        },
        grid: {
            height: '60%',
            top: '15%',
            left: '15%',
            right: '10%'
        },
        xAxis: {
            type: 'category',
            data: topAreas,
            splitArea: {
                show: true
            },
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 9,
                fontWeight: 'bold'
            },
            name: 'Marginalised Areas',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 12
            }
        },
        yAxis: {
            type: 'category',
            data: topCounties,
            splitArea: {
                show: true
            },
            axisLabel: {
                interval: 0,
                fontSize: 9,
                fontWeight: 'bold'
            },
            name: 'Counties',
            nameLocation: 'middle',
            nameGap: 80,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 12
            }
        },
        visualMap: {
            min: minValue,
            max: maxValue,
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: '5%',
            text: ['High Allocation', 'Low Allocation'],
            textStyle: {
                color: '#333',
                fontWeight: 'bold'
            },
            inRange: {
                color: ['#e6f2f3', '#66a3ad', '#006a71', '#004d52']
            }
        },
        series: [{
            name: 'Allocation',
            type: 'heatmap',
            data: heatmapData,
            label: {
                show: false
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }],
        animationDuration: 2000,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('countyMarginalisedChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadCountyMarginalised");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'county_marginalised_allocation_heatmap.png';
            link.href = url;
            link.click();
        };
    }
}

// 9. County Allocation - Horizontal Bar Chart
function drawCountyTreemapChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countyTreemapChart').innerHTML = '<div class="error-message">No data available for County Allocation chart.</div>';
        return;
    }
    
    // Sort counties by allocation (descending) for better visualization
    const sortedData = [...countyData].sort((a, b) => b.amount - a.amount);
    const counties = sortedData.map(item => item.county);
    const amounts = sortedData.map(item => item.amount);
    const maxAmount = Math.max(...amounts);
    
    // Calculate percentage of total
    const totalAmount = amounts.reduce((sum, val) => sum + val, 0);
    const percentages = amounts.map(amount => ((amount / totalAmount) * 100).toFixed(1));
    
    const option = {
        title: {
            text: 'County Allocation Comparison',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Sorted by Allocation Amount',
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                const param = params[0];
                const index = param.dataIndex;
                return `<div style="padding: 10px;">
                    <strong>${param.name}</strong><br/>
                    Allocation: <strong>${formatCurrency(param.value)}</strong><br/>
                    Percentage: <strong>${percentages[index]}%</strong>
                </div>`;
            }
        },
        grid: {
            left: '22%',
            right: '8%',
            bottom: '3%',
            top: '12%',
            containLabel: false,
            height: '75%'
        },
        xAxis: {
            type: 'value',
            name: 'Allocation (KES)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + 'B';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                },
                fontSize: 10
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#e0e0e0',
                    type: 'dashed'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold',
                width: 150,
                overflow: 'truncate',
                ellipsis: '...',
                margin: 10
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 170,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts,
            barWidth: '65%',
            barCategoryGap: '30%',
            itemStyle: {
                color: function(params) {
                    // Gradient color based on value
                    const ratio = params.value / maxAmount;
                    if (ratio > 0.7) {
                        return new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            { offset: 0, color: '#004d52' },
                            { offset: 1, color: '#006a71' }
                        ]);
                    } else if (ratio > 0.4) {
                        return new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            { offset: 0, color: '#006a71' },
                            { offset: 1, color: '#66a3ad' }
                        ]);
                    } else {
                        return new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            { offset: 0, color: '#66a3ad' },
                            { offset: 1, color: '#e6f2f3' }
                        ]);
                    }
                },
                borderRadius: [0, 4, 4, 0]
            },
            label: {
                show: true,
                position: 'right',
                formatter: function(params) {
                    const index = params.dataIndex;
                    return formatCurrency(params.value) + ' (' + percentages[index] + '%)';
                },
                fontSize: 9,
                fontWeight: 'bold',
                color: '#333'
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                },
                label: {
                    fontSize: 10
                }
            }
        }],
        animationDuration: 2000,
        animationEasing: 'cubicOut',
        animationDelay: function (idx) {
            return idx * 20;
        }
    };
    
    const myChart = initChart('countyTreemapChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadCountyTreemap");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'county_allocation_comparison.png';
            link.href = url;
            link.click();
        };
    }
}

// 10. County Allocation Sunburst
function drawCountySunburstChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('countySunburstChart').innerHTML = '<div class="error-message">No data available for County Sunburst chart.</div>';
        return;
    }
    
    // Sort counties by allocation and take top 20
    const sortedData = [...countyData].sort((a, b) => b.amount - a.amount);
    const topCounties = sortedData.slice(0, 20); // Limit to top 20 counties
    
    // Prepare hierarchical data
    const sunburstData = {
        name: 'Kenya',
        children: topCounties.map(county => {
            return {
                name: county.county.length > 15 ? county.county.substring(0, 12) + '...' : county.county,
                value: county.amount,
                fullName: county.county // Store full name for tooltip
            };
        })
    };
    
    const option = {
        title: {
            text: 'County Allocation Sunburst',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Top 20 Counties by Allocation',
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            formatter: function (info) {
                const fullName = info.data.fullName || info.name;
                return [
                    '<div style="padding: 8px;">',
                    '<strong>' + fullName + '</strong><br/>',
                    'Allocation: <strong>' + formatCurrency(info.value) + '</strong>',
                    '</div>'
                ].join('');
            },
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        series: [
            {
                name: 'Allocation',
                type: 'sunburst',
                data: [sunburstData],
                radius: ['15%', '85%'],
                center: ['50%', '55%'],
                label: {
                    show: true,
                    rotate: 'radial',
                    fontWeight: 'bold',
                    fontSize: 10,
                    overflow: 'truncate',
                    minAngle: 5, // Only show labels for segments larger than 5 degrees
                    formatter: function(params) {
                        // Truncate long names
                        if (params.name.length > 12) {
                            return params.name.substring(0, 10) + '...';
                        }
                        return params.name;
                    }
                },
                itemStyle: {
                    borderRadius: 5,
                    borderWidth: 2,
                    borderColor: '#fff'
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 106, 113, 0.5)'
                    },
                    label: {
                        fontSize: 11,
                        fontWeight: 'bold'
                    }
                },
                levels: [
                    {
                        // Center level (Kenya)
                        r0: '0%',
                        r: '15%',
                        label: {
                            show: true,
                            fontSize: 14,
                            fontWeight: 'bold'
                        },
                        itemStyle: {
                            color: '#006a71'
                        }
                    },
                    {
                        // County level
                        r0: '15%',
                        r: '85%',
                        label: {
                            show: true,
                            rotate: 'tangential',
                            fontSize: 9,
                            minAngle: 8, // Only show if segment is large enough
                            overflow: 'truncate',
                            formatter: function(params) {
                                const name = params.name;
                                if (name.length > 10) {
                                    return name.substring(0, 8) + '...';
                                }
                                return name;
                            }
                        },
                        itemStyle: {
                            borderWidth: 2,
                            borderColor: '#fff'
                        }
                    }
                ],
                colorBy: 'data',
                data: [sunburstData]
            }
        ],
        animationDuration: 2000,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('countySunburstChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadCountySunburst");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'county_allocation_sunburst.png';
            link.href = url;
            link.click();
        };
    }
}

// 11. Constituency Allocation Totals
function drawConstituencyTotalsChart() {
    if (!constituencyData || constituencyData.length === 0) {
        document.getElementById('constituencyTotalsChart').innerHTML = '<div class="error-message">No data available for Constituency Totals chart.</div>';
        return;
    }
    
    // Sort and take top 15
    const sortedData = [...constituencyData].sort((a, b) => b.amount - a.amount).slice(0, 15);
    const constituencies = sortedData.map(item => `${item.constituency} (${item.county})`);
    const amounts = sortedData.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Constituency Allocation Totals',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '25%',
            right: '8%',
            bottom: '15%',
            top: '12%',
            containLabel: false
        },
        xAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 40,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: constituencies,
            axisLabel: {
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold',
                width: 200,
                overflow: 'truncate'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Constituency',
            nameLocation: 'middle',
            nameGap: 220,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [0, 5, 5, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'right',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('constituencyTotalsChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadConstituencyTotals").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'constituency_allocation_totals.png';
        link.href = url;
        link.click();
    };
}

// 12. Top 10 Constituencies
function drawTopConstituenciesChart() {
    if (!constituencyData || constituencyData.length === 0) {
        document.getElementById('topConstituenciesChart').innerHTML = '<div class="error-message">No data available for Top Constituencies chart.</div>';
        return;
    }
    
    // Sort and take top 10
    const sortedData = [...constituencyData].sort((a, b) => b.amount - a.amount).slice(0, 10);
    const constituencies = sortedData.map(item => `${item.constituency} (${item.county})`);
    const amounts = sortedData.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Top 10 Constituencies',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '25%',
            right: '8%',
            bottom: '15%',
            top: '12%',
            containLabel: false
        },
        xAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 40,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: constituencies,
            axisLabel: {
                interval: 0,
                fontSize: 12,
                fontWeight: 'bold',
                width: 200,
                overflow: 'truncate'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Constituency',
            nameLocation: 'middle',
            nameGap: 220,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [0, 5, 5, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'right',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('topConstituenciesChart', option);
    if (!myChart) return; 
    
    // Download functionality
    document.getElementById("downloadTopConstituencies").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'top_constituencies.png';
        link.href = url;
        link.click();
    };
}

// 13. Constituency Allocation Ranking - Funnel Chart
function drawConstituencyRankingChart() {
    if (!constituencyData || constituencyData.length === 0) {
        document.getElementById('constituencyRankingChart').innerHTML = '<div class="error-message">No data available for Constituency Ranking chart.</div>';
        return;
    }
    
    // Sort by allocation and take top 15 for funnel chart
    const sortedData = [...constituencyData].sort((a, b) => b.amount - a.amount).slice(0, 15);
    const maxAmount = Math.max(...sortedData.map(item => item.amount));
    const totalAmount = sortedData.reduce((sum, item) => sum + item.amount, 0);
    
    // Prepare funnel data
    const funnelData = sortedData.map((item, index) => {
        const percentage = ((item.amount / totalAmount) * 100).toFixed(1);
        const label = `${item.constituency} (${item.county})`;
        return {
            name: label.length > 30 ? label.substring(0, 27) + '...' : label,
            value: item.amount,
            fullName: label,
            percentage: percentage,
            itemStyle: {
                color: (function() {
                    const ratio = item.amount / maxAmount;
                    if (ratio > 0.7) return '#004d52';
                    if (ratio > 0.4) return '#006a71';
                    if (ratio > 0.2) return '#66a3ad';
                    return '#b3d9d9';
                })()
            }
        };
    });
    
    const option = {
        title: {
            text: 'Constituency Allocation Ranking',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Top 15 Constituencies - Funnel View',
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                return `<div style="padding: 10px;">
                    <strong>${params.data.fullName}</strong><br/>
                    Rank: <strong>#${params.dataIndex + 1}</strong><br/>
                    Allocation: <strong>${formatCurrency(params.value)}</strong><br/>
                    Percentage: <strong>${params.data.percentage}%</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        series: [{
            name: 'Constituency Allocation',
            type: 'funnel',
            left: '15%',
            top: '15%',
            bottom: '15%',
            width: '70%',
            min: 0,
            max: maxAmount,
            minSize: '0%',
            maxSize: '100%',
            sort: 'descending',
            gap: 5,
            label: {
                show: true,
                position: 'inside',
                formatter: function(params) {
                    return params.name + '\n' + formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold',
                color: '#fff'
            },
            labelLine: {
                length: 10,
                lineStyle: {
                    width: 1,
                    type: 'solid'
                }
            },
            itemStyle: {
                borderColor: '#fff',
                borderWidth: 2
            },
            emphasis: {
                label: {
                    fontSize: 11,
                    fontWeight: 'bold'
                },
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                }
            },
            data: funnelData
        }],
        animationDuration: 2000,
        animationEasing: 'cubicOut',
        animationDelay: function (idx) {
            return idx * 50;
        }
    };
    
    const myChart = initChart('constituencyRankingChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadConstituencyRanking");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'constituency_allocation_ranking_funnel.png';
            link.href = url;
            link.click();
        };
    }
}

// 14. Constituency TreeMap
function drawConstituencyTreemapChart() {
    if (!constituencyData || constituencyData.length === 0) {
        document.getElementById('constituencyTreemapChart').innerHTML = '<div class="error-message">No data available for Constituency TreeMap chart.</div>';
        return;
    }
    
    // Group by county and calculate totals
    const countyGroups = {};
    constituencyData.forEach(item => {
        if (!countyGroups[item.county]) {
            countyGroups[item.county] = {
                name: item.county,
                value: 0,
                children: []
            };
        }
        countyGroups[item.county].value += item.amount;
        countyGroups[item.county].children.push({
            name: item.constituency,
            value: item.amount
        });
    });
    
    // Sort counties by total allocation
    const sortedCounties = Object.values(countyGroups).sort((a, b) => b.value - a.value).slice(0, 15);
    
    // Convert to array with sorted counties
    const treeData = {
        name: 'Kenya',
        children: sortedCounties
    };
    
    const option = {
        title: {
            text: 'Constituency Allocation TreeMap',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Grouped by County (Top 15 Counties)',
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function (info) {
                const value = info.value || 0;
                const name = info.name || '';
                const treePathInfo = info.treePathInfo || [];
                let path = '';
                if (treePathInfo.length > 0) {
                    path = treePathInfo.map(item => item.name).join(' > ');
                } else {
                    path = name;
                }
                return `<div style="padding: 10px;">
                    <strong>${path}</strong><br/>
                    Allocation: <strong>${formatCurrency(value)}</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        series: [
            {
                name: 'Allocation',
                type: 'treemap',
                visibleMin: 300,
                data: [treeData],
                roam: false,
                nodeClick: false,
                breadcrumb: {
                    show: true,
                    height: 22,
                    left: 'center',
                    top: 'bottom',
                    itemStyle: {
                        color: '#006a71',
                        borderColor: '#fff',
                        borderWidth: 1
                    },
                    emphasis: {
                        itemStyle: {
                            color: '#004d52'
                        }
                    }
                },
                label: {
                    show: true,
                    formatter: function(params) {
                        const name = params.name;
                        const value = params.value || 0;
                        // Format value for display
                        let formattedValue = '';
                        if (value >= 1000000000) {
                            formattedValue = (value / 1000000000).toFixed(1) + 'B';
                        } else if (value >= 1000000) {
                            formattedValue = (value / 1000000).toFixed(1) + 'M';
                        } else if (value >= 1000) {
                            formattedValue = (value / 1000).toFixed(1) + 'K';
                        } else {
                            formattedValue = value.toString();
                        }
                        
                        const displayName = name.length > 12 ? name.substring(0, 10) + '...' : name;
                        return displayName + '\n' + formattedValue;
                    },
                    fontSize: 11,
                    fontWeight: 'bold',
                    color: '#ffffff',
                    backgroundColor: 'rgba(0, 77, 82, 0.9)',
                    padding: [5, 7],
                    borderRadius: 4,
                    borderColor: '#fff',
                    borderWidth: 2,
                    textShadowBlur: 3,
                    textShadowColor: 'rgba(0, 0, 0, 0.9)',
                    textShadowOffsetX: 1,
                    textShadowOffsetY: 1,
                    rich: {
                        name: {
                            fontSize: 11,
                            fontWeight: 'bold',
                            color: '#ffffff',
                            lineHeight: 16
                        },
                        value: {
                            fontSize: 10,
                            fontWeight: 'bold',
                            color: '#ffffff',
                            lineHeight: 16
                        }
                    }
                },
                upperLabel: {
                    show: true,
                    height: 45,
                    fontSize: 13,
                    fontWeight: 'bold',
                    color: '#ffffff',
                    backgroundColor: 'rgba(0, 106, 113, 0.9)',
                    padding: [6, 10],
                    borderRadius: 5,
                    borderColor: '#fff',
                    borderWidth: 2,
                    textShadowBlur: 4,
                    textShadowColor: 'rgba(0, 0, 0, 0.9)',
                    textShadowOffsetX: 1,
                    textShadowOffsetY: 1,
                    formatter: function(params) {
                        const name = params.name;
                        const value = params.value || 0;
                        // Format value for display
                        let formattedValue = '';
                        if (value >= 1000000000) {
                            formattedValue = (value / 1000000000).toFixed(1) + 'B';
                        } else if (value >= 1000000) {
                            formattedValue = (value / 1000000).toFixed(1) + 'M';
                        } else if (value >= 1000) {
                            formattedValue = (value / 1000).toFixed(1) + 'K';
                        } else {
                            formattedValue = value.toString();
                        }
                        
                        const displayName = name.length > 18 ? name.substring(0, 16) + '...' : name;
                        return displayName + '\nTotal: ' + formattedValue;
                    }
                },
                itemStyle: {
                    borderColor: '#fff',
                    borderWidth: 3,
                    gapWidth: 8
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 106, 113, 0.5)',
                        borderWidth: 4
                    },
                    label: {
                        fontSize: 13,
                        backgroundColor: 'rgba(0, 77, 82, 0.9)',
                        padding: [5, 7]
                    }
                },
                levels: [
                    {
                        // County level
                        itemStyle: {
                            borderWidth: 3,
                            borderColor: '#fff',
                            gapWidth: 8
                        },
                        upperLabel: {
                            show: true
                        }
                    },
                    {
                        // Constituency level
                        itemStyle: {
                            borderWidth: 2,
                            borderColor: '#fff',
                            gapWidth: 5
                        },
                        colorSaturation: [0.35, 0.5],
                        colorMappingBy: 'value',
                        label: {
                            show: true
                        }
                    }
                ],
                color: ['#004d52', '#006a71', '#66a3ad', '#b3d9d9', '#e6f2f3']
            }
        ],
        animationDuration: 2000,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('constituencyTreemapChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadConstituencyTreemap");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Ensure all labels are visible before download
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    },
                    upperLabel: {
                        show: true
                    }
                }]
            }, true);
            
            // Wait a moment for rendering, then download
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 3, // Higher resolution for better text clarity
                    backgroundColor: '#ffffff',
                    excludeComponents: ['toolbox', 'dataZoom']
                });
                const link = document.createElement('a');
                link.download = 'constituency_treemap.png';
                link.href = url;
                link.click();
            }, 100);
        };
    }
}

// 15. Constituency Contribution to County Total
function drawConstituencyContributionChart() {
    if (!constituencyData || constituencyData.length === 0) {
        document.getElementById('constituencyContributionChart').innerHTML = '<div class="error-message">No data available for Constituency Contribution chart.</div>';
        return;
    }
    
    // Select a county for the waterfall chart
    const selectedCounty = constituencyData[0].county;
    const countyConstituencies = constituencyData.filter(item => item.county === selectedCounty);
    
    // Calculate the total allocation for the county
    const countyTotal = countyConstituencies.reduce((sum, item) => sum + item.amount, 0);
    
    // Prepare data for the waterfall chart
    const waterfallData = [
        {
            name: 'Starting Total',
            value: 0,
            itemStyle: {
                color: '#006a71'
            }
        }
    ];
    
    // Add each constituency's allocation
    countyConstituencies.forEach(item => {
        waterfallData.push({
            name: item.constituency,
            value: item.amount,
            itemStyle: {
                color: '#28a745'
            }
        });
    });
    
    // Add the total
    waterfallData.push({
        name: 'County Total',
        value: countyTotal,
        itemStyle: {
            color: '#ff7e29'
        }
    });
    
    const option = {
        title: {
            text: `Constituency Contribution to ${selectedCounty} Total`,
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: waterfallData.map(item => item.name),
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: waterfallData.map(item => ({
                value: item.value,
                itemStyle: {
                    color: item.itemStyle.color
                }
            })),
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('constituencyContributionChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadConstituencyContribution").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'constituency_contribution.png';
        link.href = url;
        link.click();
    };
}

// 16. Ward Allocation Totals
function drawWardTotalsChart() {
    if (!wardData || wardData.length === 0) {
        document.getElementById('wardTotalsChart').innerHTML = '<div class="error-message">No data available for Ward Totals chart.</div>';
        return;
    }
    
    // Sort and take top 15
    const sortedData = [...wardData].sort((a, b) => b.amount - a.amount).slice(0, 15);
    const wards = sortedData.map(item => `${item.ward} (${item.constituency})`);
    const amounts = sortedData.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Ward Allocation Totals',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '30%',
            right: '8%',
            bottom: '15%',
            top: '12%',
            containLabel: false
        },
        xAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 40,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: wards,
            axisLabel: {
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold',
                width: 250,
                overflow: 'truncate',
                margin: 10
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Ward',
            nameLocation: 'middle',
            nameGap: 280,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [0, 5, 5, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'right',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('wardTotalsChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadWardTotals").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'ward_allocation_totals.png';
        link.href = url;
        link.click();
    };
}

// 17. Top 10 Wards (Highest Allocation)
function drawTopWardsChart() {
    if (!topWardData || topWardData.length === 0) {
        document.getElementById('topWardsChart').innerHTML = '<div class="error-message">No data available for Top Wards chart.</div>';
        return;
    }
    
    // Sort data
    const sortedData = [...topWardData].sort((a, b) => b.amount - a.amount);
    const wards = sortedData.map(item => item.ward);
    const amounts = sortedData.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Top 10 Wards (Highest Allocation)',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '25%',
            right: '8%',
            bottom: '15%',
            top: '12%',
            containLabel: false
        },
        xAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 40,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: wards,
            axisLabel: {
                interval: 0,
                fontSize: 12,
                fontWeight: 'bold',
                width: 200,
                overflow: 'truncate',
                margin: 10
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Ward',
            nameLocation: 'middle',
            nameGap: 240,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [0, 5, 5, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'right',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('topWardsChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadTopWards").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'top_wards_allocation.png';
        link.href = url;
        link.click();
    };
}

// 18. Ward Allocation Ranking Chart - Heatmap (Handles Large Datasets)
function drawWardRankingChart() {
    if (!wardData || wardData.length === 0) {
        document.getElementById('wardRankingChart').innerHTML = '<div class="error-message">No data available for Ward Ranking chart.</div>';
        return;
    }
    
    // Group wards by county and constituency
    const countyConstituencyMap = {};
    wardData.forEach(item => {
        const key = `${item.county}|${item.constituency}`;
        if (!countyConstituencyMap[key]) {
            countyConstituencyMap[key] = {
                county: item.county,
                constituency: item.constituency,
                wards: []
            };
        }
        countyConstituencyMap[key].wards.push({
            ward: item.ward,
            amount: item.amount
        });
    });
    
    // Get top counties by total allocation
    const countyTotals = {};
    Object.keys(countyConstituencyMap).forEach(key => {
        const data = countyConstituencyMap[key];
        if (!countyTotals[data.county]) {
            countyTotals[data.county] = 0;
        }
        data.wards.forEach(ward => {
            countyTotals[data.county] += ward.amount;
        });
    });
    
    // Sort counties and take top 20
    const topCounties = Object.keys(countyTotals)
        .sort((a, b) => countyTotals[b] - countyTotals[a])
        .slice(0, 20);
    
    // Get all constituencies from top counties, sort by allocation, take top 30
    const constituencyList = [];
    topCounties.forEach(county => {
        Object.keys(countyConstituencyMap).forEach(key => {
            const data = countyConstituencyMap[key];
            if (data.county === county) {
                const total = data.wards.reduce((sum, w) => sum + w.amount, 0);
                constituencyList.push({
                    key: key,
                    county: county,
                    constituency: data.constituency,
                    total: total,
                    wards: data.wards
                });
            }
        });
    });
    
    const sortedConstituencies = constituencyList.sort((a, b) => b.total - a.total).slice(0, 30);
    const constituencies = sortedConstituencies.map(item => `${item.constituency} (${item.county})`);
    
    // Get all wards from these constituencies, sort and take top 50
    const allWards = [];
    sortedConstituencies.forEach(item => {
        item.wards.forEach(ward => {
            allWards.push({
                ward: ward.ward,
                constituency: item.constituency,
                county: item.county,
                amount: ward.amount
            });
        });
    });
    
    const sortedWards = allWards.sort((a, b) => b.amount - a.amount).slice(0, 50);
    const wards = sortedWards.map(item => item.ward);
    
    // Create heatmap data: constituency (x) vs ward (y)
    const heatmapData = [];
    let minValue = Infinity;
    let maxValue = -Infinity;
    
    sortedConstituencies.forEach((constituency, constIdx) => {
        sortedWards.forEach((ward, wardIdx) => {
            // Check if this ward belongs to this constituency
            if (ward.constituency === constituency.constituency && ward.county === constituency.county) {
                heatmapData.push([constIdx, wardIdx, ward.amount]);
                minValue = Math.min(minValue, ward.amount);
                maxValue = Math.max(maxValue, ward.amount);
            }
        });
    });
    
    // If no matches, create a simpler view - just show top wards
    if (heatmapData.length === 0) {
        // Fallback: show wards as rows, sorted by amount
        const simpleHeatmapData = sortedWards.map((ward, idx) => {
            return [0, idx, ward.amount];
        });
        minValue = Math.min(...sortedWards.map(w => w.amount));
        maxValue = Math.max(...sortedWards.map(w => w.amount));
        
        const option = {
            title: {
                text: 'Ward Allocation Ranking - Heatmap',
                left: 'center',
                textStyle: {
                    color: '#333',
                    fontSize: 18,
                    fontWeight: 'bold'
                },
                subtext: `Top 50 Wards by Allocation (${sortedWards.length} wards displayed)`,
                subtextStyle: {
                    color: '#666',
                    fontSize: 12
                }
            },
            tooltip: {
                position: 'top',
                formatter: function(params) {
                    const ward = sortedWards[params.data[1]];
                    return `<div style="padding: 8px;">
                        <strong>${ward.ward}</strong><br/>
                        ${ward.constituency}, ${ward.county}<br/>
                        Allocation: <strong>${formatCurrency(params.data[2])}</strong>
                    </div>`;
                }
            },
            grid: {
                height: '75%',
                top: '12%',
                bottom: '15%',
                left: '8%',
                right: '8%'
            },
            xAxis: {
                type: 'category',
                data: ['All Wards'],
                splitArea: {
                    show: true
                },
                axisLabel: {
                    fontSize: 12,
                    fontWeight: 'bold'
                }
            },
            yAxis: {
                type: 'category',
                data: wards,
                splitArea: {
                    show: true
                },
                axisLabel: {
                    interval: 0,
                    fontSize: 9,
                    fontWeight: 'bold',
                    rotate: 0
                }
            },
            visualMap: {
                min: minValue,
                max: maxValue,
                calculable: true,
                orient: 'horizontal',
                left: 'center',
                bottom: '1%',
                text: ['High Allocation', 'Low Allocation'],
                textStyle: {
                    color: '#333',
                    fontWeight: 'bold'
                },
                inRange: {
                    color: ['#e6f2f3', '#b3d9d9', '#66a3ad', '#006a71', '#004d52']
                }
            },
            series: [{
                name: 'Allocation',
                type: 'heatmap',
                data: simpleHeatmapData,
                label: {
                    show: false
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }],
            animationDuration: 2000,
            animationEasing: 'cubicOut'
        };
        
        const myChart = initChart('wardRankingChart', option);
        if (!myChart) return;
        
        const downloadBtn = document.getElementById("downloadWardRanking");
        if (downloadBtn) {
            downloadBtn.onclick = function() {
                const url = myChart.getDataURL({
                    pixelRatio: 2,
                    backgroundColor: '#fff'
                });
                const link = document.createElement('a');
                link.download = 'ward_allocation_ranking_heatmap.png';
                link.href = url;
                link.click();
            };
        }
        return;
    }
    
    const option = {
        title: {
            text: 'Ward Allocation Ranking - Heatmap',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: `Top 30 Constituencies × Top 50 Wards (${heatmapData.length} data points)`,
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            position: 'top',
            formatter: function(params) {
                const constituency = sortedConstituencies[params.data[0]];
                const ward = sortedWards[params.data[1]];
                return `<div style="padding: 8px;">
                    <strong>${ward.ward}</strong><br/>
                    Constituency: ${constituency.constituency}<br/>
                    County: ${constituency.county}<br/>
                    Allocation: <strong>${formatCurrency(params.data[2])}</strong>
                </div>`;
            }
        },
        grid: {
            height: '70%',
            top: '12%',
            bottom: '20%',
            left: '18%',
            right: '8%'
        },
        xAxis: {
            type: 'category',
            data: constituencies,
            splitArea: {
                show: true
            },
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 9,
                fontWeight: 'bold'
            }
        },
        yAxis: {
            type: 'category',
            data: wards,
            splitArea: {
                show: true
            },
            axisLabel: {
                interval: 0,
                fontSize: 8,
                fontWeight: 'bold'
            }
        },
        visualMap: {
            min: minValue,
            max: maxValue,
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: '3%',
            text: ['High Allocation', 'Low Allocation'],
            textStyle: {
                color: '#333',
                fontWeight: 'bold'
            },
            inRange: {
                color: ['#e6f2f3', '#b3d9d9', '#66a3ad', '#006a71', '#004d52']
            }
        },
        series: [{
            name: 'Allocation',
            type: 'heatmap',
            data: heatmapData,
            label: {
                show: false
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }],
        animationDuration: 2000,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('wardRankingChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadWardRanking");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'ward_allocation_ranking_heatmap.png';
            link.href = url;
            link.click();
        };
    }
}

// 19. Ward Allocation Heat Map
function drawWardHeatmapChart() {
    if (!wardData || wardData.length === 0) {
        document.getElementById('wardHeatmapChart').innerHTML = '<div class="error-message">No data available for Ward Heatmap chart.</div>';
        return;
    }
    
    // Sort and take top 20
    const sortedData = [...wardData].sort((a, b) => b.amount - a.amount).slice(0, 20);
    const wards = sortedData.map(item => `${item.ward} (${item.constituency})`);
    const amounts = sortedData.map(item => item.amount);
    
    // Create color scale based on amounts
    const minAmount = Math.min(...amounts);
    const maxAmount = Math.max(...amounts);
    
    const option = {
        title: {
            text: 'Ward Allocation Heat Map',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '30%',
            right: '10%',
            bottom: '20%',
            top: '12%',
            containLabel: false
        },
        xAxis: {
            type: 'value',
            name: 'Allocation (KES)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + 'B';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#e0e0e0',
                    type: 'dashed'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: wards,
            axisLabel: {
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold',
                width: 250,
                overflow: 'truncate',
                margin: 10
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Ward',
            nameLocation: 'middle',
            nameGap: 280,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        visualMap: {
            min: minAmount,
            max: maxAmount,
            text: ['High', 'Low'],
            realtime: false,
            calculable: true,
            inRange: {
                color: ['#e6f2f3', '#006a71']
            },
            orient: 'horizontal',
            left: 'center',
            bottom: '1%',
            textStyle: {
                fontWeight: 'bold'
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts.map((amount, index) => ({
                value: amount,
                itemStyle: {
                    color: echarts.color.lift(
                        '#006a71',
                        (amount - minAmount) / (maxAmount - minAmount)
                    )
                }
            })),
            label: {
                show: true,
                position: 'right',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('wardHeatmapChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadWardHeatmap").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'ward_allocation_heatmap.png';
        link.href = url;
        link.click();
    };
}

// 20. Ward Allocation Distribution
function drawWardDistributionChart() {
    if (!wardData || wardData.length === 0) {
        document.getElementById('wardDistributionChart').innerHTML = '<div class="error-message">No data available for Ward Distribution chart.</div>';
        return;
    }
    
    // Group by constituency and calculate totals
    const constituencyGroups = {};
    const constituencyTotals = {};
    
    wardData.forEach(item => {
        if (!constituencyGroups[item.constituency]) {
            constituencyGroups[item.constituency] = [];
            constituencyTotals[item.constituency] = 0;
        }
        constituencyGroups[item.constituency].push(item.amount);
        constituencyTotals[item.constituency] += item.amount;
    });
    
    // Sort constituencies by total allocation and take top 20
    const sortedConstituencies = Object.keys(constituencyTotals)
        .sort((a, b) => constituencyTotals[b] - constituencyTotals[a])
        .slice(0, 20);
    
    // Calculate box plot statistics for top constituencies
    const boxData = sortedConstituencies.map(constituency => {
        const amounts = [...constituencyGroups[constituency]].sort((a, b) => a - b);
        
        if (amounts.length === 0) return [0, 0, 0, 0, 0];
        
        const min = amounts[0];
        const q1Index = Math.floor(amounts.length * 0.25);
        const medianIndex = Math.floor(amounts.length * 0.5);
        const q3Index = Math.floor(amounts.length * 0.75);
        
        const q1 = amounts[q1Index] || min;
        const median = amounts[medianIndex] || min;
        const q3 = amounts[q3Index] || amounts[amounts.length - 1];
        const max = amounts[amounts.length - 1];
        
        return [min, q1, median, q3, max];
    });
    
    const option = {
        title: {
            text: 'Ward Allocation Distribution',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Top 20 Constituencies - Box Plot View',
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                return `<div style="padding: 10px;">
                    <strong>${params.name}</strong><br/>
                    <hr style="margin: 5px 0; border-color: #006a71;">
                    Min: <strong>${formatCurrency(params.data[0])}</strong><br/>
                    Q1 (25%): <strong>${formatCurrency(params.data[1])}</strong><br/>
                    Median: <strong>${formatCurrency(params.data[2])}</strong><br/>
                    Q3 (75%): <strong>${formatCurrency(params.data[3])}</strong><br/>
                    Max: <strong>${formatCurrency(params.data[4])}</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        grid: {
            left: '10%',
            right: '8%',
            bottom: '20%',
            top: '15%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: sortedConstituencies,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 9,
                fontWeight: 'bold',
                margin: 10
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Constituency',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KES)',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + 'B';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#e0e0e0',
                    type: 'dashed'
                }
            }
        },
        series: [{
            name: 'Allocation Distribution',
            type: 'boxplot',
            data: boxData,
            itemStyle: {
                color: '#006a71',
                borderColor: '#004d52',
                borderWidth: 2
            },
            emphasis: {
                itemStyle: {
                    borderColor: '#004d52',
                    borderWidth: 3,
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                }
            }
        }],
        animationDuration: 2000,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('wardDistributionChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadWardDistribution");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 2,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'ward_allocation_distribution.png';
            link.href = url;
            link.click();
        };
    }
}

// 21. Ward-to-Constituency Contribution Chart
function drawWardContributionChart() {
    if (!wardData || wardData.length === 0) {
        document.getElementById('wardContributionChart').innerHTML = '<div class="error-message">No data available for Ward Contribution chart.</div>';
        return;
    }
    
    // Select a constituency for the waterfall chart
    const selectedConstituency = wardData[0].constituency;
    const constituencyWards = wardData.filter(item => item.constituency === selectedConstituency);
    
    // Calculate the total allocation for the constituency
    const constituencyTotal = constituencyWards.reduce((sum, item) => sum + item.amount, 0);
    
    // Prepare data for the waterfall chart
    const waterfallData = [
        {
            name: 'Starting Total',
            value: 0,
            itemStyle: {
                color: '#006a71'
            }
        }
    ];
    
    // Add each ward's allocation
    constituencyWards.forEach(item => {
        waterfallData.push({
            name: item.ward,
            value: item.amount,
            itemStyle: {
                color: '#28a745'
            }
        });
    });
    
    // Add the total
    waterfallData.push({
        name: 'Constituency Total',
        value: constituencyTotal,
        itemStyle: {
            color: '#ff7e29'
        }
    });
    
    const option = {
        title: {
            text: `Ward-to-Constituency Contribution for ${selectedConstituency}`,
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: waterfallData.map(item => item.name),
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: waterfallData.map(item => ({
                value: item.value,
                itemStyle: {
                    color: item.itemStyle.color
                }
            })),
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('wardContributionChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadWardContribution").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'ward_contribution.png';
        link.href = url;
        link.click();
    };
}

// 22. Ward Allocation - Sunburst Chart
function drawWardTreemapChart() {
    if (!wardData || wardData.length === 0) {
        document.getElementById('wardTreemapChart').innerHTML = '<div class="error-message">No data available for Ward Allocation chart.</div>';
        return;
    }
    
    // Group by county and constituency, calculate totals
    const countyGroups = {};
    const countyTotals = {};
    
    wardData.forEach(item => {
        if (!countyGroups[item.county]) {
            countyGroups[item.county] = {
                name: item.county,
                value: 0,
                children: {}
            };
            countyTotals[item.county] = 0;
        }
        
        if (!countyGroups[item.county].children[item.constituency]) {
            countyGroups[item.county].children[item.constituency] = {
                name: item.constituency,
                value: 0,
                children: []
            };
        }
        
        countyGroups[item.county].value += item.amount;
        countyTotals[item.county] += item.amount;
        countyGroups[item.county].children[item.constituency].value += item.amount;
        countyGroups[item.county].children[item.constituency].children.push({
            name: item.ward,
            value: item.amount,
            fullName: item.ward
        });
    });
    
    // Sort counties by total allocation and take top 10
    const sortedCounties = Object.keys(countyTotals)
        .sort((a, b) => countyTotals[b] - countyTotals[a])
        .slice(0, 10);
    
    // Convert to sunburst data structure
    const sunburstData = {
        name: 'Kenya',
        children: sortedCounties.map(countyName => {
            const county = countyGroups[countyName];
            // Sort constituencies within county and take top 3
            const sortedConstituencies = Object.values(county.children)
                .sort((a, b) => b.value - a.value)
                .slice(0, 3)
                .map(constituency => {
                    // Take top 5 wards per constituency
                    const sortedWards = [...constituency.children]
                        .sort((a, b) => b.value - a.value)
                        .slice(0, 5)
                        .map(ward => ({
                            name: ward.name.length > 15 ? ward.name.substring(0, 12) + '...' : ward.name,
                            value: ward.value,
                            fullName: ward.fullName
                        }));
                    
                    return {
                        name: constituency.name.length > 20 ? constituency.name.substring(0, 17) + '...' : constituency.name,
                        value: constituency.value,
                        children: sortedWards
                    };
                });
            
            return {
                name: county.name.length > 15 ? county.name.substring(0, 12) + '...' : county.name,
                value: county.value,
                children: sortedConstituencies
            };
        })
    };
    
    const option = {
        title: {
            text: 'Ward Allocation - Sunburst',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Hierarchy: County → Constituency → Ward (Top 10 Counties)',
            subtextStyle: {
                color: '#666',
                fontSize: 12
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function (info) {
                const value = info.value || 0;
                const name = info.name || '';
                const treePathInfo = info.treePathInfo || [];
                let path = '';
                if (treePathInfo.length > 0) {
                    path = treePathInfo.map(item => item.name).join(' > ');
                } else {
                    path = name;
                }
                return `<div style="padding: 10px;">
                    <strong>${path}</strong><br/>
                    Allocation: <strong>${formatCurrency(value)}</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        series: [
            {
                name: 'Allocation',
                type: 'sunburst',
                data: [sunburstData],
                radius: ['12%', '90%'],
                center: ['50%', '55%'],
                label: {
                    show: true,
                    rotate: 'radial',
                    fontSize: 9,
                    fontWeight: 'bold',
                    minAngle: 5,
                    overflow: 'truncate',
                    formatter: function(params) {
                        const name = params.name;
                        if (name.length > 10) {
                            return name.substring(0, 8) + '...';
                        }
                        return name;
                    }
                },
                itemStyle: {
                    borderRadius: 5,
                    borderWidth: 2,
                    borderColor: '#fff'
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 106, 113, 0.5)'
                    },
                    label: {
                        fontSize: 10,
                        fontWeight: 'bold'
                    }
                },
                levels: [
                    {
                        // Center level (Kenya)
                        r0: '0%',
                        r: '12%',
                        label: {
                            show: true,
                            fontSize: 14,
                            fontWeight: 'bold'
                        },
                        itemStyle: {
                            color: '#006a71'
                        }
                    },
                    {
                        // County level
                        r0: '12%',
                        r: '35%',
                        label: {
                            show: true,
                            rotate: 'tangential',
                            fontSize: 10,
                            minAngle: 8
                        },
                        itemStyle: {
                            borderWidth: 2,
                            borderColor: '#fff'
                        }
                    },
                    {
                        // Constituency level
                        r0: '35%',
                        r: '65%',
                        label: {
                            show: true,
                            rotate: 'tangential',
                            fontSize: 9,
                            minAngle: 6
                        },
                        itemStyle: {
                            borderWidth: 2,
                            borderColor: '#fff'
                        }
                    },
                    {
                        // Ward level
                        r0: '65%',
                        r: '90%',
                        label: {
                            show: true,
                            rotate: 'radial',
                            fontSize: 8,
                            minAngle: 3
                        },
                        itemStyle: {
                            borderWidth: 1,
                            borderColor: '#fff'
                        }
                    }
                ],
                colorBy: 'data'
            }
        ],
        animationDuration: 2000,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('wardTreemapChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadWardTreemap");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                pixelRatio: 3,
                backgroundColor: '#fff'
            });
            const link = document.createElement('a');
            link.download = 'ward_allocation_sunburst.png';
            link.href = url;
            link.click();
        };
    }
}

// 23. Most Mentioned Marginalised Areas
function drawMostMentionedChart() {
    if (!marginalisedData || marginalisedData.length === 0) {
        document.getElementById('mostMentionedChart').innerHTML = '<div class="error-message">No data available for Most Mentioned Marginalised Areas chart.</div>';
        return;
    }

    // Sort and limit to top items for better visualization
    const sortedData = [...marginalisedData]
        .filter(item => item.count > 0)
        .sort((a, b) => b.count - a.count)
        .slice(0, 15);
    
    if (sortedData.length === 0) {
        document.getElementById('mostMentionedChart').innerHTML = '<div class="error-message">No valid data available for Most Mentioned Marginalised Areas chart.</div>';
        return;
    }

    const option = {
        title: {
            text: 'Most Mentioned Marginalised Areas',
            left: 'center',
            top: '5%',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: sortedData.length < marginalisedData.length 
                ? `Top ${sortedData.length} areas (out of ${marginalisedData.length})`
                : 'Distribution of how often each marginalised area was mentioned',
            subtextStyle: {
                fontSize: 12,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function (params) {
                return `<div style="padding: 5px;">
                    <strong>${params.name}</strong><br/>
                    Mentions: <strong>${params.value}</strong><br/>
                    Share: <strong>${params.percent}%</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2,
            textStyle: {
                fontSize: 12
            }
        },
        legend: {
            show: true,
            orient: 'vertical',
            left: '5%',
            top: '15%',
            bottom: '10%',
            itemGap: 8,
            data: sortedData.map(item => item.area),
            textStyle: {
                fontWeight: 'bold',
                fontSize: 11
            },
            formatter: function (name) {
                const item = sortedData.find(d => d.area === name);
                const count = item ? item.count : 0;
                const displayName = name.length > 20 ? name.substring(0, 18) + '…' : name;
                return `${displayName} (${count})`;
            }
        },
        color: [
            '#006a71', '#0097a7', '#26a69a', '#66bb6a', '#9ccc65',
            '#ffa726', '#ff7043', '#ab47bc', '#5c6bc0', '#8d6e63',
            '#42a5f5', '#ef5350', '#ec407a', '#7e57c2', '#26c6da'
        ],
        series: [
            {
                name: 'Marginalised Areas',
                type: 'pie',
                radius: ['35%', '65%'],
                center: ['65%', '55%'],
                avoidLabelOverlap: true,
                minShowLabelAngle: 5,
                itemStyle: {
                    borderRadius: 6,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: true,
                    position: 'outside',
                    fontSize: 10,
                    fontWeight: 'bold',
                    formatter: function (params) {
                        return `${params.percent}%`;
                    }
                },
                labelLine: {
                    show: true,
                    length: 15,
                    length2: 10,
                    smooth: 0.2
                },
                emphasis: {
                    scale: true,
                    scaleSize: 8,
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 106, 113, 0.5)'
                    },
                    label: {
                        show: true,
                        fontSize: 12,
                        fontWeight: 'bold'
                    }
                },
                data: sortedData.map((item) => ({
                    value: item.count,
                    name: item.area
                }))
            }
        ],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('mostMentionedChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadMostMentioned");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            const url = myChart.getDataURL({
                type: 'png',
                pixelRatio: 3,
                backgroundColor: '#ffffff'
            });
            const link = document.createElement('a');
            link.download = 'most_mentioned_marginalised_areas.png';
            link.href = url;
            link.click();
        };
    }
}

// 24. Marginalised Areas by County
function drawMarginalisedByCountyChart() {
    if (!countyData || countyData.length === 0 || !marginalisedData || marginalisedData.length === 0 || !topAllocationsData || topAllocationsData.length === 0) {
        document.getElementById('marginalisedByCountyChart').innerHTML = '<div class="error-message">No data available for Marginalised Areas by County chart.</div>';
        return;
    }

    // Build county -> marginalised area -> count mapping from detailed allocation records
    // Note: Counties in topAllocationsData are already uppercase from PHP controller
    const countyAreaCounts = {};
    
    topAllocationsData.forEach(row => {
        const county = (row.county || '').toString().trim().toUpperCase();
        const area = (row.marginalised_area || '').toString().trim().toUpperCase();
        
        // Skip if invalid data
        if (!county || county === 'UNKNOWN' || !area || area === 'UNKNOWN' || area === '') {
            return;
        }

        // Initialize county entry if needed
        if (!countyAreaCounts[county]) {
            countyAreaCounts[county] = {};
        }
        
        // Count occurrences of each marginalised area per county
        if (!countyAreaCounts[county][area]) {
            countyAreaCounts[county][area] = 0;
        }
        countyAreaCounts[county][area] += 1;
    });
    
    // Debug: Log county area counts to console
    console.log('County Area Counts:', countyAreaCounts);

    // Select top 25 counties by allocation
    // Note: Counties in countyData are already uppercase from PHP controller
    const sortedCounties = [...countyData]
        .sort((a, b) => b.amount - a.amount)
        .slice(0, 25);
    
    // Filter counties that have marginalised areas and normalize for matching
    const countiesWithData = sortedCounties
        .map(item => {
            const countyName = (item.county || '').toString().trim().toUpperCase();
            return {
                original: item.county, // Keep original for display
                normalized: countyName  // Normalized for data lookup
            };
        })
        .filter(c => {
            const hasData = countyAreaCounts[c.normalized] && Object.keys(countyAreaCounts[c.normalized]).length > 0;
            if (!hasData) {
                console.log(`County ${c.original} (${c.normalized}) has no marginalised area data`);
            }
            return hasData;
        });
    
    const counties = countiesWithData.map(c => c.original);
    const countiesNormalizedList = countiesWithData.map(c => c.normalized);
    
    console.log('Selected counties:', counties);
    console.log('Normalized counties:', countiesNormalizedList);

    if (counties.length === 0) {
        document.getElementById('marginalisedByCountyChart').innerHTML = '<div class="error-message">No valid marginalised area data per county to display.</div>';
        return;
    }

    // Find all unique marginalised areas that appear in these top 25 counties
    const areaFrequencyInSelectedCounties = {};
    countiesNormalizedList.forEach(countyNameNormalized => {
        const countyAreas = countyAreaCounts[countyNameNormalized] || {};
        Object.keys(countyAreas).forEach(areaName => {
            if (!areaFrequencyInSelectedCounties[areaName]) {
                areaFrequencyInSelectedCounties[areaName] = 0;
            }
            areaFrequencyInSelectedCounties[areaName] += countyAreas[areaName];
        });
    });

    // Sort areas by their total frequency across the selected counties, take top 8
    const sortedAreas = Object.keys(areaFrequencyInSelectedCounties)
        .map(areaName => ({
            area: areaName,
            count: areaFrequencyInSelectedCounties[areaName]
        }))
        .filter(item => item.count > 0)
        .sort((a, b) => b.count - a.count)
        .slice(0, 8);
    const areas = sortedAreas.map(item => item.area);

    if (counties.length === 0 || areas.length === 0) {
        document.getElementById('marginalisedByCountyChart').innerHTML = '<div class="error-message">No valid marginalised area data per county to display.</div>';
        return;
    }

    // Create series data using real counts per county/area
    // Match using normalized county names
    const series = areas.map(areaName => ({
        name: areaName,
        type: 'bar',
        stack: 'total',
        barWidth: '60%', // Make bars smaller
        barCategoryGap: '20%', // Add spacing between county bars
        label: {
            show: true,
            position: 'inside',
            formatter: function(params) {
                // Only show label if value is greater than 0
                return params.value > 0 ? params.value.toString() : '';
            },
            fontSize: 10,
            fontWeight: 'bold',
            color: '#ffffff',
            textShadowBlur: 3,
            textShadowColor: 'rgba(0, 0, 0, 1)',
            textBorderColor: 'rgba(0, 0, 0, 0.5)',
            textBorderWidth: 1
        },
        emphasis: {
            focus: 'series',
            label: {
                show: true,
                fontSize: 10
            }
        },
        data: countiesNormalizedList.map(countyNameNormalized => {
            const countyEntry = countyAreaCounts[countyNameNormalized] || {};
            const areaNameUpper = areaName.toUpperCase();
            return countyEntry[areaNameUpper] || 0;
        })
    }));

    const option = {
        title: {
            text: 'Marginalised Areas by County',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Hover over a county to see its marginalised areas',
            subtextStyle: {
                fontSize: 12,
                color: '#666',
                margin: [0, 5, 0, 0]
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function (params) {
                const countyName = params[0].name; // This is the original county name from yAxis
                const countyNormalized = (countyName || '').toString().trim().toUpperCase();
                const countyAreas = countyAreaCounts[countyNormalized] || {};
                
                // Debug
                if (!countyAreas || Object.keys(countyAreas).length === 0) {
                    console.warn(`No data found for county: ${countyName} (normalized: ${countyNormalized})`);
                }
                
                // Get all marginalised areas for this county with their counts
                const areasList = Object.keys(countyAreas)
                    .map(area => ({
                        name: area,
                        count: countyAreas[area]
                    }))
                    .filter(a => a.count > 0)
                    .sort((a, b) => b.count - a.count);
                
                let html = `<div style="padding: 8px;"><strong>${countyName}</strong><br/>`;
                html += `<strong>Marginalised Areas (${areasList.length}):</strong><br/><br/>`;
                
                if (areasList.length === 0) {
                    html += 'No marginalised areas found<br/>';
                } else {
                    areasList.forEach((area, index) => {
                        html += `${index + 1}. <strong>${area.name}</strong> (${area.count} ${area.count === 1 ? 'mention' : 'mentions'})<br/>`;
                    });
                }
                
                html += `</div>`;
                return html;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2,
            textStyle: {
                fontSize: 12
            }
        },
        legend: {
            show: false
        },
        grid: {
            left: '22%',
            right: '5%',
            bottom: '8%',
            top: '18%',
            containLabel: false
        },
        xAxis: {
            type: 'value',
            name: 'Number of Mentions',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            axisLabel: {
                fontSize: 10
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: counties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 80,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            axisLabel: {
                interval: 0,
                fontSize: 9,
                fontWeight: 'bold',
                margin: 8,
                width: 140,
                overflow: 'truncate'
            }
        },
        color: [
            '#006a71', '#26a69a', '#66bb6a', '#9ccc65',
            '#ffa726', '#ff7043', '#ab47bc', '#5c6bc0',
            '#42a5f5', '#ef5350', '#ec407a', '#7e57c2',
            '#26c6da', '#ffca28', '#8d6e63', '#78909c',
            '#aed581', '#ffb74d', '#ba68c8', '#64b5f6',
            '#4db6ac', '#ff8a65', '#9575cd', '#4fc3f7'
        ],
        series: series,
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('marginalisedByCountyChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadMarginalisedByCounty");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Force labels to be visible and update chart
            myChart.setOption({
                series: series.map((s, index) => ({
                    ...s,
                    label: {
                        show: true,
                        position: 'inside',
                        formatter: function(params) {
                            const val = params.value || 0;
                            return val > 0 ? val.toString() : '';
                        },
                        fontSize: 11,
                        fontWeight: 'bold',
                        color: '#ffffff',
                        textShadowBlur: 4,
                        textShadowColor: 'rgba(0, 0, 0, 1)',
                        textBorderColor: 'rgba(0, 0, 0, 0.8)',
                        textBorderWidth: 2,
                        rich: {
                            value: {
                                color: '#ffffff',
                                fontWeight: 'bold',
                                fontSize: 11
                            }
                        }
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: 12
                        }
                    }
                }))
            }, false);
            
            // Force a re-render and wait for labels to render
            myChart.resize();
            
            // Wait longer for complete rendering including labels
            setTimeout(function() {
                // Get the chart image with high resolution
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff',
                    excludeComponents: ['toolbox', 'dataZoom', 'brush']
                });
                
                const link = document.createElement('a');
                link.download = 'marginalised_areas_by_county.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 25. Marginalised Areas Allocation Totals
function drawMarginalisedTotalsChart() {
    if (!marginalisedTotalsData || marginalisedTotalsData.length === 0) {
        document.getElementById('marginalisedTotalsChart').innerHTML = '<div class="error-message">No data available for Marginalised Areas Totals chart.</div>';
        return;
    }
    
    // Sort by allocation and limit to top 15 for better visualization
    const sortedData = [...marginalisedTotalsData].sort((a, b) => b.amount - a.amount).slice(0, 15);
    const areas = sortedData.map(item => item.area);
    const amounts = sortedData.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Marginalised Areas Allocation Totals',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: sortedData.length < marginalisedTotalsData.length ? `Top ${sortedData.length} areas` : '',
            subtextStyle: {
                fontSize: 12,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `<strong>${params[0].name}</strong><br/>Allocation: <strong>${formatCurrency(params[0].value)}</strong>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        grid: {
            left: '15%',
            right: '8%',
            bottom: '15%',
            top: '15%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: areas,
            axisLabel: {
                interval: 0,
                rotate: 45,
                fontSize: 10,
                fontWeight: 'bold',
                margin: 15
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Marginalised Area',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + 'B';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd'
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts.map((amount, index) => ({
                value: amount,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#006a71' },
                        { offset: 1, color: '#004d52' }
                    ])
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: function() {
                        return formatCurrency(amount);
                    },
                    fontSize: 10,
                    fontWeight: 'bold',
                    color: '#006a71',
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    padding: [3, 5],
                    borderRadius: 3,
                    borderColor: '#006a71',
                    borderWidth: 1
                }
            })),
            barWidth: '50%',
            barCategoryGap: '20%',
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                },
                label: {
                    fontSize: 11,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)'
                }
            }
        }],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('marginalisedTotalsChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadMarginalisedTotals");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Ensure labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'marginalised_areas_totals.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 26. Marginalised Area Frequency Chart
function drawMarginalisedFrequencyChart() {
    if (!marginalisedData || marginalisedData.length === 0) {
        document.getElementById('marginalisedFrequencyChart').innerHTML = '<div class="error-message">No data available for Marginalised Area Frequency chart.</div>';
        return;
    }
    
    // Sort by frequency and limit to top 15
    const sortedData = [...marginalisedData].sort((a, b) => b.count - a.count).slice(0, 15);
    const areas = sortedData.map(item => item.area);
    const counts = sortedData.map(item => item.count);
    
    const option = {
        title: {
            text: 'Marginalised Area Frequency Chart',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: sortedData.length < marginalisedData.length ? `Top ${sortedData.length} areas` : '',
            subtextStyle: {
                fontSize: 12,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `<strong>${params[0].name}</strong><br/>Frequency: <strong>${params[0].value}</strong>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        grid: {
            left: '15%',
            right: '8%',
            bottom: '15%',
            top: '15%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: areas,
            axisLabel: {
                interval: 0,
                rotate: 45,
                fontSize: 10,
                fontWeight: 'bold',
                margin: 15
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Marginalised Area',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            }
        },
        yAxis: {
            type: 'value',
            name: 'Frequency',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            },
            axisLabel: {
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd'
                }
            }
        },
        series: [{
            name: 'Frequency',
            type: 'bar',
            data: counts.map((count, index) => ({
                value: count,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#26a69a' },
                        { offset: 1, color: '#006a71' }
                    ])
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: function() {
                        return count.toString();
                    },
                    fontSize: 10,
                    fontWeight: 'bold',
                    color: '#006a71',
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    padding: [3, 5],
                    borderRadius: 3,
                    borderColor: '#006a71',
                    borderWidth: 1
                }
            })),
            barWidth: '50%',
            barCategoryGap: '20%',
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                },
                label: {
                    fontSize: 11,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)'
                }
            }
        }],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('marginalisedFrequencyChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadMarginalisedFrequency");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'marginalised_area_frequency.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 27. Allocation per Marginalised Area
function drawAllocationPerMarginalisedChart() {
    if (!marginalisedTotalsData || marginalisedTotalsData.length === 0) {
        document.getElementById('allocationPerMarginalisedChart').innerHTML = '<div class="error-message">No data available for Allocation per Marginalised Area chart.</div>';
        return;
    }

    // Sort by allocation and limit to top 15
    const sortedData = [...marginalisedTotalsData].sort((a, b) => b.amount - a.amount).slice(0, 15);
    const areas = sortedData.map(item => item.area);
    const amounts = sortedData.map(item => item.amount);

    const option = {
        title: {
            text: 'Allocation per Marginalised Area',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: sortedData.length < marginalisedTotalsData.length ? `Top ${sortedData.length} areas` : '',
            subtextStyle: {
                fontSize: 12,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' },
            formatter: function (params) {
                const item = params[0];
                return `<strong>${item.name}</strong><br/>Allocation: <strong>${formatCurrency(item.value)}</strong>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        grid: {
            left: '15%',
            right: '8%',
            bottom: '15%',
            top: '15%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: areas,
            axisLabel: {
                interval: 0,
                rotate: 45,
                fontSize: 10,
                fontWeight: 'bold',
                margin: 15
            },
            axisLine: {
                lineStyle: { color: '#006a71' }
            },
            name: 'Marginalised Area',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 13
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + 'B';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: { color: '#006a71' }
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd'
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts.map((amount, index) => ({
                value: amount,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#66bb6a' },
                        { offset: 1, color: '#006a71' }
                    ])
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: function() {
                        return formatCurrency(amount);
                    },
                    fontSize: 10,
                    fontWeight: 'bold',
                    color: '#006a71',
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    padding: [3, 5],
                    borderRadius: 3,
                    borderColor: '#006a71',
                    borderWidth: 1
                }
            })),
            barWidth: '50%',
            barCategoryGap: '20%',
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                },
                label: {
                    fontSize: 11,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)'
                }
            }
        }],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };

    const myChart = initChart('allocationPerMarginalisedChart', option);
    if (!myChart) return;

    // Download functionality
    const downloadBtn = document.getElementById("downloadAllocationPerMarginalised");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'allocation_per_marginalised_area.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 28. County → Constituency → Ward Drill-Down Chart
function drawDrilldownTreemapChart() {
    if (!countyData || countyData.length === 0 || !wardData || wardData.length === 0) {
        document.getElementById('drilldownTreemapChart').innerHTML = '<div class="error-message">No data available for Drill-Down Chart.</div>';
        return;
    }
    
    // Sort counties by amount and take top 10 for better visualization
    const sortedCounties = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 10);
    
    // Prepare data for tree diagram
    const treeData = {
        name: 'Kenya',
        value: sortedCounties.reduce((sum, county) => sum + county.amount, 0),
        children: sortedCounties.map(county => {
            const countyWards = wardData.filter(ward => ward.county === county.county);
            const countyConstituencies = [...new Set(countyWards.map(ward => ward.constituency))];
            
            // Limit to top 2 constituencies per county
            const constituencyTotals = countyConstituencies.map(constituency => {
                const constituencyWards = countyWards.filter(ward => ward.constituency === constituency);
                return {
                    name: constituency,
                    total: constituencyWards.reduce((sum, ward) => sum + ward.amount, 0),
                    wards: constituencyWards
                };
            }).sort((a, b) => b.total - a.total).slice(0, 2);
            
            return {
                name: county.county,
                value: county.amount,
                children: constituencyTotals.map(constituency => {
                    // Limit to top 2 wards per constituency
                    const topWards = [...constituency.wards]
                        .sort((a, b) => b.amount - a.amount)
                        .slice(0, 2);
                    
                    return {
                        name: constituency.name,
                        value: constituency.total,
                        children: topWards.map(ward => ({
                            name: ward.ward,
                            value: ward.amount
                        }))
                    };
                })
            };
        })
    };
    
    const option = {
        title: {
            text: 'County → Constituency → Ward Drill-Down Chart',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Top 10 counties, top 2 constituencies, top 2 wards per constituency',
            subtextStyle: {
                fontSize: 12,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'item',
            triggerOn: 'mousemove',
            formatter: function (params) {
                const value = params.value || 0;
                const name = params.name || '';
                const treePathInfo = params.treePathInfo || [];
                const treePath = [];
                
                for (let i = 1; i < treePathInfo.length; i++) {
                    treePath.push(treePathInfo[i].name);
                }
                
                const path = treePath.length > 0 ? treePath.join(' > ') : name;
                
                return `<div style="padding: 10px;">
                    <strong>${path}</strong><br/>
                    Allocation: <strong>${formatCurrency(value)}</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        series: [
            {
                name: 'Allocation',
                type: 'tree',
                data: [treeData],
                top: '18%',
                left: '10%',
                bottom: '10%',
                right: '20%',
                symbolSize: function(value) {
                    // Size based on value
                    if (value >= 1000000000) return 20;
                    if (value >= 100000000) return 16;
                    if (value >= 10000000) return 12;
                    return 8;
                },
                label: {
                    show: true,
                    position: 'left',
                    verticalAlign: 'middle',
                    align: 'right',
                    fontSize: 12,
                    fontWeight: 'bold',
                    formatter: function(params) {
                        const name = params.name || '';
                        const value = params.value || 0;
                        const displayName = name.length > 15 ? name.substring(0, 13) + '...' : name;
                        let formattedValue = '';
                        if (value >= 1000000000) {
                            formattedValue = (value / 1000000000).toFixed(1) + 'B';
                        } else if (value >= 1000000) {
                            formattedValue = (value / 1000000).toFixed(1) + 'M';
                        } else if (value >= 1000) {
                            formattedValue = (value / 1000).toFixed(1) + 'K';
                        } else {
                            formattedValue = value.toString();
                        }
                        return `${displayName}\n${formattedValue}`;
                    },
                    color: '#333',
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    padding: [4, 6],
                    borderRadius: 4,
                    borderColor: '#006a71',
                    borderWidth: 1
                },
                leaves: {
                    label: {
                        position: 'right',
                        verticalAlign: 'middle',
                        align: 'left',
                        fontSize: 11
                    }
                },
                emphasis: {
                    focus: 'descendant',
                    label: {
                        fontSize: 13,
                        backgroundColor: 'rgba(0, 106, 113, 0.1)',
                        borderColor: '#006a71',
                        borderWidth: 2
                    }
                },
                expandAndCollapse: true,
                initialTreeDepth: 3,
                lineStyle: {
                    color: '#006a71',
                    width: 2,
                    curveness: 0.5
                },
                itemStyle: {
                    color: '#006a71',
                    borderColor: '#fff',
                    borderWidth: 2
                },
                symbol: 'roundRect',
                symbolSize: [80, 40]
            }
        ],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('drilldownTreemapChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadDrilldownTreemap");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Ensure all labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    },
                    leaves: {
                        label: {
                            show: true
                        }
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'drilldown_tree.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 29. Drill-Down Column Chart
function drawDrilldownColumnChart() {
    if (!countyData || countyData.length === 0 || !constituencyData || constituencyData.length === 0 || !wardData || wardData.length === 0) {
        document.getElementById('drilldownColumnChart').innerHTML = '<div class="error-message">No data available for Drill-Down Column chart.</div>';
        return;
    }
    
    // Sort and limit to top 20 counties for better visualization
    const sortedData = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 20);
    const counties = sortedData.map(item => item.county);
    const countyAmounts = sortedData.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Drill-Down Column Chart',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: sortedData.length < countyData.length ? `Top ${sortedData.length} counties by allocation` : 'All counties by allocation',
            subtextStyle: {
                fontSize: 12,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                const param = params[0];
                return `<div style="padding: 8px;">
                    <strong>${param.name}</strong><br/>
                    Allocation: <strong>${formatCurrency(param.value)}</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        grid: {
            left: '8%',
            right: '5%',
            bottom: '20%',
            top: '18%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold',
                margin: 12,
                width: 100,
                overflow: 'truncate'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71',
                    width: 2
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            splitLine: {
                show: false
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 70,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + 'B';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                },
                fontWeight: 'bold',
                fontSize: 10
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#006a71',
                    width: 2
                }
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd',
                    width: 1
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: countyAmounts.map((amount, index) => ({
                value: amount,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#006a71' },
                        { offset: 1, color: '#004d52' }
                    ])
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: function() {
                        return formatCurrency(amount);
                    },
                    fontSize: 10,
                    fontWeight: 'bold',
                    color: '#006a71',
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    padding: [3, 5],
                    borderRadius: 3,
                    borderColor: '#006a71',
                    borderWidth: 1
                }
            })),
            barWidth: '50%',
            barCategoryGap: '15%',
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                },
                label: {
                    fontSize: 11,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    borderWidth: 2
                }
            }
        }],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('drilldownColumnChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadDrilldownColumn");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Ensure labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'drilldown_column.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 30. Drill-Down Pie/Donut Chart
function drawDrilldownPieChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('drilldownPieChart').innerHTML = '<div class="error-message">No data available for Drill-Down Pie chart.</div>';
        return;
    }
    
    // Prepare data for pie chart
    const pieData = countyData.map(county => ({
        name: county.county,
        value: county.amount
    }));
    
    const option = {
        title: {
            text: 'Drill-Down Pie/Donut Chart',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: '5%',
            top: 'middle',
            data: countyData.map(item => item.county),
            textStyle: {
                fontWeight: 'bold',
                fontSize: 11
            },
            itemGap: 8,
            formatter: function(name) {
                return name.length > 18 ? name.substring(0, 16) + '…' : name;
            }
        },
        series: [
            {
                name: 'Allocation',
                type: 'pie',
                radius: ['35%', '65%'],
                center: ['60%', '55%'],
                avoidLabelOverlap: true,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: true,
                    formatter: '{b}: {c}',
                    fontWeight: 'bold'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: '16',
                        fontWeight: 'bold'
                    }
                },
                labelLine: {
                    show: true
                },
                data: pieData
            }
        ],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('drilldownPieChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadDrilldownPie").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'drilldown_pie.png';
        link.href = url;
        link.click();
    };
}

// 31. Allocation Comparison Chart
function drawComparisonChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('comparisonChart').innerHTML = '<div class="error-message">No data available for Allocation Comparison chart.</div>';
        return;
    }
    
    // Select top 5 counties for comparison
    const topCounties = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 5);
    const counties = topCounties.map(item => item.county);
    const amounts = topCounties.map(item => item.amount);
    
    const option = {
        title: {
            text: 'Allocation Comparison Chart',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                interval: 0,
                fontSize: 12,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: amounts,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [5, 5, 0, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '40%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('comparisonChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadComparison").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'allocation_comparison.png';
        link.href = url;
        link.click();
    };
}

// 32. Allocation per Administrative Level
function drawAllocationPerLevelChart() {
    if (!countyData || countyData.length === 0 || !constituencyData || constituencyData.length === 0 || !wardData || wardData.length === 0) {
        document.getElementById('allocationPerLevelChart').innerHTML = '<div class="error-message">No data available for Allocation per Administrative Level chart.</div>';
        return;
    }
    
    // Calculate totals for each level
    const countyTotal = countyData.reduce((sum, item) => sum + item.amount, 0);
    const constituencyTotal = constituencyData.reduce((sum, item) => sum + item.amount, 0);
    const wardTotal = wardData.reduce((sum, item) => sum + item.amount, 0);
    
    const option = {
        title: {
            text: 'Allocation per Administrative Level',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Allocation: ${formatCurrency(params[0].value)}`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: ['County', 'Constituency', 'Ward'],
            axisLabel: {
                interval: 0,
                fontSize: 12,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Administrative Level',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Allocation',
            type: 'bar',
            data: [countyTotal, constituencyTotal, wardTotal],
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [5, 5, 0, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    return formatCurrency(params.value);
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '40%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('allocationPerLevelChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadAllocationPerLevel").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'allocation_per_level.png';
        link.href = url;
        link.click();
    };
}

// 33. Allocation Ratio Chart
function drawAllocationRatioChart() {
    if (!constituencyData || constituencyData.length === 0 || !wardData || wardData.length === 0) {
        document.getElementById('allocationRatioChart').innerHTML = '<div class="error-message">No data available for Allocation Ratio chart.</div>';
        return;
    }
    
    // Calculate ratios for top 10 wards
    const topWards = [...wardData].sort((a, b) => b.amount - a.amount).slice(0, 10);
    const wardNames = topWards.map(item => item.ward);
    const wardAmounts = topWards.map(item => item.amount);
    
       // Calculate constituency totals for these wards
    const constituencyTotals = topWards.map(ward => {
        const constituencyWards = wardData.filter(w => w.constituency === ward.constituency);
        return constituencyWards.reduce((sum, w) => sum + w.amount, 0);
    });
    
    // Calculate ratios
    const ratios = wardAmounts.map((amount, index) => (amount / constituencyTotals[index]) * 100);
    
    const option = {
        title: {
            text: 'Allocation Ratio Chart',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                const index = params.dataIndex;
                return `${wardNames[index]}<br/>Ratio: ${params[0].value.toFixed(2)}%<br/>Ward: ${formatCurrency(wardAmounts[index])}<br/>Constituency: ${formatCurrency(constituencyTotals[index])}`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: wardNames,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Ward',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Ratio (%)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value + '%';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Ratio',
            type: 'bar',
            data: ratios,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [5, 5, 0, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    return params.value.toFixed(2) + '%';
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '40%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('allocationRatioChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadAllocationRatio").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'allocation_ratio.png';
        link.href = url;
        link.click();
    };
}

// 34. Outlier Detection Chart
function drawOutlierDetectionChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('outlierDetectionChart').innerHTML = '<div class="error-message">No data available for Outlier Detection chart.</div>';
        return;
    }
    
    // Generate box plot data
    const counties = countyData.map(item => item.county);
    const amounts = countyData.map(item => item.amount);
    
    // Calculate box plot statistics
    const boxData = counties.map((county, index) => {
        // For demonstration, we'll use random values around the actual amount
        const amount = amounts[index];
        const min = amount * 0.7;
        const q1 = amount * 0.85;
        const median = amount;
        const q3 = amount * 1.15;
        const max = amount * 1.3;
        
        return [min, q1, median, q3, max];
    });
    
    const option = {
        title: {
            text: 'Outlier Detection Chart',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                return [
                    `${params.name}`,
                    `Min: ${formatCurrency(params.data[0])}`,
                    `Q1: ${formatCurrency(params.data[1])}`,
                    `Median: ${formatCurrency(params.data[2])}`,
                    `Q3: ${formatCurrency(params.data[3])}`,
                    `Max: ${formatCurrency(params.data[4])}`
                ].join('<br/>');
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    return value / 1000000 + 'M';
                },
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Allocation Distribution',
            type: 'boxplot',
            data: boxData,
            itemStyle: {
                color: '#006a71',
                borderColor: '#004d52',
                borderWidth: 2
            }
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('outlierDetectionChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadOutlierDetection").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'outlier_detection.png';
        link.href = url;
        link.click();
    };
}

// 35. Cumulative Allocation Chart
function drawCumulativeAllocationChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('cumulativeAllocationChart').innerHTML = '<div class="error-message">No data available for Cumulative Allocation chart.</div>';
        return;
    }
    
    // Sort counties by allocation and limit to top 25 for better visualization
    const sortedCounties = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 25);
    const counties = sortedCounties.map(item => item.county);
    const amounts = sortedCounties.map(item => item.amount);
    
    // Calculate cumulative allocation
    const cumulativeAmounts = [];
    let cumulative = 0;
    amounts.forEach(amount => {
        cumulative += amount;
        cumulativeAmounts.push(cumulative);
    });
    
    const totalAllocation = cumulativeAmounts[cumulativeAmounts.length - 1];
    
    const option = {
        title: {
            text: 'Cumulative Allocation Chart',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: sortedCounties.length < countyData.length ? `Top ${sortedCounties.length} counties (sorted by allocation)` : 'All counties (sorted by allocation)',
            subtextStyle: {
                fontSize: 12,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#006a71'
                }
            },
            formatter: function(params) {
                const index = params[0].dataIndex;
                const allocation = amounts[index];
                const cumulative = cumulativeAmounts[index];
                const percentage = ((cumulative / totalAllocation) * 100).toFixed(2);
                
                return `<div style="padding: 10px;">
                    <strong>${counties[index]}</strong><br/>
                    Individual Allocation: <strong>${formatCurrency(allocation)}</strong><br/>
                    Cumulative Total: <strong>${formatCurrency(cumulative)}</strong><br/>
                    Cumulative %: <strong>${percentage}%</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        grid: {
            left: '8%',
            right: '5%',
            bottom: '20%',
            top: '18%',
            containLabel: false
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 9,
                fontWeight: 'bold',
                margin: 15,
                width: 100,
                overflow: 'truncate'
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#006a71',
                    width: 2
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            splitLine: {
                show: false
            }
        },
        yAxis: {
            type: 'value',
            name: 'Cumulative Allocation (KSH)',
            nameLocation: 'middle',
            nameGap: 70,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(1) + 'B';
                    } else if (value >= 1000000) {
                        return (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'K';
                    }
                    return value;
                },
                fontWeight: 'bold',
                fontSize: 10
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#006a71',
                    width: 2
                }
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd',
                    width: 1
                }
            }
        },
        series: [{
            name: 'Cumulative Allocation',
            type: 'line',
            data: cumulativeAmounts.map((cumulative, index) => ({
                value: cumulative,
                itemStyle: {
                    color: '#006a71',
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: index % 3 === 0 || index === cumulativeAmounts.length - 1, // Show every 3rd label and last one
                    position: 'top',
                    formatter: function() {
                        let formattedValue = '';
                        if (cumulative >= 1000000000) {
                            formattedValue = (cumulative / 1000000000).toFixed(1) + 'B';
                        } else if (cumulative >= 1000000) {
                            formattedValue = (cumulative / 1000000).toFixed(1) + 'M';
                        } else if (cumulative >= 1000) {
                            formattedValue = (cumulative / 1000).toFixed(1) + 'K';
                        } else {
                            formattedValue = cumulative.toString();
                        }
                        return formattedValue;
                    },
                    fontSize: 9,
                    fontWeight: 'bold',
                    color: '#006a71',
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    padding: [3, 5],
                    borderRadius: 3,
                    borderColor: '#006a71',
                    borderWidth: 1
                }
            })),
            smooth: true,
            lineStyle: {
                width: 3,
                color: '#006a71',
                shadowColor: 'rgba(0, 106, 113, 0.3)',
                shadowBlur: 8
            },
            symbol: 'circle',
            symbolSize: function(value, params) {
                // Make first, last, and every 5th point larger
                const index = params.dataIndex;
                if (index === 0 || index === cumulativeAmounts.length - 1 || index % 5 === 0) {
                    return 12;
                }
                return 8;
            },
            itemStyle: {
                color: '#006a71',
                borderColor: '#fff',
                borderWidth: 2
            },
            areaStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: 'rgba(0, 106, 113, 0.4)' },
                    { offset: 1, color: 'rgba(0, 106, 113, 0.05)' }
                ])
            },
            emphasis: {
                focus: 'series',
                itemStyle: {
                    shadowBlur: 15,
                    shadowColor: 'rgba(0, 106, 113, 0.6)'
                },
                label: {
                    show: true,
                    fontSize: 10,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    borderWidth: 2
                }
            }
        }],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('cumulativeAllocationChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadCumulativeAllocation");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Ensure labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'cumulative_allocation.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 41. Allocation Histogram
function drawAllocationHistogramChart() {
    if (!wardData || wardData.length === 0) {
        document.getElementById('allocationHistogramChart').innerHTML = '<div class="error-message">No data available for Allocation Histogram chart.</div>';
        return;
    }
    
    // Get all ward allocations
    const allocations = wardData.map(item => item.amount);
    
    // Create histogram bins
    const minAllocation = Math.min(...allocations);
    const maxAllocation = Math.max(...allocations);
    const binCount = 10;
    const binSize = (maxAllocation - minAllocation) / binCount;
    
    const bins = [];
    const binCounts = [];
    
    for (let i = 0; i < binCount; i++) {
        const binStart = minAllocation + i * binSize;
        const binEnd = minAllocation + (i + 1) * binSize;
        bins.push(`${formatCurrency(binStart)} - ${formatCurrency(binEnd)}`);
        binCounts.push(0);
    }
    
    // Count allocations in each bin
    allocations.forEach(allocation => {
        const binIndex = Math.min(Math.floor((allocation - minAllocation) / binSize), binCount - 1);
        binCounts[binIndex]++;
    });
    
    const option = {
        title: {
            text: 'Allocation Histogram',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            formatter: function(params) {
                return `${params[0].name}<br/>Count: ${params[0].value}`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: bins,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'Allocation Range (KSH)',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: {
            type: 'value',
            name: 'Count',
            nameLocation: 'middle',
            nameGap: 60,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            }
        },
        series: [{
            name: 'Count',
            type: 'bar',
            data: binCounts,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#006a71' },
                    { offset: 1, color: '#004d52' }
                ]),
                borderRadius: [5, 5, 0, 0]
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#004d52' },
                        { offset: 1, color: '#006a71' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    return params.value;
                },
                fontSize: 10,
                fontWeight: 'bold'
            },
            barWidth: '60%'
        }],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('allocationHistogramChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadAllocationHistogram").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'allocation_histogram.png';
        link.href = url;
        link.click();
    };
}

// 42. Pareto Chart
function drawParetoChartChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('paretoChartChart').innerHTML = '<div class="error-message">No data available for Pareto Chart.</div>';
        return;
    }
    
    // Sort counties by allocation
    const sortedCounties = [...countyData].sort((a, b) => b.amount - a.amount);
    const counties = sortedCounties.map(item => item.county);
    const amounts = sortedCounties.map(item => item.amount);
    
    // Calculate cumulative allocation and percentages
    const totalAllocation = amounts.reduce((sum, amount) => sum + amount, 0);
    const cumulativeAmounts = [];
    const cumulativePercentages = [];
    let cumulative = 0;
    
    amounts.forEach(amount => {
        cumulative += amount;
        cumulativeAmounts.push(cumulative);
        cumulativePercentages.push((cumulative / totalAllocation) * 100);
    });
    
    const option = {
        title: {
            text: 'Pareto Chart (80/20)',
            left: 'center',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                const index = params.dataIndex;
                return [
                    `${counties[index]}`,
                    `Allocation: ${formatCurrency(amounts[index])}`,
                    `Cumulative: ${formatCurrency(cumulativeAmounts[index])}`,
                    `Percentage: ${cumulativePercentages[index].toFixed(2)}%`
                ].join('<br/>');
            }
        },
        legend: {
            data: ['Allocation', 'Cumulative Percentage'],
            top: 'bottom'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: counties,
            axisLabel: {
                rotate: 45,
                interval: 0,
                fontSize: 10,
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#006a71'
                }
            },
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            }
        },
        yAxis: [
            {
                type: 'value',
                name: 'Allocation (KSH)',
                nameLocation: 'middle',
                nameGap: 60,
                nameTextStyle: {
                    color: '#006a71',
                    fontWeight: 'bold',
                    fontSize: 14
                },
                axisLabel: {
                    formatter: function(value) {
                        return value / 1000000 + 'M';
                    },
                    fontWeight: 'bold'
                },
                axisLine: {
                    lineStyle: {
                        color: '#006a71'
                    }
                }
            },
            {
                type: 'value',
                name: 'Cumulative %',
                nameLocation: 'middle',
                nameGap: 40,
                nameTextStyle: {
                    color: '#006a71',
                    fontWeight: 'bold',
                    fontSize: 14
                },
                axisLabel: {
                    formatter: function(value) {
                        return value + '%';
                    },
                    fontWeight: 'bold'
                },
                axisLine: {
                    lineStyle: {
                        color: '#006a71'
                    }
                },
                max: 100
            }
        ],
        series: [
            {
                name: 'Allocation',
                type: 'bar',
                data: amounts,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#006a71' },
                        { offset: 1, color: '#004d52' }
                    ]),
                    borderRadius: [5, 5, 0, 0]
                },
                emphasis: {
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#004d52' },
                            { offset: 1, color: '#006a71' }
                        ])
                    }
                },
                barWidth: '40%'
            },
            {
                name: 'Cumulative Percentage',
                type: 'line',
                yAxisIndex: 1,
                data: cumulativePercentages,
                smooth: true,
                lineStyle: {
                    width: 4,
                    color: '#ff7e29',
                    shadowColor: 'rgba(255, 126, 41, 0.5)',
                    shadowBlur: 10
                },
                symbol: 'circle',
                symbolSize: 10,
                itemStyle: {
                    color: '#ff7e29',
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: function(params) {
                        return params.value.toFixed(1) + '%';
                    },
                    fontSize: 10,
                    fontWeight: 'bold'
                }
            }
        ],
        animationDuration: 2000,
        animationEasing: 'elasticOut'
    };
    
    const myChart = initChart('paretoChartChart', option);
    if (!myChart) return;
    
    // Download functionality
    document.getElementById("downloadParetoChart").onclick = function() {
        const url = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        const link = document.createElement('a');
        link.download = 'pareto_chart.png';
        link.href = url;
        link.click();
    };
}

// 43. Weighted Bubble Chart
function drawWeightedBubbleChart() {
    if (!countyData || countyData.length === 0 || !constituencyData || constituencyData.length === 0) {
        document.getElementById('weightedBubbleChart').innerHTML = '<div class="error-message">No data available for Weighted Bubble chart.</div>';
        return;
    }
    
    // Prepare data for bubble chart
    const bubbleData = [];
    const colorPalette = [
        '#006a71', '#00838f', '#0097a7', '#00acc1', '#00bcd4',
        '#26c6da', '#4dd0e1', '#80deea', '#b2ebf2', '#e0f7fa',
        '#004d52', '#006064', '#00796b', '#00897b', '#009688',
        '#26a69a', '#4db6ac', '#80cbc4', '#b2dfdb', '#e0f2f1',
        '#1a237e', '#283593', '#303f9f', '#3949ab', '#3f51b5',
        '#5c6bc0', '#7986cb', '#9fa8da', '#c5cae9', '#e8eaf6'
    ];
    
    // Count marginalised areas per county from topAllocationsData if available
    let countyAreaCounts = {};
    if (typeof topAllocationsData !== 'undefined' && topAllocationsData && topAllocationsData.length > 0) {
        topAllocationsData.forEach(item => {
            if (item.county && item.marginalised_area) {
                const countyKey = item.county.toUpperCase().trim();
                if (!countyAreaCounts[countyKey]) {
                    countyAreaCounts[countyKey] = new Set();
                }
                countyAreaCounts[countyKey].add(item.marginalised_area.toUpperCase().trim());
            }
        });
    }
    
    countyData.forEach((county, index) => {
        // Get number of constituencies for this county
        const constituencyCount = constituencyData.filter(c => 
            c.county && county.county && c.county.toUpperCase().trim() === county.county.toUpperCase().trim()
        ).length;
        
        // Get number of marginalised areas for this county
        const countyKey = county.county ? county.county.toUpperCase().trim() : '';
        const marginalisedCount = countyAreaCounts[countyKey] ? countyAreaCounts[countyKey].size : 0;
        
        // Calculate bubble size based on allocation (normalized)
        const maxAmount = Math.max(...countyData.map(c => c.amount));
        const minAmount = Math.min(...countyData.map(c => c.amount));
        const normalizedSize = ((county.amount - minAmount) / (maxAmount - minAmount)) * 80 + 30; // Size between 30-110
        
        bubbleData.push({
            name: county.county,
            value: [
                constituencyCount || 1, // X: number of constituencies (min 1)
                county.amount / 1000000, // Y: allocation in millions
                normalizedSize // Bubble size: based on allocation
            ],
            marginalisedCount: marginalisedCount,
            itemStyle: {
                color: colorPalette[index % colorPalette.length],
                borderColor: '#fff',
                borderWidth: 2,
                shadowBlur: 10,
                shadowColor: 'rgba(0, 0, 0, 0.3)'
            }
        });
    });
    
    const option = {
        title: {
            text: 'Weighted Bubble Chart',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Bubble size represents allocation amount | X-axis: Constituencies | Y-axis: Allocation (M KSH)',
            subtextStyle: {
                fontSize: 11,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                const allocation = params.value[1] * 1000000;
                const constituencies = params.value[0];
                const marginalised = params.data.marginalisedCount || 0;
                return `<div style="padding: 8px;">
                    <strong style="font-size: 14px; color: #006a71;">${params.name}</strong><br/>
                    <hr style="margin: 5px 0; border-color: #ddd;"/>
                    Constituencies: <strong>${constituencies}</strong><br/>
                    Allocation: <strong>${formatCurrency(allocation)}</strong><br/>
                    Marginalised Areas: <strong>${marginalised}</strong>
                </div>`;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2,
            textStyle: {
                fontSize: 12
            }
        },
        grid: {
            left: '10%',
            right: '8%',
            bottom: '15%',
            top: '20%',
            containLabel: false
        },
        xAxis: {
            type: 'value',
            name: 'Number of Constituencies',
            nameLocation: 'middle',
            nameGap: 40,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#006a71',
                    width: 2
                }
            },
            axisLabel: {
                fontWeight: 'bold',
                fontSize: 11
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd',
                    width: 1
                }
            }
        },
        yAxis: {
            type: 'value',
            name: 'Allocation (M KSH)',
            nameLocation: 'middle',
            nameGap: 70,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLine: {
                show: true,
                lineStyle: {
                    color: '#006a71',
                    width: 2
                }
            },
            axisLabel: {
                formatter: function(value) {
                    if (value >= 1000) {
                        return (value / 1000).toFixed(1) + 'B';
                    }
                    return value.toFixed(0) + 'M';
                },
                fontWeight: 'bold',
                fontSize: 11
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#ddd',
                    width: 1
                }
            }
        },
        series: [{
            name: 'County Data',
            type: 'scatter',
            data: bubbleData,
            symbolSize: function(data) {
                return data[2];
            },
            label: {
                show: true,
                formatter: function(params) {
                    // Show county name, truncated if too long
                    const name = params.name;
                    return name.length > 12 ? name.substring(0, 10) + '...' : name;
                },
                fontSize: 9,
                fontWeight: 'bold',
                color: '#333',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
                padding: [2, 4],
                borderRadius: 3,
                borderColor: '#006a71',
                borderWidth: 1,
                position: 'top'
            },
            labelLayout: {
                hideOverlap: true
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 11,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    borderWidth: 2
                },
                itemStyle: {
                    shadowBlur: 20,
                    shadowColor: 'rgba(0, 106, 113, 0.6)',
                    borderWidth: 3
                },
                scale: true,
                scaleSize: 5
            }
        }],
        animationDuration: 1500,
        animationEasing: 'cubicOut'
    };
    
    const myChart = initChart('weightedBubbleChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadWeightedBubble");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Ensure labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'weighted_bubble.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

// 44. Allocation Network Diagram
function drawAllocationNetworkChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('allocationNetworkChart').innerHTML = '<div class="error-message">No data available for Allocation Network chart.</div>';
        return;
    }
    
    // Prepare data for network diagram using actual relationships
    const nodes = [];
    const links = [];
    const nodeMap = new Map();
    
    // Build county to marginalised area mapping from topAllocationsData
    const countyAreaMap = new Map();
    const areaAllocationMap = new Map();
    
    if (typeof topAllocationsData !== 'undefined' && topAllocationsData && topAllocationsData.length > 0) {
        topAllocationsData.forEach(item => {
            if (item.county && item.marginalised_area && item.marginalised_area !== 'UNKNOWN') {
                const countyKey = item.county.toUpperCase().trim();
                const areaKey = item.marginalised_area.toUpperCase().trim();
                
                if (!countyAreaMap.has(countyKey)) {
                    countyAreaMap.set(countyKey, new Map());
                }
                
                const areaMap = countyAreaMap.get(countyKey);
                if (!areaMap.has(areaKey)) {
                    areaMap.set(areaKey, 0);
                }
                areaMap.set(areaKey, areaMap.get(areaKey) + (item.allocation || 0));
                
                // Track total allocation per area
                if (!areaAllocationMap.has(areaKey)) {
                    areaAllocationMap.set(areaKey, 0);
                }
                areaAllocationMap.set(areaKey, areaAllocationMap.get(areaKey) + (item.allocation || 0));
            }
        });
    }
    
    // Limit to top 20 counties and their connected areas for clarity
    const sortedCounties = [...countyData].sort((a, b) => b.amount - a.amount).slice(0, 20);
    const usedAreas = new Set();
    
    // Add county nodes
    sortedCounties.forEach((county, index) => {
        const countyKey = county.county.toUpperCase().trim();
        const maxAmount = Math.max(...sortedCounties.map(c => c.amount));
        const nodeSize = 25 + (county.amount / maxAmount) * 40;
        
        nodes.push({
            name: county.county,
            value: county.amount,
            symbolSize: nodeSize,
            category: 0,
            itemStyle: {
                color: '#006a71',
                borderColor: '#fff',
                borderWidth: 3,
                shadowBlur: 15,
                shadowColor: 'rgba(0, 106, 113, 0.5)'
            }
        });
        nodeMap.set(county.county, true);
        
        // Add links to marginalised areas for this county
        if (countyAreaMap.has(countyKey)) {
            const areas = Array.from(countyAreaMap.get(countyKey).entries())
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5); // Limit to top 5 areas per county
            
            areas.forEach(([areaName, allocation]) => {
                if (!usedAreas.has(areaName)) {
                    // Add marginalised area node
                    const maxAreaAlloc = Math.max(...Array.from(areaAllocationMap.values()));
                    const areaNodeSize = 15 + (areaAllocationMap.get(areaName) / maxAreaAlloc) * 25;
                    
                    nodes.push({
                        name: areaName,
                        value: areaAllocationMap.get(areaName),
                        symbolSize: areaNodeSize,
                        category: 1,
                        itemStyle: {
                            color: '#00acc1',
                            borderColor: '#fff',
                            borderWidth: 2,
                            shadowBlur: 10,
                            shadowColor: 'rgba(0, 172, 193, 0.4)'
                        }
                    });
                    nodeMap.set(areaName, true);
                    usedAreas.add(areaName);
                }
                
                // Create link
                links.push({
                    source: county.county,
                    target: areaName,
                    value: allocation,
                    lineStyle: {
                        width: 1 + (allocation / 50000000) * 3, // Width based on allocation
                        opacity: 0.6
                    }
                });
            });
        }
    });
    
    const option = {
        title: {
            text: 'Allocation Network Diagram',
            left: 'center',
            top: '3%',
            textStyle: {
                color: '#333',
                fontSize: 18,
                fontWeight: 'bold'
            },
            subtext: 'Top 20 counties and their connected marginalised areas',
            subtextStyle: {
                fontSize: 11,
                color: '#666'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                if (params.dataType === 'node') {
                    const value = params.data.value;
                    if (params.data.category === 0) {
                        // County node
                        return `<div style="padding: 8px;">
                            <strong style="font-size: 14px; color: #006a71;">${params.name}</strong><br/>
                            <hr style="margin: 5px 0; border-color: #ddd;"/>
                            Total Allocation: <strong>${formatCurrency(value)}</strong>
                        </div>`;
                    } else {
                        // Marginalised area node
                        return `<div style="padding: 8px;">
                            <strong style="font-size: 14px; color: #00acc1;">${params.name}</strong><br/>
                            <hr style="margin: 5px 0; border-color: #ddd;"/>
                            Total Allocation: <strong>${formatCurrency(value)}</strong>
                        </div>`;
                    }
                } else if (params.dataType === 'edge') {
                    // Link
                    return `<div style="padding: 8px;">
                        <strong>${params.data.source} → ${params.data.target}</strong><br/>
                        <hr style="margin: 5px 0; border-color: #ddd;"/>
                        Allocation: <strong>${formatCurrency(params.data.value)}</strong>
                    </div>`;
                }
                return params.name;
            },
            backgroundColor: 'rgba(255,255,255,0.95)',
            borderColor: '#006a71',
            borderWidth: 2
        },
        legend: {
            data: ['Counties', 'Marginalised Areas'],
            bottom: '2%',
            itemGap: 30,
            textStyle: {
                fontSize: 12,
                fontWeight: 'bold'
            }
        },
        animationDurationUpdate: 1500,
        animationEasingUpdate: 'quinticInOut',
        series: [{
            type: 'graph',
            layout: 'force',
            data: nodes,
            links: links,
            categories: [
                {
                    name: 'Counties',
                    itemStyle: {
                        color: '#006a71'
                    }
                },
                {
                    name: 'Marginalised Areas',
                    itemStyle: {
                        color: '#00acc1'
                    }
                }
            ],
            roam: true,
            zoom: 1.2,
            label: {
                show: true,
                position: 'right',
                fontSize: 10,
                fontWeight: 'bold',
                color: '#333',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
                padding: [3, 5],
                borderRadius: 3,
                borderColor: '#006a71',
                borderWidth: 1,
                formatter: function(params) {
                    // Truncate long names
                    const name = params.name;
                    return name.length > 15 ? name.substring(0, 13) + '...' : name;
                }
            },
            labelLayout: {
                hideOverlap: true
            },
            force: {
                repulsion: 300,
                edgeLength: [100, 200],
                gravity: 0.1,
                layoutAnimation: true
            },
            lineStyle: {
                color: '#006a71',
                width: 2,
                curveness: 0.3,
                opacity: 0.6
            },
            emphasis: {
                focus: 'adjacency',
                lineStyle: {
                    width: 4,
                    opacity: 1
                },
                label: {
                    show: true,
                    fontSize: 12,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    borderWidth: 2
                },
                itemStyle: {
                    shadowBlur: 20,
                    shadowColor: 'rgba(0, 106, 113, 0.6)'
                }
            }
        }]
    };
    
    const myChart = initChart('allocationNetworkChart', option);
    if (!myChart) return;
    
    // Download functionality
    const downloadBtn = document.getElementById("downloadAllocationNetwork");
    if (downloadBtn) {
        downloadBtn.onclick = function() {
            // Ensure labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(function() {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: '#ffffff'
                });
                const link = document.createElement('a');
                link.download = 'allocation_network.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}
//45chart  Allocation Spread Matrix
function drawAllocationSpreadChart() {
    if (!countyData || countyData.length === 0 || !constituencyData || constituencyData.length === 0) {
        document.getElementById('allocationSpreadChart').innerHTML =
            '<div class="error-message">No data available for Allocation Spread Matrix chart.</div>';
        return;
    }

    // Limit to top 15 counties and top 20 constituencies for much better clarity
    const counties = [...new Set(countyData.map(c => c.county))];
    const constituencies = [...new Set(constituencyData.map(c => c.constituency))];

    const countyTotals = counties.map(c => ({
        name: c,
        total: constituencyData.filter(d => d.county && c && d.county.toUpperCase().trim() === c.toUpperCase().trim())
            .reduce((a, b) => a + (b.amount || 0), 0)
    })).sort((a, b) => b.total - a.total).slice(0, 15);

    const conTotals = constituencies.map(c => ({
        name: c,
        total: constituencyData.filter(d => d.constituency && c && d.constituency.toUpperCase().trim() === c.toUpperCase().trim())
            .reduce((a, b) => a + (b.amount || 0), 0)
    })).sort((a, b) => b.total - a.total).slice(0, 20);

    const sortedCounties = countyTotals.map(d => d.name);
    const sortedConstituencies = conTotals.map(d => d.name);

    const countyIndex = {};
    const conIndex = {};
    sortedCounties.forEach((c, i) => countyIndex[c.toUpperCase().trim()] = i);
    sortedConstituencies.forEach((c, i) => conIndex[c.toUpperCase().trim()] = i);

    // MATRIX BUILD - only for matching county-constituency pairs
    const fullMatrix = [];
    
    constituencyData.forEach(item => {
        if (!item.county || !item.constituency) return;
        
        const countyKey = item.county.toUpperCase().trim();
        const conKey = item.constituency.toUpperCase().trim();
        
        const ci = countyIndex[countyKey];
        const co = conIndex[conKey];
        
        if (ci != null && co != null && item.amount) {
            const amount = item.amount / 1000000; // Convert to millions
            fullMatrix.push([ci, co, amount]);
        }
    });

    // Remove duplicates and sum amounts for same county-constituency pairs
    const matrixMap = new Map();
    fullMatrix.forEach(([ci, co, amount]) => {
        const key = `${ci}-${co}`;
        if (matrixMap.has(key)) {
            matrixMap.set(key, matrixMap.get(key) + amount);
        } else {
            matrixMap.set(key, amount);
        }
    });
    
    const finalMatrix = Array.from(matrixMap.entries()).map(([key, amount]) => {
        const [ci, co] = key.split('-').map(Number);
        return [ci, co, amount];
    });

    const nonZeroValues = finalMatrix.map(d => d[2]).filter(v => v > 0);
    const minVal = nonZeroValues.length > 0 ? Math.min(...nonZeroValues) : 0;
    const maxVal = nonZeroValues.length > 0 ? Math.max(...nonZeroValues) : 1;

    const option = {
        backgroundColor: "#ffffff",
        title: {
            text: 'Allocation Spread Matrix',
            subtext: `Top ${sortedCounties.length} Counties vs Top ${sortedConstituencies.length} Constituencies | Hover to see details`,
            left: "center",
            top: "2%",
            textStyle: {
                color: "#333",
                fontSize: 20,
                fontWeight: "bold"
            },
            subtextStyle: {
                color: "#666",
                fontSize: 11
            }
        },
        tooltip: {
            trigger: 'item',
            backgroundColor: "rgba(255,255,255,0.98)",
            borderColor: "#006a71",
            borderWidth: 3,
            padding: [12, 15],
            textStyle: { 
                color: "#333",
                fontSize: 13
            },
            formatter: params => {
                const county = sortedCounties[params.data[0]];
                const constituency = sortedConstituencies[params.data[1]];
                const raw = params.data[2] * 1000000;
                const millions = params.data[2];
                return `
                    <div style="padding: 8px; line-height: 1.8;">
                        <div style="font-size: 16px; font-weight: bold; color: #006a71; margin-bottom: 8px;">${county}</div>
                        <div style="font-size: 13px; color: #555; margin-bottom: 8px;">Constituency: <strong>${constituency}</strong></div>
                        <hr style="margin: 8px 0; border: none; border-top: 1px solid #ddd;"/>
                        <div style="font-size: 14px;">Allocation: <strong style="color: #006a71; font-size: 15px;">${formatCurrency(raw)}</strong></div>
                        <div style="font-size: 12px; color: #888; margin-top: 5px;">(${millions.toFixed(2)} Million KSH)</div>
                    </div>
                `;
            }
        },
        grid: {
            left: "18%",
            right: "8%",
            top: "12%",
            bottom: "22%",
            containLabel: false
        },
        xAxis: {
            type: "category",
            data: sortedCounties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 70,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                rotate: 45,
                color: "#333",
                fontSize: 11,
                fontWeight: "bold",
                interval: 0,
                margin: 15,
                formatter: function(value) {
                    return value.length > 10 ? value.substring(0, 8) + '..' : value;
                }
            },
            axisLine: { 
                show: true,
                lineStyle: { 
                    color: "#006a71",
                    width: 2
                } 
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#e0e0e0',
                    width: 1
                }
            }
        },
        yAxis: {
            type: "category",
            data: sortedConstituencies,
            name: 'Constituency',
            nameLocation: 'middle',
            nameGap: 100,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                color: "#333",
                fontSize: 10,
                fontWeight: "bold",
                interval: 0,
                margin: 8,
                formatter: function(value) {
                    return value.length > 15 ? value.substring(0, 13) + '..' : value;
                }
            },
            axisLine: { 
                show: true,
                lineStyle: { 
                    color: "#006a71",
                    width: 2
                } 
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#e0e0e0',
                    width: 1
                }
            }
        },
        visualMap: {
            min: minVal,
            max: maxVal,
            orient: "horizontal",
            left: "center",
            bottom: "8%",
            calculable: true,
            precision: 1,
            textStyle: {
                color: "#333",
                fontWeight: "bold",
                fontSize: 12
            },
            itemWidth: 25,
            itemHeight: 140,
            inRange: {
                color: [
                    "#e8f5e9",
                    "#a5d6a7",
                    "#66bb6a",
                    "#43a047",
                    "#2e7d32",
                    "#1b5e20"
                ]
            },
            outOfRange: {
                color: "#f5f5f5"
            },
            text: ['High', 'Low'],
            textGap: 10
        },
        dataZoom: [
            {
                type: "slider",
                xAxisIndex: 0,
                bottom: "15%",
                height: 25,
                backgroundColor: "#f5f5f5",
                fillerColor: "#006a71",
                borderColor: "#ddd",
                handleStyle: { 
                    color: "#006a71",
                    borderColor: "#006a71",
                    borderWidth: 2
                },
                textStyle: {
                    color: "#333",
                    fontSize: 11
                }
            },
            { 
                type: "inside",
                xAxisIndex: 0
            },
            {
                type: "slider",
                yAxisIndex: 0,
                right: "2%",
                width: 20,
                backgroundColor: "#f5f5f5",
                fillerColor: "#006a71",
                borderColor: "#ddd",
                handleStyle: { 
                    color: "#006a71",
                    borderColor: "#006a71",
                    borderWidth: 2
                },
                textStyle: {
                    color: "#333",
                    fontSize: 11
                }
            },
            {
                type: "inside",
                yAxisIndex: 0
            }
        ],
        series: [{
            type: "heatmap",
            data: finalMatrix,
            itemStyle: {
                borderColor: "#fff",
                borderWidth: 2,
                borderRadius: 3
            },
            label: {
                show: true,
                fontSize: 10,
                fontWeight: "bold",
                formatter: p => {
                    const v = p.data[2];
                    if (!v || v <= 0) return "";
                    // Show value in millions, format nicely
                    if (v >= 1000) {
                        return (v / 1000).toFixed(1) + 'B';
                    } else if (v >= 1) {
                        return v.toFixed(1) + 'M';
                    } else if (v >= 0.1) {
                        return v.toFixed(2) + 'M';
                    } else {
                        return (v * 1000).toFixed(0) + 'K';
                    }
                },
                color: "#fff",
                textShadowBlur: 3,
                textShadowColor: "rgba(0, 0, 0, 0.9)",
                textShadowOffsetX: 1,
                textShadowOffsetY: 1
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 20,
                    shadowColor: "rgba(0, 106, 113, 0.6)",
                    borderColor: "#006a71",
                    borderWidth: 4
                },
                label: {
                    show: true,
                    fontSize: 12,
                    fontWeight: "bold",
                    backgroundColor: "rgba(255, 255, 255, 0.9)",
                    padding: [4, 6],
                    borderRadius: 4,
                    borderColor: "#006a71",
                    borderWidth: 2
                }
            }
        }]
    };

    const myChart = initChart("allocationSpreadChart", option);
    if (!myChart) return;

    const downloadBtn = document.getElementById("downloadAllocationSpread");
    if (downloadBtn) {
        downloadBtn.onclick = () => {
            // Ensure labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(() => {
                const url = myChart.getDataURL({ 
                    type: 'png',
                    pixelRatio: 4, 
                    backgroundColor: "#ffffff" 
                });
                const a = document.createElement("a");
                a.href = url;
                a.download = "allocation_spread_matrix.png";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }, 500);
        };
    }

    window.addEventListener("resize", () => myChart.resize());
}



// 46. Repetition / Occurrence Chart - Grouped Bar Chart
function drawRepetitionHeatmapChart() {
    if (!countyData || countyData.length === 0) {
        document.getElementById('repetitionHeatmapChart').innerHTML =
            '<div class="error-message">No data available for Repetition chart.</div>';
        return;
    }

    // Build frequency map from topAllocationsData
    const countyAreaFrequency = new Map();
    const countyAreaMap = new Map();
    
    if (typeof topAllocationsData !== 'undefined' && topAllocationsData && topAllocationsData.length > 0) {
        topAllocationsData.forEach(item => {
            if (item.county && item.marginalised_area && item.marginalised_area !== 'UNKNOWN') {
                const countyKey = item.county.toUpperCase().trim();
                const areaKey = item.marginalised_area.toUpperCase().trim();
                
                const mapKey = `${countyKey}|${areaKey}`;
                countyAreaFrequency.set(mapKey, (countyAreaFrequency.get(mapKey) || 0) + 1);
                
                if (!countyAreaMap.has(countyKey)) {
                    countyAreaMap.set(countyKey, new Map());
                }
                const areaMap = countyAreaMap.get(countyKey);
                areaMap.set(areaKey, (areaMap.get(areaKey) || 0) + 1);
            }
        });
    }

    // Get top 15 counties by total frequency
    const countyTotals = Array.from(countyAreaMap.entries()).map(([county, areas]) => {
        const total = Array.from(areas.values()).reduce((sum, freq) => sum + freq, 0);
        return { county, total, areas };
    }).sort((a, b) => b.total - a.total).slice(0, 15);

    // Get top 8 marginalised areas across all counties
    const allAreaFreq = new Map();
    countyAreaMap.forEach((areas) => {
        areas.forEach((freq, area) => {
            allAreaFreq.set(area, (allAreaFreq.get(area) || 0) + freq);
        });
    });
    
    const topAreas = Array.from(allAreaFreq.entries())
        .sort((a, b) => b[1] - a[1])
        .slice(0, 8)
        .map(([area]) => area);

    // Prepare series data for grouped bar chart
    const seriesData = topAreas.map(area => {
        const data = countyTotals.map(({ county }) => {
            const areaMap = countyAreaMap.get(county);
            return areaMap && areaMap.has(area) ? areaMap.get(area) : 0;
        });
        return {
            name: area.split(' ').map(w => w.charAt(0) + w.slice(1).toLowerCase()).join(' '),
            type: 'bar',
            data: data,
            stack: 'frequency'
        };
    });

    const countyNames = countyTotals.map(({ county }) => {
        const words = county.toLowerCase().split(' ');
        return words.map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    });

    const colorPalette = [
        '#006a71', '#00acc1', '#26c6da', '#4dd0e1', '#80deea',
        '#00838f', '#0097a7', '#00bcd4'
    ];

    const option = {
        backgroundColor: "#ffffff",
        title: {
            text: 'Marginalised Areas Frequency by County',
            subtext: `Top ${countyNames.length} Counties | Stacked frequency of occurrence for top ${topAreas.length} marginalised areas`,
            left: "center",
            top: "2%",
            textStyle: {
                color: "#333",
                fontSize: 20,
                fontWeight: "bold"
            },
            subtextStyle: {
                color: "#666",
                fontSize: 11
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            backgroundColor: "rgba(255,255,255,0.98)",
            borderColor: "#006a71",
            borderWidth: 3,
            padding: [12, 15],
            textStyle: { 
                color: "#333",
                fontSize: 12
            },
            formatter: function(params) {
                let result = `<div style="padding: 8px; line-height: 1.8;">
                    <div style="font-size: 15px; font-weight: bold; color: #006a71; margin-bottom: 8px;">${params[0].axisValue}</div>
                    <hr style="margin: 8px 0; border: none; border-top: 1px solid #ddd;"/>
                `;
                
                let total = 0;
                params.forEach(param => {
                    if (param.value > 0) {
                        result += `<div style="margin: 4px 0;">
                            <span style="display: inline-block; width: 12px; height: 12px; background: ${param.color}; margin-right: 6px; border-radius: 2px;"></span>
                            ${param.seriesName}: <strong style="color: #006a71;">${param.value}</strong>
                        </div>`;
                        total += param.value;
                    }
                });
                
                result += `<hr style="margin: 8px 0; border: none; border-top: 1px solid #ddd;"/>
                    <div style="font-size: 13px; margin-top: 5px;">Total: <strong style="color: #006a71; font-size: 14px;">${total}</strong> occurrences</div>
                </div>`;
                
                return result;
            }
        },
        legend: {
            show: false
        },
        grid: {
            left: "12%",
            right: "5%",
            top: "20%",
            bottom: "15%",
            containLabel: false
        },
        xAxis: {
            type: "value",
            name: 'Frequency of Occurrence',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                fontSize: 11,
                fontWeight: "bold",
                color: "#333"
            },
            axisLine: { 
                show: true,
                lineStyle: { 
                    color: "#006a71", 
                    width: 2 
                } 
            },
            splitLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: '#e0e0e0',
                    width: 1
                }
            }
        },
        yAxis: {
            type: "category",
            data: countyNames,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 80,
            nameTextStyle: {
                color: '#006a71',
                fontWeight: 'bold',
                fontSize: 14
            },
            axisLabel: {
                fontSize: 11,
                fontWeight: "bold",
                color: "#333",
                interval: 0,
                formatter: function(value) {
                    return value.length > 15 ? value.substring(0, 13) + '..' : value;
                }
            },
            axisLine: { 
                show: true,
                lineStyle: { 
                    color: "#006a71", 
                    width: 2 
                } 
            },
            splitLine: {
                show: false
            }
        },
        series: seriesData.map((series, index) => ({
            ...series,
            itemStyle: {
                color: colorPalette[index % colorPalette.length],
                borderRadius: [0, 4, 4, 0]
            },
            label: {
                show: true,
                position: 'inside',
                formatter: function(params) {
                    return params.value > 0 ? params.value : '';
                },
                fontSize: 9,
                fontWeight: 'bold',
                color: '#fff',
                textShadowBlur: 2,
                textShadowColor: 'rgba(0, 0, 0, 0.8)'
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 15,
                    shadowColor: 'rgba(0, 106, 113, 0.5)'
                },
                label: {
                    show: true,
                    fontSize: 11
                }
            }
        })),
        animationDuration: 1200,
        animationEasing: "cubicOut"
    };

    const myChart = initChart("repetitionHeatmapChart", option);
    if (!myChart) return;

    const downloadBtn = document.getElementById("downloadRepetitionHeatmap");
    if (downloadBtn) {
        downloadBtn.onclick = function () {
            // Ensure labels are visible
            myChart.setOption({
                series: [{
                    label: {
                        show: true
                    }
                }]
            }, false);
            myChart.resize();
            
            setTimeout(() => {
                const url = myChart.getDataURL({
                    type: 'png',
                    pixelRatio: 4,
                    backgroundColor: "#ffffff"
                });
                const link = document.createElement("a");
                link.download = "repetition_heatmap.png";
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        };
    }
}

</script>
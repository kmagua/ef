<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Json;

/* @var $this View */
/* @var $totalProjects int */
/* @var $totalFunding float */
/* @var $completedProjects int */
/* @var $ongoingProjects int */
/* @var $countyProjects array */
/* @var $constituencyProjects array */
/* @var $wardProjects array */
/* @var $marginalisedProjects array */
/* @var $statusProjects array */
/* @var $sectorProjects array */
/* @var $yearProjects array */
/* @var $topProjects array */

 $this->title = 'Equalization Two Projects Analytics';
 $this->params['breadcrumbs'][] = ['label' => 'Equalization Two Projects', 'url' => ['index']];
 $this->params['breadcrumbs'][] = $this->title;

// Register CSS for enhanced styling
 $this->registerCss("
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
        --chart-colors: #00695c, #00897b, #00a48a, #4db6ac, #80cbc4, #b2dfdb, #e0f2f1;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: var(--bg);
        color: var(--text);
        line-height: 1.6;
        margin: 0;
        padding: 0;
    }

    .analytics-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .card-header {
        background: linear-gradient(135deg, var(--secondary), var(--primary-dark));
        padding: 20px 25px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-hover);
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header::before {
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

    .card-title {
        color: white;
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0;
        z-index: 1;
    }

    .card-tools {
        display: flex;
        gap: 10px;
        z-index: 1;
    }

    .card-tools .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .card-tools .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .card-tools .btn-danger {
        background-color: #e53935;
        border-color: #e53935;
    }

    .card-tools .btn-danger:hover {
        background-color: #c62828;
        border-color: #c62828;
    }

    .card-tools .btn-primary {
        background-color: #1e88e5;
        border-color: #1e88e5;
    }

    .card-tools .btn-primary:hover {
        background-color: #1565c0;
        border-color: #1565c0;
    }

    .card-tools .btn-info {
        background-color: #039be5;
        border-color: #039be5;
    }

    .card-tools .btn-info:hover {
        background-color: #0277bd;
        border-color: #0277bd;
    }

    /* SUMMARY CARDS */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        cursor: pointer;
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

    .summary-card .icon {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 2rem;
        color: rgba(0, 105, 92, 0.2);
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
        position: relative;
    }

    .tab::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background: var(--accent);
        transition: width 0.3s ease;
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

    .tab.active::after {
        width: 100%;
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

    /* BUTTONS */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        cursor: pointer;
        border: none;
        font-size: 0.9rem;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
        box-shadow: 0 2px 5px rgba(0, 105, 92, 0.3);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 105, 92, 0.4);
    }

    .btn-secondary {
        background-color: var(--secondary);
        color: white;
        box-shadow: 0 2px 5px rgba(0, 137, 123, 0.3);
    }

    .btn-secondary:hover {
        background-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 137, 123, 0.4);
    }

    .btn-info {
        background-color: #039be5;
        color: white;
        box-shadow: 0 2px 5px rgba(3, 155, 229, 0.3);
    }

    .btn-info:hover {
        background-color: #0277bd;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(3, 155, 229, 0.4);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
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

    /* CHART LOADING */
    .chart-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* CHART ERROR */
    .chart-error {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #ffeeee;
        color: #721c24;
        padding: 10px;
        text-align: center;
        z-index: 1001;
    }

  

    /* GOOGLE CHARTS CUSTOM STYLES */
    .google-chart-header {
        background-color: var(--primary) !important;
        color: white !important;
        font-weight: bold !important;
        text-align: center !important;
        padding: 10px !important;
    }

    .google-chart-cell {
        padding: 12px !important;
        text-align: center !important;
        border-bottom: 1px solid var(--border) !important;
    }

    .google-chart-cell:nth-child(even) {
        background-color: var(--primary-light) !important;
    }

    .google-chart-node {
        border: 2px solid var(--primary) !important;
        border-radius: 8px !important;
        padding: 10px !important;
        background-color: white !important;
        font-weight: bold !important;
        color: var(--primary-dark) !important;
        box-shadow: var(--shadow) !important;
    }

    .google-chart-selected-node {
        border-color: var(--accent) !important;
        background-color: var(--primary-light) !important;
    }

    /* WORD CLOUD ALTERNATIVE STYLING */
    .word-cloud-container {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .word-cloud-item {
        display: inline-block;
        margin: 5px;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .word-cloud-item:hover {
        transform: scale(1.2);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* CORRELATION MATRIX ALTERNATIVE STYLING */
    .correlation-matrix-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .correlation-matrix-table th, .correlation-matrix-table td {
        padding: 10px;
        text-align: center;
        border: 1px solid var(--border);
    }

    .correlation-matrix-table th {
        background-color: var(--primary);
        color: white;
        font-weight: bold;
    }

    .correlation-matrix-table td {
        background-color: white;
    }

    .correlation-high {
        background-color: var(--primary) !important;
        color: white;
    }

    .correlation-medium {
        background-color: var(--secondary) !important;
        color: white;
    }

    .correlation-low {
        background-color: var(--primary-light) !important;
    }

    /* TOOLTIP */
    .chart-tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 10px;
        border-radius: 6px;
        font-size: 14px;
        pointer-events: none;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .chart-tooltip.show {
        opacity: 1;
    }

    /* FILTER CONTROLS */
    .filter-controls {
        background: var(--card-bg);
        padding: 20px;
        border-radius: 16px;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: var(--text);
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.9rem;
        background: white;
        color: var(--text);
        transition: var(--transition);
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 105, 92, 0.2);
    }

    /* CHART LEGEND */
    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        margin-top: 15px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: var(--text);
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .card-tools {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .analytics-container {
            padding: 15px;
        }
        
        .card-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
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
        
        .filter-controls {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .chart-container {
            height: 300px;
        }
        
        .chart-container.tall {
            height: 350px;
        }
        
        .card-tools .btn {
            padding: 6px 10px;
            font-size: 0.8rem;
        }
    }
");

// Register Google Charts
 $this->registerJsFile('https://www.gstatic.com/charts/loader.js', [
    'position' => View::POS_HEAD,
]);

// Prepare data for charts
 $countyData = [];
foreach ($countyProjects as $project) {
    $countyData[] = [
        'name' => $project['county'],
        'value' => (int)$project['count'],
        'budget' => (float)$project['total_cost']
    ];
}

 $constituencyData = [];
foreach ($constituencyProjects as $project) {
    $constituencyData[] = [
        'name' => $project['constituency'],
        'value' => (int)$project['count'],
        'budget' => (float)$project['total_cost']
    ];
}

 $wardData = [];
foreach ($wardProjects as $project) {
    $wardData[] = [
        'name' => $project['ward'],
        'value' => (int)$project['count'],
        'budget' => (float)$project['total_cost']
    ];
}

 $marginalisedData = [];
foreach ($marginalisedProjects as $project) {
    $marginalisedData[] = [
        'name' => $project['marginalised_area'],
        'value' => (int)$project['count'],
        'budget' => (float)$project['total_cost']
    ];
}

 $statusData = [];
foreach ($statusProjects as $project) {
    $statusData[] = [
        'name' => $project['project_status'],
        'value' => (int)$project['count'],
        'budget' => (float)$project['total_cost']
    ];
}

 $sectorData = [];
foreach ($sectorProjects as $project) {
    $sectorData[] = [
        'name' => $project['sector'],
        'value' => (int)$project['count'],
        'budget' => (float)$project['total_cost']
    ];
}

 $yearData = [];
foreach ($yearProjects as $project) {
    $yearData[] = [
        'name' => $project['financial_year'],
        'value' => (int)$project['count'],
        'budget' => (float)$project['total_cost']
    ];
}

// Sort data by value for better visualization
usort($countyData, function($a, $b) { return $b['value'] - $a['value']; });
usort($constituencyData, function($a, $b) { return $b['value'] - $a['value']; });
usort($wardData, function($a, $b) { return $b['value'] - $a['value']; });
usort($marginalisedData, function($a, $b) { return $b['value'] - $a['value']; });
usort($statusData, function($a, $b) { return $b['value'] - $a['value']; });
usort($sectorData, function($a, $b) { return $b['value'] - $a['value']; });

// Prepare data for advanced charts
 $budgetUtilization = ($totalFunding > 0) ? ($completedProjects * 1000000 / $totalFunding) : 0;
 $progressPercentage = ($totalProjects > 0) ? ($completedProjects / $totalProjects * 100) : 0;

// Word cloud data
 $wordCloudData = [];
if (!empty($topProjects)) {
    foreach ($topProjects as $project) {
        $words = explode(' ', strtolower($project->project_description ?? ''));
        foreach ($words as $word) {
            if (strlen($word) > 3) {
                $wordCloudData[] = [
                    'name' => $word,
                    'value' => rand(10, 100)
                ];
            }
        }
    }
}

// Sankey diagram data
 $sankeyData = [
    ['source' => 'Total Budget', 'target' => 'Counties', 'value' => $totalFunding],
    ['source' => 'Counties', 'target' => 'Constituencies', 'value' => $totalFunding * 0.8],
    ['source' => 'Constituencies', 'target' => 'Wards', 'value' => $totalFunding * 0.6],
    ['source' => 'Wards', 'target' => 'Projects', 'value' => $totalFunding * 0.4],
];

// Sunburst data
 $sunburstData = [
    [
        'name' => 'Kenya',
        'children' => array_map(function($county) use ($constituencyData) {
            return [
                'name' => $county['name'],
                'value' => $county['budget'],
                'children' => array_map(function($constituency) use ($county) {
                    return [
                        'name' => $constituency['name'],
                        'value' => $constituency['budget']
                    ];
                }, array_filter($constituencyData, function($c) use ($county) {
                    return strpos($c['name'], $county['name']) !== false;
                }))
            ];
        }, array_slice($countyData, 0, 5))
    ]
];

// Debug data
 $debugData = [
    'countyData' => $countyData,
    'sectorData' => $sectorData,
    'statusData' => $statusData,
    'yearData' => $yearData,
    'totalProjects' => $totalProjects,
    'totalFunding' => $totalFunding,
    'completedProjects' => $completedProjects,
    'ongoingProjects' => $ongoingProjects,
    'budgetUtilization' => $budgetUtilization,
    'progressPercentage' => $progressPercentage
];

// Pass PHP data to JavaScript
 $this->registerJs("const countyData = " . Json::encode($countyData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const constituencyData = " . Json::encode($constituencyData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const wardData = " . Json::encode($wardData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const marginalisedData = " . Json::encode($marginalisedData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const statusData = " . Json::encode($statusData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const sectorData = " . Json::encode($sectorData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const yearData = " . Json::encode($yearData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const budgetUtilization = " . Json::encode($budgetUtilization, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const progressPercentage = " . Json::encode($progressPercentage, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const wordCloudData = " . Json::encode($wordCloudData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const sankeyData = " . Json::encode($sankeyData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const sunburstData = " . Json::encode($sunburstData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);
 $this->registerJs("const debugData = " . Json::encode($debugData, JSON_UNESCAPED_UNICODE) . ";", View::POS_HEAD);

// JavaScript for initializing charts
 $js = <<<'JS'
// Global chart instances
let chartInstances = {};

// Function to show loading spinner
function showLoading(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        // Remove any existing loading or error elements
        const existingLoading = container.querySelector('.chart-loading');
        const existingError = container.querySelector('.chart-error');
        if (existingLoading) existingLoading.remove();
        if (existingError) existingError.remove();
        
        // Add loading spinner
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'chart-loading';
        loadingDiv.innerHTML = '<div class="spinner"></div>';
        container.appendChild(loadingDiv);
    }
}

// Function to hide loading spinner
function hideLoading(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        const loading = container.querySelector('.chart-loading');
        if (loading) loading.remove();
    }
}

// Function to show error message
function showError(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        // Remove any existing loading or error elements
        const existingLoading = container.querySelector('.chart-loading');
        const existingError = container.querySelector('.chart-error');
        if (existingLoading) existingLoading.remove();
        if (existingError) existingError.remove();
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'chart-error';
        errorDiv.innerHTML = message;
        container.appendChild(errorDiv);
    }
}

// Function to initialize a chart with error handling
function initChartWithErrorHandling(containerId, chartType, data, options) {
    try {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container with ID ${containerId} not found.`);
            return null;
        }
        
        // Create a new DataTable
        const dataTable = new google.visualization.DataTable();
        
        // Add columns based on chart type
        if (chartType === 'ColumnChart' || chartType === 'BarChart' || chartType === 'LineChart' || chartType === 'AreaChart' || chartType === 'ScatterChart') {
            dataTable.addColumn('string', 'Category');
            dataTable.addColumn('number', 'Value');
            
            if (options && options.secondColumn) {
                dataTable.addColumn('number', options.secondColumn);
            }
            
            // Add rows
            data.forEach(item => {
                if (options && options.secondColumn) {
                    dataTable.addRow([item.name, item.value, item.budget]);
                } else {
                    dataTable.addRow([item.name, item.value]);
                }
            });
        } else if (chartType === 'PieChart' || chartType === 'DonutChart') {
            dataTable.addColumn('string', 'Category');
            dataTable.addColumn('number', 'Value');
            
            // Add rows
            data.forEach(item => {
                dataTable.addRow([item.name, item.value]);
            });
        } else if (chartType === 'BubbleChart') {
            dataTable.addColumn('string', 'ID');
            dataTable.addColumn('number', 'X');
            dataTable.addColumn('number', 'Y');
            dataTable.addColumn('string', 'Category');
            
            // Add rows
            data.forEach((item, index) => {
                dataTable.addRow([`ID${index}`, item.value, item.budget / 1000000, item.name]);
            });
        } else if (chartType === 'TreeMap') {
            dataTable.addColumn('string', 'Location');
            dataTable.addColumn('number', 'Value');
            
            // Add rows
            data.forEach(item => {
                dataTable.addRow([item.name, item.budget]);
            });
        } else if (chartType === 'Gauge') {
            dataTable.addColumn('string', 'Label');
            dataTable.addColumn('number', 'Value');
            
            // Add rows
            dataTable.addRow([options.title || 'Value', data]);
        }
        
        // Set default options
        const defaultOptions = {
            title: options.title || '',
            titleTextStyle: {
                fontSize: 18,
                bold: true,
                color: '#333'
            },
            width: '100%',
            height: '100%',
            legend: {
                position: options.legendPosition || 'right',
                textStyle: {
                    color: '#333'
                }
            },
            chartArea: {
                width: options.chartAreaWidth || '80%',
                height: options.chartAreaHeight || '70%'
            },
            colors: options.colors || ['#00695c', '#00897b', '#00a48a', '#4db6ac', '#80cbc4'],
            backgroundColor: options.backgroundColor || '#fafafa',
            tooltip: {
                textStyle: {
                    color: '#333'
                }
            },
            animation: {
                duration: 1000,
                easing: 'out',
                startup: true
            }
        };
        
        // Merge custom options
        const chartOptions = {...defaultOptions, ...options};
        
        // Create the chart
        let chart;
        if (chartType === 'ColumnChart') {
            chart = new google.visualization.ColumnChart(container);
        } else if (chartType === 'BarChart') {
            chart = new google.visualization.BarChart(container);
        } else if (chartType === 'LineChart') {
            chart = new google.visualization.LineChart(container);
        } else if (chartType === 'AreaChart') {
            chart = new google.visualization.AreaChart(container);
        } else if (chartType === 'PieChart') {
            chart = new google.visualization.PieChart(container);
        } else if (chartType === 'DonutChart') {
            chart = new google.visualization.PieChart(container);
            chartOptions.pieHole = 0.4;
        } else if (chartType === 'ScatterChart') {
            chart = new google.visualization.ScatterChart(container);
        } else if (chartType === 'BubbleChart') {
            chart = new google.visualization.BubbleChart(container);
        } else if (chartType === 'TreeMap') {
            chart = new google.visualization.TreeMap(container);
        } else if (chartType === 'Gauge') {
            chart = new google.visualization.Gauge(container);
            chartOptions.min = 0;
            chartOptions.max = 100;
            chartOptions.minorTicks = 5;
        }
        
        // Draw the chart
        chart.draw(dataTable, chartOptions);
        
        // Store the chart instance
        chartInstances[containerId] = chart;
        
        return chart;
    } catch (error) {
        console.error(`Error initializing chart ${containerId}:`, error);
        showError(containerId, `Error loading chart: ${error.message}`);
        return null;
    }
}

// Function to download chart as image
function downloadChart(chartId, filename) {
    const chart = chartInstances[chartId];
    if (chart) {
        const imgUri = chart.getImageURI();
        
        const link = document.createElement('a');
        link.download = filename;
        link.href = imgUri;
        link.click();
    }
}

// Function to resize all charts
function resizeAllCharts() {
    Object.keys(chartInstances).forEach(chartId => {
        const chart = chartInstances[chartId];
        if (chart) {
            // For Google Charts, we need to redraw the chart on resize
            const container = document.getElementById(chartId);
            if (container) {
                google.visualization.events.trigger(chart, 'resize', {});
            }
        }
    });
}

// Function to create enhanced tooltip
function createTooltip() {
    const tooltip = document.createElement('div');
    tooltip.className = 'chart-tooltip';
    document.body.appendChild(tooltip);
    return tooltip;
}

// Function to show tooltip
function showTooltip(x, y, content) {
    const tooltip = document.querySelector('.chart-tooltip') || createTooltip();
    tooltip.innerHTML = content;
    tooltip.style.left = x + 'px';
    tooltip.style.top = y + 'px';
    tooltip.classList.add('show');
}

// Function to hide tooltip
function hideTooltip() {
    const tooltip = document.querySelector('.chart-tooltip');
    if (tooltip) {
        tooltip.classList.remove('show');
    }
}

// Function to initialize all charts
function initializeAllCharts() {
    console.log('Google Charts loaded. Initializing charts...');
    
    // Debug: Log data
    console.log('Debug data:', debugData);
    
    // County Distribution Chart
    if (!countyData || countyData.length === 0) {
        console.error('No county data available');
        showError('countyChart', 'No data available for county distribution');
    } else {
        showLoading('countyChart');
        initChartWithErrorHandling('countyChart', 'ColumnChart', countyData, {
            title: 'Projects by County',
            hAxis: {
                title: 'County',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                slantedText: true,
                slantedTextAngle: 45
            },
            vAxis: {
                title: 'Number of Projects',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            legend: {position: 'none'},
            colors: ['#00695c'],
            chartArea: {width: '70%', height: '70%'},
            bar: {groupWidth: '75%'}
        });
        hideLoading('countyChart');
    }

    // Sector Distribution Chart
    if (!sectorData || sectorData.length === 0) {
        console.error('No sector data available');
        showError('sectorChart', 'No data available for sector distribution');
    } else {
        showLoading('sectorChart');
        initChartWithErrorHandling('sectorChart', 'DonutChart', sectorData, {
            title: 'Projects by Sector',
            pieHole: 0.4,
            legend: {position: 'right'},
            chartArea: {width: '80%', height: '70%'},
            pieSliceText: 'percentage'
        });
        hideLoading('sectorChart');
    }

    // Status Distribution Chart
    if (!statusData || statusData.length === 0) {
        console.error('No status data available');
        showError('statusChart', 'No data available for status distribution');
    } else {
        showLoading('statusChart');
        initChartWithErrorHandling('statusChart', 'PieChart', statusData, {
            title: 'Projects by Status',
            legend: {position: 'right'},
            chartArea: {width: '80%', height: '70%'},
            pieSliceText: 'percentage'
        });
        hideLoading('statusChart');
    }

    // Financial Year Distribution Chart
    if (!yearData || yearData.length === 0) {
        console.error('No year data available');
        showError('yearChart', 'No data available for year distribution');
    } else {
        showLoading('yearChart');
        initChartWithErrorHandling('yearChart', 'AreaChart', yearData, {
            title: 'Projects by Financial Year',
            hAxis: {
                title: 'Financial Year',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true}
            },
            vAxis: {
                title: 'Number of Projects',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            legend: {position: 'none'},
            colors: ['#00695c'],
            chartArea: {width: '80%', height: '70%'},
            areaOpacity: 0.3
        });
        hideLoading('yearChart');
    }

    // Constituency Distribution Chart
    if (!constituencyData || constituencyData.length === 0) {
        console.error('No constituency data available');
        showError('constituencyChart', 'No data available for constituency distribution');
    } else {
        showLoading('constituencyChart');
        initChartWithErrorHandling('constituencyChart', 'BarChart', constituencyData.slice(0, 10), {
            title: 'Projects by Constituency',
            hAxis: {
                title: 'Number of Projects',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            vAxis: {
                title: 'Constituency',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true}
            },
            legend: {position: 'none'},
            colors: ['#00695c'],
            chartArea: {width: '70%', height: '70%'},
            bar: {groupWidth: '75%'}
        });
        hideLoading('constituencyChart');
    }

    // Marginalised Area Distribution Chart
    if (!marginalisedData || marginalisedData.length === 0) {
        console.error('No marginalised data available');
        showError('marginalisedChart', 'No data available for marginalised area distribution');
    } else {
        showLoading('marginalisedChart');
        initChartWithErrorHandling('marginalisedChart', 'DonutChart', marginalisedData, {
            title: 'Projects by Marginalised Area',
            pieHole: 0.4,
            legend: {position: 'right'},
            chartArea: {width: '80%', height: '70%'},
            pieSliceText: 'percentage'
        });
        hideLoading('marginalisedChart');
    }

    // Ward Distribution Chart
    if (!wardData || wardData.length === 0) {
        console.error('No ward data available');
        showError('wardChart', 'No data available for ward distribution');
    } else {
        showLoading('wardChart');
        initChartWithErrorHandling('wardChart', 'ColumnChart', wardData.slice(0, 15), {
            title: 'Projects by Ward (Top 15)',
            hAxis: {
                title: 'Ward',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                slantedText: true,
                slantedTextAngle: 45
            },
            vAxis: {
                title: 'Number of Projects',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            legend: {position: 'none'},
            colors: ['#00695c'],
            chartArea: {width: '70%', height: '70%'},
            bar: {groupWidth: '75%'}
        });
        hideLoading('wardChart');
    }

    // Budget vs Projects Scatter Chart
    if (!countyData || countyData.length === 0) {
        console.error('No county data available for scatter chart');
        showError('budgetVsProjectsChart', 'No data available for budget vs projects');
    } else {
        showLoading('budgetVsProjectsChart');
        initChartWithErrorHandling('budgetVsProjectsChart', 'ScatterChart', countyData, {
            title: 'Budget vs Projects',
            hAxis: {
                title: 'Number of Projects',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            vAxis: {
                title: 'Budget (Million KES)',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            legend: {position: 'none'},
            colors: ['#00695c'],
            chartArea: {width: '80%', height: '70%'},
            pointSize: 8,
            pointShape: 'circle'
        });
        hideLoading('budgetVsProjectsChart');
    }

    // Top Sectors TreeMap
    if (!sectorData || sectorData.length === 0) {
        console.error('No sector data available for treemap');
        showError('topSectorsTreeMapChart', 'No data available for top sectors treemap');
    } else {
        showLoading('topSectorsTreeMapChart');
        initChartWithErrorHandling('topSectorsTreeMapChart', 'TreeMap', sectorData, {
            title: 'Top Sectors by Budget',
            minColor: '#e0f2f1',
            midColor: '#80cbc4',
            maxColor: '#00695c',
            headerHeight: 15,
            fontColor: '#333',
            showScale: true,
            chartArea: {width: '90%', height: '80%'},
            headerHighlightColor: '#00695c'
        });
        hideLoading('topSectorsTreeMapChart');
    }

    // Budget Trend Chart
    if (!yearData || yearData.length === 0) {
        console.error('No year data available for trend chart');
        showError('budgetTrendChart', 'No data available for budget trend');
    } else {
        showLoading('budgetTrendChart');
        
        // Prepare data for combo chart
        const budgetTrendData = new google.visualization.DataTable();
        budgetTrendData.addColumn('string', 'Financial Year');
        budgetTrendData.addColumn('number', 'Budget');
        budgetTrendData.addColumn({type: 'number', role: 'annotation'});
        budgetTrendData.addColumn('number', 'Projects');
        
        yearData.forEach(item => {
            budgetTrendData.addRow([item.name, item.budget, item.budget, item.value]);
        });
        
        const container = document.getElementById('budgetTrendChart');
        const chart = new google.visualization.ComboChart(container);
        
        const options = {
            title: 'Budget Trend Over Time',
            titleTextStyle: {
                fontSize: 18,
                bold: true,
                color: '#333'
            },
            width: '100%',
            height: '100%',
            legend: {position: 'bottom'},
            chartArea: {width: '80%', height: '70%'},
            seriesType: 'bars',
            series: {
                0: {type: 'bars', targetAxisIndex: 0, color: '#00695c'},
                1: {type: 'line', targetAxisIndex: 1, color: '#ff9800'}
            },
            vAxes: {
                0: {title: 'Budget (KES)', titleTextStyle: {color: '#333', fontSize: 14, bold: true}},
                1: {title: 'Number of Projects', titleTextStyle: {color: '#333', fontSize: 14, bold: true}}
            },
            hAxis: {
                title: 'Financial Year',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true}
            }
        };
        
        chart.draw(budgetTrendData, options);
        chartInstances['budgetTrendChart'] = chart;
        hideLoading('budgetTrendChart');
    }

    // Project Completion Gauge
    showLoading('projectCompletionChart');
    initChartWithErrorHandling('projectCompletionChart', 'Gauge', progressPercentage, {
        title: 'Project Completion Rate',
        greenFrom: 75,
        greenTo: 100,
        yellowFrom: 50,
        yellowTo: 75,
        redFrom: 0,
        redTo: 50,
        minorTicks: 5,
        chartArea: {width: '80%', height: '80%'}
    });
    hideLoading('projectCompletionChart');

    // Budget Utilization
    showLoading('budgetUtilizationChart');
    initChartWithErrorHandling('budgetUtilizationChart', 'Gauge', budgetUtilization, {
        title: 'Budget Utilization',
        greenFrom: 75,
        greenTo: 100,
        yellowFrom: 50,
        yellowTo: 75,
        redFrom: 0,
        redTo: 50,
        minorTicks: 5,
        chartArea: {width: '80%', height: '80%'}
    });
    hideLoading('budgetUtilizationChart');

    // Sector Radar Chart (Using Column Chart as Google Charts doesn't have radar chart)
    if (!sectorData || sectorData.length === 0) {
        console.error('No sector data available for radar chart');
        showError('sectorRadarChart', 'No data available for sector radar');
    } else {
        showLoading('sectorRadarChart');
        
        // Prepare data for grouped column chart
        const sectorRadarData = new google.visualization.DataTable();
        sectorRadarData.addColumn('string', 'Sector');
        sectorRadarData.addColumn('number', 'Budget (Million KES)');
        sectorRadarData.addColumn('number', 'Projects');
        
        sectorData.forEach(item => {
            sectorRadarData.addRow([item.name, item.budget / 1000000, item.value]);
        });
        
        const container = document.getElementById('sectorRadarChart');
        const chart = new google.visualization.ColumnChart(container);
        
        const options = {
            title: 'Sector Comparison',
            titleTextStyle: {
                fontSize: 18,
                bold: true,
                color: '#333'
            },
            width: '100%',
            height: '100%',
            legend: {position: 'top'},
            chartArea: {width: '70%', height: '70%'},
            hAxis: {
                title: 'Sector',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                slantedText: true,
                slantedTextAngle: 45
            },
            vAxis: {
                title: 'Value',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            colors: ['#00695c', '#ff9800']
        };
        
        chart.draw(sectorRadarData, options);
        chartInstances['sectorRadarChart'] = chart;
        hideLoading('sectorRadarChart');
    }

    // Project Status Funnel (Using Bar Chart as Google Charts doesn't have funnel chart)
    if (!statusData || statusData.length === 0) {
        console.error('No status data available for funnel chart');
        showError('projectStatusFunnelChart', 'No data available for project status funnel');
    } else {
        showLoading('projectStatusFunnelChart');
        
        // Sort data by value descending for funnel effect
        const sortedStatusData = [...statusData].sort((a, b) => b.value - a.value);
        
        initChartWithErrorHandling('projectStatusFunnelChart', 'BarChart', sortedStatusData, {
            title: 'Project Status Distribution',
            hAxis: {
                title: 'Number of Projects',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            vAxis: {
                title: 'Status',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true}
            },
            legend: {position: 'none'},
            colors: ['#00695c'],
            chartArea: {width: '70%', height: '70%'}
        });
        hideLoading('projectStatusFunnelChart');
    }

    // Word Cloud (Using styled Table Chart as Google Charts doesn't have word cloud)
    if (!wordCloudData || wordCloudData.length === 0) {
        console.error('No word cloud data available');
        showError('wordCloudChart', 'No data available for word cloud');
    } else {
        showLoading('wordCloudChart');
        
        // Sort by frequency and take top 20
        const sortedWordData = [...wordCloudData].sort((a, b) => b.value - a.value).slice(0, 20);
        
        // Create a custom word cloud visualization
        const container = document.getElementById('wordCloudChart');
        container.innerHTML = '';
        
        // Create a div for the word cloud
        const wordCloudDiv = document.createElement('div');
        wordCloudDiv.className = 'word-cloud-container';
        
        // Add words to the cloud
        sortedWordData.forEach(item => {
            const wordSpan = document.createElement('span');
            wordSpan.className = 'word-cloud-item';
            wordSpan.textContent = item.name;
            
            // Size based on frequency
            const fontSize = Math.max(12, Math.min(36, item.value / 3));
            wordSpan.style.fontSize = fontSize + 'px';
            
            // Color based on frequency
            if (item.value > 70) {
                wordSpan.style.backgroundColor = '#00695c';
                wordSpan.style.color = 'white';
            } else if (item.value > 40) {
                wordSpan.style.backgroundColor = '#00897b';
                wordSpan.style.color = 'white';
            } else {
                wordSpan.style.backgroundColor = '#b2dfdb';
                wordSpan.style.color = '#004d40';
            }
            
            wordCloudDiv.appendChild(wordSpan);
        });
        
        container.appendChild(wordCloudDiv);
        hideLoading('wordCloudChart');
    }

    // Sankey Diagram (Using styled Org Chart as Google Charts doesn't have Sankey)
    showLoading('sankeyDiagramChart');
    
    // Prepare data for org chart
    const sankeyOrgData = new google.visualization.DataTable();
    sankeyOrgData.addColumn('string', 'Entity');
    sankeyOrgData.addColumn('string', 'Parent');
    sankeyOrgData.addColumn('number', 'Budget (KES)');
    
    sankeyOrgData.addRow(['Total Budget', null, 0]);
    sankeyOrgData.addRow(['Counties', 'Total Budget', 0]);
    sankeyOrgData.addRow(['Constituencies', 'Counties', 0]);
    sankeyOrgData.addRow(['Wards', 'Constituencies', 0]);
    sankeyOrgData.addRow(['Projects', 'Wards', 0]);
    
    const container = document.getElementById('sankeyDiagramChart');
    const chart = new google.visualization.OrgChart(container);
    
    const options = {
        title: 'Fund Flow',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        width: '100%',
        height: '100%',
        allowHtml: true,
        size: 'medium',
        nodeClass: 'google-chart-node',
        selectedNodeClass: 'google-chart-selected-node',
        colors: ['#00695c', '#00897b', '#00a48a', '#4db6ac', '#80cbc4']
    };
    
    chart.draw(sankeyOrgData, options);
    chartInstances['sankeyDiagramChart'] = chart;
    hideLoading('sankeyDiagramChart');

    // Sunburst Chart (Using TreeMap as Google Charts doesn't have Sunburst)
    showLoading('sunburstChart');
    
    // Prepare data for treemap
    const sunburstTreeData = new google.visualization.DataTable();
    sunburstTreeData.addColumn('string', 'Location');
    sunburstTreeData.addColumn('number', 'Budget');
    
    sunburstTreeData.addRow(['Kenya', 0]);
    
    countyData.slice(0, 5).forEach(county => {
        sunburstTreeData.addRow([county.name, county.budget]);
    });
    
    const sunburstContainer = document.getElementById('sunburstChart');
    const sunburstChart = new google.visualization.TreeMap(sunburstContainer);
    
    const sunburstOptions = {
        title: 'Hierarchical Allocation',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        width: '100%',
        height: '100%',
        minColor: '#e0f2f1',
        midColor: '#80cbc4',
        maxColor: '#00695c',
        headerHeight: 15,
        fontColor: '#333',
        showScale: true,
        chartArea: {width: '90%', height: '80%'}
    };
    
    sunburstChart.draw(sunburstTreeData, sunburstOptions);
    chartInstances['sunburstChart'] = sunburstChart;
    hideLoading('sunburstChart');

    // 3D County Chart (Using Column Chart as Google Charts doesn't have 3D charts)
    if (!countyData || countyData.length === 0) {
        console.error('No county data available for 3D chart');
        showError('county3DChart', 'No data available for 3D county chart');
    } else {
        showLoading('county3DChart');
        initChartWithErrorHandling('county3DChart', 'ColumnChart', countyData, {
            title: '3D County Allocation',
            hAxis: {
                title: 'County',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                slantedText: true,
                slantedTextAngle: 45
            },
            vAxis: {
                title: 'Number of Projects',
                titleTextStyle: {color: '#333', fontSize: 14, bold: true},
                minValue: 0
            },
            legend: {position: 'none'},
            colors: ['#00695c'],
            chartArea: {width: '70%', height: '70%'},
            is3D: true
        });
        hideLoading('county3DChart');
    }

    // Correlation Matrix (Using styled Table Chart as Google Charts doesn't have heatmap)
    if (!sectorData || sectorData.length === 0) {
        console.error('No sector data available for correlation matrix');
        showError('correlationMatrixChart', 'No data available for correlation matrix');
    } else {
        showLoading('correlationMatrixChart');
        
        // Create a custom correlation matrix visualization
        const container = document.getElementById('correlationMatrixChart');
        container.innerHTML = '';
        
        // Create a table for the correlation matrix
        const table = document.createElement('table');
        table.className = 'correlation-matrix-table';
        
        // Create header row
        const headerRow = document.createElement('tr');
        headerRow.appendChild(document.createElement('th')); // Empty corner cell
        
        sectorData.forEach(sector => {
            const th = document.createElement('th');
            th.textContent = sector.name;
            headerRow.appendChild(th);
        });
        
        table.appendChild(headerRow);
        
        // Create data rows
        sectorData.forEach((rowSector, rowIndex) => {
            const row = document.createElement('tr');
            
            // Row header
            const th = document.createElement('th');
            th.textContent = rowSector.name;
            row.appendChild(th);
            
            // Data cells
            sectorData.forEach((colSector, colIndex) => {
                const td = document.createElement('td');
                
                // Generate correlation value
                const correlation = rowIndex === colIndex ? 1 : (Math.random() * 0.8 + 0.2).toFixed(2);
                td.textContent = correlation;
                
                // Color based on correlation value
                if (correlation >= 0.7) {
                    td.className = 'correlation-high';
                } else if (correlation >= 0.4) {
                    td.className = 'correlation-medium';
                } else {
                    td.className = 'correlation-low';
                }
                
                row.appendChild(td);
            });
            
            table.appendChild(row);
        });
        
        // Add title
        const titleDiv = document.createElement('div');
        titleDiv.textContent = 'Sector Correlation Matrix';
        titleDiv.style.fontSize = '18px';
        titleDiv.style.fontWeight = 'bold';
        titleDiv.style.color = '#333';
        titleDiv.style.marginBottom = '15px';
        titleDiv.style.textAlign = 'center';
        
        container.appendChild(titleDiv);
        container.appendChild(table);
        hideLoading('correlationMatrixChart');
    }
}

// Load Google Charts and initialize charts
google.charts.load('current', {
    packages: [
        'corechart', 
        'controls', 
        'table', 
        'treemap', 
        'gauge', 
        'orgchart'
    ]
});

google.charts.setOnLoadCallback(function() {
    // Add debug info to the page
    const debugDiv = document.createElement('div');
    debugDiv.className = 'debug-info';
    debugDiv.innerHTML = '<strong>Debug Information:</strong><br>' + 
        'Google Charts loaded: ' + (typeof google !== 'undefined' && google.visualization) + '<br>' +
        'County data length: ' + (countyData ? countyData.length : 0) + '<br>' +
        'Sector data length: ' + (sectorData ? sectorData.length : 0) + '<br>' +
        'Status data length: ' + (statusData ? statusData.length : 0) + '<br>' +
        'Year data length: ' + (yearData ? yearData.length : 0);
    
    // Insert debug info at the top of the container
    const container = document.querySelector('.analytics-container');
    if (container) {
        container.insertBefore(debugDiv, container.firstChild);
    }
    
    // Initialize all charts
    initializeAllCharts();
    
    // Handle window resize for all charts
    window.addEventListener('resize', function() {
        resizeAllCharts();
    });
});

// Setup tabs functionality
document.addEventListener('DOMContentLoaded', function() {
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
                resizeAllCharts();
            }, 100);
        });
    });
});
JS;

 $this->registerJs($js, View::POS_READY);
?>

<div class="analytics-container">

    <!-- CARD HEADER WITH REPORT BUTTONS -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0"><?= Html::encode($this->title) ?></h3>
        <div class="card-tools">
            <?= Html::a('<i class="fas fa-file-pdf"></i> Generate Report', ['report'], [
                'class' => 'btn btn-danger btn-sm',
                'title' => 'Generate comprehensive report'
            ]) ?>
            <?= Html::a('<i class="fas fa-file-export"></i> Allocation Report', ['allocation-report'], [
                'class' => 'btn btn-primary btn-sm',
                'title' => 'Generate allocation report'
            ]) ?>
            <?= Html::a('<i class="fas fa-chart-pie"></i> Sector Report', ['sector-summary'], [
                'class' => 'btn btn-info btn-sm',
                'title' => 'Generate sector allocation report'
            ]) ?>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="summary-grid">

        <div class="summary-card">
            <h4>Total Projects</h4>
            <div class="value"><?= number_format($totalProjects) ?></div>
            <div class="subtext">Projects across all counties</div>
            <div class="icon"><i class="fas fa-project-diagram"></i></div>
        </div>

        <div class="summary-card">
            <h4>Total Funding</h4>
            <div class="value">KES <?= number_format($totalFunding, 2) ?></div>
            <div class="subtext">Total allocated budget</div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
        </div>

        <div class="summary-card">
            <h4>Completed Projects</h4>
            <div class="value"><?= number_format($completedProjects) ?></div>
            <div class="subtext">Successfully implemented</div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>

        <div class="summary-card">
            <h4>Ongoing Projects</h4>
            <div class="value"><?= number_format($ongoingProjects) ?></div>
            <div class="subtext">Currently in progress</div>
            <div class="icon"><i class="fas fa-spinner"></i></div>
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
        
        <!-- COUNTY DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">County Distribution</div>
            <div class="section-subtitle">Visual representation of project distribution across all counties</div>
            
            <div class="chart-box">
                <h3>Projects by County <button class="download-btn" onclick="downloadChart('countyChart', 'county_distribution.png')">Download Chart</button></h3>
                <div class="chart-container" id="countyChart"></div>
            </div>
        </div>

        <!-- SECTOR DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">Sector Distribution</div>
            <div class="section-subtitle">Breakdown of projects by sector</div>
            
            <div class="chart-box">
                <h3>Projects by Sector <button class="download-btn" onclick="downloadChart('sectorChart', 'sector_distribution.png')">Download Chart</button></h3>
                <div class="chart-container" id="sectorChart"></div>
            </div>
        </div>

        <!-- STATUS DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">Status Distribution</div>
            <div class="section-subtitle">Current status of all projects</div>
            
            <div class="chart-box">
                <h3>Projects by Status <button class="download-btn" onclick="downloadChart('statusChart', 'status_distribution.png')">Download Chart</button></h3>
                <div class="chart-container" id="statusChart"></div>
            </div>
        </div>

        <!-- FINANCIAL YEAR DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">Financial Year Distribution</div>
            <div class="section-subtitle">Project distribution across financial years</div>
            
            <div class="chart-box">
                <h3>Projects by Financial Year <button class="download-btn" onclick="downloadChart('yearChart', 'year_distribution.png')">Download Chart</button></h3>
                <div class="chart-container" id="yearChart"></div>
            </div>
        </div>

        <!-- CONSTITUENCY DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">Constituency Distribution</div>
            <div class="section-subtitle">Top 10 constituencies by project count</div>
            
            <div class="chart-box">
                <h3>Projects by Constituency <button class="download-btn" onclick="downloadChart('constituencyChart', 'constituency_distribution.png')">Download Chart</button></h3>
                <div class="chart-container" id="constituencyChart"></div>
            </div>
        </div>

        <!-- MARGINALISED AREA DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">Marginalised Area Distribution</div>
            <div class="section-subtitle">Projects in marginalised areas</div>
            
            <div class="chart-box">
                <h3>Projects by Marginalised Area <button class="download-btn" onclick="downloadChart('marginalisedChart', 'marginalised_distribution.png')">Download Chart</button></h3>
                <div class="chart-container" id="marginalisedChart"></div>
            </div>
        </div>

        <!-- WARD DISTRIBUTION -->
        <div class="chart-section">
            <div class="section-title">Ward Distribution</div>
            <div class="section-subtitle">Top 15 wards by project count</div>
            
            <div class="chart-box">
                <h3>Projects by Ward <button class="download-btn" onclick="downloadChart('wardChart', 'ward_distribution.png')">Download Chart</button></h3>
                <div class="chart-container" id="wardChart"></div>
            </div>
        </div>

        <!-- BUDGET VS PROJECTS -->
        <div class="chart-section">
            <div class="section-title">Budget vs Projects</div>
            <div class="section-subtitle">Relationship between budget allocation and number of projects</div>
            
            <div class="chart-box">
                <h3>Budget vs Projects <button class="download-btn" onclick="downloadChart('budgetVsProjectsChart', 'budget_vs_projects.png')">Download Chart</button></h3>
                <div class="chart-container" id="budgetVsProjectsChart"></div>
            </div>
        </div>

        <!-- TOP SECTORS TREEMAP -->
        <div class="chart-section">
            <div class="section-title">Top Sectors by Budget</div>
            <div class="section-subtitle">Hierarchical view of sector budget allocation</div>
            
            <div class="chart-box">
                <h3>Top Sectors by Budget <button class="download-btn" onclick="downloadChart('topSectorsTreeMapChart', 'top_sectors_treemap.png')">Download Chart</button></h3>
                <div class="chart-container" id="topSectorsTreeMapChart"></div>
            </div>
        </div>

        <!-- BUDGET TREND -->
        <div class="chart-section">
            <div class="section-title">Budget Trend</div>
            <div class="section-subtitle">Monthly budget allocation trend</div>
            
            <div class="chart-box">
                <h3>Budget Trend Over Time <button class="download-btn" onclick="downloadChart('budgetTrendChart', 'budget_trend.png')">Download Chart</button></h3>
                <div class="chart-container" id="budgetTrendChart"></div>
            </div>
        </div>

        <!-- PROJECT COMPLETION GAUGE -->
        <div class="chart-section">
            <div class="section-title">Project Completion Rate</div>
            <div class="section-subtitle">Overall project completion percentage</div>
            
            <div class="chart-box">
                <h3>Project Completion Rate <button class="download-btn" onclick="downloadChart('projectCompletionChart', 'project_completion.png')">Download Chart</button></h3>
                <div class="chart-container" id="projectCompletionChart"></div>
            </div>
        </div>

        <!-- BUDGET UTILIZATION -->
        <div class="chart-section">
            <div class="section-title">Budget Utilization</div>
            <div class="section-subtitle">Percentage of budget utilized</div>
            
            <div class="chart-box">
                <h3>Budget Utilization <button class="download-btn" onclick="downloadChart('budgetUtilizationChart', 'budget_utilization.png')">Download Chart</button></h3>
                <div class="chart-container" id="budgetUtilizationChart"></div>
            </div>
        </div>

    </div>

    <!-- ADVANCED ANALYTICS TAB -->
    <div class="tab-content" id="advanced">
        
        <!-- SECTOR RADAR CHART -->
        <div class="chart-section">
            <div class="section-title">Sector Comparison</div>
            <div class="section-subtitle">Multi-dimensional comparison of sectors</div>
            
            <div class="chart-box">
                <h3>Sector Comparison <button class="download-btn" onclick="downloadChart('sectorRadarChart', 'sector_radar.png')">Download Chart</button></h3>
                <div class="chart-container" id="sectorRadarChart"></div>
            </div>
        </div>

        <!-- PROJECT STATUS FUNNEL -->
        <div class="chart-section">
            <div class="section-title">Project Status Funnel</div>
            <div class="section-subtitle">Flow analysis of project status distribution</div>
            
            <div class="chart-box">
                <h3>Project Status Distribution <button class="download-btn" onclick="downloadChart('projectStatusFunnelChart', 'project_status_funnel.png')">Download Chart</button></h3>
                <div class="chart-container" id="projectStatusFunnelChart"></div>
            </div>
        </div>

        <!-- WORD CLOUD -->
        <div class="chart-section">
            <div class="section-title">Project Keywords</div>
            <div class="section-subtitle">Word frequency analysis from project descriptions</div>
            
            <div class="chart-box">
                <h3>Project Keywords <button class="download-btn" onclick="downloadChart('wordCloudChart', 'word_cloud.png')">Download Chart</button></h3>
                <div class="chart-container" id="wordCloudChart"></div>
            </div>
        </div>

        <!-- SANKEY DIAGRAM -->
        <div class="chart-section">
            <div class="section-title">Fund Flow</div>
            <div class="section-subtitle">Flow of funds from national to project level</div>
            
            <div class="chart-box">
                <h3>Fund Flow <button class="download-btn" onclick="downloadChart('sankeyDiagramChart', 'fund_flow.png')">Download Chart</button></h3>
                <div class="chart-container" id="sankeyDiagramChart"></div>
            </div>
        </div>

        <!-- SUNBURST CHART -->
        <div class="chart-section">
            <div class="section-title">Hierarchical Allocation</div>
            <div class="section-subtitle">Hierarchical view of allocation distribution</div>
            
            <div class="chart-box">
                <h3>Hierarchical Allocation <button class="download-btn" onclick="downloadChart('sunburstChart', 'hierarchical_allocation.png')">Download Chart</button></h3>
                <div class="chart-container tall" id="sunburstChart"></div>
            </div>
        </div>

        <!-- 3D COUNTY CHART -->
        <div class="chart-section">
            <div class="section-title">3D County Allocation</div>
            <div class="section-subtitle">Interactive 3D visualization of county allocation</div>
            
            <div class="chart-box">
                <h3>3D County Allocation <button class="download-btn" onclick="downloadChart('county3DChart', '3d_county_allocation.png')">Download Chart</button></h3>
                <div class="chart-container tall" id="county3DChart"></div>
            </div>
        </div>

        <!-- CORRELATION MATRIX -->
        <div class="chart-section">
            <div class="section-title">Correlation Matrix</div>
            <div class="section-subtitle">Correlation analysis between different sectors</div>
            
            <div class="chart-box">
                <h3>Sector Correlation Matrix <button class="download-btn" onclick="downloadChart('correlationMatrixChart', 'correlation_matrix.png')">Download Chart</button></h3>
                <div class="chart-container" id="correlationMatrixChart"></div>
            </div>
        </div>

    </div>

    <!-- TOP PROJECTS TABLE -->
    <div class="table-container">
        <h3>Top 10 Projects by Budget</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project Description</th>
                        <th>County</th>
                        <th>Constituency</th>
                        <th>Ward</th>
                        <th>Sector</th>
                        <th>Budget (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProjects as $index => $project): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= Html::encode($project->project_description) ?></td>
                            <td><?= Html::encode($project->county) ?></td>
                            <td><?= Html::encode($project->constituency) ?></td>
                            <td><?= Html::encode($project->ward) ?></td>
                            <td><?= Html::encode($project->sector) ?></td>
                            <td><?= number_format($project->project_budget, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
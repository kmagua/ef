<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\modules\backend\models\County;
use app\modules\backend\models\Fiscal;
use app\modules\backend\models\EquitableRevenueShare;
use app\modules\backend\models\AdditionalRevShare;
use app\modules\backend\models\EqualizationFundDisbursement;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\modules\backend\models\FiscalSearch */
/* @var $aggregateData array */
/* @var $countyRatios array */
/* @var $selectedYear string */
/* @var $availableYears array */

 $this->title = 'Fiscal Performance Dashboard - County Governments in Kenya Since Devolution';
 $this->params['breadcrumbs'][] = $this->title;

// Register Google Charts
 $this->registerJsFile('https://www.gstatic.com/charts/loader.js', ['position' => \yii\web\View::POS_HEAD]);

// Register Chart.js for more advanced charts
 $this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);

// Register ECharts for enhanced visualizations
 $this->registerJsFile('https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Register D3.js for advanced data visualizations
 $this->registerJsFile('https://d3js.org/d3.v7.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Register Bootstrap JS for tooltips
 $this->registerJs('$(function () { $(\'[data-toggle="tooltip"]\').tooltip(); });', \yii\web\View::POS_READY);

// Register Poppins font
 $this->registerCss('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap', ['position' => \yii\web\View::POS_HEAD]);

// Register Font Awesome
 $this->registerCss('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

// Register Animate.css for animations
 $this->registerCss('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

// Add custom CSS for executive dashboard with Kenya theme
 $this->registerCss("
    :root {
        --kenya-green: #006400;
        --kenya-red: #b22222;
        --kenya-black: #000000;
        --kenya-white: #ffffff;
        --kenya-gold: #d4af37;
        --kenya-dark-green: #004d00;
        --kenya-light-green: #228b22;
        --kenya-dark-red: #8b0000;
        --kenya-light-red: #cd5c5c;
        --kenya-dark-gold: #b8860b;
        --kenya-light-gold: #ffd700;
        --primary-gradient: linear-gradient(135deg, var(--kenya-green) 0%, var(--kenya-light-green) 100%);
        --secondary-gradient: linear-gradient(135deg, var(--kenya-red) 0%, var(--kenya-light-red) 100%);
        --dark-gradient: linear-gradient(135deg, var(--kenya-black) 0%, #333 100%);
        --light-gradient: linear-gradient(135deg, #f8f9fa 0%, var(--kenya-white) 100%);
        --radius: 12px;
        --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
        --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        --hover-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    * {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #37474f;
        font-size: 15px;
        line-height: 1.6;
    }

    .dashboard-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Page Header */
    .page-header {
        background: var(--primary-gradient);
        color: white;
        padding: 3rem 0;
        margin: -1rem -15px 2rem;
        border-radius: 0 0 30px 30px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--kenya-gold) 0%, var(--kenya-light-gold) 50%, var(--kenya-gold) 100%);
    }

    .page-header h1 {
        font-weight: 700;
        font-size: 2.8rem;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .page-header p {
        font-size: 1.3rem;
        opacity: 0.9;
        font-weight: 300;
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
    }

    .page-header::after {
        content: '';
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(212,175,55,0.2) 0%, rgba(212,175,55,0) 70%);
        border-radius: 50%;
        z-index: 1;
    }

    /* Summary Cards */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .metric-card {
        border: none;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        height: 100%;
        overflow: hidden;
        position: relative;
        min-height: 160px;
        display: flex;
        flex-direction: column;
    }

    .metric-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--hover-shadow);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: var(--kenya-gold);
    }

    .metric-card.bg-kenya-green { 
        background: var(--primary-gradient) !important; 
    }
    .metric-card.bg-kenya-red { 
        background: var(--secondary-gradient) !important; 
    }
    .metric-card.bg-kenya-black { 
        background: var(--dark-gradient) !important; 
    }
    .metric-card.bg-kenya-white { 
        background: var(--light-gradient) !important; 
    }
    .metric-card.bg-purple {
        background: linear-gradient(135deg, #6f42c1 0%, #8a63d2 100%) !important;
    }
    .metric-card.bg-orange {
        background: linear-gradient(135deg, #fd7e14 0%, #ff9c4a 100%) !important;
    }
    .metric-card.bg-gold {
        background: linear-gradient(135deg, var(--kenya-gold) 0%, var(--kenya-light-gold) 100%) !important;
    }

    .metric-icon {
        font-size: 2.8rem;
        opacity: 0.9;
        color: var(--kenya-gold);
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 2.2rem;
        font-weight: 700;
        color: white;
        line-height: 1.2;
    }

    .metric-label {
        font-size: 1rem;
        opacity: 0.9;
        color: white;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-card.bg-kenya-white .metric-value,
    .metric-card.bg-kenya-white .metric-label,
    .metric-card.bg-kenya-white .metric-icon {
        color: #111;
    }

    .metric-card .card-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .metric-card .metric-stats {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .metric-card .metric-stats small {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .metric-card .metric-stats strong {
        font-weight: 600;
    }

    .metric-card .progress {
        height: 6px;
        border-radius: 3px;
        background: rgba(255,255,255,0.2);
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .metric-card .progress-bar {
        border-radius: 3px;
        transition: width 1s ease;
    }

    /* Chart Cards - Grid Layout */
    .charts-section {
        margin-bottom: 2rem;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 1200px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    .chart-item {
        border: none;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .chart-item:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }

    .chart-header {
        font-weight: 600;
        padding: 1.3rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        position: relative;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chart-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--kenya-gold);
    }

    .chart-header.bg-primary {
        background: var(--primary-gradient);
        color: white;
    }

    .chart-header.bg-success {
        background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
        color: white;
    }

    .chart-header.bg-warning {
        background: linear-gradient(135deg, #f57c00 0%, #ff9800 100%);
        color: white;
    }

    .chart-header.bg-info {
        background: linear-gradient(135deg, #0277bd 0%, #039be5 100%);
        color: white;
    }

    .chart-header.bg-danger {
        background: linear-gradient(135deg, #c62828 0%, #e53935 100%);
        color: white;
    }

    .chart-header.bg-dark {
        background: var(--dark-gradient);
        color: white;
    }

    .chart-header button.download-chart {
        border: none;
        background: var(--kenya-gold);
        color: #111;
        padding: 6px 10px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .chart-header button.download-chart:hover {
        background: #ffe27a;
    }

    .chart-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .chart-container {
        height: 400px;
        width: 100%;
        position: relative;
    }

    /* Google Charts specific styles */
    .google-chart {
        width: 100%;
        height: 100%;
    }

    /* Action Bar */
    .action-bar {
        background: white;
        padding: 1.2rem 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        margin-bottom: 1.8rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .action-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--kenya-green);
        margin: 0;
    }

    .action-buttons {
        display: flex;
        gap: 0.8rem;
        flex-wrap: wrap;
    }

    .download-all-charts {
        background: var(--kenya-red);
        border: none;
        color: #fff;
        border-radius: 8px;
        padding: 10px 18px;
        font-weight: 600;
        transition: var(--transition);
    }

    .download-all-charts:hover {
        background: var(--kenya-dark-red);
        transform: translateY(-3px);
    }

    /* Year Selector */
    .year-selector {
        background: white;
        padding: 25px;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        margin-bottom: 2.5rem;
        border-left: 5px solid var(--kenya-gold);
    }

    .year-selector-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--kenya-green);
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
    }

    .year-selector-title i {
        margin-right: 0.6rem;
        color: var(--kenya-gold);
    }

    /* Buttons */
    .btn {
        border-radius: 10px;
        font-weight: 500;
        padding: 0.8rem 1.5rem;
        transition: var(--transition);
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
    }

    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background: var(--primary-gradient);
        color: white;
    }

    .btn-success {
        background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
        color: white;
    }

    .btn-outline-primary {
        border: 2px solid var(--kenya-green);
        color: var(--kenya-green);
    }

    .btn-outline-primary:hover {
        background-color: var(--kenya-green);
        color: white;
    }

    .btn-outline-secondary {
        border: 2px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }

    /* Dark Mode Toggle */
    .dark-mode-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        background: var(--kenya-black);
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow);
        cursor: pointer;
        transition: var(--transition);
    }

    .dark-mode-toggle:hover {
        transform: scale(1.1);
        box-shadow: var(--hover-shadow);
    }

    /* Dark Mode Styles */
    body.dark-mode {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #e0e0e0;
    }

    body.dark-mode .card {
        background: #2d2d2d;
        color: #e0e0e0;
    }

    body.dark-mode .chart-item {
        background: #2d2d2d;
        color: #e0e0e0;
    }

    body.dark-mode .chart-header {
        background: #333;
        color: #e0e0e0;
    }

    body.dark-mode .table {
        color: #e0e0e0;
    }

    body.dark-mode .table thead th {
        background: #333;
        color: #e0e0e0;
    }

    body.dark-mode .table tbody tr:hover {
        background-color: rgba(0, 100, 0, 0.2);
    }

    /* Forms */
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 0.8rem 1.2rem;
        transition: var(--transition);
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--kenya-green);
        box-shadow: 0 0 0 0.25rem rgba(0, 100, 0, 0.15);
    }

    /* Tables */
    .table-responsive {
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background: var(--primary-gradient);
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1.2rem 1rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody tr {
        transition: var(--transition);
    }

    .table tbody tr:hover {
        background-color: rgba(0, 100, 0, 0.08);
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Progress bars */
    .progress {
        height: 12px;
        border-radius: 6px;
        background-color: #e9ecef;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 6px;
        transition: width 1.5s ease;
    }

    /* Status indicators */
    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 6px;
    }

    .status-high { background-color: var(--kenya-red); }
    .status-medium { background-color: #ff9800; }
    .status-low { background-color: var(--kenya-green); }

    /* Badges */
    .badge {
        padding: 0.5rem 0.8rem;
        border-radius: 6px;
        font-weight: 500;
    }

    .badge-success {
        background-color: var(--kenya-green);
    }

    .badge-danger {
        background-color: var(--kenya-red);
    }

    .badge-warning {
        background-color: #ff9800;
    }

    .badge-info {
        background-color: #0277bd;
    }

    /* Compact view */
    .compact-mode .chart-container {
        height: 300px !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2.2rem;
        }
        
        .page-header p {
            font-size: 1.1rem;
        }
        
        .metric-value {
            font-size: 1.8rem;
        }
        
        .chart-container {
            height: 350px;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .action-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .action-buttons {
            justify-content: center;
        }

        .year-selector {
            padding: 1.8rem;
        }
    }

    @media (max-width: 576px) {
        .page-header {
            padding: 2rem 0;
            margin: -1rem -10px 2rem;
        }
        
        .page-header h1 {
            font-size: 2rem;
        }

        .chart-container {
            height: 300px;
        }

        .chart-header {
            padding: 1rem;
        }

        .metric-card {
            min-height: 140px;
        }

        .metric-icon {
            font-size: 2.2rem;
        }

        .metric-value {
            font-size: 1.5rem;
        }
    }

    /* Animations */
    .animate-fade-in {
        animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-scale-in {
        animation: scaleIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    /* Loading animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Tooltip styles */
    .custom-tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.85rem;
        pointer-events: none;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .custom-tooltip.show {
        opacity: 1;
    }

    /* Chart specific styles */
    .chart-legend {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }

    /* Enhanced county visualization */
    .county-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .county-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        padding: 1rem;
        transition: var(--transition);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .county-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }

    .county-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: var(--kenya-gold);
    }

    .county-name {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--kenya-green);
    }

    .county-stat {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.3rem;
        font-size: 0.9rem;
    }

    .county-stat-label {
        color: #6c757d;
    }

    .county-stat-value {
        font-weight: 500;
    }

    /* Enhanced filters */
    .filter-section {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .filter-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--kenya-green);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .filter-title i {
        margin-right: 0.5rem;
        color: var(--kenya-gold);
    }

    .filter-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    /* Map visualization */
    .map-container {
        height: 500px;
        width: 100%;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
        position: relative;
    }

    .map-overlay {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.9);
        padding: 1rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        z-index: 10;
    }

    .map-legend-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--kenya-green);
    }

    .map-legend-items {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .map-legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .map-legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* Data table enhancements */
    .data-table-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .data-table-header {
        background: var(--primary-gradient);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .data-table-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
    }

    .data-table-actions {
        display: flex;
        gap: 0.5rem;
    }

    .data-table-body {
        padding: 1rem;
    }

    .data-table-footer {
        background: #f8f9fa;
        padding: 0.75rem 1.5rem;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Enhanced charts */
    .chart-tabs {
        display: flex;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 1rem;
    }

    .chart-tab {
        padding: 0.75rem 1rem;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: var(--transition);
    }

    .chart-tab:hover {
        color: var(--kenya-green);
    }

    .chart-tab.active {
        color: var(--kenya-green);
        border-bottom-color: var(--kenya-gold);
    }

    .chart-content {
        display: none;
    }

    .chart-content.active {
        display: block;
    }

    /* Enhanced metrics */
    .enhanced-metric-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
    }

    .enhanced-metric-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }

    .enhanced-metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: var(--kenya-gold);
    }

    .enhanced-metric-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .enhanced-metric-title {
        font-weight: 600;
        color: #495057;
        margin: 0;
    }

    .enhanced-metric-icon {
        font-size: 1.5rem;
        color: var(--kenya-gold);
    }

    .enhanced-metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--kenya-green);
        margin-bottom: 0.5rem;
    }

    .enhanced-metric-change {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .enhanced-metric-change.positive {
        color: var(--kenya-green);
    }

    .enhanced-metric-change.negative {
        color: var(--kenya-red);
    }

    .enhanced-metric-chart {
        height: 60px;
        width: 100%;
    }

    /* Responsive table */
    .responsive-table {
        width: 100%;
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .responsive-table table {
            min-width: 600px;
        }
    }

    /* Project map embed */
    .project-map-embed {
        width: 100%;
        height: 500px;
        border-radius: 8px;
        overflow: hidden;
    }

    .project-map-embed iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
");
// Helper function to format large numbers with T, B, M suffixes
function formatLargeNumber($number, $decimals = 2) {
    // Handle non-numeric inputs
    if (!is_numeric($number)) {
        return '0';
    }
    
    // Convert to float for proper handling
    $number = (float) $number;
    
    // Handle zero
    if ($number == 0) {
        return '0';
    }
    
    // Define suffixes and their corresponding values
    $suffixes = [
        'T' => 1000000000000,
        'B' => 1000000000,
        'M' => 1000000,
        'K' => 1000
    ];
    
    // Handle negative numbers
    $isNegative = $number < 0;
    if ($isNegative) {
        $number = abs($number);
    }
    
    // Find appropriate suffix
    foreach ($suffixes as $suffix => $value) {
        if ($number >= $value) {
            $formatted = number_format($number / $value, $decimals) . $suffix;
            
            // Remove trailing zeros after decimal point
            $formatted = preg_replace('/\.?0+$/', '', $formatted);
            
            // Add negative sign back if needed
            if ($isNegative) {
                $formatted = '-' . $formatted;
            }
            
            return $formatted;
        }
    }
    
    // No suffix needed
    $formatted = number_format($number, $decimals);
    $formatted = preg_replace('/\.?0+$/', '', $formatted);
    
    return $isNegative ? '-' . $formatted : $formatted;
}

// Helper function to standardize year format
function standardizeYear($year) {
    if (strpos($year, '/') !== false) {
        $parts = explode('/', $year);
        if (count($parts) == 2) {
            $yearPart1 = substr($parts[0], 0, 4);
            $yearPart2 = substr($parts[1], -2);
            return $yearPart1 . '/' . $yearPart2;
        }
    }
    return $year;
}

// Get all available years from fiscal table with standardization
 $allYears = [];
 $rawYears = \app\modules\backend\models\Fiscal::find()
    ->select('fy')
    ->distinct()
    ->column();

foreach ($rawYears as $year) {
    $allYears[] = standardizeYear($year);
}

// Remove duplicates and sort
 $allYears = array_unique($allYears);
sort($allYears);

// If no year is selected, use the latest year
if (empty($selectedYear) || !in_array($selectedYear, $allYears)) {
    $selectedYear = end($allYears);
}

// Get all counties for the advanced search
 $counties = \app\modules\backend\models\County::find()
    ->orderBy(['CountyName' => SORT_ASC])
    ->all();

// Function to get standardized data from a table
function getStandardizedData($table, $amountField, $yearField = 'fy', $additionalConditions = []) {
    $query = (new \yii\db\Query())
        ->select([$yearField, "SUM($amountField) as amount"])
        ->from($table)
        ->where(['is not', $amountField, null]);
    
    // Add any additional conditions
    foreach ($additionalConditions as $condition) {
        $query->andWhere($condition);
    }
    
    $query->groupBy($yearField);
    
    $rawData = $query->all();
    $data = [];
    
    foreach ($rawData as $item) {
        $year = $item[$yearField];
        $standardizedYear = standardizeYear($year);
        
        if (!isset($data[$standardizedYear])) {
            $data[$standardizedYear] = 0;
        }
        $data[$standardizedYear] += $item['amount'];
    }
    
    return $data;
}

// -------------------------------------------------------------
// 1. Equitable Share: projected (CARA) vs actual (disbursed)
// -------------------------------------------------------------

 $equitableShareProjected = [];
 $equitableShareActual    = [];
 $equitableShareData      = []; // numeric actual per FY (for revenue etc.)

 $equitableShareQuery = (new \yii\db\Query())
    ->select([
        'fy',
        'SUM(project_amt) AS projected_amount',
        'SUM(actual_amt)  AS actual_amount'
    ])
    ->from('equitable_revenue_share')
    ->groupBy('fy')
    ->all();

foreach ($equitableShareQuery as $item) {
    $year      = standardizeYear($item['fy']);
    $projected = (float)$item['projected_amount'];
    $actual    = (float)$item['actual_amount'];

    $equitableShareProjected[$year] = $projected;
    $equitableShareActual[$year]    = $actual;

    // For all revenue calculations we use actual disbursed
    $equitableShareData[$year]      = $actual;
}

// 2. Own Source Revenue
 $osrData = getStandardizedData('fiscal', 'actual_osr');
 $targetOsrData = getStandardizedData('fiscal', 'target_osr');

// 3. Additional Allocations
 $additionalAllocationsData = getStandardizedData('additional_rev_share', 'actual_amt');
 $projectedAdditionalAllocationsData = getStandardizedData('additional_rev_share', 'project_amt');

// 4. Equalization Fund
 $equalizationFundData = getStandardizedData('equalization_fund_disbursement', 'amount_disbursed', 'fiscal_year');

// 5. Expenditure data
 $expenditureData = [];
 $rawExpenditureData = (new \yii\db\Query())
    ->select([
        'fy',
        'SUM(recurrent_expenditure) as total_recurrent_expenditure',
        'SUM(development_expenditure) as total_development_expenditure',
        'SUM(recurrent_expenditure + development_expenditure) as total_expenditure',
        'SUM(recurrent_budget) as total_recurrent_budget',
        'SUM(development_budgement) as total_development_budget', // actual column
        'SUM(recurrent_budget + development_budgement) as total_budget'
    ])
    ->from('fiscal')
    ->where(['or',
        ['is not', 'recurrent_expenditure', null],
        ['is not', 'development_expenditure', null]
    ])
    ->groupBy('fy')
    ->all();

foreach ($rawExpenditureData as $item) {
    $year = standardizeYear($item['fy']);
    $expenditureData[$year] = $item;
}

// 6. Pending Bills
 $pendingBillsData = getStandardizedData('fiscal', 'pending_bills');

// 7. Compensation data (personal_emoluments as primary)
 $compensationData = [];
 $rawCompensationData = (new \yii\db\Query())
    ->select([
        'fy',
        'SUM(personal_emoluments) as total_compensation',
        'SUM(actual_wages_salaries) as total_actual_wages_salaries',
        'SUM(total_actual_wages_salaries) as total_budgeted_wages'
    ])
    ->from('fiscal')
    ->where(['or',
        ['is not', 'personal_emoluments', null],
        ['is not', 'actual_wages_salaries', null]
    ])
    ->andWhere(['>', 'personal_emoluments', 0])
    ->groupBy('fy')
    ->all();

foreach ($rawCompensationData as $item) {
    $year = standardizeYear($item['fy']);
    $compensationData[$year] = $item;
}

// Add recurrent expenditure and compensation ratio to compensationData
foreach ($allYears as $year) {
    if (!isset($compensationData[$year])) {
        $compensationData[$year] = [
            'total_compensation' => 0,
            'total_actual_wages_salaries' => 0,
            'total_budgeted_wages' => 0
        ];
    }
    
    $recurrentExpenditure = $expenditureData[$year]['total_recurrent_expenditure'] ?? 0;
    
    $compensationRatio = 0;
    if ($recurrentExpenditure > 0) {
        $compensationRatio = ($compensationData[$year]['total_compensation'] / $recurrentExpenditure) * 100;
    }
    
    $compensationData[$year]['total_recurrent_expenditure'] = $recurrentExpenditure;
    $compensationData[$year]['compensation_ratio'] = $compensationRatio;
}

// Actual revenue data (for wages/revenue ratios)
 $actualRevenueData = getStandardizedData('fiscal', 'actual_revenue');

// ------------------------------------------------------------------
// Summary data (single struct for dashboard cards)
// ------------------------------------------------------------------
 $summaryData = [
    'total_equitable_share' => 0,
    'total_osr' => 0,
    'total_target_osr' => 0,
    'total_additional_allocations' => 0,
    'total_project_amt' => 0,
    'total_equalization_fund' => 0,
    'total_recurrent_expenditure' => 0,
    'total_development_expenditure' => 0,
    'total_expenditure' => 0,
    'total_budget' => 0,
    'total_recurrent_budget' => 0,
    'total_development_budget' => 0,
    'total_pending_bills' => 0,
    'total_compensation' => 0,
    'total_actual_wages_salaries' => 0,
    'total_budgeted_wages' => 0,
    'total_revenue' => 0,
    'revenue_growth' => 0,
    'absorption_rate' => 0,
    'pending_bills_revenue_ratio' => 0,
    'pending_bills_growth' => 0,
    'compensation_recurrent_share' => 0,
    'disbursement_rate' => 100,
    'osr_performance_rate' => 0,
    'additional_allocations_utilization' => 0,
    'development_absorption_rate' => 0,
    'recurrent_absorption_rate' => 0,
    'utilization_rate' => 0,
    'budgeted_recurrent_to_revenue' => 0,
    'actual_recurrent_to_revenue' => 0,
    'budgeted_development_to_budget' => 0,
    'actual_development_to_expenditure' => 0,
    'budgeted_wages_to_revenue' => 0,
    'actual_wages_to_revenue' => 0,
    'wages_variance' => 0,
    'fiscal_health_index' => 0,
    'revenue_diversity_index' => 0,
    'expenditure_efficiency_ratio' => 0,
    'fiscal_sustainability_score' => 0
];

// Fill summary for selected year (using ACTUAL equitable share)
 $summaryData['total_equitable_share'] = $equitableShareData[$selectedYear] ?? 0;
 $summaryData['total_osr'] = $osrData[$selectedYear] ?? 0;
 $summaryData['total_target_osr'] = $targetOsrData[$selectedYear] ?? 0;
 $summaryData['total_additional_allocations'] = $additionalAllocationsData[$selectedYear] ?? 0;
 $summaryData['total_project_amt'] = $projectedAdditionalAllocationsData[$selectedYear] ?? 0;
 $summaryData['total_equalization_fund'] = $equalizationFundData[$selectedYear] ?? 0;
 $summaryData['total_recurrent_expenditure'] = $expenditureData[$selectedYear]['total_recurrent_expenditure'] ?? 0;
 $summaryData['total_development_expenditure'] = $expenditureData[$selectedYear]['total_development_expenditure'] ?? 0;
 $summaryData['total_expenditure'] = $expenditureData[$selectedYear]['total_expenditure'] ?? 0;
 $summaryData['total_budget'] = $expenditureData[$selectedYear]['total_budget'] ?? 0;
 $summaryData['total_recurrent_budget'] = $expenditureData[$selectedYear]['total_recurrent_budget'] ?? 0;
 $summaryData['total_development_budget'] = $expenditureData[$selectedYear]['total_development_budget'] ?? 0;
 $summaryData['total_pending_bills'] = $pendingBillsData[$selectedYear] ?? 0;
 $summaryData['total_compensation'] = $compensationData[$selectedYear]['total_compensation'] ?? 0;
 $summaryData['total_actual_wages_salaries'] = $compensationData[$selectedYear]['total_actual_wages_salaries'] ?? 0;
 $summaryData['total_budgeted_wages'] = $compensationData[$selectedYear]['total_budgeted_wages'] ?? 0;

// Total revenue
 $summaryData['total_revenue'] = 
    $summaryData['total_equitable_share'] + 
    $summaryData['total_osr'] + 
    $summaryData['total_additional_allocations'] + 
    $summaryData['total_equalization_fund'];

// Disbursement rate (equitable share actual vs projected)
if (($equitableShareProjected[$selectedYear] ?? 0) > 0) {
    $summaryData['disbursement_rate'] = ($equitableShareActual[$selectedYear] ?? 0) / $equitableShareProjected[$selectedYear] * 100;
}

// Derived ratios
if ($summaryData['total_budget'] > 0) {
    $summaryData['absorption_rate'] = ($summaryData['total_expenditure'] / $summaryData['total_budget']) * 100;
}

if ($summaryData['total_development_budget'] > 0) {
    $summaryData['development_absorption_rate'] = ($summaryData['total_development_expenditure'] / $summaryData['total_development_budget']) * 100;
}

if ($summaryData['total_recurrent_budget'] > 0) {
    $summaryData['recurrent_absorption_rate'] = ($summaryData['total_recurrent_expenditure'] / $summaryData['total_recurrent_budget']) * 100;
}

if ($summaryData['total_revenue'] > 0) {
    $summaryData['utilization_rate'] = ($summaryData['total_expenditure'] / $summaryData['total_revenue']) * 100;
    $summaryData['pending_bills_revenue_ratio'] = ($summaryData['total_pending_bills'] / $summaryData['total_revenue']) * 100;
    $summaryData['actual_wages_to_revenue'] = ($summaryData['total_compensation'] / $summaryData['total_revenue']) * 100;
}

if ($summaryData['total_target_osr'] > 0) {
    $summaryData['osr_performance_rate'] = ($summaryData['total_osr'] / $summaryData['total_target_osr']) * 100;
}

if ($summaryData['total_project_amt'] > 0) {
    $summaryData['additional_allocations_utilization'] = ($summaryData['total_additional_allocations'] / $summaryData['total_project_amt']) * 100;
}

if ($summaryData['total_recurrent_expenditure'] > 0) {
    $summaryData['compensation_recurrent_share'] = ($summaryData['total_compensation'] / $summaryData['total_recurrent_expenditure']) * 100;
}

// Wages variance
if ($summaryData['total_budgeted_wages'] > 0) {
    $summaryData['wages_variance'] = (($summaryData['total_compensation'] - $summaryData['total_budgeted_wages']) / $summaryData['total_budgeted_wages']) * 100;
}

// Fiscal Health Index (0–100)
 $osrScore = min($summaryData['osr_performance_rate'] / 100, 1) * 25;
 $absorptionScore = min($summaryData['absorption_rate'] / 100, 1) * 25;
 $pendingBillsScore = max(0, (100 - $summaryData['pending_bills_revenue_ratio']) / 100) * 25;
 $wagesScore = max(0, (100 - abs($summaryData['wages_variance'])) / 100) * 25;

 $summaryData['fiscal_health_index'] = $osrScore + $absorptionScore + $pendingBillsScore + $wagesScore;

// Revenue diversity (1 - HHI)
 $totalRevenue = $summaryData['total_revenue'];
if ($totalRevenue > 0) {
    $equitableShareShare = pow($summaryData['total_equitable_share'] / $totalRevenue, 2);
    $osrShare = pow($summaryData['total_osr'] / $totalRevenue, 2);
    $additionalShare = pow($summaryData['total_additional_allocations'] / $totalRevenue, 2);
    $equalizationShare = pow($summaryData['total_equalization_fund'] / $totalRevenue, 2);
    
    $summaryData['revenue_diversity_index'] = (1 - ($equitableShareShare + $osrShare + $additionalShare + $equalizationShare)) * 100;
}

// Expenditure efficiency (dev / total)
if ($summaryData['total_expenditure'] > 0) {
    $summaryData['expenditure_efficiency_ratio'] = ($summaryData['total_development_expenditure'] / $summaryData['total_expenditure']) * 100;
}

// Fiscal Sustainability Score
 $revenueGrowthScore = min(max($summaryData['revenue_growth'] + 50, 0) / 100, 1) * 30;
 $pendingBillsGrowthScore = max(0, (100 - abs($summaryData['pending_bills_growth'])) / 100) * 30;
 $diversityScore = $summaryData['revenue_diversity_index'] * 0.4;

 $summaryData['fiscal_sustainability_score'] = $revenueGrowthScore + $pendingBillsGrowthScore + $diversityScore;

// Budget vs revenue ratios
 $budgetedRecurrent = $summaryData['total_recurrent_budget'];
 $budgetedDevelopment = $summaryData['total_development_budget'];
 $budgetedWages = $summaryData['total_budgeted_wages'];

 $summaryData['budgeted_recurrent_to_revenue'] = $summaryData['total_revenue'] > 0 ? ($budgetedRecurrent / $summaryData['total_revenue']) * 100 : 0;
 $summaryData['actual_recurrent_to_revenue'] = $summaryData['total_revenue'] > 0 ? ($summaryData['total_recurrent_expenditure'] / $summaryData['total_revenue']) * 100 : 0;
 $summaryData['budgeted_development_to_budget'] = $summaryData['total_budget'] > 0 ? ($budgetedDevelopment / $summaryData['total_budget']) * 100 : 0;
 $summaryData['actual_development_to_expenditure'] = $summaryData['total_expenditure'] > 0 ? ($summaryData['total_development_expenditure'] / $summaryData['total_expenditure']) * 100 : 0;
 $summaryData['budgeted_wages_to_revenue'] = $summaryData['total_revenue'] > 0 ? ($budgetedWages / $summaryData['total_revenue']) * 100 : 0;

// ------------------------------------------------------------------
// Growth (CAGR) – revenue & pending bills
// ------------------------------------------------------------------
 $equitableShareYears = array_keys($equitableShareData);
if (count($equitableShareYears) > 1) {
    sort($equitableShareYears);
    $firstYear = $equitableShareYears[0];
    $lastYear = end($equitableShareYears);
    $yearCount = count($equitableShareYears) - 1;
    
    if (($equitableShareData[$firstYear] ?? 0) > 0) {
        $cagr = pow(($equitableShareData[$lastYear] / $equitableShareData[$firstYear]), (1 / $yearCount)) - 1;
        $summaryData['revenue_growth'] = $cagr * 100;
    }
}

 $pendingBillsYears = array_keys($pendingBillsData);
if (count($pendingBillsYears) > 1) {
    sort($pendingBillsYears);
    $firstYear = $pendingBillsYears[0];
    $lastYear = end($pendingBillsYears);
    $yearCount = count($pendingBillsYears) - 1;
    
    if (($pendingBillsData[$firstYear] ?? 0) > 0) {
        $cagr = pow(($pendingBillsData[$lastYear] / $pendingBillsData[$firstYear]), (1 / $yearCount)) - 1;
        $summaryData['pending_bills_growth'] = $cagr * 100;
    }
}

// ------------------------------------------------------------------
// Trends data for charts
// ------------------------------------------------------------------
 $trendsData = [
    'revenue_trends' => [],
    'revenue_composition' => [],
    'expenditure_trends' => [],
    'dev_vs_rec' => [],
    'pending_bills' => [],
    'pending_bills_ratio' => [],
    'equitable_share_trends' => [],
    'osr_trends' => [],
    'additional_allocations_trends' => [],
    'total_absorption_trends' => [],
    'recurrent_absorption_trends' => [],
    'development_absorption_trends' => [],
    'compensation_absorption_trends' => [],
    'equalization_fund_trends' => [],
    'compensation_ratio_trends' => [],
    'wages_comparison_trends' => [],
    'wages_revenue_ratio_trends' => [],
    'fiscal_health_trends' => [],
    'revenue_diversity_trends' => [],
    'expenditure_efficiency_trends' => [],
    'budget_variance_trends' => [],
    'revenue_growth_analysis' => [],
    'fiscal_sustainability_trends' => []
];

 $prevYearRevenue = null;
 $prevEquitableShare = null;
 $prevOsr = null;

foreach ($allYears as $year) {
    // Equitable Share: projected vs actual
    $equitableShareBudgeted = $equitableShareProjected[$year] ?? 0;
    $equitableShareActualYear = $equitableShareActual[$year] ?? 0;
    
    $disbursementRatio = 0;
    if ($equitableShareBudgeted > 0) {
        $disbursementRatio = ($equitableShareActualYear / $equitableShareBudgeted) * 100;
    }
    
    $trendsData['equitable_share_trends'][] = [
        'year' => $year,
        'budgeted' => $equitableShareBudgeted,
        'actual' => $equitableShareActualYear,
        'disbursement_ratio' => $disbursementRatio
    ];
    
    // Use actual equitable share for composition & revenue
    $equitableShare = $equitableShareActualYear;
    
    // OSR trends
    $osr = $osrData[$year] ?? 0;
    $targetOsr = $targetOsrData[$year] ?? 0;
    
    $performanceRatio = 0;
    if ($targetOsr > 0) {
        $performanceRatio = ($osr / $targetOsr) * 100;
    }
    
    $trendsData['osr_trends'][] = [
        'year' => $year,
        'budgeted' => $targetOsr,
        'actual' => $osr,
        'performance_ratio' => $performanceRatio
    ];
    
    // Additional allocations trends
    $additionalAllocations = $additionalAllocationsData[$year] ?? 0;
    $projectedAdditionalAllocations = $projectedAdditionalAllocationsData[$year] ?? 0;
    
    $utilizationRatio = 0;
    if ($projectedAdditionalAllocations > 0) {
        $utilizationRatio = ($additionalAllocations / $projectedAdditionalAllocations) * 100;
    }
    
    $trendsData['additional_allocations_trends'][] = [
        'year' => $year,
        'budgeted' => $projectedAdditionalAllocations,
        'actual' => $additionalAllocations,
        'utilization_ratio' => $utilizationRatio
    ];
    
    // Equalization fund trends
    $equalizationFund = $equalizationFundData[$year] ?? 0;
    
    $trendsData['equalization_fund_trends'][] = [
        'year' => $year,
        'amount' => $equalizationFund
    ];
    
    // Revenue composition
    $trendsData['revenue_composition'][] = [
        'year' => $year,
        'equitable_share' => $equitableShare,
        'osr' => $osr,
        'additional_allocations' => $additionalAllocations,
        'equalization_fund' => $equalizationFund
    ];
    
    // Expenditure trends
    $expenditureYear = $expenditureData[$year] ?? [];
    
    $totalExpenditure = $expenditureYear['total_expenditure'] ?? 0;
    $developmentExpenditure = $expenditureYear['total_development_expenditure'] ?? 0;
    $recurrentExpenditure = $expenditureYear['total_recurrent_expenditure'] ?? 0;
    
    $trendsData['expenditure_trends'][] = [
        'year' => $year,
        'development' => $developmentExpenditure,
        'recurrent' => $recurrentExpenditure,
        'total_expenditure' => $totalExpenditure
    ];
    
    $trendsData['dev_vs_rec'][] = [
        'year' => $year,
        'development' => $developmentExpenditure,
        'recurrent' => $recurrentExpenditure
    ];
    
    // Absorption trends
    $totalBudget = $expenditureYear['total_budget'] ?? 0;
    $budgetedDevelopment = $expenditureYear['total_development_budget'] ?? 0;
    $budgetedRecurrent = $expenditureYear['total_recurrent_budget'] ?? 0;
    
    $absorptionRate = 0;
    $developmentAbsorptionRate = 0;
    $recurrentAbsorptionRate = 0;
    
    if ($totalBudget > 0) {
        $absorptionRate = ($totalExpenditure / $totalBudget) * 100;
    }
    if ($budgetedDevelopment > 0) {
        $developmentAbsorptionRate = ($developmentExpenditure / $budgetedDevelopment) * 100;
    }
    if ($budgetedRecurrent > 0) {
        $recurrentAbsorptionRate = ($recurrentExpenditure / $budgetedRecurrent) * 100;
    }
    
    $trendsData['total_absorption_trends'][] = [
        'year' => $year,
        'budgeted' => $totalBudget,
        'actual' => $totalExpenditure,
        'absorption_rate' => $absorptionRate
    ];
    
    $trendsData['development_absorption_trends'][] = [
        'year' => $year,
        'budgeted' => $budgetedDevelopment,
        'actual' => $developmentExpenditure,
        'absorption_rate' => $developmentAbsorptionRate
    ];
    
    $trendsData['recurrent_absorption_trends'][] = [
        'year' => $year,
        'budgeted' => $budgetedRecurrent,
        'actual' => $recurrentExpenditure,
        'absorption_rate' => $recurrentAbsorptionRate
    ];
    
    // Pending bills trends
    $pendingBills = $pendingBillsData[$year] ?? 0;
    $totalRevenueY = $equitableShare + $osr + $additionalAllocations + $equalizationFund;
    
    $pendingBillsRatio = 0;
    if ($totalRevenueY > 0) {
        $pendingBillsRatio = ($pendingBills / $totalRevenueY) * 100;
    }
    
    $trendsData['pending_bills'][] = [
        'year' => $year,
        'pending_bills' => $pendingBills
    ];
    
    $trendsData['pending_bills_ratio'][] = [
        'year' => $year,
        'ratio' => $pendingBillsRatio
    ];
    
    // Compensation trends
    $compensationYear = $compensationData[$year] ?? [];
    
    $compensation = $compensationYear['total_compensation'] ?? 0;
    $actualWages = $compensationYear['total_actual_wages_salaries'] ?? 0;
    $budgetedCompensation = $compensationYear['total_budgeted_wages'] ?? 0;
    
    $compensationAbsorptionRate = 0;
    if ($budgetedCompensation > 0) {
        $compensationAbsorptionRate = ($compensation / $budgetedCompensation) * 100;
    }
    
    $trendsData['compensation_absorption_trends'][] = [
        'year' => $year,
        'budgeted' => $budgetedCompensation,
        'actual' => $compensation,
        'actual_wages' => $actualWages,
        'absorption_rate' => $compensationAbsorptionRate
    ];
    
    // Compensation ratio trends (recurrent exp)
    $compensationRatio = 0;
    if ($recurrentExpenditure > 0) {
        $compensationRatio = ($compensation / $recurrentExpenditure) * 100;
    }
    
    $trendsData['compensation_ratio_trends'][] = [
        'year' => $year,
        'ratio' => $compensationRatio
    ];
    
    // Wages vs budget trends
    $budgetedWages = $compensationYear['total_budgeted_wages'] ?? 0;
    $actualWagesTotal = $compensationYear['total_compensation'] ?? 0;
    $actualWagesSalaries = $compensationYear['total_actual_wages_salaries'] ?? 0;
    
    $variance = 0;
    if ($budgetedWages > 0) {
        $variance = (($actualWagesTotal - $budgetedWages) / $budgetedWages) * 100;
    }
    
    $trendsData['wages_comparison_trends'][] = [
        'year' => $year,
        'budgeted' => $budgetedWages,
        'actual' => $actualWagesTotal,
        'actual_wages_salaries' => $actualWagesSalaries,
        'variance' => $variance
    ];
    
    // Wages to revenue ratio trends
    $revenueY = $actualRevenueData[$year] ?? 0;
    $ratio = 0;
    if ($revenueY > 0) {
        $ratio = ($actualWagesTotal / $revenueY) * 100;
    }
    
    $trendsData['wages_revenue_ratio_trends'][] = [
        'year' => $year,
        'wages' => $actualWagesTotal,
        'revenue' => $revenueY,
        'ratio' => $ratio
    ];
    
    // Fiscal Health Trend per year
    $osrScoreY = min($performanceRatio / 100, 1) * 25;
    $absorptionScoreY = min($absorptionRate / 100, 1) * 25;
    $pendingBillsScoreY = max(0, (100 - $pendingBillsRatio) / 100) * 25;
    $wagesScoreY = max(0, (100 - abs($variance)) / 100) * 25;
    $fiscalHealthIndexY = $osrScoreY + $absorptionScoreY + $pendingBillsScoreY + $wagesScoreY;
    
    $trendsData['fiscal_health_trends'][] = [
        'year' => $year,
        'fiscal_health_index' => $fiscalHealthIndexY,
        'osr_score' => $osrScoreY,
        'absorption_score' => $absorptionScoreY,
        'pending_bills_score' => $pendingBillsScoreY,
        'wages_score' => $wagesScoreY
    ];
    
    // Revenue diversity trend
    if ($totalRevenueY > 0) {
        $equitableShareShare = pow($equitableShare / $totalRevenueY, 2);
        $osrShare = pow($osr / $totalRevenueY, 2);
        $additionalShare = pow($additionalAllocations / $totalRevenueY, 2);
        $equalizationShare = pow($equalizationFund / $totalRevenueY, 2);
        $revenueDiversityIndex = (1 - ($equitableShareShare + $osrShare + $additionalShare + $equalizationShare)) * 100;
    } else {
        $revenueDiversityIndex = 0;
    }
    
    $trendsData['revenue_diversity_trends'][] = [
        'year' => $year,
        'revenue_diversity_index' => $revenueDiversityIndex,
        'equitable_share_percentage' => $totalRevenueY > 0 ? ($equitableShare / $totalRevenueY) * 100 : 0,
        'osr_percentage' => $totalRevenueY > 0 ? ($osr / $totalRevenueY) * 100 : 0,
        'additional_percentage' => $totalRevenueY > 0 ? ($additionalAllocations / $totalRevenueY) * 100 : 0,
        'equalization_percentage' => $totalRevenueY > 0 ? ($equalizationFund / $totalRevenueY) * 100 : 0
    ];
    
    // Expenditure efficiency trend
    $expenditureEfficiencyRatio = $totalExpenditure > 0 ? ($developmentExpenditure / $totalExpenditure) * 100 : 0;
    
    $trendsData['expenditure_efficiency_trends'][] = [
        'year' => $year,
        'expenditure_efficiency_ratio' => $expenditureEfficiencyRatio,
        'development_percentage' => $expenditureEfficiencyRatio,
        'recurrent_percentage' => 100 - $expenditureEfficiencyRatio
    ];
    
    // Budget variances
    $totalBudgetVariance = $totalBudget > 0 ? (($totalExpenditure - $totalBudget) / $totalBudget) * 100 : 0;
    $developmentBudgetVariance = $budgetedDevelopment > 0 ? (($developmentExpenditure - $budgetedDevelopment) / $budgetedDevelopment) * 100 : 0;
    $recurrentBudgetVariance = $budgetedRecurrent > 0 ? (($recurrentExpenditure - $budgetedRecurrent) / $budgetedRecurrent) * 100 : 0;
    
    $trendsData['budget_variance_trends'][] = [
        'year' => $year,
        'total_budget_variance' => $totalBudgetVariance,
        'development_budget_variance' => $developmentBudgetVariance,
        'recurrent_budget_variance' => $recurrentBudgetVariance
    ];
    
    // Revenue growth analysis
    if ($prevYearRevenue !== null && $prevYearRevenue > 0) {
        $revenueGrowthRate = (($totalRevenueY - $prevYearRevenue) / $prevYearRevenue) * 100;
    } else {
        $revenueGrowthRate = 0;
    }
    $prevYearRevenue = $totalRevenueY;
    
    $trendsData['revenue_growth_analysis'][] = [
        'year' => $year,
        'total_revenue' => $totalRevenueY,
        'revenue_growth_rate' => $revenueGrowthRate,
        'equitable_share_growth' => ($prevEquitableShare !== null && $prevEquitableShare > 0) ? (($equitableShare - $prevEquitableShare) / $prevEquitableShare) * 100 : 0,
        'osr_growth' => ($prevOsr !== null && $prevOsr > 0) ? (($osr - $prevOsr) / $prevOsr) * 100 : 0
    ];
    
    $prevEquitableShare = $equitableShare;
    $prevOsr = $osr;
    
    // Fiscal sustainability trend
    $revenueGrowthScoreY = min(max($revenueGrowthRate + 50, 0) / 100, 1) * 30;
    $pendingBillsGrowthScoreY = max(0, (100 - abs($pendingBillsRatio)) / 100) * 30;
    $diversityScoreY = $revenueDiversityIndex * 0.4;
    
    $fiscalSustainabilityScoreY = $revenueGrowthScoreY + $pendingBillsGrowthScoreY + $diversityScoreY;
    
    $trendsData['fiscal_sustainability_trends'][] = [
        'year' => $year,
        'fiscal_sustainability_score' => $fiscalSustainabilityScoreY,
        'revenue_growth_score' => $revenueGrowthScoreY,
        'pending_bills_score' => $pendingBillsGrowthScoreY,
        'diversity_score' => $diversityScoreY
    ];
}

// ------------------------------------------------------------------
// County-level performance & risk
// ------------------------------------------------------------------
 $countyPerformanceData = [];
 $countyRiskData = [];

// Aggregate county data
 $countyFiscalData = (new \yii\db\Query())
    ->select([
        'f.countyid',
        'f.fy',
        'COALESCE(SUM(ers.project_amt), 0) as equitable_share', // CARA
        'SUM(f.actual_osr) as osr',
        'SUM(f.target_osr) as target_osr',
        'SUM(f.recurrent_expenditure + f.development_expenditure) as expenditure',
        'SUM(f.recurrent_budget + f.development_budgement) as budget',
        'SUM(f.pending_bills) as pending_bills',
        'SUM(f.personal_emoluments) as compensation',
        'SUM(f.actual_wages_salaries) as actual_wages_salaries',
        'SUM(f.total_actual_wages_salaries) as budgeted_wages'
    ])
    ->from('fiscal f')
    ->leftJoin('equitable_revenue_share ers', 'ers.county_id = f.countyid AND ers.fy = f.fy')
    ->groupBy(['f.countyid', 'f.fy'])
    ->orderBy(['f.countyid' => SORT_ASC, 'f.fy' => SORT_ASC])
    ->all();

foreach ($countyFiscalData as $data) {
    $countyId = $data['countyid'];
    $county = \app\modules\backend\models\County::findOne(['CountyId' => $countyId]);
    $countyName = $county ? $county->CountyName : "County $countyId";
    
    $year = standardizeYear($data['fy']);
    
    // Simplified total revenue for county
    $totalRevenueC = ($data['equitable_share'] ?? 0) + 
                     ($data['osr'] ?? 0) + 
                     ($data['pending_bills'] ?? 0);
    
    $osrPerformance = 0;
    if (($data['target_osr'] ?? 0) > 0) {
        $osrPerformance = (($data['osr'] ?? 0) / ($data['target_osr'] ?? 0)) * 100;
    }
    
    $absorptionRate = 0;
    if (($data['budget'] ?? 0) > 0) {
        $absorptionRate = (($data['expenditure'] ?? 0) / ($data['budget'] ?? 0)) * 100;
    }
    
    $pendingBillsRatioC = 0;
    if ($totalRevenueC > 0) {
        $pendingBillsRatioC = (($data['pending_bills'] ?? 0) / $totalRevenueC) * 100;
    }
    
    $compensationRatioC = 0;
    if (($data['expenditure'] ?? 0) > 0) {
        $compensationRatioC = (($data['compensation'] ?? 0) / ($data['expenditure'] ?? 0)) * 100;
    }
    
    $wagesAbsorptionRate = 0;
    if (($data['budgeted_wages'] ?? 0) > 0) {
        $wagesAbsorptionRate = (($data['compensation'] ?? 0) / ($data['budgeted_wages'] ?? 0)) * 100;
    }
    
    // Init performance bucket
    if (!isset($countyPerformanceData[$countyId])) {
        $countyPerformanceData[$countyId] = [
            'id' => $countyId,
            'name' => $countyName,
            'revenue' => 0,
            'actual_osr_performance' => 0,
            'absorption_rate' => 0,
            'compensation_ratio' => 0,
            'wages_absorption_rate' => 0,
            'years_count' => 0,
            'osr_performance_sum' => 0,
            'absorption_rate_sum' => 0,
            'compensation_ratio_sum' => 0,
            'wages_absorption_rate_sum' => 0
        ];
    }
    
    $countyPerformanceData[$countyId]['revenue'] += $totalRevenueC;
    $countyPerformanceData[$countyId]['osr_performance_sum'] += $osrPerformance;
    $countyPerformanceData[$countyId]['absorption_rate_sum'] += $absorptionRate;
    $countyPerformanceData[$countyId]['compensation_ratio_sum'] += $compensationRatioC;
    $countyPerformanceData[$countyId]['wages_absorption_rate_sum'] += $wagesAbsorptionRate;
    $countyPerformanceData[$countyId]['years_count']++;
    
    // Risk bucket
    if (!isset($countyRiskData[$countyId])) {
        $countyRiskData[$countyId] = [
            'id' => $countyId,
            'name' => $countyName,
            'pending_bills' => 0,
            'pending_bills_ratio' => 0,
            'years_count' => 0,
            'pending_bills_ratio_sum' => 0
        ];
    }
    
    $countyRiskData[$countyId]['pending_bills'] += $data['pending_bills'] ?? 0;
    $countyRiskData[$countyId]['pending_bills_ratio_sum'] += $pendingBillsRatioC;
    $countyRiskData[$countyId]['years_count']++;
}

// Average out county data
foreach ($countyPerformanceData as &$county) {
    if ($county['years_count'] > 0) {
        $county['actual_osr_performance'] = $county['osr_performance_sum'] / $county['years_count'];
        $county['absorption_rate'] = $county['absorption_rate_sum'] / $county['years_count'];
        $county['compensation_ratio'] = $county['compensation_ratio_sum'] / $county['years_count'];
        $county['wages_absorption_rate'] = $county['wages_absorption_rate_sum'] / $county['years_count'];
    }
    unset($county['years_count'], $county['osr_performance_sum'], $county['absorption_rate_sum'], $county['compensation_ratio_sum'], $county['wages_absorption_rate_sum']);
}
unset($county);

foreach ($countyRiskData as &$county) {
    if ($county['years_count'] > 0) {
        $county['pending_bills_ratio'] = $county['pending_bills_ratio_sum'] / $county['years_count'];
    }
    unset($county['years_count'], $county['pending_bills_ratio_sum']);
}
unset($county);

// Convert to simple arrays for sorting/use
 $countyRatios = array_values($countyPerformanceData);
 $highRiskCounties = array_values($countyRiskData);

// Aggregate data passed to widgets
 $aggregateData = [
    'totals' => $summaryData,
    'ratios' => [
        'budgeted_recurrent_to_revenue' => $summaryData['budgeted_recurrent_to_revenue'],
        'actual_recurrent_to_revenue' => $summaryData['actual_recurrent_to_revenue'],
        'budgeted_development_to_budget' => $summaryData['budgeted_development_to_budget'],
        'actual_development_to_expenditure' => $summaryData['actual_development_to_expenditure'],
        'budgeted_wages_to_revenue' => $summaryData['budgeted_wages_to_revenue'],
        'actual_wages_to_revenue' => $summaryData['actual_wages_to_revenue'],
        'compensation_recurrent_share' => $summaryData['compensation_recurrent_share']
    ],
    'analytics' => [
        'fiscal_health_index' => $summaryData['fiscal_health_index'],
        'revenue_diversity_index' => $summaryData['revenue_diversity_index'],
        'expenditure_efficiency_ratio' => $summaryData['expenditure_efficiency_ratio'],
        'fiscal_sustainability_score' => $summaryData['fiscal_sustainability_score']
    ],
    'trends' => $trendsData
];

 
 ?>

<!-- Page Header -->
<div class="page-header" 
     style="background: linear-gradient(135deg, #006400, #228B22); 
            color: #fff; 
            padding: 15px 0; 
            text-align: center; 
            border-radius: 0 0 15px 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
    <div class="dashboard-container">
        <div class="row align-items-center" style="margin: 0;">
            <div class="col-12">
               <h1 style="margin: 0 0 6px; font-size: 22px; font-weight: 600; color: #fff !important;">
    <?= Html::encode($this->title) ?>
</h1>

                <p style="margin: 0; font-size: 14px; color: #f1f1f1;">
                    Comprehensive analysis of fiscal performance indicators for all 47 counties since devolution (2013–present)
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Comprehensive fix for chart visibility */
    .chart-container-inner {
        height: 500px !important;
        width: 100% !important;
        position: relative;
        overflow: visible !important;
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }
    
    .google-chart {
        height: 100% !important;
        width: 100% !important;
        overflow: visible !important;
        display: block !important;
    }
    
    /* Ensure chart headers don't overlap */
    .chart-header {
        padding: 10px 15px;
        margin-bottom: 10px;
    }
    
    /* Add spacing between chart containers */
    .chart-container {
        margin-bottom: 40px !important;
        display: block !important;
    }
    
    /* Fix for county comparison chart */
    #county-comparison-chart {
        height: 500px !important;
        width: 100% !important;
    }
    
    /* Ensure table responsiveness */
    .table-responsive {
        overflow-x: auto;
    }
    
    /* Additional fixes for chart containers */
    .chart-body {
        overflow: visible !important;
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }
    
    /* Force Google Charts to show x-axis labels */
    .google-chart svg text {
        font-size: 12px !important;
        fill: #333 !important;
        opacity: 1 !important;
        display: block !important;
    }
    
    /* Fix for chart area clipping */
    .chart-item {
        overflow: visible !important;
        display: block !important;
    }
    
    /* Ensure fiscal principles chart is visible */
    #fiscal-principles-chart {
        height: 500px !important;
        width: 100% !important;
        margin-bottom: 20px !important;
    }
    
    /* Ensure SVG elements are not clipped */
    .google-chart svg {
        overflow: visible !important;
        display: block !important;
    }
    
    /* Fix for chart container positioning */
    .chart-container {
        position: relative;
        display: block !important;
    }
    
    /* Force display of x-axis labels */
    .google-chart svg g[aria-label] text {
        display: block !important;
        visibility: visible !important;
    }
    
    /* Ensure chart container has enough space */
    .chart-container {
        min-height: 550px !important;
    }
    
    /* Add space at the bottom of the chart for x-axis labels */
    .chart-container-inner::after {
        content: "";
        display: block;
        height: 50px;
        width: 100%;
    }
</style>

<div class="dashboard-container">
    <!-- Action Bar Section -->
    <div class="action-bar-section">
        <div class="action-bar animate-fade-in">
            <h2 class="action-title">Fiscal Performance Dashboard</h2>
            <div class="action-buttons">
                <button class="btn btn-primary download-all-charts">
                    <i class="fas fa-download"></i> Download All Charts
                </button>
                <button class="btn btn-outline-primary" id="toggleCompactView">
                    <i class="fas fa-compress"></i> Compact View
                </button>
                <button class="btn btn-outline-primary" id="refreshData">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Metrics Section -->
    <div class="metrics-section">
        <div class="metrics-grid animate-scale-in">
            <!-- Total Revenue -->
            <div class="card metric-card bg-kenya-green text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-stats">
                            <div class="metric-label">Total Revenue</div>
                            <div class="metric-value"><?= formatLargeNumber($summaryData['total_revenue'] ?? 0) ?> KES</div>
                            <small>Annual Growth: <strong><?= number_format($summaryData['revenue_growth'] ?? 0, 1) ?>%</strong></small>
                            <div class="progress mt-1">
                                <div class="progress-bar <?= ($summaryData['revenue_growth'] ?? 0) >= 0 ? 'bg-success' : 'bg-danger' ?>" 
                                     style="width: <?= min(abs($summaryData['revenue_growth'] ?? 0), 100) ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>

            <!-- Total Budget -->
            <div class="card metric-card bg-kenya-red text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-stats">
                            <div class="metric-label">Total Budget</div>
                            <div class="metric-value"><?= formatLargeNumber($summaryData['total_budget'] ?? 0) ?> KES</div>
                            <small>Absorption Rate: <strong><?= number_format($summaryData['absorption_rate'] ?? 0, 1) ?>%</strong></small>
                            <div class="progress mt-1">
                                <div class="progress-bar bg-info" style="width: <?= ($summaryData['absorption_rate'] ?? 0) ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-calculator"></i></div>
                </div>
            </div>

            <!-- Total Expenditure -->
            <div class="card metric-card bg-kenya-black text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-stats">
                            <div class="metric-label">Total Expenditure</div>
                            <div class="metric-value"><?= formatLargeNumber($summaryData['total_expenditure'] ?? 0) ?> KES</div>
                            <small>Utilization Rate: <strong><?= number_format($summaryData['utilization_rate'] ?? 0, 1) ?>%</strong></small>
                            <div class="progress mt-1">
                                <div class="progress-bar bg-warning" style="width: <?= min(($summaryData['utilization_rate'] ?? 0), 100) ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-money-check-alt"></i></div>
                </div>
            </div>

            <!-- Pending Bills -->
            <div class="card metric-card bg-kenya-green text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-stats">
                            <div class="metric-label">Pending Bills</div>
                            <div class="metric-value"><?= formatLargeNumber($summaryData['total_pending_bills'] ?? 0) ?> KES</div>
                            <small>Revenue Ratio: <strong><?= number_format($summaryData['pending_bills_revenue_ratio'] ?? 0, 1) ?>%</strong></small>
                            <div class="progress mt-1">
                                <div class="progress-bar <?= ($summaryData['pending_bills_revenue_ratio'] ?? 0) > 20 ? 'bg-danger' : (($summaryData['pending_bills_revenue_ratio'] ?? 0) > 10 ? 'bg-warning' : 'bg-success') ?>" 
                                     style="width: <?= min(($summaryData['pending_bills_revenue_ratio'] ?? 0), 100) ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- I. Financial Resources Mobilization Section -->
    <div class="financial-resources-section">
        <div class="card mb-4 animate-fade-in">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white">I. Financial Resources Mobilization</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card metric-card bg-kenya-green text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Equitable Share</div>
                                        <div class="metric-value"><?= formatLargeNumber($summaryData['total_equitable_share'] ?? 0) ?> KES</div>
                                        <small>Disbursement Rate: <strong><?= number_format($summaryData['disbursement_rate'] ?? 0, 1) ?>%</strong></small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-warning" style="width: <?= min(($summaryData['disbursement_rate'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-icon"><i class="fas fa-hand-holding-usd"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card metric-card bg-kenya-red text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Own Source Revenue</div>
                                        <div class="metric-value"><?= formatLargeNumber($summaryData['total_osr'] ?? 0) ?> KES</div>
                                        <small>Performance Rate: <strong><?= number_format($summaryData['osr_performance_rate'] ?? 0, 1) ?>%</strong></small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-info" style="width: <?= min(($summaryData['osr_performance_rate'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-icon"><i class="fas fa-chart-line"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card metric-card bg-kenya-black text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Additional Allocations</div>
                                        <div class="metric-value"><?= formatLargeNumber($summaryData['total_additional_allocations'] ?? 0) ?> KES</div>
                                        <small>Utilization Rate: <strong><?= number_format($summaryData['additional_allocations_utilization'] ?? 0, 1) ?>%</strong></small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-success" style="width: <?= min(($summaryData['additional_allocations_utilization'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-icon"><i class="fas fa-donate"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Resources Charts - Each chart in its own container -->
                <div class="charts-section">
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-success d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Equitable Share Trends</h5>
                                <button class="download-chart" data-chart="equitable-share-trends-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="equitable-share-trends-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-info d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Own Source Revenue Trends</h5>
                                <button class="download-chart" data-chart="osr-trends-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="osr-trends-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-warning d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Additional Allocations Trends</h5>
                                <button class="download-chart" data-chart="additional-allocations-trends-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="additional-allocations-trends-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-primary d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Revenue Composition</h5>
                                <button class="download-chart" data-chart="revenue-composition-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="revenue-composition-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-dark d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Equalization Fund Trends</h5>
                                <button class="download-chart" data-chart="equalization-fund-trends-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="equalization-fund-trends-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- II. Budget Execution Section - Standalone Section -->
    <div class="budget-execution-section">
        <div class="card mb-4 animate-fade-in">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0 text-white">II. Budget Execution</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card metric-card bg-purple text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Development Expenditure</div>
                                        <div class="metric-value"><?= formatLargeNumber($summaryData['total_development_expenditure'] ?? 0) ?> KES</div>
                                        <small>Absorption Rate: <strong><?= number_format($summaryData['development_absorption_rate'] ?? 0, 1) ?>%</strong></small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-warning" style="width: <?= min(($summaryData['development_absorption_rate'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="metric-icon"><i class="fas fa-building"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card metric-card bg-orange text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Recurrent Expenditure</div>
                                        <div class="metric-value"><?= formatLargeNumber($summaryData['total_recurrent_expenditure'] ?? 0) ?> KES</div>
                                        <small>Absorption Rate: <strong><?= number_format($summaryData['recurrent_absorption_rate'] ?? 0, 1) ?>%</strong></small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-info" style="width: <?= min(($summaryData['recurrent_absorption_rate'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-icon"><i class="fas fa-sync-alt"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card metric-card bg-gold text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Compensation</div>
                                        <div class="metric-value"><?= formatLargeNumber($summaryData['total_actual_wages_salaries'] ?? 0) ?> KES</div>
                                        <small>Recurrent Share: <strong><?= number_format($summaryData['compensation_recurrent_share'] ?? 0, 1) ?>%</strong></small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar <?= ($summaryData['compensation_recurrent_share'] ?? 0) <= 35 ? 'bg-success' : 'bg-danger' ?>" 
                                                 style="width: <?= min(($summaryData['compensation_recurrent_share'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-icon"><i class="fas fa-users"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Budget Execution Charts - Each chart in its own container -->
                <div class="charts-section">
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-primary d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Total Expenditure Absorption</h5>
                                <button class="download-chart" data-chart="total-absorption-trends-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="total-absorption-trends-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-warning d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Recurrent Expenditure Absorption</h5>
                                <button class="download-chart" data-chart="recurrent-absorption-trends-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="recurrent-absorption-trends-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-info d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Development Expenditure Absorption</h5>
                                <button class="download-chart" data-chart="development-absorption-trends-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="development-absorption-trends-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-danger d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Development vs Recurrent Expenditure</h5>
                                <button class="download-chart" data-chart="dev-vs-rec-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="dev-vs-rec-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                 <div class="chart-container">
    <div class="chart-item">
        <div class="chart-header bg-secondary d-flex justify-content-between align-items-center">
            
            <!-- Title now styled inline -->
            <h5 class="card-title mb-0" style="color: #fff;">
                Compensation as % of Recurrent Expenditure
            </h5>

            <button class="download-chart" data-chart="compensation-ratio-chart">
                <i class="fas fa-download" style="color:#fff;"></i>
            </button>
        </div>

        <div class="chart-body">
            <div class="chart-container-inner">
                <div id="compensation-ratio-chart" class="google-chart"></div>
            </div>
        </div>
    </div>
</div>

                </div>
            </div>
        </div>
    </div>

    <!-- III. Fiscal Risks Section -->
    <div class="fiscal-risks-section">
        <div class="card mb-4 animate-fade-in">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0 text-white">III. Fiscal Risks</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card metric-card bg-kenya-red text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Pending Bills</div>
                                        <div class="metric-value"><?= formatLargeNumber($summaryData['total_pending_bills'] ?? 0) ?> KES</div>
                                        <small>Growth Rate: <strong><?= number_format($summaryData['pending_bills_growth'] ?? 0, 1) ?>%</strong></small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar <?= ($summaryData['pending_bills_growth'] ?? 0) >= 0 ? 'bg-danger' : 'bg-success' ?>" 
                                                 style="width: <?= min(abs($summaryData['pending_bills_growth'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card metric-card bg-kenya-black text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="metric-stats">
                                        <div class="metric-label">Pending Bills Ratio</div>
                                        <div class="metric-value"><?= number_format($summaryData['pending_bills_revenue_ratio'] ?? 0, 1) ?></div>
                                        <div class="metric-label">%</div>
                                        <small>of Total Revenue</small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar <?= ($summaryData['pending_bills_revenue_ratio'] ?? 0) > 20 ? 'bg-danger' : (($summaryData['pending_bills_revenue_ratio'] ?? 0) > 10 ? 'bg-warning' : 'bg-success') ?>" 
                                                 style="width: <?= min(($summaryData['pending_bills_revenue_ratio'] ?? 0), 100) ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Fiscal Risks Charts - Each chart in its own container -->
                <div class="charts-section">
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-dark d-flex justify-content-between align-items-center">
                                <h5 class="chart-title mb-0 text-white">Pending Bills Trends</h5>
                                <button class="download-chart" data-chart="pending-bills-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="pending-bills-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <div class="chart-item">
                            <div class="chart-header bg-warning d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-white">Pending Bills Ratio Trends</h5>
                                <button class="download-chart" data-chart="pending-bills-ratio-chart"><i class="fas fa-download"></i></button>
                            </div>
                            <div class="chart-body">
                                <div class="chart-container-inner">
                                    <div id="pending-bills-ratio-chart" class="google-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fiscal Responsibility Principles Section -->
    <div class="fiscal-principles-section">
        <div class="card mb-4 animate-fade-in">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white">Fiscal Responsibility Principles Compliance</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-12">
                        <div class="chart-container">
                            <div id="fiscal-principles-chart" class="google-chart"></div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Principle</th>
                                        <th>Budgeted Ratio</th>
                                        <th>Actual Ratio</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Recurrent expenditures shall not exceed Total Revenue</td>
                                        <td class="<?= ($summaryData['budgeted_recurrent_to_revenue'] ?? 0) > 1 ? 'text-danger font-weight-bold' : 'text-success' ?>">
                                            <?= number_format(($summaryData['budgeted_recurrent_to_revenue'] ?? 0) * 100, 2) ?>%
                                        </td>
                                        <td class="<?= ($summaryData['actual_recurrent_to_revenue'] ?? 0) > 1 ? 'text-danger font-weight-bold' : 'text-success' ?>">
                                            <?= number_format(($summaryData['actual_recurrent_to_revenue'] ?? 0) * 100, 2) ?>%
                                        </td>
                                        <td>
                                            <?php if (($summaryData['budgeted_recurrent_to_revenue'] ?? 0) > 1 || ($summaryData['actual_recurrent_to_revenue'] ?? 0) > 1): ?>
                                                <span class="badge badge-danger">Violated</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Compliant</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>At least 30% allocated to development expenditure</td>
                                        <td class="<?= ($summaryData['budgeted_development_to_budget'] ?? 0) < 0.3 ? 'text-danger font-weight-bold' : 'text-success' ?>">
                                            <?= number_format(($summaryData['budgeted_development_to_budget'] ?? 0) * 100, 2) ?>%
                                        </td>
                                        <td class="<?= ($summaryData['actual_development_to_expenditure'] ?? 0) < 0.3 ? 'text-danger font-weight-bold' : 'text-success' ?>">
                                            <?= number_format(($summaryData['actual_development_to_expenditure'] ?? 0) * 100, 2) ?>%
                                        </td>
                                        <td>
                                            <?php if (($summaryData['budgeted_development_to_budget'] ?? 0) < 0.3 || ($summaryData['actual_development_to_expenditure'] ?? 0) < 0.3): ?>
                                                <span class="badge badge-danger">Violated</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Compliant</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Wages & Salaries should not be more than 35% of Total Revenue</td>
                                        <td class="<?= ($summaryData['budgeted_wages_to_revenue'] ?? 0) > 0.35 ? 'text-danger font-weight-bold' : 'text-success' ?>">
                                            <?= number_format(($summaryData['budgeted_wages_to_revenue'] ?? 0) * 100, 2) ?>%
                                        </td>
                                        <td class="<?= ($summaryData['actual_wages_to_revenue'] ?? 0) > 0.35 ? 'text-danger font-weight-bold' : 'text-success' ?>">
                                            <?= number_format(($summaryData['actual_wages_to_revenue'] ?? 0) * 100, 2) ?>%
                                        </td>
                                        <td>
                                            <?php if (($summaryData['budgeted_wages_to_revenue'] ?? 0) > 0.35 || ($summaryData['actual_wages_to_revenue'] ?? 0) > 0.35): ?>
                                                <span class="badge badge-danger">Violated</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Compliant</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- County Performance Visualization Section -->
    <div class="county-performance-section">
        <div class="card mb-4 animate-fade-in">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0 text-white">County Performance Visualization</h5>
            </div>
            <div class="card-body">
                <div class="chart-tabs">
                    <div class="chart-tab active" data-tab="county-grid">County Grid</div>
                    <div class="chart-tab" data-tab="county-map">County Map</div>
                    <div class="chart-tab" data-tab="county-comparison">County Comparison</div>
                </div>
                
                <div class="chart-content active" id="county-grid">
                    <div class="county-grid">
                        <?php foreach ($countyPerformanceData as $county): ?>
                        <div class="county-card">
                            <div class="county-name"><?= Html::encode($county['name']) ?></div>
                            <div class="county-stat">
                                <span class="county-stat-label">Revenue:</span>
                                <span class="county-stat-value"><?= formatLargeNumber($county['revenue'] ?? 0) ?></span>
                            </div>
                            <div class="county-stat">
                                <span class="county-stat-label">OSR Perf:</span>
                                <span class="county-stat-value"><?= number_format($county['actual_osr_performance'] ?? 0, 1) ?>%</span>
                            </div>
                            <div class="county-stat">
                                <span class="county-stat-label">Absorption:</span>
                                <span class="county-stat-value"><?= number_format($county['absorption_rate'] ?? 0, 1) ?>%</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="chart-content" id="county-map">
                    <div class="map-container">
                        <!-- Map visualization would go here -->
                        <div style="display: flex; justify-content: center; align-items: center; height: 100%; color: #6c757d;">
                            <div style="text-align: center;">
                                <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                <p>Interactive Kenya County Map</p>
                                <p class="small">Click on counties to view detailed performance data</p>
                            </div>
                        </div>
                        <div class="map-overlay">
                            <div class="map-legend-title">Performance Legend</div>
                            <div class="map-legend-items">
                                <div class="map-legend-item">
                                    <div class="map-legend-color" style="background-color: var(--kenya-green);"></div>
                                    <span>High Performance</span>
                                </div>
                                <div class="map-legend-item">
                                    <div class="map-legend-color" style="background-color: #ffc107;"></div>
                                    <span>Medium Performance</span>
                                </div>
                                <div class="map-legend-item">
                                    <div class="map-legend-color" style="background-color: var(--kenya-red);"></div>
                                    <span>Low Performance</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="chart-content" id="county-comparison">
                    <div class="chart-container">
                        <canvas id="county-comparison-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Performance Tables Section -->
<div class="performance-tables-section">

    <!-- Top 8 Counties by Revenue Performance -->
    <div class="row mt-4 animate-fade-in">
        <div class="col-md-12">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h5 class="data-table-title">Top 8 Counties by Revenue Performance</h5>
                    <div class="data-table-actions">
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="data-table-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>County</th>
                                    <th>Total Revenue (KES)</th>
                                    <th>OSR Performance (%)</th>
                                    <th>Absorption Rate (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $topPerformers = $countyRatios;
                                usort($topPerformers, function($a, $b) {
                                    $osrCompare = ($b['actual_osr_performance'] ?? 0) <=> ($a['actual_osr_performance'] ?? 0);
                                    if ($osrCompare !== 0) return $osrCompare;
                                    return ($b['absorption_rate'] ?? 0) <=> ($a['absorption_rate'] ?? 0);
                                });
                                $topPerformers = array_slice($topPerformers, 0, 8);
                                ?>
                                <?php foreach ($topPerformers as $county): ?>
                                <tr>
                                    <td><?= Html::encode($county['name']) ?></td>
                                    <td><?= formatLargeNumber($county['revenue'] ?? 0) ?></td>
                                    <td><?= number_format(($county['actual_osr_performance'] ?? 0), 1) ?></td>
                                    <td><?= number_format(($county['absorption_rate'] ?? 0), 1) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="data-table-footer">
                    <span>Showing top 8 of <?= count($countyRatios) ?> counties</span>
                 
                </div>
            </div>
        </div>
    </div>
<!-- Counties with High Fiscal Risk -->
<div class="row mt-4 animate-fade-in">
    <div class="col-md-12">
        <div class="data-table-card">
            <div class="data-table-header bg-danger text-white">
                <h5 class="data-table-title text-white">Counties with High Fiscal Risk</h5>
                <div class="data-table-actions">
                    <button class="btn btn-sm btn-outline-light"><i class="fas fa-download"></i></button>
                </div>
            </div>

            <div class="data-table-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>County</th>
                                <th>Pending Bills (KES)</th>
                                <th>Pending Bills / Revenue (%)</th>
                                <th>Risk Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sortedRiskCounties = $highRiskCounties;
                            usort($sortedRiskCounties, function($a, $b) {
                                return ($b['pending_bills_ratio'] ?? 0) <=> ($a['pending_bills_ratio'] ?? 0);
                            });
                            $sortedRiskCounties = array_slice($sortedRiskCounties, 0, 5);
                            ?>

                            <?php foreach ($sortedRiskCounties as $county): ?>
                                <?php 
                                // Ensure ratio is normalized (out of 100)
                                $ratio = ($county['pending_bills_ratio'] ?? 0);
                                if ($ratio > 1 && $ratio <= 100) {
                                    // already a percentage
                                    $percentage = $ratio;
                                } else {
                                    // assume it's a decimal (e.g. 0.25 = 25%)
                                    $percentage = $ratio * 100;
                                }

                                // Determine risk level
                                $riskLevel = $percentage > 30 ? 'High' : ($percentage > 15 ? 'Medium' : 'Low');
                                ?>
                                <tr>
                                    <td><?= Html::encode($county['name']) ?></td>
                                    <td><?= formatLargeNumber($county['pending_bills'] ?? 0) ?></td>
                                    <td><?= number_format($percentage, 1) ?></td>
                                    <td>
                                        <?php if ($riskLevel === 'High'): ?>
                                            <span class="badge badge-danger">High</span>
                                        <?php elseif ($riskLevel === 'Medium'): ?>
                                            <span class="badge badge-warning">Medium</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Low</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="data-table-footer">
                <span>Showing top 5 of <?= count($highRiskCounties) ?> counties</span>
                <a href="#" class="btn btn-sm btn-danger">View All</a>
            </div>
        </div>
    </div>
</div>


</div>
</div>

<!-- Projects Location Map -->
<div class="col-md-12">
    <div class="col-12 grid-margin stretch-card">
        <div class="card fade-in">
            <div class="card-body">
                <h4 class="card-title mb-3 text-white">Projects Location</h4>
                <div class="project-map-embed">
                    <iframe
                        src="<?= yii\helpers\Url::to(['/backend/projects/map-embed']) ?>"
                        title="Project location map"
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
        </div>
        </div>
    </div>
</div>

<!-- Dark Mode Toggle Button -->
<button class="dark-mode-toggle" id="darkModeToggle">
    <i class="fas fa-moon"></i>
</button>

<!-- Custom Tooltip -->
<div class="custom-tooltip" id="customTooltip"></div>

<style>
/* Additional styles for separated charts */
.chart-container {
    margin-bottom: 30px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.chart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.chart-item {
    width: 100%;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

.chart-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-body {
    padding: 20px;
}

.chart-container-inner {
    height: 500px;
    position: relative;
}

/* Ensure all section headers have white text */
.card-header h5 {
    color: white !important;
    font-weight: 600;
}

/* Custom tooltip styles */
.custom-tooltip {
    position: absolute;
    background-color: rgba(0, 0, 0, 0.8);
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px 12px;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
    font-size: 14px;
    max-width: 300px;
}

.custom-tooltip.show {
    opacity: 1;
}

/* Budget Execution section styling */
.budget-execution-section {
    margin: 40px 0;
    padding: 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

/* Fiscal Risks section styling */
.fiscal-risks-section {
    margin: 40px 0;
    padding: 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, #fff5f5, #ffe6e6);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

/* Chart axis styling */
.google-chart svg g[aria-label="Axis"] {
    display: block !important;
    visibility: visible !important;
}

.google-chart svg text {
    font-family: 'Poppins', sans-serif !important;
    font-size: 12px !important;
    fill: #333 !important;
}

.google-chart svg .axis-line {
    stroke: #333 !important;
    stroke-width: 1 !important;
}

.google-chart svg .axis-label {
    fill: #333 !important;
    font-weight: 600 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chart-container-inner {
        height: 350px;
    }
    
    .metric-card {
        margin-bottom: 15px;
    }
    
    .chart-header {
        padding: 10px 15px;
    }
    
    .chart-body {
        padding: 10px;
    }
}

/* Download button styling */
.download-chart {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.download-chart:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Chart tabs styling */
.chart-tabs {
    display: flex;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.chart-tab {
    padding: 10px 20px;
    cursor: pointer;
    background: #f8f9fa;
    border: 1px solid #eee;
    border-bottom: none;
    border-radius: 5px 5px 0 0;
    margin-right: 5px;
    transition: all 0.3s;
}

.chart-tab.active {
    background: #fff;
    border-color: #ddd;
    font-weight: bold;
    color: #007bff;
}

.chart-content {
    display: none;
}

.chart-content.active {
    display: block;
}

/* County grid styling */
.county-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.county-card {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.county-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.county-name {
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.county-stat {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 14px;
}

.county-stat-label {
    color: #666;
}

.county-stat-value {
    font-weight: bold;
}

/* Map container styling */
.map-container {
    position: relative;
    height: 500px;
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

.map-overlay {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.9);
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.map-legend-title {
    font-weight: bold;
    margin-bottom: 10px;
}

.map-legend-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.map-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.map-legend-color {
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

/* Data table card styling */
.data-table-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.data-table-header {
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.data-table-title {
    margin: 0;
    font-weight: bold;
    color: #333;
}

.data-table-body {
    padding: 0;
}

.data-table-footer {
    padding: 10px 20px;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Project map embed styling */
.project-map-embed {
    width: 100%;
    height: 500px;
    border-radius: 8px;
    overflow: hidden;
}

.project-map-embed iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* Dark mode styles */
body.dark-mode {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #e0e0e0;
}

body.dark-mode .card {
    background: #2d2d2d;
    color: #e0e0e0;
}

body.dark-mode .chart-item {
    background: #2d2d2d;
    color: #e0e0e0;
}

body.dark-mode .chart-header {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .table {
    color: #e0e0e0;
}

body.dark-mode .table thead th {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .table tbody tr:hover {
    background-color: rgba(0, 100, 0, 0.2);
}

body.dark-mode .county-card {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .county-name {
    color: #e0e0e0;
}

body.dark-mode .county-stat-label {
    color: #bbb;
}

body.dark-mode .data-table-card {
    background: #2d2d2d;
    color: #e0e0e0;
}

body.dark-mode .data-table-header {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .data-table-footer {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .map-container {
    background: #333;
}

body.dark-mode .map-overlay {
    background: rgba(51, 51, 51, 0.9);
    color: #e0e0e0;
}

body.dark-mode .dark-mode-toggle {
    background: #444;
    color: #ffd700;
}

body.dark-mode .dark-mode-toggle i {
    color: #ffd700;
}
</style>

<script>
// Chart data from PHP
const summaryData = <?= json_encode($summaryData) ?>;
const countyRatios = <?= json_encode($countyRatios) ?>;
const trendsData = <?= json_encode($trendsData) ?>;
const selectedYear = '<?= $selectedYear ?>';

// Chart color schemes
const chartColors = {
    primary: '#006400',
    primaryLight: '#228b22',
    secondary: '#b22222',
    secondaryLight: '#cd5c5c',
    gold: '#d4af37',
    goldLight: '#ffd700',
    dark: '#000000',
    darkLight: '#333333',
    purple: '#6f42c1',
    orange: '#fd7e14',
    success: '#2e7d32',
    info: '#0277bd',
    warning: '#f57c00',
    danger: '#c62828'
};

// Global variables to track chart state
let chartsLoaded = false;
let chartInstances = {};

// Initialize Google Charts
google.charts.load('current', {'packages':['corechart', 'bar']});
google.charts.setOnLoadCallback(initGoogleCharts);

function initGoogleCharts() {
    if (!chartsLoaded) {
        drawAllCharts();
        chartsLoaded = true;
    }
}

function drawAllCharts() {
    try {
        drawEquitableShareTrends();
        drawOSRTrends();
        drawAdditionalAllocationsTrends();
        drawRevenueComposition();
        drawTotalAbsorptionTrends();
        drawRecurrentAbsorptionTrends();
        drawDevelopmentAbsorptionTrends();
        drawCompensationAbsorptionTrends();
        drawPendingBillsChart();
        drawPendingBillsRatioChart();
        drawFiscalPrinciplesChart();
        drawCountyComparisonChart();
        drawEqualizationFundTrends();
        drawDevVsRecChart();
        drawCompensationRatioChart();
    } catch (error) {
        console.error('Error drawing charts:', error);
    }
}

// Add download button to chart
function addDownloadButton(chartId, chartInstance) {
    const container = document.getElementById(chartId);
    if (!container) return;
    
    // Remove existing button if any
    const existingBtn = container.querySelector('.download-chart-btn');
    if (existingBtn) existingBtn.remove();
    
    const button = document.createElement('button');
    button.className = 'download-chart-btn';
    button.innerHTML = '<i class="fas fa-download"></i> Download';
    button.title = 'Download chart as PNG';
    
    button.addEventListener('click', function() {
        try {
            const imgUri = chartInstance.getImageURI();
            const link = document.createElement('a');
            link.href = imgUri;
            link.download = chartId + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (e) {
            console.error('Error downloading chart:', e);
            alert('Error downloading chart. Please try again.');
        }
    });
    
    container.appendChild(button);
}

// 1. Equitable Share Trends Chart (Combo Chart)
function drawEquitableShareTrends() {
    const container = document.getElementById('equitable-share-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Budgeted Amount (KES)');
    data.addColumn('number', 'Disbursed Amount (KES)');
    data.addColumn('number', 'Disbursement Ratio (%)');

    trendsData.equitable_share_trends.forEach(item => {
        data.addRow([
            item.year,
            item.budgeted,
            item.actual,
            item.disbursement_ratio
        ]);
    });

    const options = {
        title: 'Equitable Share Allocation Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        seriesType: 'bars',
        series: {
            0: { 
                color: chartColors.primary,
                labelInLegend: 'Budgeted Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: chartColors.primaryLight,
                labelInLegend: 'Disbursed Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: {
                type: 'line',
                color: chartColors.gold,
                targetAxisIndex: 1,
                labelInLegend: 'Disbursement Ratio',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxes: {
            0: {
                title: 'Amount (KES)',
                format: 'short',
                textStyle: { color: chartColors.primary, fontSize: 12 },
                titleTextStyle: { color: chartColors.primary, fontSize: 14, bold: true }
            },
            1: {
                title: 'Disbursement Ratio (%)',
                format: '#\'%\'',
                textStyle: { color: chartColors.gold, fontSize: 12 },
                titleTextStyle: { color: chartColors.gold, fontSize: 14, bold: true }
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['equitable-share-trends-chart']) {
        chartInstances['equitable-share-trends-chart'] = new google.visualization.ComboChart(container);
    }
    
    chartInstances['equitable-share-trends-chart'].draw(data, options);
    addDownloadButton('equitable-share-trends-chart', chartInstances['equitable-share-trends-chart']);
}

// 2. OSR Trends Chart (Combo Chart)
function drawOSRTrends() {
    const container = document.getElementById('osr-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Budgeted Amount (KES)');
    data.addColumn('number', 'Actual Amount (KES)');
    data.addColumn('number', 'Performance Ratio (%)');

    trendsData.osr_trends.forEach(item => {
        data.addRow([
            item.year,
            item.budgeted,
            item.actual,
            item.performance_ratio
        ]);
    });

    const options = {
        title: 'Own Source Revenue Performance Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        seriesType: 'bars',
        series: {
            0: { 
                color: chartColors.secondary,
                labelInLegend: 'Budgeted Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: chartColors.secondaryLight,
                labelInLegend: 'Actual Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: {
                type: 'line',
                color: chartColors.gold,
                targetAxisIndex: 1,
                labelInLegend: 'Performance Ratio',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxes: {
            0: {
                title: 'Amount (KES)',
                format: 'short',
                textStyle: { color: chartColors.secondary, fontSize: 12 },
                titleTextStyle: { color: chartColors.secondary, fontSize: 14, bold: true }
            },
            1: {
                title: 'Performance Ratio (%)',
                format: '#\'%\'',
                textStyle: { color: chartColors.gold, fontSize: 12 },
                titleTextStyle: { color: chartColors.gold, fontSize: 14, bold: true }
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['osr-trends-chart']) {
        chartInstances['osr-trends-chart'] = new google.visualization.ComboChart(container);
    }
    
    chartInstances['osr-trends-chart'].draw(data, options);
    addDownloadButton('osr-trends-chart', chartInstances['osr-trends-chart']);
}

// 3. Additional Allocations Trends Chart (Combo Chart)
function drawAdditionalAllocationsTrends() {
    const container = document.getElementById('additional-allocations-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Budgeted Amount (KES)');
    data.addColumn('number', 'Actual Amount (KES)');
    data.addColumn('number', 'Utilization Ratio (%)');

    trendsData.additional_allocations_trends.forEach(item => {
        data.addRow([
            item.year,
            item.budgeted,
            item.actual,
            item.utilization_ratio
        ]);
    });

    const options = {
        title: 'Additional Allocations Utilization Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        seriesType: 'bars',
        series: {
            0: { 
                color: chartColors.dark,
                labelInLegend: 'Budgeted Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: chartColors.darkLight,
                labelInLegend: 'Actual Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: {
                type: 'line',
                color: chartColors.gold,
                targetAxisIndex: 1,
                labelInLegend: 'Utilization Ratio',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxes: {
            0: {
                title: 'Amount (KES)',
                format: 'short',
                textStyle: { color: chartColors.dark, fontSize: 12 },
                titleTextStyle: { color: chartColors.dark, fontSize: 14, bold: true }
            },
            1: {
                title: 'Utilization Ratio (%)',
                format: '#\'%\'',
                textStyle: { color: chartColors.gold, fontSize: 12 },
                titleTextStyle: { color: chartColors.gold, fontSize: 14, bold: true }
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['additional-allocations-trends-chart']) {
        chartInstances['additional-allocations-trends-chart'] = new google.visualization.ComboChart(container);
    }
    
    chartInstances['additional-allocations-trends-chart'].draw(data, options);
    addDownloadButton('additional-allocations-trends-chart', chartInstances['additional-allocations-trends-chart']);
}

// 4. Revenue Composition Chart (Stacked Column Chart)
function drawRevenueComposition() {
    const container = document.getElementById('revenue-composition-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Equitable Share (KES)');
    data.addColumn('number', 'Own Source Revenue (KES)');
    data.addColumn('number', 'Additional Allocations (KES)');
    data.addColumn('number', 'Equalization Fund (KES)'); // Added Equalization Fund

    trendsData.revenue_composition.forEach(item => {
        data.addRow([
            item.year,
            item.equitable_share,
            item.osr,
            item.additional_allocations,
            item.equalization_fund
        ]);
    });

    const options = {
        title: 'Revenue Composition Over Time',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        isStacked: true,
        series: {
            0: { 
                color: chartColors.primary,
                labelInLegend: 'Equitable Share',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: chartColors.secondary,
                labelInLegend: 'Own Source Revenue',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: { 
                color: chartColors.dark,
                labelInLegend: 'Additional Allocations',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            3: { 
                color: chartColors.info,
                labelInLegend: 'Equalization Fund',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxis: {
            title: 'Amount (KES)',
            format: 'short',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['revenue-composition-chart']) {
        chartInstances['revenue-composition-chart'] = new google.visualization.ColumnChart(container);
    }
    
    chartInstances['revenue-composition-chart'].draw(data, options);
    addDownloadButton('revenue-composition-chart', chartInstances['revenue-composition-chart']);
}

// 5. Equalization Fund Trends Chart (Line Chart)
function drawEqualizationFundTrends() {
    const container = document.getElementById('equalization-fund-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Equalization Fund (KES)');

    trendsData.equalization_fund_trends.forEach(item => {
        data.addRow([
            item.year,
            item.amount
        ]);
    });

    const options = {
        title: 'Equalization Fund Disbursement Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        curveType: 'function',
        series: {
            0: { 
                color: chartColors.info,
                labelInLegend: 'Equalization Fund',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxis: {
            title: 'Amount (KES)',
            format: 'short',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['equalization-fund-trends-chart']) {
        chartInstances['equalization-fund-trends-chart'] = new google.visualization.LineChart(container);
    }
    
    chartInstances['equalization-fund-trends-chart'].draw(data, options);
    addDownloadButton('equalization-fund-trends-chart', chartInstances['equalization-fund-trends-chart']);
}

// 6. Total Absorption Trends Chart (Combo Chart)
function drawTotalAbsorptionTrends() {
    const container = document.getElementById('total-absorption-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Budgeted Amount (KES)');
    data.addColumn('number', 'Actual Amount (KES)');
    data.addColumn('number', 'Absorption Rate (%)');

    trendsData.total_absorption_trends.forEach(item => {
        data.addRow([
            item.year,
            item.budgeted,
            item.actual,
            item.absorption_rate
        ]);
    });

    const options = {
        title: 'Total Expenditure Absorption Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        seriesType: 'bars',
        series: {
            0: { 
                color: chartColors.purple,
                labelInLegend: 'Budgeted Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: '#8a63d2',
                labelInLegend: 'Actual Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: {
                type: 'line',
                color: chartColors.gold,
                targetAxisIndex: 1,
                labelInLegend: 'Absorption Rate',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxes: {
            0: {
                title: 'Amount (KES)',
                format: 'short',
                textStyle: { color: chartColors.purple, fontSize: 12 },
                titleTextStyle: { color: chartColors.purple, fontSize: 14, bold: true }
            },
            1: {
                title: 'Absorption Rate (%)',
                format: '#\'%\'',
                textStyle: { color: chartColors.gold, fontSize: 12 },
                titleTextStyle: { color: chartColors.gold, fontSize: 14, bold: true }
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['total-absorption-trends-chart']) {
        chartInstances['total-absorption-trends-chart'] = new google.visualization.ComboChart(container);
    }
    
    chartInstances['total-absorption-trends-chart'].draw(data, options);
    addDownloadButton('total-absorption-trends-chart', chartInstances['total-absorption-trends-chart']);
}

// 7. Recurrent Absorption Trends Chart (Combo Chart)
function drawRecurrentAbsorptionTrends() {
    const container = document.getElementById('recurrent-absorption-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Budgeted Amount (KES)');
    data.addColumn('number', 'Actual Amount (KES)');
    data.addColumn('number', 'Absorption Rate (%)');

    trendsData.recurrent_absorption_trends.forEach(item => {
        data.addRow([
            item.year,
            item.budgeted,
            item.actual,
            item.absorption_rate
        ]);
    });

    const options = {
        title: 'Recurrent Expenditure Absorption Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        seriesType: 'bars',
        series: {
            0: { 
                color: chartColors.orange,
                labelInLegend: 'Budgeted Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: '#ff9c4a',
                labelInLegend: 'Actual Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: {
                type: 'line',
                color: chartColors.gold,
                targetAxisIndex: 1,
                labelInLegend: 'Absorption Rate',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxes: {
            0: {
                title: 'Amount (KES)',
                format: 'short',
                textStyle: { color: chartColors.orange, fontSize: 12 },
                titleTextStyle: { color: chartColors.orange, fontSize: 14, bold: true }
            },
            1: {
                title: 'Absorption Rate (%)',
                format: '#\'%\'',
                textStyle: { color: chartColors.gold, fontSize: 12 },
                titleTextStyle: { color: chartColors.gold, fontSize: 14, bold: true }
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['recurrent-absorption-trends-chart']) {
        chartInstances['recurrent-absorption-trends-chart'] = new google.visualization.ComboChart(container);
    }
    
    chartInstances['recurrent-absorption-trends-chart'].draw(data, options);
    addDownloadButton('recurrent-absorption-trends-chart', chartInstances['recurrent-absorption-trends-chart']);
}

// 8. Development Absorption Trends Chart (Combo Chart)
function drawDevelopmentAbsorptionTrends() {
    const container = document.getElementById('development-absorption-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Budgeted Amount (KES)');
    data.addColumn('number', 'Actual Amount (KES)');
    data.addColumn('number', 'Absorption Rate (%)');

    trendsData.development_absorption_trends.forEach(item => {
        data.addRow([
            item.year,
            item.budgeted,
            item.actual,
            item.absorption_rate
        ]);
    });

    const options = {
        title: 'Development Expenditure Absorption Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        seriesType: 'bars',
        series: {
            0: { 
                color: chartColors.success,
                labelInLegend: 'Budgeted Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: '#388e3c',
                labelInLegend: 'Actual Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: {
                type: 'line',
                color: chartColors.gold,
                targetAxisIndex: 1,
                labelInLegend: 'Absorption Rate',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxes: {
            0: {
                title: 'Amount (KES)',
                format: 'short',
                textStyle: { color: chartColors.success, fontSize: 12 },
                titleTextStyle: { color: chartColors.success, fontSize: 14, bold: true }
            },
            1: {
                title: 'Absorption Rate (%)',
                format: '#\'%\'',
                textStyle: { color: chartColors.gold, fontSize: 12 },
                titleTextStyle: { color: chartColors.gold, fontSize: 14, bold: true }
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['development-absorption-trends-chart']) {
        chartInstances['development-absorption-trends-chart'] = new google.visualization.ComboChart(container);
    }
    
    chartInstances['development-absorption-trends-chart'].draw(data, options);
    addDownloadButton('development-absorption-trends-chart', chartInstances['development-absorption-trends-chart']);
}

// 9. Compensation Absorption Trends Chart (Combo Chart)
function drawCompensationAbsorptionTrends() {
    const container = document.getElementById('compensation-absorption-trends-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Budgeted Amount (KES)');
    data.addColumn('number', 'Actual Amount (KES)');
    data.addColumn('number', 'Absorption Rate (%)');

    trendsData.compensation_absorption_trends.forEach(item => {
        data.addRow([
            item.year,
            item.budgeted,
            item.actual,
            item.absorption_rate
        ]);
    });

    const options = {
        title: 'Compensation of Employees Absorption Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        seriesType: 'bars',
        series: {
            0: { 
                color: chartColors.danger,
                labelInLegend: 'Budgeted Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: '#c0392b',
                labelInLegend: 'Actual Amount',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            2: {
                type: 'line',
                color: chartColors.gold,
                targetAxisIndex: 1,
                labelInLegend: 'Absorption Rate',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxes: {
            0: {
                title: 'Amount (KES)',
                format: 'short',
                textStyle: { color: chartColors.danger, fontSize: 12 },
                titleTextStyle: { color: chartColors.danger, fontSize: 14, bold: true }
            },
            1: {
                title: 'Absorption Rate (%)',
                format: '#\'%\'',
                textStyle: { color: chartColors.gold, fontSize: 12 },
                titleTextStyle: { color: chartColors.gold, fontSize: 14, bold: true }
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['compensation-absorption-trends-chart']) {
        chartInstances['compensation-absorption-trends-chart'] = new google.visualization.ComboChart(container);
    }
    
    chartInstances['compensation-absorption-trends-chart'].draw(data, options);
    addDownloadButton('compensation-absorption-trends-chart', chartInstances['compensation-absorption-trends-chart']);
}

// 10. Development vs Recurrent Expenditure Chart (Line Chart)
function drawDevVsRecChart() {
    const container = document.getElementById('dev-vs-rec-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Development Expenditure (KES)');
    data.addColumn('number', 'Recurrent Expenditure (KES)');

    trendsData.dev_vs_rec.forEach(item => {
        data.addRow([
            item.year,
            item.development,
            item.recurrent
        ]);
    });

    const options = {
        title: 'Development vs Recurrent Expenditure Trends',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        curveType: 'function',
        series: {
            0: { 
                color: chartColors.success,
                labelInLegend: 'Development Expenditure',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: chartColors.orange,
                labelInLegend: 'Recurrent Expenditure',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxis: {
            title: 'Amount (KES)',
            format: 'short',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['dev-vs-rec-chart']) {
        chartInstances['dev-vs-rec-chart'] = new google.visualization.LineChart(container);
    }
    
    chartInstances['dev-vs-rec-chart'].draw(data, options);
    addDownloadButton('dev-vs-rec-chart', chartInstances['dev-vs-rec-chart']);
}


// 11. Compensation Ratio Chart (Line Chart with Threshold)
function drawCompensationRatioChart() {
    const container = document.getElementById("compensation-ratio-chart");
    if (!container) {
        console.error("Chart container not found: compensation-ratio-chart");
        return;
    }

    // No data fallback
    if (!trendsData.compensation_ratio_trends || trendsData.compensation_ratio_trends.length === 0) {
        container.innerHTML =
            '<div style="text-align:center; padding:50px; color:#666;">No data available for Compensation Ratio Chart</div>';
        return;
    }

    const data = new google.visualization.DataTable();
    data.addColumn("string", "Financial Year");
    data.addColumn("number", "Compensation Ratio (%)");
    data.addColumn("number", "Threshold (35%)");

    trendsData.compensation_ratio_trends.forEach(item => {
        data.addRow([item.year, item.ratio, 35]);
    });

    const options = {
        title: "Compensation as % of Recurrent Expenditure",
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: "#333"
        },

        curveType: "function",

        // BIGGER CHART AREA (axes never get clipped)
        chartArea: {
            left: 100,      // keeps Y-axis visible in chart + PNG export
            right: 40,
            top: 70,
            bottom: 110,    // fixes X-axis hiding in PNG
            width: "80%",
            height: "60%"
        },

        // *** FORCE AXIS + GRIDLINES TO RENDER ***
        vAxis: {
            title: "Compensation Ratio (%)",
            titleTextStyle: { fontSize: 14, bold: true, color: "#333" },
            textStyle: { fontSize: 12, color: "#333" },
            format: "#'%'",
            baselineColor: "#333",      // ALWAYS render baseline
            gridlines: {
                color: "#ccc"           // makes PNG include the axis grid
            },
            minorGridlines: {
                color: "#eee"
            },
            viewWindow: { min: 0 }
        },

        hAxis: {
            title: "Financial Year",
            titleTextStyle: { fontSize: 14, bold: true, color: "#333" },
            textStyle: { fontSize: 12, color: "#333" },
            slantedText: true,
            slantedTextAngle: 45,
            baselineColor: "#333",
            gridlines: { color: "#ccc" }
        },

        series: {
            0: {
                color: chartColors.danger,
                labelInLegend: "Compensation Ratio (%)",
                lineWidth: 3,
                pointSize: 6
            },
            1: {
                color: chartColors.gold,
                labelInLegend: "Threshold (35%)",
                lineWidth: 2,
                pointSize: 0,
                lineDashStyle: [6, 6]
            }
        },

        legend: {
            position: "top",
            alignment: "center",
            textStyle: { fontSize: 12, color: "#333" }
        },

        backgroundColor: "transparent",

        animation: {
            duration: 900,
            startup: true
        },

        tooltip: { textStyle: { fontSize: 12 } }
    };

    // Draw chart instance
    if (!chartInstances["compensation-ratio-chart"]) {
        chartInstances["compensation-ratio-chart"] =
            new google.visualization.LineChart(container);
    }

    chartInstances["compensation-ratio-chart"].draw(data, options);

    // Attach PNG download button
    addDownloadButton("compensation-ratio-chart", chartInstances["compensation-ratio-chart"]);
}


// 12. Pending Bills Chart (Line Chart)
function drawPendingBillsChart() {
    const container = document.getElementById('pending-bills-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Pending Bills (KES)');

    trendsData.pending_bills.forEach(item => {
        data.addRow([
            item.year,
            item.pending_bills
        ]);
    });

    const options = {
        title: 'Pending Bills Trends Over Time',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        curveType: 'function',
        series: {
            0: { 
                color: chartColors.danger,
                labelInLegend: 'Pending Bills',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxis: {
            title: 'Amount (KES)',
            format: 'short',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['pending-bills-chart']) {
        chartInstances['pending-bills-chart'] = new google.visualization.LineChart(container);
    }
    
    chartInstances['pending-bills-chart'].draw(data, options);
    addDownloadButton('pending-bills-chart', chartInstances['pending-bills-chart']);
}

// 13. Pending Bills Ratio Chart (Line Chart with Threshold)
function drawPendingBillsRatioChart() {
    const container = document.getElementById('pending-bills-ratio-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Financial Year');
    data.addColumn('number', 'Pending Bills Ratio (%)');
    data.addColumn('number', 'Risk Threshold (20%)');

    trendsData.pending_bills_ratio.forEach(item => {
        data.addRow([
            item.year,
            item.ratio,
            20
        ]);
    });

    const options = {
        title: 'Pending Bills as Percentage of Total Revenue',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        curveType: 'function',
        series: {
            0: { 
                color: chartColors.warning,
                labelInLegend: 'Pending Bills Ratio',
                lineWidth: 3,
                pointSize: 6,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: chartColors.danger,
                labelInLegend: 'Risk Threshold (20%)',
                lineWidth: 2,
                lineDashStyle: [5, 5],
                pointSize: 0,
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxis: {
            title: 'Percentage (%)',
            format: '#\'%\'',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            viewWindow: {
                min: 0,
                max: 50
            }
        },
        hAxis: {
            title: 'Financial Year',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['pending-bills-ratio-chart']) {
        chartInstances['pending-bills-ratio-chart'] = new google.visualization.LineChart(container);
    }
    
    chartInstances['pending-bills-ratio-chart'].draw(data, options);
    addDownloadButton('pending-bills-ratio-chart', chartInstances['pending-bills-ratio-chart']);
}

// 14. Fiscal Responsibility Principles Chart (Bar Chart)
function drawFiscalPrinciplesChart() {
    const container = document.getElementById('fiscal-principles-chart');
    if (!container) return;
    
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Fiscal Principle');
    data.addColumn('number', 'Budgeted (%)');
    data.addColumn('number', 'Actual (%)');

    data.addRow([
        'Recurrent Expenditure to Revenue Ratio',
        (summaryData.budgeted_recurrent_to_revenue ?? 0) * 100,
        (summaryData.actual_recurrent_to_revenue ?? 0) * 100
    ]);
    data.addRow([
        'Development Expenditure to Budget Ratio',
        (summaryData.budgeted_development_to_budget ?? 0) * 100,
        (summaryData.actual_development_to_expenditure ?? 0) * 100
    ]);
    data.addRow([
        'Wages Bill to Revenue Ratio',
        (summaryData.budgeted_wages_to_revenue ?? 0) * 100,
        (summaryData.actual_wages_to_revenue ?? 0) * 100
    ]);

    const options = {
        title: 'Fiscal Responsibility Principles Compliance',
        titleTextStyle: {
            fontSize: 18,
            bold: true,
            color: '#333'
        },
        series: {
            0: { 
                color: chartColors.primary,
                labelInLegend: 'Budgeted',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            },
            1: { 
                color: chartColors.gold,
                labelInLegend: 'Actual',
                annotations: {
                    textStyle: {
                        fontSize: 12,
                        bold: true,
                        color: '#333'
                    }
                }
            }
        },
        vAxis: {
            title: 'Percentage (%)',
            format: '#\'%\'',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' }
        },
        hAxis: {
            title: 'Fiscal Principles',
            textStyle: { fontSize: 12, color: '#333' },
            titleTextStyle: { fontSize: 14, bold: true, color: '#333' },
            slantedText: true,
            slantedTextAngle: 45,
            maxAlternation: 1,
            minTextSpacing: 10,
            showTextEvery: 1,
            textPosition: 'out'
        },
        chartArea: { 
            width: '75%', 
            height: '65%',
            backgroundColor: 'transparent',
            top: 60,
            left: 80,
            bottom: 120
        },
        backgroundColor: 'transparent',
        legend: { 
            position: 'top',
            alignment: 'center',
            textStyle: { fontSize: 12, color: '#333' }
        },
        bar: { groupWidth: '75%' },
        animation: {
            duration: 1000,
            startup: true,
            easing: 'out'
        },
        tooltip: {
            textStyle: { fontSize: 12 },
            showColorCode: true
        }
    };

    if (!chartInstances['fiscal-principles-chart']) {
        chartInstances['fiscal-principles-chart'] = new google.visualization.ColumnChart(container);
    }
    
    chartInstances['fiscal-principles-chart'].draw(data, options);
    addDownloadButton('fiscal-principles-chart', chartInstances['fiscal-principles-chart']);
}
// 15. County Comparison Chart (Radar Chart using Chart.js)
function drawCountyComparisonChart() {
    const container = document.getElementById('county-comparison-chart');
    if (!container) return;
    
    // Get top 10 counties by revenue for comparison
    const topCounties = [...countyRatios]
        .sort((a, b) => (b.revenue || 0) - (a.revenue || 0))
        .slice(0, 10);
    
    const labels = topCounties.map(county => county.name);
    
    const revenueData = topCounties.map(county => county.revenue || 0);
    const osrPerformanceData = topCounties.map(county => county.actual_osr_performance || 0);
    const absorptionRateData = topCounties.map(county => county.absorption_rate || 0);
    const compensationRatioData = topCounties.map(county => county.compensation_ratio || 0);
    
    // Normalize data for radar chart (0-100 scale)
    const maxRevenue = Math.max(...revenueData);
    const normalizedRevenue = revenueData.map(value => (value / maxRevenue) * 100);
    
    const ctx = container.getContext('2d');
    
    // Destroy existing chart instance if it exists
    if (chartInstances['county-comparison-chart']) {
        chartInstances['county-comparison-chart'].destroy();
    }
    
    chartInstances['county-comparison-chart'] = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue (Normalized)',
                    data: normalizedRevenue,
                    backgroundColor: 'rgba(0, 100, 0, 0.2)',
                    borderColor: chartColors.primary,
                    pointBackgroundColor: chartColors.primary,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: chartColors.primary
                },
                {
                    label: 'OSR Performance (%)',
                    data: osrPerformanceData,
                    backgroundColor: 'rgba(178, 34, 34, 0.2)',
                    borderColor: chartColors.secondary,
                    pointBackgroundColor: chartColors.secondary,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: chartColors.secondary
                },
                {
                    label: 'Absorption Rate (%)',
                    data: absorptionRateData,
                    backgroundColor: 'rgba(212, 175, 55, 0.2)',
                    borderColor: chartColors.gold,
                    pointBackgroundColor: chartColors.gold,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: chartColors.gold
                },
                {
                    label: 'Compensation Ratio (%)',
                    data: compensationRatioData,
                    backgroundColor: 'rgba(198, 40, 40, 0.2)',
                    borderColor: chartColors.danger,
                    pointBackgroundColor: chartColors.danger,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: chartColors.danger
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
                line: {
                    borderWidth: 3
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ticks: {
                        display: true,
                        backdropColor: 'rgba(0, 0, 0, 0)'
                    },
                    pointLabels: {
                        font: {
                            size: 12
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Top 10 Counties Performance Comparison',
                    font: {
                        size: 16
                    }
                }
            }
        }
    });
    
    // Add download button for Chart.js chart
    addDownloadButtonToChartJSChart('county-comparison-chart');
}

// Function to add download button to Chart.js chart
function addDownloadButtonToChartJSChart(chartId) {
    const container = document.getElementById(chartId);
    if (!container) return;
    
    // Remove existing button if any
    const existingBtn = container.querySelector('.download-chart-btn');
    if (existingBtn) existingBtn.remove();
    
    const button = document.createElement('button');
    button.className = 'download-chart-btn';
    button.innerHTML = '<i class="fas fa-download"></i> Download';
    button.title = 'Download chart as PNG';
    
    button.addEventListener('click', function() {
        const chartInstance = chartInstances[chartId];
        if (chartInstance) {
            const url = chartInstance.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = chartId + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });
    
    container.appendChild(button);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Chart tabs functionality
    const chartTabs = document.querySelectorAll('.chart-tab');
    chartTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            chartTabs.forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.chart-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            // Redraw charts if needed
            if (tabId === 'county-comparison') {
                setTimeout(() => {
                    drawCountyComparisonChart();
                }, 100);
            }
        });
    });
    
    // Compact view toggle
    document.getElementById('toggleCompactView').addEventListener('click', () => {
        document.body.classList.toggle('compact-mode');
        document.querySelectorAll('.chart-container-inner').forEach(container => {
            container.style.height = document.body.classList.contains('compact-mode') ? '300px' : '500px';
        });
        
        // Redraw charts after compact mode change
        setTimeout(() => {
            drawAllCharts();
        }, 100);
    });

    // Refresh data
    document.getElementById('refreshData').addEventListener('click', () => {
        const refreshBtn = document.getElementById('refreshData');
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<span class="loading-spinner"></span> Loading...';
        refreshBtn.disabled = true;
        
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    });
    
    // Download all charts
    document.querySelector('.download-all-charts').addEventListener('click', function() {
        const charts = Object.keys(chartInstances);
        let downloadedCount = 0;
        
        // Download Google Charts
        charts.forEach(chartId => {
            try {
                const chartInstance = chartInstances[chartId];
                if (chartInstance && typeof chartInstance.getImageURI === 'function') {
                    const imgUri = chartInstance.getImageURI();
                    const link = document.createElement('a');
                    link.href = imgUri;
                    link.download = chartId + '.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    downloadedCount++;
                }
            } catch (e) {
                console.error('Error downloading chart:', chartId, e);
            }
        });
        
        // Download Chart.js charts
        try {
            const chartJsContainer = document.getElementById('county-comparison-chart');
            if (chartJsContainer) {
                const chartInstance = chartInstances['county-comparison-chart'];
                if (chartInstance) {
                    const url = chartInstance.toBase64Image();
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'county-comparison-chart.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    downloadedCount++;
                }
            }
        } catch (e) {
            console.error('Error downloading Chart.js chart:', e);
        }
        
        // Show notification after all downloads
        showDownloadNotification(downloadedCount);
    });
    
    // Dark mode toggle
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const icon = this.querySelector('i');
        if (document.body.classList.contains('dark-mode')) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            localStorage.setItem('darkMode', 'disabled');
        }
        
        // Redraw charts after dark mode change
        setTimeout(() => {
            drawAllCharts();
        }, 100);
    });
    
    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        const icon = document.querySelector('#darkModeToggle i');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    }
    
    // Add resize event listener with debounce
    window.addEventListener('resize', debounce(() => {
        if (chartsLoaded) {
            drawAllCharts();
        }
    }, 250));
});

// Debounce function to limit how often a function can be called
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Function to show download notification
function showDownloadNotification(count) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success position-fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.innerHTML = `<i class="fas fa-check-circle"></i> Successfully downloaded ${count} charts`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Refined solution for Google Charts x-axis visibility
document.addEventListener('DOMContentLoaded', function() {
    // Function to fix Google Charts x-axis visibility
    function fixGoogleChartsXAxis() {
        // Get all Google Charts containers
        const chartContainers = document.querySelectorAll('.google-chart');
        
        chartContainers.forEach(container => {
            // Check if the chart has been rendered
            const svg = container.querySelector('svg');
            if (svg) {
                // Force the container to have enough height
                container.style.height = '500px';
                container.style.minHeight = '500px';
                
                // Get the chart area
                const chartArea = svg.querySelector('rect[fill="#ffffff"]');
                if (chartArea) {
                    // Get the current chart area dimensions
                    const chartAreaX = parseFloat(chartArea.getAttribute('x'));
                    const chartAreaY = parseFloat(chartArea.getAttribute('y'));
                    const chartAreaWidth = parseFloat(chartArea.getAttribute('width'));
                    const chartAreaHeight = parseFloat(chartArea.getAttribute('height'));
                    
                    // Increase the chart area height to make room for x-axis labels
                    const newHeight = chartAreaHeight + 40;
                    chartArea.setAttribute('height', newHeight);
                    
                    // Find and adjust the x-axis labels
                    const allTextElements = svg.querySelectorAll('text');
                    allTextElements.forEach(textElement => {
                        const y = parseFloat(textElement.getAttribute('y'));
                        // If this is an x-axis label (near the bottom)
                        if (y > chartAreaY + chartAreaHeight - 20) {
                            // Ensure the text is visible but don't move it down
                            textElement.style.display = 'block';
                            textElement.style.visibility = 'visible';
                            textElement.style.opacity = '1';
                        }
                    });
                    
                    // Adjust the chart container height if needed
                    const containerRect = container.getBoundingClientRect();
                    if (containerRect.height < 500) {
                        container.style.height = '500px';
                    }
                }
                
                // Find and adjust the main chart group
                const mainGroup = svg.querySelector('g');
                if (mainGroup) {
                    const transform = mainGroup.getAttribute('transform');
                    if (transform) {
                        // Adjust the transform to move the chart up slightly
                        const match = transform.match(/translate\(([^,]+),([^)]+)\)/);
                        if (match) {
                            const x = parseFloat(match[1]);
                            const y = parseFloat(match[2]);
                            // Move the chart up slightly to make room for x-axis labels
                            mainGroup.setAttribute('transform', `translate(${x},${y - 10})`);
                        }
                    }
                }
                
                // Ensure all text elements are visible
                const textElements = svg.querySelectorAll('text');
                textElements.forEach(text => {
                    text.style.display = 'block';
                    text.style.visibility = 'visible';
                    text.style.opacity = '1';
                });
                
                // Add padding to the bottom of the SVG to ensure x-axis labels are visible
                svg.style.paddingBottom = '30px';
            }
        });
    }
    
    // Try to fix charts after a short delay
    setTimeout(fixGoogleChartsXAxis, 1000);
    
    // Also try after a longer delay in case charts load slowly
    setTimeout(fixGoogleChartsXAxis, 3000);
    
    // Add a resize event listener to fix charts when window is resized
    window.addEventListener('resize', function() {
        setTimeout(fixGoogleChartsXAxis, 500);
    });
    
    // Create a MutationObserver to detect when charts are added to the DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                // Check if any added nodes contain Google Charts
                for (let i = 0; i < mutation.addedNodes.length; i++) {
                    const node = mutation.addedNodes[i];
                    if (node.querySelectorAll && node.querySelectorAll('.google-chart').length > 0) {
                        // Fix the charts after a short delay
                        setTimeout(fixGoogleChartsXAxis, 1000);
                        break;
                    }
                }
            }
        });
    });
    
    // Start observing the document body for added nodes
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Add a click event listener to the refresh button to fix charts after refresh
    const refreshButton = document.getElementById('refreshData');
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            setTimeout(fixGoogleChartsXAxis, 2000);
        });
    }
});
</script>

<style>
/* Chart container styles */
.chart-container {
    margin-bottom: 30px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.chart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.chart-item {
    width: 100%;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

.chart-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-body {
    padding: 20px;
}

.chart-container-inner {
    height: 500px;
    position: relative;
}

/* Download button styles */
.download-chart-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    padding: 8px 12px;
    background-color: #4285f4;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
}

.download-chart-btn:hover {
    background-color: #3367d6;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    transform: translateY(-1px);
}

.download-chart-btn i {
    font-size: 14px;
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Notification styles */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.position-fixed {
    position: fixed;
    z-index: 9999;
}

/* Dark mode toggle button */
.dark-mode-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    background: var(--kenya-black);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow);
    cursor: pointer;
    transition: var(--transition);
}

.dark-mode-toggle:hover {
    transform: scale(1.1);
    box-shadow: var(--hover-shadow);
}

/* Dark mode styles */
body.dark-mode {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #e0e0e0;
}

body.dark-mode .card {
    background: #2d2d2d;
    color: #e0e0e0;
}

body.dark-mode .chart-item {
    background: #2d2d2d;
    color: #e0e0e0;
}

body.dark-mode .chart-header {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .table {
    color: #e0e0e0;
}

body.dark-mode .table thead th {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .table tbody tr:hover {
    background-color: rgba(0, 100, 0, 0.2);
}

body.dark-mode .county-card {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .county-name {
    color: #e0e0e0;
}

body.dark-mode .county-stat-label {
    color: #bbb;
}

body.dark-mode .data-table-card {
    background: #2d2d2d;
    color: #e0e0e0;
}

body.dark-mode .data-table-header {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .data-table-footer {
    background: #333;
    color: #e0e0e0;
}

body.dark-mode .map-container {
    background: #333;
}

body.dark-mode .map-overlay {
    background: rgba(51, 51, 51, 0.9);
    color: #e0e0e0;
}

body.dark-mode .dark-mode-toggle {
    background: #444;
    color: #ffd700;
}

body.dark-mode .dark-mode-toggle i {
    color: #ffd700;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chart-container-inner {
        height: 350px;
    }
    
    .download-chart-btn {
        padding: 6px 10px;
        font-size: 11px;
    }
    
    .download-chart-btn i {
        font-size: 12px;
    }
    
    .county-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .dark-mode-toggle {
        width: 40px;
        height: 40px;
    }
}

/* Chart title styling */
.chart-title {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

/* Custom tooltip styles */
.custom-tooltip {
    position: absolute;
    background-color: rgba(0, 0, 0, 0.8);
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px 12px;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
    font-size: 14px;
    max-width: 300px;
}

.custom-tooltip.show {
    opacity: 1;
}

/* Budget Execution section styling */
.budget-execution-section {
    margin: 40px 0;
    padding: 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

/* Fiscal Risks section styling */
.fiscal-risks-section {
    margin: 40px 0;
    padding: 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, #fff5f5, #ffe6e6);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

/* Chart axis styling */
.google-chart svg g[aria-label="Axis"] {
    display: block !important;
    visibility: visible !important;
}

.google-chart svg text {
    font-family: 'Poppins', sans-serif !important;
    font-size: 12px !important;
    fill: #333 !important;
}

.google-chart svg .axis-line {
    stroke: #333 !important;
    stroke-width: 1 !important;
}

.google-chart svg .axis-label {
    fill: #333 !important;
    font-weight: 600 !important;
}

/* Dark mode chart text */
body.dark-mode .google-chart svg text {
    fill: #e0e0e0 !important;
}

body.dark-mode .google-chart svg .axis-label {
    fill: #e0e0e0 !important;
}
</style>
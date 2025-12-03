<?php

/** @var yii\web\View $this */
/** @var app\modules\backend\models\AdditionalRevenueSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

 $this->title = 'Revenue Distribution Chart → County Revenue Distribution';
 $this->params['breadcrumbs'][] = $this->title;

// Get data for charts
 $fiscalData = \app\modules\backend\models\Fiscal::find()
    ->with(['county', 'fy0'])
    ->orderBy(['fy' => SORT_DESC])
    ->all();

// Prepare data arrays
 $years = [];
 $countyData = [];
 $regionalData = [];
 $performanceData = [];

foreach ($fiscalData as $record) {
    $countyName = $record->county->CountyName ?? 'Unknown';
    $region = $record->county->region->region_name ?? 'Unknown';
    $year = $record->fy;
    
    if (!in_array($year, $years)) {
        $years[] = $year;
    }
    
    // Calculate performance indicators
    $totalBudget = ($record->development_budgement ?? 0) + ($record->recurrent_budget ?? 0);
    $totalActualExpenditure = ($record->recurrent_expenditure ?? 0) + ($record->development_expenditure ?? 0);
    $totalActualRevenue = ($record->actual_revenue ?? 0) + ($record->actual_osr ?? 0);
    
    $performanceIndicators = [
        'revenue_performance' => $record->total_revenue > 0 ? round(($record->actual_revenue / $record->total_revenue) * 100, 1) : 0,
        'osr_performance' => $record->target_osr > 0 ? round(($record->actual_osr / $record->target_osr) * 100, 1) : 0,
        'absorption_rate' => $totalBudget > 0 ? round(($totalActualExpenditure / $totalBudget) * 100, 1) : 0,
        'dev_allocation' => $totalBudget > 0 ? round(($record->development_budgement / $totalBudget) * 100, 1) : 0,
        'wages_ratio' => $record->total_revenue > 0 ? round(($record->personal_emoluments / $record->total_revenue) * 100, 1) : 0,
        'pending_bills_burden' => $totalActualRevenue > 0 ? round(($record->pending_bills / $totalActualRevenue) * 100, 1) : 0,
    ];
    
    $countyData[$countyName][$year] = [
        'equitable_share' => $record->county->equitableRevenueShares[0]->project_amt ?? 0,
        'development_budget' => $record->development_budgement,
        'recurrent_budget' => $record->recurrent_budget,
        'total_revenue' => $record->total_revenue,
        'actual_revenue' => $record->actual_revenue,
        'development_expenditure' => $record->development_expenditure,
        'recurrent_expenditure' => $record->recurrent_expenditure,
        'actual_osr' => $record->actual_osr,
        'target_osr' => $record->target_osr,
        'personal_emoluments' => $record->personal_emoluments,
        'pending_bills' => $record->pending_bills,
        'performance' => $performanceIndicators
    ];
    
    if (!isset($regionalData[$region][$year])) {
        $regionalData[$region][$year] = [
            'total_equitable' => 0,
            'total_development' => 0,
            'total_recurrent' => 0,
            'counties' => 0
        ];
    }
    
    $regionalData[$region][$year]['total_equitable'] += $record->county->equitableRevenueShares[0]->project_amt ?? 0;
    $regionalData[$region][$year]['total_development'] += $record->development_budgement;
    $regionalData[$region][$year]['total_recurrent'] += $record->recurrent_budget;
    $regionalData[$region][$year]['counties']++;
}

// Sort years descending
rsort($years);
 $latestYear = $years[0] ?? '';

// Prepare chart data as JSON
 $chartDataJson = json_encode([
    'years' => $years,
    'countyData' => $countyData,
    'regionalData' => $regionalData,
    'latestYear' => $latestYear
]);

// Register required assets
 $this->registerJsFile('https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js', ['position' => \yii\web\View::POS_HEAD]);
 $this->registerJsFile('https://code.jquery.com/jquery-3.6.0.min.js', ['position' => \yii\web\View::POS_HEAD]);
 $this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', ['position' => \yii\web\View::POS_HEAD]);
 $this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_HEAD]);
 $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', ['position' => \yii\web\View::POS_HEAD]);

// Build JavaScript code
 $js = "// Chart data from PHP\n";
 $js .= "const chartData = " . $chartDataJson . ";\n\n";

 $js .= <<<'JS'
// Chart objects
let charts = {};

// Initialize all charts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts immediately
    initializeCharts();
});

// Initialize all charts
function initializeCharts() {
    showLoadingIndicators();
    
    // Dispose existing charts to prevent memory leaks
    Object.values(charts).forEach(chart => {
        if (chart) chart.dispose();
    });
    charts = {};
    
    // Initialize all charts
    setTimeout(() => {
        initRevenueChart();
        initRegionalChart();
        initPerformanceChart();
        initCompositionChart();
        initOSRChart();
        initComplianceChart();
        initRevenueExpenditureChart();
        initBudgetAllocationChart();
        initOSRTrendChart();
        initDevRecurrentChart();
        updatePerformanceTable('top');
        hideLoadingIndicators();
    }, 100);
}

// Show loading indicators for all charts
function showLoadingIndicators() {
    const chartContainers = document.querySelectorAll('.chart-container');
    chartContainers.forEach(container => {
        // Only add loading if not already present
        if (!container.querySelector('.chart-loading')) {
            container.innerHTML = '<div class="chart-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        }
    });
}

// Hide loading indicators
function hideLoadingIndicators() {
    const chartContainers = document.querySelectorAll('.chart-container');
    chartContainers.forEach(container => {
        const loading = container.querySelector('.chart-loading');
        if (loading) loading.remove();
    });
}

// Get selected year or default to all years
function getSelectedYear() {
    const yearFilter = document.getElementById('yearFilter');
    return yearFilter ? yearFilter.value : 'all';
}

// Initialize Revenue Distribution Chart
function initRevenueChart() {
    const chartDom = document.getElementById('revenueChart');
    const selectedYear = getSelectedYear();
    
    // Prepare data
    const counties = [];
    const equitableData = [];
    const developmentData = [];
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData) {
                counties.push(county);
                equitableData.push(yearData.equitable_share / 1000000);
                developmentData.push(yearData.development_budget / 1000000);
            }
        }
    });
    
    // Sort by equitable share
    const combined = counties.map((county, i) => ({
        county,
        equitable: equitableData[i],
        development: developmentData[i]
    }));
    
    combined.sort((a, b) => b.equitable - a.equitable);
    
    const sortedCounties = combined.map(item => item.county);
    const sortedEquitable = combined.map(item => item.equitable);
    const sortedDevelopment = combined.map(item => item.development);
    
    // Set dynamic height based on number of counties
    const height = Math.max(400, sortedCounties.length * 25 + 100);
    chartDom.style.height = `${height}px`;
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Revenue Distribution by County',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                let result = params[0].name + '<br/>';
                params.forEach(param => {
                    result += `${param.marker} ${param.seriesName}: KES ${param.value.toLocaleString()} M<br/>`;
                });
                return result;
            }
        },
        legend: {
            data: ['Equitable Share', 'Development Budget'],
            top: 40,
            textStyle: {
                color: '#34495e'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            name: 'Amount (KES Millions)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: sortedCounties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                fontSize: 10,
                interval: 0
            }
        },
        series: [
            {
                name: 'Equitable Share',
                type: 'bar',
                data: sortedEquitable,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#3498db'},
                        {offset: 1, color: '#2980b9'}
                    ]),
                    borderRadius: [0, 5, 5, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10;
                }
            },
            {
                name: 'Development Budget',
                type: 'bar',
                data: sortedDevelopment,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#2ecc71'},
                        {offset: 1, color: '#27ae60'}
                    ]),
                    borderRadius: [0, 5, 5, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10 + 100;
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        },
        dataZoom: [
            {
                type: 'inside',
                start: 0,
                end: 100,
                zoomLock: true
            },
            {
                start: 0,
                end: 100,
                handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                handleSize: '80%',
                handleStyle: {
                    color: '#fff',
                    shadowBlur: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.revenueChart = chart;
    
    // Add download button
    addDownloadButton('revenueChart', 'revenue_distribution_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize Regional Analysis Chart
function initRegionalChart() {
    const chartDom = document.getElementById('regionalChart');
    const selectedYear = getSelectedYear();
    
    // Prepare data
    const regions = [];
    const values = [];
    
    Object.keys(chartData.regionalData).forEach(function(region) {
        if (selectedYear === 'all' || chartData.regionalData[region][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.regionalData[region][chartData.latestYear] : chartData.regionalData[region][selectedYear];
            if (yearData) {
                regions.push(region);
                values.push(yearData.total_equitable / 1000000);
            }
        }
    });
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Revenue Distribution by Region',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: KES {c} M ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 10,
            top: 'center',
            textStyle: {
                color: '#34495e'
            }
        },
        series: [
            {
                name: 'Revenue Share',
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: true,
                    formatter: '{b}: {d}%'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 16,
                        fontWeight: 'bold'
                    }
                },
                labelLine: {
                    show: true
                },
                data: regions.map((region, index) => ({
                    value: values[index],
                    name: region,
                    itemStyle: {
                        color: [
                            '#3498db', '#2ecc71', '#f1c40f', '#e74c3c', 
                            '#9b59b6', '#1abc9c', '#f39c12', '#34495e', 
                            '#8e44ad', '#27ae60', '#2980b9', '#16a085'
                        ][index % 12]
                    }
                })),
                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function (idx) {
                    return Math.random() * 200;
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.regionalChart = chart;
    
    // Add download button
    addDownloadButton('regionalChart', 'regional_analysis_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize Performance Trends Chart
function initPerformanceChart() {
    const chartDom = document.getElementById('performanceChart');
    
    // Prepare data - always show all years
    const yearsData = [];
    const revenueData = [];
    const osrData = [];
    const absorptionData = [];
    
    chartData.years.forEach(function(year) {
        let revenueTotal = 0, osrTotal = 0, absorptionTotal = 0;
        let count = 0;
        
        Object.keys(chartData.countyData).forEach(function(county) {
            if (chartData.countyData[county][year] && chartData.countyData[county][year].performance) {
                const perf = chartData.countyData[county][year].performance;
                revenueTotal += perf.revenue_performance;
                osrTotal += perf.osr_performance;
                absorptionTotal += perf.absorption_rate;
                count++;
            }
        });
        
        if (count > 0) {
            yearsData.push(year.toString());
            revenueData.push(Math.round(revenueTotal / count));
            osrData.push(Math.round(osrTotal / count));
            absorptionData.push(Math.round(absorptionTotal / count));
        }
    });
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Performance Trends Over Time',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['Revenue Performance', 'OSR Performance', 'Absorption Rate'],
            top: 40,
            textStyle: {
                color: '#34495e'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: yearsData,
            name: 'Year',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d'
            }
        },
        yAxis: {
            type: 'value',
            name: 'Performance (%)',
            nameLocation: 'middle',
            nameGap: 40,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            min: 0,
            max: 100,
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                formatter: '{value}%'
            }
        },
        series: [
            {
                name: 'Revenue Performance',
                type: 'line',
                data: revenueData,
                smooth: true,
                showSymbol: true,
                symbolSize: 8,
                lineStyle: {
                    width: 3,
                    color: '#3498db'
                },
                itemStyle: {
                    color: '#3498db'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {offset: 0, color: 'rgba(52, 152, 219, 0.5)'},
                        {offset: 1, color: 'rgba(52, 152, 219, 0.1)'}
                    ])
                }
            },
            {
                name: 'OSR Performance',
                type: 'line',
                data: osrData,
                smooth: true,
                showSymbol: true,
                symbolSize: 8,
                lineStyle: {
                    width: 3,
                    color: '#2ecc71'
                },
                itemStyle: {
                    color: '#2ecc71'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {offset: 0, color: 'rgba(46, 204, 113, 0.5)'},
                        {offset: 1, color: 'rgba(46, 204, 113, 0.1)'}
                    ])
                }
            },
            {
                name: 'Absorption Rate',
                type: 'line',
                data: absorptionData,
                smooth: true,
                showSymbol: true,
                symbolSize: 8,
                lineStyle: {
                    width: 3,
                    color: '#f1c40f'
                },
                itemStyle: {
                    color: '#f1c40f'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {offset: 0, color: 'rgba(241, 196, 15, 0.5)'},
                        {offset: 1, color: 'rgba(241, 196, 15, 0.1)'}
                    ])
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        }
    };
    
    chart.setOption(option);
    charts.performanceChart = chart;
    
    // Add download button
    addDownloadButton('performanceChart', 'performance_trends', chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize Budget Composition Chart
function initCompositionChart() {
    const chartDom = document.getElementById('compositionChart');
    const selectedYear = getSelectedYear();
    
    let devTotal = 0, recurrentTotal = 0;
    let count = 0;
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData && yearData.performance) {
                const perf = yearData.performance;
                devTotal += perf.dev_allocation;
                recurrentTotal += (100 - perf.dev_allocation);
                count++;
            }
        }
    });
    
    const devAvg = count > 0 ? Math.round(devTotal / count) : 0;
    const recurrentAvg = count > 0 ? Math.round(recurrentTotal / count) : 0;
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Budget Allocation Composition',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c}% ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 10,
            top: 'center',
            textStyle: {
                color: '#34495e'
            }
        },
        series: [
            {
                name: 'Budget Type',
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: true,
                    formatter: '{b}: {d}%'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 16,
                        fontWeight: 'bold'
                    }
                },
                labelLine: {
                    show: true
                },
                data: [
                    {
                        value: devAvg,
                        name: 'Development',
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 1, 1, [
                                {offset: 0, color: '#3498db'},
                                {offset: 1, color: '#2980b9'}
                            ])
                        }
                    },
                    {
                        value: recurrentAvg,
                        name: 'Recurrent',
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 1, 1, [
                                {offset: 0, color: '#2ecc71'},
                                {offset: 1, color: '#27ae60'}
                            ])
                        }
                    }
                ],
                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function (idx) {
                    return Math.random() * 200;
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.compositionChart = chart;
    
    // Add download button
    addDownloadButton('compositionChart', 'budget_composition_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize OSR Performance Chart
function initOSRChart() {
    const chartDom = document.getElementById('osrChart');
    const selectedYear = getSelectedYear();
    
    // Prepare data
    const counties = [];
    const osrPerformance = [];
    const colors = [];
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData && yearData.performance) {
                const performance = yearData.performance.osr_performance;
                counties.push(county);
                osrPerformance.push(performance);
                
                // Set color based on performance
                if (performance >= 90) {
                    colors.push('#2ecc71'); // Green
                } else if (performance >= 70) {
                    colors.push('#f1c40f'); // Yellow
                } else if (performance >= 50) {
                    colors.push('#e67e22'); // Orange
                } else {
                    colors.push('#e74c3c'); // Red
                }
            }
        }
    });
    
    // Sort by OSR performance
    const combined = counties.map((county, i) => ({
        county,
        performance: osrPerformance[i],
        color: colors[i]
    }));
    
    combined.sort((a, b) => b.performance - a.performance);
    
    const sortedCounties = combined.map(item => item.county);
    const sortedPerformance = combined.map(item => item.performance);
    const sortedColors = combined.map(item => item.color);
    
    // Set dynamic height based on number of counties
    const height = Math.max(400, sortedCounties.length * 25 + 100);
    chartDom.style.height = `${height}px`;
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'OSR Performance Analysis',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>OSR Performance: ${params[0].value}%`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            name: 'OSR Performance (%)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            min: 0,
            max: 100,
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                formatter: '{value}%'
            }
        },
        yAxis: {
            type: 'category',
            data: sortedCounties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                fontSize: 10,
                interval: 0
            }
        },
        series: [
            {
                type: 'bar',
                data: sortedPerformance.map((value, index) => ({
                    value: value,
                    itemStyle: {
                        color: sortedColors[index],
                        borderRadius: [0, 5, 5, 0]
                    }
                })),
                animationDelay: function (idx) {
                    return idx * 10;
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        },
        dataZoom: [
            {
                type: 'inside',
                start: 0,
                end: 100,
                zoomLock: true
            },
            {
                start: 0,
                end: 100,
                handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                handleSize: '80%',
                handleStyle: {
                    color: '#fff',
                    shadowBlur: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.osrChart = chart;
    
    // Add download button
    addDownloadButton('osrChart', 'osr_performance_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize Compliance Chart
function initComplianceChart() {
    const chartDom = document.getElementById('complianceChart');
    const selectedYear = getSelectedYear();
    
    // Prepare data
    const counties = [];
    const complianceScores = [];
    const colors = [];
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData && yearData.performance) {
                const performance = yearData.performance;
                const complianceScore = (performance.revenue_performance + performance.osr_performance + performance.absorption_rate) / 3;
                counties.push(county);
                complianceScores.push(Math.round(complianceScore));
                
                // Set color based on compliance score
                if (complianceScore >= 80) {
                    colors.push('#2ecc71'); // Green
                } else if (complianceScore >= 60) {
                    colors.push('#f1c40f'); // Yellow
                } else if (complianceScore >= 40) {
                    colors.push('#e67e22'); // Orange
                } else {
                    colors.push('#e74c3c'); // Red
                }
            }
        }
    });
    
    // Sort by compliance score
    const combined = counties.map((county, i) => ({
        county,
        score: complianceScores[i],
        color: colors[i]
    }));
    
    combined.sort((a, b) => b.score - a.score);
    
    const sortedCounties = combined.map(item => item.county);
    const sortedScores = combined.map(item => item.score);
    const sortedColors = combined.map(item => item.color);
    
    // Set dynamic height based on number of counties
    const height = Math.max(400, sortedCounties.length * 25 + 100);
    chartDom.style.height = `${height}px`;
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Fiscal Compliance Heatmap',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return `${params[0].name}<br/>Compliance Score: ${params[0].value}%`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            name: 'Compliance Score (%)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            min: 0,
            max: 100,
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                formatter: '{value}%'
            }
        },
        yAxis: {
            type: 'category',
            data: sortedCounties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                fontSize: 10,
                interval: 0
            }
        },
        series: [
            {
                type: 'bar',
                data: sortedScores.map((value, index) => ({
                    value: value,
                    itemStyle: {
                        color: sortedColors[index],
                        borderRadius: [0, 5, 5, 0]
                    }
                })),
                animationDelay: function (idx) {
                    return idx * 10;
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        },
        dataZoom: [
            {
                type: 'inside',
                start: 0,
                end: 100,
                zoomLock: true
            },
            {
                start: 0,
                end: 100,
                handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                handleSize: '80%',
                handleStyle: {
                    color: '#fff',
                    shadowBlur: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.complianceChart = chart;
    
    // Add download button
    addDownloadButton('complianceChart', 'compliance_heatmap_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize Revenue vs Expenditure Chart
function initRevenueExpenditureChart() {
    const chartDom = document.getElementById('revenueExpenditureChart');
    const selectedYear = getSelectedYear();
    
    // Prepare data
    const counties = [];
    const revenueData = [];
    const expenditureData = [];
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData) {
                counties.push(county);
                revenueData.push(yearData.total_revenue / 1000000);
                expenditureData.push((yearData.development_expenditure + yearData.recurrent_expenditure) / 1000000);
            }
        }
    });
    
    // Sort by revenue
    const combined = counties.map((county, i) => ({
        county,
        revenue: revenueData[i],
        expenditure: expenditureData[i]
    }));
    
    combined.sort((a, b) => b.revenue - a.revenue);
    
    const sortedCounties = combined.map(item => item.county);
    const sortedRevenue = combined.map(item => item.revenue);
    const sortedExpenditure = combined.map(item => item.expenditure);
    
    // Set dynamic height based on number of counties
    const height = Math.max(400, sortedCounties.length * 25 + 100);
    chartDom.style.height = `${height}px`;
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Revenue vs Expenditure',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                let result = params[0].name + '<br/>';
                params.forEach(param => {
                    result += `${param.marker} ${param.seriesName}: KES ${param.value.toLocaleString()} M<br/>`;
                });
                return result;
            }
        },
        legend: {
            data: ['Total Revenue', 'Total Expenditure'],
            top: 40,
            textStyle: {
                color: '#34495e'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            name: 'Amount (KES Millions)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: sortedCounties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                fontSize: 10,
                interval: 0
            }
        },
        series: [
            {
                name: 'Total Revenue',
                type: 'bar',
                data: sortedRevenue,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#3498db'},
                        {offset: 1, color: '#2980b9'}
                    ]),
                    borderRadius: [0, 5, 5, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10;
                }
            },
            {
                name: 'Total Expenditure',
                type: 'bar',
                data: sortedExpenditure,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#e74c3c'},
                        {offset: 1, color: '#c0392b'}
                    ]),
                    borderRadius: [0, 5, 5, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10 + 100;
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        },
        dataZoom: [
            {
                type: 'inside',
                start: 0,
                end: 100,
                zoomLock: true
            },
            {
                start: 0,
                end: 100,
                handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                handleSize: '80%',
                handleStyle: {
                    color: '#fff',
                    shadowBlur: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.revenueExpenditureChart = chart;
    
    // Add download button
    addDownloadButton('revenueExpenditureChart', 'revenue_expenditure_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize Budget Allocation Chart
function initBudgetAllocationChart() {
    const chartDom = document.getElementById('budgetAllocationChart');
    const selectedYear = getSelectedYear();
    
    // Prepare data
    const counties = [];
    const developmentData = [];
    const recurrentData = [];
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData) {
                counties.push(county);
                developmentData.push(yearData.development_budget / 1000000);
                recurrentData.push(yearData.recurrent_budget / 1000000);
            }
        }
    });
    
    // Sort by total budget
    const combined = counties.map((county, i) => ({
        county,
        development: developmentData[i],
        recurrent: recurrentData[i],
        total: developmentData[i] + recurrentData[i]
    }));
    
    combined.sort((a, b) => b.total - a.total);
    
    const sortedCounties = combined.map(item => item.county);
    const sortedDevelopment = combined.map(item => item.development);
    const sortedRecurrent = combined.map(item => item.recurrent);
    
    // Set dynamic height based on number of counties
    const height = Math.max(400, sortedCounties.length * 25 + 100);
    chartDom.style.height = `${height}px`;
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Budget Allocation',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                let result = params[0].name + '<br/>';
                params.forEach(param => {
                    result += `${param.marker} ${param.seriesName}: KES ${param.value.toLocaleString()} M<br/>`;
                });
                return result;
            }
        },
        legend: {
            data: ['Development Budget', 'Recurrent Budget'],
            top: 40,
            textStyle: {
                color: '#34495e'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            name: 'Amount (KES Millions)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            }
        },
        yAxis: {
            type: 'category',
            data: sortedCounties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                fontSize: 10,
                interval: 0
            }
        },
        series: [
            {
                name: 'Development Budget',
                type: 'bar',
                stack: 'total',
                data: sortedDevelopment,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#3498db'},
                        {offset: 1, color: '#2980b9'}
                    ]),
                    borderRadius: [0, 0, 0, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10;
                }
            },
            {
                name: 'Recurrent Budget',
                type: 'bar',
                stack: 'total',
                data: sortedRecurrent,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#2ecc71'},
                        {offset: 1, color: '#27ae60'}
                    ]),
                    borderRadius: [0, 5, 5, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10 + 100;
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        },
        dataZoom: [
            {
                type: 'inside',
                start: 0,
                end: 100,
                zoomLock: true
            },
            {
                start: 0,
                end: 100,
                handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                handleSize: '80%',
                handleStyle: {
                    color: '#fff',
                    shadowBlur: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.budgetAllocationChart = chart;
    
    // Add download button
    addDownloadButton('budgetAllocationChart', 'budget_allocation_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize OSR Trend Chart
function initOSRTrendChart() {
    const chartDom = document.getElementById('osrTrendChart');
    
    // Prepare data - always show all years
    const yearsData = [];
    const targetData = [];
    const actualData = [];
    
    const counties = Object.keys(chartData.countyData);
    
    chartData.years.forEach(function(year) {
        let targetTotal = 0, actualTotal = 0;
        
        counties.forEach(function(county) {
            if (chartData.countyData[county][year]) {
                targetTotal += chartData.countyData[county][year].target_osr || 0;
                actualTotal += chartData.countyData[county][year].actual_osr || 0;
            }
        });
        
        yearsData.push(year.toString());
        targetData.push(targetTotal / 1000000);
        actualData.push(actualTotal / 1000000);
    });
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'OSR Trend Over Years',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['Target OSR', 'Actual OSR'],
            top: 40,
            textStyle: {
                color: '#34495e'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: yearsData,
            name: 'Year',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d'
            }
        },
        yAxis: {
            type: 'value',
            name: 'Amount (KES Millions)',
            nameLocation: 'middle',
            nameGap: 40,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            },
            axisLabel: {
                color: '#7f8c8d'
            }
        },
        series: [
            {
                name: 'Target OSR',
                type: 'line',
                data: targetData,
                smooth: true,
                showSymbol: true,
                symbolSize: 8,
                lineStyle: {
                    width: 3,
                    color: '#3498db'
                },
                itemStyle: {
                    color: '#3498db'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {offset: 0, color: 'rgba(52, 152, 219, 0.5)'},
                        {offset: 1, color: 'rgba(52, 152, 219, 0.1)'}
                    ])
                }
            },
            {
                name: 'Actual OSR',
                type: 'line',
                data: actualData,
                smooth: true,
                showSymbol: true,
                symbolSize: 8,
                lineStyle: {
                    width: 3,
                    color: '#2ecc71'
                },
                itemStyle: {
                    color: '#2ecc71'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        {offset: 0, color: 'rgba(46, 204, 113, 0.5)'},
                        {offset: 1, color: 'rgba(46, 204, 113, 0.1)'}
                    ])
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        }
    };
    
    chart.setOption(option);
    charts.osrTrendChart = chart;
    
    // Add download button
    addDownloadButton('osrTrendChart', 'osr_trend', chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Initialize Development vs Recurrent Chart
function initDevRecurrentChart() {
    const chartDom = document.getElementById('devRecurrentChart');
    const selectedYear = getSelectedYear();
    
    // Prepare data
    const counties = [];
    const devAllocation = [];
    const recurrentAllocation = [];
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData && yearData.performance) {
                const perf = yearData.performance;
                counties.push(county);
                devAllocation.push(perf.dev_allocation);
                recurrentAllocation.push(100 - perf.dev_allocation);
            }
        }
    });
    
    // Sort by development allocation
    const combined = counties.map((county, i) => ({
        county,
        dev: devAllocation[i],
        recurrent: recurrentAllocation[i]
    }));
    
    combined.sort((a, b) => b.dev - a.dev);
    
    const sortedCounties = combined.map(item => item.county);
    const sortedDev = combined.map(item => item.dev);
    const sortedRecurrent = combined.map(item => item.recurrent);
    
    // Set dynamic height based on number of counties
    const height = Math.max(400, sortedCounties.length * 25 + 100);
    chartDom.style.height = `${height}px`;
    
    const chart = echarts.init(chartDom);
    const option = {
        title: {
            text: 'Development vs Recurrent Allocation',
            left: 'center',
            top: 10,
            textStyle: {
                fontSize: 18,
                fontWeight: 'bold',
                color: '#2c3e50'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                let result = params[0].name + '<br/>';
                params.forEach(param => {
                    result += `${param.marker} ${param.seriesName}: ${param.value}%<br/>`;
                });
                return result;
            }
        },
        legend: {
            data: ['Development Allocation', 'Recurrent Allocation'],
            top: 40,
            textStyle: {
                color: '#34495e'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            name: 'Allocation (%)',
            nameLocation: 'middle',
            nameGap: 30,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            min: 0,
            max: 100,
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#ecf0f1'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                formatter: '{value}%'
            }
        },
        yAxis: {
            type: 'category',
            data: sortedCounties,
            name: 'County',
            nameLocation: 'middle',
            nameGap: 50,
            nameTextStyle: {
                color: '#34495e',
                fontWeight: 'bold'
            },
            axisLine: {
                lineStyle: {
                    color: '#bdc3c7'
                }
            },
            axisLabel: {
                color: '#7f8c8d',
                fontSize: 10,
                interval: 0
            }
        },
        series: [
            {
                name: 'Development Allocation',
                type: 'bar',
                stack: 'total',
                data: sortedDev,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#3498db'},
                        {offset: 1, color: '#2980b9'}
                    ]),
                    borderRadius: [0, 0, 0, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10;
                }
            },
            {
                name: 'Recurrent Allocation',
                type: 'bar',
                stack: 'total',
                data: sortedRecurrent,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                        {offset: 0, color: '#2ecc71'},
                        {offset: 1, color: '#27ae60'}
                    ]),
                    borderRadius: [0, 5, 5, 0]
                },
                animationDelay: function (idx) {
                    return idx * 10 + 100;
                }
            }
        ],
        animationEasing: 'elasticOut',
        animationDelayUpdate: function (idx) {
            return idx * 5;
        },
        dataZoom: [
            {
                type: 'inside',
                start: 0,
                end: 100,
                zoomLock: true
            },
            {
                start: 0,
                end: 100,
                handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                handleSize: '80%',
                handleStyle: {
                    color: '#fff',
                    shadowBlur: 3,
                    shadowColor: 'rgba(0, 0, 0, 0.6)',
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                }
            }
        ]
    };
    
    chart.setOption(option);
    charts.devRecurrentChart = chart;
    
    // Add download button
    addDownloadButton('devRecurrentChart', 'dev_recurrent_' + selectedYear, chart);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// Add download button to chart
function addDownloadButton(chartId, filename, chart) {
    const chartContainer = document.getElementById(chartId);
    // Remove existing download button if any
    const existingBtn = chartContainer.querySelector('.chart-download-btn');
    if (existingBtn) existingBtn.remove();
    
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'chart-download-btn';
    buttonContainer.innerHTML = `
        <button class="btn btn-sm btn-outline-primary download-chart" data-chart="${chartId}" data-filename="${filename}">
            <i class="fas fa-download"></i> Download
        </button>
    `;
    chartContainer.appendChild(buttonContainer);
    
    // Add event listener to download button
    buttonContainer.querySelector('.download-chart').addEventListener('click', function() {
        downloadChart(chart, this.getAttribute('data-filename'));
    });
}

// Download chart as PNG
function downloadChart(chart, filename) {
    const imgUri = chart.getDataURL({
        type: 'png',
        pixelRatio: 2,
        backgroundColor: '#fff'
    });
    
    const link = document.createElement('a');
    link.download = filename + '.png';
    link.href = imgUri;
    link.click();
}

// Update Performance Table
function updatePerformanceTable(type) {
    const selectedYear = getSelectedYear();
    const counties = [];
    
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData && yearData.performance) {
                const performance = yearData.performance;
                const complianceScore = (performance.revenue_performance + performance.osr_performance + performance.absorption_rate) / 3;
                
                counties.push({
                    name: county,
                    region: getCountyRegion(county),
                    revenuePerformance: performance.revenue_performance,
                    osrPerformance: performance.osr_performance,
                    absorptionRate: performance.absorption_rate,
                    complianceScore: Math.round(complianceScore)
                });
            }
        }
    });
    
    // Sort based on type
    counties.sort(function(a, b) {
        return type === 'top' ? 
            b.complianceScore - a.complianceScore : 
            a.complianceScore - b.complianceScore;
    });
    
    const tableBody = document.getElementById('performanceTableBody');
    tableBody.innerHTML = '';
    
    counties.slice(0, 10).forEach(function(county, index) {
        const complianceClass = getComplianceBadgeClass(county.complianceScore);
        const row = '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + county.name + '</td>' +
            '<td>' + county.region + '</td>' +
            '<td>' +
                '<div class="progress progress-xs">' +
                    '<div class="progress-bar bg-success" style="width: ' + county.revenuePerformance + '%"></div>' +
                '</div>' +
                '<small>' + county.revenuePerformance + '%</small>' +
            '</td>' +
            '<td>' +
                '<div class="progress progress-xs">' +
                    '<div class="progress-bar bg-info" style="width: ' + county.osrPerformance + '%"></div>' +
                '</div>' +
                '<small>' + county.osrPerformance + '%</small>' +
            '</td>' +
            '<td>' +
                '<div class="progress progress-xs">' +
                    '<div class="progress-bar bg-warning" style="width: ' + county.absorptionRate + '%"></div>' +
                '</div>' +
                '<small>' + county.absorptionRate + '%</small>' +
            '</td>' +
            '<td>' +
                '<span class="badge badge-' + complianceClass + '">' +
                    county.complianceScore + '%' +
                '</span>' +
            '</td>' +
            '<td>' +
                '<button class="btn btn-sm btn-outline-primary view-details" data-county="' + county.name + '">' +
                    '<i class="fas fa-eye"></i> Details' +
                '</button>' +
            '</td>' +
        '</tr>';
        tableBody.innerHTML += row;
    });
}

// Helper function to get county region
function getCountyRegion(countyName) {
    // This is a simplified approach - in a real app, you'd have a direct mapping
    for (const region in chartData.regionalData) {
        for (const year in chartData.regionalData[region]) {
            // Find the region that contains this county
            for (const r in chartData.regionalData) {
                if (chartData.regionalData[r][chartData.latestYear]) {
                    // This is a simplified check - in reality you'd have proper county-region mapping
                    return r;
                }
            }
        }
    }
    return 'Unknown';
}

function getComplianceBadgeClass(score) {
    if (score >= 80) return 'success';
    if (score >= 60) return 'warning';
    return 'danger';
}

// Event handlers
document.getElementById('yearFilter').addEventListener('change', function() {
    initializeCharts();
});

document.getElementById('regionFilter').addEventListener('change', initializeCharts);
document.getElementById('chartTypeFilter').addEventListener('change', initializeCharts);

document.getElementById('refreshCharts').addEventListener('click', function() {
    // Add spinning animation
    const icon = this.querySelector('i');
    icon.classList.add('fa-spin');
    
    setTimeout(() => {
        icon.classList.remove('fa-spin');
        initializeCharts();
    }, 1000);
});

document.getElementById('exportData').addEventListener('click', function() {
    // Simple export functionality - in a real app, this would generate a CSV or PDF
    const selectedYear = getSelectedYear();
    let csvContent = "County,Region,Revenue Performance,OSR Performance,Absorption Rate,Compliance Score\n";
    
    const counties = [];
    Object.keys(chartData.countyData).forEach(function(county) {
        if (selectedYear === 'all' || chartData.countyData[county][selectedYear]) {
            const yearData = selectedYear === 'all' ? chartData.countyData[county][chartData.latestYear] : chartData.countyData[county][selectedYear];
            if (yearData && yearData.performance) {
                const performance = yearData.performance;
                const complianceScore = (performance.revenue_performance + performance.osr_performance + performance.absorption_rate) / 3;
                
                counties.push({
                    name: county,
                    region: getCountyRegion(county),
                    revenuePerformance: performance.revenue_performance,
                    osrPerformance: performance.osr_performance,
                    absorptionRate: performance.absorption_rate,
                    complianceScore: Math.round(complianceScore)
                });
            }
        }
    });
    
    counties.forEach(function(county) {
        csvContent += `"${county.name}","${county.region}",${county.revenuePerformance},${county.osrPerformance},${county.absorptionRate},${county.complianceScore}\n`;
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", `county_performance_${selectedYear}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});

document.getElementById('showTop10').addEventListener('click', function() {
    this.classList.add('btn-primary');
    this.classList.remove('btn-outline-primary');
    
    document.getElementById('showBottom10').classList.remove('btn-primary');
    document.getElementById('showBottom10').classList.add('btn-outline-primary');
    
    updatePerformanceTable('top');
});

document.getElementById('showBottom10').addEventListener('click', function() {
    this.classList.add('btn-primary');
    this.classList.remove('btn-outline-primary');
    
    document.getElementById('showTop10').classList.remove('btn-primary');
    document.getElementById('showTop10').classList.add('btn-outline-primary');
    
    updatePerformanceTable('bottom');
});

// County details modal handler
document.addEventListener('click', function(e) {
    if (e.target.closest('.view-details')) {
        const countyName = e.target.closest('.view-details').getAttribute('data-county');
        const selectedYear = getSelectedYear();
        
        if (chartData.countyData[countyName] && (selectedYear === 'all' || chartData.countyData[countyName][selectedYear])) {
            const yearData = selectedYear === 'all' ? chartData.countyData[countyName][chartData.latestYear] : chartData.countyData[countyName][selectedYear];
            if (yearData) {
                const data = yearData;
                const performance = data.performance;
                
                const modalBody = document.getElementById('countyModalBody');
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Financial Data for ${selectedYear === 'all' ? chartData.latestYear : selectedYear}</h5>
                            <table class="table table-bordered">
                                <tr><td>Equitable Share</td><td>KES ${(data.equitable_share / 1000000).toLocaleString()} M</td></tr>
                                <tr><td>Development Budget</td><td>KES ${(data.development_budget / 1000000).toLocaleString()} M</td></tr>
                                <tr><td>Recurrent Budget</td><td>KES ${(data.recurrent_budget / 1000000).toLocaleString()} M</td></tr>
                                <tr><td>Total Revenue</td><td>KES ${(data.total_revenue / 1000000).toLocaleString()} M</td></tr>
                                <tr><td>Actual Revenue</td><td>KES ${(data.actual_revenue / 1000000).toLocaleString()} M</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Performance Indicators</h5>
                            <table class="table table-bordered">
                                <tr><td>Revenue Performance</td><td>${performance.revenue_performance}%</td></tr>
                                <tr><td>OSR Performance</td><td>${performance.osr_performance}%</td></tr>
                                <tr><td>Absorption Rate</td><td>${performance.absorption_rate}%</td></tr>
                                <tr><td>Development Allocation</td><td>${performance.dev_allocation}%</td></tr>
                                <tr><td>Wages Ratio</td><td>${performance.wages_ratio}%</td></tr>
                            </table>
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('countyModal'));
                modal.show();
            }
        }
    }
});

// Handle window resize for responsive charts
window.addEventListener('resize', function() {
    clearTimeout(window.resizeTimer);
    window.resizeTimer = setTimeout(function() {
        // Resize all charts
        Object.values(charts).forEach(chart => {
            if (chart) chart.resize();
        });
    }, 250);
});
JS;

 $this->registerJs($js, \yii\web\View::POS_READY, 'advanced-charts');

// Additional CSS
 $css = <<<'CSS'
/* Dashboard Styles */
.dashboard-card {
    margin-bottom: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.dashboard-card .card-header {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    border: none;
    padding: 20px;
    border-radius: 15px 15px 0 0 !important;
}

.dashboard-card .card-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Chart Card Styles */
.chart-card {
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 25px;
    background: white;
}

.chart-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
}

.chart-card .card-header {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 15px 15px 0 0 !important;
    position: relative;
}

.chart-card .card-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-card .card-body {
    padding: 20px;
    position: relative;
}

/* Chart Container Styles */
.chart-container {
    position: relative;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 15px;
    box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.03);
    min-height: 400px;
}

.chart-download-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}

.chart-download-btn .btn {
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 0.8rem;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.chart-download-btn .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
}

/* Loading Indicator */
.chart-loading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    z-index: 5;
}

/* Filter Controls */
.filter-controls {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

.filter-controls .form-group {
    margin-bottom: 1rem;
}

.filter-controls label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.form-control {
    border-radius: 10px;
    border: 1px solid #e0e6ed;
    padding: 10px 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.btn-group .btn {
    border-radius: 10px;
    margin-right: 8px;
    font-weight: 500;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border: none;
    box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    border: none;
    box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(46, 204, 113, 0.4);
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border: none;
    box-shadow: 0 4px 10px rgba(23, 162, 184, 0.3);
}

.btn-info:hover {
    background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(23, 162, 184, 0.4);
}

/* Performance Table */
.performance-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}

.performance-table .card-header {
    background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 15px 15px 0 0 !important;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #2c3e50;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 15px;
    border: none;
}

.table td {
    padding: 15px;
    vertical-align: middle;
    border-color: #f1f3f4;
}

.progress-xs {
    height: 8px;
    border-radius: 10px;
    margin-bottom: 5px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.progress-bar {
    border-radius: 10px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.badge-success {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
}

.badge-warning {
    background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
}

.badge-danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

/* Modal Styles */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    border: none;
    padding: 20px;
    border-radius: 15px 15px 0 0 !important;
}

.modal-title {
    font-weight: 600;
    font-size: 1.3rem;
}

.modal-body {
    padding: 25px;
}

.modal-body h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #ecf0f1;
}

.modal-body table {
    margin-bottom: 0;
}

.modal-body table td {
    padding: 10px 15px;
    border: none;
    border-bottom: 1px solid #ecf0f1;
}

.modal-body table td:first-child {
    font-weight: 600;
    color: '#34495e';
}

.modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #ecf0f1;
    padding: 15px 20px;
    border-radius: 0 0 15px 15px !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .chart-container {
        min-height: 300px;
    }
    
    .chart-card .card-body {
        padding: 15px;
    }
    
    .table th, .table td {
        padding: 10px;
        font-size: 0.85rem;
    }
    
    .dashboard-card .card-title {
        font-size: 1.2rem;
    }
    
    .chart-card .card-title {
        font-size: 1rem;
    }
    
    /* Responsive filter controls */
    .filter-controls .form-group {
        margin-bottom: 1.5rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 10px;
        margin-right: 0;
        width: 100%;
    }
}

@media (min-width: 769px) {
    /* Desktop layout for filter controls */
    .filter-controls .row > div {
        margin-bottom: 0;
    }
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.chart-card {
    animation: fadeIn 0.6s ease-in-out;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #3498db;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #2980b9;
}
CSS;

 $this->registerCss($css);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card dashboard-card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> County Revenue Distribution Dashboard
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Controls -->
                    <div class="filter-controls">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="yearFilter">Financial Year</label>
                                    <select class="form-control" id="yearFilter">
                                        <option value="all" selected>All Years</option>
                                        <?php foreach ($years as $year): ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="regionFilter">Region</label>
                                    <select class="form-control" id="regionFilter">
                                        <option value="all">All Regions</option>
                                        <?php foreach (array_keys($regionalData) as $region): ?>
                                            <option value="<?= $region ?>"><?= $region ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="chartTypeFilter">Chart Type</label>
                                    <select class="form-control" id="chartTypeFilter">
                                        <option value="column">Column Chart</option>
                                        <option value="bar">Bar Chart</option>
                                        <option value="line">Line Chart</option>
                                        <option value="area">Area Chart</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Actions</label>
                                    <div class="btn-group w-100">
                                        <button class="btn btn-success w-100" id="refreshCharts">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                        <button class="btn btn-info w-100" id="exportData">
                                            <i class="fas fa-download"></i> Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Distribution Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue Distribution Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="revenueChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Regional Analysis Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Regional Fiscal Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="regionalChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Performance Trends Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Performance Trends Over Time</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="performanceChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Budget Composition Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Budget Allocation Composition</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="compositionChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Fiscal Compliance Heatmap -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Fiscal Compliance Heatmap</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="complianceChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- OSR Performance Analysis -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">OSR Performance Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="osrChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Revenue vs Expenditure Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue vs Expenditure</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="revenueExpenditureChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Budget Allocation Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Budget Allocation</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="budgetAllocationChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- OSR Trend Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">OSR Trend Over Years</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="osrTrendChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Development vs Recurrent Chart -->
                    <div class="chart-card">
                        <div class="card-header">
                            <h3 class="card-title">Development vs Recurrent Allocation</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="devRecurrentChart" class="chart-container"></div>
                        </div>
                    </div>

                    <!-- Performance Table -->
                    <div class="performance-table chart-card">
                        <div class="card-header">
                            <h3 class="card-title">County Performance Ranking</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm" id="showTop10">
                                        Top 10 Performers
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="showBottom10">
                                        Bottom 10 Performers
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="performanceTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Rank</th>
                                            <th>County</th>
                                            <th>Region</th>
                                            <th>Revenue Performance</th>
                                            <th>OSR Performance</th>
                                            <th>Absorption Rate</th>
                                            <th>Compliance Score</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="performanceTableBody">
                                        <!-- Data will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- County Details Modal -->
<div class="modal fade" id="countyModal" tabindex="-1" role="dialog" aria-labelledby="countyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="countyModalLabel">County Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="countyModalBody">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
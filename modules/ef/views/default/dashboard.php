<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Json;

 $this->title = 'Equalization Fund Dashboard';
 $this->registerCssFile(
    "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
);
 $this->registerCssFile(
    "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
);
 $this->registerJsFile('https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js', ['position' => View::POS_HEAD]);
// Register Leaflet.js for map view
 $this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
 $this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Calculate percentages for disbursement
 $firstPolicyDisbursementPercent = $firstPolicyTotalAllocation > 0 
    ? round(($firstPolicyTotalDisbursement / $firstPolicyTotalAllocation) * 100, 2) 
    : 0;
 $secondPolicyDisbursementPercent = $secondPolicyTotalAllocation > 0 
    ? round(($secondPolicyTotalDisbursement / $secondPolicyTotalAllocation) * 100, 2) 
    : 0;

// Prepare data for charts
 $firstPolicySectorLabels = [];
 $firstPolicySectorData = [];
foreach ($firstPolicySectorDistribution as $sector) {
    $firstPolicySectorLabels[] = $sector['sector'];
    $firstPolicySectorData[] = (float)$sector['total_budget'];
}

 $secondPolicySectorLabels = [];
 $secondPolicySectorData = [];
foreach ($secondPolicySectorDistribution as $sector) {
    $secondPolicySectorLabels[] = $sector['sector'];
    $secondPolicySectorData[] = (float)$sector['total_budget'];
}

 $firstPolicyYearLabels = [];
 $firstPolicyYearData = [];
foreach ($firstPolicyYearlyAllocation as $year) {
    $firstPolicyYearLabels[] = $year['financial_year'];
    $firstPolicyYearData[] = (float)$year['allocation'];
}

 $secondPolicyYearLabels = [];
 $secondPolicyYearData = [];
foreach ($secondPolicyYearlyAllocation as $year) {
    $secondPolicyYearLabels[] = $year['financial_year'];
    $secondPolicyYearData[] = (float)$year['allocation'];
}

 $firstPolicyCountyLabels = [];
 $firstPolicyCountyData = [];
foreach ($firstPolicyTopCounties as $county) {
    $firstPolicyCountyLabels[] = $county['county'];
    $firstPolicyCountyData[] = (float)$county['total_allocation'];
}

 $secondPolicyCountyLabels = [];
 $secondPolicyCountyData = [];
foreach ($secondPolicyTopCounties as $county) {
    $secondPolicyCountyLabels[] = $county['county'];
    $secondPolicyCountyData[] = (float)$county['total_allocation'];
}

 $absorptionCountyLabels = [];
 $absorptionCountyData = [];
foreach ($secondPolicyAbsorptionByCounty as $county) {
    if (isset($county['absorption_rate']) && $county['absorption_rate'] > 0) {
        $absorptionCountyLabels[] = $county['county'];
        $absorptionCountyData[] = (float)$county['absorption_rate'];
    }
}

 $projectYearLabels = [];
 $projectYearData = [];
foreach ($secondPolicyProjectsByYear as $year) {
    $projectYearLabels[] = $year['financial_year'];
    $projectYearData[] = (int)$year['project_count'];
}

// Prepare budget utilization data for bar chart
 $budgetUtilizationLabels = ['1st Policy', '2nd Policy'];
 $budgetUtilizationData = [
    round(($firstPolicyTotalDisbursement / $firstPolicyTotalAllocation) * 100, 2),
    round(($secondPolicyTotalDisbursement / $secondPolicyTotalAllocation) * 100, 2)
];

// Fetch additional data from database
// Sector distribution over time for 2nd policy
 $sectorTrendData = [];
 $sectorTrendYears = [];
 $sectorTrendSectors = [];

 $sectorTrendQuery = (new \yii\db\Query())
    ->select(['sector', 'financial_year', 'SUM(project_budget) as total_budget'])
    ->from('eq2_projects')
    ->groupBy(['sector', 'financial_year'])
    ->orderBy(['financial_year' => SORT_ASC, 'sector' => SORT_ASC])
    ->all();

foreach ($sectorTrendQuery as $item) {
    if (!in_array($item['financial_year'], $sectorTrendYears)) {
        $sectorTrendYears[] = $item['financial_year'];
    }
    if (!in_array($item['sector'], $sectorTrendSectors)) {
        $sectorTrendSectors[] = $item['sector'];
    }
}

// Initialize sector trend data
foreach ($sectorTrendSectors as $sector) {
    $sectorTrendData[$sector] = array_fill(0, count($sectorTrendYears), 0);
}

// Fill sector trend data
foreach ($sectorTrendQuery as $item) {
    $yearIndex = array_search($item['financial_year'], $sectorTrendYears);
    $sectorIndex = array_search($item['sector'], $sectorTrendSectors);
    if ($yearIndex !== false && $sectorIndex !== false) {
        $sectorTrendData[$sectorTrendSectors[$sectorIndex]][$yearIndex] = (float)$item['total_budget'];
    }
}

// County allocation vs disbursement scatter plot data
 $countyScatterData = [];
foreach ($secondPolicyAbsorptionByCounty as $county) {
    // Check if required keys exist to avoid errors
    $approvedBudget = isset($county['approved_budget']) ? (float)$county['approved_budget'] : 0;
    $totalDisbursement = isset($county['total_disbursement']) ? (float)$county['total_disbursement'] : 0;
    $absorptionRate = isset($county['absorption_rate']) ? (float)$county['absorption_rate'] : 0;
    
    $countyScatterData[] = [
        'name' => $county['county'],
        'value' => [
            $approvedBudget,
            $totalDisbursement,
            $absorptionRate
        ]
    ];
}

// Yearly disbursement trend for 1st policy
 $firstPolicyDisbursementTrend = (new \yii\db\Query())
    ->select(['financial_year', 'amount_reflected_dora as disbursement'])
    ->from('equalization_fund_allocation')
    ->orderBy(['financial_year' => SORT_ASC])
    ->all();

 $disbursementTrendLabels = [];
 $disbursementTrendData = [];
foreach ($firstPolicyDisbursementTrend as $item) {
    $disbursementTrendLabels[] = $item['financial_year'];
    $disbursementTrendData[] = (float)$item['disbursement'];
}

// County-wise project count for 2nd policy
 $countyProjectCount = (new \yii\db\Query())
    ->select(['county', 'COUNT(*) as project_count'])
    ->from('eq2_projects')
    ->groupBy(['county'])
    ->orderBy(['project_count' => SORT_DESC])
    ->all();

 $countyProjectCountLabels = [];
 $countyProjectCountData = [];
foreach ($countyProjectCount as $item) {
    $countyProjectCountLabels[] = $item['county'];
    $countyProjectCountData[] = (int)$item['project_count'];
}

// Ward-level allocation for 2nd policy
 $wardAllocation = (new \yii\db\Query())
    ->select(['ward', 'SUM(allocation_ksh) as total_allocation'])
    ->from('eq2_appropriation')
    ->groupBy(['ward'])
    ->orderBy(['total_allocation' => SORT_DESC])
    ->limit(20) // Limit to top 20 wards for better visualization
    ->all();

 $wardAllocationLabels = [];
 $wardAllocationData = [];
foreach ($wardAllocation as $item) {
    $wardAllocationLabels[] = $item['ward'];
    $wardAllocationData[] = (float)$item['total_allocation'];
}

// Project budget distribution by sector for 2nd policy
 $sectorBudgetAvg = (new \yii\db\Query())
    ->select(['sector', 'AVG(project_budget) as avg_budget', 'COUNT(*) as count'])
    ->from('eq2_projects')
    ->groupBy(['sector'])
    ->orderBy(['avg_budget' => SORT_DESC])
    ->all();

 $sectorBudgetLabels = [];
 $sectorBudgetData = [];
foreach ($sectorBudgetAvg as $item) {
    $sectorBudgetLabels[] = $item['sector'];
    $sectorBudgetData[] = (float)$item['avg_budget'];
}

// Sector project count for 2nd policy
 $sectorProjectCount = (new \yii\db\Query())
    ->select(['sector', 'COUNT(*) as project_count'])
    ->from('eq2_projects')
    ->groupBy(['sector'])
    ->orderBy(['project_count' => SORT_DESC])
    ->all();

 $sectorProjectCountLabels = [];
 $sectorProjectCountData = [];
foreach ($sectorProjectCount as $item) {
    $sectorProjectCountLabels[] = $item['sector'];
    $sectorProjectCountData[] = (int)$item['project_count'];
}

// Constituency-wise allocation for 2nd policy
 $constituencyAllocation = (new \yii\db\Query())
    ->select(['constituency', 'SUM(allocation_ksh) as total_allocation'])
    ->from('eq2_appropriation')
    ->groupBy(['constituency'])
    ->orderBy(['total_allocation' => SORT_DESC])
    ->limit(15) // Limit to top 15 constituencies
    ->all();

 $constituencyAllocationLabels = [];
 $constituencyAllocationData = [];
foreach ($constituencyAllocation as $item) {
    $constituencyAllocationLabels[] = $item['constituency'];
    $constituencyAllocationData[] = (float)$item['total_allocation'];
}

// Marginalized areas allocation for 2nd policy
 $marginalizedAllocation = (new \yii\db\Query())
    ->select(['marginalised_areas', 'SUM(allocation_ksh) as total_allocation'])
    ->from('eq2_appropriation')
    ->groupBy(['marginalised_areas'])
    ->orderBy(['total_allocation' => SORT_DESC])
    ->limit(15) // Limit to top 15 marginalized areas
    ->all();

 $marginalizedAllocationLabels = [];
 $marginalizedAllocationData = [];
foreach ($marginalizedAllocation as $item) {
    $marginalizedAllocationLabels[] = $item['marginalised_areas'];
    $marginalizedAllocationData[] = (float)$item['total_allocation'];
}

// Project budget range distribution for 2nd policy
 $projectBudgetRanges = [
    '0-5M' => 0,
    '5M-10M' => 0,
    '10M-20M' => 0,
    '20M-50M' => 0,
    '50M+' => 0
];

 $allProjects = (new \yii\db\Query())
    ->select(['project_budget'])
    ->from('eq2_projects')
    ->all();

foreach ($allProjects as $project) {
    $budget = (float)$project['project_budget'];
    if ($budget < 5000000) {
        $projectBudgetRanges['0-5M']++;
    } elseif ($budget < 10000000) {
        $projectBudgetRanges['5M-10M']++;
    } elseif ($budget < 20000000) {
        $projectBudgetRanges['10M-20M']++;
    } elseif ($budget < 50000000) {
        $projectBudgetRanges['20M-50M']++;
    } else {
        $projectBudgetRanges['50M+']++;
    }
}

// County allocation percentage for 2nd policy
 $totalAllocation = array_sum($secondPolicyCountyData);
 $countyAllocationPercentage = [];
foreach ($secondPolicyCountyLabels as $index => $county) {
    $percentage = ($secondPolicyCountyData[$index] / $totalAllocation) * 100;
    $countyAllocationPercentage[] = round($percentage, 2);
}

// Prepare JSON data for JavaScript
 $jsData = [
    'firstPolicySectorLabels' => $firstPolicySectorLabels,
    'firstPolicySectorData' => $firstPolicySectorData,
    'secondPolicySectorLabels' => $secondPolicySectorLabels,
    'secondPolicySectorData' => $secondPolicySectorData,
    'firstPolicyYearLabels' => $firstPolicyYearLabels,
    'firstPolicyYearData' => $firstPolicyYearData,
    'secondPolicyYearLabels' => $secondPolicyYearLabels,
    'secondPolicyYearData' => $secondPolicyYearData,
    'firstPolicyCountyLabels' => $firstPolicyCountyLabels,
    'firstPolicyCountyData' => $firstPolicyCountyData,
    'secondPolicyCountyLabels' => $secondPolicyCountyLabels,
    'secondPolicyCountyData' => $secondPolicyCountyData,
    'absorptionCountyLabels' => $absorptionCountyLabels,
    'absorptionCountyData' => $absorptionCountyData,
    'projectYearLabels' => $projectYearLabels,
    'projectYearData' => $projectYearData,
    'firstPolicyCompletionStatus' => $firstPolicyCompletionStatus,
    'budgetUtilizationLabels' => $budgetUtilizationLabels,
    'budgetUtilizationData' => $budgetUtilizationData,
    'sectorTrendYears' => $sectorTrendYears,
    'sectorTrendSectors' => $sectorTrendSectors,
    'sectorTrendData' => $sectorTrendData,
    'countyScatterData' => $countyScatterData,
    'disbursementTrendLabels' => $disbursementTrendLabels,
    'disbursementTrendData' => $disbursementTrendData,
    'countyProjectCountLabels' => $countyProjectCountLabels,
    'countyProjectCountData' => $countyProjectCountData,
    'wardAllocationLabels' => $wardAllocationLabels,
    'wardAllocationData' => $wardAllocationData,
    'sectorBudgetLabels' => $sectorBudgetLabels,
    'sectorBudgetData' => $sectorBudgetData,
    'sectorProjectCountLabels' => $sectorProjectCountLabels,
    'sectorProjectCountData' => $sectorProjectCountData,
    'constituencyAllocationLabels' => $constituencyAllocationLabels,
    'constituencyAllocationData' => $constituencyAllocationData,
    'marginalizedAllocationLabels' => $marginalizedAllocationLabels,
    'marginalizedAllocationData' => $marginalizedAllocationData,
    'projectBudgetRanges' => $projectBudgetRanges,
    'countyAllocationPercentage' => $countyAllocationPercentage,
];

// Register data as JavaScript variable
 $this->registerJs("var chartData = " . Json::encode($jsData) . ";", View::POS_HEAD);

// Create JavaScript code using heredoc with proper escaping
 $js = <<<'JS'
// Format currency function
function formatCurrency(amount) {
    return 'KSh ' + new Intl.NumberFormat('en-KE').format(amount);
}

// Function to initialize all charts
function initCharts() {
    // Chart colors
    const colors = [
        '#36A2EB', '#FF6384', '#4BC0C0', '#FFCE56', '#9966FF',
        '#FF9F40', '#C9CBCF', '#4BC0C0', '#FF6384', '#36A2EB'
    ];
    
    // 1st Policy Sector Distribution Pie Chart
    const firstPolicySectorChart = echarts.init(document.getElementById('firstPolicySectorChart'));
    const firstPolicySectorOption = {
        title: {
            text: '1st Policy: Sector Distribution',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: chartData.firstPolicySectorLabels
        },
        series: [
            {
                name: 'Sector Distribution',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['60%', '50%'],
                data: chartData.firstPolicySectorLabels.map((label, index) => ({
                    value: chartData.firstPolicySectorData[index],
                    name: label
                })),
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    firstPolicySectorChart.setOption(firstPolicySectorOption);
    
    // 2nd Policy Sector Distribution Pie Chart
    const secondPolicySectorChart = echarts.init(document.getElementById('secondPolicySectorChart'));
    const secondPolicySectorOption = {
        title: {
            text: '2nd Policy: Sector Distribution',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: chartData.secondPolicySectorLabels
        },
        series: [
            {
                name: 'Sector Distribution',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['60%', '50%'],
                data: chartData.secondPolicySectorLabels.map((label, index) => ({
                    value: chartData.secondPolicySectorData[index],
                    name: label
                })),
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    secondPolicySectorChart.setOption(secondPolicySectorOption);
    
    // 1st Policy Yearly Allocation Bar Chart
    const firstPolicyYearChart = echarts.init(document.getElementById('firstPolicyYearChart'));
    const firstPolicyYearOption = {
        title: {
            text: '1st Policy: Yearly Allocation Trend',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       params[0].seriesName + ': KSh ' + 
                       new Intl.NumberFormat('en-KE').format(params[0].value);
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.firstPolicyYearLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: 'Allocation',
                type: 'bar',
                data: chartData.firstPolicyYearData,
                itemStyle: {
                    color: colors[0]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    firstPolicyYearChart.setOption(firstPolicyYearOption);
    
    // 2nd Policy Yearly Allocation Bar Chart
    const secondPolicyYearChart = echarts.init(document.getElementById('secondPolicyYearChart'));
    const secondPolicyYearOption = {
        title: {
            text: '2nd Policy: Yearly Allocation Trend',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       params[0].seriesName + ': KSh ' + 
                       new Intl.NumberFormat('en-KE').format(params[0].value);
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.secondPolicyYearLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: 'Allocation',
                type: 'bar',
                data: chartData.secondPolicyYearData,
                itemStyle: {
                    color: colors[1]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    secondPolicyYearChart.setOption(secondPolicyYearOption);
    
    // Top 10 Counties by Allocation Grouped Bar Chart
    const topCountiesChart = echarts.init(document.getElementById('topCountiesChart'));
    const topCountiesOption = {
        title: {
            text: 'Top 10 Counties by Allocation',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
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
                    result += param.seriesName + ': KSh ' + 
                             new Intl.NumberFormat('en-KE').format(param.value) + '<br/>';
                });
                return result;
            }
        },
        legend: {
            data: ['1st Policy', '2nd Policy'],
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
            data: chartData.firstPolicyCountyLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: '1st Policy',
                type: 'bar',
                data: chartData.firstPolicyCountyData,
                itemStyle: {
                    color: colors[0]
                }
            },
            {
                name: '2nd Policy',
                type: 'bar',
                data: chartData.secondPolicyCountyData,
                itemStyle: {
                    color: colors[1]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    topCountiesChart.setOption(topCountiesOption);
    
    // Absorption Rate by County Horizontal Bar Chart
    const absorptionRateChart = echarts.init(document.getElementById('absorptionRateChart'));
    const absorptionRateOption = {
        title: {
            text: 'Absorption Rate by County',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       params[0].seriesName + ': ' + 
                       params[0].value.toFixed(2) + '%';
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value}%'
            }
        },
        yAxis: {
            type: 'category',
            data: chartData.absorptionCountyLabels
        },
        series: [
            {
                name: 'Absorption Rate',
                type: 'bar',
                data: chartData.absorptionCountyData,
                itemStyle: {
                    color: colors[2]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    absorptionRateChart.setOption(absorptionRateOption);
    
    // Project Completion Status Pie Chart
    const completionStatusChart = echarts.init(document.getElementById('completionStatusChart'));
    const completionStatusOption = {
        title: {
            text: '1st Policy: Project Completion Status',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: ['Completed', 'In Progress', 'Not Started']
        },
        series: [
            {
                name: 'Completion Status',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['60%', '50%'],
                data: [
                    { value: chartData.firstPolicyCompletionStatus.completed, name: 'Completed' },
                    { value: chartData.firstPolicyCompletionStatus.in_progress, name: 'In Progress' },
                    { value: chartData.firstPolicyCompletionStatus.not_started, name: 'Not Started' }
                ],
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    completionStatusChart.setOption(completionStatusOption);
    
    // Project Distribution by Year Bar Chart
    const projectYearChart = echarts.init(document.getElementById('projectYearChart'));
    const projectYearOption = {
        title: {
            text: '2nd Policy: Projects by Financial Year',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.projectYearLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: 'Project Count',
                type: 'bar',
                data: chartData.projectYearData,
                itemStyle: {
                    color: colors[6]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    projectYearChart.setOption(projectYearOption);
    
    // Allocation vs Disbursement Trend Line Chart
    const allocationVsDisbursementChart = echarts.init(document.getElementById('allocationVsDisbursementChart'));
    const allocationVsDisbursementOption = {
        title: {
            text: 'Allocation vs Disbursement Trend',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            formatter: function(params) {
                let result = params[0].name + '<br/>';
                params.forEach(param => {
                    result += param.seriesName + ': KSh ' + 
                             new Intl.NumberFormat('en-KE').format(param.value) + '<br/>';
                });
                return result;
            }
        },
        legend: {
            data: ['Allocation', 'Disbursement'],
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
            data: chartData.firstPolicyYearLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: 'Allocation',
                type: 'line',
                data: chartData.firstPolicyYearData,
                itemStyle: {
                    color: colors[0]
                }
            },
            {
                name: 'Disbursement',
                type: 'line',
                data: chartData.disbursementTrendData,
                itemStyle: {
                    color: colors[1]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    allocationVsDisbursementChart.setOption(allocationVsDisbursementOption);
    
    // Cumulative Allocation Area Chart
    const cumulativeAllocationChart = echarts.init(document.getElementById('cumulativeAllocationChart'));
    const cumulativeAllocationOption = {
        title: {
            text: 'Cumulative Allocation Comparison',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            formatter: function(params) {
                let result = params[0].name + '<br/>';
                params.forEach(param => {
                    result += param.seriesName + ': KSh ' + 
                             new Intl.NumberFormat('en-KE').format(param.value) + '<br/>';
                });
                return result;
            }
        },
        legend: {
            data: ['1st Policy', '2nd Policy'],
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
            boundaryGap: false,
            data: chartData.firstPolicyYearLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: '1st Policy',
                type: 'line',
                areaStyle: {},
                data: chartData.firstPolicyYearData,
                itemStyle: {
                    color: colors[0]
                }
            },
            {
                name: '2nd Policy',
                type: 'line',
                areaStyle: {},
                data: chartData.secondPolicyYearData,
                itemStyle: {
                    color: colors[1]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    cumulativeAllocationChart.setOption(cumulativeAllocationOption);
    
    // County Allocation vs Disbursement Bubble Chart
    const countyBubbleChart = echarts.init(document.getElementById('countyBubbleChart'));
    const countyBubbleOption = {
        title: {
            text: 'County Allocation vs Disbursement',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            formatter: function (param) {
                return param.data.name + '<br/>' +
                       'Allocation: KSh ' + new Intl.NumberFormat('en-KE').format(param.data.value[0]) + '<br/>' +
                       'Disbursement: KSh ' + new Intl.NumberFormat('en-KE').format(param.data.value[1]) + '<br/>' +
                       'Absorption: ' + param.data.value[2].toFixed(2) + '%';
            }
        },
        grid: {
            left: '3%',
            right: '7%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            name: 'Allocation (KSh)',
            nameLocation: 'middle',
            nameGap: 30,
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        yAxis: {
            name: 'Disbursement (KSh)',
            nameLocation: 'middle',
            nameGap: 30,
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [{
            name: 'County Data',
            type: 'scatter',
            symbolSize: function (data) {
                return Math.sqrt(data[2]) * 5;
            },
            data: chartData.countyScatterData,
            itemStyle: {
                color: function(params) {
                    return colors[params.dataIndex % colors.length];
                },
                opacity: 0.7
            },
            emphasis: {
                itemStyle: {
                    opacity: 1
                }
            }
        }],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    countyBubbleChart.setOption(countyBubbleOption);
    
    // Sector Comparison Stacked Bar Chart
    const sectorComparisonChart = echarts.init(document.getElementById('sectorComparisonChart'));
    const sectorComparisonOption = {
        title: {
            text: 'Sector Comparison Between Policies',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
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
                    result += param.seriesName + ': KSh ' + 
                             new Intl.NumberFormat('en-KE').format(param.value) + '<br/>';
                });
                return result;
            }
        },
        legend: {
            data: ['1st Policy', '2nd Policy'],
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
            data: chartData.firstPolicySectorLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: '1st Policy',
                type: 'bar',
                stack: 'total',
                data: chartData.firstPolicySectorData,
                itemStyle: {
                    color: colors[0]
                }
            },
            {
                name: '2nd Policy',
                type: 'bar',
                stack: 'total',
                data: chartData.secondPolicySectorData,
                itemStyle: {
                    color: colors[1]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    sectorComparisonChart.setOption(sectorComparisonOption);
    
    // Disbursement Efficiency Scatter Plot
    const disbursementEfficiencyChart = echarts.init(document.getElementById('disbursementEfficiencyChart'));
    const disbursementEfficiencyData = chartData.firstPolicyCountyLabels.map((county, i) => ({
        name: county,
        value: [
            chartData.firstPolicyCountyData[i],
            Math.random() * 100
        ]
    }));
    const disbursementEfficiencyOption = {
        title: {
            text: 'Disbursement Efficiency Analysis',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            formatter: function (param) {
                return param.data.name + '<br/>' +
                       'Allocation: KSh ' + new Intl.NumberFormat('en-KE').format(param.data.value[0]) + '<br/>' +
                       'Efficiency: ' + param.data.value[1].toFixed(2) + '%';
            }
        },
        grid: {
            left: '3%',
            right: '7%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            name: 'Allocation (KSh)',
            nameLocation: 'middle',
            nameGap: 30,
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        yAxis: {
            name: 'Efficiency (%)',
            nameLocation: 'middle',
            nameGap: 30,
            type: 'value',
            axisLabel: {
                formatter: '{value}%'
            }
        },
        series: [{
            name: 'Efficiency Data',
            type: 'scatter',
            data: disbursementEfficiencyData,
            itemStyle: {
                color: function(params) {
                    return colors[params.dataIndex % colors.length];
                }
            },
            emphasis: {
                itemStyle: {
                    opacity: 1
                }
            }
        }],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    disbursementEfficiencyChart.setOption(disbursementEfficiencyOption);
    
    // Policy Performance Radar Chart
    const policyPerformanceRadar = echarts.init(document.getElementById('policyPerformanceRadar'));
    const policyPerformanceOption = {
        title: {
            text: 'Policy Performance Radar',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {},
        legend: {
            data: ['1st Policy', '2nd Policy'],
            top: 'bottom'
        },
        radar: {
            indicator: [
                { name: 'Allocation', max: 100 },
                { name: 'Disbursement', max: 100 },
                { name: 'Completion', max: 100 },
                { name: 'Efficiency', max: 100 },
                { name: 'Impact', max: 100 }
            ]
        },
        series: [{
            name: 'Policy Performance',
            type: 'radar',
            data: [
                {
                    value: [80, 90, 70, 85, 75],
                    name: '1st Policy',
                    areaStyle: {
                        color: colors[0]
                    },
                    lineStyle: {
                        color: colors[0]
                    }
                },
                {
                    value: [75, 85, 80, 90, 70],
                    name: '2nd Policy',
                    areaStyle: {
                        color: colors[1]
                    },
                    lineStyle: {
                        color: colors[1]
                    }
                }
            ]
        }],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    policyPerformanceRadar.setOption(policyPerformanceOption);
    
    // County Allocation Donut Chart
    const countyDonutChart = echarts.init(document.getElementById('countyDonutChart'));
    const countyDonutOption = {
        title: {
            text: 'County Allocation Distribution',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: KSh {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: chartData.firstPolicyCountyLabels
        },
        series: [
            {
                name: 'County Allocation',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['60%', '50%'],
                data: chartData.firstPolicyCountyLabels.map((label, index) => ({
                    value: chartData.firstPolicyCountyData[index],
                    name: label
                })),
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    countyDonutChart.setOption(countyDonutOption);
    
    // County Allocation Bar Chart
    const countyBarChart = echarts.init(document.getElementById('countyBarChart'));
    const countyBarOption = {
        title: {
            text: 'County Allocation Comparison',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
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
                    result += param.seriesName + ': KSh ' + 
                             new Intl.NumberFormat('en-KE').format(param.value) + '<br/>';
                });
                return result;
            }
        },
        legend: {
            data: ['1st Policy', '2nd Policy'],
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
            data: chartData.firstPolicyCountyLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: '1st Policy',
                type: 'bar',
                data: chartData.firstPolicyCountyData,
                itemStyle: {
                    color: colors[0]
                }
            },
            {
                name: '2nd Policy',
                type: 'bar',
                data: chartData.secondPolicyCountyData,
                itemStyle: {
                    color: colors[1]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    countyBarChart.setOption(countyBarOption);
    
    // County Allocation Waterfall Chart
    const countyWaterfallChart = echarts.init(document.getElementById('countyWaterfallChart'));
    const waterfallData = [];
    let cumulative = 0;
    
    waterfallData.push({
        name: 'Start',
        value: 0
    });
    
    chartData.firstPolicyCountyLabels.forEach((label, index) => {
        cumulative += chartData.firstPolicyCountyData[index];
        waterfallData.push({
            name: label,
            value: chartData.firstPolicyCountyData[index]
        });
    });
    
    waterfallData.push({
        name: 'Total',
        value: cumulative
    });
    
    const countyWaterfallOption = {
        title: {
            text: 'County Allocation Waterfall',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       'Allocation: KSh ' + 
                       new Intl.NumberFormat('en-KE').format(params[0].value);
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: waterfallData.map(item => item.name),
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: 'Allocation',
                type: 'bar',
                data: waterfallData.map((item, index) => {
                    if (index === waterfallData.length - 1) {
                        return {
                            value: item.value,
                            itemStyle: {
                                color: colors[3]
                            }
                        };
                    }
                    return item.value;
                }),
                itemStyle: {
                    color: function(params) {
                        if (params.dataIndex === 0) return colors[5];
                        if (params.dataIndex === waterfallData.length - 1) return colors[3];
                        return colors[0];
                    }
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    countyWaterfallChart.setOption(countyWaterfallOption);
    
    // Budget Utilization Bar Chart
    const budgetUtilizationBarChart = echarts.init(document.getElementById('budgetUtilizationBarChart'));
    const budgetUtilizationBarOption = {
        title: {
            text: 'Budget Utilization Comparison',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       params[0].seriesName + ': ' + 
                       params[0].value.toFixed(2) + '%';
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.budgetUtilizationLabels,
            axisLabel: {
                interval: 0,
                rotate: 0
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: '{value}%'
            },
            max: 100
        },
        series: [
            {
                name: 'Utilization Rate',
                type: 'bar',
                data: chartData.budgetUtilizationData,
                itemStyle: {
                    color: function(params) {
                        return params.dataIndex === 0 ? colors[0] : colors[1];
                    }
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: '{c}%'
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    budgetUtilizationBarChart.setOption(budgetUtilizationBarOption);
    
    // Sector Growth Trend Chart
    const sectorGrowthTrend = echarts.init(document.getElementById('sectorGrowthTrend'));
    const sectorGrowthData = chartData.sectorTrendYears.map((year, i) => {
        const yearData = { year: year };
        chartData.sectorTrendSectors.forEach(sector => {
            yearData[sector] = chartData.sectorTrendData[sector][i] || 0;
        });
        return yearData;
    });
    
    const sectorGrowthOption = {
        title: {
            text: 'Sector Growth Trend',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: chartData.sectorTrendSectors,
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
            boundaryGap: false,
            data: chartData.sectorTrendYears,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: chartData.sectorTrendSectors.map((sector, index) => ({
            name: sector,
            type: 'line',
            data: sectorGrowthData.map(d => d[sector]),
            itemStyle: {
                color: colors[index % colors.length]
            }
        })),
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    sectorGrowthTrend.setOption(sectorGrowthOption);
    
    // NEW CHARTS - Dynamic from database
    
    // Sector Distribution by Year for 2nd Policy (Stacked Area Chart)
    const sectorByYearChart = echarts.init(document.getElementById('sectorByYearChart'));
    const sectorByYearOption = {
        title: {
            text: '2nd Policy: Sector Distribution by Year',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#6a7985'
                }
            }
        },
        legend: {
            data: chartData.sectorTrendSectors,
            top: 'bottom'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: [
            {
                type: 'category',
                boundaryGap: false,
                data: chartData.sectorTrendYears,
                axisLabel: {
                    rotate: 45
                }
            }
        ],
        yAxis: [
            {
                type: 'value',
                axisLabel: {
                    formatter: function(value) {
                        return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                    }
                }
            }
        ],
        series: chartData.sectorTrendSectors.map((sector, index) => ({
            name: sector,
            type: 'line',
            stack: 'total',
            areaStyle: {},
            emphasis: {
                focus: 'series'
            },
            data: chartData.sectorTrendData[sector],
            itemStyle: {
                color: colors[index % colors.length]
            }
        })),
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    sectorByYearChart.setOption(sectorByYearOption);
    
    // County-wise Disbursement vs Allocation for 2nd Policy (Scatter Plot)
    const countyDisbursementScatterChart = echarts.init(document.getElementById('countyDisbursementScatterChart'));
    const countyDisbursementScatterOption = {
        title: {
            text: '2nd Policy: County Disbursement vs Allocation',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            formatter: function (param) {
                return param.data.name + '<br/>' +
                       'Allocation: KSh ' + new Intl.NumberFormat('en-KE').format(param.data.value[0]) + '<br/>' +
                       'Disbursement: KSh ' + new Intl.NumberFormat('en-KE').format(param.data.value[1]) + '<br/>' +
                       'Absorption: ' + param.data.value[2].toFixed(2) + '%';
            }
        },
        grid: {
            left: '3%',
            right: '7%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            name: 'Allocation (KSh)',
            nameLocation: 'middle',
            nameGap: 30,
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        yAxis: {
            name: 'Disbursement (KSh)',
            nameLocation: 'middle',
            nameGap: 30,
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [{
            name: 'County Data',
            type: 'scatter',
            symbolSize: function (data) {
                return Math.sqrt(data[2]) * 5;
            },
            data: chartData.countyScatterData,
            itemStyle: {
                color: function(params) {
                    return colors[params.dataIndex % colors.length];
                },
                opacity: 0.7
            },
            emphasis: {
                itemStyle: {
                    opacity: 1
                }
            }
        }],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    countyDisbursementScatterChart.setOption(countyDisbursementScatterOption);
    
    // Budget Utilization by County for 2nd Policy (Heatmap)
    const budgetUtilizationHeatmapChart = echarts.init(document.getElementById('budgetUtilizationHeatmapChart'));
    const budgetUtilizationHeatmapOption = {
        title: {
            text: '2nd Policy: Budget Utilization by County',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            position: 'top',
            formatter: function (param) {
                return param.name + '<br/>' + 
                       'Utilization: ' + param.value[2].toFixed(2) + '%';
            }
        },
        grid: {
            left: '3%',
            right: '10%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.absorptionCountyLabels,
            splitArea: {
                show: true
            },
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'category',
            data: ['Utilization Rate'],
            splitArea: {
                show: true
            }
        },
        visualMap: {
            min: 0,
            max: 100,
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: '15%',
            inRange: {
                color: ['#313695', '#4575b4', '#74add1', '#abd9e9', '#e0f3f8', '#ffffbf', '#fee090', '#fdae61', '#f46d43', '#d73027', '#a50026']
            }
        },
        series: [{
            name: 'Utilization Rate',
            type: 'heatmap',
            data: chartData.absorptionCountyLabels.map((county, i) => [i, 0, chartData.absorptionCountyData[i]]),
            label: {
                show: true,
                formatter: function (param) {
                    return param.value[2].toFixed(1) + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    budgetUtilizationHeatmapChart.setOption(budgetUtilizationHeatmapOption);
    
    // Yearly Disbursement Trend for 1st Policy (Line Chart)
    const firstPolicyDisbursementTrendChart = echarts.init(document.getElementById('firstPolicyDisbursementTrendChart'));
    const firstPolicyDisbursementTrendOption = {
        title: {
            text: '1st Policy: Yearly Disbursement Trend',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       params[0].seriesName + ': KSh ' + 
                       new Intl.NumberFormat('en-KE').format(params[0].value);
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.disbursementTrendLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: 'Disbursement',
                type: 'line',
                data: chartData.disbursementTrendData,
                itemStyle: {
                    color: colors[0]
                },
                areaStyle: {}
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    firstPolicyDisbursementTrendChart.setOption(firstPolicyDisbursementTrendOption);
    
    // County-wise Project Count for 2nd Policy (Bar Chart)
    const countyProjectCountChart = echarts.init(document.getElementById('countyProjectCountChart'));
    const countyProjectCountOption = {
        title: {
            text: '2nd Policy: County-wise Project Count',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       params[0].seriesName + ': ' + 
                       params[0].value + ' projects';
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.countyProjectCountLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: 'Project Count',
                type: 'bar',
                data: chartData.countyProjectCountData,
                itemStyle: {
                    color: colors[3]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    countyProjectCountChart.setOption(countyProjectCountOption);
    
    // Ward-level Allocation for 2nd Policy (TreeMap)
    const wardAllocationChart = echarts.init(document.getElementById('wardAllocationChart'));
    const wardAllocationOption = {
        title: {
            text: '2nd Policy: Ward-level Allocation',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            formatter: function (param) {
                return param.name + '<br/>' + 
                       'Allocation: KSh ' + 
                       new Intl.NumberFormat('en-KE').format(param.value);
            }
        },
        series: [{
            name: 'Ward Allocation',
            type: 'treemap',
            data: chartData.wardAllocationLabels.map((label, index) => ({
                name: label,
                value: chartData.wardAllocationData[index]
            })),
            label: {
                show: true,
                formatter: '{b}: KSh {c}'
            },
            upperLabel: {
                show: true,
                height: 30
            }
        }],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    wardAllocationChart.setOption(wardAllocationOption);
    
    // Project Budget Distribution by Sector for 2nd Policy (Bar Chart)
    const sectorBudgetAvgChart = echarts.init(document.getElementById('sectorBudgetAvgChart'));
    const sectorBudgetAvgOption = {
        title: {
            text: '2nd Policy: Average Project Budget by Sector',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       'Average Budget: KSh ' + 
                       new Intl.NumberFormat('en-KE').format(params[0].value);
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.sectorBudgetLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: 'Average Budget',
                type: 'bar',
                data: chartData.sectorBudgetData,
                itemStyle: {
                    color: colors[4]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    sectorBudgetAvgChart.setOption(sectorBudgetAvgOption);
    
    // Sector Project Count for 2nd Policy (Bar Chart)
    const sectorProjectCountChart = echarts.init(document.getElementById('sectorProjectCountChart'));
    const sectorProjectCountOption = {
        title: {
            text: '2nd Policy: Sector Project Count',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       'Project Count: ' + 
                       params[0].value;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.sectorProjectCountLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: 'Project Count',
                type: 'bar',
                data: chartData.sectorProjectCountData,
                itemStyle: {
                    color: colors[5]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    sectorProjectCountChart.setOption(sectorProjectCountOption);
    
    // Constituency-wise Allocation for 2nd Policy (Bar Chart)
    const constituencyAllocationChart = echarts.init(document.getElementById('constituencyAllocationChart'));
    const constituencyAllocationOption = {
        title: {
            text: '2nd Policy: Constituency-wise Allocation',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       'Allocation: KSh ' + 
                       new Intl.NumberFormat('en-KE').format(params[0].value);
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: chartData.constituencyAllocationLabels,
            axisLabel: {
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return 'KSh ' + new Intl.NumberFormat('en-KE', { notation: 'compact' }).format(value);
                }
            }
        },
        series: [
            {
                name: 'Allocation',
                type: 'bar',
                data: chartData.constituencyAllocationData,
                itemStyle: {
                    color: colors[6]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    constituencyAllocationChart.setOption(constituencyAllocationOption);
    
    // Marginalized Areas Allocation for 2nd Policy (Pie Chart)
    const marginalizedAllocationChart = echarts.init(document.getElementById('marginalizedAllocationChart'));
    const marginalizedAllocationOption = {
        title: {
            text: '2nd Policy: Marginalized Areas Allocation',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: KSh {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: chartData.marginalizedAllocationLabels
        },
        series: [
            {
                name: 'Marginalized Areas Allocation',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['60%', '50%'],
                data: chartData.marginalizedAllocationLabels.map((label, index) => ({
                    value: chartData.marginalizedAllocationData[index],
                    name: label
                })),
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    marginalizedAllocationChart.setOption(marginalizedAllocationOption);
    
    // Project Budget Range Distribution for 2nd Policy (Bar Chart)
    const projectBudgetRangeChart = echarts.init(document.getElementById('projectBudgetRangeChart'));
    const projectBudgetRangeOption = {
        title: {
            text: '2nd Policy: Project Budget Range Distribution',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                return params[0].name + '<br/>' + 
                       'Project Count: ' + 
                       params[0].value;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: Object.keys(chartData.projectBudgetRanges),
            axisLabel: {
                rotate: 0
            }
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: 'Project Count',
                type: 'bar',
                data: Object.values(chartData.projectBudgetRanges),
                itemStyle: {
                    color: colors[7]
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    projectBudgetRangeChart.setOption(projectBudgetRangeOption);
    
    // County Allocation Percentage for 2nd Policy (Pie Chart)
    const countyAllocationPercentageChart = echarts.init(document.getElementById('countyAllocationPercentageChart'));
    const countyAllocationPercentageOption = {
        title: {
            text: '2nd Policy: County Allocation Percentage',
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'bold'
            }
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c}% ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: chartData.secondPolicyCountyLabels
        },
        series: [
            {
                name: 'County Allocation Percentage',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['60%', '50%'],
                data: chartData.secondPolicyCountyLabels.map((label, index) => ({
                    value: chartData.countyAllocationPercentage[index],
                    name: label
                })),
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ],
        toolbox: {
            feature: {
                saveAsImage: {
                    title: 'Download'
                }
            }
        }
    };
    countyAllocationPercentageChart.setOption(countyAllocationPercentageOption);
    
    // Resize charts when window is resized
    window.addEventListener('resize', function() {
        firstPolicySectorChart.resize();
        secondPolicySectorChart.resize();
        firstPolicyYearChart.resize();
        secondPolicyYearChart.resize();
        topCountiesChart.resize();
        absorptionRateChart.resize();
        completionStatusChart.resize();
        projectYearChart.resize();
        allocationVsDisbursementChart.resize();
        cumulativeAllocationChart.resize();
        countyBubbleChart.resize();
        sectorComparisonChart.resize();
        disbursementEfficiencyChart.resize();
        policyPerformanceRadar.resize();
        countyDonutChart.resize();
        countyBarChart.resize();
        countyWaterfallChart.resize();
        budgetUtilizationBarChart.resize();
        sectorGrowthTrend.resize();
        sectorByYearChart.resize();
        countyDisbursementScatterChart.resize();
        budgetUtilizationHeatmapChart.resize();
        firstPolicyDisbursementTrendChart.resize();
        countyProjectCountChart.resize();
        wardAllocationChart.resize();
        sectorBudgetAvgChart.resize();
        sectorProjectCountChart.resize();
        constituencyAllocationChart.resize();
        marginalizedAllocationChart.resize();
        projectBudgetRangeChart.resize();
        countyAllocationPercentageChart.resize();
    });
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', initCharts);
JS;

// Map View JavaScript
$mapJs = <<<'MAPJS'
// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Leaflet to load
    setTimeout(function() {
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded!');
            var mapDiv = document.getElementById('dashboardMap');
            if (mapDiv) {
                mapDiv.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Map library failed to load. Please refresh the page.</div>';
            }
            return;
        }
        
        try {
            // Remove loading indicator
            var mapDiv = document.getElementById('dashboardMap');
            if (!mapDiv) return;
            
            mapDiv.innerHTML = '';
            
            // Initialize map centered on Kenya
            var map = L.map('dashboardMap').setView([-0.0236, 37.9062], 6);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Projects data from PHP
            var projectsData = {$mapProjectsDataJson};
            
            // Store markers - use object for easy lookup by project ID
            var markers = {};
            var markersArray = [];
            
            // Function to highlight project in list
            function highlightProjectInList(projectId) {
                // Remove active class from all items
                document.querySelectorAll('#dashboard-projects-list .project-item').forEach(function(item) {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                var projectItem = document.querySelector('#dashboard-projects-list .project-item[data-project-id="' + projectId + '"]');
                if (projectItem) {
                    projectItem.classList.add('active');
                    projectItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            
            // Create markers for each project
            if (projectsData && projectsData.length > 0) {
                projectsData.forEach(function(project) {
                    if (project.latitude && project.longitude) {
                        var marker = L.marker([project.latitude, project.longitude]).addTo(map);
                        
                        // Create popup content
                        var popupContent = `
                            <div style="min-width: 200px;">
                                <h4 style="margin: 0 0 10px 0; color: #008a8a; font-weight: 600;">${project.name}</h4>
                                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>County:</strong> ${project.county}</p>
                                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Constituency:</strong> ${project.constituency}</p>
                                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Ward:</strong> ${project.ward}</p>
                                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Area:</strong> ${project.marginalised_area}</p>
                                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Sector:</strong> ${project.sector}</p>
                                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Budget:</strong> KES ${project.budget}</p>
                                <a href="/ef/eq-two-projects/view?id=${project.id}" style="display: inline-block; margin-top: 10px; padding: 5px 10px; background: #008a8a; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85rem;">View Details</a>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent, {
                            closeOnClick: false,
                            autoClose: false,
                            closeOnEscapeKey: true
                        });
                        
                        marker.projectId = project.id;
                        
                        // Show popup on hover and highlight in list
                        marker.on('mouseover', function(e) {
                            this.openPopup();
                            highlightProjectInList(project.id);
                        });
                        
                        // Keep popup open when hovering over it
                        marker.on('popupopen', function(e) {
                            var popup = e.popup;
                            popup.getElement().addEventListener('mouseenter', function() {
                                clearTimeout(marker._closeTimeout);
                            });
                            popup.getElement().addEventListener('mouseleave', function() {
                                marker._closeTimeout = setTimeout(function() {
                                    marker.closePopup();
                                }, 200);
                            });
                        });
                        
                        // Close popup when mouse leaves marker
                        marker.on('mouseout', function(e) {
                            var self = this;
                            marker._closeTimeout = setTimeout(function() {
                                self.closePopup();
                            }, 200);
                        });
                        
                        // Add click event to highlight in list and keep popup open
                        marker.on('click', function() {
                            highlightProjectInList(project.id);
                            this.openPopup();
                        });
                        
                        markers[project.id] = marker;
                        markersArray.push(marker);
                    }
                });
                
                // Fit map to show all markers
                if (markersArray.length > 0) {
                    var group = new L.featureGroup(markersArray);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
                
                // Click on project item to focus map on that marker
                document.querySelectorAll('#dashboard-projects-list .project-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        var lat = parseFloat(this.getAttribute('data-lat'));
                        var lng = parseFloat(this.getAttribute('data-lng'));
                        var projectId = this.getAttribute('data-project-id');
                        
                        // Find and open marker popup
                        if (markers[projectId]) {
                            map.setView([lat, lng], 12);
                            markers[projectId].openPopup();
                            highlightProjectInList(projectId);
                        }
                    });
                });
            } else {
                mapDiv.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-light);"><i class="fas fa-map-marked-alt"></i><p>No projects with coordinates found.</p></div>';
            }
        } catch (error) {
            console.error('Error initializing map:', error);
            var mapDiv = document.getElementById('dashboardMap');
            if (mapDiv) {
                mapDiv.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Error loading map: ' + error.message + '</div>';
            }
        }
    }, 500); // Wait 500ms for Leaflet to load
});
MAPJS;

// Prepare map projects data as JSON
$mapProjectsDataJson = Json::encode($mapProjectsData ?? []);

// Replace placeholder in JavaScript
$mapJs = str_replace('{$mapProjectsDataJson}', $mapProjectsDataJson, $mapJs);

$this->registerJs($mapJs, View::POS_END);
$this->registerJs($js, View::POS_END);
?>

<style>
:root {
    --primary: #008a8a;
    --primary-dark: #006666;
    --primary-light: #e0f7f7;
    --secondary: #43cea2;
    --secondary-dark: #185a9d;
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

.dashboard-wrapper {
    background: var(--card-bg);
    padding: 35px;
    border-radius: 16px;
    max-width: 1400px;
    margin: 25px auto;
    box-shadow: var(--shadow);
}

.dashboard-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
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

.policies-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

.policy-card {
    background: linear-gradient(135deg, #ffffff, #f5f5f5);
    padding: 35px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: 2px solid var(--border);
}

.policy-card:first-child {
    border-left: 6px solid #4CAF50;
}

.policy-card:last-child {
    border-left: 6px solid #2196F3;
}

.policy-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(0, 105, 92, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.policy-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.policy-card h3 {
    margin: 0 0 30px 0;
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--text);
    text-transform: uppercase;
    letter-spacing: 1px;
    padding-bottom: 20px;
    border-bottom: 3px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
}

.policy-card:first-child h3 {
    color: #4CAF50;
    border-bottom-color: #4CAF50;
}

.policy-card:last-child h3 {
    color: #2196F3;
    border-bottom-color: #2196F3;
}

.policy-card h3::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.policy-card:first-child h3::before {
    background: #4CAF50;
}

.policy-card:last-child h3::before {
    background: #2196F3;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: white;
    padding: 25px 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
    transition: var(--transition);
    border-top: 4px solid var(--primary);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(0, 138, 138, 0.05) 0%, transparent 70%);
    border-radius: 50%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    border-top-width: 5px;
}

.stat-card .icon {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 15px;
    display: block;
}

.stat-card h4 {
    margin: 0 0 15px 0;
    font-size: 0.85rem;
    text-transform: uppercase;
    font-weight: 600;
    color: var(--text-light);
    letter-spacing: 1px;
    position: relative;
    z-index: 1;
}

.stat-card .value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-dark);
    margin: 15px 0;
    position: relative;
    z-index: 1;
    line-height: 1.2;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

.stat-card .subtext {
    font-size: 0.8rem;
    color: var(--text-light);
    margin-top: 10px;
    font-weight: 500;
    position: relative;
    z-index: 1;
}

.stat-card .progress-bar-container {
    margin-top: 15px;
    height: 6px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

.stat-card .progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    border-radius: 10px;
    transition: width 0.6s ease;
}

.stat-card.counties {
    border-top-color: #4CAF50;
}

.stat-card.counties .icon {
    color: #4CAF50;
}

.stat-card.allocation {
    border-top-color: #2196F3;
}

.stat-card.allocation .icon {
    color: #2196F3;
}

.stat-card.disbursement {
    border-top-color: #FF9800;
}

.stat-card.disbursement .icon {
    color: #FF9800;
}

.comparison-section {
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    padding: 40px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    margin-top: 40px;
    border: 2px solid var(--border);
}

.comparison-section h3 {
    margin: 0 0 30px 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text);
    text-align: center;
    border-bottom: 4px solid var(--accent);
    padding-bottom: 20px;
    position: relative;
}

.comparison-section h3::after {
    content: '📊';
    margin-left: 15px;
    font-size: 1.5rem;
}

.comparison-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.comparison-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    border: 2px solid var(--border);
    transition: var(--transition);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.comparison-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
}

.comparison-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.comparison-card h4 {
    margin: 0 0 20px 0;
    font-size: 1.1rem;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--text);
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.comparison-card h4 i {
    color: var(--primary);
    font-size: 1.2rem;
}

.comparison-item {
    margin: 20px 0;
    padding: 20px;
    background: linear-gradient(135deg, #fafafa, #ffffff);
    border-radius: 10px;
    border: 1px solid var(--border);
    position: relative;
}

.comparison-item .label {
    font-size: 0.85rem;
    color: var(--text-light);
    font-weight: 600;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.comparison-item .policy-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}

.comparison-item .policy-row:last-of-type {
    border-bottom: none;
}

.comparison-item .first-value {
    font-size: 0.95rem;
    font-weight: 700;
    color: #4CAF50;
    margin: 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.comparison-item .second-value {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2196F3;
    margin: 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.comparison-item .total {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--primary-dark);
    margin-top: 20px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    padding-top: 20px;
    border-top: 3px solid var(--primary);
    background: linear-gradient(135deg, rgba(0, 138, 138, 0.05), rgba(0, 138, 138, 0.02));
    padding: 15px;
    border-radius: 8px;
}

/* Charts Section */
.charts-section {
    margin-top: 40px;
}

.charts-section h3 {
    margin: 0 0 30px 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text);
    text-align: center;
    border-bottom: 4px solid var(--accent);
    padding-bottom: 20px;
    position: relative;
}

.charts-section h3::after {
    content: '📈';
    margin-left: 15px;
    font-size: 1.5rem;
}

.chart-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: var(--shadow);
    transition: var(--transition);
    border: 1px solid var(--border);
    position: relative;
    margin-bottom: 30px;
}

.chart-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.chart-container {
    position: relative;
    height: 400px;
    margin-top: 20px;
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 20px;
    text-align: center;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--border);
}

.chart-description {
    font-size: 0.9rem;
    color: var(--text-light);
    margin-bottom: 20px;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .comparison-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-wrapper {
        padding: 20px;
    }
    
    .dashboard-header {
        padding: 20px;
        font-size: 1.8rem;
    }
    
    .policy-card h3 {
        font-size: 1.5rem;
    }
    
    .chart-container {
        height: 300px;
    }
    
    .map-view-container .row {
        flex-direction: column;
    }
    
    .map-view-container .col-md-8,
    .map-view-container .col-md-4 {
        padding: 0 !important;
        margin-bottom: 20px;
    }
    
    .map-container {
        height: 400px !important;
    }
    
    .project-info-panel {
        max-height: 400px !important;
    }
}

/* Project List Panel Styles */
.project-info-panel {
    background: #fff;
    border-radius: 12px;
    padding: 0;
    box-shadow: var(--shadow);
    max-height: 600px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.projects-list-container::-webkit-scrollbar {
    width: 6px;
}

.projects-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.projects-list-container::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.projects-list-container::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}

.project-item:hover {
    border-color: var(--primary) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 138, 138, 0.15) !important;
}

.project-item:hover > div:first-child {
    width: 6px !important;
}

.project-item.active {
    background: linear-gradient(135deg, var(--primary-light), #f0f9f9) !important;
    border-color: var(--primary) !important;
    box-shadow: 0 4px 15px rgba(0, 138, 138, 0.25) !important;
}

.project-item.active > div:first-child {
    width: 6px !important;
    background: var(--primary-dark) !important;
}

.project-item.active .project-item-title {
    color: var(--primary-dark) !important;
}

.project-item.active .project-item-icon {
    background: var(--primary) !important;
    color: #fff !important;
    transform: scale(1.1);
}

.project-item.active .project-detail-item {
    color: var(--text-dark) !important;
}

.project-item.active .project-detail-item i {
    color: var(--primary-dark) !important;
}

.project-item.active .project-budget {
    border-top-color: var(--primary) !important;
}

.project-item.active .budget-label {
    color: var(--text-dark) !important;
}

.project-item.active .budget-value {
    color: var(--primary-dark) !important;
}
</style>

<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <?= Html::encode($this->title) ?>
    </div>

    <!-- Policies Comparison -->
    <div class="policies-grid">
        <!-- 1st Marginalization Policy -->
        <div class="policy-card">
            <h3>1st Marginalization Policy</h3>
            <div class="stats-grid">
                <div class="stat-card counties">
                    <i class="fas fa-map-marked-alt icon"></i>
                    <h4>Total Counties</h4>
                    <div class="value"><?= number_format($firstPolicyTotalCounties) ?></div>
                    <div class="subtext">Counties covered</div>
                </div>
                <div class="stat-card allocation">
                    <i class="fas fa-money-bill-wave icon"></i>
                    <h4>Total Allocation</h4>
                    <div class="value">KES <?= number_format($firstPolicyTotalAllocation, 2) ?></div>
                    <div class="subtext">Budget allocated</div>
                </div>
                <div class="stat-card disbursement">
                    <i class="fas fa-hand-holding-usd icon"></i>
                    <h4>Total Disbursement</h4>
                    <div class="value">KES <?= number_format($firstPolicyTotalDisbursement, 2) ?></div>
                    <div class="subtext">Amount disbursed (<?= $firstPolicyDisbursementPercent ?>%)</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= min($firstPolicyDisbursementPercent, 100) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2nd Marginalization Policy -->
        <div class="policy-card">
            <h3>2nd Marginalization Policy</h3>
            <div class="stats-grid">
                <div class="stat-card counties">
                    <i class="fas fa-map-marked-alt icon"></i>
                    <h4>Total Counties</h4>
                    <div class="value"><?= number_format($secondPolicyTotalCounties) ?></div>
                    <div class="subtext">Counties covered</div>
                </div>
                <div class="stat-card allocation">
                    <i class="fas fa-money-bill-wave icon"></i>
                    <h4>Total Allocation</h4>
                    <div class="value">KES <?= number_format($secondPolicyTotalAllocation, 2) ?></div>
                    <div class="subtext">Budget allocated</div>
                </div>
                <div class="stat-card disbursement">
                    <i class="fas fa-hand-holding-usd icon"></i>
                    <h4>Total Disbursement</h4>
                    <div class="value">KES <?= number_format($secondPolicyTotalDisbursement, 2) ?></div>
                    <div class="subtext">Amount disbursed (<?= $secondPolicyDisbursementPercent ?>%)</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= min($secondPolicyDisbursementPercent, 100) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Section -->
    <div class="comparison-section">
        <h3>Policy Comparison Summary</h3>
        <div class="comparison-grid">
            <div class="comparison-card">
                <h4><i class="fas fa-map-marked-alt"></i> Total Counties</h4>
                <div class="comparison-item">
                    <div class="policy-row">
                        <span class="label">1st Policy</span>
                        <span class="first-value"><?= number_format($firstPolicyTotalCounties) ?></span>
                    </div>
                    <div class="policy-row">
                        <span class="label">2nd Policy</span>
                        <span class="second-value"><?= number_format($secondPolicyTotalCounties) ?></span>
                    </div>
                    <div class="total">Total: <?= number_format($firstPolicyTotalCounties + $secondPolicyTotalCounties) ?> Counties</div>
                </div>
            </div>
            <div class="comparison-card">
                <h4><i class="fas fa-money-bill-wave"></i> Total Allocation</h4>
                <div class="comparison-item">
                    <div class="policy-row">
                        <span class="label">1st Policy</span>
                        <span class="first-value">KES <?= number_format($firstPolicyTotalAllocation, 2) ?></span>
                    </div>
                    <div class="policy-row">
                        <span class="label">2nd Policy</span>
                        <span class="second-value">KES <?= number_format($secondPolicyTotalAllocation, 2) ?></span>
                    </div>
                    <div class="total">Total: KES <?= number_format($firstPolicyTotalAllocation + $secondPolicyTotalAllocation, 2) ?></div>
                </div>
            </div>
            <div class="comparison-card">
                <h4><i class="fas fa-hand-holding-usd"></i> Total Disbursement</h4>
                <div class="comparison-item">
                    <div class="policy-row">
                        <span class="label">1st Policy</span>
                        <span class="first-value">KES <?= number_format($firstPolicyTotalDisbursement, 2) ?></span>
                    </div>
                    <div class="policy-row">
                        <span class="label">2nd Policy</span>
                        <span class="second-value">KES <?= number_format($secondPolicyTotalDisbursement, 2) ?></span>
                    </div>
                    <div class="total">Total: KES <?= number_format($firstPolicyTotalDisbursement + $secondPolicyTotalDisbursement, 2) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Map View Section -->
    <div class="comparison-section" style="margin-top: 40px;">
        <h3><i class="fas fa-map-marked-alt"></i> Projects Map View</h3>
        <div class="map-view-container" style="margin-top: 30px;">
            <!-- Stats Bar -->
            <div class="stats-bar" style="background: var(--primary-light); padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 15px;">
                <div class="stat-item" style="text-align: center;">
                    <div class="stat-label" style="font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">Total Projects</div>
                    <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?= count($mapProjects ?? []) ?></div>
                </div>
                <div class="stat-item" style="text-align: center;">
                    <div class="stat-label" style="font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">With Coordinates</div>
                    <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);"><?= count($mapProjects ?? []) ?></div>
                </div>
                <div class="stat-item" style="text-align: center;">
                    <div class="stat-label" style="font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">Total Budget</div>
                    <div class="stat-value" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">KES <?= number_format(array_sum(array_map(function($p) { return $p->project_budget ?: 0; }, $mapProjects ?? [])), 2) ?></div>
                </div>
            </div>

            <!-- Map and Project List -->
            <div class="row" style="margin: 0;">
                <div class="col-md-8" style="padding: 0 10px 0 0;">
                    <div class="map-container" style="width: 100%; height: 600px; border-radius: 12px; overflow: hidden; margin-bottom: 20px; border: 3px solid var(--primary); box-shadow: var(--shadow); position: relative; background: #e0e0e0;">
                        <div id="dashboardMap" style="width: 100%; height: 100%; min-height: 600px;">
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-light);">
                                <i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i> Loading map...
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" style="padding: 0 0 0 10px;">
                    <div class="project-info-panel" style="background: #fff; border-radius: 12px; padding: 0; box-shadow: var(--shadow); max-height: 600px; overflow-y: auto; display: flex; flex-direction: column;">
                        <div class="project-info-panel-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; padding: 20px; border-radius: 12px 12px 0 0; position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <h3 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-map-pin"></i> Projects on Map
                                <span class="project-count" style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; margin-left: auto;"><?= count($mapProjects ?? []) ?> Projects</span>
                            </h3>
                        </div>
                        <div class="projects-list-container" id="dashboard-projects-list" style="padding: 15px; flex: 1; overflow-y: auto;">
                            <?php if (empty($mapProjects)): ?>
                                <div class="empty-state" style="text-align: center; padding: 40px 20px; color: var(--text-light);">
                                    <i class="fas fa-map-marked-alt" style="font-size: 3rem; color: var(--primary-light); margin-bottom: 15px;"></i>
                                    <p style="margin: 0; font-size: 0.95rem;">No projects with coordinates found.<br>Please add coordinates to projects first.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($mapProjects as $index => $project): ?>
                                    <div class="project-item" data-project-id="<?= $project->id ?>" data-lat="<?= $project->latitude ?>" data-lng="<?= $project->longitude ?>" style="background: #fff; border: 2px solid #e0e0e0; border-radius: 10px; padding: 15px; margin-bottom: 12px; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--primary); transition: width 0.3s ease;"></div>
                                        <div class="project-item-header" style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px;">
                                            <h4 class="project-item-title" style="font-size: 1rem; font-weight: 700; color: var(--text-dark); margin: 0; line-height: 1.4; flex: 1; padding-right: 10px;">
                                                <?= Html::encode($project->project_description ?: 'Unnamed Project') ?>
                                            </h4>
                                            <div class="project-item-icon" style="background: var(--primary-light); color: var(--primary); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; transition: all 0.3s ease;">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                        </div>
                                        <div class="project-item-details" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px;">
                                            <div class="project-detail-item" style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: var(--text-light);">
                                                <i class="fas fa-building" style="color: var(--primary); font-size: 0.75rem; width: 16px;"></i>
                                                <span><?= Html::encode($project->county ?: 'N/A') ?></span>
                                            </div>
                                            <div class="project-detail-item" style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: var(--text-light);">
                                                <i class="fas fa-landmark" style="color: var(--primary); font-size: 0.75rem; width: 16px;"></i>
                                                <span><?= Html::encode($project->constituency ?: 'N/A') ?></span>
                                            </div>
                                            <div class="project-detail-item" style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: var(--text-light);">
                                                <i class="fas fa-map" style="color: var(--primary); font-size: 0.75rem; width: 16px;"></i>
                                                <span><?= Html::encode($project->ward ?: 'N/A') ?></span>
                                            </div>
                                            <div class="project-detail-item" style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: var(--text-light);">
                                                <i class="fas fa-tag" style="color: var(--primary); font-size: 0.75rem; width: 16px;"></i>
                                                <span><?= Html::encode($project->sector ?: 'N/A') ?></span>
                                            </div>
                                            <div class="project-budget" style="grid-column: 1 / -1; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: space-between;">
                                                <span class="budget-label" style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px;">Budget</span>
                                                <span class="budget-value" style="font-size: 1rem; font-weight: 700; color: var(--primary);">KES <?= number_format($project->project_budget ?: 0, 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <h3>Data Analytics & Visualizations</h3>
        
        <!-- 1st Policy Sector Distribution Pie Chart -->
        <div class="chart-card">
            <div class="chart-title">1st Policy: Sector Distribution</div>
            <div class="chart-description">Breakdown of funding by sector for the 1st Marginalization Policy</div>
            <div id="firstPolicySectorChart" class="chart-container"></div>
        </div>

        <!-- 2nd Policy Sector Distribution Pie Chart -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Sector Distribution</div>
            <div class="chart-description">Breakdown of funding by sector for the 2nd Marginalization Policy</div>
            <div id="secondPolicySectorChart" class="chart-container"></div>
        </div>

        <!-- 1st Policy Yearly Allocation Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">1st Policy: Yearly Allocation Trend</div>
            <div class="chart-description">Allocation amounts by financial year for the 1st Policy</div>
            <div id="firstPolicyYearChart" class="chart-container"></div>
        </div>

        <!-- 2nd Policy Yearly Allocation Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Yearly Allocation Trend</div>
            <div class="chart-description">Allocation amounts by financial year for the 2nd Policy</div>
            <div id="secondPolicyYearChart" class="chart-container"></div>
        </div>

        <!-- Top 10 Counties by Allocation Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">Top 10 Counties by Allocation</div>
            <div class="chart-description">Comparison of allocation amounts between policies for the top 10 counties</div>
            <div id="topCountiesChart" class="chart-container"></div>
        </div>

        <!-- Absorption Rate by County Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">Absorption Rate by County</div>
            <div class="chart-description">Disbursement efficiency as percentage of allocation by county</div>
            <div id="absorptionRateChart" class="chart-container"></div>
        </div>

        <!-- Project Completion Status Pie Chart -->
        <div class="chart-card">
            <div class="chart-title">1st Policy: Project Completion Status</div>
            <div class="chart-description">Status of projects under the 1st Marginalization Policy</div>
            <div id="completionStatusChart" class="chart-container"></div>
        </div>

        <!-- Project Distribution by Year Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Projects by Financial Year</div>
            <div class="chart-description">Number of projects implemented each financial year</div>
            <div id="projectYearChart" class="chart-container"></div>
        </div>

        <!-- Allocation vs Disbursement Trend Line Chart -->
        <div class="chart-card">
            <div class="chart-title">Allocation vs Disbursement Trend</div>
            <div class="chart-description">Comparison of allocation and disbursement trends over time</div>
            <div id="allocationVsDisbursementChart" class="chart-container"></div>
        </div>

        <!-- Cumulative Allocation Area Chart -->
        <div class="chart-card">
            <div class="chart-title">Cumulative Allocation Comparison</div>
            <div class="chart-description">Cumulative allocation comparison between both policies</div>
            <div id="cumulativeAllocationChart" class="chart-container"></div>
        </div>

        <!-- County Allocation vs Disbursement Bubble Chart -->
        <div class="chart-card">
            <div class="chart-title">County Allocation vs Disbursement</div>
            <div class="chart-description">Bubble chart showing relationship between allocation, disbursement, and absorption rate</div>
            <div id="countyBubbleChart" class="chart-container"></div>
        </div>

        <!-- Sector Comparison Stacked Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">Sector Comparison Between Policies</div>
            <div class="chart-description">Stacked bar chart comparing sector allocations between both policies</div>
            <div id="sectorComparisonChart" class="chart-container"></div>
        </div>

        <!-- Disbursement Efficiency Scatter Plot -->
        <div class="chart-card">
            <div class="chart-title">Disbursement Efficiency Analysis</div>
            <div class="chart-description">Scatter plot showing relationship between allocation and disbursement efficiency</div>
            <div id="disbursementEfficiencyChart" class="chart-container"></div>
        </div>

        <!-- Policy Performance Radar Chart -->
        <div class="chart-card">
            <div class="chart-title">Policy Performance Radar</div>
            <div class="chart-description">Radar chart comparing performance metrics between policies</div>
            <div id="policyPerformanceRadar" class="chart-container"></div>
        </div>

   
                    <div class="chart-card">
             
                             <div class="chart-title">County Allocation Distribution</div>
            <div class="chart-description">Donut chart showing allocation distribution across counties</div>
            <div id="countyDonutChart" class="chart-container"></div>
        </div>

        <!-- County Allocation Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">County Allocation Comparison</div>
            <div class="chart-description">Bar chart comparing allocation amounts between policies for each county</div>
            <div id="countyBarChart" class="chart-container"></div>
        </div>

        <!-- County Allocation Waterfall Chart -->
        <div class="chart-card">
            <div class="chart-title">County Allocation Waterfall</div>
            <div class="chart-description">Waterfall chart showing cumulative allocation across counties</div>
            <div id="countyWaterfallChart" class="chart-container"></div>
        </div>

        <!-- Budget Utilization Bar Chart -->
        <div class="chart-card">
            <div class="chart-title">Budget Utilization Comparison</div>
            <div class="chart-description">Comparison of budget utilization between the two policies</div>
            <div id="budgetUtilizationBarChart" class="chart-container"></div>
        </div>

        <!-- Sector Growth Trend Chart -->
        <div class="chart-card">
            <div class="chart-title">Sector Growth Trend</div>
            <div class="chart-description">Line chart showing growth trends for each sector over time</div>
            <div id="sectorGrowthTrend" class="chart-container"></div>
        </div>

        <!-- Sector Distribution by Year for 2nd Policy (Stacked Area Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Sector Distribution by Year</div>
            <div class="chart-description">Stacked area chart showing sector distribution over time for the 2nd Policy</div>
            <div id="sectorByYearChart" class="chart-container"></div>
        </div>

        <!-- County-wise Disbursement vs Allocation for 2nd Policy (Scatter Plot) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: County Disbursement vs Allocation</div>
            <div class="chart-description">Scatter plot showing relationship between allocation and disbursement by county</div>
            <div id="countyDisbursementScatterChart" class="chart-container"></div>
        </div>

        <!-- Budget Utilization by County for 2nd Policy (Heatmap) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Budget Utilization by County</div>
            <div class="chart-description">Heatmap showing budget utilization rates by county</div>
            <div id="budgetUtilizationHeatmapChart" class="chart-container"></div>
        </div>

        <!-- Yearly Disbursement Trend for 1st Policy (Line Chart) -->
        <div class="chart-card">
            <div class="chart-title">1st Policy: Yearly Disbursement Trend</div>
            <div class="chart-description">Line chart showing disbursement trends over time for the 1st Policy</div>
            <div id="firstPolicyDisbursementTrendChart" class="chart-container"></div>
        </div>

        <!-- County-wise Project Count for 2nd Policy (Bar Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: County-wise Project Count</div>
            <div class="chart-description">Number of projects implemented in each county</div>
            <div id="countyProjectCountChart" class="chart-container"></div>
        </div>

        <!-- Ward-level Allocation for 2nd Policy (TreeMap) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Ward-level Allocation</div>
            <div class="chart-description">TreeMap showing allocation distribution by ward</div>
            <div id="wardAllocationChart" class="chart-container"></div>
        </div>

        <!-- Project Budget Distribution by Sector for 2nd Policy (Bar Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Average Project Budget by Sector</div>
            <div class="chart-description">Average budget per project by sector</div>
            <div id="sectorBudgetAvgChart" class="chart-container"></div>
        </div>

        <!-- Sector Project Count for 2nd Policy (Bar Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Sector Project Count</div>
            <div class="chart-description">Number of projects by sector</div>
            <div id="sectorProjectCountChart" class="chart-container"></div>
        </div>

        <!-- Constituency-wise Allocation for 2nd Policy (Bar Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Constituency-wise Allocation</div>
            <div class="chart-description">Allocation amounts by constituency</div>
            <div id="constituencyAllocationChart" class="chart-container"></div>
        </div>

        <!-- Marginalized Areas Allocation for 2nd Policy (Pie Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Marginalized Areas Allocation</div>
            <div class="chart-description">Distribution of funds across marginalized areas</div>
            <div id="marginalizedAllocationChart" class="chart-container"></div>
        </div>

        <!-- Project Budget Range Distribution for 2nd Policy (Bar Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: Project Budget Range Distribution</div>
            <div class="chart-description">Distribution of projects by budget range</div>
            <div id="projectBudgetRangeChart" class="chart-container"></div>
        </div>

        <!-- County Allocation Percentage for 2nd Policy (Pie Chart) -->
        <div class="chart-card">
            <div class="chart-title">2nd Policy: County Allocation Percentage</div>
            <div class="chart-description">Percentage share of allocation by county</div>
            <div id="countyAllocationPercentageChart" class="chart-container"></div>
        </div>
    </div>
</div>
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\ef\models\EqualizationTwoProjects;
use app\modules\ef\models\EqualizationTwoAppropriation;

/* @var $this yii\web\View */
/* @var $model EqualizationTwoProjects */

 $this->title = 'Equalization Fund Reports';
 $this->params['breadcrumbs'][] = ['label' => 'Equalization Fund', 'url' => ['/ef']];
 $this->params['breadcrumbs'][] = $this->title;

// Register Font Awesome for icons
 $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
// Register Poppins font
 $this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
?>

<div class="reports-index">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(45deg, #008a8a, #007373);">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="card-title text-white mb-0"><?= Html::encode($this->title) ?></h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#helpModal">
                                    <i class="fas fa-question-circle"></i> Help
                                </button>
                                <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Alert Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <h5><i class="fas fa-info-circle mr-2"></i> Report Generation</h5>
                                <p>Select a report category below to view available reports. All reports are generated in PDF format and can be downloaded or printed.</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Stats Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #008a8a; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title"><?= number_format($totalProjects) ?></h4>
                                            <p class="card-text">Total Projects</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-project-diagram fa-3x"></i>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #007373; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title"><?= number_format($totalAppropriations) ?></h4>
                                            <p class="card-text">Total Appropriations</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-coins fa-3x"></i>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #008a8a; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title"><?= number_format($countiesCount) ?></h4>
                                            <p class="card-text">Counties Covered</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-map-marker-alt fa-3x"></i>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #007373; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title"><?= number_format($sectorsCount) ?></h4>
                                            <p class="card-text">Sectors</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-industry fa-3x"></i>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 60%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Budget Summary Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 text-center">
                                            <h5 class="text-muted">Total Project Budget</h5>
                                            <h3 class="text-primary" style="color: #008a8a !important;">KES <?= number_format($totalProjectBudget, 2) ?></h3>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5 class="text-muted">Total Allocation</h5>
                                            <h3 class="text-success" style="color: #007373 !important;">KES <?= number_format($totalAllocation, 2) ?></h3>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5 class="text-muted">Variance</h5>
                                            <h3 class="<?= ($totalAllocation - $totalProjectBudget) >= 0 ? 'text-success' : 'text-danger' ?>">
                                                KES <?= number_format($totalAllocation - $totalProjectBudget, 2) ?>
                                            </h3>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5 class="text-muted">Utilization Rate</h5>
                                            <h3 class="text-info" style="color: #008a8a !important;">
                                                <?= $totalAllocation > 0 ? round(($totalProjectBudget / $totalAllocation) * 100, 1) : 0 ?>%
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabs for Report Categories -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="reportTabs" role="tablist" style="border-bottom: 2px solid #008a8a;">
                                <li class="nav-item">
                                    <a class="nav-link active" id="projects-tab" data-toggle="tab" href="#projects" role="tab" aria-controls="projects" aria-selected="true" style="color: #008a8a; border-color: transparent;">
                                        <i class="fas fa-project-diagram mr-2"></i> Projects Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="appropriations-tab" data-toggle="tab" href="#appropriations" role="tab" aria-controls="appropriations" aria-selected="false" style="color: #008a8a; border-color: transparent;">
                                        <i class="fas fa-coins mr-2"></i> Appropriations Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="combined-tab" data-toggle="tab" href="#combined" role="tab" aria-controls="combined" aria-selected="false" style="color: #008a8a; border-color: transparent;">
                                        <i class="fas fa-chart-line mr-2"></i> Combined Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tab" data-toggle="tab" href="#custom" role="tab" aria-controls="custom" aria-selected="false" style="color: #008a8a; border-color: transparent;">
                                        <i class="fas fa-filter mr-2"></i> Custom Reports
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="reportTabsContent">
                                <!-- Projects Reports Tab -->
                                <div class="tab-pane fade show active" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-map-marked-alt mr-2"></i> County Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Summary of projects grouped by county with total budgets and project counts.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'county', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-industry mr-2"></i> Sector Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Summary of projects grouped by sector with total budgets and project counts.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'sector', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-hands-helping mr-2"></i> Marginalised Areas</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Summary of projects in marginalized areas with total budgets and project counts.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'marginalised', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-list-alt mr-2"></i> Detailed Projects</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Comprehensive list of all projects with full details including budgets and locations.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'detailed', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2"></i> Allocation Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Project allocation analysis by county with averages and totals.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'allocation', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i> Financial Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Financial overview of projects with budget analysis and variance calculations.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'financial', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-chart-line mr-2"></i> Trend Analysis</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Historical trend analysis of projects and budgets over financial years.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'trend', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i> Ward Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Detailed report of projects organized by wards within counties.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'ward', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-landmark mr-2"></i> Constituency Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Detailed report of projects organized by constituencies within counties.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'constituency', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Appropriations Reports Tab -->
                                <div class="tab-pane fade" id="appropriations" role="tabpanel" aria-labelledby="appropriations-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #007373; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-map-marked-alt mr-2"></i> County Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Summary of appropriations grouped by county with total allocations.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #007373;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'county', 'model' => 'appropriations'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #007373; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #007373; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2"></i> Disbursement Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Analysis of disbursements by county with averages and totals.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #007373;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'disbursement', 'model' => 'appropriations'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #007373; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #007373; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-industry mr-2"></i> Sector Disbursements</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Sector-wise disbursement analysis across all counties.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #007373;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'sector-disbursements', 'model' => 'appropriations'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #007373; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Combined Reports Tab -->
                                <div class="tab-pane fade" id="combined" role="tabpanel" aria-labelledby="combined-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-tachometer-alt mr-2"></i> Performance Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Comprehensive performance analysis including utilization rates and variance between projects and appropriations.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'performance', 'model' => 'projects'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-balance-scale mr-2"></i> Comparison Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Side-by-side comparison of projects and appropriations with variance analysis.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'comparison', 'model' => 'combined'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #008a8a; color: white;">
                                                    <h5 class="mb-0"><i class="fas fa-file-contract mr-2"></i> Executive Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">High-level overview of the Equalization Fund with key metrics and performance indicators.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge" style="background-color: #008a8a;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['generate', 'type' => 'executive', 'model' => 'combined'], 
                                                            ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Custom Reports Tab -->
                                <div class="tab-pane fade" id="custom" role="tabpanel" aria-labelledby="custom-tab">
                                    <div class="card mt-3 shadow-sm">
                                        <div class="card-header" style="background-color: #007373; color: white;">
                                            <h4 class="mb-0"><i class="fas fa-filter mr-2"></i> Custom Report Generator</h4>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">Generate a custom report by applying filters below. Select one or more criteria to narrow down the results.</p>
                                            
                                            <?php $form = ActiveForm::begin([
                                                'action' => ['custom-report'],
                                                'method' => 'get',
                                                'options' => ['class' => 'form-horizontal']
                                            ]); ?>
                                            
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <?= $form->field($model, 'county')->dropDownList(
                                                        ['' => 'All Counties'] + array_combine($counties, $counties),
                                                        ['prompt' => 'All Counties', 'class' => 'form-control form-control-sm']
                                                    )->label('County') ?>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <?= $form->field($model, 'sector')->dropDownList(
                                                        ['' => 'All Sectors'] + array_combine($sectors, $sectors),
                                                        ['prompt' => 'All Sectors', 'class' => 'form-control form-control-sm']
                                                    )->label('Sector') ?>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <?= $form->field($model, 'financial_year')->dropDownList(
                                                        ['' => 'All Years'] + array_combine($financialYears, $financialYears),
                                                        ['prompt' => 'All Years', 'class' => 'form-control form-control-sm']
                                                    )->label('Financial Year') ?>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <?= $form->field($model, 'marginalised_area')->dropDownList(
                                                        ['' => 'All Areas'] + array_combine($marginalisedAreas, $marginalisedAreas),
                                                        ['prompt' => 'All Areas', 'class' => 'form-control form-control-sm']
                                                    )->label('Marginalised Area') ?>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-12 text-center">
                                                    <?= Html::submitButton('<i class="fas fa-file-pdf mr-2"></i> Generate Custom Report', 
                                                        ['class' => 'btn btn-lg', 'style' => 'background-color: #007373; color: white; border: none;']) ?>
                                                </div>
                                            </div>
                                            
                                            <?php ActiveForm::end(); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card mt-3 shadow-sm">
                                        <div class="card-header" style="background-color: #008a8a; color: white;">
                                            <h4 class="mb-0"><i class="fas fa-history mr-2"></i> Recently Generated Reports</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Report Name</th>
                                                            <th>Type</th>
                                                            <th>Generated Date</th>
                                                            <th>Generated By</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>County Summary Projects</td>
                                                            <td><span class="badge" style="background-color: #008a8a;">Projects</span></td>
                                                            <td><?= date('Y-m-d H:i:s') ?></td>
                                                            <td><?= Yii::$app->user->identity->username ?></td>
                                                            <td>
                                                                <?= Html::a('<i class="fas fa-download"></i>', '#', ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;', 'title' => 'Download']) ?>
                                                                <?= Html::a('<i class="fas fa-eye"></i>', '#', ['class' => 'btn btn-sm', 'style' => 'background-color: #007373; color: white; border: none;', 'title' => 'View']) ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Sector Disbursements</td>
                                                            <td><span class="badge" style="background-color: #007373;">Appropriations</span></td>
                                                            <td><?= date('Y-m-d H:i:s', strtotime('-1 day')) ?></td>
                                                            <td><?= Yii::$app->user->identity->username ?></td>
                                                            <td>
                                                                <?= Html::a('<i class="fas fa-download"></i>', '#', ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;', 'title' => 'Download']) ?>
                                                                <?= Html::a('<i class="fas fa-eye"></i>', '#', ['class' => 'btn btn-sm', 'style' => 'background-color: #007373; color: white; border: none;', 'title' => 'View']) ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Executive Summary</td>
                                                            <td><span class="badge" style="background-color: #008a8a;">Combined</span></td>
                                                            <td><?= date('Y-m-d H:i:s', strtotime('-2 days')) ?></td>
                                                            <td><?= Yii::$app->user->identity->username ?></td>
                                                            <td>
                                                                <?= Html::a('<i class="fas fa-download"></i>', '#', ['class' => 'btn btn-sm', 'style' => 'background-color: #008a8a; color: white; border: none;', 'title' => 'Download']) ?>
                                                                <?= Html::a('<i class="fas fa-eye"></i>', '#', ['class' => 'btn btn-sm', 'style' => 'background-color: #007373; color: white; border: none;', 'title' => 'View']) ?>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #008a8a; color: white;">
                <h5 class="modal-title" id="helpModalLabel"><i class="fas fa-question-circle mr-2"></i> Report Generation Help</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 style="color: #008a8a;">How to Generate Reports</h4>
                <ol>
                    <li>Select a report category from the tabs (Projects, Appropriations, Combined, or Custom).</li>
                    <li>Choose the specific report you want to generate from the available options.</li>
                    <li>Click the "Generate" button to create the report in PDF format.</li>
                    <li>For custom reports, apply filters as needed before generating.</li>
                </ol>
                
                <h4 style="color: #008a8a;">Report Types</h4>
                <ul>
                    <li><strong>Projects Reports:</strong> Focus on project data including budgets, locations, and sectors.</li>
                    <li><strong>Appropriations Reports:</strong> Focus on financial allocations and disbursements.</li>
                    <li><strong>Combined Reports:</strong> Combine project and financial data for comprehensive analysis.</li>
                    <li><strong>Custom Reports:</strong> Create tailored reports based on specific criteria.</li>
                </ul>
                
                <h4 style="color: #008a8a;">Tips</h4>
                <ul>
                    <li>Use the Quick Stats cards to get an overview of the data before generating reports.</li>
                    <li>Check the Budget Summary card for key financial metrics.</li>
                    <li>Access recently generated reports from the Custom Reports tab.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    /* Apply Poppins font globally */
    body, h1, h2, h3, h4, h5, h6, p, span, div, button, input, select, textarea, .card-title, .card-text, .nav-link, .badge, .btn, .form-control {
        font-family: 'Poppins', sans-serif !important;
    }
    
    .stats-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .stats-icon {
        opacity: 0.7;
    }
    
    .report-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .card-header {
        font-weight: 600;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .nav-tabs {
        border-bottom: 2px solid #008a8a;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #008a8a;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #008a8a;
        font-weight: 600;
        border-bottom: 2px solid #008a8a;
        background-color: transparent;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom: 2px solid #008a8a;
        background-color: rgba(0, 138, 138, 0.1);
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .progress {
        background-color: rgba(255,255,255,0.2);
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .modal-body h4 {
        color: #008a8a;
        font-weight: 600;
        margin-top: 1.5rem;
    }
    
    .modal-body h4:first-child {
        margin-top: 0;
    }
    
    .modal-body ol, .modal-body ul {
        padding-left: 20px;
    }
    
    .modal-body li {
        margin-bottom: 0.5rem;
    }
    
    /* Fix for tabs not being clickable */
    .nav-tabs .nav-link {
        cursor: pointer;
    }
    
    /* Ensure tab content is visible */
    .tab-pane {
        padding-top: 20px;
    }
    
    /* Additional Poppins styling enhancements */
    .card-title {
        font-weight: 600;
    }
    
    .card-text {
        font-weight: 400;
    }
    
    .btn {
        font-weight: 500;
    }
    
    .form-control {
        font-weight: 400;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .nav-link {
        font-weight: 500;
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-weight: 600;
    }
    
    .modal-title {
        font-weight: 600;
    }
    
    .table th {
        font-weight: 600;
    }
    
    .alert h5 {
        font-weight: 600;
    }
    
    .alert p {
        font-weight: 400;
    }
</style>

<!-- JavaScript to ensure tabs work properly -->
<?php
 $this->registerJs('
    $(document).ready(function() {
        // Ensure tabs work properly
        $("#reportTabs a").on("click", function(e) {
            e.preventDefault();
            $(this).tab("show");
        });
    });
');
?>
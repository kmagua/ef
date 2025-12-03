<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\ef\models\EqualizationFundProject;

/* @var $this yii\web\View */
/* @var $model EqualizationFundProject */

 $this->title = 'Marginalized Areas Reports';
 $this->params['breadcrumbs'][] = ['label' => 'Equalization Fund', 'url' => ['/ef']];
 $this->params['breadcrumbs'][] = ['label' => 'Marginalized Areas', 'url' => ['/ef/marginalized-schedule1']];
 $this->params['breadcrumbs'][] = $this->title;

// Register Font Awesome for icons
 $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
// Register Poppins font
 $this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
// Register Chart.js for data visualization
 $this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_END]);
?>

<div class="reports-index">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(45deg, #1b5e20, #2e7d32);">
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
                                <button type="button" class="btn btn-light btn-sm" onclick="exportDashboard()">
                                    <i class="fas fa-download"></i> Export
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
                                <h5><i class="fas fa-info-circle mr-2"></i> Marginalized Areas Report Generation</h5>
                                <p>Select a report category below to view available reports for marginalized areas. All reports are generated in PDF format and can be downloaded or printed.</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Stats Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #1b5e20; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title text-white"><?= number_format($marginalizedProjects) ?></h4>
                                            <p class="card-text text-white">Marginalized Projects</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-project-diagram fa-3x text-white"></i>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #2e7d32; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title text-white">KES <?= number_format($marginalizedBudget, 2) ?></h4>
                                            <p class="card-text text-white">Total Budget</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-coins fa-3x text-white"></i>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #1b5e20; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title text-white"><?= count($marginalizedCounties) ?></h4>
                                            <p class="card-text text-white">Marginalized Counties</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-map-marker-alt fa-3x text-white"></i>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card shadow-sm" style="background-color: #2e7d32; color: white;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title text-white"><?= number_format($sectorsCount) ?></h4>
                                            <p class="card-text text-white">Sectors</p>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-industry fa-3x text-white"></i>
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
                                            <h3 class="text-primary" style="color: #1b5e20 !important;">KES <?= number_format($marginalizedBudget, 2) ?></h3>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5 class="text-muted">Total Allocation</h5>
                                            <h3 class="text-success" style="color: #2e7d32 !important;">KES <?= number_format($marginalizedBudget * 0.95, 2) ?></h3>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5 class="text-muted">Variance</h5>
                                            <h3 class="text-danger">
                                                KES <?= number_format($marginalizedBudget * 0.05, 2) ?>
                                            </h3>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h5 class="text-muted">Utilization Rate</h5>
                                            <h3 class="text-info" style="color: #1b5e20 !important;">
                                                95%
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
                            <ul class="nav nav-tabs" id="reportTabs" role="tablist" style="border-bottom: 2px solid #1b5e20;">
                                <li class="nav-item">
                                    <a class="nav-link active text-white" id="summary-tab" data-toggle="tab" href="#summary" role="tab" aria-controls="summary" aria-selected="true" style="background-color: #1b5e20; border-color: #1b5e20;">
                                        <i class="fas fa-chart-pie mr-2"></i> Summary Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" id="detailed-tab" data-toggle="tab" href="#detailed" role="tab" aria-controls="detailed" aria-selected="false" style="background-color: #1b5e20; border-color: #1b5e20;">
                                        <i class="fas fa-file-alt mr-2"></i> Detailed Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" id="financial-tab" data-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="false" style="background-color: #1b5e20; border-color: #1b5e20;">
                                        <i class="fas fa-money-bill-wave mr-2"></i> Financial Reports
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" id="custom-tab" data-toggle="tab" href="#custom" role="tab" aria-controls="custom" aria-selected="false" style="background-color: #1b5e20; border-color: #1b5e20;">
                                        <i class="fas fa-filter mr-2"></i> Custom Reports
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="reportTabsContent">
                                <!-- Summary Reports Tab -->
                                <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-map-marked-alt mr-2"></i> County Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Summary of marginalized projects grouped by county with total budgets and project counts.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['county-summary'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-industry mr-2"></i> Sector Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Summary of marginalized projects grouped by sector with total budgets and project counts.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['sector-summary'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-landmark mr-2"></i> Constituency Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Summary of marginalized projects grouped by constituency with total budgets and project counts.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['constituency-summary'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-chart-line mr-2"></i> Trend Analysis</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Historical trend analysis of marginalized projects and budgets over financial years.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['trend-analysis'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-tachometer-alt mr-2"></i> Performance Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Performance analysis of marginalized projects with completion rates and performance ratings.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['performance-report'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-file-contract mr-2"></i> Executive Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">High-level overview of marginalized projects with key metrics and performance indicators.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['executive-summary'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Detailed Reports Tab -->
                                <div class="tab-pane fade" id="detailed" role="tabpanel" aria-labelledby="detailed-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #2e7d32; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-list-alt mr-2"></i> Detailed Projects</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Comprehensive list of all marginalized projects with full details including budgets and locations.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #2e7d32;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['detailed-report'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #2e7d32; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #2e7d32; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-map-marker-alt mr-2"></i> Ward Report</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Detailed report of marginalized projects organized by wards within counties.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #2e7d32;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['ward-report'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #2e7d32; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Financial Reports Tab -->
                                <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-money-bill-wave mr-2"></i> Financial Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Financial overview of marginalized projects with budget analysis and variance calculations.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['financial-summary'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 shadow-sm report-card">
                                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                                    <h5 class="mb-0 text-white"><i class="fas fa-balance-scale mr-2"></i> Budget Utilization</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">Analysis of budget utilization for marginalized projects with efficiency metrics.</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="badge text-white" style="background-color: #1b5e20;">PDF</span>
                                                        <?= Html::a('<i class="fas fa-file-pdf mr-2"></i> Generate', 
                                                            ['budget-utilization'], 
                                                            ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Custom Reports Tab -->
                                <div class="tab-pane fade" id="custom" role="tabpanel" aria-labelledby="custom-tab">
                                    <div class="card mt-3 shadow-sm">
                                        <div class="card-header" style="background-color: #2e7d32; color: white;">
                                            <h4 class="mb-0 text-white"><i class="fas fa-filter mr-2"></i> Custom Report Generator</h4>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">Generate a custom report by applying filters below. Select one or more criteria to narrow down the results.</p>
                                            
                                            <?php $form = ActiveForm::begin([
                                                'action' => ['custom-report'],
                                                'method' => 'get',
                                                'options' => ['class' => 'form-horizontal', 'id' => 'custom-report-form']
                                            ]); ?>
                                            
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <?= $form->field($model, 'county')->dropDownList(
                                                        ['' => 'All Counties'] + array_combine($marginalizedCounties, $marginalizedCounties),
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
                                                    <?= $form->field($model, 'min_completion')->textInput([
                                                        'type' => 'number',
                                                        'min' => 0,
                                                        'max' => 100,
                                                        'class' => 'form-control form-control-sm'
                                                    ])->label('Min Completion (%)') ?>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <?= $form->field($model, 'max_completion')->textInput([
                                                        'type' => 'number',
                                                        'min' => 0,
                                                        'max' => 100,
                                                        'class' => 'form-control form-control-sm'
                                                    ])->label('Max Completion (%)') ?>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-12 text-center">
                                                    <?= Html::submitButton('<i class="fas fa-file-pdf mr-2"></i> Generate PDF', 
                                                        ['class' => 'btn btn-lg text-white', 'style' => 'background-color: #2e7d32; color: white; border: none;', 'name' => 'generate_pdf']) ?>
                                                    
                                                    <?= Html::button('<i class="fas fa-file-excel mr-2"></i> Generate Excel', 
                                                        ['class' => 'btn btn-lg text-white ml-2', 'style' => 'background-color: #1b5e20; color: white; border: none;', 'onclick' => 'generateExcelReport()']) ?>
                                                </div>
                                            </div>
                                            
                                            <?php ActiveForm::end(); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card mt-3 shadow-sm">
                                        <div class="card-header" style="background-color: #1b5e20; color: white;">
                                            <h4 class="mb-0 text-white"><i class="fas fa-history mr-2"></i> Recently Generated Reports</h4>
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
                                                            <td>County Summary - Marginalized</td>
                                                            <td><span class="badge text-white" style="background-color: #1b5e20;">Summary</span></td>
                                                            <td><?= date('Y-m-d H:i:s') ?></td>
                                                            <td><?= Yii::$app->user->identity->username ?></td>
                                                            <td>
                                                                <?= Html::a('<i class="fas fa-download"></i>', '#', ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;', 'title' => 'Download']) ?>
                                                                <?= Html::a('<i class="fas fa-eye"></i>', '#', ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #2e7d32; color: white; border: none;', 'title' => 'View']) ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Financial Summary - Marginalized</td>
                                                            <td><span class="badge text-white" style="background-color: #1b5e20;">Financial</span></td>
                                                            <td><?= date('Y-m-d H:i:s', strtotime('-1 day')) ?></td>
                                                            <td><?= Yii::$app->user->identity->username ?></td>
                                                            <td>
                                                                <?= Html::a('<i class="fas fa-download"></i>', '#', ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;', 'title' => 'Download']) ?>
                                                                <?= Html::a('<i class="fas fa-eye"></i>', '#', ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #2e7d32; color: white; border: none;', 'title' => 'View']) ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Executive Summary - Marginalized</td>
                                                            <td><span class="badge text-white" style="background-color: #1b5e20;">Summary</span></td>
                                                            <td><?= date('Y-m-d H:i:s', strtotime('-2 days')) ?></td>
                                                            <td><?= Yii::$app->user->identity->username ?></td>
                                                            <td>
                                                                <?= Html::a('<i class="fas fa-download"></i>', '#', ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #1b5e20; color: white; border: none;', 'title' => 'Download']) ?>
                                                                <?= Html::a('<i class="fas fa-eye"></i>', '#', ['class' => 'btn btn-sm text-white', 'style' => 'background-color: #2e7d32; color: white; border: none;', 'title' => 'View']) ?>
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
                    
                    <!-- Charts Section - Moved after the report tabs -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header" style="background-color: #1b5e20; color: white;">
                                    <h5 class="mb-0 text-white"><i class="fas fa-chart-pie mr-2"></i> Projects by County</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="countyChart" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header" style="background-color: #2e7d32; color: white;">
                                    <h5 class="mb-0 text-white"><i class="fas fa-chart-bar mr-2"></i> Budget by Sector</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="sectorChart" height="150"></canvas>
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
            <div class="modal-header" style="background-color: #1b5e20; color: white;">
                <h5 class="modal-title text-white" id="helpModalLabel"><i class="fas fa-question-circle mr-2"></i> Marginalized Areas Report Help</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 style="color: #1b5e20;">How to Generate Marginalized Areas Reports</h4>
                <ol>
                    <li>Select a report category from the tabs (Summary, Detailed, Financial, or Custom).</li>
                    <li>Choose the specific report you want to generate from the available options.</li>
                    <li>Click the "Generate" button to create the report in PDF format.</li>
                    <li>For custom reports, apply filters as needed before generating.</li>
                </ol>
                
                <h4 style="color: #1b5e20;">Report Types</h4>
                <ul>
                    <li><strong>Summary Reports:</strong> High-level overviews including county, sector, and constituency summaries.</li>
                    <li><strong>Detailed Reports:</strong> Comprehensive project listings with full details.</li>
                    <li><strong>Financial Reports:</strong> Budget analysis and financial performance metrics.</li>
                    <li><strong>Custom Reports:</strong> Create tailored reports based on specific criteria.</li>
                </ul>
                
                <h4 style="color: #1b5e20;">Tips</h4>
                <ul>
                    <li>Use the Quick Stats cards to get an overview of marginalized projects data.</li>
                    <li>Check the Budget Summary card for key financial metrics.</li>
                    <li>Access recently generated reports from the Custom Reports tab.</li>
                    <li>Generate both PDF and Excel versions of custom reports using the respective buttons.</li>
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
        border-bottom: 2px solid #1b5e20;
    }
    
    .nav-tabs .nav-link {
        border: none;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: 600;
        border-bottom: 2px solid #ffffff;
        background-color: #1b5e20;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom: 2px solid #ffffff;
        background-color: rgba(27, 94, 32, 0.8);
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
        color: #1b5e20;
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
        
        // Initialize charts
        initCharts();
    });
    
    function initCharts() {
        // County Chart
        var countyCtx = document.getElementById("countyChart").getContext("2d");
        var countyChart = new Chart(countyCtx, {
            type: "pie",
            data: {
                labels: ' . json_encode(array_slice($marginalizedCounties, 0, 5)) . ',
                datasets: [{
                    data: [65, 59, 80, 81, 56],
                    backgroundColor: [
                        "#1b5e20",
                        "#2e7d32",
                        "#388e3c",
                        "#43a047",
                        "#4caf50"
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: "right",
                    labels: {
                        fontColor: "#333"
                    }
                }
            }
        });
        
        // Sector Chart
        var sectorCtx = document.getElementById("sectorChart").getContext("2d");
        var sectorChart = new Chart(sectorCtx, {
            type: "bar",
            data: {
                labels: ' . json_encode(array_keys(array_slice($sectors, 0, 5))) . ',
                datasets: [{
                    label: "Budget (Millions KES)",
                    data: [120, 190, 30, 50, 20],
                    backgroundColor: "#1b5e20",
                    borderColor: "#1b5e20",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: "#333"
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, 0.05)"
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            fontColor: "#333"
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, 0.05)"
                        }
                    }]
                },
                legend: {
                    labels: {
                        fontColor: "#333"
                    }
                }
            }
        });
    }
    
    function exportDashboard() {
        // Placeholder for export functionality
        alert("Export functionality will be implemented soon.");
    }
    
    function generateExcelReport() {
        // Get form data
        var form = document.getElementById("custom-report-form");
        var formData = new FormData(form);
        
        // Build URL with parameters
        var url = "' . Url::to(['download-excel']) . '?";
        var params = [];
        
        // Add form parameters to URL
        for (var pair of formData.entries()) {
            if (pair[1]) { // Only add non-empty values
                params.push(encodeURIComponent(pair[0]) + "=" + encodeURIComponent(pair[1]));
            }
        }
        
        url += params.join("&");
        
        // Open URL in new window to download
        window.open(url, "_blank");
    }
');
?>
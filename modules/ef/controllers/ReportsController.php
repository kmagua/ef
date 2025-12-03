<?php

namespace app\modules\ef\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\ef\models\EqualizationTwoProjects;
use app\modules\ef\models\EqualizationTwoAppropriation;
use app\modules\ef\models\EqualizationTwoProjectsSearch;
use app\modules\ef\models\EqualizationTwoAppropriationSearch;
use yii\db\Query;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * ReportsController handles all report generation for Equalization Fund
 */
class ReportsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => [
                                'index', 'generate', 'county-summary-projects', 'county-summary-appropriations',
                                'sector-summary', 'marginalised-summary', 'detailed-projects',
                                'allocation-report', 'disbursement-report', 'sector-disbursements-per-county',
                                'financial-summary', 'performance-report', 'comparison-report', 'custom-report',
                                'trend-analysis', 'ward-report', 'constituency-report', 'executive-summary'
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],                        
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all available reports
     * @return string
     */
    public function actionIndex()
    {
        // Create a model instance for the custom report form
        $model = new EqualizationTwoProjects();
        
        // Get unique values for dropdown filters
        $counties = EqualizationTwoProjects::find()->select('county')->distinct()->orderBy('county')->column();
        $sectors = EqualizationTwoProjects::find()->select('sector')->distinct()->orderBy('sector')->column();
        $financialYears = EqualizationTwoProjects::find()->select('financial_year')->distinct()->orderBy('financial_year')->column();
        $marginalisedAreas = EqualizationTwoProjects::find()->select('marginalised_area')->distinct()->orderBy('marginalised_area')->column();
        
        // Get statistics for the dashboard
        $totalProjects = EqualizationTwoProjects::find()->count();
        $totalAppropriations = EqualizationTwoAppropriation::find()->count();
        $countiesCount = count($counties);
        $sectorsCount = count($sectors);
        
        // Calculate total budgets
        $totalProjectBudget = EqualizationTwoProjects::find()->sum('project_budget') ?? 0;
        $totalAllocation = EqualizationTwoAppropriation::find()->sum('allocation_ksh') ?? 0;
        
        return $this->render('index', [
            'model' => $model,
            'counties' => $counties,
            'sectors' => $sectors,
            'financialYears' => $financialYears,
            'marginalisedAreas' => $marginalisedAreas,
            'totalProjects' => $totalProjects,
            'totalAppropriations' => $totalAppropriations,
            'countiesCount' => $countiesCount,
            'sectorsCount' => $sectorsCount,
            'totalProjectBudget' => $totalProjectBudget,
            'totalAllocation' => $totalAllocation,
        ]);
    }

    /**
     * Main report generator action
     * @param string $type Report type
     * @param string $model Data model (projects or appropriations)
     * @return mixed
     */
    public function actionGenerate($type = 'county', $model = 'projects')
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportDate = date('F d, Y');
        
        // Generate report based on type and model
        switch ($model) {
            case 'projects':
                return $this->generateProjectsReport($type, $logoUrl, $reportBy, $reportDate);
            case 'appropriations':
                return $this->generateAppropriationsReport($type, $logoUrl, $reportBy, $reportDate);
            case 'combined':
                return $this->generateCombinedReport($type, $logoUrl, $reportBy, $reportDate);
            default:
                throw new NotFoundHttpException('Invalid report model specified.');
        }
    }

    /**
     * Generate projects-based reports
     */
    private function generateProjectsReport($type, $logoUrl, $reportBy, $reportDate)
    {
        switch ($type) {
            case 'county':
                return $this->actionCountySummaryProjects();
            case 'sector':
                return $this->actionSectorSummary();
            case 'marginalised':
                return $this->actionMarginalisedSummary();
            case 'detailed':
                return $this->actionDetailedProjects();
            case 'financial':
                return $this->actionFinancialSummary();
            case 'performance':
                return $this->actionPerformanceReport();
            case 'trend':
                return $this->actionTrendAnalysis();
            case 'ward':
                return $this->actionWardReport();
            case 'constituency':
                return $this->actionConstituencyReport();
            default:
                throw new NotFoundHttpException('Invalid report type specified.');
        }
    }

    /**
     * Generate appropriations-based reports
     */
    private function generateAppropriationsReport($type, $logoUrl, $reportBy, $reportDate)
    {
        switch ($type) {
            case 'county':
                return $this->actionCountySummaryAppropriations();
            case 'allocation':
                return $this->actionAllocationReport();
            case 'disbursement':
                return $this->actionDisbursementReport();
            case 'sector-disbursements':
                return $this->actionSectorDisbursementsPerCounty();
            default:
                throw new NotFoundHttpException('Invalid report type specified.');
        }
    }

    /**
     * Generate combined reports (projects + appropriations)
     */
    private function generateCombinedReport($type, $logoUrl, $reportBy, $reportDate)
    {
        switch ($type) {
            case 'comparison':
                return $this->actionComparisonReport();
            case 'custom':
                return $this->actionCustomReport();
            case 'executive':
                return $this->actionExecutiveSummary();
            default:
                throw new NotFoundHttpException('Invalid report type specified.');
        }
    }

    /**
     * County Summary Projects Report
     * @return mixed
     */
    public function actionCountySummaryProjects()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO COUNTY SUMMARY REPORT - PROJECTS';
        $reportDate = date('F d, Y');
        
        // Get county data
        $countyData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy('county')
            ->orderBy(['project_count' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;

        foreach ($countyData as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;

            $data[] = [
                'county' => $county,
                'project_count' => $projectCount,
                'total_budget' => $budget,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
        }

        $html = $this->generateCountySummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_County_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

 /**
 * County Summary Appropriations Report
 * @return mixed
 */
public function actionCountySummaryAppropriations()
{
    // Load and encode the logo image
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $logoUrl = 'data:image/png;base64,' . $imageData;
    } else {
        $logoUrl = '';
    }

    $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
    $reportTitle = 'EQUALIZATION TWO COUNTY SUMMARY REPORT - APPROPRIATIONS';
    $reportDate = date('F d, Y');
    
    // Get county data
    $countyData = (new Query())
        ->select([
            'county', 
            'COUNT(*) as appropriation_count', 
            'SUM(allocation_ksh) as total_allocation'
        ])
        ->from('eq2_appropriation') // Fixed table name here (changed from plural to singular)
        ->groupBy('county')
        ->orderBy(['appropriation_count' => SORT_DESC])
        ->all();

    // Prepare data for the report
    $data = [];
    $totalAppropriations = 0;
    $totalAllocation = 0;

    foreach ($countyData as $item) {
        $county = $item['county'];
        $appropriationCount = $item['appropriation_count'] ?? 0;
        $allocation = $item['total_allocation'] ?? 0;

        $data[] = [
            'county' => $county,
            'appropriation_count' => $appropriationCount,
            'total_allocation' => $allocation,
        ];

        $totalAppropriations += $appropriationCount;
        $totalAllocation += $allocation;
    }

    $html = $this->generateCountyAppropriationSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalAppropriations, $totalAllocation);

    // Prepare Dompdf with proper Yii handling
    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');

    $options = new Options();
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Equalization_Two_County_Appropriation_Summary_Report.pdf", ["Attachment" => true]);

    exit;
}

    /**
     * Sector Summary Report
     * @return mixed
     */
    public function actionSectorSummary()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO SECTOR SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get sector data
        $sectorData = (new Query())
            ->select([
                'sector', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy('sector')
            ->orderBy(['project_count' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;

        foreach ($sectorData as $item) {
            $sector = $item['sector'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;

            $data[] = [
                'sector' => $sector,
                'project_count' => $projectCount,
                'total_budget' => $budget,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
        }

        $html = $this->generateSectorSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Sector_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Marginalised Summary Report
     * @return mixed
     */
    public function actionMarginalisedSummary()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO MARGINALISED AREAS SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalised area data
        $marginalisedData = (new Query())
            ->select([
                'marginalised_area', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->where(['not', ['marginalised_area' => '']])
            ->groupBy('marginalised_area')
            ->orderBy(['project_count' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;

        foreach ($marginalisedData as $item) {
            $marginalisedArea = $item['marginalised_area'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;

            $data[] = [
                'marginalised_area' => $marginalisedArea,
                'project_count' => $projectCount,
                'total_budget' => $budget,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
        }

        $html = $this->generateMarginalisedSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Marginalised_Areas_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Detailed Projects Report
     * @return mixed
     */
    public function actionDetailedProjects()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO DETAILED PROJECTS REPORT';
        $reportDate = date('F d, Y');
        
        // Get all projects
        $projects = EqualizationTwoProjects::find()->asArray()->all();
        
        // Prepare data for the report
        $data = [];
        $totalBudget = 0;
        
        foreach ($projects as $project) {
            // Use null coalescing operator to handle missing keys
            $projectName = $project['project_name'] ?? $project['project_description'] ?? 'Unnamed Project';
            $county = $project['county'] ?? 'Unknown';
            $constituency = $project['constituency'] ?? 'Unknown';
            $ward = $project['ward'] ?? 'Unknown';
            $marginalisedArea = $project['marginalised_area'] ?? 'Not Specified';
            $sector = $project['sector'] ?? 'Not Specified';
            $budget = $project['project_budget'] ?? 0;

            $data[] = [
                'project_name' => $projectName,
                'county' => $county,
                'constituency' => $constituency,
                'ward' => $ward,
                'marginalised_area' => $marginalisedArea,
                'sector' => $sector,
                'budget' => $budget,
            ];

            $totalBudget += $budget;
        }

        $html = $this->generateDetailedProjectsReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Detailed_Projects_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Allocation Report
     * @return mixed
     */
public function actionAllocationReport()
{
    // Load and encode the logo image
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $logoUrl = 'data:image/png;base64,' . $imageData;
    } else {
        $logoUrl = '';
    }

    $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
    $reportTitle = 'EQUALIZATION TWO ALLOCATION REPORT';
    $reportDate = date('F d, Y');
    
    // Get allocation data
    $allocationData = (new Query())
        ->select([
            'county', 
            'COUNT(*) as project_count',
            'SUM(allocation_ksh) as total_allocation'
        ])
        ->from('eq2_appropriation') // Fixed table name here (changed from plural to singular)
        ->groupBy('county')
        ->orderBy(['total_allocation' => SORT_DESC])
        ->all();

    // Prepare data for the report
    $data = [];
    $overallTotal = 0;
    $overallCount = 0;

    foreach ($allocationData as $item) {
        $county = $item['county'];
        $projectCount = $item['project_count'] ?? 0;
        $totalAllocation = $item['total_allocation'] ?? 0;
        $averageAllocation = $projectCount > 0 ? $totalAllocation / $projectCount : 0;

        $data[] = [
            'county' => $county,
            'project_count' => $projectCount,
            'total_allocation' => $totalAllocation,
            'average_allocation' => $averageAllocation,
        ];

        $overallTotal += $totalAllocation;
        $overallCount += $projectCount;
    }

    $overallAverage = $overallCount > 0 ? $overallTotal / $overallCount : 0;

    $html = $this->generateAllocationReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $overallTotal, $overallCount, $overallAverage);

    // Prepare Dompdf with proper Yii handling
    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');

    $options = new Options();
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Equalization_Two_Allocation_Report.pdf", ["Attachment" => true]);

    exit;
}

    /**
     * Disbursement Report
     * @return mixed
     */
    public function actionDisbursementReport()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO DISBURSEMENT REPORT';
        $reportDate = date('F d, Y');
        
        // Get disbursement data
        $disbursementData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as appropriation_count',
                'SUM(disbursement_ksh) as total_disbursement'
            ])
            ->from('eq2_appropriations')
            ->where(['not', ['disbursement_ksh' => null]])
            ->groupBy('county')
            ->orderBy(['total_disbursement' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $overallTotal = 0;
        $overallCount = 0;

        foreach ($disbursementData as $item) {
            $county = $item['county'];
            $appropriationCount = $item['appropriation_count'] ?? 0;
            $totalDisbursement = $item['total_disbursement'] ?? 0;
            $averageDisbursement = $appropriationCount > 0 ? $totalDisbursement / $appropriationCount : 0;

            $data[] = [
                'county' => $county,
                'appropriation_count' => $appropriationCount,
                'total_disbursement' => $totalDisbursement,
                'average_disbursement' => $averageDisbursement,
            ];

            $overallTotal += $totalDisbursement;
            $overallCount += $appropriationCount;
        }

        $overallAverage = $overallCount > 0 ? $overallTotal / $overallCount : 0;

        $html = $this->generateDisbursementReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $overallTotal, $overallCount, $overallAverage);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Disbursement_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Sector Disbursements Per County Report
     * @return mixed
     */
    public function actionSectorDisbursementsPerCounty()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO SECTOR DISBURSEMENTS PER COUNTY REPORT';
        $reportDate = date('F d, Y');
        
        // Get sector disbursement data per county
        $sectorData = (new Query())
            ->select([
                'county', 
                'sector',
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy(['county', 'sector'])
            ->orderBy(['county' => SORT_ASC, 'total_budget' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;

        foreach ($sectorData as $item) {
            $county = $item['county'];
            $sector = $item['sector'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;

            $data[] = [
                'county' => $county,
                'sector' => $sector,
                'project_count' => $projectCount,
                'total_budget' => $budget,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
        }

        $html = $this->generateSectorDisbursementsPerCountyReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Sector_Disbursements_Per_County_Report.pdf", ["Attachment" => true]);

        exit;
    }

public function actionFinancialSummary()
{
    // Load and encode the logo image
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $logoUrl = 'data:image/png;base64,' . $imageData;
    } else {
        $logoUrl = '';
    }

    $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
    $reportTitle = 'EQUALIZATION TWO FINANCIAL SUMMARY REPORT';
    $reportDate = date('F d, Y');
    
    // Get financial summary data
    $financialData = (new Query())
        ->select([
            'p.county', 
            'COUNT(DISTINCT p.id) as project_count',
            'SUM(p.project_budget) as total_project_budget',
            'COUNT(DISTINCT a.id) as appropriation_count',
            'SUM(a.allocation_ksh) as total_allocation'
        ])
        ->from('eq2_projects p')
        ->leftJoin('eq2_appropriation a', 'p.county = a.county') // This is correct
        ->groupBy('p.county')
        ->orderBy(['p.county' => SORT_ASC])
        ->all();

    // Prepare data for the report
    $data = [];
    $totalProjects = 0;
    $totalProjectBudget = 0;
    $totalAppropriations = 0;
    $totalAllocation = 0;
    $totalVariance = 0;

    foreach ($financialData as $item) {
        $county = $item['county'];
        $projectCount = $item['project_count'] ?? 0;
        $projectBudget = $item['total_project_budget'] ?? 0;
        $appropriationCount = $item['appropriation_count'] ?? 0;
        $allocation = $item['total_allocation'] ?? 0;
        $variance = $allocation - $projectBudget;

        $data[] = [
            'county' => $county,
            'project_count' => $projectCount,
            'total_project_budget' => $projectBudget,
            'appropriation_count' => $appropriationCount,
            'total_allocation' => $allocation,
            'variance' => $variance,
        ];

        $totalProjects += $projectCount;
        $totalProjectBudget += $projectBudget;
        $totalAppropriations += $appropriationCount;
        $totalAllocation += $allocation;
        $totalVariance += $variance;
    }

    $html = $this->generateFinancialSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalProjectBudget, $totalAppropriations, $totalAllocation, $totalVariance);

    // Prepare Dompdf with proper Yii handling
    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');

    $options = new Options();
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Equalization_Two_Financial_Summary_Report.pdf", ["Attachment" => true]);

    exit;
}

    /**
     * Performance Report
     * @return mixed
     */
    public function actionPerformanceReport()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO PERFORMANCE REPORT';
        $reportDate = date('F d, Y');
        
        // Get performance data
        $performanceData = (new Query())
            ->select([
                'p.county', 
                'COUNT(DISTINCT p.id) as project_count',
                'SUM(p.project_budget) as total_project_budget',
                'COUNT(DISTINCT a.id) as appropriation_count',
                'SUM(a.allocation_ksh) as total_allocation'
            ])
            ->from('eq2_projects p')
            ->leftJoin('eq2_appropriations a', 'p.county = a.county')
            ->groupBy('p.county')
            ->orderBy(['p.county' => SORT_ASC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalProjectBudget = 0;
        $totalAppropriations = 0;
        $totalAllocation = 0;
        $totalVariance = 0;
        $totalUtilizationRate = 0;

        foreach ($performanceData as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'] ?? 0;
            $projectBudget = $item['total_project_budget'] ?? 0;
            $appropriationCount = $item['appropriation_count'] ?? 0;
            $allocation = $item['total_allocation'] ?? 0;
            $variance = $allocation - $projectBudget;
            $utilizationRate = $allocation > 0 ? ($projectBudget / $allocation) * 100 : 0;

            $data[] = [
                'county' => $county,
                'project_count' => $projectCount,
                'total_project_budget' => $projectBudget,
                'appropriation_count' => $appropriationCount,
                'total_allocation' => $allocation,
                'variance' => $variance,
                'utilization_rate' => $utilizationRate,
            ];

            $totalProjects += $projectCount;
            $totalProjectBudget += $projectBudget;
            $totalAppropriations += $appropriationCount;
            $totalAllocation += $allocation;
            $totalVariance += $variance;
            $totalUtilizationRate += $utilizationRate;
        }

        $avgUtilizationRate = count($data) > 0 ? $totalUtilizationRate / count($data) : 0;

        $html = $this->generatePerformanceReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalProjectBudget, $totalAppropriations, $totalAllocation, $totalVariance, $avgUtilizationRate);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Performance_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Comparison Report
     * @return mixed
     */
    public function actionComparisonReport()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO COMPARISON REPORT';
        $reportDate = date('F d, Y');
        
        // Get comparison data
        $comparisonData = (new Query())
            ->select([
                'p.county', 
                'COUNT(DISTINCT p.id) as project_count',
                'SUM(p.project_budget) as total_project_budget',
                'COUNT(DISTINCT a.id) as appropriation_count',
                'SUM(a.allocation_ksh) as total_allocation'
            ])
            ->from('eq2_projects p')
            ->leftJoin('eq2_appropriations a', 'p.county = a.county')
            ->groupBy('p.county')
            ->orderBy(['p.county' => SORT_ASC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalProjectBudget = 0;
        $totalAppropriations = 0;
        $totalAllocation = 0;
        $totalVariance = 0;

        foreach ($comparisonData as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'] ?? 0;
            $projectBudget = $item['total_project_budget'] ?? 0;
            $appropriationCount = $item['appropriation_count'] ?? 0;
            $allocation = $item['total_allocation'] ?? 0;
            $variance = $allocation - $projectBudget;

            $data[] = [
                'county' => $county,
                'project_count' => $projectCount,
                'total_project_budget' => $projectBudget,
                'appropriation_count' => $appropriationCount,
                'total_allocation' => $allocation,
                'variance' => $variance,
            ];

            $totalProjects += $projectCount;
            $totalProjectBudget += $projectBudget;
            $totalAppropriations += $appropriationCount;
            $totalAllocation += $allocation;
            $totalVariance += $variance;
        }

        $html = $this->generateComparisonReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalProjectBudget, $totalAppropriations, $totalAllocation, $totalVariance);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Comparison_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Trend Analysis Report
     * @return mixed
     */
    public function actionTrendAnalysis()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO TREND ANALYSIS REPORT';
        $reportDate = date('F d, Y');
        
        // Get trend data by financial year
        $trendData = (new Query())
            ->select([
                'financial_year', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget',
                'COUNT(DISTINCT county) as counties_covered'
            ])
            ->from('eq2_projects')
            ->groupBy('financial_year')
            ->orderBy('financial_year')
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $previousYearCount = 0;
        $previousYearBudget = 0;
        $growthData = [];

        foreach ($trendData as $item) {
            $year = $item['financial_year'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $countiesCovered = $item['counties_covered'] ?? 0;
            
            // Calculate growth rates
            $projectGrowth = $previousYearCount > 0 ? (($projectCount - $previousYearCount) / $previousYearCount) * 100 : 0;
            $budgetGrowth = $previousYearBudget > 0 ? (($budget - $previousYearBudget) / $previousYearBudget) * 100 : 0;
            
            $data[] = [
                'financial_year' => $year,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'counties_covered' => $countiesCovered,
                'project_growth' => $projectGrowth,
                'budget_growth' => $budgetGrowth,
            ];

            $growthData[] = [
                'year' => $year,
                'projects' => $projectCount,
                'budget' => $budget / 1000000, // Convert to millions for better readability
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
            $previousYearCount = $projectCount;
            $previousYearBudget = $budget;
        }

        $html = $this->generateTrendAnalysisReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $growthData);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Trend_Analysis_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Ward Report
     * @return mixed
     */
    public function actionWardReport()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO WARD REPORT';
        $reportDate = date('F d, Y');
        
        // Get ward data
        $wardData = (new Query())
            ->select([
                'county', 
                'constituency',
                'ward', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->where(['not', ['ward' => '']])
            ->groupBy(['county', 'constituency', 'ward'])
            ->orderBy(['county' => SORT_ASC, 'constituency' => SORT_ASC, 'ward' => SORT_ASC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $countyData = [];

        foreach ($wardData as $item) {
            $county = $item['county'];
            $constituency = $item['constituency'];
            $ward = $item['ward'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;

            $data[] = [
                'county' => $county,
                'constituency' => $constituency,
                'ward' => $ward,
                'project_count' => $projectCount,
                'total_budget' => $budget,
            ];

            // Aggregate county data
            if (!isset($countyData[$county])) {
                $countyData[$county] = [
                    'project_count' => 0,
                    'total_budget' => 0,
                    'constituencies' => []
                ];
            }
            $countyData[$county]['project_count'] += $projectCount;
            $countyData[$county]['total_budget'] += $budget;
            
            if (!isset($countyData[$county]['constituencies'][$constituency])) {
                $countyData[$county]['constituencies'][$constituency] = [
                    'project_count' => 0,
                    'total_budget' => 0
                ];
            }
            $countyData[$county]['constituencies'][$constituency]['project_count'] += $projectCount;
            $countyData[$county]['constituencies'][$constituency]['total_budget'] += $budget;

            $totalProjects += $projectCount;
            $totalBudget += $budget;
        }

        $html = $this->generateWardReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $countyData);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Ward_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Constituency Report
     * @return mixed
     */
    public function actionConstituencyReport()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO CONSTITUENCY REPORT';
        $reportDate = date('F d, Y');
        
        // Get constituency data
        $constituencyData = (new Query())
            ->select([
                'county', 
                'constituency',
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget',
                'COUNT(DISTINCT ward) as wards_covered'
            ])
            ->from('eq2_projects')
            ->where(['not', ['constituency' => '']])
            ->groupBy(['county', 'constituency'])
            ->orderBy(['county' => SORT_ASC, 'constituency' => SORT_ASC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $countyData = [];

        foreach ($constituencyData as $item) {
            $county = $item['county'];
            $constituency = $item['constituency'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $wardsCovered = $item['wards_covered'] ?? 0;

            $data[] = [
                'county' => $county,
                'constituency' => $constituency,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'wards_covered' => $wardsCovered,
            ];

            // Aggregate county data
            if (!isset($countyData[$county])) {
                $countyData[$county] = [
                    'project_count' => 0,
                    'total_budget' => 0,
                    'constituencies' => 0
                ];
            }
            $countyData[$county]['project_count'] += $projectCount;
            $countyData[$county]['total_budget'] += $budget;
            $countyData[$county]['constituencies']++;

            $totalProjects += $projectCount;
            $totalBudget += $budget;
        }

        $html = $this->generateConstituencyReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $countyData);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Constituency_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Executive Summary Report
     * @return mixed
     */
    public function actionExecutiveSummary()
    {
        // Load and encode the logo image
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO EXECUTIVE SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get executive summary data
        $totalProjects = EqualizationTwoProjects::find()->count();
        $totalProjectBudget = EqualizationTwoProjects::find()->sum('project_budget') ?? 0;
        $totalAppropriations = EqualizationTwoAppropriation::find()->count();
        $totalAllocation = EqualizationTwoAppropriation::find()->sum('allocation_ksh') ?? 0;
        
        // Get county data
        $countyData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy('county')
            ->orderBy('project_count DESC')
            ->limit(5)
            ->all();
        
        // Get sector data
        $sectorData = (new Query())
            ->select([
                'sector', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy('sector')
            ->orderBy('project_count DESC')
            ->limit(5)
            ->all();
        
        // Get trend data
        $trendData = (new Query())
            ->select([
                'financial_year', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy('financial_year')
            ->orderBy('financial_year DESC')
            ->limit(3)
            ->all();
        
        // Calculate variance
        $variance = $totalAllocation - $totalProjectBudget;
        $variancePercentage = $totalAllocation > 0 ? ($variance / $totalAllocation) * 100 : 0;
        
        // Prepare data for the report
        $data = [
            'totalProjects' => $totalProjects,
            'totalProjectBudget' => $totalProjectBudget,
            'totalAppropriations' => $totalAppropriations,
            'totalAllocation' => $totalAllocation,
            'variance' => $variance,
            'variancePercentage' => $variancePercentage,
            'countyData' => $countyData,
            'sectorData' => $sectorData,
            'trendData' => $trendData,
        ];

        $html = $this->generateExecutiveSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Equalization_Two_Executive_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }



 /**
 * Custom Report Generator
 * @return mixed
 */
public function actionCustomReport()
{
    // Load and encode the logo image
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $logoUrl = 'data:image/png;base64,' . $imageData;
    } else {
        $logoUrl = '';
    }

    $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
    $reportTitle = 'EQUALIZATION TWO CUSTOM REPORT';
    $reportDate = date('F d, Y');
    
    // Get custom report parameters from request
    $request = Yii::$app->request;
    $params = $request->get('EqualizationTwoProjects', []);
    $county = $params['county'] ?? '';
    $sector = $params['sector'] ?? '';
    $financialYear = $params['financial_year'] ?? '';
    $marginalisedArea = $params['marginalised_area'] ?? '';
    
    // Build query based on parameters
    $query = EqualizationTwoProjects::find();
    
    if (!empty($county)) {
        $query->andWhere(['county' => $county]);
    }
    
    if (!empty($sector)) {
        $query->andWhere(['sector' => $sector]);
    }
    
    if (!empty($financialYear)) {
        $query->andWhere(['financial_year' => $financialYear]);
    }
    
    if (!empty($marginalisedArea)) {
        $query->andWhere(['marginalised_area' => $marginalisedArea]);
    }
    
    $projects = $query->asArray()->all();
    
    // Prepare data for the report
    $data = [];
    $totalBudget = 0;
    $countyData = [];
    $sectorData = [];
    
    foreach ($projects as $project) {
        // Use null coalescing operator to handle missing keys
        $projectName = $project['project_name'] ?? $project['project_description'] ?? 'Unnamed Project';
        $county = $project['county'] ?? 'Unknown';
        $constituency = $project['constituency'] ?? 'Unknown';
        $ward = $project['ward'] ?? 'Unknown';
        $marginalisedArea = $project['marginalised_area'] ?? 'Not Specified';
        $sector = $project['sector'] ?? 'Not Specified';
        $budget = $project['project_budget'] ?? 0;
        $description = $project['project_description'] ?? '';

        $data[] = [
            'project_name' => $projectName,
            'county' => $county,
            'constituency' => $constituency,
            'ward' => $ward,
            'marginalised_area' => $marginalisedArea,
            'sector' => $sector,
            'budget' => $budget,
            'description' => $description,
        ];

        $totalBudget += $budget;
        
        // Aggregate county data
        if (!isset($countyData[$county])) {
            $countyData[$county] = [
                'project_count' => 0,
                'total_budget' => 0
            ];
        }
        $countyData[$county]['project_count']++;
        $countyData[$county]['total_budget'] += $budget;
        
        // Aggregate sector data
        if (!isset($sectorData[$sector])) {
            $sectorData[$sector] = [
                'project_count' => 0,
                'total_budget' => 0
            ];
        }
        $sectorData[$sector]['project_count']++;
        $sectorData[$sector]['total_budget'] += $budget;
    }
    
    // Prepare filter information for the report
    $filterInfo = [];
    if (!empty($county)) {
        $filterInfo[] = 'County: ' . $county;
    }
    if (!empty($sector)) {
        $filterInfo[] = 'Sector: ' . $sector;
    }
    if (!empty($financialYear)) {
        $filterInfo[] = 'Financial Year: ' . $financialYear;
    }
    if (!empty($marginalisedArea)) {
        $filterInfo[] = 'Marginalised Area: ' . $marginalisedArea;
    }

    $html = $this->generateCustomReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget, $countyData, $sectorData, $filterInfo);

    // Prepare Dompdf with proper Yii handling
    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Equalization_Two_Custom_Report.pdf", ["Attachment" => true]);

    exit;
}

/**
 * Generate HTML for the custom report with enhanced styling
 */
private function generateCustomReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget, $countyData, $sectorData, $filterInfo)
{
    $html = '
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
        
        body { 
            font-family: "Poppins", sans-serif; 
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .container {
            width: 100%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .logo {
            margin-bottom: 15px;
        }
        
        .logo img {
            max-width: 400px;
            height: auto;
        }
        
        .title {
            font-size: 28px;
            font-weight: 700;
            color: #1b5e20;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .flag-bar {
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
            margin-bottom: 15px;
        }
        
        .report-info {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }
        
        .filter-box {
            background-color: #f8f9fa;
            border-left: 5px solid #1b5e20;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .filter-title {
            font-size: 18px;
            font-weight: 600;
            color: #1b5e20;
            margin-bottom: 10px;
        }
        
        .filter-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .filter-item {
            background-color: #e8f5e9;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border-left: 5px solid #1b5e20;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .summary-title {
            font-size: 18px;
            font-weight: 600;
            color: #1b5e20;
            margin-bottom: 10px;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            min-width: 150px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1b5e20;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1b5e20;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1b5e20;
        }
        
        .two-column {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .column {
            width: 48%;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 12px; 
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        
        th { 
            background-color: #1b5e20; 
            color: white; 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e8f5e9;
        }
        
        .grand-total { 
            font-weight: bold; 
            background-color: #e8f5e9; 
        }
        
        .signature {
            margin-top: 50px;
            font-size: 14px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            width: 250px;
            margin-bottom: 5px;
        }
        
        .system-note {
            font-size: 12px;
            color: #757575;
            margin-top: 20px;
            font-style: italic;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            color: #757575;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .page-number {
            text-align: right;
            font-size: 10px;
            color: #757575;
        }
        
        @page {
            margin: 20px;
            size: A4 landscape;
        }
        
        @page :first {
            margin-top: 20px;
        }
        
        @page :last {
            margin-bottom: 20px;
        }
    </style>
    
    <div class="container">
        <div class="header">
            <div class="logo">
                ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
            </div>
            <div class="title">' . $reportTitle . '</div>
            <div class="flag-bar"></div>
            <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
        </div>';
        
        // Add filter information if any filters were applied
        if (!empty($filterInfo)) {
            $html .= '
            <div class="filter-box">
                <div class="filter-title">Applied Filters</div>
                <div class="filter-list">';
            
            foreach ($filterInfo as $filter) {
                $html .= '<div class="filter-item">' . $filter . '</div>';
            }
            
            $html .= '
                </div>
            </div>';
        }
        
        $html .= '
        <div class="summary-box">
            <div class="summary-title">Executive Summary</div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value">' . number_format(count($data)) . '</div>
                    <div class="stat-label">Total Projects</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                    <div class="stat-label">Total Budget</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">' . count($countyData) . '</div>
                    <div class="stat-label">Counties</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">KES ' . number_format($totalBudget / count($data), 2) . '</div>
                    <div class="stat-label">Avg. Budget/Project</div>
                </div>
            </div>
        </div>';
        
        // Add county breakdown if there's more than one county
        if (count($countyData) > 1) {
            $html .= '
            <div class="section">
                <div class="section-title">County Breakdown</div>
                <table>
                    <thead>
                        <tr>
                            <th>County</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>% of Total</th>
                            <th>Avg. Budget/Project</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($countyData as $county => $stats) {
                $percentage = ($stats['total_budget'] / $totalBudget) * 100;
                $avgBudget = $stats['project_count'] > 0 ? $stats['total_budget'] / $stats['project_count'] : 0;
                
                $html .= '
                        <tr>
                            <td>' . $county . '</td>
                            <td>' . number_format($stats['project_count']) . '</td>
                            <td>KES ' . number_format($stats['total_budget'], 2) . '</td>
                            <td>' . number_format($percentage, 1) . '%</td>
                            <td>KES ' . number_format($avgBudget, 2) . '</td>
                        </tr>';
            }
            
            $html .= '
                    </tbody>
                </table>
            </div>';
        }
        
        // Add sector breakdown if there's more than one sector
        if (count($sectorData) > 1) {
            $html .= '
            <div class="section">
                <div class="section-title">Sector Breakdown</div>
                <table>
                    <thead>
                        <tr>
                            <th>Sector</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>% of Total</th>
                            <th>Avg. Budget/Project</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($sectorData as $sector => $stats) {
                $percentage = ($stats['total_budget'] / $totalBudget) * 100;
                $avgBudget = $stats['project_count'] > 0 ? $stats['total_budget'] / $stats['project_count'] : 0;
                
                $html .= '
                        <tr>
                            <td>' . $sector . '</td>
                            <td>' . number_format($stats['project_count']) . '</td>
                            <td>KES ' . number_format($stats['total_budget'], 2) . '</td>
                            <td>' . number_format($percentage, 1) . '%</td>
                            <td>KES ' . number_format($avgBudget, 2) . '</td>
                        </tr>';
            }
            
            $html .= '
                    </tbody>
                </table>
            </div>';
        }
        
        // Add detailed project list
        $html .= '
        <div class="section">
            <div class="section-title">Detailed Project List</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project Name</th>
                        <th>County</th>
                        <th>Constituency</th>
                        <th>Ward</th>
                        <th>Marginalised Area</th>
                        <th>Sector</th>
                        <th>Budget</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $row['project_name'] . '</td>
                        <td>' . $row['county'] . '</td>
                        <td>' . $row['constituency'] . '</td>
                        <td>' . $row['ward'] . '</td>
                        <td>' . $row['marginalised_area'] . '</td>
                        <td>' . $row['sector'] . '</td>
                        <td>KES ' . number_format($row['budget'], 2) . '</td>
                        <td>' . substr($row['description'], 0, 100) . (strlen($row['description']) > 100 ? '...' : '') . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="8"><strong>Grand Total</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>-</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the trend analysis report
     */
    private function generateTrendAnalysisReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $growthData)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            .chart-container {
                margin-bottom: 30px;
                padding: 20px;
                background-color: #f8f9fa;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .chart-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 15px;
                text-align: center;
            }
            
            .chart {
                height: 200px;
                position: relative;
                display: flex;
                align-items: flex-end;
                justify-content: space-around;
                padding: 0 20px;
            }
            
            .bar {
                width: 60px;
                background-color: #1b5e20;
                position: relative;
                border-radius: 4px 4px 0 0;
                transition: height 0.5s;
            }
            
            .bar-label {
                position: absolute;
                bottom: -25px;
                left: 0;
                width: 100%;
                text-align: center;
                font-size: 12px;
            }
            
            .bar-value {
                position: absolute;
                top: -25px;
                left: 0;
                width: 100%;
                text-align: center;
                font-size: 12px;
                font-weight: 600;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .growth-positive {
                color: #28a745;
                font-weight: 600;
            }
            
            .growth-negative {
                color: #dc3545;
                font-weight: 600;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($data) . '</div>
                        <div class="stat-label">Years Covered</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget / $totalProjects, 2) . '</div>
                        <div class="stat-label">Avg. Budget/Project</div>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-title">Project Budget Trend (in Millions KES)</div>
                <div class="chart">';
        
        // Find max value for scaling
        $maxValue = 0;
        foreach ($growthData as $item) {
            if ($item['budget'] > $maxValue) {
                $maxValue = $item['budget'];
            }
        }
        
        // Generate bars
        foreach ($growthData as $item) {
            $height = $maxValue > 0 ? ($item['budget'] / $maxValue) * 150 : 0;
            $html .= '
                    <div class="bar" style="height: ' . $height . 'px;">
                        <div class="bar-value">' . number_format($item['budget'], 1) . 'M</div>
                        <div class="bar-label">' . $item['year'] . '</div>
                    </div>';
        }
        
        $html .= '
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Financial Year</th>
                        <th>Project Count</th>
                        <th>Growth</th>
                        <th>Total Budget</th>
                        <th>Growth</th>
                        <th>Counties Covered</th>
                        <th>Avg. Budget/Project</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $projectGrowthClass = $row['project_growth'] >= 0 ? 'growth-positive' : 'growth-negative';
            $budgetGrowthClass = $row['budget_growth'] >= 0 ? 'growth-positive' : 'growth-negative';
            $projectGrowthSymbol = $row['project_growth'] >= 0 ? '+' : '';
            $budgetGrowthSymbol = $row['budget_growth'] >= 0 ? '+' : '';
            $avgBudget = $row['project_count'] > 0 ? $row['total_budget'] / $row['project_count'] : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['financial_year'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td class="' . $projectGrowthClass . '">' . $projectGrowthSymbol . number_format($row['project_growth'], 1) . '%</td>
                        <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                        <td class="' . $budgetGrowthClass . '">' . $budgetGrowthSymbol . number_format($row['budget_growth'], 1) . '%</td>
                        <td>' . $row['counties_covered'] . '</td>
                        <td>KES ' . number_format($avgBudget, 2) . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>KES ' . number_format($totalBudget / $totalProjects, 2) . '</strong></td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the ward report
     */
    private function generateWardReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $countyData)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            .county-section {
                margin-bottom: 30px;
                page-break-inside: avoid;
            }
            
            .county-title {
                font-size: 20px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 15px;
                padding-bottom: 5px;
                border-bottom: 2px solid #1b5e20;
            }
            
            .county-stats {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($countyData) . '</div>
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget / $totalProjects, 2) . '</div>
                        <div class="stat-label">Avg. Budget/Project</div>
                    </div>
                </div>
            </div>';
        
        // Group data by county
        $countyGroups = [];
        foreach ($data as $item) {
            $county = $item['county'];
            if (!isset($countyGroups[$county])) {
                $countyGroups[$county] = [];
            }
            $countyGroups[$county][] = $item;
        }
        
        // Generate county sections
        foreach ($countyGroups as $county => $wards) {
            $countyTotalProjects = $countyData[$county]['project_count'];
            $countyTotalBudget = $countyData[$county]['total_budget'];
            
            $html .= '
            <div class="county-section">
                <div class="county-title">' . strtoupper($county) . ' COUNTY</div>
                <div class="county-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($countyTotalProjects) . '</div>
                        <div class="stat-label">Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($countyTotalBudget, 2) . '</div>
                        <div class="stat-label">Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($wards) . '</div>
                        <div class="stat-label">Wards</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($countyTotalBudget / $countyTotalProjects, 2) . '</div>
                        <div class="stat-label">Avg. Budget/Project</div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Constituency</th>
                            <th>Ward</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>Avg. Budget/Project</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $counter = 1;
            foreach ($wards as $ward) {
                $avgBudget = $ward['project_count'] > 0 ? $ward['total_budget'] / $ward['project_count'] : 0;
                
                $html .= '
                        <tr>
                            <td>' . $counter++ . '</td>
                            <td>' . $ward['constituency'] . '</td>
                            <td>' . $ward['ward'] . '</td>
                            <td>' . number_format($ward['project_count']) . '</td>
                            <td>KES ' . number_format($ward['total_budget'], 2) . '</td>
                            <td>KES ' . number_format($avgBudget, 2) . '</td>
                        </tr>';
            }
            
            $html .= '
                    </tbody>
                </table>
            </div>';
        }

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

  /**
 * Generate HTML for the constituency report
 */
private function generateConstituencyReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $countyData)
{
    $html = '
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
        
        body { 
            font-family: "Poppins", sans-serif; 
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .container {
            width: 100%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .logo {
            margin-bottom: 15px;
        }
        
        .logo img {
            max-width: 400px;
            height: auto;
        }
        
        .title {
            font-size: 28px;
            font-weight: 700;
            color: #1b5e20;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .flag-bar {
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
            margin-bottom: 15px;
        }
        
        .report-info {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border-left: 5px solid #1b5e20;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .summary-title {
            font-size: 18px;
            font-weight: 600;
            color: #1b5e20;
            margin-bottom: 10px;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            min-width: 150px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1b5e20;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        
        .county-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .county-title {
            font-size: 20px;
            font-weight: 600;
            color: #1b5e20;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1b5e20;
        }
        
        .county-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 14px; 
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        th, td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left; 
        }
        
        th { 
            background-color: #1b5e20; 
            color: white; 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e8f5e9;
        }
        
        .grand-total { 
            font-weight: bold; 
            background-color: #e8f5e9; 
        }
        
        .signature {
            margin-top: 50px;
            font-size: 14px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            width: 250px;
            margin-bottom: 5px;
        }
        
        .system-note {
            font-size: 12px;
            color: #757575;
            margin-top: 20px;
            font-style: italic;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            color: #757575;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .page-number {
            text-align: right;
            font-size: 10px;
            color: #757575;
        }
        
        @page {
            margin: 20px;
            size: A4 landscape;
        }
        
        @page :first {
            margin-top: 20px;
        }
        
        @page :last {
            margin-bottom: 20px;
        }
    </style>
    
    <div class="container">
        <div class="header">
            <div class="logo">
                ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
            </div>
            <div class="title">' . $reportTitle . '</div>
            <div class="flag-bar"></div>
            <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
        </div>
        
        <div class="summary-box">
            <div class="summary-title">Executive Summary</div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value">' . number_format($totalProjects) . '</div>
                    <div class="stat-label">Total Projects</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                    <div class="stat-label">Total Budget</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">' . count($countyData) . '</div>
                    <div class="stat-label">Counties</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">KES ' . number_format($totalBudget / $totalProjects, 2) . '</div>
                    <div class="stat-label">Avg. Budget/Project</div>
                </div>
            </div>
        </div>';
    
    // Group data by county
    $countyGroups = [];
    foreach ($data as $item) {
        $county = $item['county'];
        if (!isset($countyGroups[$county])) {
            $countyGroups[$county] = [];
        }
        $countyGroups[$county][] = $item;
    }
    
    // Generate county sections
    foreach ($countyGroups as $county => $constituencies) {
        $countyTotalProjects = $countyData[$county]['project_count'];
        $countyTotalBudget = $countyData[$county]['total_budget'];
        $countyConstituenciesCount = $countyData[$county]['constituencies']; // This is already a count, not an array
        
        $html .= '
        <div class="county-section">
            <div class="county-title">' . strtoupper($county) . ' COUNTY</div>
            <div class="county-stats">
                <div class="stat-item">
                    <div class="stat-value">' . number_format($countyTotalProjects) . '</div>
                    <div class="stat-label">Projects</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">KES ' . number_format($countyTotalBudget, 2) . '</div>
                    <div class="stat-label">Budget</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">' . $countyConstituenciesCount . '</div>
                    <div class="stat-label">Constituencies</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">KES ' . number_format($countyTotalBudget / $countyTotalProjects, 2) . '</div>
                    <div class="stat-label">Avg. Budget/Project</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Constituency</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                        <th>Wards Covered</th>
                        <th>Avg. Budget/Project</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($constituencies as $constituency) {
            $avgBudget = $constituency['project_count'] > 0 ? $constituency['total_budget'] / $constituency['project_count'] : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $constituency['constituency'] . '</td>
                        <td>' . number_format($constituency['project_count']) . '</td>
                        <td>KES ' . number_format($constituency['total_budget'], 2) . '</td>
                        <td>' . $constituency['wards_covered'] . '</td>
                        <td>KES ' . number_format($avgBudget, 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
        </div>';
    }

    // Add Signature Section
    $html .= '
        <div class="signature">
            <p><strong>Signed By:</strong></p>
            <div class="signature-line"></div>
            <p><strong>Mr. Guyo Boru</strong></p>
            <p>Chief Executive Officer</p>
            <p>Date: ' . $reportDate . '</p>
        </div>';

    // System Generated Note
    $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

    // Footer
    $html .= '<div class="footer">Equalization Two Projects Management System</div>';

    return $html;
}


    /**
     * Generate HTML for the executive summary report
     */
    private function generateExecutiveSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .executive-summary {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 20px;
                margin-bottom: 30px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 22px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 15px;
                text-align: center;
            }
            
            .summary-content {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            
            .key-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
                margin-bottom: 30px;
            }
            
            .stat-card {
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                padding: 20px;
                text-align: center;
                width: 23%;
                margin-bottom: 20px;
                border-top: 4px solid #1b5e20;
            }
            
            .stat-value {
                font-size: 32px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .stat-label {
                font-size: 16px;
                color: #666;
            }
            
            .section {
                margin-bottom: 30px;
            }
            
            .section-title {
                font-size: 20px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 15px;
                padding-bottom: 5px;
                border-bottom: 2px solid #1b5e20;
            }
            
            .two-column {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }
            
            .column {
                width: 48%;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .variance-positive {
                color: #28a745;
                font-weight: 600;
            }
            
            .variance-negative {
                color: #dc3545;
                font-weight: 600;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 portrait;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="executive-summary">
                <div class="summary-title">EXECUTIVE SUMMARY</div>
                <div class="summary-content">
                    This report provides a comprehensive overview of the Equalization Fund II projects, including key statistics, 
                    financial performance, and trends. The Equalization Fund II aims to address regional imbalances in Kenya by 
                    providing financial resources to marginalized counties and regions.
                </div>
            </div>
            
            <div class="key-stats">
                <div class="stat-card">
                    <div class="stat-value">' . number_format($data['totalProjects']) . '</div>
                    <div class="stat-label">Total Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">KES ' . number_format($data['totalProjectBudget'], 2) . '</div>
                    <div class="stat-label">Total Project Budget</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">' . number_format($data['totalAppropriations']) . '</div>
                    <div class="stat-label">Total Appropriations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">KES ' . number_format($data['totalAllocation'], 2) . '</div>
                    <div class="stat-label">Total Allocation</div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Financial Performance</div>
                <div class="two-column">
                    <div class="column">
                        <table>
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total Project Budget</td>
                                    <td>KES ' . number_format($data['totalProjectBudget'], 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Total Allocation</td>
                                    <td>KES ' . number_format($data['totalAllocation'], 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Variance</td>
                                    <td class="' . ($data['variance'] >= 0 ? 'variance-positive' : 'variance-negative') . '">
                                        KES ' . number_format($data['variance'], 2) . '
                                    </td>
                                </tr>
                                <tr>
                                    <td>Variance Percentage</td>
                                    <td class="' . ($data['variancePercentage'] >= 0 ? 'variance-positive' : 'variance-negative') . '">
                                        ' . number_format($data['variancePercentage'], 1) . '%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="column">
                        <table>
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Average Budget per Project</td>
                                    <td>KES ' . number_format($data['totalProjectBudget'] / $data['totalProjects'], 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Average Allocation per Appropriation</td>
                                    <td>KES ' . number_format($data['totalAllocation'] / $data['totalAppropriations'], 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Projects per Appropriation</td>
                                    <td>' . number_format($data['totalProjects'] / $data['totalAppropriations'], 1) . '</td>
                                </tr>
                                <tr>
                                    <td>Budget Utilization</td>
                                    <td>' . number_format(($data['totalProjectBudget'] / $data['totalAllocation']) * 100, 1) . '%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Top Counties by Project Count</div>
                <table>
                    <thead>
                        <tr>
                            <th>County</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>% of Total Projects</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($data['countyData'] as $county) {
            $percentage = ($county['project_count'] / $data['totalProjects']) * 100;
            $html .= '
                        <tr>
                            <td>' . $county['county'] . '</td>
                            <td>' . number_format($county['project_count']) . '</td>
                            <td>KES ' . number_format($county['total_budget'], 2) . '</td>
                            <td>' . number_format($percentage, 1) . '%</td>
                        </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <div class="section-title">Top Sectors by Project Count</div>
                <table>
                    <thead>
                        <tr>
                            <th>Sector</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>% of Total Projects</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($data['sectorData'] as $sector) {
            $percentage = ($sector['project_count'] / $data['totalProjects']) * 100;
            $html .= '
                        <tr>
                            <td>' . $sector['sector'] . '</td>
                            <td>' . number_format($sector['project_count']) . '</td>
                            <td>KES ' . number_format($sector['total_budget'], 2) . '</td>
                            <td>' . number_format($percentage, 1) . '%</td>
                        </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <div class="section-title">Recent Financial Year Performance</div>
                <table>
                    <thead>
                        <tr>
                            <th>Financial Year</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>Avg. Budget per Project</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($data['trendData'] as $trend) {
            $avgBudget = $trend['project_count'] > 0 ? $trend['total_budget'] / $trend['project_count'] : 0;
            $html .= '
                        <tr>
                            <td>' . $trend['financial_year'] . '</td>
                            <td>' . number_format($trend['project_count']) . '</td>
                            <td>KES ' . number_format($trend['total_budget'], 2) . '</td>
                            <td>KES ' . number_format($avgBudget, 2) . '</td>
                        </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
            </div>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the county appropriation summary report
     */
    private function generateCountyAppropriationSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalAppropriations, $totalAllocation)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .percentage-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .percentage-fill {
                height: 100%;
                background-color: #1b5e20;
                border-radius: 10px;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalAppropriations) . '</div>
                        <div class="stat-label">Total Appropriations</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalAllocation, 2) . '</div>
                        <div class="stat-label">Total Allocation</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($data) . '</div>
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalAllocation / $totalAppropriations, 2) . '</div>
                        <div class="stat-label">Avg. Allocation/Appropriation</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Appropriation Count</th>
                        <th>% of Total</th>
                        <th>Total Allocation</th>
                        <th>% of Total</th>
                        <th>Avg. Allocation/Appropriation</th>
                        <th>Allocation Distribution</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $appropriationPercentage = $totalAppropriations > 0 ? ($row['appropriation_count'] / $totalAppropriations) * 100 : 0;
            $allocationPercentage = $totalAllocation > 0 ? ($row['total_allocation'] / $totalAllocation) * 100 : 0;
            $avgAllocation = $row['appropriation_count'] > 0 ? $row['total_allocation'] / $row['appropriation_count'] : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>' . number_format($row['appropriation_count']) . '</td>
                        <td>' . number_format($appropriationPercentage, 1) . '%</td>
                        <td>KES ' . number_format($row['total_allocation'], 2) . '</td>
                        <td>' . number_format($allocationPercentage, 1) . '%</td>
                        <td>KES ' . number_format($avgAllocation, 2) . '</td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: ' . $allocationPercentage . '%"></div>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalAppropriations) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($totalAllocation, 2) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($totalAllocation / $totalAppropriations, 2) . '</strong></td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

     /**
     * Generate HTML for the marginalised summary report
     */
    private function generateMarginalisedSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .percentage-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .percentage-fill {
                height: 100%;
                background-color: #1b5e20;
                border-radius: 10px;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($data) . '</div>
                        <div class="stat-label">Marginalised Areas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget / $totalProjects, 2) . '</div>
                        <div class="stat-label">Avg. Budget/Project</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Marginalised Area</th>
                        <th>Project Count</th>
                        <th>% of Total</th>
                        <th>Total Budget</th>
                        <th>% of Total</th>
                        <th>Avg. Budget/Project</th>
                        <th>Budget Distribution</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $projectPercentage = $totalProjects > 0 ? ($row['project_count'] / $totalProjects) * 100 : 0;
            $budgetPercentage = $totalBudget > 0 ? ($row['total_budget'] / $totalBudget) * 100 : 0;
            $avgBudget = $row['project_count'] > 0 ? $row['total_budget'] / $row['project_count'] : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['marginalised_area'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>' . number_format($projectPercentage, 1) . '%</td>
                        <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                        <td>' . number_format($budgetPercentage, 1) . '%</td>
                        <td>KES ' . number_format($avgBudget, 2) . '</td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: ' . $budgetPercentage . '%"></div>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($totalBudget / $totalProjects, 2) . '</strong></td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the detailed projects report
     */
    private function generateDetailedProjectsReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 12px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 11px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format(count($data)) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget / count($data), 2) . '</div>
                        <div class="stat-label">Avg. Budget/Project</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project Name</th>
                        <th>County</th>
                        <th>Constituency</th>
                        <th>Ward</th>
                        <th>Marginalised Area</th>
                        <th>Sector</th>
                        <th>Budget</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $row['project_name'] . '</td>
                        <td>' . $row['county'] . '</td>
                        <td>' . $row['constituency'] . '</td>
                        <td>' . $row['ward'] . '</td>
                        <td>' . $row['marginalised_area'] . '</td>
                        <td>' . $row['sector'] . '</td>
                        <td>KES ' . number_format($row['budget'], 2) . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="7"><strong>Grand Total</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the allocation report
     */
    private function generateAllocationReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $overallTotal, $overallCount, $overallAverage)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .percentage-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .percentage-fill {
                height: 100%;
                background-color: #1b5e20;
                border-radius: 10px;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($overallCount) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($overallTotal, 2) . '</div>
                        <div class="stat-label">Total Allocation</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($data) . '</div>
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($overallAverage, 2) . '</div>
                        <div class="stat-label">Avg. Allocation/Project</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Total Allocation</th>
                        <th>% of Total</th>
                        <th>Project Count</th>
                        <th>% of Total</th>
                        <th>Average Allocation</th>
                        <th>Allocation Distribution</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $allocationPercentage = $overallTotal > 0 ? ($row['total_allocation'] / $overallTotal) * 100 : 0;
            $projectPercentage = $overallCount > 0 ? ($row['project_count'] / $overallCount) * 100 : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>KES ' . number_format($row['total_allocation'], 2) . '</td>
                        <td>' . number_format($allocationPercentage, 1) . '%</td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>' . number_format($projectPercentage, 1) . '%</td>
                        <td>KES ' . number_format($row['average_allocation'], 2) . '</td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: ' . $allocationPercentage . '%"></div>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>KES ' . number_format($overallTotal, 2) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>' . number_format($overallCount) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($overallAverage, 2) . '</strong></td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the disbursement report
     */
    private function generateDisbursementReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $overallTotal, $overallCount, $overallAverage)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .percentage-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .percentage-fill {
                height: 100%;
                background-color: #1b5e20;
                border-radius: 10px;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($overallCount) . '</div>
                        <div class="stat-label">Total Appropriations</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($overallTotal, 2) . '</div>
                        <div class="stat-label">Total Disbursement</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($data) . '</div>
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($overallAverage, 2) . '</div>
                        <div class="stat-label">Avg. Disbursement</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Total Disbursement</th>
                        <th>% of Total</th>
                        <th>Appropriation Count</th>
                        <th>% of Total</th>
                        <th>Average Disbursement</th>
                        <th>Disbursement Distribution</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $disbursementPercentage = $overallTotal > 0 ? ($row['total_disbursement'] / $overallTotal) * 100 : 0;
            $appropriationPercentage = $overallCount > 0 ? ($row['appropriation_count'] / $overallCount) * 100 : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>KES ' . number_format($row['total_disbursement'], 2) . '</td>
                        <td>' . number_format($disbursementPercentage, 1) . '%</td>
                        <td>' . number_format($row['appropriation_count']) . '</td>
                        <td>' . number_format($appropriationPercentage, 1) . '%</td>
                        <td>KES ' . number_format($row['average_disbursement'], 2) . '</td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: ' . $disbursementPercentage . '%"></div>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>KES ' . number_format($overallTotal, 2) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>' . number_format($overallCount) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($overallAverage, 2) . '</strong></td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the sector disbursements per county report
     */
    private function generateSectorDisbursementsPerCountyReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            .county-section {
                margin-bottom: 30px;
                page-break-inside: avoid;
            }
            
            .county-title {
                font-size: 20px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 15px;
                padding-bottom: 5px;
                border-bottom: 2px solid #1b5e20;
            }
            
            .county-stats {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count(array_unique(array_column($data, 'county'))) . '</div>
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count(array_unique(array_column($data, 'sector'))) . '</div>
                        <div class="stat-label">Sectors</div>
                    </div>
                </div>
            </div>';
        
        // Group data by county
        $countyGroups = [];
        foreach ($data as $item) {
            $county = $item['county'];
            if (!isset($countyGroups[$county])) {
                $countyGroups[$county] = [];
            }
            $countyGroups[$county][] = $item;
        }
        
        // Generate county sections
        foreach ($countyGroups as $county => $sectors) {
            $countyTotalProjects = array_sum(array_column($sectors, 'project_count'));
            $countyTotalBudget = array_sum(array_column($sectors, 'total_budget'));
            
            $html .= '
            <div class="county-section">
                <div class="county-title">' . strtoupper($county) . ' COUNTY</div>
                <div class="county-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($countyTotalProjects) . '</div>
                        <div class="stat-label">Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($countyTotalBudget, 2) . '</div>
                        <div class="stat-label">Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($sectors) . '</div>
                        <div class="stat-label">Sectors</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($countyTotalBudget / $countyTotalProjects, 2) . '</div>
                        <div class="stat-label">Avg. Budget/Project</div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sector</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>% of County Total</th>
                            <th>Avg. Budget/Project</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $counter = 1;
            foreach ($sectors as $sector) {
                $percentage = $countyTotalBudget > 0 ? ($sector['total_budget'] / $countyTotalBudget) * 100 : 0;
                $avgBudget = $sector['project_count'] > 0 ? $sector['total_budget'] / $sector['project_count'] : 0;
                
                $html .= '
                        <tr>
                            <td>' . $counter++ . '</td>
                            <td>' . $sector['sector'] . '</td>
                            <td>' . number_format($sector['project_count']) . '</td>
                            <td>KES ' . number_format($sector['total_budget'], 2) . '</td>
                            <td>' . number_format($percentage, 1) . '%</td>
                            <td>KES ' . number_format($avgBudget, 2) . '</td>
                        </tr>';
            }
            
            $html .= '
                    </tbody>
                </table>
            </div>';
        }

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the financial summary report
     */
    private function generateFinancialSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalProjectBudget, $totalAppropriations, $totalAllocation, $totalVariance)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            .variance-positive {
                color: #28a745;
                font-weight: 600;
            }
            
            .variance-negative {
                color: #dc3545;
                font-weight: 600;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .percentage-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .percentage-fill {
                height: 100%;
                background-color: #1b5e20;
                border-radius: 10px;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalProjectBudget, 2) . '</div>
                        <div class="stat-label">Total Project Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalAppropriations) . '</div>
                        <div class="stat-label">Total Appropriations</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalAllocation, 2) . '</div>
                        <div class="stat-label">Total Allocation</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value ' . ($totalVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            KES ' . number_format($totalVariance, 2) . '
                        </div>
                        <div class="stat-label">Variance</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Project Budget</th>
                        <th>Appropriation Count</th>
                        <th>Total Allocation</th>
                        <th>Variance</th>
                        <th>Variance %</th>
                        <th>Budget Utilization</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $variancePercentage = $row['total_allocation'] > 0 ? ($row['variance'] / $row['total_allocation']) * 100 : 0;
            $utilizationRate = $row['total_allocation'] > 0 ? ($row['total_project_budget'] / $row['total_allocation']) * 100 : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>KES ' . number_format($row['total_project_budget'], 2) . '</td>
                        <td>' . number_format($row['appropriation_count']) . '</td>
                        <td>KES ' . number_format($row['total_allocation'], 2) . '</td>
                        <td class="' . ($row['variance'] >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            KES ' . number_format($row['variance'], 2) . '
                        </td>
                        <td class="' . ($variancePercentage >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            ' . number_format($variancePercentage, 1) . '%
                        </td>
                        <td>' . number_format($utilizationRate, 1) . '%</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>KES ' . number_format($totalProjectBudget, 2) . '</strong></td>
                        <td><strong>' . number_format($totalAppropriations) . '</strong></td>
                        <td><strong>KES ' . number_format($totalAllocation, 2) . '</strong></td>
                        <td class="' . ($totalVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            <strong>KES ' . number_format($totalVariance, 2) . '</strong>
                        </td>
                        <td class="' . ($totalVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            <strong>' . number_format(($totalVariance / $totalAllocation) * 100, 1) . '%</strong>
                        </td>
                        <td><strong>' . number_format(($totalProjectBudget / $totalAllocation) * 100, 1) . '%</strong></td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the performance report
     */
    private function generatePerformanceReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalProjectBudget, $totalAppropriations, $totalAllocation, $totalVariance, $avgUtilizationRate)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            .performance-indicator {
                display: inline-block;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                margin-right: 5px;
            }
            
            .performance-excellent {
                background-color: #28a745;
            }
            
            .performance-good {
                background-color: #17a2b8;
            }
            
            .performance-average {
                background-color: #ffc107;
            }
            
            .performance-poor {
                background-color: #dc3545;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalProjectBudget, 2) . '</div>
                        <div class="stat-label">Total Project Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalAppropriations) . '</div>
                        <div class="stat-label">Total Appropriations</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalAllocation, 2) . '</div>
                        <div class="stat-label">Total Allocation</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($avgUtilizationRate, 1) . '%</div>
                        <div class="stat-label">Avg. Utilization Rate</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Project Budget</th>
                        <th>Appropriation Count</th>
                        <th>Total Allocation</th>
                        <th>Variance</th>
                        <th>Utilization Rate</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $utilizationRate = $row['utilization_rate'];
            
            // Determine performance indicator
            if ($utilizationRate >= 90) {
                $performanceClass = 'performance-excellent';
                $performanceLabel = 'Excellent';
            } elseif ($utilizationRate >= 70) {
                $performanceClass = 'performance-good';
                $performanceLabel = 'Good';
            } elseif ($utilizationRate >= 50) {
                $performanceClass = 'performance-average';
                $performanceLabel = 'Average';
            } else {
                $performanceClass = 'performance-poor';
                $performanceLabel = 'Poor';
            }
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>KES ' . number_format($row['total_project_budget'], 2) . '</td>
                        <td>' . number_format($row['appropriation_count']) . '</td>
                        <td>KES ' . number_format($row['total_allocation'], 2) . '</td>
                        <td>KES ' . number_format($row['variance'], 2) . '</td>
                        <td>' . number_format($utilizationRate, 1) . '%</td>
                        <td>
                            <span class="performance-indicator ' . $performanceClass . '"></span>
                            ' . $performanceLabel . '
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>KES ' . number_format($totalProjectBudget, 2) . '</strong></td>
                        <td><strong>' . number_format($totalAppropriations) . '</strong></td>
                        <td><strong>KES ' . number_format($totalAllocation, 2) . '</strong></td>
                        <td><strong>KES ' . number_format($totalVariance, 2) . '</strong></td>
                        <td><strong>' . number_format($avgUtilizationRate, 1) . '%</strong></td>
                        <td><strong>-</strong></td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the comparison report
     */
    private function generateComparisonReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalProjectBudget, $totalAppropriations, $totalAllocation, $totalVariance)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            .comparison-chart {
                margin-bottom: 30px;
                padding: 20px;
                background-color: #f8f9fa;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .chart-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 15px;
                text-align: center;
            }
            
            .chart {
                height: 200px;
                position: relative;
                display: flex;
                align-items: flex-end;
                justify-content: space-around;
                padding: 0 20px;
            }
            
            .bar-group {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 60px;
            }
            
            .bar {
                width: 25px;
                margin: 0 2px;
                border-radius: 4px 4px 0 0;
            }
            
            .bar-projects {
                background-color: #1b5e20;
            }
            
            .bar-appropriations {
                background-color: #17a2b8;
            }
            
            .bar-label {
                margin-top: 10px;
                font-size: 12px;
                text-align: center;
            }
            
            .bar-value {
                position: absolute;
                top: -25px;
                font-size: 10px;
                font-weight: 600;
            }
            
            .legend {
                display: flex;
                justify-content: center;
                margin-top: 20px;
            }
            
            .legend-item {
                display: flex;
                align-items: center;
                margin: 0 15px;
            }
            
            .legend-color {
                width: 15px;
                height: 15px;
                margin-right: 5px;
            }
            
            .legend-color-projects {
                background-color: #1b5e20;
            }
            
            .legend-color-appropriations {
                background-color: #17a2b8;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .variance-positive {
                color: #28a745;
                font-weight: 600;
            }
            
            .variance-negative {
                color: #dc3545;
                font-weight: 600;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalProjectBudget, 2) . '</div>
                        <div class="stat-label">Total Project Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalAppropriations) . '</div>
                        <div class="stat-label">Total Appropriations</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalAllocation, 2) . '</div>
                        <div class="stat-label">Total Allocation</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value ' . ($totalVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            KES ' . number_format($totalVariance, 2) . '
                        </div>
                        <div class="stat-label">Variance</div>
                    </div>
                </div>
            </div>
            
            <div class="comparison-chart">
                <div class="chart-title">Projects vs Appropriations by County</div>
                <div class="chart">';
        
        // Find max values for scaling
        $maxProjects = 0;
        $maxAppropriations = 0;
        foreach ($data as $item) {
            if ($item['project_count'] > $maxProjects) {
                $maxProjects = $item['project_count'];
            }
            if ($item['appropriation_count'] > $maxAppropriations) {
                $maxAppropriations = $item['appropriation_count'];
            }
        }
        
        // Generate bars for each county
        foreach ($data as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'];
            $appropriationCount = $item['appropriation_count'];
            
            $projectHeight = $maxProjects > 0 ? ($projectCount / $maxProjects) * 150 : 0;
            $appropriationHeight = $maxAppropriations > 0 ? ($appropriationCount / $maxAppropriations) * 150 : 0;
            
            $html .= '
                    <div class="bar-group">
                        <div class="bar bar-projects" style="height: ' . $projectHeight . 'px;">
                            <div class="bar-value" style="left: -10px;">' . $projectCount . '</div>
                        </div>
                        <div class="bar bar-appropriations" style="height: ' . $appropriationHeight . 'px;">
                            <div class="bar-value" style="left: 15px;">' . $appropriationCount . '</div>
                        </div>
                        <div class="bar-label">' . substr($county, 0, 8) . '</div>
                    </div>';
        }
        
        $html .= '
                </div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color legend-color-projects"></div>
                        <div>Projects</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-color-appropriations"></div>
                        <div>Appropriations</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Project Budget</th>
                        <th>Appropriation Count</th>
                        <th>Total Allocation</th>
                        <th>Variance</th>
                        <th>Variance %</th>
                        <th>Budget Utilization</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $variancePercentage = $row['total_allocation'] > 0 ? ($row['variance'] / $row['total_allocation']) * 100 : 0;
            $utilizationRate = $row['total_allocation'] > 0 ? ($row['total_project_budget'] / $row['total_allocation']) * 100 : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>KES ' . number_format($row['total_project_budget'], 2) . '</td>
                        <td>' . number_format($row['appropriation_count']) . '</td>
                        <td>KES ' . number_format($row['total_allocation'], 2) . '</td>
                        <td class="' . ($row['variance'] >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            KES ' . number_format($row['variance'], 2) . '
                        </td>
                        <td class="' . ($variancePercentage >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            ' . number_format($variancePercentage, 1) . '%
                        </td>
                        <td>' . number_format($utilizationRate, 1) . '%</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>KES ' . number_format($totalProjectBudget, 2) . '</strong></td>
                        <td><strong>' . number_format($totalAppropriations) . '</strong></td>
                        <td><strong>KES ' . number_format($totalAllocation, 2) . '</strong></td>
                        <td class="' . ($totalVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            <strong>KES ' . number_format($totalVariance, 2) . '</strong>
                        </td>
                        <td class="' . ($totalVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            <strong>' . number_format(($totalVariance / $totalAllocation) * 100, 1) . '%</strong>
                        </td>
                        <td><strong>' . number_format(($totalProjectBudget / $totalAllocation) * 100, 1) . '%</strong></td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }
    
    /**
     * Generate HTML for the sector summary report
     */
    private function generateSectorSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                position: relative;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 28px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .summary-box {
                background-color: #f8f9fa;
                border-left: 5px solid #1b5e20;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1b5e20;
                margin-bottom: 10px;
            }
            
            .summary-stats {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            
            .stat-item {
                text-align: center;
                padding: 10px;
                min-width: 150px;
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
            }
            
            .stat-label {
                font-size: 14px;
                color: #666;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 14px; 
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 12px; 
                text-align: left; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: 600;
                text-transform: uppercase;
                font-size: 13px;
            }
            
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tr:hover {
                background-color: #e8f5e9;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .percentage-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .percentage-fill {
                height: 100%;
                background-color: #1b5e20;
                border-radius: 10px;
            }
            
            .signature {
                margin-top: 50px;
                font-size: 14px;
            }
            
            .signature-line {
                border-bottom: 1px solid #333;
                width: 250px;
                margin-bottom: 5px;
            }
            
            .system-note {
                font-size: 12px;
                color: #757575;
                margin-top: 20px;
                font-style: italic;
            }
            
            .footer {
                text-align: center;
                font-size: 10px;
                color: #757575;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
            
            .page-number {
                text-align: right;
                font-size: 10px;
                color: #757575;
            }
            
            @page {
                margin: 20px;
                size: A4 landscape;
            }
            
            @page :first {
                margin-top: 20px;
            }
            
            @page :last {
                margin-bottom: 20px;
            }
        </style>
        
        <div class="container">
            <div class="header">
                <div class="logo">
                    ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo"/>' : '') . '
                </div>
                <div class="title">' . $reportTitle . '</div>
                <div class="flag-bar"></div>
                <div class="report-info">' . $reportBy . ' | ' . $reportDate . '</div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">Executive Summary</div>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($totalProjects) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($data) . '</div>
                        <div class="stat-label">Sectors</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget / $totalProjects, 2) . '</div>
                        <div class="stat-label">Avg. Budget/Project</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sector</th>
                        <th>Project Count</th>
                        <th>% of Total</th>
                        <th>Total Budget</th>
                        <th>% of Total</th>
                        <th>Avg. Budget/Project</th>
                        <th>Budget Distribution</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $projectPercentage = $totalProjects > 0 ? ($row['project_count'] / $totalProjects) * 100 : 0;
            $budgetPercentage = $totalBudget > 0 ? ($row['total_budget'] / $totalBudget) * 100 : 0;
            $avgBudget = $row['project_count'] > 0 ? $row['total_budget'] / $row['project_count'] : 0;
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['sector'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>' . number_format($projectPercentage, 1) . '%</td>
                        <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                        <td>' . number_format($budgetPercentage, 1) . '%</td>
                        <td>KES ' . number_format($avgBudget, 2) . '</td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: ' . $budgetPercentage . '%"></div>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>100%</strong></td>
                        <td><strong>KES ' . number_format($totalBudget / $totalProjects, 2) . '</strong></td>
                        <td>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>';

        // Add Signature Section
        $html .= '
            <div class="signature">
                <p><strong>Signed By:</strong></p>
                <div class="signature-line"></div>
                <p><strong>Mr. Guyo Boru</strong></p>
                <p>Chief Executive Officer</p>
                <p>Date: ' . $reportDate . '</p>
            </div>';

        // System Generated Note
        $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

        // Footer
        $html .= '<div class="footer">Equalization Two Projects Management System</div>';

        return $html;
    }
    
    
}
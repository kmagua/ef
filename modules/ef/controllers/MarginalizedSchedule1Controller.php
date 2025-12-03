<?php

namespace app\modules\ef\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\ef\models\EqualizationFundProject;
use app\modules\ef\models\EqualizationFundProjectSearch;
use yii\db\Query;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * MarginalizedSchedule1Controller implements the report actions for Equalization Fund marginalized projects (Schedule 1).
 */
class MarginalizedSchedule1Controller extends Controller
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
                                'index', 'county-summary', 'sector-summary', 'constituency-summary',
                                'detailed-report', 'financial-summary', 'performance-report',
                                'trend-analysis', 'custom-report', 'executive-summary',
                                'download-excel'
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
     * Lists all available marginalized reports
     * @return string
     */
    public function actionIndex()
    {
        // Get statistics for the dashboard
        $totalProjects = EqualizationFundProject::find()->count();
        $totalBudget = EqualizationFundProject::find()->sum('budget_2018_19') ?? 0;
        $countiesCount = EqualizationFundProject::find()->select('county')->distinct()->count();
        $sectorsCount = EqualizationFundProject::find()->select('sector')->distinct()->count();
        
        // Get marginalized counties (assuming we have a predefined list)
        $marginalizedCounties = $this->getMarginalizedCounties();
        $marginalizedProjects = EqualizationFundProject::find()->where(['in', 'county', $marginalizedCounties])->count();
        $marginalizedBudget = EqualizationFundProject::find()->where(['in', 'county', $marginalizedCounties])->sum('budget_2018_19') ?? 0;
        
        // Get all sectors for dropdown
        $sectors = EqualizationFundProject::find()->select('sector')->distinct()->column();
        $sectors = array_combine($sectors, $sectors);
        
        // Create search model for form
        $searchModel = new \app\modules\ef\models\MarginalizedReportSearch();
        
        return $this->render('index', [
            'totalProjects' => $totalProjects,
            'totalBudget' => $totalBudget,
            'countiesCount' => $countiesCount,
            'sectorsCount' => $sectorsCount,
            'marginalizedProjects' => $marginalizedProjects,
            'marginalizedBudget' => $marginalizedBudget,
            'marginalizedCounties' => $marginalizedCounties,
            'sectors' => $sectors,
            'model' => $searchModel,  // Use the search model instead
        ]);
    }

    /**
     * County Summary Report for marginalized areas
     * @return mixed
     */
    public function actionCountySummary()
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS COUNTY SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get county data for marginalized areas
        $countyData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->groupBy('county')
            ->orderBy(['project_count' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $overallCompletion = 0;

        foreach ($countyData as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $avgCompletion = $item['avg_completion'] ?? 0;

            $data[] = [
                'county' => $county,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'avg_completion' => $avgCompletion,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
            $overallCompletion += $avgCompletion;
        }

        $overallAvgCompletion = count($data) > 0 ? $overallCompletion / count($data) : 0;

        $html = $this->generateCountySummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Fund_Marginalized_County_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Sector Summary Report for marginalized areas
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS SECTOR SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get sector data for marginalized areas
        $sectorData = (new Query())
            ->select([
                'sector', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->groupBy('sector')
            ->orderBy(['project_count' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $overallCompletion = 0;

        foreach ($sectorData as $item) {
            $sector = $item['sector'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $avgCompletion = $item['avg_completion'] ?? 0;

            $data[] = [
                'sector' => $sector,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'avg_completion' => $avgCompletion,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
            $overallCompletion += $avgCompletion;
        }

        $overallAvgCompletion = count($data) > 0 ? $overallCompletion / count($data) : 0;

        $html = $this->generateSectorSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Fund_Marginalized_Sector_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Constituency Summary Report for marginalized areas
     * @return mixed
     */
    public function actionConstituencySummary()
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS CONSTITUENCY SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get constituency data for marginalized areas
        $constituencyData = (new Query())
            ->select([
                'county',
                'constituency', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->andWhere(['not', ['constituency' => '']])
            ->groupBy(['county', 'constituency'])
            ->orderBy(['county' => SORT_ASC, 'project_count' => SORT_DESC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $overallCompletion = 0;
        $countyData = [];

        foreach ($constituencyData as $item) {
            $county = $item['county'];
            $constituency = $item['constituency'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $avgCompletion = $item['avg_completion'] ?? 0;

            $data[] = [
                'county' => $county,
                'constituency' => $constituency,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'avg_completion' => $avgCompletion,
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
            $overallCompletion += $avgCompletion;
        }

        $overallAvgCompletion = count($data) > 0 ? $overallCompletion / count($data) : 0;

        $html = $this->generateConstituencySummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion, $countyData);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Fund_Marginalized_Constituency_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Detailed Report for marginalized areas
     * @return mixed
     */
    public function actionDetailedReport()
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS DETAILED PROJECTS REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get all projects in marginalized areas
        $projects = EqualizationFundProject::find()
            ->where(['in', 'county', $marginalizedCounties])
            ->asArray()
            ->all();
        
        // Prepare data for the report
        $data = [];
        $totalBudget = 0;
        $totalCompletion = 0;
        
        foreach ($projects as $project) {
            $projectName = $project['project_name'] ?? 'Unnamed Project';
            $county = $project['county'] ?? 'Unknown';
            $constituency = $project['constituency'] ?? 'Unknown';
            $sector = $project['sector'] ?? 'Not Specified';
            $budget = $project['budget_2018_19'] ?? 0;
            $contractSum = $project['contract_sum'] ?? 0;
            $completion = $project['percent_completion'] ?? 0;
            $fundingSource = $project['funding_source'] ?? 'Equalization Fund';

            $data[] = [
                'project_name' => $projectName,
                'county' => $county,
                'constituency' => $constituency,
                'sector' => $sector,
                'budget' => $budget,
                'contract_sum' => $contractSum,
                'completion' => $completion,
                'funding_source' => $fundingSource,
            ];

            $totalBudget += $budget;
            $totalCompletion += $completion;
        }

        $avgCompletion = count($data) > 0 ? $totalCompletion / count($data) : 0;

        $html = $this->generateDetailedReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget, $avgCompletion);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Fund_Marginalized_Detailed_Projects_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Financial Summary Report for marginalized areas
     * @return mixed
     */
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS FINANCIAL SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get financial summary data
        $financialData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count',
                'SUM(budget_2018_19) as total_budget',
                'SUM(contract_sum) as total_contract_sum',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->groupBy('county')
            ->orderBy(['county' => SORT_ASC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $totalContractSum = 0;
        $overallCompletion = 0;

        foreach ($financialData as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $contractSum = $item['total_contract_sum'] ?? 0;
            $avgCompletion = $item['avg_completion'] ?? 0;
            $variance = $budget - $contractSum;

            $data[] = [
                'county' => $county,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'total_contract_sum' => $contractSum,
                'variance' => $variance,
                'avg_completion' => $avgCompletion,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
            $totalContractSum += $contractSum;
            $overallCompletion += $avgCompletion;
        }

        $overallVariance = $totalBudget - $totalContractSum;
        $overallAvgCompletion = count($data) > 0 ? $overallCompletion / count($data) : 0;

        $html = $this->generateFinancialSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $totalContractSum, $overallVariance, $overallAvgCompletion);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Fund_Marginalized_Financial_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Performance Report for marginalized areas
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS PERFORMANCE REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get performance data
        $performanceData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count',
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->groupBy('county')
            ->orderBy(['county' => SORT_ASC])
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $overallCompletion = 0;

        foreach ($performanceData as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $avgCompletion = $item['avg_completion'] ?? 0;

            // Determine performance rating
            if ($avgCompletion >= 80) {
                $performanceRating = 'Excellent';
                $performanceClass = 'performance-excellent';
            } elseif ($avgCompletion >= 60) {
                $performanceRating = 'Good';
                $performanceClass = 'performance-good';
            } elseif ($avgCompletion >= 40) {
                $performanceRating = 'Average';
                $performanceClass = 'performance-average';
            } else {
                $performanceRating = 'Poor';
                $performanceClass = 'performance-poor';
            }

            $data[] = [
                'county' => $county,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'avg_completion' => $avgCompletion,
                'performance_rating' => $performanceRating,
                'performance_class' => $performanceClass,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
            $overallCompletion += $avgCompletion;
        }

        $overallAvgCompletion = count($data) > 0 ? $overallCompletion / count($data) : 0;
        
        // Determine overall performance rating
        if ($overallAvgCompletion >= 80) {
            $overallPerformanceRating = 'Excellent';
        } elseif ($overallAvgCompletion >= 60) {
            $overallPerformanceRating = 'Good';
        } elseif ($overallAvgCompletion >= 40) {
            $overallPerformanceRating = 'Average';
        } else {
            $overallPerformanceRating = 'Poor';
        }

        $html = $this->generatePerformanceReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion, $overallPerformanceRating);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Fund_Marginalized_Performance_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Trend Analysis Report for marginalized areas
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS TREND ANALYSIS REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Since we don't have a year field in the table, we'll simulate trend analysis by county
        // In a real scenario, you would have a year field or date field to track trends over time
        
        // Get trend data by county
        $trendData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget',
                'AVG(percent_completion) as avg_completion'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->groupBy('county')
            ->orderBy('county')
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;
        $overallCompletion = 0;

        foreach ($trendData as $item) {
            $county = $item['county'];
            $projectCount = $item['project_count'] ?? 0;
            $budget = $item['total_budget'] ?? 0;
            $avgCompletion = $item['avg_completion'] ?? 0;

            $data[] = [
                'county' => $county,
                'project_count' => $projectCount,
                'total_budget' => $budget,
                'avg_completion' => $avgCompletion,
            ];

            $totalProjects += $projectCount;
            $totalBudget += $budget;
            $overallCompletion += $avgCompletion;
        }

        $overallAvgCompletion = count($data) > 0 ? $overallCompletion / count($data) : 0;

        $html = $this->generateTrendAnalysisReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion);

        // Prepare Dompdf with proper Yii handling
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Equalization_Fund_Marginalized_Trend_Analysis_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Custom Report for marginalized areas
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS CUSTOM REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get custom report parameters from request
        $request = Yii::$app->request;
        $county = $request->get('county', '');
        $sector = $request->get('sector', '');
        $minCompletion = $request->get('min_completion', 0);
        $maxCompletion = $request->get('max_completion', 100);
        
        // Build query based on parameters
        $query = EqualizationFundProject::find()
            ->where(['in', 'county', $marginalizedCounties]);
        
        if (!empty($county)) {
            $query->andWhere(['county' => $county]);
        }
        
        if (!empty($sector)) {
            $query->andWhere(['sector' => $sector]);
        }
        
        $query->andWhere(['>=', 'percent_completion', $minCompletion]);
        $query->andWhere(['<=', 'percent_completion', $maxCompletion]);
        
        $projects = $query->asArray()->all();
        
        // Prepare data for the report
        $data = [];
        $totalBudget = 0;
        $totalCompletion = 0;
        $countyData = [];
        $sectorData = [];
        
        foreach ($projects as $project) {
            $projectName = $project['project_name'] ?? 'Unnamed Project';
            $county = $project['county'] ?? 'Unknown';
            $constituency = $project['constituency'] ?? 'Unknown';
            $sector = $project['sector'] ?? 'Not Specified';
            $budget = $project['budget_2018_19'] ?? 0;
            $contractSum = $project['contract_sum'] ?? 0;
            $completion = $project['percent_completion'] ?? 0;
            $fundingSource = $project['funding_source'] ?? 'Equalization Fund';

            $data[] = [
                'project_name' => $projectName,
                'county' => $county,
                'constituency' => $constituency,
                'sector' => $sector,
                'budget' => $budget,
                'contract_sum' => $contractSum,
                'completion' => $completion,
                'funding_source' => $fundingSource,
            ];

            $totalBudget += $budget;
            $totalCompletion += $completion;
            
            // Aggregate county data
            if (!isset($countyData[$county])) {
                $countyData[$county] = [
                    'project_count' => 0,
                    'total_budget' => 0,
                    'avg_completion' => 0
                ];
            }
            $countyData[$county]['project_count']++;
            $countyData[$county]['total_budget'] += $budget;
            $countyData[$county]['avg_completion'] += $completion;
            
            // Aggregate sector data
            if (!isset($sectorData[$sector])) {
                $sectorData[$sector] = [
                    'project_count' => 0,
                    'total_budget' => 0,
                    'avg_completion' => 0
                ];
            }
            $sectorData[$sector]['project_count']++;
            $sectorData[$sector]['total_budget'] += $budget;
            $sectorData[$sector]['avg_completion'] += $completion;
        }
        
        // Calculate averages
        foreach ($countyData as $county => $stats) {
            if ($stats['project_count'] > 0) {
                $countyData[$county]['avg_completion'] = $stats['avg_completion'] / $stats['project_count'];
            }
        }
        
        foreach ($sectorData as $sector => $stats) {
            if ($stats['project_count'] > 0) {
                $sectorData[$sector]['avg_completion'] = $stats['avg_completion'] / $stats['project_count'];
            }
        }
        
        $avgCompletion = count($data) > 0 ? $totalCompletion / count($data) : 0;
        
        // Prepare filter information for the report
        $filterInfo = [];
        if (!empty($county)) {
            $filterInfo[] = 'County: ' . $county;
        }
        if (!empty($sector)) {
            $filterInfo[] = 'Sector: ' . $sector;
        }
        $filterInfo[] = 'Completion: ' . $minCompletion . '% - ' . $maxCompletion . '%';

        $html = $this->generateCustomReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget, $avgCompletion, $countyData, $sectorData, $filterInfo);

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
        $dompdf->stream("Equalization_Fund_Marginalized_Custom_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Executive Summary Report for marginalized areas
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
        $reportTitle = 'EQUALIZATION FUND MARGINALIZED AREAS EXECUTIVE SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Get executive summary data
        $totalProjects = EqualizationFundProject::find()->where(['in', 'county', $marginalizedCounties])->count();
        $totalBudget = EqualizationFundProject::find()->where(['in', 'county', $marginalizedCounties])->sum('budget_2018_19') ?? 0;
        $totalContractSum = EqualizationFundProject::find()->where(['in', 'county', $marginalizedCounties])->sum('contract_sum') ?? 0;
        $avgCompletion = EqualizationFundProject::find()->where(['in', 'county', $marginalizedCounties])->average('percent_completion') ?? 0;
        
        // Get county data
        $countyData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->groupBy('county')
            ->orderBy('project_count DESC')
            ->limit(5)
            ->all();
        
        // Get sector data
        $sectorData = (new Query())
            ->select([
                'sector', 
                'COUNT(*) as project_count', 
                'SUM(budget_2018_19) as total_budget'
            ])
            ->from('equalization_fund_project')
            ->where(['in', 'county', $marginalizedCounties])
            ->groupBy('sector')
            ->orderBy('project_count DESC')
            ->limit(5)
            ->all();
        
        // Calculate variance
        $variance = $totalBudget - $totalContractSum;
        $variancePercentage = $totalBudget > 0 ? ($variance / $totalBudget) * 100 : 0;
        
        // Prepare data for the report
        $data = [
            'totalProjects' => $totalProjects,
            'totalBudget' => $totalBudget,
            'totalContractSum' => $totalContractSum,
            'avgCompletion' => $avgCompletion,
            'variance' => $variance,
            'variancePercentage' => $variancePercentage,
            'countyData' => $countyData,
            'sectorData' => $sectorData,
            'marginalizedCounties' => $marginalizedCounties,
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
        $dompdf->stream("Equalization_Fund_Marginalized_Executive_Summary_Report.pdf", ["Attachment" => true]);

        exit;
    }

    /**
     * Download Excel Report for marginalized areas
     * @return mixed
     */
    public function actionDownloadExcel()
    {
        // Get report type from request
        $request = Yii::$app->request;
        $reportType = $request->get('type', 'custom');
        
        // Get marginalized counties
        $marginalizedCounties = $this->getMarginalizedCounties();
        
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("FiscalBridge Portal")
            ->setLastModifiedBy("FiscalBridge Portal")
            ->setTitle("Equalization Fund Marginalized Areas Report")
            ->setSubject("Equalization Fund Marginalized Areas Report")
            ->setDescription("Report for Equalization Fund Marginalized Areas Projects")
            ->setKeywords("equalization fund marginalized areas report")
            ->setCategory("Report");
        
        // Add header data
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'EQUALIZATION FUND MARGINALIZED AREAS REPORT')
            ->setCellValue('A2', '1st MARGINALIZED SCHEDULE')
            ->setCellValue('A3', 'Report by: FiscalBridge Portal - ICTS - JKM, CISA')
            ->setCellValue('A4', 'Date: ' . date('F d, Y'));
        
        // Style the header
        $spreadsheet->getActiveSheet()->getStyle('A1:A4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $spreadsheet->getActiveSheet()->getStyle('A3:A4')->getFont()->setSize(10);
        
        // Merge cells for title
        $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
        $spreadsheet->getActiveSheet()->mergeCells('A2:H2');
        $spreadsheet->getActiveSheet()->mergeCells('A3:H3');
        $spreadsheet->getActiveSheet()->mergeCells('A4:H4');
        
        // Center align header
        $spreadsheet->getActiveSheet()->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set row height for header
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(15);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(15);
        
        // Add empty row
        $spreadsheet->getActiveSheet()->mergeCells('A5:H5');
        
        // Starting row for data
        $startRow = 6;
        
        // Get custom report parameters from request
        $county = $request->get('county', '');
        $sector = $request->get('sector', '');
        $minCompletion = $request->get('min_completion', 0);
        $maxCompletion = $request->get('max_completion', 100);
        
        // Build query based on parameters
        $query = EqualizationFundProject::find()
            ->where(['in', 'county', $marginalizedCounties]);
        
        if (!empty($county)) {
            $query->andWhere(['county' => $county]);
        }
        
        if (!empty($sector)) {
            $query->andWhere(['sector' => $sector]);
        }
        
        $query->andWhere(['>=', 'percent_completion', $minCompletion]);
        $query->andWhere(['<=', 'percent_completion', $maxCompletion]);
        
        $projects = $query->asArray()->all();
        
        // Add column headers
        $spreadsheet->getActiveSheet()
            ->setCellValue('A' . $startRow, '#')
            ->setCellValue('B' . $startRow, 'Project Name')
            ->setCellValue('C' . $startRow, 'County')
            ->setCellValue('D' . $startRow, 'Constituency')
            ->setCellValue('E' . $startRow, 'Sector')
            ->setCellValue('F' . $startRow, 'Budget (KES)')
            ->setCellValue('G' . $startRow, 'Contract Sum (KES)')
            ->setCellValue('H' . $startRow, 'Completion (%)');
        
        // Style header row
        $spreadsheet->getActiveSheet()->getStyle('A' . $startRow . ':H' . $startRow)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A' . $startRow . ':H' . $startRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1B5E20');
        $spreadsheet->getActiveSheet()->getStyle('A' . $startRow . ':H' . $startRow)->getFont()->getColor()->setARGB('FFFFFFFF');
        
        // Add borders to header row
        $spreadsheet->getActiveSheet()->getStyle('A' . $startRow . ':H' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Starting row for data
        $dataRow = $startRow + 1;
        $counter = 1;
        $totalBudget = 0;
        $totalContractSum = 0;
        
        // Add data rows
        foreach ($projects as $project) {
            $projectName = $project['project_name'] ?? 'Unnamed Project';
            $county = $project['county'] ?? 'Unknown';
            $constituency = $project['constituency'] ?? 'Unknown';
            $sector = $project['sector'] ?? 'Not Specified';
            $budget = $project['budget_2018_19'] ?? 0;
            $contractSum = $project['contract_sum'] ?? 0;
            $completion = $project['percent_completion'] ?? 0;
            
            $spreadsheet->getActiveSheet()
                ->setCellValue('A' . $dataRow, $counter++)
                ->setCellValue('B' . $dataRow, $projectName)
                ->setCellValue('C' . $dataRow, $county)
                ->setCellValue('D' . $dataRow, $constituency)
                ->setCellValue('E' . $dataRow, $sector)
                ->setCellValue('F' . $dataRow, $budget)
                ->setCellValue('G' . $dataRow, $contractSum)
                ->setCellValue('H' . $dataRow, $completion);
            
            // Add borders to data row
            $spreadsheet->getActiveSheet()->getStyle('A' . $dataRow . ':H' . $dataRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Format currency columns
            $spreadsheet->getActiveSheet()->getStyle('F' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $spreadsheet->getActiveSheet()->getStyle('G' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.00');
            
            // Format percentage column
            $spreadsheet->getActiveSheet()->getStyle('H' . $dataRow)->getNumberFormat()->setFormatCode('0.0');
            
            $totalBudget += $budget;
            $totalContractSum += $contractSum;
            
            $dataRow++;
        }
        
        // Add total row
        $spreadsheet->getActiveSheet()
            ->setCellValue('A' . $dataRow, 'TOTAL')
            ->setCellValue('F' . $dataRow, $totalBudget)
            ->setCellValue('G' . $dataRow, $totalContractSum);
        
        // Style total row
        $spreadsheet->getActiveSheet()->getStyle('A' . $dataRow . ':H' . $dataRow)->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A' . $dataRow . ':H' . $dataRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE8F5E9');
        
        // Add borders to total row
        $spreadsheet->getActiveSheet()->getStyle('A' . $dataRow . ':H' . $dataRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Format currency columns in total row
        $spreadsheet->getActiveSheet()->getStyle('F' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('G' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set active sheet index to the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        
        // Redirect output to a client's web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Equalization_Fund_Marginalized_Report.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Get list of marginalized counties
     * This should be customized based on your specific definition of marginalized counties
     * @return array
     */
    private function getMarginalizedCounties()
    {
        // This is a sample list of marginalized counties in Kenya
        // You should customize this based on your specific requirements
        return [
            'Mandera', 'Wajir', 'Garissa', 'Marsabit', 'Isiolo', 
            'Turkana', 'West Pokot', 'Samburu', 'Baringo', 'Laikipia',
            'Narok', 'Kajiado', 'Tana River', 'Lamu', 'Kilifi',
            'Kwale', 'Taita Taveta', 'Kitui', 'Makueni', 'Machakos'
        ];
    }

    /**
     * Generate HTML for the county summary report
     */
    private function generateCountySummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion)
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
                font-size: 10px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($overallAvgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                        <th>Avg. Completion</th>
                        <th>Completion Progress</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                        <td>' . number_format($row['avg_completion'], 1) . '%</td>
                        <td>
                            <div class="completion-bar">
                                <div class="completion-fill" style="width: ' . $row['avg_completion'] . '%"></div>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>' . number_format($overallAvgCompletion, 1) . '%</strong></td>
                        <td>
                            <div class="completion-bar">
                                <div class="completion-fill" style="width: ' . $overallAvgCompletion . '%"></div>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the sector summary report
     */
    private function generateSectorSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion)
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
                font-size: 10px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
                        <div class="stat-value">' . number_format($overallAvgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sector</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                        <th>Avg. Completion</th>
                        <th>Completion Progress</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['sector'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                        <td>' . number_format($row['avg_completion'], 1) . '%</td>
                        <td>
                            <div class="completion-bar">
                                <div class="completion-fill" style="width: ' . $row['avg_completion'] . '%"></div>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>' . number_format($overallAvgCompletion, 1) . '%</strong></td>
                        <td>
                            <div class="completion-bar">
                                <div class="completion-fill" style="width: ' . $overallAvgCompletion . '%"></div>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the constituency summary report
     */
    private function generateConstituencySummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion, $countyData)
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
                font-size: 10px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
                        <div class="stat-value">' . count($countyData) . '</div>
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($overallAvgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
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
            $countyConstituenciesCount = $countyData[$county]['constituencies'];
            
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
                            <th>Avg. Completion</th>
                            <th>Completion Progress</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $counter = 1;
            foreach ($constituencies as $constituency) {
                $html .= '
                        <tr>
                            <td>' . $counter++ . '</td>
                            <td>' . $constituency['constituency'] . '</td>
                            <td>' . number_format($constituency['project_count']) . '</td>
                            <td>KES ' . number_format($constituency['total_budget'], 2) . '</td>
                            <td>' . number_format($constituency['avg_completion'], 1) . '%</td>
                            <td>
                                <div class="completion-bar">
                                    <div class="completion-fill" style="width: ' . $constituency['avg_completion'] . '%"></div>
                                </div>
                            </td>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the detailed report
     */
    private function generateDetailedReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget, $avgCompletion)
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
                font-size: 10px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
                        <div class="stat-value">' . number_format(count($data)) . '</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">KES ' . number_format($totalBudget, 2) . '</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($avgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
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
                        <th>Sector</th>
                        <th>Budget</th>
                        <th>Contract Sum</th>
                        <th>Completion</th>
                        <th>Funding Source</th>
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
                        <td>' . $row['sector'] . '</td>
                        <td>KES ' . number_format($row['budget'], 2) . '</td>
                        <td>KES ' . number_format($row['contract_sum'], 2) . '</td>
                        <td>
                            ' . number_format($row['completion'], 1) . '%
                            <div class="completion-bar">
                                <div class="completion-fill" style="width: ' . $row['completion'] . '%"></div>
                            </div>
                        </td>
                        <td>' . $row['funding_source'] . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="5"><strong>Grand Total</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>' . number_format($avgCompletion, 1) . '%</strong></td>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the financial summary report
     */
    private function generateFinancialSummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $totalContractSum, $overallVariance, $overallAvgCompletion)
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
                font-size: 10px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
                        <div class="stat-value">KES ' . number_format($totalContractSum, 2) . '</div>
                        <div class="stat-label">Total Contract Sum</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value ' . ($overallVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            KES ' . number_format($overallVariance, 2) . '
                        </div>
                        <div class="stat-label">Variance</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($overallAvgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                        <th>Total Contract Sum</th>
                        <th>Variance</th>
                        <th>Avg. Completion</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $varianceClass = $row['variance'] >= 0 ? 'variance-positive' : 'variance-negative';
            
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                        <td>KES ' . number_format($row['total_contract_sum'], 2) . '</td>
                        <td class="' . $varianceClass . '">KES ' . number_format($row['variance'], 2) . '</td>
                        <td>' . number_format($row['avg_completion'], 1) . '%</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>KES ' . number_format($totalContractSum, 2) . '</strong></td>
                        <td class="' . ($overallVariance >= 0 ? 'variance-positive' : 'variance-negative') . '">
                            <strong>KES ' . number_format($overallVariance, 2) . '</strong>
                        </td>
                        <td><strong>' . number_format($overallAvgCompletion, 1) . '%</strong></td>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the performance report
     */
    private function generatePerformanceReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion, $overallPerformanceRating)
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
                font-size: 10px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
                        <div class="stat-value">' . number_format($overallAvgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . $overallPerformanceRating . '</div>
                        <div class="stat-label">Overall Performance</div>
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                        <th>Avg. Completion</th>
                        <th>Performance Rating</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td><strong>' . $row['county'] . '</strong></td>
                        <td>' . number_format($row['project_count']) . '</td>
                        <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                        <td>' . number_format($row['avg_completion'], 1) . '%</td>
                        <td>
                            <span class="performance-indicator ' . $row['performance_class'] . '"></span>
                            ' . $row['performance_rating'] . '
                        </td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2"><strong>Grand Total</strong></td>
                        <td><strong>' . number_format($totalProjects) . '</strong></td>
                        <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                        <td><strong>' . number_format($overallAvgCompletion, 1) . '%</strong></td>
                        <td><strong>' . $overallPerformanceRating . '</strong></td>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the trend analysis report
     */
    private function generateTrendAnalysisReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget, $overallAvgCompletion)
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
                font-size: 10px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
                        <div class="stat-label">Counties</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . number_format($overallAvgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-title">Project Budget Distribution by County (in Millions KES)</div>
                <div class="chart">';
    
        // Find max value for scaling
        $maxValue = 0;
        foreach ($data as $item) {
            if ($item['total_budget'] > $maxValue) {
                $maxValue = $item['total_budget'];
            }
        }
        
        // Generate bars
        foreach ($data as $item) {
            $height = $maxValue > 0 ? ($item['total_budget'] / $maxValue) * 150 : 0;
            $html .= '
                <div class="bar" style="height: ' . $height . 'px;">
                    <div class="bar-value">' . number_format($item['total_budget'] / 1000000, 1) . 'M</div>
                    <div class="bar-label">' . substr($item['county'], 0, 8) . '</div>
                </div>';
        }
    
        $html .= '
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">County Performance Trends</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                        <th>Avg. Completion</th>
                        <th>Completion Progress</th>
                    </tr>
                </thead>
                <tbody>';
    
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                <tr>
                    <td>' . $counter++ . '</td>
                    <td><strong>' . $row['county'] . '</strong></td>
                    <td>' . number_format($row['project_count']) . '</td>
                    <td>KES ' . number_format($row['total_budget'], 2) . '</td>
                    <td>' . number_format($row['avg_completion'], 1) . '%</td>
                    <td>
                        <div class="completion-bar">
                            <div class="completion-fill" style="width: ' . $row['avg_completion'] . '%"></div>
                        </div>
                    </td>
                </tr>';
        }

        $html .= '
                <tr class="grand-total">
                    <td colspan="2"><strong>Grand Total</strong></td>
                    <td><strong>' . number_format($totalProjects) . '</strong></td>
                    <td><strong>KES ' . number_format($totalBudget, 2) . '</strong></td>
                    <td><strong>' . number_format($overallAvgCompletion, 1) . '%</strong></td>
                    <td>
                        <div class="completion-bar">
                            <div class="completion-fill" style="width: ' . $overallAvgCompletion . '%"></div>
                        </div>
                    </td>
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
    $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

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
                font-size: 10px;
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
                    This report provides a comprehensive overview of the Equalization Fund projects in marginalized areas, 
                    including key statistics, financial performance, and progress. The Equalization Fund aims to address 
                    regional imbalances in Kenya by providing financial resources to marginalized counties and regions.
                </div>
            </div>
            
            <div class="key-stats">
                <div class="stat-card">
                    <div class="stat-value">' . number_format($data['totalProjects']) . '</div>
                    <div class="stat-label">Total Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">KES ' . number_format($data['totalBudget'], 2) . '</div>
                    <div class="stat-label">Total Budget</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">' . number_format($data['avgCompletion'], 1) . '%</div>
                    <div class="stat-label">Avg. Completion</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">' . count($data['marginalizedCounties']) . '</div>
                    <div class="stat-label">Marginalized Counties</div>
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
                                    <td>KES ' . number_format($data['totalBudget'], 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Total Contract Sum</td>
                                    <td>KES ' . number_format($data['totalContractSum'], 2) . '</td>
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
                                    <td>KES ' . number_format($data['totalBudget'] / $data['totalProjects'], 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Average Contract Sum per Project</td>
                                    <td>KES ' . number_format($data['totalContractSum'] / $data['totalProjects'], 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Projects per County</td>
                                    <td>' . number_format($data['totalProjects'] / count($data['marginalizedCounties']), 1) . '</td>
                                </tr>
                                <tr>
                                    <td>Budget Utilization</td>
                                    <td>' . number_format(($data['totalContractSum'] / $data['totalBudget']) * 100, 1) . '%</td>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }

    /**
     * Generate HTML for the custom report
     */
    private function generateCustomReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalBudget, $avgCompletion, $countyData, $sectorData, $filterInfo)
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
                font-size: 10px;
                color: #555;
                margin-bottom: 20px;
            }
            
            .filter-info {
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
            
            .filter-details {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }
            
            .filter-item {
                background-color: #e8f5e9;
                padding: 8px 15px;
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
            
            .completion-bar {
                height: 20px;
                background-color: #e0e0e0;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 5px;
            }
            
            .completion-fill {
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
            
            <div class="filter-info">
                <div class="filter-title">Report Filters</div>
                <div class="filter-details">';
        
        foreach ($filterInfo as $filter) {
            $html .= '<div class="filter-item">' . $filter . '</div>';
        }
        
        $html .= '
                </div>
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
                        <div class="stat-value">' . number_format($avgCompletion, 1) . '%</div>
                        <div class="stat-label">Avg. Completion</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">' . count($countyData) . '</div>
                        <div class="stat-label">Counties</div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">County Breakdown</div>
                <table>
                    <thead>
                        <tr>
                            <th>County</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>Avg. Completion</th>
                            <th>Completion Progress</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($countyData as $county => $stats) {
            $html .= '
                <tr>
                    <td><strong>' . $county . '</strong></td>
                    <td>' . number_format($stats['project_count']) . '</td>
                    <td>KES ' . number_format($stats['total_budget'], 2) . '</td>
                    <td>' . number_format($stats['avg_completion'], 1) . '%</td>
                    <td>
                        <div class="completion-bar">
                            <div class="completion-fill" style="width: ' . $stats['avg_completion'] . '%"></div>
                        </div>
                    </td>
                </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <div class="section-title">Sector Breakdown</div>
                <table>
                    <thead>
                        <tr>
                            <th>Sector</th>
                            <th>Project Count</th>
                            <th>Total Budget</th>
                            <th>Avg. Completion</th>
                            <th>Completion Progress</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($sectorData as $sector => $stats) {
            $html .= '
                <tr>
                    <td><strong>' . $sector . '</strong></td>
                    <td>' . number_format($stats['project_count']) . '</td>
                    <td>KES ' . number_format($stats['total_budget'], 2) . '</td>
                    <td>' . number_format($stats['avg_completion'], 1) . '%</td>
                    <td>
                        <div class="completion-bar">
                            <div class="completion-fill" style="width: ' . $stats['avg_completion'] . '%"></div>
                        </div>
                    </td>
                </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <div class="section-title">Project Details</div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project Name</th>
                            <th>County</th>
                            <th>Constituency</th>
                            <th>Sector</th>
                            <th>Budget</th>
                            <th>Contract Sum</th>
                            <th>Completion</th>
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
                    <td>' . $row['sector'] . '</td>
                    <td>KES ' . number_format($row['budget'], 2) . '</td>
                    <td>KES ' . number_format($row['contract_sum'], 2) . '</td>
                    <td>
                        ' . number_format($row['completion'], 1) . '%
                        <div class="completion-bar">
                            <div class="completion-fill" style="width: ' . $row['completion'] . '%"></div>
                        </div>
                    </td>
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
        $html .= '<div class="footer">Equalization Fund Marginalized Areas Management System</div>';

        return $html;
    }
}
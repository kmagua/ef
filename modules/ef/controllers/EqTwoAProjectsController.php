<?php

namespace app\modules\ef\controllers;

use app\modules\ef\models\EqualizationTwoProjects;
use app\modules\ef\models\EqualizationTwoProjectsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * EqTwoProjectsController implements the CRUD actions for EqualizationTwoProjects model.
 */
class EqTwoProjectsController extends Controller
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
                                'create', 'analytics','report', 'allocation-report', 
                                'visualization', 'update', 'view', 
                                'index', 'card', 'summaries', 'per-county', 'county-summary', 
                                'sector-summary', 'marginalised-summary', 
                                'detailed-projects-report'
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
     * Lists all EqualizationTwoProjects models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EqualizationTwoProjectsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays analytics dashboard for Equalization Two Projects.
     *
     * @return string
     */
    public function actionAnalytics()
    {
        // Summary statistics
        $totalProjects = EqualizationTwoProjects::find()->count();
        $totalFunding = EqualizationTwoProjects::find()->sum('project_budget');
        
        // Projects by county
        $countyProjects = (new Query())
            ->select(['county', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('county')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        // Projects by constituency
        $constituencyProjects = (new Query())
            ->select(['constituency', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('constituency')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        // Projects by ward
        $wardProjects = (new Query())
            ->select(['ward', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('ward')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        // Projects by marginalised area
        $marginalisedProjects = (new Query())
            ->select(['marginalised_area', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->where(['not', ['marginalised_area' => '']])
            ->groupBy('marginalised_area')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        // Projects by sector
        $sectorProjects = (new Query())
            ->select(['sector', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('sector')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        // Top projects by budget
        $topProjects = EqualizationTwoProjects::find()
            ->orderBy(['project_budget' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('analytics', [
            'totalProjects' => $totalProjects,
            'totalFunding' => $totalFunding,
            'countyProjects' => $countyProjects,
            'constituencyProjects' => $constituencyProjects,
            'wardProjects' => $wardProjects,
            'marginalisedProjects' => $marginalisedProjects,
            'sectorProjects' => $sectorProjects,
            'topProjects' => $topProjects,
        ]);
    }

    /**
     * Displays a single EqualizationTwoProjects model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EqualizationTwoProjects model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EqualizationTwoProjects();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EqualizationTwoProjects model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EqualizationTwoProjects model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EqualizationTwoProjects model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EqualizationTwoProjects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EqualizationTwoProjects::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Generate a comprehensive report with allocation data
     * @return mixed
     */
    public function actionReport()
    {
        // Ensure the logo is correctly loaded using base64 encoding
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        // Header information
        $reportBy = 'Report by: FiscalBridge Information System - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO PROJECTS REPORT';
        $reportDate = date('F d, Y');

        // Fetch data from models
        $projects = EqualizationTwoProjects::find()->asArray()->all();

        // Initialize arrays for totals
        $countyAllocations = [];

        // Sum all allocations per county
        foreach ($projects as $project) {
            $county = strtoupper($project['county']);
            $allocation = $project['project_budget'] ?? 0;

            if (!isset($countyAllocations[$county])) {
                $countyAllocations[$county] = 0;
            }
            $countyAllocations[$county] += $allocation;
        }

        // Prepare data for the report
        $data = [];
        $totalAllocation = 0;

        foreach ($countyAllocations as $county => $totalAlloc) {
            $data[] = [
                'county' => $county,
                'total_allocation' => $totalAlloc,
            ];

            // Calculate overall totals
            $totalAllocation += $totalAlloc;
        }

        $html = $this->generateReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalAllocation);

        // Initialize Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Poppins');
        $options->set('isRemoteEnabled', true); // Allows loading images
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Stream the PDF for download
        $dompdf->stream("Equalization_Two_Projects_Report.pdf", ["Attachment" => true]);
        return;
    }

    /**
     * Generate allocation report
     * @return mixed
     */
    public function actionAllocationReport()
    {
        // Load and encode the logo image (using base64)
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO PROJECTS ALLOCATION REPORT';
        $reportDate = date('F d, Y');
        
        $projects = EqualizationTwoProjects::find()->asArray()->all();

        $countyAllocations = [];
        $countyCounts = [];
        foreach ($projects as $project) {
            $county = strtoupper($project['county']);
            $allocation = $project['project_budget'] ?? 0;
            if (!isset($countyAllocations[$county])) {
                $countyAllocations[$county] = 0;
                $countyCounts[$county] = 0;
            }
            $countyAllocations[$county] += $allocation;
            $countyCounts[$county] += 1;
        }

        $data = [];
        $overallTotal = 0;
        $overallCount = 0;
        foreach ($countyAllocations as $county => $totalAllocation) {
            $count = $countyCounts[$county];
            $average = ($count > 0) ? $totalAllocation / $count : 0;
            $data[] = [
                'county' => $county,
                'total_allocation' => $totalAllocation,
                'project_count' => $count,
                'average_allocation' => $average,
            ];
            $overallTotal += $totalAllocation;
            $overallCount += $count;
        }
        $overallAverage = ($overallCount > 0) ? $overallTotal / $overallCount : 0;

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
     * Generate county summary report
     * @return mixed
     */
    public function actionCountySummary()
    {
        // Load and encode the logo image (using base64)
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO PROJECTS COUNTY SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get projects grouped by county
        $countyData = (new Query())
            ->select([
                'county', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy('county')
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;

        foreach ($countyData as $county) {
            $countyName = strtoupper($county['county']);
            $projectCount = $county['project_count'] ?? 0;
            $budget = $county['total_budget'] ?? 0;

            $data[] = [
                'county' => $countyName,
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
     * Generate sector summary report
     * @return mixed
     */
    public function actionSectorSummary()
    {
        // Load and encode the logo image (using base64)
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO PROJECTS SECTOR SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get projects grouped by sector
        $sectorData = (new Query())
            ->select([
                'sector', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->groupBy('sector')
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;

        foreach ($sectorData as $sector) {
            $sectorName = strtoupper($sector['sector']);
            $projectCount = $sector['project_count'] ?? 0;
            $budget = $sector['total_budget'] ?? 0;

            $data[] = [
                'sector' => $sectorName,
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
     * Generate marginalised areas summary report
     * @return mixed
     */
    public function actionMarginalisedSummary()
    {
        // Load and encode the logo image (using base64)
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO PROJECTS MARGINALISED AREAS SUMMARY REPORT';
        $reportDate = date('F d, Y');
        
        // Get projects grouped by marginalised area
        $marginalisedData = (new Query())
            ->select([
                'marginalised_area', 
                'COUNT(*) as project_count', 
                'SUM(project_budget) as total_budget'
            ])
            ->from('eq2_projects')
            ->where(['not', ['marginalised_area' => '']])
            ->groupBy('marginalised_area')
            ->all();

        // Prepare data for the report
        $data = [];
        $totalProjects = 0;
        $totalBudget = 0;

        foreach ($marginalisedData as $marginalised) {
            $areaName = strtoupper($marginalised['marginalised_area']);
            $projectCount = $marginalised['project_count'] ?? 0;
            $budget = $marginalised['total_budget'] ?? 0;

            $data[] = [
                'marginalised_area' => $areaName,
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
     * Generate detailed projects report
     * @return mixed
     */
    public function actionDetailedProjectsReport()
    {
        // Load and encode the logo image (using base64)
        $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $logoUrl = 'data:image/png;base64,' . $imageData;
        } else {
            $logoUrl = '';
        }

        $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';
        $reportTitle = 'EQUALIZATION TWO PROJECTS DETAILED REPORT';
        $reportDate = date('F d, Y');
        
        // Get all projects with their details
        $projects = EqualizationTwoProjects::find()->asArray()->all();

        // Prepare data for the report
        $data = [];
        $totalBudget = 0;

        foreach ($projects as $project) {
            $projectName = $project['project_name'];
            $county = $project['county'];
            $constituency = $project['constituency'];
            $ward = $project['ward'];
            $marginalisedArea = $project['marginalised_area'];
            $sector = $project['sector'];
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
     * Display projects in card view
     * @return string
     */
    public function actionCard()
    {
        $searchModel = new EqualizationTwoProjectsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('card', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Display project summaries
     * @return string
     */
    public function actionSummaries()
    {
        // Summary statistics
        $totalProjects = EqualizationTwoProjects::find()->count();
        $totalFunding = EqualizationTwoProjects::find()->sum('project_budget');

        // Projects by sector
        $sectorProjects = (new Query())
            ->select(['sector', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('sector')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        // Projects by county
        $countyProjects = (new Query())
            ->select(['county', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('county')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        return $this->render('summaries', [
            'totalProjects' => $totalProjects,
            'totalFunding' => $totalFunding,
            'sectorProjects' => $sectorProjects,
            'countyProjects' => $countyProjects,
        ]);
    }

    /**
     * Display projects per county
     * @return string
     */
    public function actionPerCounty()
    {
        $county = Yii::$app->request->get('county');
        
        $query = EqualizationTwoProjects::find();
        
        if ($county) {
            $query->where(['county' => $county]);
        }
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        // Get list of counties for filter dropdown
        $counties = (new Query())
            ->select(['county'])
            ->from('eq2_projects')
            ->groupBy('county')
            ->orderBy('county')
            ->column();

        return $this->render('per-county', [
            'dataProvider' => $dataProvider,
            'counties' => $counties,
            'selectedCounty' => $county,
        ]);
    }

    /**
     * Display data visualizations
     * @return string
     */
    public function actionVisualization()
    {
        // Projects by county
        $countyData = (new Query())
            ->select(['county', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('county')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        // Projects by sector
        $sectorData = (new Query())
            ->select(['sector', 'COUNT(*) AS count', 'SUM(project_budget) AS total_cost'])
            ->from('eq2_projects')
            ->groupBy('sector')
            ->orderBy(['count' => SORT_DESC])
            ->all();

        return $this->render('visualization', [
            'countyData' => $countyData,
            'sectorData' => $sectorData,
        ]);
    }

    /**
     * Generate HTML for the main report
     */
    private function generateReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalAllocation)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 12px;
                color: #555;
                margin-bottom: 20px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 12px; 
                margin-bottom: 20px;
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: center; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: bold; 
            }
            
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 40px;
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
                margin-top: 20px;
            }
            
            @page {
                margin: 20px;
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
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Total Allocation</th>
                    </tr>
                </thead>
                <tbody>';

        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $row['county'] . '</td>
                        <td>' . number_format($row['total_allocation'], 2) . '</td>
                    </tr>';
        }

        // Add Total Row
        $html .= '
                    <tr class="grand-total">
                        <td colspan="2">Grand Total</td>
                        <td>' . number_format($totalAllocation, 2) . '</td>
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
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 12px;
                color: #555;
                margin-bottom: 20px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 12px; 
                margin-bottom: 20px;
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: center; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: bold; 
            }
            
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 40px;
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
                margin-top: 20px;
            }
            
            @page {
                margin: 20px;
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
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Total Allocation</th>
                        <th>Project Count</th>
                        <th>Average Allocation</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $row['county'] . '</td>
                        <td>' . number_format($row['total_allocation'], 2) . '</td>
                        <td>' . $row['project_count'] . '</td>
                        <td>' . number_format($row['average_allocation'], 2) . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2">Grand Total</td>
                        <td>' . number_format($overallTotal, 2) . '</td>
                        <td>' . $overallCount . '</td>
                        <td>' . number_format($overallAverage, 2) . '</td>
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
     * Generate HTML for the county summary report
     */
    private function generateCountySummaryReportHtml($logoUrl, $reportTitle, $reportBy, $reportDate, $data, $totalProjects, $totalBudget)
    {
        $html = '
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");
            
            body { 
                font-family: "Poppins", sans-serif; 
                margin: 0;
                padding: 0;
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 12px;
                color: #555;
                margin-bottom: 20px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 12px; 
                margin-bottom: 20px;
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: center; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: bold; 
            }
            
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 40px;
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
                margin-top: 20px;
            }
            
            @page {
                margin: 20px;
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
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>County</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $row['county'] . '</td>
                        <td>' . $row['project_count'] . '</td>
                        <td>' . number_format($row['total_budget'], 2) . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2">Grand Total</td>
                        <td>' . $totalProjects . '</td>
                        <td>' . number_format($totalBudget, 2) . '</td>
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
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 12px;
                color: #555;
                margin-bottom: 20px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 12px; 
                margin-bottom: 20px;
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: center; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: bold; 
            }
            
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 40px;
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
                margin-top: 20px;
            }
            
            @page {
                margin: 20px;
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
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sector</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $row['sector'] . '</td>
                        <td>' . $row['project_count'] . '</td>
                        <td>' . number_format($row['total_budget'], 2) . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2">Grand Total</td>
                        <td>' . $totalProjects . '</td>
                        <td>' . number_format($totalBudget, 2) . '</td>
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
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 12px;
                color: #555;
                margin-bottom: 20px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 12px; 
                margin-bottom: 20px;
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: center; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: bold; 
            }
            
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 40px;
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
                margin-top: 20px;
            }
            
            @page {
                margin: 20px;
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
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Marginalised Area</th>
                        <th>Project Count</th>
                        <th>Total Budget</th>
                    </tr>
                </thead>
                <tbody>';
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= '
                    <tr>
                        <td>' . $counter++ . '</td>
                        <td>' . $row['marginalised_area'] . '</td>
                        <td>' . $row['project_count'] . '</td>
                        <td>' . number_format($row['total_budget'], 2) . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="2">Grand Total</td>
                        <td>' . $totalProjects . '</td>
                        <td>' . number_format($totalBudget, 2) . '</td>
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
            }
            
            .container {
                width: 100%;
                margin: 0 auto;
            }
            
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .logo img {
                max-width: 400px;
                height: auto;
            }
            
            .title {
                font-size: 24px;
                font-weight: 700;
                color: #1b5e20;
                margin-bottom: 5px;
            }
            
            .flag-bar {
                width: 100%;
                height: 10px;
                background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%);
                margin-bottom: 15px;
            }
            
            .report-info {
                font-size: 12px;
                color: #555;
                margin-bottom: 20px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                font-size: 12px; 
                margin-bottom: 20px;
            }
            
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: center; 
            }
            
            th { 
                background-color: #1b5e20; 
                color: white; 
                font-weight: bold; 
            }
            
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            
            .grand-total { 
                font-weight: bold; 
                background-color: #e8f5e9; 
            }
            
            .signature {
                margin-top: 40px;
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
                margin-top: 20px;
            }
            
            @page {
                margin: 20px;
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
                        <td>' . number_format($row['budget'], 2) . '</td>
                    </tr>';
        }

        $html .= '
                    <tr class="grand-total">
                        <td colspan="7">Grand Total</td>
                        <td>' . number_format($totalBudget, 2) . '</td>
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
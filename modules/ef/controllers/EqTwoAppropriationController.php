<?php

namespace app\modules\ef\controllers;

use app\modules\ef\models\EqualizationTwoAppropriation;
use app\modules\ef\models\EqualizationTwoAppropriationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * EqTwoAppropriationController implements the CRUD actions for EqualizationTwoAppropriation model.
 */
class EqTwoAppropriationController extends Controller
{
    /**
     * @inheritDoc
     */
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
                    //'only' => ['index', ],
                    'rules' => [
                        [
                            'actions' => ['create','insights', 'analytics','allocation-report','disbursement-report','sector-disbursements-per-county','visualization','update', 'view', 'index', 'card', 'summaries', 'per-county'],
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
     * Lists all EqualizationTwoAppropriation models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EqualizationTwoAppropriationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EqualizationTwoAppropriation model.
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
 public function actionGetConstituencies($county)
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    return EqualizationTwoAppropriation::find()
        ->select('constituency')
        ->where(['county' => $county])
        ->distinct()
        ->orderBy('constituency')
        ->column();
}
public function actionInsights()
{
    // Load appropriation records
    $records = \app\modules\ef\models\EqualizationTwoAppropriation::find()->all();

    // COUNTY TOTALS
    $countyTotals = [];
    foreach ($records as $r) {
        $countyTotals[$r->county] =
            ($countyTotals[$r->county] ?? 0) + floatval($r->allocation_ksh);
    }

    // CONSTITUENCY TOTALS  (since appropriation has no sector)
    $constituencyTotals = [];
    foreach ($records as $r) {
        $constituencyTotals[$r->constituency] =
            ($constituencyTotals[$r->constituency] ?? 0) + floatval($r->allocation_ksh);
    }

    // YEAR TOTALS
    $yearTotals = [];
    foreach ($records as $r) {
        $yearTotals[$r->financial_year] =
            ($yearTotals[$r->financial_year] ?? 0) + floatval($r->allocation_ksh);
    }

    return $this->render('insights', [
        'countyTotals'       => $countyTotals,
        'constituencyTotals' => $constituencyTotals,
        'yearTotals'         => $yearTotals,
    ]);
}

public function actionGetWards($county, $constituency)
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    return EqualizationTwoAppropriation::find()
        ->select('ward')
        ->where(['county' => $county, 'constituency' => $constituency])
        ->distinct()
        ->orderBy('ward')
        ->column();
}
   
    
    
public function actionAnalytics()
{
    $rows = \app\modules\ef\models\EqualizationTwoAppropriation::find()->asArray()->all();
    
    // Initialize analytics arrays
    $countyTotals = [];
    $yearTotals = [];
    $constituencyTotals = [];
    $wardTotals = [];
    $marginalisedCounts = [];
    $marginalisedTotals = []; // New: Total allocation per marginalized area
    $topAllocations = [];
    $wardAllocations = [];
    $constituencyAllocations = []; // New: For constituency-level analysis
    $yearlyGrowth = []; // New: Year-over-year growth analysis
    $countyWardCount = []; // New: Number of wards per county
    $countyConstituencyCount = []; // New: Number of constituencies per county
    $allocationRanges = [ // New: Allocation distribution ranges
        '0-5M' => 0,
        '5M-10M' => 0,
        '10M-15M' => 0,
        '15M+' => 0
    ];
    $financialYears = []; // New: All unique financial years
    $countyYearlyData = []; // New: County data by year for trend analysis

    foreach ($rows as $r) {
        $county = strtoupper(trim($r['county'] ?? 'Unknown'));
        $constituency = strtoupper(trim($r['constituency'] ?? 'Unknown'));
        $ward = strtoupper(trim($r['ward'] ?? 'Unknown'));
        $year = trim($r['financial_year'] ?? 'Unknown');
        $margArea = strtoupper(trim($r['marginalised_areas'] ?? 'Unknown'));
        $allocation = (float)($r['allocation_ksh'] ?? 0);
        
        // Track unique financial years
        if (!in_array($year, $financialYears)) {
            $financialYears[] = $year;
        }
        
        // County totals
        if (!isset($countyTotals[$county])) {
            $countyTotals[$county] = 0;
            $countyWardCount[$county] = [];
            $countyConstituencyCount[$county] = [];
        }
        $countyTotals[$county] += $allocation;
        $countyWardCount[$county][$ward] = true;
        $countyConstituencyCount[$county][$constituency] = true;
        
        // County data by year for trend analysis
        if (!isset($countyYearlyData[$county][$year])) {
            $countyYearlyData[$county][$year] = 0;
        }
        $countyYearlyData[$county][$year] += $allocation;
        
        // Year totals
        if (!isset($yearTotals[$year])) {
            $yearTotals[$year] = 0;
        }
        $yearTotals[$year] += $allocation;
        
        // Constituency totals
        $constituencyKey = $county . '|' . $constituency; // Composite key for uniqueness
        if (!isset($constituencyTotals[$constituencyKey])) {
            $constituencyTotals[$constituencyKey] = 0;
            $constituencyAllocations[$constituencyKey] = [
                'county' => $county,
                'constituency' => $constituency,
                'total' => 0
            ];
        }
        $constituencyTotals[$constituencyKey] += $allocation;
        $constituencyAllocations[$constituencyKey]['total'] += $allocation;
        
        // Ward totals
        $wardKey = $county . '|' . $constituency . '|' . $ward; // Composite key for uniqueness
        if (!isset($wardTotals[$wardKey])) {
            $wardTotals[$wardKey] = 0;
            $wardAllocations[$wardKey] = [
                'county' => $county,
                'constituency' => $constituency,
                'ward' => $ward,
                'total' => 0
            ];
        }
        $wardTotals[$wardKey] += $allocation;
        $wardAllocations[$wardKey]['total'] += $allocation;
        
        // Marginalized areas analysis
        if (!isset($marginalisedCounts[$margArea])) {
            $marginalisedCounts[$margArea] = 0;
            $marginalisedTotals[$margArea] = 0;
        }
        $marginalisedCounts[$margArea]++;
        $marginalisedTotals[$margArea] += $allocation;
        
        // Allocation distribution ranges
        if ($allocation < 5000000) {
            $allocationRanges['0-5M']++;
        } elseif ($allocation < 10000000) {
            $allocationRanges['5M-10M']++;
        } elseif ($allocation < 15000000) {
            $allocationRanges['10M-15M']++;
        } else {
            $allocationRanges['15M+']++;
        }
        
        // Store full record for top allocations
        $topAllocations[] = [
            'county' => $county,
            'constituency' => $constituency,
            'ward' => $ward,
            'financial_year' => $year,
            'allocation' => $allocation,
            'marginalised_area' => $margArea
        ];
    }
    
    // Calculate year-over-year growth
    sort($financialYears);
    for ($i = 1; $i < count($financialYears); $i++) {
        $prevYear = $financialYears[$i-1];
        $currYear = $financialYears[$i];
        
        if (isset($yearTotals[$prevYear]) && isset($yearTotals[$currYear]) && $yearTotals[$prevYear] > 0) {
            $growth = (($yearTotals[$currYear] - $yearTotals[$prevYear]) / $yearTotals[$prevYear]) * 100;
            $yearlyGrowth[$currYear] = [
                'growth_percent' => round($growth, 2),
                'previous_total' => $yearTotals[$prevYear],
                'current_total' => $yearTotals[$currYear]
            ];
        }
    }
    
    // Calculate county-level statistics
    foreach ($countyWardCount as $county => $wards) {
        $countyWardCount[$county] = count($wards);
    }
    
    foreach ($countyConstituencyCount as $county => $constituencies) {
        $countyConstituencyCount[$county] = count($constituencies);
    }
    
    // Calculate averages
    $countyAverages = [];
    foreach ($countyTotals as $county => $total) {
        $countyAverages[$county] = $total / ($countyWardCount[$county] ?? 1);
    }
    
    // Sort arrays for better presentation
    arsort($countyTotals);
    ksort($yearTotals);
    arsort($constituencyTotals);
    arsort($wardTotals);
    arsort($marginalisedTotals);
    
    // Sort top allocations (desc)
    usort($topAllocations, function ($a, $b) {
        return $b['allocation'] <=> $a['allocation'];
    });
    
    // Top 10 wards by allocation
    arsort($wardAllocations);
    $topWardAllocations = array_slice($wardAllocations, 0, 10, true);
    
    // Bottom 10 wards by allocation
    asort($wardAllocations);
    $bottomWardAllocations = array_slice($wardAllocations, 0, 10, true);
    
    // Top 10 constituencies by allocation
    arsort($constituencyAllocations);
    $topConstituencyAllocations = array_slice($constituencyAllocations, 0, 10, true);
    
    // Bottom 10 constituencies by allocation
    asort($constituencyAllocations);
    $bottomConstituencyAllocations = array_slice($constituencyAllocations, 0, 10, true);
    
    // Calculate overall statistics
    $totalAllocation = array_sum($yearTotals);
    $totalRecords = count($rows);
    $averageAllocation = $totalAllocation / $totalRecords;
    $medianAllocation = $this->calculateMedian($topAllocations, 'allocation');
    
    return $this->render('analytics', [
        'countyTotals' => $countyTotals,
        'yearTotals' => $yearTotals,
        'constituencyTotals' => $constituencyTotals,
        'wardTotals' => $wardTotals,
        'marginalisedCounts' => $marginalisedCounts,
        'marginalisedTotals' => $marginalisedTotals,
        'topAllocations' => $topAllocations, // Send all allocations for proper county-area mapping
        'topWardAllocations' => $topWardAllocations,
        'bottomWardAllocations' => $bottomWardAllocations,
        'topConstituencyAllocations' => $topConstituencyAllocations,
        'bottomConstituencyAllocations' => $bottomConstituencyAllocations,
        'yearlyGrowth' => $yearlyGrowth,
        'countyWardCount' => $countyWardCount,
        'countyConstituencyCount' => $countyConstituencyCount,
        'countyAverages' => $countyAverages,
        'allocationRanges' => $allocationRanges,
        'financialYears' => $financialYears,
        'countyYearlyData' => $countyYearlyData,
        'totalAllocation' => $totalAllocation,
        'totalRecords' => $totalRecords,
        'averageAllocation' => $averageAllocation,
        'medianAllocation' => $medianAllocation
    ]);
}

/**
 * Helper function to calculate median value
 */
private function calculateMedian($data, $field)
{
    $values = array_column($data, $field);
    sort($values);
    $count = count($values);
    $middle = floor($count / 2);
    
    if ($count % 2) {
        return $values[$middle];
    } else {
        return ($values[$middle - 1] + $values[$middle]) / 2;
    }
}
    /**
     * Creates a new EqualizationTwoAppropriation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EqualizationTwoAppropriation();

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
     * Updates an existing EqualizationTwoAppropriation model.
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
     * Deletes an existing EqualizationTwoAppropriation model.
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
     * Finds the EqualizationTwoAppropriation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EqualizationTwoAppropriation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EqualizationTwoAppropriation::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

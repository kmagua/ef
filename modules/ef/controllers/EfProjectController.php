<?php

namespace app\modules\ef\controllers;

use app\modules\ef\models\EqualizationFundProject;
use app\modules\ef\models\EqualizationFundProjectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\ef\models\Disbursement;
use Dompdf\Dompdf;
use Dompdf\Options;
use yii\data\ArrayDataProvider;
use app\modules\ef\models\DisbursementSearch;
use yii\filters\AccessControl;
use yii;
/**
 * EfProjectController implements the CRUD actions for EqualizationFundProject model.
 */
class EfProjectController extends Controller
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
                    //'only' => ['index', ],
                    'rules' => [
                        [
                            'actions' => ['create', 'update','visualization','county-disbursement','report', 'view','table-summary', 'index', 'card'],
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
     * Lists all EqualizationFundProject models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EqualizationFundProjectSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EqualizationFundProject model.
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
     * Creates a new EqualizationFundProject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EqualizationFundProject();

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
public function actionSectorDisbursementsPerCounty()
{
    $searchModel = new DisbursementSearch(); // Ensure this model exists
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('county-disbursement', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}
public function actionVisualization()
{
    // Fetch project and disbursement data
    $projects = EqualizationFundProject::find()->asArray()->all();
    $disbursements = Disbursement::find()->asArray()->all();

    $groupedData = [];
    $countyDisbursementData = [];
    $grandTotalAllocation = 0;
    $grandTotalDisbursed = 0;

    // Aggregate Disbursements per County
    foreach ($disbursements as $disbursement) {
        $county = strtoupper($disbursement['county']);
        if (!isset($countyDisbursementData[$county])) {
            $countyDisbursementData[$county] = 0;
        }
        $countyDisbursementData[$county] += $disbursement['amount_disbursed'];
        $grandTotalDisbursed += $disbursement['amount_disbursed'];
    }

    // Group Projects by County + Constituency
    foreach ($projects as $project) {
        $county = strtoupper($project['county']);
        $constituency = strtoupper($project['constituency']);
        $sector = strtoupper($project['sector']);
        $projectName = $project['project_name'];
        $allocation = $project['budget_2018_19'] ?? 0;

        if (!isset($groupedData[$county])) {
            $groupedData[$county] = [
                'total_allocation' => 0,
                'total_disbursed' => $countyDisbursementData[$county] ?? 0,
                'constituencies' => [],
            ];
        }

        $groupedData[$county]['total_allocation'] += $allocation;
        $grandTotalAllocation += $allocation;

        if (!isset($groupedData[$county]['constituencies'][$constituency])) {
            $groupedData[$county]['constituencies'][$constituency] = [];
        }

        $groupedData[$county]['constituencies'][$constituency][] = [
            'sector' => $sector,
            'project_name' => $projectName,
            'allocation' => $allocation,
        ];
    }

    return $this->render('visualization', [
        'groupedData' => $groupedData,
        'countyDisbursementData' => $countyDisbursementData,
        'grandTotalAllocation' => $grandTotalAllocation,
        'grandTotalDisbursed' => $grandTotalDisbursed,
    ]);
}



public function actionCountyDisbursement()
{
    // Fetch project data
    $projects = EqualizationFundProject::find()->asArray()->all();
    $disbursements = Disbursement::find()->asArray()->all();

    // Initialize variables
    $groupedData = [];
    $countyDisbursementData = [];
    $grandTotalAllocation = 0;
    $grandTotalDisbursed = 0;

    // Step 1: Aggregate Disbursements per County
    foreach ($disbursements as $disbursement) {
        $county = strtoupper($disbursement['county']);
        if (!isset($countyDisbursementData[$county])) {
            $countyDisbursementData[$county] = 0;
        }
        $countyDisbursementData[$county] += $disbursement['amount_disbursed'];
        $grandTotalDisbursed += $disbursement['amount_disbursed'];
    }

    // Step 2: Group Projects by County and Aggregate Allocations
    foreach ($projects as $project) {
        $county = strtoupper($project['county']);
        $allocation = $project['budget_2018_19'] ?? 0;

        if (!isset($groupedData[$county])) {
            $groupedData[$county] = [
                'total_allocation' => 0,
                'total_disbursed' => $countyDisbursementData[$county] ?? 0,
            ];
        }

        $groupedData[$county]['total_allocation'] += $allocation;
        $grandTotalAllocation += $allocation;
    }

    return $this->render('county-disbursement', [
        'groupedData' => $groupedData,
        'countyDisbursementData' => $countyDisbursementData,
        'grandTotalAllocation' => $grandTotalAllocation,
        'grandTotalDisbursed' => $grandTotalDisbursed,
    ]);
}

public function actionReport()
{
    // Load logo
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
    $logoUrl = file_exists($imagePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) : '';

    // Fetch project data
    $projects = EqualizationFundProject::find()->asArray()->all();
    $disbursements = Disbursement::find()->asArray()->all();

    // Data processing for grouping
    $groupedData = [];
    $countyDisbursementData = [];
    $grandTotalAllocation = 0;
    $grandTotalDisbursed = 0;

    // Aggregate disbursements per county
    foreach ($disbursements as $disbursement) {
        $county = strtoupper($disbursement['county']);
        if (!isset($countyDisbursementData[$county])) {
            $countyDisbursementData[$county] = 0;
        }
        $countyDisbursementData[$county] += $disbursement['amount_disbursed'];
    }

    // Group projects by county and constituency
    foreach ($projects as $project) {
        $county = strtoupper($project['county']);
        $constituency = strtoupper($project['constituency']);
        $sector = strtoupper($project['sector']);
        $projectName = $project['project_name'];
        $allocation = $project['budget_2018_19'] ?? 0;

        if (!isset($groupedData[$county])) {
            $groupedData[$county] = [
                'total_allocation' => 0,
                'constituencies' => [],
            ];
        }

        $groupedData[$county]['total_allocation'] += $allocation;
        $grandTotalAllocation += $allocation;

        if (!isset($groupedData[$county]['constituencies'][$constituency])) {
            $groupedData[$county]['constituencies'][$constituency] = [];
        }

        $groupedData[$county]['constituencies'][$constituency][] = [
            'sector' => $sector,
            'project_name' => $projectName,
            'allocation' => $allocation,
        ];
    }

    // Start HTML content for PDF
    $html = '
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #1b5e20; color: white; font-weight: bold; }
        .logo { text-align: center; margin-bottom: 10px; }
        .signature { margin-top: 30px; font-size: 14px; text-align: left; }
        .negative { color: red; font-weight: bold; }
    </style>';

    // Header with Logo
    $html .= '
    <div class="logo">' . ($logoUrl ? '<img src="' . $logoUrl . '" style="width:200px;"/>' : '') . '</div>
    <h2 style="text-align:center;">Equalization Fund Project Report</h2>
    <p style="text-align:center; font-size:10px; color:gray;">Report by: FiscalBridge Portal - ICTS - JKM, CISA</p>
    
    <table>
        <thead>
            <tr>
                <th>County</th>
                <th>Constituency</th>
                <th>Sector</th>
                <th>Project Name</th>
                <th>Total Allocation (A)</th>
                <th>Disbursed Amount (B)</th>
                <th>% Disbursed (B / A * 100)</th>
                <th>Amount Due (A - B)</th>
            </tr>
        </thead>
        <tbody>';

    // Generate grouped report content
    foreach ($groupedData as $county => $details) {
        $countyTotalAllocation = $details['total_allocation'];
        $countyTotalDisbursed = $countyDisbursementData[$county] ?? 0;
        $percentageDisbursed = ($countyTotalAllocation > 0) ? ($countyTotalDisbursed / $countyTotalAllocation) * 100 : 0;
        $amountDue = $countyTotalAllocation - $countyTotalDisbursed;

        foreach ($details['constituencies'] as $constituency => $projects) {
            $constituencyPrinted = false;
            foreach ($projects as $project) {
                $html .= "<tr>
                    <td>" . (!$constituencyPrinted ? $county : '') . "</td>
                    <td>" . (!$constituencyPrinted ? $constituency : '') . "</td>
                    <td>{$project['sector']}</td>
                    <td>{$project['project_name']}</td>
                    <td>" . number_format($project['allocation'], 2) . "</td>
                    <td></td> <!-- Leave Disbursed Amount blank for individual projects -->
                    <td></td> <!-- Leave % Disbursed blank for individual projects -->
                    <td></td> <!-- Leave Amount Due blank for individual projects -->
                </tr>";
                $constituencyPrinted = true;
            }
        }

        // County Total Row
        $html .= "<tr style='background-color:#f0f0f0; font-weight:bold;'>
            <td>{$county}</td>
            <td colspan='2' class='text-center'>Total for County</td>
            <td></td>
            <td>" . number_format($countyTotalAllocation, 2) . "</td>
            <td>" . ($countyTotalDisbursed > 0 ? number_format($countyTotalDisbursed, 2) : '') . "</td>
            <td>" . ($countyTotalDisbursed > 0 ? number_format($percentageDisbursed, 2) . '%' : '') . "</td>
            <td class='" . ($amountDue < 0 ? "negative" : "") . "'>" . number_format($amountDue, 2) . "</td>
        </tr>";
    }

    // Grand Total Row
    $html .= "<tr style='background-color:#1b5e20; color:white; font-weight:bold;'>
        <td colspan='4' class='text-center'>Grand Total</td>
        <td>" . number_format($grandTotalAllocation, 2) . "</td>
        <td>" . number_format(array_sum($countyDisbursementData), 2) . "</td>
        <td>" . ($grandTotalAllocation > 0 ? number_format((array_sum($countyDisbursementData) / $grandTotalAllocation) * 100, 2) . '%' : '') . "</td>
        <td class='" . (($grandTotalAllocation - array_sum($countyDisbursementData)) < 0 ? "negative" : "") . "'>" . number_format($grandTotalAllocation - array_sum($countyDisbursementData), 2) . "</td>
    </tr>";

    $html .= '</tbody></table>';

    // Signature section
    $html .= '
    <div class="signature">
        <p><strong>Signed By:</strong></p>
        <p>__________________________</p>
        <p><strong>Mr. Guyo Boru</strong></p>
        <p>Chief Executive Officer</p>
        <p>Date: ' . date('F d, Y') . '</p>
    </div>';

    // Generate PDF using Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    return Yii::$app->response->sendContentAsFile(
        $dompdf->output(),
        'Equalization_Fund_Project_Report.pdf',
        ['mimeType' => 'application/pdf']
    );
}

    /**
     * Updates an existing EqualizationFundProject model.
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
     * Deletes an existing EqualizationFundProject model.
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

public function actionTableSummary()
{
    $searchModel = new EqualizationFundProjectSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    // Your existing logic for grouping
    $projects = EqualizationFundProject::find()->asArray()->all();
    $disbursements = Disbursement::find()->asArray()->all();

    $groupedData = [];
    $countyDisbursementData = [];
    $grandTotalAllocation = 0;
    $grandTotalDisbursed = 0;

    foreach ($disbursements as $disbursement) {
        $county = strtoupper($disbursement['county']);
        if (!isset($countyDisbursementData[$county])) {
            $countyDisbursementData[$county] = 0;
        }
        $countyDisbursementData[$county] += $disbursement['amount_disbursed'];
        $grandTotalDisbursed += $disbursement['amount_disbursed'];
    }

    foreach ($projects as $project) {
        $county = strtoupper($project['county']);
        $constituency = strtoupper($project['constituency']);
        $sector = strtoupper($project['sector']);
        $projectName = $project['project_name'];
        $allocation = $project['budget_2018_19'] ?? 0;

        if (!isset($groupedData[$county])) {
            $groupedData[$county] = [
                'total_allocation' => 0,
                'total_disbursed' => $countyDisbursementData[$county] ?? 0,
                'constituencies' => [],
            ];
        }

        $groupedData[$county]['total_allocation'] += $allocation;
        $grandTotalAllocation += $allocation;

        if (!isset($groupedData[$county]['constituencies'][$constituency])) {
            $groupedData[$county]['constituencies'][$constituency] = [];
        }

        $groupedData[$county]['constituencies'][$constituency][] = [
            'county' => $county,
            'constituency' => $constituency,
            'sector' => $sector,
            'project_name' => $projectName,
            'allocation' => $allocation,
        ];
    }

    return $this->render('table-summary', [
        'searchModel' => $searchModel, 
        'dataProvider' => $dataProvider,
        'groupedData' => $groupedData,
        'countyDisbursementData' => $countyDisbursementData,
        'grandTotalAllocation' => $grandTotalAllocation,
        'grandTotalDisbursed' => $grandTotalDisbursed,
    ]);
}


    /**
     * Finds the EqualizationFundProject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EqualizationFundProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EqualizationFundProject::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

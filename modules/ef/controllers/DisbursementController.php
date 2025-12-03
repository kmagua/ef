<?php

namespace app\modules\ef\controllers;

use app\modules\ef\models\Disbursement;
use app\modules\ef\models\DisbursementSearch;
use yii\web\Controller;
use app\modules\ef\models\EqualizationFundProject;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii;
use yii\helpers\Url;
use Dompdf\Dompdf;
use Dompdf\Options; 



/**
 * DisbursementController implements the CRUD actions for Disbursement model.
 */
class DisbursementController extends Controller
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
                            'actions' => ['create', 'report','allocation-report','disbursement-report','sector-disbursements-per-county','visualization','update', 'view', 'index', 'card', 'summaries', 'per-county'],
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
     * Lists all Disbursement models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DisbursementSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    

public function actionVisualization()
{
    // Fetch data from EqualizationFundProject and Disbursement tables
    $projects = EqualizationFundProject::find()->asArray()->all();
    $disbursements = Disbursement::find()->asArray()->all();

    // Initialize arrays for totals
    $countyAllocations = [];
    $countyDisbursements = [];

    // Sum all allocations per county
    foreach ($projects as $project) {
        $county = strtoupper($project['county']);
        $allocation = $project['budget_2018_19'] ?? 0;

        if (!isset($countyAllocations[$county])) {
            $countyAllocations[$county] = 0;
        }
        $countyAllocations[$county] += $allocation;
    }

    // Sum all disbursements per county
    foreach ($disbursements as $disbursement) {
        $county = strtoupper($disbursement['county']);
        $amount = $disbursement['amount_disbursed'] ?? 0;

        if (!isset($countyDisbursements[$county])) {
            $countyDisbursements[$county] = 0;
        }
        $countyDisbursements[$county] += $amount;
    }

    // Merge data for final display
    $data = [];
    foreach ($countyAllocations as $county => $totalAllocation) {
        $disbursedAmount = $countyDisbursements[$county] ?? 0;
        $percentageDisbursed = ($totalAllocation > 0) ? ($disbursedAmount / $totalAllocation) * 100 : 0;
        $amountDue = $totalAllocation - $disbursedAmount;

        $data[$county] = [
            'county' => $county,
            'total_allocation' => $totalAllocation,
            'disbursed' => $disbursedAmount,
            'percentage_disbursed' => $percentageDisbursed,
            'amount_due' => $amountDue,
        ];
    }

    // Pass data to the visualization view
    return $this->render('visualization', [
        'data' => $data,
    ]);
}


public function actionReport()
{
   

    // Ensure the logo is correctly loaded using base64 encoding
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png'); // Path to your logo
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $logoUrl = 'data:image/png;base64,' . $imageData;
    } else {
        $logoUrl = '';
    }
 // Header information
    $reportBy = 'Report by: FiscalBridge Information System - ICTS - JKM, CISA';
    // Fetch data from models
    $projects = EqualizationFundProject::find()->asArray()->all();
    $disbursements = Disbursement::find()->asArray()->all();

    // Initialize arrays for totals
    $countyAllocations = [];
    $countyDisbursements = [];

    // Sum all allocations per county
    foreach ($projects as $project) {
        $county = strtoupper($project['county']);
        $allocation = $project['budget_2018_19'] ?? 0;

        if (!isset($countyAllocations[$county])) {
            $countyAllocations[$county] = 0;
        }
        $countyAllocations[$county] += $allocation;
    }

    // Sum all disbursements per county
    foreach ($disbursements as $disbursement) {
        $county = strtoupper($disbursement['county']);
        $amount = $disbursement['amount_disbursed'] ?? 0;

        if (!isset($countyDisbursements[$county])) {
            $countyDisbursements[$county] = 0;
        }
        $countyDisbursements[$county] += $amount;
    }

    // Merge data for the report
    $data = [];
    $totalAllocation = 0;
    $totalDisbursed = 0;
    $totalAmountDue = 0;

    foreach ($countyAllocations as $county => $totalAlloc) {
        $disbursedAmount = $countyDisbursements[$county] ?? 0;
        $percentageDisbursed = ($totalAlloc > 0) ? ($disbursedAmount / $totalAlloc) * 100 : 0;
        $amountDue = $totalAlloc - $disbursedAmount;

        $data[] = [
            'county' => $county,
            'total_allocation' => $totalAlloc,
            'disbursed' => $disbursedAmount,
            'percentage_disbursed' => $percentageDisbursed,
            'amount_due' => $amountDue,
        ];

        // Calculate overall totals
        $totalAllocation += $totalAlloc;
        $totalDisbursed += $disbursedAmount;
        $totalAmountDue += $amountDue;
    }

    $html = '
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");

        body { font-family: "Poppins", sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #1b5e20; color: white; font-weight: bold; }

        /* Ensure table headers repeat on every page */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; }

        .header { text-align: center; margin-bottom: 10px; font-size: 18px; font-weight: bold; }
        .flag-bar { width: 100%; height: 10px; display: block; background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%); margin-bottom: 10px; }
        .logo { text-align: center; margin-bottom: 10px; }
        .signature { margin-top: 30px; font-size: 14px; text-align: left; }
        .system-note { font-size: 12px; text-align: center; margin-top: 20px; color: gray; }
        .footer { text-align: center; font-size: 10px; color: gray; margin-top: 20px; }
        .grand-total { font-weight: bold; background-color: #f4f4f4; text-align: center; }
        .report-by { text-align: center; font-size: 10px; color: gray; margin-bottom: 10px; }
    </style>

  <div class="logo">
        ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo" style="width:400px;"/>' : '') . '
    </div>
    
    <div class="header">
        <h2>DISBURSEMENT AND ALLOCATION ANALYSIS</h2>
    </div>

    <div class="flag-bar"></div>

    <div class="report-by">' . $reportBy . '</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>County</th>
                <th>Total Allocation</th>
                <th>Disbursed Amount</th>
                <th>% Disbursed</th>
                <th>Amount Due</th>
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
            <td>' . number_format($row['disbursed'], 2) . '</td>
            <td>' . number_format($row['percentage_disbursed'], 2) . '%</td>
            <td ' . ($row['amount_due'] < 0 ? 'style="color:red; font-weight:bold;"' : '') . '>' . number_format($row['amount_due'], 2) . '</td>
        </tr>';
}

// Add Total Row
$html .= '
        <tr class="grand-total">
            <td colspan="2">Grand Total</td>
            <td>' . number_format($totalAllocation, 2) . '</td>
            <td>' . number_format($totalDisbursed, 2) . '</td>
            <td>' . number_format(($totalAllocation > 0) ? ($totalDisbursed / $totalAllocation) * 100 : 0, 2) . '%</td>
            <td>' . number_format($totalAmountDue, 2) . '</td>
        </tr>
    </tbody>
</table>';


    // Add Signature Section
    $html .= '
        <div class="signature">
            <p><strong>Signed By:</strong></p>
            <p>__________________________</p>
            <p><strong>Mr. Guyo Boru</strong></p>
            <p>Chief Executive Officer</p>
            <p>Date: ' . date('F d, Y') . '</p>
        </div>';

    // System Generated Note
    $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

    // Footer for every page
    $html .= '<div class="footer">Page {PAGE_NUM} of {PAGE_COUNT}</div>';

    // Initialize Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Poppins');
    $options->set('isRemoteEnabled', true); // Allows loading images
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Stream the PDF for download
    $dompdf->stream("Disbursement_and_Allocation_Analysis.pdf", ["Attachment" => true]);
    return;
}

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
    $projects = \app\modules\ef\models\EqualizationFundProject::find()->asArray()->all();

    $countyAllocations = [];
    $countyCounts = [];
    foreach ($projects as $project) {
        $county = strtoupper($project['county']);
        $allocation = $project['budget_2018_19'] ?? 0;
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

    $html = '
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");

        body { font-family: "Poppins", sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #1b5e20; color: white; font-weight: bold; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; }
        .header { text-align: center; margin-bottom: 10px; font-size: 18px; font-weight: bold; }
        .flag-bar { width: 100%; height: 10px; display: block; background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%); margin-bottom: 10px; }
        .logo { text-align: center; margin-bottom: 10px; }
        .signature { margin-top: 30px; font-size: 14px; text-align: left; }
        .system-note { font-size: 12px; text-align: center; margin-top: 20px; color: gray; }
        .footer { text-align: center; font-size: 10px; color: gray; margin-top: 20px; }
        .grand-total { font-weight: bold; background-color: #f4f4f4; text-align: center; }
        .report-by { text-align: center; font-size: 10px; color: gray; margin-bottom: 10px; }
    </style>

    <div class="logo">
        ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo" style="width:400px;"/>' : '') . '
    </div>
    
    <div class="header">
        <h2>ALLOCATION REPORT</h2>
    </div>

    <div class="flag-bar"></div>

    <div class="report-by">' . $reportBy . '</div>

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

    $html .= '
        <div class="signature">
            <p><strong>Signed By:</strong></p>
            <p>__________________________</p>
            <p><strong>Mr. Guyo Boru</strong></p>
            <p>Chief Executive Officer</p>
            <p>Date: ' . date('F d, Y') . '</p>
        </div>';

    $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';
    $html .= '<div class="footer">Page {PAGE_NUM} of {PAGE_COUNT}</div>';

    // Prepare Dompdf with proper Yii handling
    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');

    $options = new \Dompdf\Options();
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Allocation_Report.pdf", ["Attachment" => true]);

    exit;
}


    /**
     * Displays the Sector Disbursements per County page.
     * @return mixed
     */
    public function actionSectorDisbursementsPerCounty()
    {
        $searchModel = new DisbursementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('sector-disbursements-per-county', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Disbursement model.
     * @param string $id ID
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
     * Creates a new Disbursement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Disbursement();
        $model->user_id = \Yii::$app->user->identity->id;

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
     * Updates an existing Disbursement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
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
     * Deletes an existing Disbursement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Disbursement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Disbursement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Disbursement::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionDisbursementReport()
{
    // Load and encode the logo image (using base64)
    $imagePath = Yii::getAlias('@webroot/igfr_front/img/eq.png');
    if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
        $logoUrl = 'data:image/png;base64,' . $imageData;
    } else {
        $logoUrl = '';
    }

    // Header information for the report
    $reportBy = 'Report by: FiscalBridge Portal - ICTS - JKM, CISA';

    // Fetch disbursement records
    $disbursements = \app\modules\ef\models\Disbursement::find()->asArray()->all();

    // Group disbursements by county and calculate totals
    $countyDisbursements = [];
    $countyCounts = [];
    foreach ($disbursements as $disbursement) {
        $county = strtoupper($disbursement['county']);
        $amount = $disbursement['amount_disbursed'] ?? 0;
        if (!isset($countyDisbursements[$county])) {
            $countyDisbursements[$county] = 0;
            $countyCounts[$county] = 0;
        }
        $countyDisbursements[$county] += $amount;
        $countyCounts[$county] += 1;
    }

    // Prepare data and calculate overall totals
    $data = [];
    $overallTotal = 0;
    $overallCount = 0;
    foreach ($countyDisbursements as $county => $totalAmount) {
        $count = $countyCounts[$county];
        $average = ($count > 0) ? $totalAmount / $count : 0;
        $data[] = [
            'county' => $county,
            'total_disbursed' => $totalAmount,
            'disbursement_count' => $count,
            'average_disbursement' => $average,
        ];
        $overallTotal += $totalAmount;
        $overallCount += $count;
    }
    $overallAverage = ($overallCount > 0) ? $overallTotal / $overallCount : 0;

    // Build HTML for the PDF report (using a similar style as your other report)
    $html = '
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap");

        body { font-family: "Poppins", sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #1b5e20; color: white; font-weight: bold; }

        /* Ensure table headers repeat on every page */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; }

        .header { text-align: center; margin-bottom: 10px; font-size: 18px; font-weight: bold; }
        .flag-bar { width: 100%; height: 10px; display: block; background: linear-gradient(to right, black 25%, red 25%, red 50%, white 50%, white 75%, green 75%); margin-bottom: 10px; }
        .logo { text-align: center; margin-bottom: 10px; }
        .signature { margin-top: 30px; font-size: 14px; text-align: left; }
        .system-note { font-size: 12px; text-align: center; margin-top: 20px; color: gray; }
        .footer { text-align: center; font-size: 10px; color: gray; margin-top: 20px; }
        .grand-total { font-weight: bold; background-color: #f4f4f4; text-align: center; }
        .report-by { text-align: center; font-size: 10px; color: gray; margin-bottom: 10px; }
    </style>

    <div class="logo">
        ' . ($logoUrl ? '<img src="' . $logoUrl . '" alt="Logo" style="width:400px;"/>' : '') . '
    </div>
    
    <div class="header">
        <h2>DISBURSEMENT REPORT</h2>
    </div>

    <div class="flag-bar"></div>

    <div class="report-by">' . $reportBy . '</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>County</th>
                <th>Total Disbursed</th>
                <th>Disbursement Count</th>
                <th>Average Disbursement</th>
            </tr>
        </thead>
        <tbody>';
    
    $counter = 1;
    foreach ($data as $row) {
        $html .= '
            <tr>
                <td>' . $counter++ . '</td>
                <td>' . $row['county'] . '</td>
                <td>' . number_format($row['total_disbursed'], 2) . '</td>
                <td>' . $row['disbursement_count'] . '</td>
                <td>' . number_format($row['average_disbursement'], 2) . '</td>
            </tr>';
    }
    
    // Add Grand Total Row
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
            <p>__________________________</p>
            <p><strong>Mr. Guyo Boru</strong></p>
            <p>Chief Executive Officer</p>
            <p>Date: ' . date('F d, Y') . '</p>
        </div>';

    // System Generated Note
    $html .= '<p class="system-note">This is a system-generated report. No signature is required.</p>';

    // Footer (for page numbering on every page)
    $html .= '<div class="footer">Page {PAGE_NUM} of {PAGE_COUNT}</div>';

    // Initialize and configure Dompdf
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Poppins');
    $options->set('isRemoteEnabled', true); // To load remote images
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Stream the PDF document to the browser for download
    $dompdf->stream("Disbursement_Report.pdf", ["Attachment" => true]);
    return;
}

    /**
     * Lists all Disbursement models.
     *
     * @return string
     */
   
    
    public function actionSummaries()
{
    $searchModel = new DisbursementSearch(); // Ensure you have a search model
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $total_per_county = Disbursement::getTotalPerCounty();

    return $this->render('summaries', [
        'searchModel' => $searchModel,  // Pass the search model
        'dataProvider' => $dataProvider, // Pass the data provider
        'total_per_county' => $total_per_county
    ]);
}

    /**
     * Lists all Disbursement models.
     *
     * @return string
     */
    public function actionPerCounty($cnt)
    {
        $total_per_county = Disbursement::getPerCountyDisbursement($cnt);

        return $this->render('index_summary', [
            'dataProvider' => $total_per_county,
            'county' =>$cnt
        ]);
    }
}

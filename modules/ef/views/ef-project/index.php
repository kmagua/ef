<?php

use app\modules\ef\models\EqualizationFundProject;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationFundProjectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = strtoupper('Equalization Fund Projects');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

// GROUP DATA BY SECTOR
$projects = $dataProvider->getModels();
$groupedData = [];
$totalBudget = 0;

foreach ($projects as $project) {
    $sector = $project->sector;

    if (!isset($groupedData[$sector])) {
        $groupedData[$sector] = [
            'sector' => $sector,
            'budget_2018_19' => 0,
            'count' => 0,
        ];
    }

    $groupedData[$sector]['budget_2018_19'] += $project->budget_2018_19;
    $groupedData[$sector]['count']++;
    $totalBudget += $project->budget_2018_19;
}

$groupedProvider = new ArrayDataProvider([
    'allModels' => array_values($groupedData),
]);

?>

<style>

/* GLOBAL */
body {
    font-family: 'Poppins', sans-serif;
    background: #eef2f3;
    color: #333;
}

/* PAGE WRAPPER */
.equalization-fund-project-index {
    background: #fff;
    padding: 35px;
    border-radius: 14px;
    box-shadow: 0px 8px 22px rgba(0,0,0,0.10);
}

/* PAGE TITLE */
.equalization-fund-project-index h1 {
    background: linear-gradient(135deg, #008a8a, #006f6f);
    color: white !important;
    padding: 18px;
    font-size: 28px;
    font-weight: 700;
    border-radius: 10px;
    text-align: center;
    letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

/* SEARCH FORM */
.search-form {
    background: #e7f7f7;
    padding: 20px;
    border-radius: 12px;
    border-left: 5px solid #008a8a;
    margin-bottom: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
}

.select2-selection {
    border: 2px solid #008a8a !important;
    border-radius: 6px !important;
    padding: 6px;
}

.select2-selection__choice {
    background-color: #008a8a !important;
    color: white !important;
    border-radius: 4px !important;
    font-size: 13px;
    padding: 3px 8px !important;
}

.btn-primary {
    background: #008a8a !important;
    border-color: #006f6f !important;
    font-weight: 600;
}
.btn-outline-secondary {
    border-color: #008a8a !important;
    color: #008a8a !important;
    font-weight: 600;
}
.btn-outline-secondary:hover {
    background: #008a8a !important;
    color: white !important;
}

/* SECTOR AND TOTAL SUMMARY */
.section-title {
    color: #008a8a;
    font-weight: 700;
    font-size: 22px;
    margin-top: 40px;
    border-left: 5px solid #008a8a;
    padding-left: 12px;
}

/* SUMMARY CARDS */
.card {
    border-radius: 12px !important;
    border: none !important;
    overflow: hidden;
}
.card-header {
    background: linear-gradient(135deg, #008a8a, #006f6f) !important;
    color: white !important;
    font-weight: 700;
    font-size: 18px;
}
.card-body h5 {
    color: #006f6f;
    font-weight: 800;
    font-size: 26px;
}

/* TABLE HEADER */
.table thead th {
    background-color: #008a8a !important;
    color: white !important;
    text-transform: uppercase;
    padding: 12px;
    font-weight: 700;
    text-align: center;
    border-color: #007070 !important;
}
.table thead th a {
    color: white !important;
}

/* TABLE BODY */
.table tbody td {
    color: #003b3b !important;
    padding: 12px;
    font-size: 15px;
}
.table tbody tr:hover {
    background: #dff5f5 !important;
}

/* ACTION ICONS */
.action-column a {
    margin-right: 8px;
    font-size: 17px;
    color: #008a8a;
}
.action-column a:hover {
    color: #006f6f;
}

</style>



<div class="equalization-fund-project-index">

<h1><?= Html::encode($this->title) ?></h1>

<!-- SEARCH FORM -->
<div class="search-form">
<?php $form = ActiveForm::begin(['method' => 'get']); ?>

<div class="row">

    <!-- COUNTY MULTI SELECT -->
    <div class="col-md-4">
        <?= $form->field($searchModel, 'county')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                EqualizationFundProject::find()->select('county')->distinct()->all(),
                'county',
                'county'
            ),
            'options' => [
                'placeholder' => 'Select Counties...',
                'multiple' => true
            ],
            'pluginOptions' => ['allowClear' => true]
        ]); ?>
    </div>

    <!-- SECTOR MULTI SELECT ✔✔✔ -->
    <div class="col-md-4">
        <?= $form->field($searchModel, 'sector')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(
                EqualizationFundProject::find()->select('sector')->distinct()->all(),
                'sector',
                'sector'
            ),
            'options' => [
                'placeholder' => 'Select Sectors...',
                'multiple' => true
            ],
            'pluginOptions' => ['allowClear' => true]
        ]); ?>
    </div>

</div>

<div class="row mt-3">
    <div class="col-md-12 text-end">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>


<!-- SECTOR SUMMARY -->
<h2 class="section-title">Sector Summary</h2>

<?= GridView::widget([
    'dataProvider' => $groupedProvider,
    'summary' => '',
    'tableOptions' => ['class' => 'table table-bordered table-striped table-hover'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'sector',
            'contentOptions' => ['style' => 'font-weight:700;color:#008a8a;font-size:16px;']
        ],

        [
            'attribute' => 'budget_2018_19',
            'label' => 'Budget',
            'value' => fn($model) => number_format($model['budget_2018_19'], 2),
            'contentOptions' => ['class' => 'text-end', 'style' => 'color:#006f6f;font-weight:700;font-size:15px;'],
        ],

        [
            'attribute' => 'count',
            'label' => 'Projects',
            'contentOptions' => [
                'class' => 'text-center fw-bold',
                'style' => 'font-size:15px;color:#006f6f;'
            ],
        ],
    ],
]); ?>


<!-- TOTAL SUMMARY -->
<h2 class="section-title">Total Summary</h2>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header">Total Budget</div>
            <div class="card-body text-center">
                <h5><?= number_format($totalBudget, 2) ?></h5>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header">Total Projects</div>
            <div class="card-body text-center">
                <h5><?= array_sum(array_column($groupedData, 'count')) ?></h5>
            </div>
        </div>
    </div>
</div>


<!-- PROJECT DETAILS -->
<h2 class="section-title">Project Details</h2>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table table-bordered table-striped'],
    'columns' => [

        ['class' => 'yii\grid\SerialColumn'],

        'county',
        'constituency',
        'sector',

        [
            'attribute' => 'project_name',
            'contentOptions' => ['style' => 'white-space:normal;max-width:260px;font-size:15px;'],
        ],

        [
            'attribute' => 'budget_2018_19',
            'label' => 'Total Allocation',
            'value' => fn($model) => number_format($model->budget_2018_19, 2),
            'contentOptions' => [
                'style' => 'color:#008a8a;font-weight:700;text-align:right;font-size:15px;'
            ],
        ],

        [
            'class' => ActionColumn::class,
            'template' => '{view} {update}',
            'contentOptions' => ['class' => 'text-center action-column'],
            'urlCreator' => function ($action, EqualizationFundProject $model) {
                return Url::toRoute([$action, 'id' => $model->id]);
            },
        ],
    ],
]); ?>

</div>

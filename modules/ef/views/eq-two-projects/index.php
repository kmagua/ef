<?php

use app\modules\ef\models\EqualizationTwoProjects;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\data\ArrayDataProvider;
use yii\grid\ActionColumn;

$this->title = strtoupper('2nd Marginalization Policy - Projects');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap'
);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

// Dropdowns
$counties        = ArrayHelper::map(EqualizationTwoProjects::find()->select('county')->distinct()->all(), 'county','county');
$constituencies = ArrayHelper::map(EqualizationTwoProjects::find()->select('constituency')->distinct()->all(),'constituency','constituency');
$wards           = ArrayHelper::map(EqualizationTwoProjects::find()->select('ward')->distinct()->all(),'ward','ward');
$sectors         = ArrayHelper::map(EqualizationTwoProjects::find()->select('sector')->distinct()->all(),'sector','sector');

// Global totals
$allProjects = EqualizationTwoProjects::find()->count();
$allBudget   = EqualizationTwoProjects::find()->sum('project_budget');

// Filtered totals
$models = $dataProvider->getModels();
$totalProjects = count($models);
$totalBudget = array_sum(array_column($models, 'project_budget'));

$sectorGrouped = [];
foreach ($models as $m) {
    $sector = $m->sector ?: 'Unspecified';

    if (!isset($sectorGrouped[$sector])) {
        $sectorGrouped[$sector] = ['sector'=>$sector,'total_budget'=>0,'project_count'=>0];
    }

    $sectorGrouped[$sector]['total_budget'] += $m->project_budget;
    $sectorGrouped[$sector]['project_count']++;
}

$sectorProvider = new ArrayDataProvider([
    'allModels'=>array_values($sectorGrouped),
    'pagination'=>false,
]);

?>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #eef2f3;
}
/* FORCE PAGE TITLE TO BE PURE WHITE */
.projects-wrapper h1,
.equalization-index h1 {
    color: #ffffff !important;
    text-shadow: none !important;
}

/* MAIN WRAPPER */
.projects-wrapper {
    background: #fff;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.07);
}
/* FORCE TABLE HEADER TEXT TO PURE WHITE */
.table thead th,
.table thead th a {
    color: #ffffff !important;
    text-decoration: none !important;
}

/* Optional: Remove link blue hover */
.table thead th a:hover {
    color: #f1f1f1 !important;
}

/* HEADER */
.projects-wrapper h1 {
    background: linear-gradient(135deg, #00796b, #009688);
    color: #fff;
    padding: 16px;
    border-radius: 8px;
    text-align: center;
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 1px;
}

/* SUMMARY CARDS */
.summary-card {
    background: #e0f7fa;
    padding: 12px;
    border-radius: 8px;
    border-left: 6px solid #00796b;
    margin-bottom: 12px;
}

/* Map Button Card - Special Styling */
.map-button-card {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
    }
    50% {
        box-shadow: 0 4px 20px rgba(255, 107, 53, 0.5);
    }
}
.summary-title {
    font-size: 0.75rem;
    font-weight: 600;
    color: #006666;
}
.summary-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #004d40;
}

/* SEARCH BOX */
.search-box {
    background: #e0f2f1;
    padding: 14px;
    border-left: 6px solid #00897b;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* SELECT2 */
.select2-container .select2-selection--single {
    height: 38px !important;
    padding-top: 4px;
}

/* FIX BLACK SCROLL AREA → CLEAN WHITE SCROLL */
.table-wrapper {
    width: 100%;
    background: white !important;
    border-radius: 10px;
    overflow-x: auto;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #b2dfdb #ffffff;
    max-height: 70vh;
}

/* CHROME SCROLLBAR */
.table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}
.table-wrapper::-webkit-scrollbar-track {
    background: #ffffff;
}
.table-wrapper::-webkit-scrollbar-thumb {
    background-color: #80cbc4;
    border-radius: 10px;
}

/* TABLE HEADER (White text, better contrast) */
.table thead th {
    background: #004d40 !important;
    color: #fff !important;
    text-transform: uppercase;
    font-size: 0.82rem;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
    padding: 12px;
}

/* FIX TEXT WRAPPING */
.wrap-text {
    white-space: normal !important;
    word-wrap: break-word;
    word-break: break-word;
    max-width: 260px;
}

/* HOVER FIX → smooth, light, elegant */
.table tbody tr:hover {
    background: #e0f2f1 !important;
    transition: 0.2s;
}

/* SECTOR BADGE */
.sector-badge {
    background: #c2f5ea;
    color: #004d40;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* BUTTONS */
.action-btn {
    padding: 6px 14px;
    border-radius: 6px;
    color: white !important;
    font-size: 12px;
    margin: 0 2px;
    display: inline-block;
    text-decoration: none;
    white-space: nowrap;
}
.view-btn { background: #1976d2; }
.view-btn:hover { background: #125a9c; }
.edit-btn { background: #f9a825; }
.edit-btn:hover { background: #d48d00; }
.coordinates-btn { 
    background: #7b1fa2; 
    font-size: 0.75rem !important;
    padding: 2px 6px !important;
}
.coordinates-btn:hover { background: #6a1b9a; }
.delete-btn { 
    background: #d32f2f; 
    font-size: 0.75rem !important;
    padding: 4px 8px !important;
    line-height: 1;
}
.delete-btn:hover { background: #b71c1c; }
.delete-btn i {
    font-size: 0.875rem;
}

/* SUMMARY TABLE HEADERS */
.summary-table thead th {
    background: #00695c !important;
    color: #fff !important;
}

</style>



<div class="projects-wrapper">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- GLOBAL TOTALS -->
    <div class="row">
        <div class="col-md-3 col-6">
            <div class="summary-card">
                <div class="summary-title">All Projects</div>
                <div class="summary-value"><?= number_format($allProjects) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <div class="summary-title">All Budget</div>
                <div class="summary-value">Ksh <?= number_format($allBudget,2) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <a href="<?= \yii\helpers\Url::to(['map-view']) ?>" style="text-decoration: none; display: block;">
                <div class="summary-card map-button-card" style="background: linear-gradient(135deg, #ff6b35, #ff8c42); border-left: 6px solid #ff4500; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);" onmouseover="this.style.transform='translateY(-5px) scale(1.02)'; this.style.boxShadow='0 8px 25px rgba(255, 107, 53, 0.5)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 4px 15px rgba(255, 107, 53, 0.3)';">
                    <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -10px; left: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%;"></div>
                    <div class="summary-title" style="color: #fff; position: relative; z-index: 1; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">View on Map</div>
                    <div class="summary-value" style="color: #fff; font-size: 2rem; position: relative; z-index: 1; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- FILTERED TOTALS -->
    <div class="row">
        <div class="col-md-3 col-6">
            <div class="summary-card">
                <div class="summary-title">Filtered Projects</div>
                <div class="summary-value"><?= number_format($totalProjects) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <div class="summary-title">Filtered Budget</div>
                <div class="summary-value">Ksh <?= number_format($totalBudget,2) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <div class="summary-title">Sectors</div>
                <div class="summary-value"><?= number_format(count($sectorGrouped)) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="summary-card">
                <div class="summary-title">Avg Budget</div>
                <div class="summary-value">Ksh <?= number_format($totalProjects ? $totalBudget/$totalProjects : 0,2) ?></div>
            </div>
        </div>
    </div>

    <p><?= Html::a('Create Project', ['create'], ['class' => 'btn btn-success mb-2']) ?></p>

    <!-- SEARCH BOX -->
    <div class="search-box">
        <?php $form = ActiveForm::begin(['method'=>'get']); ?>
        <div class="row">

            <div class="col-md-3"><?= $form->field($searchModel,'county')->widget(Select2::class,['data'=>$counties,'options'=>['placeholder'=>'County'],'pluginOptions'=>['allowClear'=>true]]) ?></div>
            <div class="col-md-3"><?= $form->field($searchModel,'constituency')->widget(Select2::class,['data'=>$constituencies,'options'=>['placeholder'=>'Constituency'],'pluginOptions'=>['allowClear'=>true]]) ?></div>
            <div class="col-md-3"><?= $form->field($searchModel,'ward')->widget(Select2::class,['data'=>$wards,'options'=>['placeholder'=>'Ward'],'pluginOptions'=>['allowClear'=>true]]) ?></div>
            <div class="col-md-3"><?= $form->field($searchModel,'sector')->widget(Select2::class,['data'=>$sectors,'options'=>['placeholder'=>'Sector'],'pluginOptions'=>['allowClear'=>true]]) ?></div>

        </div>

        <div class="text-end">
            <?= Html::submitButton('Search',['class'=>'btn btn-primary']) ?>
            <?= Html::a('Reset',['index'],['class'=>'btn btn-outline-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
        <?= GridView::widget([
            'dataProvider'=>$dataProvider,
            'summary'=>'',
            'showFooter'=>true,
            'tableOptions'=>['class'=>'table table-bordered table-hover table-striped'],

            'columns'=>[
                ['class'=>'yii\grid\SerialColumn'],

                'county',
                'constituency',
                'ward',

                [
                    'attribute'=>'marginalised_area',
                    'contentOptions'=>['class'=>'wrap-text']
                ],

                [
                    'attribute'=>'project_description',
                    'format'=>'ntext',
                    'contentOptions'=>['class'=>'wrap-text']
                ],

                [
                    'attribute'=>'sector',
                    'format'=>'raw',
                    'value'=>fn($m)=>'<span class="sector-badge">'.$m->sector.'</span>'
                ],

                [
                    'attribute'=>'project_budget',
                    'value'=>fn($m)=>number_format($m->project_budget,2),
                    'contentOptions'=>['class'=>'text-end fw-bold'],
                    'footer'=>'Total: '.number_format($totalBudget,2),
                    'footerOptions'=>['class'=>'text-end bg-success text-white fw-bold']
                ],

                [
                    'class'=>ActionColumn::class,
                    'template'=>'{view} {coordinates} {update} {delete}',
                    'contentOptions'=>['class'=>'text-center'],
                    'headerOptions'=>['style'=>'min-width: 200px;'],
                    'buttons'=>[
                        'view'=>fn($url)=>Html::a('View',$url,['class'=>'action-btn view-btn']),
                        'update'=>fn($url)=>Html::a('Edit',$url,['class'=>'action-btn edit-btn']),
                        'coordinates'=>fn($url, $model)=>Html::a('Coords',['coordinates', 'id' => $model->id],['class'=>'action-btn coordinates-btn']),
                        'delete'=>function($url, $model) {
                            return Html::a('<i class="fas fa-trash"></i>', $url, [
                                'class'=>'action-btn delete-btn',
                                'data-confirm'=>'Are you sure you want to delete this item?',
                                'data-method'=>'post',
                                'title'=>'Delete'
                            ]);
                        }
                    ]
                ]

            ]
        ]); ?>
    </div>

    <!-- SUMMARY BY SECTOR -->
    <h4 class="mt-4 mb-2">Summary by Sector</h4>

    <div class="table-wrapper">
        <?= GridView::widget([
            'dataProvider'=>$sectorProvider,
            'summary'=>'',
            'tableOptions'=>['class'=>'table table-bordered table-hover summary-table'],

            'columns'=>[
                ['class'=>'yii\grid\SerialColumn'],

                [
                    'attribute'=>'sector',
                    'format'=>'raw',
                    'value'=>fn($r)=>'<span class="sector-badge">'.$r['sector'].'</span>'
                ],

                [
                    'attribute'=>'project_count',
                    'label'=>'Projects',
                    'contentOptions'=>['class'=>'text-center fw-bold']
                ],

                [
                    'attribute'=>'total_budget',
                    'label'=>'Total Budget (Ksh)',
                    'value'=>fn($r)=>number_format($r['total_budget'],2),
                    'contentOptions'=>['class'=>'text-end fw-bold text-success']
                ]
            ]
        ]); ?>
    </div>

</div>

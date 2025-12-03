<?php

use app\modules\ef\models\EqualisationFundEntitlements;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\grid\ActionColumn;

/* ---------------------------
   FILTER DATASETS
---------------------------- */
 $financialYears = ArrayHelper::map(
    EqualisationFundEntitlements::find()->select('financial_year')->distinct()->orderBy('financial_year')->all(),
    'financial_year', 'financial_year'
);

/* ---------------------------
   TITLE & FONTS
---------------------------- */
 $this->title = strtoupper('Equalisation Fund Entitlements');
 $this->params['breadcrumbs'][] = $this->title;

 $this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
?>

<style>
/* GLOBAL */
body {
    font-family: 'Poppins', sans-serif;
    background: #eef3f2;
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

/* PAGE WRAPPER */
.entitlements-index {
    background: #fff;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.10);
    margin: 15px;
    max-width: 100%;
    overflow: visible;
}

/* TITLE */
.entitlements-index h1 {
    background: linear-gradient(135deg, #00695c, #00bfa5);
    color: #ffffff !important;
    padding: 20px;
    border-radius: 14px;
    text-align: center;
    font-weight: 800;
    font-size: 1.8rem;
    letter-spacing: 1px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

/* SUMMARY CARDS */
.summary-card {
    background: #e0f7fa;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.08);
    margin-bottom: 15px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: transform 0.2s;
}
.summary-card:hover {
    transform: translateY(-3px);
}
.summary-card h4 {
    margin: 0 0 8px 0;
    font-size: .85rem;
    color: #006064;
    font-weight: 600;
    text-transform: uppercase;
}
.summary-card .value {
    font-size: 1.4rem;
    font-weight: 800;
    color: #004d40;
}

/* SEARCH FORM */
.search-form {
    background: #e0f2f1;
    padding: 18px;
    border-radius: 14px;
    border-left: 6px solid #00a58a;
    margin-bottom: 20px;
    box-shadow: 0px 2px 10px rgba(0,0,0,0.05);
    position: relative;
    z-index: 2;
}

.select2-container--krajee .select2-selection--multiple {
    height: 40px !important;
    padding-top: 4px;
    font-size: 13px;
    border: 1px solid #008f7a !important;
}

.select2-selection__placeholder {
    color: #009688 !important;
    font-weight: 500;
}

/* TABLE CONTAINER */
.table-container {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

/* TABLE HEADER (PURE WHITE TEXT) */
.table thead th {
    background: #004d40 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    padding: 10px 8px !important;
    font-size: 0.8rem;
    letter-spacing: 0.3px;
    text-align: center !important;
    white-space: nowrap;
    text-transform: uppercase;
    border: none !important;
    position: sticky;
    top: 0;
    z-index: 10;
}

/* FORCE WHITE LINK INSIDE THE HEADER */
.grid-view table thead th a {
    color: #ffffff !important;
    font-weight: 700 !important;
    text-decoration: none !important;
}
.grid-view table thead th a:hover {
    color: #e6e6e6 !important;
}

/* TABLE BODY */
.table tbody td {
    padding: 8px 6px !important;
    font-size: 0.85rem;
    color: #003b3b !important;
    border: none !important;
    border-bottom: 1px solid #e0f2f1 !important;
    vertical-align: middle !important;
}
.table tbody tr:hover {
    background: #d7f8ea !important;
}

/* ACTION BUTTONS */
.action-btn {
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    color: white !important;
    text-decoration: none;
    margin: 1px;
    display: inline-block;
    transition: all 0.2s;
}

.view-btn { background: #1976d2; }
.view-btn:hover { background: #0d47a1; transform: translateY(-1px); }

.edit-btn { background: #f9a825; }
.edit-btn:hover { background: #c68400; transform: translateY(-1px); }

/* COMPACT LAYOUT */
.form-group {
    margin-bottom: 12px;
}
.btn {
    padding: 6px 14px;
    font-size: 13px;
    border-radius: 6px;
    transition: all 0.2s;
}
.btn:hover {
    transform: translateY(-1px);
}

/* PAGINATION */
.pagination {
    margin-top: 15px;
}
.pagination .page-link {
    color: #004d40;
    border-radius: 6px;
    margin: 0 2px;
}
.pagination .page-item.active .page-link {
    background-color: #004d40;
    border-color: #004d40;
}

/* RESPONSIVE ADJUSTMENTS */
@media (max-width: 768px) {
    .entitlements-index {
        padding: 15px;
        margin: 10px;
    }
    
    .entitlements-index h1 {
        font-size: 1.5rem;
        padding: 15px;
    }
    
    .summary-card {
        margin-bottom: 10px;
        padding: 12px;
    }
    
    .search-form {
        padding: 15px;
    }
    
    .table thead th, .table tbody td {
        padding: 6px 4px !important;
        font-size: 0.75rem;
    }
    
    .action-btn {
        padding: 4px 8px;
        font-size: 10px;
    }
    
    .btn {
        padding: 5px 10px;
        font-size: 12px;
    }
}

/* ENSURE ALL SECTIONS VISIBLE */
.entitlements-index > * {
    margin-bottom: 15px;
}
.entitlements-index > *:last-child {
    margin-bottom: 0;
}

/* HORIZONTAL SCROLL FOR TABLE */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 8px;
}

/* FIX Z-INDEX LAYERING */
.entitlements-index {
    position: relative;
    z-index: 1;
}
</style>

<div class="entitlements-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    /* SUMMARY AGGREGATES */
    $globalTotal = EqualisationFundEntitlements::find()->sum('ef_entitlement_ksh');
    $globalCount = EqualisationFundEntitlements::find()->count();
    $pageTotal = array_sum(array_column($dataProvider->getModels(), 'ef_entitlement_ksh'));
    ?>

    <!-- SUMMARY CARDS -->
    <div class="row mb-3">

        <div class="col-md-3 col-6 mb-3">
            <div class="summary-card">
                <h4>Total Records</h4>
                <div class="value"><?= number_format($globalCount) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="summary-card">
                <h4>Total Entitlements</h4>
                <div class="value">Ksh <?= number_format($globalTotal, 2) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="summary-card">
                <h4>Filtered Records</h4>
                <div class="value"><?= count($dataProvider->getModels()) ?></div>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="summary-card">
                <h4>Filtered Entitlements</h4>
                <div class="value">Ksh <?= number_format($pageTotal, 2) ?></div>
            </div>
        </div>

    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <?= Html::a('Create Equalisation Fund Entitlements', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <!-- SEARCH FORM -->
    <div class="search-form">

        <?php $form = ActiveForm::begin(['method' => 'get']); ?>

        <div class="row">

            <!-- MULTIPLE FINANCIAL YEAR SELECTION -->
            <div class="col-md-4">
                <?= $form->field($searchModel, 'financial_year')->widget(Select2::class, [
                    'data' => $financialYears,
                    'options' => ['placeholder' => 'Select Financial Year...', 'multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Filter by Financial Year') ?>
            </div>

        </div>

        <div class="text-end mt-3">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary px-4']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary px-4']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <!-- MAIN TABLE -->
    <div class="table-container">
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-bordered table-striped table-hover mb-0'],
                'layout' => "{summary}\n{items}\n{pager}",
                'pager' => [
                    'options' => ['class' => 'pagination justify-content-center'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                ],

                'columns' => [

                    ['class' => 'yii\grid\SerialColumn', 'headerOptions' => ['style' => 'width: 40px']],

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 50px'],
                    ],
                    [
                        'attribute' => 'financial_year',
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'financial_year',
                            'data' => $financialYears,
                            'options' => ['placeholder' => 'Select Financial Year...', 'multiple' => true],
                            'pluginOptions' => ['allowClear' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 90px'],
                    ],
                    [
                        'attribute' => 'base_year_most_recent_audited_revenue',
                        'contentOptions' => ['class' => 'text-end fw-bold'],
                        'headerOptions' => ['style' => 'width: 120px'],
                        'value' => function($m) {
                            return is_numeric($m->base_year_most_recent_audited_revenue) 
                                ? number_format($m->base_year_most_recent_audited_revenue, 2) 
                                : $m->base_year_most_recent_audited_revenue;
                        },
                    ],
                    [
                        'attribute' => 'audited_approved_revenue_ksh',
                        'contentOptions' => ['class' => 'text-end fw-bold'],
                        'headerOptions' => ['style' => 'width: 120px'],
                        'value' => function($m) {
                            return is_numeric($m->audited_approved_revenue_ksh) 
                                ? number_format($m->audited_approved_revenue_ksh, 2) 
                                : $m->audited_approved_revenue_ksh;
                        },
                    ],
                    [
                        'attribute' => 'ef_entitlement_ksh',
                        'contentOptions' => ['class' => 'text-end fw-bold'],
                        'headerOptions' => ['style' => 'width: 120px'],
                        'value' => function($m) {
                            return is_numeric($m->ef_entitlement_ksh) 
                                ? number_format($m->ef_entitlement_ksh, 2) 
                                : $m->ef_entitlement_ksh;
                        },
                    ],
                    [
                        'attribute' => 'amount_reflected_in_dora_ksh',
                        'contentOptions' => ['class' => 'text-end fw-bold'],
                        'headerOptions' => ['style' => 'width: 120px'],
                        'value' => function($m) {
                            return is_numeric($m->amount_reflected_in_dora_ksh) 
                                ? number_format($m->amount_reflected_in_dora_ksh, 2) 
                                : $m->amount_reflected_in_dora_ksh;
                        },
                    ],
                    [
                        'attribute' => 'transfers_into_ef',
                        'contentOptions' => ['class' => 'text-end fw-bold'],
                        'headerOptions' => ['style' => 'width: 100px'],
                        'value' => function($m) {
                            return is_numeric($m->transfers_into_ef) 
                                ? number_format($m->transfers_into_ef, 2) 
                                : $m->transfers_into_ef;
                        },
                    ],
                    [
                        'attribute' => 'arrears',
                        'contentOptions' => ['class' => 'text-end fw-bold'],
                        'headerOptions' => ['style' => 'width: 100px'],
                        'value' => function($m) {
                            return is_numeric($m->arrears) 
                                ? number_format($m->arrears, 2) 
                                : $m->arrears;
                        },
                    ],

                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {update}',
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 90px'],
                        'headerOptions' => ['style' => 'width: 90px'],
                        'buttons' => [
                            'view' => fn($url) => Html::a('View', $url, ['class' => 'action-btn view-btn']),
                            'update' => fn($url) => Html::a('Edit', $url, ['class' => 'action-btn edit-btn']),
                        ],
                        'urlCreator' => function ($action, EqualisationFundEntitlements $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'id' => $model->id]);
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
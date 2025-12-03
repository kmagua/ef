<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoProjectsSearch $searchModel */
/** @var array $countyAppropriation */
/** @var array $projectCountyTotals */
/** @var array $projectSectorTotals */
/** @var array $joinedData */
/** @var int $totalProjects */
/** @var float $totalProjectFunding */
/** @var array $filterCounties */
/** @var array $filterConstituencies */
/** @var array $filterWards */
/** @var array $filterSectors */

$this->title = strtoupper('2nd Marginalization Policy - Unified Analytics');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile(
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap'
);
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #eef2f3;
    }

    .ua-wrapper {
        background: #fff;
        padding: 20px;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    h1.ua-title {
        background: linear-gradient(135deg, #00695c, #009688);
        color: #fff;
        padding: 18px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 22px;
        font-weight: 700;
        font-size: 1.7rem;
        letter-spacing: .7px;
    }

    .summary-card {
        background: #e0f7fa;
        border-left: 6px solid #00796b;
        padding: 14px;
        border-radius: 10px;
        margin-bottom: 16px;
    }
    .summary-title {
        font-size: .78rem;
        font-weight: 600;
        color: #005f56;
    }
    .summary-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #003c35;
    }

    .search-box {
        background: #e0f2f1;
        padding: 18px;
        border-radius: 12px;
        border-left: 6px solid #00897b;
        margin-bottom: 25px;
    }

    .table-wrapper {
        border-radius: 10px;
        overflow-x: auto;
        background: #fff;
        max-height: 65vh;
        scrollbar-width: thin;
        scrollbar-color: #b2dfdb #ffffff;
    }
    .table-wrapper::-webkit-scrollbar { height: 8px; width: 8px; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #80cbc4; border-radius: 12px; }

    .table thead th {
        background: #00695c !important;
        color: #fff !important;
        text-transform: uppercase;
        letter-spacing: .4px;
        font-size: .8rem;
        padding: 12px;
        position: sticky;
        top: 0;
        z-index: 8;
    }

    .table tbody tr:hover {
        background: #e0f2f1 !important;
    }

    .dashboard-card {
        border-radius: 12px;
        background: #fff;
        border: 1px solid #e0e0e0;
        padding: 18px;
        transition: .2s;
    }
    .dashboard-card:hover {
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: #004d40;
    }

    .view-all-box {
        padding: 16px;
        background: #e8f5e9;
        border-left: 6px solid #2e7d32;
        border-radius: 10px;
        margin-top: 28px;
    }

    .btn-relations {
        padding: 4px 10px;
        font-size: 11px;
        border-radius: 14px;
    }
</style>


<div class="ua-wrapper">

    <h1 class="ua-title"><?= Html::encode($this->title) ?></h1>

    <!-- SUMMARY CARDS -->
    <div class="row">
        <div class="col-md-4 col-6">
            <div class="summary-card">
                <div class="summary-title">Total Projects (Filtered)</div>
                <div class="summary-value"><?= number_format($totalProjects) ?></div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="summary-card">
                <div class="summary-title">Total Project Cost (Filtered)</div>
                <div class="summary-value">Ksh <?= number_format($totalProjectFunding, 2) ?></div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="summary-card">
                <div class="summary-title">Total Appropriation (Filtered)</div>
                <div class="summary-value">
                    Ksh <?= number_format(array_sum($countyAppropriation), 2) ?>
                </div>
            </div>
        </div>
    </div>


    <!-- FILTERS (County, Constituency, Ward, Sector) -->
    <div class="search-box">

        <?php $form = ActiveForm::begin(['method' => 'get']); ?>

        <div class="row g-3">
            <div class="col-md-3">
                <?= $form->field($searchModel, 'county')->widget(Select2::class, [
                    'data' => array_combine($filterCounties, $filterCounties),
                    'options' => ['placeholder' => 'County'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($searchModel, 'constituency')->widget(Select2::class, [
                    'data' => array_combine($filterConstituencies, $filterConstituencies),
                    'options' => ['placeholder' => 'Constituency'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($searchModel, 'ward')->widget(Select2::class, [
                    'data' => array_combine($filterWards, $filterWards),
                    'options' => ['placeholder' => 'Ward'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($searchModel, 'sector')->widget(Select2::class, [
                    'data' => array_combine($filterSectors, $filterSectors),
                    'options' => ['placeholder' => 'Sector'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <div class="text-end mt-2">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Reset', ['unified-analytics'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>


    <!-- UNIFIED TABLE WITH RELATIONS LINK -->
    <div class="dashboard-card mb-4">
        <div class="section-title">Appropriation vs Project Spending (Relations)</div>

        <div class="table-wrapper">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>County</th>
                        <th>Constituency</th>
                        <th>Ward</th>
                        <th>Marginalised Area</th>
                        <th>Allocation (Ksh)</th>
                        <th>Project Cost (Ksh)</th>
                        <th>Total Projects</th>
                        <th>Relations</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($joinedData as $row): ?>
                    <?php
                        $relationUrl = Url::to([
                            '/ef/eq-two-projects/index',
                            'EqualizationTwoProjectsSearch[county]'        => $row['county'],
                            'EqualizationTwoProjectsSearch[constituency]' => $row['constituency'],
                            'EqualizationTwoProjectsSearch[ward]'         => $row['ward'],
                        ]);
                    ?>
                    <tr>
                        <td><?= Html::encode($row['county']) ?></td>
                        <td><?= Html::encode($row['constituency']) ?></td>
                        <td><?= Html::encode($row['ward']) ?></td>
                        <td><?= Html::encode($row['marginalised_areas']) ?></td>
                        <td><?= number_format((float)($row['total_allocation'] ?? 0), 2) ?></td>
                        <td><?= number_format((float)($row['total_project_cost'] ?? 0), 2) ?></td>
                        <td><?= (int)($row['total_projects'] ?? 0) ?></td>
                        <td>
                            <a href="<?= $relationUrl ?>" class="btn btn-sm btn-outline-success btn-relations">
                                View related projects
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- CHARTS -->
    <div class="row g-4">

        <div class="col-md-5">
            <div class="dashboard-card">
                <div class="section-title">Projects by Sector</div>
                <canvas id="sectorChart"></canvas>
            </div>
        </div>

        <div class="col-md-7">
            <div class="dashboard-card">
                <div class="section-title">County: Appropriation vs Spending</div>
                <canvas id="countyChart"></canvas>
            </div>
        </div>

    </div>


    <!-- VIEW ALL RECORDS -->
    <div class="view-all-box mt-4">
        <h4 class="mb-2 text-success">View All Raw Project Records</h4>
        <p class="mb-3">
            Open the full project list with detailed records, filters and pagination.
        </p>
        <?= Html::a('View All Projects', ['/ef/eq-two-projects/index'], ['class' => 'btn btn-success']) ?>
    </div>

</div>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// SECTOR PIE CHART
const sectorLabels = <?= json_encode(array_column($projectSectorTotals, 'sector')) ?>;
const sectorCounts = <?= json_encode(array_column($projectSectorTotals, 'count')) ?>;

new Chart(document.getElementById('sectorChart'), {
    type: 'pie',
    data: {
        labels: sectorLabels,
        datasets: [{
            data: sectorCounts,
            borderWidth: 1
        }]
    }
});

// COUNTY BAR CHART
const countyLabels = <?= json_encode(array_keys($countyAppropriation)) ?>;
const countyAlloc  = <?= json_encode(array_values($countyAppropriation)) ?>;
const countyProjectCost = <?= json_encode(array_map(
    fn($r) => (float)($r['cost'] ?? 0),
    $projectCountyTotals
)) ?>;

new Chart(document.getElementById('countyChart'), {
    type: 'bar',
    data: {
        labels: countyLabels,
        datasets: [
            {
                label: 'Appropriation (Ksh)',
                data: countyAlloc,
                backgroundColor: 'rgba(54,162,235,0.6)'
            },
            {
                label: 'Project Cost (Ksh)',
                data: countyProjectCost,
                backgroundColor: 'rgba(255,99,132,0.6)'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

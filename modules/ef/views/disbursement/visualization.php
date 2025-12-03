<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Disbursement and Allocation Analysis';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-md-12 px-md-4">
            <h1 class="text-center text-white bg-success p-3 rounded shadow"><?= Html::encode($this->title) ?></h1>

            <!-- Download PDF Button -->
            <div class="text-end mb-3">
                <a href="<?= Url::to(['/ef/disbursement/report']) ?>" class="btn btn-danger">
                    📄 Download Disbursement Vs Allocation Official Report
                </a>
            </div>
            
              <div class="text-end mb-3">
                <a href="<?= Url::to(['disbursement/disbursement-report']) ?>" class="btn btn-danger">
                    📄 Generate Disbursement Report
                </a>
            </div>

            
            <div class="text-end mb-3">
    <a href="<?= Url::to(['disbursement/allocation-report']) ?>" class="btn btn-primary">
        📄 Generate Allocation Report
    </a>
</div>

            <!-- Pie Chart First -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    📊 Disbursement Analysis in %
                </div>
              <div class="card-body">
    <div id="piechart" style="width: 100%; height: 450px;"></div>
    
    <!-- Styled Note Section -->
    <p class="text-center mt-3 p-3 rounded fw-bold"
       style="background-color: #f8f9fa; color: #333; border-left: 5px solid #007bff; font-size: 16px;">
        📌 <span style="color: #007bff; font-weight: bold;">Note:</span> The chart represents the 
        <span style="color: #28a745; font-weight: bold;">total disbursed amount per county</span> 
        relative to other counties. The 
        <span style="color: #dc3545; font-weight: bold;">size of each segment</span> 
        indicates the proportion of funds disbursed.
    </p>
</div>

            </div>

            <!-- Data Table Below -->
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white fw-bold">
                    📋 Disbursement Vs Allocation Analysis
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-success text-white text-center">
                                    <th>Sn</th>
                                    <th>County</th>
                                    <th>Total Allocation</th>
                                    <th>Disbursed Amount</th>
                                    <th>% Disbursed</th>
                                    <th>Amount Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                <?php foreach ($data as $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $counter++ ?></td>
                                        <td><?= Html::encode($row['county']) ?></td>
                                        <td class="text-end"><?= number_format($row['total_allocation'], 2) ?></td>
                                        <td class="text-end"><?= number_format($row['disbursed'], 2) ?></td>
                                        <td class="text-center"><?= number_format($row['percentage_disbursed'], 2) ?>%</td>
                                        <td class="text-end <?= $row['amount_due'] < 0 ? 'text-danger fw-bold' : '' ?>">
                                            <?= number_format($row['amount_due'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Load Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['County', 'Disbursed Amount'],
            <?php foreach ($data as $row): ?>
                ['<?= $row['county'] ?>', <?= $row['disbursed'] ?>],
            <?php endforeach; ?>
        ]);

        var options = {
            title: 'Disbursement per County',
            pieHole: 0.4,
            height: 450,
            chartArea: {width: '85%', height: '80%'},
            legend: { position: 'right' },
            pieSliceText: 'percentage', // Show percentage values on the slices
            tooltip: { text: 'value' }, // Show actual disbursed values on hover
            slices: {
                0: { color: '#4682B4' }, // Steel Blue
                1: { color: '#708090' }, // Slate Gray
                2: { color: '#2F4F4F' }, // Dark Slate Gray
                3: { color: '#B0C4DE' }, // Light Steel Blue
                4: { color: '#778899' }, // Light Slate Gray
                5: { color: '#C0C0C0' }, // Silver
                6: { color: '#D3D3D3' }, // Light Gray
                7: { color: '#A9A9A9' }, // Dark Gray
                8: { color: '#696969' }  // Dim Gray
            },
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            }
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
</script>

<!-- Custom CSS -->
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f7f6;
    }

    .table thead th {
        background-color: #1b5e20 !important;
        color: white !important;
        font-weight: 600;
        text-align: center;
    }

    .table td {
        font-weight: 400;
    }

    .btn-danger:hover {
        opacity: 0.8;
    }

    .card {
        border-radius: 10px;
    }

    .card-header {
        font-weight: bold;
    }

    .text-danger {
        color: red !important;
    }

    .text-end {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }
</style>

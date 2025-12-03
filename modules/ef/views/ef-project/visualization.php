<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\data\Pagination;
?>


<style>
    /* Header Title Fix */
    .page-header {
        position: relative;
        background: linear-gradient(90deg, #43cea2, #185a9d);
        padding: 1rem;
        border-radius: .5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
  

    /* Loader */
    .loader {
        display: none;
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
  
</style>
<br>
<div class="container-fluid py-4">

   
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div class="page-header mb-2">
         <h1 class="fw-bold d-flex align-items-center gap-2" style="color: #008a8a;">
    <?= Html::encode('County Allocation Vs Disbursement') ?>
    <i class="bi bi-info-circle" style="color: #555; cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="This dashboard compares allocation vs disbursement by county." 
       onmouseover="this.style.color='#008a8a';" 
       onmouseout="this.style.color='#555';"></i>
</h1>

        </div>
        <div class="mt-2 mt-md-0">
            <a href="<?= Url::to(['/ef/ef-project/report']) ?>" class="btn btn-danger me-2">📄 Download Official Report</a>
            <button id="toggleTheme" class="btn btn-secondary me-2">🌓 Toggle Theme</button>
            <button id="refreshData" class="btn btn-outline-primary">🔄 Refresh Data</button>
        </div>
    </div>

    <!-- Metrics Section -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5 class="fw-bold">Total Allocation</h5>
                    <h2 class="text-success" id="totalAllocationCounter">
                        <?= $grandTotalAllocation > 0 ? number_format($grandTotalAllocation) : 'No Data' ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5 class="fw-bold">Total Disbursed</h5>
                    <h2 class="text-primary" id="totalDisbursedCounter">
                        <?= $grandTotalDisbursed > 0 ? number_format($grandTotalDisbursed) : 'No Data' ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5 class="fw-bold">Overall Disbursement %</h5>
                    <h2 class="text-warning" id="totalPercentCounter">
                        <?= $grandTotalAllocation > 0 ? round(($grandTotalDisbursed / $grandTotalAllocation) * 100, 2) . '%' : '0%' ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Loader -->
    <div id="chartsLoader" class="loader mb-4"></div>

    <!-- Charts Row -->
    <div class="row" id="chartsContent" style="display:none;">
        <!-- Your charts go here, as per previous code -->
    </div>
</div>

<script>
    $(document).ready(function(){
        // Enable tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Show loader then load charts
        $("#chartsLoader").show();
        setTimeout(function() {
            $("#chartsLoader").hide();
            $("#chartsContent").fadeIn();
            drawCharts();
        }, 1000);

        // Refresh Data button
        $('#refreshData').click(function() {
            location.reload();
        });
    });
</script>


    <!-- Row for Charts -->
    <div class="row mb-4">
        
        <!-- Column Chart -->
        <div class="col-lg-6 mb-4">
           
            <div class="card shadow">
                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                    📊 County-wise Allocation vs Disbursement
                    <button id="downloadColumn" class="btn btn-light btn-sm">⬇️ Export</button>
                </div>
                <div class="card-body">
                    <div id="countyHistogram" style="height: 500px;"></div>
                </div>
            </div>
        </div>

        <!-- Donut Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                    🍩 Disbursement Breakdown (Donut)
                    <button id="downloadDonut" class="btn btn-light btn-sm">⬇️ Export</button>
                </div>
                <div class="card-body">
                    <div id="disbursementDonutChart" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stacked Bar Graph -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white fw-bold d-flex justify-content-between align-items-center">
                    📊 Stacked Bar - Allocation vs Disbursement
                    <button id="downloadStacked" class="btn btn-light btn-sm">⬇️ Export</button>
                </div>
                <div class="card-body">
                    <div id="stackedBarChart" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                    🗂️ Detailed Allocation vs Disbursement Table
                </div>
                <div class="card-body">
                    <!-- Custom filter input -->
                    <div class="mb-3">
                        <input type="text" id="customSearch" placeholder="🔍 Search Counties..." class="form-control">
                    </div>
                    <table id="allocationTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>County</th>
                                <th>Total Allocation (KES)</th>
                                <th>Total Disbursed (KES)</th>
                                <th>Disbursement %</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grandTotalAllocation = 0;
                            $grandTotalDisbursed = 0;
                            foreach ($groupedData as $county => $row): 
                                $percentage = $row['total_allocation'] > 0 ? round(($row['total_disbursed'] / $row['total_allocation']) * 100, 2) : 0;
                                $grandTotalAllocation += $row['total_allocation'];
                                $grandTotalDisbursed += $row['total_disbursed'];
                            ?>
                                <tr>
                                    <td><?= $county ?></td>
                                    <td><?= number_format($row['total_allocation']) ?></td>
                                    <td><?= number_format($row['total_disbursed']) ?></td>
                                    <td><?= $percentage ?>%</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar <?= $percentage < 50 ? 'bg-danger' : ($percentage < 80 ? 'bg-warning' : 'bg-success') ?>" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"><?= $percentage ?>%</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<!-- External Libraries -->
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<script>
    const grandTotalAllocation = <?= $grandTotalAllocation ?>;
    const grandTotalDisbursed = <?= $grandTotalDisbursed ?>;
    const grandTotalPercent = grandTotalAllocation > 0 ? ((grandTotalDisbursed / grandTotalAllocation) * 100).toFixed(2) : 0;

    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        drawHistogram();
        drawDonutChart();
        drawStackedBar();
    }

    // Column Chart
    function drawHistogram() {
        var data = google.visualization.arrayToDataTable([
            ['County', 'Total Allocation', 'Disbursed', { role: 'tooltip', type: 'string', p: { html: true } }],
            <?php foreach ($groupedData as $county => $row): ?>
                ['<?= $county ?>', <?= $row['total_allocation'] ?>, <?= $row['total_disbursed'] ?>, '<b><?= $county ?></b><br>Allocated: <?= number_format($row['total_allocation']) ?><br>Disbursed: <?= number_format($row['total_disbursed']) ?>'],
            <?php endforeach; ?>
        ]);
        var options = {
            tooltip: { isHtml: true },
            legend: { position: 'top' },
            animation: { startup: true, duration: 1000, easing: 'out' },
            colors: ['#2E7D32', '#FF7043'],
            hAxis: { title: 'Counties', slantedText: true, slantedTextAngle: 45 },
            vAxis: { title: 'KES', format: 'short' },
            bar: { groupWidth: '65%' },
            chartArea: { width: '85%', height: '70%' }
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('countyHistogram'));
        chart.draw(data, options);
        google.visualization.events.addListener(chart, 'ready', function () {
            document.getElementById('downloadColumn').onclick = function() {
                var imgUri = chart.getImageURI();
                downloadImage(imgUri, 'column_chart.png');
            }
        });
    }

    // New Donut Chart
    function drawDonutChart() {
        var data = google.visualization.arrayToDataTable([
            ['County', 'Disbursed'],
            <?php foreach ($groupedData as $county => $row): ?>
                ['<?= $county ?>', <?= $row['total_disbursed'] ?>],
            <?php endforeach; ?>
        ]);
        var options = {
            pieHole: 0.5,
            tooltip: { text: 'percentage' },
            chartArea: { width: '80%', height: '80%' },
            legend: { position: 'right' },
            animation: { startup: true, duration: 1000, easing: 'out' }
        };
        var chart = new google.visualization.PieChart(document.getElementById('disbursementDonutChart'));
        chart.draw(data, options);
        google.visualization.events.addListener(chart, 'ready', function () {
            document.getElementById('downloadDonut').onclick = function() {
                var imgUri = chart.getImageURI();
                downloadImage(imgUri, 'donut_chart.png');
            }
        });
    }

    // Stacked Bar Chart (unchanged)
    function drawStackedBar() {
        var data = google.visualization.arrayToDataTable([
            ['County', 'Allocation', 'Disbursed'],
            <?php foreach ($groupedData as $county => $row): ?>
                ['<?= $county ?>', <?= $row['total_allocation'] ?>, <?= $row['total_disbursed'] ?>],
            <?php endforeach; ?>
        ]);
        var options = {
            isStacked: true,
            colors: ['#00897B', '#F9A825'],
            legend: { position: 'top' },
            animation: { startup: true, duration: 1000, easing: 'out' },
            hAxis: { title: 'Counties', slantedText: true, slantedTextAngle: 45 },
            vAxis: { title: 'KES', format: 'short' },
            chartArea: { width: '85%', height: '70%' }
        };
        var chart = new google.visualization.BarChart(document.getElementById('stackedBarChart'));
        chart.draw(data, options);
        google.visualization.events.addListener(chart, 'ready', function () {
            document.getElementById('downloadStacked').onclick = function() {
                var imgUri = chart.getImageURI();
                downloadImage(imgUri, 'stacked_chart.png');
            }
        });
    }

    // Download Utility
    function downloadImage(uri, filename) {
        var a = document.createElement('a');
        a.href = uri;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    // Smooth Scroll
    function scrollToSection(id) {
        document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
    }

    // Theme toggle with localStorage + DataTable custom filter
    $(document).ready(function() {
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'enabled') enableDarkMode();

        $('#toggleTheme').click(function(){
            if ($('body').hasClass('bg-dark')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });

        function enableDarkMode() {
            $('body, .card, .dataTables_wrapper').addClass('bg-dark text-white');
            localStorage.setItem('darkMode', 'enabled');
        }

        function disableDarkMode() {
            $('body, .card, .dataTables_wrapper').removeClass('bg-dark text-white');
            localStorage.setItem('darkMode', 'disabled');
        }

        const table = $('#allocationTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });

        // Link custom search box
        $('#customSearch').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Animate metrics
        animateCounter('#totalAllocationCounter', grandTotalAllocation);
        animateCounter('#totalDisbursedCounter', grandTotalDisbursed);
        animateCounter('#totalPercentCounter', grandTotalPercent, '%');
    });

    function animateCounter(selector, end, suffix = '') {
        $({ countNum: 0 }).animate({ countNum: end }, {
            duration: 1500,
            easing: 'swing',
            step: function () {
                $(selector).text(Math.floor(this.countNum).toLocaleString() + suffix);
            },
            complete: function () {
                $(selector).text(parseFloat(this.countNum).toLocaleString() + suffix);
            }
        });
    }
</script>

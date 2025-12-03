<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\models\UserLoginActivity;
use app\models\UserRole;
use app\models\AuthRole;

$this->title = 'User Login Activity';
$this->params['breadcrumbs'][] = $this->title;

// ✅ Dashboard summary
$totalLogins   = UserLoginActivity::find()->count();
$successLogins = UserLoginActivity::find()->where(['login_status' => 'success'])->count();
$failedLogins  = UserLoginActivity::find()->where(['login_status' => 'failed'])->count();
$activeUsers   = UserLoginActivity::find()->where(['login_status' => 'success'])->distinct('user_id')->count();
$totalAttempts = UserLoginActivity::find()->sum('attempt_count');

// ✅ Top 5 users with roles
$topUsers = UserLoginActivity::find()
    ->select(['user_id', 'COUNT(*) AS login_count'])
    ->where(['login_status' => 'success'])
    ->groupBy('user_id')
    ->orderBy(['login_count' => SORT_DESC])
    ->limit(5)
    ->with(['user.userRole.role'])
    ->asArray()
    ->all();
?>

<style>
/* ===== Dashboard Styling ===== */
.dashboard-card {
    border-radius: 14px;
    color: #fff !important;
    text-align: center;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.dashboard-card * { color: #fff !important; }
.dashboard-card h6 { font-weight: 400; opacity: 0.9; letter-spacing: 0.4px; }
.dashboard-card h3 { font-weight: 700; font-size: 2rem; }

/* ===== Gradients ===== */
.bg-green { background: linear-gradient(135deg, #27ae60, #2ecc71); }
.bg-brown { background: linear-gradient(135deg, #8b5e3c, #a47148); }
.bg-darkgreen { background: linear-gradient(135deg, #1e8449, #229954); }
.bg-fail { background: linear-gradient(135deg, #c0392b, #e74c3c); }

/* ===== Chart Containers ===== */
.chart-container {
    background: #fff;
    border-radius: 12px;
    padding: 1.4rem;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.chart-container:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.12); }

/* ===== Tables ===== */
.table-responsive {
    border-radius: 10px;
    overflow-x: auto;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.table {
    background-color: #fff;
    border-collapse: separate !important;
    border-spacing: 0;
}
.table thead th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    border-bottom: 2px solid #dee2e6;
}
.table tbody tr:hover {
    background-color: #f6f9fc;
    transition: background-color 0.2s ease-in-out;
}
.table tfoot td {
    background: #fafafa;
    font-weight: 600;
}
.badge-lg { font-size: 0.9rem; padding: 0.45em 0.8em; }

/* ===== Card Header ===== */
.card-header.bg-primary {
    background: linear-gradient(135deg, #0062cc, #007bff);
}
</style>

<div class="container-fluid py-4">

    <!-- ✅ Summary Stats -->
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card bg-green shadow">
                <div class="card-body">
                    <h6>Total Logins</h6>
                    <h3><?= $totalLogins ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card bg-brown shadow">
                <div class="card-body">
                    <h6>Active Users</h6>
                    <h3><?= $activeUsers ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card bg-darkgreen shadow">
                <div class="card-body">
                    <h6>Successful Logins</h6>
                    <h3><?= $successLogins ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card bg-fail shadow">
                <div class="card-body">
                    <h6>Failed Logins</h6>
                    <h3><?= $failedLogins ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 Charts -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3 text-secondary">Login Success Ratio</h6>
                <canvas id="loginPieChart"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3 text-secondary">Logins (Last 7 Days)</h6>
                <canvas id="loginBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 👑 Top 5 Users -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-trophy-fill"></i> Top 5 Active Users</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th class="text-center">Logins</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topUsers)): ?>
                                    <?php foreach ($topUsers as $i => $u): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= Html::encode($u['user']['user_names'] ?? 'Unknown') ?></td>
                                            <td><?= Html::encode($u['user']['userRole']['role']['role_name'] ?? 'N/A') ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-success badge-lg"><?= $u['login_count'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart for Top 5 Users -->
        <div class="col-md-6 mb-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3 text-secondary">Top 5 Users Chart</h6>
                <canvas id="topUsersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 🧩 Login Activity Table -->
    <div class="card shadow border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-shield-lock"></i> <?= Html::encode($this->title) ?></h5>
            <div>
                <span class="badge bg-success me-2"><i class="bi bi-people"></i> <?= $activeUsers ?> Active</span>
                <?= Html::a('<i class="bi bi-download"></i> Export CSV', ['export-login-activity'], ['class' => 'btn btn-light btn-sm']) ?>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <?php Pjax::begin(); ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel'  => $searchModel,
                    'tableOptions' => ['class' => 'table table-striped table-hover align-middle mb-0'],
                    'headerRowOptions' => ['class' => 'table-light'],
                    'showFooter' => true,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'user_name',
                            'label' => 'User',
                            'value' => fn($m) => $m->user->user_names ?? 'Unknown',
                            'footer' => '<b>Totals:</b>',
                        ],
                        [
                            'label' => 'Role',
                            'value' => fn($m) => $m->user->userRole->role->role_name ?? 'N/A',
                        ],
                        [
                            'attribute' => 'login_at',
                            'format' => ['datetime', 'php:M d, Y H:i'],
                        ],
                        [
                            'attribute' => 'logout_at',
                            'format' => ['datetime', 'php:M d, Y H:i'],
                        ],
                        [
                            'attribute' => 'login_status',
                            'format' => 'raw',
                            'filter' => ['success' => 'Success', 'failed' => 'Failed'],
                            'value' => fn($m) => $m->login_status === 'success'
                                ? "<span class='badge bg-success badge-lg'><i class='bi bi-check-circle'></i> Success</span>"
                                : "<span class='badge bg-danger badge-lg'><i class='bi bi-x-circle'></i> Failed</span>",
                            'footer' => "<span class='badge bg-success'>Success: {$successLogins}</span> 
                                        <span class='badge bg-danger'>Failed: {$failedLogins}</span>",
                        ],
                        [
                            'attribute' => 'auth_method',
                            'format' => 'raw',
                            'value' => fn($m) => "<span class='badge bg-info text-dark badge-lg'>" . strtoupper($m->auth_method) . "</span>",
                        ],
                        'login_ip',
                        'browser',
                        'os',
                        'device',
                        'location',
                        [
                            'attribute' => 'risk_score',
                            'format' => 'raw',
                            'value' => function($m) {
                                $color = $m->risk_score >= 5 ? 'danger' : ($m->risk_score >= 3 ? 'warning' : 'success');
                                return "<span class='badge bg-{$color} badge-lg'>{$m->risk_score}</span>";
                            },
                            'footer' => "<span class='badge bg-secondary'>Attempts: {$totalAttempts}</span>",
                            'contentOptions' => ['class' => 'text-center'],
                        ],
                        [
                            'label' => 'Login Count',
                            'format' => 'raw',
                            'value' => function($m) {
                                $count = UserLoginActivity::find()
                                    ->where(['user_id' => $m->user_id, 'login_status' => 'success'])
                                    ->count();
                                return "<span class='badge bg-primary badge-lg'><i class='bi bi-bar-chart'></i> {$count}</span>";
                            },
                            'footer' => "<span class='badge bg-primary'>Total Logins: {$totalLogins}</span>",
                            'contentOptions' => ['class' => 'text-center fw-bold'],
                        ],
                    ],
                ]) ?>

                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>

<!-- 📈 Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Pie Chart
    new Chart(document.getElementById('loginPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Success', 'Failed'],
            datasets: [{
                data: [<?= $successLogins ?>, <?= $failedLogins ?>],
                backgroundColor: ['#28a745', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Bar Chart (Last 7 Days)
    new Chart(document.getElementById('loginBarChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(
                array_map(fn($d) => date('M d', strtotime($d['date'])),
                    Yii::$app->db->createCommand("
                        SELECT DATE(login_at) as date, COUNT(*) as count
                        FROM user_login_activity
                        WHERE login_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY DATE(login_at)
                        ORDER BY date ASC
                    ")->queryAll()
                )
            ) ?>,
            datasets: [{
                label: 'Logins',
                data: <?= json_encode(
                    array_map(fn($d) => (int)$d['count'],
                        Yii::$app->db->createCommand("
                            SELECT DATE(login_at) as date, COUNT(*) as count
                            FROM user_login_activity
                            WHERE login_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                            GROUP BY DATE(login_at)
                            ORDER BY date ASC
                        ")->queryAll()
                    )
                ) ?>,
                backgroundColor: '#007bff',
                borderRadius: 6
            }]
        },
        options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });

    // Top 5 Users Chart
    new Chart(document.getElementById('topUsersChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(fn($u) => $u['user']['user_names'] ?? 'Unknown', $topUsers)) ?>,
            datasets: [{
                label: 'Logins',
                data: <?= json_encode(array_map(fn($u) => (int)$u['login_count'], $topUsers)) ?>,
                backgroundColor: '#20c997',
                borderRadius: 6
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
});
</script>

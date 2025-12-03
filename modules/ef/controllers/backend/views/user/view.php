<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'email:email',
            'user_names',
            //'password',
            'status',
            //'recent_passwords',
            //'authKey',
            //'accessToken',
            'last_password_change_date',
            'last_login_date',
            'date_created',
            'last_updated',
        ],
    ]) ?>
    <h3>Audit Trail</h3>
    <?php
    $searchModel = new app\modules\backend\models\AuditTrailSearch();
    $searchModel->model_id = $model->id;
    $searchModel->model = 'app\models\User';
    $trailDataProvider = $searchModel->search([]);
    ?>
    
    <?= yii\grid\GridView::widget([
        'dataProvider' => $trailDataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'old_value:ntext',
            'new_value:ntext',
            'action',
            'model',
            'field',
            'stamp',
            //'comments',
            'user_id',
            //'model_id',
            //'change_no',
            
        ],
    ]); ?>
</div>

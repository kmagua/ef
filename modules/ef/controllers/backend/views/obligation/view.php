<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\modules\backend\models\ObligationData;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Obligation $model */

$this->title = $model->description;
$this->params['breadcrumbs'][] = ['label' => 'Obligations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="obligation-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <!--<p>
        <?= ''//Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= ''/*Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>-->

    <?php
    $searchModel = new \app\modules\backend\models\ObligationDataSearch();
    $searchModel->obligation_id = $model->id;
    $dataProvider = $searchModel->search([]);
    
    ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'obligation_id',
            'fy',
            'amt',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ObligationData $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

</div>

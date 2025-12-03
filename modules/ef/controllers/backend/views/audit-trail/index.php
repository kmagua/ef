<?php

use app\modules\backend\models\AuditTrail;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\AuditTrailSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Audit Trails';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    .table th a {
  color: #fff !important;
}
    .table th a:hover {
  color: #000 !important;
}
    .bg-primary h1 {
        font-size: 1em;
        font-weight: bold;
        text-shadow: 2px 2px 4px #000;
        margin-bottom: 0;
        font-family: 'Poppins', sans-serif;
    }

    .county-budget-index {
        background-color: #f9f9f9;
        padding: 2px;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        margin-top: 2px;
    }

    .county-budget-index .grid-view {
        margin-top: 20px;
    }

    .county-budget-index .grid-view th {
        background-color: #7C4102;
        color: white !important;
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-size: 1em;
        padding: 10px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        border-bottom: 2px solid #5A2E01;
    }

    .county-budget-index .grid-view td {
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-size: 1em;
        padding: 15px;
        border-bottom: 1px solid #ddd;
        transition: background-color 0.3s ease;
    }

    .county-budget-index .grid-view td:hover {
        background-color: #f1f1f1;
    }

    .county-budget-index .grid-view td:nth-child(even) {
        background-color: #f7f7f7;
    }

    .county-budget-index .action-column {
        width: 70px;
    }

    .county-budget-index .btn-view {
        color: #fff;
        background-color: #005baa;
        border-color: #005baa;
        padding: 5px 10px;
        border-radius: 4px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .county-budget-index .btn-view:hover {
        background-color: #003f8a;
        border-color: #003f8a;
    }

    .bg-gold {
        background-color: #7C4102;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .bg-gold h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5em;
        font-weight: bold;
        text-shadow: 3px 3px 6px #000;
        margin: 0;
        color: white;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination li {
        margin: 0 5px;
        list-style: none;
    }

    .pagination a,
    .pagination span {
        display: block;
        padding: 10px;
        border: 1px solid #007bff;
        color: #007bff;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .pagination .active a,
    .pagination .active span {
        background-color: #007bff;
        color: white;
    }

    .pagination a:hover {
        background-color: #0056b3;
        color: white;
    }

    .table-responsive {
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .county-budget-index {
            padding: 10px;
        }

        .county-budget-index .grid-view th,
        .county-budget-index .grid-view td {
            font-size: 0.9em;
            padding: 10px;
        }

        .pagination a,
        .pagination span {
            padding: 8px;
            font-size: 0.9em;
        }
    }

    @media (max-width: 576px) {
        .bg-gold h1 {
            font-size: 1.8em;
        }

        .county-budget-index .grid-view th,
        .county-budget-index .grid-view td {
            font-size: 0.8em;
            padding: 8px;
        }

        .pagination a,
        .pagination span {
            padding: 6px;
            font-size: 0.8em;
        }
    }

    .table th,
    .table td {
        padding: 10px;
        text-align: left;
        border: 1px solid #1e73be;
    }
</style>
<div class="audit-trail-index">

   
     <h1 style="background-color:goldenrod; color: white!important; padding: 10px 20px; text-align: center; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); font-size: 24px;"><?= Html::encode($this->title) ?></h1>


    <p>
        <?= Html::a('Create Audit Trail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'old_value:ntext',
            'new_value:ntext',
            'action',
            'model',
            //'field',
            //'stamp',
            //'comments',
            //'user_id',
            //'model_id',
            //'change_no',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, AuditTrail $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

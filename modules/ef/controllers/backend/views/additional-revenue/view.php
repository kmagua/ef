<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\AdditionalRevenue $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Additional Revenues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body, html {
        font-family: 'Poppins', sans-serif !important;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .table th, .table td {
        padding: 12px 15px;
        border: 1px solid #7c4102;
        text-align: left;
    }

    .table th {
        background-color: #7c4102;
        color: white;
         border: 1px solid #fff;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 0.1em;
    }
    .table th a {
        color: white;
    }
    .table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
   .table a {
      color: #7C4102FF;
    
    .table td a:hover {
      color: #ff0000;
    }      
    .table tr:hover {
        background-color: #f1f1f1;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a,
    .pagination span {
        display: block;
        padding: 10px;
        border: 1px solid #007bff;
        text-align: center;
        text-decoration: none;
        color: #007bff;
        border-radius: 5px;
        margin: 0 5px;
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

    h1 {
        background-color: #BB8114;
        color: white;
        padding: 10px 20px;
        text-align: center;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        font-size: 24px;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
  </style>

<div class="additional-revenue-view">

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
            'fy',
            'county.CountyName',
            'project.project_name',
            'project_amt',
            'actual_amt',
            'allocationCode.description',
        ],
    ]) ?>

</div>

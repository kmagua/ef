<?php

use app\modules\backend\models\DocumentLibrary;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentLibrarySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'IGFR Document Library';
$dataProvider->pagination->pageSize = 15;
$this->params['breadcrumbs'][] = $this->title;
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
        color: white !important;
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
  
<div class="document-library-index">

  
     <h1 style="background-color:goldenrod; color: white!important; padding: 10px 20px; text-align: center; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); font-size: 24px;"><?= Html::encode($this->title) ?></h1>


    <p>
        <?= Html::a('New Document Library', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'document_name',
            'documentType.document_type',
            'financialYear.financial_year',
            [
                'label' => 'Document',
                'content' => function($data){
                    return $data->fileLink();
                }
            ],
            //'keywords:ntext',
            //'upload_date',
             [
            'attribute' => 'uploadedBy.user_names', 
            'label' => 'Uploaded By', 
            'value' => function ($model) {
               

                return $model->uploadedBy ? $model->uploadedBy->user_names : 'Unknown';
            },
        ],
            //'publish_status',
            //'published_date',
            //'published_by',
            //'document_date',
            //'status',
            //'updated_at',
            //'deleted_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, DocumentLibrary $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

</div>
</div>





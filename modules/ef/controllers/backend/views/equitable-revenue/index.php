<?php

use app\modules\backend\models\EquitableRevenueShare;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\EquitableShareSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$dataProvider->pagination->pageSize = 25;
$this->title = 'County Equitable Revenue Shares';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    
/* Add this style to your existing styles */
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
    padding: 2px;
    border: 4px solid goldenrod;
    color: brown;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
}

.pagination .active a,
.pagination .active span {
    background-color: #007bff;
    color: white;
}
</style>
<div class="equitable-revenue-share-index">

     <h1 style="background-color:goldenrod; color: white!important; padding: 10px 20px; text-align: center; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); font-size: 24px;"><?= Html::encode($this->title) ?></h1>


    <p>
        <?= Html::a('New Record', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'cnt_name',
                'content' => function($model){
                    return $model->county->CountyName;
                }
            ],
            'fy', 
          [
            'attribute' => 'project_amt',
            'value' => function ($model) {
                return Yii::$app->formatter->asDecimal($model->project_amt, 2);
            },
        ],
                    [
    'attribute' => 'actual_amt',
    'value' => function ($model) {
        if ($model->actual_amt === null) {
            // Option 1: Return a default value, e.g., '0.00'
            return Yii::$app->formatter->asDecimal(0, 2);

            // Option 2: Return a custom message, e.g., 'Not Available'
            // return 'Not Available';
        } else {
            return Yii::$app->formatter->asDecimal($model->actual_amt, 2);
        }
    },
],

            
            //'balance_bf',
            //'osr_projected',
            //'osr_actual',
            //'fy',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, EquitableRevenueShare $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); 


    ?>

</div>
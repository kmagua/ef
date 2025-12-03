<?php

use app\modules\backend\models\Obligation;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ObligationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Obligations';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    
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
<div class="obligation-index">

  <h1 style="background-color:goldenrod; color: white!important; padding: 10px 20px; text-align: center; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); font-size: 24px;"><?= Html::encode($this->title) ?></h1>


    <p>
        <?= Html::a('New Obligation Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'description:ntext',
            [
                'template' => '{view} | {update} | {adddata}',
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Obligation $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
                ,
                'buttons'=> [   
                    'adddata' => function ($url, $model) {
                       return Html::a('add data', ['/backend/obligation-data/add', 'oid'=>$model->id], [
                           'onclick' => "getDataForm(this.href, '<h2>Add Data for an Obligation</h2>'); return false;",
                       ]);
                    }
                ],
            ],
        ],
    ]); ?>


</div>

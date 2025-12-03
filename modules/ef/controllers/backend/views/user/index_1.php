<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Budget Portal Users';
$this->params['breadcrumbs'][] = $this->title;
$dataProvider->pagination->pageSize = 10;
?>
<style>
  
  body {
        font-family: 'Poppins', sans-serif !important;
    }

.table {
    width: 100%;
    color: black !important;
    border: 1px solid #007bff;
}
.table th, .table td {
    border: 4px solid #ddd;
    padding: 10px;
    font-size: 16px;
     
}
   .table th,
.table td {
    padding: 10px; /* Adjust the padding as needed */
    text-align: left; /* Adjust the text alignment as needed */
    border: 1px solid #000; /* Add border for each cell if needed */
  
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
}

.pagination .active a,
.pagination .active span {
    background-color: #7C4102;
    color: white;
}
 
    .pagination li {
        margin: 0 5px;
        list-style: none;
    }

    .pagination a,
    .pagination span {
        padding: 5px;
        border: 1px solid #007bff;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<div class="user-index">

<h1 style="background-color: #BB8114; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); font-size: 24px;"><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('New User', ['new'], ['class' => 'btn btn-success']) ?>
        
         <?= Html::a('New County User', ['new-county-user'], ['class' => 'btn btn-info', 'style'=>'float:right']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'email:email',
                 [
    'attribute' => ['first_name', 'last_name'], 
    'label' => 'Full Names',
    'value' => function ($model) {
        return $model->first_name . ' ' . $model->last_name;
    }
],
            
      

            //'',
            [
                'label' => 'Role(s)',
                'content' => function($data){
                    $roles = $data->getRoleNames();
                    return implode(', ', $roles);
                }
            ],
            [
                'label' => 'MCDA',
                'value' => function($data){
                    return $data->getCounty();
                }
            ],
            'status',
            'last_login_date',            
            
            //'date_created',
            //'last_updated',

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 4%'],
                //'visible'=> Yii::$app->user->isGuest ? false : true,
                'template' => '{view} {update} {change_role} {change_pwd}',
                'buttons'=>[
                    'view' => function ($url, $model) {
                        return Html::a('', $url, ['class' => 'bi bi-eye',
                            'title' =>"View User"]);
                    },
                    'change_role' => function ($url, $model) {
                        return Html::a('', ['user/change-role', 'id' => $model->id], ['class' => 'bi bi-lock',
                            'title' =>"Change User Role"]);
                    },
                    'change_pwd' => function ($url, $model){
                        if($model->id == Yii::$app->user->identity->id || Yii::$app->user->identity->inGroup('publisher')){
                            return Html::a('', ['user/change-password', 'id' => $model->id], ['class' => 'bi bi-unlock',
                                'title' =>"Change User Password"]);
                        }
                        return '';
                    }
                ],
            ],
        ],
    ]); ?>


</div>

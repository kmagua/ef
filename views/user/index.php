<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
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

</style>
<div class="user-index">

    
     <h1 style="background-color:goldenrod; color: white!important; padding: 10px 20px; text-align: center; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); font-size: 24px;"><?= Html::encode($this->title) ?></h1>


    <p>
        <?= Html::a('Add User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'email:email',
            'user_names',
            //'password',
            //'status',
            //'recent_passwords',
            //'authKey',
            //'accessToken',
            //'last_password_change_date',
            //'last_login_date',
            //'date_created',
            //'last_updated',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

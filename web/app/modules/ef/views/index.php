<?php

use app\modules\ef\models\EqualizationFundProject;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationFundProjectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Equalization Fund Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equalization-fund-project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Equalization Fund Project', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'project_name',
            'county',
            'constituency',
            'sector',
            //'budget_2018_19',
            //'contract_sum',
            //'percent_completion',
            //'funding_source',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, EqualizationFundProject $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

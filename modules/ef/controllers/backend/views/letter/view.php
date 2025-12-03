<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Letter $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Letters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="letter-view">

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
            'title',
            'from_county',
            'entity_id',
            'letter',
            'status',
            'added_by',
            'date_added',
        ],
    ]) ?>
    
    <?php
    $actSearchModel = new \app\modules\backend\models\LetterActionSearch();
    $actSearchModel->letter_id = $model->id;
    $actDataProvider= $actSearchModel->search([]);
    ?>
    <p>&nbsp;</p>
    <h3>Actions</h3>
    <?= yii\grid\GridView::widget([
        'dataProvider' => $actDataProvider,
        //'filterModel' => $actSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'action_name',
            'assignedTo.user_names',
            'comment',
            'action_by',
            'file_upload',
            //'date_actioned',
            
        ],
    ]); ?>
    

</div>

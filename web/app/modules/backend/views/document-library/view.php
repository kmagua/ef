<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentLibrary $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Document Libraries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="document-library-view">

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
            'id',
            'document_name',
            'document_type',
            'financial_year',
            'document_upload_path',
            'keywords:ntext',
            'upload_date',
            'uploaded_by',
            'publish_status',
            'published_date',
            'published_by',
            'document_date',
            'status',
            'updated_at',
            'deleted_at',
        ],
    ]) ?>

</div>

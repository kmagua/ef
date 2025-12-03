<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\County $model */

$this->title = 'Update County: ' . $model->CountyId;
$this->params['breadcrumbs'][] = ['label' => 'Counties', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->CountyId, 'url' => ['view', 'CountyId' => $model->CountyId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="county-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

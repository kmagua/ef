<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Fiscal $model */

$this->title = 'Update Fiscal: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fiscals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fiscal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

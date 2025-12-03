<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ExternalEntity $model */

$this->title = 'Update External Entity: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'External Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="external-entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

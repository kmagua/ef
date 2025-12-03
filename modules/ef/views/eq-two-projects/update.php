<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoProjects $model */

$this->title = 'Update Equalization Two Projects: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equalization Two Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equalization-two-projects-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

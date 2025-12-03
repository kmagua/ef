<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ExternalEntity $model */

$this->title = 'New External Entity';
$this->params['breadcrumbs'][] = ['label' => 'External Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

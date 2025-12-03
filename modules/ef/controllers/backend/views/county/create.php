<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\County $model */

$this->title = 'Create County';
$this->params['breadcrumbs'][] = ['label' => 'Counties', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="county-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

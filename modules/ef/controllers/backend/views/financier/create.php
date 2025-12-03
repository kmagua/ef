<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Financier $model */

$this->title = 'Create Financier';
$this->params['breadcrumbs'][] = ['label' => 'Financiers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="financier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

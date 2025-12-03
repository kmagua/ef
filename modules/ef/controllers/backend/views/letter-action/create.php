<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\LetterAction $model */

$this->title = 'Create Letter Action';
$this->params['breadcrumbs'][] = ['label' => 'Letter Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="letter-action-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\ef\models\EqualizationTwoProjects $model */

$this->title = 'Create Equalization Two Projects';
$this->params['breadcrumbs'][] = ['label' => 'Equalization Two Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equalization-two-projects-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

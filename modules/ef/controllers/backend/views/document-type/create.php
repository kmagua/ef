<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\DocumentType $model */

$this->title = 'New Document Type';
$this->params['breadcrumbs'][] = ['label' => 'Document Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

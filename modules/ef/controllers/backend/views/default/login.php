<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
$this->title = 'IGFR Portal Login';
$this->params['breadcrumbs'][] = $this->title;
?>


<style>
    :root {
        --primary-color: #007bff;
        --background-gradient-start: #485563;
        --background-gradient-end: #29323c;
        --input-background: #e3e3e3;
        --hover-color: #0056b3;
        --input-height: 36px; /* Reduced height for input fields */
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(to right, var(--background-gradient-start), var(--background-gradient-end));
        color: #fff;
    }

    .site-login {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 50px; /* Reduced padding for a more compact form */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        max-width: 700px; /* Limiting max width for a slimmer form */
      
    }

.login-title {
    font-size: 2.4rem; /* Larger font size for emphasis */
    color: #fff; /* Set text color to white */
    text-align: center; /* Center the title */
    font-weight: bold; /* Make the text bold */
    text-transform: uppercase; /* Uppercase letters for a more formal appearance */
    letter-spacing: 1.2px; /* Add spacing between letters */
    margin-bottom: 30px; /* Add some space below the title */
    padding: 20px 0; /* Add padding to the top and bottom for better spacing */
    border-radius: 10px; /* Rounded corners for a softer look */
  
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Enhanced text shadow for better depth and readability */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Soft box shadow for a floating effect */
}

    .form-control {
        background: var(--input-background);
        border: none;
        box-shadow: inset 2px 2px 5px #babecc, inset -5px -5px 10px #fff;
        border-radius: 15px;
        padding: 15px 15px;
        color: #333;
        height: var(--input-height); /* Apply reduced height */
    }

    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        box-shadow: 3px 3px 8px #b1b1b1, -3px -3px 8px #ffffff;
        transition: all 0.3s ease;
        font-weight: bold;
    }

    
    .btn-primary:hover {
        background-color: var(--hover-color);
        box-shadow: none;
    }
    @media (max-width: 768px) {
        .site-login {
            padding: 10px;
            border-radius: 15px;
        }

        .form-control {
            padding: 8px;
        }

        .btn-block {
            width: 100%;
        }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<div class="site-login">
   <h1 class="login-title"><?= Html::encode($this->title) ?></h1>
    <p>Please fill out the following fields to login:</p>
    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label'],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div>{error}</div>",
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?= Html::a('Password Reset',['/backend/user/reset-password'], ['class' => 'btn btn-primary btn-block']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

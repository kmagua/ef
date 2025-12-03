<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'IGFR Portal Reset Password';
$this->params['breadcrumbs'][] = $this->title;

//$this->registerCss($css);
$web = Yii::getAlias("@web");
?>
<style>
    .site-login {
        width: 100%;
        max-width: 1000px; 
        
        background: linear-gradient(to bottom right, #ffffff, #f0f0f0); /* Gradient background */
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12), 0 2px 4px rgba(0, 0, 0, 0.06); 
        border-radius: 15px;
        border: 3px solid #7C4102; 
        text-align: left;
        position: relative; 
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease; 
    }
    .site-login:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18), 0 3px 6px rgba(0, 0, 0, 0.1);
    }
    .form-control {
        width: 100%;
        height: 50px;
        border: 1px solid #ccc;
        padding: 0 15px;
        font-size: 16px;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: border 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .form-control.password-input {
        border: 2px solid transparent;
        background: linear-gradient(to right, #eef2f3, #8faadc);
        background-origin: border-box;
        background-clip: content-box, border-box;
    }
    .form-control.password-input:hover {
        border: 2px solid #7C4102;
    }
    .form-control.password-input:focus {
        outline: none;
        box-shadow: 0 0 8px rgba(0, 86, 179, 0.6);
        background: linear-gradient(to right, #ccd9ff, #cce7ff);
    }
    .form-control.password-input::before {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: #7C4102;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.5s ease-in-out;
    }
    .form-control.password-input:focus::before {
        transform: scaleX(1);
    }
    .btn-primary {
        width:100%;
        padding:5px;
        border-radius: 2px;
        background-color: #007bff;
        color: white;
        font-size: 18px;
        border: none; 
        margin-bottom: 5px; 
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    .btn-link {
        color: #007bff;
        font-size: 16px;
        text-decoration: none;
    }
    .btn-link:hover {
        color: #0056b3;
    }
    .username-input {
        width: 30%;
    }
    .password-input {
        width: 30%;
    }
    .btn-reset-password {
        background-color: #24537F;
        font-size: 12px;
        width: 30%; 
        display: block; 
        text-align: left; 
        font-weight: bold;
    }
    .left-align-container {
        text-align: left;
    }
    .btn-custom {
        background: linear-gradient(135deg, #7C4102, #9C6123);
        color: white;
        padding: 15px 20px;
        font-size: 18px;
        border-radius: 10px;
        width: 35%;
        display: inline-block;
        text-align: center;
        border: none; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
    }
    .btn-custom:hover, .btn-custom:focus {
        background: linear-gradient(135deg, #9C6123, #7C4102);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }

    h1 {
        background: linear-gradient(to right, #7C4102, #A06533);
        color: white;
        padding: 15px 25px; 
        border-radius: 12px;
        box-shadow: 
            0 6px 20px rgba(0, 0, 0, 0.2), 
            0 10px 30px rgba(0, 0, 0, 0.1) inset; 
        text-align: left;
        font-family: 'Arial', sans-serif;
        font-weight: bold;
        font-size: calc(2.5vw + 10px); 
        width: 80%;
        margin: 15px auto; 
        transition: all 0.3s ease;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); 
        position: relative;
        overflow: hidden; 
        
    }

    h1::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        border-radius: 12px;
    }

    h1:hover::before {
        opacity: 1; /* Change opacity on hover for a subtle interactive effect */
    }


    h1::after {
        content: "";
        position: absolute;
        top: -50%; /* Starting above the visible area */
        left: -50%;
        width: 200%; /* Wide enough to cover the entire element when moving */
        height: 200%;
        background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0) 100%);
        animation: shimmer 8s linear infinite;
    }
</style>

<h1>
    <?= Html::encode($this->title) ?>
</h1>

        <!-- /.login-logo -->
<div class="user-form">
    <div style="text-align: center;">        
    </div>
        <div class="site-login" style="min-width:560px !important">
          <?php if(Yii::$app->session->hasFlash('user_confirmation')): ?>
          <div class="alert alert-success alert-dismissable">
              <h4><?php echo Yii::$app->session->getFlash('user_confirmation'); ?></h4>
          </div>      
          <?php endif; ?>

          <?php if(Yii::$app->session->hasFlash('logins_exceeded')): ?>
          <div class="alert alert-danger alert-dismissable">
              <h4><?php echo Yii::$app->session->getFlash('logins_exceeded'); ?></h4>
          </div>
          <?php endif; ?>

          <?php $form = ActiveForm::begin(); ?>

              <?= Html::label('Enter your E-Mail Address', 'kra_pin_number') ?>

              <?= $form->field($model, 'email')->textInput()->label('') ?>

              <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(), [
                  // configure additional widget properties here
              ]) ?>

              <div class="form-group">
                  <div class="col-md-6">
                 <?= Html::submitButton('Reset Password', ['class' => 'btn btn-success']) ?>
                  </div>
              </div>

          <?php ActiveForm::end(); ?>
        </div>
    </div>

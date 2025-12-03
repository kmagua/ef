<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Verify OTP';
$this->context->layout = false; // no main layout for login
/** @var string|null $username */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #006a71, #daa520);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .otp-container {
            background: #fff;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 480px;
            text-align: center;
            animation: fadeInUp 0.6s ease-in-out;
        }
        .flash-message {
            background: linear-gradient(135deg,#007bff,#0056b3);
            color: #fff;
            font-family: Poppins, sans-serif;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
            text-align: left;
        }
        .otp-container h3 {
            font-weight: 700;
            margin-bottom: 14px;
            font-size: 1.6rem;
            color: #006a71;
        }
        .otp-container p {
            color: #444;
            margin-bottom: 25px;
            font-size: 1rem;
        }
        .otp-container strong.username {
            color: #006a71;
            font-weight: 700;
        }
        .otp-boxes {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }
        .otp-input-box {
            width: 52px;
            height: 56px;
            font-size: 1.4rem;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-weight: 600;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .otp-input-box:focus {
            border-color: #006a71;
            outline: none;
            box-shadow: 0 0 6px rgba(0,106,113,0.4);
        }
        .btn-primary {
            width: 100%;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 10px;
            border-radius: 10px;
            background: linear-gradient(135deg, #006a71, #009999);
            border: none;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #005f5f, #008080);
            transform: translateY(-2px);
        }
        .countdown {
            font-size: 0.9rem;
            margin-top: 14px;
            color: #d9534f;
            font-weight: 500;
        }
        .resend-link {
            margin-top: 18px;
            font-size: 0.9rem;
        }
        .resend-link a {
            text-decoration: none;
            font-weight: 600;
            color: #006a71;
        }
        .resend-link a:hover {
            text-decoration: underline;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="otp-container">

    <!-- Flash messages -->
    <?php if (Yii::$app->session->hasFlash('info')): ?>
        <div class="flash-message">
            <?= Yii::$app->session->getFlash('info') ?>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="flash-message" style="background: linear-gradient(135deg,#dc3545,#c82333);">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="flash-message" style="background: linear-gradient(135deg,#28a745,#1e7e34);">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <h3>🔐 Verify OTP</h3>
    <p>
        Welcome to <span style="color:#daa520;font-weight:600;">Fiscal Bridge</span>, 
        We’ve sent a one-time password (OTP) to your email. Enter it below:
    </p>

    <?php $form = ActiveForm::begin(); ?>
        <!-- Hidden input to store OTP -->
        <?= Html::hiddenInput('otp', '', ['id' => 'otp']) ?>

        <!-- OTP Split Boxes -->
        <div class="otp-boxes">
            <?php for ($i = 0; $i < 6; $i++): ?>
                <input type="text" maxlength="1" class="otp-input-box" data-index="<?= $i ?>">
            <?php endfor; ?>
        </div>

        <?= Html::submitButton('Verify OTP', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>

    <div class="countdown">
        ⏳ OTP expires in <span id="timer">05:00</span>
    </div>

    <div class="resend-link">
        Didn’t receive the OTP? <?= Html::a('Resend OTP', ['/backend/default/resend-otp']) ?>
    </div>
</div>

<script>
    // Countdown Timer (5 minutes)
    let timeLeft = 300; 
    const timerDisplay = document.getElementById('timer');

    const timer = setInterval(function() {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        timerDisplay.textContent = `${minutes}:${seconds}`;
        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(timer);
            timerDisplay.textContent = "Expired";
        }
    }, 1000);

    // OTP input handling
    const inputs = document.querySelectorAll('.otp-input-box');
    const hiddenOtp = document.getElementById('otp');

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateOtpValue();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    function updateOtpValue() {
        hiddenOtp.value = Array.from(inputs).map(i => i.value).join('');
    }
</script>

</body>
</html>

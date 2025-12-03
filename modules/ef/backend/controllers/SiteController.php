<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        // Auto-login if the user is already authenticated
        if (!Yii::$app->user->isGuest) {
            return $this->redirectUserByRole(Yii::$app->user->identity->role);
        }

        $this->layout = false;
        $model = new LoginForm();

        // Ensure session is active
        if (!Yii::$app->session->isActive) {
            Yii::$app->session->open();
        }

        // Generate a security stamp (captcha)
        $securityStamp = $this->generateCaptcha();

        // Handle form submission
        if ($model->load(Yii::$app->request->post())) {
            $submittedAnswer = Yii::$app->request->post('security_stamp');
            $expectedAnswer = Yii::$app->session->get('security_stamp_answer');

            // Validate security stamp (captcha)
            if (empty($submittedAnswer) || $submittedAnswer != $expectedAnswer) {
                Yii::$app->session->setFlash('error', 'Incorrect Security Stamp. Please try again.');
                return $this->render('login', [
                    'model' => $model,
                    'question' => $securityStamp['question']
                ]);
            }

            // Attempt login
            if ($model->login()) {
                $username = Yii::$app->user->identity->username;
                Yii::$app->session->setFlash('success', "Welcome, $username! You've entered FiscalBridge Portal.");

                // Clear captcha session after successful login
                Yii::$app->session->remove('security_stamp_answer');

                // Redirect user based on their role
                return $this->redirectUserByRole(Yii::$app->user->identity->role);
            } else {
                Yii::$app->session->setFlash('error', 'Invalid username or password. Please try again.');
            }
        }

        // Render login page with new captcha
        return $this->render('login', [
            'model' => $model,
            'question' => $securityStamp['question']
        ]);
    }

    /**
     * Redirect user based on their role
     */
    private function redirectUserByRole($role)
{
    Yii::debug("Redirecting user based on role: " . $role); // Debug log

    if (Yii::$app->user->identity->inEqualizationFund()) {
        return $this->redirect(['/ef/ef-project/index']); // Ensure this is the correct route
    }

    switch ($role) {
        case 'admin':
            return $this->redirect(['/admin/dashboard']);
        case 'equalization':
            return $this->redirect(['/ef/ef-project/index']); // Ensure this matches the Equalization Fund route
        case 'igfr':
            return $this->redirect(['/igfrd']);
        case 'finance':
            return $this->redirect(['/finance/dashboard']);
        default:
            return $this->goHome(); // Fallback redirect
    }
}

    /**
     * Generate a new security stamp (captcha)
     */
    private function generateCaptcha()
    {
        $number1 = rand(1, 10);
        $number2 = rand(1, 20);
        $correctAnswer = $number1 + $number2;

        // Store in session
        Yii::$app->session->set('security_stamp_answer', $correctAnswer);

        return ['question' => "{$number1} + {$number2}"];
    }

    /**
     * AJAX endpoint to generate a new captcha
     */
    public function actionGenerateCaptcha()
    {
        return $this->asJson($this->generateCaptcha());
    }

    /**
     * Displays contact page.
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}

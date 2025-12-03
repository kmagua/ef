<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "password_reset".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status 0 = pending, 1 = used
 * @property string $hash
 * @property string $date_created
 * @property string $last_updated
 *
 * @property User $user
 */
class PasswordReset extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_USED    = 1;

    // For messaging only (you can also enforce this in validateToken if you want)
    const TOKEN_LIFETIME_HOURS = 2;

    /** ----------------------- TABLE NAME ----------------------- */
    public static function tableName()
    {
        return 'password_reset';
    }

    /** ----------------------- RULES ----------------------- */
    public function rules()
    {
        return [
            [['user_id', 'hash'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['date_created', 'last_updated'], 'safe'],
            [['status'], 'in', 'range' => [0, 1], 'message' => 'Status must be 0 (pending) or 1 (used)'],
            [['hash'], 'string', 'max' => 120],
            [['user_id'], 'unique'],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /** ----------------------- LABELS ----------------------- */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'user_id'      => 'User ID',
            'status'       => 'Status',
            'hash'         => 'Hash',
            'date_created' => 'Date Created',
            'last_updated' => 'Last Updated',
        ];
    }

    /** ----------------------- RELATIONS ----------------------- */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /** ----------------------- CREATE TOKEN & SEND EMAIL ----------------------- */
    public static function passwordReset($email)
    {
        /** @var User|null $user */
        $user = User::find()->where(['email' => $email])->one();

        if (!$user) {
            // Do not reveal whether the email exists
            return "If the email exists in our system, a password reset link has been sent.";
        }

        // Use your Utility helper or Yii's security
        // $hash = Yii::$app->security->generateRandomString(64);
        $hash = Utility::generateRandomString();

        $sql = "
            INSERT INTO password_reset (user_id, hash, status, date_created, last_updated)
            VALUES (:user_id, :hash, :status, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                hash = :hash,
                status = :status,
                last_updated = NOW()
        ";

        Yii::$app->db->createCommand($sql, [
            ':user_id' => $user->id,
            ':hash'    => $hash,
            ':status'  => self::STATUS_PENDING,
        ])->execute();

        try {
            if (self::sendEmail($user, $hash)) {
                return "A password reset link has been sent to {$user->email}. Please check your inbox.";
            }
            return "We could not send the reset email. Please try again later.";
        } catch (\Throwable $e) {
            Yii::error("Password reset email failed: " . $e->getMessage(), __METHOD__);
            return "An error occurred while sending the reset email. Please try again.";
        }
    }

    /**
     * Sends the password reset email to the user (styled HTML + plain text).
     *
     * Branded for Fiscal Bridge Information System.
     */
    public static function sendEmail($user, $hash)
    {
        $subject = 'Password Reset - Fiscal Bridge Information System';

        $resetLink = Url::to(
            ['/backend/user/set-new-password', 'id' => $user->id, 'ph' => $hash],
            true
        );

        $displayName  = $user->user_names ?: $user->email;
        $supportEmail = 'help@ict.go.ke';
        $expiresText  = self::TOKEN_LIFETIME_HOURS . ' hour' . (self::TOKEN_LIFETIME_HOURS > 1 ? 's' : '');
        $year         = date('Y');
        $requestTime  = date('d M Y, H:i:s');

        // --- Styled HTML version (Fiscal Bridge theme) ---
        $htmlBody = <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset - Fiscal Bridge Information System</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f5f7;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f5f7;padding:24px 0;">
    <tr>
        <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #dde3eb;">
                <tr>
                    <td style="background:linear-gradient(90deg,#004b6e,#0a8f6a);padding:18px 24px;color:#ffffff;font-size:18px;font-weight:bold;">
                        Fiscal Bridge Information System – Password Reset
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 24px 8px 24px;font-size:14px;color:#333333;line-height:1.6;">
                        <p style="margin:0 0 12px 0;">Dear <strong>{$displayName}</strong>,</p>
                        <p style="margin:0 0 12px 0;">
                            We received a request to reset the password for your Fiscal Bridge Information System account.
                        </p>
                        <p style="margin:0 0 12px 0;">
                            Request time: <strong>{$requestTime}</strong>
                        </p>
                        <p style="margin:0 0 12px 0;">
                            Click the button below to choose a new password. For your security,
                            this link will be valid for <strong>{$expiresText}</strong> from the time of the request.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:10px 24px 8px 24px;">
                        <a href="{$resetLink}"
                           style="display:inline-block;padding:12px 28px;border-radius:4px;
                                  background:linear-gradient(90deg,#0a8f6a,#f9b234);color:#ffffff;
                                  text-decoration:none;font-weight:bold;font-size:14px;">
                            Reset My Password
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 24px 20px 24px;font-size:12px;color:#555555;line-height:1.7;">
                        <p style="margin:16px 0 8px 0;">
                            If the button does not work, copy and paste this link into your browser:
                        </p>
                        <p style="word-break:break-all;margin:0 0 12px 0;">
                            <a href="{$resetLink}" style="color:#004b6e;text-decoration:none;">{$resetLink}</a>
                        </p>
                        <p style="margin:0 0 12px 0;">
                            If you did not request this password reset, you can safely ignore this email.
                            Your Fiscal Bridge Information System account will remain unchanged.
                        </p>
                        <p style="margin:0 0 4px 0;">
                            For assistance, contact our support team at
                            <a href="mailto:{$supportEmail}" style="color:#0a8f6a;text-decoration:none;">{$supportEmail}</a>.
                        </p>
                        <p style="margin:0;font-size:11px;color:#999999;">
                            This is an automated message from the Fiscal Bridge Information System. Please do not reply directly to this email.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background-color:#f7f8fa;border-top:1px solid #dde3eb;padding:10px 24px;font-size:11px;color:#999999;text-align:center;">
                        © {$year} Fiscal Bridge Information System. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
HTML;

        // --- Plain text version ---
        $textBody = <<<TEXT
Dear {$displayName},

We received a request to reset the password for your Fiscal Bridge Information System account.

Request time: {$requestTime}

Use the link below to choose a new password. This link will be valid for {$expiresText} from the time of the request:

{$resetLink}

If you did not request this change, you can ignore this email and your password will stay the same.

For help, contact us at {$supportEmail}.

Thank you,
Fiscal Bridge Information System Team
TEXT;

        // Sender / reply-to
        $fromEmail  = $supportEmail;
        $fromName   = 'Fiscal Bridge Information System';
        $replyTo    = $supportEmail;

        try {
            $mailer = Yii::$app->mailer->compose()
                ->setTo($user->email)
                ->setFrom([$fromEmail => $fromName])
                ->setSubject($subject)
                ->setTextBody($textBody)
                ->setHtmlBody($htmlBody)
                ->setReplyTo($replyTo);

            // Optional: audit copies
            // $mailer->setBcc('audit@ict.go.ke');

            $sent = $mailer->send();

            if ($sent) {
                Yii::info("Password reset email sent successfully to {$user->email}", __METHOD__);
                return true;
            }

            Yii::error("Password reset email failed to send to {$user->email}", __METHOD__);
            return false;
        } catch (\Throwable $e) {
            Yii::error("Password reset email exception: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /** ----------------------- TOKEN VALIDATION ----------------------- */
    public static function validateToken($userId, $hash)
    {
        return self::find()
            ->where([
                'user_id' => $userId,
                'hash'    => $hash,
                'status'  => self::STATUS_PENDING,
            ])
            ->one();
    }

    /** ----------------------- MARK TOKEN USED ----------------------- */
    public function markUsed()
    {
        $this->status       = self::STATUS_USED;
        $this->last_updated = date('Y-m-d H:i:s');
        return $this->save(false);
    }
}

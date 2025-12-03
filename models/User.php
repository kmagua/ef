<?php

namespace app\models;

use Yii;
use yii\base\Security;
use yii\web\UploadedFile;
use kartik\password\StrengthValidator;
use app\models\UserRole;
use app\models\AuthRole;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string|null $user_names
 * @property string|null $password
 * @property string|null $status
 * @property string|null $recent_passwords
 * @property string $last_password_change_date
 * @property string|null $last_login_date
 * @property string|null $profile_photo
 * @property string|null $profile_photo_type
 * @property string $date_created
 * @property string|null $last_updated
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    // Virtual & helper attributes
    public $username;
    public $password_repeat;
    public $current_password;
    public $captcha;
    public $sel_roles = [];
    public $roles;
    public $profileImageFile;
    protected $profilePhotoToDelete;

    /** ----------------------- TABLE NAME ----------------------- */
    public static function tableName()
    {
        return 'user';
    }

    /** ----------------------- SCENARIOS ----------------------- */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        // what each scenario can load/validate
        $scenarios['register'] = ['email', 'user_names', 'password', 'password_repeat', 'captcha'];
        $scenarios['register_internal'] = ['email', 'user_names', 'password', 'password_repeat'];
        $scenarios['password_reset'] = ['email', 'captcha'];
        $scenarios['password_update'] = ['password', 'password_repeat', 'current_password'];
        // default already exists and contains all attributes

        return $scenarios;
    }

    /** ----------------------- RULES ----------------------- */
    public function rules()
    {
        return [
            /* ---------- REQUIRED FIELDS (NORMAL USE, NOT RESET) ---------- */
            [
                ['email', 'user_names'],
                'required',
                'on' => ['default', 'register', 'register_internal', 'password_update'],
            ],

            /* ---------- REQUIRED FIELDS (PASSWORD RESET FORM) ---------- */
            [
                ['email'],
                'required',
                'on' => ['password_reset'],
            ],

            /* ---------- EMAIL FORMAT & LENGTH ---------- */
            [['email'], 'email'],
            [['email'], 'string', 'max' => 60],

            /* ---------- UNIQUE EMAIL (NEVER ON password_reset) ---------- */
            [
                ['email'],
                'unique',
                'on'   => ['default', 'register', 'register_internal', 'password_update'],
                'when' => function ($model) {
                    return $model->scenario !== 'password_reset';
                },
            ],

            /* ---------- OTHER FIELDS / FILTERS ---------- */
            [['last_password_change_date', 'last_login_date', 'date_created', 'last_updated', 'sel_roles'], 'safe'],
            [['user_names', 'password'], 'string', 'max' => 100],
            [['recent_passwords'], 'string', 'max' => 500],
            [['status'], 'filter', 'filter' => 'strval'],
            [['profile_photo'], 'string', 'max' => 255],
            [['profile_photo_type'], 'string', 'max' => 100],

            /* ---------- CAPTCHA & PASSWORD (REGISTER + RESET) ---------- */
            // Registration: captcha + password required
            [['captcha', 'password'], 'required', 'on' => 'register'],

            // Password reset request: only captcha required here
            [['captcha'], 'required', 'on' => ['password_reset']],

            // Captcha validator for both forms
            [
                'captcha',
                'captcha',
                'captchaAction' => '/backend/site/captcha',
                'on' => ['register', 'password_reset'],
            ],

            /* ---------- PASSWORD STRENGTH (WHEN USER ENTERS A PASSWORD) ---------- */
            [
                ['password'],
                StrengthValidator::class,
                'min' => 8,
                'upper' => 1,
                'lower' => 1,
                'digit' => 1,
                'special' => 1,
                'userAttribute' => 'email',
                'repeat' => 10,
                'on' => ['register', 'password_update', 'register_internal'],
            ],

            /* ---------- PASSWORD REPEAT / UPDATE ---------- */
            [['password_repeat'], 'validatePasswordRepeat', 'on' => ['register', 'password_update', 'register_internal']],
            [['password', 'password_repeat'], 'required', 'on' => 'password_update'],

           /* ---------- FILE UPLOAD VALIDATION ---------- */
[
    ['profileImageFile'],
    'file',
    'skipOnEmpty' => true,
    'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'checkExtensionByMimeType' => false,   // <-- prevents finfo_file error
    'maxSize' => 2 * 1024 * 1024,          // 2 MB
    'when' => function ($model) {
        // skip validation during updates unless a new file is uploaded
        return !empty($model->profileImageFile);
    },
],

            /* ---------- OTHER DB FIELDS ---------- */
            [['division_id'], 'integer'],
            [['is_head'], 'boolean'],
        ];
    }

    /** ----------------------- LABELS ----------------------- */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'user_names' => 'Full Name',
            'password' => 'New Password',
            'password_repeat' => 'Repeat Password',
            'current_password' => 'Current Password',
            'status' => 'Status',
            'recent_passwords' => 'Recent Passwords',
            'last_password_change_date' => 'Last Password Change Date',
            'last_login_date' => 'Last Login Date',
            'date_created' => 'Date Created',
            'last_updated' => 'Last Updated',
            'profile_photo' => 'Profile Photo',
            'profile_photo_type' => 'Profile Photo Type',
            'profileImageFile' => 'Upload Profile Photo',
            'sel_roles' => 'Roles',
        ];
    }

    /** ----------------------- RELATIONS ----------------------- */
    public function getDivision()
    {
        return $this->hasOne(\app\modules\backend\models\Divisions::class, ['id' => 'division_id']);
    }

    public function getAssignedRoles()
    {
        return $this->hasMany(UserRole::class, ['user_id' => 'id']);
    }

    public function getRoles()
    {
        return $this->hasMany(AuthRole::class, ['id' => 'role_id'])->via('assignedRoles');
    }

    public function getSelRoles($id = null)
    {
        $uid = $id ?: $this->id;
        return UserRole::find()
            ->select('role_id')
            ->where(['user_id' => $uid])
            ->column();
    }

    public function getUserRole()
    {
        return $this->hasOne(UserRole::class, ['user_id' => 'id'])->andWhere(['status' => 1]);
    }

    public function getRole()
    {
        return $this->userRole && $this->userRole->role
            ? strtolower($this->userRole->role->role_name)
            : null;
    }

    public function getRoleNames(): array
    {
        return $this->roles ? array_column($this->roles, 'role_name') : [];
    }

    public function hasRole($role): bool
    {
        $role = strtolower(trim($role));
        $all = array_map('strtolower', $this->roleNames);
        return in_array($role, $all);
    }

    public function inGroup($roles): bool
    {
        $roles = array_map(fn($r) => strtolower(trim($r)), (array)$roles);
        $userRoles = array_map('strtolower', $this->roleNames);
        return !empty(array_intersect($roles, $userRoles));
    }
/**
 * Returns the full URL of the user's profile photo.
 *
 * Features:
 * - Supports external URLs
 * - Supports local uploads
 * - Supports a thumbnail version (if $size is provided)
 * - Falls back when the file doesn't exist
 * - Safe alias resolution
 * - Custom default avatar support
 *
 * @param string|null $size Optional thumbnail name (e.g. 'small', 'medium')
 * @return string
 */
public function getProfileImageUrl($size = null)
{
    $photo = $this->profile_photo;
    $baseUrl = Yii::getAlias('@web');
    $uploadPath = Yii::getAlias('@webroot/uploads/profile/');
    $uploadUrl = $baseUrl . '/uploads/profile/';

    // Default fallback image
    $default = $baseUrl . '/images/default-avatar.png';

    // No photo saved
    if (empty($photo)) {
        return $default;
    }

    // If already a valid external URL
    if (filter_var($photo, FILTER_VALIDATE_URL)) {
        return $photo;
    }

    // If marked as external but not a valid URL (edge case)
    if ($this->profile_photo_type === 'external') {
        return $photo;
    }

    // --- Local file handling ---

    // Build local path
    $filePath = $uploadPath . $photo;
    $fileUrl = $uploadUrl . $photo;

    // Thumbnail support: filename_small.jpg
    if (!empty($size)) {
        $dotPos = strrpos($photo, '.');

        if ($dotPos !== false) {
            $name = substr($photo, 0, $dotPos);
            $ext = substr($photo, $dotPos);
            $thumbFile = $name . '_' . $size . $ext;

            // Full thumbnail path
            $thumbPath = $uploadPath . $thumbFile;

            if (file_exists($thumbPath)) {
                return $uploadUrl . $thumbFile;
            }
        }
    }

    // If the main file exists
    if (file_exists($filePath)) {
        return $fileUrl;
    }

    // If file missing → return default avatar
    return $default;
}
public function storeProfileImage()
{
    if (!$this->profileImageFile) {
        return true;
    }

    $uploadPath = Yii::getAlias('@webroot/uploads/profile/');

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0775, true);
    }

    // Generate unique filename
    $filename = 'profile_' . $this->id . '_' . time() . '.' . $this->profileImageFile->extension;
    $fullPath = $uploadPath . $filename;

    // Save file immediately before tmp file is deleted
    if (!$this->profileImageFile->saveAs($fullPath)) {
        return false;
    }

    // Remove old photo
    if (!empty($this->profile_photo)) {
        $old = $uploadPath . $this->profile_photo;
        if (file_exists($old)) {
            @unlink($old);
        }
    }

    // Save filename in database
    $this->profile_photo = $filename;
    $this->profile_photo_type = 'local';

    return true;
}

    public function saveUserRoles()
    {
        UserRole::deleteAll(['user_id' => $this->id]);

        if (!empty($this->sel_roles)) {
            foreach ($this->sel_roles as $roleId) {
                $role = new UserRole();
                $role->user_id = $this->id;
                $role->role_id = $roleId;
                $role->assigned_by = Yii::$app->user->id ?? 1;
                $role->status = '1';
                $role->save(false);
            }
        }
        return true;
    }

    /** ----------------------- FILE UPLOADS ----------------------- */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->profileImageFile = UploadedFile::getInstance($this, 'profileImageFile');
            return true;
        }
        return false;
    }

    protected function handleProfileUpload(): bool
    {
        if (!$this->profileImageFile instanceof UploadedFile) {
            return false;
        }

        $uploadDir = Yii::getAlias('@webroot/uploads/users/');
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0775, true);
        }

        $safeBase = 'user_' . ($this->id ?: 'tmp') . '_' . time();
        $ext = strtolower($this->profileImageFile->extension ?: 'jpg');
        $fileName = $safeBase . '.' . $ext;
        $fullPath = $uploadDir . $fileName;

        if ($this->profileImageFile->saveAs($fullPath)) {
            $this->profile_photo = 'uploads/users/' . $fileName;
            $this->profile_photo_type = $this->profileImageFile->type ?: null;
            return true;
        }

        $this->addError('profileImageFile', 'Unable to save uploaded file.');
        return false;
    }

    /** ----------------------- PASSWORD VALIDATION ----------------------- */
    public function validatePasswordRepeat($attribute, $params)
    {
        if ($this->password !== $this->password_repeat) {
            $this->addError($attribute, "Passwords do not match!");
        }
    }

    // Keep for future use (temporarily bypassed)
    public function validateCurrentPassword($attribute, $params)
    {
        if (!$this->validatePassword($this->$attribute)) {
            $this->addError($attribute, 'Incorrect current password.');
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (isset($this->status)) {
            $this->status = (string)$this->status;
        }

        if ($this->scenario === 'password_update' || $insert) {
            if (!empty($this->password)) {
                $plainPassword = $this->password;
                $this->password = password_hash($plainPassword, PASSWORD_DEFAULT);

                if (!$this->customPasswordChecks($insert, $plainPassword)) {
                    $this->addError('password', 'Password must not match your last 3 passwords.');
                    return false;
                }

                $this->last_password_change_date = date('Y-m-d H:i:s');
            }
        }

        return true;
    }

    /** ----------------------- PASSWORD HISTORY CHECK ----------------------- */
    protected function customPasswordChecks($isNewRecord, $newPassword)
    {
        if ($isNewRecord || empty($this->recent_passwords)) {
            return true;
        }

        $recent = json_decode($this->recent_passwords, true);
        if (!is_array($recent)) {
            $recent = [];
        }

        foreach ($recent as $oldHash) {
            if (password_verify($newPassword, $oldHash)) {
                return false;
            }
        }

        // Keep last 3 passwords
        $recent[] = password_hash($newPassword, PASSWORD_DEFAULT);
        $recent = array_slice($recent, -3);
        $this->recent_passwords = json_encode($recent);

        return true;
    }

    /** ----------------------- IDENTITY INTERFACE ----------------------- */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return "test" . $this->getId() . "key";
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return password_verify($password, $this->password);
    }

   /**
 * Sends the account creation confirmation email.
 */
public function sendEmailConfirmationEmail(): void
{
    $subject = 'Account Confirmation – Fiscal Bridge Information Management System';

    $displayName  = $this->user_names ?: $this->email;
    $supportEmail = 'help@ict.go.ke';
    $year         = date('Y');

    // Styled HTML Email
    $html = <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Confirmation</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f6f8;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px 0;background-color:#f5f6f8;">
    <tr>
        <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" 
                   style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #d9dfe7;">
                
                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(90deg,#004b6e,#0a8f6a);
                               padding:18px 24px;color:#ffffff;font-size:18px;font-weight:bold;">
                        Fiscal Bridge Information Management System
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:24px;font-size:14px;color:#333;">
                        <p>Dear <strong>{$displayName}</strong>,</p>

                        <p>Your account for the 
                        <strong>Fiscal Bridge Information Management System (Equalization Fund & IGFR Department)</strong>
                        has been created successfully.</p>

                        <p>You can now sign in using the email address:</p>
                        <p><strong>{$this->email}</strong></p>

                        <p>If this was not you, please contact our support team immediately.</p>

                        <p style="margin-top:24px;">Best regards,<br>
                        <strong>IGFR Support Team</strong></p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f7f8fa;border-top:1px solid #d9dfe7;
                               padding:12px 24px;font-size:11px;color:#999;text-align:center;">
                        Need help? Contact us at 
                        <a style="color:#0a8f6a;text-decoration:none;" href="mailto:{$supportEmail}">
                            {$supportEmail}
                        </a><br><br>
                        © {$year} Fiscal Bridge Information Management System
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
HTML;

    Utility::sendMail($this->email, $subject, $html);
}

}

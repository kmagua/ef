<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_role".
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property int $assigned_by
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AuthRole $role
 * @property User $user
 * @property User $assignedBy
 */
class UserRole extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'role_id'], 'required'],
            [['user_id', 'role_id', 'assigned_by', 'status'], 'integer'],

            // Prevent duplicate role assignments for the same user
            [['user_id', 'role_id'], 'unique', 'targetAttribute' => ['user_id', 'role_id']],

            // FK constraints
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
            [
                ['role_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => AuthRole::class,
                'targetAttribute' => ['role_id' => 'id']
            ],
            [
                ['assigned_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['assigned_by' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'user_id'     => 'User',
            'role_id'     => 'Role',
            'assigned_by' => 'Assigned By',
            'status'      => 'Status',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Role]].
     */
    public function getRole()
    {
        return $this->hasOne(AuthRole::class, ['id' => 'role_id']);
    }

    /**
     * Gets query for [[User]].
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[AssignedBy]] (the admin who assigned the role).
     */
    public function getAssignedBy()
    {
        return $this->hasOne(User::class, ['id' => 'assigned_by']);
    }

    /**
     * Automatically set timestamps and assigned_by before saving.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $now = date('Y-m-d H:i:s');
            if ($insert) {
                $this->created_at = $now;
                $this->status = $this->status ?? 1;
                $this->assigned_by = $this->assigned_by ?? (Yii::$app->user->id ?? 1); // default to system/admin
            }
            $this->updated_at = $now;
            return true;
        }
        return false;
    }
}

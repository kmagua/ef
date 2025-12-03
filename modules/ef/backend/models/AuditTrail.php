<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "audit_trail".
 *
 * @property int $id
 * @property string|null $old_value
 * @property string|null $new_value
 * @property string|null $model
 * @property string|null $field
 * @property int|null $model_id
 * @property string|null $comments
 * @property string|null $action
 * @property string|null $stamp
 * @property int|null $user_id
 * @property int|null $change_no
 */
class AuditTrail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_audit_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_value', 'new_value', 'comments'], 'string'],
            [['model_id', 'user_id', 'change_no'], 'integer'],
            [['model', 'field'], 'string', 'max' => 150],
            [['action', 'stamp'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'model' => 'Model',
            'field' => 'Column',
            'model_id' => 'Record ID',
            'comments' => 'Comments',
            'action' => 'Action',
            'stamp' => 'Time',
            'user_id' => 'User ID',
            'change_no' => 'Change No',
        ];
    }
    
    /**
	 * Adds a new change to this entry.
	 * 
	 * @param string $attr the name of the attribute
	 * @param mixed $from the old value
	 * @param mixed $to the new value
	 */
    public function addChange($attr, $from, $to)
    {
        $this->field = $attr;
        $this->old_value = $from;
        $this->new_value = $to;
    }
}

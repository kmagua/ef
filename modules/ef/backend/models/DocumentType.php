<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "document_type".
 *
 * @property int $id
 * @property string $document_type
 * @property int $is_active
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $user_id
 * @property string $applicable_to
 *
 * @property DocumentLibrary[] $documentLibraries
 */
class DocumentType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_type'], 'required'],
            [['is_active', 'user_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['applicable_to'], 'string'],
            [['document_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_type' => 'Document Type',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'user_id' => 'User ID',
            'applicable_to' => 'Applicable To',
        ];
    }

    /**
     * Gets query for [[DocumentLibraries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentLibraries()
    {
        return $this->hasMany(DocumentLibrary::class, ['document_type' => 'id']);
    }
}

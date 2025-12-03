<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "financial_year".
 *
 * @property int $id
 * @property string $start_date
 * @property string $end_date
 * @property string $financial_year
 * @property int $is_active
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property DocumentLibrary[] $documentLibraries
 */
class FinancialYear extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'financial_year';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date', 'financial_year'], 'required'],
            [['start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['is_active'], 'integer'],
            [['financial_year'], 'string', 'max' => 255],
            [['financial_year'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'financial_year' => 'Financial Year',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[DocumentLibraries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentLibraries()
    {
        return $this->hasMany(DocumentLibrary::class, ['financial_year' => 'id']);
    }
}

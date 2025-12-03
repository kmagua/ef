<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "allocation_type".
 *
 * @property int $id
 * @property string|null $description
 *
 * @property AdditionalRevShare[] $additionalRevShares
 * @property RevenueShare[] $revenueShares
 */
class AllocationType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'allocation_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Allocation Type',
        ];
    }

    /**
     * Gets query for [[AdditionalRevShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalRevShares()
    {
        return $this->hasMany(AdditionalRevShare::class, ['allocation_code' => 'id']);
    }

    /**
     * Gets query for [[RevenueShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRevenueShares()
    {
        return $this->hasMany(RevenueShare::class, ['allocation_code' => 'id']);
    }
}

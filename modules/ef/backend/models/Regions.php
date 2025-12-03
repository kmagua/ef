<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "regions".
 *
 * @property int $RegionId
 * @property string|null $region_name
 *
 * @property County[] $counties
 */
class Regions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'RegionId' => 'Region ID',
            'region_name' => 'Region Name',
        ];
    }

    /**
     * Gets query for [[Counties]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCounties()
    {
        return $this->hasMany(County::class, ['RegionId' => 'RegionId']);
    }
}

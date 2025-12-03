<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "county".
 *
 * @property int $CountyId
 * @property int|null $RegionId
 * @property string|null $CountyName
 * @property string|null $CountyCode
 * @property float|null $size
 * @property int|null $population
 * @property float|null $poverty_index
 * @property float|null $gcp
 *
 * @property AdditionalRevShare[] $additionalRevShares
 * @property EquitableRevenueShare[] $equitableRevenueShares
 * @property Fiscal[] $fiscals
 * @property Letter[] $letters
 * @property Regions $region
 */
class County extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'county';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['RegionId', 'population'], 'integer'],
            [['size', 'poverty_index', 'gcp'], 'number'],
            [['CountyName', 'CountyCode'], 'string', 'max' => 50],
            [['RegionId'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::class, 'targetAttribute' => ['RegionId' => 'RegionId']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'CountyId' => 'County ID',
            'RegionId' => 'Region ID',
            'CountyName' => 'County Name',
            'CountyCode' => 'County Code',
            'size' => 'Size',
            'population' => 'Population',
            'poverty_index' => 'Poverty Index',
            'gcp' => 'Gcp',
        ];
    }

    /**
     * Gets query for [[AdditionalRevShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalRevShares()
    {
        return $this->hasMany(AdditionalRevShare::class, ['county_id' => 'CountyId']);
    }

    /**
     * Gets query for [[EquitableRevenueShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquitableRevenueShares()
    {
        return $this->hasMany(EquitableRevenueShare::class, ['county_id' => 'CountyId']);
    }

    /**
     * Gets query for [[Fiscals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiscals()
    {
        return $this->hasMany(Fiscal::class, ['countyid' => 'CountyId']);
    }

    /**
     * Gets query for [[Letters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetters()
    {
        return $this->hasMany(Letter::class, ['from_county' => 'CountyId']);
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::class, ['RegionId' => 'RegionId']);
    }
}

<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "equitable_revenue_share".
 *
 * @property int $id
 * @property int|null $county_id
 * @property float|null $project_amt
 * @property float|null $actual_amt
 * @property float|null $balance_bf
 * @property float|null $osr_projected
 * @property float|null $osr_actual
 * @property string|null $fy
 *
 * @property County $county
 * @property FinancialYear $fy0
 */
class EquitableRevenueShare extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equitable_revenue_share';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county_id',], 'integer'],
            [['project_amt'], 'required'],
            [['project_amt', 'actual_amt', 'balance_bf', 'osr_projected', 'osr_actual'], 'number'],
            [['fy'], 'string', 'max' => 15],
            [['fy'], 'exist', 'skipOnError' => true, 'targetClass' => FinancialYear::class, 'targetAttribute' => ['fy' => 'financial_year']],
            [['county_id'], 'exist', 'skipOnError' => true, 'targetClass' => County::class, 'targetAttribute' => ['county_id' => 'CountyId']],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'county_id' => 'County',
            'project_amt' => 'Projected Amount (KES)',
            'actual_amt' => 'Actual Amount (KES)',
            'balance_bf' => 'Balance B/F',
            'osr_projected' => 'Own Source Revenue Projected',
            'osr_actual' => 'Own Source Revenue Actual',
            'fy' => 'Financial year',
        ];
    }

    /**
     * Gets query for [[County]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCounty()
    {
        return $this->hasOne(County::class, ['CountyId' => 'county_id']);
    }

    /**
     * Gets query for [[Fy0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFy0()
    {
        return $this->hasOne(FinancialYear::class, ['financial_year' => 'fy']);
    }
}

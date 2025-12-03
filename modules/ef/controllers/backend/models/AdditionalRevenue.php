<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "additional_rev_share".
 *
 * @property int $id
 * @property string|null $fy
 * @property int|null $county_id
 * @property int|null $project_id
 * @property float|null $project_amt
 * @property float|null $actual_amt
 * @property int|null $allocation_code
 *
 * @property AllocationType $allocationCode
 * @property County $county
 * @property FinancialYear $fy0
 * @property Projects $project
 */
class AdditionalRevenue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'additional_rev_share';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county_id', 'fy', 'project_amt'], 'required'],
            [['county_id', 'project_id', 'allocation_code'], 'integer'],
            [['project_amt', 'actual_amt'], 'number'],
            [['fy'], 'string', 'max' => 15],
            [['county_id'], 'exist', 'skipOnError' => true, 'targetClass' => County::class, 'targetAttribute' => ['county_id' => 'CountyId']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::class, 'targetAttribute' => ['project_id' => 'id']],
            [['allocation_code'], 'exist', 'skipOnError' => true, 'targetClass' => AllocationType::class, 'targetAttribute' => ['allocation_code' => 'id']],
            [['fy'], 'exist', 'skipOnError' => true, 'targetClass' => FinancialYear::class, 'targetAttribute' => ['fy' => 'financial_year']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fy' => 'Financial Year',
            'county_id' => 'County',
            'project_id' => 'Project',
            'project_amt' => 'Projected Amount (KES)',
            'actual_amt' => 'Actual Amount (KES)',
            'allocation_code' => 'Allocation Type',
        ];
    }

    /**
     * Gets query for [[AllocationCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAllocationCode()
    {
        return $this->hasOne(AllocationType::class, ['id' => 'allocation_code']);
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

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }
}

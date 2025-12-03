<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "fiscal".
 *
 * @property int $id
 * @property int $countyid
 * @property string $fy
 * @property float|null $development_budgement
 * @property float|null $recurrent_budget
 * @property float|null $total_revenue
 * @property float|null $actual_revenue
 * @property float|null $recurrent_expenditure
 * @property float|null $development_expenditure
 * @property float|null $target_osr
 * @property float|null $actual_osr
 * @property float|null $personal_emoluments
 * @property float|null $pending_bills
 * @property string $date_created
 * @property int $added_by
 *
 * @property User $addedBy
 * @property County $county
 * @property FinancialYear $fy0
 */
class Fiscal extends \yii\db\ActiveRecord
{
    public $total_budget;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fiscal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['countyid', 'fy', 'added_by'], 'required'],
            [['countyid', 'added_by'], 'integer'],
            [['development_budgement', 'recurrent_budget', 'total_revenue', 'actual_revenue', 'recurrent_expenditure', 'development_expenditure', 'target_osr', 'actual_osr', 'personal_emoluments', 'pending_bills'], 'number'],
            [['date_created'], 'safe'],
            [['fy'], 'string', 'max' => 15],
            [['countyid'], 'exist', 'skipOnError' => true, 'targetClass' => County::class, 'targetAttribute' => ['countyid' => 'CountyId']],
            [['fy'], 'exist', 'skipOnError' => true, 'targetClass' => FinancialYear::class, 'targetAttribute' => ['fy' => 'financial_year']],
            [['added_by'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['added_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'countyid' => 'County',
            'fy' => 'Financial year',
            'development_budgement' => 'Development Budget',
            'recurrent_budget' => 'Recurrent Budget',
            'total_revenue' => 'Total Revenue',
            'actual_revenue' => 'Actual Revenue',
            'recurrent_expenditure' => 'Recurrent Expenditure',
            'development_expenditure' => 'Development Expenditure',
            'target_osr' => 'Target OSR',
            'actual_osr' => 'Actual OSR',
            'personal_emoluments' => 'Personal Emoluments',
            'pending_bills' => 'Pending Bills',
            'date_created' => 'Date Created',
            'added_by' => 'Added By',
        ];
    }

    /**
     * Gets query for [[County]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCounty()
    {
        return $this->hasOne(County::class, ['CountyId' => 'countyid']);
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
    * Gets query for [[AddedBy]]. 
    * 
    * @return \yii\db\ActiveQuery 
    */ 
    public function getAddedBy() 
    { 
        return $this->hasOne(\app\models\User::class, ['id' => 'added_by']); 
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->total_budget = $this->development_budgement + $this->recurrent_budget;
        return true;
    }
}

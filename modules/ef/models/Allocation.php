<?php

namespace app\modules\ef\models;

use Yii;

/**
 * This is the model class for table "equalization_fund_allocation".
 *
 * @property int $id
 * @property string $financial_year
 * @property string $base_year
 * @property float $audited_revenues
 * @property float|null $ef_allocation
 * @property float $ef_entitlement
 * @property float $amount_reflected_dora
 * @property string $date_added
 */
class Allocation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equalization_fund_allocation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ef_allocation'], 'default', 'value' => 0],
            [['financial_year', 'base_year', 'audited_revenues', 'ef_entitlement', 'amount_reflected_dora'], 'required'],
            [['audited_revenues', 'ef_allocation', 'ef_entitlement', 'amount_reflected_dora'], 'number'],
            [['date_added'], 'safe'],
            [['financial_year', 'base_year'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'financial_year' => 'Financial Year',
            'base_year' => 'Base Year',
            'audited_revenues' => 'Audited Revenues',
            'ef_allocation' => 'Ef Allocation',
            'ef_entitlement' => 'Ef Entitlement',
            'amount_reflected_dora' => 'Amount Reflected Dora',
            'date_added' => 'Date Added',
        ];
    }

}

<?php

namespace app\modules\ef\models;

use Yii;

/**
 * This is the model class for table "equalisation_fund_entitlements".
 *
 * @property int $id
 * @property string|null $financial_year
 * @property string|null $base_year_most_recent_audited_revenue
 * @property float|null $audited_approved_revenue_ksh
 * @property float|null $ef_entitlement_ksh
 * @property float|null $amount_reflected_in_dora_ksh
 * @property float|null $transfers_into_ef
 * @property float|null $arrears
 */
class EqualisationFundEntitlements extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equalisation_fund_entitlements';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['financial_year', 'base_year_most_recent_audited_revenue', 'audited_approved_revenue_ksh', 'ef_entitlement_ksh', 'amount_reflected_in_dora_ksh', 'transfers_into_ef', 'arrears'], 'default', 'value' => null],
            [['audited_approved_revenue_ksh', 'ef_entitlement_ksh', 'amount_reflected_in_dora_ksh', 'transfers_into_ef', 'arrears'], 'number'],
            [['financial_year'], 'string', 'max' => 20],
            [['base_year_most_recent_audited_revenue'], 'string', 'max' => 255],
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
            'base_year_most_recent_audited_revenue' => 'Base Year Most Recent Audited Revenue',
            'audited_approved_revenue_ksh' => 'Audited Approved Revenue Ksh',
            'ef_entitlement_ksh' => 'Ef Entitlement Ksh',
            'amount_reflected_in_dora_ksh' => 'Amount Reflected In Dora Ksh',
            'transfers_into_ef' => 'Transfers Into Ef',
            'arrears' => 'Arrears',
        ];
    }

}

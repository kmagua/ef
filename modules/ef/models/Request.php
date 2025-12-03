<?php

namespace app\modules\ef\models;

use Yii;

/**
 * This is the model class for table "equalization_fund_request".
 *
 * @property int $id
 * @property string|null $county
 * @property string|null $sector
 * @property string|null $fiscal_year
 * @property float|null $amount_requested
 * @property string|null $status
 * @property int $user_id
 * @property string $date_requested
 *
 * @property EqualizationFundDisbursement[] $equalizationFundDisbursements
 */
class Request extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equalization_fund_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount_requested'], 'number'],
            [['status'], 'string'],
            [['user_id'], 'integer'],
            [['date_requested'], 'safe'],
            [['county', 'sector', 'fiscal_year'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'county' => 'County',
            'sector' => 'Sector',
            'fiscal_year' => 'Fiscal Year',
            'amount_requested' => 'Amount Requested',
            'status' => 'Status',
            'user_id' => 'User ID',
            'date_requested' => 'Date Requested',
        ];
    }

    /**
     * Gets query for [[EqualizationFundDisbursements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEqualizationFundDisbursements()
    {
        return $this->hasMany(EqualizationFundDisbursement::class, ['request_id' => 'id']);
    }
}

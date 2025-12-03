<?php

namespace app\modules\ef\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "equalization_fund_disbursement".
 *
 * @property int $id
 * @property string|null $county
 * @property string|null $sector
 * @property string|null $fiscal_year
 * @property float|null $amount_disbursed
 * @property int $user_id
 * @property string $date_disbursed
 */
class Disbursement extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equalization_fund_disbursement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Default values
            [['county', 'sector', 'fiscal_year', 'amount_disbursed'], 'default', 'value' => null],
            [['user_id'], 'default', 'value' => 1],

            // Data types
            [['amount_disbursed'], 'number'],
            [['user_id'], 'integer'],
            [['date_disbursed'], 'safe'],

            // Max lengths
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
            'amount_disbursed' => 'Amount Disbursed',
            'user_id' => 'User ID',
            'date_disbursed' => 'Date Disbursed',
        ];
    }

    /**
     * Get total disbursement grouped by county.
     * Returns simple array of objects for charts.
     */
    public static function getTotalPerCounty()
    {
        $sql = "
            SELECT county, SUM(amount_disbursed) AS amount_disbursed
            FROM equalization_fund_disbursement
            GROUP BY county
            ORDER BY county ASC
        ";

        return self::findBySql($sql)->all();
    }

    /**
     * Get disbursement summary per sector for a given county.
     * Returns an ActiveDataProvider (used in detail views).
     */
    public static function getPerCountyDisbursement($county)
    {
        $sql = "
            SELECT sector, SUM(amount_disbursed) AS amount_disbursed
            FROM equalization_fund_disbursement
            WHERE county = :county
            GROUP BY sector
            ORDER BY sector ASC
        ";

        $query = self::findBySql($sql, [':county' => $county]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }
}

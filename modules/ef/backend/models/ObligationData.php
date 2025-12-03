<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "obligation_data".
 *
 * @property int $id
 * @property int $obligation_id
 * @property string|null $fy
 * @property float|null $amt
 *
 * @property FinancialYear $fy0
 * @property Obligation $obligation
 */
class ObligationData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'obligation_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obligation_id', 'amt', 'fy'], 'required'],
            [['obligation_id'], 'integer'],
            [['amt'], 'number'],
            [['fy'], 'string', 'max' => 15],
            [['obligation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Obligation::class, 'targetAttribute' => ['obligation_id' => 'id']],
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
            'obligation_id' => 'Obligation ID',
            'fy' => 'Financial year',
            'amt' => 'Amount (KES)',
        ];
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
     * Gets query for [[Obligation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObligation()
    {
        return $this->hasOne(Obligation::class, ['id' => 'obligation_id']);
    }
}

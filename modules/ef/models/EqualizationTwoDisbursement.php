<?php

namespace app\modules\ef\models;

use Yii;

/**
 * This is the model class for table "eq2_disbursement".
 *
 * @property int $id
 * @property string|null $county
 * @property float|null $approved_budget
 * @property float|null $total_disbursement
 */
class EqualizationTwoDisbursement extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eq2_disbursement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county', 'approved_budget', 'total_disbursement'], 'default', 'value' => null],
            [['approved_budget', 'total_disbursement'], 'number'],
            [['county'], 'string', 'max' => 100],
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
            'approved_budget' => 'Approved Budget',
            'total_disbursement' => 'Total Disbursement',
        ];
    }

}

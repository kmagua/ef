<?php

namespace app\modules\ef\models;

use Yii;

/**
 * This is the model class for table "eq2_appropriation".
 *
 * @property int $id
 * @property string|null $county
 * @property string|null $constituency
 * @property string|null $ward
 * @property string|null $marginalised_areas
 * @property float|null $allocation_ksh
 * @property string|null $financial_year
 */
class EqualizationTwoAppropriation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eq2_appropriation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county', 'constituency', 'ward', 'marginalised_areas', 'allocation_ksh', 'financial_year'], 'default', 'value' => null],
            [['allocation_ksh'], 'number'],
            [['county', 'constituency', 'ward'], 'string', 'max' => 100],
            [['marginalised_areas'], 'string', 'max' => 255],
            [['financial_year'], 'string', 'max' => 9],
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
            'constituency' => 'Constituency',
            'ward' => 'Ward',
            'marginalised_areas' => 'Marginalised Areas',
            'allocation_ksh' => 'Allocation Ksh',
            'financial_year' => 'Financial Year',
        ];
    }

}

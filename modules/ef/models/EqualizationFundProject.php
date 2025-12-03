<?php

namespace app\modules\ef\models;

use Yii;

/**
 * This is the model class for table "equalization_fund_project".
 *
 * @property int $id
 * @property string $project_name
 * @property string $county
 * @property string|null $constituency
 * @property string $sector
 * @property float|null $budget_2018_19
 * @property float|null $contract_sum
 * @property int|null $percent_completion
 * @property string $funding_source
 */
class EqualizationFundProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equalization_fund_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_name', 'county', 'sector'], 'required'],
            [['budget_2018_19', 'contract_sum'], 'number'],
            [['percent_completion'], 'integer'],
            [['project_name'], 'string', 'max' => 255],
            [['county', 'constituency', 'sector'], 'string', 'max' => 100],
            [['funding_source'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_name' => 'Project Name',
            'county' => 'County',
            'constituency' => 'Constituency',
            'sector' => 'Sector',
            'budget_2018_19' => 'Budget',
            'contract_sum' => 'Contract Sum',
            'percent_completion' => 'Percent Completion',
            'funding_source' => 'Funding Source',
        ];
    }
}

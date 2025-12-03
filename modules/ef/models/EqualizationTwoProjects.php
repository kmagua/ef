<?php

namespace app\modules\ef\models;

use Yii;

/**
 * This is the model class for table "eq2_projects".
 *
 * @property int $id
 * @property string|null $county
 * @property string|null $constituency
 * @property string|null $ward
 * @property string|null $marginalised_area
 * @property string|null $project_description
 * @property string|null $sector
 * @property float|null $project_budget
 * @property string|null $financial_year
 */
class EqualizationTwoProjects extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eq2_projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['county', 'constituency', 'ward', 'marginalised_area', 'project_description', 'sector', 'project_budget', 'financial_year'], 'default', 'value' => null],
            [['project_description'], 'string'],
            [['project_budget'], 'number'],
            [['latitude', 'longitude'], 'number'],
            [['county', 'constituency', 'ward', 'sector'], 'string', 'max' => 100],
            [['marginalised_area'], 'string', 'max' => 150],
            [['financial_year'], 'string', 'max' => 20],
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
            'marginalised_area' => 'Marginalised Area',
            'project_description' => 'Project Description',
            'sector' => 'Sector',
            'project_budget' => 'Project Budget',
            'financial_year' => 'Financial Year',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

}

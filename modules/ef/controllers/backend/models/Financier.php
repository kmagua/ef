<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "financier".
 *
 * @property int $id
 * @property string|null $financier_name
 *
 * @property Projects[] $projects
 */
class Financier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'financier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['financier_name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'financier_name' => 'Financier Name',
        ];
    }

    /**
     * Gets query for [[Projects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Projects::class, ['financierid' => 'id']);
    }
}

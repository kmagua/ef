<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property int|null $financierid
 * @property string $project_code
 * @property string|null $project_name
 *
 * @property AdditionalRevShare[] $additionalRevShares
 * @property Financier $financier
 * @property RevenueShare[] $revenueShares
 */
class Projects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['financierid'], 'integer'],
            [['project_code', 'project_name', 'financierid'], 'required'],
            [['project_name'], 'string'],
            [['project_code'], 'string', 'max' => 4],
            [['project_code'], 'unique'],
            [['financierid'], 'exist', 'skipOnError' => true, 'targetClass' => Financier::class, 'targetAttribute' => ['financierid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'financierid' => 'Financierid',
            'project_code' => 'Project Code',
            'project_name' => 'Project Name',
        ];
    }

    /**
     * Gets query for [[AdditionalRevShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalRevShares()
    {
        return $this->hasMany(AdditionalRevShare::class, ['project_id' => 'id']);
    }

    /**
     * Gets query for [[Financier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinancier()
    {
        return $this->hasOne(Financier::class, ['id' => 'financierid']);
    }

    /**
     * Gets query for [[RevenueShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRevenueShares()
    {
        return $this->hasMany(RevenueShare::class, ['project_id' => 'id']);
    }
}

<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "obligations".
 *
 * @property int $id
 * @property string|null $description
 *
 * @property ObligationData[] $obligationDatas
 */
class Obligation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'obligations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['description'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[ObligationDatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObligationDatas()
    {
        return $this->hasMany(ObligationData::class, ['obligation_id' => 'id']);
    }
}

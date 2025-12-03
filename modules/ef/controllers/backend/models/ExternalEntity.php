<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "external_entity".
 *
 * @property int $id
 * @property string $entity_name
 * @property string|null $type
 * @property int|null $parent_mda
 * @property string|null $po_box
 * @property string|null $physical_address
 * @property int|null $added_by
 * @property string|null $date_added
 *
 * @property Letter[] $letters
 */
class ExternalEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'external_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity_name', 'type'], 'required'],
            [['type'], 'string'],
            [['parent_mda', 'added_by'], 'integer'],
            [['entity_name'], 'unique'],
            [['date_added'], 'safe'],
            /*[
                'parent_mda', 'required', 'when' => function($model){
                return $model->type == 'Government MDA';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#externalentity-type').val() == 'Government MDA';
                }"
            ],*/
            [['entity_name'], 'string', 'max' => 150],
            [['po_box', 'physical_address'], 'string', 'max' => 400],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_name' => 'Entity Name',
            'type' => 'Type',
            'parent_mda' => 'Parent MDA',
            'po_box' => 'P.O. Box',
            'physical_address' => 'Physical Address',
            'added_by' => 'Added By',
            'date_added' => 'Date Added',
        ];
    }

    /**
     * Gets query for [[Letters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetters()
    {
        return $this->hasMany(Letter::class, ['entity_id' => 'id']);
    }
}

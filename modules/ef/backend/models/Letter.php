<?php

namespace app\modules\backend\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "letter".
 *
 * @property int $id
 * @property string $title
 * @property int|null $from_county
 * @property int|null $entity_id
 * @property string|null $letter
 * @property string $letter_date
 * @property string $reference_no
 * @property string $status
 * @property int $added_by
 * @property string|null $date_added
 *
 * @property User $addedBy
 * @property ExternalEntity $entity
 * @property County $fromCounty
 */
class Letter extends \yii\db\ActiveRecord
{
    public $letter_upload, $assign_to;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'letter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'added_by', 'letter_date', 'entity_id'], 'required'],
            [['letter_upload'], 'required', 'on'=>'new'],
            [['from_county', 'entity_id', 'added_by', 'assign_to'], 'integer'],
            [['date_added'], 'safe'],
            [
                'from_county', 'required', 'when' => function($model){
                return $model->entity_id == '6';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#letter-entity_id').val() == '6';
                }"
            ],
            [['title', 'reference_no', 'letter_date'], 'string', 'max' => 250],
            [['letter'], 'string', 'max' => 200],
            [['status'], 'string', 'max' => 100],
            [['letter_upload'], 'file', 'skipOnEmpty' => true, 'extensions' => ['doc','docx', 'xls','xlsx', 'pdf', 'ppt', 'pptx'] , 'maxSize'=> 1024*1024*10],
            [['from_county'], 'exist', 'skipOnError' => true, 'targetClass' => County::class, 'targetAttribute' => ['from_county' => 'CountyId']],
            [['added_by'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['added_by' => 'id']],
            [['entity_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExternalEntity::class, 'targetAttribute' => ['entity_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'from_county' => 'From: County',
            'entity_id' => 'From: Entity',
            'letter' => 'Letter',
            'reference_no' => 'Reference No.',
            'status' => 'Status',
            'added_by' => 'Added By',
            'date_added' => 'Date Added',
        ];
    }

    /**
     * Gets query for [[AddedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddedBy()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'added_by']);
    }

    /**
     * Gets query for [[Entity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntity()
    {
        return $this->hasOne(ExternalEntity::class, ['id' => 'entity_id']);
    }

    /**
     * Gets query for [[FromCounty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromCounty()
    {
        return $this->hasOne(County::class, ['CountyId' => 'from_county']);
    }
    
    /**
     * 
     * @return type
     */
    public function behaviors()
    {
    	return [
			\raoul2000\workflow\base\SimpleWorkflowBehavior::className(),
            'auditTrail' => ['class' => \app\components\AuditTrailLogger::className()]
    	];
    }
    
    /*public function beforeValidate() {
        parent::beforeValidate();
        if($this->entity_id == '' && $this->from_county == ''){
            $this->addError('from_county', 'Either County or an External Entity Must be filled');
            $this->addError('entity_id', 'Either County or an External Entity Must be filled');
        }
        if($this->entity_id != '' && $this->from_county != ''){
            $this->addError('from_county', 'You cannot fill both County and External Entity');
            $this->addError('entity_id', 'You cannot fill both County and External Entity');
        }
        return true;
    }*/
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if($insert && $this->assign_to != ''){
            $this->assignLetter();
        }
        return true;
    }
    
    public function assignLetter()
    {
        $rec = new LetterAction();
        $rec->action_by = Yii::$app->user->identity->id;
        $rec->assigned_to = $this->assign_to;
        $rec->action_name = 'Assign';
        $rec->comment = 'Address';
        $rec->letter_id = $this->id;
        if($rec->save(false)){
            $rec->letter->status = 'LetterWorkflow/assigned';
            $rec->letter->save('false');
            $rec->sendEmail();
        }       
    }
    
    /**
     * 
     * @return boolean
     */
    public function saveWithFile()
    {
        $this->letter_upload = \yii\web\UploadedFile::getInstance($this, 'letter_upload');
        if($this->letter_upload){
            $this->letter = 'uploads/letters/correspondence-' . microtime() .
                '.' . $this->letter_upload->extension;
        }
        if($this->save()){
            //$this->refresh();
            if($this->letter_upload){
                ($this->letter_upload)? $this->letter_upload->saveAs($this->letter):null;
            }
            return true;
        }
        return false;
    }
    
    /**
     * return the link to a protocol file
     * @author kmagua
     * @return string
     */
    public function fileLink($icon = true)
    {
        if($this->letter != ''){
            $text = ($icon== true)?"<span class='glyphicon glyphicon-download-alt' title='Download - {$this->letter}'>Download Document</span>" :
                \yii\helpers\Html::encode($this->letter);
            $path = Yii::getAlias('@web') ."/";
            return \yii\helpers\Html::a($text,$path . $this->letter,['data-pjax'=>"0", 'target'=>'_blank']);
        }else{
            return '';
        }
    }
    
    public function getStatus()
    {
        switch($this->status){
            case 'LetterWorkflow/new':
                return $this->assignLink();
            case 'LetterWorkflow/assigned':
                return $this->assignOrCompleteLink();
            case 'LetterWorkflow/re-assigned':
                return $this->assignOrCompleteLink();
            case 'completed':
                return 'completed';
        }
    }
    
    /**
     * 
     * @return type
     */
    public function assignLink()
    {
        return Html::a('assign',
            ['/backend/letter/action', 'lid' => $this->id, 'st'=>'a'], ['data-pjax'=>'0',
                'onclick' => "getDataForm(this.href, '<h3>Assign for Action</h3>'); return false;"]);
    }
    
    /**
     * 
     * @return type
     */
    public function assignOrCompleteLink()
    {
        return $this->assignLink() . ' | ' . Html::a('complete',
            ['/backend/letter/action', 'lid' => $this->id, 'st'=>'co'], ['data-pjax'=>'0',
                'onclick' => "getDataForm(this.href, '<h3>Mark as Completed</h3>'); return false;"]);
    }
    
    /**
     * 
     * @param type $st
     */
    public function updateAfterAssigning($st)
    {
        if($st == 'a'){
            if($this->status == 'LetterWorkflow/new'){
                $this->status = 'LetterWorkflow/assigned';
            }else{
                $this->status = 'LetterWorkflow/re-assigned';
            }
        }else if($st == 'co'){
            $this->status = 'LetterWorkflow/complete';
        }
        $this->save(false);
    }
}

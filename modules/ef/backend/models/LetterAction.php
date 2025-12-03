<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "letter_action".
 *
 * @property int $id
 * @property string $action_name
 * @property int $letter_id
 * @property int|null $assigned_to
 * @property string $comment
 * @property int $action_by
 * @property string|null $file_upload
 * @property string $date_actioned
 *
 * @property User $actionBy
 * @property User $assignedTo
 * @property Letter $letter
 */
class LetterAction extends \yii\db\ActiveRecord
{
    public $file_upload_file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'letter_action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action_name', 'comment', 'action_by', 'letter_id'], 'required'],
            [['action_name'], 'string'],
            [['assigned_to', 'action_by', 'letter_id'], 'integer'],
            [['date_actioned'], 'safe'],
            [['comment'], 'string', 'max' => 250],
            [['file_upload'], 'string', 'max' => 200],
            [['file_upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => ['doc','docx', 'xls','xlsx', 'pdf', 'ppt', 'pptx'] , 'maxSize'=> 1024*1024*10],
            [['action_by'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['action_by' => 'id']],
            [['assigned_to'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\User::class, 'targetAttribute' => ['assigned_to' => 'id']],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::class, 'targetAttribute' => ['letter_id' => 'id']], 
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_name' => 'Action Name',
            'assigned_to' => 'Assigned To',
            'comment' => 'Comment',
            'action_by' => 'Action By',
            'file_upload' => 'File Upload',
            'date_actioned' => 'Date Actioned',
        ];
    }

    /**
     * Gets query for [[ActionBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActionBy()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'action_by']);
    }

    /**
     * Gets query for [[AssignedTo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedTo()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'assigned_to']);
    }
    
    /**
    * Gets query for [[Letter]].
    *
    * @return \yii\db\ActiveQuery
    */
    public function getLetter()
    {
        return $this->hasOne(Letter::class, ['id' => 'letter_id']);
    }
    
    /**
     * 
     * @return boolean
     */
    public function saveWithFile()
    {
        $this->file_upload_file = \yii\web\UploadedFile::getInstance($this, 'file_upload_file');
        if($this->file_upload_file){
            $this->file_upload = 'uploads/letters/actiondoc-' . microtime() .
                '.' . $this->file_upload_file->extension;
        }
        if($this->save()){
            //$this->refresh();
            if($this->file_upload_file){
                ($this->file_upload_file)? $this->file_upload_file->saveAs($this->file_upload):null;
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
        if($this->file_upload != ''){
            $text = ($icon== true)?"<span class='glyphicon glyphicon-download-alt' title='Download - {$this->file_upload}'>Download Document</span>" :
                \yii\helpers\Html::encode($this->file_upload);
            $path = Yii::getAlias('@web') ."/";
            return \yii\helpers\Html::a($text,$path . $this->file_upload,['data-pjax'=>"0", 'target'=>'_blank']);
        }else{
            return '';
        }
    }
    
    public function sendEmail()
    {
        $this->refresh(); // pull updates from table
        $hash = \app\models\Utility::generateRandomString();
        
        //$hash = password_hash($this->id . '' . $this->status . strtolower($this->email), PASSWORD_DEFAULT);
        
        $link = \yii\helpers\Url::to(['/backend/letter/view', 'id' => $this->letter->id], true);
        $header = 'You have been assigned a Correspondence';
        $text_link = '<a href="' . $link. '" target="_blank">linked here</a>';
        $msg = <<<MSG
            Dear {$this->assignedTo->user_names}, <p>
            Your have been assigned the letter $text_link for your review and action. </p>
            $link

        <p>Regards,</p>
        IGFR Team.
MSG;
        \app\models\Utility::sendMail($this->assignedTo->email, "$header", $msg);        
    }
}

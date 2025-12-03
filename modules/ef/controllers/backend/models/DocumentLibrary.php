<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "document_library".
 *
 * @property int $id
 * @property string $document_name
 * @property int $document_type
 * @property int $financial_year
 * @property string $document_upload_path
 * @property string|null $keywords
 * @property string $upload_date
 * @property int $uploaded_by
 * @property string $publish_status
 * @property string|null $published_date
 * @property int|null $published_by
 * @property string|null $document_date
 * @property string|null $status
 * @property string|null $applicable_to
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property DocumentType $documentType
 * @property FinancialYear $financialYear
 * @property User $uploadedBy
 */
class DocumentLibrary extends \yii\db\ActiveRecord
{
    public $document_upload_path_file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_library';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_name', 'document_type', 'financial_year', 'document_upload_path', 'upload_date', 'uploaded_by'], 'required'],
            [['document_type', 'financial_year', 'uploaded_by', 'published_by'], 'integer'],
            [['keywords', 'applicable_to'], 'string'],
            [['upload_date', 'published_date', 'document_date', 'updated_at', 'deleted_at'], 'safe'],
            [['document_name', 'document_upload_path', 'publish_status'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 100],
            [['document_upload_path_file'], 'file', 'skipOnEmpty' => true, 'extensions' => ['doc','docx', 'xls','xlsx', 'pdf', 'ppt', 'pptx'] , 'maxSize'=> 1024*1024*10],
            [['financial_year'], 'exist', 'skipOnError' => true, 'targetClass' => FinancialYear::class, 'targetAttribute' => ['financial_year' => 'id']],
            [['document_type'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentType::class, 'targetAttribute' => ['document_type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_name' => 'Document Name',
            'document_type' => 'Document Type',
            'financial_year' => 'Financial Year',
            'document_upload_path' => 'Document Upload Path',
            'keywords' => 'Keywords',
            'upload_date' => 'Upload Date',
            'uploaded_by' => 'Uploaded By',
            'publish_status' => 'Publish Status',
            'published_date' => 'Published Date',
            'published_by' => 'Published By',
            'document_date' => 'Document Date',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[DocumentType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentType()
    {
        return $this->hasOne(DocumentType::class, ['id' => 'document_type']);
    }
    
    /**
     * Gets query for [[DocumentType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploadedBy()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'uploaded_by']);
    }

    /**
     * Gets query for [[FinancialYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinancialYear()
    {
        return $this->hasOne(FinancialYear::class, ['id' => 'financial_year']);
    }
    
    /**
     * 
     * @return boolean
     */
    public function saveWithFile()
    {
        $this->document_upload_path_file = \yii\web\UploadedFile::getInstance($this, 'document_upload_path_file');
        if($this->document_upload_path_file){
            $this->document_upload_path = 'uploads/documents/doc-' . microtime() .
                '.' . $this->document_upload_path_file->extension;
        }
        if($this->save()){
            //$this->refresh();
            if($this->document_upload_path_file){
                ($this->document_upload_path_file)? $this->document_upload_path_file->saveAs($this->document_upload_path):null;
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
        if($this->document_upload_path != ''){
            $text = ($icon== true)?"<span class='glyphicon glyphicon-download-alt' title='Download - {$this->document_upload_path}'>Download Document</span>" :
                \yii\helpers\Html::encode($this->document_upload_path);
            $path = Yii::getAlias('@web') ."/";
            return \yii\helpers\Html::a($text,$path . $this->document_upload_path,['data-pjax'=>"0", 'target'=>'_blank']);
        }else{
            return '';
        }
    }
    
    public static function getAllDocumentsByType($dt)
    {
        //$govt_level = ($ctid != '')?' AND county_id =' . $ctid:'';
        $sql = "SELECT COUNT(id) FROM document_library dl WHERE document_type = $dt
            -- AND `status` = 'DocumentLibrariesWorkflow/published'             
            GROUP BY document_type";
        $rst = \Yii::$app->db->createCommand($sql)->queryScalar();
        if($rst){
            return $rst;
        }
        return 0;
    }
}

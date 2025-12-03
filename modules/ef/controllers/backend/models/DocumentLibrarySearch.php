<?php

namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\DocumentLibrary;

/**
 * DocumentLibrarySearch represents the model behind the search form of `app\modules\backend\models\DocumentLibrary`.
 */
class DocumentLibrarySearch extends DocumentLibrary
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'document_type', 'financial_year', 'uploaded_by', 'published_by'], 'integer'],
            [['document_name', 'document_upload_path', 'keywords', 'upload_date', 'publish_status', 'published_date', 'document_date', 'status', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = DocumentLibrary::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'document_type' => $this->document_type,
            'financial_year' => $this->financial_year,
            'upload_date' => $this->upload_date,
            'uploaded_by' => $this->uploaded_by,
            'published_date' => $this->published_date,
            'published_by' => $this->published_by,
            'document_date' => $this->document_date,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'document_name', $this->document_name])
            ->andFilterWhere(['like', 'document_upload_path', $this->document_upload_path])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'publish_status', $this->publish_status])
            ->andFilterWhere(['like', 'status', $this->status]);
        $query->orderBy('financial_year desc');
        return $dataProvider;
    }
}

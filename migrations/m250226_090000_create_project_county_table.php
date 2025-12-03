<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;

class m250226_090000_create_project_county_table extends Migration
{
    private const TABLE_NAME = '{{%project_county}}';

    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'project_id' => $this->integer()->notNull(),
            'county_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-project_county', self::TABLE_NAME, ['project_id', 'county_id']);
        $this->createIndex('idx-project_county-project_id', self::TABLE_NAME, 'project_id');
        $this->createIndex('idx-project_county-county_id', self::TABLE_NAME, 'county_id');

        $this->addForeignKey(
            'fk-project_county-project',
            self::TABLE_NAME,
            'project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-project_county-county',
            self::TABLE_NAME,
            'county_id',
            '{{%county}}',
            'CountyId',
            'CASCADE',
            'CASCADE'
        );

        $projectRows = (new \yii\db\Query())
            ->select(['id', 'county_id'])
            ->from('{{%projects}}')
            ->where(['not', ['county_id' => null]])
            ->all();

        if (!empty($projectRows)) {
            $rows = [];
            foreach ($projectRows as $row) {
                $projectId = (int) ($row['id'] ?? 0);
                $countyId = (int) ($row['county_id'] ?? 0);
                if ($projectId > 0 && $countyId > 0) {
                    $rows[] = [$projectId, $countyId];
                }
            }

            if (!empty($rows)) {
                $this->batchInsert(self::TABLE_NAME, ['project_id', 'county_id'], $rows);
            }
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-project_county-project', self::TABLE_NAME);
        $this->dropForeignKey('fk-project_county-county', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}

<?php

use yii\db\Migration;

class m250226_090100_create_project_location_table extends Migration
{
    private const TABLE_NAME = '{{%project_location}}';

    public function safeUp()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'latitude' => $this->decimal(10, 6)->notNull(),
            'longitude' => $this->decimal(10, 6)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-project_location-project', self::TABLE_NAME, 'project_id');
        $this->createIndex('idx-project_location-lat_lng', self::TABLE_NAME, ['latitude', 'longitude']);

        $this->addForeignKey(
            'fk-project_location-project',
            self::TABLE_NAME,
            'project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $rows = (new \yii\db\Query())
            ->select(['id', 'latitude', 'longitude'])
            ->from('{{%projects}}')
            ->where([
                'and',
                ['not', ['latitude' => null]],
                ['not', ['longitude' => null]],
            ])
            ->all();

        if (!empty($rows)) {
            $insertRows = [];
            foreach ($rows as $row) {
                $lat = (float) ($row['latitude'] ?? null);
                $lng = (float) ($row['longitude'] ?? null);
                $projectId = (int) ($row['id'] ?? 0);
                if ($projectId > 0 && is_finite($lat) && is_finite($lng)) {
                    $insertRows[] = [$projectId, $lat, $lng];
                }
            }

            if (!empty($insertRows)) {
                $this->batchInsert(self::TABLE_NAME, ['project_id', 'latitude', 'longitude'], $insertRows);
            }
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-project_location-project', self::TABLE_NAME);
        $this->dropIndex('idx-project_location-lat_lng', self::TABLE_NAME);
        $this->dropIndex('idx-project_location-project', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}

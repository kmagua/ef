<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;

class m230924_150000_add_county_and_location_to_projects extends Migration
{
    public function safeUp()
    {
        $tableSchema = $this->db->schema->getTableSchema('{{%projects}}');
        if ($tableSchema === null) {
            throw new InvalidConfigException('The projects table does not exist.');
        }

        if ($tableSchema->getColumn('county_id') === null) {
            $this->addColumn('{{%projects}}', 'county_id', $this->integer()->null()->after('financierid'));
        }

        if ($tableSchema->getColumn('latitude') === null) {
            $this->addColumn('{{%projects}}', 'latitude', $this->decimal(10, 6)->null()->after('project_name'));
        }

        if ($tableSchema->getColumn('longitude') === null) {
            $this->addColumn('{{%projects}}', 'longitude', $this->decimal(10, 6)->null()->after('latitude'));
        }

        $tableSchema = $this->db->schema->getTableSchema('{{%projects}}', true);
        if ($tableSchema->getColumn('county_id') !== null) {
            $indexExists = $this->db->createCommand(
                "SHOW INDEX FROM {{%projects}} WHERE Key_name = :name"
            )->bindValue(':name', 'idx-projects-county_id')->queryOne();
            if ($indexExists === false) {
                $this->createIndex('idx-projects-county_id', '{{%projects}}', 'county_id');
            }

            $fkExists = $this->db->createCommand(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND CONSTRAINT_NAME = :name"
            )->bindValues([':table' => $this->db->schema->getRawTableName('{{%projects}}'), ':name' => 'fk_projects_county'])
             ->queryScalar();

            if ($fkExists === false || $fkExists === null) {
                $this->addForeignKey(
                    'fk_projects_county',
                    '{{%projects}}',
                    'county_id',
                    '{{%county}}',
                    'CountyId',
                    'SET NULL',
                    'CASCADE'
                );
            }
        }
    }

    public function safeDown()
    {
        $tableSchema = $this->db->schema->getTableSchema('{{%projects}}');
        if ($tableSchema === null) {
            return;
        }

        $fkExists = $this->db->createCommand(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND CONSTRAINT_NAME = :name"
        )->bindValues([':table' => $this->db->schema->getRawTableName('{{%projects}}'), ':name' => 'fk_projects_county'])->queryScalar();
        if ($fkExists) {
            $this->dropForeignKey('fk_projects_county', '{{%projects}}');
        }

        $indexExists = $this->db->createCommand(
            "SHOW INDEX FROM {{%projects}} WHERE Key_name = :name"
        )->bindValue(':name', 'idx-projects-county_id')->queryOne();
        if ($indexExists) {
            $this->dropIndex('idx-projects-county_id', '{{%projects}}');
        }

        if ($tableSchema->getColumn('longitude') !== null) {
            $this->dropColumn('{{%projects}}', 'longitude');
        }
        if ($tableSchema->getColumn('latitude') !== null) {
            $this->dropColumn('{{%projects}}', 'latitude');
        }
        if ($tableSchema->getColumn('county_id') !== null) {
            $this->dropColumn('{{%projects}}', 'county_id');
        }
    }
}

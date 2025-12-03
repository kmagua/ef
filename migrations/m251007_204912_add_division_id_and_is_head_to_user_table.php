<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `user`.
 */
class mXXXXXXXXXXXXXX_add_division_id_and_is_head_to_user_table extends Migration
{
    public function safeUp()
    {
        // add column division_id
        $this->addColumn('{{%user}}', 'division_id', $this->integer()->null()->after('last_updated'));

        // add column is_head (boolean)
        $this->addColumn('{{%user}}', 'is_head', $this->boolean()->notNull()->defaultValue(false)->after('division_id'));

        // create index for division_id
        $this->createIndex(
            '{{%idx-user-division_id}}',
            '{{%user}}',
            'division_id'
        );

        // optional: add foreign key if divisions table exists
        // uncomment if you have created the divisions table
        /*
        $this->addForeignKey(
            '{{%fk-user-division_id}}',
            '{{%user}}',
            'division_id',
            '{{%divisions}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        */
    }

    public function safeDown()
    {
        // optional: drop fk first if added
        /*
        $this->dropForeignKey('{{%fk-user-division_id}}', '{{%user}}');
        */
        $this->dropIndex('{{%idx-user-division_id}}', '{{%user}}');
        $this->dropColumn('{{%user}}', 'is_head');
        $this->dropColumn('{{%user}}', 'division_id');
    }
}

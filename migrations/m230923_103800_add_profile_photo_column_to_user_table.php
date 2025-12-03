<?php

use yii\db\Migration;

class m230923_103800_add_profile_photo_column_to_user_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'profile_photo', $this->string(255)->null()->after('last_login_date'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'profile_photo');
    }
}

<?php

use yii\db\Migration;
use yii\db\Query;

class m230923_111500_change_profile_photo_storage extends Migration
{
    public function safeUp()
    {
        $this->execute("ALTER TABLE {{%user}} MODIFY `profile_photo` LONGBLOB NULL");
        $this->addColumn('{{%user}}', 'profile_photo_type', $this->string(100)->null()->after('profile_photo'));

        $rows = (new Query())
            ->select(['id', 'profile_photo'])
            ->from('{{%user}}')
            ->where(['not', ['profile_photo' => null]])
            ->andWhere(['!=', 'profile_photo', ''])
            ->all();

        foreach ($rows as $row) {
            $stored = $row['profile_photo'];
            if (!is_string($stored)) {
                continue;
            }

            $path = ltrim($stored, '/');
            $absolute = \Yii::getAlias('@webroot/' . $path);
            if (is_file($absolute)) {
                $data = @file_get_contents($absolute);
                if ($data !== false) {
                    $mime = function_exists('mime_content_type') ? @mime_content_type($absolute) : null;
                    $this->update('{{%user}}', [
                        'profile_photo' => $data,
                        'profile_photo_type' => $mime ?: 'application/octet-stream',
                    ], ['id' => $row['id']]);
                    continue;
                }
            }

            $this->update('{{%user}}', [
                'profile_photo' => null,
                'profile_photo_type' => null,
            ], ['id' => $row['id']]);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'profile_photo_type');
        $this->alterColumn('{{%user}}', 'profile_photo', $this->string(255)->null());
    }
}

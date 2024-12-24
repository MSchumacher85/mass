<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request}}`.
 */
class m241221_085027_create_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('Имя пользователя'),
            'email' => $this->string()->notNull()->comment('Почта'),
            'status' => "ENUM('Active', 'Resolved') DEFAULT 'Active'",
            'message' => $this->text()->notNull()->comment('Сообщение'),
            'comment' => $this->text()->defaultValue(null)->comment('Комментарий'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('Дата создания'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('Дата обновления'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request}}');
    }
}

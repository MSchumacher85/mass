<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Request extends ActiveRecord
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_RESOLVED = 'Resolved';

    public static function tableName()
    {
        return '{{%request}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            ['email', 'email'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_RESOLVED]],
            ['comment', 'required', 'when' => function ($model) {
                return $model->status === self::STATUS_RESOLVED;
            }],
        ];
    }
}

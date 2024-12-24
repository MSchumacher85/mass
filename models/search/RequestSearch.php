<?php

namespace app\models\search;

use yii\data\ActiveDataProvider;
use app\models\Request;

class RequestSearch extends Request
{
    public $date_from;
    public $date_to;

    public function rules()
    {
        return [
            [['status'], 'string'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params)
    {
        $query = Request::find();

        $this->load($params, '');
        if (!$this->validate()) {
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        if ($this->status) {
            $query->andWhere(['status' => $this->status]);
        }

        if ($this->date_from) {
            $query->andWhere(['>=', 'created_at', $this->date_from . ' 00:00:00']);
        }
        if ($this->date_to) {
            $query->andWhere(['<=', 'created_at', $this->date_to . ' 23:59:59']);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}

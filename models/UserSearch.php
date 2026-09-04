<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public $role; // Field kustom untuk filter Role RBAC

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'role'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10, // Menentukan limit 10 data per halaman
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC], // Order ID DESC secara default
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filter kondisi pencarian
        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email]);

        // Filter pencarian berdasarkan Role RBAC
        if (!empty($this->role)) {
            $authManager = Yii::$app->authManager;
            $userIds = $authManager->getUserIdsByRole($this->role);
            $query->andWhere(['id' => $userIds]);
        }

        return $dataProvider;
    }
}

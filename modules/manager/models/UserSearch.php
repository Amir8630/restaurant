<?php

namespace app\modules\manager\models;

use app\models\Role;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use PhpParser\Node\Expr\Cast\String_;
use Psy\Util\Str;
use Yii;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{
    public int $title_search = 0;
    public int $user_id = 0;
    public int $id_created_by = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by_id', 'role_id'], 'integer'],
            [['fio', 'email', 'gender', 'phone', 'password', 'auth_key'], 'safe'],
            [['title_search', 'id_created_by', 'user_id'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        // Если текущий пользователь не админ, скрываем админов и обычных пользователей
        $query = (Yii::$app->user->identity->role_id == Role::getRoleId('admin'))
            ? $query
            : $query->where(['!=', 'role_id', Role::getRoleId('admin')])
                ->andWhere(['!=', 'role_id', Role::getRoleId('user')]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 6], 
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_by_id' => $this->created_by_id,
            'role_id' => $this->role_id,
        ]);

        $query->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key]);

        return $dataProvider;
    }
}

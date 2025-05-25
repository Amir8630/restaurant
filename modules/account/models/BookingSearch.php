<?php

namespace app\modules\account\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Booking;
use Yii;

/**
 * BookingSearch represents the model behind the search form of `app\models\Booking`.
 */
class BookingSearch extends Booking
{
    public int $id_search = 0;
    public int $title_search = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'count_guest'], 'integer'],
            [['fio_guest', 'created_at', 'booking_date', 'booking_time_start', 'booking_time_end', 'phone', 'email', 'status_id'], 'safe'],
            [['title_search', 'id_search'], 'number'],

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
        $query = Booking::find()->where(['user_id' => Yii::$app->user->id])
        // ->with(['bookingTables.table.orders'])
        ;

        // add conditions that should always apply here

        // Сначала сортируем по статусу "забронировано", затем по дате создания
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 6],
            'sort' => [
            'defaultOrder' => [
                'status_id' => SORT_ASC, // предполагается, что "забронировано" имеет наименьший id
                'created_at' => SORT_DESC
            ]
            ]
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
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'booking_date' => $this->booking_date,
            'booking_time_start' => $this->booking_time_start,
            'booking_time_end' => $this->booking_time_end,
            'status_id' => $this->status_id,
            'count_guest' => $this->count_guest,
        ]);

        $query->andFilterWhere(['like', 'fio_guest', $this->fio_guest])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}

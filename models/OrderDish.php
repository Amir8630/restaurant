<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_dish".
 *
 * @property int $id
 * @property int $order_id
 * @property int $dish_id
 * @property int $count количество борща
 *
 * @property Dish $dish
 * @property Order $order
 */
class OrderDish extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_dish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                      [['dish_id', 'count'], 'required'],
            [['order_id','dish_id','count'], 'integer'],
            ['dish_id', 'exist',
                'skipOnError' => true,
                'targetClass' => Dish::class,
                'targetAttribute' => ['dish_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ №',
            'dish_id' => 'Блюдо №',
            'count' => 'Количество',
        ];
    }

    /**
     * Gets query for [[Dish]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDish()
    {
        return $this->hasOne(Dish::class, ['id' => 'dish_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}

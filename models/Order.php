<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $table_id
 * @property string $created_at
 * @property int $order_type с собой или на месте
 * @property int $order_status готов ил нет
 * @property int $waiter_id
 *
 * @property OrderDish[] $orderDishes
 * @property Table $table
 * @property User $waiter
 */
class Order extends \yii\db\ActiveRecord
{
    public $dishes = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_type', 'order_status', 'waiter_id'], 'required'],
            [['table_id', 'order_type', 'order_status', 'waiter_id'], 'integer'],
            [['created_at'], 'safe'],
            [['table_id'], 'exist', 'skipOnError' => true, 'targetClass' => Table::class, 'targetAttribute' => ['table_id' => 'id']],
            [['waiter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['waiter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Заказ №',
            'table_id' => 'Стол №',
            'created_at' => 'Время создания заказа',
            'order_type' => 'Тип заказа',
            'order_status' => 'Статус',
            'waiter_id' => 'Номер Официанта',
        ];
    }

    /**
     * Gets query for [[OrderDishes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDishes()
    {
        return $this->hasMany(OrderDish::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Table]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTable()
    {
        return $this->hasOne(Table::class, ['id' => 'table_id']);
    }

    /**
     * Gets query for [[Waiter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWaiter()
    {
        return $this->hasOne(User::class, ['id' => 'waiter_id']);
    }

    public function validateDishes($attr)
    {
        foreach ($this->dishes as $i => $df) {
            if (!$df->validate()) {
                $this->addError($attr."[$i]", 'Неверные данные в строке '.($i+1));
            }
        }
    }

    public function loadDishes($data)
    {
        $this->dishes = [];
        foreach ($data as $i => $item) {
            $df = new OrderDishForm();
            $df->load($item, '');
            $this->dishes[] = $df;
        }
    }
}

    class OrderDishForm extends Model
    {
    public $dish_id;
    public $count;
    public function rules()
    {
        return [
            [['dish_id','count'], 'required'],
            ['count', 'integer', 'min' => 1],
        ];
    }

    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'order_status']);
    }

}

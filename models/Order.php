<?php

namespace app\models;

use Yii;

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
            [['table_id', 'order_type', 'order_status', 'waiter_id'], 'required'],
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
            'id' => 'ID',
            'table_id' => 'Table ID',
            'created_at' => 'Created At',
            'order_type' => 'Order Type',
            'order_status' => 'Order Status',
            'waiter_id' => 'Waiter ID',
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
}

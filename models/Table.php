<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "table".
 *
 * @property int $id
 *
 * @property BookingTable[] $bookingTables
 * @property Order[] $orders
 */
class Table extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'table';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    /**
     * Gets query for [[BookingTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookingTables()
    {
        return $this->hasMany(BookingTable::class, ['table_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['table_id' => 'id']);
    }
}

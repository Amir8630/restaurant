<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "booking_table".
 *
 * @property int $id
 * @property int $booking_id
 * @property int $table_id
 *
 * @property Booking $booking
 * @property Table $table
 */
class BookingTable extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking_table';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['booking_id', 'table_id'], 'required'],
            [['booking_id', 'table_id'], 'integer'],
            [['booking_id'], 'exist', 'skipOnError' => true, 'targetClass' => Booking::class, 'targetAttribute' => ['booking_id' => 'id']],
            [['table_id'], 'exist', 'skipOnError' => true, 'targetClass' => Table::class, 'targetAttribute' => ['table_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_id' => 'Booking ID',
            'table_id' => 'Table ID',
        ];
    }

    /**
     * Gets query for [[Booking]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
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
    * Gets query for [[Status]]. 
    * 
    * @return \yii\db\ActiveQuery 
    */ 
   public function getStatus() 
   { 
       return $this->hasOne(Status::class, ['id' => 'status_id']); 
   } 
}

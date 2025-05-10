<?php

namespace app\models;

use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property string $fio_guest
 * @property int $user_id
 * @property string $created_at
 * @property string $booking_date
 * @property string $booking_time_start
 * @property string $booking_time_end по умол +2 часа от старта / пользватель указываеть если больше
 * @property int $status_id
 * @property int $count_guest
 * @property string $phone
 * @property string $email
 *
 * @property BookingTable[] $bookingTables
 * @property Satatus $status
 * @property User $user
 */
class Booking extends \yii\db\ActiveRecord
{
    public string $selected_tables = '';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio_guest', 'user_id', 'booking_date', 'booking_time_start', 'booking_time_end', 'status_id', 'count_guest', 'phone', 'email'], 'required'],
            ['selected_tables', 'required', 'message' => 'Выберите столик'],
            [['user_id', 'status_id', 'count_guest'], 'integer'],
            [['created_at', 'booking_date','booking_time_start' ,'booking_time_end'], 'safe'],
            [['fio_guest', 'phone', 'email'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/^\+7 \([0-9]{3}\)\-[0-9]{3}\-[0-9]{4}$/', 'message' => 'Только в формате +7 (999)-999-9999'],
            [['selected_tables', 'count_guest'], 'validateCountGuest'],
            ['booking_date', 'validateBookingDate'],
            [['booking_time_start', 'booking_time_end', 'booking_date'], 'validateTimeStart'],
            // нез насколько это плохо что всё отдаю
            // [['booking_date', 'booking_time_start', 'booking_time_end', 'count_guest', 'selected_tables'], 'validateBookingDate'],
            // [['booking_date', 'booking_time_start', 'booking_time_end', 'count_guest', 'selected_tables'], 'validateTimeStart'],
            // [['booking_date', 'booking_time_start', 'booking_time_end', 'count_guest', 'selected_tables'], 'validateCountGuest'],
        ];
    }
// Мы работаем с 07:00 до 23:00 и т.к у нас есть 
    public function validateBookingDate()
    {
        if ($this->booking_date < date('Y-m-d')) {
            return $this->addError('booking_date', 'Дата бронирования не может быть в прошлом.');
        }

        if ($this->booking_time_start < date('H:i') && $this->booking_date == date('Y-m-d')) {
            return $this->addError('booking_time_start', 'Вы не можете забронировать на прошедшее время.');
        }
        
    }

    public function validateTimeStart()
    {             
        if ($this->booking_time_start > '22:00') {
            return $this->addError('booking_time_start', 'Мы работаем ежедневно с 7:00 до 23:00. ');
        }

        if ($this->booking_time_start < '07:00') {
            return $this->addError('booking_time_start', 'Мы работаем ежедневно с 7:00 до 23:00. ');
        }

        if ($this->booking_time_start < date('H:i') && $this->booking_date == date('Y-m-d')) {
            return $this->addError('booking_time_start', 'Вы не можете забронировать на прошедшее время.');
        }
 
        if ($this->booking_time_start > $this->booking_time_end) {
            return $this->addError('booking_time_start', 'Время начала не может быть позже времени окончания.');
        }
    }
    
    public function validateTimeEnd()
    {
        // if($this->booking_time_start >= $this->booking_time_end) {
        //     if(! $this->booking_time_start == '22:00') {
        //         return $this->addError('booking_time_end', 'минимальный интеравал 2 часа или 1 час'); // плохо работате 12:12 12:12 нет ошибок чо не правильно 
        //     }
        // }
    }


    public function validateCountGuest()
    {     
        if (empty($this->selected_tables)) {
            return $this->addError('count_guest', 'Выберите столик');
        } else {
            $countTables = count(explode(',', $this->selected_tables));
        }

        if ($this->count_guest > $countTables * 6) { 
            $tableWord = $countTables == 1 ? 'стол' : ($countTables > 1 && $countTables < 5 ? 'стола' : 'столов');
            return $this->addError(
                'count_guest', 
                'Вы выбрали ' . $countTables . ' ' . $tableWord . ', максимальное количество гостей: ' . ($countTables * 6)
            );
        }

        if ($this->count_guest < $countTables) {
            $tableWord = $countTables == 1 ? 'стол' : ($countTables > 1 && $countTables < 5 ? 'стола' : 'столов');
            $guestWord = $this->count_guest == 1 ? 'гостя' : 'гостей';
            return $this->addError(
                'count_guest', 
                'Вы выбрали ' . $countTables . ' ' . $tableWord . ', минимальное количество гостей: ' . $countTables
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Бронь №',
            'fio_guest' => 'На кого бронируем?',
            'user_id' => 'Заказчик',
            'created_at' => 'Дата и время создания брони',
            'booking_date' => 'Дата брони',
            'booking_time_start' => 'Начало',
            // 'booking_time_end' => 'Окончание (*необходимо указать если больше 2 часов)',
            'booking_time_end' => 'Окончание',
            'status_id' => 'Статус',
            'count_guest' => 'Количество персон',
            'phone' => 'Номер телефона',
            'email' => 'Email',
        ];
    }

    /**
     * Gets query for [[BookingTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookingTables()
    {
        return $this->hasMany(BookingTable::class, ['booking_id' => 'id']);
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

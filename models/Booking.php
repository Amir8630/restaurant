<?php

namespace app\models;

use Yii;

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
            // ['phone', 'match', 'pattern' => '/^\+7 \([0-9]{3}\)\-[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Только в формате +7 (999)-999-99-99'],
            ['selected_tables', 'validateCountGuest'],
            ['booking_time_start', 'validateTimeStart'],
            ['booking_time_end', 'validateTimeEnd'],
        ];
    }

    public function validateTimeStart($attribute, $params)
    {
        if ($this->$attribute > '22:00') {
            $this->addError($attribute, 'Время прибытия не может быть позже 22:00.');
        }
    }
    
    public function validateTimeEnd($attribute, $params)
    {
        if($this->booking_time_start >= $this->$attribute) {
            if(! $this->booking_time_start == '22:00') {
                $this->addError($attribute, 'минимальный интеравал 2 часа или 1 час'); // плохо работате 12:12 12:12 нет ошибок чо не правильно 
            }
        }
    }


    public function validateCountGuest()
    {
        $countTables = explode(',', $this->selected_tables);
        $countTables = count($countTables); 

        if($this->count_guest > $countTables * 6) { 
            $this->addError('count_guest', 'Максимальное количество гостей на такое колисество столов: ' . $countTables * 6);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Бронь №',
            'fio_guest' => 'На какое имя бронируем?',
            'user_id' => 'Заказчик',
            'created_at' => 'Дата и время создания брони',
            'booking_date' => 'Дата брони',
            'booking_time_start' => 'Время прибытия',
            'booking_time_end' => 'Время отбытия (*необходимо указать если вы планируете остоваться больше 2 часов)',
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

<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $fio
 * @property string $email
 * @property int $gender
 * @property string $phone
 * @property string $password
 * @property int $role_id
 * @property string $auth_key 
 * @property string $gender
 *
 * @property Booking[] $bookings
 * @property Order[] $orders
 * @property Role $role
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio', 'gender', 'password', 'role_id', 'auth_key', 'email', 'phone'], 'required'],
            [['role_id'], 'integer'],
            [['fio', 'email', 'phone', 'password', 'auth_key'], 'string', 'max' => 255],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
            ['fio', 'match', 'pattern' => '/^[а-яёa-z\s\-]+$/ui', 'message' => 'ФИО должно содержать только кириллические или латинские буквы, пробелы и дефисы.'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот Email уже зарегистрирован.'],
            ['gender', 'string', 'max' => 255],
            ['phone', 'match', 'pattern' => '/^\+7 \([0-9]{3}\)\-[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Телефон должен быть в формате +7 (999)-999-99-99.'],
            ['password', 'match', 'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{6,}$/', 'message' => 'Только латиница и цифры, Минимум 6 символов, одну цифру, одну строчную и одну заглавную букву'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'fio' => 'ФИО',
            'email' => 'Email',
            'gender' => 'пол',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'role_id' => 'Роль',
            'created_by_id' => 'Кем Зарегистрирован',
        ];
    }

    /**
     * Gets query for [[Bookings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['waiter_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

     /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool|null if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getIsAdmin()
    {
        return $this->role_id == Role::getRoleId('admin');
    }

    public function getUserRole()
    {
        return $this->role->title;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public static function findByUsername($email)
    {
        return self::findOne(['email' => $email]);
    }

    public function getRoleTitle()
    {
        $roles = [
            1 => 'Администратор',
            2 => 'Пользователь',
            3 => 'Менеджер',
            4 => 'Повар',
            5 => 'Официант',
        ];

        return $roles[(int) $this->role_id] ?? 'Неизвестная роль';
    }
}

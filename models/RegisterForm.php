<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;

class RegisterForm extends Model
{
    public string $fio = '';
    public string $email = '';
    public string $gender = '';
    public string $phone = '';
    public string $password = '';
    public bool $rules = false;


    public function rules()
    {
        return [
            [['fio', 'email', 'phone', 'password', 'rules'], 'required'],
            ['fio', 'match', 'pattern' => '/^[а-яёa-z\s\-]+$/ui', 'message' => 'ФИО должно содержать только кириллические или латинские буквы, пробелы и дефисы.'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот Email уже зарегистрирован.'],
            ['gender', 'required', 'message' => 'Пожалуйста, выберите пол.'],
            ['gender', 'string', 'max' => 255],
            ['phone', 'match', 'pattern' => '/^\+7 \([0-9]{3}\)\-[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Телефон должен быть в формате +7 (999)-999-99-99.'],
            ['password', 'match', 'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{6,}$/', 'message' => 'Только латиница и цифры, Минимум 6 символов, одну цифру, одну строчную и одну заглавную букву'],
            ['rules', 'required', 'requiredValue' => 1, 'message' => 'Необходимо согласиться с правилами регистации']
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО',
            'email' => 'Email',
            'gender' => 'Пол',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'rules' => 'Согласие с правилами регистрации'
        ];
    }


    public function register(): bool|object
    {
        if ($this->validate()) {
            $user = new User();
            $user->load($this->attributes, '');
            $user->password = Yii::$app->security->generatePasswordHash($this->password);
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->role_id = Role::getRoleId('user');

            if(! $user->save(false)) {
                VarDumper::dump($user->errors, 10, true); die;
            }

        }
        return $user ?? false;
    }
}

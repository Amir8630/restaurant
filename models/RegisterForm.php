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
            [['fio', 'email', 'gender', 'phone', 'password', 'rules'], 'required'],
            ['fio', 'match', 'pattern' => '/^[а-яё\s]+$/ui', 'message' => 'Только кирилица и пробелы'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class],
            ['phone', 'match', 'pattern' => '/^\+7 \([0-9]{3}\)\-[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Только в формате +7 (999)-999-99-99'],
            ['password', 'match', 'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{6,}$/', 'message' => 'Минимум 6 символов из которых 1 число 1 буква маленького регистра и 1 большого регистра'],
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

            if(! $user->save()) {
                VarDumper::dump($user->errors, 10, true); die;
            }

        }
        return $user ?? false;
    }
}

<?php

namespace app\commands;

use app\models\Booking;
use app\models\BookingTable;
use app\models\Status;
use yii\console\Controller;
use yii\db\Expression;
use app\models\User;
use Yii;
use yii\console\ExitCode;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class MailController extends Controller
{
    public function actionSend()
    {
        echo "Рассылка завершена.\n";
        return ExitCode::OK;
    }

    public function actionSendReminders()
    {
        // var_dump(pathinfo($_SERVER['PWD'])); die;

        Yii::$app->urlManager->baseUrl = '';
        Yii::$app->urlManager->scriptUrl = '';
        Yii::$app->urlManager->hostInfo = '';

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $reservations = Booking::findAll(['booking_date' => $tomorrow, 'status_id' => Status::getStatusId('Забронировано')]);

        foreach ($reservations as $reservation) {

            $tabels = BookingTable::findAll(['booking_id' => $reservation]);
            $tabels = implode(',', array_map(fn($t) => $t->table_id, $tabels));

            // для сервера
            $restaurant_link = 'http://'
            . pathinfo($_SERVER['PWD'])['filename'] 
            . '.wsr.ru/account/booking/mail-view?token=' . $reservation->token;

            // для localhost
            // $restaurant_link = 'http://localhost/account/booking/mail-view?token=' . $reservation->token; 

            Yii::$app->mailer->htmlLayout = '@app/mail/layouts/html';
            if (Yii::$app->mailer
                ->compose('mailRemember', [
                    'fio_guest' => $reservation->fio_guest,
                    'booking_date' => $reservation->booking_date,
                    'booking_time_start' => $reservation->booking_time_start,
                    'booking_time_end' => $reservation->booking_time_end,
                    'count_guest' => $reservation->count_guest,
                    'IdTables' => $tabels,
                    'email' => $reservation->email,
                    'restaurant_link' => $restaurant_link,
                ])
                ->setFrom('restaurant.project@mail.ru')
                ->setTo($reservation->email)
                ->setSubject('Напоминание о бронировании')
                ->send()
            ) {
                // Yii::$app->session->setFlash('success', 'Вы успешно отправили письмо');
            } else {
                echo "Рассылка не завершена.\n";
                return ExitCode::OK;

                // Yii::$app->session->setFlash('warning', 'Ошибка!');
            }
        }
        echo "Рассылка завершена.\n";
        return ExitCode::OK;
    }
}

// base64_decode($str);
// base64_encode($str);

// Генерация токена
// $secretKey = 'your_secret_key'; // Задай секретный ключ
// $id = $reservation->id;
// $hash = hash_hmac('sha256', $id, $secretKey); // Хешируем ID с ключом
// $token = base64_encode($id . ':' . $hash);

// // Формируем ссылку с токеном
// $restaurant_link = 'http://' . pathinfo($_SERVER['PWD'])['filename'] . 
//     '.wsr.ru/account/booking/view?token=' . urlencode($token);

// public function actionView($token)
// {
//     $secretKey = 'your_secret_key'; // Должен совпадать с тем, что в MailController

//     // Декодируем токен
//     $decoded = base64_decode($token);
//     if (!$decoded) {
//         throw new \yii\web\ForbiddenHttpException('Неверный токен');
//     }

//     // Разбираем ID и хеш
//     [$id, $hash] = explode(':', $decoded, 2);

//     // Проверяем хеш
//     if (hash_hmac('sha256', $id, $secretKey) !== $hash) {
//         throw new \yii\web\ForbiddenHttpException('Ошибка аутентификации');
//     }

//     // Ищем бронь по ID
//     $booking = Booking::findOne($id);
//     if (!$booking) {
//         throw new \yii\web\NotFoundHttpException('Бронь не найдена');
//     }

//     // Показываем страницу с бронью
//     return $this->render('view', ['model' => $booking]);
// }
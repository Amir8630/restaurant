<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Отмена бронирования</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            text-align: center;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #888;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Бронь успешно отменена</h2>
        <p>Уважаемый(ая) <?=$fio_guest?>,</p>
        <p>Мы подтверждаем, что ваша бронь на столик(и) №<?=$IdTables?> успешно отменена.</p>
        <ul>
            <li><strong>Дата бронирования:</strong> <?=$booking_date?></li>
            <li><strong>Время прибытия:</strong> <?=$booking_time_start?></li>
            <li><strong>Время отбытия:</strong> <?=$booking_time_end?></li>
            <li><strong>Количество гостей:</strong> <?=$count_guest?></li>
        </ul>
        <a href="<?=$restaurant_link?>" class="btn">Вернуться на сайт</a>
        <div class="footer">
            <p>С уважением, команда ресторана</p>
        </div>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поиск по сведениям о гражданине</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Ваш header -->
    <header>
        <!-- Содержимое хедера -->
    </header>

    <!-- Область поиска по сведениям о гражданине -->
    <div class="search-area">
        <h3>Область поиска по сведениям о гражданине</h3>

        <form action="search_results.php" method="get">
            <h4>По общим сведениям</h4>
            <label>Фамилия:</label>
            <input type="text" name="last_name">

            <label>Имя:</label>
            <input type="text" name="first_name">

            <label>Отчество:</label>
            <input type="text" name="middle_name">

            <label>Дата рождения:</label>
            <input type="date" name="birth_date">

            <label>Место рождения:</label>
            <input type="text" name="birth_place">

            <label>Пол:</label>
            <select name="gender">
                <option value="">Не указано</option>
                <option value="Мужской">Мужской</option>
                <option value="Женский">Женский</option>
            </select>

            <label>Серия и номер паспорта:</label>
            <input type="text" name="passport_number">

            <label>Номер СНИЛС:</label>
            <input type="text" name="snils_number">

            <label>Номер полиса ОМС:</label>
            <input type="text" name="oms_number">

            <h4>По адресу места жительства</h4>
            <label>Субъект Российской Федерации:</label>
            <input type="text" name="region">

            <label>Муниципальный район:</label>
            <input type="text" name="district">

            <label>Населенный пункт:</label>
            <input type="text" name="locality">

            <label>Улица:</label>
            <input type="text" name="street">

            <label>Дом:</label>
            <input type="text" name="house">

            <label>Строение:</label>
            <input type="text" name="building">

            <label>Корпус:</label>
            <input type="text" name="block">

            <label>Квартира:</label>
            <input type="text" name="apartment">

            <!-- Кнопки -->
            <div class="buttons">
                <button type="reset">Сбросить результат поиска</button>
                <button type="submit">Найти</button>
            </div>
        </form>
    </div>

    <!-- Ваш footer -->
    <footer>
        <!-- Содержимое футера -->
    </footer>
</body>
</html>

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
    <title>Анкета-опросник</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Подключение jQuery для AJAX-поиска -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Скрипт для печати -->
    <script>
        function printForm() {
            // Печатаем
            window.print();
        }

        // Функция для открытия модального окна поиска
        function openSearchModal() {
            document.getElementById('searchModal').style.display = 'block';
        }

        // Функция для закрытия модального окна поиска
        function closeSearchModal() {
            document.getElementById('searchModal').style.display = 'none';
        }

        // Закрытие модального окна при клике вне его области
        window.onclick = function(event) {
            var modal = document.getElementById('searchModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    <style>
        /* Стили для модального окна */
        .modal {
            display: none; /* Скрыто по умолчанию */
            position: fixed; /* Оставаться на месте */
            z-index: 1; /* На вершине */
            left: 0;
            top: 0;
            width: 100%; /* Полная ширина */
            height: 100%; /* Полная высота */
            overflow: auto; /* Включить прокрутку, если нужно */
            background-color: rgba(0,0,0,0.4); /* Черный с прозрачностью */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* Центрирование по вертикали и горизонтали */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Ширина модального окна */
            max-width: 800px; /* Максимальная ширина */
        }
        /* Кнопка закрытия модального окна */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        /* Стили для формы поиска внутри модального окна */
        .search-form {
            display: flex;
            flex-direction: column;
            align-items: center; /* Центрирование по горизонтали */
        }
        .search-form label {
            width: 100%;
            max-width: 600px;
            margin-top: 10px;
        }
        .search-form input,
        .search-form select {
            width: 100%;
            max-width: 600px;
            padding: 8px;
            margin-bottom: 10px;
        }
        .search-form .buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .search-form .buttons button {
            margin: 0 10px;
        }
        /* Стили для области поиска */
        .search-area {
            text-align: center; /* Центрирование содержимого */
        }
        /* Стили для формы анкеты */
        .form-container {
            max-width: 800px;
            margin: 0 auto; /* Центрирование формы анкеты */
            padding: 20px;
        }
        .form-container label {
            display: block;
            margin-top: 10px;
        }
        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="file"],
        .form-container select {
            width: 100%;
            padding: 8px;
        }
        .form-container button {
            margin-top: 20px;
        }
        /* Стили для кнопок */
        .buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .buttons button {
            margin: 0 10px;
        }
        /* Скрываем элементы с классом no-print при печати */
        @media print {
            .no-print {
                display: none;
            }
            /* Стили для печатной версии */
            input, select, textarea {
                border: none;
                background-color: transparent;
                /* Добавляем нижнюю границу для имитации строки для заполнения */
                border-bottom: 1px solid black;
                width: 100%;
                height: 20px;
                margin-bottom: 10px;
            }
            /* Скрываем плейсхолдеры и значения в полях */
            input::placeholder {
                color: transparent;
            }
            input::-ms-input-placeholder {
                color: transparent;
            }
            input::-webkit-input-placeholder {
                color: transparent;
            }
            input::-moz-placeholder {
                color: transparent;
            }
            input:-moz-placeholder {
                color: transparent;
            }
            /* Скрываем значения в полях ввода */
            input {
                color: transparent;
            }
            /* Стили для радиокнопок и чекбоксов */
            input[type="radio"], input[type="checkbox"] {
                visibility: hidden;
            }
            label {
                position: relative;
            }
            input[type="radio"] + label::before, input[type="checkbox"] + label::before {
                content: '';
                display: inline-block;
                width: 15px;
                height: 15px;
                border: 1px solid #000;
                margin-right: 5px;
                vertical-align: middle;
            }
        }
    </style>
</head>
<body>
    <header class="no-print">
        <div class="header-content">
            <div class="header-left">
                <a href="index.php">
                    <img src="images/logo.png" alt="Логотип" class="logo small-logo">
                </a>
                <div class="dropdown">
                    <button class="dropbtn">Меню ▼</button>
                    <div class="dropdown-content">
                        <!-- Добавлено условие для отображения пункта "Управление пользователями" -->
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="user_management.php">Управление пользователями</a>
                        <?php endif; ?>
                        <a href="form.php">Анкета-опросник</a>
                        <a href="decisions.php">Реестр решений</a>
                        <a href="journal.php">Журнал учета</a>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <a href="user_data.php" class="user-email"><?php echo htmlspecialchars($_SESSION['email']); ?></a>
                <a href="php/logout.php" class="login-link">Выход</a>
            </div>
        </div>
    </header>

    <!-- Область с фразами и логотипом -->
    <div class="search-area no-print">
        <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <!-- Логотип компании -->
            <div style="margin: 20px 0;">
                <img src="images/company.png" alt="Логотип компании" style="width: 200px; height: auto;">
            </div>

            <!-- Фразы под логотипом -->
            <div>
                <p style="font-size: 16px; font-weight: bold;">Система долговременного ухода</p>
                <p>Индивидуальная программа предоставления социальных услуг</p>
            </div>

            <!-- Заголовок области поиска -->
            <div style="margin-top: 20px;">
                <h3>Область поиска по сведениям о гражданине</h3>
            </div>

            <!-- Кнопка "Поиск", открывающая модальное окно -->
            <div style="margin-top: 20px;">
                <button onclick="openSearchModal()" style="padding: 8px 16px; background-color: #3B873E; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Поиск</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно с формой поиска -->
    <div id="searchModal" class="modal no-print">
        <div class="modal-content">
            <span class="close" onclick="closeSearchModal()">&times;</span>
            <h2 style="text-align: center;">Поиск по сведениям о гражданине</h2>
            <form action="search_results.php" method="get" class="search-form">
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
                    <button type="reset" style="padding: 8px 16px; background-color: #ccc; color: #000; border: none; border-radius: 4px; cursor: pointer;">Сбросить</button>
                    <button type="submit" style="padding: 8px 16px; background-color: #3B873E; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Найти</button>
                </div>
            </form>
        </div>
    </div>

    <div class="form-container">
        <h2>Анкета-опросник для определения индивидуальной потребности гражданина в социальном обслуживании</h2>
        <form action="php/save_form.php" method="post" enctype="multipart/form-data">
            <!-- БЛОК А -->
            <h4>1. Сведения о гражданине</h4>
            <h5>1.1. Общие сведения</h5>

            <label>Фамилия:</label>
            <input type="text" name="last_name" required>

            <label>Имя:</label>
            <input type="text" name="first_name" required>

            <label>Отчество:</label>
            <input type="text" name="middle_name">

            <label>Дата рождения:</label>
            <input type="date" name="birth_date" required>

            <label>Место рождения:</label>
            <input type="text" name="birth_place">

            <label>Пол:</label>
            <div style="display: flex; justify-content: flex-start; align-items: center;">
                <input type="radio" name="gender" value="Мужской" required id="gender_male">
                <label for="gender_male" style="margin-right: 20px;">Мужской</label>
                <input type="radio" name="gender" value="Женский" required id="gender_female">
                <label for="gender_female">Женский</label>
            </div>

            <label>Серия и номер паспорта:</label>
            <input type="text" name="passport_number">

            <label>Номер СНИЛС:</label>
            <input type="text" name="snils_number">

            <label>Номер полиса ОМС:</label>
            <input type="text" name="oms_number">

            <!-- Добавьте остальные поля анкеты в соответствии с техническим заданием -->

            <!-- Раздел "Загрузка файлов" и кнопки, которые не должны печататься -->
            <div class="no-print">
                <h4>Загрузка файлов</h4>
                <label>Загрузите документы, на основании которых заполнялась анкета:</label>
                <input type="file" name="documents[]" multiple>

                <div class="buttons">
                    <button type="submit" style="padding: 10px; background-color: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Сохранить</button>
                    <!-- Кнопка печати -->
                    <button type="button" onclick="printForm()" style="margin-left: 10px; padding: 10px; background-color: #007BFF; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Печать</button>
                </div>
            </div>
        </form>
    </div>

    <footer class="no-print">
        <div class="footer-content">
            © 2024 Система долговременного ухода
        </div>
    </footer>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'php/connect.php';

// Получение данных из формы поиска
$params = [];
$sql_conditions = [];
$sql = "SELECT * FROM citizens WHERE 1=1";

// Список полей для поиска
$fields = [
    'last_name',
    'first_name',
    'middle_name',
    'birth_date',
    'birth_place',
    'gender',
    'passport_number',
    'snils_number',
    'oms_number',
    'region',
    'district',
    'locality',
    'street',
    'house',
    'building',
    'block',
    'apartment'
];

// Построение условий запроса
foreach ($fields as $field) {
    if (!empty($_GET[$field])) {
        $sql_conditions[] = "$field LIKE ?";
        $params[] = '%' . $_GET[$field] . '%';
    }
}

// Объединение условий в запрос
if ($sql_conditions) {
    $sql .= ' AND ' . implode(' AND ', $sql_conditions);
}

// Подготовка и выполнение запроса
$stmt = $conn->prepare($sql);

// Связывание параметров
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты поиска</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Скрипт для печати -->
    <script>
        function printResults() {
            window.print();
        }

        // Функции для открытия и закрытия модального окна
        function openSearchModal() {
            document.getElementById('searchModal').style.display = 'block';
        }

        function closeSearchModal() {
            document.getElementById('searchModal').style.display = 'none';
        }

        // Закрытие модального окна при клике вне его
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
            overflow: auto; /* Включить прокрутку при необходимости */
            background-color: rgba(0, 0, 0, 0.5); /* Черный с прозрачностью */
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px; /* Ограничение по ширине */
        }

        /* Центрирование формы внутри модального окна */
        .search-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .search-form label {
            width: 100%;
            max-width: 600px;
            margin-top: 10px;
        }

        .search-form input, .search-form select {
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

        /* Стили для кнопки закрытия модального окна */
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

        /* Центрирование результатов */
        .results-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Центрирование таблицы */
        .results-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .results-container th, .results-container td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .results-container th {
            background-color: #f2f2f2;
            color: #000; /* Текст заголовков таблицы теперь черного цвета */
        }
    </style>
</head>
<body>
    <!-- Ваш header -->
    <header>
        <div class="header-content">
            <div class="header-left">
                <a href="index.php">
                    <img src="images/logo.png" alt="Логотип" class="logo small-logo">
                </a>
                <div class="dropdown">
                    <button class="dropbtn">Меню ▼</button>
                    <div class="dropdown-content">
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
    <div class="search-area">
        <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <!-- Логотип компании -->
            <div style="margin: 20px 0;">
                <img src="images/company.png" alt="Логотип компании" style="width: 200px; height: auto;">
            </div>

            <!-- Фразы -->
            <div>
                <h3>Результаты поиска</h3>
                <p style="font-size: 16px; font-weight: bold;">Система долговременного ухода</p>
                <p>Индивидуальная программа предоставления социальных услуг</p>
            </div>
        </div>

        <!-- Кнопка "Новый поиск" -->
        <div style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
            <button onclick="openSearchModal()" style="padding: 8px 16px; background-color: #3B873E; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Новый поиск</button>
        </div>
    </div>

    <!-- Модальное окно с формой поиска -->
    <div id="searchModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSearchModal()">&times;</span>
            <h2 style="text-align: center;">Поиск по сведениям о гражданине</h2>
            <form action="search_results.php" method="get" class="search-form">
                <!-- Поля формы поиска -->
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

    <div class="results-container">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Дата рождения</th>
                        <th>Место рождения</th>
                        <th>Пол</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['birth_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['birth_place']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td>
                                <a href="view_citizen.php?id=<?php echo $row['id']; ?>">Просмотреть</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Кнопка печати результатов -->
            <button onclick="printResults()" style="margin-top: 20px; padding: 10px; background-color: #007BFF; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Печать</button>
        <?php else: ?>
            <p>Ничего не найдено по заданным критериям.</p>
        <?php endif; ?>
    </div>

    <!-- Ваш footer -->
    <footer>
        <div class="footer-content">
            © 2024 Система долговременного ухода
        </div>
    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'php/connect.php';

// Обработка добавления нового решения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_decision'])) {
    // Проверяем, установлен ли form_id и является ли он числом
    if (isset($_POST['form_id']) && is_numeric($_POST['form_id'])) {
        $form_id = intval($_POST['form_id']);
    } else {
        // Если form_id не передан, устанавливаем значение по умолчанию или обрабатываем ошибку
        $form_id = null; // Если поле допускает NULL
        // Или можно вывести сообщение об ошибке и прекратить выполнение
        // die("Ошибка: form_id не передан.");
    }

    $decision_date = $_POST['decision_date'];
    $citizen_name = $_POST['citizen_name'];
    $decision_type = $_POST['decision_type'];
    $decision_text = $_POST['decision_text'];

    // Подготовка запроса с учетом возможного NULL для form_id
    $stmt = $conn->prepare("INSERT INTO decisions (form_id, decision_date, citizen_name, decision_type, decision_text) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $form_id, $decision_date, $citizen_name, $decision_type, $decision_text);
    $stmt->execute();
    $stmt->close();
}

// Получение данных из формы поиска
$search_params = [];
$sql_conditions = [];
$sql = "SELECT * FROM decisions WHERE 1=1";

// Список полей для поиска
$search_fields = [
    'decision_date',
    'citizen_name',
    'decision_type'
];

// Построение условий запроса
foreach ($search_fields as $field) {
    if (!empty($_GET[$field])) {
        $sql_conditions[] = "$field LIKE ?";
        $search_params[] = '%' . $_GET[$field] . '%';
    }
}

// Объединение условий в запрос
if ($sql_conditions) {
    $sql .= ' AND ' . implode(' AND ', $sql_conditions);
}

// Подготовка и выполнение запроса
$stmt = $conn->prepare($sql);

// Связывание параметров
if ($search_params) {
    $types = str_repeat('s', count($search_params));
    $stmt->bind_param($types, ...$search_params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Реестр решений о социальном обслуживании</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Добавляем стили для модальных окон и формы -->
    <style>
        /* Стили для модальных окон и формы */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            position: relative;
        }
        .close {
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            position: absolute;
            right: 15px;
            top: 5px;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        /* Стили для таблицы */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            color: #000;
        }
        /* Кнопки */
        .button {
            padding: 8px 16px;
            background-color: #3B873E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .button-print {
            background-color: #007BFF;
            margin-top: 20px;
        }
        /* Стили для формы */
        .form-group {
            margin-bottom: 15px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-group label {
            margin-bottom: 5px;
            text-align: center;
            width: 100%;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 80%;
            padding: 8px;
            margin: 0 auto;
        }
        /* Стили для поиска */
        .search-container {
            margin-bottom: 20px;
        }
        .search-container form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
        .search-container .form-group {
            flex: 1;
            min-width: 200px;
            margin-right: 20px;
            text-align: center;
        }
        .search-container .form-group:last-child {
            margin-right: 0;
        }
        /* Печать */
        @media print {
            .no-print {
                display: none;
            }
        }
        /* Центрирование кнопки */
        .center-button {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
    </style>
    <!-- Скрипты -->
    <script>
        // Функции для модальных окон
        function showModal(data) {
            // Заполняем модальное окно данными
            document.getElementById('modal-id').textContent = data.id;
            document.getElementById('modal-date').textContent = data.decision_date;
            document.getElementById('modal-name').textContent = data.citizen_name;
            document.getElementById('modal-type').textContent = data.decision_type;
            document.getElementById('modal-text').textContent = data.decision_text;

            // Отображаем модальное окно
            document.getElementById('decisionModal').style.display = 'block';
        }

        function closeModal(modalId) {
            // Скрываем указанное модальное окно
            document.getElementById(modalId).style.display = 'none';
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            var decisionModal = document.getElementById('decisionModal');
            var addModal = document.getElementById('addDecisionModal');
            if (event.target == decisionModal) {
                decisionModal.style.display = 'none';
            }
            if (event.target == addModal) {
                addModal.style.display = 'none';
            }
        }

        // Открытие модального окна для добавления решения
        function openAddModal() {
            document.getElementById('addDecisionModal').style.display = 'block';
        }

        // Функция для печати решения
        function printDecision() {
            var printContents = document.getElementById('decisionModal').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload(); // Обновляем страницу после печати
        }
    </script>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="header-left">
                <a href="index.php"><img src="images/logo.png" alt="Логотип" class="logo"></a>
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
    <div class="search-area">
        <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <!-- Логотип компании -->
            <div style="margin: 20px 0;">
                <img src="images/company.png" alt="Логотип компании" style="width: 200px; height: auto;">
            </div>

            <!-- Фразы под логотипом -->
            <div>
                <p style="font-size: 16px; font-weight: bold;">Система долговременного ухода</p>
                <h2>Реестр решений о социальном обслуживании</h2>
            </div>
        </div>
    </div>

    <!-- Кнопка для добавления нового решения -->
    <div class="center-button">
        <button class="button no-print" onclick="openAddModal()">Добавить решение</button>
    </div>

    <!-- Область поиска решений -->
    <div class="search-container">
        <form action="decisions.php" method="get">
            <div class="form-group">
                <label for="decision_date">Дата решения:</label>
                <input type="date" name="decision_date" id="decision_date" value="<?php echo isset($_GET['decision_date']) ? htmlspecialchars($_GET['decision_date']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="citizen_name">ФИО гражданина:</label>
                <input type="text" name="citizen_name" id="citizen_name" value="<?php echo isset($_GET['citizen_name']) ? htmlspecialchars($_GET['citizen_name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="decision_type">Тип решения:</label>
                <select name="decision_type" id="decision_type">
                    <option value="">Все</option>
                    <option value="Признание" <?php if(isset($_GET['decision_type']) && $_GET['decision_type'] == 'Признание') echo 'selected'; ?>>Признание</option>
                    <option value="Отказ" <?php if(isset($_GET['decision_type']) && $_GET['decision_type'] == 'Отказ') echo 'selected'; ?>>Отказ</option>
                    <option value="Продление" <?php if(isset($_GET['decision_type']) && $_GET['decision_type'] == 'Продление') echo 'selected'; ?>>Продление</option>
                </select>
            </div>
            <div style="width: 100%; text-align: center; margin-top: 10px;">
                <button type="submit" class="button">Поиск</button>
            </div>
        </form>
    </div>

    <div class="container">
        <!-- Таблица решений -->
        <table>
            <tr>
                <th>№</th>
                <th>Дата решения</th>
                <th>ФИО гражданина</th>
                <th>Тип решения</th>
                <th>Действия</th>
            </tr>
            <!-- Динамический вывод данных из базы -->
            <?php
            while($row = $result->fetch_assoc()) {
                // Подготавливаем данные для передачи в JavaScript
                $decisionData = array(
                    'id' => $row['id'],
                    'decision_date' => $row['decision_date'],
                    'citizen_name' => $row['citizen_name'],
                    'decision_type' => $row['decision_type'],
                    'decision_text' => $row['decision_text']
                );
                $decisionDataJson = htmlspecialchars(json_encode($decisionData), ENT_QUOTES, 'UTF-8');

                echo "<tr>";
                echo "<td>".$row['id']."</td>";
                echo "<td>".$row['decision_date']."</td>";
                echo "<td>".$row['citizen_name']."</td>";
                echo "<td>".$row['decision_type']."</td>";
                echo "<td><a href='#' onclick='showModal(".$decisionDataJson.")'>Просмотреть</a></td>";
                echo "</tr>";
            }
            $stmt->close();
            // НЕ закрываем $conn здесь
            // $conn->close();
            ?>
        </table>
    </div>

    <!-- Модальное окно для просмотра решения -->
    <div id="decisionModal" class="modal">
        <div class="modal-content">
            <span class="close no-print" onclick="closeModal('decisionModal')">&times;</span>
            <h2>Детали решения</h2>
            <p><strong>Номер:</strong> <span id="modal-id"></span></p>
            <p><strong>Дата решения:</strong> <span id="modal-date"></span></p>
            <p><strong>ФИО гражданина:</strong> <span id="modal-name"></span></p>
            <p><strong>Тип решения:</strong> <span id="modal-type"></span></p>
            <p><strong>Текст решения:</strong></p>
            <p id="modal-text"></p>
            <!-- Кнопка печати -->
            <button class="button button-print no-print" onclick="printDecision()">Печать</button>
        </div>
    </div>

    <!-- Модальное окно для добавления нового решения -->
    <div id="addDecisionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addDecisionModal')">&times;</span>
            <h2 style="text-align: center;">Добавить новое решение</h2>
            <form action="decisions.php" method="post">
                <input type="hidden" name="add_decision" value="1">
                <!-- Добавляем поле form_id -->
                <div class="form-group">
                    <label for="new_form_id">Связать с формой:</label>
                    <select name="form_id" id="new_form_id" required>
                        <option value="">Выберите форму</option>
                        <?php
                        // Получаем список форм из базы данных
                        $forms_result = $conn->query("SELECT id, last_name, first_name, middle_name FROM forms");
                        if ($forms_result && $forms_result->num_rows > 0) {
                            while ($form = $forms_result->fetch_assoc()) {
                                // Формируем ФИО гражданина
                                $citizen_name = $form['last_name'] . ' ' . $form['first_name'] . ' ' . $form['middle_name'];
                                echo "<option value='{$form['id']}'>ID {$form['id']}: {$citizen_name}</option>";
                            }
                        } else {
                            echo "<option value=''>Нет доступных форм</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_decision_date">Дата решения:</label>
                    <input type="date" name="decision_date" id="new_decision_date" required>
                </div>
                <div class="form-group">
                    <label for="new_citizen_name">ФИО гражданина:</label>
                    <input type="text" name="citizen_name" id="new_citizen_name" required>
                </div>
                <div class="form-group">
                    <label for="new_decision_type">Тип решения:</label>
                    <select name="decision_type" id="new_decision_type" required>
                        <option value="Признание">Признание</option>
                        <option value="Отказ">Отказ</option>
                        <option value="Продление">Продление</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_decision_text">Текст решения:</label>
                    <textarea name="decision_text" id="new_decision_text" rows="5" required></textarea>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" class="button">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="no-print">
        <div class="footer-content">
            © 2024 Система долговременного ухода
        </div>
    </footer>
</body>
</html>

<?php
// Теперь закрываем соединение с базой данных после завершения работы
$conn->close();
?>

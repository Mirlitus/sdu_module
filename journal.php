<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'php/connect.php';

// Обработка добавления или редактирования записи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, является ли пользователь администратором
    if ($_SESSION['role'] !== 'admin') {
        echo "<script>alert('У вас нет прав для выполнения данной операции.'); window.location.href='journal.php';</script>";
        exit();
    }

    if (isset($_POST['add_journal_entry'])) {
        // Добавление новой записи в журнал
        $application_date = $_POST['application_date'];
        $application_type = $_POST['application_type'];
        $citizen_name = $_POST['citizen_name'];
        $address = $_POST['address'];
        $category = $_POST['category'];
        $decision_date_number = $_POST['decision_date_number'];
        $ippsu_date_number = $_POST['ippsu_date_number'];
        $service_form = $_POST['service_form'];
        $care_level = $_POST['care_level'];

        $stmt = $conn->prepare("INSERT INTO journal (application_date, application_type, citizen_name, address, category, decision_date_number, ippsu_date_number, service_form, care_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssss', $application_date, $application_type, $citizen_name, $address, $category, $decision_date_number, $ippsu_date_number, $service_form, $care_level);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_journal_entry'])) {
        // Редактирование существующей записи
        $id = $_POST['id'];
        $application_date = $_POST['application_date'];
        $application_type = $_POST['application_type'];
        $citizen_name = $_POST['citizen_name'];
        $address = $_POST['address'];
        $category = $_POST['category'];
        $decision_date_number = $_POST['decision_date_number'];
        $ippsu_date_number = $_POST['ippsu_date_number'];
        $service_form = $_POST['service_form'];
        $care_level = $_POST['care_level'];

        $stmt = $conn->prepare("UPDATE journal SET application_date=?, application_type=?, citizen_name=?, address=?, category=?, decision_date_number=?, ippsu_date_number=?, service_form=?, care_level=? WHERE id=?");
        $stmt->bind_param('sssssssssi', $application_date, $application_type, $citizen_name, $address, $category, $decision_date_number, $ippsu_date_number, $service_form, $care_level, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Обработка удаления записи
if (isset($_GET['delete_id'])) {
    // Проверяем, является ли пользователь администратором
    if ($_SESSION['role'] !== 'admin') {
        echo "<script>alert('У вас нет прав для выполнения данной операции.'); window.location.href='journal.php';</script>";
        exit();
    }

    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM journal WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

// Получение данных из формы поиска
$search_params = [];
$sql_conditions = [];
$sql = "SELECT * FROM journal WHERE 1=1";

// Список полей для поиска
$search_fields = [
    'application_date',
    'application_type',
    'citizen_name',
    'address',
    'category',
    'decision_date_number',
    'ippsu_date_number',
    'service_form',
    'care_level'
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
    <title>Журнал учета</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Добавьте ваши стили здесь */
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
            max-width: 800px;
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
        /* Стили для таблицы */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
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
        /* Центрирование кнопки */
        .center-button {
            display: flex;
            justify-content: center;
            margin: 20px 0;
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
    </style>
    <script>
        // Функции для работы с модальными окнами
        function openAddModal() {
            <?php if ($_SESSION['role'] == 'admin'): ?>
                document.getElementById('addJournalModal').style.display = 'block';
            <?php else: ?>
                alert('У вас нет прав для выполнения данной операции.');
            <?php endif; ?>
        }

        function openEditModal(data) {
            <?php if ($_SESSION['role'] == 'admin'): ?>
                // Заполняем форму редактирования данными
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-application-date').value = data.application_date;
                document.getElementById('edit-application-type').value = data.application_type;
                document.getElementById('edit-citizen-name').value = data.citizen_name;
                document.getElementById('edit-address').value = data.address;
                document.getElementById('edit-category').value = data.category;
                document.getElementById('edit-decision-date-number').value = data.decision_date_number;
                document.getElementById('edit-ippsu-date-number').value = data.ippsu_date_number;
                document.getElementById('edit-service-form').value = data.service_form;
                document.getElementById('edit-care-level').value = data.care_level;

                document.getElementById('editJournalModal').style.display = 'block';
            <?php else: ?>
                alert('У вас нет прав для выполнения данной операции.');
            <?php endif; ?>
        }

        function confirmDelete(url) {
            <?php if ($_SESSION['role'] == 'admin'): ?>
                if (confirm('Вы уверены, что хотите удалить эту запись?')) {
                    window.location.href = url;
                }
            <?php else: ?>
                alert('У вас нет прав для выполнения данной операции.');
            <?php endif; ?>
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Закрытие модальных окон при клике вне их
        window.onclick = function(event) {
            var addModal = document.getElementById('addJournalModal');
            var editModal = document.getElementById('editJournalModal');
            if (event.target == addModal) {
                addModal.style.display = 'none';
            }
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <header>
        <!-- Шапка сайта -->
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

    <div class="container">
        <!-- Область поиска -->
        <div class="search-container">
            <form action="journal.php" method="get">
                <!-- Поля для поиска -->
                <div class="form-group">
                    <label for="application_date">Дата заявления:</label>
                    <input type="date" name="application_date" id="application_date" value="<?php echo isset($_GET['application_date']) ? htmlspecialchars($_GET['application_date']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="citizen_name">ФИО:</label>
                    <input type="text" name="citizen_name" id="citizen_name" value="<?php echo isset($_GET['citizen_name']) ? htmlspecialchars($_GET['citizen_name']) : ''; ?>">
                </div>
                <!-- Добавьте остальные поля по аналогии -->
                <button type="submit" class="button">Поиск</button>
            </form>
        </div>

        <main class="content">
            <h2 style="text-align: center;">Журнал регистрации заявлений и учета решений о социальном обслуживании</h2>
            <!-- Кнопка для добавления новой записи -->
            <div class="center-button">
                <button class="button" onclick="openAddModal()">Добавить запись</button>
            </div>
            <table>
                <tr>
                    <th>№ п/п</th>
                    <th>Дата поступления заявления</th>
                    <th>Вид заявления</th>
                    <th>Ф.И.О.</th>
                    <th>Адрес проживания</th>
                    <th>Категория</th>
                    <th>Дата и номер решения</th>
                    <th>Дата, номер и срок действия ИППСУ</th>
                    <th>Форма социального обслуживания</th>
                    <th>Уровень нуждаемости в уходе</th>
                    <th>Действия</th>
                </tr>
                <!-- Динамический вывод данных из базы -->
                <?php
                while($row = $result->fetch_assoc()) {
                    $journalData = array(
                        'id' => $row['id'],
                        'application_date' => $row['application_date'],
                        'application_type' => $row['application_type'],
                        'citizen_name' => $row['citizen_name'],
                        'address' => $row['address'],
                        'category' => $row['category'],
                        'decision_date_number' => $row['decision_date_number'],
                        'ippsu_date_number' => $row['ippsu_date_number'],
                        'service_form' => $row['service_form'],
                        'care_level' => $row['care_level']
                    );
                    $journalDataJson = htmlspecialchars(json_encode($journalData), ENT_QUOTES, 'UTF-8');

                    echo "<tr>";
                    echo "<td>".$row['id']."</td>";
                    echo "<td>".$row['application_date']."</td>";
                    echo "<td>".$row['application_type']."</td>";
                    echo "<td>".$row['citizen_name']."</td>";
                    echo "<td>".$row['address']."</td>";
                    echo "<td>".$row['category']."</td>";
                    echo "<td>".$row['decision_date_number']."</td>";
                    echo "<td>".$row['ippsu_date_number']."</td>";
                    echo "<td>".$row['service_form']."</td>";
                    echo "<td>".$row['care_level']."</td>";
                    echo "<td>";
                    echo "<a href='#' onclick='openEditModal(".$journalDataJson.")'>Редактировать</a> | ";
                    echo "<a href='#' onclick=\"confirmDelete('journal.php?delete_id=".$row['id']."')\">Удалить</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                $stmt->close();
                $conn->close();
                ?>
            </table>
        </main>
    </div>

    <!-- Модальное окно для добавления записи -->
    <div id="addJournalModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addJournalModal')">&times;</span>
            <h2>Добавить новую запись</h2>
            <form action="journal.php" method="post">
                <input type="hidden" name="add_journal_entry" value="1">
                <!-- Поля формы -->
                <div class="form-group">
                    <label for="add-application-date">Дата поступления заявления:</label>
                    <input type="date" name="application_date" id="add-application-date" required>
                </div>
                <div class="form-group">
                    <label for="add-application-type">Вид заявления:</label>
                    <input type="text" name="application_type" id="add-application-type" required>
                </div>
                <div class="form-group">
                    <label for="add-citizen-name">Ф.И.О.:</label>
                    <input type="text" name="citizen_name" id="add-citizen-name" required>
                </div>
                <div class="form-group">
                    <label for="add-address">Адрес проживания:</label>
                    <input type="text" name="address" id="add-address" required>
                </div>
                <div class="form-group">
                    <label for="add-category">Категория:</label>
                    <input type="text" name="category" id="add-category" required>
                </div>
                <div class="form-group">
                    <label for="add-decision-date-number">Дата и номер решения:</label>
                    <input type="text" name="decision_date_number" id="add-decision-date-number">
                </div>
                <div class="form-group">
                    <label for="add-ippsu-date-number">Дата, номер и срок действия ИППСУ:</label>
                    <input type="text" name="ippsu_date_number" id="add-ippsu-date-number">
                </div>
                <div class="form-group">
                    <label for="add-service-form">Форма социального обслуживания:</label>
                    <input type="text" name="service_form" id="add-service-form">
                </div>
                <div class="form-group">
                    <label for="add-care-level">Уровень нуждаемости в уходе:</label>
                    <input type="text" name="care_level" id="add-care-level">
                </div>
                <button type="submit" class="button">Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Модальное окно для редактирования записи -->
    <div id="editJournalModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editJournalModal')">&times;</span>
            <h2>Редактировать запись</h2>
            <form action="journal.php" method="post">
                <input type="hidden" name="edit_journal_entry" value="1">
                <input type="hidden" name="id" id="edit-id">
                <!-- Поля формы -->
                <div class="form-group">
                    <label for="edit-application-date">Дата поступления заявления:</label>
                    <input type="date" name="application_date" id="edit-application-date" required>
                </div>
                <div class="form-group">
                    <label for="edit-application-type">Вид заявления:</label>
                    <input type="text" name="application_type" id="edit-application-type" required>
                </div>
                <div class="form-group">
                    <label for="edit-citizen-name">Ф.И.О.:</label>
                    <input type="text" name="citizen_name" id="edit-citizen-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-address">Адрес проживания:</label>
                    <input type="text" name="address" id="edit-address" required>
                </div>
                <div class="form-group">
                    <label for="edit-category">Категория:</label>
                    <input type="text" name="category" id="edit-category" required>
                </div>
                <div class="form-group">
                    <label for="edit-decision-date-number">Дата и номер решения:</label>
                    <input type="text" name="decision_date_number" id="edit-decision-date-number">
                </div>
                <div class="form-group">
                    <label for="edit-ippsu-date-number">Дата, номер и срок действия ИППСУ:</label>
                    <input type="text" name="ippsu_date_number" id="edit-ippsu-date-number">
                </div>
                <div class="form-group">
                    <label for="edit-service-form">Форма социального обслуживания:</label>
                    <input type="text" name="service_form" id="edit-service-form">
                </div>
                <div class="form-group">
                    <label for="edit-care-level">Уровень нуждаемости в уходе:</label>
                    <input type="text" name="care_level" id="edit-care-level">
                </div>
                <button type="submit" class="button">Сохранить</button>
            </form>
        </div>
    </div>

    <footer>
        <!-- Футер -->
        <div class="footer-content">
            © 2024 Система долговременного ухода
        </div>
    </footer>
</body>
</html>

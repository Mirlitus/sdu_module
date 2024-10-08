<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'php/connect.php';

// Получение ID гражданина
$citizen_id = intval($_GET['id']);

// Получение данных из базы данных
$stmt = $conn->prepare("SELECT * FROM citizens WHERE id = ?");
$stmt->bind_param('i', $citizen_id);
$stmt->execute();
$result = $stmt->get_result();
$citizen = $result->fetch_assoc();

if (!$citizen) {
    echo "Гражданин не найден.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Информация о гражданине</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Скрипт для печати -->
    <script>
        function printPage() {
            // Скрываем элементы, которые не должны печататься
            document.querySelector('.header-content').style.display = 'none';
            document.querySelector('.footer-content').style.display = 'none';
            // Печатаем
            window.print();
            // Возвращаем элементы после печати
            document.querySelector('.header-content').style.display = 'flex';
            document.querySelector('.footer-content').style.display = 'block';
        }
    </script>
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

    <!-- Область с информацией о гражданине -->
    <div class="citizen-info">
        <h2>Информация о гражданине</h2>
        <p><strong>Фамилия:</strong> <?php echo htmlspecialchars($citizen['last_name']); ?></p>
        <p><strong>Имя:</strong> <?php echo htmlspecialchars($citizen['first_name']); ?></p>
        <p><strong>Отчество:</strong> <?php echo htmlspecialchars($citizen['middle_name']); ?></p>
        <p><strong>Дата рождения:</strong> <?php echo htmlspecialchars($citizen['birth_date']); ?></p>
        <p><strong>Место рождения:</strong> <?php echo htmlspecialchars($citizen['birth_place']); ?></p>
        <p><strong>Пол:</strong> <?php echo htmlspecialchars($citizen['gender']); ?></p>
        <p><strong>Серия и номер паспорта:</strong> <?php echo htmlspecialchars($citizen['passport_number']); ?></p>
        <p><strong>Номер СНИЛС:</strong> <?php echo htmlspecialchars($citizen['snils_number']); ?></p>
        <p><strong>Номер полиса ОМС:</strong> <?php echo htmlspecialchars($citizen['oms_number']); ?></p>
        <!-- Добавьте остальные поля -->

        <!-- Кнопка печати -->
        <button onclick="printPage()" style="margin-top: 20px; padding: 10px; background-color: #007BFF; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Печать</button>
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

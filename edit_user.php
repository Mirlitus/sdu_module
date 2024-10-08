<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'php/connect.php';

// Проверяем, передан ли ID пользователя
if (!isset($_GET['id'])) {
    header('Location: user_management.php');
    exit();
}

$user_id = intval($_GET['id']);

// Получаем данные пользователя из базы данных
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Пользователь не найден
    header('Location: user_management.php');
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать пользователя</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Стили для сообщений об ошибках */
        .error-message {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="header-left">
                <!-- Кнопка "Домой" (логотип слева сверху) -->
                <a href="index.php"><img src="images/logo.png" alt="Логотип" class="logo"></a>
                <!-- Выпадающее меню -->
                <div class="dropdown">
                    <button class="dropbtn">Меню ▼</button>
                    <div class="dropdown-content">
                        <a href="user_management.php">Управление пользователями</a>
                        <a href="form.php">Анкета-опросник</a>
                        <a href="decisions.php">Реестр решений</a>
                        <a href="journal.php">Журнал учета</a>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <!-- E-mail как ссылка на страницу информации о пользователе -->
                <a href="user_data.php" class="user-email"><?php echo $_SESSION['email']; ?></a>
                <a href="php/logout.php" class="login-link">Выход</a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Боковое меню с изображениями -->
        <nav class="sidebar">
            <ul>
                <li>
                    <img src="images/gear-solid.png" alt="Управление пользователями" class="menu-icon">
                    <a href="user_management.php"><strong>Управление пользователями</strong></a>
                </li>
                <!-- Добавьте другие пункты меню, если необходимо -->
            </ul>
        </nav>

        <main class="content">
            <h2>Редактировать пользователя</h2>

            <?php
            if (isset($_GET['error'])) {
                $error = htmlspecialchars($_GET['error']);
                if ($error == 'email_exists') {
                    echo '<div class="error-message">Пользователь с таким E-mail уже существует.</div>';
                } else {
                    echo '<div class="error-message">Произошла ошибка при обновлении данных.</div>';
                }
            }
            ?>

            <form action="php/update_user.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">

                <label for="email">E-mail:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="password">Новый пароль (оставьте пустым, если не хотите менять):</label>
                <input type="password" name="password">

                <label for="role">Роль:</label>
                <select name="role">
                    <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>Пользователь</option>
                    <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Администратор</option>
                </select>

                <button type="submit">Сохранить изменения</button>
            </form>
        </main>
    </div>

    <footer>
        <div class="footer-content">
            © 2024 Система долгосрочного ухода
        </div>
    </footer>
</body>
</html>

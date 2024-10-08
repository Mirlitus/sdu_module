<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
include 'php/connect.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Стили для сообщений об ошибках и успехе */
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
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
    <nav class="sidebar">
        <ul>
            <li>
                <img src="images/big-test.png" alt="Анкета-опросник" class="menu-icon">
                <a href="form.php">
                    <strong>Анкета-опросник</strong>
                    <span>по определению индивидуальной потребности гражданина в социальном обслуживании, в том числе в социальных услугах по уходу.</span>
                </a>
            </li>
            <li>
                <img src="images/big-normdoc.png" alt="Реестр решений" class="menu-icon">
                <a href="decisions.php">
                    <strong>Реестр решений о социальном обслуживании</strong>
                    <ul class="submenu">
                        <li>о признании нуждающимся в социальном обслуживании;</li>
                        <li>об отказе в социальном обслуживании;</li>
                        <li>о продлении срока предоставления социальных услуг.</li>
                    </ul>
                </a>
            </li>
            <li>
                <img src="images/big-glossary.png" alt="Журнал учета" class="menu-icon">
                <a href="journal.php">
                    <strong>Журнал</strong>
                    <span>регистрации заявлений и учета решений о социальном обслуживании.</span>
                </a>
            </li>
        </ul>
    </nav>

        <main class="content">
            <h2>Управление пользователями</h2>

            <?php
            // Вывод сообщений об ошибках или успехе
            if (isset($_GET['message'])) {
                $message = htmlspecialchars($_GET['message']);
                if ($message == 'user_added') {
                    echo '<div class="message success-message">Пользователь успешно добавлен.</div>';
                } elseif ($message == 'user_deleted') {
                    echo '<div class="message success-message">Пользователь успешно удален.</div>';
                } elseif ($message == 'user_updated') {
                    echo '<div class="message success-message">Данные пользователя успешно обновлены.</div>';
                } elseif ($message == 'cannot_delete_self') {
                    echo '<div class="message error-message">Вы не можете удалить свой собственный аккаунт.</div>';
                } elseif ($message == 'user_exists') {
                    echo '<div class="message error-message">Пользователь с таким E-mail уже существует.</div>';
                } else {
                    echo '<div class="message error-message">Произошла ошибка.</div>';
                }
            }
            ?>

            <h3>Список пользователей</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>E-mail</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
                <!-- Динамический вывод данных из базы -->
                <?php
                $sql = "SELECT * FROM users";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($row['id'])."</td>";
                    echo "<td>".htmlspecialchars($row['email'])."</td>";
                    echo "<td>".htmlspecialchars($row['role'])."</td>";
                    echo "<td>
                        <a href='edit_user.php?id=".urlencode($row['id'])."'>Редактировать</a> | 
                        <a href='php/delete_user.php?id=".urlencode($row['id'])."' onclick=\"return confirm('Вы уверены, что хотите удалить этого пользователя?');\">Удалить</a>
                    </td>";
                    echo "</tr>";
                }
                $conn->close();
                ?>
            </table>

            <h3>Добавить нового пользователя</h3>
            <form action="php/register_user.php" method="post">
                <label for="email">E-mail:</label>
                <input type="email" name="email" required>

                <label for="password">Пароль:</label>
                <input type="password" name="password" required>

                <label for="role">Роль:</label>
                <select name="role">
                    <option value="user">Пользователь</option>
                    <option value="admin">Администратор</option>
                </select>

                <button type="submit">Добавить пользователя</button>
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

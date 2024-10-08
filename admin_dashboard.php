<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="header-left">
                <a href="index.php"><img src="images/logo.png" alt="Логотип" class="logo"></a>
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
            <h2 style="text-align: center; margin-bottom: 30px;">Панель администратора</h2>
            <p style="
                text-align: center;
                font-weight: bold;
                font-size: 24px;
                color: #3B873E;
                margin-bottom: 40px;
            ">
                Добро пожаловать, администратор!
            </p>
            <!-- Добавьте необходимый функционал панели администратора -->
        </main>
    </div>

    <footer>
        <div class="footer-content">
            © 2024 Система долгосрочного ухода
        </div>
    </footer>
</body>
</html>

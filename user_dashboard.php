<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет пользователя</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="index.php"><img src="images/logo.png" alt="Логотип" class="logo"></a>
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
            <div class="main-header">
                <h1>Система долгосрочного ухода</h1>
                <h2>Индивидуальная программа предоставления социальных услуг</h2>
            </div>
            <img src="images/stages.jpg" alt="Этапы системы" class="stages-image">
        </main>
    </div>

    <footer>
        <div class="footer-content">
            © 2024 Система долгосрочного ухода
        </div>
    </footer>
</body>
</html>

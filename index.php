<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Добавляем стиль для значка шестерёнок */
        .gear-icon {
            width: 24px;
            height: 24px;
            margin-left: 10px;
            vertical-align: middle;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="header-left">
                <!-- Логотип -->
                <a href="index.php">
                    <img src="images/logo.png" alt="Логотип" class="logo small-logo">
                </a>
                <?php
                // Проверяем, является ли пользователь администратором
                if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                    echo '<a href="user_management.php">
                            <img src="images/gear-solid.png" alt="Управление пользователями" class="gear-icon" style="width: 10%; height: auto;">
                          </a>';
                }
                ?>
            </div>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<a href="php/logout.php" class="login-link">Выход</a>';
            } else {
                echo '<a href="login.php" class="login-link">Вход</a>';
            }
            ?>
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
            <!-- Новый блок для логотипа компании и заголовка -->
            <div class="header-section" style="text-align: center; margin-bottom: 20px;">
                <img src="images/company.png" alt="Логотип компании" class="company-logo" style="width: 200px; height: auto; margin-bottom: 10px;">
                <h1>Система долгосрочного ухода</h1>
            </div>
            <h2>Индивидуальная программа предоставления социальных услуг</h2>
            <img src="images/stages.jpg" alt="Этапы системы долгосрочного ухода" class="stages-image">
        </main>
    </div>

    <footer>
        <div class="footer-content">
            © 2024 Система долгосрочного ухода
        </div>
    </footer>
</body>
</html>

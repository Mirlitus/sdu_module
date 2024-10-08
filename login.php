<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: user_dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Окно авторизации</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="index.php"><img src="images/logo.png" alt="Логотип" class="logo"></a>
            <a href="login.php" class="login-link">Вход</a>
        </div>
    </header>

    <div class="login-container">
        <div class="login-box">
            <h2>Вход в систему</h2>
            <a href="register.php" class="small-link">Зарегистрироваться</a>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                        if ($_GET['error'] == 'invalid_credentials') {
                            echo 'Неверный E-mail или пароль.';
                        } elseif ($_GET['error'] == 'missing_fields') {
                            echo 'Пожалуйста, заполните все поля.';
                        } else {
                            echo 'Произошла ошибка. Попробуйте снова.';
                        }
                    ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" action="php/login.php" method="post">
                <label for="email">Электронная почта или доменное имя</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Войти</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            © 2024 Система долгосрочного ухода
        </div>
    </footer>
</body>
</html>

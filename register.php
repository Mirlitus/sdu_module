<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация пользователя</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
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
                <a href="login.php" class="login-link">Вход</a>
            </div>
        </div>
    </header>

    <div class="search-area">
        <!-- Центрирование логотипа компании -->
        <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
            <img src="images/company.png" alt="Логотип компании" style="width: 200px; height: auto;">
        </div>
    </div>

    <!-- Центрирование формы регистрации -->
    <div class="register-container" style="display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 20px;">
        <div class="register-box" style="width: 400px; padding: 30px; background-color: #fff; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; margin-bottom: 20px;">Регистрация пользователя</h2>
            <form action="register.php" method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
                <label for="first_name">Имя:</label>
                <input type="text" name="first_name" id="first_name" required style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="last_name">Фамилия:</label>
                <input type="text" name="last_name" id="last_name" required style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="middle_name">Отчество:</label>
                <input type="text" name="middle_name" id="middle_name" style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="birth_date">Дата рождения:</label>
                <input type="date" name="birth_date" id="birth_date" style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="gender">Пол:</label>
                <select name="gender" id="gender" style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">Не указан</option>
                    <option value="Мужской">Мужской</option>
                    <option value="Женский">Женский</option>
                </select>

                <label for="position">Должность:</label>
                <input type="text" name="position" id="position" style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="phone">Телефон:</label>
                <input type="text" name="phone" id="phone" style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="snils_number">СНИЛС:</label>
                <input type="text" name="snils_number" id="snils_number" style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="oms_number">ОМС:</label>
                <input type="text" name="oms_number" id="oms_number" style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="password">Пароль:</label>
                <input type="password" name="password" id="password" required style="padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">

                <label for="photo">Фотография профиля:</label>
                <input type="file" name="photo" id="photo" accept="image/*" style="margin-bottom: 20px;">

                <button type="submit" name="register" style="padding: 10px; background-color: #3B873E; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Зарегистрироваться</button>
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

<?php
if (isset($_POST['register'])) {
    include 'php/connect.php';

    // Получение данных из формы и их фильтрация
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $middle_name = htmlspecialchars(trim($_POST['middle_name']));
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $position = htmlspecialchars(trim($_POST['position']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $snils_number = htmlspecialchars(trim($_POST['snils_number']));
    $oms_number = htmlspecialchars(trim($_POST['oms_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    // Валидация обязательных полей
    $errors = [];
    if (empty($first_name)) $errors[] = "Имя обязательно для заполнения.";
    if (empty($last_name)) $errors[] = "Фамилия обязательна для заполнения.";
    if (empty($email)) $errors[] = "E-mail обязателен для заполнения.";
    if (empty($password)) $errors[] = "Пароль обязателен для заполнения.";

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "'); window.history.back();</script>";
        exit();
    }

    // Хеширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Обработка загрузки фотографии
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['photo']['type'], $allowed_types)) {
            $photo_name = time() . '_' . basename($_FILES['photo']['name']);
            $photo_dir = 'uploads/photos/';
            if (!is_dir($photo_dir)) {
                mkdir($photo_dir, 0777, true);
            }
            $photo_path = $photo_dir . $photo_name;
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        } else {
            echo "<script>alert('Недопустимый формат файла для фотографии.'); window.history.back();</script>";
            exit();
        }
    }

    // Проверка, существует ли пользователь с таким email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt === false) {
        echo "<script>alert('Ошибка подготовки запроса.'); window.history.back();</script>";
        exit();
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Пользователь с таким E-mail уже существует.'); window.history.back();</script>";
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Вставка данных в базу данных с использованием подготовленного выражения
    $stmt = $conn->prepare("INSERT INTO users (email, password, role, first_name, last_name, middle_name, birth_date, gender, position, phone, snils_number, oms_number, photo) VALUES (?, ?, 'user', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo "<script>alert('Ошибка подготовки запроса.'); window.history.back();</script>";
        exit();
    }
    $stmt->bind_param('sssssssssssss',
        $email,
        $hashed_password,
        $first_name,
        $last_name,
        $middle_name,
        $birth_date,
        $gender,
        $position,
        $phone,
        $snils_number,
        $oms_number,
        $photo_path
    );

    if ($stmt->execute()) {
        echo "<script>alert('Регистрация прошла успешно. Теперь вы можете войти в систему.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Ошибка при регистрации. Попробуйте еще раз.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

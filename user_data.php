<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'php/connect.php';

// Получаем данные пользователя из базы данных
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Обработка обновления данных пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $position = $_POST['position'];
    $snils_number = $_POST['snils_number'];

    // Обработка загрузки фотографии
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Разрешенные расширения файлов
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Ограничение размера файла (например, 5 МБ)
            if ($fileSize < 5 * 1024 * 1024) {
                // Директория для загрузки файлов
                $uploadFileDir = 'uploads/';
                // Создаем директорию, если ее нет
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                // Генерируем уникальное имя файла
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                // Перемещаем загруженный файл в директорию
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $photoPath = $dest_path;
                } else {
                    $error_message = 'Ошибка при перемещении загруженного файла.';
                }
            } else {
                $error_message = 'Размер файла превышает допустимый лимит.';
            }
        } else {
            $error_message = 'Недопустимый формат файла. Разрешены только JPG, JPEG, PNG и GIF.';
        }
    } else {
        // Если файл не загружен, оставляем текущий путь к фотографии
        $photoPath = $_POST['current_photo'];
    }

    // Обновляем данные пользователя в базе данных
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, middle_name=?, birth_date=?, gender=?, phone=?, position=?, snils_number=?, photo=? WHERE id=?");
    $stmt->bind_param('sssssssssi', $first_name, $last_name, $middle_name, $birth_date, $gender, $phone, $position, $snils_number, $photoPath, $user_id);
    $stmt->execute();
    $stmt->close();

    // Перенаправляем пользователя, чтобы избежать повторной отправки формы
    header('Location: user_data.php');
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Стили для модального окна */
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
            margin: 5% auto;
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
        .profile-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .edit-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #3B873E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .profile-details p {
            margin: 5px 0;
        }
        .profile-section {
            text-align: center;
        }
        .profile-info {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: flex-start;
            margin-top: 20px;
        }
        /* Стили для формы */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
        }
        .modal-content button {
            padding: 10px 20px;
            background-color: #3B873E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .password-section {
            margin-top: 40px;
        }
        .password-section form {
            max-width: 400px;
            margin: 0 auto;
        }
        .password-section label {
            display: block;
            margin-bottom: 5px;
        }
        .password-section input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
        .password-section button {
            padding: 10px 20px;
            background-color: #3B873E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error-message {
            color: red;
            text-align: center;
        }
    </style>
    <script>
        // Функции для работы с модальным окном
        function openEditModal() {
            document.getElementById('editProfileModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            var modal = document.getElementById('editProfileModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
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
                <!-- E-mail как ссылка на страницу информации о пользователе -->
                <a href="user_data.php" class="user-email"><?php echo htmlspecialchars($_SESSION['email']); ?></a>
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
            <h2>Общая информация</h2>
            <div class="profile-info">
                <div class="profile-section">
                    <!-- Проверяем наличие фотографии -->
                    <?php
                    if (!empty($user['photo'])) {
                        echo '<img src="' . htmlspecialchars($user['photo'], ENT_QUOTES, 'UTF-8') . '" alt="Фото профиля" class="profile-photo">';
                    } else {
                        echo '<img src="images/profile-photo-placeholder.png" alt="Фото профиля" class="profile-photo">';
                    }
                    ?>
                    <p><strong><?php echo htmlspecialchars($user['last_name'] . ' ' . $user['first_name'] . ' ' . $user['middle_name']); ?></strong></p>
                    <p><?php echo isset($user['position']) ? htmlspecialchars($user['position']) : ''; ?></p>
                    <!-- Кнопка "Редактировать информацию о себе" -->
                    <button class="edit-button" onclick="openEditModal()">Редактировать информацию о себе</button>
                </div>

                <div class="profile-details">
                    <p><strong>Дата рождения:</strong> <?php echo isset($user['birth_date']) ? htmlspecialchars($user['birth_date']) : ''; ?></p>
                    <p><strong>Пол:</strong> <?php echo isset($user['gender']) ? htmlspecialchars($user['gender']) : ''; ?></p>
                    <p><strong>Телефон:</strong> <?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?></p>
                    <p><strong>СНИЛС:</strong> <?php echo isset($user['snils_number']) ? htmlspecialchars($user['snils_number']) : ''; ?></p>
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>

            <div class="password-section">
                <h3>Изменить пароль</h3>
                <form action="php/change_password.php" method="post">
                    <label for="current-password">Текущий пароль:</label>
                    <input type="password" id="current-password" name="current_password" required>

                    <label for="new-password">Новый пароль:</label>
                    <input type="password" id="new-password" name="new_password" required>

                    <label for="confirm-password">Подтвердите новый пароль:</label>
                    <input type="password" id="confirm-password" name="confirm_password" required>

                    <button type="submit">Изменить пароль</button>
                </form>
            </div>
        </main>
    </div>

    <!-- Модальное окно для редактирования профиля -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editProfileModal')">&times;</span>
            <h2>Редактировать информацию о себе</h2>
            <!-- Форма редактирования профиля -->
            <form action="user_data.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="update_profile" value="1">
                <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($user['photo'], ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Фото профиля -->
                <div style="text-align: center;">
                    <?php if (!empty($user['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($user['photo'], ENT_QUOTES, 'UTF-8'); ?>" alt="Фото профиля" class="profile-photo">
                    <?php else: ?>
                        <img src="images/profile-photo-placeholder.png" alt="Фото профиля" class="profile-photo">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="photo">Загрузить новое фото:</label>
                    <input type="file" name="photo" id="photo" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="last_name">Фамилия:</label>
                    <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="first_name">Имя:</label>
                    <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="middle_name">Отчество:</label>
                    <input type="text" name="middle_name" id="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="birth_date">Дата рождения:</label>
                    <input type="date" name="birth_date" id="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>">
                </div>

                <div class="form-group">
                    <label for="gender">Пол:</label>
                    <select name="gender" id="gender">
                        <option value="">Не указано</option>
                        <option value="Мужской" <?php if ($user['gender'] == 'Мужской') echo 'selected'; ?>>Мужской</option>
                        <option value="Женский" <?php if ($user['gender'] == 'Женский') echo 'selected'; ?>>Женский</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>

                <div class="form-group">
                    <label for="snils_number">СНИЛС:</label>
                    <input type="text" name="snils_number" id="snils_number" value="<?php echo htmlspecialchars($user['snils_number']); ?>">
                </div>

                <div class="form-group">
                    <label for="position">Должность:</label>
                    <input type="text" name="position" id="position" value="<?php echo htmlspecialchars($user['position']); ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <button type="submit">Сохранить изменения</button>
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

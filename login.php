<?php
session_start();
include_once 'config.php';

if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $sql = "SELECT * FROM user WHERE login = ? AND password = ?";
    $query = $pdo->prepare($sql);
    $userLogin = strip_tags($_POST['username']);
    $userPassword = md5(strip_tags($_POST['password']));
    $query->execute([$userLogin, $userPassword]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!empty($user)) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        header('Location: index.php');
    }
    echo 'Вы ввели несуществующие данные';
} 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Запросы SELECT, INSERT, UPDATE и DELETE</title>
    <link rel="stylesheet" href="css/style.css">  
</head>
<body>
<div>
<h2>ВОЙТИ</h2>
    <form method="POST">
        <p>Имя пользователя</p>
        <p><input type="text" name="username" placeholder="Введите login"></p>
        <p>Пароль</p>
        <p><input type="password" name="password" placeholder="Введите пароль"></p>
        <p><button type="submit" class="btn" name="login">Войти</button></p>
        <p>Не зарегистрированы?</p>
        <p><a href="register.php">Зарегистрируйтесь здесь!</a></p>
    </form>
    </div>
</body>
</html>	


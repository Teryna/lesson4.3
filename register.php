<?php
session_start();
include_once 'config.php';

if (isset($_POST['register']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $login = strip_tags($_POST['username']);
    $password = md5(strip_tags($_POST['password']));
    $sql = "SELECT login, password FROM user WHERE login = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$login]);
    $user = $query->fetch();
    if (isset($_POST['register'])) {
        if ($user !== false) {
            echo 'Такой пользователь уже есть!';
    } else {
            $sql = "INSERT INTO user (login, password) VALUES (?, ?)";
            $query = $pdo->prepare($sql);
            $userLogin = strip_tags($_POST['username']);
            $userPassword = md5(strip_tags($_POST['password']));
            $query->execute([$userLogin, $userPassword]);
            echo 'Регистрация успешна, войдите, используя свой логин и пароль.';
        } 
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация пользователя</title>
    <link rel="stylesheet" href="css/style.css">  
</head>
<body>
<div>
<h2>РЕГИСТРАЦИЯ</h2>
    <form name="registerform" id="registerform" action="register.php" method="post">
        <p>Имя пользователя</p>
        <p><input type="text" name="username" placeholder="Введите имя"></p>
        <p>Пароль</p>
        <p><input type="password" name="password" placeholder="Введите пароль"></p>
       	<p><button type="submit" class="btn" name="register">Зарегистрироваться</button></p>
        <p>Уже зарегистрированы? <a href="login.php">Войти тут!</a></p>
    </form>
</div>
</body>
</html>










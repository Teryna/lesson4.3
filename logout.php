<?php
session_start();

$old_user = $_SESSION['user']['id'];
unset($_SESSION['user']['id']);
session_destroy();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/style.css">
        <title>Выход из системы</title>
    </head>
    <body>
        <h2>Выход</h2>
        <?php
        if(!empty($old_user)) {
            echo 'Успешный выход.<br/>';
        } else {
            echo 'Вы не входили в систему.';
        }
        ?>
        <a href="index.php">На главную страницу</a>
    </body>
</html>        

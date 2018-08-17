<?php
ob_start();
session_start();

if(!isset($_SESSION['user']['id'])):
    header('location: login.php');
    exit();
else:
    include_once 'config.php';
    $username = $_SESSION['user']['login'];
?>
<div>
<h1 class="welcome">Добро пожаловать, <span><?=$username?>!</span></h1>
    <button class="btn"><a href="logout.php">Выйти из системы</a></button>
</div>
<?php endif; 

if (isset($_POST['add'])){
    if (empty($_POST['description']) && empty($_GET['action'])) {
        echo 'Вы не добавили задание';
    } else if (!empty($_POST['description']) && empty($_GET['action'])) {
        $description = strip_tags($_POST['description']);
        $date = date('Y-m-d H:i:s');
        $sql = "INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES (?, ?, ?, ?)";
        $res = $pdo->prepare($sql);
        $res->execute([$_SESSION['user']['id'], $_SESSION['user']['id'], $_POST['description'], $date]); 
        header('Location: index.php');
    } 
}

if (!empty($_GET['id']) && !empty($_GET['action'])) {
    if (($_GET['action'] == 'edit') && !empty($_POST['description'])) {
        $sql = "UPDATE task SET description = ? WHERE id = ?";
        $res = $pdo->prepare($sql);
        $res->execute([$_POST['description'], $_GET['id']]);
        header('Location: index.php');	
        } else {
				$sql = "SELECT * FROM task";       
    }
    
    if ($_GET['action'] == 'done') {
        $sql = "UPDATE task SET is_done = 1 WHERE id = ?";
        $res = $pdo->prepare($sql);
        $res->execute([$_GET['id']]);
        header('Location: index.php');        
    }
    
    if ($_GET['action'] == 'delete') {
        $sql = "DELETE FROM task WHERE id = ?";
        $res = $pdo->prepare($sql);
        $res->execute([$_GET['id']]);
        header('Location: index.php');
    }
}

$newusers = $pdo->query('SELECT login FROM user');
$users = $newusers->fetchAll(PDO::FETCH_ASSOC);
if (!empty($_POST['assign']) && !empty($_POST['select'])) {   
    $select = $_POST['select'];
    $userId = $pdo->query("SELECT id FROM user WHERE login = '$select'")->fetch()['id'];
	$sql = $pdo->prepare("UPDATE task SET assigned_user_id = ? WHERE id = ?");
	$sql->execute([$userId, $_POST['id']]);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>SELECT из нескольких таблиц</title>
    <link rel="stylesheet" href="css/style.css">  
</head>
<body>
<h1>Список дел на неделю</h1>
<div class="action">
    <form method="POST">
        <input type="text" name="description" placeholder="Веедите задание" value="<?php if (!empty($_GET['description'])) echo $_GET['description']; ?>">
        <button type="submit" class="btn" name="add">Добавить</button>
    </form>
</div>
<table>
    <thead>
        <tr>
            <th>Описание задачи</th>
            <th>Дата добавления</th>
            <th>Статус</th>
            <th>Действия</th>
            <th>Ответственный</th>
            <th>Автор</th>
            <th>Закрепить задачу<br> за пользователем</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $sql = "SELECT *, t.id as task_id, u.id as author_id, u.login as author_name FROM task t INNER JOIN user u ON u.id=t.user_id INNER JOIN user auth ON t.assigned_user_id=auth.id WHERE u.login = ?";
    $res = $pdo->prepare($sql);
    $res->execute([$username]);
    while ($result = $res->fetch()) : ?> 
        <tr>
            <td><?=$result['description']?></td>
            <td><?=$result['date_added']?></td>
            <td><?php  
                if ($result['is_done'] == 1) {
                    echo "<span class=\"done\">Выполнено</span>";
                } else  {
                    echo "<span class=\"undone\">В процессе</span>";
                }
            ?></td>
            <td>
                <a href="?id=<?=$result['task_id']?>&action=edit&description=<?=$result['description']?>">Изменить</a>
                <a href="index.php?id=<?=$result['task_id']?>&action=done">Выполнить</a>
                <a href="index.php?id=<?=$result['task_id']?>&action=delete">Удалить</a>
            </td>
            <td><?=$result['login']?></td>
            <td><?=$result['author_name']?></td>
            <td>
             <form method="post">
                <select name="select">
                    <?php 
                        foreach ($users as $user) {
                             echo '<option>' . $user['login'] . '</option>';
                        }
                     ?>
                </select>
                <p><input type="hidden" name="id" value="<?=$result['task_id']?>">
                <input type="submit" class="btn" name="assign" value="Переложить ответственность"></p>
            </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<h2>Также, посмотрите, что от Вас требуют другие люди:</h2>
<table>
    <thead>
        <tr>
            <th>Описание задачи</th>
            <th>Дата добавления</th>
            <th>Статус</th>
            <th>Действия</th>
            <th>Ответственный</th>
            <th>Автор</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $sql = "SELECT *, t.id as task_id, u.id as author_id, u.login as author_name FROM task t INNER JOIN user as u ON u.id=t.user_id INNER JOIN user as auth ON t.assigned_user_id=auth.id WHERE auth.login = ? AND u.login != ?";
    $res = $pdo->prepare($sql);
    $res->execute([$username, $username]);
    while ($result = $res->fetch()) : ?>
        <tr>
            <td><?=$result['description']?></td>
            <td><?=$result['date_added']?></td>
            <td><?php  
                if ($result['is_done'] == 1) {
                    echo "<span class=\"done\">Выполнено</span>";
                } else  {
                    echo "<span class=\"undone\">В процессе</span>";
                }
            ?></td>
            <td>
                <a href="?id=<?=$result['task_id']?>&action=edit&description=<?=$result['description']?>">Изменить</a>
                <a href="index.php?id=<?=$result['task_id']?>&action=done">Выполнить</a>
                <a href="index.php?id=<?=$result['task_id']?>&action=delete">Удалить</a>
            </td>
            <td><?=$result['login']?></td>
            <td><?=$result['author_name']?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
<?php 
//error_reporting(E_ALL);
$pdo = new PDO("mysql:host=localhost;dbname=JLoseva;charset=utf8", "JLoseva", "neto1801");
if (!$pdo)
    {
        die('Невозможно подключиться');
    }
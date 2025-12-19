<?php
$host = 'mysql'; // Docker 中用服务名；本机可用 localhost
$user = 'tpuser';
$pass = '123456'; // 以你 compose 里的为准
$db = 'trade_platform'; // 实际数据库名
$charset = 'utf8mb4';

// 使用PDO连接数据库
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('DB ERROR: ' . $e->getMessage());
}
<?php
// 引入通用函数文件
require_once 'common/common.php';

$error = '';
$success = '';

// 处理注册请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $uid = $_POST['uid'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? 1 : 0; // 管理员角色需要显式勾选

    // 验证表单数据
    if (empty($uid) || empty($name) || empty($password) || empty($confirmPassword)) {
        $error = '所有字段都必须填写';
    } elseif ($password != $confirmPassword) {
        $error = '两次输入的密码不一致';
    } else {
        // 检查用户ID是否已存在
        $sql = "SELECT COUNT(*) as count FROM userInfo16 WHERE uid = ?";
        $result = queryOne($sql, [$uid]);
        
        if ($result['count'] > 0) {
            $error = '用户ID已存在';
        } else {
            // 密码哈希
            $pw_hash = hashPassword($password);
            
            // 插入用户数据
            $sql = "INSERT INTO userInfo16 (uid, name, pw_hash, role, status) VALUES (?, ?, ?, ?, 1)";
            $params = [$uid, $name, $pw_hash, $role];
            
            if (execute($sql, $params) > 0) {
                $success = '注册成功，请登录';
                // 注册成功后跳转到登录页面
                // redirect('login.php');
            } else {
                $error = '注册失败，请稍后重试';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>二手交易平台 - 用户注册</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="uid">用户ID：</label>
                <input type="text" id="uid" name="uid" required>
            </div>
            
            <div class="form-group">
                <label for="name">用户名：</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="password">密码：</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">确认密码：</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="role">
                    <input type="checkbox" id="role" name="role" value="1"> 注册为管理员
                </label>
            </div>
            
            <div class="form-group">
                <button type="submit">注册</button>
            </div>
        </form>
        
        <div class="login-link">
            已有账号？<a href="login.php">立即登录</a>
        </div>
    </div>
</body>
</html>
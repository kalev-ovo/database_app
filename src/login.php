<?php
// 引入通用函数文件
require_once 'common/common.php';

$error = '';

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $uid = $_POST['uid'];
    $password = $_POST['password'];

    // 验证表单数据
    if (empty($uid) || empty($password)) {
        $error = '用户名和密码都必须填写';
    } else {
        // 根据用户ID查询用户信息
        $sql = "SELECT * FROM userInfo16 WHERE uid = ?";
        $user = queryOne($sql, [$uid]);
        
        if ($user) {
            // 验证密码
            if (verifyPassword($password, $user['pw_hash'])) {
                // 检查用户账号状态
                if ($user['status'] == 0) {
                    $error = '您的账号已被冻结，请联系管理员';
                } else {
                    // 登录成功，设置会话变量
                    $_SESSION['uid'] = $user['uid'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['status'] = $user['status'];
                    
                    // 根据用户角色跳转到不同页面
                    if ($user['role'] == 1) {
                        // 管理员跳转到后台管理页面
                        redirect('admin/index.php');
                    } else {
                        // 普通用户跳转到首页
                        redirect('index.php');
                    }
                }
            } else {
                $error = '密码错误';
            }
        } else {
            $error = '用户不存在';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>二手交易平台 - 用户登录</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="uid">用户ID：</label>
                <input type="text" id="uid" name="uid" required>
            </div>
            
            <div class="form-group">
                <label for="password">密码：</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit">登录</button>
            </div>
        </form>
        
        <div class="register-link">
            还没有账号？<a href="register.php">立即注册</a>
        </div>
    </div>
</body>
</html>
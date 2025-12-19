<?php
require_once '../common/common.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// 处理用户冻结/解冻
if (isset($_GET['action']) && in_array($_GET['action'], ['freeze', 'unfreeze']) && isset($_GET['uid'])) {
    $action_uid = $_GET['uid'];
    $status = ($_GET['action'] == 'freeze') ? 0 : 1;
    
    $sql = "UPDATE userInfo16 SET status = ? WHERE uid = ? AND role = 0";
    execute($sql, [$status, $action_uid]);
    
    $message = ($_GET['action'] == 'freeze') ? '用户已冻结' : '用户已解冻';
    echo '<script>alert("' . $message . '"); window.location.href="users.php";</script>';
}

// 获取用户列表
$users = query("SELECT u.*, COUNT(v.violation_id) as violation_count FROM userInfo16 u LEFT JOIN violation16 v ON u.uid = v.uid WHERE u.role = 0 GROUP BY u.uid");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>二手交易平台 - 管理员后台</h1>
            <nav>
                <a href="index.php">首页</a> | 
                <a href="products.php">物品管理</a> | 
                <a href="complaints.php">投诉处理</a> | 
                <a href="users.php">用户管理</a> | 
                <a href="../logout.php">退出登录</a>
            </nav>
        </header>
        
        <main>
            <h2>用户管理</h2>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>用户ID</th>
                        <th>用户名</th>
                        <th>违规次数</th>
                        <th>账号状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['uid']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo $user['violation_count']; ?></td>
                            <td><?php echo $user['status'] == 1 ? '正常' : '冻结'; ?></td>
                            <td>
                                <?php if ($user['status'] == 1): ?>
                                    <a href="?action=freeze&uid=<?php echo $user['uid']; ?>" class="btn btn-danger" onclick="return confirm('确定要冻结该用户账号吗?')">冻结</a>
                                <?php else: ?>
                                    <a href="?action=unfreeze&uid=<?php echo $user['uid']; ?>" class="btn btn-success" onclick="return confirm('确定要解冻该用户账号吗?')">解冻</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
        
        <?php include '../common/footer.php'; ?>
    </div>
</body>
</html>
<?php
require_once '../common/common.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// 处理添加违规记录
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_violation'])) {
    $uid = $_POST['uid'];
    $reason = $_POST['reason'];
    
    // 验证输入
    if (empty($uid) || empty($reason)) {
        $error = '请填写完整信息';
    } else {
        // 生成违规ID
        $violation_id = generateId('viol_');
        
        // 插入违规记录
        $sql = "INSERT INTO violation16 (violation_id, uid, reason) VALUES (?, ?, ?)";
        execute($sql, [$violation_id, $uid, $reason]);
        $success = '违规记录添加成功';
    }
}

// 获取违规记录
$violations = query("SELECT v.*, u.name as username FROM violation16 v JOIN userInfo16 u ON v.uid = u.uid");

// 获取所有用户
$users = query("SELECT uid, name FROM userInfo16 WHERE role = 0");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>违规记录</title>
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
            <h2>违规记录</h2>
            
            <!-- 添加违规记录表单 -->
            <div class="add-violation">
                <h3>添加违规记录</h3>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="success"><?php echo $success; ?></div>
                <?php endif; ?>
                <form method="post" action="violations.php">
                    <div class="form-group">
                        <label for="uid">用户:</label>
                        <select id="uid" name="uid" required>
                            <option value="">选择用户</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['uid']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reason">违规原因:</label>
                        <textarea id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="add_violation" class="btn btn-primary">添加违规记录</button>
                    </div>
                </form>
            </div>
            
            <!-- 违规记录列表 -->
            <div class="violations-list">
                <h3>违规记录列表</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>违规ID</th>
                            <th>用户</th>
                            <th>违规原因</th>
                            <th>违规时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($violations as $violation): ?>
                            <tr>
                                <td><?php echo $violation['violation_id']; ?></td>
                                <td><?php echo htmlspecialchars($violation['username']); ?></td>
                                <td><?php echo htmlspecialchars($violation['reason']); ?></td>
                                <td><?php echo $violation['vdate']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
        
        <?php include '../common/footer.php'; ?>
    </div>
</body>
</html>
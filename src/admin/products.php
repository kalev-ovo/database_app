<?php
require_once '../common/common.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// 处理物品下架
if (isset($_GET['action']) && $_GET['action'] == 'unpublish' && isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $sql = "UPDATE product16 SET status = 0 WHERE pid = ?";
    execute($sql, [$pid]);
    echo '<script>alert("物品已下架"); window.location.href="products.php";</script>';
}

// 处理物品上架
if (isset($_GET['action']) && $_GET['action'] == 'publish' && isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $sql = "UPDATE product16 SET status = 1 WHERE pid = ?";
    execute($sql, [$pid]);
    echo '<script>alert("物品已上架"); window.location.href="products.php";</script>';
}

// 获取物品列表
$status = isset($_GET['status']) ? $_GET['status'] : 1;
$products = query("SELECT p.*, c.category_name, u.name as username FROM product16 p JOIN category16 c ON p.category_id = c.category_id JOIN userInfo16 u ON p.uid = u.uid WHERE p.status = ?", [$status]);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>物品管理</title>
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
            <h2>物品管理</h2>
            
            <div class="filter-options">
                <a href="products.php?status=1" class="btn btn-primary">上架物品</a>
                <a href="products.php?status=0" class="btn btn-secondary">下架物品</a>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>物品ID</th>
                        <th>物品名称</th>
                        <th>类别</th>
                        <th>发布者</th>
                        <th>购买年份</th>
                        <th>新旧程度</th>
                        <th>价格</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['pid']; ?></td>
                            <td><?php echo htmlspecialchars($product['pname']); ?></td>
                            <td><?php echo $product['category_name']; ?></td>
                            <td><?php echo htmlspecialchars($product['username']); ?></td>
                            <td><?php echo $product['pyear']; ?></td>
                            <td><?php echo $product['usedmonth']; ?>个月</td>
                            <td><?php echo $product['price']; ?>元</td>
                            <td><?php echo $product['status'] == 1 ? '上架' : '下架'; ?></td>
                            <td>
                                <?php if ($product['status'] == 1): ?>
                                    <a href="?action=unpublish&pid=<?php echo $product['pid']; ?>" class="btn btn-danger" onclick="return confirm('确定要下架该物品吗?')">下架</a>
                                <?php else: ?>
                                    <a href="?action=publish&pid=<?php echo $product['pid']; ?>" class="btn btn-success">上架</a>
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
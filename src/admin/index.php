<?php
require_once '../common/common.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// 获取统计信息
$totalUsers = queryOne("SELECT COUNT(*) as count FROM userInfo16 WHERE role = 0")['count'];
$totalProducts = queryOne("SELECT COUNT(*) as count FROM product16 WHERE status = 1")['count'];
$totalOrders = queryOne("SELECT COUNT(*) as count FROM order16")['count'];
$pendingComplaints = queryOne("SELECT COUNT(*) as count FROM cmplain16 WHERE status = 0")['count'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员后台</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin-top: 0;
            color: #333;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        .admin-menu {
            margin: 20px 0;
        }
        .admin-menu ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .admin-menu li {
            background: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .admin-menu a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
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
            <div class="admin-dashboard">
                <div class="stat-card">
                    <h3>总用户数</h3>
                    <div class="stat-number"><?php echo $totalUsers; ?></div>
                </div>
                <div class="stat-card">
                    <h3>总商品数</h3>
                    <div class="stat-number"><?php echo $totalProducts; ?></div>
                </div>
                <div class="stat-card">
                    <h3>总订单数</h3>
                    <div class="stat-number"><?php echo $totalOrders; ?></div>
                </div>
                <div class="stat-card">
                    <h3>待处理投诉</h3>
                    <div class="stat-number"><?php echo $pendingComplaints; ?></div>
                </div>
            </div>
            
            <div class="admin-menu">
                <h2>管理功能</h2>
                <ul>
                    <li><a href="products.php">物品管理</a></li>
                    <li><a href="complaints.php">投诉处理</a></li>
                    <li><a href="users.php">用户管理</a></li>
                    <li><a href="violations.php">违规记录</a></li>
                </ul>
            </div>
        </main>
        
        <?php include '../common/footer.php'; ?>
    </div>
</body>
</html>
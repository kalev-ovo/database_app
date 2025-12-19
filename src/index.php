<?php
// 引入通用函数文件
require_once 'common/common.php';

// 获取分类列表
$categories = getCategories();

// 获取搜索条件
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// 构建查询条件
$conditions = [];
$params = [];

$conditions[] = "p.status = 1";

if (!empty($category_id)) {
    $conditions[] = "p.category_id = ?";
    $params[] = $category_id;
}

if (!empty($search)) {
    $conditions[] = "p.pname LIKE ?";
    $params[] = "%$search%";
}

// 构建SQL查询
$whereClause = implode(" AND ", $conditions);
$sql = "SELECT p.*, c.category_name FROM product16 p JOIN category16 c ON p.category_id = c.category_id WHERE $whereClause";

// 获取商品列表
$products = query($sql, $params);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>二手交易平台</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>二手交易平台</h1>
        
        <!-- 登录状态 -->
        <div class="user-status">
            <?php if (isLoggedIn()): ?>
                欢迎，<?php echo $_SESSION['name']; ?> |
                <a href="cart.php">购物车</a> |
                <a href="order.php">订单</a> |
                <a href="user/favorites.php">收藏夹</a> |
                <a href="user/complaints.php">我的投诉</a> |
                <?php if (isAdmin()): ?>
                    <a href="admin/admin_dashboard.php">后台管理</a> |
                <?php endif; ?>
                <a href="logout.php">退出</a>
            <?php else: ?>
                <a href="login.php">登录</a> |
                <a href="register.php">注册</a>
            <?php endif; ?>
        </div>

        <hr>

        <!-- 搜索 + 分类 -->
        <div class="search-section">
            <form method="get" action="index.php">
                <div class="search-fields">
                    <div class="search-field">
                        <label for="category_id">分类：</label>
                        <select name="category_id" id="category_id">
                            <option value="">全部</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>" 
                                <?php if ($category_id == $c['category_id']) echo 'selected'; ?>>
                                    <?php echo $c['category_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="search-field">
                        <label for="search">关键词：</label>
                        <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit">搜索</button>
                    </div>
                </div>
            </form>
        </div>

        <hr>

        <!-- 发布物品按钮 -->
        <?php if (isLoggedIn() && !isAccountFrozen()): ?>
            <div class="post-product">
                <a href="post_product.php">发布二手物品</a>
            </div>
        <?php endif; ?>

        <!-- 商品列表 -->
        <div class="products-section">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $p): ?>
                    <div class="product-card">
                        <h3><a href="product_detail.php?pid=<?php echo $p['pid']; ?>"><?php echo $p['pname']; ?></a></h3>
                        <div class="product-info">
                            <p>分类：<?php echo $p['category_name']; ?></p>
                            <p>价格：￥<?php echo $p['price']; ?></p>
                            <p>年份：<?php echo $p['pyear']; ?></p>
                            <p>已使用：<?php echo $p['usedmonth']; ?> 月</p>
                            <p>新旧程度：<?php 
                                switch (true) {
                                    case $p['usedmonth'] < 3: echo '几乎全新'; break;
                                    case $p['usedmonth'] < 12: echo '九成新'; break;
                                    case $p['usedmonth'] < 24: echo '八成新'; break;
                                    case $p['usedmonth'] < 36: echo '七成新'; break;
                                    default: echo '六成新及以下'; break;
                                }
                            ?></p>
                        </div>
                        
                        <?php if (isLoggedIn() && !isAccountFrozen()): ?>
                            <div class="product-actions">
                                <form method="post" action="cart.php" style="display: inline;">
                                    <input type="hidden" name="pid" value="<?php echo $p['pid']; ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-primary">加入购物车</button>
                                </form>
                                
                                <form method="post" action="user/favorites.php" style="display: inline;">
                                    <input type="hidden" name="pid" value="<?php echo $p['pid']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" name="add_to_favorite" class="btn btn-secondary">收藏</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <p>暂无符合条件的二手物品</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
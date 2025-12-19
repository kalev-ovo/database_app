<?php
// 引入通用函数文件
require_once 'common/common.php';

$error = '';
$success = '';

// 检查是否提供了物品ID
if (!isset($_GET['pid'])) {
    redirect('index.php');
}

$pid = $_GET['pid'];

// 获取物品详情
$product = getProductById($pid);

// 检查物品是否存在
if (!$product) {
    redirect('index.php');
}

// 处理加入购物车请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    // 检查用户是否已登录
    if (!isLoggedIn()) {
        redirect('login.php');
    }
    
    // 检查用户账号是否被冻结
    if (isAccountFrozen()) {
        $error = '您的账号已被冻结，无法购买物品';
    } else {
        // 获取当前用户ID
        $uid = $_SESSION['uid'];
        
        // 检查购物车中是否已有该物品
        if (isInCart($uid, $pid)) {
            $error = '该物品已在购物车中';
        } else {
            // 获取用户的未支付购物车
            $sql = "SELECT cid FROM cart16 WHERE uid = ? AND status = 0";
            $cart = queryOne($sql, [$uid]);
            
            // 如果没有未支付购物车，则创建一个
            if (!$cart) {
                $cid = generateId('c');
                $sql = "INSERT INTO cart16 (cid, uid, cdate, status) VALUES (?, ?, NOW(), 0)";
                execute($sql, [$cid, $uid]);
            } else {
                $cid = $cart['cid'];
            }
            
            // 将物品添加到购物车
            $sql = "INSERT INTO cart_product16 (pid, cid, quantity) VALUES (?, ?, 1)";
            
            if (execute($sql, [$pid, $cid]) > 0) {
                $success = '物品已加入购物车';
            } else {
                $error = '加入购物车失败，请稍后重试';
            }
        }
    }
}

// 处理收藏请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_favorite'])) {
    // 检查用户是否已登录
    if (!isLoggedIn()) {
        redirect('login.php');
    }
    
    // 检查用户账号是否被冻结
    if (isAccountFrozen()) {
        $error = '您的账号已被冻结，无法收藏物品';
    } else {
        // 获取当前用户ID
        $uid = $_SESSION['uid'];
        
        // 检查物品是否已被收藏
        if (isFavorited($uid, $pid)) {
            $error = '该物品已被收藏';
        } else {
            // 将物品添加到收藏夹
            // 这里需要根据实际的收藏表结构来实现，暂时假设收藏表名为favorite16
            $sql = "INSERT INTO favorite16 (uid, pid) VALUES (?, ?)";
            
            if (execute($sql, [$uid, $pid]) > 0) {
                $success = '物品已收藏';
            } else {
                $error = '收藏失败，请稍后重试';
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
    <title><?php echo $product['pname']; ?> - 物品详情</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>二手交易平台 - 物品详情</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="product-detail">
            <h2><?php echo $product['pname']; ?></h2>
            
            <div class="product-info">
                <p>分类：<?php echo $product['category_name']; ?></p>
                <p>价格：￥<?php echo $product['price']; ?></p>
                <p>购买年份：<?php echo $product['pyear']; ?></p>
                <p>已使用月数：<?php echo $product['usedmonth']; ?> 月</p>
                <p>新旧程度：<?php 
                    switch (true) {
                        case $product['usedmonth'] < 3: echo '几乎全新'; break;
                        case $product['usedmonth'] < 12: echo '九成新'; break;
                        case $product['usedmonth'] < 24: echo '八成新'; break;
                        case $product['usedmonth'] < 36: echo '七成新'; break;
                        default: echo '六成新及以下'; break;
                    }
                ?></p>
                <p>联系方式：<?php echo $product['contact']; ?></p>
            </div>
            
            <div class="product-actions">
                <?php if (isLoggedIn() && !isAccountFrozen()): ?>
                    <form method="post" action="product_detail.php?pid=<?php echo $pid; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">加入购物车</button>
                        <button type="submit" name="add_to_favorite" class="btn btn-secondary">收藏</button>
                    </form>
                <?php endif; ?>
                <a href="index.php" class="btn btn-default">返回首页</a>
            </div>
        </div>
    </div>
</body>
</html>
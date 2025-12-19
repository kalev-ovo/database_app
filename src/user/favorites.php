<?php
require_once '../common/common.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$uid = $_SESSION['uid'];

// 处理收藏商品请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_favorite'])) {
    $pid = $_POST['pid'];
    
    // 检查是否已经收藏
    if (!isProductFavorited($pid)) {
        // 添加到收藏
        $sql = "INSERT INTO favorite16 (uid, pid) VALUES (?, ?)";
        execute($sql, [$uid, $pid]);
        echo '<script>alert("收藏成功!");</script>';
    } else {
        echo '<script>alert("您已经收藏过该商品!");</script>';
    }
    // 重定向回原页面
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: ../index.php');
    }
    exit;
}

// 处理取消收藏请求
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $sql = "DELETE FROM favorite16 WHERE uid = ? AND pid = ?";
    execute($sql, [$uid, $pid]);
    echo '<script>alert("取消收藏成功!");</script>';
}

// 获取用户的所有收藏商品
$sql = "SELECT p.* FROM product16 p JOIN favorite16 f ON p.pid = f.pid WHERE f.uid = ?";
$favorites = query($sql, [$uid]);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的收藏</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <?php include '../common/header.php'; ?>
        
        <h2>我的收藏</h2>
        
        <?php if (empty($favorites)): ?>
            <p>您还没有收藏任何商品</p>
        <?php else: ?>
            <div class="product-list">
                <?php foreach ($favorites as $product): ?>
                    <div class="product-item">
                        <h3><?php echo htmlspecialchars($product['pname']); ?></h3>
                        <p>类别: <?php echo getCategoryName($product['category_id']); ?></p>
                        <p>购买年份: <?php echo $product['pyear']; ?></p>
                        <p>新旧程度: <?php echo $product['usedmonth']; ?>个月</p>
                        <p>价格: <?php echo $product['price']; ?>元</p>
                        <p>联系方式: <?php echo htmlspecialchars($product['contact']); ?></p>
                        <div class="product-actions">
                            <a href="../product_detail.php?pid=<?php echo $product['pid']; ?>" class="btn btn-primary">查看详情</a>
                            <a href="../cart.php?action=add&pid=<?php echo $product['pid']; ?>&quantity=1" class="btn btn-secondary">加入购物车</a>
                            <a href="?action=remove&pid=<?php echo $product['pid']; ?>" class="btn btn-danger" onclick="return confirm('确定要取消收藏吗?')">取消收藏</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php include '../common/footer.php'; ?>
    </div>
</body>
</html>
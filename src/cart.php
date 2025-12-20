<?php
// 引入通用函数文件
require_once 'common/common.php';

// 检查用户是否已登录
if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

// 获取当前用户ID
$uid = $_SESSION['uid'];

// 处理加入购物车请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    // 检查用户账号是否被冻结
    if (isAccountFrozen()) {
        $error = '您的账号已被冻结，无法购买物品';
    } else {
        $pid = $_POST['pid'];
        
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

// 处理删除购物车物品请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_from_cart'])) {
    $pid = $_POST['pid'];
    
    // 获取用户的未支付购物车
    $sql = "SELECT cid FROM cart16 WHERE uid = ? AND status = 0";
    $cart = queryOne($sql, [$uid]);
    
    if ($cart) {
        $cid = $cart['cid'];
        
        // 从购物车中删除物品
        $sql = "DELETE FROM cart_product16 WHERE pid = ? AND cid = ?";
        
        if (execute($sql, [$pid, $cid]) > 0) {
            $success = '物品已从购物车中删除';
        } else {
            $error = '删除物品失败，请稍后重试';
        }
    }
}

// 处理更新购物车物品数量请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $pid = $_POST['pid'];
    $quantity = $_POST['quantity'];
    
    // 验证数量
    if (!is_numeric($quantity) || $quantity < 1) {
        $error = '数量必须是大于0的数字';
    } else {
        // 获取用户的未支付购物车
        $sql = "SELECT cid FROM cart16 WHERE uid = ? AND status = 0";
        $cart = queryOne($sql, [$uid]);
        
        if ($cart) {
            $cid = $cart['cid'];
            
            // 更新购物车物品数量
            $sql = "UPDATE cart_product16 SET quantity = ? WHERE pid = ? AND cid = ?";
            
            if (execute($sql, [$quantity, $pid, $cid]) > 0) {
                $success = '购物车已更新';
            } else {
                $error = '更新购物车失败，请稍后重试';
            }
        }
    }
}

// 处理结算购物车请求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    // 检查用户账号是否被冻结
    if (isAccountFrozen()) {
        $error = '您的账号已被冻结，无法购买物品';
    } else {
        // 获取用户的未支付购物车
        $sql = "SELECT cid FROM cart16 WHERE uid = ? AND status = 0";
        $cart = queryOne($sql, [$uid]);
        
        if ($cart) {
            $cid = $cart['cid'];
            
            // 获取购物车中的物品
            $sql = "SELECT cp.*, p.pname, p.price FROM cart_product16 cp JOIN product16 p ON cp.pid = p.pid WHERE cp.cid = ?";
            $cartItems = query($sql, [$cid]);
            
            if (count($cartItems) > 0) {
                // 生成订单ID
                $oid = generateId('o');
                
                // 计算总金额
                $totalAmount = 0;
                foreach ($cartItems as $item) {
                    $totalAmount += $item['price'] * $item['quantity'];
                }
                
                // 事务开始
                try {
                    // 更新购物车状态为已支付
                    $sql = "UPDATE cart16 SET status = 1 WHERE cid = ?";
                    execute($sql, [$cid]);
                    
                    // 为每个物品创建订单
                    foreach ($cartItems as $item) {
                        $amount = $item['price'] * $item['quantity'];
                        
                        // 创建订单
                        $sql = "INSERT INTO order16 (oid, pid, uid, cid, tdate, amount, status) VALUES (?, ?, ?, ?, NOW(), ?, 0)";
                        execute($sql, [$oid, $item['pid'], $uid, $cid, $amount]);
                    }
                    
                    // 事务提交
                    $success = '订单已生成，交易完成';
                    
                    // 跳转到订单页面
                    redirect('order.php');
                } catch (Exception $e) {
                    // 事务回滚
                    $error = '结算失败，请稍后重试';
                }
            } else {
                $error = '购物车是空的，无法结算';
            }
        } else {
            $error = '购物车不存在，无法结算';
        }
    }
}

// 获取用户的未支付购物车
$sql = "SELECT cid FROM cart16 WHERE uid = ? AND status = 0";
$cart = queryOne($sql, [$uid]);

$cartItems = [];
$totalAmount = 0;

if ($cart) {
    $cid = $cart['cid'];
    
    // 获取购物车中的物品
    $sql = "SELECT cp.*, p.pname, p.price FROM cart_product16 cp JOIN product16 p ON cp.pid = p.pid WHERE cp.cid = ?";
    $cartItems = query($sql, [$cid]);
    
    // 计算总金额
    foreach ($cartItems as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>购物车</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>二手交易平台 - 购物车</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="cart-section">
            <?php if (count($cartItems) > 0): ?>
                <form method="POST" action="cart.php">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>物品名称</th>
                                <th>单价</th>
                                <th>数量</th>
                                <th>小计</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><a href="product_detail.php?pid=<?php echo $item['pid']; ?>"><?php echo $item['pname']; ?></a></td>
                                    <td>￥<?php echo $item['price']; ?></td>
                                    <td>
                                        <input type="number" name="quantity[<?php echo $item['pid']; ?>]" value="<?php echo $item['quantity']; ?>" min="1">
                                        <button type="submit" name="update_quantity" value="<?php echo $item['pid']; ?>">更新</button>
                                    </td>
                                    <td>￥<?php echo $item['price'] * $item['quantity']; ?></td>
                                    <td>
                                        <form method="POST" action="cart.php" style="display: inline;">
                                            <input type="hidden" name="pid" value="<?php echo $item['pid']; ?>">
                                            <button type="submit" name="remove_from_cart">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="cart-total">
                        <p>总金额：￥<?php echo $totalAmount; ?></p>
                        <button type="submit" name="checkout" class="btn btn-primary">结算</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="empty-cart">
                    <p>购物车是空的，快去添加物品吧！</p>
                    <a href="index.php">返回首页</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
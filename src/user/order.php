<?php
require_once __DIR__ . '/../common/common.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['uid'];

// 获取用户的所有订单
$orders = query("SELECT o.*, p.pname, p.pimage FROM order16 o JOIN product16 p ON o.pid = p.pid WHERE o.uid = ?", [$uid]);

// 获取订单的交易状态
$orderStatus = [
    'pending' => '待支付',
    'paid' => '已支付',
    'shipped' => '已发货',
    'delivered' => '已送达',
    'completed' => '交易完成',
    'cancelled' => '已取消'
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的订单</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/../common/header.php'; ?>
        
        <h2>我的订单</h2>
        
        <?php if (empty($orders)): ?>
            <p>您还没有任何订单</p>
        <?php else: ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>商品名称</th>
                        <th>商品图片</th>
                        <th>单价</th>
                        <th>数量</th>
                        <th>总价</th>
                        <th>订单状态</th>
                        <th>下单时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['oid']; ?></td>
                            <td><?php echo htmlspecialchars($order['pname']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($order['pimage']); ?>" width="80" height="80"></td>
                            <td><?php echo $order['oprice']; ?>元</td>
                            <td><?php echo $order['onumber']; ?></td>
                            <td><?php echo $order['ototalprice']; ?>元</td>
                            <td><?php echo $orderStatus[$order['ostatus']]; ?></td>
                            <td><?php echo $order['otime']; ?></td>
                            <td>
                                <a href="order_detail.php?oid=<?php echo $order['oid']; ?>">查看详情</a>
                                <?php if ($order['ostatus'] == 'pending'): ?>
                                    <a href="payment.php?oid=<?php echo $order['oid']; ?>">去支付</a>
                                <?php endif; ?>
                                <?php if ($order['ostatus'] == 'delivered'): ?>
                                    <a href="confirm_receipt.php?oid=<?php echo $order['oid']; ?>">确认收货</a>
                                <?php endif; ?>
                                <?php if ($order['ostatus'] == 'completed'): ?>
                                    <a href="complaint.php?oid=<?php echo $order['oid']; ?>">投诉</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <?php include __DIR__ . '/../common/footer.php'; ?>
    </div>
</body>
</html>
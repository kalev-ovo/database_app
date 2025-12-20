<?php
require_once '../common/common.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['uid'];
$error = '';
$success = '';

// 处理投诉提交
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_complaint'])) {
    $oid = $_POST['oid'];
    $reason = $_POST['reason'];
    
    // 验证输入
    if (empty($reason)) {
        $error = '请填写投诉原因';
    } else {
        // 生成投诉ID
        $complain_id = generateId('comp_');
        
        // 获取订单信息
        $order = queryOne("SELECT * FROM order16 WHERE oid = ? AND uid = ?", [$oid, $uid]);
        
        if ($order) {
            // 插入投诉记录
            $sql = "INSERT INTO cmplain16 (oid, uid, complain_id, pid, reason, status) VALUES (?, ?, ?, ?, ?, 0)";
            execute($sql, [$oid, $uid, $complain_id, $order['pid'], $reason]);
            $success = '投诉提交成功，请等待处理';
        } else {
            $error = '订单不存在或不属于当前用户';
        }
    }
}

// 获取订单信息
$oid = isset($_GET['oid']) ? $_GET['oid'] : '';
if (empty($oid)) {
    echo '<script>alert("缺少订单ID"); window.location.href="order.php";</script>';
    exit;
}

$order = queryOne("SELECT o.*, p.pname FROM order16 o JOIN product16 p ON o.pid = p.pid WHERE o.oid = ? AND o.uid = ?", [$oid, $uid]);
if (!$order) {
    echo '<script>alert("订单不存在或不属于当前用户"); window.location.href="order.php";</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投诉页面</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'common/header.php'; ?>
        
        <h2>提交投诉</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="order-info">
            <h3>订单信息</h3>
            <p>订单号: <?php echo $order['oid']; ?></p>
            <p>商品名称: <?php echo htmlspecialchars($order['pname']); ?></p>
            <p>下单时间: <?php echo $order['tdate']; ?></p>
            <p>订单金额: <?php echo $order['amount']; ?>元</p>
        </div>
        
        <form method="post" action="complaint.php" class="complaint-form">
            <input type="hidden" name="oid" value="<?php echo $order['oid']; ?>">
            
            <div class="form-group">
                <label for="reason">投诉原因:</label>
                <textarea id="reason" name="reason" rows="5" required><?php echo isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit_complaint" class="btn btn-primary">提交投诉</button>
                <a href="order.php" class="btn btn-secondary">返回订单列表</a>
            </div>
        </form>
        
        <?php include 'common/footer.php'; ?>
    </div>
</body>
</html>
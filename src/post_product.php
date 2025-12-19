<?php
// 引入通用函数文件
require_once 'common/common.php';

// 检查用户是否已登录
if (!isLoggedIn()) {
    redirect('login.php');
}

// 检查用户账号是否被冻结
if (isAccountFrozen()) {
    redirect('index.php');
}

$error = '';
$success = '';

// 获取分类列表
$categories = getCategories();

// 处理物品发布请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $pname = $_POST['pname'];
    $category_id = $_POST['category_id'];
    $pyear = $_POST['pyear'];
    $usedmonth = $_POST['usedmonth'];
    $price = $_POST['price'];
    $contact = $_POST['contact'];

    // 验证表单数据
    if (empty($pname) || empty($category_id) || empty($pyear) || empty($usedmonth) || empty($price) || empty($contact)) {
        $error = '所有字段都必须填写';
    } elseif (!is_numeric($pyear) || !is_numeric($usedmonth) || !is_numeric($price)) {
        $error = '购买年份、已使用月数和价格必须是数字';
    } else {
        // 生成物品ID
        $pid = generateId('p');
        
        // 获取当前用户ID
        $uid = $_SESSION['uid'];
        
        // 插入物品数据
        $sql = "INSERT INTO product16 (pid, pname, category_id, uid, pyear, usedmonth, price, contact, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $params = [$pid, $pname, $category_id, $uid, $pyear, $usedmonth, $price, $contact];
        
        if (execute($sql, $params) > 0) {
            $success = '物品发布成功';
            // 发布成功后跳转到首页
            redirect('index.php');
        } else {
            $error = '物品发布失败，请稍后重试';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发布二手物品</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>二手交易平台 - 发布二手物品</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="post_product.php">
            <div class="form-group">
                <label for="pname">物品名称：</label>
                <input type="text" id="pname" name="pname" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">物品分类：</label>
                <select name="category_id" id="category_id" required>
                    <option value="">请选择分类</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="pyear">购买年份：</label>
                <input type="number" id="pyear" name="pyear" min="2000" max="<?php echo date('Y'); ?>" value="<?php echo date('Y'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="usedmonth">已使用月数：</label>
                <input type="number" id="usedmonth" name="usedmonth" min="0" max="120" required>
            </div>
            
            <div class="form-group">
                <label for="price">转让价格（元）：</label>
                <input type="number" id="price" name="price" min="0.01" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="contact">联系方式：</label>
                <input type="text" id="contact" name="contact" required>
            </div>
            
            <div class="form-group">
                <button type="submit">发布物品</button>
                <a href="index.php" class="cancel">取消</a>
            </div>
        </form>
    </div>
</body>
</html>
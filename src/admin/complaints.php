<?php
require_once '../common/common.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// 处理投诉
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_complaint'])) {
    $oid = $_POST['oid'];
    $uid = $_POST['uid'];
    $complain_id = $_POST['complain_id'];
    $result = $_POST['result'];
    
    // 生成处理ID
    $process_id = generateId('proc_');
    
    // 插入处理记录
    $sql = "INSERT INTO process16 (oid, process_id, complain_id, uid, result) VALUES (?, ?, ?, ?, ?)";
    execute($sql, [$oid, $process_id, $complain_id, $uid, $result]);
    
    // 更新投诉状态
    $sql = "UPDATE cmplain16 SET status = 1 WHERE oid = ? AND uid = ? AND complain_id = ?";
    execute($sql, [$oid, $uid, $complain_id]);
    
    echo '<script>alert("投诉处理完成"); window.location.href="complaints.php";</script>';
}

// 获取投诉列表
$status = isset($_GET['status']) ? $_GET['status'] : 0;
$complaints = query("SELECT c.*, p.pname, u.name as username FROM cmplain16 c JOIN product16 p ON c.pid = p.pid JOIN userInfo16 u ON c.uid = u.uid WHERE c.status = ?", [$status]);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投诉处理</title>
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
            <h2>投诉处理</h2>
            
            <div class="filter-options">
                <a href="complaints.php?status=0" class="btn btn-primary">待处理投诉</a>
                <a href="complaints.php?status=1" class="btn btn-secondary">已处理投诉</a>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>商品名称</th>
                        <th>投诉用户</th>
                        <th>投诉ID</th>
                        <th>投诉原因</th>
                        <th>投诉时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo $complaint['oid']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['pname']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['username']); ?></td>
                            <td><?php echo $complaint['complain_id']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['reason']); ?></td>
                            <td><?php echo $complaint['cdate']; ?></td>
                            <td><?php echo $complaint['status'] == 0 ? '待处理' : '已处理'; ?></td>
                            <td>
                                <?php if ($complaint['status'] == 0): ?>
                                    <button type="button" class="btn btn-primary" onclick="openProcessModal('<?php echo $complaint['oid']; ?>', '<?php echo $complaint['uid']; ?>', '<?php echo $complaint['complain_id']; ?>')">处理</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
        
        <?php include '../common/footer.php'; ?>
    </div>
    
    <!-- 处理投诉模态框 -->
    <div id="processModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeProcessModal()">&times;</span>
            <h3>处理投诉</h3>
            <form method="post" action="complaints.php">
                <input type="hidden" name="oid" id="modal_oid">
                <input type="hidden" name="uid" id="modal_uid">
                <input type="hidden" name="complain_id" id="modal_complain_id">
                
                <div class="form-group">
                    <label for="result">处理意见:</label>
                    <textarea id="result" name="result" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="process_complaint" class="btn btn-primary">提交处理意见</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openProcessModal(oid, uid, complain_id) {
            document.getElementById('modal_oid').value = oid;
            document.getElementById('modal_uid').value = uid;
            document.getElementById('modal_complain_id').value = complain_id;
            document.getElementById('processModal').style.display = 'block';
        }
        
        function closeProcessModal() {
            document.getElementById('processModal').style.display = 'none';
        }
        
        // 点击模态框外部关闭
        window.onclick = function(event) {
            var modal = document.getElementById('processModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
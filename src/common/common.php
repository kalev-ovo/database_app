<?php
// 引入数据库配置文件
require_once __DIR__ . '/../config.php';

// 开启会话
session_start();

/**
 * 执行参数化查询（SELECT）
 * @param string $sql SQL查询语句
 * @param array $params 参数数组
 * @return array 查询结果
 */
function query($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * 执行参数化查询（单行SELECT）
 * @param string $sql SQL查询语句
 * @param array $params 参数数组
 * @return array 查询结果
 */
function queryOne($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * 执行参数化查询（INSERT/UPDATE/DELETE）
 * @param string $sql SQL查询语句
 * @param array $params 参数数组
 * @return int 受影响的行数
 */
function execute($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * 生成唯一ID
 * @param string $prefix ID前缀
 * @return string 唯一ID
 */
function generateId($prefix = '') {
    return $prefix . uniqid() . rand(1000, 9999);
}

/**
 * 检查用户是否已登录
 * @return bool 是否已登录
 */
function isLoggedIn() {
    return isset($_SESSION['uid']);
}

/**
 * 检查用户是否为管理员
 * @return bool 是否为管理员
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 1;
}

/**
 * 检查用户账号是否被冻结
 * @return bool 是否被冻结
 */
function isAccountFrozen() {
    return isset($_SESSION['status']) && $_SESSION['status'] == 0;
}

/**
 * 重定向到指定页面
 * @param string $url 目标URL
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * 显示错误信息
 * @param string $message 错误信息
 */
function showError($message) {
    echo "<div style='color: red;'>$message</div>";
}

/**
 * 显示成功信息
 * @param string $message 成功信息
 */
function showSuccess($message) {
    echo "<div style='color: green;'>$message</div>";
}

/**
 * 哈希密码
 * @param string $password 原始密码
 * @return string 哈希后的密码
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * 验证密码
 * @param string $password 原始密码
 * @param string $hash 哈希后的密码
 * @return bool 密码是否正确
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * 获取用户违规次数
 * @param string $uid 用户ID
 * @return int 违规次数
 */
function getViolationCount($uid) {
    $sql = "SELECT COUNT(*) as count FROM violation16 WHERE uid = ?";
    $result = queryOne($sql, [$uid]);
    return $result['count'];
}

/**
 * 获取所有物品分类
 * @return array 分类列表
 */
function getCategories() {
    $sql = "SELECT * FROM category16";
    return query($sql);
}

/**
 * 获取物品详情
 * @param string $pid 物品ID
 * @return array 物品详情
 */
function getProductById($pid) {
    $sql = "SELECT p.*, c.category_name FROM product16 p JOIN category16 c ON p.category_id = c.category_id WHERE p.pid = ? AND p.status = 1";
    return queryOne($sql, [$pid]);
}

/**
 * 检查物品是否已被收藏
 * @param string $uid 用户ID
 * @param string $pid 物品ID
 * @return bool 是否已收藏
 */
function isFavorited($uid, $pid) {
    $sql = "SELECT COUNT(*) as count FROM favorite16 WHERE uid = ? AND pid = ?";
    $result = queryOne($sql, [$uid, $pid]);
    return $result['count'] > 0;
}

/**
 * 检查物品是否已在购物车中
 * @param string $uid 用户ID
 * @param string $pid 物品ID
 * @return bool 是否已在购物车中
 */
function isInCart($uid, $pid) {
    $sql = "SELECT COUNT(*) as count FROM cart16 c JOIN cart_product16 cp ON c.cid = cp.cid WHERE c.uid = ? AND cp.pid = ? AND c.status = 0";
    $result = queryOne($sql, [$uid, $pid]);
    return $result['count'] > 0;
}

/**
 * 根据分类ID获取分类名称
 * @param string $category_id 分类ID
 * @return string 分类名称
 */
function getCategoryName($category_id) {
    $sql = "SELECT category_name FROM category16 WHERE category_id = ?";
    $result = queryOne($sql, [$category_id]);
    return $result ? $result['category_name'] : '未知分类';
}

/**
 * 检查当前登录用户是否已收藏某商品
 * @param string $pid 商品ID
 * @return bool 是否已收藏
 */
function isProductFavorited($pid) {
    if (!isLoggedIn()) {
        return false;
    }
    $uid = $_SESSION['uid'];
    return isFavorited($uid, $pid);
}

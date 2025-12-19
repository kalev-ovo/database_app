<?php
// 引入通用函数文件
require_once 'common/common.php';

// 销毁会话
session_destroy();

// 跳转到登录页面
redirect('login.php');
?>
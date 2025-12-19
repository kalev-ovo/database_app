// 表单验证函数
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.value.trim() === '') {
            isValid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
        }
    });
    
    if (!isValid) {
        alert('请填写所有必填字段');
    }
    
    return isValid;
}

// 确认对话框
function confirmAction(message) {
    return confirm(message);
}

// 显示加载指示器
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="loading">加载中...</div>';
    }
}

// 隐藏加载指示器
function hideLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '';
    }
}

// 动态加载内容
function loadContent(url, targetId) {
    showLoading(targetId);
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.getElementById(targetId).innerHTML = data;
        })
        .catch(error => {
            console.error('加载内容失败:', error);
            document.getElementById(targetId).innerHTML = '<div class="error">加载失败</div>';
        });
}

// 更新数量
function updateQuantity(itemId, change) {
    const input = document.getElementById('quantity_' + itemId);
    let current = parseInt(input.value);
    const newQuantity = Math.max(1, current + change);
    input.value = newQuantity;
}

// 搜索功能
function searchProducts() {
    const searchInput = document.getElementById('searchInput');
    const productItems = document.querySelectorAll('.product-item');
    const searchTerm = searchInput.value.toLowerCase();
    
    productItems.forEach(item => {
        const productName = item.querySelector('h3').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// 分类筛选
function filterByCategory(categoryId) {
    const productItems = document.querySelectorAll('.product-item');
    
    productItems.forEach(item => {
        const category = item.querySelector('.product-category').textContent;
        if (categoryId === 'all' || category.includes(categoryId)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// 模态框函数
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// 点击模态框外部关闭
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}

// 表单提交前的处理
function handleFormSubmit(formId, callback) {
    const form = document.getElementById(formId);
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm(formId)) {
            if (callback) {
                callback();
            } else {
                form.submit();
            }
        }
    });
}

// 添加到购物车
function addToCart(pid) {
    if (confirm('确定要添加到购物车吗？')) {
        window.location.href = 'cart.php?action=add&pid=' + pid + '&quantity=1';
    }
}

// 收藏商品
function addToFavorite(pid) {
    if (confirm('确定要收藏该商品吗？')) {
        const form = document.createElement('form');
        form.method = 'post';
        form.action = 'user/favorites.php';
        
        const pidInput = document.createElement('input');
        pidInput.type = 'hidden';
        pidInput.name = 'pid';
        pidInput.value = pid;
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'add_to_favorite';
        actionInput.value = '1';
        
        form.appendChild(pidInput);
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// 初始化页面
function initPage() {
    // 添加表单验证
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredInputs = this.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            requiredInputs.forEach(input => {
                if (input.value.trim() === '') {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('请填写所有必填字段');
            }
        });
    });
    
    // 添加搜索功能
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', searchProducts);
    }
    
    // 添加分类筛选
    const categorySelect = document.getElementById('categoryFilter');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            filterByCategory(this.value);
        });
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', initPage);
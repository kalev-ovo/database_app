FROM php:8.0-apache

# 安装PHP扩展
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 启用Apache重写模块
RUN a2enmod rewrite

# 设置工作目录
WORKDIR /var/www/html

# 项目文件通过docker-compose volumes映射

# 设置文件权限
RUN chown -R www-data:www-data /var/www/html

# 暴露端口
EXPOSE 80
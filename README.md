这个扩展包用来将Geetest验证码集成进laravel-admin中使用

## 安装

1、注册<a href="https://auth.geetest.com/register">geetest</a>,获取key

2、引入包

    composer require james.xue/login-geetest

3、发布静态资源

    php artisan vendor:publish --provider=James\Geetest\GeetestServiceProvider.php
    
或者
     
    php artisan vendor:publish --tag=geetest
    

